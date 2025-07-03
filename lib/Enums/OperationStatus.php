<?php

namespace Itscript\Rmq\Enums;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

enum OperationStatus: string
{
    case SUCCESS = 'SUCCESS';
    case FAIL = 'FAIL';

    public static function match($case)
    {
        return match($case) {
            self::SUCCESS => Loc::getMessage('ITSCRIPT_RMQ_OPERATION_SUCCESS'),
            self::FAIL => Loc::getMessage('ITSCRIPT_RMQ_OPERATION_FAIL'),
        };
    }

    public function text()
    {
        return self::match($this);
    }
}
