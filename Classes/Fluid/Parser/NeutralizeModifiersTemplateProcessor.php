<?php
declare(strict_types = 1);
namespace In2code\Powermail\Fluid\Parser;

use TYPO3Fluid\Fluid\Core\Parser\TemplateProcessor\EscapingModifierTemplateProcessor;
use TYPO3Fluid\Fluid\Core\Parser\TemplateProcessorInterface;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Class NeutralizeModifiersTemplateProcessor
 *
 * Removes Fluid's inline parser modifiers from a string without applying them.
 *
 * This replaces two of Fluid's own pre-processors for strings that powermail parses, because both
 * give the author of the string control over the rendering engine itself:
 *
 * - "{escaping=false}" (EscapingModifierTemplateProcessor) switches output escaping off for the
 *   whole string, which would let submitted values reach the output unescaped.
 * - "{parsing off}" (PassthroughSourceModifierTemplateProcessor) makes AbstractTemplateView::render()
 *   return the source verbatim, bypassing both variable interpolation and escaping.
 */
class NeutralizeModifiersTemplateProcessor implements TemplateProcessorInterface
{
    /**
     * @param RenderingContextInterface $renderingContext
     * @return void
     */
    public function setRenderingContext(RenderingContextInterface $renderingContext)
    {
    }

    /**
     * @param string $templateSource
     * @return string
     */
    public function preProcessSource($templateSource)
    {
        if (strpos($templateSource, '{escaping') !== false) {
            $templateSource = (string)preg_replace(
                EscapingModifierTemplateProcessor::SCAN_PATTERN_ESCAPINGMODIFIER,
                '',
                $templateSource
            );
        }

        return str_replace(['{parsing off}', '{parsing on}'], '', $templateSource);
    }
}
