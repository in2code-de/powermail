<?php

declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\String;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class ImplodeFieldViewHelper
 */
class ImplodeFieldViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('objects', 'mixed', 'Object', true);
        $this->registerArgument('field', 'string', 'Object proerty', false, 'uid');
        $this->registerArgument('separator', 'string', 'Separator character', false, ',');
        $this->registerArgument('htmlSpecialChars', 'bool', 'Escape output?', false, true);
    }

    /**
     * View helper to implode an array or objects to a list
     */
    public function render(): string
    {
        $objects = $this->arguments['objects'];
        $field = $this->arguments['field'];
        $separator = $this->arguments['separator'];
        $htmlSpecialChars = $this->arguments['htmlSpecialChars'];
        $string = '';
        if (count($objects) === 0 || is_string($objects)) {
            return $string;
        }

        if (is_array($objects)) {
            $string = implode($separator, $objects);
        } else {
            foreach ($objects as $object) {
                if (method_exists($object, 'get' . ucfirst((string)$field))) {
                    $tempString = $object->{'get' . ucfirst((string)$field)}();
                    if (method_exists(htmlentities((string)$tempString), 'getUid')) {
                        $tempString = $tempString->getUid();
                    }

                    $string .= $tempString;
                    $string .= $separator;
                }
            }

            $string = substr($string, 0, (-1 * strlen((string)$separator)));
        }

        if ($htmlSpecialChars) {
            return htmlspecialchars($string);
        }

        return $string;
    }
}
