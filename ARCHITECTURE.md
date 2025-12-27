# 🎯 Arquitetura e Fluxo do Sistema TDC

## Fluxo de Autenticação

```
┌─────────────────────────────────────┐
│     http://localhost/visualtdc      │
└──────────────┬──────────────────────┘
               │
               ▼
        ┌─────────────┐
        │  index.php  │
        │  Verifica   │
        │  session    │
        └──┬──────┬───┘
           │      │
      ✅ Yes │      │ ❌ No
           │      │
           ▼      ▼
      dashboard  login.php
        .php     │
               New user?
                 │
                 ├─ ❌ No (existing): LOGIN
                 │     │
                 │     ▼
                 │   authenticate
                 │   user
                 │     │
                 │     ▼
                 │ set $_SESSION
                 │     │
                 │     └──►┐
                 │         │
                 └─ ✅ Yes: └──► dashboard.php
                   (register.php)

```

---

## Fluxo CRUD TDC

```
DASHBOARD.PHP (Menu Principal)
│
├─ 📋 Itens (LEGACY)
│  └─ items.php → item_edit.php → (CRUD genérico)
│
└─ 🏥 Fichas TDC (NOVO)
   │
   ├─ TDC_LIST.PHP (Listar)
   │  │
   │  ├─ SELECT * FROM tdc_records WHERE created_by=$_SESSION['user_id']
   │  │
   │  └─ Tabela com linhas:
   │     │ ID | Data | Serviço | Diagnóstico | Score | [Ver][Editar][Remover] │
   │     │
   │     ├─ Ver ──────────► TDC_VIEW.PHP (READ ONLY)
   │     │                  └─ Display formatted fields
   │     │                     └─ [Editar][Remover][Voltar]
   │     │
   │     ├─ Editar ────────► TDC_FORM.PHP (EDIT MODE)
   │     │                  └─ GET id=... → Load record
   │     │                     └─ Fill form
   │     │                        └─ POST → UPDATE
   │     │                           └─ Redirect tdc_list.php
   │     │
   │     └─ Remover ────────► TDC_DELETE.PHP
   │                          └─ DELETE * WHERE id=... AND created_by=$uid
   │                             └─ Redirect tdc_list.php
   │
   └─ [+ Nova Ficha TDC] ──► TDC_FORM.PHP (CREATE MODE)
      │                      └─ Form empty
      │                         └─ POST → INSERT
      │                            └─ Redirect tdc_list.php

```

---

## Arquitetura de Banco de Dados

```
┌─────────────────────────────────┐
│     BANCO: visualtdc            │
├─────────────────────────────────┤
│
├─ 👤 USERS
│  ├─ id (PRIMARY KEY)
│  ├─ name
│  ├─ email (UNIQUE)
│  ├─ password (hashed)
│  └─ created_at
│
├─ 📋 TDC_RECORDS (FICHA PRINCIPAL)
│  ├─ id (PRIMARY KEY)
│  ├─ created_by (FK → users.id)
│  ├─ ficha_numero
│  ├─ data_ficha
│  ├─ servico
│  ├─ medico_servico
│  ├─ destino
│  ├─ hora_contacto
│  ├─ diagnostico
│  ├─ score_tdc (0-10)
│  ├─ gcs (3-15)
│  ├─ notas_enfermagem
│  ├─ created_at
│  └─ updated_at
│
├─ 🫁 TDC_RESPIRATORY_SUPPORT
│  ├─ id (PRIMARY KEY)
│  ├─ tdc_id (FK → tdc_records.id)
│  ├─ oxygen_nasal
│  ├─ o2_mask
│  ├─ high_flow
│  └─ ...
│
├─ ❤️ TDC_CARDIOVASCULAR_SUPPORT
│  ├─ id
│  ├─ tdc_id (FK)
│  ├─ vasopressores
│  ├─ fluids
│  └─ ...
│
├─ 🫀 TDC_AIRWAY
│  ├─ id
│  ├─ tdc_id (FK)
│  ├─ guedell
│  ├─ aspiration
│  └─ ...
│
├─ 💨 TDC_VENTILATION
│  ├─ id
│  ├─ tdc_id (FK)
│  ├─ type (ON/MV/MAC/Alto Fluxo)
│  ├─ fio2
│  └─ ...
│
├─ 🩸 TDC_CIRCULATION
│  ├─ id
│  ├─ tdc_id (FK)
│  ├─ vascular_access
│  ├─ caliber
│  └─ ...
│
├─ 🔧 TDC_INTERVENTIONS
│  ├─ id
│  ├─ tdc_id (FK)
│  ├─ compressions
│  ├─ defibrillation
│  ├─ sav
│  └─ ...
│
├─ 👥 TDC_TEAM
│  ├─ id
│  ├─ tdc_id (FK)
│  ├─ nurse_name
│  ├─ doctor_name
│  └─ mecanografico
│
└─ ⚠️ TDC_INTERCURRENCIES
   ├─ id
   ├─ tdc_id (FK)
   ├─ description
   └─ ...

```

