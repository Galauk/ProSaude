<html>
<head>
<script language="JavaScript" type="text/javascript" src="ajax_motor.js"></script>
<script type="text/javascript">
	function validaRegistro()
	{
	//	alert('OI');
		var nome = document.getElementById('nome').value;
		var codigo = document.getElementById('codigo').value;
		var dia = document.getElementById('dia').value;
		var mes = document.getElementById('mes').value;
		var ano = document.getElementById('ano').value;
		var data = dia+'-'+mes+'-'+ano;
		var senha = document.getElementById('senha').value;
		url = "validaPagoOuNao.php?nome="+nome+"&codigo="+codigo+"&data="+data+"&senha="+senha+"&dia="+dia;
			
		ajax_tudo(url, validaInformacao);
		//alert("");		
		
	}
	function validaInformacao(txt)
	{
		
		if(txt == "no")
		{
			alert("Informações Invalidas");
		}
		else{
			alert(txt+" Periodo cadastrado com Sucesso.");
			window.close();
		}		
	}
	
	
</script>
</head>
<body>
<?php
session_start();
include_once $_SESSION[root].$_SESSION[modulo]."funcao.calendario.php";
echo"
	<table width=480 border='0'>
		<tr>
			<td align='center' colspan='4' bgcolor=#000000><font color=#FFFFFF>Reativação do Sistema</font><td>
		</tr>
		<tr>
			<td>Nome</td>
			<td><input type='text' id='nome' name='nome' size='50'></td>
		</tr>
			<tr>
			<td>Codigo</td>
			<td><input type='text' id='codigo' name='codigo' size='30'></td>
		</tr>
			<tr>
			<td>Data Validade</td> <td>";
			campodata("dia", "mes", "ano");echo"</td>";
		echo"</tr>
			<tr>
			<td>Senha</td>
			<td><input type='text' id='senha' name='senha' size='20'></td>
		</tr>
		<tr>
			<td>
				<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/cancelar_on.jpg' onclick='window.close()'>
			</td>
			<td>
				<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/enviar_dados_on.jpg' OnClick='validaRegistro()'>
			</td>
		</tr>
		<tr>
			<td colspan='2' id='fechar'></td>
		</tr>
	</table>";
?>
</body>
</html>