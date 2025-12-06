<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ImportExampleProducts extends Command
{
    protected $signature = 'import:examples';
    protected $description = 'Import example products (seeders)';

    public function handle()
    {
        $this->info('Seeding example products...');
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\ExamplesProductSeeder']);
        $this->info(Artisan::output());
        $this->info('Finished.');
        return 0;
    }
}
