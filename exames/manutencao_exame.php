<?php
session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
//verauth($id_login);

require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
cabecario();
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";

//------------------------------------------------------------------>

?>
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
 

 function buscaferiado(ver) {
  var data = ver;
  var url = 'age_ajax/busca_feriado.php?data='+data;
	 if(data!='') {
		ajax(url);
	 }
 }
 
function atualiza_dia( dia )
{
	var url = "<?php print "{$PHP_SELF}?id_login={$id_login}&med_codigo={$med_codigo}&proc_codigo_busca={$proc_codigo_busca}&uni_codigo={$uni_codigo}&agt_codigo={$agt_codigo}&id_dia="; ?>"+dia;
	//alert( url );
	document.location.href = url;
}

function atualiza_busca_vagas( obj )
{
	var url = "<?php print "{$PHP_SELF}?id_login={$id_login}&med_codigo={$med_codigo}&id_dia={$id_dia}&uni_codigo={$uni_codigo}&agt_codigo={$agt_codigo}&proc_codigo_busca=" ; ?>"+obj.value;
	//alert( url );
	document.location.href = url;
}
-->
</script>
<?php

reglog($id_login,"Acesando Manutenção de Exames");

echo "
	<fieldset>
	<legend>Opções</legend>";
		if(SelPerm($id_login,'agendar_exame.php') != "0")
		{
			echo ChmodBtn($id_login,'fazeragendamento','agendar_exame.php?');
		} else {
			echo "<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/fazeragendamento_off.jpg' />";
		}
		if(SelPerm($id_login,'manutencao_exame.php') != "0")
		{
			echo ChmodBtn($id_login,'manutencao_agenda_exames','manutencao_exame.php?');
		} else {
			echo "<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/manutencao_agenda_exames_off.jpg' />";
		}
		if(SelPerm($id_login,'manutencao_exame_mensal.php') != "0")
		{
			echo ChmodBtn($id_login,'manutencao_exames','manutencao_exame_mensal.php?');
		} else {
			echo "<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/manutencao_exames_off.jpg' />";
		}
		if(SelPerm($id_login,'procedimento.php') != "0")
		{
			echo ChmodBtn($id_login,'procedimento','procedimento.php?');
		} else {
			echo "<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/procedimento_off.jpg' />";
		}
		if(SelPerm($id_login,'laboratorio.php') != "0")
		{
			echo ChmodBtn($id_login,'laboratorio','laboratorio.php?');
		} else {
			echo "<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/laboratorio_off.jpg' />";
		}
	echo "</fieldset>
	<br>";



//
//-> Botoes
echo "
<form name='forms' method='post' action='$PHP_SELF?$QUERY_STRING'>
<input type='hidden' name='id_login' value='$id_login'>
<input type='hidden' name='med_codigo' value='$med_codigo'>
<input type='hidden' name='proc_codigo' value='$proc_codigo'>
<input type='hidden' name=acao value='mostra_age'>
<table border=0>

<tr>
    <td width='100' align='right'><label for='uni_codigo'>Unidade</label></td>
    <td>
        <select name='uni_codigo'  id='uni_codigo' class='box' onChange=\"changeLocation(this)\">
         <option value='0'>-- Escolha uma --</option>";
     $qry_uni = db_query("SELECT uni_codigo, uni_desc FROM unidade ORDER BY 2");
     while( $row_uni = pg_fetch_array($qry_uni) )
    {
        $location = "$_SERVER[PHP_SELF]?id_login=$id_login&med_codigo=$med_codigo".
		    "&gex_tipo=$gex_tipo&uni_codigo={$row_uni[0]}&agt_codigo={$agt_codigo}";

        $sel = ( $uni_codigo == $row_uni[0] ? ' selected="selected"' : '' );
        //print "\n\t\t\t\t<option value='{$row_uni[0]}'{$sel}>{$row_uni[1]}</option>";
        print "\n\t\t\t\t<option value='{$location}'{$sel}>{$row_uni[1]}</option>";
   }

print "
    </select>
    </td>
</tr>    
<tr>
    <td align='right'><label for='agt_codigo'>Agente</label></td>
    <td>
    <select name='agt_codigo' id='agt_codigo' class='box' onChange=\"changeLocation(this)\">
        <option value='-1'>-- Escolha um --</option>";
            $uni_codigo = intval($uni_codigo);
            $stmt_agt = "SELECT agt_codigo, agt_numero, COALESCE(agt_responsavel,agt_descricao) 
            FROM agente WHERE uni_codigo = {$uni_codigo} ORDER BY 3";
            $qry_agt = db_query($stmt_agt);
            while( $row_agt = pg_fetch_array($qry_agt) )
            {
               	$location = "$_SERVER[PHP_SELF]?id_login=$id_login&med_codigo=$med_codigo".
			        "&gex_tipo=$gex_tipo&uni_codigo={$uni_codigo}&agt_codigo={$row_agt[0]}";
                $sel = ( $agt_codigo == $row_agt[0] ? ' selected="selected"' : '' );
                //print "\n\t\t\t\t<option value='{$row_agt[0]}'{$sel}>{$row_agt[2]}</option>";
                print "\n\t\t\t\t<option value='{$location}'{$sel}>{$row_agt[2]}</option>";
            }
    print "
        </select>
    </td>
