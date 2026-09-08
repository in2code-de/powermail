<?php
declare(strict_types = 1);
namespace In2code\Powermail\Exception;

use TYPO3\CMS\Core\Error\Http\ForbiddenException;

/**
 * Class NoPageAccessException
 *        Thrown if a backend user requests a page in the backend module that she/he is not allowed to access
 */
class NoPageAccessException extends ForbiddenException
{
}
