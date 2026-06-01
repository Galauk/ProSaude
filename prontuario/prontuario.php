<?php 
	session_start();
?>
<link href="estiloProntuario.css" rel="stylesheet" type="text/css">
<link href="../estilo.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="../atalhos.js"></script>

<script language="JavaScript" type="text/javascript" src="../g_ajax.js"></script>
<script language="JavaScript" type="text/javascript" src="../ajax_motor.js"></script>
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.7.2.custom.min.js"></script>


<link rel="stylesheet" type=text/css href="js/jquery-ui-1.7.2.custom.css" />




<!--SCRIPT DO JQUERY  -->

<script>
function $( )
{
	var A  = new Array;
	for( var i = 0; i < arguments.length; i++ )
	{
		var obj = document.getElementById( arguments[i] );
		if( ! obj )
		{
			alert("O elemento '" + arguments[i] + "' nao foi encontrado !");
			return null;
		}
		A.push( obj );
	}
	return ( A.length == 1 ? A[0] : A ); 
}

/** Retorna o valor "value" do objeto
* Se passado mais de um elemento, ele ira devolver um Array, senao, devolve o proprio elemento
*/
function $F( )
{
	var A = new Array;
	for( var i = 0; i < arguments.length; i++ )
	{
		var obj = $( arguments[i] );
		if( ! obj ) continue;
		A.push( obj.value );
	}
	return ( A.length == 1 ? A[0] : A ); 
}

/*
 Funções para busca de pacientes
*/
function buscar_nome(valor, acao)
{						
		url = "buscar_nomes.php?palavra="+valor+"&acao="+acao;
		ajax_tudo(url, popular_nome);
		$('lista_nomes').style.display = '';
		$('table_nomes').innerHTML = '';
		$("lista_carregando").style.display = '';
}


function popular_nome(txt)
{
		try {
				t = $('table_nomes');
				$("lista_carregando").style.display = 'none';
				t.innerHTML = txt;
		} catch(e) {
				alert(e);
		}
}
function trocar_cor(id, id2)
{
		campo = $(id);
		campo.style.background = "#ABCDEF";
		if(id2 != null)
		{
				$(id2).style.display = '';
		}
}

function retirar_cor(id, id2)
{
		campo = $(id);
		campo.style.background = "#FFFFFF";
		if(id2 != null)
		{
				$(id2).style.display = 'none';
		}
}

function passar_usuario(codigo, nome, mae, data_nasc, cidade, prontuario)
{
		
		$("pac_codigo").value = codigo;
		$("pac_nome").value = nome;
		$("pac_mae").value = mae;
		$("pac_nascimento").value = data_nasc;
		$("pac_cidade").value = cidade;
		
		if(document.getElementById("pac_prontuario") != null)
		{
				$("pac_prontuario").value = prontuario;
				/*if( at_iframe_esq != null )
					at_iframe_esq();*/
		}
		
		$('lista_nomes').style.display = 'none';
		$('pac_nome').focus();
		ajaxMedico();
}
function ajaxMedico(){			
			url = "ajaxBuscaMedico.php";
			ajax_tudo(url,preencheMedico)
}
function preencheMedico(txt){
	document.getElementById('minhaDiv').innerHTML = txt;
	ajaxMedico2();
}


</script>
<!-- <style>
      p { background:yellow; }
   </style>-->
<?

include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
//verauth($id_login);

include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
cabecario();

echo "<div id='topo' class='topo'>";
///////////////////////////////////////////////////////////////


