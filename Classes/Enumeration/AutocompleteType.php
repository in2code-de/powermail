<?php
declare(strict_types=1);
namespace In2code\Powermail\Enumeration;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Type\Enumeration;

class AutocompleteType extends Enumeration
{

    const ON = 'on';
    const OFF = 'off';
    const NAME = 'name';
    const HONORIFIC_PREFIX = 'honorific-prefix';
    const GIVEN_NAME = 'given-name';
    const ADDITIONAL_NAME = 'additional-name';
    const FAMILY_NAME = 'family-name';
    const HONORIFIC_SUFFIX = 'honorific-suffix';
    const NICKNAME = 'nickname';
    const USERNAME = 'username';
    const NEW_PASSWORD = 'new-password';
    const CURRENT_PASSWORD = 'current-password';
    const ORGANIZATION_TITLE = 'organization-title';
    const ORGANIZATION = 'organization';
    const STREET_ADDRESS = 'street-address';
    const ADDRESS_LINE1 = 'address-line1';
    const ADDRESS_LINE2 = 'address-line2';
    const ADDRESS_LINE3 = 'address-line3';
    const ADDRESS_LEVEL4 = 'address-level4';
    const ADDRESS_LEVEL3 = 'address-level3';
    const ADDRESS_LEVEL2 = 'address-level2';
    const ADDRESS_LEVEL1 = 'address-level1';
    const COUNTRY = 'country';
    const COUNTRY_NAME = 'country-name';
    const POSTAL_CODE = 'postal-code';
    const CC_NAME = 'cc-name';
    const CC_GIVEN_NAME = 'cc-given-name';
    const CC_ADDITIONAL_NAME = 'cc-additional-name';
    const CC_FAMILY_NAME = 'cc-family-name';
    const CC_NUMBER = 'cc-number';
    const CC_EXP = 'cc-exp';
    const CC_EXP_MONTH = 'cc-exp-month';
    const CC_EXP_YEAR = 'cc-exp-year';
    const CC_CSC = 'cc-csc';
    const CC_TYPE = 'cc-type';
    const TRANSACTION_CURRENCY = 'transaction-currency';
    const TRANSACTION_AMOUNT = 'transaction-amount';
    const LANGUAGE = 'language';
    const BDAY = 'bday';
    const BDAY_DAY = 'bday-day';
    const BDAY_MONTH = 'bday-month';
    const BDAY_YEAR = 'bday-year';
    const SEX = 'sex';
    const URL = 'url';
    const PHOTO = 'photo';
    const TEL = 'tel';
    const TEL_COUNTRY_CODE = 'tel-country-code';
    const TEL_NATIONAL = 'tel-national';
    const TEL_AREA_CODE = 'tel-area-code';
    const TEL_LOCAL = 'tel-local';
    const TEL_LOCAL_PREFIX = 'tel-local-prefix';
    const TEL_LOCAL_SUFFIX = 'tel-local-suffix';
    const TEL_EXTENSION = 'tel-extension';
    const EMAIL = 'email';
    const IMPP = 'impp';

}