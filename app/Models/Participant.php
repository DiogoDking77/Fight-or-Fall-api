<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    protected $fillable = [
        'name', 'current_position', 'id_edition'
    ];
    
    public function edition()
    {
        return $this->belongsTo(Edition::class, 'id_edition');
    }
}
