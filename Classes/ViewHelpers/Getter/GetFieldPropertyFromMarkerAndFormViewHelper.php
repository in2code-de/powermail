<?php
declare(strict_types = 1);
namespace In2code\Powermail\ViewHelpers\Getter;

use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Repository\FieldRepository;
use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Reflection\Exception\PropertyNotAccessibleException;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class GetFieldPropertyFromMarkerAndFormViewHelper
 */
class GetFieldPropertyFromMarkerAndFormViewHelper extends AbstractViewHelper
{

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('marker', 'string', 'markername', true);
        $this->registerArgument('form', Form::class, 'Form', true);
        $this->registerArgument('property', 'string', 'Field property', true);
    }

    /**
     * Get any property from tx_powermail_domain_model_field by markername and form
     *
     * @return mixed|string
     * @throws Exception
     * @throws InvalidQueryException
     * @throws PropertyNotAccessibleException
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function render()
    {
        /** @var Form $form */
        $form = $this->arguments['form'];
        $fieldRepository = ObjectUtility::getObjectManager()->get(FieldRepository::class);
        $field = $fieldRepository->findByMarkerAndForm((string)$this->arguments['marker'], $form->getUid());
        if ($field !== null) {
            return ObjectAccess::getProperty($field, $this->arguments['property']);
        }
        return '';
    }
}
