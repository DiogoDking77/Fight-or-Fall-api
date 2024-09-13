<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Matches extends Model
{
    protected $fillable = [
        'round', 'score1', 'score2', 'id_participant1', 'id_participant2', 'id_winner', 'id_loser', 'id_group', 'id_bracket'
    ];

    public function participant1()
    {
        return $this->belongsTo(Participant::class, 'id_participant1');
    }

    public function participant2()
    {
        return $this->belongsTo(Participant::class, 'id_participant2');
    }
}
