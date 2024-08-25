<?php

namespace App\Http\Controllers;

use App\Models\Tourney;
use Illuminate\Http\Request;

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
}
