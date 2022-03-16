<?php
declare(strict_types = 1);
namespace In2code\Powermail\Controller;

use Doctrine\DBAL\DBALException;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Exception\DeprecatedException;
use In2code\Powermail\Utility\ArrayUtility;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\FrontendUtility;
use In2code\Powermail\Utility\LocalizationUtility;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Annotation as ExtbaseAnnotation;
use TYPO3\CMS\Extbase\Http\ForwardResponse;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidArgumentNameException;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
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
    public function listAction(): ResponseInterface
    {
        $searchSettings = $this->settings['search'] ?? [];
        $listSettings = $this->settings['list'] ?? [];
        $this->prepareFilterPluginVariables($this->piVars, (array)($searchSettings['staticPluginsVariables'] ?? []));
        $fieldArray = $this->getFieldList((string)($listSettings['fields'] ?? ''));
        $searchFields = $this->fieldRepository->findByUids(
            GeneralUtility::trimExplode(',', $searchSettings['fields'] ?? '', true)
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
        return $this->htmlResponse();
    }

    /**
     * @param Mail $mail
     * @return void
     * @noinspection PhpUnused
     * @throws Exception
     */
    public function showAction(Mail $mail): ResponseInterface
    {
        $fieldArray = $this->getFieldList($this->settings['single']['fields']);
        $this->view->assignMultiple(
            [
                'fields' => $this->fieldRepository->findByUids($fieldArray),
                'mail' => $mail
            ]
        );
        $this->assignMultipleActions();
        return $this->htmlResponse();
    }

    /**
     * @param Mail $mail
     * @return void
     * @noinspection PhpUnused
     * @throws Exception
     */
    public function editAction(Mail $mail = null): ResponseInterface
    {
        $fieldArray = $this->getFieldList($this->settings['edit']['fields']);
        $this->view->assignMultiple(
            [
                'selectedFields' => $this->fieldRepository->findByUids($fieldArray),
                'mail' => $mail
            ]
        );
        $this->assignMultipleActions();
        return $this->htmlResponse();
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
    public function initializeUpdateAction()
    {
        $arguments = $this->request->getArguments();
        if (!FrontendUtility::isAllowedToEdit($this->settings, $arguments['field']['__identity'])) {
            $this->controllerContext = $this->buildControllerContext();
            $this->addFlashmessage(
                LocalizationUtility::translate('PowermailFrontendEditFailed'),
                '',
                AbstractMessage::ERROR
            );
            return new ForwardResponse('list');
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
    public function initializeDeleteAction()
    {
        $arguments = $this->request->getArguments();
        if (!FrontendUtility::isAllowedToEdit($this->settings, $arguments['mail'])) {
            $this->controllerContext = $this->buildControllerContext();
            $this->addFlashmessage(
                LocalizationUtility::translate('PowermailFrontendDeleteFailed'),
                '',
                AbstractMessage::ERROR
            );
            return new ForwardResponse('list');
        }
    }

    /**
     * @param Mail $mail
     * @return void
     * @throws IllegalObjectTypeException
     * @noinspection PhpUnused
     */
    public function deleteAction(Mail $mail): ResponseInterface
    {
        $this->assignMultipleActions();
        $this->mailRepository->remove($mail);
        $this->addFlashmessage(LocalizationUtility::translate('PowermailFrontendDeleteSuccessful'));
        return $this->htmlResponse();
    }

    /**
     * @param array $export Field Array with mails and format
     * @return void
     * @throws InvalidQueryException
     * @throws StopActionException
     * @throws Exception
     * @noinspection PhpUnused
     */
    public function exportAction(array $export = []): ResponseInterface
    {
        if (!$this->settings['list']['export']) {
            return $this->htmlResponse(null);
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
            return (new ForwardResponse('exportXls'))->withArguments(['mails' => $mails, 'fields' => $fields]);
        }
        return (new ForwardResponse('exportCsv'))->withArguments(['mails' => $mails, 'fields' => $fields]);
        return $this->htmlResponse();
    }

    /**
     * @param QueryResult $mails mails objects
     * @param array $fields uid field list
     * @return void
     * @noinspection PhpUnused
     */
    public function exportXlsAction(QueryResult $mails = null, array $fields = []): ResponseInterface
    {
        $this->view->assign('mails', $mails);
        $this->view->assign('fields', $fields);
        return $this->htmlResponse();
    }

    /**
     * @param QueryResult $mails mails objects
     * @param array $fields uid field list
     * @return void
     * @noinspection PhpUnused
     */
    public function exportCsvAction(QueryResult $mails = null, array $fields = []): ResponseInterface
    {
        $this->view->assign('mails', $mails);
        $this->view->assign('fields', $fields);
        return $this->htmlResponse();
    }

    /**
     * @return void
     * @throws InvalidQueryException
     * @noinspection PhpUnused
     */
    public function rssAction(): ResponseInterface
    {
        $mails = $this->mailRepository->findListBySettings($this->settings, $this->piVars);
        $this->view->assign('mails', $mails);
        $this->assignMultipleActions();
        return $this->htmlResponse();
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
            return GeneralUtility::trimExplode(',', $list, true);
        }

        if (\TYPO3\CMS\Core\Utility\ArrayUtility::isValidPath($this->settings, 'main/form')) {
            return $this->formRepository->getFieldUidsFromForm(((int)$this->settings['main']['form']));
        }
        return [];
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
