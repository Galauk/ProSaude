<?php
/**
 * Agendamento de Exames
*/
session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
//verauth($id_login);
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
cabecario();
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";


reglog($id_login,"Acessando Agendamento de Exames");
//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>
?>

<script type="text/javascript"><!--
function changeLocation(menuObj)
{
   var i = menuObj.selectedIndex;

   if(i > 0)
   {
      window.location = menuObj.options[i].value;
   }
}

 function agente(obj,obj2) {
   document.agendamento.num_agt.value = +obj;
   document.agendamento.nam_agt.value = +obj2;
}

function executeAcao() {
// var head = document.getElementsByTagName('head')[0];
// 	var eScript = document.createElement("script");
// 	id_registro = document.getElementById('teste').value;
// 	eScript.setAttribute('src','select_paciente.php?id_registro='+id_registro);
// 	head.appendChild(eScript);
	//alert(eScript);
	alert('euuu');
}

--></script>

<?php
echo "
<script>
//function pacientes(nome,codigo,nascimento,mae,cidade) {
function pacientes(codigo,nome,nascimento,mae,cidade) {
	document.agendamento.pac_nome.value = nome;
	//document.agendamento.pac_codigo.value = codigo;
	document.agendamento.usu_codigo.value = codigo;
	document.agendamento.pac_nascimento.value = nascimento;
	document.agendamento.pac_mae.value = mae;
	document.agendamento.pac_cidade.value = cidade;
	document.agendamento.r_esp_codigo.value = 0;
}

</script>";

//------------------------------------------------------------------>
// botões
//------------------------------------------------------------------>
echo "
	<!--<fieldset>
	<legend>Op&ccedil;&otilde;es</legend>
		".ChmodBtn($id_login,'fazeragendamento','agendar_exame.php?')."
		".ChmodBtn($id_login,'manutencao_agenda_exames','manutencaoagendaexame.php?')."
		".ChmodBtn($id_login,'manutencao_exames','manutencao_exame_mensal.php?')."
		".ChmodBtn($id_login,'procedimento','procedimento.php?')."
		".ChmodBtn($id_login,'laboratorio','laboratorio.php?')."
	</fieldset>-->";


//------------------------------------------------------------------>
// dados do paciente
//------------------------------------------------------------------>
echo "
<form method='GET' action='agendar_exame_iframe1.php?' name='agendamento' target='fazer'>
<input type='hidden' name='id_login' value='$id_login'>

<fieldset>
<legend>Dados do Exame</legend>
<fieldset>
<legend>Dados do Paciente</legend>

	<table>
	<tr>
		<td width=113 align=right>Numero do Paciente</td>
		<td><input type=text name='usu_codigo' id='teste' class=boxl size=10 OnChange='executeAcao()' value='$pac_codigo' readonly></td>
		<td align=right>Paciente</td>
		<td><input type=text name=pac_nome class=boxl size=60 value='$pac_nome' readonly><a href='#' OnClick='window.open(\"list_pacientes.php?id_login=$id_login&controle=1\",null,\"height=460,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes\");'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg align=absmiddle border=0></a></td>
		<td>Nascimento</td>
		<td><input type=text name=pac_nascimento class=boxl size=14 value='$pac_nascimento' readonly></td>
	</tr>
	</table>
	<table>
	<tr>
		<td width=233 align=right>Mãe</td>
		<td width=100><input type=text name=pac_mae class=boxl size=50 value='$pac_mae' readonly></td>
		<td width=40 align=right>Cidade</td>
		<td width=60><input type=text name=pac_cidade class=boxl size=20 value='$pac_cidade' value='$pac_cidade' rfazereadonly></td>
		<td><a href='#' OnClick='window.open(\"paciente_ficha.php?id_login=$id_login&acao=form_add&controle=1\",null,\"height=460,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes\");'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/ficha_on.jpg border=0></a></td>
	</tr>
	</table>
	
</fieldset>
";

//------------------------------------------------------------------>
// escolha do médico
//------------------------------------------------------------------>

// javascript

