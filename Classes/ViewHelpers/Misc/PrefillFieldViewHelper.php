<?php
namespace In2code\Powermail\ViewHelpers\Misc;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Signal\SignalTrait;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\FrontendUtility;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\SessionUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Prefill a field
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class PrefillFieldViewHelper extends AbstractViewHelper
{
    use SignalTrait;

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     * @inject
     */
    protected $configurationManager;

    /**
     * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
     */
    protected $contentObject;

    /**
     * @var string|array
     */
    protected $value = null;

    /**
     * @var array
     */
    protected $settings;

    /**
     * @var array
     */
    protected $piVars;

    /**
     * Field
     *
     * @var Field $field
     */
    protected $field = null;

    /**
     * Mail
     *
     * @var Mail $mail
     */
    protected $mail = null;

    /**
     * Markername
     *
     * @var string
     */
    protected $marker = '';

    /**
     * Prefill fields from type
     *        input
     *        textarea
     *        select
     *        country
     *        file
     *        hidden
     *        date
     *        location
     *
     * @param Field $field
     * @param Mail $mail To prefill in Edit Action
     * @param string $default Fallback value
     * @return string|array Prefill field
     */
    public function render(Field $field, Mail $mail = null, $default = '')
    {
        $this->setMarker($field->getMarker())
            ->setField($field)
            ->setMail($mail)
            ->setValue($default);
        if (!$this->isCachedForm()) {
            $this->buildValue();
        }
        $this->signalDispatch(__CLASS__, __FUNCTION__, [$field, $mail, $default, $this]);
        return $this->getValue();
    }

    /**
     * @return void
     */
    protected function buildValue()
    {
        $value = '';
        $value = $this->getFromMail($value);
        $value = $this->getFromMarker($value);
        $value = $this->getFromRawMarker($value);
        $value = $this->getFromFieldUid($value);
        $value = $this->getFromOldPowermailFieldUid($value);
        $value = $this->getFromFrontendUser($value);
        $value = $this->getFromPrefillValue($value);
        $value = $this->getFromTypoScriptContentObject($value);
        $value = $this->getFromTypoScriptRaw($value);
        $value = $this->getFromSession($value);
        $this->setValue($value);
    }

    /**
     * Get value from existing answer for edit view
     *
     * @param string $value
     * @return string|array
     */
    protected function getFromMail($value)
    {
        if (empty($value) && $this->getMail() !== null && $this->getMail()->getAnswers()) {
            foreach ($this->getMail()->getAnswers() as $answer) {
                if ($answer->getField() === $this->getField()) {
                    return $answer->getValue();
                }
            }
        }
        return $value;
    }

    /**
     * Get value from GET/POST param &tx_powermail_pi1[field][marker]
     *
     * @param string $value
     * @return string
     */
    protected function getFromMarker($value)
    {
        if (empty($value) && isset($this->piVars['field'][$this->getMarker()])) {
            $value = $this->piVars['field'][$this->getMarker()];
        }
        return $value;
    }

    /**
     * Get value from GET/POST param &tx_powermail_pi1[marker]
     *
     * @param string $value
     * @return string
     */
    protected function getFromRawMarker($value)
    {
        if (empty($value) && isset($this->piVars[$this->getMarker()])) {
            $value = $this->piVars[$this->getMarker()];
        }
        return $value;
    }

    /**
     * Get value from GET/POST param &tx_powermail_pi1[field][123]
     *
     * @param string $value
     * @return string
     */
    protected function getFromFieldUid($value)
    {
        if (empty($value) && isset($this->piVars['field'][$this->getField()->getUid()])) {
            $value = $this->piVars['field'][$this->getField()->getUid()];
        }
        return $value;
    }

    /**
     * Get value from GET/POST param &tx_powermail_pi1[uid123]
     *
     * @param string $value
     * @return string
     */
    protected function getFromOldPowermailFieldUid($value)
    {
        if (empty($value) && isset($this->piVars['uid' . $this->getField()->getUid()])) {
            $value = $this->piVars['uid' . $this->getField()->getUid()];
        }
        return $value;
    }

    /**
     * Get value from current logged in Frontend User
     *
     * @param string $value
     * @return string
     */
    protected function getFromFrontendUser($value)
    {
        if (empty($value) && $this->getField()->getFeuserValue()) {
            $value = FrontendUtility::getPropertyFromLoggedInFrontendUser($this->getField()->getFeuserValue());
        }
        return $value;
    }

    /**
     * Get value from prefill value from field record
     *
     * @param string $value
     * @return string
     */
    protected function getFromPrefillValue($value)
    {
        if (empty($value) && $this->getField()->getPrefillValue()) {
            $value = $this->getField()->getPrefillValue();
        }
        return $value;
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
     * @param string $value
     * @return array|string
     */
    protected function getFromTypoScriptContentObject($value)
    {
        if (
            empty($value) &&
            isset($this->settings['prefill.'][$this->getMarker() . '.']) &&
            is_array($this->settings['prefill.'][$this->getMarker() . '.'])
        ) {
            $this->contentObject->start(ObjectAccess::getGettableProperties($this->getField()));
            // Multivalue
            if (isset($this->settings['prefill.'][$this->getMarker() . '.']['0'])) {
                $value = [];
                foreach (array_keys($this->settings['prefill.'][$this->getMarker() . '.']) as $key) {
                    if (stristr($key, '.')) {
                        continue;
                    }
                    $value[] = $this->contentObject->cObjGetSingle(
                        $this->settings['prefill.'][$this->getMarker() . '.'][$key],
                        $this->settings['prefill.'][$this->getMarker() . '.'][$key . '.']
                    );
                }
            } else {
                // Single value
                $value = $this->contentObject->cObjGetSingle(
                    $this->settings['prefill.'][$this->getMarker()],
                    $this->settings['prefill.'][$this->getMarker() . '.']
                );
            }
        }
        return $value;
    }

    /**
     * Get from raw TypoScript settings like
     *        plugin.tx_powermail.settings.setup.prefill.marker = red
     *
     * @param string $value
     * @return string
     */
    protected function getFromTypoScriptRaw($value)
    {
        if (
            empty($value) &&
            !empty($this->settings['prefill.'][$this->getMarker()]) &&
            empty($this->settings['prefill.'][$this->getMarker() . '.'])
        ) {
            $value = $this->settings['prefill.'][$this->getMarker()];
        }
        return $value;
    }

    /**
     * Get value from session if defined in TypoScript
     *
     * @param string $value
     * @return string
     */
    protected function getFromSession($value)
    {
        if (empty($value)) {
            $sessionValues = SessionUtility::getSessionValuesForPrefill($this->settings);
            if (!empty($sessionValues) && count($sessionValues)) {
                foreach ($sessionValues as $marker => $valueInSession) {
                    if ($this->getMarker() === $marker) {
                        return $valueInSession;
                    }
                }
            }
        }
        return $value;
    }

    /**
     * @return string|array
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string|array $value
     * @return PrefillFieldViewHelper
     */
    public function setValue($value)
    {
        $this->value = $value;
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
     * @return PrefillFieldViewHelper
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
     * @return PrefillFieldViewHelper
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
     * @return PrefillFieldViewHelper
     */
    public function setMarker($marker)
    {
        $this->marker = $marker;
        return $this;
    }

    /**
     * Check if form is cached (stop prefilling for cached forms to prevent wrong or outdated values)
     *
     * @return bool
     */
    protected function isCachedForm()
    {
        return ConfigurationUtility::isEnableCachingActive()
            && ObjectUtility::getTyposcriptFrontendController()->no_cache !== true;
    }

    /**
     * @return void
     */
    public function initialize()
    {
        $this->piVars = GeneralUtility::_GP('tx_powermail_pi1');
        $this->contentObject = $this->objectManager->get(ContentObjectRenderer::class);
        $typoScriptSetup = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
        );
        $this->settings = $typoScriptSetup['plugin.']['tx_powermail.']['settings.']['setup.'];
    }
}
