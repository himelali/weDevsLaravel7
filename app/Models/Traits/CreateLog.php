<?php

namespace App\Models\Traits;

use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;

trait CreateLog
{
    use CausesActivity, LogsActivity;

    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected static $logAttributes = ['*'];
    protected static $recordEvents = ['created','deleted','updated'];
    protected static $logAttributesToIgnore = ['updated_at','created_at'];
}
