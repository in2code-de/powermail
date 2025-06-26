<?php

declare(strict_types=1);

namespace In2code\Powermail\EventListener;

use In2code\Powermail\Utility\DatabaseUtility;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Configuration\Event\AfterFlexFormDataStructureParsedEvent;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class FlexFormParsingModifyEventListener
{
    protected array $allowedSheets = [
        'main',
        'receiver',
        'sender',
        'thx',
    ];

    public function modifyDataStructure(AfterFlexFormDataStructureParsedEvent $event): void
    {
        // nothing to do in cli context
        if (Environment::isCli()) {
            return;
        }

        $identifier = $event->getIdentifier();

        if (($identifier['dataStructureKey'] ?? '') === '*,powermail_pi1') {
            $parsedDataStructure = $event->getDataStructure();
            foreach ($this->getFieldConfiguration() as $key => $fieldConfiguration) {
                $sheet = $this->getSheetNameAndRemoveFromConfiguration($fieldConfiguration);
                $parsedDataStructure['sheets'][$sheet]['ROOT']['el'][$key] = $fieldConfiguration;
            }

            $event->setDataStructure($parsedDataStructure);
        }
    }

    /**
     * Get field configuration from page TSconfig
     *
     * @param int $pid Record pid
     */
    protected function getFieldConfiguration(): array
    {
        $pid = $this->getPidForCurrentRecord();

        $tsConfiguration = BackendUtility::getPagesTSconfig($pid);
        if (!empty($tsConfiguration['tx_powermail.']['flexForm.']['addField.'])) {
            $eConfiguration = $tsConfiguration['tx_powermail.']['flexForm.']['addField.'];
            $tsService = GeneralUtility::makeInstance(TypoScriptService::class);
            return $tsService->convertTypoScriptArrayToPlainArray($eConfiguration);
        }

        return [];
    }

    /**
     * Get sheetname and remove from configuration array
     */
    protected function getSheetNameAndRemoveFromConfiguration(array &$configuration): string
    {
        $sheet = $this->allowedSheets[0];
        if (!empty($configuration['_sheet']) && in_array($configuration['_sheet'], $this->allowedSheets)) {
            $sheet = $configuration['_sheet'];
        }

        unset($configuration['_sheet']);
        return $sheet;
    }

    private function getPidForCurrentRecord(): int
    {
        $uid = 0;
        $request = $this->getRequest();
        if ($request instanceof \Psr\Http\Message\ServerRequestInterface) {
            $queryParams = $request->getQueryParams();
            $uid = (int)(array_keys($queryParams['edit']['tt_content'] ?? [])[0] ?? 0);
        }

        if (0 === $uid) {
            return 0;
        }

        return DatabaseUtility::getPidForRecord($uid, 'tt_content');
    }

    private function getRequest(): ?ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }
}
