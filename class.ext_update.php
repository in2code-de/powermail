<?php
namespace In2code\Powermail;

use In2code\Powermail\Domain\Service\ConvertTableNamesService;
use In2code\Powermail\Utility\ObjectUtility;

/**
 * Class ext_update will be automaticly included from the extension manager
 *
 * @package In2code\Powermail
 */
class ext_update
{

    /**
     * Main function, returning the HTML content
     *
     * @return string HTML
     */
    public function main()
    {
        /** @var ConvertTableNamesService $convertService */
        $convertService = ObjectUtility::getObjectManager()->get(ConvertTableNamesService::class);
        return $convertService->convert();
    }

    /**
     * @return bool
     */
    public function access()
    {
        return true;
    }
}
