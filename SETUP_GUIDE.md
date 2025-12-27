# 🚀 Guia de Setup - Sistema TDC

## Pré-requisitos
- XAMPP com PHP 7.4+ e MySQL 5.7+ instalados
- Projeto em: `C:\xampp1\htdocs\visualtdc`
- Navegador moderno (Chrome, Firefox, Edge)

---

## Passo 1: Importar Banco de Dados

### Opção A: phpMyAdmin (GUI)
1. Abra `http://localhost/phpmyadmin`
2. Clique em "Novo" ou "Create"
3. Digite database name: `visualtdc`
4. Clique "Criar"
5. Selecione a database `visualtdc`
6. Clique em "Importar"
7. Selecione arquivo: `db.sql` (em `C:\xampp1\htdocs\visualtdc\db.sql`)
8. Clique "Executar"

### Opção B: MySQL CLI
```powershell
mysql -u root
mysql> CREATE DATABASE visualtdc;
mysql> USE visualtdc;
mysql> source C:\xampp1\htdocs\visualtdc\db.sql;
mysql> EXIT;
```

### Verificar Importação
```sql
USE visualtdc;
SHOW TABLES;
-- Deve exibir 11 tabelas:
-- users, tdc_records, tdc_respiratory_support, tdc_cardiovascular_support,
-- tdc_airway, tdc_ventilation, tdc_circulation, tdc_interventions,
-- tdc_team, tdc_intercurrencies
```

---

## Passo 2: Verificar config.php

Abra `C:\xampp1\htdocs\visualtdc\config.php` e confirme:

```php
$host = 'localhost';
$user = 'root';
$pass = '';              // Deixar vazio para XAMPP padrão
$database = 'visualtdc';

$mysqli = new mysqli($host, $user, $pass, $database);
if ($mysqli->connect_error) {
    die('Erro de conexão: ' . $mysqli->connect_error);
}
```

**Se usar senha diferente:**
```php
$pass = 'sua_senha_aqui';  // Atualizar
```

---

## Passo 3: Inicie os Serviços XAMPP

### Windows:
1. Abra XAMPP Control Panel
2. Clique em "Start" para:
   - **Apache** (servidor web)
   - **MySQL** (banco de dados)
3. Espere até ficarem "green" (verdes)

---

## Passo 4: Acesse o Sistema

Abra navegador e acesse:
```
http://localhost/visualtdc
```

Deve exibir **página de Login**.

---

## Passo 5: Criar Primeiro Usuário

1. Clique em "Registrar" ou acesse `http://localhost/visualtdc/register.php`
2. Preencha:
   - **Nome**: ex. "Dr. Silva"
   - **Email**: ex. "silva@hospital.pt"
   - **Senha**: ex. "senha123"
3. Clique "Registrar"

---

## Passo 6: Login

1. Acesse `http://localhost/visualtdc/login.php`
2. Preencha email e senha do usuário criado
3. Clique "Login"

---

## Passo 7: Testar CRUD - Criar Ficha TDC

1. Após login, deve exibir **Dashboard**
2. Clique em "🏥 Fichas TDC"
3. Clique em "+ Nova Ficha TDC"
4. Preencha formulário:
   - **Nº Ficha**: 001
   - **Data Ficha**: 2024-01-15
   - **Serviço**: Urgência
   - **Médico do Serviço**: Dr. Silva
   - **Destino**: Hospital Geral
   - **Hora Contacto**: 10:30
   - **Diagnóstico**: Infarto agudo do miocárdio
   - **Score TDC**: 8
   - **GCS**: 15
   - **Notas**: Paciente crítico, necessário transporte urgente
5. Clique "Guardar"

---

## Passo 8: Testar Visualização

1. Volta a "Fichas TDC" (tdc_list.php)
2. Deve exibir ficha criada em tabela
3. Clique em "Ver" para visualizar detalhes

---

## Passo 9: Testar Edição

1. Em "Fichas TDC", clique em "Editar" de uma ficha
2. Altere algum campo (ex. Score TDC para 9)
3. Clique "Guardar"
4. Verifique se atualização foi registrada

