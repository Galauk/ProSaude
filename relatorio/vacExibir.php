<link href="estilo.css" rel="stylesheet" type="text/css" />
<link href="estilo2.css" rel="stylesheet" type="text/css" />
<link href="tabela.css" rel="stylesheet" type="text/css" />
<?
	session_start();
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	verauth($id_login);
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	cabecario();
	include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";
	include_once $_SESSION[root].$_SESSION[modulo]."processaData.php";
?>

<script language="JavaScript" src="atalhos.js"></script>
<script language="JavaScript" type="text/javascript" src="../funcoes.js"></script>
<script language="JavaScript" type="text/javascript" src="../g_ajax.js"></script>
<script language="JavaScript" type="text/javascript" src="../ajax_motor.js"></script>
<script>
function carregaCerto(event)
{
	var aplicando = document.getElementById('A').value;	
	var paciente = document.getElementById('pac_codigo').value;
	if(paciente == "")
	{
		alert("O CAMPO USUARIO ESTA VAZIO !")
		exit;	
	}else{
	url = "loadCarteirinha.php?paciente="+paciente;
	ajax_tudo(url, AparecerDiv);
	}
}

function loadCarteirinha()
{
	url = "loadCarteirinha.php";
	ajax_tudo(url, AparecerDiv);	
}

