<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    use HasFactory;

    protected $table = 'themes'; // Nome da tabela
    protected $fillable = ['name']; // Campos que podem ser preenchidos em massa
}
