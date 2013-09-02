<?php
ob_start();
session_start();
if(!isset($_SESSION['487usuario'])) die("<strong>Acesso Negado!</strong>");
?>
<!doctype html>
<html ng-app>
<head>
    <meta charset="utf-8">
    <title>Mobile Provider - Painel de Licenças</title>
    <link rel="stylesheet" href="default.css">
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.0.7/angular.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <style>
    	#container {
            width: 900px;
            background: #F6F7F8;
       	}
    </style>
</head>
<body>
    <div id="container">
        <h1>Compra de Licenças</h1>
        <!-- Declaração do formulário -->
        <form id="form_comprar" method="post" action="" ng-controller="Comprar">
            <p>Escolha abaixo entre os diferentes tipos de licença SIGMA Android e faça o pagamento usando o PagSeguro do UOL:</p><br>

            <?php
            $user = $_SESSION['487name']; //E-mail do cliente
            
            require_once '../classes/MPLicense.php';
            $client = MPLIcense::getClient($user, $_GET['aplicativo']);
            
            $cap_id = $client['cap_id']; //id do cliente neste aplicativo
            $empresa = $client['empresa'];
            //$cnpj = $client['cnpj'];
            $telefone = $client['telefone'];
            $email = $client['email'];
            $licenca_instalacao = $client['licenca_instalacao']; //retorna 0 se não houver
            
			if ($licenca_instalacao) {
				$dias_remaining = MPLicense::getRemainingDays($cap_id);
				$currentMaxUsers = MPLicense::getCurrentMaxUsers($cap_id);
			}else {
				$dias_remaining = 0; //default
				$currentMaxUsers = 0; //default
			}
			$ref_code = MPLIcense::setCode(8, false); //8 caracteres somente letras maiúsculas e números

            ?>

            <input type="hidden" name="cap_id" value="<?php echo $cap_id; ?>">
            <input type="hidden" name="telefone" value="<?php echo $telefone; ?>"> 
            <input type="hidden" id="empresa" name="empresa" value="<?php echo $empresa; ?>">
            <input type="hidden" name="email" value="<?php echo $email; ?>">
            
            <?php if ($licenca_instalacao == 0) { ?>
				<!-- Itens do pagamento (ao menos um item é obrigatório) -->
				<input type="hidden" name="itemDescription1" ng-model="itemDescription1">
				<input type="hidden" name="itemQuantity1" value="1">
				<input type="hidden" name="itemAmount1" value="500.00">
            <?php } ?>
				
            <span>Licença:</span>
            <select id="licenca" name="licenca" class="block" ng-model="licenca">
                <option value="Licença por Usuário">Licença por Usuário</option>
                <option value="Licença Ilimitada">Licença Ilimitada</option>
            </select><br>
			<input type="hidden" name="itemDescription2" id="itemDescription2" ng-model="itemDescription2">
	    
			<span>Plano:</span>
			<select id="dias_licenca" name="dias_licenca" class="block" ng-model="dias_licenca">
				<option value="30" ng-show="dias_faltando<30">30 dias</option>
				<option value="60" ng-show="dias_faltando<60">60 dias</option>
				<option value="90" ng-show="dias_faltando<90">90 dias</option>
				<option value="180" ng-show="dias_faltando<180">180 dias</option>
				<option value="360" ng-show="dias_faltando<360">360 dias</option>
			</select><br>

			<div ng-show="licenca=='Licença por Usuário'">
				<span id="qnt">Quantidade de Usuários Permitidos:</span>
				<input type="text" name="itemQuantity2" id="quantidade" class="block" ng-model="quantidade"><br>
			</div>

			<span>Valor:</span><br>
			<div id="valor">
				<?php if (!$licenca_instalacao) { ?>
					<div id="valor-instalacao">Instalação e configuração: R$ 500,00</div><div id="mais"> + </div>	
				<?php } ?>
				<div id="valor-licenca">Licença {{msg_licenca}}: 
					R$ {{licencaUso}} (válido por {{dias_licenca}} dias)</div>
				<div id="igual"> = </div>
				<div id="valor-total">Total: R$ {{licencaTotal}}</div>
			</div>
			<input type="hidden" id="itemAmount2" name="itemAmount2" class="block" ng-model="itemAmount2"><br>
			
			<div>{{descricao}}</div>
			<br>

			<!-- Código de referência do pagamento no seu sistema (opcional) -->
			<input type="hidden" name="ref_code" value="<?php echo $ref_code; ?>">

			<!-- submit do form (obrigatório) -->
			<button id="btn">Comprar licença</button>

        </form>

<script>


