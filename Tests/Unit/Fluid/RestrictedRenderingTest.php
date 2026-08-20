<?php

declare(strict_types=1);
namespace In2code\Powermail\Tests\Unit\Fluid;

use In2code\Powermail\Fluid\Parser\NeutralizeModifiersTemplateProcessor;
use In2code\Powermail\Fluid\ViewHelper\RestrictedViewHelperInvoker;
use In2code\Powermail\Fluid\ViewHelper\RestrictedViewHelperResolver;
use In2code\Powermail\Fluid\ViewHelper\ViewHelperPolicy;
use In2code\Powermail\Tests\Helper\TestingHelper;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3Fluid\Fluid\Core\Parser\TemplateProcessor\NamespaceDetectionTemplateProcessor;
use TYPO3Fluid\Fluid\Core\Parser\UnknownNamespaceException;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContext;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperResolver;
use TYPO3Fluid\Fluid\View\TemplateView;

/**
 * Class RestrictedRenderingTest
 *
 * Renders strings through the real Fluid TemplateParser with the powermail restriction installed.
 *
 * This is the regression test for the ViewHelper injection: a value that a website visitor submits
 * ends up as a Fluid template source, which allowed executing any ViewHelper.
 */
class RestrictedRenderingTest extends UnitTestCase
{
    /**
     * The payload from the security report
     */
    private const PAYLOAD = '{namespace i=TYPO3\CMS\Install\ViewHelpers}'
        . '<f:asset.script identifier="pi"><i:phpInfo/></f:asset.script>';

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        TestingHelper::setDefaultConstants();
    }

    /**
     * Render a string the way RestrictedStringRenderer does, but on a plain Fluid view so that no
     * TYPO3 request or container is needed.
     *
     * @param string $source
     * @param string[] $allowedViewHelpers
     * @param array $variables
     * @return string
     */
    protected function render(string $source, array $allowedViewHelpers = [], array $variables = []): string
    {
        $policy = ViewHelperPolicy::fromIdentifiers($allowedViewHelpers);

        // a resolver with the namespaces a TYPO3 installation registers for "f"
        $decorated = new ViewHelperResolver();
        $decorated->setNamespaces([
            'f' => ['TYPO3Fluid\\Fluid\\ViewHelpers', 'TYPO3\\CMS\\Fluid\\ViewHelpers'],
        ]);

        $renderingContext = new RenderingContext();
        $resolver = new RestrictedViewHelperResolver($decorated, $policy);
        $renderingContext->setViewHelperResolver($resolver);
        $renderingContext->setViewHelperInvoker(new RestrictedViewHelperInvoker($policy, $resolver));
        $renderingContext->setTemplateProcessors([
            new NeutralizeModifiersTemplateProcessor(),
            new NamespaceDetectionTemplateProcessor(),
        ]);
        $renderingContext->setControllerName('PowermailRestrictedString');
        $renderingContext->setControllerAction('Parse' . $policy->getFingerprint());
        $renderingContext->getTemplatePaths()->setTemplateSource($source);

        $view = new TemplateView($renderingContext);
        $view->assignMultiple($variables);

        return $view->render();
    }

    /**
     * @return void
     * @test
     * @covers \In2code\Powermail\Fluid\ViewHelper\RestrictedViewHelperResolver
     * @covers \In2code\Powermail\Fluid\ViewHelper\BlockedViewHelper
     */
    public function payloadFromTheSecurityReportDoesNotExecuteAnyViewHelper()
    {
        $result = $this->render(self::PAYLOAD, ['f:cObject']);

        self::assertStringNotContainsStringIgnoringCase('phpinfo', $result);
        self::assertStringNotContainsStringIgnoringCase('php version', $result);
        self::assertStringNotContainsString('<script', $result);
    }

    /**
     * Data Provider for injectedNamespaceDoesNotResolveInsideAnAllowedViewHelper()
     *
     * @return array
     */
    public function injectedNamespaceDoesNotResolveInsideAnAllowedViewHelperDataProvider()
    {
        return [
            // f:format.nl2br renders its children unescaped ($escapeChildren = false), so the inert
            // tag is passed through verbatim
            'view helper that does not escape its children' => [
                '<f:format.nl2br><i:phpInfo/></f:format.nl2br>',
                'f:format.nl2br',
                '<i:phpInfo/>',
            ],
            'view helper that escapes its children' => [
                '<f:format.case mode="lower"><i:phpInfo/></f:format.case>',
                'f:format.case',
                '&lt;i:phpinfo/&gt;',
            ],
            'no wrapping view helper at all' => [
                '<i:phpInfo/>',
                'f:cObject',
                '<i:phpInfo/>',
            ],
        ];
    }

    /**
     * The injected namespace stays unknown even inside an allowed ViewHelper, so "i:phpInfo" can
     * never resolve - allowing a ViewHelper does not open a door for the ones nested in it.
     *
     * Different from Fluid 4/5 (powermail 13/14), Fluid 2 keeps the unresolvable tag as literal text
     * without escaping it. That is inert either way - the assertions below pin down both the actual
     * output and the property that matters: the ViewHelper is never executed.
     *
     * @param string $source
     * @param string $allowedViewHelper
     * @param string $expectedResult
     * @dataProvider injectedNamespaceDoesNotResolveInsideAnAllowedViewHelperDataProvider
     * @return void
     * @test
     * @covers \In2code\Powermail\Fluid\ViewHelper\RestrictedViewHelperResolver
     */
    public function injectedNamespaceDoesNotResolveInsideAnAllowedViewHelper(
        $source,
        $allowedViewHelper,
        $expectedResult
    ) {
        $result = $this->render(
            '{namespace i=TYPO3\CMS\Install\ViewHelpers}' . $source,
            [$allowedViewHelper]
        );

        self::assertSame($expectedResult, $result);
        self::assertStringNotContainsStringIgnoringCase('php version', $result);
    }

    /**
     * @return void
     * @test
     * @covers \In2code\Powermail\Fluid\ViewHelper\RestrictedViewHelperResolver
     */
    public function namespaceImportCannotReAliasAnAllowedIdentifier()
    {
        $result = $this->render(
            '{namespace f=TYPO3\CMS\Install\ViewHelpers}<f:phpInfo/>',
            ['f:phpInfo']
        );

        self::assertStringNotContainsStringIgnoringCase('phpinfo', $result);
        self::assertStringNotContainsStringIgnoringCase('php version', $result);
    }

    /**
     * Data Provider for markerHandlingReturnsString()
     *
     * @return array
     */
    public function markerHandlingReturnsStringDataProvider()
    {
        return [
            'plain marker' => [
                'Hello {firstname}',
                ['firstname' => 'Max'],
                'Hello Max',
            ],
            'marker value is escaped' => [
                'Hello {firstname}',
                ['firstname' => '<b>Max</b>'],
                'Hello &lt;b&gt;Max&lt;/b&gt;',
            ],
            'marker value containing fluid syntax is not parsed again' => [
                'Hello {firstname}',
                ['firstname' => '{f:cObject(typoscriptObjectPath:\'lib.evil\')}'],
                'Hello {f:cObject(typoscriptObjectPath:&#039;lib.evil&#039;)}',
            ],
            'unknown marker stays empty' => [
                'Hello {unknown}',
                [],
                'Hello ',
            ],
        ];
    }

    /**
     * @param string $source
     * @param array $variables
     * @param string $expectedResult
     * @dataProvider markerHandlingReturnsStringDataProvider
     * @return void
     * @test
     * @covers \In2code\Powermail\Fluid\ViewHelper\RestrictedViewHelperResolver
     */
    public function markerHandlingReturnsString($source, $variables, $expectedResult)
    {
        self::assertSame($expectedResult, $this->render($source, ['f:cObject'], $variables));
    }

    /**
     * Different from Fluid 4/5 (powermail 13/14): Fluid 2 cannot ignore a namespace in inline
     * notation, it always raises an UnknownNamespaceException. RestrictedStringRenderer catches it
     * and returns the value with the Fluid syntax removed, so nothing is executed either way.
     *
     * @return void
     * @test
     * @covers \In2code\Powermail\Fluid\ViewHelper\RestrictedViewHelperResolver
     */
    public function unknownNamespaceInInlineNotationThrowsInsteadOfExecuting()
    {
        $this->expectException(UnknownNamespaceException::class);

        $this->render('{i:phpInfo()}', ['f:cObject']);
    }

    /**
     * @return void
     * @test
     * @covers \In2code\Powermail\Fluid\ViewHelper\RestrictedViewHelperResolver
     * @covers \In2code\Powermail\Fluid\ViewHelper\BlockedViewHelper
     */
    public function blockedViewHelperOfAKnownNamespaceRendersNothing()
    {
        self::assertSame('', $this->render('<f:asset.script identifier="x">alert(1)</f:asset.script>', ['f:cObject']));
    }

    /**
     * @return void
     * @test
     * @covers \In2code\Powermail\Fluid\ViewHelper\RestrictedViewHelperResolver
     */
    public function allowedViewHelperIsStillExecuted()
    {
        self::assertSame(
            'a<br />' . "\n" . 'b',
            $this->render('{firstname -> f:format.nl2br()}', ['f:format.nl2br'], ['firstname' => "a\nb"])
        );
    }

    /**
     * @return void
     * @test
     * @covers \In2code\Powermail\Fluid\ViewHelper\RestrictedViewHelperResolver
     */
    public function notAllowedViewHelperIsNotExecutedInInlineNotation()
    {
        self::assertSame(
            '',
            $this->render('{firstname -> f:format.raw()}', ['f:cObject'], ['firstname' => '<script>alert(1)</script>'])
        );
    }

    /**
     * Data Provider for conditionViewHelperReturnsString()
     *
     * @return array
     */
    public function conditionViewHelperReturnsStringDataProvider()
    {
        return [
            'f:if is dropped when it is not allowed' => [
                ['f:cObject'],
                '',
            ],
            'f:if works when it is allowed' => [
                ['f:if', 'f:then', 'f:else'],
                'yes',
            ],
        ];
    }

    /**
     * @param array $allowedViewHelpers
     * @param string $expectedResult
     * @dataProvider conditionViewHelperReturnsStringDataProvider
     * @return void
     * @test
     * @covers \In2code\Powermail\Fluid\ViewHelper\RestrictedViewHelperResolver
     */
    public function conditionViewHelperReturnsString($allowedViewHelpers, $expectedResult)
    {
        self::assertSame(
            $expectedResult,
            $this->render('<f:if condition="1">yes</f:if>', $allowedViewHelpers)
        );
    }

    /**
     * "{escaping=false}" would switch output escaping off for the whole string
     *
     * @return void
     * @test
     * @covers \In2code\Powermail\Fluid\Parser\NeutralizeModifiersTemplateProcessor
     */
    public function escapingModifierIsStrippedWithoutEffect()
    {
        self::assertSame(
            'Hello &lt;b&gt;Max&lt;/b&gt;',
            $this->render('{escaping=false}Hello {firstname}', ['f:cObject'], ['firstname' => '<b>Max</b>'])
        );
    }

    /**
     * "{parsing off}" would make Fluid return the source verbatim, skipping marker replacement and
     * escaping altogether
     *
     * @return void
     * @test
     * @covers \In2code\Powermail\Fluid\Parser\NeutralizeModifiersTemplateProcessor
     */
    public function parsingModifierIsStrippedWithoutEffect()
    {
        self::assertSame(
            'Hello &lt;b&gt;Max&lt;/b&gt;',
            $this->render('{parsing off}Hello {firstname}', ['f:cObject'], ['firstname' => '<b>Max</b>'])
        );
    }

    /**
     * @return void
     * @test
     * @covers \In2code\Powermail\Fluid\ViewHelper\RestrictedViewHelperResolver
     */
    public function localNamespaceDeclarationIsRemovedFromTheOutput()
    {
        self::assertSame('x', $this->render('{namespace i=TYPO3\CMS\Install\ViewHelpers}x', ['f:cObject']));
    }
}
