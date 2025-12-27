<?php
require 'config.php';
$uid = 1; // user_id padrão (sem autenticação)

// carregar ficha
if (!isset($_GET['id'])){
  header('Location: tdc_list.php');
  exit;
}

$id = (int)$_GET['id'];
$res = $mysqli->query('SELECT * FROM transporte_doente_critico WHERE id_tdc='.$id.' AND created_by='.$uid.' LIMIT 1');
$tdc = $res->fetch_assoc();

if (!$tdc) {
  header('Location: tdc_list.php');
  exit;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Ficha TDC #<?php echo $id; ?></title>
<link rel="stylesheet" href="styles.css">
<style>
.view-section { margin-bottom: 25px; }
.view-section h3 { background: #007bff; color: white; padding: 10px; margin-top: 0; }
.view-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 10px; }
.view-field { padding: 10px; background: #f9f9f9; border: 1px solid #ddd; }
.view-label { font-weight: bold; color: #555; font-size: 12px; }
.view-value { margin-top: 3px; color: #222; }
</style>
</head>
<body>
<div class="container">
  <h1>Ficha TDC #<?php echo $id; ?></h1>
  <p>
    <a href="tdc_list.php">Voltar</a> | 
    <a href="tdc_form.php?id=<?php echo $id; ?>">Editar</a> | 
    <a href="tdc_delete.php?id=<?php echo $id; ?>" onclick="return confirm('Remover ficha?')">Remover</a> | 
    <a href="javascript:window.print()">Imprimir</a>
  </p>
  
  <div class="view-section">
    <h3>Informações Gerais</h3>
    <div class="view-row">
      <div class="view-field">
        <div class="view-label">Ficha Nº</div>
        <div class="view-value"><?php echo esc($tdc['ficha_numero'] ?? 'N/A'); ?></div>
      </div>
      <div class="view-field">
        <div class="view-label">Data</div>
        <div class="view-value"><?php echo $tdc['data'] ?? 'N/A'; ?></div>
      </div>
    </div>
    <div class="view-row">
      <div class="view-field">
        <div class="view-label">Serviço (Origem)</div>
        <div class="view-value"><?php echo esc($tdc['servico'] ?? 'N/A'); ?></div>
      </div>
      <div class="view-field">
        <div class="view-label">Médico (Origem)</div>
        <div class="view-value"><?php echo esc($tdc['medico_responsavel'] ?? 'N/A'); ?></div>
      </div>
    </div>
    <div class="view-row">
      <div class="view-field">
        <div class="view-label">Destino</div>
        <div class="view-value"><?php echo esc($tdc['destino'] ?? 'N/A'); ?></div>
      </div>
      <div class="view-field">
        <div class="view-label">Hora Contacto</div>
        <div class="view-value"><?php echo $tdc['hora_contacto'] ?? 'N/A'; ?></div>
      </div>
    </div>
  </div>
  
  <div class="view-section">
    <h3>Avaliação Clínica</h3>
    <div class="view-row">
      <div class="view-field">
        <div class="view-label">Score TDC</div>
        <div class="view-value"><?php echo $tdc['score_tdc'] ?? '-'; ?></div>
      </div>
      <div class="view-field">
        <div class="view-label">GCS (Glasgow Coma Scale)</div>
        <div class="view-value"><?php echo $tdc['gcs'] ?? '-'; ?></div>
      </div>
    </div>
  </div>
  
  <div class="view-section">
    <h3>Diagnóstico</h3>
    <div class="view-field" style="grid-column: 1/-1;">
      <div class="view-value"><?php echo nl2br(esc($tdc['diagnostico'] ?? 'N/A')); ?></div>
    </div>
  </div>
  
  <div class="view-section">
    <h3>Notas de Enfermagem</h3>
    <div class="view-field" style="grid-column: 1/-1;">
      <div class="view-value"><?php echo ($tdc['notas_enfermagem'] ? 'Sim' : 'Não'); ?></div>
    </div>
  </div>
  
  <p style="color: #999; font-size: 12px;">
    Criado em: <?php echo $tdc['created_at'] ?? '-'; ?>
  </p>
</div>
</body>
</html>