<?php

namespace App\Traits;

use App\Models\ActivityLog;
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

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => $action.'_'.class_basename($model),
            'description' => "{$action} ".class_basename($model)." #{$model->getKey()}",
        ]);
    }
}
