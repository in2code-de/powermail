<?php

declare(strict_types=1);

use In2code\Powermail\Domain\Model\BackendUser;
use In2code\Powermail\Domain\Model\BackendUserGroup;
use In2code\Powermail\Domain\Model\User;
use In2code\Powermail\Domain\Model\UserGroup;

return [
    User::class => [
        'tableName' => 'fe_users',
    ],
    UserGroup::class => [
        'tableName' => 'fe_groups',
    ],
    BackendUser::class => [
        'tableName' => 'fe_users',
    ],
    BackendUserGroup::class => [
        'tableName' => 'fe_groups',
    ],
];
