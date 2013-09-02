<?php

require_once 'MPDatabase.php';

class MPLicense {
    
    private static $client_info;
    private static $ref_code;
    private static $payment_success;
    private static $phone;
    private static $remaining_days;
	private static $currentMaxUsers;
    
    public static function getClient($email, $app) {
		$conn = MPDatabase::db();
		$sql = "select c.id as cliente_id, c.empresa, c.cnpj, c.telefone, c.email, 
			a.id as cap_id, l.id as id_licenca 
						from clientes as c
						left join clientes_aplicativos as a on a.cliente_id = c.id
						left join licencas as l on l.cap_id = a.id
						where c.email = ?
						and a.aplicativo = ?";
		$query = $conn->prepare($sql);
		$query->execute(array($email, $app));
			$res = $query->fetch();

		//Se há id de licença retorna TRUE
		$licenca_instalacao = ($res['id_licenca'] !== null) ? 1 : 0;

		return self::$client_info = array(
			'cap_id' => $res['cap_id'],
			'empresa' => $res['empresa'],
			'cnpj' => $res['cnpj'],
			'telefone' => $res['telefone'],
			'email' => $res['email'],
			'licenca_instalacao' => $licenca_instalacao
		);
    }
    
    public static function setCode($size = 8, $lower = true, $capitals = true, $numbers = true, $symbols = false) {
		$lmin = 'abcdefghijklmnopqrstuvwxyz';
		$lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$num = '1234567890';
		$symb = '!@#$%*-';
		$return = 'REF'; //code begins with REF
		$chars = '';

		if ($lower) $chars .= $lmin;
		if ($capitals) $chars .= $lmai;
		if ($numbers) $chars .= $num;
		if ($symbols) $chars .= $symb;

		$len = strlen($chars);
		for ($n = 1; $n <= $size; $n++) {
			$rand = mt_rand(1, $len);
			$return .= $chars[$rand-1];
		}

		return self::$ref_code = $return;
	}
     
    public static function registerPayment($ref_code, $cap_id, $item1, $valor1, $item2, $quantidade2, $valor2, $valor_total) {
		$conn = MPDatabase::db();
		$sql = "insert into pagamento_pagseguro 
			(cap_id, ref_code, item1, valor1, item2, quantidade2, valor2, total, status_pagamento)
			values (:cap_id, :ref_code, :item1, :valor1, :item2, :quantidade2, :valor2, :total, 'Pendente')";
		$query = $conn->prepare($sql);
		$success = $query->execute(array(
			':ref_code' => $ref_code,
			':cap_id' => $cap_id,
			':item1' => $item1,
			':valor1' => $valor1,
			':item2' => $item2,
			':quantidade2' => $quantidade2,
			':valor2' => $valor2,
			':total' => $valor_total
		));

		return self::$payment_success = $success;
	 
    }
    
    public static function decode_telefone($telefone){
		$telefone = trim($telefone);
		if($telefone=="") return "";
		$nums = "0123456789";

		$numsarr = str_split($nums);
		$telsarr = str_split($telefone);

		$novo_telefone = "";

		foreach($telsarr as $tel){
			$ex = false;
			foreach($numsarr as $num){
				if($tel == $num){
					$ex = true;
					break;
				}
			}

			if($ex) $novo_telefone .= $tel;
		}

		return self::$phone = $novo_telefone;
    }
    
    public static function registerLicense($ref_code) {
		$conn = MPDatabase::db();
		$sql = "select p.cap_id, 
			if (p.item2='Licença por Usuário', p.quantidade2, null) as quantidade,
			if (l.id, true, false) as licenca_instalacao
			from pagamento_pagseguro as p
			left join licencas as l on l.cap_id = p.cap_id
			where p.ref_code = :ref_code";
			$query = $conn->prepare($sql);
			$query->execute(array(':ref_code' => $ref_code));
			$resultado = $query->fetch();

		$cap_id = $resultado['cap_id'];
			$quantidade = $resultado['quantidade'];
		$licenca_instalacao = $resultado['licenca_instalacao']; //0 ou 1

        if($licenca_instalacao == 1) {
            $sql2 = "update licencas set dias_licenca = '30', data_fim = date_add(current_date, interval 30 day),
             max_usuarios = :quantidade where cap_id = :cap_id";
            $query2 = $conn->prepare($sql2);
            $query2->bindValue(':cap_id', $cap_id, PDO::PARAM_INT);
            $query2->bindValue(':quantidade', $quantidade, PDO::PARAM_INT);
            $query2->execute();
        }else {
            $sql2 = "insert into licencas (cap_id, dias_licenca, data_fim, max_usuarios) 
            values (:cap_id, '30', date_add(current_date, interval 30 day), :quantidade)";
            $query2 = $conn->prepare($sql2);
            $query2->bindValue(':cap_id', $cap_id, PDO::PARAM_INT);
            $query2->bindValue(':quantidade', $quantidade, PDO::PARAM_INT);
            $query2->execute();
        }

        $sql3 = "update pagamento_pagseguro set stuatus_pagamento = 'Completo' where cap_id = :cap_id";
        $query3 = $conn->prepare($sql3);
        $query3->bindValue(':cap_id', $cap_id, PDO::PARAM_INT);
        $query3->execute();
    }
    
    public static function getRemainingDays($cap_id) {
		$conn = MPDatabase::db();
		$query = $conn->prepare("select datediff(data_fim, current_date) as remaining from licencas where cap_id = :cap_id");
		$query->execute(array(':cap_id' => $cap_id));
		if ($query->rowCount() > 0) {
			return self::$remaining_days = (int) $query->fetchColumn();
		}else {
			return self::$remaining_days = 0;	
		}
    }
	
	public static function getCurrentMaxUsers($cap_id) {
		$conn = MPDatabase::db();
		$query = $conn->prepare("select max_usuarios from licencas where cap_id = :cap_id");
		$query->execute(array(':cap_id' => $cap_id));
		if ($query->rowCount() > 0) {
			$users = $query->fetchColumn();
			
			if (is_null($users)) return self::$currentMaxUsers = 'Ilimitada';
			else return self::$currentMaxUsers = (int) $users;
		}else {
			return self::$currentMaxUsers = 0;	
		}
	}

}
