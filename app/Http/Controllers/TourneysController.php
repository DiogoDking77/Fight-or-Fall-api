<?php

namespace App\Http\Controllers;

use App\Models\Tourney;
use Illuminate\Http\Request;

class TourneysController extends Controller
{
    public function index()
    {
        $tourneys = Tourney::all();
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
