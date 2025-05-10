<?php
declare(strict_types=1);

namespace In2code\Powermail\Tca;

/**
 * Class AddAutocompleteTokens
 * @package In2code\Powermail\Tca
 */
class AddAutocompleteTokens
{
    /**
     * @var string
     */
    public static string $LLL = 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:autocomplete_token.';

    /**
     * @param array $config
     *
     * @return void
     */
    public function getAutocompleteTokens(array &$config)
    {
        if ($config['config']['itemsProcConfig']['useDefaultItems'] ?? true) {
            $defaultSelectItems = self::getDefaultAutocompleteTokens();
            $config['items'] = array_merge(
                $config['items'],
                $defaultSelectItems
            );
        }
    }

    /**
     * https://html.spec.whatwg.org/multipage/form-control-infrastructure.html#autofill-field
     * @return array[]
     */
    public static function getDefaultAutocompleteTokens(): array
    {
        return [
            ['label' => self::$LLL . 'name', 'value' => 'name', 'icon' => '', 'group' => 'name'],
            ['label' => self::$LLL . 'honorific-prefix', 'value' => 'honorific-prefix', 'icon' => '', 'group' => 'name'],
            ['label' => self::$LLL . 'given-name', 'value' => 'given-name', 'icon' => '', 'group' => 'name'],
            ['label' => self::$LLL . 'additional-name', 'value' => 'additional-name', 'icon' => '', 'group' => 'name'],
            ['label' => self::$LLL . 'family-name', 'value' => 'family-name', 'icon' => '', 'group' => 'name'],
            ['label' => self::$LLL . 'honorific-suffix', 'value' => 'honorific-suffix', 'icon' => '', 'group' => 'name'],
            ['label' => self::$LLL . 'nickname', 'value' => 'nickname', 'icon' => '', 'group' => 'name'],
            ['label' => self::$LLL . 'sex', 'value' => 'sex', 'icon' => '', 'group' => 'name'],
            ['label' => self::$LLL . 'email', 'value' => 'email', 'icon' => '', 'group' => 'contact'],
            ['label' => self::$LLL . 'impp', 'value' => 'impp', 'icon' => '', 'group' => 'contact'],
            ['label' => self::$LLL . 'url', 'value' => 'url', 'icon' => '', 'group' => 'contact'],
            ['label' => self::$LLL . 'organization-title', 'value' => 'organization-title', 'icon' => '', 'group' => 'contact'],
            ['label' => self::$LLL . 'organization', 'value' => 'organization', 'icon' => '', 'group' => 'contact'],
            ['label' => self::$LLL . 'street-address', 'value' => 'street-address', 'icon' => '', 'group' => 'contact'],
            ['label' => self::$LLL . 'country', 'value' => 'country', 'icon' => '', 'group' => 'contact'],
            ['label' => self::$LLL . 'country-name', 'value' => 'country-name', 'icon' => '', 'group' => 'contact'],
            ['label' => self::$LLL . 'postal-code', 'value' => 'postal-code', 'icon' => '', 'group' => 'contact'],
            ['label' => self::$LLL . 'address-line1', 'value' => 'address-line1', 'icon' => '', 'group' => 'address'],
            ['label' => self::$LLL . 'address-line2', 'value' => 'address-line2', 'icon' => '', 'group' => 'address'],
            ['label' => self::$LLL . 'address-line3', 'value' => 'address-line3', 'icon' => '', 'group' => 'address'],
            ['label' => self::$LLL . 'address-level1', 'value' => 'address-level1', 'icon' => '', 'group' => 'address'],
            ['label' => self::$LLL . 'address-level2', 'value' => 'address-level2', 'icon' => '', 'group' => 'address'],
            ['label' => self::$LLL . 'address-level3', 'value' => 'address-level3', 'icon' => '', 'group' => 'address'],
            ['label' => self::$LLL . 'address-level4', 'value' => 'address-level4', 'icon' => '', 'group' => 'address'],
            ['label' => self::$LLL . 'tel', 'value' => 'tel', 'icon' => '', 'group' => 'tel'],
            ['label' => self::$LLL . 'tel-country-code', 'value' => 'tel-country-code', 'icon' => '', 'group' => 'tel'],
            ['label' => self::$LLL . 'tel-area-code', 'value' => 'tel-area-code', 'icon' => '', 'group' => 'tel'],
            ['label' => self::$LLL . 'tel-national', 'value' => 'tel-national', 'icon' => '', 'group' => 'tel'],
            ['label' => self::$LLL . 'tel-local', 'value' => 'tel-local', 'icon' => '', 'group' => 'tel'],
            ['label' => self::$LLL . 'tel-local-prefix', 'value' => 'tel-local-prefix', 'icon' => '', 'group' => 'tel'],
            ['label' => self::$LLL . 'tel-local-suffix', 'value' => 'tel-local-suffix', 'icon' => '', 'group' => 'tel'],
            ['label' => self::$LLL . 'tel-extension', 'value' => 'tel-extension', 'icon' => '', 'group' => 'tel'],
            ['label' => self::$LLL . 'username', 'value' => 'username', 'icon' => '', 'group' => 'user'],
            ['label' => self::$LLL . 'new-password', 'value' => 'new-password', 'icon' => '', 'group' => 'user'],
            ['label' => self::$LLL . 'current-password', 'value' => 'current-password', 'icon' => '', 'group' => 'user'],
            ['label' => self::$LLL . 'one-time-code', 'value' => 'one-time-code', 'icon' => '', 'group' => 'user'],
            ['label' => self::$LLL . 'bday', 'value' => 'bday', 'icon' => '', 'group' => 'bday'],
            ['label' => self::$LLL . 'bday-day', 'value' => 'bday-day', 'icon' => '', 'group' => 'bday'],
            ['label' => self::$LLL . 'bday-month', 'value' => 'bday-month', 'icon' => '', 'group' => 'bday'],
            ['label' => self::$LLL . 'bday-year', 'value' => 'bday-year', 'icon' => '', 'group' => 'bday'],
            ['label' => self::$LLL . 'cc-name', 'value' => 'cc-name', 'icon' => '', 'group' => 'cc'],
            ['label' => self::$LLL . 'cc-given-name', 'value' => 'cc-given-name', 'icon' => '', 'group' => 'cc'],
            ['label' => self::$LLL . 'cc-additional-name', 'value' => 'cc-additional-name', 'icon' => '', 'group' => 'cc'],
            ['label' => self::$LLL . 'cc-family-name', 'value' => 'cc-family-name', 'icon' => '', 'group' => 'cc'],
            ['label' => self::$LLL . 'cc-number', 'value' => 'cc-number', 'icon' => '', 'group' => 'cc'],
            ['label' => self::$LLL . 'cc-exp', 'value' => 'cc-exp', 'icon' => '', 'group' => 'cc'],
            ['label' => self::$LLL . 'cc-exp-month', 'value' => 'cc-exp-month', 'icon' => '', 'group' => 'cc'],
            ['label' => self::$LLL . 'cc-exp-year', 'value' => 'cc-exp-year', 'icon' => '', 'group' => 'cc'],
            ['label' => self::$LLL . 'cc-csc', 'value' => 'cc-csc', 'icon' => '', 'group' => 'cc'],
            ['label' => self::$LLL . 'cc-type', 'value' => 'cc-type', 'icon' => '', 'group' => 'cc'],
            ['label' => self::$LLL . 'transaction-currency', 'value' => 'transaction-currency', 'icon' => '', 'group' => 'cc'],
            ['label' => self::$LLL . 'transaction-amount', 'value' => 'transaction-amount', 'icon' => '', 'group' => 'cc'],
            ['label' => self::$LLL . 'language', 'value' => 'language', 'icon' => '', 'group' => 'other'],
            ['label' => self::$LLL . 'photo', 'value' => 'photo', 'icon' => '', 'group' => 'other'],
        ];
    }
}