<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Theme;

class ThemeController extends Controller
{
    // Método para listar todos os temas
    public function index()
    {
        try {
            $themes = Theme::all();
            return response()->json($themes, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch themes', 'message' => $e->getMessage()], 500);
        }
    }

    // Método para criar um novo tema
    public function store(Request $request)
    {
        // Validação dos dados
        $request->validate([
            'name' => 'required|string|max:100|unique:themes,name',
        ]);

        try {
            // Cria um novo tema
            $theme = Theme::create($request->only('name'));

            // Retorna uma resposta JSON com o tema criado
            return response()->json($theme, 201);
        } catch (\Exception $e) {
            // Retorna uma resposta JSON com o erro
            return response()->json(['error' => 'Failed to create theme', 'message' => $e->getMessage()], 500);
        }
    }

    // Método para deletar um tema por ID
    public function destroy($id)
    {
        try {
            $theme = Theme::findOrFail($id);
            $theme->delete();
            return response()->json(['message' => 'Theme deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete theme', 'message' => $e->getMessage()], 500);
        }
    }
}