---

## Arquitetura de Pastas & Arquivos

```
C:\xampp1\htdocs\visualtdc\
│
├─ 🔐 AUTENTICAÇÃO
│  ├─ config.php (DB + esc() function)
│  ├─ login.php (POST: authenticate user)
│  ├─ register.php (POST: create user)
│  └─ logout.php (destroy session)
│
├─ 📄 INTERFACE
│  ├─ index.php (redirect automático)
│  ├─ dashboard.php (menu principal)
│  └─ styles.css (CSS global)
│
├─ 🏥 FICHAS TDC
│  ├─ tdc_list.php (SELECT * + table)
│  ├─ tdc_form.php (GET: load | POST: insert/update)
│  ├─ tdc_view.php (SELECT single + read-only)
│  └─ tdc_delete.php (DELETE + redirect)
│
├─ 📦 LEGADO
│  ├─ items.php (CRUD genérico, ainda funciona)
│  └─ item_edit.php
│
├─ 🗄️ BANCO DE DADOS
│  └─ db.sql (11 CREATE TABLE statements)
│
├─ 📚 DOCUMENTAÇÃO
│  ├─ README.md (overview)
│  ├─ SETUP_GUIDE.md (passo-a-passo)
│  ├─ PROJECT_STATUS.md (este arquivo)
│  └─ ARCHITECTURE.md (diagrama - este arquivo)
│
├─ 📸 UTILIDADES
│  ├─ ocr_pdf.py (Python: extract PDF)
│  ├─ Scan 34.pdf (original)
│  └─ Scan_34_extracted.txt (output)
│
└─ (arquivo este) ARCHITECTURE.md
```

---

## Fluxo de Dados - Exemplo: Criar Ficha TDC

```
1. USUÁRIO CLICA "+ NOVA FICHA"
   └─ GET tdc_form.php (sem parâmetros)

2. TDC_FORM.PHP RENDERIZA
   ├─ if (isset($_GET['id'])):
   │  └─ $id > 0: modo EDIÇÃO (load record)
   │  └─ $id = 0 ou ausente: modo CRIAR (form vazio)
   │
   └─ Exibe HTML FORM com campos:
      ├─ ficha_numero
      ├─ data_ficha
      ├─ servico
      ├─ medico_servico
      ├─ destino
      ├─ hora_contacto
      ├─ diagnostico
      ├─ score_tdc
      ├─ gcs
      ├─ notas_enfermagem
      └─ [GUARDAR] button (type=submit)

3. USUÁRIO PREENCHE FORM E CLICA GUARDAR
   └─ POST tdc_form.php (mesmo arquivo)

4. TDC_FORM.PHP PROCESSA POST
   ├─ Recebe dados do formulário
   │  ├─ $ficha_numero = $_POST['ficha_numero']
   │  ├─ $data_ficha = $_POST['data_ficha']
   │  ├─ ... (outros campos)
   │  └─ $id = isset($_GET['id']) ? (int)$_GET['id'] : 0
   │
   ├─ if ($id > 0): UPDATE
   │  └─ $stmt = $mysqli->prepare('UPDATE tdc_records SET ... WHERE id=?')
   │     └─ $stmt->bind_param('...',...)
   │        └─ $stmt->execute()
   │
   └─ else: INSERT
      └─ $stmt = $mysqli->prepare('INSERT INTO tdc_records (...) VALUES (...)')
         └─ $stmt->bind_param('...',...)
            └─ $stmt->execute()
               └─ $new_id = $mysqli->insert_id

5. REDIRECIONAMENTO
   └─ header('Location: tdc_list.php')

6. TDC_LIST.PHP CARREGA
   ├─ SELECT * FROM tdc_records WHERE created_by=$_SESSION['user_id']
   └─ Exibe tabela com ficha nova/atualizada

```

