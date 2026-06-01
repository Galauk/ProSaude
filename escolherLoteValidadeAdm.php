<html>
<head>
  <title>Selecionar Lote e Validade</title>
  <style type="text/css">
  	span {
    	width:200px;
		float:left;
	}
	input{
		float:left;
	}
  </style>
<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<script language="JavaScript" type="text/javascript" src="ajax_motor.js"></script>
<script language="JavaScript" type="text/javascript" src="g_ajax.js"></script>
<script language="JavaScript" type="text/javascript" src="g_script.js"></script>
<script language="JavaScript" type="text/javascript" src="json.js"></script>
  <script>
  
 /* function fechaPopUp(lote,validade,quantidade) 
	{
	 window.opener.respostaCota2(lote,validade,quantidade);
	 window.close();
	}*/
	function fechaPopUp(contagem,resp,qtde,pro_codigo)
	{
		//alert(resp+"cONTEGEM"+contagem);
	
	 window.opener.respostaCota(resp,contagem,qtde,pro_codigo);	
	 window.close();
	}
//  	function validaQtde(form) {	
//	//*****************************************
//	// Soma Todas as quantidades do estoque
//		tudo = new Number(0);
//		todos=document.getElementsByTagName('input');
//		var i = 0;
//		var contagem = 0;
//		for(x = 0; x < todos.length; x++){
//			if(todos[x].checked){
//				var teste = todos[x].value;
//				recebeSplit = teste.split('|');
//				tudo = tudo + new Number(recebeSplit[0]);
//				contagem = new Number(contagem) + 1;				
//				//alert(todos[x].value);	
//				//alert(tudo);										
//			}
//		}
//		var array = new Array(todos.length);
//		for(x = 0; x < todos.length; x++){
//			if(todos[x].checked){
//				var teste = todos[x].value;
//				recebeSplit = teste.split('|');							
//				array[i] = new Number(recebeSplit[0]);
//
//				//quantidade[i] = recebeSplit[0];
//				i++;
//						
//				
//				/*alert(recebeSplit[1]);
//				alert(recebeSplit[2]);
//				alert('OK');*/
//				
//							
//			}
//		//document.getElementById("send_string_array").value = array_produtos.join("|");
//			
//
//		}
//		
//	//alert(tudo);	
//	if (tudo == 0) {
//		alert('Voce deve selecionar pelo menos um lote para ser dispensado!');
//		return false;
//	}
//	qtde = new Number(document.getElementById('qtde').value);
//
//	for(x = 0; x < todos.length; x++){
//		if(todos[x].checked){
//			qtdePrimeiroLote = new Number(recebeSplit[0]);			
//			break;
//		}
//	}
//	
//	if ((qtdePrimeiroLote > qtde) && (contagem > 1)){
//		alert('Apenas o primeiro lote ja suficiente, voce nao deve selecionar mais.');
//		return false;
//	}
//
//	if (qtde > tudo){
//		alert('Quantidade selecionada insuficiente. Voce deve selecionar pelo menos mais um lote para ser dispensado!');
//		return false;
//	}
//	
//	var resultado = (tudo - qtde);
//
//	i=0;
//	resp = new Array(contagem);
//	for(x = 0; x < todos.length; x++){
//		if(todos[x].checked){
//			var teste = todos[x].value;
//			
//			resp[i] = teste;
//			recebeSplit = teste.split('|');							
//			array[i] = new Number(recebeSplit[0]);
//			i++;
//		
//		 }
//	}
//	
//	fechaPopUp(recebeSplit[1],recebeSplit[2],recebeSplit[0],contagem,resp);	
//	//alert(resultado);	
//	//window.close();	
//	//quantidade = document.getElementById("quantidade").value;
//		var qtde = document.getElementById("qtde").value;		
//	
//	}
function validaQtde(form,set) {	

	qtde2 = document.getElementById('qtde').value;
	pro_codigo = document.getElementById('pro_codigo').value;
	

	//*****************************************
	// Soma Todas as quantidades do estoque
		tudo = new Number(0);
		todos=document.getElementsByTagName('input');
		var i = 0;
		new Number(contagem = 0) ;
		for(x = 0; x < todos.length; x++){
			if(todos[x].checked){
				var teste = todos[x].value;
				recebeSplit = teste.split('|');
				tudo = tudo + new Number(recebeSplit[0]);				
				contagem = new Number(contagem) + 1;
				
										
			}
		}
		var array = new Array(todos.length);
		for(x = 0; x < todos.length; x++){
			if(todos[x].checked){
				var teste = todos[x].value;
				recebeSplit = teste.split('|');							
				array[i] = new Number(recebeSplit[0]);

				i++;	
				lote = recebeSplit[1];
				validade = recebeSplit[2];
				
							
			}
		
		}
		
	if (tudo == 0) {
		alert('Voce deve selecionar pelo menos um lote para ser dispensado!');
		return false;
	}
	qtde = new Number(document.getElementById('qtde').value);
	var cont = 0;
	for(x = 0; x < todos.length; x++){
		if(todos[x].checked){
			var teste = todos[x].value;
			recebeSplit = teste.split('|');
			qtdePrimeiroLote = new Number(recebeSplit[0]);						
			break;
			
		}
	}

	if ((qtdePrimeiroLote > qtde2) && (contagem > 1)){
		alert('Apenas o primeiro lote ja suficiente, voce nao deve selecionar mais.');
		return false;
	}
	
	if (qtde2 > tudo){
		alert('Quantidade selecionada insuficiente. Voce deve selecionar pelo menos mais um lote para ser dispensado!');
		return false;
	}
	var resultado = (tudo - qtde);
	i=0;
	resp = new Array(contagem);
	for(x = 0; x < todos.length; x++){
		if(todos[x].checked){
			var teste = todos[x].value;
			
			resp[i] = teste;
			recebeSplit = teste.split('|');							
			array[i] = new Number(recebeSplit[0]);
			i++;
		
		 }
	}
fechaPopUp(contagem,resp,qtde2,pro_codigo);
	//window.location ="recepcao_transferencia.php?acao=edit_item&resp="+resp+"&set_codigo="+set+"&id_login="+id_login+"&req_codigo="+req_codigo+"&ireq_codigo="+ireq_codigo+"&contagem="+contagem+"&pro_codigo="+pro_codigo+"&quantidadeSolicitada="+document.getElementById('ireq_quantidade').value;;
	var qtde = document.getElementById("qtde").value;


}  

  </script>

