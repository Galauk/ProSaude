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
		if(SelPerm($id_login,'manutencaoagendaexame.php') != "0")
		{
			echo ChmodBtn($id_login,'manutencao_agenda_exames','manutencaoagendaexame.php?');
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
		if(SelPerm($id_login,'manutencao_exame_copiaagenda.php') != "0")
		{
			echo ChmodBtn($id_login,'copiar_agenda','manutencao_exame_copiaagenda.php?');
		} else {
			echo "<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/copiar_agenda_on.jpg' />";
		}
	echo "</fieldset>";

reglog($id_login,"Acessando Manutencao de Exames");

// variaveis
$med_codigo 	= intval($_GET['med_codigo']);
$uni_codigo 	= intval($_GET['uni_codigo']);
$proc_codigo 	= intval($_GET['proc_codigo']);


//colocar o action 1 - digitadados
if( $acao == "gravaexame" )
{
	// qual o tipo do laboratorio ?
	$stmt_lab = "SELECT med_tipoagendamento as proc_tipo_manut FROM medico 
	             WHERE med_codigo='$med_codigo'";
	$manut_row = db_getRow($stmt_lab);
	// o select verifica se a data entrada está num intervalo válido
	// pelo menos 30 dias maior que o maior entrado
	if(( $manut_row['proc_tipo_manut'] == 'Q' ) || ( $manut_row['proc_tipo_manut'] == 'P' ))
	{
		$stmt = "SELECT 
		to_char(MAX(gex_periodo), 'dd/mm/yyyy') as max, 
		('$gex_periodo' > MAX(gex_periodo)+29) as ok_max ,
		TO_CHAR(MAX(gex_periodo)+30,'dd/mm/YYYY') as prox_max
		FROM grade_exame_mensal AS m
		WHERE med_codigo = {$med_codigo} ";
	}
	else
	{
		$stmt = "SELECT 
		to_char(MAX(gem_periodo), 'dd/mm/yyyy') as max, 
		('$gex_periodo' > MAX(gem_periodo)+29) as ok_max ,
		TO_CHAR(MAX(gem_periodo)+30,'dd/mm/YYYY') as prox_max
		FROM grade_exame_mensal_manut AS m
		WHERE med_codigo = {$med_codigo} ";
	}
	$per_row = db_getRow($stmt);
        if(( $manut_row['proc_tipo_manut'] == 'Q' ) || ($manut_row['proc_tipo_manut'] == 'P'))
            {
	       $verificaexames = "select count(*) from grade_exame
	                          where med_codigo = $med_codigo
				  and   proc_codigo = $proc_codigo
				  and   graex_data >= '$gex_periodo'
				  and   graex_data < (select to_date('$gex_periodo', 'yyyy-mm-dd')+29) ";
	       $verexames = db_getRow($verificaexames);
	       if ($verexames[0] > 0) {
	       print " 
		<script type=\"text/javascript\">
			alert(\"Ja existe configuracao para este procedimento neste periodo.\")
			setTimeout(\"location='$PHP_SELF?id_login=$id_login&med_codigo=$med_codigo&gex_periodo={$gex_periodo}&navdata=$navdata&uni_codigo={$uni_codigo}&proc_codigo={$proc_codigo}'\", 0);
		</script>";
		exit();
	       }
               reglog($id_login,"Adicionando Dados para a Manutencao de Exame Geral - Cod: $proc_codigo");
 	       $conta=1;
	       $dataini = $per_row[0];
	       $datainc = $per_row[0];
	       $datafin = $per_row[2];
	       $q = "BEGIN; ";
	       while ($conta <= 30) 
	       {
	       //verificar feriado
	       $vediafer = "SELECT count(*) from feriado WHERE fer_data = '$datainc'"; 
	       $vefer = db_getRow($vediafer);

               if ($vefer[0] == 0) { 
 	           //verificar dia da semana
	           $vediasem = "SELECT EXTRACT(dow from TO_DATE('$datainc', 'dd/mm/yyyy'))";
	           $vedia = db_getRow($vediasem);
	           if (($vedia[0] <> 0) && ($vedia[0]<>6)) {
    	              //gravar dados
		     if ($manut_row['proc_tipo_manut'] == 'P') $proc_codigo = 0;
  	             $q .= "INSERT INTO grade_exame
		       (med_codigo, proc_codigo, graex_qtde, graex_data, usr_codigo_cad)
		       VALUES 
		       ($med_codigo, $proc_codigo, $qtde_exame, '$datainc', $id_login);  ";
		     $sqldata = "select to_char(to_date('$datainc', 'dd/mm/yyyy')+1, 'dd/mm/yyyy')";
		     $rowdata = db_getRow($sqldata);
	             $conta = $conta + 1; 
		     $datainc = $rowdata[0];
                   } //fim if diasemana
		   else
		   {
		     $sqldata = "select to_char(to_date('$datainc', 'dd/mm/yyyy')+1, 'dd/mm/yyyy')";
		     $rowdata = db_getRow($sqldata);
	             $conta = $conta + 1; 
		     $datainc = $rowdata[0];
		   } //fim do else - final de semana 
		} // fim if feriado   
		else
		{
		$sqldata = "select to_char(to_date('$datainc', 'dd/mm/yyyy')+1, 'dd/mm/yyyy')";
		$rowdata = db_getRow($sqldata);
	        $conta = $conta + 1; 
		$datainc = $rowdata[0];
		} //fim do else - feriado
	       } //fim while
	       $q .= " COMMIT; ";
	       $rq = db_query($q);		
	       print " 
		<script type=\"text/javascript\">
			alert(\"Dados gravados com sucesso\")
			setTimeout(\"location='$PHP_SELF?id_login=$id_login&med_codigo=$med_codigo&gex_periodo={$gex_periodo}&navdata=$navdata&uni_codigo={$uni_codigo}&proc_codigo={$proc_codigo}'\", 0);
		</script>";

	} // if proc_tipo_manut
	//Tipo de manutencao => 'D' ou 'V'
        if(( $manut_row['proc_tipo_manut'] == 'V' ) || ($manut_row['proc_tipo_manut'] == 'D'))
	{
	       $verificaexames = "select gem_codigo, gem_periodo, med_codigo 
	                          from grade_exame_mensal_manut
	                          where med_codigo = $med_codigo
				  and   gem_periodo  = '$gex_periodo'";
	       $verexames = db_getRow($verificaexames);
	       if (($verexames[0]) and ($manut_row['proc_tipo_manut'] == 'V')) {
  	             $q = "UPDATE grade_exame_mensal_manut
		           SET gem_valor = $qtde_exame, 
			       usr_codigo_alt = $id_login
			   WHERE gem_codigo = $verexames[0]";
	             $rq = db_query($q);		
	             print " 
		             <script type=\"text/javascript\">
			             alert(\"Dados gravados com sucesso\")
			             setTimeout(\"location='$PHP_SELF?id_login=$id_login&med_codigo=$med_codigo&gex_periodo={$gex_periodo}&navdata=$navdata&uni_codigo={$uni_codigo}&proc_codigo={$proc_codigo}'\", 0);
		             </script>";
		     exit();
	       }
	       $verificaexames = "select count(*) from grade_exame
	                          where med_codigo = $med_codigo
				  and   proc_codigo = $proc_codigo
				  and   graex_data >= '$gex_periodo'
				  and   graex_data < (select to_date('$gex_periodo', 'yyyy-mm-dd')+29) ";
	       $verexames = db_getRow($verificaexames);
	       if ($verexames[0] > 0) {
	             print " 
		             <script type=\"text/javascript\">
			             alert(\"Ja existem dados  gravados para este Laboratorio neste periodo\")
			             setTimeout(\"location='$PHP_SELF?id_login=$id_login&med_codigo=$med_codigo&gex_periodo={$gex_periodo}&navdata=$navdata&uni_codigo={$uni_codigo}&proc_codigo={$proc_codigo}'\", 0);
		             </script>";
		exit();
	       }
               reglog($id_login,"Adicionando Dados para a Manutencao de Exame Por Valor/Demanda - Cod: $med_codigo");
 	       $conta=1;
	       $dataini = $per_row[0];
	       $datainc = $per_row[0];
	       $datafin = $per_row[2];
	       $q = "BEGIN; ";
	       while ($conta <= 30) 
	       {
	       //verificar feriado
	       $vediafer = "SELECT count(*) from feriado WHERE fer_data = '$datainc'"; 
	       $vefer = db_getRow($vediafer);

               if ($vefer[0] == 0) { 
 	           //verificar dia da semana
	           $vediasem = "SELECT EXTRACT(dow from TO_DATE('$datainc', 'dd/mm/yyyy'))";
	           $vedia = db_getRow($vediasem);
	           if (($vedia[0] <> 0) && ($vedia[0]<>6)) {
    	              //gravar dados
		     $proc_codigo = 0;
		     $qtde_exame = 0;
  	             $q .= "INSERT INTO grade_exame
		       (med_codigo, proc_codigo, graex_qtde, graex_data, usr_codigo_cad)
		       VALUES 
		       ($med_codigo, $proc_codigo, $qtde_exame, '$datainc', $id_login);  ";
		     $sqldata = "select to_char(to_date('$datainc', 'dd/mm/yyyy')+1, 'dd/mm/yyyy')";
		     $rowdata = db_getRow($sqldata);
	             $conta = $conta + 1; 
		     $datainc = $rowdata[0];
                   } //fim if diasemana
		   else
		   {
		     $sqldata = "select to_char(to_date('$datainc', 'dd/mm/yyyy')+1, 'dd/mm/yyyy')";
		     $rowdata = db_getRow($sqldata);
	             $conta = $conta + 1; 
		     $datainc = $rowdata[0];
		   } //fim do else - final de semana 
		} // fim if feriado   
		else
		{
		$sqldata = "select to_char(to_date('$datainc', 'dd/mm/yyyy')+1, 'dd/mm/yyyy')";
		$rowdata = db_getRow($sqldata);
	        $conta = $conta + 1; 
		$datainc = $rowdata[0];
		} //fim do else - feriado
	       } //fim while
	       $q .= " COMMIT; ";
	       $rq = db_query($q);		
	       print " 
		<script type=\"text/javascript\">
			alert(\"Dados gravados com sucesso\")
			setTimeout(\"location='$PHP_SELF?id_login=$id_login&med_codigo=$med_codigo&gex_periodo={$gex_periodo}&navdata=$navdata&uni_codigo={$uni_codigo}&proc_codigo={$proc_codigo}'\", 0);
		</script>";

	}//fim do if para tipo de manutencao em valor

	//aqui colocar o else para fazer update no valor qdo for por valor
} //if($act=="newdate") {

//------------------------------------------------------------------>
//-> Cadastra um novo período
//------------------------------------------------------------------>
if( $acao == "nova_data" )
{
	
	// qual o tipo do laboratorio ?
	$stmt_lab = "SELECT med_tipoagendamento as proc_tipo_manut FROM medico 
	             WHERE med_codigo='$med_codigo'";
	$manut_row = db_getRow($stmt_lab);
	
	// o select verifica se a data entrada está num intervalo válido
	// pelo menos 30 dias maior que o maior entrado
	
	if(( $manut_row['proc_tipo_manut'] == 'Q' ) || ($manut_row['proc_tipo_manut'] == 'P'))
	{
		$stmt = "SELECT 
		to_char(MAX(gex_periodo), 'dd/mm/yyyy') as max, 
		('$gex_periodo' > MAX(gex_periodo)+29) as ok_max ,
		TO_CHAR(MAX(gex_periodo)+30,'dd/mm/YYYY') as prox_max
		FROM grade_exame_mensal AS m
		WHERE med_codigo = {$med_codigo} ";
        
	}
	else
	{
	       $verificaexames = "select count(*) from grade_exame_mensal_manut
	                          where med_codigo = $med_codigo
				  and   gem_periodo >= '$gex_periodo'";
	       $verexames = db_getRow($verificaexames);
	       if ($verexames[0] > 0) {
	       print " 
		<script type=\"text/javascript\">
			alert(\"Ja existe configuracao para este procedimento neste periodo.\")
			setTimeout(\"location='$PHP_SELF?id_login=$id_login&med_codigo=$med_codigo&gex_periodo={$gex_periodo}&navdata=$navdata&uni_codigo={$uni_codigo}&proc_codigo={$proc_codigo}'\", 0);
		</script>";
		exit();
	       }
		$stmt = "SELECT 
		to_char(MAX(gem_periodo), 'dd/mm/yyyy') as max, 
		('$gex_periodo' > MAX(gem_periodo)+29) as ok_max ,
		TO_CHAR(MAX(gem_periodo)+30,'dd/mm/YYYY') as prox_max
		FROM grade_exame_mensal_manut AS m
		WHERE med_codigo = {$med_codigo} ";
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

		if(( $manut_row['proc_tipo_manut'] == 'Q' ) || ($manut_row['proc_tipo_manut'] == 'P'))
		{

				$q .= "INSERT into grade_exame_mensal ( " .
					"med_codigo, " .
					"gex_periodo, ".
					"usr_codigo_cad  " .
					") values ( " .
					"'$med_codigo', " .
					"'$gex_periodo', " .
					"'$id_login' ".
					");";

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
			
			
				$q .= "INSERT into grade_exame_mensal_manut ( " .
					"med_codigo, " .
					"gem_valor, ".
					"gem_periodo, ".
					"usr_codigo_cad  " .
					") values ( " .
					"'$med_codigo', " .
					"0, " .
					"'$gex_periodo', " .
					"'$id_login'  ".
					");";

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
		"&gex_tipo={$gex_tipo}&gex_periodo={$gex_periodo}&uni_codigo={$uni_codigo}&proc_codigo={$proc_codigo}";

	print "
	<form name=criaperiodo method=post action='{$action}' onsubmit=\"return valida_form_data()\">

	<fieldset>
	<legend>Manuten&ccedil;&atilde;o dos Exames</legend>
	
	<table>
	<tr>
		<td width='100' align='right'>Laborat&oacute;rio</td>
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
		<td align='right'>Data</td>
		<td width='365'>
		<select name='gex_periodo' class='box' style='width:95px'
			onChange=\"javascript:changeLocation(this)\">
			<option selected>-- Escolha --</option>\n"; 


		$med_row = db_getRow("SELECT med_tipoagendamento FROM medico WHERE med_codigo = $med_codigo");

		if(( $med_row['med_tipoagendamento'] == 'Q' ) || ($med_row['med_tipoagendamento'] == 'P'))
		{
			$stmt = "SELECT to_char(gex_periodo,'DD/MM/YYYY') as gex_periodo,gex_periodo as gex_periodo2 
				from grade_exame_mensal as m
                                where med_codigo='$med_codigo' 
				".
				"group by gex_periodo order by to_char(gex_periodo,'YYY-mm-dd') desc";
		} 
		else
		{
			$stmt = "SELECT to_char(gem_periodo,'DD/MM/YYYY') as gex_periodo,gem_periodo as gex_periodo2 
				from grade_exame_mensal_manut as m
                                where med_codigo='$med_codigo' ".
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

		//echo $stmt;

		
		if( $gex_periodo )
		{

			if(( $med_row['med_tipoagendamento'] == 'P' ) || ($med_row['med_tipoagendamento'] == 'Q'))
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
</fieldset>
";

	$action1 = $_SERVER['PHP_SELF']."?id_login={$id_login}&acao=gravaexame&med_codigo={$med_codigo}".
		"&gex_tipo={$gex_tipo}&gex_periodo={$gex_periodo}&uni_codigo={$uni_codigo}&proc_codigo={$proc_codigo}&qtde_exame=$qtde_exame";
print "		
	<form name=digitadados  method=post action='{$action1}'>

	<fieldset>
	<legend>Digita&ccedil;&atilde;o dos Procedimentos</legend>
	
	<table>";

$med_row = db_getRow("SELECT med_tipoagendamento,
                             case when med_tipoagendamento = 'D' then 'Por Demanda '
			          when med_tipoagendamento = 'V' then 'Por Valor   '
				  when med_tipoagendamento = 'Q' then 'Por Quantidade de Procedimentos '
				  when med_tipoagendamento = 'P' then 'Por Quantidade de Pacientes '
		             end as desctipoagendamento
	              FROM medico WHERE med_codigo = $med_codigo");
print "<center><h3> Laboratorio com Agendamento - $med_row[desctipoagendamento] </h3></center>";		      
if ($med_row['med_tipoagendamento'] == 'Q')
{
	print "
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
    </tr> ";
 }   
 if ($med_row['med_tipoagendamento'] <> 'D') {
    print "
    <tr>
		<td align='right'>Quantidade/Valor</td>
                <td colspan='2'>
	        <input type='text' id='qtde_exame' name='qtde_exame' size='20' class='boxl' value='$qtde'></td>
    </tr>		";
 }   

    print "
    <tr>
                <td colspan='3'>

   		        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  
   		        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  
   		        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  
			<input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/fazer_agenda_on.jpg>
		</td>
    </tr>		
	</form>
	</table>
</legend>
";

/*print "
<table>
<tr>
	<td align=center>
	<iframe name='frameprincipal' frameborder=no marginheight=0 marginwidth=0
		scrolling=yes width='100%' height='270' src='{$isrc}'></iframe>
	</td>
</tr>
</table>";*/
}


?>
