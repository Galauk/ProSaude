<?php
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
#verauth($id_login);

$pac_codigo = $usu_codigo;

session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
Cabecario();

$sq = pg_query("select *from logon where id_login = '$id_login'") or die(pg_last_error());
$uni = pg_fetch_array($sq);
$uni_codigo = $uni[uni_codigo];

//------------------------------------------------------------------>
if(($med_codigo=="" OR $med_codigo == 0)) { echo "*<b><font size=2 color=red>ATENCAO</font></b><font size=1 color=black><br>Selecione uma Especialidade um Médico e um Procedimento"; exit; }
if(($proc_codigo=="" OR $proc_codigo == 0)) { echo "*<b><font size=2 color=red>ATENCAO</font></b><font size=1 color=black><br>Selecione uma Especialidade um Médico e um Procedimento"; exit; }




?>

<script>
function changeLocation(menuObj) {
   	 var i = menuObj.selectedIndex;
   	   if(i > 0) {
      	      window.location = menuObj.options[i].value;
           }
}

function btn() {
    
   location.href='msg_agendado.php';

}

</script>

<?php
//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>
$data = date("d/m/Y");
$data = explode("/",$data);
$data = mktime(0,0,0,$data[1],$data[0],$data[2]);

$data_db = $pdia;
$data_db = explode("/",$data_db);
$data_db = mktime(0,0,0,$data_db[1],$data_db[0],$data_db[2]);



if(empty($pdia))
{
	//->teste         $sql_qtdmed = "select a.med_codigo, a.uni_codigo, a.qtde, a.gra_hora_ini, to_char(a.gra_data,'DD/MM/YYYY') as gra_data from  view_qtde_grade  as a where   a.med_codigo = '$med_codigo' and   a.uni_codigo = '$uni_codigo' and a.gra_data between to_date('$grm_mensal[grm_periodo]', 'yyyy/mm/dd') and to_date('$grm_mensal[grm_periodo]', 'yyyy/mm/dd') + 30";
    $sql_qtdmed = "select a.med_codigo, a.uni_codigo, a.qtde, a.gra_hora_ini, to_char(a.gra_data,'DD/MM/YYYY') as gra_data from  view_qtde_grade  as a where   a.med_codigo = '$med_codigo' and   a.uni_codigo = '$uni_codigo'";
	#$qa = "select a.med_codigo, a.uni_codigo, a.qtde, a.gra_hora_ini, to_char(a.gra_data,'DD/MM/YYYY') as gra_data from  view_qtde_grade  as a where   a.med_codigo = '$med_codigo' and   a.uni_codigo = '$uni_codigo' and a.gra_data between to_date('$grm_mensal[grm_periodo]', 'yyyy/mm/dd') and to_date('$grm_mensal[grm_periodo]', 'yyyy/mm/dd') + 30";
} else {
	$sep = explode("/",$pdia);
	$tpdata = $sep[2]."-".$sep[1]."-".$sep[0];
	$sql_qtdmed = "select a.med_codigo, a.uni_codigo, a.qtde, a.gra_hora_ini, to_char(a.gra_data,'DD/MM/YYYY') as gra_data from  view_qtde_grade  as a where   a.med_codigo = '$med_codigo' and   a.uni_codigo = '$uni_codigo' and a.gra_data = '$tpdata'";
}

$query = pg_query($sql_qtdmed);

# $query_grm  = pg_query("select *from grade_mensal where med_codigo = '$med_codigo' and esp_codigo = '$esp_codigo' and agt_codigo = '$agt_codigo' and age_item='$age_item' and grm_periodo >= current_date");
# $qa = "select *from grade_mensal where med_codigo = '$med_codigo' and esp_codigo = '$esp_codigo' and agt_codigo = '$agt_codigo' and age_item='$age_item' and current_date between grm_periodo and grm_periodo + 30";
 
# echo $qa;

 $agta = pg_fetch_array($query);
