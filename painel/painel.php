<?php
session_start();
if(!isset($_SESSION['487usuario'])) {
	die("<strong>Acesso Negado!</strong>");
}
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>Painel de Controle</title>
	<link rel="stylesheet" href="default.css">
	<style>
	#container {
		width: 300px;
	}
	</style>
</head>
<body>
<div id="container">
	<h1>Painel de Controle</h1>
	<table>
		<tr class="firstline">
			<td><a href="aprova_cadastro.php">Aprovar Cadastro</a></td>
		</tr>
		<tr class="secondline">
			<td><a href="comprar_licenca.php">Compra de Licença</a></td>
		</tr>
	</table>
</div>
</body>
</html>
