<?php

declare(strict_types=1);
namespace In2code\Powermail\Controller;

use Doctrine\DBAL\DBALException;
use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Repository\PageRepository;
use In2code\Powermail\Domain\Service\SlidingWindowPagination;
use In2code\Powermail\Exception\FileCannotBeCreatedException;
use In2code\Powermail\Utility\BackendUtility;
use In2code\Powermail\Utility\BasicFileUtility;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\MailUtility;
use In2code\Powermail\Utility\ReportingUtility;
use In2code\Powermail\Utility\StringUtility;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Http\ForwardResponse;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Reflection\Exception\PropertyNotAccessibleException;

/**
 * Class ModuleController for backend modules
 */
class ModuleController extends AbstractController
{
    /**
     * @param string $forwardToAction
     * @throws StopActionException
     * @return void
     * @noinspection PhpUnused
     */
    public function dispatchAction(string $forwardToAction = 'list'): ResponseInterface
    {
        return (new ForwardResponse($forwardToAction))
            ->withControllerName('Module')
            ->withExtensionName('Powermail');
    }

    /**
     * @return ResponseInterface
     * @throws InvalidQueryException
     * @throws RouteNotFoundException
     * @throws NoSuchArgumentException
     * @noinspection PhpUnused
     */
    public function listAction(): ResponseInterface
    {
        $formUids = $this->mailRepository->findGroupedFormUidsToGivenPageUid((int)$this->id);
        $mails = $this->mailRepository->findAllInPid((int)$this->id, $this->settings, $this->piVars);

        $currentPage = 1;
        if ($this->request->hasArgument('currentPage')) {
            $currentPage =  $this->request->getArgument('currentPage');
        }
        $itemsPerPage = $this->settings['perPage'] ?? 10;
        $paginator = GeneralUtility::makeInstance(QueryResultPaginator::class, $mails, $currentPage, $itemsPerPage);
        $pagination = GeneralUtility::makeInstance(SlidingWindowPagination::class, $paginator, 15);

        $firstFormUid = StringUtility::conditionalVariable($this->piVars['filter']['form'] ?? '', key($formUids));
        $beUser = BackendUtility::getBackendUserAuthentication();
        $this->view->assignMultiple(
            [
                'mails' => $mails,
                'formUids' => $formUids,
                'firstForm' => $this->formRepository->findByUid($firstFormUid),
                'piVars' => $this->piVars,
                'pid' => $this->id,
                'moduleUri' => BackendUtility::getRoute('ajax_record_process'),
                'pagination' => [
                    'pagination' => $pagination,
                    'paginator' => $paginator,
                ],
                'perPage' => $this->settings['perPage'] ?? 10,
                'writeAccess' => $beUser->check('tables_modify', Answer::TABLE_NAME)
                    && $beUser->check('tables_modify', Mail::TABLE_NAME),
            ]
        );
        return $this->htmlResponse();
    }

    /**
     * @return void
     * @throws InvalidQueryException
     * @noinspection PhpUnused
     */
    public function exportXlsAction(): ResponseInterface
    {
        $this->view->assignMultiple(
            [
                'mails' => $this->mailRepository->findAllInPid($this->id, $this->settings, $this->piVars),
                'fieldUids' => GeneralUtility::trimExplode(
                    ',',
                    StringUtility::conditionalVariable($this->piVars['export']['fields'], ''),
                    true
                ),
            ]
        );

        $fileName = StringUtility::conditionalVariable($this->settings['export']['filenameXls'], 'export.xls');
        return $this->htmlResponse()
            ->withHeader('Content-Type', 'application/vnd.ms-excel')
            ->withAddedHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->withAddedHeader('Pragma', 'no-cache')
        ;
    }

    /**
     * @return void
     * @throws InvalidQueryException
     * @noinspection PhpUnused
     */
    public function exportCsvAction(): ResponseInterface
    {
        $this->view->assignMultiple(
            [
                'mails' => $this->mailRepository->findAllInPid($this->id, $this->settings, $this->piVars),
                'fieldUids' => GeneralUtility::trimExplode(
                    ',',
                    StringUtility::conditionalVariable($this->piVars['export']['fields'], ''),
                    true
                ),
            ]
        );

        $fileName = StringUtility::conditionalVariable($this->settings['export']['filenameCsv'], 'export.csv');
        return $this->htmlResponse()
            ->withHeader('Content-Type', 'text/x-csv')
            ->withAddedHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->withAddedHeader('Pragma', 'no-cache')
        ;
    }

    /**
     * @return void
     * @throws InvalidQueryException
     * @throws RouteNotFoundException
     * @noinspection PhpUnused
     */
    public function reportingFormBeAction(): ResponseInterface
    {
        $mails = $this->mailRepository->findAllInPid($this->id, $this->settings, $this->piVars);
        $firstMail = $this->mailRepository->findFirstInPid($this->id);
        $groupedAnswers = ReportingUtility::getGroupedAnswersFromMails($mails);

        $this->view->assignMultiple(
            [
                'groupedAnswers' => $groupedAnswers,
                'mails' => $mails,
                'firstMail' => $firstMail,
                'piVars' => $this->piVars,
                'pid' => $this->id,
                'moduleUri' => BackendUtility::getRoute('ajax_record_process'),
                'perPage' => ($this->settings['perPage'] ? $this->settings['perPage'] : 10),
            ]
        );
        return $this->htmlResponse();
    }

