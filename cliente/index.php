<?php
ob_start();
session_start();
$_SESSION = array(); // Clears the $_SESSION variable
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>Painel de Usuário</title>
    <link rel="stylesheet" href="home.css">
</head>
<body>
<div id="container">
	<p id="chamada">
		Portabilidade e automação de serviços do PCM são a marca da nova geração do SIGMA ANDROID.
	</p>
	<a id="home" href="../index.php">Voltar para o site</a>
	<div id="form">
		<form action="" method="post" id="form_login">
			
			<p style="font-size: 18px;">Entre na sua conta!</p>
			
			<span class="login">Usuário</span>:<br>
			<input type="text" name="login" id="login" maxlength="50" class="block"><br>
	
			<span class="senha">Senha</span>:<br/>
			<input type="password" name="senha" id="senha" maxlength="50" class="block">
	
			<button type="submit">Entrar</button>
			
			<p>ou <a id="a-cadastro" href="../cadastro.php">Cadastre-se!</a></p>
		</form>
	</div>

<?php


// Verifica se um formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// Salva duas variáveis com o que foi digitado no formulário
	// Detalhe: faz uma verificação com isset() pra saber se o campo foi preenchido
	$user = (isset($_POST['login'])) ? $_POST['login'] : '';
	$password = (isset($_POST['senha'])) ? $_POST['senha'] : '';

	$db = new PDO('mysql:host=mysql.centralsigma.com.br;dbname=mobile_provider',
                	'webadmin', 'webADMIN',
                	array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        $sql = "select email, senha from clientes where email = :user";
        $query = $db->prepare($sql);
        $query->execute(array(':user' => $user));
        $res = $query->fetch();

        $usuario = $res['email'];
        $senha = $res['senha'];

	if ($user == $usuario && md5($password) == $senha) {
		session_start();
		$_SESSION['487usuario'] = true;
                $_SESSION['487name'] = $usuario;

		// O usuário e a senha digitados foram validados, manda pra página interna
		header("Location: comprar.php");
	} else {
		// O usuário e/ou a senha são inválidos, manda de volta pro form de login
		echo '<div id="erro">
				Usuário ou senha inválidos.
			</div>';
	}

}
?>

</div>
</body>
</html>
