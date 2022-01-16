<?php
declare(strict_types = 1);
namespace In2code\Powermail\Command;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Service\GetNewMarkerNamesForFormService;
use In2code\Powermail\Utility\DatabaseUtility;
use In2code\Powermail\Utility\ObjectUtility;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Extbase\Object\Exception;

/**
 * Class ResetMarkersCommand
 */
class ResetMarkersCommand extends Command
{
    /**
     * @return void
     */
    public function configure()
    {
        $description = 'Reset all marker names in fields if there are broken fields without or duplicated markernames.';
        $this->setDescription($description);
        $this->addArgument('formUid', InputArgument::REQUIRED, 'Add the form uid, 0 resets markers of all forms');
        $this->addArgument(
            'forceReset',
            InputArgument::OPTIONAL,
            'Force to reset markers even if they are already filled',
            false
        );
    }

    /**
     * Reset all marker names in fields if there are broken fields without or duplicated markernames.
     * Note: Only non-hidden and non-deleted fields in non-hidden and non-deleted pages will be respected.
     * Attention: If you add "0" as form Uid, all fields in all forms will be resetted!
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $markerService = ObjectUtility::getObjectManager()->get(GetNewMarkerNamesForFormService::class);
        $markers = $markerService->getMarkersForFieldsDependingOnForm(
            (int)$input->getArgument('formUid'),
            (bool)$input->getArgument('forceReset')
        );
        foreach ($markers as $formMarkers) {
            foreach ($formMarkers as $uid => $marker) {
                $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Field::TABLE_NAME);
                $queryBuilder
                    ->update(Field::TABLE_NAME)
                    ->where($queryBuilder->expr()->eq('uid', (int)$uid))
                    ->set('marker', $marker)
                    ->execute();
            }
        }
        $output->writeln('Markers successfully resetted');
        // todo implement error handling
        return Command::SUCCESS;
    }
}
