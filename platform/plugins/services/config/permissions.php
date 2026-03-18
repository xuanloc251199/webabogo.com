<?php

return [
    [
        'name' => 'Services',
        'flag' => 'services.index',
    ],
    [
        'name' => 'Create',
        'flag' => 'services.create',
        'parent_flag' => 'services.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'services.edit',
        'parent_flag' => 'services.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'services.destroy',
        'parent_flag' => 'services.index',
    ],
];
