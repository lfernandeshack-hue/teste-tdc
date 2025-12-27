<?php
require 'config.php';
$uid = 1; // user_id padrão (sem autenticação)
$err = '';
$tdc = null;

// carregar ficha existente
if (isset($_GET['id'])){
  $id = (int)$_GET['id'];
  $res = $mysqli->query('SELECT * FROM transporte_doente_critico WHERE id_tdc='.$id.' AND created_by='.$uid.' LIMIT 1');
  $tdc = $res->fetch_assoc();
  if (!$tdc) { header('Location: tdc_list.php'); exit; }
}

// salvar ficha
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  $id = (int)($_POST['id'] ?? 0);
  $ficha_numero = $_POST['ficha_numero'] ?? '';
  $data_ficha = $_POST['data_ficha'] ?? null;
  $servico = $_POST['servico'] ?? '';
  $medico_responsavel = $_POST['medico_responsavel'] ?? '';
  $hora_contacto = $_POST['hora_contacto'] ?? null;
  $destino = $_POST['destino'] ?? '';
  $medico_destino = $_POST['medico_destino'] ?? '';
  $diagnostico = $_POST['diagnostico'] ?? '';
  $score_tdc = intval($_POST['score_tdc'] ?? 0);
  $gcs = intval($_POST['gcs'] ?? 0);
  $notas_flag = isset($_POST['notas_enfermagem']) ? 1 : 0;
  $historia_flag = isset($_POST['historia_clinica']) ? 1 : 0;
  
  if ($id > 0){
    $stmt = $mysqli->prepare('UPDATE transporte_doente_critico SET ficha_numero=?, `data`=?, servico=?, medico_responsavel=?, hora_contacto=?, destino=?, medico_destino=?, diagnostico=?, score_tdc=?, gcs=?, notas_enfermagem=?, historia_clinica=? WHERE id_tdc=? AND created_by=?');
    $stmt->bind_param('ssssssssiisiii', $ficha_numero, $data_ficha, $servico, $medico_responsavel, $hora_contacto, $destino, $medico_destino, $diagnostico, $score_tdc, $gcs, $notas_flag, $historia_flag, $id, $uid);
    $stmt->execute();
  } else {
    $stmt = $mysqli->prepare('INSERT INTO transporte_doente_critico (created_by,ficha_numero,`data`,servico,medico_responsavel,hora_contacto,destino,medico_destino,diagnostico,score_tdc,gcs,notas_enfermagem, historia_clinica) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)');
    $stmt->bind_param('issssssssiiii', $uid, $ficha_numero, $data_ficha, $servico, $medico_responsavel, $hora_contacto, $destino, $medico_destino, $diagnostico, $score_tdc, $gcs, $notas_flag, $historia_flag);
    $stmt->execute();
  }
  
  header('Location: tdc_list.php');
  exit;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $tdc ? 'Editar' : 'Nova'; ?> Ficha TDC</title>
<link rel="stylesheet" href="styles.css">
<style>
.form-group { margin-bottom: 15px; }
.form-group label { display: block; font-weight: bold; margin-bottom: 5px; }
.form-group input[type=text], 
.form-group input[type=date],
.form-group input[type=time],
.form-group input[type=number],
.form-group textarea { width: 100%; padding: 8px; border: 1px solid #ccc; box-sizing: border-box; }
.form-group textarea { min-height: 100px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
.btn-group { margin-top: 20px; }
.btn-group button { padding: 10px 20px; margin-right: 10px; background: #007bff; color: white; border: none; border-radius: 3px; cursor: pointer; }
.btn-group button:hover { background: #0056b3; }
</style>
</head>
<body>
<div class="container">
  <h1><?php echo $tdc ? 'Editar' : 'Nova'; ?> Ficha TDC</h1>
  
  <form method="post">
    <?php if($tdc): ?><input type="hidden" name="id" value="<?php echo $tdc['id']; ?>"><?php endif; ?>
    
    <h3>Informações Gerais</h3>
    <div class="form-row">
      <div class="form-group">
        <label>Ficha Nº</label>
        <input type="text" name="ficha_numero" value="<?php echo esc($tdc['ficha_numero'] ?? ''); ?>">
      </div>
      <div class="form-group">
        <label>Data</label>
        <input type="date" name="data_ficha" value="<?php echo $tdc['data_ficha'] ?? ''; ?>">
      </div>
    </div>
    
    <div class="form-row">
      <div class="form-group">
        <label>Serviço (Origem)</label>
        <input type="text" name="servico" value="<?php echo esc($tdc['servico'] ?? ''); ?>">
      </div>
      <div class="form-group">
        <label>Médico (Origem)</label>
        <input type="text" name="medico_servico" value="<?php echo esc($tdc['medico_servico'] ?? ''); ?>">
      </div>
    </div>
    
    <div class="form-row">
      <div class="form-group">
        <label>Destino</label>
        <input type="text" name="destino" value="<?php echo esc($tdc['destino'] ?? ''); ?>">
      </div>
      <div class="form-group">
        <label>Hora Contacto</label>
        <input type="time" name="hora_contacto" value="<?php echo $tdc['hora_contacto'] ?? ''; ?>">
      </div>
    </div>
    
    <h3>Avaliação</h3>
    <div class="form-row">
      <div class="form-group">
        <label>Score TDC</label>
        <input type="number" name="score_tdc" min="0" max="100" value="<?php echo $tdc['score_tdc'] ?? ''; ?>">
      </div>
      <div class="form-group">
        <label>GCS (Glasgow Coma Scale)</label>
        <input type="number" name="gcs" min="3" max="15" value="<?php echo $tdc['gcs'] ?? ''; ?>">
      </div>
    </div>
    
    <div class="form-group">
      <label>Diagnóstico</label>
      <textarea name="diagnostico"><?php echo esc($tdc['diagnostico'] ?? ''); ?></textarea>
    </div>
    
    <div class="form-group">
      <label>Notas de Enfermagem</label>
      <textarea name="notas_enfermagem"><?php echo esc($tdc['notas_enfermagem'] ?? ''); ?></textarea>
    </div>
    
    <div class="btn-group">
      <button type="submit"><?php echo $tdc ? 'Atualizar' : 'Criar'; ?></button>
      <a href="tdc_list.php" style="display:inline-block; padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 3px;">Cancelar</a>
    </div>
  </form>
</div>
</body>
</html>