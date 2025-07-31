<?php

namespace Itscript\Rmq\Services;

use Bitrix\Main\Config\Option;
use Itscript\Rmq\Traits\ModuleTrait;
use Bitrix\Main\Application;

class ModuleService
{
    use ModuleTrait;

    protected array $propVals = [];

    public function __construct() {
        $this->setPropVals();
    }

    public function getPropVals(): array
    {
        return $this->propVals;
    }

    public function getPropVal(string $key): mixed
    {
        return$this->propVals[$key] ?? null;
    }

    private function setPropVals(): void
    {
        $conn = Application::getConnection();

        $rows = $conn->query("SELECT `NAME` FROM `b_option` WHERE `MODULE_ID` = '" . self::$moduleId . "'")->fetchAll();

        foreach ($rows as $row) {
            $this->propVals[$row['NAME']] = Option::get(self::$moduleId, $row['NAME']);
        }
    }
}
