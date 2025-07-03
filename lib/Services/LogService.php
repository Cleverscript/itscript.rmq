<?php

namespace Itscript\Rmq\Services;

use Itscript\Rmq\Tables\LogsTable;
use Itscript\Rmq\Enums\OperationTypes;
use Itscript\Rmq\Enums\OperationStatus;
use Bitrix\Main\ORM\Fields\ExpressionField;
use Bitrix\Main\SystemException;

class LogService
{
    public static function add(OperationStatus $status, OperationTypes $type, string $msg)
    {
        $result = LogsTable::add([
            'STATUS' => $status->value,
            'TYPE' => $type->value,
            'DESCRIPTION' => $msg
        ]);

        if (!$result->isSuccess()) {
            throw new SystemException(implode(', ', $result->getErrorMessages()));
        }

        return $result->getId();
    }

    public static function getList(array $columns = ['ID'], array $filter = [], int $page = 1, array $order = ['ID' => 'DESC'], int $limit = 10)
    {
        $offset = $limit * ($page-1);

        $query = LogsTable::query()
            ->setSelect($columns)
            ->addOrder('ID', 'DESC');

        if (!empty($order)) {
            foreach ($order as $key => $value) {
                $query->addOrder($key, $value);
            }
        }

        if (!empty($filter)) {
            foreach ($filter as $field => $value) {
                $query->where($field, $value);
            }
        }

        $query->setLimit($limit);
        $query->setOffset($offset);

        return $query->fetchAll();
    }

    public static function getTotalCount(): int
    {
        return LogsTable::query()
            ->setSelect([new ExpressionField('CNT', 'COUNT(1)')])
            ->exec()
            ->fetch()['CNT'];
    }

    public static function addSystemLog(string $msg)
    {
        \CEventLog::Add(array(
            "SEVERITY" => "ERROR",
            "AUDIT_TYPE_ID" => "ITSCRIPT_RMQ",
            "MODULE_ID" => ITSCRIPT_RMQ_MODULE_ID,
            "ITEM_ID" => null,
            "DESCRIPTION" => $msg,
        ));
    }
}
