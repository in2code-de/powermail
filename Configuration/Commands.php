<?php
declare(strict_types = 1);

use In2code\Powermail\Command\CleanupExportsCommand;
use In2code\Powermail\Command\CleanupUnusedUploadsCommand;
use In2code\Powermail\Command\CleanupUploadsCommand;
use In2code\Powermail\Command\ExportCommand;
use In2code\Powermail\Command\ResetMarkersCommand;

return [
    'powermail:export' => [
        'class' => ExportCommand::class,
        'schedulable' => true
    ],
    'powermail:resetMarkers' => [
        'class' => ResetMarkersCommand::class,
        'schedulable' => true
    ],
    'powermail:cleanupUploads' => [
        'class' => CleanupUploadsCommand::class,
        'schedulable' => true
    ],
    'powermail:cleanupExports' => [
        'class' => CleanupExportsCommand::class,
        'schedulable' => true
    ],
    'powermail:cleanupUnusedUploads' => [
        'class' => CleanupUnusedUploadsCommand::class,
        'schedulable' => true
    ]
];
