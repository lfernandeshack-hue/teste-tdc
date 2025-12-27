<?php
require 'config.php';
$uid = 1; // user_id padrão (sem autenticação)
$err = '';
$success = '';
$id_tdc = isset($_GET['id']) ? (int)$_GET['id'] : null;
$is_edit = false;
$tdc = null;

// Carregar registo existente se edit
if ($id_tdc) {
  $is_edit = true;
  $res = $mysqli->query("SELECT * FROM tdc_records WHERE id_tdc=$id_tdc AND created_by=$uid");
  if ($tdc = $res->fetch_assoc()) {
    // Carregar dados relacionados
    $airway = $mysqli->query("SELECT * FROM tdc_airway WHERE id_tdc=$id_tdc")->fetch_assoc();
    $ventilation = $mysqli->query("SELECT * FROM tdc_ventilation WHERE id_tdc=$id_tdc")->fetch_assoc();
    $circulation = $mysqli->query("SELECT * FROM tdc_circulation WHERE id_tdc=$id_tdc")->fetch_assoc();
    $neurological = $mysqli->query("SELECT * FROM tdc_neurological WHERE id_tdc=$id_tdc")->fetch_assoc();
    $exposure = $mysqli->query("SELECT * FROM tdc_exposure WHERE id_tdc=$id_tdc")->fetch_assoc();
    $monitoring = $mysqli->query("SELECT * FROM tdc_monitoring WHERE id_tdc=$id_tdc ORDER BY sequencia")->fetch_all(MYSQLI_ASSOC);
    $perfusions = $mysqli->query("SELECT * FROM tdc_perfusions WHERE id_tdc=$id_tdc ORDER BY sequencia")->fetch_all(MYSQLI_ASSOC);
    $farmacos = $mysqli->query("SELECT * FROM tdc_farmacos WHERE id_tdc=$id_tdc ORDER BY sequencia")->fetch_all(MYSQLI_ASSOC);
    $intercurrencies = $mysqli->query("SELECT * FROM tdc_intercurrencies WHERE id_tdc=$id_tdc ORDER BY sequencia")->fetch_all(MYSQLI_ASSOC);
    $team = $mysqli->query("SELECT * FROM tdc_team WHERE id_tdc=$id_tdc")->fetch_assoc();
  } else {
    header('Location: tdc_list.php');
    exit;
  }
}

