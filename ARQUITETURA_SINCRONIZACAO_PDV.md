# Arquitetura de Sincronização E-commerce ↔ Loja Física (PDV)

## 🎯 PREMISSAS FUNDAMENTAIS

### 1. **Modularidade Total**
- Cada módulo funciona independentemente
- Sincronização é um módulo opcional
- Sistema funciona 100% sem sincronização

### 2. **Configurabilidade Completa**
- Tudo controlado via Settings (banco de dados)
- Ativação/desativação em tempo real
- Sem necessidade de alterar código

### 3. **Reversibilidade Garantida**
- Desativar = voltar ao estado anterior
- Nenhuma dependência permanente
- Dados preservados

### 4. **Isolamento de Funcionalidades**
- E-commerce funciona sozinho
- PDV funciona sozinho
- Sincronização é camada adicional

---

## 📐 ARQUITETURA PROPOSTA

```
┌─────────────────────────────────────────────────────────┐
│                    CAMADA DE CONFIG                     │
│  Settings: enable_physical_store_sync (boolean)         │
│  Settings: physical_store_name (string)                 │
│  Settings: sync_inventory (boolean)                     │
│  Settings: sync_sales (boolean)                         │
│  Settings: sync_coupons (boolean)                        │
└─────────────────────────────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────┐
│              CAMADA DE SERVIÇOS (Services)              │
│                                                          │
│  ┌──────────────────┐      ┌──────────────────┐       │
│  │ InventoryService │      │  SalesService    │       │
│  │                  │      │                  │       │
│  │ - getStock()     │      │ - createSale()   │       │
│  │ - updateStock()  │      │ - syncSale()     │       │
│  │ - reserveStock() │      │ - getSales()     │       │
│  └──────────────────┘      └──────────────────┘       │
│                                                          │
│  ┌──────────────────┐      ┌──────────────────┐       │
│  │ CouponService   │      │  SyncService     │       │
│  │                  │      │                  │       │
│  │ - validate()    │      │ - syncInventory()│       │
│  │ - apply()       │      │ - syncSales()    │       │
│  │ - sync()        │      │ - checkStatus()  │       │
│  └──────────────────┘      └──────────────────┘       │
└─────────────────────────────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────┐
│              CAMADA DE CONTROLLERS                      │
│                                                          │
│  ┌──────────────────┐      ┌──────────────────┐       │
│  │ ProductController│      │  PDVController   │       │
│  │                  │      │                  │       │
│  │ - index()        │      │ - index()       │       │
│  │ - show()         │      │ - search()      │       │
│  │ - updateStock()  │◄─────┤ - createSale()  │       │
│  └──────────────────┘      │ - printReceipt()│       │
│                             └──────────────────┘       │
└─────────────────────────────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────┐
│              CAMADA DE DADOS (Models)                   │
│                                                          │
│  ┌──────────────────┐      ┌──────────────────┐       │
│  │ Product          │      │  PhysicalSale    │       │
│  │                  │      │                  │       │
│  │ - stock_quantity │      │ - total          │       │
│  │ - reserved_stock │      │ - payment_method │       │
│  │ - sync_enabled   │      │ - synced_at      │       │
│  └──────────────────┘      └──────────────────┘       │
│                                                          │
│  ┌──────────────────┐      ┌──────────────────┐       │
│  │ InventoryLog     │      │  SyncLog         │       │
│  │                  │      │                  │       │
│  │ - type           │      │ - entity_type    │       │
│  │ - quantity       │      │ - entity_id      │       │
│  │ - source         │      │ - status         │       │
│  └──────────────────┘      └──────────────────┘       │
└─────────────────────────────────────────────────────────┘
```

---

## 🔧 MÓDULOS E DEPENDÊNCIAS

### Módulo 1: Configuração Base
**Status:** ✅ Já existe (Settings)
- Sistema de configurações funcionando
- Helper `setting()` disponível
- Interface admin para gerenciar

### Módulo 2: Inventory Service (Novo)
**Dependências:** Módulo 1
**Funcionalidade:**
- Gerenciar estoque unificado
- Reservas temporárias
- Sincronização opcional

**Check de Ativação:**
```php
if (setting('enable_physical_store_sync', false)) {
    // Lógica de sincronização
} else {
    // Lógica normal (sem sincronização)
}
```

### Módulo 3: PDV Interface (Novo)
**Dependências:** Módulo 1, Módulo 2
**Funcionalidade:**
- Interface de caixa
- Venda rápida
- Impressão

**Check de Ativação:**
```php
if (setting('enable_physical_store_sync', false)) {
    // Mostrar interface PDV
} else {
    // Ocultar ou desabilitar
}
```

### Módulo 4: Sync Service (Novo)
**Dependências:** Módulo 1, Módulo 2
**Funcionalidade:**
- Sincronização bidirecional
- Logs de sincronização
- Tratamento de erros

**Check de Ativação:**
```php
if (setting('enable_physical_store_sync', false) && setting('sync_inventory', false)) {
    // Executar sincronização
}
```

---

## 📋 SETTINGS NECESSÁRIOS

