<?php
declare(strict_types=1);
namespace In2code\Powermail\Controller;

use Doctrine\DBAL\DBALException;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Exception\DeprecatedException;
use In2code\Powermail\Utility\ArrayUtility;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\FrontendUtility;
use In2code\Powermail\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Annotation as ExtbaseAnnotation;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidArgumentNameException;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException;

/**
 * Controller for powermail frontend output in Pi2
 * (former part of the powermail_frontend extension)
 */
class OutputController extends AbstractController
{
    /**
     * @return void
     * @throws InvalidQueryException
     * @throws Exception
     * @noinspection PhpUnused
     */
    public function listAction(): void
    {
        $this->prepareFilterPluginVariables($this->piVars, (array)$this->settings['search']['staticPluginsVariables']);
        $fieldArray = $this->getFieldList((string)$this->settings['list']['fields']);
        $searchFields = $this->fieldRepository->findByUids(
            GeneralUtility::trimExplode(',', $this->settings['search']['fields'], true)
        );
        $this->view->assignMultiple(
            [
                'mails' => $this->mailRepository->findListBySettings($this->settings, $this->piVars),
                'searchFields' => $searchFields,
                'fields' => $this->fieldRepository->findByUids($fieldArray),
                'piVars' => $this->piVars,
                'abc' => ArrayUtility::getAbcArray()
            ]
        );
        $this->assignMultipleActions();
    }

    /**
     * @param Mail $mail
     * @return void
     * @noinspection PhpUnused
     * @throws Exception
     */
    public function showAction(Mail $mail): void
    {
        $fieldArray = $this->getFieldList($this->settings['single']['fields']);
        $this->view->assignMultiple(
            [
                'fields' => $this->fieldRepository->findByUids($fieldArray),
                'mail' => $mail
            ]
        );
        $this->assignMultipleActions();
    }

    /**
     * @param Mail $mail
     * @return void
     * @noinspection PhpUnused
     * @throws Exception
     */
    public function editAction(Mail $mail = null): void
    {
        $fieldArray = $this->getFieldList($this->settings['edit']['fields']);
        $this->view->assignMultiple(
            [
                'selectedFields' => $this->fieldRepository->findByUids($fieldArray),
                'mail' => $mail
            ]
        );
        $this->assignMultipleActions();
    }

    /**
     * @return void
     * @throws InvalidArgumentNameException
     * @throws InvalidQueryException
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     * @throws NoSuchArgumentException
     * @throws StopActionException
     * @throws DBALException
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws Exception
     * @throws DeprecatedException
     * @noinspection PhpUnused
     */
    public function initializeUpdateAction(): void
    {
        $arguments = $this->request->getArguments();
        if (!FrontendUtility::isAllowedToEdit($this->settings, $arguments['field']['__identity'])) {
            $this->controllerContext = $this->buildControllerContext();
            $this->addFlashmessage(
                LocalizationUtility::translate('PowermailFrontendEditFailed'),
                '',
                AbstractMessage::ERROR
            );
            $this->forward('list');
        }
        $this->reformatParamsForAction();
    }

    /**
     * @param Mail $mail
     * @ExtbaseAnnotation\Validate("In2code\Powermail\Domain\Validator\InputValidator", param="mail")
     * @return void
     * @throws StopActionException
     * @throws UnsupportedRequestTypeException
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     * @throws \Exception
     * @noinspection PhpUnused
     */
    public function updateAction(Mail $mail): void
    {
        $this->uploadService->uploadAllFiles();
        $this->mailRepository->update($mail);
        $this->addFlashmessage(LocalizationUtility::translate('PowermailFrontendEditSuccessful'));
        $this->redirect('edit', null, null, ['mail' => $mail]);
    }

    /**
     * @return void
     * @throws DBALException
     * @throws Exception
     * @throws StopActionException
     * @noinspection PhpUnused
     */
    public function initializeDeleteAction(): void
    {
        $arguments = $this->request->getArguments();
        if (!FrontendUtility::isAllowedToEdit($this->settings, $arguments['mail'])) {
            $this->controllerContext = $this->buildControllerContext();
            $this->addFlashmessage(
                LocalizationUtility::translate('PowermailFrontendDeleteFailed'),
                '',
                AbstractMessage::ERROR
            );
            $this->forward('list');
        }
    }

