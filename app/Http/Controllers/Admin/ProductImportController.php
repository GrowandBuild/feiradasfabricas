<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class ProductImportController extends Controller
{
    public function showImportForm()
    {
        return view('admin.products.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        
        if ($extension === 'csv') {
            return $this->importCSV($file);
        } else {
            return $this->importExcel($file);
        }
    }

    private function importCSV($file)
    {
        $handle = fopen($file->getPathname(), 'r');
        $header = fgetcsv($handle);
        $imported = 0;
        $errors = [];

        // Mapear colunas
        $columns = array_flip($header);
        
        while (($row = fgetcsv($handle)) !== false) {
            try {
                $data = array_combine($header, $row);
                $this->createProductFromArray($data);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Linha " . ($imported + 1) . ": " . $e->getMessage();
            }
        }
        fclose($handle);

        return redirect()->back()->with([
            'success' => "{$imported} produtos importados com sucesso!",
            'errors' => $errors
        ]);
    }

    private function createProductFromArray($data)
    {
        $validator = Validator::make($data, [
            'nome' => 'required|string|max:255',
            'marca' => 'required|string|max:255',
            'preco' => 'required|numeric|min:0',
            'estoque' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            throw new \Exception(implode(', ', $validator->errors()->all()));
        }

        $product = Product::create([
            'name' => $data['nome'],
            'slug' => Str::slug($data['nome']),
            'description' => $data['descricao'] ?? 'Produto importado automaticamente',
            'sku' => $data['sku'] ?? 'IMP-' . time() . '-' . rand(1000, 9999),
            'price' => (float) $data['preco'],
            'b2b_price' => isset($data['preco_b2b']) ? (float) $data['preco_b2b'] : (float) $data['preco'] * 0.9,
            'cost_price' => isset($data['preco_custo']) ? (float) $data['preco_custo'] : (float) $data['preco'] * 0.7,
            'stock_quantity' => (int) $data['estoque'],
            'min_stock' => (int) ($data['estoque_minimo'] ?? 5),
            'manage_stock' => true,
            'in_stock' => (int) $data['estoque'] > 0,
            'is_active' => true,
            'is_featured' => false,
            'brand' => $data['marca'],
            'model' => $data['modelo'] ?? $data['marca'],
            'specifications' => $this->parseSpecifications($data),
            'weight' => (float) ($data['peso'] ?? 0.1),
            'sort_order' => 1,
        ]);

        // Adicionar à categoria padrão
        $category = Category::where('slug', 'smartphones')->first();
        if ($category) {
            $product->categories()->attach($category->id);
        }
    }

    private function parseSpecifications($data)
    {
        $specs = [];
        
        if (isset($data['especificacoes'])) {
            $specs = json_decode($data['especificacoes'], true) ?? [];
        }

        // Adicionar specs básicas se não existirem
        if (!isset($specs['Processador']) && isset($data['processador'])) {
            $specs['Processador'] = $data['processador'];
        }
        if (!isset($specs['Tela']) && isset($data['tela'])) {
            $specs['Tela'] = $data['tela'];
        }

        return $specs;
    }

    public function downloadTemplate()
    {
        $template = [
            ['nome', 'marca', 'modelo', 'sku', 'preco', 'preco_b2b', 'preco_custo', 'estoque', 'estoque_minimo', 'peso', 'processador', 'tela', 'descricao'],
            ['iPhone 15 Pro', 'Apple', 'iPhone 15 Pro', 'IPH15P-256-BK', '7499.00', '7199.00', '5999.00', '25', '5', '0.174', 'A17 Pro', '6.1" Super Retina XDR', 'iPhone 15 Pro com chip A17 Pro'],
        ];

        $filename = 'template_produtos.csv';
        $handle = fopen('php://temp', 'r+');
        
        foreach ($template as $row) {
            fputcsv($handle, $row);
        }
        
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
