<?php

declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Validation;

use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Service\RedirectUriService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class EnableJavascriptValidationAndAjaxViewHelper
 */
class EnableJavascriptValidationAndAjaxViewHelper extends AbstractValidationViewHelper
{
    /**
     * Could be disabled for testing
     *
     * @var bool
     */
    protected bool $addRedirectUri = true;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('form', Form::class, 'Form', true);
        $this->registerArgument('additionalAttributes', 'array', 'additionalAttributes', false, []);
    }

    /**
     * Returns Data Attribute Array to enable validation
     *
     * @return array for data attributes
     */
    public function render(): array
    {
        /** @var Form $field */
        $form = $this->arguments['form'];
        $additionalAttributes = $this->arguments['additionalAttributes'];
        if ($this->isClientValidationEnabled()) {
            $additionalAttributes['data-powermail-validate'] = 'data-powermail-validate';
        }

        if ($this->isNativeValidationEnabled()) {
            $additionalAttributes['data-validate'] = 'html5';
        }

        if (($this->settings['misc']['ajaxSubmit'] ?? '0') === '1') {
            $additionalAttributes['data-powermail-ajax'] = 'true';
            $additionalAttributes['data-powermail-form'] = $form->getUid();

            if ($this->addRedirectUri) {
                $redirectService = GeneralUtility::makeInstance(RedirectUriService::class, $this->contentObject);
                $redirectUri = $redirectService->getRedirectUri();
                if ($redirectUri) {
                    $additionalAttributes['data-powermail-ajax-uri'] = $redirectUri;
                }
            }
        }

        return $additionalAttributes;
    }
}
