<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Move extends Model
{
    protected $table = 'moves';

    public function player()
    {
        return $this->belongsTo(Player::class, 'player_id', 'id');
    }
}
