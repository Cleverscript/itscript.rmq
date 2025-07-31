<?php

namespace Itscript\Rmq\Helpers;

use Bitrix\Main\Application;
use Bitrix\Main\SystemException;

class Logger
{
    public static function write($data='', $logFileName="pLog.txt"): void
    {
        $path = $_SERVER["DOCUMENT_ROOT"] . "/local/log/";
        \CheckDirPath($path);
        $fp = fopen($path . "/" . $logFileName, "a");
        fwrite($fp, "=================================\r\n" . date('d.m.Y H:i:s') . "\r\n" . print_r($data,true) . "\r\n");
        fclose($fp);
    }

    public static function writeSysException(string $mess): void
    {
        $exception = new SystemException($mess);
        $application = Application::getInstance();
        $exceptionHandler = $application->getExceptionHandler();
        $exceptionHandler->writeToLog($exception);
    }
}