<?php

return [
    'errors' => [
        '14.0' => 'not unique',
        '19.0' => 'not exists on db',
        '20.0' => 'one or more not exists on db'
    ],
    'validators' => [
        'unique' => 'Iqomp\\Model\\Validator::unique',
        'exists' => 'Iqomp\\Model\\Validator::exists',
        'exists-list' => 'Iqomp\\Model\\Validator::existsList'
    ]
];