---

## Passo 10: Testar Eliminação

1. Em "Fichas TDC", clique em "Remover" de uma ficha
2. Confirme eliminação
3. Verifique se ficha foi removida da tabela

---

## ✅ Checklist de Funcionamento

- [ ] Database `visualtdc` importado com 11 tabelas
- [ ] Apache e MySQL rodando no XAMPP
- [ ] Página de login acessível em `http://localhost/visualtdc`
- [ ] Usuário criado com sucesso via registro
- [ ] Login funcionando
- [ ] Dashboard exibido após login
- [ ] Link "🏥 Fichas TDC" funcional
- [ ] Ficha TDC criada com sucesso
- [ ] Ficha exibida em tabela (tdc_list.php)
- [ ] Detalhes visualizáveis (tdc_view.php)
- [ ] Edição funcionando (tdc_form.php)
- [ ] Eliminação funcionando (tdc_delete.php)

---

## 🆘 Troubleshooting

### ❌ "Access denied for user 'root'@'localhost'"
**Causa**: Credenciais MySQL em `config.php` estão incorretas.

**Solução**:
1. Verifique password no XAMPP (geralmente vazio)
2. Edite `config.php`:
   ```php
   $pass = '';  // Ou a senha que está usando
   ```
3. Teste conexão via phpMyAdmin

---

### ❌ "Table 'visualtdc.users' doesn't exist"
**Causa**: `db.sql` não foi importado corretamente.

**Solução**:
1. Acesse phpMyAdmin
2. Verifique se database `visualtdc` existe
3. Re-importe `db.sql` seguindo Passo 1
4. Verifique com: `SHOW TABLES;`

---

### ❌ "Cannot start session" 
**Causa**: `session_start()` chamado após output HTML.

**Solução**:
- Verifique se não há espaços ou newlines antes de `<?php` em todos os arquivos .php
- Verifique BOM (Byte Order Mark) em editor (UTF-8 sem BOM recomendado)

---

### ❌ Ficha não salva ao clicar "Guardar"
**Causa**: Prepared statement binding falhou.

**Solução**:
1. Abra Developer Tools (F12) → Console
2. Procure por mensagens de erro
3. Verifique `error_log` do Apache em XAMPP\logs
4. Confirme que todos os campos obrigatórios foram preenchidos

---

### ❌ Logout não funciona
**Causa**: Script `logout.php` não encontrado ou session não destruída.

**Solução**:
1. Verifique se arquivo `logout.php` existe
2. Limpe cookies do navegador: F12 → Storage → Cookies → Delete

---

## 📖 Próximas Expansões

Após validar o fluxo básico, você pode:

1. **Expandir Formulário TDC**
   - Adicionar seções para suporte respiratório, cardiovascular, intervenções, etc.
   - Editar `tdc_form.php` para incluir campos adicionais

2. **Exportar para PDF**
   - Instalar biblioteca como TCPDF ou mPDF
   - Criar página `tdc_export.php`

3. **Relatórios**
   - Criar página `reports.php` com filtros por data/serviço
   - Gráficos com Chart.js ou similar

4. **Multi-usuário Admin**
   - Adicionar role `admin` na tabela `users`
   - Admin vê fichas de todos; enfermeiro vê só suas

---

## 📞 Suporte Rápido

Erro não listado acima?

1. Verifique logs:
   - Apache: `C:\xampp\apache\logs\error.log`
   - MySQL: `C:\xampp\mysql\data\error.log`
   - PHP: `C:\xampp\php\logs\php_error.log` (se habilitado)

2. Teste componentes individualmente:
   - BD: `http://localhost/phpmyadmin` → Conexão OK?
   - PHP: Crie arquivo `test.php` com `<?php phpinfo(); ?>`
   - Acesso: Clique em `test.php`

3. Reinicie serviços:
   - XAMPP Control Panel → Stop Apache → Stop MySQL
   - Aguarde 5 segundos
   - Click Start nos dois novamente

---

**Versão**: 1.0  
**Data**: 2024  
**Sistema**: Sistema TDC - Transporte Doente Crítico
