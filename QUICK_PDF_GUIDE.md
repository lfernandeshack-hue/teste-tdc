# 🎯 GUIA RÁPIDO - Exportação PDF

## ✨ O Que de Novo?

Agora você pode **exportar qualquer registo TDC para PDF** profissional com um único clique!

---

## 🚀 Como Usar (3 Passos)

### 1️⃣ Aceder à Lista de Registos
```
http://localhost/visualtdc/tdc_list_novo.php
```
Você verá uma tabela com todos os seus registos.

### 2️⃣ Clicar em "📄 PDF" ou "👁️ Ver"
- **Opção Rápida**: Clique no botão amarelo **"📄 PDF"** direto da lista
- **Opção Completa**: Clique em **"👁️ Ver"** para visualizar o registo completo, depois clique em **"📄 Exportar PDF"**

### 3️⃣ Guardar o PDF
Uma janela abre com o documento formatado:
1. Clique no botão **"🖨️ Imprimir / Exportar para PDF"**
2. Na janela de impressão, selecione **"Guardar como PDF"** no dropdown "Destino"
3. Escolha onde guardar e clique **"Guardar"**

---

## 📋 O PDF Contém

✅ Motivo do transporte  
✅ Serviço de destino  
✅ Avaliação ABCDE (5 letras)  
✅ Sinais vitais (temperatura, FC, PA, etc.)  
✅ Medicações (perfusões e fármacos)  
✅ Intercorrências  
✅ Responsáveis (enfermeiro e médico)  
✅ Antecedentes e alergias  
✅ Espaço para assinatura  

---

## 💡 Dicas

### Impressão em Papel
1. Na janela de impressão, selecione a impressora
2. Desative "Cabeçalhos e rodapés"
3. Clique "Imprimir"

### Salvar Múltiplos PDFs
1. Vá à lista: `tdc_list_novo.php`
2. Clique em "📄 PDF" para cada registo
3. Cada um abre numa nova janela
4. Guarde cada um com um nome diferente

### Enviar por Email
1. Guarde o PDF no seu computador
2. Anexe o PDF ao seu email
3. Envie normalmente

---

## 🎨 Personalização

O PDF é formatado em A4 (21cm × 29.7cm) e pronto para imprimir.

Se quiser mudar as cores ou o layout, contacte um programador para editar:
- Arquivo: `tdc_export_pdf.php`
- Seção: `<style>...</style>` (linhas 80-200)

---

## ❓ FAQ

**P: O PDF sai em branco?**  
R: Certifique-se que preencheu os campos no formulário antes de exportar.

**P: Posso editar o PDF?**  
R: O PDF gerado é apenas para leitura e impressão. Para editar dados, volte ao formulário.

**P: Funciona em telemóvel?**  
R: Sim, mas é melhor usar um computador para imprimir.

**P: Posso guardar o PDF automaticamente?**  
R: Atualmente é manual. Contacte um programador para automatizar.

---

## 🔗 Links Úteis

- **Dashboard**: [http://localhost/visualtdc/dashboard.php](http://localhost/visualtdc/dashboard.php)
- **Nova Ficha**: [http://localhost/visualtdc/tdc_form_novo.php](http://localhost/visualtdc/tdc_form_novo.php)
- **Lista Completa**: [http://localhost/visualtdc/tdc_list_novo.php](http://localhost/visualtdc/tdc_list_novo.php)
- **Guia Completo**: [EXPORT_PDF_GUIDE.md](EXPORT_PDF_GUIDE.md)

---

## 🆘 Precisa de Ajuda?

1. Leia [EXPORT_PDF_GUIDE.md](EXPORT_PDF_GUIDE.md) para guia detalhado
2. Verifique o browser console (F12 → Console) para erros
3. Contacte administrador se tiver problemas técnicos

---

**Versão**: 1.0  
**Data**: Dezembro 2025  
**Status**: ✅ Pronto para Usar
