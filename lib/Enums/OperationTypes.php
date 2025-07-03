<?php

namespace Itscript\Rmq\Enums;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

enum OperationTypes: string
{

    case RUN = 'RUN';
    case EXPORT = 'EXPORT';
    case NOTIFY = 'NOTIFY';

    public static function match($case)
    {
        return match($case) {
            self::RUN => Loc::getMessage('ITSCRIPT_RMQ_OPERATION_RUN'),
            self::EXPORT => Loc::getMessage('ITSCRIPT_RMQ_OPERATION_EXPORT'),
            self::NOTIFY => Loc::getMessage('ITSCRIPT_RMQ_OPERATION_NOTIFY'),
        };
    }

    public function text()
    {
        return self::match($this);
    }
}