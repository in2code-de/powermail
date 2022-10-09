<?php

declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Misc;

use Doctrine\DBAL\DBALException;
use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Service\ConfigurationService;
use In2code\Powermail\Events\PrefillFieldViewHelperEvent;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\FrontendUtility;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\SessionUtility;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class PrefillFieldViewHelper
 */
class PrefillFieldViewHelper extends AbstractViewHelper
{
    /**
     * @var ContentObjectRenderer
     */
    protected ContentObjectRenderer $contentObject;

    /**
     * @var string|array|null
     */
    protected $value = null;

    /**
     * @var array
     */
    protected array $configuration;

    /**
     * @var array
     */
    protected array $variables;

    /**
     * @var ?Field $field
     */
    protected ?Field $field = null;

    /**
     * @var ?Mail $mail
     */
    protected ?Mail $mail = null;

    /**
     * @var string
     */
    protected string $marker = '';

    /**
     * @var EventDispatcherInterface
     */
    private EventDispatcherInterface $eventDispatcher;

    /**
     * Constructor
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('field', Field::class, 'Field', true);
        $this->registerArgument('mail', Mail::class, 'Mail for a prefill in edit action', false, null);
        $this->registerArgument('default', 'string', 'Fallback value', false, null);
    }

    /**
     * Prefill fields of type
     *        input
     *        textarea
     *        select
     *        country
     *        file
     *        hidden
     *        date
     *        location
     *
     * @return string|array Prefill field
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws DBALException
     */
    public function render()
    {
        /** @var Field $field */
        $field = $this->arguments['field'];
        $mail = $this->arguments['mail'];
        $default = $this->arguments['default'];
        $this->setMarker($field->getMarker())
            ->setField($field)
            ->setMail($mail)
            ->setValue($default);
        if (!$this->isCachedForm()) {
            $this->buildValue();
        }

        /** @var PrefillFieldViewHelperEvent $event */
        $event = $this->eventDispatcher->dispatch(
            GeneralUtility::makeInstance(
                PrefillFieldViewHelperEvent::class,
                $this->getValue(),
                $field,
                $mail,
                $default
            )
        );
        return $event->getValue();
    }

    /**
     * @return void
     */
    protected function buildValue(): void
    {
        $value = '';
        $value = $this->getFromMail($value);
        $value = $this->getFromMarker($value);
        $value = $this->getFromRawMarker($value);
        $value = $this->getFromFrontendUser($value);
        $value = $this->getFromPrefillValue($value);
        $value = $this->getFromTypoScriptContentObject($value);
        $value = $this->getFromTypoScriptRaw($value);
        $value = $this->getFromSession($value);
        $value = $this->getFromDefaultValue($value);
        $this->setValue($value);
    }

    /**
     * Get value from existing answer for edit view
     *
     * @param string $value
     * @return string|array
     */
    protected function getFromMail(string $value)
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
     * @param string|array $value
     * @return string|array
     */
    protected function getFromMarker($value)
    {
        if (empty($value) && isset($this->variables['field'][$this->getMarker()])) {
            $value = $this->variables['field'][$this->getMarker()];
        }
        return $value;
    }

    /**
     * Get value from GET/POST param &tx_powermail_pi1[marker]
     *
     * @param string|array $value
     * @return string|array
     */
    protected function getFromRawMarker($value)
    {
        if (empty($value) && isset($this->variables[$this->getMarker()])) {
            $value = $this->variables[$this->getMarker()];
        }
        return $value;
    }

    /**
     * Get value from current logged in Frontend User
     *
     * @param string|array $value
     * @return string|array
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
     * @param string|array $value
     * @return string|array
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
     * @param string|array $value
     * @return array|string
     */
    protected function getFromTypoScriptContentObject($value)
    {
        if (empty($value) &&
            isset($this->configuration['prefill.'][$this->getMarker() . '.']) &&
            is_array($this->configuration['prefill.'][$this->getMarker() . '.'])
        ) {
            $this->contentObject->start(ObjectAccess::getGettableProperties($this->getField()));
            // Multivalue
            if (isset($this->configuration['prefill.'][$this->getMarker() . '.']['0'])) {
                $value = [];
                foreach (array_keys($this->configuration['prefill.'][$this->getMarker() . '.']) as $key) {
                    if (stristr((string)$key, '.')) {
                        continue;
                    }
                    $value[] = $this->contentObject->cObjGetSingle(
                        $this->configuration['prefill.'][$this->getMarker() . '.'][$key],
                        $this->configuration['prefill.'][$this->getMarker() . '.'][$key . '.']
                    );
                }
            } else {
                // Single value
                $value = $this->contentObject->cObjGetSingle(
                    $this->configuration['prefill.'][$this->getMarker()],
                    $this->configuration['prefill.'][$this->getMarker() . '.']
                );
            }
        }
        return $value;
    }

    /**
     * Get from raw TypoScript settings like
     *        plugin.tx_powermail.settings.setup.prefill.marker = red
     *
     * @param string|array $value
     * @return string|array
     */
    protected function getFromTypoScriptRaw($value)
    {
        if (empty($value) &&
            !empty($this->configuration['prefill.'][$this->getMarker()]) &&
            empty($this->configuration['prefill.'][$this->getMarker() . '.'])
        ) {
            $value = $this->configuration['prefill.'][$this->getMarker()];
        }
        return $value;
    }

    /**
     * Get value from session if defined in TypoScript
     *
     * @param string|array $value
     * @return string|array
     */
    protected function getFromSession($value)
    {
        if (empty($value)) {
            $sessionValues = SessionUtility::getSessionValuesForPrefill($this->configuration);
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
     * Get value from default ViewHelper argument
     *
     * @param string|array $value
     * @return string|array
     */
    protected function getFromDefaultValue($value)
    {
        if (empty($value)) {
            $value = (string)$this->getValue();
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
    public function setValue($value): PrefillFieldViewHelper
    {
        $this->value = $value;
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
     * @return PrefillFieldViewHelper
     */
    public function setField(Field $field): PrefillFieldViewHelper
    {
        $this->field = $field;
        return $this;
    }

    /**
     * @return ?Mail
     */
    public function getMail(): ?Mail
    {
        return $this->mail;
    }

    /**
     * @param ?Mail $mail
     * @return PrefillFieldViewHelper
     */
    public function setMail(Mail $mail = null): PrefillFieldViewHelper
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
     * @return PrefillFieldViewHelper
     */
    public function setMarker(string $marker): PrefillFieldViewHelper
    {
        $this->marker = $marker;
        return $this;
    }

    /**
     * Check if form is cached (stop prefilling for cached forms to prevent wrong or outdated values)
     *
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
     */
    public function initialize()
    {
        $this->variables = FrontendUtility::getArguments();
        $this->contentObject = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        $configurationService = GeneralUtility::makeInstance(ConfigurationService::class);
        $this->configuration = $configurationService->getTypoScriptConfiguration();
    }
}