---

## Fluxo de Dados - Exemplo: Editar Ficha TDC

```
1. USUÁRIO CLICA "EDITAR" EM LINHA DA TABELA
   └─ GET tdc_form.php?id=5

2. TDC_FORM.PHP CARREGA (GET mode)
   ├─ $id = (int)$_GET['id'] = 5
   ├─ SELECT * FROM tdc_records WHERE id=5 AND created_by=$_SESSION['user_id']
   ├─ $tdc = $res->fetch_assoc()
   └─ Popula form com dados de $tdc
      ├─ <input value="<?php echo $tdc['ficha_numero']; ?>" />
      ├─ <input value="<?php echo $tdc['data_ficha']; ?>" />
      └─ ... (todos campos)

3. USUÁRIO EDITA CAMPOS E CLICA GUARDAR
   └─ POST tdc_form.php?id=5

4. TDC_FORM.PHP PROCESSA POST
   ├─ $id = (int)$_GET['id'] = 5
   ├─ Recebe dados novos do formulário
   └─ UPDATE tdc_records SET ... WHERE id=5 AND created_by=$_SESSION['user_id']

5. REDIRECIONAMENTO
   └─ header('Location: tdc_list.php')

6. TDC_LIST.PHP CARREGA COM DADOS ATUALIZADOS
```

---

## Fluxo de Dados - Exemplo: Visualizar Ficha TDC

```
1. USUÁRIO CLICA "VER" EM LINHA DA TABELA
   └─ GET tdc_view.php?id=5

2. TDC_VIEW.PHP CARREGA (READ-ONLY mode)
   ├─ $id = (int)$_GET['id'] = 5
   ├─ SELECT * FROM tdc_records WHERE id=5 AND created_by=$_SESSION['user_id']
   ├─ $tdc = $res->fetch_assoc()
   └─ Exibe dados formatados (NÃO em form inputs)
      ├─ <div class="view-field">
      │  ├─ <label>Nº Ficha:</label>
      │  └─ <span><?php echo esc($tdc['ficha_numero']); ?></span>
      │
      └─ ... (todos campos)

3. OPÇÕES DE AÇÃO
   ├─ [Editar] → GET tdc_form.php?id=5
   ├─ [Remover] → GET tdc_delete.php?id=5 (com confirm)
   └─ [Voltar] → GET tdc_list.php
```

---

## Fluxo de Dados - Exemplo: Deletar Ficha TDC

```
1. USUÁRIO CLICA "REMOVER" EM LINHA DA TABELA
   └─ GET tdc_delete.php?id=5 (with JavaScript confirm)

2. TDC_DELETE.PHP PROCESSA (NO FORM)
   ├─ $id = (int)$_GET['id'] = 5
   ├─ DELETE FROM tdc_records WHERE id=5 AND created_by=$_SESSION['user_id']
   │
   ├─ Verificação de segurança:
   │  └─ Confirma que ficha pertence ao usuário (created_by=$_SESSION['user_id'])
   │     └─ Se não pertencer: DELETE não ocorre (0 rows affected)
   │
   └─ header('Location: tdc_list.php')

3. TDC_LIST.PHP CARREGA SEM A FICHA DELETADA
   └─ SELECT retorna agora 1 menos ficha
```

---

## Segurança - Camadas Implementadas

