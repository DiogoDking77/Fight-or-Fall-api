<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Edition extends Model
{
    protected $fillable = [
        'name', 'edition_order', 'start_date', 'type', 'n_participants', 'current_phase', 'tourney_id', 'status'
    ];

    // Relacionamento com as fases
    public function phases()
    {
        return $this->hasMany(Phase::class, 'id_edition');
    }
}