</head>
<body>

<?php
session_start();
$qtde = $_GET['quantidade'];
$acao = $_GET['acao'];
$quantidade = $_GET['loteVal'];
$pro_nome = $_GET['pro_nome'];
$set_codigo = $_GET['set_codigo']; 

/*$sql = "SELECT to_char(sal_validade,'dd/mm/yyyy') as datavalidade,* FROM saldo
		 WHERE 
		pro_codigo = {$_GET[pro_codigo]} and set_codigo = $set_codigo and sal_validade > CURRENT_DATE and sal_qtde > 0 order by sal_validade";

$sql = "SELECT to_char(sal_validade,'dd/mm/yyyy') as datavalidade,* FROM saldo
		WHERE 
		pro_codigo = {$_GET[pro_codigo]} and sal_validade > CURRENT_DATE order by sal_validade";*/
$sql ="SELECT
			 ite_codigo, 
			 itens_movimento.pro_codigo, 
			 pro_nome, 
			 pro_validade, 
			 ite_quantidade, 
			 ite_vlrunit, 
			 ite_vlrdesc,
			 ite_lote, 
			 ite_validade, 
			 to_char( ite_vlrtotal, '999999999.99') as valortotal
	   FROM 
	     itens_movimento, produto
	WHERE 
		itens_movimento.pro_codigo = produto.pro_codigo
	and   
		(select mov_tipo from movimento where mov_codigo = itens_movimento.mov_codigo) = 'E'
	and itens_movimento.ite_lote is not null
	and itens_movimento.pro_codigo = {$_GET[pro_codigo]}
    AND itens_movimento.ite_validade > CURRENT_DATE
		";
	$res = pg_query($sql);
		
	$acao = $_POST['acao'];
	
	if($acao == ""){	
		echo "<form name='seleciona' method='post'>
		<input type='hidden' id='send_string_array' name='send_string_array' value=''>
		<input type='hidden' id='acao' name='acao' value='add'>
		<input type='hidden' id='qtde' name='qtde' value='$qtde'>
		<input type='hidden' id='pro_nome' name='pro_nome' value='$pro_nome'>
		<input type='hidden' id='pro_codigo' name='pro_codigo' value='$_GET[pro_codigo]'>";
		$contador = 0;
		while($registro = pg_fetch_array($res)){
			$lote = $registro["sal_lote"];
			$validade = $registro["datavalidade"];
			$quantidade = $registro["sal_qtde"];
			 echo"
				<label>
					<input type='checkbox' name='loteVal' value='$quantidade|$lote|$validade'>";
					//<input type='hidden' id='quantidade[$contador]' name='quantidade[$contador]' value='$quantidade'>
					echo "<span>Lote: $lote</span>
					<span>Validade: $validade</span>
					<span>Quantidade: $quantidade</span>
				</label><br>";
				$contador++;
					
					
				/*echo"$lote <br>";
				echo"$qtde <br>";
				echo"$quantidade<br>";
				echo"$pro_nome<br>";
				echo"$mov_codigo<br>";
				echo"$acao<br>";*/
				
		}
	
	echo"<br>
			<table align='center' style='width:600px;'>
				<tr>
					<td>
						<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/enviar_dados_on.jpg' onClick='return validaQtde(this,$set_codigo)'>
					</td>
				</tr>
			</table>
		</div>
		</form>";
	}
?>
</body>
</html>
