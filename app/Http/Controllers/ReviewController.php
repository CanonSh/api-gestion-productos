<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;


class ReviewController extends Controller
{
    public function store(Request $request, $productId)
    {
        try {
            $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'nullable|string'
            ]);

            Product::findOrFail($productId);

            // AHORA SÍ: auth()->id() (con paréntesis)
            $userId = auth()->id();

            $review = Review::create([
                'product_id' => $productId,
                'user_id' => $userId,
                'rating' => $request->rating,
                'comment' => $request->comment
            ]);

            return response()->json([
                'message' => 'Review creada con éxito',
                'review' => $review
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Ocurrió un error al crear la review.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function index($productId)
    {
        try {
            Product::findOrFail($productId);

            $reviews = Review::where('product_id', $productId)
                             ->with('user')
                             ->get();

            return response()->json($reviews);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Ocurrió un error al obtener las reviews.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
{
    try {
        $review = Review::find($id);

        if (!$review) {
            return response()->json([
                'error' => 'Review no encontrada'
            ], 404);
        }

        // ID del usuario autenticado
        $userId = auth()->id();

        if (!$userId) {
            return response()->json([
                'error' => 'Token inválido o expirado'
            ], 401);
        }

        if ($review->user_id !== $userId) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $request->validate([
            'rating' => 'integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);

        $review->update([
            'rating' => $request->rating ?? $review->rating,
            'comment' => $request->comment ?? $review->comment
        ]);

        return response()->json([
            'message' => 'Review actualizada',
            'review' => $review
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Error al actualizar.',
            'message' => $e->getMessage()
        ], 500);
    }
}


    public function destroy($id)
    {
        try {
            $review = Review::findOrFail($id);

            if ($review->user_id !== auth()->id()) {
                return response()->json(['error' => 'No autorizado'], 403);
            }

            $review->delete();

            return response()->json([
                'message' => 'Review eliminada'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Ocurrió un error al eliminar la review.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function average($productId)
    {
        try {
            Product::findOrFail($productId);

            $avg = Review::where('product_id', $productId)->avg('rating');

            return response()->json([
                'product_id' => $productId,
                'average_rating' => round($avg, 2)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Ocurrió un error al calcular el promedio de calificación.',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
