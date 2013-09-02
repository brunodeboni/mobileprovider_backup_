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
	<title>Aprovação de Cadastros</title>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<link rel="stylesheet" href="default.css">
	<style>
	#container {
		width: 900px;	
	}
	.tab_wrapper {
		overflow: auto;
		width: 100%;
	}
	.tab_wrapper > h2{
		display:block;
		margin: 3px;
		padding:10px;
		background: #E37002;
		font-size: 14px;
		color:#FFF;
		-webkit-border-radius: 5px;
	    border-radius: 5px;
	}
	.tab_wrapper > h2 > span {
		float: right;
		margin-right: 10px;
	}
	.tab_wrapper > h2 > span > a {
		color: #FFF;
		text-decoration: none;
		cursor: pointer;
	}
	#load {
		display: none;
		z-index: 5;
		position: fixed;
		top: 0;
		left: 0;
		bottom: 0;
		right: 0;
		width: 100%;
		height: 100%;
		background: rgba( 0, 0, 0, 0.8 );
		color: #FFF;
		padding: 200px 45%;
	}
	table{
		margin-bottom:5px;
		width:100%;
		border-collapse:collapse;
		border-bottom:2px solid #6AC334;
		-webkit-border-radius: 5px;
	    border-radius: 5px;
	}
	table th, table td{
		padding:10px;
		text-align:right;
		font-weight: bold;
	}
	.firstline {background-color: #FAEBD7; width: 100%;}
	.secondline {background-color: #FAF0E6; width: 100%;}
	.aplicativo {color: #E37002;}
	</style>
</head>
<body>
<div id="container">
	<div id="load">Carregando...</div>
	<h1>Cadastros Pendentes</h1>
<?php 
	require '../classes/MPDatabase.php';
	$db = MPDatabase::db();
    
    $sql = "select c.id, c.empresa, c.cnpj, c.endereco, c.email, c.complemento, c.cidade, c.uf, 
    c.telefone, c.celular, c.email, c.subdominio, a.aplicativo
    from clientes as c
    left join clientes_aplicativos as a on a.cliente_id = c.id
    where a.aprovado = 0";
    $query = $db->query($sql);
    $result = $query->fetchAll();
    
    foreach ($result as $res) {
		echo '<div class="tab_wrapper" id="'.$res['empresa']."-".$res['subdominio']."-".$res['aplicativo'].'">
		<h2>
			'.$res['empresa'].'
			<span>
				<a id="'.$res['id']."-".$res['aplicativo'].'" class="recusar">Recusar</a> |
				<a id="'.$res['id']."-".$res['aplicativo'].'" class="aprovar">Aprovar</a>
			</span>
		</h2>
		
	<table>
		<tr class="firstline">
			<td>Empresa</td>
			<td>'.$res['empresa'].'</td>
		</tr>
		<tr class="secondline">
			<td>CNPJ</td>
			<td>'.$res['cnpj'].'</td>
		</tr>
		<tr class="firstline">
			<td>Endereço/Cidade</td>
			<td>'.$res['endereco'].' '.$res['complemento'].' - '.$res['cidade'].'/'.$res['uf'].'</td>
		</tr>
		<tr class="secondline">
			<td>Telefone</td>
			<td>'.$res['telefone'].'</td>
		</tr>
                <tr class="firstline">
			<td>Celular</td>
			<td>'.$res['celular'].'</td>
		</tr>
		<tr class="secondline">
			<td>E-mail</td>
			<td>'.$res['email'].'</td>
		</tr>
		<tr class="firstline">
			<td>Subdomínio</td>
			<td>'.$res['subdominio'].'</td>
		</tr>
                <tr class="secondline">
			<td>Aplicativo</td>
			<td class="aplicativo">'.$res['aplicativo'].'</td>
		</tr>
	</table></div>';

    }

?>
	<br><a href="index.php" id="sair">Sair</a><br>
</div>
<script>
$.ajaxSetup({
	beforeSend: function() {
		$('#load').show();
	}
});

$('.aprovar').click(function() {
	if (confirm('Tem certeza que deseja aprovar este cadastro?')) {
		insertMap($(this).attr('id'));
		informar($(this).parents('div').attr('id'));
		aprovar($(this).attr('id'));
	}
});

function aprovar(id) {
	$.post('ajax_aprovar.php', {id: id}, function(data) {
		if (data == 'true') {
			alert('Empresa cadastrada com sucesso.');
		}else {
			alert('Ocorreu um erro ao inserir no banco de dados. Tente novamente');
		}
		window.location.reload();
	});	
}

function informar(id) {
	$.post('ajax_informar.php', {id: id}, function(data) {
		if (data == 'true') {
			console.log('E-mail enviado com sucesso.');
		}else {
			console.log('Ocorreu um erro ao enviar e-mail para análise. Tente novamente');
		}
		//window.location.reload();
	});
}

function insertMap(id) {
	$.post('ajax_insertMap.php', {id: id}, function(data) {
		if (data == 'true') {
			console.log('Cadastro inserido no mapa.');
		}else {
			console.log('Ocorreu um erro ao inserir no mapa. Tente novamente');
		}
	});
}

$('.recusar').click(function() {
	if (confirm('Tem certeza que deseja recusar este cadastro?')) {
		recusar($(this).attr('id'));
	}
});

function recusar(id) {
	$.post('ajax_recusar.php', {id: id}, function(data) {
		if (data == 'true') {
			alert('Cadastro recusado com sucesso.');
			window.location.reload();
		}else {
			alert('Ocorreu um erro. Tente novamente');
		}

	});	
}
</script>
</body>
</html>