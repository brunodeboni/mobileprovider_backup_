function Comprar($scope) {

	$scope.licenca = 'Licença Ilimitada';
	$scope.itemDescription2 = 'Licença Ilimitada';
	$scope.itemAmount2 = '250.00';
	$scope.quantidade = 1;
	$scope.meses = 1;
	
	var dias_remaining = <?php echo $dias_remaining; ?>;
	var dias = dias_remaining + 30;

	$scope.$watch('quantidade', function() {
	    var dias_total = ($scope.meses * 30) + dias_remaining;
	
		if ($scope.licenca === "Licença por Usuário") {
			$scope.itemDescription2 = 'Licença por Usuário (válido por '+ dias_total +' dias)';
			var quantidade = $scope.quantidade;
			var valor_licenca_mes = quantidade * 5;
			var valor_licenca_total = valor_licenca_mes * (($scope.meses/30) * (dias - dias_remaining));
			var amount = 5 * (($scope.meses/30) * (dias - dias_remaining));
			$scope.itemAmount2 = amount+'.00';
			var total = valor_licenca_total + 500;

			<?php if ($licenca_instalacao == 0) { ?>
				$('#valor').html('<div id="valor-instalacao">Instalação e configuração: R$ 500,00</div><div id="mais"> + </div><div id="valor-licenca">Licença para '+ quantidade +' Usuário(s): R$ '+ valor_licenca_total +',00 (válido por '+ dias_total +' dias)</div><div id="igual"> = </div><div id="valor-total">R$ '+ total +',00</div>');
			<?php }else { ?>
				$('#valor').html('<div id="valor-licenca">Licença para '+ quantidade +' Usuário(s): R$ '+ valor_licenca_total +',00 (válido por '+ dias_total +' dias)</div><div id="igual"> = </div><div id="valor-total">R$ '+ valor_licenca_total +',00</div>');
			<?php } ?>
			$('#itemAmount2').val('5.00');
		}else {
		    $scope.itemDescription2 = 'Licença Ilimitada (válido por '+ dias_total +' dias)';
		    var valor_licenca = 250 * (($scope.meses/30) * (dias - dias_remaining));
		    $scope.itemAmount2 = valor_licenca+'.00';
		    $scope.quantidade = 1;
		}
	});

	$scope.$watch('licenca', function() {
	    var dias_total = ($scope.meses * 30) + dias_remaining;
	    
	    if ($scope.licenca === "Licença por Usuário") {
		$scope.itemDescription2 = 'Licença por Usuário (válido por '+ dias_total +' dias)';
		$('#qnt').show();
		$('#quantidade').show();

		var quantidade = $scope.quantidade;
		var valor_licenca_mes = quantidade * 5;
		var valor_licenca_total = valor_licenca_mes * (($scope.meses/30) * (dias - dias_remaining));
		var total = valor_licenca_total + 500;
		var amount = 5 * (($scope.meses/30) * (dias - dias_remaining));
		$scope.itemAmount2 = amount+'.00';
		
		<?php if ($licenca_instalacao == 0) { ?>
			$('#valor').html('<div id="valor-instalacao">Instalação e configuração: R$ 500,00</div><div id="mais"> + </div><div id="valor-licenca">Licença para '+ quantidade +' Usuário(s): R$ '+ valor_licenca_total +',00 (válido por '+ dias_total +' dias)</div><div id="igual"> = </div><div id="valor-total">R$ '+ total +',00</div>');
		<?php }else { ?>
			$('#valor').html('<div id="valor-licenca">Licença para '+ quantidade +' Usuário(s): R$ '+ valor_licenca_total +',00 (válido por '+ dias_total +' dias)</div><div id="igual"> = </div><div id="valor-total">R$ '+ valor_licenca_total +',00</div>');
		<?php } ?>
	    }
	    if ($scope.licenca === "Licença Ilimitada") {
		$scope.itemDescription2 = 'Licença Ilimitada (válido por '+ dias_total +' dias)';
		var valor_licenca_total = 250 * (($scope.meses/30) * (dias - dias_remaining));
		var total = valor_licenca_total + 500;
		$scope.itemAmount2 = valor_licenca_total+'.00';
		
		<?php if ($licenca_instalacao == 0) { ?>
			$('#valor').html('<div id="valor-instalacao">Instalação e configuração: R$ 500,00</div><div id="mais"> + </div><div id="valor-licenca">Licença Ilimitada: R$ '+ valor_licenca_total +',00 (válido por '+ dias_total +' dias)</div><div id="igual"> = </div><div id="valor-total">R$ '+ total +',00</div>');
		<?php }else { ?>
			$('#valor').html('<div id="valor-licenca">Licença Ilimitada: R$ '+ valor_licenca_total +',00 (válido por '+ dias_total +' dias)</div><div id="igual"> = </div><div id="valor-total">R$ '+ valor_licenca_total +',00</div>');
		<?php } ?>
		$('#itemAmount1').val('750.00');

		$scope.quantidade = 1;
		
		$('#quantidade').hide();
		$('#qnt').hide();
		$('#itemAmount2').val('250.00');
	    }
	});
	
	$scope.$watch('meses', function() {
	    var dias_total = ($scope.meses * 30) + dias_remaining;
	    if ($scope.licenca === "Licença por Usuário") {
		
		$scope.itemDescription2 = 'Licença por Usuário (válido por '+ dias_total +' dias)';
		var valor_licenca_mes = $scope.quantidade * 5;
		var valor_licenca_total = valor_licenca_mes * (($scope.meses/30) * (dias - dias_remaining));
		var total = valor_licenca_total + 500;
		var amount = 5 * (($scope.meses/30) * (dias - dias_remaining));
		$scope.itemAmount2 = amount+'.00';
		
		$('#valor-licenca').html('Licença para '+ $scope.quantidade +' Usuário(s): R$ '+ valor_licenca_total +',00 (válido por '+ dias_total +' dias)');
		<?php if ($licenca_instalacao == 0) { ?>
		    $('#valor-total').html('R$ '+ total +',00');
		<?php }else { ?>
		    $('#valor-total').html('R$ '+ valor_licenca_total +',00');
		<?php } ?>   
	    }else {
		
		$scope.itemDescription2 = 'Licença Ilimitada (válido por '+ dias_total +' dias)';
		var valor_licenca_total = 250 * (($scope.meses/30) * (dias - dias_remaining));
		var total = valor_licenca_total + 500;
		$scope.itemAmount2 = valor_licenca_total+'.00';
		
		$('#valor-licenca').html('Licença Ilimitada: R$ '+ valor_licenca_total +',00 (válido por '+ dias_total +' dias)');
		<?php if ($licenca_instalacao == 0) { ?>
		    $('#valor-total').html('R$ '+ total +',00');
		<?php }else { ?>
		    $('#valor-total').html('R$ '+ valor_licenca_total +',00');
		<?php } ?> 
	    }
	});
	 
}

