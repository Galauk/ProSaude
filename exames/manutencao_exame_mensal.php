<?php
/**
 * Arquivo responsavel pela manutencao "MENSAL" do agendamento
 * - unidade
 * - medico (laboratorio)
 * - procedimento
 * - agente (iframe que manuseia)
 * @warning
 * Dependencias:
 * - manutencao_exame_mensal_iframe.php
 * -- manutencao_exame_mensal_iframe_ajax.php
*/
session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
//verauth($id_login);
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
cabecario();
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";

?>
<script type="text/javascript" src="funcoes.js"></script>
<script type="text/javascript">
function changeLocation(menuObj)
{
   var i = menuObj.selectedIndex;

   if(i > 0)
   {
      window.location = menuObj.options[i].value;
   }
}

function valida_form_data( )
{
	vdata = document.getElementById('data');
	if( vdata.value.length != 10 )
	{
		alert('O campo "Novo Periodo" deve ser preenchido corretamente !');
		vdata.focus();
		return false;
	}
}
</script>

<?php

print "
	<fieldset>
	<legend>Op&ccedil;&otilde;es</legend>";
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
	echo "</fieldset>";

reglog($id_login,"Acessando Manutencao de Exames");

// variaveis
$med_codigo 	= intval($_GET['med_codigo']);
$uni_codigo 	= intval($_GET['uni_codigo']);
$proc_codigo 	= intval($_GET['proc_codigo']);

//------------------------------------------------------------------>
//-> Cadastra um novo período
//------------------------------------------------------------------>

