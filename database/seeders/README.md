# Seeders da Feira das Fábricas

Este documento descreve como usar os seeders do sistema de forma segura e eficiente.

## 🚀 Seeders Obrigatórios

### DatabaseSeeder (Principal)
```bash
# Desenvolvimento (com dados de exemplo)
php artisan db:seed

# Apenas dados essenciais
php artisan db:seed --class=ProductionSeeder

# Com dados de exemplo
php artisan db:seed --with-examples

# Com produtos de exemplo
php artisan db:seed --with-examples --with-products
```

### Seeders Individuais

#### 1. SettingsSeeder
Configurações básicas do sistema (APIs, site, segurança).
```bash
php artisan db:seed --class=SettingsSeeder
```

#### 2. DepartmentSeeder
Departamentos básicos (eletrônicos, vestuário masculino, vestuário feminino).
```bash
php artisan db:seed --class=DepartmentSeeder
```

#### 3. CategoriesSeeder
Categorias básicas para cada departamento.
```bash
php artisan db:seed --class=CategoriesSeeder
```

#### 4. AdminSeeder
Usuários administradores padrão.
```bash
php artisan db:seed --class=AdminSeeder
```

## 🔐 Credenciais Padrão

### Super Admin
- **Email:** admin@feiradasfabricas.com
- **Senha:** admin123
- **Permissões:** Todas (*)

### Gerente
- **Email:** gerente@feiradasfabricas.com
- **Senha:** gerente123
- **Permissões:** Produtos, pedidos, clientes

## ⚠️ Segurança

### Produção
- Use apenas `ProductionSeeder` em produção
- **ALTERE AS SENHAS PADRÃO** imediatamente após o primeiro login
- Configure as APIs nas configurações do admin
- Verifique todas as configurações de segurança

### Desenvolvimento
- Use `DatabaseSeeder` completo para desenvolvimento
- Inclua dados de exemplo com `--with-examples`
- Use `--with-products` para incluir produtos de teste

## 📋 Ordem de Execução

Os seeders devem ser executados nesta ordem:

1. **SettingsSeeder** - Configurações básicas
2. **DepartmentSeeder** - Departamentos
3. **CategoriesSeeder** - Categorias (depende dos departamentos)
4. **AdminSeeder** - Usuários admin

## 🔄 Idempotência

Todos os seeders são **idempotentes**, ou seja:
- Podem ser executados múltiplas vezes sem causar duplicação
- Usam `updateOrCreate()` para atualizar ou criar
- Não causam erros se executados novamente

## 🛠️ Troubleshooting

### Erro de Departamento não encontrado
```bash
# Execute o DepartmentSeeder primeiro
php artisan db:seed --class=DepartmentSeeder
```

### Erro de Configuração
```bash
# Execute o SettingsSeeder primeiro
php artisan db:seed --class=SettingsSeeder
```

### Reset completo do banco
```bash
php artisan migrate:fresh --seed
```

## 📊 Dados Criados

### Configurações (SettingsSeeder)
- APIs de pagamento (Stripe, PagSeguro, PayPal, Mercado Pago)
- APIs de entrega (Correios, Total Express, Jadlog, Loggi)
- Configurações do site
- Configurações de estoque
- Configurações de notificação
- Configurações de segurança

### Departamentos (DepartmentSeeder)
- Eletrônicos
- Vestuário Masculino
- Vestuário Feminino

### Categorias (CategoriesSeeder)
- **Eletrônicos:** Smartphones, Tablets, Notebooks, Acessórios
- **Vestuário Masculino:** Camisetas, Calças, Camisas, Calçados
- **Vestuário Feminino:** Blusas, Vestidos, Calças, Calçados

### Usuários (AdminSeeder)
- Super Admin (todas as permissões)
- Gerente (permissões limitadas)

## 🎯 Próximos Passos

Após executar os seeders:

1. Acesse `/admin/login`
2. Faça login com as credenciais padrão
3. Vá em **Configurações** e configure as APIs
4. Altere as senhas padrão
5. Configure os dados da empresa
6. Adicione produtos através do admin
7. Teste as funcionalidades principais
