<?php

declare(strict_types=1);

namespace In2code\Powermail\ViewHelpers\String;

use In2code\Powermail\Domain\Model\Field;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class AutocompleteViewHelper
 */
class AutocompleteViewHelper extends AbstractViewHelper
{
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('field', Field::class, 'Field', true);
    }

    /**
     * render the value for the autocomplete attribute
     *
     * @return string
     */
    public function render(): string
    {
        $field = $this->arguments['field'];

        [$fieldType, $autocompleteTokens, $token, $section, $type, $purpose]
            = [
            $field->getType(),
            '',
            $field->getAutocompleteToken(),
            trim($field->getAutocompleteSection()),
            $field->getAutocompleteType(),
            $field->getAutocompletePurpose(),
        ];

        // If token is empty or 'on'/'off', other tokens are not allowed.
        if (empty($token) || in_array($token, ['on', 'off'])) {
            return $token;
        }

        if (!$this->tokenIsAllowedForFieldType($token, $fieldType)) {
            return '';
        }

        // Optional section token must begin with the string 'section-'
        if (!empty($section)) {
            if ($this->tokenIsAllowedForSection($token)) {
                $autocompleteTokens .= 'section-' . $section . ' ';
            }
        }

        // Optional type token must be either 'shipping' or 'billing'
        if (!empty($type)) {
            if ($this->tokenIsAllowedForType($token, $type)) {
                $autocompleteTokens .= $type . ' ';
            }
        }

        // Optional purpose token is only allowed for certain autofill-field tokens
        if (!empty($purpose)) {
            if ($this->tokenIsAllowedForPurpose($token, $purpose, $fieldType)) {
                $autocompleteTokens .= $purpose . ' ';
            }
        }

        return $autocompleteTokens . $token;
    }

    /**
     * Checks if the given type token is allowed for the specified autocomplete field token.
     *
     * Based on WHATWG HTML Spec:
     * https://html.spec.whatwg.org/multipage/form-control-infrastructure.html#autofill
     *
     * @param string $token
     * @param string $type
     *
     * @return bool
     */
    protected function tokenIsAllowedForType(string $token, string $type): bool
    {
        $allowedTypes = ['shipping', 'billing'];
        $tokensNotSupportingType = [
            'nickname', 'sex', 'impp', 'url', 'organization-title',
            'tel-country-code', 'tel-area-code', 'tel-national', 'tel-local',
            'tel-local-prefix', 'tel-local-suffix', 'tel-extension',
            'username', 'new-password', 'current-password', 'one-time-code',
            'bday', 'bday-day', 'bday-month', 'bday-year', 'language', 'photo',
        ];
        return in_array($type, $allowedTypes)
            && !in_array($token, $tokensNotSupportingType);
    }

    /**
     * Checks if the given purpose token is allowed for the specified autocomplete field token.
     *
     * Based on WHATWG HTML Spec:
     * https://html.spec.whatwg.org/multipage/form-control-infrastructure.html#autofill
     *
     * @param string $token
     * @param string $purpose
     * @param string $fieldType
     *
     * @return bool
     */
    protected function tokenIsAllowedForPurpose(string $token, string $purpose, string $fieldType): bool
    {
        $allowedPurposes = ['home', 'work', 'mobile', 'fax', 'pager'];
        $tokensSupportingPurpose = ['tel', 'email', 'impp'];
        $purposeAllowedForFields = ['input', 'textarea', 'hidden'];

        return in_array($fieldType, $purposeAllowedForFields)
            && in_array($purpose, $allowedPurposes, true)
            && in_array($token, $tokensSupportingPurpose, true);
    }

    /**
     * Checks if the given autocomplete field token allows a section token prefix.
     *
     * Based on WHATWG HTML Spec:
     * https://html.spec.whatwg.org/multipage/form-control-infrastructure.html#autofill
     *
     * @param string $token
     *
     * @return bool
     */
    protected function tokenIsAllowedForSection(string $token): bool
    {
        $tokensNotSupportingSection = [
            'nickname', 'sex', 'impp', 'url', 'organization-title',
            'username', 'new-password', 'current-password', 'one-time-code',
            'bday', 'bday-day', 'bday-month', 'bday-year', 'language', 'photo',
        ];
        return !in_array($token, $tokensNotSupportingSection, true);
    }

    /**
     *  Checks if the given autocomplete field token is allowed for the current field type.
     *
     *  Based on WHATWG HTML Spec:
     *  https://html.spec.whatwg.org/multipage/form-control-infrastructure.html#autofill
     *
     * @param string $token
     * @param string $fieldType
     *
     * @return bool
     */
    protected function tokenIsAllowedForFieldType(string $token, string $fieldType): bool
    {
        $allowedForAllTypes = ['on', 'off'];
        $allowedForSelect = ['country', 'country-name', 'language', 'sex', 'bday', 'bday-day', 'bday-month', 'bday-year', 'title', 'address-level1', 'address-level2', 'cc-exp-month', 'cc-exp-year'];
        $allowedForLocation = ['country', 'country-name', 'street-address', 'postal-code', 'address-line1', 'address-line2', 'address-line3', 'address-level1', 'address-level2', 'address-level3', 'address-level4'];
        $allowedForCountry = ['country', 'country-name'];
        $allowedForHidden = ['name', 'honorific-prefix', 'given-name', 'additional-name', 'family-name', 'honorific-suffix', 'email', 'username', 'organization', 'organization-title', 'country', 'country-name', 'language'];
        $allowedForPassword = ['new-password', 'current-password'];

        switch ($fieldType) {
            case 'input':
            case 'textarea':
                //allow all
                return true;
            case 'location':
                return in_array($token, $allowedForAllTypes, true) || in_array($token, $allowedForLocation, true);
            case 'select':
                return in_array($token, $allowedForAllTypes, true) || in_array($token, $allowedForSelect, true);
            case 'country':
                return in_array($token, $allowedForAllTypes, true) || in_array($token, $allowedForCountry, true);
            case 'hidden':
                return in_array($token, $allowedForAllTypes, true) || in_array($token, $allowedForHidden, true);
            case 'password':
                return in_array($token, $allowedForAllTypes, true) || in_array($token, $allowedForPassword, true);
        }
    }
}
