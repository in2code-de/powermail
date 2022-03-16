<?php
declare(strict_types = 1);
namespace In2code\Powermail\Command;

use In2code\Powermail\Domain\Repository\AnswerRepository;
use In2code\Powermail\Utility\BasicFileUtility;
use In2code\Powermail\Utility\ObjectUtility;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\Exception;

/**
 * Class CleanupUnusedUploadsCommand
 */
class CleanupUnusedUploadsCommand extends Command
{
    /**
     * @return void
     */
    public function configure()
    {
        $this->setDescription('Remove unused uploaded Files with a scheduler task');
        $this->addArgument('uploadPath', InputArgument::OPTIONAL, 'Define the upload Path', 'uploads/tx_powermail/');
    }

    /**
     * This task can clean up unused uploaded files with powermail from your server
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $usedUploads = $this->getUsedUploads();
        $allUploads = BasicFileUtility::getFilesFromRelativePath($input->getArgument('uploadPath'));
        $removeCounter = 0;
        foreach ($allUploads as $upload) {
            if (!in_array($upload, $usedUploads)) {
                $absoluteFilePath = GeneralUtility::getFileAbsFileName($input->getArgument('uploadPath') . $upload);
                if (filemtime($absoluteFilePath) < (time() - 3600)) {
                    unlink($absoluteFilePath);
                    $removeCounter++;
                }
            }
        }
        $output->writeln('Overall Files: ' . count($allUploads));
        $output->writeln('Removed Files: ' . $removeCounter);
        // todo implement error handling
        return Command::SUCCESS;
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function getUsedUploads()
    {
        $answerRepository = ObjectUtility::getObjectManager()->get(AnswerRepository::class);
        $answers = $answerRepository->findByAnyUpload();
        $usedUploads = [];
        foreach ($answers as $answer) {
            foreach ((array)$answer->getValue() as $singleUpload) {
                $usedUploads[] = $singleUpload;
            }
        }
        return $usedUploads;
    }
}
