<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateStorageLink extends Command
{
    protected $signature = 'storage:link-manual';
    protected $description = 'Criar link simbólico do storage manualmente (alternativa quando symlink está desabilitado)';

    public function handle()
    {
        $target = storage_path('app/public');
        $link = public_path('storage');

        // Verificar se o diretório target existe
        if (!File::exists($target)) {
            File::makeDirectory($target, 0755, true);
            $this->info("Diretório criado: {$target}");
        }

        // Se o link já existe, verificar se está correto
        if (File::exists($link)) {
            if (is_link($link) || is_dir($link)) {
                $this->warn("O link já existe: {$link}");
                if ($this->confirm('Deseja remover o link existente e criar um novo?')) {
                    if (is_link($link)) {
                        unlink($link);
                    } elseif (is_dir($link)) {
                        File::deleteDirectory($link);
                    }
                } else {
                    return 0;
                }
            }
        }

        try {
            // Tentar usar symlink() se disponível
            if (function_exists('symlink')) {
                if (symlink($target, $link)) {
                    $this->info("Link simbólico criado com sucesso usando symlink()");
                    $this->info("Link: {$link} -> {$target}");
                    return 0;
                }
            }

            // Alternativa: usar exec para criar link simbólico
            $this->info("Tentando criar link usando comando do sistema...");
            
            if (PHP_OS_FAMILY === 'Windows') {
                // Windows: usar mklink
                $target = str_replace('/', '\\', $target);
                $link = str_replace('/', '\\', $link);
                exec("mklink /D " . escapeshellarg($link) . " " . escapeshellarg($target), $output, $return);
            } else {
                // Linux/Unix: usar ln -s
                exec("ln -s " . escapeshellarg($target) . " " . escapeshellarg($link), $output, $return);
            }

            if ($return === 0) {
                $this->info("Link simbólico criado com sucesso usando comando do sistema");
                $this->info("Link: {$link} -> {$target}");
                return 0;
            } else {
                $this->error("Erro ao criar link simbólico");
                $this->error("Output: " . implode("\n", $output));
                
                // Última alternativa: criar um script PHP que cria o link
                $this->warn("Tentando criar link usando script alternativo...");
                return $this->createLinkViaScript($target, $link);
            }
        } catch (\Exception $e) {
            $this->error("Erro: " . $e->getMessage());
            $this->warn("Tentando criar link usando script alternativo...");
            return $this->createLinkViaScript($target, $link);
        }
    }

    private function createLinkViaScript($target, $link)
    {
        $script = <<<'PHP'
<?php
$target = '{{TARGET}}';
$link = '{{LINK}}';

if (file_exists($link)) {
    if (is_link($link)) {
        unlink($link);
    } elseif (is_dir($link)) {
        rmdir($link);
    }
}

if (function_exists('symlink')) {
    if (symlink($target, $link)) {
        echo "Link criado com sucesso\n";
        exit(0);
    }
}

// Tentar via exec
$command = "ln -s " . escapeshellarg($target) . " " . escapeshellarg($link);
exec($command, $output, $return);

if ($return === 0) {
    echo "Link criado com sucesso\n";
    exit(0);
} else {
    echo "Erro ao criar link\n";
    exit(1);
}
PHP;

        $script = str_replace('{{TARGET}}', $target, $script);
        $script = str_replace('{{LINK}}', $link, $script);

        $scriptPath = storage_path('app/temp_link_creator.php');
        file_put_contents($scriptPath, $script);

        $this->info("Script criado: {$scriptPath}");
        $this->info("Execute manualmente: php {$scriptPath}");
        $this->info("Ou execute no servidor: ln -s {$target} {$link}");

        return 1;
    }
}