$('#btn').click(function() {
    $('#form_comprar').submit();
});

function Comprar($scope) {
	
	//Define valores default
    $scope.licenca = 'Licença Ilimitada';
	$scope.msg_licenca = 'Ilimitada';
    $scope.itemDescription2 = 'Licença Ilimitada';
    $scope.itemAmount2 = '250.00';
	$scope.quantidade_old = '<?php echo $currentMaxUsers; ?>';
	$scope.quantidade = 1;
    $scope.dias_licenca = 360;
	$scope.dias_faltando = <?php echo $dias_remaining; ?>; //Dias faltando para o fim da licença
    if ($scope.dias_faltando < 0) $scope.dias_faltando = 0;
	$scope.licencaUso = '0,00';
    $scope.licencaInstalacao = <?php echo $licenca_instalacao; ?>; //0 ou 1
    if ($scope.licencaInstalacao === 0) {
		$scope.itemDescription1 = 'Licença de Instalação e Configuração';
		$scope.itemAmount1 = '500.00';
    }else {
		$scope.itemDescription1 = null;
		$scope.itemAmount1 = null;
    }
	
	
	//Função que calcula o valor das licenças
    $scope.calculaLicenca = function() {
		
		if ($scope.licenca === "Licença por Usuário") {
			
			if ($scope.quantidade_old === 'Ilimitada') {
				$scope.descricao = 'Você possui licença Ilimitada por mais '+ $scope.dias_faltando +' dia(s). \n\
				A data final de sua licença será atualizada para '+ $scope.dias_licenca +' dias a partir da confirmação do pagamento. \n\
				A quantidade máxima de usuários permitidos será substituída para '+ $scope.quantidade +' usuário(s).';
			}else if ($scope.quantidade_old > 0) {
				$scope.descricao = 'Você possui licença para '+ $scope.quantidade_old +' usuário(s) por mais '+ $scope.dias_faltando +' dia(s). \n\
				A data final de sua licença será atualizada para '+ $scope.dias_licenca +' dias a partir da confirmação do pagamento. \n\
				A quantidade máxima de usuários permitidos será substituída para '+ $scope.quantidade +' usuário(s).';
			}else {
				$scope.descricao = 'A data final de sua licença será de '+ $scope.dias_licenca +' dias a partir da confirmação do pagamento. \n\
				A quantidade máxima de usuários permitidos será de '+ $scope.quantidade +' usuário(s).';
			}
			
			$scope.itemDescription2 = 'Licença para '+ $scope.quantidade +' Usuário(s) (válido por '+ $scope.dias_licenca +' dias)';
			$scope.msg_licenca = 'para '+ $scope.quantidade +' Usuário(s)';
			
			var valor_licenca_usuario = 5; //Valor da licença por usuário
			var valor_licenca_usuario_dia = valor_licenca_usuario/30; //Valor da licença por usuário pra cada dia
			var valor_licenca_plano_usuario = $scope.dias_licenca * valor_licenca_usuario_dia; //Valor da licença para cada usuário para o total de dias
			var valor_licenca_plano_total = valor_licenca_plano_usuario * $scope.quantidade; //Valor total da licença para o plano
			var valor_licenca_total = valor_licenca_plano_total; //Valor total é o valor do plano
			
			$scope.itemAmount2 = valor_licenca_plano_usuario.toFixed(2); //É o valor da licença para cada usuário para o total de dias
			$scope.licencaUso = valor_licenca_total.toFixed(2).replace('.', ','); //É o valor total da licença

		}else {
			
			if ($scope.quantidade_old === 'Ilimitada') {
				$scope.descricao = 'Você possui licença Ilimitada por mais '+ $scope.dias_faltando +' dia(s). \n\
				A data final de sua licença será atualizada para '+ $scope.dias_licenca +' dias a partir da confirmação do pagamento.';
			}else if ($scope.quantidade_old > 0) {
				$scope.descricao = 'Você possui licença para '+ $scope.quantidade_old +' usuário(s) por mais '+ $scope.dias_faltando +' dia(s). \n\
				A data final de sua licença será atualizada para '+ $scope.dias_licenca +' dias a partir da confirmação do pagamento. \n\
				A quantidade máxima de usuários permitidos será ilimitada.';
			}else {
				$scope.descricao = 'A data final de sua licença será de '+ $scope.dias_licenca +' dias a partir da confirmação do pagamento. \n\
				A quantidade máxima de usuários permitidos será ilimitada.';
			}
			
			$scope.itemDescription2 = 'Licença Ilimitada (válido por '+ $scope.dias_licenca +' dias)';
			$scope.quantidade = 1;
			$scope.msg_licenca = 'Ilimitada';
			
			var valor_licenca_ilimitada = 250;
			var valor_licenca_dia = valor_licenca_ilimitada / 30; //Valor da licença por dia
			var valor_licenca_plano = valor_licenca_dia * $scope.dias_licenca; //Valor da licença para o total de dias do plano
			var valor_licenca_total = valor_licenca_plano; //Valor total é o valor do plano

			$scope.itemAmount2 = valor_licenca_total.toFixed(2);
			$scope.licencaUso = valor_licenca_total.toFixed(2).replace('.', ',');

		}

		if ($scope.licencaInstalacao === 0) {
			$scope.licencaTotal = (parseFloat(valor_licenca_total) + 500)+',00'; //Valor das duas licenças somadas
		}else {
			$scope.licencaTotal = $scope.licencaUso.replace('.', ',');
		}
    };
	
    $scope.$watch('licenca', $scope.calculaLicenca);
    $scope.$watch('dias_licenca', $scope.calculaLicenca);
    $scope.$watch('quantidade', $scope.calculaLicenca);
	 
}

