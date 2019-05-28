<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Misc;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Service\ConfigurationService;
use In2code\Powermail\Signal\SignalTrait;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\FrontendUtility;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\SessionUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class PrefillMultiFieldViewHelper
 */
class PrefillMultiFieldViewHelper extends AbstractViewHelper
{
    use SignalTrait;

    /**
     * @var bool
     */
    protected $selected = false;

    /**
     * @var array
     */
    protected $configuration;

    /**
     * @var array
     */
    protected $variables;

    /**
     * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
     */
    protected $contentObjectRenderer;

    /**
     * @var Field $field
     */
    protected $field = null;

    /**
     * @var Mail $mail
     */
    protected $mail = null;

    /**
     * @var string
     */
    protected $marker = '';

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var int
     */
    protected $index = 0;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('field', Field::class, 'Field', true);
        $this->registerArgument('mail', Mail::class, 'Mail for a prefill in edit action', false, null);
        $this->registerArgument('cycle', 'int', 'Cycle Number (1,2,3...) - if filled checkbox/radiobutton', false, 0);
        $this->registerArgument('default', 'string', 'Fallback value', false, null);
    }

    /**
     * Prefill fields from type
     *        check
     *        radio
     *
     * @return bool
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     */
    public function render()
    {
        /** @var Field $field */
        $field = $this->arguments['field'];
        $mail = $this->arguments['mail'];
        $cycle = $this->arguments['cycle'];
        $default = $this->arguments['default'];
        // @extensionScannerIgnoreLine False positive alert in TYPO3 9.5
        $this
            ->setMarker($field->getMarker())
            ->setField($field)
            ->setMail($mail)
            ->setSelected($default)
            ->setOptions($field->getModifiedSettings())
            ->setIndex($cycle - 1);

        // stop prefilling for cached forms to prevent wrong values
        if (!$this->isCachedForm()) {
            $this->buildSelectedValue();
        }
        $this->signalDispatch(__CLASS__, __FUNCTION__, [$field, $mail, $cycle, $default, $this]);
        return $this->getSelected();
    }

    /**
     * Build selected value
     *
     * @return void
     */
    protected function buildSelectedValue()
    {
        $selected = $this->getFromMail();
        if (!$selected) {
            $selected = $this->getFromMarker();
        }
        if (!$selected) {
            $selected = $this->getFromRawMarker();
        }
        if (!$selected) {
            $selected = $this->getFromFieldUid();
        }
        if (!$selected) {
            $selected = $this->getFromOldPowermailFieldUid();
        }
        if (!$selected) {
            $selected = $this->getFromFrontendUser();
        }
        if (!$selected) {
            $selected = $this->getFromPrefillValue();
        }
        if (!$selected) {
            $selected = $this->getFromTypoScriptContentObject();
        }
        if (!$selected) {
            $selected = $this->getFromTypoScriptRaw();
        }
        if (!$selected) {
            $selected = $this->getFromSession();
        }
        $this->setSelected($selected);
    }

    /**
     * Check if value from existing answer (for edit view)
     * is set to current value
     *
     * @return bool
     */
    protected function getFromMail()
    {
        $selected = false;
        if ($this->getMail() !== null && $this->getMail()->getAnswers()) {
            foreach ($this->getMail()->getAnswers() as $answer) {
                if ($answer->getField() === $this->getField()) {
                    $values = $answer->getValue();
                    foreach ((array)$values as $value) {
                        if ($value === $this->options[$this->index]['value'] ||
                            $value === $this->options[$this->index]['label']
                        ) {
                            return true;
                        }
                    }
                }
            }
        }
        return $selected;
    }

    /**
     * Check if value from GET/POST param
     * &tx_powermail_pi1[field][marker][index] or
     * &tx_powermail_pi1[field][marker]
     * is current value
     *
     * @return bool
     */
    protected function getFromMarker()
    {
        $selected = false;
        if (isset($this->variables['field'][$this->getMarker()])) {
            if (is_array($this->variables['field'][$this->getMarker()])) {
                foreach (array_keys($this->variables['field'][$this->getMarker()]) as $key) {
                    if ($this->variables['field'][$this->getMarker()][$key] === $this->options[$this->index]['value'] ||
                        $this->variables['field'][$this->getMarker()][$key] === $this->options[$this->index]['label']
                    ) {
                        return true;
                    }
                }
            } else {
                if ($this->variables['field'][$this->getMarker()] === $this->options[$this->index]['value'] ||
                    $this->variables['field'][$this->getMarker()] === $this->options[$this->index]['label']
                ) {
                    return true;
                }
            }
        }
        return $selected;
    }

    /**
     * Check if value from GET/POST param
     * &tx_powermail_pi1[marker][index] or
     * &tx_powermail_pi1[marker]
     * is current value
     *
     * @return bool
     */
    protected function getFromRawMarker()
    {
        $selected = false;
        if (isset($this->variables[$this->getMarker()])) {
            if (is_array($this->variables[$this->getMarker()])) {
                foreach (array_keys($this->variables[$this->getMarker()]) as $key) {
                    if ($this->variables[$this->getMarker()][$key] === $this->options[$this->index]['value'] ||
                        $this->variables[$this->getMarker()][$key] === $this->options[$this->index]['label']
                    ) {
                        return true;
                    }
                }
            } else {
                if ($this->variables[$this->getMarker()] === $this->options[$this->index]['value'] ||
                    $this->variables[$this->getMarker()] === $this->options[$this->index]['label']
                ) {
                    return true;
                }
            }
        }
        return $selected;
    }

    /**
     * Check if value from GET/POST param
     * &tx_powermail_pi1[field][123][index] or
     * &tx_powermail_pi1[field][123]
     * is current value
     *
     * @return bool
     */
    protected function getFromFieldUid()
    {
        $selected = false;
        $fieldUid = $this->getField()->getUid();
        if (isset($this->variables['field'][$fieldUid])) {
            if (is_array($this->variables['field'][$fieldUid])) {
                foreach (array_keys($this->variables['field'][$fieldUid]) as $key) {
                    if ($this->variables['field'][$fieldUid][$key] === $this->options[$this->index]['value'] ||
                        $this->variables['field'][$fieldUid][$key] === $this->options[$this->index]['label']
                    ) {
                        return true;
                    }
                }
            } else {
                if ($this->variables['field'][$fieldUid] === $this->options[$this->index]['value'] ||
                    $this->variables['field'][$fieldUid] === $this->options[$this->index]['label']
                ) {
                    return true;
                }
            }
        }
        return $selected;
    }

    /**
     * Check if value from GET/POST param &tx_powermail_pi1[uid123]
     * is set to current value
     *
     * @return bool
     */
    protected function getFromOldPowermailFieldUid()
    {
        $selected = false;
        if (isset($this->variables['uid' . $this->getField()->getUid()])) {
            if ($this->variables['uid' . $this->getField()->getUid()] === $this->options[$this->index]['value'] ||
                $this->variables['uid' . $this->getField()->getUid()] === $this->options[$this->index]['label']
            ) {
                $selected = true;
            }
        }
        return $selected;
    }

    /**
     * Get value from current logged in Frontend User
     *
     * @return bool
     */
    protected function getFromFrontendUser()
    {
        $selected = false;
        $feUserValue = $this->getField()->getFeuserValue();
        if ($feUserValue && FrontendUtility::isLoggedInFrontendUser()) {
            if (FrontendUtility::getPropertyFromLoggedInFrontendUser($feUserValue)
                === $this->options[$this->index]['value'] ||
                FrontendUtility::getPropertyFromLoggedInFrontendUser($feUserValue)
                === $this->options[$this->index]['label']
            ) {
                $selected = true;
            }
        }
        return $selected;
    }

    /**
     * Get value from prefill value from field record
     *
     * @return bool
     */
    protected function getFromPrefillValue()
    {
        $selected = false;
        if ($this->options[$this->index]['selected']) {
            $selected = true;
        }
        return $selected;
    }

    /**
     * Get from raw TypoScript settings like
     *        plugin.tx_powermail.settings.setup.prefill.marker = red
     *
     * @return bool
     */
    protected function getFromTypoScriptRaw()
    {
        $selected = false;
        if (!empty($this->configuration['prefill.'][$this->getMarker()])) {
            if ($this->configuration['prefill.'][$this->getMarker()] === $this->options[$this->index]['value'] ||
                $this->configuration['prefill.'][$this->getMarker()] === $this->options[$this->index]['label']
            ) {
                $selected = true;
            }
        }
        return $selected;
    }

    /**
     * Get value from session if defined in TypoScript
     *
     * @return string
     */
    protected function getFromSession()
    {
        $selected = false;
        $sessionValues = SessionUtility::getSessionValuesForPrefill($this->configuration);
        if (!empty($sessionValues) && count($sessionValues)) {
            foreach ($sessionValues as $marker => $valueInSession) {
                if ($this->getMarker() === $marker) {
                    if ($valueInSession === $this->options[$this->index]['value'] ||
                        $valueInSession === $this->options[$this->index]['label']
                    ) {
                        $selected = true;
                    }
                }
            }
        }
        return $selected;
    }

    /**
     * Get from TypoScript content object like
     *
     *        # direct value
     *        plugin.tx_powermail.settings.setup.prefill.marker = TEXT
     *        plugin.tx_powermail.settings.setup.prefill.marker.value = red
     *
     *        # multiple value
     *        plugin.tx_powermail.settings.setup.prefill.marker.0 = TEXT
     *        plugin.tx_powermail.settings.setup.prefill.marker.0.value = red
     *
     * @return bool
     */
    protected function getFromTypoScriptContentObject()
    {
        $selected = false;
        if (isset($this->configuration['prefill.'][$this->getMarker() . '.']) &&
            is_array($this->configuration['prefill.'][$this->getMarker() . '.'])
        ) {
            $this->contentObjectRenderer->start(ObjectAccess::getGettableProperties($this->getField()));
            // Multivalue
            if (isset($this->configuration['prefill.'][$this->getMarker() . '.']['0'])) {
                foreach (array_keys($this->configuration['prefill.'][$this->getMarker() . '.']) as $key) {
                    if (stristr((string)$key, '.')) {
                        continue;
                    }
                    $prefill = $this->contentObjectRenderer->cObjGetSingle(
                        $this->configuration['prefill.'][$this->getMarker() . '.'][$key],
                        $this->configuration['prefill.'][$this->getMarker() . '.'][$key . '.']
                    );
                    if ($prefill === $this->options[$this->index]['value'] ||
                        $prefill === $this->options[$this->index]['label']
                    ) {
                        $selected = true;
                    }
                }
            } else {
                // Single value
                $prefill = $this->contentObjectRenderer->cObjGetSingle(
                    $this->configuration['prefill.'][$this->getMarker()],
                    $this->configuration['prefill.'][$this->getMarker() . '.']
                );
                if ($prefill === $this->options[$this->index]['value'] ||
                    $prefill === $this->options[$this->index]['label']
                ) {
                    $selected = true;
                }
            }
        }
        return $selected;
    }

    /**
     * @return bool
     */
    public function getSelected()
    {
        return $this->selected;
    }

    /**
     * @param bool $selected
     * @return PrefillMultiFieldViewHelper
     */
    public function setSelected($selected)
    {
        $this->selected = $selected;
        return $this;
    }

    /**
     * @return Field
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param Field $field
     * @return PrefillMultiFieldViewHelper
     */
    public function setField($field)
    {
        $this->field = $field;
        return $this;
    }

    /**
     * @return Mail
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * @param Mail $mail
     * @return PrefillMultiFieldViewHelper
     */
    public function setMail($mail)
    {
        $this->mail = $mail;
        return $this;
    }

    /**
     * @return string
     */
    public function getMarker()
    {
        return $this->marker;
    }

    /**
     * @param string $marker
     * @return PrefillMultiFieldViewHelper
     */
    public function setMarker($marker)
    {
        $this->marker = $marker;
        return $this;
    }

    /**
     * @param array $options
     * @return PrefillMultiFieldViewHelper
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @param int $index
     * @return PrefillMultiFieldViewHelper
     */
    public function setIndex($index)
    {
        $this->index = $index;
        return $this;
    }

    /**
     * Check if form is cached
     *
     * @return bool
     */
    protected function isCachedForm()
    {
        return ConfigurationUtility::isEnableCachingActive()
            && ObjectUtility::getTyposcriptFrontendController()->no_cache !== true;
    }

    /**
     * Init
     *
     * @return void
     */
    public function initialize()
    {
        $this->variables = GeneralUtility::_GP('tx_powermail_pi1');
        $this->contentObjectRenderer = ObjectUtility::getObjectManager()->get(ContentObjectRenderer::class);
        $configurationService = ObjectUtility::getObjectManager()->get(ConfigurationService::class);
        $this->configuration = $configurationService->getTypoScriptConfiguration();
    }
}
