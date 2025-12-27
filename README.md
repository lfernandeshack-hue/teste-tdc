# Sistema TDC - Transporte Doente Crítico

Aplicação PHP/MySQL para registro e gerenciamento de fichas de enfermagem de transporte de doentes críticos baseado em formulário PDF scaneado.

## 📋 Visão Geral

- **Tipo**: Aplicação Web (PHP 7.4+, MySQL 5.7+)
- **Funcionalidade**: CRUD completo para fichas TDC (Transporte Doente Crítico)
- **Origem**: PDF scaneado extraído via OCR (Python + Tesseract)
- **Usuários**: Enfermeiros, Médicos, Equipes de Transporte

## 🚀 Instalação Rápida (XAMPP)

### 1. Preparar Banco de Dados
Abra phpMyAdmin ou MySQL CLI:
```sql
CREATE DATABASE visualtdc;
USE visualtdc;
source db.sql;  -- Importar schema
```

### 2. Configurar Credenciais
Editar `config.php` com suas credenciais MySQL:
```php
$mysqli = new mysqli('localhost', 'root', '', 'visualtdc');
```

### 3. Acessar Sistema
```
http://localhost/visualtdc
```

## 👤 Fluxo de Uso

1. **Dashboard** → `dashboard.php` (Página inicial)
2. **Nova Ficha TDC** → `tdc_form_novo.php` (Criar registo)
3. **Listar Fichas** → `tdc_list_novo.php` (Ver todos os registos)
4. **Visualizar Ficha** → `tdc_view_novo.php` (Ver detalhes)
5. **Exportar para PDF** → `tdc_export_pdf.php` (Gerar PDF)
6. **Editar Ficha** → `tdc_form_novo.php?id=X` (Atualizar registo)

## 📁 Arquivos Principais

**Formulários TDC (Sistema Principal):**
- [tdc_form_novo.php](tdc_form_novo.php) - Criar/editar fichas TDC com 6 abas
- [tdc_list_novo.php](tdc_list_novo.php) - Listar todos os registos TDC
- [tdc_view_novo.php](tdc_view_novo.php) - Visualizar detalhes completos
- [tdc_export_pdf.php](tdc_export_pdf.php) - **NOVO** - Exportar registo para PDF

**Navegação e Configuração:**
- [dashboard.php](dashboard.php) - Página inicial com menu
- [config.php](config.php) - Configuração de banco de dados
- [styles.css](styles.css) - Estilos CSS compartilhados

**Documentação:**
- [EXPORT_PDF_GUIDE.md](EXPORT_PDF_GUIDE.md) - **NOVO** - Guia completo de exportação PDF
- [SETUP_GUIDE.md](SETUP_GUIDE.md) - Guia de instalação
- [ARCHITECTURE.md](ARCHITECTURE.md) - Arquitetura e design do sistema
- [db.sql](db.sql) - Schema MySQL (16 tabelas)

## ✨ Novas Funcionalidades

### 📄 Exportação para PDF
O sistema agora suporta exportação completa de registos para PDF profissional:
- **Layout Formatado**: Segue o padrão do documento "Registos de Enfermagem - TDC.pdf"
- **Conteúdo Completo**: Todas as seções (ABCDE, monitorização, terapêutica, etc.)
- **Pronto para Impressão**: CSS otimizado com espaço para assinaturas
- **Fácil de Usar**: Botão verde "📄 Exportar PDF" na página de visualização

Para instruções detalhadas, consulte [EXPORT_PDF_GUIDE.md](EXPORT_PDF_GUIDE.md)

## 🗄️ Schema - 16 Tabelas

| Tabela | Descrição |
|--------|-----------|
| tdc_records | Registo principal (motivo, serviços, horas, score) |
| tdc_airway | Avaliação A - Via Aérea |
| tdc_ventilation | Avaliação B - Ventilação |
| tdc_circulation | Avaliação C - Circulação |
| tdc_neurological | Avaliação D - Neurológico |
| tdc_exposure | Avaliação E - Exposição |
| tdc_monitoring | Monitorização de sinais vitais (múltiplas entradas) |
| tdc_perfusions | Perfusões IV (múltiplas entradas) |
| tdc_farmacos | Fármacos administrados (múltiplas entradas) |
| tdc_intercurrencies | Intercorrências durante transporte |
| tdc_team | Responsáveis (enfermeiro, médico) |
| equipa_tdc | Referência de equipas |
| farmacos_tdc | Referência de fármacos |
| intervencoes_tdc | Referência de intervenções |
| estado_atual_doente | Estados de saúde predefinidos |
| users | Utilizadores do sistema |

*Todas as tabelas de detalhe usam FK: `id_tdc` → `tdc_records(id_tdc)` com `ON DELETE CASCADE`*
*Suporte a múltiplas entradas para monitorização, perfusões e fármacos*

## 🔐 Segurança

✅ Prepared statements (mysqli) contra SQL injection  
✅ HTML escaping com função `esc()`  
✅ User_id validation (isolamento de dados)  
✅ Validação de acesso aos registos (apenas ver próprios registos)  
✅ Sem dependências de frameworks externos  

## 📝 Notas de Desenvolvimento

- **Sem Autenticação Obrigatória**: `user_id` hardcoded como 1 para simplificar
- **Prepared Statements**: Todos os queries usam parameterização
- **Múltiplas Entradas**: Arrays de inputs para monitorização, perfusões, fármacos
- **JavaScript DOM**: Funções para adicionar/remover linhas dinamicamente
- **CSS de Impressão**: Otimizado para PDF e papel
- **Extensibilidade**: Schema permite adicionar novos campos facilmente

## 🐛 Troubleshooting

| Problema | Solução |
|----------|---------|
| "Unknown database 'tdc_enfermagem'" | Importar db.sql em MySQL |
| Página em branco | Verificar config.php e credenciais MySQL |
| Campos não aparecem | Verifique browser console (F12) para erros JS |
| PDF não exporta | Certifique-se que o registo foi salvo completamente |
| Múltiplas linhas não salvam | Confirme que preenche pelo menos um campo por linha |

## 📞 Suporte

Para dúvidas ou problemas:
1. Consulte a documentação (SETUP_GUIDE.md, EXPORT_PDF_GUIDE.md)
2. Verifique o browser console (F12 → Console)
3. Verifique logs do MySQL em XAMPP

---

**Versão**: 2.0 (Sistema TDC Completo com Exportação PDF)  
**Stack**: PHP 7.4+, MySQL 5.7+, HTML5, CSS3, JavaScript  
**Última atualização**: Dezembro 2025  
**Status**: ✅ Funcional e Pronto para Produção