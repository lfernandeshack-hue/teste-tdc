# 🧪 Testes SQL & Validação

Este arquivo contém queries SQL e testes para validar o sistema TDC.

## ✅ Teste 1: Verificar Importação do Schema

Após importar `db.sql` via phpMyAdmin, execute no MySQL Client:

```sql
USE visualtdc;

-- Contar tabelas criadas (deve ser 10)
SELECT COUNT(*) as total_tables FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'visualtdc';
-- Esperado: 10

-- Listar todas as tabelas
SHOW TABLES;
-- Esperado output:
-- +---------------------------+
-- | Tables_in_visualtdc       |
-- +---------------------------+
-- | users                     |
-- | tdc_records               |
-- | tdc_respiratory_support   |
-- | tdc_cardiovascular_support|
-- | tdc_airway                |
-- | tdc_ventilation           |
-- | tdc_circulation           |
-- | tdc_interventions         |
-- | tdc_team                  |
-- | tdc_intercurrencies       |
-- +---------------------------+

-- Verificar estrutura da tabela users
DESC users;
-- Esperado: id, name, email, password, created_at

-- Verificar estrutura de tdc_records
DESC tdc_records;
-- Esperado: id, created_by, ficha_numero, data_ficha, servico, medico_servico, destino, 
--           hora_contacto, diagnostico, score_tdc, gcs, notas_enfermagem, created_at, updated_at
```

---

## ✅ Teste 2: Adicionar Usuário de Teste Manualmente

Se o registro via web não funcionar, adicione manualmente:

```sql
USE visualtdc;

-- Inserir usuário de teste
INSERT INTO users (name, email, password) VALUES (
  'Dr. Silva',
  'silva@hospital.pt',
  '$2y$10$H1K5zzLJfwvI1iJz7uj1POOzJJJpBQrR7PwPzGjy0XBLPzH.j8NQe'  -- senha: teste123
);

-- Verificar inserção
SELECT * FROM users;
-- Esperado: 1 row | id=1, name=Dr. Silva, email=silva@hospital.pt

-- Hash password para test: (use em seu script)
-- password_hash('teste123', PASSWORD_BCRYPT) gera: $2y$10$H1K5zzLJfwvI1iJz7uj1POOzJJJpBQrR7PwPzGjy0XBLPzH.j8NQe
```

---

## ✅ Teste 3: Criar Ficha TDC de Teste Manualmente

Simular dados que seriam inseridos via `tdc_form.php`:

```sql
USE visualtdc;

-- Inserir ficha TDC
INSERT INTO tdc_records (
  created_by,
  ficha_numero,
  data_ficha,
  servico,
  medico_servico,
  destino,
  hora_contacto,
  diagnostico,
  score_tdc,
  gcs,
  notas_enfermagem
) VALUES (
  1,                              -- created_by (usuario Dr. Silva id=1)
  'TDC-2024-001',                 -- ficha_numero
  '2024-01-15',                   -- data_ficha
  'Urgência',                     -- servico
  'Dr. Silva',                    -- medico_servico
  'Hospital Geral',               -- destino
  '10:30',                        -- hora_contacto
  'Infarto agudo do miocárdio',   -- diagnostico
  8,                              -- score_tdc (0-10)
  15,                             -- gcs (3-15)
  'Paciente crítico. Sinais vitais instáveis. Necessário transporte urgente.' -- notas
);

-- Verificar inserção
SELECT * FROM tdc_records WHERE created_by = 1;
-- Esperado: 1 row com dados acima
```

---

## ✅ Teste 4: Testar Isolamento de Dados

Usuários só devem ver suas próprias fichas:

```sql
-- Criar segundo usuário
INSERT INTO users (name, email, password) VALUES (
  'Enfermeira Ana',
  'ana@hospital.pt',
  'hash_de_senha_aqui'
);

-- Criar ficha para usuário 2
INSERT INTO tdc_records (created_by, ficha_numero, data_ficha, servico, diagnostico, score_tdc, gcs) VALUES (
  2, 'TDC-2024-002', '2024-01-16', 'Internamento', 'AVC Isquémico', 6, 14
);

-- Query como se fosse usuário 1 (Dr. Silva):
SELECT * FROM tdc_records WHERE created_by = 1;
-- Esperado: 1 ficha (TDC-2024-001)

-- Query como se fosse usuário 2 (Enfermeira Ana):
SELECT * FROM tdc_records WHERE created_by = 2;
-- Esperado: 1 ficha (TDC-2024-002)

-- Nenhum usuário vê fichas do outro ✅
```

---

## ✅ Teste 5: Validar Constraints

Testar validações de dados:

