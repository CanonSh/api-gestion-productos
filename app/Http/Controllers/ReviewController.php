<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Crear una review para un producto
     */
    public function store(Request $request, $productId)
    {
        try {
            $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'nullable|string'
            ]);

            // Verificar que el producto exista
            $product = Product::findOrFail($productId);

            // Crear la review (tu migración permite varias por usuario)
            $review = Review::create([
                'product_id' => $productId,
                'user_id' => auth()->id, 
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

    /**
     * Obtener todas las reviews de un producto
     */
    public function index($productId)
    {
        try {
            // Verifica que el producto exista
            $product = Product::findOrFail($productId);

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

    /**
     * Actualizar una review (solo el autor)
     */
    public function update(Request $request, $id)
    {
        try {
            $review = Review::findOrFail($id);

            // Revisar que la review sea del usuario autenticado
            if ($review->user_id !== auth()->id) { 
                return response()->json(['error' => 'No autorizado'], 403);
            }

            $request->validate([
                'rating' => 'integer|min:1|max:5',
                'comment' => 'nullable|string'
            ]);

            $review->update($request->only(['rating', 'comment']));

            return response()->json([
                'message' => 'Review actualizada',
                'review' => $review
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Ocurrió un error al actualizar la review.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar una review (solo el autor)
     */
    public function destroy($id)
    {
        try {
            $review = Review::findOrFail($id);

            if ($review->user_id !== auth()->id) { 
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

    /**
     * Obtener promedio de calificación de un producto
     */
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
