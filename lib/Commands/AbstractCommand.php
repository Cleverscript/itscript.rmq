<?php

namespace Itscript\Rmq\Commands;

use Bitrix\Main\Loader;
use Bitrix\Main\SystemException;
use Itscript\Rmq\Helpers\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\DebugFormatterHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand extends Command
{
    public const BEGIN_MSG = '';
    public const END_MSG = '';

    public function __construct(string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure()
    {
        if (method_exists(Loader::class, 'setRequireThrowException')) {
            Loader::setRequireThrowException(false);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            if (!empty(static::BEGIN_MSG)) {
                $output->write((new DebugFormatterHelper)->start($this->getName(), static::BEGIN_MSG, "START"));
            }

            $this->exec($input, $output);

            if (!empty(static::END_MSG)) {
                $output->write((new DebugFormatterHelper)->stop(
                    $this->getName(),
                    static::END_MSG,
                    $this->isSuccess,
                    'END'
                ));
            }

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            Logger::writeSysException($e->getMessage() . '' . $e->getTraceAsString());
        }

        // return Command::INVALID

        return Command::FAILURE;
    }

    abstract protected function exec(InputInterface $input, OutputInterface $output): void;
}