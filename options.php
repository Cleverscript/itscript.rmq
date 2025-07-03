<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpApplication;
use Itfactory\Report\Services\Form;
use Itfactory\Report\Services\EventType;

$moduleId = "itscript.rmq";

IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/options.php');
IncludeModuleLangFile(__FILE__);

Loader::includeModule($moduleId);

global $APPLICATION;

$request = HttpApplication::getInstance()->getContext()->getRequest();

$defaultOptions = Option::getDefaults($moduleId);

$arMainPropsTab = [
    "DIV" => "edit1",
    "TAB" => Loc::getMessage("ITSCRIPT_RMQ_MAIN_TAB_SETTINGS"),
    "TITLE" => Loc::getMessage("ITSCRIPT_RMQ_MAIN_TAB_SETTINGS_TITLE"),
    "OPTIONS" => [
        [
            "ITSCRIPT_RMQ_HOST",
            Loc::getMessage("ITSCRIPT_RMQ_HOST"),
            $defaultOptions["ITSCRIPT_RMQ_HOST"],
            [
                "text",
                100
            ]
        ],
        [
            "ITSCRIPT_RMQ_PORT",
            Loc::getMessage("ITSCRIPT_RMQ_PORT"),
            $defaultOptions["ITSCRIPT_RMQ_PORT"],
            [
                "text",
                100
            ]
        ],
        [
            "ITSCRIPT_RMQ_USER",
            Loc::getMessage("ITSCRIPT_RMQ_USER"),
            $defaultOptions["ITSCRIPT_RMQ_USER"],
            [
                "text",
                100
            ]
        ],
        [
            "ITSCRIPT_RMQ_PASS",
            Loc::getMessage("ITSCRIPT_RMQ_PASS"),
            $defaultOptions["ITSCRIPT_RMQ_PASS"],
            [
                "text",
                100
            ]
        ],
        [
            "ITSCRIPT_RMQ_VHOST",
            Loc::getMessage("ITSCRIPT_RMQ_VHOST"),
            $defaultOptions["ITSCRIPT_RMQ_VHOST"],
            [
                "text",
                100
            ]
        ],
    ]
];

$aTabs = [
    $arMainPropsTab,
    [
        "DIV" => "edit2",
        "TAB" => Loc::getMessage("MAIN_TAB_RIGHTS"),
        "TITLE" => Loc::getMessage("MAIN_TAB_TITLE_RIGHTS")
    ],
];
?>

<?php
//Save form
if ($request->isPost() && $request["save"] && check_bitrix_sessid()) {
    foreach ($aTabs as $aTab) {
        if (!empty($aTab['OPTIONS'])) {
            __AdmSettingsSaveOptions($moduleId, $aTab["OPTIONS"]);
        }
    }
}
?>

<!-- FORM TAB -->
<?php
$tabControl = new CAdminTabControl("tabControl", $aTabs);
?>
<?php $tabControl->Begin(); ?>
<form method="post" action="<?=$APPLICATION->GetCurPage();?>?mid=<?=htmlspecialcharsbx($request["mid"]);?>&amp;lang=<?=LANGUAGE_ID?>" name="<?=$moduleId;?>">
    <?php $tabControl->BeginNextTab(); ?>

    <?php
    foreach ($aTabs as $aTab) {
        if(is_array($aTab['OPTIONS'])) {
            __AdmSettingsDrawList($moduleId, $aTab['OPTIONS']);
            $tabControl->BeginNextTab();
        }
    }
    ?>

    <?php //require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php"); ?>

    <?php $tabControl->Buttons(array('btnApply' => false, 'btnCancel' => false, 'btnSaveAndAdd' => false)); ?>

    <?=bitrix_sessid_post();?>
</form>
<?php $tabControl->End(); ?>
<!-- X FORM TAB -->