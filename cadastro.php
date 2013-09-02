<!DOCTYPE html>
<html ng-app>
<head>
<title>SIGMA ANDROID: Cadastro de Empresa</title>
    <meta charset="utf-8">
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.0.7/angular.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script type="text/javascript" src="plugins/jquery.maskedinput.js"></script>
    <link href="css/cadastro.css" rel="stylesheet">
</head>
<body>
    <div id="container">
        <!-- Conteúdo -->
        <div id="content">
            <!-- Formulário -->
            <div id="formulario" ng-controller="Cadastro">
                <form id="cadastro_empresa" action="" method="post">
                    <h1>Cadastre sua Empresa</h1>

                    <span>Empresa:</span>
                    <input type="text" id="empresa" name="empresa" class="block"><br>

                    <span>CNPJ:</span>
                    <input type="text" id="cnpj" name="cnpj" class="block"><br>

                    <span>Rua:</span>
                    <input type="text" id="logradouro" name="logradouro" class="block" placeholder="Ex: Avenida São João"><br>

                    <span>Número:</span>
                    <input type="text" id="nro" name="nro">

                    <span style="margin-left: 10px;">Complemento:</span>
                    <input type="text" id="complemento" name="complemento" size="50"><br><br>

                    <span>Cidade:</span>
                    <input type="text" id="cidade" name="cidade" class="block"><br>

                    <span>Estado:</span>
                    <select id="uf" name="uf" class="block">
                        <option value="">Selecione...</option>
                        <option value="AC">Acre</option>
                        <option value="AL">Alagoas</option>
                        <option value="AP">Amapá</option>
                        <option value="AM">Amazonas</option>
                        <option value="BA">Bahia</option>
                        <option value="CE">Ceará</option>
                        <option value="DF">Distrito Federal</option>
                        <option value="ES">Espírito Santo</option>
                        <option value="GO">Goiás</option>
                        <option value="MA">Maranhão</option>
                        <option value="MT">Mato Grosso</option>
                        <option value="MS">Mato Grosso do Sul</option>
                        <option value="MG">Minas Gerais</option>
                        <option value="PA">Pará</option>
                        <option value="PB">Paraíba</option>
                        <option value="PR">Paraná</option>
                        <option value="PE">Pernambuco</option>
                        <option value="PI">Piauí</option>
                        <option value="RJ">Rio de Janeiro</option>
                        <option value="RN">Rio Grande do Norte</option>
                        <option value="RS">Rio Grande do Sul</option>
                        <option value="RO">Rondônia</option>
                        <option value="RR">Roraima</option>
                        <option value="SC">Santa Catarina</option>
                        <option value="SP">São Paulo</option>
                        <option value="SE">Sergipe</option>
                        <option value="TO">Tocantins</option>
                    </select><br>

                    <span>Telefone Comercial:</span>
                    <input type="text" id="telefone" name="telefone" class="block"><br>
                    
                    <span>Celular:</span>
                    <input type="text" id="celular" name="celular" class="block"><br>
                    
                    <h1>Dados de Usuário</h1>
                    
                    <span>Nome:</span>
                    <input type="text" id="nome" name="nome" class="block"><br>
                    
                    <span>E-mail:</span> <span class="descricao">Será o usuário administrador da conta da empresa.</span>
                    <input type="text" id="email" name="email" class="block"><br>

                    <span>Senha:</span>
                    <input type="password" id="senha" name="senha" class="block"><br>

                    <span>Confirme a senha:</span>
                    <input type="password" id="confirma" name="confirma" class="block"><br>
                                  
                    <span>Subdomínio para acesso ao aplicativo: </span> <span class="descricao">Não deve conter sinais, acentos, cedilha, maiúsculas ou espaços.</span><br>
                    <input type="text" id="subdominio" name="subdominio" placeholder="nomedaempresa" ng-model="subdominio">.aplicativo.com.br<br>
                    
                    <br>
                    <div id="erro"></div>

                    <button id="btn">Enviar</button>

                </form>
				
<script>

$(document).ready(function() {
    $('#cnpj').mask('99.999.999/9999-99');
    $('#telefone').mask('(99) 9999-9999?9');
    $('#celular').mask('(99) 9999-9999?9');
    $('#nro').mask('?99999999');
});

function Cadastro($scope) {
    $scope.$watch('subdominio', function() {
        var newval = valid($scope.subdominio, 'special');
        var novoval = newval.toLowerCase();
        $scope.subdominio = novoval;
    });
}

//Subsitui caracteres especiais
var r={'special':/[\W]/g};
function valid(o,w){
    o = o.replace(r[w],'');
    return o;
}