# $query_age  = pg_query("select *from agendamento where agt_codigo = '$agt_codigo' and med_codigo = '$med_codigo' and esp_codigo = '$esp_codigo' and uni_codigo = '$uni_codigo' and age_data >=current_date");


 


if(!empty($usu_codigo))
{
	
	if( ! empty($guia_extra) )
	{
		// procura por uma ocorrencia
		$stmt = "SELECT gra_codigo FROM grade_medico WHERE
		med_codigo = ".intval($med_codigo)." AND 
		gra_data =  '$pdia' AND 
		uni_codigo = ".intval($uni_codigo)." AND 
		esp_codigo = ".intval($esp_codigo)." AND
		age_item = 'ES' AND
		age_tipo =  'AL' ";
		
		$gra_codigo = db_get($stmt);
		
		// se nao achar: insere, senao: atualiza
		if( empty($gra_codigo) )
		{
			// removi hora_ini: TO_CHAR(NOW(),'HH24:MI')
			// SQL INSERT
			$sqlPegaUnidade = "select * from usuarios where usr_codigo = $id_login";
			$queryPegaUnidade = pg_query($sqlPegaUnidade);
			$line = pg_fetch_array($queryPegaUnidade);
			$unidade = $line["uni_codigo"];
			
			$stmt = "INSERT INTO grade_medico ( 
				med_codigo, 
				gra_data, 
				uni_codigo, 
				gra_tipo, 
				gra_status, 
				gra_qtde, 
				esp_codigo, 
				gra_hora_ini, 
				age_item, 
				age_tipo, 
				usr_codigo_cad, 
				usr_codigo_alt
				 ) VALUES ( 
				".intval($med_codigo).", 
				'$pdia', 
				'$uni_codigo', 
				'C', 
				'S', 
				 1, 
				 ".intval($esp_codigo).", 
				 '', 
				 'ES', 
				 'AL', 
				 ".intval($id_login).", 
				 null )";	
		} 
		else
		{
			// SQL update
			$stmt = "UPDATE grade_medico 
				SET gra_qtde = gra_qtde + 1 ,
				usr_codigo_alt = $id_login
				WHERE gra_codigo = $gra_codigo";
		}
		
		//print $stmt;
		db_query( $stmt );
		//db_query( 'COMMIT' );
	}
	echo "
	<form method=post action='agendamento_atd_balcao.php?jah_agendado=true' target='atendimento' OnSubmit=\"btn()\">
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=esp_codigo value=$esp_codigo>
	<input type=hidden name=age_item value=$age_item>
	<input type=hidden name=uni_codigo value=$uni_codigo>
	<input type=hidden name=med_codigo value=$med_codigo>
	<input type=hidden name=proc_codigo value=$proc_codigo>
	<input type=hidden name=agt_codigo value=$agt_codigo>
	<input type=hidden name=age_tipo value=$age_tipo>
	<input type=hidden name=age_vaga value=$age_vaga>
	<input type=hidden name=usu_codigo value=$usu_codigo>
	<input type=hidden name=age_paciente value='$age_paciente'>
	<input type=hidden name=acao value=addagendamento>
	<input type=hidden name=grm_mensal value=$grm_mensal[grm_qtde]>
	<table width=100% cellspacing=1 cellpadding=4 border=0>
	<tr bgcolor=CCCCCC>
	<td width=10 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>&nbsp;</font></td>
	<td width=10 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Tp.Age.</font></td>
	<td width=10 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Semana</font></td>
	<td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>&nbsp;</font></td>
	</tr>";

	if($acao=="age")
	{
		echo "<tr bgcolor=ffffff><td colspan=5><font color=red>*</font>&nbsp;<b>Agendado</b></td></tr>";
	}

	reglog($id_login,"Entrando em Fazer Agendamento");

	if($age_item=="CB")
	{ 
		//$age = "CLÍNICA BÁSICA";
		$age = "CL&Iacute;ICA B&Aacute;ICA";
	} else { 
		$age = "ESPECIALIDADE"; 
	}

	echo "<tr bgcolor=FFFFFF>";

	/*if(empty($pdia)) { $presh = ">= current_date"; } else { $presh = "= '$pdia'"; }
	if(empty($pdia)) { $presh_h = ""; } else { $presh_h = "and b.gra_hora_ini = '$horario'"; }*/
	
	$presh = "= '$pdia'";
	
	#	and a.gra_data between to_date('$grm_mensal[grm_periodo]', 'yyyy/mm/dd') and to_date('$grm_mensal[grm_periodo]', 'yyyy/mm/dd') + 30
	#       coalesce((select qtde from view_qtde_medico as c where c.med_codigo = '$med_codigo' and c.uni_codigo = '$uni_codigo' and c.age_data = b.gra_data and c.age_hora = b.gra_hora_ini),0) > 0 
	#        coalesce((select qtde from view_qtde_medico as c where c.med_codigo = '$med_codigo' and c.uni_codigo = '$uni_codigo' and c.age_data = b.gra_data and c.age_hora = b.gra_hora_ini),0) as calc_qtde


	// SQL alterado, declaracoes originais:
	// and b.age_tipo = '$age_tipo'
	
	$stmt = "select  to_char(b.gra_data,'DD/MM/YYYY') as gra_data, b.gra_hora_ini, coalesce((select a.qtde from view_qtde_grade as a where a.med_codigo = '$med_codigo' and a.uni_codigo = '$uni_codigo' and a.esp_codigo = '$esp_codigo' and a.gra_data >= b.gra_data and a.gra_hora_ini = b.gra_hora_ini order by gra_data limit 1),0) -
		coalesce((select qtde from view_qtde_medico as c where c.med_codigo = '$med_codigo' and c.uni_codigo = '$uni_codigo' and c.esp_codigo = '$esp_codigo' and c.age_data = b.gra_data and c.age_hora = b.gra_hora_ini),0) as calc_qtde
		from view_qtde_grade as b
		where b.med_codigo = '$med_codigo'
		and b.uni_codigo = '$uni_codigo'
		and b.age_tipo IN ( '$age_tipo', 'AL' )
		and b.esp_codigo = '$esp_codigo'
		and b.gra_data $presh
		$presh_h
		and coalesce((select a.qtde from view_qtde_grade as a where a.med_codigo = '$med_codigo' and a.uni_codigo = '$uni_codigo' and a.esp_codigo = '$esp_codigo' and a.gra_data >= b.gra_data order by gra_data limit 1),0) -
		coalesce((select qtde from view_qtde_medico as c where c.med_codigo = '$med_codigo' and c.uni_codigo = '$uni_codigo' and c.esp_codigo = '$esp_codigo' and c.age_data = b.gra_data and c.age_hora = b.gra_hora_ini),0) > 0
		order by b.gra_data, b.gra_hora_ini
		limit 1";

	$sqlG = pg_query($stmt);

	$row=pg_fetch_array($sqlG);
	if(($row[calc_qtde]<="0" and $pdia!=""))
	{
		
		$age_tipo = ( empty($age_tipo) ? 'AL' : '' );
		
		$query_string = "pdia={$pdia}&agt_codigo={$agt_codigo}&id_login={$id_login}&med_codigo={$med_codigo}".
		"&uni_codigo={$uni_codigo}&esp_codigo={$esp_codigo}&usu_codigo={$usu_codigo}&age_tipo={$age_tipo}".
		"&age_item={$age_item}&age_paciente={$age_paciente}&horario={$horario}";

		//echo "<tr bgcolor=ffffff><td colspan=5><font color=red>*</font>&nbsp;M&eacute;dico sem vaga.</td></tr>";
		echo "<tr bgcolor=ffffff><td colspan=5>
			<strong><a href='{$PHP_SELF}?{$query_string}&guia_extra=1' title='guia extra'>
				<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/gerar_guia_extra_on.jpg' alt='Gerar guia extra' border='0' /></a></strong></td></tr>";
		exit;
	}
	$sep = explode("/",$row[gra_data]);
	
	if($sep[0]=="14") 
	{
		$query_grm  = pg_query("select * from grade_mensal where med_codigo = '$med_codigo' and esp_codigo = '$esp_codigo' and agt_codigo = '$agt_codigo' and age_item='$age_item' and grm_periodo between to_date('$row[gra_data]','yyyy/mm/dd') and grm_periodo + 30");
	}
	else
	{
		$query_grm  = pg_query("select * from grade_mensal where med_codigo = '$med_codigo' and esp_codigo = '$esp_codigo' and agt_codigo = '$agt_codigo' and age_item='$age_item' and to_date('$row[gra_data]','dd/mm/yyyy') between grm_periodo and grm_periodo + 30");
	}

	$grm_mensal = pg_fetch_array($query_grm);

	#if($sep[0]=="14") {
	# $query_age  = pg_query("select *from agendamento where  agt_codigo = '$agt_codigo' and med_codigo = '$med_codigo' and esp_codigo = '$esp_codigo' and uni_codigo = '$uni_codigo' and age_data between to_date('$grm_mensal[grm_periodo]', 'yyyy-mm-dd') and to_date('$grm_mensal[grm_periodo]', 'yyyy-mm-dd') + 30");
	#} else {
	
	$query_age  = pg_query("select *from agendamento where  agt_codigo = '$agt_codigo' and med_codigo = '$med_codigo' and esp_codigo = '$esp_codigo' and uni_codigo = '$uni_codigo' and age_data between to_date('$grm_mensal[grm_periodo]', 'yyyy-mm-dd') and to_date('$grm_mensal[grm_periodo]', 'yyyy-mm-dd') + 30");
	
	#}

	#echo $grm_mensal[grm_qtde]."->".pg_num_rows($query_age);

	if($row['calc_qtde']=="0")
	{ 
		echo "<tr bgcolor=ffffff><td colspan=5><font color=red>*</font>&nbsp;M&eacute;dico Sem Vaga Dispon&iacute;l</td></tr>";
		exit;
	} 

	
		$rowVerif=pg_fetch_array(pg_query("select  b.gra_data, b.gra_hora_ini, coalesce((select a.qtde from view_qtde_grade as a where a.med_codigo = '$med_codigo' and a.uni_codigo = '$uni_codigo' and a.esp_codigo = '$esp_codigo' and a.gra_data >= b.gra_data and a.gra_hora_ini = b.gra_hora_ini order by gra_data limit 1),0) -
			coalesce((select qtde from view_qtde_medico as c where c.med_codigo = '$med_codigo' and c.uni_codigo = '$uni_codigo' and c.esp_codigo = '$esp_codigo' and c.age_data = b.gra_data and c.age_hora = b.gra_hora_ini),0) as qtdegeral
			from view_qtde_grade as b
			where b.med_codigo = '$med_codigo'
			and b.uni_codigo = '$uni_codigo'
			and b.gra_data = '$age_data'
			and b.gra_hora_ini = '$age_hr'
			and b.esp_codigo = '$esp_codigo'
			and coalesce((select a.qtde from view_qtde_grade as a where a.med_codigo = '$med_codigo' and a.uni_codigo = '$uni_codigo' and a.esp_codigo = '$esp_codigo' and a.gra_data >= b.gra_data order
			by gra_data limit 1),0) - coalesce((select qtde from view_qtde_medico as c where c.med_codigo = '$med_codigo' and c.esp_codigo = '$esp_codigo' and c.uni_codigo = '$uni_codigo' 
			and c.age_data = b.gra_data and c.age_hora = b.gra_hora_ini),0) > 0 order by b.gra_data , b.gra_hora_ini"));
			//echo "<script> window.open(\"print_guia.php?uni_codigo=$uni_codigo&esp_codigo=$esp_codigo&agt_codigo=$agt_codigo&usu_codigo=$usu_codigo&age_codigo=$age_codigo&med_codigo=$med_codigo\",null,\"height=500,width=750,status=yes,toolbar=no,menubar=no,location=no\"); </script>";
		if($rowVerif[qtdegeral]>="1")
		  {
echo "sera?";
exit;
		  }
	
	
	
	if($grm_mensal['grm_qtde']<=pg_num_rows($query_age))
	{ 
		if(empty($agt_codigo)) { $agt_codigo = 0; }
		$stmt = "INSERT INTO agendamento 
		( proc_codigo_sus,usu_codigo, agt_codigo, med_codigo, esp_codigo, uni_codigo, age_data, age_tipo, age_hora, age_atendido )
		VALUES
		('$proc_codigo', '$usu_codigo', '$agt_codigo', '$med_codigo', '$esp_codigo', '$uni_codigo', CURRENT_DATE, 'AL', to_char(NOW(),'HH24:MI'), 'S' )";
		
		db_query($stmt);
		
		//die(1111);
		
		//echo "<tr bgcolor=ffffff><td colspan=5><font color=red>*</font>&nbsp;Este Agente Não Possui Agendamento Disponível</td></tr>";
		//exit;
	} 

	#if(($data_db <= $data and $pdia!="")) {
	#           echo "<tr bgcolor=ffffff><td colspan=5><font color=red>*</font>&nbsp;Data Retroativa.</td></tr>";
	#exit;
	#}

	if(!empty($row['gra_data']))
	{
		echo "<td width=10 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/agendar_on.jpg></td>";
		echo "<td width=10 align=center style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$age_tipo</td>
			<td width=110 align=center style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>";
	}
	
	echo "<input type=hidden name=age_data value=$row[gra_data]>
		<input type=hidden name=age_hr value='$row[gra_hora_ini]'>";

	if(empty($row[gra_data]))
	{
		echo "<td colspan=4><b>=> <font color=red>M&eacute;dico n&atilde;possui vagas.</b></td>";
	}
	else
	{
		echo "<b><font color=blue>$row[gra_hora_ini]</font> $row[gra_data] (<font color=red>$row[calc_qtde]</font>)</b>";
	}
	
	$exp=explode("/",$row[gra_data]);
	$ALLSEMANA = date('w', mktime(0,0,0,$exp[1],$exp[0],$exp[2]));
	
	switch($ALLSEMANA)
	{
		case 1:
			$dia_da_semana = "Segunda Feira";
			break;

		case 2:
			$dia_da_semana = "Terça Feira";
			break;

		case 3:
			$dia_da_semana = "Quarta Feira";
			break;

		case 4:
			$dia_da_semana = "Quinta Feira";
			break;

		case 5:
			$dia_da_semana = "Sexta Feira";
			break;

		case 6:
			$dia_da_semana = "Sábado";
			break;

		case 0:
			$dia_da_semana = "Domingo";
			break;

	}

	if(!empty($row[gra_data]))
	{
		echo "</td>
			<td width=90 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$dia_da_semana</td>";
	}
	
	$verage = pg_query("select *from agendamento where med_codigo='$med_codigo' and usu_codigo='$usu_codigo' and age_item='$age_item' and esp_codigo='$esp_codigo' and agt_codigo='$agt_codigo' and age_data = current_date");
	$v = "select *from agendamento where med_codigo='$med_codigo' and usu_codigo='$usu_codigo' and age_item='$age_item' and esp_codigo='$esp_codigo' and agt_codigo='$agt_codigo' and age_data = current_date";

	echo "</form></tr>";
	echo "</table>";

}

echo "</body></html>";

/** fim **/