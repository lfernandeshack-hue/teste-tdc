# 📝 Update - Múltiplas Perfusões e Fármacos

**Data:** 27/12/2025  
**Status:** ✅ Implementado

---

## ✨ O Que Mudou

### **1. Tabelas Dinâmicas**

As seções de **Perfusões** e **Fármacos** foram transformadas em tabelas interativas que permitem:
- ✅ Adicionar múltiplas entradas
- ✅ Remover entradas individuais
- ✅ Editar existentes diretamente
- ✅ Suporte para arrays de input

### **2. Interface de Perfusões**

| Coluna | Tipo | Descrição |
|--------|------|-----------|
| **Fármaco** | Texto | Nome do medicamento |
| **Posologia** | Texto | Dose e via (Ex: 5mg/kg IV) |
| **Hora** | Time | Hora de início |
| **Taxa 1-4** | Número | Até 4 taxas de infusão paralelas (mL/h) |
| **Ação** | Botão | 🗑️ Remover linha |

**Botão:** ➕ Adicionar Perfusão (adiciona nova linha em branco)

### **3. Interface de Fármacos**

| Coluna | Tipo | Descrição |
|--------|------|-----------|
| **Fármaco** | Texto | Nome do medicamento |
| **Hora** | Time | Hora de administração |
| **Ação** | Botão | 🗑️ Remover linha |

**Botão:** ➕ Adicionar Fármaco (adiciona nova linha em branco)

---

## 🔧 Como Funciona

### **Adicionar Nova Perfusão:**
1. Clique no botão **"➕ Adicionar Perfusão"**
2. Uma nova linha aparece na tabela
3. Preencha os campos
4. Clique **"✅ Guardar Registo"** para salvar todas as linhas

### **Remover Perfusão:**
1. Na linha da perfusão que quer remover
2. Clique no botão **"🗑️"** na última coluna
3. A linha é removida imediatamente
4. Clique **"✅ Guardar Registo"** para confirmar

### **Editar Perfusão Existente:**
1. Os campos já vêm preenchidos em modo edição
2. Modifique o que necessário
3. Clique **"✅ Guardar Registo"**

---

## 📊 Exemplo de Uso

**Cenário:** Doente com múltiplos medicamentos IV

```
Perfusão 1: Noradrenalina 5mg/kg/min IV → 14:30 → Taxas: 20, 25, 30, 35 mL/h
Perfusão 2: Propofol 10mg/kg/h IV → 14:35 → Taxa: 50 mL/h
Perfusão 3: Morfina 5mg/h IV → 14:40 → Taxa: 10 mL/h

Fármaco 1: Succinilcolina → 14:25
Fármaco 2: Atropina → 14:26
Fármaco 3: Midazolam → 14:28
```

---

## 💻 Implementação Técnica

### **Frontend (JavaScript)**
```javascript
// Adiciona nova linha de perfusão
function addPerfusao() { ... }

// Remove linha de perfusão
function removePerfusao(btn) { ... }

// Adiciona novo fármaco
function addFarmaco() { ... }

// Remove fármaco
function removeFarmaco(btn) { ... }
```

### **Backend (PHP)**
```php
// Processa múltiplas perfusões
if (isset($_POST['perfusao_farmaco']) && is_array(...)) {
  foreach ($_POST['perfusao_farmaco'] as $idx => $farmaco) {
    // INSERT para cada linha
  }
}

// Processa múltiplos fármacos
if (isset($_POST['farmaco_nome']) && is_array(...)) {
  foreach ($_POST['farmaco_nome'] as $idx => $farmaco) {
    // INSERT para cada linha
  }
}
```

### **Compatibilidade com Edição**
- Ao editar, as linhas existentes aparecem preenchidas
- Pode adicionar novas linhas
- Pode remover linhas existentes
- DELETE antes de inserir (para evitar duplicatas)

---

## 🎯 Benefícios

✅ **Interface Intuitiva** - Tabelas familiares para utilizadores  
✅ **Sem Limite** - Adicionar quantas entradas forem necessárias  
✅ **Validação** - Linhas vazias são ignoradas  
✅ **Edição Fácil** - Modificar campos diretamente na tabela  
✅ **Rápida Remoção** - Um clique para remover  
✅ **Sequência Automática** - Números de sequência atribuídos automaticamente  

---

## 📋 Checklist

- [x] Interface de tabela para perfusões
- [x] Interface de tabela para fármacos
- [x] Botões de adicionar/remover funcionar
- [x] Múltiplas entradas salvas na BD
- [x] Edição de entradas existentes
- [x] Validação de linhas vazias
- [x] Sequência automática
- [x] Sem erros de sintaxe

---

## 🚀 Próximas Melhorias (Opcional)

- Expandir mesma funcionalidade para **Eventos Adversos** (intercorrências)
- Expandir para **Monitorização** (sinais vitais)
- Validação de campos obrigatórios por linha
- Cálculos automáticos (Ex: total de medicação)
- Atalhos de teclado (Enter = adicionar linha)

---

**Sistema de Múltiplas Entradas está operacional! 🎉**
