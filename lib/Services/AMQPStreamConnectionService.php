<?php

namespace Itscript\Rmq\Services;

use Bitrix\Main\DI\ServiceLocator;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class AMQPStreamConnectionService extends AMQPStreamConnection
{
    public function __construct()
    {
        $moduleService = ServiceLocator::getInstance()->get(ITSCRIPT_RMQ_MODULE_ID . '.ModuleService');

        parent::__construct(
            $moduleService->getPropVal('ITSCRIPT_RMQ_HOST'),
            $moduleService->getPropVal('ITSCRIPT_RMQ_PORT'),
            $moduleService->getPropVal('ITSCRIPT_RMQ_USER'),
            $moduleService->getPropVal('ITSCRIPT_RMQ_PASS'),
            $moduleService->getPropVal('ITSCRIPT_RMQ_VHOST')
        );
    }
}