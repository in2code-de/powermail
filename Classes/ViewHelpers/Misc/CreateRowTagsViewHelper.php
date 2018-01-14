<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Misc;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Class CreateRowTagsViewHelper
 */
class CreateRowTagsViewHelper extends AbstractViewHelper
{

    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('columns', 'int', 'number of columns - 0 disables function', true);
        $this->registerArgument('iteration', 'array', 'Field iteration array', true);
        $this->registerArgument('tagName', 'string', 'Tag to render');
        $this->registerArgument('class', 'string', 'CSS class');
        $this->registerArgument('additionalAttributes', 'array', 'Any attributes to render');
    }

    /**
     * @return string
     */
    public function render()
    {
        return self::renderStatic(
            $this->arguments,
            $this->buildRenderChildrenClosure(),
            $this->renderingContext
        );
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $content = '';
        if ((int)$arguments['columns'] > 0) {
            $content .= self::getBeginningTag($arguments);
            $content .= $renderChildrenClosure();
            $content .= self::getEndingTag($arguments);
        } else {
            $content .= $renderChildrenClosure();
        }
        return $content;
    }

    /**
     * @param array $arguments
     * @return string
     */
    public static function getBeginningTag(array $arguments)
    {
        $content = '';
        if (self::shouldAddBeginningTag($arguments)) {
            $content = '<' . self::getTagName($arguments) . self::getAttributes($arguments) . '>';
        }
        return $content;
    }

    /**
     * @param array $arguments
     * @return string
     */
    public static function getEndingTag(array $arguments)
    {
        $content = '';
        if (self::shouldAddEndingTag($arguments)) {
            $content = '</' . self::getTagName($arguments) . '>';
        }
        return $content;
    }

    /**
     * @param array $arguments
     * @return string
     */
    protected static function getTagName(array $arguments)
    {
        $tagName = 'div';
        if (!empty($arguments['tagName'])) {
            $tagName = $arguments['tagName'];
        }
        return $tagName;
    }

    /**
     * @param array $arguments
     * @return string
     */
    protected static function getAttributes(array $arguments)
    {
        $attributes = '';
        if (!empty($arguments['additionalAttributes'])) {
            foreach ($arguments['additionalAttributes'] as $key => $value) {
                $attributes .= ' ' . $key . '="' . $value . '"';
            }
        }
        if (!empty($arguments['class'])) {
            $attributes .= ' class="' . $arguments['class'] . '"';
        }
        return $attributes;
    }

    /**
     * @param array $arguments
     * @return bool
     */
    protected static function shouldAddBeginningTag(array $arguments)
    {
        return $arguments['iteration']['isFirst'] === true
            || !(($arguments['iteration']['cycle'] - 1) % $arguments['columns']);
    }

    /**
     * @param array $arguments
     * @return bool
     */
    protected static function shouldAddEndingTag(array $arguments)
    {
        return $arguments['iteration']['isLast'] === true
            || !($arguments['iteration']['cycle'] % $arguments['columns']);
    }
}
