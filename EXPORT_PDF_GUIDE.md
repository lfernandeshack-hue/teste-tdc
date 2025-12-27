# Guia de Exportação para PDF

## 📄 Funcionalidade de Exportação

O sistema TDC agora inclui uma funcionalidade completa de exportação de registos para PDF, seguindo o layout e estrutura do documento "Registos de Enfermagem - TDC.pdf" original.

## ✨ Características

- **Exportação Formatada**: O PDF segue o layout profissional do documento original
- **Conteúdo Completo**: Inclui todas as seções do formulário:
  - Informações Administrativas
  - Avaliação ABCDE
  - Monitorização de Sinais Vitais
  - Terapêutica (Perfusões e Fármacos)
  - Intercorrências
  - Responsáveis pelo Transporte
  - Antecedentes, Alergias e Medicação Relevante
- **Impressão Otimizada**: CSS específico para impressão garante boa visualização em PDF
- **Assinaturas**: Espaços reservados para assinatura de enfermeiro e médico

## 🖨️ Como Usar

### Opção 1: Desde a Página de Visualização
1. Abra a página de visualização de um registo (`tdc_view_novo.php?id=X`)
2. Clique no botão **📄 Exportar PDF** (botão verde)
3. Isso abrirá uma nova janela com o documento formatado
4. Use o botão **🖨️ Imprimir / Exportar para PDF** para:
   - **Imprimir** para papel
   - **Exportar como PDF**: Selecione "Guardar como PDF" na janela de impressão

### Opção 2: Acesso Direto
Aceda diretamente à URL de exportação:
```
http://localhost/visualtdc/tdc_export_pdf.php?id={ID_DO_REGISTO}
```
Exemplo:
```
http://localhost/visualtdc/tdc_export_pdf.php?id=1
```

## 📋 Conteúdo do PDF

O PDF inclui automaticamente:

### 1. Cabeçalho
- Título: "REGISTO DE ENFERMAGEM - TRANSPORTE DE DOENTE CRÍTICO (TDC)"
- Número do registo
- Data

### 2. Informações Administrativas
- Motivo do Transporte
- Serviço de Destino
- Horas (Ativação, Saída ULSCB, Chegada SD)
- Score TDC

### 3. Avaliação ABCDE
Apresentada em 5 colunas visuais:
- **A (Airway)**: Status de via aérea
- **B (Breathing)**: Tipo de ventilação, FR
- **C (Circulation)**: PA, FC
- **D (Disability)**: Estado de consciência, pupilas
- **E (Exposure)**: Temperatura, lesões

### 4. Monitorização de Sinais Vitais
Tabela com todos os registos de monitorização:
- Hora, Temperatura, FC, PA, FR, SpO₂, Glicemia

### 5. Terapêutica
- **Perfusões Intravenosas**: Fármaco, Concentração, Velocidade, Via, Observações
- **Fármacos**: Nome, Dosagem, Via, Hora, Observações

### 6. Intercorrências
Tabela com eventos adversos registados:
- Hora, Evento, Descrição, Ação Tomada

### 7. Antecedentes e Informações Clínicas
- Antecedentes Pessoais
- Alergias
- Medicação Relevante

### 8. Responsáveis
- Enfermeiro
- Médico

### 9. Assinaturas
Espaço reservado para assinatura de:
- Enfermeiro
- Médico

## 🎨 Personalização do Layout

O arquivo `tdc_export_pdf.php` contém CSS customizável para ajustar:

### Cores
```css
background: #003366; /* Azul principal */
```

### Dimensões
```css
max-width: 210mm; /* Largura A4 */
height: 297mm;   /* Altura A4 */
```

### Fontes
```css
font-family: 'Arial', sans-serif;
```

Para modificar estilos, edite a seção `<style>` no arquivo `tdc_export_pdf.php`.

## 🐛 Resolução de Problemas

### O PDF não mostra dados
- Verifique se o ID do registo existe
- Confirme que todos os campos foram preenchidos no formulário

### Formatação incorreta em PDF
- Use o navegador Chrome ou Firefox para melhores resultados
- Nas configurações de impressão, desative "Cabeçalhos e rodapés"

### Campos em branco
- Campos vazios não são exibidos (exceto nas tabelas de monitorização)
- Isso é intencional para manter o PDF limpo

## 💾 Guardar o PDF

Para guardar permanentemente o PDF:

1. Abra a página de exportação
2. Use **Ctrl+P** ou o botão **🖨️ Imprimir / Exportar para PDF**
3. Na janela de impressão:
   - Selecione "Guardar como PDF" como destino
   - Escolha a localização
   - Clique "Guardar"

## 🔐 Segurança

- A exportação usa os mesmos dados do banco de dados
- Apenas registos do utilizador atual podem ser exportados
- Nenhum dado é armazenado no servidor após exportação

## 📝 Notas Técnicas

- Arquivo responsável: `tdc_export_pdf.php`
- Métodos: GET (recebe `id` do registo)
- Segurança: Usa prepared statements e validação de ID
- Impressão: CSS com media query `@media print` para otimizar saída

---

**Última Atualização**: Dezembro 2025
