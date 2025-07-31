<?php
return [
    'services' => [
        'value' => [
            'itscript.rmq.ModuleService' => [
                'className' => '\\Itscript\\Rmq\\Services\\ModuleService',
            ],
            'itscript.rmq.AMQPStreamConnectionService' => [
                'className' => '\\Itscript\\Rmq\\Services\\AMQPStreamConnectionService',
            ],
            'itscript.rmq.SomeQueueListenCommand' => [
                'className' => '\\Itscript\\Rmq\\Commands\\SomeQueueListenCommand',
            ],
        ],
        'readonly' => true,
    ]
];