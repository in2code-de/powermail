<?php

declare(strict_types=1);

namespace In2code\Powermail\UserFunc;

use TYPO3\CMS\Core\Configuration\Features;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TestForFeature
{
    public function isFeatureEnabled(array $arguments = []): bool
    {
        $features = GeneralUtility::makeInstance(Features::class);
        if ($features->isFeatureEnabled($arguments['conditionParameters'][0])) {
            return true;
        }
        return false;
    }
}
