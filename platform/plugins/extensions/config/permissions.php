<?php

return [
    [
        'name' => 'Extensions',
        'flag' => 'extensions.index',
    ],
    [
        'name' => 'Create',
        'flag' => 'extensions.create',
        'parent_flag' => 'extensions.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'extensions.edit',
        'parent_flag' => 'extensions.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'extensions.destroy',
        'parent_flag' => 'extensions.index',
    ],
];
