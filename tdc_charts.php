<?php
require 'config.php';

// API endpoints (action parameter) + UI page (no action)
$action = $_GET['action'] ?? '';

if ($action === 'summaries') {
    header('Content-Type: application/json');

    // Registos por dia (últimos 30 dias)
    $rows = [];
    $sql = "SELECT DATE(created_at) as d, COUNT(*) as c FROM tdc_records WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY d ORDER BY d";
    $res = $mysqli->query($sql);
    $by_day = [];
    if ($res) {
        while ($r = $res->fetch_assoc()) { $by_day[] = $r; }
    }

    // Distribuição de score_tdc
    $score = [];
    $sql = "SELECT score_tdc as s, COUNT(*) as c FROM tdc_records GROUP BY s ORDER BY s";
    $res = $mysqli->query($sql);
    if ($res) {
        while ($r = $res->fetch_assoc()) { $score[] = $r; }
    }

    // Top fármacos em perfusões
    $drugs = [];
    $sql = "SELECT farmaco, COUNT(*) as c FROM tdc_perfusions GROUP BY farmaco ORDER BY c DESC LIMIT 10";
    $res = $mysqli->query($sql);
    if ($res) {
        while ($r = $res->fetch_assoc()) { $drugs[] = $r; }
    }

    // Lista de registos para seleção (últimos 200)
    $records = [];
    $sql = "SELECT id_tdc, motivo_transporte, created_at FROM tdc_records ORDER BY created_at DESC LIMIT 200";
    $res = $mysqli->query($sql);
    if ($res) {
        while ($r = $res->fetch_assoc()) { $records[] = $r; }
    }

    echo json_encode(['by_day' => $by_day, 'score' => $score, 'drugs' => $drugs, 'records' => $records]);
    exit;
}

if ($action === 'monitoring') {
    header('Content-Type: application/json');
    $id = intval($_GET['id'] ?? 0);
    if (!$id) { echo json_encode(['error' => 'missing id']); exit; }

    $sql = "SELECT hora, temperatura, frequencia_cardiaca, pressao_arterial, frequencia_respiratoria, spo2, glicemia, created_at FROM tdc_monitoring WHERE id_tdc = ? ORDER BY created_at";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $data = [];
    while ($r = $res->fetch_assoc()) { $data[] = $r; }
    echo json_encode($data);
    exit;
}

