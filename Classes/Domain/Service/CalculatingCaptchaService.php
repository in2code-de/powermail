<?php
declare(strict_types=1);
namespace In2code\Powermail\Domain\Service;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Utility\BasicFileUtility;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\MathematicUtility;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\SessionUtility;
use In2code\Powermail\Utility\StringUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class CalculatingCaptchaService
 */
class CalculatingCaptchaService
{

    /**
     * TypoScript with captcha configuration
     *
     * @var array
     */
    protected $configuration;

    /**
     * Operators
     *
     * @var array
     */
    protected $operators = [
        '+',
        '-',
        'x',
        ':'
    ];

    /**
     * Prefix for captcha image filename
     * [prefix][fieldUid].png
     *
     * @var string
     */
    protected $imageFilenamePrefix = 'Captcha%d.png';

    /**
     * Path for captcha images
     *
     * @var string
     */
    protected $imagePath = 'typo3temp/tx_powermail/';

    /**
     * Relative path and filename of captcha image
     *
     * @var string
     */
    protected $pathAndFilename = '';

    /**
     * Background image path and filename
     *        e.g. EXT:ext/filename.png
     *
     * @var string
     */
    protected $backgroundImage = '';

    /**
     * Font path and filename
     *        e.g. EXT:ext/font.ttf
     *
     * @var string
     */
    protected $fontPathAndFilename = '';

    /**
     * Turn off exceptions for testing
     *
     * @var bool
     */
    protected $test = false;

    /**
     * Initialize
     *
     * @param bool $test
     */
    public function __construct($test = false)
    {
        $this->test = $test;
        $this->setConfiguration();
    }

    /**
     * Render Link to Captcha Image
     *
     * @param Field $field
     * @return string|null
     * @throws \Exception
     */
    public function render(Field $field)
    {
        ConfigurationUtility::testGdExtension();
        if ($this->configurationExists()) {
            $this
                ->setBackgroundImage($this->configuration['image'])
                ->setFontPathAndFilename($this->configuration['font'])
                ->setPathAndFilename($field);
            BasicFileUtility::createFolderIfNotExists($this->getImagePath(true));
            $captchaValue = $this->getStringAndResultForCaptcha();
            SessionUtility::setCaptchaSession($captchaValue['result'], $field->getUid());
            return $this->createImage($captchaValue['string']);
        }
        return null;
    }

    /**
     * Check if given code is correct
     *
     * @param string $code String to compare
     * @param Field $field String to compare
     * @param bool $clearSession
     * @return boolean
     */
    public function validCode($code, $field, $clearSession = true)
    {
        if ((int)$code > 0 && (int)$code === SessionUtility::getCaptchaSession($field->getUid())) {
            if ($clearSession) {
                SessionUtility::setCaptchaSession('', $field->getUid());
            }
            return true;
        }
        return false;
    }

    /**
     * Create Image File
     *
     * @param string $content
     * @param bool $addHash
     * @return string Image URI
     * @throws \Exception
     */
    protected function createImage($content, $addHash = true)
    {
        $imageResource = imagecreatefrompng($this->getBackgroundImage());
        imagettftext(
            $imageResource,
            (float)$this->configuration['textSize'],
            $this->getFontAngleForCaptcha(),
            $this->getHorizontalDistanceForCaptcha(),
            $this->getVerticalDistanceForCaptcha(),
            $this->getColorForCaptcha($imageResource),
            $this->getFontPathAndFilename(),
            $content
        );
        if (imagepng($imageResource, $this->getPathAndFilename(true)) === false) {
            throw new \DomainException('Captcha image could not be generated under ' . $this->getPathAndFilename());
        }
        imagedestroy($imageResource);
        return $this->getPathAndFilename(false, $addHash);
    }

    /**
     * Get color from configuration
     *
     * @param resource $imageResource
     * @return int color identifier
     */
    protected function getColorForCaptcha($imageResource)
    {
        $colorRgb = sscanf($this->configuration['textColor'], '#%2x%2x%2x');
        return imagecolorallocate($imageResource, $colorRgb[0], $colorRgb[1], $colorRgb[2]);
    }

    /**
     * Get random font angle from configuration
     *
     * @return int
     */
    protected function getFontAngleForCaptcha()
    {
        $angles = GeneralUtility::trimExplode(',', $this->configuration['textAngle'], true);
        return mt_rand((int)$angles[0], (int)$angles[1]);
    }

    /**
     * Get random horizontal distance from configuration
     *
     * @return int
     */
    protected function getHorizontalDistanceForCaptcha()
    {
        $distances = GeneralUtility::trimExplode(',', $this->configuration['distanceHor'], true);
        return mt_rand((int)$distances[0], (int)$distances[1]);
    }

