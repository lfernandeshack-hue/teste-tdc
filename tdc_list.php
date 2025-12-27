<?php
require 'config.php';
$uid = 1; // user_id padrão (sem autenticação)

// listar fichas TDC do usuário (tabela `tdc_records`)
$sql = 'SELECT id_tdc,ficha_numero,`data`,servico,diagnostico,score_tdc FROM tdc_records WHERE created_by=' . (int)$uid . ' ORDER BY `data` DESC LIMIT 50';
$res = $mysqli->query($sql);
if ($res === false) {
  echo '<div class="container"><p style="color:red;">Erro na query: ' . esc($mysqli->error) . '</p></div>';
  $res = null;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Fichas TDC</title>
<link rel="stylesheet" href="styles.css">
<style>
.tdc-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
.tdc-table th, .tdc-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
.tdc-table th { background: #007bff; color: #fff; }
.action-btn { margin: 0 3px; padding: 5px 10px; background: #28a745; color: white; text-decoration: none; border: none; border-radius: 3px; cursor: pointer; font-size: 12px; }
.action-btn:hover { background: #218838; }
.delete-btn { background: #dc3545; }
.delete-btn:hover { background: #c82333; }
</style>
</head>
<body>
<div class="container">
  <?php include 'header_inc.php'; ?>
  <h1>Fichas de Transporte Doente Crítico (TDC)</h1>
  <p><a href="dashboard.php">Voltar ao Dashboard</a> | <a href="tdc_form.php" class="action-btn" style="display:inline-block;">+ Nova Ficha</a></p>
  
  <table class="tdc-table">
    <tr>
        <th>ID</th>
        <th>Data</th>
        <th>Serviço</th>
        <th>Diagnóstico</th>
        <th>Score TDC</th>
      <th>Ações</th>
    </tr>
    <?php if ($res && $res->num_rows > 0): while($row = $res->fetch_assoc()): ?>
    <tr>
      <td><?php echo $row['id_tdc']; ?></td>
      <td><?php echo $row['data'] ?? 'N/A'; ?></td>
      <td><?php echo esc($row['servico'] ?? 'N/A'); ?></td>
      <td><?php echo substr(esc($row['diagnostico'] ?? ''), 0, 50) . '...'; ?></td>
      <td><?php echo $row['score_tdc'] ?? '-'; ?></td>
      <td>
        <a href="tdc_view.php?id=<?php echo $row['id_tdc']; ?>" class="action-btn">Ver</a>
        <a href="tdc_form.php?id=<?php echo $row['id_tdc']; ?>" class="action-btn">Editar</a>
        <a href="tdc_delete.php?id=<?php echo $row['id_tdc']; ?>" class="action-btn delete-btn" onclick="return confirm('Remover?')">Remover</a>
      </td>
    </tr>
    <?php endwhile; else: ?>
    <tr><td colspan="6">Nenhuma ficha encontrada.</td></tr>
    <?php endif; ?>
  </table>
</div>
</body>
</html>