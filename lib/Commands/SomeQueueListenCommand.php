<?php

namespace Itscript\Rmq\Commands;

use Bitrix\Main\Application;
use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\SystemException;
use Symfony\Component\Console\Helper\DebugFormatterHelper;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class SomeQueueListenCommand extends AbstractCommand
{
    protected const EXCHANGE_NAME = ITSCRIPT_RMQ_MODULE_ID . '.direct';
    protected const QUEUE_NAME = ITSCRIPT_RMQ_MODULE_ID . '.some';
    public const BEGIN_MSG = 'Начато чтение из очереди ' . self::QUEUE_NAME;
    public const END_MSG = 'Чтение из очереди завершено' . self::QUEUE_NAME;

    protected function configure()
    {
        $this->setName('queue:listen')
            ->setAliases(['q:l'])
            ->setDescription('Queue listen')
            ->setHelp('Queue listen')
            /*->setDefinition(new InputDefinition([
                new InputOption('type', 't', InputOption::VALUE_OPTIONAL, 'clear cache type', 'full'),
            ]))*/;
        parent::configure();
    }

    protected function exec(InputInterface $input, OutputInterface $output): void
    {
        $moduleService = ServiceLocator::getInstance()->get(ITSCRIPT_RMQ_MODULE_ID . '.ModuleService');

        $exchange = self::EXCHANGE_NAME;
        $queue = self::QUEUE_NAME;
        $consumerTag = 'consumer';

        // TODO: вынести кудато глобально в ServiceLocator например в include.php
        $connection = new AMQPStreamConnection(
            $moduleService->getPropVal('ITSCRIPT_RMQ_HOST'),
            $moduleService->getPropVal('ITSCRIPT_RMQ_PORT'),
            $moduleService->getPropVal('ITSCRIPT_RMQ_USER'),
            $moduleService->getPropVal('ITSCRIPT_RMQ_PASS'),
            $moduleService->getPropVal('ITSCRIPT_RMQ_VHOST')
        );

        $channel = $connection->channel();

        $callback = function ($message) use ($channel, $exchange, $queue) {
            //$data = json_decode($message->body, true);

            pLog($message->body);
        };

        $channel->basic_consume($queue, $consumerTag, false, true, false, false, $callback);

        // Listening cycle
        while ($channel->is_consuming()) {
            $channel->wait();
        }
    }
}