// GUARDAR FORMULÁRIO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Dados Administrativos
  $motivo = $_POST['motivo_transporte'] ?? '';
  $servico = $_POST['servico_destino'] ?? '';
  $h_ativa = $_POST['hora_ativacao'] ?? '';
  $h_saida = $_POST['hora_saida_ulscb'] ?? '';
  $h_chegada_sd = $_POST['hora_chegada_sd'] ?? '';
  $h_chegada_ulscb = $_POST['hora_chegada_ulscb'] ?? '';
  $antecedentes = $_POST['antecedentes_pessoais'] ?? '';
  $alergias = $_POST['alergias'] ?? '';
  $medicacao = $_POST['medicacao_relevante'] ?? '';
  $refeicao = $_POST['ultima_refeicao'] ?? '';
  $score = $_POST['score_tdc'] ?? null;

  if (!$motivo || !$servico) {
    $err = 'Motivo do transporte e serviço de destino são obrigatórios.';
  } else {
    // Inserir ou atualizar tdc_records
    if ($id_tdc) {
      $stmt = $mysqli->prepare('UPDATE tdc_records SET motivo_transporte=?, servico_destino=?, hora_ativacao=?, hora_saida_ulscb=?, hora_chegada_sd=?, hora_chegada_ulscb=?, antecedentes_pessoais=?, alergias=?, medicacao_relevante=?, ultima_refeicao=?, score_tdc=? WHERE id_tdc=?');
      $stmt->bind_param('ssssssssssii', $motivo, $servico, $h_ativa, $h_saida, $h_chegada_sd, $h_chegada_ulscb, $antecedentes, $alergias, $medicacao, $refeicao, $score, $id_tdc);
      $stmt->execute();
    } else {
      $stmt = $mysqli->prepare('INSERT INTO tdc_records (created_by, motivo_transporte, servico_destino, hora_ativacao, hora_saida_ulscb, hora_chegada_sd, hora_chegada_ulscb, antecedentes_pessoais, alergias, medicacao_relevante, ultima_refeicao, score_tdc) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
      $stmt->bind_param('issssssssssi', $uid, $motivo, $servico, $h_ativa, $h_saida, $h_chegada_sd, $h_chegada_ulscb, $antecedentes, $alergias, $medicacao, $refeicao, $score);
      $stmt->execute();
      $id_tdc = $mysqli->insert_id;
    }

    // Via Aérea
    $va_patente = isset($_POST['va_patente']) ? 1 : 0;
    $secrecoes = $_POST['secrecoes'] ?? '';
    $adj_tipo = $_POST['adjuvante_va_tipo'] ?? '';
    $adj_num = $_POST['adjuvante_va_numero'] ?? '';
    $adj_data = $_POST['adjuvante_va_data'] ?? null;
    $def_tipo = $_POST['va_definitiva_tipo'] ?? '';
    $def_num = $_POST['va_definitiva_numero'] ?? '';
    $def_nivel = $_POST['va_definitiva_nivel'] ?? '';
    $def_data = $_POST['va_definitiva_data'] ?? null;

    if ($is_edit) {
      $stmt = $mysqli->prepare('UPDATE tdc_airway SET va_patente=?, secrecoes=?, adjuvante_va_tipo=?, adjuvante_va_numero=?, adjuvante_va_data=?, va_definitiva_tipo=?, va_definitiva_numero=?, va_definitiva_nivel=?, va_definitiva_data=? WHERE id_tdc=?');
      $stmt->bind_param('issssssssi', $va_patente, $secrecoes, $adj_tipo, $adj_num, $adj_data, $def_tipo, $def_num, $def_nivel, $def_data, $id_tdc);
      $stmt->execute();
    } else {
      $stmt = $mysqli->prepare('INSERT INTO tdc_airway (id_tdc, va_patente, secrecoes, adjuvante_va_tipo, adjuvante_va_numero, adjuvante_va_data, va_definitiva_tipo, va_definitiva_numero, va_definitiva_nivel, va_definitiva_data) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
      $stmt->bind_param('iissssssss', $id_tdc, $va_patente, $secrecoes, $adj_tipo, $adj_num, $adj_data, $def_tipo, $def_num, $def_nivel, $def_data);
      $stmt->execute();
    }

    // Ventilação
    $vent_esp = isset($_POST['ventilacao_espontanea']) ? 1 : 0;
    $o2_lit = $_POST['o2_litros'] ?? null;
    $tipo_vent = $_POST['tipo_vent_suplementar'] ?? '';
    $vni_ipap = $_POST['vni_ipap'] ?? null;
    $vni_epap = $_POST['vni_epap'] ?? null;
    $vni_fr = $_POST['vni_fr'] ?? null;
    $vni_fio2 = $_POST['vni_fio2'] ?? null;
    $vmi_tipo = $_POST['vmi_tipo'] ?? '';
    $vmi_vc = $_POST['vmi_vc_pc_pa'] ?? null;
    $vmi_fio2 = $_POST['vmi_fio2'] ?? null;
    $vmi_peep = $_POST['vmi_peep'] ?? null;
    $vmi_fr = $_POST['vmi_fr'] ?? null;
    $drenagem = isset($_POST['drenagem_toracica']) ? 1 : 0;

    if ($is_edit) {
      $stmt = $mysqli->prepare('UPDATE tdc_ventilation SET ventilacao_espontanea=?, o2_litros=?, tipo_vent_suplementar=?, vni_ipap=?, vni_epap=?, vni_fr=?, vni_fio2=?, vmi_tipo=?, vmi_vc_pc_pa=?, vmi_fio2=?, vmi_peep=?, vmi_fr=?, drenagem_toracica=? WHERE id_tdc=?');
      $stmt->bind_param('idsssiiidddiii', $vent_esp, $o2_lit, $tipo_vent, $vni_ipap, $vni_epap, $vni_fr, $vni_fio2, $vmi_tipo, $vmi_vc, $vmi_fio2, $vmi_peep, $vmi_fr, $drenagem, $id_tdc);
      $stmt->execute();
    } else {
      $stmt = $mysqli->prepare('INSERT INTO tdc_ventilation (id_tdc, ventilacao_espontanea, o2_litros, tipo_vent_suplementar, vni_ipap, vni_epap, vni_fr, vni_fio2, vmi_tipo, vmi_vc_pc_pa, vmi_fio2, vmi_peep, vmi_fr, drenagem_toracica) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
      $stmt->bind_param('idsssiiidddiii', $id_tdc, $vent_esp, $o2_lit, $tipo_vent, $vni_ipap, $vni_epap, $vni_fr, $vni_fio2, $vmi_tipo, $vmi_vc, $vmi_fio2, $vmi_peep, $vmi_fr, $drenagem);
      $stmt->execute();
    }

    // Circulação
    $la_disp = isset($_POST['dispositivo_la']) ? 1 : 0;
    $la_local = $_POST['la_local'] ?? '';
    $la_data = $_POST['la_data'] ?? null;
    $cvc = isset($_POST['cvc']) ? 1 : 0;
    $cvc_lumens = $_POST['cvc_lumens'] ?? null;
    $cvc_local = $_POST['cvc_local'] ?? '';
    $cvc_data = $_POST['cvc_data'] ?? null;
    $cvp_val = $_POST['cvp_valor'] ?? null;
    $cvp_unit = $_POST['cvp_unidade'] ?? '';
    $cvp_loc = $_POST['cvp_locais'] ?? '';
    $hemorragia = isset($_POST['hemorragia_ativa']) ? 1 : 0;
    $transfusao = isset($_POST['suporte_transfusional']) ? 1 : 0;
    $sonda_v = isset($_POST['sonda_vesical']) ? 1 : 0;
    $sonda_v_num = $_POST['sonda_vesical_numero'] ?? '';
    $sonda_v_data = $_POST['sonda_vesical_data'] ?? null;
    $lavagem_v = isset($_POST['lavagem_vesical']) ? 1 : 0;
    $lavagem_v_ml = $_POST['lavagem_vesical_ml_h'] ?? null;

    if ($is_edit) {
      $stmt = $mysqli->prepare('UPDATE tdc_circulation SET dispositivo_la=?, la_local=?, la_data=?, cvc=?, cvc_lumens=?, cvc_local=?, cvc_data=?, cvp_valor=?, cvp_unidade=?, cvp_locais=?, hemorragia_ativa=?, suporte_transfusional=?, sonda_vesical=?, sonda_vesical_numero=?, sonda_vesical_data=?, lavagem_vesical=?, lavagem_vesical_ml_h=? WHERE id_tdc=?');
      $stmt->bind_param('issidssdssiiissidi', $la_disp, $la_local, $la_data, $cvc, $cvc_lumens, $cvc_local, $cvc_data, $cvp_val, $cvp_unit, $cvp_loc, $hemorragia, $transfusao, $sonda_v, $sonda_v_num, $sonda_v_data, $lavagem_v, $lavagem_v_ml, $id_tdc);
      $stmt->execute();
    } else {
      $stmt = $mysqli->prepare('INSERT INTO tdc_circulation (id_tdc, dispositivo_la, la_local, la_data, cvc, cvc_lumens, cvc_local, cvc_data, cvp_valor, cvp_unidade, cvp_locais, hemorragia_ativa, suporte_transfusional, sonda_vesical, sonda_vesical_numero, sonda_vesical_data, lavagem_vesical, lavagem_vesical_ml_h) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
      $stmt->bind_param('iissidssdssiiissid', $id_tdc, $la_disp, $la_local, $la_data, $cvc, $cvc_lumens, $cvc_local, $cvc_data, $cvp_val, $cvp_unit, $cvp_loc, $hemorragia, $transfusao, $sonda_v, $sonda_v_num, $sonda_v_data, $lavagem_v, $lavagem_v_ml);
      $stmt->execute();
    }

    // Neurológico
    $ecg = $_POST['ecg_pontos'] ?? null;
    $rass = $_POST['rass_pontos'] ?? null;
    $eva = $_POST['eva_pontos'] ?? null;
    $bps = $_POST['bps_pontos'] ?? null;
    $glicemia = $_POST['glicemia_capilar'] ?? null;
    $sng_pres = isset($_POST['sng_presente']) ? 1 : 0;
    $sng_nivel = $_POST['sng_nivel'] ?? '';
    $sng_data = $_POST['sng_data'] ?? null;
    $sog_pres = isset($_POST['sog_presente']) ? 1 : 0;
    $sog_nivel = $_POST['sog_nivel'] ?? '';
    $sog_data = $_POST['sog_data'] ?? null;
    $snj_pres = isset($_POST['snj_presente']) ? 1 : 0;
    $snj_nivel = $_POST['snj_nivel'] ?? '';
    $snj_data = $_POST['snj_data'] ?? null;
    $esvaziamento = $_POST['esvaziamento_gastrico'] ?? '';

    if ($is_edit) {
      $stmt = $mysqli->prepare('UPDATE tdc_neurological SET ecg_pontos=?, rass_pontos=?, eva_pontos=?, bps_pontos=?, glicemia_capilar=?, sng_presente=?, sng_nivel=?, sng_data=?, sog_presente=?, sog_nivel=?, sog_data=?, snj_presente=?, snj_nivel=?, snj_data=?, esvaziamento_gastrico=? WHERE id_tdc=?');
      $stmt->bind_param('iiiiidisisisissi', $ecg, $rass, $eva, $bps, $glicemia, $sng_pres, $sng_nivel, $sng_data, $sog_pres, $sog_nivel, $sog_data, $snj_pres, $snj_nivel, $snj_data, $esvaziamento, $id_tdc);
      $stmt->execute();
    } else {
      $stmt = $mysqli->prepare('INSERT INTO tdc_neurological (id_tdc, ecg_pontos, rass_pontos, eva_pontos, bps_pontos, glicemia_capilar, sng_presente, sng_nivel, sng_data, sog_presente, sog_nivel, sog_data, snj_presente, snj_nivel, snj_data, esvaziamento_gastrico) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
      $stmt->bind_param('iiiiidisisisissi', $id_tdc, $ecg, $rass, $eva, $bps, $glicemia, $sng_pres, $sng_nivel, $sng_data, $sog_pres, $sog_nivel, $sog_data, $snj_pres, $snj_nivel, $snj_data, $esvaziamento);
      $stmt->execute();
    }

    // Exposição
    $temp = $_POST['temperatura'] ?? null;
    $imob = isset($_POST['imobilizacao_cervical']) ? 1 : 0;
    $fraturas = isset($_POST['fraturas']) ? 1 : 0;
    $fraturas_loc = $_POST['fraturas_locais'] ?? '';
    $feridas = isset($_POST['feridas_pensos']) ? 1 : 0;
    $feridas_local = $_POST['feridas_pensos_local'] ?? '';
    $feridas_trat = $_POST['feridas_pensos_tratamento'] ?? '';

    if ($is_edit) {
      $stmt = $mysqli->prepare('UPDATE tdc_exposure SET temperatura=?, imobilizacao_cervical=?, fraturas=?, fraturas_locais=?, feridas_pensos=?, feridas_pensos_local=?, feridas_pensos_tratamento=? WHERE id_tdc=?');
      $stmt->bind_param('diisissi', $temp, $imob, $fraturas, $fraturas_loc, $feridas, $feridas_local, $feridas_trat, $id_tdc);
      $stmt->execute();
    } else {
      $stmt = $mysqli->prepare('INSERT INTO tdc_exposure (id_tdc, temperatura, imobilizacao_cervical, fraturas, fraturas_locais, feridas_pensos, feridas_pensos_local, feridas_pensos_tratamento) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
      $stmt->bind_param('idiisiss', $id_tdc, $temp, $imob, $fraturas, $fraturas_loc, $feridas, $feridas_local, $feridas_trat);
      $stmt->execute();
    }

    // Equipa
    $enfermeiro = $_POST['enfermeiro'] ?? '';
    $medico = $_POST['medico'] ?? '';

    if ($is_edit) {
      $stmt = $mysqli->prepare('UPDATE tdc_team SET enfermeiro=?, medico=? WHERE id_tdc=?');
      $stmt->bind_param('ssi', $enfermeiro, $medico, $id_tdc);
      $stmt->execute();
    } else {
      $stmt = $mysqli->prepare('INSERT INTO tdc_team (id_tdc, enfermeiro, medico) VALUES (?, ?, ?)');
      $stmt->bind_param('iss', $id_tdc, $enfermeiro, $medico);
      $stmt->execute();
    }

    // Perfusões (múltiplas entradas)
    if ($is_edit) {
      $mysqli->query("DELETE FROM tdc_perfusions WHERE id_tdc=$id_tdc");
    }
    
    if (isset($_POST['perfusao_farmaco']) && is_array($_POST['perfusao_farmaco'])) {
      $stmt = $mysqli->prepare('INSERT INTO tdc_perfusions (id_tdc, farmaco, posologia, hora_inicio, taxa_1, taxa_2, taxa_3, taxa_4, sequencia) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
      
      foreach ($_POST['perfusao_farmaco'] as $idx => $farmaco) {
        if (trim($farmaco) === '') continue; // Ignorar linhas vazias
        
        $posologia = $_POST['perfusao_posologia'][$idx] ?? '';
        $hora = $_POST['perfusao_hora_inicio'][$idx] ?? null;
        $taxa1 = $_POST['perfusao_taxa_1'][$idx] ?? null;
        $taxa2 = $_POST['perfusao_taxa_2'][$idx] ?? null;
        $taxa3 = $_POST['perfusao_taxa_3'][$idx] ?? null;
        $taxa4 = $_POST['perfusao_taxa_4'][$idx] ?? null;
        $seq = $idx + 1;
        
        $stmt->bind_param('isssddddi', $id_tdc, $farmaco, $posologia, $hora, $taxa1, $taxa2, $taxa3, $taxa4, $seq);
        $stmt->execute();
      }
    }

    // Fármacos (múltiplas entradas)
    if ($is_edit) {
      $mysqli->query("DELETE FROM tdc_farmacos WHERE id_tdc=$id_tdc");
    }
    
    if (isset($_POST['farmaco_nome']) && is_array($_POST['farmaco_nome'])) {
      $stmt = $mysqli->prepare('INSERT INTO tdc_farmacos (id_tdc, farmaco, hora_administracao, sequencia) VALUES (?, ?, ?, ?)');
      
      foreach ($_POST['farmaco_nome'] as $idx => $farmaco) {
        if (trim($farmaco) === '') continue; // Ignorar linhas vazias
        
        $hora = $_POST['farmaco_hora'][$idx] ?? null;
        $seq = $idx + 1;
        
        $stmt->bind_param('issi', $id_tdc, $farmaco, $hora, $seq);
        $stmt->execute();
      }
    }

    $success = $is_edit ? 'Registo atualizado com sucesso!' : 'Registo criado com sucesso!';
    // Recarregar dados
    if (!$is_edit) {
      $is_edit = true;
      header("Location: tdc_form_novo.php?id=$id_tdc");
      exit;
    }
  }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $is_edit ? 'Editar' : 'Novo'; ?> Registo TDC</title>
<link rel="stylesheet" href="styles.css">
<style>
  .tabs {
    display: flex;
    border-bottom: 2px solid #ddd;
    margin: 20px 0;
    flex-wrap: wrap;
  }
  .tabs button {
    flex: 1;
    padding: 10px;
    background: #f0f0f0;
    border: none;
    cursor: pointer;
    font-size: 14px;
    border-bottom: 3px solid transparent;
    transition: all 0.3s;
  }
  .tabs button.active {
    background: #007bff;
    color: white;
    border-bottom-color: #0056b3;
  }
  .tabs button:hover {
    background: #e0e0e0;
  }
  .tabs button.active:hover {
    background: #0056b3;
  }
  .tab-content {
    display: none;
    animation: fadeIn 0.3s;
  }
  .tab-content.active {
    display: block;
  }
  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }
  .section {
    background: #f9f9f9;
    padding: 15px;
    margin: 10px 0;
    border-radius: 5px;
    border-left: 4px solid #007bff;
  }
  .section h3 {
    margin-top: 0;
    color: #007bff;
  }
  .form-group {
    margin: 15px 0;
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
  }
  .form-group label {
    flex: 1;
    min-width: 250px;
  }
  .form-group input,
  .form-group textarea,
  .form-group select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
  }
  .form-group textarea {
    resize: vertical;
    min-height: 60px;
  }
  .checkbox-group {
    display: flex;
    gap: 10px;
    align-items: center;
  }
  .checkbox-group input[type="checkbox"] {
    width: auto;
  }
  .btn {
    padding: 10px 20px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
  }
  .btn:hover {
    background: #0056b3;
  }
  .btn-danger {
    background: #dc3545;
  }
  .btn-danger:hover {
    background: #c82333;
  }
  .error { color: #dc3545; padding: 10px; background: #f8d7da; border-radius: 4px; margin: 10px 0; }
  .success { color: #155724; padding: 10px; background: #d4edda; border-radius: 4px; margin: 10px 0; }
</style>
</head>
<body>
<div class="container">
  <h1><?php echo $is_edit ? '✏️ Editar Registo TDC' : '➕ Novo Registo TDC'; ?></h1>
  <nav>
    <a href="tdc_list.php">← Voltar à Lista</a>
  </nav>

  <?php if($err): ?><p class="error">❌ <?php echo esc($err); ?></p><?php endif; ?>
  <?php if($success): ?><p class="success">✅ <?php echo esc($success); ?></p><?php endif; ?>

  <form method="post" id="form_tdc">
    <!-- ABAS DE NAVEGAÇÃO -->
    <div class="tabs">
      <button type="button" class="tab-btn active" data-tab="admin">📋 Administrativo</button>
      <button type="button" class="tab-btn" data-tab="abcde">🏥 ABCDE</button>
      <button type="button" class="tab-btn" data-tab="monitoring">📊 Monitorização</button>
      <button type="button" class="tab-btn" data-tab="terapeutica">💊 Terapêutica</button>
      <button type="button" class="tab-btn" data-tab="eventos">⚠️ Eventos</button>
      <button type="button" class="tab-btn" data-tab="equipa">👥 Equipa</button>
    </div>

    <!-- TAB 1: ADMINISTRATIVO -->
    <div id="admin" class="tab-content active">
      <div class="section">
        <h3>Identificação do Transporte</h3>
        <div class="form-group">
          <label>Motivo do Transporte *<br>
            <input type="text" name="motivo_transporte" value="<?php echo isset($tdc) ? esc($tdc['motivo_transporte']) : ''; ?>" required>
          </label>
        </div>
        <div class="form-group">
          <label>Serviço de Destino *<br>
            <input type="text" name="servico_destino" value="<?php echo isset($tdc) ? esc($tdc['servico_destino']) : ''; ?>" required>
          </label>
        </div>
        <div class="form-group">
          <label>Hora de Ativação<br>
            <input type="time" name="hora_ativacao" value="<?php echo isset($tdc) ? esc($tdc['hora_ativacao']) : ''; ?>">
          </label>
          <label>Hora de Saída (ULSCB)<br>
            <input type="time" name="hora_saida_ulscb" value="<?php echo isset($tdc) ? esc($tdc['hora_saida_ulscb']) : ''; ?>">
          </label>
        </div>
        <div class="form-group">
          <label>Hora de Chegada (SD)<br>
            <input type="time" name="hora_chegada_sd" value="<?php echo isset($tdc) ? esc($tdc['hora_chegada_sd']) : ''; ?>">
          </label>
          <label>Hora de Chegada (ULSCB)<br>
            <input type="time" name="hora_chegada_ulscb" value="<?php echo isset($tdc) ? esc($tdc['hora_chegada_ulscb']) : ''; ?>">
          </label>
        </div>
      </div>

      <div class="section">
        <h3>Dados Clínicos do Doente</h3>
        <div class="form-group">
          <label>Antecedentes Pessoais Relevantes<br>
            <textarea name="antecedentes_pessoais"><?php echo isset($tdc) ? esc($tdc['antecedentes_pessoais']) : ''; ?></textarea>
          </label>
        </div>
        <div class="form-group">
          <label>Alergias<br>
            <textarea name="alergias"><?php echo isset($tdc) ? esc($tdc['alergias']) : ''; ?></textarea>
          </label>
        </div>
        <div class="form-group">
          <label>Medicação Relevante<br>
            <textarea name="medicacao_relevante"><?php echo isset($tdc) ? esc($tdc['medicacao_relevante']) : ''; ?></textarea>
          </label>
        </div>
        <div class="form-group">
          <label>Última Refeição<br>
            <input type="text" name="ultima_refeicao" value="<?php echo isset($tdc) ? esc($tdc['ultima_refeicao']) : ''; ?>">
          </label>
          <label>Score TDC<br>
            <input type="number" name="score_tdc" value="<?php echo isset($tdc) ? esc($tdc['score_tdc']) : ''; ?>" min="0">
          </label>
        </div>
      </div>
    </div>

    <!-- TAB 2: ABCDE -->
    <div id="abcde" class="tab-content">
      <!-- A: VIA AÉREA -->
      <div class="section">
        <h3>A - VIA AÉREA</h3>
        <div class="form-group">
          <label class="checkbox-group">
            <input type="checkbox" name="va_patente" <?php echo (isset($airway) && $airway['va_patente']) ? 'checked' : ''; ?>>
            Via Aérea Patente
          </label>
        </div>
        <div class="form-group">
          <label>Secreções<br>
            <textarea name="secrecoes"><?php echo isset($airway) ? esc($airway['secrecoes']) : ''; ?></textarea>
          </label>
        </div>
        <div class="form-group">
          <label>Adjuvante VA - Tipo<br>
            <input type="text" name="adjuvante_va_tipo" value="<?php echo isset($airway) ? esc($airway['adjuvante_va_tipo']) : ''; ?>">
          </label>
          <label>Número<br>
            <input type="text" name="adjuvante_va_numero" value="<?php echo isset($airway) ? esc($airway['adjuvante_va_numero']) : ''; ?>">
          </label>
          <label>Data<br>
            <input type="date" name="adjuvante_va_data" value="<?php echo isset($airway) ? esc($airway['adjuvante_va_data']) : ''; ?>">
          </label>
        </div>
        <div class="form-group">
          <label>VA Definitiva - Tipo<br>
            <input type="text" name="va_definitiva_tipo" value="<?php echo isset($airway) ? esc($airway['va_definitiva_tipo']) : ''; ?>">
          </label>
          <label>Número<br>
            <input type="text" name="va_definitiva_numero" value="<?php echo isset($airway) ? esc($airway['va_definitiva_numero']) : ''; ?>">
          </label>
        </div>
        <div class="form-group">
          <label>Nível<br>
            <input type="text" name="va_definitiva_nivel" value="<?php echo isset($airway) ? esc($airway['va_definitiva_nivel']) : ''; ?>">
          </label>
          <label>Data<br>
            <input type="date" name="va_definitiva_data" value="<?php echo isset($airway) ? esc($airway['va_definitiva_data']) : ''; ?>">
          </label>
        </div>
      </div>

      <!-- B: VENTILAÇÃO -->
      <div class="section">
        <h3>B - VENTILAÇÃO</h3>
        <div class="form-group">
          <label class="checkbox-group">
            <input type="checkbox" name="ventilacao_espontanea" <?php echo (isset($ventilation) && $ventilation['ventilacao_espontanea']) ? 'checked' : ''; ?>>
            Ventilação Espontânea
          </label>
        </div>
        <div class="form-group">
          <label>O2 (L/min)<br>
            <input type="number" name="o2_litros" step="0.1" value="<?php echo isset($ventilation) ? esc($ventilation['o2_litros']) : ''; ?>">
          </label>
          <label>Tipo de Suplementação<br>
            <select name="tipo_vent_suplementar">
              <option value="">Selecione...</option>
              <option value="ON" <?php echo (isset($ventilation) && $ventilation['tipo_vent_suplementar']=='ON') ? 'selected' : ''; ?>>Óculos Nasais (ON)</option>
              <option value="MF" <?php echo (isset($ventilation) && $ventilation['tipo_vent_suplementar']=='MF') ? 'selected' : ''; ?>>Máscara Facial (MF)</option>
              <option value="MV" <?php echo (isset($ventilation) && $ventilation['tipo_vent_suplementar']=='MV') ? 'selected' : ''; ?>>Máscara Venturi (MV)</option>
              <option value="MAD" <?php echo (isset($ventilation) && $ventilation['tipo_vent_suplementar']=='MAD') ? 'selected' : ''; ?>>Máscara Alto Debito (MAD)</option>
              <option value="BIPAP" <?php echo (isset($ventilation) && $ventilation['tipo_vent_suplementar']=='BIPAP') ? 'selected' : ''; ?>>BiPAP</option>
              <option value="CIPAP" <?php echo (isset($ventilation) && $ventilation['tipo_vent_suplementar']=='CIPAP') ? 'selected' : ''; ?>>CIPAP</option>
              <option value="HFNO" <?php echo (isset($ventilation) && $ventilation['tipo_vent_suplementar']=='HFNO') ? 'selected' : ''; ?>>HFNO</option>
            </select>
          </label>
        </div>
        <div class="form-group">
          <label>Parâmetros VNI - IPAP<br>
            <input type="number" name="vni_ipap" step="0.1" value="<?php echo isset($ventilation) ? esc($ventilation['vni_ipap']) : ''; ?>">
          </label>
          <label>EPAP<br>
            <input type="number" name="vni_epap" step="0.1" value="<?php echo isset($ventilation) ? esc($ventilation['vni_epap']) : ''; ?>">
          </label>
          <label>FR<br>
            <input type="number" name="vni_fr" value="<?php echo isset($ventilation) ? esc($ventilation['vni_fr']) : ''; ?>">
          </label>
          <label>FIO2 (%)<br>
            <input type="number" name="vni_fio2" value="<?php echo isset($ventilation) ? esc($ventilation['vni_fio2']) : ''; ?>" min="0" max="100">
          </label>
        </div>
        <div class="form-group">
          <label>VMI - Tipo<br>
            <select name="vmi_tipo">
              <option value="">Selecione...</option>
              <option value="VC" <?php echo (isset($ventilation) && $ventilation['vmi_tipo']=='VC') ? 'selected' : ''; ?>>VC (Volume Controlado)</option>
              <option value="PC" <?php echo (isset($ventilation) && $ventilation['vmi_tipo']=='PC') ? 'selected' : ''; ?>>PC (Pressão Controlada)</option>
              <option value="PA" <?php echo (isset($ventilation) && $ventilation['vmi_tipo']=='PA') ? 'selected' : ''; ?>>PA (Pressão Assistida)</option>
              <option value="SIMV" <?php echo (isset($ventilation) && $ventilation['vmi_tipo']=='SIMV') ? 'selected' : ''; ?>>SIMV</option>
            </select>
          </label>
          <label>VC/PC/PA<br>
            <input type="number" name="vmi_vc_pc_pa" step="0.1" value="<?php echo isset($ventilation) ? esc($ventilation['vmi_vc_pc_pa']) : ''; ?>">
          </label>
          <label>FIO2 (%)<br>
            <input type="number" name="vmi_fio2" value="<?php echo isset($ventilation) ? esc($ventilation['vmi_fio2']) : ''; ?>" min="0" max="100">
          </label>
          <label>PEEP<br>
            <input type="number" name="vmi_peep" step="0.1" value="<?php echo isset($ventilation) ? esc($ventilation['vmi_peep']) : ''; ?>">
          </label>
          <label>FR<br>
            <input type="number" name="vmi_fr" value="<?php echo isset($ventilation) ? esc($ventilation['vmi_fr']) : ''; ?>">
          </label>
        </div>
        <div class="form-group">
          <label class="checkbox-group">
            <input type="checkbox" name="drenagem_toracica" <?php echo (isset($ventilation) && $ventilation['drenagem_toracica']) ? 'checked' : ''; ?>>
            Drenagem Torácica
          </label>
        </div>
      </div>

      <!-- C: CIRCULAÇÃO -->
      <div class="section">
        <h3>C - CIRCULAÇÃO</h3>
        <div class="form-group">
          <label class="checkbox-group">
            <input type="checkbox" name="dispositivo_la" <?php echo (isset($circulation) && $circulation['dispositivo_la']) ? 'checked' : ''; ?>>
            Linha Arterial (LA)
          </label>
          <label>Local<br>
            <input type="text" name="la_local" value="<?php echo isset($circulation) ? esc($circulation['la_local']) : ''; ?>">
          </label>
          <label>Data<br>
            <input type="date" name="la_data" value="<?php echo isset($circulation) ? esc($circulation['la_data']) : ''; ?>">
          </label>
        </div>
        <div class="form-group">
          <label class="checkbox-group">
            <input type="checkbox" name="cvc" <?php echo (isset($circulation) && $circulation['cvc']) ? 'checked' : ''; ?>>
            Catéter Venoso Central (CVC)
          </label>
          <label>Lúmens<br>
            <input type="number" name="cvc_lumens" value="<?php echo isset($circulation) ? esc($circulation['cvc_lumens']) : ''; ?>">
          </label>
          <label>Local<br>
            <input type="text" name="cvc_local" value="<?php echo isset($circulation) ? esc($circulation['cvc_local']) : ''; ?>">
          </label>
          <label>Data<br>
            <input type="date" name="cvc_data" value="<?php echo isset($circulation) ? esc($circulation['cvc_data']) : ''; ?>">
          </label>
        </div>
        <div class="form-group">
          <label>CVP (mmHg)<br>
            <input type="number" name="cvp_valor" step="0.1" value="<?php echo isset($circulation) ? esc($circulation['cvp_valor']) : ''; ?>">
          </label>
          <label>Unidade<br>
            <input type="text" name="cvp_unidade" value="<?php echo isset($circulation) ? esc($circulation['cvp_unidade']) : ''; ?>">
          </label>
          <label>Locais<br>
            <input type="text" name="cvp_locais" value="<?php echo isset($circulation) ? esc($circulation['cvp_locais']) : ''; ?>">
          </label>
        </div>
        <div class="form-group">
          <label class="checkbox-group">
            <input type="checkbox" name="hemorragia_ativa" <?php echo (isset($circulation) && $circulation['hemorragia_ativa']) ? 'checked' : ''; ?>>
            Hemorragia Ativa
          </label>
          <label class="checkbox-group">
            <input type="checkbox" name="suporte_transfusional" <?php echo (isset($circulation) && $circulation['suporte_transfusional']) ? 'checked' : ''; ?>>
            Suporte Transfusional
          </label>
        </div>
        <div class="form-group">
          <label class="checkbox-group">
            <input type="checkbox" name="sonda_vesical" <?php echo (isset($circulation) && $circulation['sonda_vesical']) ? 'checked' : ''; ?>>
            Sonda Vesical
          </label>
          <label>Número<br>
            <input type="text" name="sonda_vesical_numero" value="<?php echo isset($circulation) ? esc($circulation['sonda_vesical_numero']) : ''; ?>">
          </label>
          <label>Data<br>
            <input type="date" name="sonda_vesical_data" value="<?php echo isset($circulation) ? esc($circulation['sonda_vesical_data']) : ''; ?>">
          </label>
        </div>
        <div class="form-group">
          <label class="checkbox-group">
            <input type="checkbox" name="lavagem_vesical" <?php echo (isset($circulation) && $circulation['lavagem_vesical']) ? 'checked' : ''; ?>>
            Lavagem Vesical
          </label>
          <label>mL/h<br>
            <input type="number" name="lavagem_vesical_ml_h" step="0.1" value="<?php echo isset($circulation) ? esc($circulation['lavagem_vesical_ml_h']) : ''; ?>">
          </label>
        </div>
      </div>

      <!-- D: NEUROLÓGICO -->
      <div class="section">
        <h3>D - NEUROLÓGICO</h3>
        <div class="form-group">
          <label>ECG (Escala Glasgow)<br>
            <input type="number" name="ecg_pontos" value="<?php echo isset($neurological) ? esc($neurological['ecg_pontos']) : ''; ?>" min="3" max="15">
          </label>
          <label>RASS (Sedação)<br>
            <input type="number" name="rass_pontos" value="<?php echo isset($neurological) ? esc($neurological['rass_pontos']) : ''; ?>">
          </label>
          <label>EVA (Dor Visual)<br>
            <input type="number" name="eva_pontos" value="<?php echo isset($neurological) ? esc($neurological['eva_pontos']) : ''; ?>" min="0" max="10">
          </label>
          <label>BPS (Dor Comportamental)<br>
            <input type="number" name="bps_pontos" value="<?php echo isset($neurological) ? esc($neurological['bps_pontos']) : ''; ?>">
          </label>
          <label>Glicemia Capilar (mg/dL)<br>
            <input type="number" name="glicemia_capilar" step="0.1" value="<?php echo isset($neurological) ? esc($neurological['glicemia_capilar']) : ''; ?>">
          </label>
        </div>
        <div class="form-group">
          <label class="checkbox-group">
            <input type="checkbox" name="sng_presente" <?php echo (isset($neurological) && $neurological['sng_presente']) ? 'checked' : ''; ?>>
            SNG (Sonda Naso-Gástrica)
          </label>
          <label>Nível<br>
            <input type="text" name="sng_nivel" value="<?php echo isset($neurological) ? esc($neurological['sng_nivel']) : ''; ?>">
          </label>
          <label>Data<br>
            <input type="date" name="sng_data" value="<?php echo isset($neurological) ? esc($neurological['sng_data']) : ''; ?>">
          </label>
        </div>
        <div class="form-group">
          <label class="checkbox-group">
            <input type="checkbox" name="sog_presente" <?php echo (isset($neurological) && $neurological['sog_presente']) ? 'checked' : ''; ?>>
            SOG (Sonda Oro-Gástrica)
          </label>
          <label>Nível<br>
            <input type="text" name="sog_nivel" value="<?php echo isset($neurological) ? esc($neurological['sog_nivel']) : ''; ?>">
          </label>
          <label>Data<br>
            <input type="date" name="sog_data" value="<?php echo isset($neurological) ? esc($neurological['sog_data']) : ''; ?>">
          </label>
        </div>
        <div class="form-group">
          <label class="checkbox-group">
            <input type="checkbox" name="snj_presente" <?php echo (isset($neurological) && $neurological['snj_presente']) ? 'checked' : ''; ?>>
            SNJ (Sonda Naso-Jejunal)
          </label>
          <label>Nível<br>
            <input type="text" name="snj_nivel" value="<?php echo isset($neurological) ? esc($neurological['snj_nivel']) : ''; ?>">
          </label>
          <label>Data<br>
            <input type="date" name="snj_data" value="<?php echo isset($neurological) ? esc($neurological['snj_data']) : ''; ?>">
          </label>
        </div>
        <div class="form-group">
          <label>Esvaziamento Gástrico<br>
            <textarea name="esvaziamento_gastrico"><?php echo isset($neurological) ? esc($neurological['esvaziamento_gastrico']) : ''; ?></textarea>
          </label>
        </div>
      </div>

      <!-- E: EXPOSIÇÃO -->
      <div class="section">
        <h3>E - EXPOSIÇÃO</h3>
        <div class="form-group">
          <label>Temperatura (ºC)<br>
            <input type="number" name="temperatura" step="0.1" value="<?php echo isset($exposure) ? esc($exposure['temperatura']) : ''; ?>">
          </label>
          <label class="checkbox-group">
            <input type="checkbox" name="imobilizacao_cervical" <?php echo (isset($exposure) && $exposure['imobilizacao_cervical']) ? 'checked' : ''; ?>>
            Imobilização Cervical
          </label>
        </div>
        <div class="form-group">
          <label class="checkbox-group">
            <input type="checkbox" name="fraturas" <?php echo (isset($exposure) && $exposure['fraturas']) ? 'checked' : ''; ?>>
            Fraturas
          </label>
          <label>Locais<br>
            <input type="text" name="fraturas_locais" value="<?php echo isset($exposure) ? esc($exposure['fraturas_locais']) : ''; ?>">
          </label>
        </div>
        <div class="form-group">
          <label class="checkbox-group">
            <input type="checkbox" name="feridas_pensos" <?php echo (isset($exposure) && $exposure['feridas_pensos']) ? 'checked' : ''; ?>>
            Feridas/Pensos
          </label>
          <label>Local<br>
            <input type="text" name="feridas_pensos_local" value="<?php echo isset($exposure) ? esc($exposure['feridas_pensos_local']) : ''; ?>">
          </label>
          <label>Tratamento<br>
            <textarea name="feridas_pensos_tratamento"><?php echo isset($exposure) ? esc($exposure['feridas_pensos_tratamento']) : ''; ?></textarea>
          </label>
        </div>
      </div>
    </div>

    <!-- TAB 3: MONITORIZAÇÃO -->
    <div id="monitoring" class="tab-content">
      <div class="section">
        <h3>Sinais Vitais (Registos Periódicos)</h3>
        <p><em>Nota: O sistema permite múltiplos registos ao longo do transporte</em></p>
        <?php 
        if (isset($monitoring) && count($monitoring) > 0) {
          foreach ($monitoring as $idx => $mon) {
            echo '<div style="background: #fff; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px;">';
            echo '<strong>Registo ' . ($idx+1) . '</strong> - ' . esc($mon['momento']) . ' às ' . esc($mon['hora_registo']) . '<br>';
            echo 'TA: ' . esc($mon['ta_sistolica']) . '/' . esc($mon['ta_diastolica']) . ' | ';
            echo 'FC: ' . esc($mon['fc']) . ' | SPO2: ' . esc($mon['spo2']) . '% | ';
            echo 'FR: ' . esc($mon['fr']) . ' | ETCO2: ' . esc($mon['etco2']) . '<br>';
            echo '</div>';
          }
        }
        ?>
        <div style="margin-top: 20px; padding: 10px; background: #e7f3ff; border-left: 4px solid #007bff;">
          <p><strong>➕ Adicionar Novo Registo de Sinais Vitais</strong></p>
          <div class="form-group">
            <label>Momento<br>
              <select name="monitoring_momento">
                <option value="">Selecione...</option>
                <option value="Saída">Saída ULSCB</option>
                <option value="Chegada SD">Chegada SD</option>
                <option value="Chegada ULSCB">Chegada ULSCB</option>
                <option value="Em Transporte">Em Transporte</option>
              </select>
            </label>
            <label>Hora<br>
              <input type="time" name="monitoring_hora">
            </label>
          </div>
          <div class="form-group">
            <label>TA Sistólica (mmHg)<br>
              <input type="number" name="monitoring_ta_sistolica">
            </label>
            <label>TA Diastólica (mmHg)<br>
              <input type="number" name="monitoring_ta_diastolica">
            </label>
            <label>FC (bpm)<br>
              <input type="number" name="monitoring_fc">
            </label>
            <label>SPO2 (%)<br>
              <input type="number" name="monitoring_spo2" min="0" max="100">
            </label>
          </div>
          <div class="form-group">
            <label>FR (rpm)<br>
              <input type="number" name="monitoring_fr">
            </label>
            <label>ETCO2 (mmHg)<br>
              <input type="number" name="monitoring_etco2">
            </label>
          </div>
        </div>
      </div>
    </div>

    <!-- TAB 4: TERAPÊUTICA -->
    <div id="terapeutica" class="tab-content">
      <div class="section">
        <h3>Perfusões (Medicamentos IV)</h3>
        <table style="width: 100%; border-collapse: collapse; margin: 10px 0;">
          <thead>
            <tr style="background: #f0f0f0;">
              <th style="border: 1px solid #ddd; padding: 8px;">Fármaco</th>
              <th style="border: 1px solid #ddd; padding: 8px;">Posologia</th>
              <th style="border: 1px solid #ddd; padding: 8px;">Hora</th>
              <th style="border: 1px solid #ddd; padding: 8px;">Taxa 1</th>
              <th style="border: 1px solid #ddd; padding: 8px;">Taxa 2</th>
              <th style="border: 1px solid #ddd; padding: 8px;">Taxa 3</th>
              <th style="border: 1px solid #ddd; padding: 8px;">Taxa 4</th>
              <th style="border: 1px solid #ddd; padding: 8px;">Ação</th>
            </tr>
          </thead>
          <tbody id="perfusions_body">
            <?php 
            if (isset($perfusions) && count($perfusions) > 0) {
              foreach ($perfusions as $idx => $perf) {
                echo '<tr>';
                echo '<td style="border: 1px solid #ddd; padding: 5px;"><input type="text" name="perfusao_farmaco[]" value="' . esc($perf['farmaco']) . '" style="width: 100%; padding: 5px; border: 1px solid #ddd;"></td>';
                echo '<td style="border: 1px solid #ddd; padding: 5px;"><input type="text" name="perfusao_posologia[]" value="' . esc($perf['posologia']) . '" style="width: 100%; padding: 5px; border: 1px solid #ddd;"></td>';
                echo '<td style="border: 1px solid #ddd; padding: 5px;"><input type="time" name="perfusao_hora_inicio[]" value="' . esc($perf['hora_inicio']) . '" style="width: 100%; padding: 5px; border: 1px solid #ddd;"></td>';
                echo '<td style="border: 1px solid #ddd; padding: 5px;"><input type="number" step="0.1" name="perfusao_taxa_1[]" value="' . esc($perf['taxa_1']) . '" style="width: 100%; padding: 5px; border: 1px solid #ddd;"></td>';
                echo '<td style="border: 1px solid #ddd; padding: 5px;"><input type="number" step="0.1" name="perfusao_taxa_2[]" value="' . esc($perf['taxa_2']) . '" style="width: 100%; padding: 5px; border: 1px solid #ddd;"></td>';
                echo '<td style="border: 1px solid #ddd; padding: 5px;"><input type="number" step="0.1" name="perfusao_taxa_3[]" value="' . esc($perf['taxa_3']) . '" style="width: 100%; padding: 5px; border: 1px solid #ddd;"></td>';
                echo '<td style="border: 1px solid #ddd; padding: 5px;"><input type="number" step="0.1" name="perfusao_taxa_4[]" value="' . esc($perf['taxa_4']) . '" style="width: 100%; padding: 5px; border: 1px solid #ddd;"></td>';
                echo '<td style="border: 1px solid #ddd; padding: 5px;"><button type="button" class="btn btn-danger" style="width: 100%;" onclick="removePerfusao(this)">🗑️</button></td>';
                echo '</tr>';
              }
            }
            ?>
          </tbody>
        </table>
        <button type="button" class="btn" style="background: #28a745;" onclick="addPerfusao()">➕ Adicionar Perfusão</button>
      </div>

      <div class="section">
        <h3>Outros Fármacos</h3>
        <table style="width: 100%; border-collapse: collapse; margin: 10px 0;">
          <thead>
            <tr style="background: #f0f0f0;">
              <th style="border: 1px solid #ddd; padding: 8px;">Fármaco</th>
              <th style="border: 1px solid #ddd; padding: 8px;">Hora Administração</th>
              <th style="border: 1px solid #ddd; padding: 8px;">Ação</th>
            </tr>
          </thead>
          <tbody id="farmacos_body">
            <?php 
            if (isset($farmacos) && count($farmacos) > 0) {
              foreach ($farmacos as $idx => $farm) {
                echo '<tr>';
                echo '<td style="border: 1px solid #ddd; padding: 5px;"><input type="text" name="farmaco_nome[]" value="' . esc($farm['farmaco']) . '" style="width: 100%; padding: 5px; border: 1px solid #ddd;"></td>';
                echo '<td style="border: 1px solid #ddd; padding: 5px;"><input type="time" name="farmaco_hora[]" value="' . esc($farm['hora_administracao']) . '" style="width: 100%; padding: 5px; border: 1px solid #ddd;"></td>';
                echo '<td style="border: 1px solid #ddd; padding: 5px;"><button type="button" class="btn btn-danger" style="width: 100%;" onclick="removeFarmaco(this)">🗑️</button></td>';
                echo '</tr>';
              }
            }
            ?>
          </tbody>
        </table>
        <button type="button" class="btn" style="background: #dc3545;" onclick="addFarmaco()">➕ Adicionar Fármaco</button>
      </div>
    </div>

    <!-- TAB 5: EVENTOS ADVERSOS -->
    <div id="eventos" class="tab-content">
      <div class="section">
        <h3>Eventos Adversos/Intercorrências</h3>
        <?php 
        if (isset($intercurrencies) && count($intercurrencies) > 0) {
          foreach ($intercurrencies as $idx => $evt) {
            echo '<div style="background: #fff; padding: 10px; margin: 10px 0; border: 2px solid #dc3545; border-radius: 4px;">';
            echo '<strong>⚠️ Evento às ' . esc($evt['hora_evento']) . '</strong><br>';
            echo '<strong>Descrição:</strong> ' . esc($evt['evento']) . '<br>';
            echo '<strong>Intervenção:</strong> ' . esc($evt['intervencao_realizada']) . '<br>';
            echo '</div>';
          }
        }
        ?>
        <div style="margin-top: 20px; padding: 10px; background: #ffe7e7; border-left: 4px solid #dc3545;">
          <p><strong>➕ Adicionar Evento Adverso</strong></p>
          <div class="form-group">
            <label>Hora do Evento<br>
              <input type="time" name="evento_hora">
            </label>
          </div>
          <div class="form-group">
            <label>Descrição do Evento<br>
              <textarea name="evento_descricao"></textarea>
            </label>
          </div>
          <div class="form-group">
            <label>Intervenção Realizada<br>
              <textarea name="evento_intervencao"></textarea>
            </label>
          </div>
        </div>
      </div>
    </div>

    <!-- TAB 6: EQUIPA -->
    <div id="equipa" class="tab-content">
      <div class="section">
        <h3>👥 Responsáveis pelo Transporte</h3>
        <div class="form-group">
          <label>Enfermeiro Responsável<br>
            <input type="text" name="enfermeiro" value="<?php echo isset($team) ? esc($team['enfermeiro']) : ''; ?>" placeholder="Nome do enfermeiro">
          </label>
          <label>Médico Responsável<br>
            <input type="text" name="medico" value="<?php echo isset($team) ? esc($team['medico']) : ''; ?>" placeholder="Nome do médico">
          </label>
        </div>
      </div>
    </div>

    <!-- BOTÕES DE AÇÃO -->
    <div style="margin: 20px 0; display: flex; gap: 10px; flex-wrap: wrap;">
      <button type="submit" class="btn">✅ Guardar Registo</button>
      <a href="tdc_list.php" class="btn" style="background: #6c757d; text-decoration: none;">← Cancelar</a>
    </div>
  </form>
</div>

<script>
  // Sistema de abas
  document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const tabId = btn.getAttribute('data-tab');
      
      // Remover active de todos
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
      
      // Adicionar active ao clicado
      btn.classList.add('active');
      document.getElementById(tabId).classList.add('active');
    });
  });

  // FUNÇÕES PARA PERFUSÕES
  function addPerfusao() {
    const tbody = document.getElementById('perfusions_body');
    const row = document.createElement('tr');
    row.innerHTML = `
      <td style="border: 1px solid #ddd; padding: 5px;"><input type="text" name="perfusao_farmaco[]" placeholder="Nome do fármaco" style="width: 100%; padding: 5px; border: 1px solid #ddd;"></td>
      <td style="border: 1px solid #ddd; padding: 5px;"><input type="text" name="perfusao_posologia[]" placeholder="Ex: 5mg/kg IV" style="width: 100%; padding: 5px; border: 1px solid #ddd;"></td>
      <td style="border: 1px solid #ddd; padding: 5px;"><input type="time" name="perfusao_hora_inicio[]" style="width: 100%; padding: 5px; border: 1px solid #ddd;"></td>
      <td style="border: 1px solid #ddd; padding: 5px;"><input type="number" step="0.1" name="perfusao_taxa_1[]" placeholder="mL/h" style="width: 100%; padding: 5px; border: 1px solid #ddd;"></td>
      <td style="border: 1px solid #ddd; padding: 5px;"><input type="number" step="0.1" name="perfusao_taxa_2[]" placeholder="mL/h" style="width: 100%; padding: 5px; border: 1px solid #ddd;"></td>
      <td style="border: 1px solid #ddd; padding: 5px;"><input type="number" step="0.1" name="perfusao_taxa_3[]" placeholder="mL/h" style="width: 100%; padding: 5px; border: 1px solid #ddd;"></td>
      <td style="border: 1px solid #ddd; padding: 5px;"><input type="number" step="0.1" name="perfusao_taxa_4[]" placeholder="mL/h" style="width: 100%; padding: 5px; border: 1px solid #ddd;"></td>
      <td style="border: 1px solid #ddd; padding: 5px;"><button type="button" class="btn btn-danger" style="width: 100%;" onclick="removePerfusao(this)">🗑️</button></td>
    `;
    tbody.appendChild(row);
  }

  function removePerfusao(btn) {
    const row = btn.closest('tr');
    row.remove();
  }

  // FUNÇÕES PARA FÁRMACOS
  function addFarmaco() {
    const tbody = document.getElementById('farmacos_body');
    const row = document.createElement('tr');
    row.innerHTML = `
      <td style="border: 1px solid #ddd; padding: 5px;"><input type="text" name="farmaco_nome[]" placeholder="Nome do fármaco" style="width: 100%; padding: 5px; border: 1px solid #ddd;"></td>
      <td style="border: 1px solid #ddd; padding: 5px;"><input type="time" name="farmaco_hora[]" style="width: 100%; padding: 5px; border: 1px solid #ddd;"></td>
      <td style="border: 1px solid #ddd; padding: 5px;"><button type="button" class="btn btn-danger" style="width: 100%;" onclick="removeFarmaco(this)">🗑️</button></td>
    `;
    tbody.appendChild(row);
  }

  function removeFarmaco(btn) {
    const row = btn.closest('tr');
    row.remove();
  }
</script>
</body>
</html>
