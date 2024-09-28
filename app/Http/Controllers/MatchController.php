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
                $roundBrackets = [];
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

                    $roundBrackets[] = $bracket->id; // Adicionar os brackets da rodada
                }
                $brackets[] = $roundBrackets; // Adicionar a lista de brackets da rodada
            }

            // Quinta Etapa: Atualizar os vencedores que se qualificam para a próxima partida
            for ($roundIndex = 0; $roundIndex < count($rounds) - 1; $roundIndex++) {
                $currentRoundMatches = $rounds[$roundIndex];
                $currentRoundBracket = $brackets[$roundIndex];
                $nextRoundMatches = $rounds[$roundIndex + 1];

                for ($i = 0; $i < count($currentRoundMatches) / 2; $i++) {
                    $winnerBracket1 = $currentRoundBracket[$i * 2];
                    $winnerBracket2 = $currentRoundBracket[$i * 2 + 1];
                    $nextMatch = $nextRoundMatches[$i];

                    // Atualiza o bracket para saber para qual partida o vencedor se qualifica
                    Bracket::where('winner_match_id', null)
                        ->where('id', $winnerBracket1)
                        ->update([
                            'winner_match_id' => $nextMatch,
                        ]);

                    Bracket::where('winner_match_id', null)
                        ->where('id', $winnerBracket2)
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

    public function getPhaseMatches($phase_id)
    {
        // Validação para verificar se a fase existe
        $phase = Phase::find($phase_id);

        if (!$phase) {
            return response()->json([
                'message' => 'Phase not found',
            ], 404);
        }

        // Buscar todas as partidas da fase, ordenadas por rodada
        $matches = Matches::where('phase_id', $phase_id)
            ->orderBy('round')
            ->get();

        if ($matches->isEmpty()) {
            return response()->json([
                'message' => 'No matches found for this phase',
            ], 404);
        }

        // Agrupar as partidas por rodada
        $rounds = [];
        foreach ($matches as $match) {
            $roundNumber = 'round' . $match->round;

            // Obter informações dos participantes
            $participant1 = $match->participant1;
            $participant2 = $match->participant2;

            // Obter informações do bracket associado à partida
            $bracket = Bracket::find($match->id_bracket);

            // Criar o formato do jogo
            $gameData = [
                'id_match' => $match->id,
                'round' => $match->round,
                'participant_1' => [
                    'name' => $participant1 ? $participant1->name : null,
                    'current_position' => $participant1 ? $participant1->current_position : null,
                ],
                'participant_2' => [
                    'name' => $participant2 ? $participant2->name : null,
                    'current_position' => $participant2 ? $participant2->current_position : null,
                ],
                'score1' => $match->score1,
                'score2' => $match->score2,
                'id_winner' => $match->id_winner,
                'id_loser' => $match->id_loser,
                'id_group' => $match->id_group,
                'id_bracket' => $bracket ? [
                    'is_winner_bracket' => $bracket->is_winner_bracket,
                    'is_final' => $bracket->is_final,
                    'winner_match_id' => $bracket->winner_match_id,
                    'loser_match_id' => $bracket->loser_match_id,
                ] : null,
            ];

            // Agrupar os jogos pela rodada
            if (!isset($rounds[$roundNumber])) {
                $rounds[$roundNumber] = [];
            }
            $rounds[$roundNumber][] = $gameData;
        }

        // Retornar o formato esperado
        return response()->json([
            'phase_id' => $phase_id,
            'edition_id' => $phase->id_edition,
            'phase_games' => $rounds,
        ], 200);
    }
}
