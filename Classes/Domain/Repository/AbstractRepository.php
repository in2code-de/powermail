<?php
namespace In2code\Powermail\Domain\Repository;

use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Class AbstractRepository
 */
abstract class AbstractRepository extends Repository
{

    /**
     * @var \In2code\Powermail\Domain\Repository\FormRepository
     * @inject
     */
    protected $formRepository;

    /**
     * @return DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return ObjectUtility::getDatabaseConnection();
    }
}
