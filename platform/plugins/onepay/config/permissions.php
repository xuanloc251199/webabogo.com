<?php

return [
    [
        'name' => 'Onepays',
        'flag' => 'onepay.index',
    ],
    [
        'name' => 'Create',
        'flag' => 'onepay.create',
        'parent_flag' => 'onepay.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'onepay.edit',
        'parent_flag' => 'onepay.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'onepay.destroy',
        'parent_flag' => 'onepay.index',
    ],
];
