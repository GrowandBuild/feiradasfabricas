<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\FiscalService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessFiscalQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fiscal:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processar fila de emissão de notas fiscais';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $this->info('Iniciando processamento da fila de notas fiscais...');

            // Verificar se SEFAZ está habilitado
            if (!setting('sefaz_enabled', false)) {
                $this->info('SEFAZ não está habilitado. Pulando processamento.');
                return Command::SUCCESS;
            }

            $fiscalService = new FiscalService();

            // Buscar pedidos que precisam de nota fiscal
            $orders = Order::where('status', 'confirmed')
                          ->where('payment_status', 'paid')
                          ->whereNull('invoice_number')
                          ->where('created_at', '>', now()->subDays(30))
                          ->get();

            $processed = 0;
            $errors = 0;

            foreach ($orders as $order) {
                try {
                    $this->info("Processando pedido #{$order->order_number}...");
                    
                    $result = $fiscalService->emitirNotaFiscal($order);
                    
                    if ($result['success']) {
                        // Atualizar pedido com informações da nota fiscal
                        $order->update([
                            'invoice_number' => $result['numero_nota'],
                            'invoice_key' => $result['chave_acesso'],
                            'invoice_protocol' => $result['protocolo'],
                            'invoice_status' => $result['status']
                        ]);
                        
                        $processed++;
                        $this->info("✓ Nota fiscal emitida para pedido #{$order->order_number}");
                    } else {
                        $errors++;
                        $this->error("✗ Erro ao emitir nota fiscal para pedido #{$order->order_number}");
                    }
                } catch (\Exception $e) {
                    $errors++;
                    $this->error("✗ Erro ao processar pedido #{$order->order_number}: " . $e->getMessage());
                    Log::error("Erro ao emitir nota fiscal para pedido #{$order->order_number}: " . $e->getMessage());
                }
            }

            $this->info("Processamento concluído:");
            $this->info("- Notas fiscais emitidas: {$processed}");
            $this->info("- Erros: {$errors}");
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Erro ao processar fila de notas fiscais: ' . $e->getMessage());
            Log::error('Erro no comando ProcessFiscalQueue: ' . $e->getMessage());
            
            return Command::FAILURE;
        }
    }
}
