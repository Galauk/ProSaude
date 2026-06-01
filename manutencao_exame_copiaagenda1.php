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
			echo ChmodBtn($id_login,'manutencao_exames','manutencao_exame_copiaagenda.php?');
		} else {
			echo "<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/manutencao_exames_off.jpg' />";
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
	$stmt_lab = "SELECT proc_tipo_manut FROM medico WHERE med_codigo='$med_codigo'";
	$manut_row = db_getRow($stmt_lab);
	// o select verifica se a data entrada est� num intervalo v�lido
	// pelo menos 30 dias maior que o maior entrado
	if( $manut_row['proc_tipo_manut'] == 1 )
	{
		$stmt = "SELECT 
		to_char(MAX(gex_periodo), 'dd/mm/yyyy') as max, 
		('$gex_periodo' > MAX(gex_periodo)+29) as ok_max ,
		TO_CHAR(MAX(gex_periodo)+30,'dd/mm/YYYY') as prox_max
		FROM grade_exame_mensal AS m
		WHERE med_codigo = {$med_codigo} 
		and   gex_periodo = '$gex_periodo'";
	}
	else
	{
		$stmt = "SELECT 
		to_char(MAX(gem_periodo), 'dd/mm/yyyy') as max, 
		('$gex_periodo' > MAX(gem_periodo)+29) as ok_max ,
		TO_CHAR(MAX(gem_periodo)+30,'dd/mm/YYYY') as prox_max
		FROM grade_exame_mensal_manut AS m
		WHERE med_codigo = {$med_codigo} 
		and   gem_periodo = '$gex_periodo'";
	}
	$per_row = db_getRow($stmt);

        if( $manut_row['proc_tipo_manut'] == 1 )
            {
	       $verificaexames = "select count(*) from grade_exame
	                          where med_codigo = $med_codigo
				  and   graex_data >= '$periodo_copia'
				  and   graex_data < (select to_date('$periodo_copia', 'yyyy-mm-dd')+29) ";
	       $verexames = db_getRow($verificaexames);
	       if ($verexames[0] > 0) 
	       {
	          print " 
		        <script type=\"text/javascript\">
			    alert(\"Ja existe configuracao para este Laboratorio neste periodo. A copia nao pode ser feita\")
			    setTimeout(\"location='$PHP_SELF?id_login=$id_login&med_codigo=$med_codigo&gex_periodo={$gex_periodo}&navdata=$navdata&uni_codigo={$uni_codigo}&proc_codigo={$proc_codigo}&periodo_copia=$periodo_copia'\", 0);
		        </script>";
		exit();
	       }
//             reglog($id_login,"Copiando Dados para do Periodo $periodo_copia para o Periodo $gex_periodo do Laboratorio $med_codigo");
	       //While que verifica quais os procedimentos do mes anterior e trabalha para gerar a quantidade de exames
	       //que mais aparece no periodo de origem, contando atraves do count(*).
			   $proced = 
			   "select proc_codigo, graex_qtde, count(*) as contqtde
					from grade_exame
					where  grade_exame.med_codigo = $medico
					and    graex_data >= '$periodo_copia' 
					and   graex_data < (select to_date('$periodo_copia', 'yyyy-mm-dd')+29) 
					group by proc_codigo, graex_qtde
					order by proc_codigo, contqtde desc ";

			   echo $proced;
			     
			while ($verproc = pg_fetch_array($proced)) {
				if ($procedimant == $verproc['proc_codigo']) {
					continue;
				}

				$conta   = 1;
				$dataini = $per_row[0];
				$datainc = $per_row[0];
				$datafin = $per_row[2];
				$q       = "BEGIN; ";
				while ($conta <= 30) {
					//verificar feriado
					$vediafer = "SELECT count(*) from feriado WHERE fer_data = '$datainc'";
					$vefer    = db_getRow($vediafer);
					if ($vefer[0] == 0) {
						//verificar dia da semana
						$vediasem = "SELECT EXTRACT(dow from TO_DATE('$datainc', 'dd/mm/yyyy'))";
						$vedia    = db_getRow($vediasem);
						if (($vedia[0] != 0) && ($vedia[0] != 6)) {
							//gravar dados
							$q .= "INSERT INTO grade_exame (med_codigo, proc_codigo, graex_qtde, graex_data, usr_codigo_cad) VALUES ($med_codigo, {$verproc['proc_codigo']}, $verproc{['graex_qtde']}, '$datainc', $id_login);  ";
							$sqldata = "select to_char(to_date('$datainc', 'dd/mm/yyyy')+1, 'dd/mm/yyyy')";
							$rowdata = db_getRow($sqldata);
							$conta   = $conta + 1;
							$datainc = $rowdata[0];
						} //fim if diasemana
						else {
							$sqldata = "select to_char(to_date('$datainc', 'dd/mm/yyyy')+1, 'dd/mm/yyyy')";
							$rowdata = db_getRow($sqldata);
							$conta   = $conta + 1;
							$datainc = $rowdata[0];
						} //fim do else - final de semana
					} // fim if feriado
					else {
						$sqldata = "select to_char(to_date('$datainc', 'dd/mm/yyyy')+1, 'dd/mm/yyyy')";
						$rowdata = db_getRow($sqldata);
						$conta   = $conta + 1;
						$datainc = $rowdata[0];
					} //fim do else - feriado
					$procedimant = $verproc['proc_codigo'];
				} //fim while da inclusao while ($conta <= 30)
				$q .= " COMMIT; ";
				$rq = db_query($q);
			} //fim do while
  
	        print " 
		    <script type=\"text/javascript\">
		    	alert(\"Dados gravados com sucesso\")
	    		setTimeout(\"location='$PHP_SELF?id_login=$id_login&med_codigo=$med_codigo&gex_periodo={$gex_periodo}&navdata=$navdata&uni_codigo={$uni_codigo}&proc_codigo={$proc_codigo}'\", 0);
		</script>";
	} // if proc_tipo_manut
} //if($act=="newdate") {

//------------------------------------------------------------------>
//-> Mostra os laboratorios, procedimentos e unidades
//------------------------------------------------------------------>
if( empty($acao) )
{
	$action = $_SERVER['PHP_SELF']."?id_login={$id_login}&acao=gravaexame&med_codigo={$med_codigo}".
		"&gex_tipo={$gex_tipo}&gex_periodo={$gex_periodo}&uni_codigo={$uni_codigo}&proc_codigo={$proc_codigo}&periodo_copia={$periodo_copia}";

	print "
	<form name=criaperiodo method=post action='{$action}' >

	<fieldset>
	<legend>Copia de Agenda de Exames</legend>
	
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
				"&uni_codigo={$uni_codigo}&proc_codigo={$proc_codigo}&periodo_copia={$periodo_copia}";
                
			print ( $med_codigo == $med['med_codigo'] )?
				"<option value='$location' selected>$med[med_nome]</option>":
				"<option value='$location'>$med[med_nome]</option>\n";
		}
	  		
	print "
		</select>
		</td>
	</tr>
	<tr>
		<td align='right'>Periodo Destino da Copia</td>
		<td width='365'>
		<select name='gex_periodo' class='box' style='width:95px'
			onChange=\"javascript:changeLocation(this)\">
			<option selected>-- Escolha --</option>\n"; 


		$med_row = db_getRow("SELECT proc_tipo_manut FROM medico WHERE med_codigo = $med_codigo");

		if( $med_row['proc_tipo_manut'] == 1 )
		{
			$stmt = "SELECT to_char(gex_periodo,'DD/MM/YYYY') as gex_periodo,gex_periodo as gex_periodo2 
				from grade_exame_mensal as m
                                where med_codigo='$med_codigo' ".
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
			"&med_codigo={$med_codigo}&uni_codigo={$uni_codigo}&proc_codigo={$proc_codigo}&periodo_copia={$periodo_copia}";
			
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
			// o 1o dia jah conta no per�odo !
			while ($linha = pg_fetch_row($sql)) 
			{
				$tmp = mktime("0", "0", "0", substr($linha[0], 5, 2), substr($linha[0], 8, 2), substr($linha[0], 0, 4));
				//$per = date("d/m/Y", $tmp + (date("t", $tmp) - 1) * 86400);
				$per = date("d/m/Y", $tmp + (date("t", $tmp) - 2) * 86400);
				$periodo[date("Y-m-d", $tmp)] = $per;
			}
		}
		
		print "
		</td>
	</tr>

	<tr>
		<td align='right'>Periodo Origem da Copia</td>
		<td width='365'>
		<select name='periodo_copia' class='box' style='width:95px'
			onChange=\"javascript:changeLocation(this)\">
			<option selected>-- Escolha --</option>\n"; 


		$med_row = db_getRow("SELECT proc_tipo_manut FROM medico WHERE med_codigo = $med_codigo");

		if( $med_row['proc_tipo_manut'] == 1 )
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
			"&med_codigo={$med_codigo}&uni_codigo={$uni_codigo}&proc_codigo={$proc_codigo}&periodo_copia={$periodo_copia}";
			
			print ( $gex_periodo == $dt['gex_periodo2'] )?
			"\n<option value='$location' selected>$dt[gex_periodo]</option>":
			"\n<option value='$location'>$dt[gex_periodo]</option>";
		}
			
		print "</select>\n";

		
		if( $periodo_copia )
		{

			if( $med_row['proc_tipo_manut'] == 1 )
			{
				$stmt = "SELECT distinct gex_periodo FROM grade_exame_mensal 
					WHERE med_codigo = '$med_codigo' and gex_periodo='$gex_periodo' ".
                                        "order by gex_periodo";		
			}
			else
			{
				$stmt = "SELECT distinct gem_periodo FROM grade_exame_mensal_manut 
					WHERE med_codigo = '$med_codigo' and gem_periodo='$gex_periodo' ".
                                        "order by gem_periodo";		
			}

			$sql = db_query($stmt);
				
			// calcula + 29 dias
			// o 1o dia jah conta no per�odo !
			while ($linha = pg_fetch_row($sql)) 
			{
		               $tmp = mktime("0", "0", "0", substr($linha[0], 5, 2), substr($linha[0], 8, 2), substr($linha[0], 0, 4));
				//$per = date("d/m/Y", $tmp + (date("t", $tmp) - 1) * 86400);
				$per = date("d/m/Y", $tmp + (date("t", $tmp) - 2) * 86400);
				$periodo[date("Y-m-d", $tmp)] = $per;
			}
		}
		print "
		</td>
		<td>
			<input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/add_on.gif>
		</td>
	</tr>
	</form>
	</form>
	</table>
</legend>
</fieldset>
";

}

?>
