<?php

namespace App\Http\Controllers;

use App\Models\Tourney;
use Illuminate\Http\Request;
use App\Http\Controllers\EditionController;

class TourneysController extends Controller
{
    public function index()
    {
        $tourneys = Tourney::with('creator')->get();

        // Transformar os dados para incluir apenas o nome do criador
        $tourneys = $tourneys->map(function ($tourney) {
            return [
                'id' => $tourney->id,
                'name' => $tourney->name,
                'description' => $tourney->description,
                'theme_name' => $tourney->theme_name,
                'creator_name' => $tourney->creator->name,  // Inclui o nome do criador
            ];
        });

        return response()->json($tourneys);
    }

    // Método para criar um novo tourney
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'user_creator_id' => 'required|exists:users,id',
            'theme_name' => 'required|string|max:255',
        ]);

        $tourney = Tourney::create($validatedData);

        return response()->json($tourney, 201);
    }


    public function show($id)
    {
        try {
            // Buscar o torneio e carregar o relacionamento com o usuário criador
            $tourney = Tourney::with('creator')->findOrFail($id);

            // Criar a resposta inicial com os dados do torneio e o nome do criador
            $response = [
                'id' => $tourney->id,
                'name' => $tourney->name,
                'description' => $tourney->description,
                'theme_name' => $tourney->theme_name,
                'creator_name' => $tourney->creator->name, // Nome do criador
            ];

            // Reutilizar o método do EditionController para buscar as edições associadas ao torneio
            $editionController = new EditionController();
            $editionsResponse = $editionController->getByTourneyId($id);
            $editionsData = $editionsResponse->getData(); // Obter os dados da resposta

            // Verificar se o status das edições foi 200 (encontrado)
            if ($editionsResponse->status() === 200) {
                $response['editions'] = $editionsData->editions; // Adicionar as edições à resposta
            } else {
                $response['editions'] = []; // Caso não haja edições, retorna uma lista vazia
            }

            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Tourney not found', 'message' => $e->getMessage()], 404);
        }
    }

    public function getTourneysByCreator($creatorId)
    {
        // Busca os torneios filtrados pelo ID do criador e carrega o relacionamento com o criador
        $tourneys = Tourney::with('creator')
            ->where('user_creator_id', $creatorId)
            ->get();

        // Transformar os dados para incluir apenas o nome do criador
        $tourneys = $tourneys->map(function ($tourney) {
            return [
                'id' => $tourney->id,
                'name' => $tourney->name,
                'description' => $tourney->description,
                'theme_name' => $tourney->theme_name,
                'creator_name' => $tourney->creator->name, // Inclui o nome do criador
            ];
        });

        return response()->json($tourneys);
    }

}
