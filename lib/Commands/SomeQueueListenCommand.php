<?php

namespace Itscript\Rmq\Commands;

use Bitrix\Main\Application;
use Bitrix\Main\DI\ServiceLocator;
use Itscript\Rmq\Helpers\Logger;
use Bitrix\Main\SystemException;
use Symfony\Component\Console\Helper\DebugFormatterHelper;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class SomeQueueListenCommand extends AbstractCommand
{
    protected const EXCHANGE_NAME = 'direct';
    protected const QUEUE_NAME = 'some';
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
        $exchange = self::EXCHANGE_NAME;
        $queue = self::QUEUE_NAME;
        $consumerTag = 'consumer';

        if (ServiceLocator::getInstance()->has(self::AMPQSTREAM_CONNECTION_SERVICE_NAME)) {
            throw new SystemException('Service ' . self::AMPQSTREAM_CONNECTION_SERVICE_NAME . ' not registered in container');
        }

        $connection = ServiceLocator::getInstance()->get(self::AMPQSTREAM_CONNECTION_SERVICE_NAME);

        $channel = $connection->channel();

        $callback = function ($message) use ($channel, $exchange, $queue) {
            Logger::write($message->body);
        };

        $channel->basic_consume($queue, $consumerTag, false, true, false, false, $callback);

        // Listening cycle
        while ($channel->is_consuming()) {
            $channel->wait();
        }
    }
}
