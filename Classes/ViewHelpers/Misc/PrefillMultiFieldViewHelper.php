<?php
declare(strict_types = 1);
namespace In2code\Powermail\ViewHelpers\Misc;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Service\ConfigurationService;
use In2code\Powermail\Signal\SignalTrait;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\FrontendUtility;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\SessionUtility;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

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
     * @var ContentObjectRenderer
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
     * @throws Exception
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     */
    public function render(): bool
    {
        /** @var Field $field */
        $field = $this->arguments['field'];
        $mail = $this->arguments['mail'];
        $cycle = $this->arguments['cycle'];
        $default = (bool)$this->arguments['default'];
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
        return $this->isSelected();
    }

    /**
     * Build selected value
     *
     * @return void
     */
    protected function buildSelectedValue(): void
    {
        $selected = $this->isFromMail();
        if (!$selected) {
            $selected = $this->isFromMarker();
        }
        if (!$selected) {
            $selected = $this->isFromRawMarker();
        }
        if (!$selected) {
            $selected = $this->isFromFieldUid();
        }
        if (!$selected) {
            $selected = $this->isFromOldPowermailFieldUid();
        }
        if (!$selected) {
            $selected = $this->isFromFrontendUser();
        }
        if (!$selected) {
            $selected = $this->isFromPrefillValue();
        }
        if (!$selected) {
            $selected = $this->isFromTypoScriptContentObject();
        }
        if (!$selected) {
            $selected = $this->isFromTypoScriptRaw();
        }
        if (!$selected) {
            $selected = $this->isFromSession();
        }
        $this->setSelected($selected);
    }

    /**
     * Check if value from existing answer (for edit view)
     * is set to current value
     *
     * @return bool
     */
    protected function isFromMail(): bool
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
    protected function isFromMarker(): bool
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
    protected function isFromRawMarker(): bool
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
    protected function isFromFieldUid(): bool
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
    protected function isFromOldPowermailFieldUid(): bool
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
    protected function isFromFrontendUser(): bool
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
    protected function isFromPrefillValue(): bool
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
    protected function isFromTypoScriptRaw(): bool
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
     * @return bool
     */
    protected function isFromSession(): bool
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
    protected function isFromTypoScriptContentObject(): bool
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
    public function isSelected(): bool
    {
        return $this->selected;
    }

    /**
     * @param bool $selected
     * @return PrefillMultiFieldViewHelper
     */
    public function setSelected(bool $selected): PrefillMultiFieldViewHelper
    {
        $this->selected = $selected;
        return $this;
    }

    /**
     * @return Field
     */
    public function getField(): Field
    {
        return $this->field;
    }

    /**
     * @param Field $field
     * @return PrefillMultiFieldViewHelper
     */
    public function setField(Field $field): PrefillMultiFieldViewHelper
    {
        $this->field = $field;
        return $this;
    }

    /**
     * @return Mail
     */
    public function getMail(): ?Mail
    {
        return $this->mail;
    }

    /**
     * @param Mail $mail
     * @return PrefillMultiFieldViewHelper
     */
    public function setMail(Mail $mail = null): PrefillMultiFieldViewHelper
    {
        $this->mail = $mail;
        return $this;
    }

    /**
     * @return string
     */
    public function getMarker(): string
    {
        return $this->marker;
    }

    /**
     * @param string $marker
     * @return PrefillMultiFieldViewHelper
     */
    public function setMarker(string $marker): PrefillMultiFieldViewHelper
    {
        $this->marker = $marker;
        return $this;
    }

    /**
     * @param array $options
     * @return PrefillMultiFieldViewHelper
     */
    public function setOptions(array $options): PrefillMultiFieldViewHelper
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @param int $index
     * @return PrefillMultiFieldViewHelper
     */
    public function setIndex(int $index): PrefillMultiFieldViewHelper
    {
        $this->index = $index;
        return $this;
    }

    /**
     * @return bool
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    protected function isCachedForm(): bool
    {
        return ConfigurationUtility::isEnableCachingActive()
            && ObjectUtility::getTyposcriptFrontendController()->no_cache !== true;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function initialize(): void
    {
        $this->variables = FrontendUtility::getArguments();
        $this->contentObjectRenderer = ObjectUtility::getObjectManager()->get(ContentObjectRenderer::class);
        $configurationService = ObjectUtility::getObjectManager()->get(ConfigurationService::class);
        $this->configuration = $configurationService->getTypoScriptConfiguration();
    }
}
