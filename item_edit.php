<?php
require 'config.php';
$uid = 1; // user_id padrão (sem autenticação)
$err='';

// delete
if (isset($_GET['action']) && $_GET['action']==='delete' && isset($_GET['id'])){
    $id = (int)$_GET['id'];
    $mysqli->query('DELETE FROM items WHERE id='.$id.' AND created_by='.$uid);
    header('Location: items.php'); exit;
}

// load for edit
$item = ['id'=>0,'title'=>'','description'=>''];
if (isset($_GET['id'])){
    $id = (int)$_GET['id'];
    $res = $mysqli->query('SELECT * FROM items WHERE id='.$id.' AND created_by='.$uid.' LIMIT 1');
    if ($r = $res->fetch_assoc()) $item = $r;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $title = $_POST['title'] ?? '';
    $desc = $_POST['description'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    if ($id>0){
        $stmt = $mysqli->prepare('UPDATE items SET title=?, description=? WHERE id=? AND created_by=?');
        $stmt->bind_param('ssii',$title,$desc,$id,$uid);
        $stmt->execute();
    } else {
        $stmt = $mysqli->prepare('INSERT INTO items (title,description,created_by) VALUES (?,?,?)');
        $stmt->bind_param('ssi',$title,$desc,$uid);
        $stmt->execute();
    }
    header('Location: items.php'); exit;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $item['id'] ? 'Editar' : 'Criar'; ?> Item</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
  <h1><?php echo $item['id'] ? 'Editar' : 'Criar'; ?> Item</h1>
  <form method="post">
    <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
    <label>Título<br><input type="text" name="title" value="<?php echo esc($item['title']); ?>" required></label><br>
    <label>Descrição<br><textarea name="description"><?php echo esc($item['description']); ?></textarea></label><br>
    <button type="submit"><?php echo $item['id'] ? 'Salvar' : 'Criar'; ?></button>
  </form>
  <p><a href="items.php">Voltar</a></p>
</div>
</body>
</html>