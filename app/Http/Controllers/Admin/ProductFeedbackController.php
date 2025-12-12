<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductFeedback;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductFeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ProductFeedback::with(['product', 'customer', 'admin'])
            ->orderBy('created_at', 'desc');

        // Filtros
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'approved') {
                $query->where('is_approved', true);
            } elseif ($request->status === 'pending') {
                $query->where('is_approved', false);
            }
        }

        $feedbacks = $query->paginate(20);
        $products = Product::active()->orderBy('name')->get();

        return view('admin.feedbacks.index', compact('feedbacks', 'products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::active()->orderBy('name')->get();
        return view('admin.feedbacks.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'text' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ], [
            'product_id.required' => 'Selecione um produto.',
            'product_id.exists' => 'Produto inválido.',
            'text.max' => 'O texto do feedback não pode ter mais de 1000 caracteres.',
            'image.image' => 'O arquivo deve ser uma imagem.',
            'image.mimes' => 'A imagem deve ser jpeg, png, jpg, gif ou webp.',
            'image.max' => 'A imagem não pode ter mais de 5MB.',
        ]);

        // Verificar se pelo menos texto ou imagem foi fornecido
        if (empty($validated['text']) && !$request->hasFile('image')) {
            return back()->withErrors(['error' => 'Você deve fornecer pelo menos um texto ou uma imagem.'])->withInput();
        }

        $data = [
            'product_id' => $validated['product_id'],
            'admin_id' => auth('admin')->id(),
            'text' => $validated['text'] ?? null,
            'is_approved' => true, // Admin sempre aprova automaticamente
        ];

        // Upload da imagem se fornecida
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('feedbacks', 'public');
        }

        ProductFeedback::create($data);

        return redirect()->route('admin.feedbacks.index')
            ->with('success', 'Feedback adicionado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductFeedback $feedback)
    {
        $feedback->load(['product', 'customer', 'admin']);
        return view('admin.feedbacks.show', compact('feedback'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductFeedback $feedback)
    {
        $products = Product::active()->orderBy('name')->get();
        return view('admin.feedbacks.edit', compact('feedback', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductFeedback $feedback)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'text' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'is_approved' => 'boolean',
        ], [
            'product_id.required' => 'Selecione um produto.',
            'product_id.exists' => 'Produto inválido.',
            'text.max' => 'O texto do feedback não pode ter mais de 1000 caracteres.',
            'image.image' => 'O arquivo deve ser uma imagem.',
            'image.mimes' => 'A imagem deve ser jpeg, png, jpg, gif ou webp.',
            'image.max' => 'A imagem não pode ter mais de 5MB.',
        ]);

        // Verificar se pelo menos texto ou imagem existe após atualização
        $newText = $validated['text'] ?? $feedback->text;
        $hasImage = $request->hasFile('image') || $feedback->image;

        if (empty($newText) && !$hasImage) {
            return back()->withErrors(['error' => 'O feedback deve ter pelo menos um texto ou uma imagem.'])->withInput();
        }

        $data = [
            'product_id' => $validated['product_id'],
            'text' => $validated['text'] ?? $feedback->text,
            'is_approved' => $request->boolean('is_approved', $feedback->is_approved),
        ];

        // Upload da nova imagem se fornecida
        if ($request->hasFile('image')) {
            // Deletar imagem antiga se existir
            if ($feedback->image) {
                Storage::disk('public')->delete($feedback->image);
            }
            $data['image'] = $request->file('image')->store('feedbacks', 'public');
        }

        $feedback->update($data);

        return redirect()->route('admin.feedbacks.index')
            ->with('success', 'Feedback atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductFeedback $feedback)
    {
        // Deletar imagem se existir
        if ($feedback->image) {
            Storage::disk('public')->delete($feedback->image);
        }

        $feedback->delete();

        return redirect()->route('admin.feedbacks.index')
            ->with('success', 'Feedback removido com sucesso!');
    }

    /**
     * Toggle approval status
     */
    public function toggleApproval(ProductFeedback $feedback)
    {
        $feedback->update(['is_approved' => !$feedback->is_approved]);

        $status = $feedback->is_approved ? 'aprovado' : 'desaprovado';

        return back()->with('success', "Feedback {$status} com sucesso!");
    }
}
