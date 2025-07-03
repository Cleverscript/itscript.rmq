<?php

namespace Itscript\Rmq\Helpers;

use Bitrix\Main\Application;
use Itscript\Rmq\Traits\ModuleTrait;

class ExtensionHelper
{
    use ModuleTrait;

    public static function registerModuleExtension()
    {
        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();

        if (!$request->isAdminSection()) {
            return;
        }

        $langId = $context->getLanguage();

        $extPath = '/local/js/' . self::$vendorId . '/' . self::$moduleName;

        \CJSCore::RegisterExt(self::$moduleId, [
            'js' => $extPath . '/src/' . self::$moduleName . '.js',
            'css' => $extPath . '/src/' . self::$moduleName . '.css',
            'lang' => $extPath . '/lang/' . $langId . '/message.php',
            'rel' => ['popup']
        ]);

        \CJSCore::init(self::$moduleId);
    }
}