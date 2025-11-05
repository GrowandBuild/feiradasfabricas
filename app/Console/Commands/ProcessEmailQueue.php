<?php

namespace App\Console\Commands;

use App\Services\EmailService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessEmailQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processar fila de emails automáticos';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $this->info('Iniciando processamento da fila de emails...');

            $emailService = new EmailService();
            $emailService->processarFilaEmails();

            $this->info('Fila de emails processada com sucesso!');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Erro ao processar fila de emails: ' . $e->getMessage());
            Log::error('Erro no comando ProcessEmailQueue: ' . $e->getMessage());
            
            return Command::FAILURE;
        }
    }
}
