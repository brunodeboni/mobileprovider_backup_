<?php
define('_MPClient',1);
require_once 'MPDatabase.php';

class MPClient {
    
    private static $id_client;
    private static $db_info;
	
	/**
	 * Retorno personalizado
	 */
	public static function retorno($status, $message) {
		return array(
			'success' => (boolean)$status,
			'message' => $message
		);
	}
    
	/**
	 * Verifica se já existe um subdomínio para este aplicativo
	 */
    public static function subdomain_exists($subdomain, $application) {
        $conn = MPDatabase::db();
        $query = $conn->prepare('select subdominio from subdominios where subdominio = ? and aplicativo = ?');
        $query->execute(array($subdomain, $application));
        
		//se já houver o registro, retorna TRUE
		//se não houver o registro, retorna FALSE
        return ($query->rowCount() > 0);
    }
	
	/**
	 * Verifica se já existe um usuário utilizando este subdomínio
	 */
	public static function checkSubdomain($subdomain) {
		$conn = MPDatabase::db();
		$query = $conn->prepare('select count(*) from clientes where subdominio = ?');
		$query->execute(array($subdomain));
		return (boolean) $query->fetchColumn();
	}
	
	/**
	 * Retorna o subdomínio de um usuário
	 */
	public static function getSubdominio($cliente_id) {
		$conn = MPDatabase::db();
		$query = $conn->prepare('select subdominio from clientes where id = ?');
		$query->execute(array($cliente_id));
		return $query->fetchColumn();
	}
	
	/**
	 * Cadastra uma nova empresa
	 */
    public static function register($empresa, $cnpj, $endereco, $complemento, $cidade, $uf, $telefone, $celular, $email, $senha, $subdomain) {
        //Verifica se subdomínio já existe
        if (self::checkSubdomain($subdomain)) {
            return self::retorno(false, 'Este subdomínio já esta cadastrado');
	}
		
        // Cadastra os dados do cliente no banco de dados
        $conn = MPDatabase::db();
        $query = $conn->prepare('insert into clientes 
                (empresa, cnpj, endereco, complemento, cidade, uf, telefone, celular, email, senha, subdominio) 
                values (:empresa, :cnpj, :endereco, :complemento, :cidade, :uf, :telefone, :celular, :email, :senha, :subdominio)');
        $query->bindValue(':empresa', $empresa, PDO::PARAM_STR);
        $query->bindValue(':cnpj', $cnpj, PDO::PARAM_STR);
        $query->bindValue(':endereco', $endereco, PDO::PARAM_STR);
        $query->bindValue(':complemento', $complemento, PDO::PARAM_STR);
        $query->bindValue(':cidade', $cidade, PDO::PARAM_STR);
        $query->bindValue(':uf', $uf, PDO::PARAM_STR);
        $query->bindValue(':telefone', $telefone, PDO::PARAM_STR);
        $query->bindValue(':celular', $celular, PDO::PARAM_STR);
        $query->bindValue(':email', $email, PDO::PARAM_STR);
        $query->bindValue(':senha', $senha, PDO::PARAM_STR);
        $query->bindValue(':subdominio', $subdomain, PDO::PARAM_STR);
        $success = $query->execute();
        self::$id_client = $conn->lastInsertId();

        if ($success) {
            return self::retorno(true, self::$id_client);
        }

        return self::retorno(false, 'Ocorreu um erro ao registrar seu cadastro');
    }
    
	/*
    public static function createDB($id_client, $subdomain) {
        
        // Define um nome
		$newfile_dir = realpath(__DIR__ . '/../databases')
		if(!$newfile_dir) {
			return self::retorno(false, 'Não foi possível encontrar o caminho para salvar o banco de dados');
		}
		$newfile_name = $newfile_dir . "/$subdomain.FDB";
        
        // Copia arquivo do banco
        $newfile = copy('BANCO_DEFAULT.FDB', $newfile_name);
        
        if (!$newfile) {
			return self::retorno(false, 'Ocorreu um erro ao criar banco de dados');
		}
		
		//Insere dados de conexão com o novo banco na tabela subdomínios
		$db_driver = 'firebird';
		$db_username = 'SYSDBA';
		$db_password = 'masterkey';
		$db_host = 'localhost';
		$db_name = '';
		$db_url = realpath($newfile_name);

		$conn = MPDatabase::db();
		$query = $conn->prepare('insert into subdominios 
			(subdominio, db_driver, db_username, db_password, db_host, db_name, db_url, db_debug) 
			values (:subdominio, :db_driver, :db_username, :db_password, :db_host, :db_name, :db_url, 1)');
		$query->bindValue(':subdominio', $subdomain, PDO::PARAM_STR);
		$query->bindValue(':db_driver', $db_driver, PDO::PARAM_STR);
		$query->bindValue(':db_username', $db_username, PDO::PARAM_STR);
		$query->bindValue(':db_password', $db_password, PDO::PARAM_STR);
		$query->bindValue(':db_host', $db_host, PDO::PARAM_STR);
		$query->bindValue(':db_name', $db_name, PDO::PARAM_STR);
		$query->bindValue(':db_url', $db_url, PDO::PARAM_STR);
		$success = $query->execute();

		self::$db_info = array(
							'db_driver' => $db_driver, 
							'db_username' => $db_username, 
							'db_password' => $db_password, 
							'db_host' => $db_host, 
							'db_name' => $db_name, 
							'db_url' => $db_url);

		if ($success) {
			$query2 = $conn->prepare('update cadastro set subdominio = :subdominio where id = :id_client');
			$query2->bindValue(':subdominio', $subdomain, PDO::PARAM_STR);
			$query2->bindValue(':id_client', $id_client, PDO::PARAM_INT);
			$query2->execute();
			
			return self::$db_info;
		}else return 'Ocorreu um erro ao cadastrar as informações do banco de dados recém criado.';
    }
    
    function applyToApp($client_id) {
        //Por enquanto, apenas SIGMA ANDROID
        $conn = MPDatabase::db();
        $query_app = $conn->prepare("insert into clientes_aplicativos (cliente_id, aplicativo, aprovado) 
                    values (:cliente_id, 'sigmaandroid', 0");
        $query_app->bindValue(':cliente_id', $client_id, PDO::PARAM_INT);
        $success = $query_app->execute();
        
        if ($success) return true;
        else return false;
    }
	*/
}
