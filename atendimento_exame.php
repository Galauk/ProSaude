<?php
/**
 @brief Inclusao principal para montagem do sistema
*/
session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
//verauth($id_login);
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
cabecario();
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";

//------------------------------------------------------------------>

?>

<script type="text/javascript" src="ajax_motor.js"></script>
<script type="text/javascript" src="funcoes.js"></script>
<script type="text/javascript">
<!--


function changeLocation(menuObj)
{
   var i = menuObj.selectedIndex;

   if(i > 0)
   {
      window.location = menuObj.options[i].value;
   }
}
 

function form_arruma_data()
{
    var DH = $('data');
    var D = $('datar');
    var F = $('formm');
    DH.value = D.value;
    F.action += '&data='+D.value;
    //F.target = 'frameprincipal';
    //F.submit();
    //return false;
    return true;
}

-->
</script>
<?php

reglog($id_login,"Acesando Atendimento de Exames");

echo "
	<fieldset>
	<legend>Op&ccedil;&otilde;es</legend>
		".ChmodBtn($id_login,'fazeragendamento','agendar_exame.php?')."
		".ChmodBtn($id_login,'manutencao_agenda_exames','manutencao_exame.php?')."
		".ChmodBtn($id_login,'manutencao_exames','manutencao_exame_mensal.php?')."
		".ChmodBtn($id_login,'procedimento','procedimento.php?')."
		".ChmodBtn($id_login,'laboratorio','laboratorio.php?')."
	</fieldset>
	";



/**
* Formulario principal
*/

monta_calendario();    

//atendimento_exame_agt_iframe.php
echo "
<form id='formm' method='post' action='{$PHP_SELF}?id_login={$id_login}&med_codigo={$med_codigo}' onsubmit='return form_arruma_data()'>
<input type='hidden' nname='data'id='data' value='' />

<table border=0>
<tr>
	<td align='right'>Laborat&oacute;rio</td>
	<td>	
	<select name=uni_null class='box' onChange=\"javascript:changeLocation(this)\">";
	echo "<option>-- Escolha um --</option>";
		
	$sql = db_query("SELECT * FROM medico WHERE prestador_servico='S' ORDER BY med_nome");
	while($med=pg_fetch_array($sql)) 
	{
		$location = "$PHP_SELF?id_login=$id_login&med_codigo=$med[med_codigo]";
		
		echo ($med[med_codigo]==$med_codigo)?
		"\n<option value='$location' selected>$med[med_nome]</option>":
		"\n<option value='$location'>$med[med_nome]</option>";
	}
		
	echo "
		</select>
		</td>
	</tr>
	<tr>
		<td align='right' valign='midle'>Data</td>
		<td valign='midle'>
			<input type='text' name='data' class='boxn' id='datar' size='12' maxlength='10' readonly />
            <img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/calendario.png' alt='Data' style='vertical-align:middle; cursor:pointer;'
                onclick=\"abrirCalendario('datar')\" />
            &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
            <input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/enviar_on.jpg' alt='Enviar Dados' style='vertical-align:middle; cursor:pointer;'/>
        </td>
    </tr>
    
</form>
</table>";

/**
Tabela do iframe
*/
	echo "
	<table>
	<tr>
		<td align='center'>
		<iframe name='frameprincipal'
		 src='atendimento_exame_iframe.php?{$QUERY_STRING}' 
         frameborder='no' marginheight='0' marginwidth='0' scrolling='yes' width='100%' height='170'></iframe>
		</td>
	</tr>
	</table>";
	
//------------------------------------------------------------------>

print "
</body>
</html>
";

?>
