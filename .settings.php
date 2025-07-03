<?php
return [
    'services' => [
        'value' => [
            'itscript.rmq.ModuleService' => [
                'className' => '\\Itscript\\Rmq\\Services\\ModuleService',
            ],
            'itscript.rmq.SomeQueueListenCommand' => [
                'className' => '\\Itscript\\Rmq\\Commands\\SomeQueueListenCommand',
            ],
        ],
        'readonly' => true,
    ]
];