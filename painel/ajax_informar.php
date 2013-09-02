<?php
	
	$id = $_POST['id'];
	$array = explode('-', $id);
	$empresa = $array[0];
	$subdominio = $array[1];
        $aplicativo = $array[2];
	
	$assunto = "Cadastro de empresa aprovado";
	
	$mensagem = "
	Cadastro da empresa $empresa aprovado para o aplicativo $aplicativo.<br>
	Subdomínio escolhido: $subdominio";

	$destinatario = "analise@redeindustrial.com.br";
	
	// To send HTML mail, the Content-type header must be set
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
	$headers .= 'From: brunodeboni@gmail.com '. "\r\n";
	$headers .= 'CC: <henriquesschmitt@gmail.com>'."\r\n";
	$headers .= 'CC: <brunodeboni@gmail.com>'."\r\n";
	
	$enviar = mail($destinatario, $assunto, $mensagem, $headers);
	
	if($enviar) echo 'true';
	else echo 'false';
	
	
?>