<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php";
require_once dirname(__FILE__) . "/../include.php";
require_once dirname(__FILE__) . "/../prolog.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php";

use Bitrix\Main\Localization\Loc;

global $APPLICATION;
$APPLICATION->SetTitle(Loc::getMessage('ITFACTORY_REPORT_ADM_PAGE_LOG_TITLE'));

$APPLICATION->IncludeComponent(
	"itfactory:report_grid_log",
	"",
	Array(
		"NUM_PAGE" => "10"
	)
);


