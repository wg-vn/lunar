<?php

namespace Lunar\Core\Models\Concerns;

use Illuminate\Support\Arr;
use Spatie\Activitylog\Models\Concerns\LogsActivity as SpatieLogsActivity;
use Spatie\Activitylog\Support\LogOptions;

trait LogsActivity
{
    use SpatieLogsActivity;

    public static array $logExcept = [];

    /**
     * Get the log options for the activity log.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('lunar')
            ->logAll()
            ->logExcept(array_merge(['updated_at'], static::getActivitylogExcept()))
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public static function addActivitylogExcept(array|string $fields)
    {
        $fields = Arr::wrap($fields);

        static::$logExcept = array_merge(static::$logExcept, $fields);
    }

    public static function getDefaultLogExcept(): array
    {
        return [];
    }

    public static function getActivitylogExcept(): array
    {
        return array_merge(static::getDefaultLogExcept(), static::$logExcept);
    }
}
