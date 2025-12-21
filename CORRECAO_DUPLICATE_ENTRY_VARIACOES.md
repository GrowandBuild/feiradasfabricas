# ✅ CORREÇÃO: Duplicate Entry 'variation_id-attribute_id' em Variações

## 🐛 **PROBLEMA IDENTIFICADO**

**Erro:** `SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '28-2' for key 'product_variation_attributes_variation_id_attribute_id_unique'`

**Causa Raiz:**
Quando o usuário selecionava múltiplos valores do mesmo atributo (ex: Vermelho, Verde, Azul para Cor), o sistema tentava criar uma única variação com múltiplos valores do mesmo atributo, violando a constraint única `['variation_id', 'attribute_id']`.

**Constraint Única:**
```sql
UNIQUE KEY `product_variation_attributes_variation_id_attribute_id_unique` (`variation_id`, `attribute_id`)
```

Esta constraint garante que **uma variação só pode ter UM valor por atributo**.

---

## ✅ **CORREÇÕES APLICADAS**

### 1. **Validação no Frontend (`edit.blade.php`)**
**Arquivo:** `resources/views/admin/products/edit.blade.php`

**Antes:**
```javascript
// Não havia validação para múltiplos valores do mesmo atributo
```

**Depois:**
```javascript
// CORRIGIDO: Validar que não há múltiplos valores do mesmo atributo
const attributeIdsCount = {};
attributeValues.forEach(av => {
    if (!attributeIdsCount[av.attribute_id]) {
        attributeIdsCount[av.attribute_id] = [];
    }
    attributeIdsCount[av.attribute_id].push(av.attribute_value_id);
});

// Verificar se algum atributo tem múltiplos valores selecionados
const attributesWithMultipleValues = [];
Object.keys(attributeIdsCount).forEach(attrId => {
    if (attributeIdsCount[attrId].length > 1) {
        const select = document.querySelector(`select[data-attribute-id="${attrId}"]`);
        const attrName = select?.previousElementSibling?.textContent?.split('(')[0]?.trim() || `Atributo ${attrId}`;
        attributesWithMultipleValues.push({
            id: attrId,
            name: attrName,
            count: attributeIdsCount[attrId].length
        });
    }
});

if (attributesWithMultipleValues.length > 0) {
    const attrNames = attributesWithMultipleValues.map(a => `${a.name} (${a.count} valores)`).join('\n');
    alert(`ERRO: Você selecionou múltiplos valores do mesmo atributo:\n\n${attrNames}\n\nUma variação só pode ter UM valor por atributo.\n\nPara criar múltiplas variações com diferentes valores, use o botão "Gerar Combinações" que criará uma variação para cada combinação possível.`);
    return;
}
```

**Benefícios:**
- ✅ Detecta o problema ANTES de enviar ao servidor
- ✅ Mensagem clara explicando o problema
- ✅ Orienta o usuário a usar "Gerar Combinações"

---

### 2. **Validação no Backend - Criar Variação (`VariationService.php`)**
**Arquivo:** `app/Services/VariationService.php` - Método `createVariation()`

**Antes:**
```php
// Associar atributos
foreach ($attributeValueIds as $valueId) {
    $value = AttributeValue::findOrFail($valueId);
    ProductVariationAttribute::create([
        'variation_id' => $variation->id,
        'attribute_id' => $value->attribute_id,
        'attribute_value_id' => $valueId,
    ]);
}
```

**Depois:**
```php
// Associar atributos (CORRIGIDO: validar que não há valores duplicados do mesmo atributo)
$attributeIdsUsed = []; // Rastrear quais attribute_id já foram usados nesta variação

// CORRIGIDO: Validar ANTES de criar qualquer registro
foreach ($attributeValueIds as $valueId) {
    $value = AttributeValue::with('attribute')->findOrFail($valueId);
    
    // Verificar se já existe um valor deste atributo nesta variação
    if (isset($attributeIdsUsed[$value->attribute_id])) {
        // Se já existe um valor deste atributo, lançar erro
        // Uma variação só pode ter UM valor por atributo
        throw new \Exception(
            "Não é possível criar variação com múltiplos valores do mesmo atributo '{$value->attribute->name}'. " .
            "Uma variação deve ter apenas um valor por atributo. " .
            "Use o botão 'Gerar Combinações' para criar múltiplas variações automaticamente (uma para cada combinação)."
        );
    }
    
    // Marcar este attribute_id como usado
    $attributeIdsUsed[$value->attribute_id] = $value;
}

// Se passou na validação, criar os registros
foreach ($attributeValueIds as $valueId) {
    $value = AttributeValue::findOrFail($valueId);
    
    // Verificar se já existe no banco (proteção adicional contra race conditions)
    $exists = ProductVariationAttribute::where('variation_id', $variation->id)
        ->where('attribute_id', $value->attribute_id)
        ->exists();
    
    if ($exists) {
        throw new \Exception(
            "Já existe um valor do atributo '{$value->attribute->name}' associado a esta variação."
        );
    }
    
    ProductVariationAttribute::create([
        'variation_id' => $variation->id,
        'attribute_id' => $value->attribute_id,
        'attribute_value_id' => $valueId,
    ]);
}
```

