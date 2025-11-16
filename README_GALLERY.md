# Galeria (Feira das Fábricas)

Este módulo adiciona uma seção simples e elegante de galeria com gerenciamento no painel admin.

## Recursos
- Listagem pública de galerias publicadas em `/galeria`
- Página de detalhes com lightbox: `/galeria/{slug}`
- Painel Admin: CRUD de galerias, upload múltiplo, publicar/despublicar, reordenar e remover imagens

## Migrações
Execute as migrações para criar as tabelas `galleries` e `gallery_images`.

```powershell
# Windows PowerShell
php artisan migrate
```

```bash
# Linux/macOS
php artisan migrate
```

## Armazenamento de imagens
As imagens são salvas no disco `public` (pasta `storage/app/public`) e servidas via `public/storage`.
Se o link simbólico ainda não existir, crie-o:

```powershell
# Windows PowerShell
php artisan storage:link
```

```bash
# Linux/macOS
php artisan storage:link
```

## Rotas principais
- Público: `GET /galeria`, `GET /galeria/{slug}`
- Admin: `admin/galleries`, `admin/galleries/create`, `admin/galleries/{id}/edit`

## Observações
- Tamanho máximo de cada imagem: 10MB
- Formatos aceitos: jpeg, png, jpg, gif, webp
- Ordenação das imagens é numérica (campo "Ordem")
