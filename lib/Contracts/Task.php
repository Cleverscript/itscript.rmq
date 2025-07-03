<?php

namespace Itscript\Rmq\Contracts;

interface Task
{
    public function exec(): bool;
}