echo '
<script type="text/javascript" src="ajax_motor.js"></script>
<script type="text/javascript">
function escolhe_medico()
{
	var esp = document.getElementById("r_esp_codigo");
	if( ! esp.value || esp.value == 0 )
	{
		alert("Escolha uma especialidade !");
		return false;
	}
	var endereco = "ajax/operacao/agendar_exame_med.php?esp_codigo=" + esp.value;
	ajax_tudo( endereco, atualiza );
}
function atualiza( txt )
{
	var med = document.getElementById("td_med_codigo");
	med.innerHTML = txt;
	
}
function atualiza_agente( obj )
{
    if( ! obj.value || obj.value == 0 ) return;
    var endereco = "agendar_exame_op.php?acao=agente&uni_codigo="+obj.value;

    var Agt  = document.getElementById("agt_codigo");
	Agt.length = 1;
	Agt.options[0].value = 0;
	Agt.options[0].text = "...carregando..." ;

    ajax_tudo( endereco, atualiza_agente_cbk );
}
function atualiza_agente_cbk(txt)
{
	var AgtArr = ( eval(txt) );
	var AgtSel = document.getElementById("agt_codigo");
	
	AgtSel.length = 0;

    if( AgtArr.length == 0 )
        AgtSel.options[ 0 ] = new Option( "-- Sem agentes --",  0 );


	for( var i=0; i < AgtArr.length; i++ )
	{
		AgtArr[ i ].agt_desc = unescape( AgtArr[ i ].agt_desc );
		AgtSel.options[ AgtSel.options.length ] = new Option( AgtArr[ i ].agt_desc,  AgtArr[ i ].agt_codigo );
	}
}
</script>
';

// formulario
echo "
<fieldset>
<legend>M&eacute;dico Respons&aacute;vel / Agente</legend>
<table>
	<tr>
		<td width='120'>Especialidade</td>
		<td width='200'>
			<select name=\"r_esp_codigo\" id=\"r_esp_codigo\" class=\"box\" onchange=\"escolhe_medico()\">
				<option value='0' checked='checked'>...</option>\n";
			
		$stmt = "SELECT esp_codigo, esp_nome FROM especialidade ORDER BY esp_nome";
		$qry = pg_query( $stmt );
		
		while( $row = pg_fetch_array($qry) )
		{
			echo "\n\t\t<option value='$row[0]'>$row[1]</option>";
		}
		
		echo "	
			</select>
		</td>";
	     echo "<td rowspan='2' class='c'><input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/enviar_on.jpg'></td>";
        echo "
	</tr>
	<tr>
		<td>M&eacute;dico</td>
		<td id='td_med_codigo'>[ escolha uma especialidade ]</td>
	</tr>
    <tr>
        <td><label for='uni_codigo'>Unidade</label></td>
        <td>
            <select name='uni_codigo'  id='uni_codigo' class='box' onchange=\"atualiza_agente(this)\">
                <option value='0'>-- Escolha uma --</option>";
        $qry_uni = db_query("SELECT uni_codigo, uni_desc FROM unidade ORDER BY 2");
        while( $row_uni = pg_fetch_array($qry_uni) )
        {
            print "\n\t\t\t\t<option value='{$row_uni[0]}'>{$row_uni[1]}</option>";
        }


print "
            </select>
        </td>
    </tr>    
    <tr>
        <td><label for='agt_codigo'>Agente</label></td>
        <td>
            <select name='agt_codigo' id='agt_codigo' class='box'>
                <option value='-1'>-- Escolha uma unidade antes --</option>
            </select>
        </td>
    </tr>
 </table>

</fieldset>

</fieldset>
</form>
";

//------------------------------------------------------------------>
// iframe
//------------------------------------------------------------------>

echo "
	<fieldset>
	<legend>Agendamento</legend>
	<iframe name='fazer' src='agendar_exame_iframe1.php?' 
	frameborder='no' marginheight='0' marginwidth='0' scrolling='yes' width='100%' height='400'></iframe>
	</fieldset>
	
</body>
</html>
";

?>
