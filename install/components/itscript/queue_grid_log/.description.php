<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = [
    "NAME" => GetMessage("CMP_ITSCRIPT_RMQ_GRID_LOG_NAME"),
    "DESCRIPTION" => GetMessage("CMP_ITSCRIPT_RMQ_GRID_LOG_DESC"),
    "ICON" => "/images/news_list.gif",
    "SORT" => 1,
    "CACHE_PATH" => "Y",
    "PATH" => [
        "ID" => "Itscript",
        "CHILD" => [
            "ID" => "itscript_rmq_grid_log",
            "NAME" => GetMessage("CMP_ITSCRIPT_RMQ_GRID_LOG_NAME"),
            "SORT" => 10,
            "CHILD" => [
                "ID" => "itscript_rmq_grid_log",
            ],
        ],
    ],
];