# 🧪 Teste Rápido - Múltiplas Perfusões e Fármacos

## 📋 Passo 1: Criar Novo Registo

1. Abra http://localhost/visualtdc/tdc_form_novo.php
2. Preencha dados administrativos básicos:
   - **Motivo:** `Acidente rodoviário`
   - **Serviço Destino:** `Cirurgia`
   - **Hora Ativação:** `14:30`

## 🩺 Passo 2: Testar Múltiplas Perfusões

1. Vá para aba **"💊 Terapêutica"**
2. Você vê a tabela de **Perfusões** vazia
3. Clique **"➕ Adicionar Perfusão"** 3 vezes
4. Preencha assim:

| Linha | Fármaco | Posologia | Hora | Taxa 1 | Taxa 2 | Taxa 3 | Taxa 4 |
|-------|---------|-----------|------|--------|--------|--------|--------|
| 1 | Noradrenalina | 5mg/kg/min IV | 14:30 | 20 | 25 | 30 | 35 |
| 2 | Propofol | 10mg/kg/h IV | 14:35 | 50 | - | - | - |
| 3 | Morfina | 5mg/h IV | 14:40 | 10 | - | - | - |

5. Deixe uma linha vazia (para testar validação)
6. Clique no 🗑️ de uma linha para testar remoção

## 💉 Passo 3: Testar Múltiplos Fármacos

1. Continue na aba **"💊 Terapêutica"**
2. Vá para **Outros Fármacos**
3. Clique **"➕ Adicionar Fármaco"** 3 vezes
4. Preencha assim:

| Linha | Fármaco | Hora |
|-------|---------|------|
| 1 | Succinilcolina 1mg/kg | 14:25 |
| 2 | Atropina 0.5mg | 14:26 |
| 3 | Midazolam 0.1mg/kg | 14:28 |

5. Teste remover uma linha com 🗑️

## ✅ Passo 4: Guardar Registo

1. Clique **"✅ Guardar Registo"** no fim da página
2. O registo é criado com sucesso
3. Você é redirecionado para edição do mesmo registo

## 👁️ Passo 5: Verificar Dados Salvos

1. Vá para **"🏥 Fichas TDC (Nova)"** no menu
2. Encontre o registo que criou
3. Clique **"👁️ Ver"**
4. Procure a tabela de **Perfusões** e **Fármacos**
5. Deve ver todas as entradas que adicionou

**Exemplo de tabela esperada:**
```
Perfusões (Medicamentos IV)
Fármaco         | Posologia      | Hora  | TA (mmHg)
Noradrenalina   | 5mg/kg/min IV  | 14:30 | 20 | 25 | 30 | 35
Propofol        | 10mg/kg/h IV   | 14:35 | 50
Morfina         | 5mg/h IV       | 14:40 | 10
```

## ✨ Passo 6: Editar Registo Existente

1. De volta à listagem, clique **"✏️ Editar"** no mesmo registo
2. Vá para **"💊 Terapêutica"**
3. As perfusões e fármacos aparecem preenchidas
4. Adicione mais uma perfusão clicando **"➕ Adicionar Perfusão"**
5. Preencha: `Insulina | 0.1 unidade/kg/h | 14:45 | 5`
6. Remova uma das existentes com 🗑️
7. Clique **"✅ Guardar Registo"**
8. Verifique se as mudanças foram salvas

## 🐛 Checklist de Validação

- [ ] Adicionar perfusão funciona
- [ ] Adicionar fármaco funciona
- [ ] Remover linha funciona
- [ ] Dados são salvos na BD
- [ ] Edição recupera dados corretamente
- [ ] Linhas vazias não são salvas
- [ ] Visualização mostra todas as entradas
- [ ] Pode editar e adicionar novamente

---

**Se tudo funcionar, o sistema está pronto para produção! ✅**