</tr>
<tr>
	<td align='right'>Laborat&oacute;rio</td>
	<td>	
	<select name=uni_null class='box' onChange=\"javascript:changeLocation(this)\">";
	echo "<option>-- Escolha um --</option>";
		
	$sql = db_query("SELECT * FROM medico WHERE prestador_servico='S' ORDER BY med_nome");
	while($med=pg_fetch_array($sql)) 
	{
		$location = "$PHP_SELF?id_login=$id_login&med_codigo=$med[med_codigo]&proc_codigo=$proc_codigo".
			"&uni_codigo={$uni_codigo}&agt_codigo={$agt_codigo}";
		
		echo ($med[med_codigo]==$med_codigo)?
		"\n<option value='$location' selected>$med[med_nome]</option>":
		"\n<option value='$location'>$med[med_nome]</option>";
	}
		
	$exp = explode("/",$id_dia);
	$ALLSEMANA = date('w', mktime(0,0,0,$exp[1],$exp[0],$exp[2]));
	echo "
		</select>
		</td>
		<td>&nbsp;</td>
	</tr>
";

// valida a data se é feriado ou se é final de semana.

echo "
<form name='diamanutencao' method='post' action='$PHP_SELF'>
	<tr height='30'>
		<td align='right' valign='midle'>Data</td>
		<td valign='midle'>
			<input type='text' name='id_dia' class='boxn' id='data' size='12' maxlength='10'
				onKeypress=\"return Ajusta_Data(this, event);\" onchange=\"buscaferiado(this.value);\" value=\"$id_dia\">
			
		</td>
		<td><div id='horario'>&nbsp;</div></td>
    </tr>
	<!--<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td align='right'>
			<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagarlinha.jpg' width='14' height='14' align='absmiddle'>
				&nbsp;Apagar Registro&nbsp;
			<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/gravar.jpg' width='14' height='14' align='absmiddle'>
				&nbsp;Gravar Dados
		</td>
		</td>
	</tr>-->
</form>
";

//----------------------------------------------------
echo "
</table>";

/**
Tabela do iframe
*/
	echo "
	<table>
	<tr>
		<td align='center'>
		<iframe name='frameprincipal'
		 src='manutencao_exame_iframe.php?id_dia=$id_dia&med_codigo=$med_codigo&proc_codigo=$proc_codigo&id_login=$id_login&uni_codigo={$uni_codigo}&agt_codigo={$agt_codigo}' 
         frameborder='no' marginheight='0' marginwidth='0' scrolling='yes' width='100%' height='170'></iframe>
		</td>
	</tr>
	</table>";
	
//------------------------------------------------------------------>
if( $med_codigo )
{
    $stmt_proc = "SELECT p.proc_codigo, proc_nome 
		FROM laboratorio_procedimento AS lp
		INNER JOIN procedimento AS p ON p.proc_codigo = lp.proc_codigo
        WHERE lp.med_codigo=$med_codigo";
	$qry_proc = db_query($stmt_proc);

	print "
	<p><label>Verificar vagas por procedimento:
	<select name='proc_codigo_busca' class='box' onchange='atualiza_busca_vagas( this )'>
		<option value='0'>--Escolha um --</option>";

	while( $row_proc = pg_fetch_array ($qry_proc)	 ) 
		print "\n\t\t<option value='$row_proc[0]'".($row_proc[0] == $proc_codigo_busca ? ' selected' : '') .">$row_proc[1]</option>";

	print "\n\t</select></label></p>";

	//$med_row = db_getRow("SELECT proc_tipo_manut FROM medico WHERE med_codigo = $med_codigo");

	//if( $med_row['proc_tipo_manut'] == 1 )
	//{
		require_once "calendario.inc.php";
		print '<link rel="stylesheet" href="estilo_calendario.css" title="Calendario">';

		$Calendario_A = new Calendario( date('m'), date('Y') );
		$data_prox = $Calendario_A->getProxCal();

		$data_prox = explode( "/", $data_prox);
		$Calendario_B = new Calendario( $data_prox[0], $data_prox[1] );
		$data_prox = $Calendario_A->getProxCal();

		while( $dia = $Calendario_A->temDias() )
		{
			if( $dia->dia_semana > 0 && $dia->dia_semana < 6 && ! $dia->feriado )
			{
				$Calendario_A->setLink( $dia->dia, "javascript:atualiza_dia('".$dia->format( $full = true )."')");
				if( $proc_codigo_busca )
				{
					$st = "SELECT procedimento_vagas_manutencao_agt( {$med_codigo}, {$proc_codigo_busca},'".$dia->format($full=true)."'::date , {$agt_codigo}::int8)";
					$q = db_get($st);
					$Calendario_A->setTexto( $dia->dia, "($q)");
				}
			}	
		}

		while( $diab = $Calendario_B->temDias() )
		{
			if( $diab->dia_semana > 0 && $diab->dia_semana < 6 && ! $diab->feriado )
			{
				$Calendario_B->setLink( $diab->dia, "javascript:atualiza_dia('".$diab->format( $full = true )."')");
				if( $proc_codigo_busca )
				{
					$st = "SELECT procedimento_vagas_manutencao_agt({$med_codigo},{$proc_codigo_busca},'".$diab->format($full=true)."'::date, {$agt_codigo}::int8 )";
					$q = db_get($st);
					$Calendario_B->setTexto( $diab->dia, "($q)");
				}
			}	
		}
		print '
		<table>
		<tr>
			<td>'.$Calendario_A->toHtml( 1 ) . '</td>
			<td>'.$Calendario_B->toHtml( 1 ).'</td>
		</tr>
		</table>';
	//}
	
} // med_codigo
?>
