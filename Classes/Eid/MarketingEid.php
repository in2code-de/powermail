<?php
declare(strict_types=1);
namespace In2code\Powermail\Eid;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Core\Bootstrap;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Utility\EidUtility;

/**
 * This class could called with AJAX via eID
 */
class MarketingEid
{

    /**
     * configuration
     *
     * @var array
     */
    protected $configuration;

    /**
     * bootstrap
     *
     * @var array
     */
    protected $bootstrap;

    /**
     * Generates the output
     *
     * @return string rendered action
     */
    public function run()
    {
        return $this->bootstrap->run('', $this->configuration);
    }

    /**
     * Initialize Extbase
     *
     * @param array $TYPO3_CONF_VARS The global array. Will be set internally
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function __construct($TYPO3_CONF_VARS)
    {
        $this->configuration = [
            'pluginName' => 'Pi1',
            'vendorName' => 'In2code',
            'extensionName' => 'Powermail',
            'controller' => 'Form',
            'action' => 'marketing',
            'mvc' => [
                'requestHandlers' => [
                    'TYPO3\CMS\Extbase\Mvc\Web\FrontendRequestHandler' =>
                        'TYPO3\CMS\Extbase\Mvc\Web\FrontendRequestHandler'
                ]
            ],
            'settings' => []
        ];
        $_POST['tx_powermail_pi1']['action'] = 'marketing';
        $_POST['tx_powermail_pi1']['controller'] = 'Form';

        $this->bootstrap = new Bootstrap();

        $userObj = EidUtility::initFeUser();
        $pid = (GeneralUtility::_GET('id') ? GeneralUtility::_GET('id') : 1);
        $GLOBALS['TSFE'] = GeneralUtility::makeInstance(
            TypoScriptFrontendController::class,
            $TYPO3_CONF_VARS,
            $pid,
            0,
            true
        );
        $GLOBALS['TSFE']->connectToDB();
        $GLOBALS['TSFE']->fe_user = $userObj;
        $GLOBALS['TSFE']->id = $pid;
        $GLOBALS['TSFE']->determineId();
        $GLOBALS['TSFE']->initTemplate();
        $GLOBALS['TSFE']->getConfigArray();
    }
}

$eid = GeneralUtility::makeInstance(MarketingEid::class, $GLOBALS['TYPO3_CONF_VARS']);
echo $eid->run();
