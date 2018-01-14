<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Reporting;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class GetLabelsForChartsViewHelper
 */
class GetLabelsForChartsViewHelper extends AbstractViewHelper
{

    /**
     * Not allowed sign
     */
    protected $notAllowedSign = '"';

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('answers', 'array', 'Grouped Answers', true);
        $this->registerArgument('fieldUidOrKey', 'string', 'fieldUidOrKey', true);
        $this->registerArgument('separator', 'string', 'separator', false, '|');
        $this->registerArgument('crop', 'int', 'crop', false, 15);
        $this->registerArgument('append', 'string', 'append', false, '...');
        $this->registerArgument('urlEncode', 'bool', 'urlEncode', false, true);
    }

    /**
     * Get labels string for charts JavaScript like "label1|label2|label3"
     *
     * @return string
     */
    public function render(): string
    {
        $string = '';
        $answers = $this->arguments['answers'];
        $fieldUidOrKey = $this->arguments['fieldUidOrKey'];
        $separator = $this->arguments['separator'];
        $crop = $this->arguments['crop'];
        $append = $this->arguments['append'];
        if (empty($answers[$fieldUidOrKey]) || !is_array($answers[$fieldUidOrKey])) {
            return $string;
        }

        foreach (array_keys($answers[$fieldUidOrKey]) as $value) {
            $value = str_replace([$this->notAllowedSign, $separator], '', $value);
            $value = htmlspecialchars($value);
            if (strlen($value) > $crop) {
                $value = substr($value, 0, $crop) . $append;
            }
            $string .= $value;
            $string .= $separator;
        }
        $string = rtrim($string, $separator);

        if ($this->arguments['urlEncode']) {
            $string = urlencode($string);
        }
        return $string;
    }
}
