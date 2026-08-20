<?php

declare(strict_types=1);
namespace In2code\Powermail\Tests\Unit\Fluid\ViewHelper;

use In2code\Powermail\Fluid\ViewHelper\BlockedViewHelper;
use In2code\Powermail\Fluid\ViewHelper\RestrictedViewHelperResolver;
use In2code\Powermail\Fluid\ViewHelper\ViewHelperPolicy;
use In2code\Powermail\Tests\Helper\TestingHelper;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperResolver;
use TYPO3Fluid\Fluid\ViewHelpers\Format\PrintfViewHelper;

/**
 * Class RestrictedViewHelperResolverTest
 * @covers \In2code\Powermail\Fluid\ViewHelper\RestrictedViewHelperResolver
 * @covers \In2code\Powermail\Fluid\ViewHelper\BlockedViewHelper
 */
class RestrictedViewHelperResolverTest extends UnitTestCase
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        // blocked ViewHelpers are logged, which needs a usable log configuration
        TestingHelper::setDefaultConstants();
    }

    /**
     * @param string[] $allowedViewHelpers
     * @return RestrictedViewHelperResolver
     */
    protected function getSubject(array $allowedViewHelpers): RestrictedViewHelperResolver
    {
        $decorated = new ViewHelperResolver();
        $decorated->setNamespaces(['f' => ['TYPO3Fluid\\Fluid\\ViewHelpers']]);

        return new RestrictedViewHelperResolver($decorated, ViewHelperPolicy::fromIdentifiers($allowedViewHelpers));
    }

    /**
     * @return void
     * @test
     */
    public function namespaceOfAnAllowedViewHelperIsValid()
    {
        $subject = $this->getSubject(['f:format.printf']);

        self::assertTrue($subject->isNamespaceValid('f'));
        self::assertFalse($subject->isNamespaceIgnored('f'));
    }

    /**
     * @return void
     * @test
     */
    public function unknownNamespaceIsIgnoredInsteadOfInvalid()
    {
        $subject = $this->getSubject(['f:format.printf']);

        self::assertFalse($subject->isNamespaceValid('i'));
        self::assertTrue($subject->isNamespaceIgnored('i'));
    }

    /**
     * With an empty allowlist not a single namespace is relevant, so every tag stays literal text
     *
     * @return void
     * @test
     */
    public function everyNamespaceIsIgnoredWithAnEmptyAllowlist()
    {
        $subject = $this->getSubject([]);

        self::assertTrue($subject->isNamespaceIgnored('f'));
        self::assertTrue($subject->isNamespaceIgnored('i'));
    }

    /**
     * "{namespace x=...}" and 'xmlns:x="..."' are applied through addNamespace()
     *
     * @return void
     * @test
     */
    public function addNamespaceDoesNotRegisterAnything()
    {
        $subject = $this->getSubject(['f:format.printf']);
        $subject->addNamespace('i', 'TYPO3\\CMS\\Install\\ViewHelpers');

        self::assertArrayNotHasKey('i', $subject->getNamespaces());
        self::assertTrue($subject->isNamespaceIgnored('i'));
    }

    /**
     * addNamespaces() is what a compiled template calls, and it routes through addNamespace()
     *
     * @return void
     * @test
     */
    public function addNamespacesDoesNotRegisterAnything()
    {
        $subject = $this->getSubject(['f:format.printf']);
        $subject->addNamespaces(['i' => ['TYPO3\\CMS\\Install\\ViewHelpers']]);

        self::assertArrayNotHasKey('i', $subject->getNamespaces());
        self::assertTrue($subject->isNamespaceIgnored('i'));
    }

    /**
     * @return void
     * @test
     */
    public function setNamespacesDoesNotRegisterAnything()
    {
        $subject = $this->getSubject(['f:format.printf']);
        $subject->setNamespaces(['i' => ['TYPO3\\CMS\\Install\\ViewHelpers']]);

        self::assertArrayNotHasKey('i', $subject->getNamespaces());
        self::assertTrue($subject->isNamespaceIgnored('i'));
    }

    /**
     * @return void
     * @test
     */
    public function allowedViewHelperResolvesToItsRealClass()
    {
        self::assertSame(
            PrintfViewHelper::class,
            $this->getSubject(['f:format.printf'])->resolveViewHelperClassName('f', 'format.printf')
        );
    }

    /**
     * @return void
     * @test
     */
    public function notAllowedViewHelperResolvesToTheBlockedViewHelper()
    {
        self::assertSame(
            BlockedViewHelper::class,
            $this->getSubject(['f:format.printf'])->resolveViewHelperClassName('f', 'format.raw')
        );
    }

    /**
     * An allowed name that does not exist must not abort the rendering of the whole value
     *
     * @return void
     * @test
     */
    public function allowedButUnresolvableViewHelperResolvesToTheBlockedViewHelper()
    {
        self::assertSame(
            BlockedViewHelper::class,
            $this->getSubject(['f:doesNotExist'])->resolveViewHelperClassName('f', 'doesNotExist')
        );
    }

    /**
     * @return void
     * @test
     */
    public function blockedViewHelperIsInstantiatedThroughTheDecoratedResolver()
    {
        self::assertInstanceOf(
            BlockedViewHelper::class,
            $this->getSubject([])->createViewHelperInstanceFromClassName(BlockedViewHelper::class)
        );
    }

    /**
     * @return void
     * @test
     */
    public function blockedViewHelperRendersNothing()
    {
        self::assertSame('', (new BlockedViewHelper())->render());
    }
}