if( $acao == "nova_data" )
{
	
	// qual o tipo do laboratorio ?
	$stmt_lab = "SELECT proc_tipo_manut FROM medico WHERE med_codigo='$med_codigo'";
	$manut_row = db_getRow($stmt_lab);
	
	// o select verifica se a data entrada está num intervalo válido
	// pelo menos 30 dias maior que o maior entrado
	
	if( $manut_row['proc_tipo_manut'] == 1 )
	{
		$stmt = "SELECT 
		MAX(gex_periodo) as max, 
		('$gex_periodo' > MAX(gex_periodo)+29) as ok_max ,
		TO_CHAR(MAX(gex_periodo)+30,'dd/mm/YYYY') as prox_max
		FROM grade_exame_mensal AS m
        LEFT JOIN agente AS a ON a.agt_codigo = m.agt_codigo
		WHERE med_codigo = {$med_codigo} AND a.uni_codigo = {$uni_codigo}";//AND agt_codigo = {$agt_codigo}";
        
	}
	else
	{
		$stmt = "SELECT 
		MAX(gem_periodo) as max, 
		('$gex_periodo' > MAX(gem_periodo)+29) as ok_max ,
		TO_CHAR(MAX(gem_periodo)+30,'dd/mm/YYYY') as prox_max
		FROM grade_exame_mensal_manut AS m
        LEFT JOIN agente AS a ON a.agt_codigo = m.agt_codigo
		WHERE med_codigo = {$med_codigo} AND a.uni_codigo = {$uni_codigo}";//AND agt_codigo = {$agt_codigo}";
	}
	
	//print $stmt; exit;
	$per_row = db_getRow($stmt);
	
	// se houver uma data e náo for verdadeiro
	//if( $per_row['max'] && $per_row['ok_max'] != 't' )
	if( $per_row['max'] && $per_row['ok_max'] == 'f' )
	{
		print "
		<script type=\"text/javascript\">
			alert(\"O valor entrado para 'Novo Periodo' é inválido!\\nO próximo período disponível é: $per_row[prox_max]\")
			setTimeout(\"location='$PHP_SELF?id_login=$id_login&med_codigo=$med_codigo&gex_periodo={$gex_periodo}&navdata=$navdata&uni_codigo={$uni_codigo}&proc_codigo={$proc_codigo}'\", 0);
		</script>";

	}
	else
	{
		reglog($id_login,"Adicionando nova data para a Manutencao do Exame Cod: $proc_codigo");

		if( $manut_row['proc_tipo_manut'] == 1 )
		{

			$st = "SELECT agt_codigo FROM agente WHERE uni_codigo='$uni_codigo'";
			$sql = db_query($st);
		
			if( pg_num_rows($sql) == 0 )
			{
				print "
				<script type=\"text/javascript\">
					alert(\"Nenhum agente cadastrado para esta Unidade!\");
				</script>";
			}
			
			$q = "BEGIN;\n";
			
			while( $agt = pg_fetch_array($sql))
			{
				$q .= "INSERT into grade_exame_mensal ( " .
					"med_codigo, " .
					"gex_qtde, " .
					"gex_valor, ".
					//"gex_tipo, ".
					"proc_codigo, " .
					"gex_periodo, ".
					"usr_codigo_cad, " .
                    "agt_codigo ".
					") values ( " .
					"'$med_codigo', " .
					"0, " .
					"0, ".
					//"'$gex_tipo', ".
					"'$proc_codigo', " .
					"'$gex_periodo', " .
					"'$id_login', ".
                    "$agt[agt_codigo] ".
					");\n";

			} // while
			
			$q .= "COMMIT;";
			//print "<pre>$q</pre>";
			$rq = db_query($q);		
			
			$location = "$PHP_SELF?id_login=$id_login&med_codigo=$med_codigo&gex_periodo=$gex_periodo".
				"&navdata=$navdata&uni_codigo={$uni_codigo}&proc_codigo={$proc_codigo}";
				
			print "
			<script type=\"text/javascript\">
				setTimeout(\"location='$location'\", 0);
			</script>";

		} // if proc_tipo_manut
		else
		{
            $st = "SELECT agt_codigo FROM agente WHERE uni_codigo='$uni_codigo'";
			$sql = db_query($st);
		
			if( pg_num_rows($sql) == 0 )
			{
				print "
				<script type=\"text/javascript\">
					alert(\"Nenhum agente cadastrado para esta Unidade!\");
				</script>";
			}
			
			$q = "BEGIN;\n";
			
			while( $agt = pg_fetch_array($sql))
			{
				$q .= "INSERT into grade_exame_mensal_manut ( " .
					"med_codigo, " .
					"gem_valor, ".
					"gem_periodo, ".
					"usr_codigo_cad, " .
                    "agt_codigo ".
					") values ( " .
					"'$med_codigo', " .
					"0, " .
					"'$gex_periodo', " .
					"'$id_login', ".
                    "$agt[agt_codigo] ".
					");\n";

			} // while
			
			$q .= "COMMIT;";
			//print "<pre>$q</pre>";
			$rq = db_query($q);		
			
			$location = "$PHP_SELF?id_login=$id_login&med_codigo=$med_codigo&gex_periodo=$gex_periodo".
				"&navdata=$navdata&uni_codigo={$uni_codigo}&proc_codigo={$proc_codigo}";
			
            //$alert = "O laboratorio escolhido faz a manutencao por valor. \\n".
            //    "[ARRUMAR EU]Por favor escolha o agente e o valor do periodo !";
                
			print "
			<script type=\"text/javascript\">
                //alert('$alert');    
				setTimeout(\"location='$location'\", 0);
			</script>";
        
		}
		
	} // if

} //if($act=="newdate") {
//------------------------------------------------------------------>
//-> Mostra os laboratorios, procedimentos e unidades
//------------------------------------------------------------------>
else if( empty($acao) )
{

	$action = $_SERVER['PHP_SELF']."?id_login={$id_login}&acao=nova_data&med_codigo={$med_codigo}".
		"&gex_tipo={$gex_tipo}&uni_codigo={$uni_codigo}&proc_codigo={$proc_codigo}";

	print "
	<form method=post action='{$action}' onsubmit=\"return valida_form_data()\">

	<fieldset>
	<legend>Manuten&ccedil;&atilde;o dos Exames</legend>
	
	<table>
    <tr>
        <td width='100' align='right'><label for='uni_codigo'>Unidade</label></td>
        <td colspan='2'>
            <select name='uni_codigo'  id='uni_codigo' class='box' onChange=\"changeLocation(this)\">
                <option value='0'>-- Escolha uma --</option>";
                $qry_uni = db_query("SELECT uni_codigo, uni_desc FROM unidade ORDER BY 2");
                while( $row_uni = pg_fetch_array($qry_uni) )
                {
                   	//$location = "$_SERVER[PHP_SELF]?id_login=$id_login&med_codigo=$med_codigo".
				      //  "&gex_periodo={$gex_periodo}&uni_codigo={$row_uni[0]}&proc_codigo={$proc_codigo}";
                    
                    $location = "$_SERVER[PHP_SELF]?id_login=$id_login&med_codigo=$med_codigo".
				        "&uni_codigo={$row_uni[0]}&proc_codigo={$proc_codigo}";  

                    $sel = ( $uni_codigo == $row_uni[0] ? ' selected="selected"' : '' );
                    print "\n\t\t\t\t<option value='{$location}'{$sel}>{$row_uni[1]}</option>";
                }
    print "
        </select>
        </td>
    </tr>    
	<tr>
		<td align='right'>Laborat&oacute;rio</td>
		<td colspan='2'>
		<select name=med_codigo class=box onChange=\"javascript:changeLocation(this)\">
			<option>-- Escolha um --</option>";
	
		$sql = db_query("select * from medico where prestador_servico='S' order by med_nome");
		while($med=pg_fetch_array($sql))
		{
			//$location = "$_SERVER[PHP_SELF]?id_login=$id_login&med_codigo=$med[med_codigo]".
			//	"&gex_periodo={$gex_periodo}&uni_codigo={$uni_codigo}&proc_codigo={$proc_codigo}";
			$location = "$_SERVER[PHP_SELF]?id_login=$id_login&med_codigo=$med[med_codigo]".
				"&uni_codigo={$uni_codigo}&proc_codigo={$proc_codigo}";
                
			print ( $med_codigo == $med['med_codigo'] )?
				"<option value='$location' selected>$med[med_nome]</option>":
				"<option value='$location'>$med[med_nome]</option>\n";
		}
	  		
	print "
		</select>
		</td>
	</tr>
	<tr>
        <td align='right'><label for='agt_codigo'>Procedimento</label></td>
        <td colspan='2'>
        <select name='proc_codigo' id='proc_codigo' class='box' onChange=\"changeLocation(this)\">
            <option value='-1'>-- Escolha um --</option>";

				$stmt_proc = "SELECT p.proc_codigo, p.proc_nome 
					FROM laboratorio_procedimento AS lp 
					INNER JOIN procedimento AS p ON lp.proc_codigo = p.proc_codigo
					WHERE lp.med_codigo = {$med_codigo} ORDER BY 2";

				$qry_proc = db_query($stmt_proc);
				
                while( $row_proc = pg_fetch_array($qry_proc) )
                {
                   	$location = "$_SERVER[PHP_SELF]?id_login=$id_login&med_codigo=$med_codigo".
				        "&gex_periodo={$gex_periodo}&uni_codigo={$uni_codigo}&proc_codigo={$row_proc[0]}";
                    
					$sel = ( $proc_codigo == $row_proc[0] ? ' selected="selected"' : '' );
                    
                    print "\n\t\t\t\t<option value='{$location}'{$sel}>{$row_proc[1]}</option>";
                }
        print "
            </select>
        </td>
    </tr>
	<tr>
		<td align='right'>Data</td>
		<td width='365'>
		<select name='gex_periodo' class='box' style='width:90px'
			onChange=\"javascript:changeLocation(this)\">
			<option selected>-- Escolha --</option>\n"; 


		$med_row = db_getRow("SELECT proc_tipo_manut FROM medico WHERE med_codigo = $med_codigo");

		if( $med_row['proc_tipo_manut'] == 1 )
		{
			$stmt = "SELECT to_char(gex_periodo,'DD/MM/YYYY') as gex_periodo,gex_periodo as gex_periodo2 
				from grade_exame_mensal as m
                LEFT JOIN agente AS a ON a.agt_codigo = m.agt_codigo
                where med_codigo='$med_codigo' and a.uni_codigo='$uni_codigo' ".
				//AND agt_codigo='$agt_codigo' ".
				"group by gex_periodo order by to_char(gex_periodo,'YYY-mm-dd') desc";
		} 
		else
		{
			$stmt = "SELECT to_char(gem_periodo,'DD/MM/YYYY') as gex_periodo,gem_periodo as gex_periodo2 
				from grade_exame_mensal_manut as m
                LEFT JOIN agente AS a ON a.agt_codigo = m.agt_codigo
                where med_codigo='$med_codigo' and a.uni_codigo='$uni_codigo' ".
				//AND agt_codigo='$agt_codigo' ".
				"group by gem_periodo order by to_char(gem_periodo,'YYY-mm-dd') desc";
		}
		
		//print $stmt;
		$query = db_query( $stmt );

		while($dt=pg_fetch_array($query)) 
		{
			$location = "$_SERVER[PHP_SELF]?id_login=$id_login&gex_periodo=$dt[gex_periodo2]".
			"&med_codigo={$med_codigo}&uni_codigo={$uni_codigo}&proc_codigo={$proc_codigo}";
			
            //"&med_codigo=$med_codigo&select=periodo&gex_periodo={$gex_periodo}
            //&uni_codigo={$uni_codigo}&agt_codigo={$agt_codigo}";

			print ( $gex_periodo == $dt['gex_periodo2'] )?
			"\n<option value='$location' selected>$dt[gex_periodo]</option>":
			"\n<option value='$location'>$dt[gex_periodo]</option>";
		}
			
		print "</select>\n";

		
		if( $gex_periodo )
		{

			if( $med_row['proc_tipo_manut'] == 1 )
			{
				$stmt = "SELECT distinct gex_periodo FROM grade_exame_mensal 
					WHERE med_codigo = '$med_codigo' and gex_periodo='$gex_periodo' ".
					//"AND agt_codigo='$agt_codigo' ".
                    "order by gex_periodo";		
			}
			else
			{
				$stmt = "SELECT distinct gem_periodo FROM grade_exame_mensal_manut 
					WHERE med_codigo = '$med_codigo' and gem_periodo='$gex_periodo' ".
					//AND agt_codigo='$agt_codigo' ".
                    "order by gem_periodo";		
			}

			//print $stmt;
			$sql = db_query($stmt);
				
			// calcula + 29 dias
			// o 1o dia jah conta no período !
			while ($linha = pg_fetch_row($sql)) 
			{
				$tmp = mktime("0", "0", "0", substr($linha[0], 5, 2), substr($linha[0], 8, 2), substr($linha[0], 0, 4));
				//$per = date("d/m/Y", $tmp + (date("t", $tmp) - 1) * 86400);
				$per = date("d/m/Y", $tmp + (date("t", $tmp) - 2) * 86400);
				$periodo[date("Y-m-d", $tmp)] = $per;
			}
		}
		
		print "
	        <input type='text' size='12' class='boxl' value='$per' readonly>
	        &nbsp;
			Novo Periodo: 
			&nbsp;
			<input type='text' name='gex_periodo' size='12' class='boxl' id='data' maxlength='10' 
				onKeypress=\"return Ajusta_Data(this, event);\">
		</td>
		<td>
			<input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/add_on.gif>
		</td>
	</tr>
	</form>
	</table>
</legend>
";

// iframe
$isrc = "manutencao_exame_mensal_iframe.php?id_login={$id_login}&gex_periodo={$gex_periodo}".
	"&med_codigo={$med_codigo}&uni_codigo={$uni_codigo}&proc_codigo={$proc_codigo}";
	
print "
<table>
<tr>
	<td align=center>
	<iframe name='frameprincipal' frameborder=no marginheight=0 marginwidth=0
		scrolling=yes width='100%' height='270' src='{$isrc}'></iframe>
	</td>
</tr>
</table>";
}

print "</body></html>";

?>