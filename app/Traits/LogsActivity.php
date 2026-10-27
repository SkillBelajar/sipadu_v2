<?php

namespace App\Traits;

use App\Jobs\LogUserActivityJob;
use Illuminate\Database\Eloquent\Model;

trait LogsActivity
{
    protected static function bootLogsActivity(): void
    {
        static::created(fn (Model $model) => self::log($model, 'created'));
        static::updated(fn (Model $model) => self::log($model, 'updated'));
        static::deleted(fn (Model $model) => self::log($model, 'deleted'));
    }

    protected static function log(Model $model, string $action): void
    {
        $user = auth()->user();
        if (! $user) {
            return;
        }

        LogUserActivityJob::dispatch(
            userId: $user->id,
            action: $action.'_'.class_basename($model),
            description: "{$action} ".class_basename($model)." #{$model->getKey()}",
        );
    }
}
