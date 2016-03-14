<?php
namespace In2code\Powermail\Slot;

use In2code\Powermail\Domain\Service\ConvertTableNamesService;
use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Extensionmanager\Utility\InstallUtility;

/**
 * Class ConvertTableNames
 * @package In2code\Powermail\Slot
 */
class ConvertTableNames
{

    /**
     * Execute update script on extension installation
     *
     * @param string $extensionKey
     * @param InstallUtility $installUtility
     * @return void
     */
    public function convert($extensionKey, InstallUtility $installUtility)
    {
        if ($extensionKey === 'powermail') {
            /** @var ConvertTableNamesService $convertService */
            $convertService = ObjectUtility::getObjectManager()->get(ConvertTableNamesService::class);
            $convertService->convert();
        }
    }
}
