<?php
require 'config.php';
$uid = 1; // user_id padrão (sem autenticação)

// deletar ficha (usar nova tabela `transporte_doente_critico`)
if (isset($_GET['id'])){
  $id = (int)$_GET['id'];
  $mysqli->query('DELETE FROM transporte_doente_critico WHERE id_tdc='.$id.' AND created_by='.$uid);
}

header('Location: tdc_list.php');
exit;
?>