///////////////////////////////////////////////////////////////
//echo "<div id='manipulada' style='display:none'>";
echo "<table width='100%' cellspacing=0 cellpadding=1 border=0>
		<tr>
			<td class='primeiroNome' width='8%'>
				Prontu&aacute;rio:
			</td>
			<td class='segundoNome'>							
				<input type=hidden name='pac_codigo' id='pac_codigo' class=boxl size=10 onchange='buscar_dados_paciente();'>
				<input type=text name='pac_prontuario' id='pac_prontuario' class=boxl size=5 		   onchange='buscar_dados_paciente(this.value);'>
			</td>
			<td class='primeiroNome'>
				Paciente:
			</td>
			<td>
				<input type=text size=60 name=pac_nome id=pac_nome value='$pac[usu_nome]' class=box style=\"text-transform:uppercase;\">&nbsp;
				<a href='#' onclick=\"buscar_nome(document.getElementById('pac_nome').value,'buscar_nome');return false;\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg id=localizar align=absmiddle border=0></a>";
				echo divBuscaPaciente("../");
			echo "</td>
					<td class='primeiroNome'>
						Nascimento:
					</td>
					<td>
						<input type=text name=pac_nascimento id=pac_nascimento class=boxl size=11 readonly 	onfocus='buscar_prontuario();'>
					</td>";
							
							
					echo "</tr>";
				//echo "</table>";
				//echo "<table width=100% cellspacing=0 cellpadding=4 border=0>";
					echo "<tr>
							<td>
							</td>
							<td>
							</td>
							<td class='primeiroNome'>M&atilde;e</td>
							<td>
								<input type=text name=pac_mae id=pac_mae class=boxl size=30 readonly>
							</td>
							<td class='primeiroNome'>Cidade:</td>
							<td>
								<input type=text name=pac_cidade id=pac_cidade class=boxl size=20 readonly>
							</td>
					</tr>";
		


echo"
</div>	
";
echo "
<div id='agenda' class='alpha'>
	<p style='display:none'>
		<table border='0' style='display:none'>
			<tr>
				<td class='celulaAgendada' align='center'>1</td>
				<td class='celulaAgendada'>VICTOR HUGO MARQUES CALDEIRA</td>
				<td class='celulaAgendada'>08:00</td>
				<td class='celulaAgendada'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/r_on.jpg'></td>
				<td class='celulaAgendada'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/t_on.jpg'></td>
				<td class='celulaAgendada'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/f_on.jpg'></td>
				<td class='celulaAgendada'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/m_on.jpg'></td>
			</tr>
			<tr>
				<td class='celulaParaAgendar' align='center'>2</td>
				<td class='celulaParaAgendar'></td>
				<td class='celulaParaAgendar'></td>
				<td class='celulaParaAgendar'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/r_off.jpg'></td>
				<td class='celulaParaAgendar'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/t_off.jpg'></td>
				<td class='celulaParaAgendar'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/f_off.jpg'></td>
				<td class='celulaParaAgendar'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/m_off.jpg'></td>
			</tr>
			<tr>
				<td class='celulaParaAgendar' align='center'>3</td>
				<td class='celulaParaAgendar'></td>
				<td class='celulaParaAgendar'></td>
				<td class='celulaParaAgendar'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/r_off.jpg'></td>
				<td class='celulaParaAgendar'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/t_off.jpg'></td>
				<td class='celulaParaAgendar'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/f_off.jpg'></td>
				<td class='celulaParaAgendar'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/m_off.jpg'></td>
			</tr>
			<tr>
				<td class='celulaParaAgendar' align='center'>4</td>
				<td class='celulaParaAgendar'></td>
				<td class='celulaParaAgendar'></td>
				<td class='celulaParaAgendar'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/r_off.jpg'></td>
				<td class='celulaParaAgendar'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/t_off.jpg'></td>
				<td class='celulaParaAgendar'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/f_off.jpg'></td>
				<td class='celulaParaAgendar'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/m_off.jpg'></td>
			</tr>
			<tr>
				<td class='celulaParaAgendar' align='center'>5</td>
				<td class='celulaParaAgendar'></td>
				<td class='celulaParaAgendar'></td>
				<td class='celulaParaAgendar'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/r_off.jpg'></td>
				<td class='celulaParaAgendar'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/t_off.jpg'></td>
				<td class='celulaParaAgendar'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/f_off.jpg'></td>
				<td class='celulaParaAgendar'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/m_off.jpg'></td>
			</tr>
			
			<tr>
				<td class='celulaParaAgendar' align='center'>6</td>
				<td class='celulaParaAgendar'></td>
				<td class='celulaParaAgendar'></td>
				<td class='celulaParaAgendar'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/r_off.jpg'></td>
				<td class='celulaParaAgendar'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/t_off.jpg'></td>
				<td class='celulaParaAgendar'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/f_off.jpg'></td>
				<td class='celulaParaAgendar'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/m_off.jpg'></td>
			</tr>
		</table>
	</p>
</div>

";echo"<div id='minhaDiv'>";

echo"</div>";
//<div id='imagem'>  </div>"
?>
<script>

 </script>