### Grupo: `physical_store`

| Key | Type | Default | Descrição |
|-----|------|---------|-----------|
| `enable_physical_store_sync` | boolean | `false` | **MASTER SWITCH** - Ativa/desativa tudo |
| `physical_store_name` | string | `''` | Nome da loja física |
| `sync_inventory` | boolean | `false` | Sincronizar estoque |
| `sync_sales` | boolean | `false` | Sincronizar vendas |
| `sync_coupons` | boolean | `false` | Sincronizar cupons |
| `inventory_reservation_time` | number | `15` | Tempo de reserva (minutos) |
| `auto_sync_interval` | number | `5` | Intervalo de sync automático (minutos) |

---

## 🔄 FLUXO DE FUNCIONAMENTO

### Cenário 1: Sincronização DESATIVADA (Padrão)
```
E-commerce → Product → stock_quantity (normal)
PDV → Não disponível ou desabilitado
```

### Cenário 2: Sincronização ATIVADA
```
E-commerce → Product → stock_quantity (unificado)
PDV → Product → stock_quantity (unificado)
SyncService → Sincroniza em tempo real
```

### Cenário 3: Desativar Sincronização
```
1. Admin desativa em Settings
2. Sistema para de sincronizar
3. Cada sistema usa seu próprio estoque
4. Dados preservados (não deletados)
```

---

## 🛡️ GARANTIAS DE SEGURANÇA

1. **Validação de Settings**
   - Sempre verificar antes de executar
   - Fallback para comportamento padrão

2. **Isolamento de Dados**
   - Tabelas separadas para PDV
   - Não modifica tabelas existentes diretamente

3. **Rollback Automático**
   - Se sync falhar, não afeta operação normal
   - Logs detalhados para debug

4. **Performance**
   - Sync assíncrono quando possível
   - Cache de configurações
   - Queries otimizadas

---

## 📦 ESTRUTURA DE ARQUIVOS

```
app/
├── Services/
│   ├── InventoryService.php      (Novo)
│   ├── PhysicalStoreService.php   (Novo)
│   ├── SyncService.php           (Novo)
│   └── CouponSyncService.php     (Novo)
│
├── Http/Controllers/
│   ├── Admin/
│   │   └── PhysicalStoreController.php  (Novo)
│   └── PDVController.php                (Novo)
│
├── Models/
│   ├── PhysicalSale.php          (Novo)
│   ├── InventoryReservation.php  (Novo)
│   └── SyncLog.php               (Novo)
│
database/migrations/
├── create_physical_sales_table.php
├── create_inventory_reservations_table.php
├── create_sync_logs_table.php
└── add_sync_fields_to_products_table.php
```

---

## ✅ CHECKLIST DE IMPLEMENTAÇÃO

### Fase 1: Base (Sem Risco)
- [ ] Criar Settings de configuração
- [ ] Criar Services base (com checks)
- [ ] Criar migrations (sem alterar tabelas existentes)
- [ ] Testes unitários

### Fase 2: Inventory Service
- [ ] Implementar InventoryService
- [ ] Integrar com Product model (opcional)
- [ ] Interface admin para configurar
- [ ] Testes de sincronização

### Fase 3: PDV Interface
- [ ] Criar PDVController
- [ ] Interface de caixa
- [ ] Busca de produtos
- [ ] Carrinho de venda

### Fase 4: Sincronização
- [ ] SyncService
- [ ] Jobs assíncronos
- [ ] Logs e monitoramento
- [ ] Tratamento de erros

### Fase 5: Integrações
- [ ] Nota fiscal
- [ ] Impressão
- [ ] Cupons unificados
- [ ] Relatórios

---

## 🚨 REGRAS DE OURO

1. **NUNCA** modificar código existente sem check de setting
2. **SEMPRE** verificar `setting('enable_physical_store_sync')` antes de executar
3. **SEMPRE** ter fallback para comportamento padrão
4. **NUNCA** deletar dados ao desativar
5. **SEMPRE** manter logs de operações críticas

---

## 📝 EXEMPLO DE IMPLEMENTAÇÃO

```php
// InventoryService.php
class InventoryService
{
    public function updateStock($productId, $quantity, $source = 'ecommerce')
    {
        $product = Product::find($productId);
        
        // Comportamento padrão (sem sync)
        if (!setting('enable_physical_store_sync', false)) {
            $product->stock_quantity += $quantity;
            $product->save();
            return;
        }
        
        // Comportamento com sync
        if (setting('sync_inventory', false)) {
            // Lógica de sincronização
            SyncService::syncInventory($product, $quantity, $source);
        } else {
            // Apenas atualizar local
            $product->stock_quantity += $quantity;
            $product->save();
        }
    }
}
```

---

## 🎯 CONCLUSÃO

Esta arquitetura garante:
- ✅ Modularidade total
- ✅ Reversibilidade completa
- ✅ Zero impacto quando desativado
- ✅ Fácil manutenção
- ✅ Escalabilidade

**Próximo passo:** Implementar Fase 1 (Base) com todas as garantias.