```sql
-- Teste Score TDC (deve estar entre 0-10)
INSERT INTO tdc_records (created_by, ficha_numero, score_tdc, gcs) VALUES (1, 'TEST-001', 15, 10);
-- Esperado: ❌ ERROR (CHECK constraint violated)
-- Mysql 5.7: Pode não gerar erro (CHECK constraints silenciosas)
-- MySQL 8.0+: ✅ Erro: Check constraint failed

-- Teste GCS (deve estar entre 3-15)
INSERT INTO tdc_records (created_by, ficha_numero, score_tdc, gcs) VALUES (1, 'TEST-002', 5, 20);
-- Esperado: ❌ ERROR (CHECK constraint violated)

-- Teste email UNIQUE
INSERT INTO users (name, email, password) VALUES ('User X', 'silva@hospital.pt', 'hash');
-- Esperado: ❌ ERROR (Duplicate entry for key 'email')
```

---

## ✅ Teste 6: Testar Foreign Keys e CASCADE

Deletar uma ficha principal deve deletar detalhes:

```sql
-- Inserir ficha e detalhe
INSERT INTO tdc_records (created_by, ficha_numero, diagnostico) VALUES (1, 'TDC-CASCADE-TEST', 'Test');
-- Obter ID da ficha
SELECT id FROM tdc_records WHERE ficha_numero = 'TDC-CASCADE-TEST';
-- Suponhamos ID = 5

-- Adicionar detalhe de respiração
INSERT INTO tdc_respiratory_support (tdc_id, oxygen_nasal) VALUES (5, 1);
-- Suponhamos adicionado com sucesso

-- Deletar ficha principal
DELETE FROM tdc_records WHERE id = 5;

-- Verificar se detalhe foi deletado também
SELECT * FROM tdc_respiratory_support WHERE tdc_id = 5;
-- Esperado: 0 rows (deletado em CASCADE ✅)
```

---

## ✅ Teste 7: Testar Timestamps

Verificar que created_at e updated_at funcionam:

```sql
-- Inserir fichas (timestamps devem ser auto)
INSERT INTO tdc_records (created_by, ficha_numero) VALUES (1, 'TDC-TIMESTAMP-TEST');

-- Consultar
SELECT id, ficha_numero, created_at, updated_at FROM tdc_records WHERE ficha_numero = 'TDC-TIMESTAMP-TEST';
-- Esperado: created_at e updated_at preenchidos com NOW()

-- Aguarde 5 segundos, depois UPDATE
UPDATE tdc_records SET diagnostico = 'Updated at ...' WHERE ficha_numero = 'TDC-TIMESTAMP-TEST';

-- Consultar novamente
SELECT created_at, updated_at FROM tdc_records WHERE ficha_numero = 'TDC-TIMESTAMP-TEST';
-- Esperado: created_at mantido, updated_at atualizado para novo NOW()
```

---

## ✅ Teste 8: Testar Prepared Statements (Simulado)

Verificar que application segura contra SQL injection:

```sql
-- EXEMPLO DO QUE SERIA INJETADO (NÃO EXECUTE):
-- Se form tivesse: ficha_numero = "'; DROP TABLE users; --"
-- E usado: "INSERT INTO tdc_records (ficha_numero) VALUES ('" + ficha_numero + "')"
-- Resultado: ❌ DROP TABLE users seria executado

-- COM PREPARED STATEMENTS (usado em tdc_form.php):
-- $stmt = $mysqli->prepare("INSERT INTO tdc_records (ficha_numero) VALUES (?)");
-- $stmt->bind_param("s", $ficha_numero);
-- $stmt->execute();
-- Resultado: ✅ String inteira é tratada como dado, não como comando SQL

-- NO BANCO: ficha_numero = "'; DROP TABLE users; --" (como string, seguro)
```

---

## ✅ Teste 9: Performance - Índices

Verificar índices para queries frequentes:

```sql
-- Listar fichas por usuário (query mais frequente):
-- EXPLAIN SELECT * FROM tdc_records WHERE created_by = 1 ORDER BY created_at DESC LIMIT 50;

-- Sem índice: full table scan (lento se muitos registos)
-- Com índice: index scan (rápido ✅)

-- Criar índice (já deveria estar no db.sql):
ALTER TABLE tdc_records ADD INDEX idx_created_by (created_by);

-- Verificar índices criados:
SHOW INDEX FROM tdc_records;
-- Esperado: idx_created_by na lista
```

---

## ✅ Teste 10: Teste de Backup e Restore

Simular exportação e importação:

```bash
# Linux/Mac/PowerShell:
mysqldump -u root visualtdc > visualtdc_backup.sql

# Verificar arquivo criado
ls -lh visualtdc_backup.sql

# Se tudo fechar, restaurar:
mysql -u root < visualtdc_backup.sql
```

---

## 🔧 Debug Checklist

Se algo não funcionar, verifique em ordem:

### 1. Banco de Dados
```sql
-- Conectar
mysql -u root -p

-- Listar databases
SHOW DATABASES;
-- Se visualtdc não existe: import db.sql

-- Listar tabelas
USE visualtdc;
SHOW TABLES;
-- Se < 10 tabelas: import db.sql novamente

-- Verificar dados de teste
SELECT * FROM users;
SELECT * FROM tdc_records;
-- Se vazio: inserir dados de teste via queries acima
```

