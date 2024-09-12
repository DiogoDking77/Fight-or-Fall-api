<?php

namespace App\Http\Controllers;

use App\Models\Edition;
use App\Models\Phase;
use Illuminate\Http\Request;

class EditionController extends Controller
{
    /**
     * Criar uma nova edição.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validação dos dados recebidos
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'edition_order' => 'required|integer',
            'start_date' => 'nullable|date',
            
            // Validar o tipo, aceitando apenas os valores definidos
            'type' => 'nullable|string|in:Single Elimination,Double Elimination,Round Robin,Groups and Knockout,Groups and Double Knockout',
            
            'n_participants' => 'required|integer',
            'current_phase' => 'required|integer',
            'tourney_id' => 'required|exists:tourneys,id',
        ]);

        // Definir o status automaticamente baseado na start_date
        $currentDate = now();
        $startDate = $validated['start_date'] ? \Carbon\Carbon::parse($validated['start_date']) : null;

        if ($startDate && $startDate->isFuture()) {
            $validated['status'] = 'Pending';
        } else {
            $validated['status'] = 'Ongoing';
        }

        // Criar a edição no banco de dados
        $edition = Edition::create($validated);

        // Chamar o método para criar as fases automaticamente com base no tipo de edição
        $this->createPhases($edition);

        // Retornar a resposta com o objeto criado
        return response()->json([
            'message' => 'Edição e fases criadas com sucesso!',
            'edition' => $edition,
        ], 201);
    }

    /**
     * Cria fases automaticamente baseado no tipo da edição.
     *
     * @param  \App\Models\Edition  $edition
     * @return void
     */
    private function createPhases(Edition $edition)
    {
        $type = $edition->type;

        if ($type === 'Single Elimination' || $type === 'Double Elimination' || $type === 'Round Robin') {
            // Criar uma única fase com phase_order = 1
            Phase::create([
                'type' => $type,
                'phase_order' => 1,
                'id_edition' => $edition->id,
            ]);
        } elseif ($type === 'Groups and Knockout') {
            // Criar duas fases: Round Robin com phase_order = 1 e Single Elimination com phase_order = 2
            Phase::create([
                'type' => 'Round Robin',
                'phase_order' => 1,
                'id_edition' => $edition->id,
            ]);

            Phase::create([
                'type' => 'Single Elimination',
                'phase_order' => 2,
                'id_edition' => $edition->id,
            ]);
        } elseif ($type === 'Groups and Double Knockout') {
            // Criar duas fases: Round Robin com phase_order = 1 e Double Elimination com phase_order = 2
            Phase::create([
                'type' => 'Round Robin',
                'phase_order' => 1,
                'id_edition' => $edition->id,
            ]);

            Phase::create([
                'type' => 'Double Elimination',
                'phase_order' => 2,
                'id_edition' => $edition->id,
            ]);
        }
    }

    /**
     * Deletar uma edição por ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // Procurar a edição pelo ID
        $edition = Edition::find($id);

        if (!$edition) {
            return response()->json([
                'message' => 'Edição não encontrada',
            ], 404);
        }

        // Deletar a edição
        $edition->delete();

        return response()->json([
            'message' => 'Edição deletada com sucesso!',
        ], 200);
    }

    /**
     * Obter edições por ID do torneio.
     *
     * @param  int  $tourney_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByTourneyId($tourney_id)
    {
        // Buscar todas as edições que pertencem ao tourney_id informado e carregar as fases
        $editions = Edition::where('tourney_id', $tourney_id)
            ->with('phases') // Inclui as fases associadas
            ->get();

        // Verificar se existem edições
        if ($editions->isEmpty()) {
            return response()->json([
                'message' => 'Nenhuma edição encontrada para o torneio informado',
            ], 404);
        }

        // Retornar as edições encontradas, incluindo as fases
        return response()->json([
            'message' => 'Edições encontradas com sucesso!',
            'editions' => $editions,
        ], 200);
    }
}
