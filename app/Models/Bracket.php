<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bracket extends Model
{
    use HasFactory;

    protected $table = 'bracket'; // Nome da tabela no banco de dados (caso não seja o padrão)

    protected $fillable = [
        'is_winner_bracket',  // Define se o bracket é para os vencedores
        'is_final',           // Define se este bracket é a final
        'winner_match_id',    // ID da partida para onde o vencedor se qualifica
        'loser_match_id',     // ID da partida para onde o perdedor se qualifica (sempre null para eliminação simples)
        'phase_id',           // Relaciona o bracket à fase do torneio
    ];

    // Relacionamento com as partidas (matches)
    public function winnerMatch()
    {
        return $this->belongsTo(Matches::class, 'winner_match_id');
    }

    public function loserMatch()
    {
        return $this->belongsTo(Matches::class, 'loser_match_id');
    }

    // Relacionamento com a fase
    public function phase()
    {
        return $this->belongsTo(Phase::class, 'phase_id');
    }
}
