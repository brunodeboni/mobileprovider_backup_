<?php

if (isset ($_POST['id'])) {
	require '../classes/MPDatabase.php';
	$db = MPDatabase::db();
	
        $id = $_POST['id'];
	$array = explode('-', $id);
	$cliente_id = $array[0];
	$aplicativo = $array[1];
        
	$sql = "update clientes_aplicativos set aprovado = 2 
            where cliente_id = ? and aplicativo = ?";
	$query = $db->prepare($sql);
	$success = $query->execute(array($cliente_id, $aplicativo));
	
	if ($success) echo 'true';
	else echo 'false';
}

?>