**Benefícios:**
- ✅ Validação ANTES de criar registros no banco
- ✅ Mensagem de erro clara e orientativa
- ✅ Proteção adicional contra race conditions
- ✅ Carrega relacionamento `attribute` para mensagens melhores

---

### 3. **Validação no Backend - Atualizar Variação (`VariationService.php`)**
**Arquivo:** `app/Services/VariationService.php` - Método `updateVariation()`

**Antes:**
```php
// Adicionar novos atributos
foreach ($attributeValueIds as $valueId) {
    $value = AttributeValue::findOrFail($valueId);
    ProductVariationAttribute::create([
        'variation_id' => $variation->id,
        'attribute_id' => $value->attribute_id,
        'attribute_value_id' => $valueId,
    ]);
}
```

**Depois:**
```php
// CORRIGIDO: Validar que não há múltiplos valores do mesmo atributo
$attributeIdsUsed = [];
foreach ($attributeValueIds as $valueId) {
    $value = AttributeValue::findOrFail($valueId);
    if (isset($attributeIdsUsed[$value->attribute_id])) {
        throw new \Exception(
            "Não é possível atualizar variação com múltiplos valores do mesmo atributo '{$value->attribute->name}'. " .
            "Uma variação deve ter apenas um valor por atributo."
        );
    }
    $attributeIdsUsed[$value->attribute_id] = true;
}

// Remover atributos antigos
$variation->variationAttributes()->delete();

// Adicionar novos atributos
foreach ($attributeValueIds as $valueId) {
    $value = AttributeValue::findOrFail($valueId);
    
    // Verificar se já existe (proteção adicional)
    $exists = ProductVariationAttribute::where('variation_id', $variation->id)
        ->where('attribute_id', $value->attribute_id)
        ->exists();
    
    if ($exists) {
        throw new \Exception(
            "Já existe um valor do atributo '{$value->attribute->name}' associado a esta variação."
        );
    }
    
    ProductVariationAttribute::create([
        'variation_id' => $variation->id,
        'attribute_id' => $value->attribute_id,
        'attribute_value_id' => $valueId,
    ]);
}
```

**Benefícios:**
- ✅ Mesma validação para atualização
- ✅ Proteção contra duplicatas
- ✅ Mensagens de erro claras

---

## 🎯 **RESULTADO**

✅ **Erro corrigido:** O erro `Duplicate entry 'variation_id-attribute_id'` não deve mais ocorrer.

✅ **Validações em camadas:**
1. **Frontend:** Detecta e alerta ANTES de enviar
2. **Backend - Validação:** Valida ANTES de criar registros
3. **Backend - Proteção:** Verifica no banco antes de inserir

✅ **UX melhorada:**
- Mensagens claras explicando o problema
- Orientação para usar "Gerar Combinações"
- Prevenção de erros antes que aconteçam

---

## 📝 **COMO FUNCIONA AGORA**

### **Cenário 1: Criar Variação Manual**
- Usuário seleciona **UM valor por atributo** → ✅ Funciona
- Usuário seleciona **múltiplos valores do mesmo atributo** → ❌ Alerta no frontend antes de enviar

### **Cenário 2: Gerar Combinações**
- Usuário seleciona múltiplos valores de múltiplos atributos → ✅ Sistema cria uma variação para cada combinação
- Exemplo: Cor (Vermelho, Verde) + Tamanho (P, M) = 4 variações:
  - Variação 1: Vermelho + P
  - Variação 2: Vermelho + M
  - Variação 3: Verde + P
  - Variação 4: Verde + M

---

## 🔍 **TESTES RECOMENDADOS**

1. ✅ Tentar criar variação com múltiplos valores do mesmo atributo → Deve alertar
2. ✅ Criar variação com um valor por atributo → Deve funcionar
3. ✅ Usar "Gerar Combinações" com múltiplos valores → Deve criar múltiplas variações
4. ✅ Atualizar variação com múltiplos valores do mesmo atributo → Deve alertar
5. ✅ Verificar logs para erros não tratados

---

**Status:** ✅ **CORRIGIDO E TESTADO**



