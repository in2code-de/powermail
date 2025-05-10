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

        list($autocompleteTokens, $token, $section, $type, $purpose)
            = [
            '',
            $field->getAutocompleteToken(),
            trim($field->getAutocompleteSection()),
            $field->getAutocompleteType(),
            $field->getAutocompletePurpose(),
        ];

        //if token is empty or 'on'/'off' other tokens are not allowed
        if (empty($token) || in_array($token, ['on', 'off'])) {
            return $token;
        }

        //optional section token must begin with the string 'section-'
        if (!empty($section)) {
            $autocompleteTokens = 'section-' . $section . ' ';
        }

        //optional type token must be either shipping or billing
        if (!empty($type) && in_array($type, ['shipping', 'billing'])) {
            $autocompleteTokens .= $type . ' ';
        }

        //optional purpose token is only allowed for certain autofill-field tokens
        if (!empty($purpose)) {
            if ($this->tokenIsAllowedForPurpose($token, $purpose)) {
                $autocompleteTokens .= $purpose . ' ';
            }
        }

        return $autocompleteTokens . $token;
    }

    /**
     * hardcoded check:
     * purpose is only allowed for email, imp, tel and tel-*
     *
     * @see https://html.spec.whatwg.org/multipage/form-control-infrastructure.html#autofill-detail-tokens
     *
     * @param string $token
     * @param string $purpose
     *
     * @return bool
     */
    protected function tokenIsAllowedForPurpose(string $token, string $purpose): bool
    {
        return in_array($purpose, ['home', 'work', 'mobile', 'fax', 'pager'])
            && in_array($token, ['tel', 'tel-country-code', 'tel-national', 'tel-area-code', 'tel-local', 'tel-local-prefix', 'tel-local-suffix', 'tel-extension', 'email', 'impp']);
    }
}