### 2. Conexão PHP
Criar arquivo `test_connection.php` em visualtdc:

```php
<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$database = 'visualtdc';

$mysqli = new mysqli($host, $user, $pass, $database);
if ($mysqli->connect_error) {
    die('❌ Erro: ' . $mysqli->connect_error);
}

echo '✅ Conexão OK';
echo '<br>Servidor: ' . $mysqli->server_info;
echo '<br>Versão: ' . $mysqli->server_version;
echo '<br>BD selecionada: ' . $mysqli->select_db($database) ? 'SIM' : 'NÃO';

$res = $mysqli->query('SELECT COUNT(*) as cnt FROM users');
$row = $res->fetch_assoc();
echo '<br>Usuários na BD: ' . $row['cnt'];

$mysqli->close();
?>
```

Acesse: `http://localhost/visualtdc/test_connection.php`  
Esperado: `✅ Conexão OK`

### 3. Sessões PHP
Criar arquivo `test_session.php`:

```php
<?php
session_start();
$_SESSION['test'] = 'Session funcionando!';

echo '✅ Session OK';
echo '<br>Session ID: ' . session_id();
echo '<br>Session data: ' . $_SESSION['test'];
?>
```

Acesse: `http://localhost/visualtdc/test_session.php`

### 4. HTML/CSS
Abrir `http://localhost/visualtdc`  
Pressionar F12 (Developer Tools)  
Abrir Console e procurar por errors JavaScript

---

## 📋 Teste Manual - Roteiro Completo (30 min)

```
PASSO 1: Preparar (5 min)
  ☐ XAMPP Apache iniciado (green)
  ☐ XAMPP MySQL iniciado (green)
  ☐ phpMyAdmin acessível
  ☐ db.sql importado
  ☐ config.php verificado

PASSO 2: Testar Autenticação (10 min)
  ☐ Abrir http://localhost/visualtdc
  ☐ Ver página de login
  ☐ Clique "Registrar"
  ☐ Criar usuário: nome="Teste", email="teste@test.com", senha="teste123"
  ☐ Clique registrar → redireciona para login
  ☐ Login com email/senha criados
  ☐ Ver dashboard
  ☐ Clique logout → volta para login

PASSO 3: Testar CRUD TDC (10 min)
  ☐ Login novamente
  ☐ Clique "🏥 Fichas TDC"
  ☐ Tabela vazia (OK, nenhuma ficha criada ainda)
  ☐ Clique "+ Nova Ficha TDC"
  ☐ Preencher:
    - Nº Ficha: 001
    - Data: 2024-01-15
    - Serviço: Urgência
    - Médico: Dr. Teste
    - Destino: Hospital A
    - Hora: 10:30
    - Diagnóstico: Pneumonia
    - Score: 7
    - GCS: 15
    - Notas: Teste
  ☐ Clique Guardar
  ☐ Redireciona para tdc_list.php
  ☐ Ficha 001 aparece na tabela
  ☐ Clique "Ver" → tdc_view.php abre
  ☐ Dados exibidos corretamente (não em inputs)
  ☐ Clique "Editar" → tdc_form.php com dados preenchidos
  ☐ Alterar Score para 8
  ☐ Clique Guardar
  ☐ Volta para tdc_list.php
  ☐ Ficha mostra Score = 8 (updated ✅)
  ☐ Clique "Remover"
  ☐ Confirmar deleção
  ☐ Ficha desaparece da tabela (✅ CRUD completo)

PASSO 4: Testar Isolamento (5 min)
  ☐ Registre segundo usuário: "Outro"
  ☐ Login como "Outro"
  ☐ Clique "🏥 Fichas TDC"
  ☐ Tabela vazia (não vê fichas do "Teste" ✅)
  ☐ Criar ficha como "Outro"
  ☐ Logout e login como "Teste"
  ☐ Clique "🏥 Fichas TDC"
  ☐ Ver fichas de "Teste" apenas (✅ Isolamento OK)

RESULTADO FINAL: ✅ SISTEMA FUNCIONAL
```

---

## 🚨 Erros Comuns e Soluções

| Erro | Causa | Solução |
|------|-------|---------|
| Access denied for user | config.php credenciais erradas | Verificar password MySQL |
| Table doesn't exist | db.sql não importado | Re-importar db.sql |
| No rows updated | Prepared statement falhou | Ver error_log do Apache |
| Ficha não salva | POST não processado | F12 → Console → procure error |
| Login loop infinito | session_start() não encontrado | Verificar topo de login.php |
| CSS não carrega | Caminho relativo errado | Verificar <link href="styles.css"> |
| 500 Internal Error | Erro PHP grave | Ver logs em XAMPP\logs\ |
| Session expires | Timeout ou cookie | Limpar cookies browser (F12) |

---

**Versão**: 1.0  
**Data**: 2024  
**Sistema**: TDC - Transporte Doente Crítico

Fim do guia de testes.
