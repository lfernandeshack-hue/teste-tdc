# 🚀 Quick Start - Sistema TDC em 5 Minutos

## O que é isso?

Sistema web (PHP/MySQL) para registar fichas de enfermagem de transporte de doentes críticos. Baseado em formulário PDF português extraído via OCR.

---

## 🎯 Fluxo Rápido

```
1. IMPORT db.sql (phpMyAdmin)
2. START Apache + MySQL (XAMPP)
3. REGISTER usuário (http://localhost/visualtdc)
4. LOGIN
5. CREATE nova ficha TDC
6. DONE ✅
```

**Tempo total**: 5 minutos

---

## 📋 Pre-requisitos

- ✅ XAMPP instalado (Apache + MySQL + PHP)
- ✅ Pasta: `C:\xampp1\htdocs\visualtdc` (já existe)
- ✅ Todos arquivos: `.php`, `.sql`, `.css` (já presentes)

---

## ⚡ 4 Passos Mágicos

### 1️⃣ Importar Base de Dados (2 min)

**Via phpMyAdmin** (mais fácil):
1. Abra http://localhost/phpmyadmin
2. Clique "New Database" → Digite `visualtdc` → Create
3. Clique em `visualtdc` (tabela vazia)
4. Clique "Import"
5. Selecione arquivo `C:\xampp1\htdocs\visualtdc\db.sql`
6. Clique "Go" / "Import"
7. ✅ 11 tabelas criadas

**Ou via MySQL CLI**:
```powershell
mysql -u root -p
mysql> CREATE DATABASE visualtdc;
mysql> USE visualtdc;
mysql> source C:\xampp1\htdocs\visualtdc\db.sql;
mysql> EXIT;
```

---

### 2️⃣ Iniciar XAMPP (1 min)

1. Abra **XAMPP Control Panel**
2. Clique **Start** em:
   - Apache
   - MySQL
3. Aguarde até ficarem **GREEN** (verdes)

---

### 3️⃣ Criar Usuário (1 min)

1. Abra http://localhost/visualtdc
2. Clique "**Registrar**"
3. Preencha:
   - Nome: `Dr. Silva`
   - Email: `silva@test.pt`
   - Senha: `teste123`
4. Clique "**Registrar**"

---

### 4️⃣ Criar Primeira Ficha TDC (1 min)

1. Acesse http://localhost/visualtdc/login.php
2. Login com email/senha criados
3. Clique "🏥 **Fichas TDC**"
4. Clique "**+ Nova Ficha TDC**"
5. Preencha:
   - Nº Ficha: `001`
   - Data: `2024-01-15`
   - Serviço: `Urgência`
   - Diagnóstico: `Teste`
   - Score: `7`
   - GCS: `15`
6. Clique "**Guardar**"

✅ **Pronto! Ficha criada e visible na tabela!**

---

## 🔍 O que Funciona Agora

| Feature | Status | Como Testar |
|---------|--------|-------------|
| Login/Logout | ✅ Funcional | Register → Login → Logout |
| Criar Ficha | ✅ Funcional | "+ Nova Ficha" → Guardar |
| Listar Fichas | ✅ Funcional | "Fichas TDC" mostra tabela |
| Ver Detalhes | ✅ Funcional | Clique "Ver" na tabela |
| Editar Ficha | ✅ Funcional | Clique "Editar" → Update |
| Deletar Ficha | ✅ Funcional | Clique "Remover" → Confirm |

---

## 📁 Arquivos Importantes

| Arquivo | Descrição |
|---------|-----------|
| `config.php` | Conexão MySQL (credenciais) |
| `db.sql` | Schema (11 tabelas) |
| `tdc_form.php` | Form criar/editar |
| `tdc_list.php` | Tabela de fichas |
| `tdc_view.php` | Visualizar detalhe |
| `tdc_delete.php` | Deletar ficha |

---

## ❓ Dúvidas Rápidas

**P: Erro "Access denied"?**  
R: Mude password em `config.php` linha 5

**P: Erro "Table doesn't exist"?**  
R: Re-importe `db.sql` via phpMyAdmin

**P: Login não funciona?**  
R: Verifique se usuário foi criado (phpMyAdmin → users table)

**P: Quero documentação completa?**  
R: Leia `SETUP_GUIDE.md` ou `README.md`

---

## 📚 Documentação Completa

| Arquivo | Propósito |
|---------|-----------|
| `README.md` | Overview do projeto |
| `SETUP_GUIDE.md` | Instalação step-by-step |
| `TESTING.md` | Testes SQL + validação |
| `ARCHITECTURE.md` | Fluxos de dados + diagramas |
| `PROJECT_STATUS.md` | Status de desenvolvimento |
| `QUICK_START.md` | Este arquivo 🚀 |

---

## 🎓 Próximos Passos

Após validar o básico:

1. **Expandir Formulário**
   - Editar `tdc_form.php`
   - Adicionar seções de suporte respiratório, intervenções, etc.

2. **Exportar PDF**
   - Instalar biblioteca TCPDF/mPDF
   - Criar `tdc_export.php`

3. **Relatórios**
   - Filtros por data/serviço
   - Gráficos de dados

---

## 🆘 Algo Não Funciona?

**Passo 1**: Abra `SETUP_GUIDE.md` → Troubleshooting  
**Passo 2**: Abra `TESTING.md` → Execute testes SQL  
**Passo 3**: Verifique logs:
- Apache: `C:\xampp\apache\logs\error.log`
- MySQL: `C:\xampp\mysql\data\error.log`

---

## ✅ Checklist Final

- [ ] db.sql importado
- [ ] Apache + MySQL verdes
- [ ] Página login acessível
- [ ] Usuário registrado
- [ ] Login funcionando
- [ ] Ficha TDC criada
- [ ] Ficha visível na tabela
- [ ] Edição funciona
- [ ] Deleção funciona

**Todos itens ✅? Sistema TDC pronto para uso! 🎉**

---

**Tempo investido**: 5 minutos  
**Resultado**: Sistema TDC totalmente funcional  
**Próximo**: Expandir formulário ou exportar PDF

---

Versão: 1.0  
Sistema: TDC - Transporte Doente Crítico  
Criado: 2024
