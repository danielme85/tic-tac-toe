<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $table = 'players';

    protected $fillable = ['name'];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uid = uniqid();
        });
    }
}
