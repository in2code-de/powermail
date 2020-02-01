<?php
declare(strict_types=1);
namespace In2code\Powermail\Update;

use Doctrine\DBAL\DBALException;
use In2code\Powermail\Domain\Service\UpdatePowermailToVersionEightService;
use In2code\Powermail\Exception\DatabaseFieldMissingException;
use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

class PowermailUpdateWizard implements UpgradeWizardInterface
{
    /**
     * @var UpdatePowermailToVersionEightService
     */
    protected $updateService = null;

    /**
     * ext_update constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $this->updateService = ObjectUtility::getObjectManager()->get(UpdatePowermailToVersionEightService::class);
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'powermailUpdateWizard';
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return 'Update a database from powermail version < 8.0.0 to a powermail version >= 8.0.0.';
    }

    /**
     * Return the description for this wizard
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Basicly this upgrade wizard copies values from tx_powermail_domain_model_field.pages to .page and ' .
            'tx_powermail_domain_model_pages.forms to .form';
    }

    /**
     * @return bool
     */
    public function executeUpdate(): bool
    {
        try {
            $this->updateService->updateDatastructure();
        } catch (\Exception $exception) {
            return false;
        }
        return true;
    }

    /**
     * @return bool
     * @throws DBALException
     * @throws DatabaseFieldMissingException
     */
    public function updateNecessary(): bool
    {
        return $this->updateService->isUpdateNeeded();
    }

    /**
     * Returns an array of class names of prerequisite classes
     *
     * This way a wizard can define dependencies like "database up-to-date" or
     * "reference index updated"
     *
     * @return string[]
     */
    public function getPrerequisites(): array
    {
        return [
            DatabaseUpdatedPrerequisite::class
        ];
    }
}
