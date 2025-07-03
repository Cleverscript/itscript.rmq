<?php

namespace Itscript\Rmq\Enums;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

enum GridLogColumns: string
{
    case ID = 'ID';
    case STATUS = 'STATUS';
    case TYPE = 'TYPE';
    case DATE = 'DATE';
    case DESCRIPTION = 'DESCRIPTION';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function list(): array
    {
        return array_combine(
            self::values(),
            array_map(fn($case) => self::match($case), self::cases())
        );
    }

    public static function match($case)
    {
        return match($case) {
            self::ID => Loc::getMessage('ITSCRIPT_RMQ_GRID_LOG_ID'),
            self::STATUS => Loc::getMessage('ITSCRIPT_RMQ_GRID_LOG_STATUS'),
            self::TYPE => Loc::getMessage('ITSCRIPT_RMQ_GRID_LOG_TYPE'),
            self::DATE => Loc::getMessage('ITSCRIPT_RMQ_GRID_LOG_DATE'),
            self::DESCRIPTION => Loc::getMessage('ITSCRIPT_RMQ_GRID_LOG_DESCRIPTION'),
        };
    }

    public function text()
    {
        return self::match($this);
    }
}