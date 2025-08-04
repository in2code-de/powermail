<?php

declare(strict_types=1);
namespace In2code\Powermail\Command;

use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;

/**
 * It is not possible to create an instance of an extbase repository in a symfony command anymore in TYPO3 12 or newer
 * So we have to fake a request, to get this running again.
 */
trait FakeRequestTrait
{
    protected function fakeRequest(): void
    {
        if (!isset($GLOBALS['TYPO3_REQUEST'])) {
            $request = (new ServerRequest())
                ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_BE);
            $GLOBALS['TYPO3_REQUEST'] = $request;
        }
    }
}