</script>
<?php

if (isset($_POST['ref_code'])) {
    
    $valor_total = $_POST['itemAmount2'] * $_POST['itemQuantity2'];
    
    if(isset($_POST['itemDescription1'])) {
        $item1 = $_POST['itemDescription1'];
        $valor1 = $_POST['itemAmount1'];
        $valor_total = $valor1 + $valor_total; //Substitui valor definido acima
    }else {
        $item1 = null;
        $valor1 = null;
    }
	
    $valor_total .= '.00';
    
    $success = MPLicense::registerPayment(
		$_POST['ref_code'],
		$_POST['cap_id'],
		$item1,
		$valor1,
		$_POST['itemDescription2'],
		$_POST['itemQuantity2'],
		$_POST['itemAmount2'],
		$valor_total
    );

    if ($success) {
        require_once "../libraries/PagSeguroLibrary/PagSeguroLibrary.php";

        // Instantiate a new payment request
        $paymentRequest = new PagSeguroPaymentRequest();

        // Sets the currency
        $paymentRequest->setCurrency("BRL");

        // Add an item for this payment request
        //$paymentRequest->addItem('0001', 'Notebook prata', 2, 430.00);
        if (isset($_POST['itemDescription1'])) {
            $paymentRequest->addItem('001', $_POST['itemDescription1'], $_POST['itemQuantity1'], $_POST['itemAmount1']);
        }
        $paymentRequest->addItem('002', $_POST['itemDescription2'], $_POST['itemQuantity2'], $_POST['itemAmount2']);

        // Sets a reference code for this payment request, it is useful to identify this payment in future notifications.
        $paymentRequest->setReference($_POST['ref_code']);

        // Sets your customer information.
        //$paymentRequest->setSender('JoÃƒÂ£o Comprador', 'comprador@s2it.com.br', '11', '56273440', 'CPF', '156.009.442-76');
		$telefone = MPLicense::decode_telefone($_POST['telefone']);
		$ddd_cliente = substr($telefone, 0, 2);
		$telefone_cliente = substr($telefone, 2);
        $paymentRequest->setSender($_POST['empresa'], $_POST['email'], $ddd_cliente, $telefone_cliente);

        // Sets the url used by PagSeguro for redirect user after ends checkout process
        $paymentRequest->setRedirectUrl("http://www.mobileprovider.com.br/cliente/obrigado.php");

        
        try {
	    /*
	    * #### Crendencials #####
	    * Substitute the parameters below with your credentials (e-mail and token)
	    * You can also get your credentails from a config file. See an example:
	    * $credentials = PagSeguroConfig::getAccountCredentials();
	     */
	    $credentials = new PagSeguroAccountCredentials("carolina@redeindustrial.com.br", "F3696EBCE67F4F5F8E7226E2FCDA2B39");
	    // Register this payment request in PagSeguro, to obtain the payment URL for redirect your customer.
	    $url = $paymentRequest->register($credentials);
	    
	    //URL para pagamento no PagSeguro
	    if ($url) {
            	//echo "<p><a href=\"$url\"><img src=\"https://p.simg.uol.com.br/out/pagseguro/i/botoes/pagamentos/209x48-comprar-laranja-assina.gif\"></a></p>";
            	header ("Location: $url");
	    }

        }catch (PagSeguroServiceException $e) {
	    die($e->getMessage());
        }
   
    }else {
        echo '<div id="erro">Ocorreu um erro ao gerar seu pagamento. Por favor, tente novamente.</div>';
    }
}
        
        
?>
    </div>

</body> 
</html>