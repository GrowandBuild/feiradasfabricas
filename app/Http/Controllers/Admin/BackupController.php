<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BackupController extends Controller
{
    public function index()
    {
        $backups = $this->getBackupList();
        $stats = $this->getBackupStats();
        
        return view('admin.backup.index', compact('backups', 'stats'));
    }

    public function createBackup(Request $request)
    {
        $request->validate([
            'type' => 'required|in:full,products,customers,orders',
            'include_images' => 'boolean',
        ]);

        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $filename = "backup_{$request->type}_{$timestamp}";
        
        try {
            switch ($request->type) {
                case 'full':
                    $this->createFullBackup($filename);
                    break;
                case 'products':
                    $this->createProductsBackup($filename);
                    break;
                case 'customers':
                    $this->createCustomersBackup($filename);
                    break;
                case 'orders':
                    $this->createOrdersBackup($filename);
                    break;
            }

            if ($request->include_images) {
                $this->backupImages($filename);
            }

            return redirect()->back()->with('success', 'Backup criado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao criar backup: ' . $e->getMessage());
        }
    }

    public function restoreBackup(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|string',
            'confirm' => 'required|accepted',
        ]);

        try {
            $this->restoreFromBackup($request->backup_file);
            return redirect()->back()->with('success', 'Backup restaurado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao restaurar backup: ' . $e->getMessage());
        }
    }

    public function downloadBackup($filename)
    {
        $filepath = storage_path("backups/{$filename}");
        
        if (!file_exists($filepath)) {
            return redirect()->back()->with('error', 'Arquivo de backup não encontrado!');
        }

        return response()->download($filepath);
    }

    public function deleteBackup($filename)
    {
        $filepath = storage_path("backups/{$filename}");
        
        if (file_exists($filepath)) {
            unlink($filepath);
            return redirect()->back()->with('success', 'Backup removido com sucesso!');
        }

        return redirect()->back()->with('error', 'Arquivo de backup não encontrado!');
    }

    public function exportProducts()
    {
        $products = Product::with('categories')->get();
        
        $data = [
            'export_date' => Carbon::now()->toISOString(),
            'total_products' => $products->count(),
            'products' => $products->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'description' => $product->description,
                    'sku' => $product->sku,
                    'price' => $product->price,
                    'b2b_price' => $product->b2b_price,
                    'cost_price' => $product->cost_price,
                    'stock_quantity' => $product->stock_quantity,
                    'min_stock' => $product->min_stock,
                    'brand' => $product->brand,
                    'model' => $product->model,
                    'specifications' => $product->specifications,
                    'weight' => $product->weight,
                    'is_active' => $product->is_active,
                    'is_featured' => $product->is_featured,
                    'categories' => $product->categories->pluck('name')->toArray(),
                    'created_at' => $product->created_at->toISOString(),
                    'updated_at' => $product->updated_at->toISOString(),
                ];
            })->toArray()
        ];

        $filename = 'produtos_export_' . date('Y-m-d_H-i-s') . '.json';
        $filepath = storage_path("exports/{$filename}");
        
        // Criar diretório se não existir
        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }
        
        file_put_contents($filepath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return response()->download($filepath);
    }

    public function importProducts(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:json',
        ]);

        $content = file_get_contents($request->file('file')->getPathname());
        $data = json_decode($content, true);

        if (!$data || !isset($data['products'])) {
            return redirect()->back()->with('error', 'Arquivo inválido!');
        }

        $imported = 0;
        $errors = [];

        foreach ($data['products'] as $productData) {
            try {
                $product = Product::create([
                    'name' => $productData['name'],
                    'slug' => $productData['slug'],
                    'description' => $productData['description'],
                    'sku' => $productData['sku'],
                    'price' => $productData['price'],
                    'b2b_price' => $productData['b2b_price'],
                    'cost_price' => $productData['cost_price'],
                    'stock_quantity' => $productData['stock_quantity'],
                    'min_stock' => $productData['min_stock'],
                    'brand' => $productData['brand'],
                    'model' => $productData['model'],
                    'specifications' => $productData['specifications'],
                    'weight' => $productData['weight'],
                    'is_active' => $productData['is_active'],
                    'is_featured' => $productData['is_featured'],
                    'manage_stock' => true,
                    'in_stock' => $productData['stock_quantity'] > 0,
                ]);

                // Adicionar categorias
                if (isset($productData['categories'])) {
                    foreach ($productData['categories'] as $categoryName) {
                        $category = Category::where('name', $categoryName)->first();
                        if ($category) {
                            $product->categories()->attach($category->id);
                        }
                    }
                }

                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Produto {$productData['name']}: " . $e->getMessage();
            }
        }

        $response = redirect()->back()->with('success', "{$imported} produtos importados!");
        
        if (!empty($errors)) {
            $response->with('errors', $errors);
        }

        return $response;
    }

    private function createFullBackup($filename)
    {
        $tables = [
            'products', 'categories', 'category_product', 'customers', 
            'orders', 'order_items', 'admins', 'settings', 'coupons',
            'banners', 'inventory_logs', 'activity_logs'
        ];

        $backup = [
            'backup_info' => [
                'created_at' => Carbon::now()->toISOString(),
                'type' => 'full',
                'version' => '1.0'
            ],
            'data' => []
        ];

        foreach ($tables as $table) {
            $backup['data'][$table] = DB::table($table)->get()->toArray();
        }

        $this->saveBackup($filename, $backup);
    }

    private function createProductsBackup($filename)
    {
        $backup = [
            'backup_info' => [
                'created_at' => Carbon::now()->toISOString(),
                'type' => 'products',
                'version' => '1.0'
            ],
            'data' => [
                'products' => DB::table('products')->get()->toArray(),
                'categories' => DB::table('categories')->get()->toArray(),
                'category_product' => DB::table('category_product')->get()->toArray(),
            ]
        ];

        $this->saveBackup($filename, $backup);
    }

    private function createCustomersBackup($filename)
    {
        $backup = [
            'backup_info' => [
                'created_at' => Carbon::now()->toISOString(),
                'type' => 'customers',
                'version' => '1.0'
            ],
            'data' => [
                'customers' => DB::table('customers')->get()->toArray(),
            ]
        ];

        $this->saveBackup($filename, $backup);
    }

    private function createOrdersBackup($filename)
    {
        $backup = [
            'backup_info' => [
                'created_at' => Carbon::now()->toISOString(),
                'type' => 'orders',
                'version' => '1.0'
            ],
            'data' => [
                'orders' => DB::table('orders')->get()->toArray(),
                'order_items' => DB::table('order_items')->get()->toArray(),
            ]
        ];

        $this->saveBackup($filename, $backup);
    }

    private function backupImages($filename)
    {
        $imagesPath = public_path('images/products');
        $backupPath = storage_path("backups/{$filename}_images");
        
        if (is_dir($imagesPath)) {
            $this->copyDirectory($imagesPath, $backupPath);
        }
    }

    private function saveBackup($filename, $data)
    {
        $backupPath = storage_path('backups');
        
        if (!file_exists($backupPath)) {
            mkdir($backupPath, 0755, true);
        }

        $filepath = "{$backupPath}/{$filename}.json";
        file_put_contents($filepath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    private function restoreFromBackup($filename)
    {
        $filepath = storage_path("backups/{$filename}");
        
        if (!file_exists($filepath)) {
            throw new \Exception('Arquivo de backup não encontrado!');
        }

        $content = file_get_contents($filepath);
        $backup = json_decode($content, true);

        if (!$backup || !isset($backup['data'])) {
            throw new \Exception('Arquivo de backup inválido!');
        }

        DB::beginTransaction();
        
        try {
            foreach ($backup['data'] as $table => $records) {
                // Limpar tabela
                DB::table($table)->truncate();
                
                // Inserir dados
                if (!empty($records)) {
                    DB::table($table)->insert($records);
                }
            }
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    private function copyDirectory($src, $dst)
    {
        if (!file_exists($dst)) {
            mkdir($dst, 0755, true);
        }

        $dir = opendir($src);
        while (false !== ($file = readdir($dir))) {
            if ($file != '.' && $file != '..') {
                if (is_dir($src . '/' . $file)) {
                    $this->copyDirectory($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    private function getBackupList()
    {
        $backupPath = storage_path('backups');
        
        if (!file_exists($backupPath)) {
            return [];
        }

        $files = glob($backupPath . '/*.json');
        $backups = [];

        foreach ($files as $file) {
            $filename = basename($file);
            $backups[] = [
                'filename' => $filename,
                'size' => filesize($file),
                'created_at' => Carbon::createFromTimestamp(filemtime($file)),
            ];
        }

        // Ordenar por data de criação (mais recente primeiro)
        usort($backups, function($a, $b) {
            return $b['created_at'] <=> $a['created_at'];
        });

        return $backups;
    }

    private function getBackupStats()
    {
        return [
            'total_products' => Product::count(),
            'total_customers' => Customer::count(),
            'total_orders' => Order::count(),
            'total_categories' => Category::count(),
            'database_size' => $this->getDatabaseSize(),
        ];
    }

    private function getDatabaseSize()
    {
        $size = DB::select("SELECT 
            ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
            FROM information_schema.tables 
            WHERE table_schema = ?", [config('database.connections.mysql.database')]);

        return $size[0]->size_mb ?? 0;
    }
}
