<?php

declare(strict_types=1);
namespace In2code\Powermail\Exception;

use TYPO3\CMS\Core\Error\Http\ForbiddenException;

/**
 * Class NoPageAccessException
 *
 * Thrown when a backend user requests a page id in the backend module they are not allowed to access.
 */
class NoPageAccessException extends ForbiddenException
{
}