    /**
     * @param Mail $mail
     * @return void
     * @throws IllegalObjectTypeException
     * @noinspection PhpUnused
     */
    public function deleteAction(Mail $mail): void
    {
        $this->assignMultipleActions();
        $this->mailRepository->remove($mail);
        $this->addFlashmessage(LocalizationUtility::translate('PowermailFrontendDeleteSuccessful'));
    }

    /**
     * @param array $export Field Array with mails and format
     * @return void
     * @throws InvalidQueryException
     * @throws StopActionException
     * @throws Exception
     * @noinspection PhpUnused
     */
    public function exportAction(array $export = []): void
    {
        if (!$this->settings['list']['export']) {
            return;
        }
        $mails = $this->mailRepository->findByUidList($export['fields']);

        // get field array for output
        if ($this->settings['list']['fields']) {
            $fieldArray = GeneralUtility::trimExplode(',', $this->settings['list']['fields'], true);
        } else {
            $fieldArray = $this->formRepository->getFieldUidsFromForm((int)$this->settings['main']['form']);
        }
        $fields = $this->fieldRepository->findByUids($fieldArray);

        if ($export['format'] === 'xls') {
            $this->forward('exportXls', null, null, ['mails' => $mails, 'fields' => $fields]);
        }
        $this->forward('exportCsv', null, null, ['mails' => $mails, 'fields' => $fields]);
    }

    /**
     * @param QueryResult $mails mails objects
     * @param array $fields uid field list
     * @return void
     * @noinspection PhpUnused
     */
    public function exportXlsAction(QueryResult $mails = null, array $fields = []): void
    {
        $this->view->assign('mails', $mails);
        $this->view->assign('fields', $fields);
    }

    /**
     * @param QueryResult $mails mails objects
     * @param array $fields uid field list
     * @return void
     * @noinspection PhpUnused
     */
    public function exportCsvAction(QueryResult $mails = null, array $fields = []): void
    {
        $this->view->assign('mails', $mails);
        $this->view->assign('fields', $fields);
    }

    /**
     * @return void
     * @throws InvalidQueryException
     * @noinspection PhpUnused
     */
    public function rssAction(): void
    {
        $mails = $this->mailRepository->findListBySettings($this->settings, $this->piVars);
        $this->view->assign('mails', $mails);
        $this->assignMultipleActions();
    }

    /**
     * @return void
     */
    public function initializeObject(): void
    {
        $this->settings = ConfigurationUtility::mergeTypoScript2FlexForm($this->settings, 'Pi2');
    }

    /**
     * Get fieldlist from list or from database
     *
     * @param string $list
     * @return array
     * @throws Exception
     */
    protected function getFieldList(string $list = ''): array
    {
        if (!empty($list)) {
            $fieldArray = GeneralUtility::trimExplode(',', $list, true);
        } else {
            $fieldArray = $this->formRepository->getFieldUidsFromForm((int)$this->settings['main']['form']);
        }
        return (array)$fieldArray;
    }

    /**
     * @return void
     */
    protected function assignMultipleActions(): void
    {
        if (empty($this->settings['single']['pid'])) {
            $this->settings['single']['pid'] = FrontendUtility::getCurrentPageIdentifier();
        }
        if (empty($this->settings['list']['pid'])) {
            $this->settings['list']['pid'] = FrontendUtility::getCurrentPageIdentifier();
        }
        if (empty($this->settings['edit']['pid'])) {
            $this->settings['edit']['pid'] = FrontendUtility::getCurrentPageIdentifier();
        }
        $this->view->assign('singlePid', $this->settings['single']['pid']);
        $this->view->assign('listPid', $this->settings['list']['pid']);
        $this->view->assign('editPid', $this->settings['edit']['pid']);
    }

    /**
     * Add parameters to piVars from TypoScript
     *
     * @param array $pluginVariables
     * @param array $parameters
     * @return void
     */
    protected function prepareFilterPluginVariables(array &$pluginVariables, array $parameters): void
    {
        if (!empty($parameters['filter'])) {
            $pluginVariables = (array)$pluginVariables + (array)$parameters;
        }
    }
}
