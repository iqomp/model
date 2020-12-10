<?php

return [
    'handlers' => [
        'chain' => [
            'handler' => 'Iqomp\\Model\\Formatter::chain',
            'collective' => true,
            'field' => 'id'
        ],
        'multiple-object' => [
            'handler' => 'Iqomp\\Model\\Formatter::multipleObject',
            'collective' => true,
            'field' => null
        ],
        'object' => [
            'handler' => 'Iqomp\\Model\\Formatter::object',
            'collective' => true,
            'field' => null
        ],
        'object-switch' => [
            'handler' => 'Iqomp\\Model\\Formatter::objectSwitch',
            'collective' => 'id',
            'field' => null
        ],
        'partial' => [
            'handler' => 'Iqomp\\Model\\Formatter::partial',
            'collective' => true,
            'field' => 'id'
        ]
    ]
];
