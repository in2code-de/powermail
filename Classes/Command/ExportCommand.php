<?php
declare(strict_types = 1);
namespace In2code\Powermail\Command;

use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Domain\Service\ExportService;
use In2code\Powermail\Utility\ObjectUtility;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidExtensionNameException;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;

/**
 * Class ExportCommand
 */
class ExportCommand extends Command
{
    /**
     * @return void
     */
    public function configure()
    {
        $description =
            'This task can send a mail export with an attachment (XLS or CSV) to a receiver or a group of receivers';
        $this->setDescription($description);
        $this->addArgument('receiverEmails', InputArgument::REQUIRED, 'Comma separated email addresses for export');
        $this->addArgument('senderEmail', InputArgument::OPTIONAL, 'Sender email address', 'sender@domain.org');
        $this->addArgument('subject', InputArgument::OPTIONAL, 'Mail subject', 'New mail export');
        $this->addArgument('pageUid', InputArgument::OPTIONAL, 'Page Id with existing mails', 0);
        $this->addArgument('domain', InputArgument::OPTIONAL, 'Domainname for linkgeneration', 'https://domain.org/');
        $this->addArgument('period', InputArgument::OPTIONAL, 'Mails that are not older than this seconds', 2592000);
        $this->addArgument('attachment', InputArgument::OPTIONAL, 'Add export file as attachment to mail', true);
        $this->addArgument('fieldList', InputArgument::OPTIONAL, 'Define fields with a uid list (empty = all)', '');
        $this->addArgument('format', InputArgument::OPTIONAL, 'Fileformat can be "xls" or "csv"', 'xls');
        $this->addArgument(
            'storageFolder',
            InputArgument::OPTIONAL,
            'Path where to save export file',
            'typo3temp/assets/tx_powermail/'
        );
        $this->addArgument('fileName', InputArgument::OPTIONAL, 'Define a filename (no extension, empty = random)', '');
        $this->addArgument(
            'emailTemplate',
            InputArgument::OPTIONAL,
            'Path and filename of email template',
            'EXT:powermail/Resources/Private/Templates/Module/ExportTaskMail.html'
        );
    }

    /**
     * Own export command to export whole pagetrees with all records to a file which contains a json and can be
     * imported again with a different import command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws InvalidConfigurationTypeException
     * @throws InvalidExtensionNameException
     * @throws Exception
     * @throws InvalidQueryException
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $mailRepository = ObjectUtility::getObjectManager()->get(MailRepository::class);
        $exportService = ObjectUtility::getObjectManager()->get(
            ExportService::class,
            $mailRepository->findAllInPid(
                (int)$input->getArgument('pageUid'),
                [],
                $this->getFilterVariables($input->getArgument('period'))
            ),
            $input->getArgument('format'),
            ['domain' => $input->getArgument('domain')]
        );
        $exportService
            ->setReceiverEmails($input->getArgument('receiverEmails'))
            ->setSenderEmails($input->getArgument('senderEmail'))
            ->setSubject($input->getArgument('subject'))
            ->setFieldList($input->getArgument('fieldList'))
            ->setAddAttachment((bool)$input->getArgument('attachment'))
            ->setStorageFolder($input->getArgument('storageFolder'))
            ->setFileName($input->getArgument('fileName'))
            ->setEmailTemplate($input->getArgument('emailTemplate'));
        if ($exportService->send() === true) {
            $output->writeln('Export finished');
            return Command::SUCCESS;
        }
        $output->writeln('Export could not be generated');
        return Command::FAILURE;
    }

    /**
     * Create a filter array from given period
     *
     * @param int $period
     * @return array
     */
    protected function getFilterVariables($period)
    {
        $variables = ['filter' => []];
        if ($period > 0) {
            $variables = [
                'filter' => [
                    'start' => strftime('%Y-%m-%d %H:%M:%S', (time() - $period)),
                    'stop' => 'now'
                ]
            ];
        }
        return $variables;
    }
}
