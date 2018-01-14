<?php
declare(strict_types=1);
namespace In2code\Powermail\Controller;

use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Repository\PageRepository;
use In2code\Powermail\Utility\BackendUtility;
use In2code\Powermail\Utility\BasicFileUtility;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\MailUtility;
use In2code\Powermail\Utility\ReportingUtility;
use In2code\Powermail\Utility\StringUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ModuleController for backend modules
 */
class ModuleController extends AbstractController
{

    /**
     * @param string $forwardToAction
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @return void
     */
    public function dispatchAction($forwardToAction = 'list')
    {
        $this->forward($forwardToAction);
    }

    /**
     * List View Backend
     *
     * @return void
     */
    public function listAction()
    {
        $formUids = $this->mailRepository->findGroupedFormUidsToGivenPageUid($this->id);
        $firstFormUid = StringUtility::conditionalVariable($this->piVars['filter']['form'], key($formUids));
        $beUser = BackendUtility::getBackendUserAuthentication();
        $this->view->assignMultiple(
            [
                'mails' => $this->mailRepository->findAllInPid($this->id, $this->settings, $this->piVars),
                'formUids' => $formUids,
                'firstForm' => $this->formRepository->findByUid($firstFormUid),
                'piVars' => $this->piVars,
                'pid' => $this->id,
                'moduleUri' => BackendUtility::getModuleUrl('tce_db'),
                'perPage' => ($this->settings['perPage'] ? $this->settings['perPage'] : 10),
                'writeAccess' => $beUser->check('tables_modify', Answer::TABLE_NAME)
                    && $beUser->check('tables_modify', Mail::TABLE_NAME),
            ]
        );
    }

    /**
     * Export Action for XLS Files
     *
     * @return void
     */
    public function exportXlsAction()
    {
        $this->view->assignMultiple(
            [
                'mails' => $this->mailRepository->findAllInPid($this->id, $this->settings, $this->piVars),
                'fieldUids' => GeneralUtility::trimExplode(
                    ',',
                    StringUtility::conditionalVariable($this->piVars['export']['fields'], ''),
                    true
                )
            ]
        );

        $fileName = StringUtility::conditionalVariable($this->settings['export']['filenameXls'], 'export.xls');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: inline; filename="' . $fileName . '"');
        header('Pragma: no-cache');
    }

    /**
     * Export Action for CSV Files
     *
     * @return void
     */
    public function exportCsvAction()
    {
        $this->view->assignMultiple(
            [
                'mails' => $this->mailRepository->findAllInPid($this->id, $this->settings, $this->piVars),
                'fieldUids' => GeneralUtility::trimExplode(
                    ',',
                    StringUtility::conditionalVariable($this->piVars['export']['fields'], ''),
                    true
                )
            ]
        );

        $fileName = StringUtility::conditionalVariable($this->settings['export']['filenameCsv'], 'export.csv');
        header('Content-Type: text/x-csv');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Pragma: no-cache');
    }

    /**
     * Reporting Form
     *
     * @return void
     */
    public function reportingFormBeAction()
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
                'moduleUri' => BackendUtility::getModuleUrl('tce_db'),
                'perPage' => ($this->settings['perPage'] ? $this->settings['perPage'] : 10)
            ]
        );
    }

    /**
     * Reporting Marketing
     *
     * @return void
     */
    public function reportingMarketingBeAction()
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
                'moduleUri' => BackendUtility::getModuleUrl('tce_db'),
                'perPage' => ($this->settings['perPage'] ? $this->settings['perPage'] : 10)
            ]
        );
    }

    /**
     * Form Overview
     *
     * @return void
     */
    public function overviewBeAction()
    {
        $forms = $this->formRepository->findAllInPidAndRootline($this->id);
        $this->view->assign('forms', $forms);
        $this->view->assign('pid', $this->id);
    }

    /**
     * Check Permissions
     *
     * @return void
     */
    public function initializeCheckBeAction()
    {
        $this->checkAdminPermissions();
    }

    /**
     * Check View Backend
     *
     * @param string $email email address
     * @return void
     */
    public function checkBeAction($email = null)
    {
        $this->view->assign('pid', $this->id);
        $this->sendTestEmail($email);
    }

    /**
     * Send plain test mail with swiftmailer
     *
     * @param null $email
     * @return void
     */
    protected function sendTestEmail($email = null)
    {
        if ($email !== null && GeneralUtility::validEmail($email)) {
            $body = 'New Test Email from User ' . BackendUtility::getPropertyFromBackendUser('username');
            $body .= ' (' . GeneralUtility::getIndpEnv('HTTP_HOST') . ')';
            $senderEmail = ConfigurationUtility::getDefaultMailFromAddress('powermail@domain.net');
            $this->view->assignMultiple(
                [
                    'issent' => MailUtility::sendPlainMail($email, $senderEmail, 'New Powermail Test Email', $body),
                    'email' => $email
                ]
            );
        }
    }

    /**
     * Check Permissions
     *
     * @return void
     */
    public function initializeConverterBeAction()
    {
        $this->checkAdminPermissions();
    }

    /**
     * Check Permissions
     *
     * @return void
     */
    public function initializeFixUploadFolderAction()
    {
        $this->checkAdminPermissions();
    }

    /**
     * Create an upload folder
     *
     * @return void
     */
    public function fixUploadFolderAction()
    {
        BasicFileUtility::createFolderIfNotExists(GeneralUtility::getFileAbsFileName('uploads/tx_powermail/'));
        $this->redirect('checkBe');
    }

    /**
     * Check Permissions
     *
     * @return void
     */
    public function initializeFixWrongLocalizedFormsAction()
    {
        $this->checkAdminPermissions();
    }

    /**
     * Fix wrong localized forms
     *
     * @return void
     */
    public function fixWrongLocalizedFormsAction()
    {
        $this->formRepository->fixWrongLocalizedForms();
        $this->redirect('checkBe');
    }

    /**
     * Check Permissions
     *
     * @return void
     */
    public function initializeFixWrongLocalizedPagesAction()
    {
        $this->checkAdminPermissions();
    }

    /**
     * Fix wrong localized pages
     *
     * @return void
     */
    public function fixWrongLocalizedPagesAction()
    {
        $pageRepository = $this->objectManager->get(PageRepository::class);
        $pageRepository->fixWrongLocalizedPages();
        $this->redirect('checkBe');
    }

    /**
     * Check if admin is logged in
     *        If not, forward to tools overview
     *
     * @return void
     */
    protected function checkAdminPermissions()
    {
        if (!BackendUtility::isBackendAdmin()) {
            $this->controllerContext = $this->buildControllerContext();
            $this->forward('toolsBe');
        }
    }
}
