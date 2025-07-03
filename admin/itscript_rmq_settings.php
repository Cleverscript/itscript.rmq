<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php";

use Bitrix\Main\Loader;
use Bitrix\Main\Context;
use Bitrix\Main\Localization\Loc;
use Itscript\Rmq\Helpers\Config;


Loader::includeModule(Config::getModuleName());

global $APPLICATION;
$APPLICATION->SetTitle(Loc::getMessage('ITSCRIPT_RMQ_ADM_PAGE_SETTINGS_TITLE'));
$request = Context::getCurrent()->getRequest();

try {
    if ($request->isPost()) {

    }

} catch (\Exception $e) {
    echo '<div class="adm-info-message">';
    echo '<div class="adm-info-message-title">';
    echo '<h3>' . Loc::getMessage("ITSCRIPT_RMQ_ADM_FORM_SETTING_SAVE_ERROR") . '</h3>';
    echo '<p>' . $e->getMessage() . '</p>';
    echo '</div>';
    echo '</div>';
}
?>
