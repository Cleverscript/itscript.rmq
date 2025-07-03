<?php

declare(strict_types=1);

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;
use Bitrix\Main\LoaderException;

const ITSCRIPT_RMQ_MODULE_ID = "itscript.rmq";

const BGS_DEPENDENCE_MODULE = [

];

$defaultOptions = Option::getDefaults(ITSCRIPT_RMQ_MODULE_ID);

Loc::loadMessages(__FILE__);

foreach (BGS_DEPENDENCE_MODULE as $module) {
    if (!Loader::includeModule($module)) {
        throw new LoaderException(Loc::getMessage(
            "ITSCRIPT_RMQ_MODULE_IS_NOT_INSTALLED",
            ['#MODULE_ID#' => $module]
        ));
    }
}
