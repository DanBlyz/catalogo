<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::creating(function ($model) {
            if (Auth::check() && empty($model->usuario_creador_id)) {
                $model->usuario_creador_id = Auth::id();
            }
        });

        static::updating(function ($model) {
            if (Auth::check() && empty($model->usuario_modificador_id)) {
                $model->usuario_modificador_id = Auth::id();
            }
        });

        static::deleting(function ($model) {
            if (Auth::check() && method_exists($model, 'isForceDeleting') && !$model->isForceDeleting()) {
                $model->usuario_eliminador_id = Auth::id();
            }
        });

    }
}