// UI page
?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Gráficos TDC</title>
<link rel="stylesheet" href="styles.css">
<style>
.container{max-width:1100px;margin:20px auto;padding:10px}
.card{background:#fff;padding:12px;border:1px solid #eee;margin-bottom:12px;border-radius:6px}
.row{display:flex;gap:12px;flex-wrap:wrap}
.col{flex:1 1 300px}
canvas{background:#fff;border-radius:4px}
</style>
</head>
<body>
<div class="container">
  <?php include 'header_inc.php'; ?>
  <h1>Gráficos - Sistema TDC</h1>
  <p>Visualizações básicas baseadas na base de dados. Seleciona um registo para ver série temporal de monitorização.</p>

  <div class="card">
    <button id="refresh">Atualizar dados</button>
    <label style="margin-left:10px">Registo:</label>
    <select id="recordSelect"></select>
  </div>

  <div class="row">
    <div class="col card">
      <h3>Registos por dia (últimos 30 dias)</h3>
      <canvas id="chartByDay" height="120"></canvas>
    </div>

    <div class="col card">
      <h3>Distribuição Score TDC</h3>
      <canvas id="chartScore" height="120"></canvas>
    </div>

    <div class="col card">
      <h3>Top Fármacos em Perfusões</h3>
      <canvas id="chartDrugs" height="120"></canvas>
    </div>
  </div>

  <div class="card">
    <h3>Série Temporal de Monitorização</h3>
    <canvas id="chartMonitoring" height="200"></canvas>
  </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
async function fetchSummaries(){
  const res = await fetch('tdc_charts.php?action=summaries');
  return await res.json();
}
async function fetchMonitoring(id){
  const res = await fetch('tdc_charts.php?action=monitoring&id='+id);
  return await res.json();
}

let chartByDay, chartScore, chartDrugs, chartMonitoring;

function formatDateLabel(d){
  const dt = new Date(d);
  return dt.toISOString().slice(0,10);
}

function buildCharts(data){
  // By day
  const labels = data.by_day.map(r => r.d);
  const values = data.by_day.map(r => parseInt(r.c,10));
  const ctx1 = document.getElementById('chartByDay').getContext('2d');
  if (chartByDay) chartByDay.destroy();
  chartByDay = new Chart(ctx1, { type: 'line', data: { labels, datasets: [{ label: 'Registos/dia', data: values, borderColor: '#007bff', backgroundColor: 'rgba(0,123,255,0.1)', fill:true }] }, options:{ responsive:true } });

  // Score
  const scoreLabels = data.score.map(r => r.s === null ? 'N/A' : r.s);
  const scoreVals = data.score.map(r => parseInt(r.c,10));
  const ctx2 = document.getElementById('chartScore').getContext('2d');
  if (chartScore) chartScore.destroy();
  chartScore = new Chart(ctx2, { type:'bar', data:{ labels: scoreLabels, datasets:[{ label:'Count', data: scoreVals, backgroundColor:'#28a745' }] }, options:{ responsive:true } });

  // Drugs
  const drugLabels = data.drugs.map(r => r.farmaco || 'Desconhecido');
  const drugVals = data.drugs.map(r => parseInt(r.c,10));
  const ctx3 = document.getElementById('chartDrugs').getContext('2d');
  if (chartDrugs) chartDrugs.destroy();
  chartDrugs = new Chart(ctx3, { type:'bar', data:{ labels: drugLabels, datasets:[{ label:'Perfusões', data: drugVals, backgroundColor:'#ffc107' }] }, options:{ indexAxis: 'y', responsive:true } });

  // populate record select
  const sel = document.getElementById('recordSelect');
  sel.innerHTML = '';
  data.records.forEach(r => {
    const opt = document.createElement('option');
    opt.value = r.id_tdc;
    opt.text = r.id_tdc + ' — ' + (r.motivo_transporte ? r.motivo_transporte.substring(0,40) : '') + ' (' + (r.created_at || '') + ')';
    sel.appendChild(opt);
  });

  if (data.records.length>0){
    loadMonitoring(data.records[0].id_tdc);
  }
}

async function loadMonitoring(id){
  const data = await fetchMonitoring(id);
  if (data.error){ console.error(data.error); return; }
  const labels = data.map(r => r.hora || r.created_at || '');
  const temp = data.map(r => r.temperatura ? parseFloat(r.temperatura) : null);
  const fc = data.map(r => r.frequencia_cardiaca ? parseFloat(r.frequencia_cardiaca) : null);
  const pa = data.map(r => r.pressao_arterial ? r.pressao_arterial : null);
  const fr = data.map(r => r.frequencia_respiratoria ? parseFloat(r.frequencia_respiratoria) : null);
  const spo2 = data.map(r => r.spo2 ? parseFloat(r.spo2) : null);
  const glic = data.map(r => r.glicemia ? parseFloat(r.glicemia) : null);

  const ctx = document.getElementById('chartMonitoring').getContext('2d');
  if (chartMonitoring) chartMonitoring.destroy();
  chartMonitoring = new Chart(ctx, {
    type: 'line',
    data: {
      labels,
      datasets: [
        { label: 'Temperatura (°C)', data: temp, borderColor:'#ff6384', fill:false },
        { label: 'FC (/min)', data: fc, borderColor:'#36a2eb', fill:false },
        { label: 'FR (/min)', data: fr, borderColor:'#9966ff', fill:false },
        { label: 'SpO2 (%)', data: spo2, borderColor:'#4bc0c0', fill:false },
        { label: 'Glicemia', data: glic, borderColor:'#ff9f40', fill:false }
      ]
    },
    options: { responsive:true, scales:{ y:{ beginAtZero:false } } }
  });
}

document.getElementById('refresh').addEventListener('click', async ()=>{
  const data = await fetchSummaries();
  buildCharts(data);
});

document.getElementById('recordSelect').addEventListener('change', function(){
  const id = this.value; if (id) loadMonitoring(id);
});

// initial load
fetchSummaries().then(buildCharts).catch(err=>console.error(err));
</script>
</body>
</html>
