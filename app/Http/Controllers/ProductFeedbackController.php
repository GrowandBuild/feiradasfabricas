<?php

namespace App\Http\Controllers;

use App\Models\ProductFeedback;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductFeedbackController extends Controller
{
    /**
     * Store a newly created feedback from customer
     */
    public function store(Request $request, Product $product)
    {
        // Verificar se o cliente está autenticado
        if (!auth('customer')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Você precisa estar logado para enviar um feedback.'
            ], 401);
        }

        $validated = $request->validate([
            'text' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ], [
            'text.max' => 'O texto do feedback não pode ter mais de 1000 caracteres.',
            'image.image' => 'O arquivo deve ser uma imagem.',
            'image.mimes' => 'A imagem deve ser jpeg, png, jpg, gif ou webp.',
            'image.max' => 'A imagem não pode ter mais de 5MB.',
        ]);

        // Verificar se pelo menos texto ou imagem foi fornecido
        if (empty($validated['text']) && !$request->hasFile('image')) {
            return response()->json([
                'success' => false,
                'message' => 'Você deve fornecer pelo menos um texto ou uma imagem.'
            ], 422);
        }

        $data = [
            'product_id' => $product->id,
            'customer_id' => auth('customer')->id(),
            'text' => $validated['text'] ?? null,
            'is_approved' => false, // Clientes precisam de aprovação
        ];

        // Upload da imagem se fornecida
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('feedbacks', 'public');
        }

        $feedback = ProductFeedback::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Feedback enviado com sucesso! Ele será revisado antes de ser publicado.',
            'feedback' => $feedback->load('customer')
        ]);
    }

    /**
     * Get feedbacks for a product (approved only)
     */
    public function getProductFeedbacks(Product $product)
    {
        $feedbacks = ProductFeedback::where('product_id', $product->id)
            ->approved()
            ->with('customer')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'feedbacks' => $feedbacks
        ]);
    }
}
