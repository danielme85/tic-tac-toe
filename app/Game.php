<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $table = 'games';

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uid = uniqid();
        });
    }

    public function playerX()
    {
        return $this->hasOne(Player::class, 'player_id_x', 'id');
    }

    public function playerO()
    {
        return $this->hasOne(Player::class, 'player_id_O', 'id');
    }

    public function getBoardAttribute()
    {
        return json_decode($this->board);
    }

    public function setBoardAttribute($board) {
        $this->board = json_encode($board);
    }
}
