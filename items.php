<?php
require 'config.php';
$uid = 1; // user_id padrão (sem autenticação)

// criar novo item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action']==='create'){
    $title = $_POST['title'] ?? '';
    $desc = $_POST['description'] ?? '';
    if ($title){
        $stmt = $mysqli->prepare('INSERT INTO items (title,description,created_by) VALUES (?,?,?)');
        $stmt->bind_param('ssi',$title,$desc,$uid);
        $stmt->execute();
    }
    header('Location: items.php'); exit;
}

// fetch
$res = $mysqli->query('SELECT i.*, u.name AS owner FROM items i JOIN users u ON u.id=i.created_by WHERE i.created_by=' . (int)$uid . ' ORDER BY i.created_at DESC');
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Meus Itens</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
  <h1>Meus Itens</h1>
  <p><a href="dashboard.php">Voltar</a> | <a href="item_edit.php">Criar novo</a></p>
  <table class="list">
    <tr><th>ID</th><th>Título</th><th>Data</th><th>Ações</th></tr>
    <?php while($row = $res->fetch_assoc()): ?>
    <tr>
      <td><?php echo $row['id']; ?></td>
      <td><?php echo esc($row['title']); ?></td>
      <td><?php echo $row['created_at']; ?></td>
      <td><a href="item_edit.php?id=<?php echo $row['id']; ?>">Editar</a> | <a href="item_edit.php?action=delete&id=<?php echo $row['id']; ?>" onclick="return confirm('Remover?')">Remover</a></td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>
</body>
</html>