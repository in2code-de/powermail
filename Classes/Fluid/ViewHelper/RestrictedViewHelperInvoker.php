<?php

declare(strict_types=1);
namespace In2code\Powermail\Fluid\ViewHelper;

use Closure;
use In2code\Powermail\Utility\ObjectUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperInvoker;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperResolver;

/**
 * Second net below RestrictedViewHelperResolver.
 *
 * This is NOT a sandbox: every ViewHelper that overrides compile() emits inline PHP and never
 * reaches invoke() - among them f:format.raw, f:then, f:else, f:case, f:defaultCase, f:switch,
 * f:cache.disable, f:cache.static, f:section, f:comment and f:layout. The resolver is what actually
 * blocks those, because it prevents the node from being created at all.
 *
 * The invoker still matters for one case the resolver cannot cover: a template that was compiled in
 * a different (permissive) scope and is executed here after a cache hit.
 */
final class RestrictedViewHelperInvoker extends ViewHelperInvoker
{
    /**
     * @var array
     */
    private $allowedClassNames;

    /**
     * @var bool
     */
    private $enforce;

    /**
     * @param ViewHelperPolicy $policy
     * @param ViewHelperResolver $resolver
     */
    public function __construct(ViewHelperPolicy $policy, ViewHelperResolver $resolver)
    {
        $this->allowedClassNames = $policy->resolveAllowedClassNames($resolver);
        $this->allowedClassNames[BlockedViewHelper::class] = true;

        // Wildcard entries cannot be expanded to a finite list of class names. Enforcing an
        // incomplete list would break allowlisted ViewHelpers, so in that case this net is disabled
        // and the resolver remains the only gate.
        $this->enforce = $policy->hasUnexpandableEntries() === false;
    }

    /**
     * @param string|ViewHelperInterface $viewHelperClassNameOrInstance
     * @param array $arguments
     * @param RenderingContextInterface $renderingContext
     * @param Closure|null $renderChildrenClosure
     * @return mixed
     */
    public function invoke(
        $viewHelperClassNameOrInstance,
        array $arguments,
        RenderingContextInterface $renderingContext,
        ?Closure $renderChildrenClosure = null
    ) {
        if ($this->enforce) {
            $className = is_string($viewHelperClassNameOrInstance)
                ? $viewHelperClassNameOrInstance
                : get_class($viewHelperClassNameOrInstance);

            if (!isset($this->allowedClassNames[$className])) {
                ObjectUtility::getLogger(self::class)->warning(
                    'powermail blocked the invocation of a ViewHelper in a string that is parsed with Fluid',
                    ['viewHelper' => $className]
                );

                return '';
            }
        }

        return parent::invoke($viewHelperClassNameOrInstance, $arguments, $renderingContext, $renderChildrenClosure);
    }
}