    /**
     * @return void
     * @throws InvalidQueryException
     * @throws RouteNotFoundException
     * @throws PropertyNotAccessibleException
     * @noinspection PhpUnused
     */
    public function reportingMarketingBeAction(): ResponseInterface
    {
        $mails = $this->mailRepository->findAllInPid($this->id, $this->settings, $this->piVars);
        $firstMail = $this->mailRepository->findFirstInPid($this->id);
        $groupedMarketing = ReportingUtility::getGroupedMarketingPropertiesFromMails($mails);

        $this->view->assignMultiple(
            [
                'groupedMarketingStuff' => $groupedMarketing,
                'mails' => $mails,
                'firstMail' => $firstMail,
                'piVars' => $this->piVars,
                'pid' => $this->id,
                'moduleUri' => BackendUtility::getRoute('ajax_record_process'),
                'perPage' => ($this->settings['perPage'] ? $this->settings['perPage'] : 10),
            ]
        );
        return $this->htmlResponse();
    }

    /**
     * @return void
     * @throws InvalidQueryException
     * @throws Exception
     * @noinspection PhpUnused
     */
    public function overviewBeAction(): ResponseInterface
    {
        $forms = $this->formRepository->findAllInPidAndRootline($this->id);
        $this->view->assign('forms', $forms);
        $this->view->assign('pid', $this->id);
        return $this->htmlResponse();
    }

    /**
     * @return void
     */
    public function initializeCheckBeAction(): void
    {
        $this->checkAdminPermissions();
    }

    /**
     * @param string|null $email
     * @return ResponseInterface
     */
    public function checkBeAction(string $email = null): ResponseInterface
    {
        $this->view->assign('pid', $this->id);
        $this->sendTestEmail($email);
        return $this->htmlResponse();
    }

    /**
     * @param null $email
     * @return void
     */
    protected function sendTestEmail($email = null): void
    {
        if ($email !== null && GeneralUtility::validEmail($email)) {
            $body = 'New Test Email from User ' . BackendUtility::getPropertyFromBackendUser('username');
            $body .= ' (' . GeneralUtility::getIndpEnv('HTTP_HOST') . ')';
            $senderEmail = ConfigurationUtility::getDefaultMailFromAddress('powermail@domain.net');
            $this->view->assignMultiple(
                [
                    'issent' => MailUtility::sendPlainMail($email, $senderEmail, 'New Powermail Test Email', $body),
                    'email' => $email,
                ]
            );
        }
    }

    /**
     * @return void
     * @noinspection PhpUnused
     */
    public function initializeConverterBeAction(): void
    {
        $this->checkAdminPermissions();
    }

    /**
     * @return void
     * @noinspection PhpUnused
     */
    public function initializeFixUploadFolderAction(): void
    {
        $this->checkAdminPermissions();
    }

    /**
     * @return void
     * @throws StopActionException
     * @throws FileCannotBeCreatedException
     * @noinspection PhpUnused
     */
    public function fixUploadFolderAction(): void
    {
        BasicFileUtility::createFolderIfNotExists(GeneralUtility::getFileAbsFileName('uploads/tx_powermail/'));
        $this->redirect('checkBe');
    }

    /**
     * @return void
     * @noinspection PhpUnused
     */
    public function initializeFixWrongLocalizedFormsAction(): void
    {
        $this->checkAdminPermissions();
    }

    /**
     * @return void
     * @throws StopActionException
     * @throws DBALException
     * @noinspection PhpUnused
     */
    public function fixWrongLocalizedFormsAction(): void
    {
        $this->formRepository->fixWrongLocalizedForms();
        $this->redirect('checkBe');
    }

    /**
     * @return void
     * @noinspection PhpUnused
     */
    public function initializeFixWrongLocalizedPagesAction(): void
    {
        $this->checkAdminPermissions();
    }

    /**
     * @return void
     * @throws StopActionException
     * @noinspection PhpUnused
     */
    public function fixWrongLocalizedPagesAction(): void
    {
        $pageRepository = GeneralUtility::makeInstance(PageRepository::class);
        $pageRepository->fixWrongLocalizedPages();
        $this->redirect('checkBe');
    }

    /**
     * Check if admin is logged in
     *        If not, forward to tools overview
     *
     * @return ResponseInterface|null
     */
    protected function checkAdminPermissions(): ?ResponseInterface
    {
        if (!BackendUtility::isBackendAdmin()) {
            $this->controllerContext = $this->buildControllerContext();
            return new ForwardResponse('toolsBe');
        }
        return null;
    }
}
