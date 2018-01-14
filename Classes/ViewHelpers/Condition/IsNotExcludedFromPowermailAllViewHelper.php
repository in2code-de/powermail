<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Condition;

use In2code\Powermail\Domain\Model\Answer;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class IsNotExcludedFromPowermailAllViewHelper
 */
class IsNotExcludedFromPowermailAllViewHelper extends AbstractViewHelper
{

    /**
     * @var array
     */
    protected $typeToTypoScriptType = [
        'createAction' => 'submitPage',
        'confirmationAction' => 'confirmationPage',
        'sender' => 'senderMail',
        'receiver' => 'receiverMail',
        'optin' => 'optinMail'
    ];

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('answer', Answer::class, 'Answer', true);
        $this->registerArgument('type', 'string', 'Type of answer like "creatAction" or "sender", etc...', true);
        $this->registerArgument('settings', 'array', 'TypoScript settings', false, []);
    }

    /**
     * View helper check if value should be returned or not
     *
     * @return bool
     */
    public function render(): bool
    {
        /** @var Answer $answer */
        $answer = $this->arguments['answer'];
        $type = $this->arguments['type'];
        $settings = $this->arguments['settings'];

        // excludeFromFieldTypes
        if ($answer->getField() &&
            in_array(
                $answer->getField()->getType(),
                $this->getExcludedValues($type, $settings, 'excludeFromFieldTypes')
            )
        ) {
            return false;
        }

        // excludeFromMarkerNames
        if ($answer->getField() &&
            in_array(
                $answer->getField()->getMarker(),
                $this->getExcludedValues($type, $settings, 'excludeFromMarkerNames')
            )
        ) {
            return false;
        }

        return true;
    }

    /**
     * Return markers from TypoScript
     *        plugin.tx_powermail.settings.setup.excludeFromPowermailAllMarker {
     *            submitPage.excludeFromMarkerNames = marker1, marker2
     *            submitPage.excludeFromFieldTypes = marker1, marker2
     *        }
     *
     * @param string $type
     * @param array $settings
     * @param string $configurationType
     * @return array
     */
    protected function getExcludedValues($type, $settings, $configurationType = 'excludeFromFieldTypes'): array
    {
        if (!empty($settings['excludeFromPowermailAllMarker'][$this->typeToTypoScriptType[$type]][$configurationType])
        ) {
            return GeneralUtility::trimExplode(
                ',',
                $settings['excludeFromPowermailAllMarker'][$this->typeToTypoScriptType[$type]][$configurationType],
                true
            );
        }
        return [];
    }
}
