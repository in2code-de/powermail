<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Validation;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Service\CalculatingCaptchaService;
use In2code\Powermail\Domain\Service\ConfigurationService;
use In2code\Powermail\Utility\BasicFileUtility;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\StringUtility;
use In2code\Powermail\Utility\TypoScriptUtility;
use ThinkopenAt\Captcha\Utility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * Class CaptchaViewHelper
 */
class CaptchaViewHelper extends AbstractTagBasedViewHelper
{

    /**
     * @var null|string
     */
    protected $error = null;

    /**
     * Constructor
     *
     * @api
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('field', Field::class, 'powermail field', true);
        $this->registerArgument('alt', 'string', 'alt attribute');
        $this->registerArgument('class', 'string', 'class attribute');
        $this->registerArgument('id', 'string', 'id attribute');
    }

    /**
     * Render image or error message if image could not be created
     *
     * @return string
     */
    public function render()
    {
        try {
            return $this->getImage();
        } catch (\Exception $exception) {
            return $this->getErrorMessage($exception);
        }
    }

    /**
     * @return string
     */
    protected function getImage()
    {
        $this->tag->setTagName('img');
        $this->tag->addAttribute('src', $this->getImageSource($this->arguments['field']));
        $this->tag->addAttribute('alt', $this->arguments['alt']);
        $this->tag->addAttribute('class', $this->arguments['class']);
        $this->tag->addAttribute('id', $this->arguments['id']);
        return $this->tag->render();
    }

    /**
     * @param \Exception $exception
     * @return string
     */
    protected function getErrorMessage(\Exception $exception)
    {
        $this->tag->setTagName('p');
        $this->tag->addAttribute('class', 'bg-danger');
        $this->tag->forceClosingTag(true);
        $this->tag->setContent($exception->getMessage());
        return $this->tag->render();
    }

    /**
     * @param Field $field
     * @return string image URL
     */
    protected function getImageSource(Field $field)
    {
        $settings = $this->getSettings();
        switch (TypoScriptUtility::getCaptchaExtensionFromSettings($settings)) {
            case 'captcha':
                $captchaVersion = ExtensionManagementUtility::getExtensionVersion('captcha');
                $image = BasicFileUtility::getRelativeFolder(ExtensionManagementUtility::extPath('captcha'))
                    . 'captcha/captcha.php';
                if (VersionNumberUtility::convertVersionNumberToInteger($captchaVersion) >= 2000000) {
                    $imageTag = Utility::makeCaptcha($field->getUid());
                    return StringUtility::getSrcFromImageTag($imageTag);
                }
                break;

            default:
                $captchaService = ObjectUtility::getObjectManager()->get(CalculatingCaptchaService::class);
                $image = $captchaService->render($field);
        }
        return $image;
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        $configurationService = ObjectUtility::getObjectManager()->get(ConfigurationService::class);
        return $configurationService->getTypoScriptSettings();
    }
}
