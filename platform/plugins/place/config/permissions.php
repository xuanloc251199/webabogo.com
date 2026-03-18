<?php

return [
    [
        'name' => 'Places',
        'flag' => 'place.index',
    ],
    [
        'name' => 'Create',
        'flag' => 'place.create',
        'parent_flag' => 'place.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'place.edit',
        'parent_flag' => 'place.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'place.destroy',
        'parent_flag' => 'place.index',
    ],
];
