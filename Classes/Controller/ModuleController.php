<?php

declare(strict_types=1);

namespace In2code\Powermail\Controller;

use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Repository\FormRepository;
use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Domain\Repository\PageRepository;
use In2code\Powermail\Domain\Service\SlidingWindowPagination;
use In2code\Powermail\Exception\FileCannotBeCreatedException;
use In2code\Powermail\Utility\BackendUtility;
use In2code\Powermail\Utility\BasicFileUtility;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\MailUtility;
use In2code\Powermail\Utility\ReportingUtility;
use In2code\Powermail\Utility\StringUtility;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Module\ModuleData;
use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Http\ForwardResponse;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException;
use TYPO3\CMS\Extbase\Object\Exception as ExceptionExtbaseObject;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Reflection\Exception\PropertyNotAccessibleException;

/**
 * Class ModuleController for backend modules
 */
class ModuleController extends AbstractController
{
    protected ?ModuleData $moduleData = null;

    protected ModuleTemplate $moduleTemplate;

    public function __construct(
        protected FormRepository $formRepository,
        protected MailRepository $mailRepository,
        protected ModuleTemplateFactory $moduleTemplateFactory,
        protected IconFactory $iconFactory,
        protected PageRenderer $pageRenderer,
    ) {
        $this->isPhpSpreadsheetInstalled = class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class);
    }

    protected function initializeAction(): void
    {
        $this->piVars = $this->request->getArguments();
        $this->id = (int)($this->request->getParsedBody()['id'] ?? $this->request->getQueryParams()['id'] ?? null);

        $this->moduleData = $this->request->getAttribute('moduleData');
        $this->moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $this->moduleTemplate->setTitle('Powermail');
        $this->moduleTemplate->setFlashMessageQueue($this->getFlashMessageQueue());
        $this->moduleTemplate->makeDocHeaderModuleMenu(['id' => $this->id]);
    }

    /**
     * @noinspection PhpUnused
     */
    public function dispatchAction(string $forwardToAction = 'list'): ResponseInterface
    {
        return (new ForwardResponse($forwardToAction))
            ->withControllerName('Module')
            ->withExtensionName('Powermail');
    }

    /**
     * @throws InvalidQueryException
     * @throws RouteNotFoundException
     * @throws NoSuchArgumentException
     * @noinspection PhpUnused
     */
    public function listAction(): ResponseInterface
    {
        $formUids = $this->mailRepository->findGroupedFormUidsToGivenPageUid($this->id);
        $mails = $this->mailRepository->findAllInPid($this->id, $this->settings, $this->piVars);

        $currentPage = (int)($this->request->getQueryParams()['currentPage'] ?? 1);
        $currentPage = $currentPage > 0 ? $currentPage : 1;

        $itemsPerPage = (int)($this->settings['perPage'] ?? 10);
        $paginator = GeneralUtility::makeInstance(QueryResultPaginator::class, $mails, $currentPage, $itemsPerPage);
        $pagination = GeneralUtility::makeInstance(SlidingWindowPagination::class, $paginator, 15);

        $firstFormUid = StringUtility::conditionalVariable($this->piVars['filter']['form'] ?? '', key($formUids));
        $beUser = BackendUtility::getBackendUserAuthentication();
        $this->moduleTemplate->assignMultiple([
            'mails' => $mails,
            'formUids' => $formUids,
            'firstForm' => $this->formRepository->findByUid((int)$firstFormUid),
            'piVars' => $this->piVars,
            'pid' => $this->id,
            'moduleUri' => BackendUtility::getRoute('ajax_record_process'),
            'pagination' => [
                'pagination' => $pagination,
                'paginator' => $paginator,
            ],
            'settings' => $this->settings,
            'perPage' => $this->settings['perPage'] ?? 10,
            'writeAccess' => $beUser->check('tables_modify', Answer::TABLE_NAME)
                && $beUser->check('tables_modify', Mail::TABLE_NAME),
            'activateXlsxExport' => $this->isPhpSpreadsheetInstalled,
        ]);

        $this->moduleTemplate->makeDocHeaderModuleMenu(['id' => $this->id]);
        return $this->moduleTemplate->renderResponse('Module/List');
    }

    /**
     * @throws InvalidQueryException
     * @noinspection PhpUnused
     */
    public function exportXlsAction(): ?ResponseInterface
    {
        if ($this->isPhpSpreadsheetInstalled) {
            $this->view->assignMultiple(
                [
                    'mails' => $this->mailRepository->findAllInPid($this->id, $this->settings, $this->piVars),
                    'fieldUids' => GeneralUtility::trimExplode(
                        ',',
                        StringUtility::conditionalVariable($this->piVars['export']['fields'] ?? '', ''),
                        true
                    ),
                ]
            );

            $fileName = StringUtility::conditionalVariable($this->settings['export']['filenameXls'] ?? '', 'export.xls');
            $tmpFilename = GeneralUtility::tempnam('export_');

            $reader = new Html();
            $spreadsheet = $reader->loadFromString($this->view->render());

            $writer = IOFactory::createWriter($spreadsheet, 'Xls');
            $writer->save($tmpFilename);

            return $this->responseFactory->createResponse()
                ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
                ->withAddedHeader('Content-Transfer-Encoding', 'Binary')
                ->withAddedHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
                ->withAddedHeader('Pragma', 'no-cache')
                ->withBody($this->streamFactory->createStreamFromFile($tmpFilename));
        }

        return null;
    }

    /**
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
                    StringUtility::conditionalVariable($this->piVars['export']['fields'] ?? '', ''),
                    true
                ),
            ]
        );

        $fileName = StringUtility::conditionalVariable($this->settings['export']['filenameCsv'] ?? '', 'export.csv');
        return $this->htmlResponse()
            ->withHeader('Content-Type', 'text/x-csv')
            ->withAddedHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->withAddedHeader('Pragma', 'no-cache')
        ;
    }

    /**
     * @throws InvalidQueryException
     * @throws RouteNotFoundException
     * @noinspection PhpUnused
     */
    public function reportingFormBeAction(): ResponseInterface
    {
        $mails = $this->mailRepository->findAllInPid($this->id, $this->settings, $this->piVars);
        $firstMail = $this->mailRepository->findFirstInPid($this->id);
        $groupedAnswers = ReportingUtility::getGroupedAnswersFromMails($mails);

        $this->moduleTemplate->assignMultiple(
            [
                'groupedAnswers' => $groupedAnswers,
                'mails' => $mails,
                'firstMail' => $firstMail,
                'piVars' => $this->piVars,
                'pid' => $this->id,
                'moduleUri' => BackendUtility::getRoute('ajax_record_process'),
                'perPage' => ($this->settings['perPage'] ?? 10),
            ]
        );
        return $this->moduleTemplate->renderResponse('Module/ReportingFormBe');
    }

    /**
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

        $this->moduleTemplate->assignMultiple(
            [
                'groupedMarketingStuff' => $groupedMarketing,
                'mails' => $mails,
                'firstMail' => $firstMail,
                'piVars' => $this->piVars,
                'pid' => $this->id,
                'moduleUri' => BackendUtility::getRoute('ajax_record_process'),
                'perPage' => ($this->settings['perPage'] ?? 10),
            ]
        );
        return $this->moduleTemplate->renderResponse('Module/ReportingMarketingBe');
    }

    /**
     * @throws InvalidQueryException
     * @throws ExceptionExtbaseObject
     * @noinspection PhpUnused
     */
    public function overviewBeAction(): ResponseInterface
    {
        $forms = $this->formRepository->findAllInPidAndRootline($this->id);
        $this->moduleTemplate->assign('forms', $forms);
        $this->moduleTemplate->assign('pid', $this->id);
        $this->moduleTemplate->makeDocHeaderModuleMenu(['id' => $this->id]);
        return $this->moduleTemplate->renderResponse('Module/OverviewBe');
    }

    public function initializeCheckBeAction(): void
    {
        $this->checkAdminPermissions();
    }

    public function checkBeAction(): ResponseInterface
    {
        $this->moduleTemplate->assign('pid', $this->id);
        $this->moduleTemplate->assign('settings', $this->settings['setup'] ?? []);
        $this->sendTestEmail($this->piVars['email'] ?? null);
        return $this->moduleTemplate->renderResponse('Module/CheckBe');
    }

    protected function sendTestEmail(?string $email = null): void
    {
        if ($email !== null && GeneralUtility::validEmail($email)) {
            $body = 'New Test Email from User ' . BackendUtility::getPropertyFromBackendUser('username');
            $body .= ' (' . GeneralUtility::getIndpEnv('HTTP_HOST') . ')';
            $senderEmail = ConfigurationUtility::getDefaultMailFromAddress('powermail@domain.net');
            $this->moduleTemplate->assignMultiple(
                [
                    'issent' => MailUtility::sendPlainMail($email, $senderEmail, 'New Powermail Test Email', $body),
                    'email' => $email,
                ]
            );
        }
    }

    /**
     * @noinspection PhpUnused
     */
    public function initializeConverterBeAction(): void
    {
        $this->checkAdminPermissions();
    }

    /**
     * @noinspection PhpUnused
     */
    public function initializeFixUploadFolderAction(): void
    {
        $this->checkAdminPermissions();
    }

    /**
     * @throws FileCannotBeCreatedException
     * @noinspection PhpUnused
     */
    public function fixUploadFolderAction(): ResponseInterface
    {
        BasicFileUtility::createFolderIfNotExists(GeneralUtility::getFileAbsFileName('uploads/tx_powermail/'));
        return new ForwardResponse('checkBe');
    }

    /**
     * @noinspection PhpUnused
     */
    public function initializeFixWrongLocalizedFormsAction(): void
    {
        $this->checkAdminPermissions();
    }

    /**
     * @noinspection PhpUnused
     */
    public function fixWrongLocalizedFormsAction(): ResponseInterface
    {
        $this->formRepository->fixWrongLocalizedForms();
        return new ForwardResponse('checkBe');
    }

    /**
     * @noinspection PhpUnused
     */
    public function initializeFixWrongLocalizedPagesAction(): void
    {
        $this->checkAdminPermissions();
    }

    /**
     * @noinspection PhpUnused
     */
    public function fixWrongLocalizedPagesAction(): ResponseInterface
    {
        $pageRepository = GeneralUtility::makeInstance(PageRepository::class);
        $pageRepository->fixWrongLocalizedPages();
        return new ForwardResponse('checkBe');
    }

    /**
     * Check if admin is logged in
     *        If not, forward to tools overview
     */
    protected function checkAdminPermissions(): ?ResponseInterface
    {
        if (!BackendUtility::isBackendAdmin()) {
            return new ForwardResponse('toolsBe');
        }

        return null;
    }

    public function downloadFile(ServerRequestInterface $request): ?ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        if (array_key_exists('file', $queryParams)) {
            $fileName = basename($queryParams['file']);
            $absoluteFileName = GeneralUtility::getFileAbsFileName($queryParams['file']);
            if (is_file($absoluteFileName)) {
                (mime_content_type($absoluteFileName) === false) ? $mimeType = '' : $mimeType = mime_content_type($absoluteFileName);
                return $this->responseFactory->createResponse()
                    ->withHeader('Content-Type', $mimeType)
                    ->withAddedHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
                    ->withBody($this->streamFactory->createStreamFromFile($absoluteFileName));
            }
        }

        return new ForwardResponse('list');
    }
}
