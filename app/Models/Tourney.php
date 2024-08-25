<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tourney extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'user_creator_id',
        'theme_name',
    ];

    // Define a relação com o modelo User
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_creator_id');
    }
}

