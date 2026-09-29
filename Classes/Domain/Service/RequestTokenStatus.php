<?php

declare(strict_types=1);

namespace In2code\Powermail\Domain\Service;

enum RequestTokenStatus: string
{
    case Valid = 'valid';

    // No request token was sent with the request
    case Missing = 'missing';

    // A request token was sent, but could not be verified (e.g. nonce cookie missing, expired or already used)
    case Invalid = 'invalid';

    // A valid request token was sent, but it was issued for another scope or form
    case WrongScope = 'wrongScope';
}
