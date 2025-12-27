<?php
require 'config.php';
$uid = 1; // user_id padrão

// Listar fichas TDC
$res = $mysqli->query('SELECT id_tdc, motivo_transporte, servico_destino, hora_ativacao, hora_chegada_sd, score_tdc, created_at FROM tdc_records WHERE created_by=' . (int)$uid . ' ORDER BY created_at DESC LIMIT 100');
$registos = $res->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Fichas TDC</title>
<link rel="stylesheet" href="styles.css">
<style>
  .btn-small {
    padding: 5px 10px;
    font-size: 12px;
    margin: 2px;
  }
  table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
  }
  th, td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: left;
  }
  th {
    background: #007bff;
    color: white;
  }
  tr:nth-child(even) {
    background: #f9f9f9;
  }
  tr:hover {
    background: #f0f0f0;
  }
  .status {
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
  }
  .status-novo { background: #d4edda; color: #155724; }
  .empty {
    text-align: center;
    padding: 30px;
    color: #999;
  }
</style>
</head>
<body>
<div class="container">
  <?php include 'header_inc.php'; ?>
  <h1>🏥 Fichas TDC - Registos de Enfermagem</h1>
  <nav>
    <a href="dashboard.php">← Voltar ao Dashboard</a> | 
    <a href="tdc_form_novo.php">➕ Novo Registo</a>
  </nav>

  <?php if (count($registos) > 0): ?>
    <p><strong>Total de registos:</strong> <?php echo count($registos); ?></p>
    
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Motivo do Transporte</th>
          <th>Serviço de Destino</th>
          <th>Hora Ativação</th>
          <th>Hora Chegada</th>
          <th>Score TDC</th>
          <th>Data Criação</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($registos as $reg): ?>
          <tr>
            <td><strong>#<?php echo esc($reg['id_tdc']); ?></strong></td>
            <td><?php echo esc($reg['motivo_transporte']); ?></td>
            <td><?php echo esc($reg['servico_destino']); ?></td>
            <td><?php echo esc($reg['hora_ativacao']); ?></td>
            <td><?php echo esc($reg['hora_chegada_sd']); ?></td>
            <td><?php echo $reg['score_tdc'] ? '<span class="status status-novo">' . esc($reg['score_tdc']) . '</span>' : '-'; ?></td>
            <td><?php echo date('d/m/Y H:i', strtotime($reg['created_at'])); ?></td>
            <td>
              <a href="tdc_view_novo.php?id=<?php echo $reg['id_tdc']; ?>">Ver</a>
              &nbsp;|&nbsp;
              <a href="tdc_form_novo.php?id=<?php echo $reg['id_tdc']; ?>">Editar</a>
              &nbsp;|&nbsp;
              <a href="tdc_export_pdf.php?id=<?php echo $reg['id_tdc']; ?>" target="_blank">PDF</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <div class="empty">
      <p>📭 Nenhum registo criado ainda.</p>
      <p><a href="tdc_form_novo.php" class="btn">➕ Criar Novo Registo</a></p>
    </div>
  <?php endif; ?>
</div>
</body>
</html>
