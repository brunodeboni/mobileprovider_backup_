<?php
            
require '../classes/MPDatabase.php';
$db = MPDatabase::db();
            
$sql = "select id, empresa from clientes where subdominio = :subdominio";
$query = $db->prepare($sql);
$query->execute(array(':subdominio' => $_POST['subdominio']));
$res = $query->fetch();

$resultado['cliente_id'] = $res['id'];
$resultado['empresa'] = $res['empresa'];

echo json_encode($resultado);       
?>
