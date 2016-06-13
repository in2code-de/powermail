<?php
namespace In2code\Powermail\ViewHelpers\Validation;

use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Service\RedirectUriService;
use In2code\Powermail\Utility\ObjectUtility;

/**
 * Adds additional attributes for parsley or AJAX submit
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class EnableParsleyAndAjaxViewHelper extends AbstractValidationViewHelper
{

    /**
     * Could be disabled for testing
     *
     * @var bool
     */
    protected $addRedirectUri = true;

    /**
     * Returns Data Attribute Array to enable parsley
     *
     * @param Form $form
     * @param array $additionalAttributes To add further attributes
     * @return array for data attributes
     */
    public function render(Form $form, $additionalAttributes = [])
    {
        if ($this->isClientValidationEnabled()) {
            $additionalAttributes['data-parsley-validate'] = 'data-parsley-validate';
        }

        if ($this->isNativeValidationEnabled()) {
            $additionalAttributes['data-validate'] = 'html5';
        }

        if ($this->settings['misc']['ajaxSubmit'] === '1') {
            $additionalAttributes['data-powermail-ajax'] = 'true';
            $additionalAttributes['data-powermail-form'] = $form->getUid();

            if ($this->addRedirectUri) {
                /** @var RedirectUriService $redirectService */
                $redirectService = ObjectUtility::getObjectManager()->get(
                    RedirectUriService::class,
                    $this->contentObject
                );
                $redirectUri = $redirectService->getRedirectUri();
                if ($redirectUri) {
                    $additionalAttributes['data-powermail-ajax-uri'] = $redirectUri;
                }
            }
        }

        return $additionalAttributes;
    }
}
