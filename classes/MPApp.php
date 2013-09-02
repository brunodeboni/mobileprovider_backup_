<?php
//defined('_MPClient') or die('');
require_once 'MPClient.php';

class MPApp
{
	private static $conn;
	
	public static function apply($cliente_id, $aplicativo)
	{
		if(!self::$conn) {
			self::$conn = MPDatabase::db();
		}
		
		$method = strtolower("app_$aplicativo");
		
		if(!method_exists(get_class(),$method)) {
			return MPClient::retorno(false, "O aplicativo '$aplicativo' não existe ou não está habilitado para inscrição");
		}
		
		if(!self::_canApply($cliente_id, $aplicativo)) {
			return MPClient::retorno(false, "Cliente já possui este aplicativo");
		}
		
		return call_user_func(array(self, $method), $cliente_id);
	}
	
	private static function _canApply($cliente_id, $aplicativo)
	{
		$sql = 'select count(*) from clientes_aplicativos where cliente_id = ? and aplicativo = ?';
		$qry = self::$conn->prepare($sql);
		$qry->execute(array($cliente_id, $aplicativo));
		return !((boolean)$qry->fetchColumn());
	}
	
	private static function _applyUser($cliente_id, $subdominio, $aplicativo, $db_info)
	{
		$sql = 'insert into clientes_aplicativos
					(cliente_id, aplicativo, subdominio, db_driver, db_username, db_password, db_host, db_name, db_url, db_debug)
				values
					(:cliente_id,:aplicativo,:subdominio,:db_driver,:db_username,:db_password,:db_host,:db_name,:db_url,:db_debug)';
		
		$gd = function($db_key, $default_value='') use ($db_info) {
			return isset($db_info[$db_key])?$db_info[$db_key]:$default_value;
		};
		
		$arguments = array(
			':cliente_id' => $cliente_id,
			':aplicativo' => $aplicativo,
			':subdominio' => $subdominio,
			':db_driver' => $subdominio,
			':db_username' => $gd('username'),
			':db_password' => $gd('password'),
			':db_host' => $gd('host'),
			':db_name' => $gd('name'),
			':db_url' => $gd('url'),
			':db_debug' => $gd('debug',true)
		);
		
		$qry = self::$conn->prepare($sql);
		if(!$qry->execute($arguments)) {
			return false;
		};
		
		$sqlu = 'insert into usuarios (cliente_id, email, password) values (
					:cid,
					(select email from clientes where id = :cid),
					(select senha from clientes where id = :cid)
				)';
		$qryu = self::$conn->prepare($sqlu);
		return $qryu->execute(array(':cid' => $cliente_id));
	}
	
	private static function getAdminInfo($cliente_id) {
		$sql = 'select nome, email, senha from clientes where id = ?';
		$qry = self::$conn->prepare($sql);
		$qry->execute(array($cliente_id));
		return $qry->fetch();
	}
	
	private static function app_sigmaandroid($cliente_id)
	{
		$aplicativo = 'sigmaandroid';
		/*
		$conn = MPDatabase::db();
		$query_app = $conn->prepare("insert into clientes_aplicativos (cliente_id, aplicativo, aprovado) 
				values (?, 'sigmaandroid', 0");
		
		if(!$query_app->execute(array($cliente_id))) {
		*/
		$subdominio = MPClient::getSubdominio($cliente_id);
		if(!$subdominio) {
			return MPClient::retorno(false, "Cliente inválido");
		}
		
		// Diretório onde ficam as databases
		$newfile_dir = realpath(__DIR__ . '/../databases/'.$aplicativo);
		if(!$newfile_dir) {
			return MPClient::retorno(false, 'Não foi possível encontrar o caminho para salvar o banco de dados');
		}
		
		// Copia arquivo do banco
		$oldfile_name = $newfile_dir . '/BANCO_DEFAULT.FDB';
		$newfile_name = $newfile_dir . '/'.strtoupper($subdominio).'.FDB';
		if(!file_exists($oldfile_name)) {
			return MPClient::retorno(false, 'Não é possível localizar o banco de dados para copiar... ['.$oldfile_name.']');
		}
		$nfi = 1;
		while(file_exists($newfile_name)) {
			$newfile_name = $newfile_dir . '/'.strtoupper($subdominio)."_{$nfi}.FDB";
			$nfi++;
		}
		$newfile = copy($oldfile_name, $newfile_name);
		if (!$newfile) {
			return MPClient::retorno(false, 'Ocorreu um erro ao criar banco de dados');
		}
		
		// Dados de conexão do banco de dados
		$db_info = array(
			'driver' => 'firebird',
			'username' => 'SYSDBA',
			'password' => 'masterkey',
			'host' => 'localhost',
			'url' => realpath($newfile_name),
		);
		
		// Insere dados na tabela 
		if(!self::_applyUser($cliente_id, $subdominio, $aplicativo, $db_info)) {
			return MPClient::retorno(false, 'Ocorreu um erro ao vincular cliente com aplicativo');
		}
		
		/*
		// Cria usuário novo automaticamente
		require_once __DIR__ . '/../../mobileprovider_api/_config.php';
		require_once __DIR__ . '/../../mobileprovider_api/api/application/config/database.php';
		require_once __DIR__ . '/../../mobileprovider_api/api/application/config/auth.php';
		$adminData = self::getAdminInfo($cliente_id);
		
		\Sigmarest\Config\Auth::instance()->cadastrarUsuario(array(
			'nome' => $adminData['nome'],
			'email' => $adminData['email'],
			'senha' => $adminData['senha'],
		));
		*/
		
		return MPClient::retorno(true, '');
	}
}

/*
echo 'teste: ';
die(json_encode(MPApp::apply(1390, 'sigmaandroid')));
*/

