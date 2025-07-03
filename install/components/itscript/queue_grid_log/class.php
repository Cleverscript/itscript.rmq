<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Error;
use Bitrix\Main\Result;
use Bitrix\Main\Loader;
use Bitrix\Main\Context;
use Bitrix\Main\Data\Cache;
use Bitrix\Main\Application;
use Bitrix\Main\SystemException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\PageNavigation;
use Itfactory\Report\Enums\GridLogColumns;
use Itfactory\Report\Enums\OperationStatus;
use Itfactory\Report\Enums\OperationTypes;
use Itfactory\Report\Services\LogService;
use Itfactory\Report\Helpers\Config;

Loc::loadMessages(__FILE__);

class LogGrid extends CBitrixComponent
{
    const GRID_ID = 'queue_grid_log';
    const MODULE_ID = 'itscript.rmq';

    protected $cache;
    protected $taggedCache;
    protected int $cacheTime;

    public function onPrepareComponentParams($arParams)
    {
        if (empty($arParams['CACHE_TIME'])) {
            $arParams['CACHE_TIME'] = 86400;
        }

        return $arParams;
    }

	public function executeComponent(): void
	{
        $this->cache = Cache::createInstance();
        $this->taggedCache = Application::getInstance()->getTaggedCache();
        $request = Context::getCurrent()->getRequest();

        $this->cacheTime = $this->arParams['CACHE_TIME'];

        try {
            //if ($this->startResultCache($this->arParams['CACHE_TIME'], [self::GRID_ID, $request['log_list']])) {
                if (!Loader::includeModule(self::MODULE_ID)) {
                    throw new SystemException(
                        Loc::getMessage('ITSCRIPT_RMQ_FAIL_INCLUDE_MODULE', ['#MID#' => self::MODULE_ID])
                    );
                }

                if (isset($request['log_list'])) {
                    $page = explode('page-', $request['log_list']);
                    $page = $page[1];
                } else {
                    $page = 1;
                }

                $totalRowsCount = $this->getTotalCount();

                if (!$totalRowsCount) {
                    throw new SystemException(
                        Loc::getMessage('ITSCRIPT_RMQ_LOG_NOT_FOUNT')
                    );
                }

                // Get grid options
                $gridOptions = new Bitrix\Main\Grid\Options(self::GRID_ID);
                $navParams = $gridOptions->GetNavParams();

                $gridColumns = self::getColumns();

                if (!$gridColumns->isSuccess()) {
                    throw new SystemException(implode(', ', $gridColumns->getErrorMessages()));
                }

                $limit = $this->arParams['NUM_PAGE'] == $navParams['nPageSize'] ? $this->arParams['NUM_PAGE'] : $navParams['nPageSize'];

                // Page navigation
                $nav = new PageNavigation('log_list');
                $nav->allowAllRecords(false)->setPageSize($limit)->initFromUri();
                $nav->setRecordCount($totalRowsCount);

                $gridRows = self::getRows($page, $limit);

                if (!$gridRows->isSuccess()) {
                    throw new SystemException(implode(', ', $gridRows->getErrorMessages()));
                }

                $this->arResult = [
                    'GRID_ID' => self::GRID_ID,
                    'COLUMNS' => $gridColumns->getData(),
                    'ROWS' => $gridRows->getData(),
                    'NAV_OBJECT' => $nav,
                    'TOTAL_ROWS_COUNT' => $totalRowsCount,
                    'SHOW_ROW_CHECKBOXES' => $this->arParams['SHOW_ROW_CHECKBOXES'],
                    'ALLOW_SORT' => true,
                ];

                $this->IncludeComponentTemplate();
            //}
        } catch (\Throwable $e) {
            ShowError($e->getMessage());
        }
	}

    private function getColumns(): Result
    {
        $result = new Result;
        $columns = [];

        foreach (GridLogColumns::list() as $key => $value) {
            $columns[] = [
                'id' => $key,
                'name' => $value,
                'default' => true
            ];
        }

        return $result->setData($columns);
    }


    protected function getTotalCount(): int
    {
        $cacheId = md5(self::GRID_ID . '_total_cnt');
        $cachePath = Config::CMP_GRID_LOG_CACHE_PATH;
        $CacheTag = Config::CMP_GRID_LOG_CACHE_TAG;

        if ($this->cache->initCache($this->cacheTime, $cacheId, $cachePath)) {
            $cnt = $this->cache->getVars();
        } elseif ($this->cache->startDataCache()) {
            $this->taggedCache->startTagCache($cachePath);

            $cnt = LogService::getTotalCount();

            $this->taggedCache->registerTag($CacheTag);

            $this->taggedCache->endTagCache();
            $this->cache->endDataCache($cnt);
        }

        return $cnt;
    }

	protected function getRows(int $page = 1, int $limit = 10): Result
	{
        $result = new Result;
        $data = [];

        $cacheId = md5(self::GRID_ID . $page . $limit);
        $cachePath = Config::CMP_GRID_LOG_CACHE_PATH;
        $CacheTag = Config::CMP_GRID_LOG_CACHE_TAG;

        if ($this->cache->initCache($this->cacheTime, $cacheId, $cachePath)) {
            $rows = $this->cache->getVars();
        } elseif ($this->cache->startDataCache()) {
            $this->taggedCache->startTagCache($cachePath);

            $rows = LogService::getList(GridLogColumns::values(), [], $page, ['ID' => 'DESC'], $limit);

            $this->taggedCache->registerTag($CacheTag);

            if (empty($rows)) {
                $this->taggedCache->abortTagCache();
                $this->cache->abortDataCache();
            }

            $this->taggedCache->endTagCache();
            $this->cache->endDataCache($rows);
        }

        if (empty($rows)) {
            return $result->addError(new Error(Loc::getMessage('ITSCRIPT_RMQ_LOG_NOT_FOUNT')));
        }

        foreach ($rows as $row) {
            foreach ($row as $key => &$value) {

                if ($key == 'STATUS') {
                    switch ($value) {
                        case OperationStatus::SUCCESS->value == $value: $value = OperationStatus::SUCCESS->text();
                            break;
                        case OperationStatus::FAIL->value == $value: $value = OperationStatus::FAIL->text();
                            break;
                    };
                }

                if ($key == 'TYPE') {
                    switch ($value) {
                        case OperationTypes::RUN->value == $value: $value = OperationTypes::RUN->text();
                            break;
                        case OperationTypes::EXPORT->value == $value: $value = OperationTypes::EXPORT->text();
                            break;
                        case OperationTypes::NOTIFY->value == $value: $value = OperationTypes::NOTIFY->text();
                            break;
                    };
                }
            }

            $data[] = [
                'id' => $row['ID'],
                'columns' => $row,
                'actions' => []
            ];
        }

        return $result->setData($data);
	}
}

