<?php

namespace Itscript\Rmq\Tables;

use Bitrix\Main\Application;
use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\ORM\Event;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\Type\DateTime;
use Itscript\Rmq\Helpers\Config;

class LogsTable extends DataManager
{
    public static function getTableName()
    {
        return 'itscript_rmq_log';
    }

    public static function getMap()
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),

            (new StringField('STATUS'))
                ->configureRequired()
                ->configureDefaultValue('SUCCESS'),

            (new StringField('TYPE'))
                ->configureRequired(),

            (new DatetimeField('DATE'))
                ->configureRequired()
                ->configureDefaultValue(new DateTime()),

            (new TextField('DESCRIPTION'))
                ->configureRequired(),

        ];
    }

    public static function OnAfterAdd(Event $event) {
        self::clearCache();
    }

    public static function OnAfterUpdate(Event $event) {
        self::clearCache();
    }

    public static function OnAfterDelete(Event $event) {
        self::clearCache();
    }

    private static function clearCache()
    {
        (Application::getInstance()->getTaggedCache())->clearByTag(Config::CMP_GRID_LOG_CACHE_TAG);
    }
}

