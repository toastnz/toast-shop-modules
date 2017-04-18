<?php

/**
 * Class PromoCsvBulkLoader
 */
class PromoCsvBulkLoader extends CsvBulkLoader
{
    public $columnMap = [
        'Code' => 'Code'
    ];

    public $duplicateChecks = [
        'Code' => 'Code'
    ];
}
