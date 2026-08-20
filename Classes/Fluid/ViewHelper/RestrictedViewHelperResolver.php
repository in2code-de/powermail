<?php

declare(strict_types=1);
namespace In2code\Powermail\Fluid\ViewHelper;

use In2code\Powermail\Utility\ObjectUtility;
use Throwable;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperResolver;

/**
 * ViewHelperResolver for strings that powermail parses with Fluid (mail subject, receiver name,
 * field title, ...). It allows variable interpolation but only lets allowlisted ViewHelpers resolve.
 *
 * Why the resolver and not the ViewHelperInvoker: ViewHelperNode::__construct() resolves the class
 * before a ViewHelper can contribute compiled code, so a ViewHelper that does not resolve never gets
 * a node. Blocking in the invoker would miss every ViewHelper that overrides compile() and emits
 * inline PHP - f:format.raw, f:then, f:else, f:switch, f:section, f:layout, f:cache.* and more.
 *
 * All resolution and instantiation is delegated to the resolver that TYPO3 built for this rendering
 * context, so allowlisted ViewHelpers keep being instantiated through the DI container and the
 * globally registered namespaces keep working.
 */
final class RestrictedViewHelperResolver extends ViewHelperResolver
{
    /**
     * @var ViewHelperResolver
     */
    private $decorated;

    /**
     * @var ViewHelperPolicy
     */
    private $policy;

    /**
     * @param ViewHelperResolver $decorated
     * @param ViewHelperPolicy $policy
     */
    public function __construct(ViewHelperResolver $decorated, ViewHelperPolicy $policy)
    {
        $this->decorated = $decorated;
        $this->policy = $policy;
    }

    /**
     * Namespace imports from within the parsed string are ignored. Fluid registers
     * "{namespace x=...}" and 'xmlns:x="..."' through addNamespace() from within
     * NamespaceDetectionTemplateProcessor, so a no-op here is what actually neutralises them.
     * Without this, an allowlisted identifier could be re-aliased -
     * "{namespace f=Vendor\Evil\ViewHelpers}<f:if ...>" would keep an allowed name while pointing
     * it at a class of the attacker's choice.
     *
     * This also covers addNamespaces() and the addCompiledNamespaces() call of a compiled template,
     * because both are routed through this method.
     *
     * @param string $identifier
     * @param string|array|null $phpNamespace
     * @return void
     */
    public function addNamespace($identifier, $phpNamespace): void
    {
    }

    /**
     * @param array $namespaces
     * @return void
     */
    public function setNamespaces(array $namespaces): void
    {
    }

    /**
     * @return array
     */
    public function getNamespaces(): array
    {
        return $this->decorated->getNamespaces();
    }

    /**
     * A namespace is only valid if the allowlist holds at least one entry for it. Everything else is
     * reported as ignored, which makes the parser keep the tag as literal text instead of raising an
     * UnknownNamespaceException.
     *
     * @param string $namespaceIdentifier
     * @return bool
     */
    public function isNamespaceValid($namespaceIdentifier): bool
    {
        return $this->decorated->isNamespaceValid($namespaceIdentifier)
            && !$this->decorated->isNamespaceIgnored($namespaceIdentifier)
            && $this->policy->isNamespaceRelevant($namespaceIdentifier);
    }

    /**
     * @param string $namespaceIdentifier
     * @return bool
     */
    public function isNamespaceIgnored($namespaceIdentifier): bool
    {
        return !$this->isNamespaceValid($namespaceIdentifier);
    }

    /**
     * @param string $namespaceIdentifier
     * @param string $methodIdentifier
     * @return string
     */
    public function resolveViewHelperClassName($namespaceIdentifier, $methodIdentifier): string
    {
        if ($this->policy->isAllowed($namespaceIdentifier, $methodIdentifier)) {
            try {
                $viewHelperClassName = $this->decorated->resolveViewHelperClassName(
                    $namespaceIdentifier,
                    $methodIdentifier
                );
                if (is_string($viewHelperClassName) && $viewHelperClassName !== '') {
                    return $viewHelperClassName;
                }
                $message = 'The ViewHelper could not be resolved to a class name.';
            } catch (Throwable $throwable) {
                $message = $throwable->getMessage();
            }

            // An allowlisted name that does not resolve to a class must not abort the rendering of
            // the whole value - that would replace a subject or a field title with its defanged
            // fallback. This also catches the attempt to point an allowlisted identifier at another
            // PHP namespace, because the import itself is already ignored by addNamespace().
            ObjectUtility::getLogger(self::class)->warning(
                'powermail could not resolve an allowed ViewHelper in a string that is parsed with Fluid',
                [
                    'viewHelper' => $namespaceIdentifier . ':' . $methodIdentifier,
                    'exception' => $message,
                ]
            );

            return BlockedViewHelper::class;
        }

        ObjectUtility::getLogger(self::class)->warning(
            'powermail blocked a ViewHelper in a string that is parsed with Fluid',
            ['viewHelper' => $namespaceIdentifier . ':' . $methodIdentifier]
        );

        return BlockedViewHelper::class;
    }

    /**
     * Instantiating is left to the decorated resolver, so allowlisted ViewHelpers keep coming from the
     * DI container. BlockedViewHelper has no constructor arguments and is created by the same path.
     *
     * @param string $viewHelperClassName
     * @return ViewHelperInterface
     */
    public function createViewHelperInstanceFromClassName($viewHelperClassName): ViewHelperInterface
    {
        return $this->decorated->createViewHelperInstanceFromClassName($viewHelperClassName);
    }
}
