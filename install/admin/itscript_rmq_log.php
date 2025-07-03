<?php
$basePath = $_SERVER["DOCUMENT_ROOT"];
$filePath = "modules/itscript.rmq/admin/itscript_rmq_log.php";
if(file_exists($basePath . "/bitrix/" . $filePath)) {
    require($basePath . "/bitrix/" . $filePath);
} elseif($basePath . "/local/" . $filePath) {
    require($basePath . "/local/" . $filePath);
}