$('#btn').click(function() {
    if ($('#empresa').val() === "") {$('#erro').show(); $('#erro').html('Por favor, informe o nome da Empresa.'); return false;}
    if ($('#cnpj').val() === "") {$('#erro').show(); $('#erro').html('Por favor, informe o CNPJ da Empresa.'); return false;}
    if (!validarCNPJ($('#cnpj').val())) {$('#erro').show(); $('#erro').html('Por favor, informe um CNPJ válido.'); return false;}
    if ($('#logradouro').val() === "") {$('#erro').show(); $('#erro').html('Por favor, informe o endereço da Empresa.'); return false;}
    if ($('#nro').val() === "") {$('#erro').show(); $('#erro').html('Por favor, informe o endereço da Empresa.'); return false;}
    if ($('#cidade').val() === "") {$('#erro').show(); $('#erro').html('Por favor, informe a cidade da Empresa.'); return false;}
    if ($('#uf').val() === "") {$('#erro').show(); $('#erro').html('Por favor, informe o estado da Empresa.'); return false;}
    if ($('#telefone').val() === "") {$('#erro').show(); $('#erro').html('Por favor, informe o telefone da Empresa.'); return false;}
    if ($('#celular').val() === "") {$('#erro').show(); $('#erro').html('Por favor, informe um número de celular.'); return false;}
    
    if ($('#nome').val() === "") {$('#erro').show(); $('#erro').html('Por favor, informe o nome do Usuário Master.'); return false;}
    if ($('#email').val() === "" || !checarEmail($('#email').val())) {$('#erro').show(); $('#erro').html('Por favor, informe um endereço de e-mail para ser o Usuário Master.'); return false;}
    //if (!validarUsuario($('#email').val())) {$('#erro').show(); $('#erro').html('Este e-mail já está cadastrado.'); return false;}
    
    if ($('#senha').val() === "") {$('#erro').show(); $('#erro').html('Por favor, crie uma senha.'); return false;}
    if ($('#confirma').val() === "") {$('#erro').show(); $('#erro').html('Por favor, repita a senha.'); return false;}
    if ($('#senha').val() !== $('#confirma').val()) {$('#erro').show(); $('#erro').html('As duas senhas cadastradas não conferem.'); return false;}

    if ($('#subdominio').val() === "") {$('#erro').show(); $('#erro').html('Por favor, crie um subdomínio para acesso do aplicativo por sua Empresa.'); return false;}

    $('#cadastro_empresa').submit();
});

function checarEmail(mail){
    if(mail.length===0) return true;

    if ((mail.length > 7) && !((mail.indexOf("@") < 1) || (mail.indexOf('.') < 2))) {
        return true;
    }else{
        return false;
    }
}

function validarCNPJ(cnpj) {
	 
    cnpj = cnpj.replace(/[^\d]+/g,'');
 
    if(cnpj === '') return false;
     
    if (cnpj.length !== 14)
        return false;
 
    // Elimina CNPJs inválidos conhecidos
    if (cnpj === "00000000000000" || 
        cnpj === "11111111111111" || 
        cnpj === "22222222222222" || 
        cnpj === "33333333333333" || 
        cnpj === "44444444444444" || 
        cnpj === "55555555555555" || 
        cnpj === "66666666666666" || 
        cnpj === "77777777777777" || 
        cnpj === "88888888888888" || 
        cnpj === "99999999999999")
        return false;
         
    // Valida DVs
    tamanho = cnpj.length - 2;
    numeros = cnpj.substring(0,tamanho);
    digitos = cnpj.substring(tamanho);
    soma = 0;
    pos = tamanho - 7;
    for (i = tamanho; i >= 1; i--) {
      soma += numeros.charAt(tamanho - i) * pos--;
      if (pos < 2)
            pos = 9;
    }
    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado != digitos.charAt(0))
        return false;
         
    tamanho = tamanho + 1;
    numeros = cnpj.substring(0,tamanho);
    soma = 0;
    pos = tamanho - 7;
    for (i = tamanho; i >= 1; i--) {
      soma += numeros.charAt(tamanho - i) * pos--;
      if (pos < 2)
            pos = 9;
    }
    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado != digitos.charAt(1))
          return false;
           
    return true;
    
}

function validarUsuario(email) {
    $.post('ajax_usuario.php', {email: email}, function(data) {
        if (data === 'true') return true;
        else return false;
    });
}

</script>

<?php 

if (isset ($_POST['empresa'])) {
	
    $endereco = $_POST['logradouro'].", ".$_POST['nro'];

    if ($_POST['senha'] == $_POST['confirma']) {
        $senha = md5($_POST['senha']);
    }else {
        echo '<div id="erro2">Suas senhas não conferem.</div>';
        exit();
    }

    $min_subdominio = strtolower($_POST['subdominio']);
    $subdominio = preg_replace('/[^a-z0-9]/i', '', $min_subdominio);
     
    //Inserir registros
    require_once 'classes/MPCLient.php';
    $register = MPClient::register(
            $_POST['empresa'], 
            $_POST['cnpj'], 
            $endereco, 
            $_POST['complemento'], 
            $_POST['cidade'], 
            $_POST['uf'], 
            $_POST['telefone'], 
            $_POST['celular'],
            $_POST['nome'],
            $_POST['email'], 
            $senha, 
            $subdominio);
        
    if ($register['success']) {
    	echo '<div id="div_sucesso">Cadastro realizado com sucesso. Você já pode escolher um de <a href="#" onclick="GoToId(\'#page-aplicativos\');return false">nossos aplicativos</a>.</div>';
    }else {
        echo '<div id="erro2">'.$register['message'].'</div>';
    }
	
}

?>
			</div>
		</div>
 
    </div>

</body>
</html>
