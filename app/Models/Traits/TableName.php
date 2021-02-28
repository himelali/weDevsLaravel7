<?php

namespace App\Models\Traits;

trait TableName
{
    public static function getTableName()
    {
        return with(new static)->getTable();
    }
}
