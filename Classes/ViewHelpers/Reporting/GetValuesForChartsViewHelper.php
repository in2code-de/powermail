<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Reporting;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class GetValuesForChartsViewHelper
 */
class GetValuesForChartsViewHelper extends AbstractViewHelper
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
        $this->registerArgument('urlEncode', 'bool', 'urlEncode', false, true);
    }

    /**
     * @return string "label1|label2|label3"
     */
    public function render(): string
    {
        $string = '';
        $answers = $this->arguments['answers'];
        $fieldUidOrKey = $this->arguments['fieldUidOrKey'];
        $separator = $this->arguments['separator'];
        if (empty($answers[$fieldUidOrKey]) || !is_array($answers[$fieldUidOrKey])) {
            return $string;
        }

        foreach ($answers[$fieldUidOrKey] as $amount) {
            $amount = str_replace([$this->notAllowedSign, $separator], '', $amount);
            $amount = htmlspecialchars($amount);
            $string .= $amount;
            $string .= $separator;
        }

        $string = substr($string, 0, -1);
        if ($this->arguments['urlEncode']) {
            $string = urlencode($string);
        }
        return $string;
    }
}
