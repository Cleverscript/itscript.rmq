<?php
use Bitrix\Main\Localization\Loc;

IncludeModuleLangFile(__FILE__);

const MODULE_ID = 'itscript.rmq';

$vendorId = current(explode('.', MODULE_ID));

global $APPLICATION, $adminMenu;

if ($APPLICATION->GetGroupRight(MODULE_ID)!="D") {
    $arMenu = [
        "parent_menu" => "global_menu_services",
        "section" => MODULE_ID,
        "sort" => 1,
        "text" => Loc::getMessage('ITSCRIPT_RMQ_MENU_ROOT_NAME'),
        "title" => Loc::getMessage('ITSCRIPT_RMQ_MENU_ROOT_NAME'),
        "icon" => "	landing_menu_icon",
        "page_icon" => "landing_menu_icon",
        "module_id" => MODULE_ID,
        "items_id" => "menu_{$vendorId}",
        'items' => [
            [
                'text' => Loc::getMessage('ITSCRIPT_RMQ_MENU_SETTINGS'),
                'icon' => 'sys_menu_icon',
                'page_icon' => 'sys_menu_icon',
                'url' => '/bitrix/admin/itscript_rmq_settings.php',
                'more_url' => [],
                'items_id' => 'main'
            ],

            [
                'text' => Loc::getMessage('ITSCRIPT_RMQ_MENU_LOG'),
                'icon' => 'default_menu_icon',
                'page_icon' => 'constructor-menu-icon-blocks-templates',
                'url' => '/bitrix/admin/itscript_rmq_log.php',
                'more_url' => [],
                'items_id' => 'main'
            ],
        ]
    ];

    return $arMenu;
}

return false;