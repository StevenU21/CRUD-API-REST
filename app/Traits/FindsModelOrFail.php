<?php

namespace App\Traits;

use App\Exceptions\NotFoundException;
use Illuminate\Database\Eloquent\Model;

trait FindsModelOrFail
{
    public static function findOrFailCustom($id): Model
    {
        $model = static::find($id);

        if (!$model) {
            throw new NotFoundException();
        }

        return $model;
    }
}