function AparecerDiv(txt)
{   
	document.getElementById('resposta').innerHTML = txt;
	document.getElementById('resposta').style.display = "block";
}  
function selecionaData(id)
{
	resposta = "";
	var aplicando = document.getElementsByTagName('input');
	for(x = 0; x < aplicando.length; x++){
		if (aplicando[x].getAttribute("type") == "radio" && aplicando[x].checked == true) 
		{
			resposta = aplicando[x].value;
			break;
        }
	}
	if(resposta == "")
	{
		alert("Selecione um Procedimento!");
		a.setAttribute("class", "dosesVacina");
	}

	if(resposta == "C")
	{
		deletaVacina(id);
		//url = "confirmaCancela.php?id="+id+"&resposta="+resposta;
		
	
	//window.open(url, null,"height=120,width=285,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
	}else{
		url = "marcaData.php?id="+id+"&resposta="+resposta;
		window.open(url, null,"height=150,width=450,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");	
	}

}
function deletaVacina21(id){
	alert(id)
}
function imprimeAtestado()
{
	var pac_nome = document.getElementById('pac_nome').value;
	imprimi = "";
	var imprimindo = document.getElementsByTagName('input');
	for(x = 0; x < imprimindo.length; x++){
		if (imprimindo[x].getAttribute("type") == "radio" && imprimindo[x].checked == true) 
		{
			imprimi = imprimindo[x].value;
			break;
        }
	}
	if(imprimi == "IMA"){
		url = "atestado_vacina.php?pac_nome="+pac_nome;
		window.open(url, null,"height=320,width=1300,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
	}
	
	if(imprimi == "IMC"){
	 
	}
}

function deletaVacina(id)
{
	if (confirm("Deseja Apagar esse registro?")){
		var pac_codigo = document.getElementById('pac_codigo').value;
		var prod_dose = id.replace("vacina","");
		produto = "pro_codigo"+prod_dose.substring(0,1);
		dose= prod_dose.substring(1);	
		var pro_codigo = document.getElementById(produto).value;
		url = "deletaVacinas.php?pac_codigo="+pac_codigo+"&pro_codigo="+pro_codigo+"&dose="+dose+"&id="+id;
		ajax_tudo(url,sucesso);	
	}else{
		return false;
	}

}

function salvaVacina(resposta,data,unidade,id)
{
	var pac_codigo = document.getElementById('pac_codigo').value;
	var prod_dose = id.replace("vacina","");
	produto = "pro_codigo"+prod_dose.substring(0,1);
	dose= prod_dose.substring(1);	
	var pro_codigo = document.getElementById(produto).value;
	url = "salvarVacinas.php?resposta="+resposta+"&data="+data+"&unidade="+unidade+"&pac_codigo="+pac_codigo+"&pro_codigo="+pro_codigo+"&dose="+dose;
	ajax_tudo(url,sucesso);
}

function sucesso(txt)
{
	if (txt == "false"){
		alert('Houve um erro e a acao nao foi salva, tente novamente.');
	}else{
		var a = document.getElementById(txt);
		a.setAttribute("class", "dosesVacina");
		a.innerHTML = "";
		deletaVacina(id);	
	}
}

function executaAcao(id, data, unidade_cod,resposta){
    var a = document.getElementById(id);// a é um td... que id por sua vez é seu id sequencial.
	switch(resposta){
		case "A":
			a.setAttribute("class", "dosesAplicados");
			a.innerHTML = "<b>"+unidade_cod.toUpperCase()+"<br />"+data+"</b>";
			salvaVacina(resposta,data,unidade_cod,id);
			break;
		case "P":
			a.setAttribute("class", "dosesPreenchidas");
			a.innerHTML = "<b><br />"+data+"</b>";
			salvaVacina(resposta,data,unidade_cod,id);
			break;
		case "Z":
			a.setAttribute("class", "dosesAprazadas");
			a.innerHTML = "<b><br />"+data+"</b>";
			salvaVacina(resposta,data,unidade_cod,id);
			break;
		case "C":
			a.setAttribute("class", "dosesVacina");
			a.innerHTML = "";
			deletaVacina(id);

	}

}

<!--//////////////////////////-->
shortcut.add("Right",function() 
{
     add_dest();
});

shortcut.add("Left",function() 
{
     rem_dest();
});

shortcut.add("F2",function() 
{
 buscar_nome($F('pac_nome'), 'buscar_nome');return false;link_f7();
});

shortcut.add("F4",function() 
{
 buscar_nome($F('pac_nascimento'), 'buscar_data');
});

shortcut.add("F8",function() 
{
 var pac_codigo = document.form_msg.pac_codigo.value;
  window.open("exa_historico.php?id_login=<?=$id_login?>&usu_codigo="+pac_codigo,null,"height=460,width=800,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
});

shortcut.add("F9",function() 
{
  window.open("../paciente_ficha.php?acao=form_add&type=c&id_login=$id_login&controle=1",null,"height=460,width=800,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
});

</script>

<!--	<tr>
		<td width="50" align='right'>Prontu&aacute;rio:</td>
		<td width="50" align="left">
			<input type='hidden' id='pac_codigo' size='10' />
			<input type='text' id='pac_prontuario' class='boxNumero' size='10' onchange="busca_pac_prontuario()" />
			
		</td>
		<td width="50" align='right'>Paciente:</td>
		<td  width="250">
			<input type='text' id='pac_nome' class='boxTexto' size='60' readonly='readonly' >
		</td>
		<td width="50" align="right">Nascimento:</td>
		<td >
			<input type='text' id='pac_nascimento' class='box' size='12' readonly='readonly'/>
				<a href='#' onclick="teste()">
					<img src='<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/localizar.jpg' style='vertical-align:middle' border='0' alt='Localizar' />
				</a>(F7)
		</td>
	</tr>-->
<table>
<? 
		echo "<tr>";
                                                echo "<td>Prontuario:</td>";
                                                echo "<td>";
                                                        echo "<input type=text name='pac_codigo' id='pac_codigo' class=boxNumero readonly value='$pac[usu_codigo]'>";
                                                        echo "</td>";


                                                echo "<td>Paciente</td>";
                                                echo "<td>";
                                                        echo "<input type=text name=pac_nome id=pac_nome value='$pac[usu_nome]' class=boxTexto onkeyup=\"buscar_nome(this.value);\" style=\"text-transform:uppercase;\" onkeypress=\"if(event.keyCode == 13)buscar_nome(\$F('pac_nome'), 'buscar_nome')\">";
                                                echo "<a href='#' onclick=\"buscar_nome(\$F('pac_nome'), 'buscar_nome');return false;link_f7()\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg id=localizar align=absmiddle border=0></a>";



                                                echo divBuscaPaciente();
                                                echo "</td>";
                                                echo "<td>Nascimento:</td>";
                                                echo "<td>";
                                                        echo "<input type=text name=pac_nascimento value='$pac[usu_datanasc]' id=pac_nascimento class=boxNumero onkeypress=\"if(event.keyCode == 13)buscar_nome(\$F('pac_nascimento'), 'buscar_data');return Ajusta_Data(this, event);\" maxlength=\"10\">";
                                                        echo "<a href='#' onclick=\"buscar_nome(\$F('pac_nascimento'), 'buscar_data')\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg id=localizar align=absmiddle border=0></a>";
                                                echo "</td>";
                                        echo "</tr>";
                                        echo "<tr>";
                                                echo "<td>Mae</td>
													  <td><input type=text name=pac_mae value='$pac[usu_mae]' id=pac_mae class=boxTexto  readonly>
													  </td>
							 		
							 <td>Cidade:</td>
							 <td><input type=text name=pac_cidade id=pac_cidade value='$pac[usu_end_cidade]' class=boxTexto readonly></td>
							 <td><span onClick='carregaCerto(this);' style='cursor: pointer;'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_on.jpg'></span></td>
							 <td></td>					  
						</tr>";
                               
echo "</table>";

?>

<? 
echo "
<br />
<div class='faixa'>
	&nbsp;
</div>
";
	
echo"
<table border='0'>
	<tr>
		<td class='faixaOp'>
			<table border='0'> <!-- tabela de operações -->
				<tr>
					<td colspan='2' class='tituloTabela'>
						Opera&ccedil;&otilde;es
					</td>
				</tr>
				
				<tr>
					  <td>
						<label><input type='radio' id='A' name='op' value='A' />Aplicar</label>
					  </td>";
					//<td>
					  // <label><input type='radio' id='vizualizar' name='op' value='vizualizar' />Vizualizar</label>
					//</td>
					echo "<td>
						<label><input type='radio' id='P' name='op' value='P'/>Preencher</label>
					</td>
				</tr>
				<tr>
					<td>
					  	<label><input type='radio' id='C' name='op' value='C' />Cancelar</label>
					</td>
					<td align='left'>
						<label><input type='radio' id='Z' name='op' value='Z' />Aprazar</label>
					</td>
				</tr>";
/*				<tr>
					<td>
						<label><input type='radio' id='novo' name='op' value='novofrasco' />Novo Frasco</label>
					</td>
					<td>
						<label><input type='radio' id='contra' name='op' value='contra' />Contra-Indica&ccedil;&atilde;o</label>
					</td>
				</tr>*/
				echo "<tr>
					";
					//<td>
						//<label><input type='radio' id='identificar' name='op' value='identificar' />Identificar Lote</label>
						
					//</td>
				echo "</tr>
				
			</table>
			<br /><br /><br /><br /><br />
			
			
			<table> <!-- botões de impressao -->
				<tr>
					<td colspan='2' class='tituloTabela'>
						Impress&otilde;es
					</td>
				</tr>
				<tr>
					<td width='50%'>
						<label><input type='radio' id='IMC'name='op' value='IMC' /> Carteirinha</label>
					</td>
					<td>
						<label><input type='radio' id='IMA' name='op' value='IMA' /> Atestado</label>
					</td>
				</tr>
				<tr>
					<td colspan='2' align='center'>
						<span onClick='return imprimeAtestado();' style='cursor: pointer;'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/imprimir_on.jpg' / style='padding-right:40px'></span>
					</td>
				</tr>
			</table>
			
			<table class='tabLegenda' align='center'> <!-- tabela de legendas -->
				<tr>
					<td colspan='2' class='tituloTabela'>Legendas</td>
				</tr>
				<tr>
					<td class='legendaAplicar'></td>
					<td><strong>Aplicadas</strong></td>
				</tr>
				<tr>
					<td class='legendaAprazar'></td>
					<td><b>Aprazadas</b></td>
				</tr>
				<tr>
					
					<td class='legendaPreencher'></td>
					<td><b>Preenchidas</b></td>
				</tr>
			</table>	
		</td>
		<td valign='top'>
			<div id='resposta' style='display:none'>	
			</div>
		</td>
		</tr>
		</table>
	";
	?>

?>