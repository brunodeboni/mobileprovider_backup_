<?php
require_once '../apis/google-api-php-client/src/Google_Client.php';
require_once '../apis/google-api-php-client/src/contrib/Google_FusiontablesService.php';

/* Define all constants */
$CLIENT_ID = '1025745161710-4lhurfmujg78vpat56roqs0rocr8oeek.apps.googleusercontent.com';
$FT_SCOPE = 'https://www.googleapis.com/auth/fusiontables';
$SERVICE_ACCOUNT_NAME = '1025745161710-4lhurfmujg78vpat56roqs0rocr8oeek@developer.gserviceaccount.com';
$KEY_FILE = '../apis/google-api-php-client/b3785deabaa62a966a30d5e0e10be89111d5d44a-privatekey.p12';

$client = new Google_Client();
$client->setApplicationName("GFTPrototype");
$client->setClientId($CLIENT_ID);

//add key
$key = file_get_contents($KEY_FILE);
$client->setAssertionCredentials(new Google_AssertionCredentials(
    $SERVICE_ACCOUNT_NAME,
    array($FT_SCOPE),
    $key)
);


$service = new Google_FusiontablesService($client);

$id = $_POST['id'];
$array = explode('-', $id);
$cliente_id = $array[0];
//$aplicativo = $array[1];

//Pega dados do usuário
require '../classes/MPDatabase.php';
$db = MPDatabase::db();

$sql = "select empresa, endereco, cidade, uf, telefone, email, subdominio
 from clientes where id = :cliente_id";
$query = $db->prepare($sql);
$query->execute(array(':cliente_id' => $cliente_id));
$res = $query->fetch();


	$endereco = $res['endereco'].', '.$res['cidade'].', '.$res['uf'];
	
	//Primeiro verifica se este perfil já está cadastrado
	$select = "select cliente_id from 13Ozdr2Yqo5aZXuZtcSMw-C9rFwNriwEcnZ2kqGo";
	$result = $service->query->sql($select);
		
	foreach ($result['rows'] as $key => $resu) {
		if ($resu[0] == $cliente_id) {
			$ja_tem = true;
			continue;
		}else {
			$ja_tem = false;
		}	
	}
	
	//Depois insere ou atualiza registro
	if (! $ja_tem) {
		$insertQuery = "insert into 13Ozdr2Yqo5aZXuZtcSMw-C9rFwNriwEcnZ2kqGo (Empresa, cliente_id, Endereco, Telefone, Email, Subdominio) values ('".$res['empresa']."', '".$cliente_id."', '".$endereco."', '".$res['telefone']."', '".$res['email']."', '".$res['subdominio']."')";
		$service->query->sql($insertQuery);
		echo 'true';
	}else {
		$findRowid = "select rowid from 13Ozdr2Yqo5aZXuZtcSMw-C9rFwNriwEcnZ2kqGo where cliente_id = '".$cliente_id."'";
		$resultado = $service->query->sql($findRowid);
		$rowid = $resultado["rows"][0][0];
		
		$updateQuery = "update 13Ozdr2Yqo5aZXuZtcSMw-C9rFwNriwEcnZ2kqGo set Empresa = '".$res['empresa']."', Endereco = '".$endereco."', Telefone = '".$res['telefone']."', Email = '".$res['email']."', Subdominio = '".$res['subdominio']."' where rowid = '".$rowid."'";
		$service->query->sql($updateQuery);
	}

?>