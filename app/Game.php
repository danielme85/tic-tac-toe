<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $table = 'games';

    protected $hidden = ['id'];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uid = uniqid();
        });
    }

    public function playerX()
    {
        return $this->belongsTo(Player::class, 'player_id_x', 'id');
    }

    public function playerO()
    {
        return $this->belongsTo(Player::class, 'player_id_o', 'id');
    }

    public function moves()
    {
        return $this->belongsToMany(Move::class, 'game_moves', 'game_id', 'move_id')->withTimestamps();;
    }

    public function getBoardAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setBoardAttribute($value) {
        $this->attributes['board'] = json_encode($value);
    }
}
