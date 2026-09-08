<?php
namespace In2code\Powermail\Tests\Unit\Fluid;

use In2code\Powermail\Fluid\Parser\NeutralizeModifiersTemplateProcessor;
use In2code\Powermail\Fluid\ViewHelper\RestrictedViewHelperInvoker;
use In2code\Powermail\Fluid\ViewHelper\RestrictedViewHelperResolver;
use In2code\Powermail\Fluid\ViewHelper\ViewHelperPolicy;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3Fluid\Fluid\Core\Parser\TemplateProcessor\NamespaceDetectionTemplateProcessor;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContext;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperResolver;
use TYPO3Fluid\Fluid\View\TemplateView;

/**
 * Class RestrictedRenderingTest
 *
 * Renders strings through the real Fluid TemplateParser with the powermail restriction installed.
 * This is the regression test for the ViewHelper injection: a value that a website visitor submits
 * ended up as a Fluid template source, which allowed executing any ViewHelper.
 *
 * @coversDefaultClass \In2code\Powermail\Fluid\ViewHelper\RestrictedViewHelperResolver
 */
class RestrictedRenderingTest extends UnitTestCase
{
    /**
     * Render a source string through a real Fluid TemplateView with the powermail restriction.
     *
     * @param string $source
     * @param string[] $allowedViewHelpers
     * @param array $variables
     * @return string
     */
    protected function render(string $source, array $allowedViewHelpers = [], array $variables = []): string
    {
        $policy = ViewHelperPolicy::fromIdentifiers($allowedViewHelpers);

        $decorated = new ViewHelperResolver();
        $decorated->setNamespaces(['f' => ['TYPO3Fluid\\Fluid\\ViewHelpers']]);

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
     * A ViewHelper in the template source that is not on the allowlist does not execute; f:format.raw
     * would switch escaping off, so the submitted markup must stay escaped.
     *
     * @return void
     * @test
     * @covers ::resolveViewHelperClassName
     * @covers ::isNamespaceValid
     * @covers ::isNamespaceIgnored
     * @covers \In2code\Powermail\Fluid\ViewHelper\BlockedViewHelper
     * @covers \In2code\Powermail\Fluid\ViewHelper\RestrictedViewHelperInvoker
     */
    public function blockedViewHelperInSourceIsNeutralized()
    {
        $result = $this->render(
            '<f:format.raw>{value}</f:format.raw>',
            [],
            ['value' => '<script>alert(1)</script>']
        );

        $this->assertNotContains('<script>alert(1)</script>', $result);
    }

    /**
     * An allowlisted ViewHelper keeps working - f:format.raw returns its child unescaped.
     *
     * @return void
     * @test
     * @covers ::resolveViewHelperClassName
     * @covers ::createViewHelperInstanceFromClassName
     */
    public function allowedViewHelperStillExecutes()
    {
        $result = $this->render(
            '<f:format.raw>{value}</f:format.raw>',
            ['f:format.raw'],
            ['value' => '<b>x</b>']
        );

        $this->assertSame('<b>x</b>', $result);
    }

    /**
     * A "{namespace f=...}" import inside the parsed string must not re-alias an allowed identifier
     * to a class of the attacker's choice - f:format.raw stays the real Fluid RawViewHelper.
     *
     * @return void
     * @test
     * @covers ::addNamespace
     * @covers ::resolveViewHelperClassName
     */
    public function namespaceImportCannotReAliasAnAllowedIdentifier()
    {
        $result = $this->render(
            '{namespace f=TYPO3\\CMS\\Install\\ViewHelpers}<f:format.raw>{value}</f:format.raw>',
            ['f:format.raw'],
            ['value' => '<b>x</b>']
        );

        $this->assertSame('<b>x</b>', $result);
    }

    /**
     * An injected namespace can never resolve, so its ViewHelper is never turned into a node and
     * stays inert text - it is not executed even when nested inside an allowed ViewHelper.
     *
     * @return void
     * @test
     * @covers ::isNamespaceValid
     * @covers ::isNamespaceIgnored
     * @covers ::resolveViewHelperClassName
     */
    public function injectedNamespaceDoesNotResolveInsideAnAllowedViewHelper()
    {
        $result = $this->render(
            '{namespace i=TYPO3\\CMS\\Install\\ViewHelpers}<f:format.raw><i:phpInfo/></f:format.raw>',
            ['f:format.raw']
        );

        // the tag survives verbatim - a resolved ViewHelper would have been consumed, not left as text
        $this->assertContains('<i:phpInfo/>', $result);
        // and it never executed: the real phpInfo ViewHelper would render an actual phpinfo() page
        $this->assertNotContains('PHP Version', $result);
    }

    /**
     * Dataprovider markerHandlingReturnsString()
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
            'marker value containing fluid is not parsed again' => [
                'Hello {firstname}',
                ['firstname' => "{f:format.raw(value:'x')}"],
                'Hello {f:format.raw(value:&#039;x&#039;)}',
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
     * @param string $expected
     * @dataProvider markerHandlingReturnsStringDataProvider
     * @return void
     * @test
     * @covers ::resolveViewHelperClassName
     */
    public function markerHandlingReturnsString(string $source, array $variables, string $expected)
    {
        $this->assertSame($expected, $this->render($source, [], $variables));
    }

    /**
     * "{escaping=false}" would switch output escaping off for the whole string; the neutralizing
     * processor removes it, so submitted markup stays escaped.
     *
     * @return void
     * @test
     * @covers \In2code\Powermail\Fluid\Parser\NeutralizeModifiersTemplateProcessor
     * @covers ::resolveViewHelperClassName
     */
    public function escapingModifierIsNeutralized()
    {
        $result = $this->render('{escaping=false}{value}', [], ['value' => '<b>x</b>']);

        $this->assertNotContains('<b>x</b>', $result);
        $this->assertContains('&lt;b&gt;x&lt;/b&gt;', $result);
    }
}
