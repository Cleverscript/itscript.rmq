<?php
use Bitrix\Main\Loader;
use Bitrix\Main\IO\File;
use Bitrix\Main\Context;
use Bitrix\Main\Error;
use Bitrix\Main\Result;
use Bitrix\Main\IO\Directory;
use Bitrix\Main\Application;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\EventManager;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;
use Itscript\Rmq\Tables\LogsTable;

Loc::loadMessages(__FILE__);

/**
 * Class itscript_rmq
 */

if (class_exists("itscript_rmq")) return;

class itscript_rmq extends CModule
{
    public $MODULE_ID = "itscript.rmq";
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $PARTNER_NAME;
    public $PARTNER_URI;
    public $MODULE_SORT;
    public $SHOW_SUPER_ADMIN_GROUP_RIGHTS;
    public $MODULE_GROUP_RIGHTS;
    protected $eventManager;
    protected string $docRoot;
    protected string $localPath;
    protected string $jsExtPath;

    function __construct()
    {
        $arModuleVersion = array();
        include(__DIR__ . "/version.php");

        $this->exclusionAdminFiles = array(
            '..',
            '.'
        );

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = Loc::getMessage("ITSCRIPT_RMQ_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("ITSCRIPT_RMQ_MODULE_DESC");

        $this->PARTNER_NAME = Loc::getMessage("ITSCRIPT_RMQ_PARTNER_NAME");
        $this->PARTNER_URI = Loc::getMessage("ITSCRIPT_RMQ_PARTNER_URI");

        $this->MODULE_SORT = 1;
        $this->SHOW_SUPER_ADMIN_GROUP_RIGHTS = 'Y';
        $this->MODULE_GROUP_RIGHTS = "Y";

        $this->eventManager = EventManager::getInstance();
        $this->request = Context::getCurrent()->getRequest();
        $this->docRoot = Application::getDocumentRoot();

        $this->localPath = $this->docRoot . '/local';
        $this->jsExtPath = $this->docRoot . '/local/js';
        $this->localPathCmp = $this->localPath . '/components';
    }

    public function isVersionD7()
    {
        return CheckVersion(ModuleManager::getVersion('main'), '20.00.00');
    }

    public function GetPath($notDocumentRoot = false)
    {
        if ($notDocumentRoot) {
            return str_ireplace(Application::getDocumentRoot(), '', dirname(__DIR__));
        } else {
            return dirname(__DIR__);
        }
    }

    public function GetVendor()
    {
        return current(explode('.', $this->MODULE_ID));
    }

    public function InstallFiles()
    {
        \CheckDirPath($this->localPath);

        if (!CopyDirFiles(
            $this->GetPath() . '/install/admin',
            $this->docRoot . '/bitrix/admin/', true)
        ) {

            return false;
        }

        if (!CopyDirFiles(
            $this->GetPath() . '/install/components',
            $this->localPathCmp,
            true,
            true
        )) {
            return false;
        }

        if (!CopyDirFiles(
            $this->GetPath() . '/install/js',
            $this->jsExtPath, true, true)
        ) {

            return false;
        }

        return true;
    }

    public function UnInstallFiles()
    {
        $adminFiles = ['itscript_rmq_log.php', 'itscript_rmq_settings.php'];

        foreach ($adminFiles as $name) {
            File::deleteFile($this->docRoot . '/bitrix/admin/' . $name);
        }

        Directory::deleteDirectory($this->jsExtPath . '/' . str_replace('.', '/', $this->MODULE_ID));

        Directory::deleteDirectory(
            $this->localPath . '/components/' . current(explode('.', $this->MODULE_ID)) . '/report_grid_log'
        );
    }

    public function InstallEvents()
    {
        $eventManager = EventManager::getInstance();

        $eventManager->registerEventHandler(
            "main",
            "OnPageStart",
            $this->MODULE_ID,
            '\Itscript\Rmq\Helpers\ExtensionHelper',
            "registerModuleExtension"
        );

        return true;
    }

    public function UnInstallEvents()
    {
        $eventManager = EventManager::getInstance();

        $eventManager->unRegisterEventHandler(
            "main",
            "OnPageStart",
            $this->MODULE_ID,
            '\Itscript\Rmq\Helpers\ExtensionHelper',
            "registerModuleExtension"
        );

        return true;
    }

    public function InstallAgents()
    {
        $interval = 86400;

        $dateTime = (new DateTime())->add("+{$interval} seconds")->format('d.m.Y H:i:s');

        CAgent::AddAgent(
            "\Itscript\Rmq\Agents\DeleteQueueLog::init();",
            $this->MODULE_ID,
            "N",
            $interval,
            $dateTime,
            "Y",
            $dateTime,
            1
        );

        return true;
    }

    public function UnInstallAgents()
    {
        \CAgent::RemoveModuleAgents($this->MODULE_ID);
        return true;
    }

    protected function getEntities()
    {
        return [
            '\\' . LogsTable::class
        ];
    }

    public function InstallDB()
    {
        Loader::includeModule($this->MODULE_ID);

        $entities = $this->getEntities();

        foreach ($entities as $entity) {
            if (!Application::getConnection($entity::getConnectionName())->isTableExists($entity::getTableName())) {
                Base::getInstance($entity)->createDbTable();
            }
        }

        return true;
    }

    public function UninstallDB()
    {
        $connection = Application::getConnection();

        $entities = $this->getEntities();

        foreach ($entities as $entity) {
            if (Application::getConnection($entity::getConnectionName())->isTableExists($entity::getTableName())) {
                $connection->dropTable($entity::getTableName());
            }
        }

        return true;
    }

    public function checkDependencies()
    {
        $result = new Result;

        $dependencies = [
            \PhpAmqpLib\Connection\AMQPStreamConnection::class,
            \Symfony\Component\Console\Application::class,
        ];

        foreach ($dependencies as $class) {
            if (!class_exists($class)) {
                $result->addError(new Error(
                    $class
                ));
            }
        }

        if (!$result->isSuccess()) {
            throw new \Exception(Loc::getMessage(
                'ITSCRIPT_RMQ_DEPENDENCIES_NOT_EXIST',
                ['#DEPENDENCIE#' => implode(", ", $result->getErrorMessages())]
            ));
        }

        return true;
    }

    public function DoInstall()
    {
        try {
            global $APPLICATION;

            $this->checkDependencies();

            if (!$this->isVersionD7()) {
                throw new SystemException(Loc::getMessage('ITSCRIPT_RMQ_INSTALL_ERROR_VERSION'));
            }

            if (!$this->InstallFiles()) {
                return false;
            }

            if (!$this->InstallEvents()) {
                return false;
            }

            if (!$this->InstallAgents()) {
                return false;
            }

            ModuleManager::registerModule($this->MODULE_ID);

            if (!$this->InstallDB()) {
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            $APPLICATION->ThrowException($e->getMessage());
        }

        return false;
    }

    public function DoUninstall()
    {
        try {
            global $APPLICATION, $step;

            Loader::includeModule($this->MODULE_ID);

            $this->UnInstallFiles();
            $this->UnInstallEvents();
            $this->UnInstallAgents();

            if (!isset($step)) {
                $APPLICATION->IncludeAdminFile(
                    Loc::getMessage('ITSCRIPT_RMQ_UNINSTALL_TITLE'),
                    __DIR__ . '/unstep.php'
                );
            }

            if ($this->request->getPost('delete_tables') === 'Y') {
                $this->UninstallDB();
            }

            ModuleManager::unRegisterModule($this->MODULE_ID);

            return true;
        } catch (\Throwable $e) {
            $APPLICATION->ThrowException($e->getMessage());
        }

        return false;
    }

    public function GetModuleRightList()
    {
        return [
            "reference_id" => ["D", "K", "S", "W"],
            "reference" => [
                "[D] " . Loc::getMessage("ITSCRIPT_RMQ_RIGHT_DENIED"),
                "[K] " . Loc::getMessage("ITSCRIPT_RMQ_RIGHT_READ"),
                "[S] " . Loc::getMessage("ITSCRIPT_RMQ_RIGHT_WRITE_SETTINGS"),
                "[W] " . Loc::getMessage("ITSCRIPT_RMQ_RIGHT_FULL")
            ]
        ];
    }
}
