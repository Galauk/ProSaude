<?php 

session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
verauth($id_login);
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
cabecario();
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."processaData.php";

?>
<link href="estilo.css" rel="stylesheet" type="text/css" />
<link href="estilo2.css" rel="stylesheet" type="text/css" />
<link href="tabela.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="atalhos.js"></script>
<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<script language="JavaScript" type="text/javascript" src="g_ajax.js"></script>
<script language="JavaScript" type="text/javascript" src="ajax_motor.js"></script>
<?php echo "\n<script type='text/javascript' src='$_SESSION[linkroot]$_SESSION[comum]library/js/jquery-1.5.2.min.js'></script>\n"; ?>
<?php echo "\n<script type='text/javascript' src='$_SESSION[linkroot]$_SESSION[modulo]lib/ui/jquery-ui-1.8.10.custom.js'></script>\n"; ?>
<?php echo "<link rel='stylesheet' href='".$_SESSION[linkroot].$_SESSION[modulo]."lib/themes/ui-lightness/jquery-ui-1.8.10.custom.css'>"; ?>
<script><!--

var j = $.noConflict();

function bind(){
	var antes = [];
	j(".reforco:not(.dosesVacinaOff):not(.dosesVacina)").each(function(){
		var rel = antes.push( j(this).attr("onclick") );
		j(this).attr("rel",rel);
	})
	.removeAttr("onclick")
	.click(function(e) {
		
		if(e.ctrlKey){
			var pro_codigo = j(this).parent("tr").find("td:first input[name^=pro_codigo][type=hidden]:first").val();
			
			// ajax para buscar os reforços:
			j("#sys").append("<div id=\"msg\" title=\"Reforços\"></div>");
			j("#msg")
			.load("vacinaReforco.php?pro_codigo="+pro_codigo+"&paciente="+j("#pac_codigo").val() )
			.dialog({
				modal: true
			});
			return false;
			
		} else {
			rel = j(this).attr("rel")-1;
			antes[rel]();
		}
	});	
}

