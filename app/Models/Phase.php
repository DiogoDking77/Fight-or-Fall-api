<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phase extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'phase_order',
        'id_edition',
    ];

    public function edition()
    {
        return $this->belongsTo(Edition::class, 'id_edition');
    }
}
