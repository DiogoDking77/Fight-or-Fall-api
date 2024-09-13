<?php

namespace App\Http\Controllers;

use App\Models\Edition;
use App\Models\Participant;
use App\Models\Phase;
use App\Models\Matches;
use App\Models\Bracket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Import para transações

class MatchController extends Controller
{
    public function createSingleEliminationTournament(Request $request)
    {
        // Usar transações para garantir que tudo ou nada seja executado
        DB::beginTransaction();

        try {
            // Validação dos dados recebidos
            $validated = $request->validate([
                'id_edition' => 'required|exists:editions,id',
                'id_phase' => 'required|exists:phases,id', // Validação do ID da fase
                'n_participants' => 'required|integer|in:2,4,8,16,32', // Número de participantes permitido
                'participants' => 'required|array|min:2|max:32', // Lista de participantes
                'isRandomDraw' => 'required|boolean',
            ]);

            // Etapa 1: Criar participantes
            $participants = [];
            foreach ($validated['participants'] as $participantName) {
                $participant = Participant::create([
                    'name' => $participantName,
                    'current_position' => 'Round 1',
                    'id_edition' => $validated['id_edition'],
                ]);
                $participants[] = $participant->id; // Armazena o ID dos participantes
            }

            // Etapa 2: Gerar as matches
            $totalParticipants = $validated['n_participants'];
            $rounds = [];
            $matchesPerRound = $totalParticipants / 2;

            for ($round = 1; $matchesPerRound >= 1; $round++) {
                $roundMatches = [];

                for ($i = 0; $i < $matchesPerRound; $i++) {
                    $match = Matches::create([
                        'round' => $round,
                        'phase_id' => $validated['id_phase'], // Adicionando o id_phase
                        'id_edition' => $validated['id_edition'], // Referência à edição
                        'id_participant1' => null,
                        'id_participant2' => null,
                        'score1' => null,
                        'score2' => null,
                        'id_winner' => null,
                        'id_loser' => null,
                        'id_group' => null,
                        'id_bracket' => null,
                    ]);

                    $roundMatches[] = $match->id;
                }

                $matchesPerRound /= 2; // Divide o número de partidas pela metade para a próxima rodada
                $rounds[] = $roundMatches;
            }

            // Etapa 3: Realizar o sorteio e associar participantes às partidas da primeira rodada
            $remainingParticipants = $participants;
            
            if ($validated['isRandomDraw']) {
                shuffle($remainingParticipants); // Embaralha os participantes
            }

            $firstRoundMatches = $rounds[0];
            foreach ($firstRoundMatches as $matchId) {
                $match = Matches::find($matchId);

                // Atribuir dois participantes por partida
                $participant1 = array_shift($remainingParticipants);
                $participant2 = array_shift($remainingParticipants);

                // Atualizar a partida com os participantes
                $match->update([
                    'id_participant1' => $participant1,
                    'id_participant2' => $participant2,
                ]);
            }

            // Quarta Etapa: Criar brackets
            $brackets = [];
            foreach ($rounds as $roundMatches) {
                foreach ($roundMatches as $matchId) {
                    $isFinal = $matchId === end($rounds)[0]; // Se for o último jogo (a final)
                    $bracket = Bracket::create([
                        'is_winner_bracket' => true,
                        'is_final' => $isFinal,
                        'winner_match_id' => null,
                        'loser_match_id' => null,
                        'phase_id' => $validated['id_phase'],
                    ]);
                    
                    // Atualizar a match com o bracket id
                    Matches::where('id', $matchId)->update([
                        'id_bracket' => $bracket->id,
                    ]);

                    $brackets[] = $bracket->id;
                }
            }

            // Quinta Etapa: Atualizar os vencedores que se qualificam para a próxima partida
            for ($roundIndex = 0; $roundIndex < count($rounds) - 1; $roundIndex++) {
                $currentRoundMatches = $rounds[$roundIndex];
                $nextRoundMatches = $rounds[$roundIndex + 1];

                for ($i = 0; $i < count($currentRoundMatches) / 2; $i++) {
                    $winnerMatch1 = $currentRoundMatches[$i * 2];
                    $winnerMatch2 = $currentRoundMatches[$i * 2 + 1];
                    $nextMatch = $nextRoundMatches[$i];

                    // Atualiza o bracket para saber para qual partida o vencedor se qualifica
                    Bracket::where('winner_match_id', null)
                        ->where('id', $nextMatch)
                        ->update([
                            'winner_match_id' => $nextMatch,
                        ]);
                }
            }

            // Se tudo ocorrer bem, confirmar a transação
            DB::commit();

            return response()->json([
                'message' => 'Torneio de Single Elimination criado com sucesso!',
                'rounds' => $rounds, // Lista de partidas por rodada
                'brackets' => $brackets,
            ], 201);

        } catch (\Exception $e) {
            // Em caso de erro, reverter todas as alterações no banco de dados
            DB::rollBack();

            return response()->json([
                'message' => 'Erro ao criar o torneio',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
