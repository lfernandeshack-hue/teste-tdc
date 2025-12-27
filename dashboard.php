<?php
require 'config.php';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Dashboard</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="container">
  <?php include 'header_inc.php'; ?>
  <h1>Dashboard TDC</h1>
  <nav>
    <a href="tdc_form_novo.php">➕ Novo Registo</a> | 
    <a href="tdc_list_novo.php">🏥 Fichas TDC (Nova)</a> | 
    <a href="tdc_list.php">📋 Itens</a>
  </nav>
  <p><strong>Sistema TDC - Transporte Doente Crítico (Registos de Enfermagem)</strong></p>
  <p>Utilize os menus acima para criar novos registos, gerir fichas de enfermagem e itens da base de dados.</p>
  <hr>
  <h2>📈 Painel de Gráficos</h2>
  <p>Visualize métricas e séries temporais dos registos TDC.</p>
  <div style="margin-bottom:8px">
    <a href="tdc_charts.php" target="_blank">Abrir painel de gráficos numa nova aba</a>
  </div>
  <div style="width:100%;height:640px;border:1px solid #ddd;border-radius:6px;overflow:hidden">
    <iframe src="tdc_charts.php" style="width:100%;height:100%;border:0;" title="Painel de Gráficos TDC"></iframe>
  </div>
</div>
</body>
</html>