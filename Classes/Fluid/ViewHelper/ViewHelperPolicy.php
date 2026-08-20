<?php

declare(strict_types=1);
namespace In2code\Powermail\Fluid\ViewHelper;

use In2code\Powermail\Utility\ConfigurationUtility;
use Throwable;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperResolver;

/**
 * Allowlist of ViewHelpers that powermail is willing to execute while it parses a configured
 * string (a mail subject, a receiver name, a field title, ...) with Fluid.
 *
 * Identifiers are written as they appear in a template - "f:cObject", "f:format.nl2br" - and are
 * case sensitive, because Fluid resolves "f:cobject" to a different class than "f:cObject".
 * A trailing "*" makes an entry a prefix match: "f:format.*" or "f:*".
 */
final class ViewHelperPolicy
{
    /**
     * "namespace:method" => true
     *
     * @var array
     */
    private $exact;

    /**
     * "namespace:method-prefix" without the trailing "*"
     *
     * @var array
     */
    private $prefixes;

    /**
     * namespace identifiers with a "namespace:*" entry
     *
     * @var array
     */
    private $namespaces;

    /**
     * @param array $exact
     * @param array $prefixes
     * @param array $namespaces
     */
    private function __construct(array $exact, array $prefixes, array $namespaces)
    {
        $this->exact = $exact;
        $this->prefixes = $prefixes;
        $this->namespaces = $namespaces;
    }

    /**
     * @return self
     */
    public static function fromExtensionConfiguration(): self
    {
        return self::fromIdentifiers(ConfigurationUtility::getAllowedViewHelpersInParsedStrings());
    }

    /**
     * @param string[] $identifiers
     * @return self
     */
    public static function fromIdentifiers(array $identifiers): self
    {
        $exact = [];
        $prefixes = [];
        $namespaces = [];
        foreach ($identifiers as $identifier) {
            $identifier = trim((string)$identifier);
            if (strpos($identifier, ':') === false) {
                continue;
            }

            [$namespace, $method] = explode(':', $identifier, 2);
            if ($namespace === '' || $method === '') {
                continue;
            }

            if ($method === '*') {
                $namespaces[$namespace] = true;
                continue;
            }

            if (substr($method, -1) === '*') {
                $prefixes[] = $namespace . ':' . substr($method, 0, -1);
                continue;
            }

            $exact[$namespace . ':' . $method] = true;
        }

        sort($prefixes);
        ksort($exact);
        ksort($namespaces);

        return new self($exact, $prefixes, $namespaces);
    }

    /**
     * @param string $namespace
     * @param string $method
     * @return bool
     */
    public function isAllowed(string $namespace, string $method): bool
    {
        if (isset($this->namespaces[$namespace])) {
            return true;
        }

        $identifier = $namespace . ':' . $method;
        if (isset($this->exact[$identifier])) {
            return true;
        }

        foreach ($this->prefixes as $prefix) {
            if (strncmp($identifier, $prefix, strlen($prefix)) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $namespace
     * @return bool
     */
    public function isNamespaceRelevant(string $namespace): bool
    {
        if (isset($this->namespaces[$namespace])) {
            return true;
        }

        $prefix = $namespace . ':';
        foreach (array_keys($this->exact) as $identifier) {
            if (strncmp((string)$identifier, $prefix, strlen($prefix)) === 0) {
                return true;
            }
        }

        foreach ($this->prefixes as $allowedPrefix) {
            if (strncmp($allowedPrefix, $prefix, strlen($prefix)) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Part of the identifier of a compiled template, so that a changed allowlist invalidates
     * compiled templates which still have the previous policy baked in.
     *
     * Must stay alphanumeric: RenderingContext::setControllerAction() cuts everything after a dot
     * and the template compiler replaces every other special character.
     *
     * @return string
     */
    public function getFingerprint(): string
    {
        return substr(sha1($this->getCanonicalRepresentation()), 0, 10);
    }

    /**
     * @return string[]
     */
    public function getIdentifiers(): array
    {
        $identifiers = array_keys($this->exact);
        foreach ($this->prefixes as $prefix) {
            $identifiers[] = $prefix . '*';
        }

        foreach (array_keys($this->namespaces) as $namespace) {
            $identifiers[] = $namespace . ':*';
        }

        sort($identifiers);
        return $identifiers;
    }

    /**
     * Class names of all allowlisted ViewHelpers that the given resolver can resolve. Used as a
     * second net at invocation time, where only the class name is known.
     *
     * Prefix and namespace entries cannot be expanded to class names without knowing every
     * ViewHelper in a namespace, so they are reported separately by hasUnexpandableEntries().
     *
     * @param ViewHelperResolver $resolver
     * @return array
     */
    public function resolveAllowedClassNames(ViewHelperResolver $resolver): array
    {
        $classNames = [];
        foreach (array_keys($this->exact) as $identifier) {
            [$namespace, $method] = explode(':', (string)$identifier, 2);
            try {
                $className = $resolver->resolveViewHelperClassName($namespace, $method);
            } catch (Throwable $throwable) {
                // an allowlisted ViewHelper that does not exist in this installation is not a
                // reason to fail - it simply can never be invoked
                unset($throwable);
                continue;
            }
            if (is_string($className) && $className !== '') {
                $classNames[$className] = true;
            }
        }

        return $classNames;
    }

    /**
     * @return bool
     */
    public function hasUnexpandableEntries(): bool
    {
        return $this->prefixes !== [] || $this->namespaces !== [];
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->exact === [] && $this->prefixes === [] && $this->namespaces === [];
    }

    /**
     * @return string
     */
    private function getCanonicalRepresentation(): string
    {
        return implode(',', $this->getIdentifiers());
    }
}
