<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Phase;
use App\Models\Edition;

class PhaseController extends Controller
{
    public function store(Request $request, $edition_id)
    {
        // Validação do tipo e verificação da existência da edição
        $validated = $request->validate([
            'type' => 'required|string|in:Single Elimination,Double Elimination,Round Robin,Groups and Knockout,Groups and Double Knockout',
        ]);

        $edition = Edition::findOrFail($edition_id);

        // Lógica para criar fases de acordo com o tipo
        $type = $validated['type'];

        if ($type === 'Single Elimination' || $type === 'Double Elimination' || $type === 'Round Robin') {
            // Criar uma única fase com phase_order = 1
            $phase = Phase::create([
                'type' => $type,
                'phase_order' => 1,
                'id_edition' => $edition->id,
            ]);

            return response()->json([
                'message' => 'Fase criada com sucesso!',
                'phase' => $phase,
            ], 201);
        } elseif ($type === 'Groups and Knockout') {
            // Criar duas fases: Round Robin com phase_order = 1 e Single Elimination com phase_order = 2
            $phase1 = Phase::create([
                'type' => 'Round Robin',
                'phase_order' => 1,
                'id_edition' => $edition->id,
            ]);

            $phase2 = Phase::create([
                'type' => 'Single Elimination',
                'phase_order' => 2,
                'id_edition' => $edition->id,
            ]);

            return response()->json([
                'message' => 'Fases criadas com sucesso!',
                'phases' => [$phase1, $phase2],
            ], 201);
        } elseif ($type === 'Groups and Double Knockout') {
            // Criar duas fases: Round Robin com phase_order = 1 e Double Elimination com phase_order = 2
            $phase1 = Phase::create([
                'type' => 'Round Robin',
                'phase_order' => 1,
                'id_edition' => $edition->id,
            ]);

            $phase2 = Phase::create([
                'type' => 'Double Elimination',
                'phase_order' => 2,
                'id_edition' => $edition->id,
            ]);

            return response()->json([
                'message' => 'Fases criadas com sucesso!',
                'phases' => [$phase1, $phase2],
            ], 201);
        }

        return response()->json(['message' => 'Tipo inválido'], 400);
    }
}
