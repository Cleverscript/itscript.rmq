<?php

namespace Itscript\Rmq\Helpers;

class Config
{
    const CMP_GRID_LOG_CACHE_TAG = 'queue_grid_log';
    const CMP_GRID_LOG_CACHE_PATH = 'queue_grid_log';

    public static function getModuleName(): string
    {
        return 'itscript.rmq';
    }
}