<?php
namespace In2code\Powermail\Domain\Service;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Utility\BasicFileUtility;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\SessionUtility;
use In2code\Powermail\Utility\StringUtility;
use TYPO3\CMS\Core\TypoScript\TemplateService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Service\TypoScriptService;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Class CalculatingCaptchaService
 */
class CalculatingCaptchaService
{

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     * @inject
     */
    protected $configurationManager;

    /**
     * TypoScript
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
        $this->testGdExtension();
        if ($this->configurationExists()) {
            $this
                ->setBackgroundImage($this->configuration['captcha.']['default.']['image'])
                ->setFontPathAndFilename($this->configuration['captcha.']['default.']['font'])
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
        $imageResource = imagecreatefrompng($this->getBackgroundImage(true));
        imagettftext(
            $imageResource,
            $this->configuration['captcha.']['default.']['textSize'],
            $this->getFontAngleForCaptcha(),
            $this->getHorizontalDistanceForCaptcha(),
            $this->getVerticalDistanceForCaptcha(),
            $this->getColorForCaptcha($imageResource),
            $this->getFontPathAndFilename(true),
            $content
        );
        if (imagepng($imageResource, $this->getPathAndFilename(true)) === false) {
            throw new \Exception('Captcha image could not be generated under ' . $this->getPathAndFilename());
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
        $colorRgb = sscanf($this->configuration['captcha.']['default.']['textColor'], '#%2x%2x%2x');
        return imagecolorallocate($imageResource, $colorRgb[0], $colorRgb[1], $colorRgb[2]);
    }

    /**
     * Get random font angle from configuration
     *
     * @return int
     */
    protected function getFontAngleForCaptcha()
    {
        $angles = GeneralUtility::trimExplode(',', $this->configuration['captcha.']['default.']['textAngle'], true);
        return mt_rand($angles[0], $angles[1]);
    }

    /**
     * Get random horizontal distance from configuration
     *
     * @return int
     */
    protected function getHorizontalDistanceForCaptcha()
    {
        $distances = GeneralUtility::trimExplode(
            ',',
            $this->configuration['captcha.']['default.']['distanceHor'],
            true
        );
        return mt_rand($distances[0], $distances[1]);
    }

    /**
     * Get random vertical distance from configuration
     *
     * @return int
     */
    protected function getVerticalDistanceForCaptcha()
    {
        $distances = GeneralUtility::trimExplode(
            ',',
            $this->configuration['captcha.']['default.']['distanceVer'],
            true
        );
        return mt_rand($distances[0], $distances[1]);
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
            $result = $this->mathematicOperation($number1, $number2, $operator);
            if ($result > 0) {
                break;
            }
        }

        // Force values for testing
        if (!empty($this->configuration['captcha.']['default.']['forceValue'])) {
            preg_match_all(
                '~(\d+)\s*([+|\-|:|x])\s*(\d+)~',
                $this->configuration['captcha.']['default.']['forceValue'],
                $matches
            );
            $number1 = $matches[1][0];
            $number2 = $matches[3][0];
            $operator = $matches[2][0];
            $result = $this->mathematicOperation($number1, $number2, $operator);
        }

        return [
            'result' => $result,
            'string' => $number1 . ' ' . $operator . ' ' . $number2
        ];
    }

    /**
     * Mathematic operation
     *
     * @param int $number1
     * @param int $number2
     * @param string $operator +|-|x|:
     * @return int
     */
    protected function mathematicOperation($number1, $number2, $operator = '+')
    {
        switch ($operator) {
            case '-':
                $result = $number1 - $number2;
                break;
            case 'x':
                $result = $number1 * $number2;
                break;
            case ':':
                $result = $number1 / $number2;
                break;
            case '+':
            default:
                $result = $number1 + $number2;
        }
        return $result;
    }

    /**
     * @param string $string
     * @param bool $absolute
     * @return string
     */
    protected function getFilename($string, $absolute = false)
    {
        $string = str_replace('EXT:', 'typo3conf/ext/', $string);
        /** @var TemplateService $templateService */
        $templateService = ObjectUtility::getObjectManager()->get(TemplateService::class);
        $fileName = $templateService->getFileName($string);
        if ($absolute) {
            $fileName = GeneralUtility::getFileAbsFileName($fileName);
        }
        return $fileName;
    }

    /**
     * @return CalculatingCaptchaService
     */
    public function setConfiguration()
    {
        if (!$this->test) {
            /** @var ConfigurationManager $configurationManager */
            $configurationManager = ObjectUtility::getObjectManager()->get(ConfigurationManager::class);
            $typoScriptSetup = $configurationManager->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS
            );
            $typoScriptService = ObjectUtility::getObjectManager()->get(TypoScriptService::class);
            $this->configuration = $typoScriptService->convertPlainArrayToTypoScriptArray(
                (array)$typoScriptSetup['setup']
            );
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @param bool $absolute
     * @return string
     */
    public function getImagePath($absolute = false)
    {
        $imagePath = $this->imagePath;
        if ($absolute) {
            $imagePath = GeneralUtility::getFileAbsFileName($imagePath);
        }
        return $imagePath;
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
        $pathAndFilename = $this->pathAndFilename;
        if ($absolute) {
            $pathAndFilename = GeneralUtility::getFileAbsFileName($pathAndFilename);
        }
        if ($addHash) {
            $pathAndFilename .= '?hash=' . StringUtility::getRandomString(8);
        }
        return $pathAndFilename;
    }

    /**
     * @param bool $absolute
     * @return string
     */
    public function getBackgroundImage($absolute = false)
    {
        return $this->getFilename($this->backgroundImage, $absolute);
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
        if (!$this->test && !is_file($this->getBackgroundImage(true))) {
            throw new \Exception('No captcha background image found - please check your TypoScript configuration');
        }
        return $this;
    }

    /**
     * @param bool $absolute
     * @return string
     */
    public function getFontPathAndFilename($absolute = false)
    {
        return $this->getFilename($this->fontPathAndFilename, $absolute);
    }

    /**
     * @param string $fontPathAndFilename
     * @return CalculatingCaptchaService
     * @throws \Exception
     */
    public function setFontPathAndFilename($fontPathAndFilename)
    {
        $this->fontPathAndFilename = $fontPathAndFilename;
        if (!$this->test && !is_file($this->getFontPathAndFilename(true))) {
            throw new \Exception('No captcha truetype font found - please check your TypoScript configuration');
        }
        return $this;
    }

    /**
     * @return bool
     */
    protected function configurationExists()
    {
        return !empty($this->configuration['captcha.']['default.']);
    }

    /**
     * Check if gdlib is loaded on this server
     *
     * @throws \Exception
     */
    protected function testGdExtension()
    {
        if (!extension_loaded('gd')) {
            throw new \Exception('PHP extension gd not loaded.');
        }
    }
}
