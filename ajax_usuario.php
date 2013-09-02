<?php

require 'classes/MPDatabase.php';
$db = MPDatabase::db();

$sql = "select id from clientes where email = :email";
$query = $db->prepare($sql);
$query->execute(array(':email' => $_POST['email']));

if ($query->rowCount() > 0) echo 'false'; //se JÁ tem usuário, retorna FALSE
else echo 'true'; //se NÃO tem usuário, retorna TRUE


?>