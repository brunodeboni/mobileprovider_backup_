<?php

require '../classes/MPDatabase.php';
$db = MPDatabase::db();
            
$sql = "select aplicativo from clientes_aplicativos where cliente_id = :cliente_id";
$query = $db->prepare($sql);
$query->execute(array(':cliente_id' => $_POST['cliente_id']));
$result = $query->fetchAll();

foreach ($result as $res) {
    $aplicativos[] = $res['aplicativo'];
}
echo json_encode($aplicativos);

?>