function carregaCerto(event,id_login)
{
	var aplicando = document.getElementById('A').value;	
	var paciente = document.getElementById('pac_codigo').value;
	if(paciente == "")
	{
		alert("O CAMPO USUARIO ESTA VAZIO !")
		exit;	
	}else{
		url = "loadCarteirinha.php?paciente="+paciente+"&id_login="+id_login;
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
	bind(); // jQuery passa olhar o click com o CTRL precionado
} 
function verificarSeTemQtde(id,resposta,pro_codigo){
		
	url = "verificarSeTemQtdeDose.php?id="+id+"&resposta="+resposta+"&pro_codigo="+pro_codigo;
	ajax_tudo(url, respostaDoVerificar);
}
function respostaDoVerificar(txt){
	if(txt == 1 ){
		alert('Não foi possivel fazer a dispensação, vacina sem estoque ou com frasco fechado');
	}else{
		separa = txt.split("|");
		id = separa[0]; 
		resposta =	separa[1];
		//alert(id);
		//alert(resposta);
		
		var id_login = document.getElementById('id_login').value;
		url = "marcaData.php?id="+id+"&resposta="+resposta+"&id_login="+id_login;
		window.open(url, null,"height=230,width=270,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
	}	
}
function selecionaData(id,pro_codigo)
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
		verificarSeTemQtde(id,resposta,pro_codigo);
//		if(dose == '' || dose <= 0 ){
//			alert('Não foi possivel fazer a dispensação, vacina sem estoque ou com frasco fechado');
//		}else{
			//url = "marcaData.php?id="+id+"&resposta="+resposta;
			//window.open(url, null,"height=150,width=450,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
		//}	
	}

}
function deletaVacina21(id){
	alert(id)
}
function imprimeAtestado(id_login)
{
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
		url = "atestado_vacina.php?usu_codigo="+ document.getElementById('pac_codigo').value;
		window.open(url, null,"height=530,width=630,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
	}
	
	if(imprimi == "IMC"){
		var paciente = document.getElementById('pac_codigo').value;
		url = "vacExibir.php?id_login="+id_login+"&paciente="+paciente;
		window.open(url, null,"height=335,width=915,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
	 
	}
}

function deletaVacina(id)
{
	if (confirm("Deseja Apagar esse registro?")){
		var pac_codigo = document.getElementById('pac_codigo').value;
		var prod_dose = id.replace("vacina","");
		produto = "pro_codigo"+prod_dose.substring(0,2);
		linha = prod_dose.substring(0,2);
		dose= prod_dose.substring(2);	
		var pro_codigo = document.getElementById(produto).value;
		url = "deletaVacinas.php?pac_codigo="+pac_codigo+"&pro_codigo="+pro_codigo+"&dose="+dose+"&id="+id+"&linha="+linha;
		ajax_tudo(url,sucesso);	
	}else{
		return false;
	}

}

function salvaVacina(resposta,data,unidade,id)
{
	unidade = unidade.split("|")[0];
	var pac_codigo = document.getElementById('pac_codigo').value;
	var prod_dose = id.replace("vacina","");
	produto = "pro_codigo"+prod_dose.substring(0,2);
	dose= prod_dose.substring(2);	
	linha = prod_dose.substring(0,2);
	var pro_codigo = document.getElementById(produto).value;
	url = "salvarVacinas.php?resposta="+resposta+"&data="+data+"&unidade="+unidade+"&pac_codigo="+pac_codigo+"&pro_codigo="+pro_codigo+"&dose="+dose+"&linha="+linha;
	ajax_tudo(url,sucesso2);
}

function sucesso2(txt)
{

	if (txt == "false"){
		alert('Houve um erro e a acao nao foi salva, tente novamente.');
	}else{

		// recarrega toda a carteirinha:
		carregaCerto(null, j("#id_login").val() ); // + bind()
		return true;

		
		separa = txt.split("_");
		var a = document.getElementById("vacina"+separa[1]+8);
		//alert(a.value);
//		
//		alert(txt);
	//	a.setAttribute("class", "dosesVacina");
		if (separa[0] > 0){
			a.innerHTML = "<b>"+separa[0]+"</b>";
		}else{
			a.innerHTML = "<b>0</b>";
			return false;
		}
		//deletaVacina(id);	
	}
}
function sucesso(txt)
{

	if (txt == "false"){
		alert('Houve um erro e a acao nao foi salva, tente novamente.');
	}else{

		// recarrega toda a carteirinha:
		carregaCerto(null, j("#id_login").val() ); // + bind()
		return true;
		
		//alert(txt);
		separa = txt.split("_");
		var a = document.getElementById(separa[0]);
		a.setAttribute("class", "dosesVacina");
		a.innerHTML = "&nbsp;";


		//separa = txt.split("_");
		var b = document.getElementById("vacina"+separa[2]+8);
		b.innerHTML = "<b>"+separa[1]+"<b>";
		//alert(a.value);
//		
//		alert(txt);
	//	a.setAttribute("class", "dosesVacina");
		//b.innerHTML = separa[0];
		//deletaVacina(id);	
	}
}
function abrir(pro_codigo,lote,id_login,validade,id,descartar){
//	alert(pro);
// alert(lote);
	if(lote == ''){
		alert('Vacina sem estoque');
		return false;
	}
	var prod_dose = id.replace("vacina","");
	produto = "pro_codigo"+prod_dose.substring(0,2);
	dose= prod_dose.substring(2);	
	linha = prod_dose.substring(0,2);
	url = "controlarDoses.php?ite_lote="+lote+"&pro_codigo="+pro_codigo+"&id_login="+id_login+"&ite_validade="+validade+"&linha="+linha+"&descartar="+descartar;
	ajax_tudo(url,respostaSucesso);
}
function respostaSucesso(txt){	
	separa = txt.split("_");
	ite_dose = separa[0];
	ite_lote = separa[1];
	qtde = separa[2];
	linha = separa[3];
	ite_codigo= separa[4];	
	var b = document.getElementById("vacina"+separa[3]+8);
	b.innerHTML = "<b>"+separa[0]+"</b>";


	var a = document.getElementById("vacina"+separa[3]+7);
	if(ite_lote != ''){
		a.innerHTML = "<b>"+ite_lote+" / "+separa[2]+"</b>";
	}else{
		a.innerHTML = "Sem estoque";
	}
	
	
}

function executaAcao(id, data, unidade_cod,resposta){
    var a = document.getElementById(id);// a Ã© um td... que id por sua vez Ã© seu id sequencial.
	var separaCodigoNome = unidade_cod.split('|');
	var unidade_codigo = separaCodigoNome[0];
	var unidade_nome = separaCodigoNome[1];
	switch(resposta){
		case "A":
		case "P":
		case "Z":
			salvaVacina(resposta,data,unidade_cod,id);
			break;
		case "C":
			a.setAttribute("class", "dosesVacina");
			a.innerHTML = "";
			deletaVacina(id);

	}

}

//////////////////////////
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

--></script>

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
					<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg' style='vertical-align:middle' border='0' alt='Localizar' />
				</a>(F7)
		</td>
	</tr>-->

<table>
<? 
echo "<input type='hidden' name='id_login' id='id_login' value='$id_login'>";
		echo "<tr>";
                                                echo "<td>Prontuario:</td>";
                                                echo "<td>";
                                                        echo "<input type=text name='pac_codigo' id='pac_codigo' class=boxNumero readonly value='$pac[usu_codigo]'>";
                                                        echo "</td>";


                                                echo "<td>Paciente</td>";
                                                echo "<td>";
                                                        echo "<input autocomplete=\"off\" type=text name=pac_nome id=pac_nome value='$pac[usu_nome]' class=boxTexto onkeyup=\"buscar_nome(this.value);\" style=\"text-transform:uppercase;\" onkeypress=\"if(event.keyCode == 13)buscar_nome(\$F('pac_nome'), 'buscar_nome')\">";
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
							 <td><span onClick='carregaCerto(this,$_REQUEST[id_login]);' style='cursor: pointer;'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_on.jpg'></span></td>
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
			<table border='0'> <!-- tabela de operaÃ§Ãµes -->
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
			
			
			<table> <!-- botÃµes de impressao -->
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
						<span onClick='return imprimeAtestado($_REQUEST[id_login]);' style='cursor: pointer;'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/imprimir_on.jpg' / style='padding-right:40px'></span>
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
				<tr>
					<td class='legendaVarios'></td>
					<td><b>Varios Reforços</b></td>
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
<div id="sys"><!-- usado pelo jQuery --></div>

</body>
</html>