```
┌─────────────────────────────────────────────┐
│         REQUEST ENTRA (usuário)             │
└──────────────────┬──────────────────────────┘
                   │
                   ▼
        ┌──────────────────────┐
        │  SESSION VALIDATION  │◄─ Protege: Acesso não autenticado
        │  if (!isset($_SESSION['user_id']))
        │      header('Location: login.php')
        └──────────────────────┘
                   │
                   ▼
        ┌──────────────────────────────┐
        │  SQL INJECTION PREVENTION    │◄─ Protege: SQL injection
        │  $stmt = $mysqli->prepare()
        │  $stmt->bind_param(...)
        │  (NOT: string concatenation)
        └──────────────────────────────┘
                   │
                   ▼
        ┌──────────────────────────────┐
        │  USER ISOLATION CHECK        │◄─ Protege: Cross-user access
        │  WHERE created_by=$_SESSION['user_id']
        │  (Usuário vê APENAS suas fichas)
        └──────────────────────────────┘
                   │
                   ▼
        ┌──────────────────────────────┐
        │  XSS (HTML Escaping)         │◄─ Protege: JavaScript injection
        │  <?php echo esc($variable); ?>
        │  (esc = htmlspecialchars)
        └──────────────────────────────┘
                   │
                   ▼
        ┌──────────────────────────────┐
        │  PASSWORD HASHING            │◄─ Protege: Password breach
        │  password_hash($_POST['pass'])
        │  (register.php)
        └──────────────────────────────┘
                   │
                   ▼
           ✅ DATA SALVO/ATUALIZADO
                   │
                   ▼
        ┌──────────────────────────────┐
        │  RESPONSE ENVIADA (usuário)  │
        │  html + CSS renderizado      │
        └──────────────────────────────┘
```

---

## Comparação: Novo (TDC) vs Legado (Items)

```
FEATURE          │ ITEMS.PHP (LEGADO) │ TDC_*.PHP (NOVO)
─────────────────┼────────────────────┼─────────────────
Criado em        │ Skeleton inicial    │ Após OCR
Propósito        │ CRUD genérico      │ Fichas de enfermagem TDC
Tabela           │ items              │ tdc_records + 8 detalhe
Campos           │ id, name, desc     │ ficha_numero, data, serviço, diagnóstico, score_tdc, gcs, ...
Design           │ Monolítico         │ Normalizado (FK relationships)
Segurança        │ Prepared stmts     │ Prepared stmts ✅
User isolation   │ ✅ created_by      │ ✅ created_by
Status           │ Funcional/Supersed │ ✅ Ativo
Docs             │ Nenhuma            │ Completa (README + SETUP)
```

---

## Extensibilidade - Como Adicionar Novo Campo

**Exemplo: Adicionar campo "Modo de Ventilação" em tdc_records**

### Passo 1: Alterar Schema (db.sql)
```sql
ALTER TABLE tdc_records ADD COLUMN modo_ventilacao VARCHAR(50) AFTER gcs;
-- Ou re-criar tabela com campo novo
```

### Passo 2: Atualizar Form (tdc_form.php)
```php
// GET mode (load):
$modo_ventilacao = $tdc['modo_ventilacao'] ?? '';

// HTML Form:
<input type="text" name="modo_ventilacao" value="<?php echo esc($modo_ventilacao); ?>" />

// POST mode (save):
$modo_ventilacao = $_POST['modo_ventilacao'] ?? '';

// Prepared statement:
$stmt = $mysqli->prepare('INSERT INTO tdc_records (..., modo_ventilacao) VALUES (..., ?)');
$stmt->bind_param('...',  ..., $modo_ventilacao);
```

### Passo 3: Atualizar View (tdc_view.php)
```php
<div class="view-field">
  <label>Modo Ventilação:</label>
  <span><?php echo esc($tdc['modo_ventilacao'] ?? '-'); ?></span>
</div>
```

### Passo 4: Nenhuma alteração em tdc_list.php / tdc_delete.php

**Total de alterações**: ~3 arquivos | ~5 minutos

---

## Performance - Índices Recomendados

```sql
-- Já criados (PRIMARY KEY):
ALTER TABLE tdc_records ADD INDEX idx_created_by (created_by);
-- Recomendado adicionar para queries frequentes:
ALTER TABLE tdc_records ADD INDEX idx_data_ficha (data_ficha);
ALTER TABLE tdc_records ADD INDEX idx_servico (servico);
ALTER TABLE tdc_records ADD INDEX idx_created_by_data (created_by, data_ficha);
```

---

**Fim da Documentação de Arquitetura**

Versão: 1.0  
Data: 2024  
Sistema: TDC - Transporte Doente Crítico
