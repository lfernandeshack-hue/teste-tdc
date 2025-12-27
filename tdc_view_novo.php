<?php
require 'config.php';
$uid = 1;

$id_tdc = isset($_GET['id']) ? (int)$_GET['id'] : null;
if (!$id_tdc) {
  header('Location: tdc_list_novo.php');
  exit;
}

// Carregar todos os dados
$tdc = $mysqli->query("SELECT * FROM tdc_records WHERE id_tdc=$id_tdc AND created_by=$uid")->fetch_assoc();
if (!$tdc) {
  header('Location: tdc_list_novo.php');
  exit;
}

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
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Visualizar Registo TDC #<?php echo $id_tdc; ?></title>
<link rel="stylesheet" href="styles.css">
<style>
  .header-info {
    background: #f0f0f0;
    padding: 15px;
    border-radius: 5px;
    margin: 10px 0;
  }
  .section {
    background: #f9f9f9;
    padding: 15px;
    margin: 20px 0;
    border-radius: 5px;
    border-left: 4px solid #007bff;
  }
  .section h3 {
    margin-top: 0;
    color: #007bff;
  }
  .data-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
    margin: 10px 0;
  }
  .data-item {
    background: white;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
  }
  .data-item strong {
    display: block;
    color: #007bff;
    margin-bottom: 5px;
  }
  .data-item span {
    display: block;
    color: #333;
  }
  .badge {
    display: inline-block;
    padding: 5px 10px;
    background: #28a745;
    color: white;
    border-radius: 4px;
    font-size: 12px;
    margin: 2px;
  }
  .badge.danger {
    background: #dc3545;
  }
  table {
    width: 100%;
    border-collapse: collapse;
    margin: 10px 0;
  }
  th, td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
  }
  th {
    background: #007bff;
    color: white;
  }
  tr:nth-child(even) {
    background: #f9f9f9;
  }
  .btn {
    padding: 10px 20px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    margin: 5px;
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
  @media print {
    .no-print { display: none; }
    body { background: white; }
  }
</style>
</head>
<body>
<div class="container">
  <?php include 'header_inc.php'; ?>
  <h1>👁️ Visualizar Registo TDC #<?php echo $id_tdc; ?></h1>
  <div class="no-print">
    <nav>
      <a href="tdc_list_novo.php">← Voltar à Lista</a> | 
      <a href="tdc_form_novo.php?id=<?php echo $id_tdc; ?>" class="btn">✏️ Editar</a> |
      <a href="tdc_export_pdf.php?id=<?php echo $id_tdc; ?>" class="btn" style="background: #28a745;" target="_blank">📄 Exportar PDF</a> |
      <button onclick="window.print();" class="btn" style="background: #6c757d;">🖨️ Imprimir</button>
    </nav>
  </div>

  <div class="header-info">
    <h2><?php echo esc($tdc['motivo_transporte']); ?> → <?php echo esc($tdc['servico_destino']); ?></h2>
    <p>
      <strong>Data:</strong> <?php echo date('d/m/Y', strtotime($tdc['created_at'])); ?> | 
      <strong>Score TDC:</strong> <?php echo $tdc['score_tdc'] ? esc($tdc['score_tdc']) : '-'; ?> | 
      <strong>Criado:</strong> <?php echo date('d/m/Y H:i', strtotime($tdc['created_at'])); ?>
    </p>
  </div>

  <!-- DADOS ADMINISTRATIVOS -->
  <div class="section">
    <h3>📋 Dados Administrativos</h3>
    <div class="data-grid">
      <div class="data-item">
        <strong>Motivo Transporte</strong>
        <span><?php echo esc($tdc['motivo_transporte']); ?></span>
      </div>
      <div class="data-item">
        <strong>Serviço Destino</strong>
        <span><?php echo esc($tdc['servico_destino']); ?></span>
      </div>
      <div class="data-item">
        <strong>Hora Ativação</strong>
        <span><?php echo esc($tdc['hora_ativacao']); ?></span>
      </div>
      <div class="data-item">
        <strong>Saída ULSCB</strong>
        <span><?php echo esc($tdc['hora_saida_ulscb']); ?></span>
      </div>
      <div class="data-item">
        <strong>Chegada SD</strong>
        <span><?php echo esc($tdc['hora_chegada_sd']); ?></span>
      </div>
      <div class="data-item">
        <strong>Chegada ULSCB</strong>
        <span><?php echo esc($tdc['hora_chegada_ulscb']); ?></span>
      </div>
    </div>

    <div class="data-grid" style="margin-top: 20px;">
      <div class="data-item" style="grid-column: span 2;">
        <strong>Antecedentes Pessoais</strong>
        <span><?php echo esc($tdc['antecedentes_pessoais']) ?: '-'; ?></span>
      </div>
      <div class="data-item" style="grid-column: span 2;">
        <strong>Alergias</strong>
        <span><?php echo esc($tdc['alergias']) ?: '-'; ?></span>
      </div>
      <div class="data-item" style="grid-column: span 2;">
        <strong>Medicação Relevante</strong>
        <span><?php echo esc($tdc['medicacao_relevante']) ?: '-'; ?></span>
      </div>
    </div>
  </div>

  <!-- AVALIAÇÃO ABCDE -->
  <h2 style="margin-top: 30px;">🏥 Avaliação ABCDE</h2>

  <!-- A: VIA AÉREA -->
  <div class="section">
    <h3>A - VIA AÉREA</h3>
    <?php if ($airway): ?>
      <div class="data-grid">
        <div class="data-item">
          <strong>Via Aérea Patente</strong>
          <span><?php echo $airway['va_patente'] ? '✅ Sim' : '❌ Não'; ?></span>
        </div>
        <div class="data-item">
          <strong>Secreções</strong>
          <span><?php echo esc($airway['secrecoes']) ?: '-'; ?></span>
        </div>
        <div class="data-item">
          <strong>Adjuvante VA</strong>
          <span><?php echo $airway['adjuvante_va_tipo'] ? esc($airway['adjuvante_va_tipo']) . ' (' . esc($airway['adjuvante_va_numero']) . ')' : '-'; ?></span>
        </div>
        <div class="data-item">
          <strong>VA Definitiva</strong>
          <span><?php echo $airway['va_definitiva_tipo'] ? esc($airway['va_definitiva_tipo']) . ' (' . esc($airway['va_definitiva_numero']) . ')' : '-'; ?></span>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <!-- B: VENTILAÇÃO -->
  <div class="section">
    <h3>B - VENTILAÇÃO</h3>
    <?php if ($ventilation): ?>
      <div class="data-grid">
        <div class="data-item">
          <strong>Ventilação Espontânea</strong>
          <span><?php echo $ventilation['ventilacao_espontanea'] ? '✅ Sim' : '❌ Não'; ?></span>
        </div>
        <div class="data-item">
          <strong>O2 (L/min)</strong>
          <span><?php echo $ventilation['o2_litros'] ?: '-'; ?></span>
        </div>
        <div class="data-item">
          <strong>Tipo Suplementação</strong>
          <span><?php echo esc($ventilation['tipo_vent_suplementar']) ?: '-'; ?></span>
        </div>
        <div class="data-item">
          <strong>Drenagem Torácica</strong>
          <span><?php echo $ventilation['drenagem_toracica'] ? '✅ Sim' : '❌ Não'; ?></span>
        </div>
      </div>
      <?php if ($ventilation['vni_ipap']): ?>
        <p><strong>Parâmetros VNI:</strong> IPAP: <?php echo esc($ventilation['vni_ipap']); ?> | EPAP: <?php echo esc($ventilation['vni_epap']); ?> | FR: <?php echo esc($ventilation['vni_fr']); ?> | FIO2: <?php echo esc($ventilation['vni_fio2']); ?>%</p>
      <?php endif; ?>
      <?php if ($ventilation['vmi_tipo']): ?>
        <p><strong>Parâmetros VMI:</strong> Tipo: <?php echo esc($ventilation['vmi_tipo']); ?> | FIO2: <?php echo esc($ventilation['vmi_fio2']); ?>% | PEEP: <?php echo esc($ventilation['vmi_peep']); ?> | FR: <?php echo esc($ventilation['vmi_fr']); ?></p>
      <?php endif; ?>
    <?php endif; ?>
  </div>

  <!-- C: CIRCULAÇÃO -->
  <div class="section">
    <h3>C - CIRCULAÇÃO</h3>
    <?php if ($circulation): ?>
      <div class="data-grid">
        <div class="data-item">
          <strong>Linha Arterial</strong>
          <span><?php echo $circulation['dispositivo_la'] ? '✅ ' . esc($circulation['la_local']) : '❌ Não'; ?></span>
        </div>
        <div class="data-item">
          <strong>CVC</strong>
          <span><?php echo $circulation['cvc'] ? '✅ ' . esc($circulation['cvc_lumens']) . ' lúmens' : '❌ Não'; ?></span>
        </div>
        <div class="data-item">
          <strong>CVP (mmHg)</strong>
          <span><?php echo $circulation['cvp_valor'] ?: '-'; ?></span>
        </div>
        <div class="data-item">
          <strong>Hemorragia Ativa</strong>
          <span><?php echo $circulation['hemorragia_ativa'] ? '⚠️ SIM' : '✅ Não'; ?></span>
        </div>
        <div class="data-item">
          <strong>Transfusão</strong>
          <span><?php echo $circulation['suporte_transfusional'] ? '✅ Sim' : '❌ Não'; ?></span>
        </div>
        <div class="data-item">
          <strong>Sonda Vesical</strong>
          <span><?php echo $circulation['sonda_vesical'] ? '✅ ' . esc($circulation['sonda_vesical_numero']) : '❌ Não'; ?></span>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <!-- D: NEUROLÓGICO -->
  <div class="section">
    <h3>D - NEUROLÓGICO</h3>
    <?php if ($neurological): ?>
      <div class="data-grid">
        <div class="data-item">
          <strong>ECG (Glasgow)</strong>
          <span><?php echo $neurological['ecg_pontos'] ?: '-'; ?></span>
        </div>
        <div class="data-item">
          <strong>RASS (Sedação)</strong>
          <span><?php echo $neurological['rass_pontos'] !== null ? esc($neurological['rass_pontos']) : '-'; ?></span>
        </div>
        <div class="data-item">
          <strong>EVA (Dor Visual)</strong>
          <span><?php echo $neurological['eva_pontos'] !== null ? esc($neurological['eva_pontos']) : '-'; ?></span>
        </div>
        <div class="data-item">
          <strong>BPS (Dor Comportamental)</strong>
          <span><?php echo $neurological['bps_pontos'] ?: '-'; ?></span>
        </div>
        <div class="data-item">
          <strong>Glicemia Capilar (mg/dL)</strong>
          <span><?php echo $neurological['glicemia_capilar'] ?: '-'; ?></span>
        </div>
      </div>
      <?php 
      $sondas = [];
      if ($neurological['sng_presente']) $sondas[] = 'SNG';
      if ($neurological['sog_presente']) $sondas[] = 'SOG';
      if ($neurological['snj_presente']) $sondas[] = 'SNJ';
      if (count($sondas) > 0) {
        echo '<p><strong>Sondas:</strong> ' . implode(', ', $sondas) . '</p>';
      }
      ?>
    <?php endif; ?>
  </div>

  <!-- E: EXPOSIÇÃO -->
  <div class="section">
    <h3>E - EXPOSIÇÃO</h3>
    <?php if ($exposure): ?>
      <div class="data-grid">
        <div class="data-item">
          <strong>Temperatura (ºC)</strong>
          <span><?php echo $exposure['temperatura'] ?: '-'; ?></span>
        </div>
        <div class="data-item">
          <strong>Imobilização Cervical</strong>
          <span><?php echo $exposure['imobilizacao_cervical'] ? '✅ Sim' : '❌ Não'; ?></span>
        </div>
        <div class="data-item">
          <strong>Fraturas</strong>
          <span><?php echo $exposure['fraturas'] ? '⚠️ ' . esc($exposure['fraturas_locais']) : '❌ Não'; ?></span>
        </div>
        <div class="data-item">
          <strong>Feridas/Pensos</strong>
          <span><?php echo $exposure['feridas_pensos'] ? '✅ ' . esc($exposure['feridas_pensos_local']) : '❌ Não'; ?></span>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <!-- MONITORIZAÇÃO -->
  <?php if (count($monitoring) > 0): ?>
    <div class="section">
      <h3>📊 Monitorização - Sinais Vitais</h3>
      <table>
        <thead>
          <tr>
            <th>Momento</th>
            <th>Hora</th>
            <th>TA (mmHg)</th>
            <th>FC (bpm)</th>
            <th>SPO2 (%)</th>
            <th>FR (rpm)</th>
            <th>ETCO2 (mmHg)</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($monitoring as $mon): ?>
            <tr>
              <td><?php echo esc($mon['momento']); ?></td>
              <td><?php echo esc($mon['hora_registo']); ?></td>
              <td><?php echo $mon['ta_sistolica'] . '/' . $mon['ta_diastolica']; ?></td>
              <td><?php echo esc($mon['fc']); ?></td>
              <td><?php echo esc($mon['spo2']); ?></td>
              <td><?php echo esc($mon['fr']); ?></td>
              <td><?php echo esc($mon['etco2']); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

  <!-- PERFUSÕES -->
  <?php if (count($perfusions) > 0): ?>
    <div class="section">
      <h3>💊 Perfusões (Medicamentos IV)</h3>
      <table>
        <thead>
          <tr>
            <th>Fármaco</th>
            <th>Posologia</th>
            <th>Hora Início</th>
            <th>Taxas (mL/h)</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($perfusions as $perf): ?>
            <tr>
              <td><?php echo esc($perf['farmaco']); ?></td>
              <td><?php echo esc($perf['posologia']); ?></td>
              <td><?php echo esc($perf['hora_inicio']); ?></td>
              <td>
                <?php 
                $taxas = [];
                if ($perf['taxa_1']) $taxas[] = $perf['taxa_1'];
                if ($perf['taxa_2']) $taxas[] = $perf['taxa_2'];
                if ($perf['taxa_3']) $taxas[] = $perf['taxa_3'];
                if ($perf['taxa_4']) $taxas[] = $perf['taxa_4'];
                echo implode(' | ', $taxas) ?: '-';
                ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

  <!-- EVENTOS ADVERSOS -->
  <?php if (count($intercurrencies) > 0): ?>
    <div class="section">
      <h3>⚠️ Eventos Adversos</h3>
      <?php foreach ($intercurrencies as $evt): ?>
        <div style="background: white; padding: 10px; margin: 10px 0; border: 2px solid #dc3545; border-radius: 4px;">
          <p><strong>Hora:</strong> <?php echo esc($evt['hora_evento']); ?></p>
          <p><strong>Evento:</strong> <?php echo esc($evt['evento']); ?></p>
          <p><strong>Intervenção:</strong> <?php echo esc($evt['intervencao_realizada']); ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <!-- EQUIPA -->
  <?php if ($team): ?>
    <div class="section">
      <h3>👥 Responsáveis pelo Transporte</h3>
      <div class="data-grid">
        <div class="data-item">
          <strong>Enfermeiro Responsável</strong>
          <span><?php echo esc($team['enfermeiro']) ?: '-'; ?></span>
        </div>
        <div class="data-item">
          <strong>Médico Responsável</strong>
          <span><?php echo esc($team['medico']) ?: '-'; ?></span>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <div style="margin-top: 30px; padding: 20px; background: #f9f9f9; border-radius: 5px; text-align: center;">
    <p style="color: #999; font-size: 12px;">Registo criado em: <?php echo date('d/m/Y H:i:s', strtotime($tdc['created_at'])); ?> | Última atualização: <?php echo date('d/m/Y H:i:s', strtotime($tdc['updated_at'])); ?></p>
  </div>

  <div class="no-print" style="margin: 20px 0;">
    <a href="tdc_list_novo.php" class="btn">← Voltar à Lista</a>
    <a href="tdc_form_novo.php?id=<?php echo $id_tdc; ?>" class="btn">✏️ Editar Registo</a>
  </div>
</div>
</body>
</html>