    /**
     * Get random vertical distance from configuration
     *
     * @return int
     */
    protected function getVerticalDistanceForCaptcha()
    {
        $distances = GeneralUtility::trimExplode(',', $this->configuration['distanceVer'], true);
        return mt_rand((int)$distances[0], (int)$distances[1]);
    }

    /**
     * Create Random String for Captcha Image
     *
     * @param int $maxNumber
     * @param int $maxOperatorNumber choose which operators are allowed
     * @return array
     *        'result' => 3
     *        'string' => '1+2'
     */
    protected function getStringAndResultForCaptcha($maxNumber = 15, $maxOperatorNumber = 1)
    {
        $result = $number1 = $number2 = 0;
        $operator = $this->operators[mt_rand(0, $maxOperatorNumber)];
        for ($i = 0; $i < 100; $i++) {
            $number1 = mt_rand(0, $maxNumber);
            $number2 = mt_rand(0, $maxNumber);
            $result = MathematicUtility::mathematicOperation($number1, $number2, $operator);
            if ($result > 0) {
                break;
            }
        }

        // Force values for testing
        if (!empty($this->configuration['forceValue'])) {
            preg_match_all(
                '~(\d+)\s*([+|\-|:|x])\s*(\d+)~',
                $this->configuration['forceValue'],
                $matches
            );
            $number1 = $matches[1][0];
            $number2 = $matches[3][0];
            $operator = $matches[2][0];
            $result = MathematicUtility::mathematicOperation($number1, $number2, $operator);
        }

        return [
            'result' => $result,
            'string' => $number1 . ' ' . $operator . ' ' . $number2
        ];
    }

    /**
     * @return CalculatingCaptchaService
     */
    public function setConfiguration()
    {
        if (!$this->test) {
            $configurationService = ObjectUtility::getObjectManager()->get(ConfigurationService::class);
            $allConfiguration = $configurationService->getTypoScriptConfiguration();
            $this->configuration = $allConfiguration['captcha.']['default.'];
        }
        return $this;
    }

    /**
     * @param bool $absolute
     * @return string
     */
    public function getImagePath($absolute = false)
    {
        $currentImagePath = $this->imagePath;
        if ($absolute) {
            $currentImagePath = GeneralUtility::getFileAbsFileName($currentImagePath);
        }
        return $currentImagePath;
    }

    /**
     * @param string $imagePath
     * @return CalculatingCaptchaService
     */
    public function setImagePath($imagePath)
    {
        $this->imagePath = $imagePath;
        return $this;
    }

    /**
     * Create relative filename for captcha image
     *
     * @param Field $field
     * @return CalculatingCaptchaService
     */
    public function setPathAndFilename(Field $field)
    {
        $this->pathAndFilename = $this->imagePath . sprintf($this->imageFilenamePrefix, $field->getUid());
        return $this;
    }

    /**
     * Get path and filename
     *
     * @param bool $absolute
     * @param bool $addHash
     * @return string
     */
    public function getPathAndFilename($absolute = false, $addHash = false)
    {
        $pathFilename = $this->pathAndFilename;
        if ($absolute) {
            $pathFilename = GeneralUtility::getFileAbsFileName($pathFilename);
        }
        if ($addHash) {
            $pathFilename .= '?hash=' . StringUtility::getRandomString(8);
        }
        return $pathFilename;
    }

    /**
     * @return string
     */
    public function getBackgroundImage(): string
    {
        return GeneralUtility::getFileAbsFileName($this->backgroundImage);
    }

    /**
     * Get background image path and filename
     *
     * @param string $backgroundImage e.g. EXT:ext/filename.png
     * @return CalculatingCaptchaService
     * @throws \Exception
     */
    public function setBackgroundImage($backgroundImage)
    {
        $this->backgroundImage = $backgroundImage;
        if (!$this->test && !is_file($this->getBackgroundImage())) {
            throw new \InvalidArgumentException(
                'No captcha background image found - please check your TypoScript configuration',
                1540051516
            );
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getFontPathAndFilename(): string
    {
        return GeneralUtility::getFileAbsFileName($this->fontPathAndFilename);
    }

    /**
     * @param string $fontPathAndFilename
     * @return CalculatingCaptchaService
     * @throws \Exception
     */
    public function setFontPathAndFilename($fontPathAndFilename)
    {
        $this->fontPathAndFilename = $fontPathAndFilename;
        if (!$this->test && !is_file($this->getFontPathAndFilename())) {
            throw new \InvalidArgumentException(
                'No captcha truetype font found - please check your TypoScript configuration',
                1540051511
            );
        }
        return $this;
    }

    /**
     * @return bool
     */
    protected function configurationExists()
    {
        return !empty($this->configuration);
    }
}
