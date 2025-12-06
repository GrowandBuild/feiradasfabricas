# Importação de Exemplo — Produtos

Passos rápidos para testar o import de exemplos que criei:

1. Rodar migrations (se necessário):

```powershell
php artisan migrate
```

2. Registrar comandos (já adicionado automaticamente em `app/Console/Kernel.php`).

3. Rodar o comando que importa os exemplos:

```powershell
php artisan import:examples
```

Isso executará o seeder `Database\\Seeders\\ExamplesProductSeeder` que cria 3 produtos de exemplo e variações quando aplicável.

Observações:
- Os exemplos são mínimos e servem para testes locais; ajuste campos conforme seu schema real.
- Se sua base já contém produtos com os mesmos SKUs, o seeder usa `updateOrCreate` e atualizará os registros.

Próximo passo sugerido: integrar normalização (ProductNormalizer) em endpoints de criação/atualização de produto.
