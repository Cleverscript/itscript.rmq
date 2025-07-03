<?php

$_SERVER["DOCUMENT_ROOT"] = realpath(__DIR__ . '/../../../../..');

require_once $_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/cli/bootstrap.php';

use Symfony\Component\Console;
use Bitrix\Main\DI\ServiceLocator;
use Itscript\Rmq\Commands\QueueListen;

$console = new Console\Application();

$container = ServiceLocator::getInstance();

$console->add($container->get(ITSCRIPT_RMQ_MODULE_ID . '.SomeQueueListenCommand'));

$console->run();