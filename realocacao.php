<link href="css/estiloForm.css" rel="stylesheet" type="text/css" />
<link href="css/estiloCommon.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" type="text/javascript" src="relatorio/funcoes.js"></script>
<script src=relatorio/script.js></script>
<link rel="stylesheet" href="/WebSocialSaude/lib/themes/base/jquery.ui.all.css">
<link rel="stylesheet" href="/WebSocialSaude/lib/themes/ui-lightness/jquery-ui-1.8.10.custom.css">
<script type="text/javascript" src="/WebSocialComum/library/js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="/WebSocialSaude/lib/ui/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="/WebSocialComum/library/js/jquery.buscar.js"></script>
<?
session_start();
require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

$form = new classForm();
$common = new commonClass();
$table = new tableClass();
	echo "<link type=\"text/css\" href=estiloPE.css rel=\"stylesheet\"/>";
	echo $common->incJquery();

$hoje = date("d/m/Y");
$uni_codigo = getUnidadeByLogon();

$sql = "SELECT a.age_codigo, 
	pc_clas_risco, 
	u.usr_codigo, 
	u.usr_nome, 
	usu.usu_nome, 
	a.age_horario as age_hora, 
	a.age_tipo, 
	a.age_item, 
	e.esp_nome, 
	a.age_atendido,
	to_char(usu_datanasc,'DD/MM/YYYY')as datanasc,
	age_ordem,
	0 as io_situacao_internacao
  FROM agendamento AS a 
  LEFT JOIN pre_consulta pre 
    ON pre.age_codigo = a.age_codigo 
  JOIN usuarios AS u 
    ON u.usr_codigo=a.med_codigo 
  JOIN especialidade AS e 
    ON e.esp_codigo=a.esp_codigo 
  JOIN usuario AS usu 
    ON usu.usu_codigo=a.usu_codigo 
 WHERE age_data='$hoje' 
   AND a.uni_codigo=$uni_codigo 
   ORDER BY med_codigo,age_ordem,age_codigo";
//die($sql);
$query = pg_query($sql);

function openTable(){
	echo <<< TBL
	<table class="grid ui-widget ui-widget-content ui-corner-all" width='100%'>
		<thead>
			<tr class="ui-widget-header">
				<th>Hora</th>
				<th>Paciente</th>
				<th>Data Nasc.</th>
				<th>Localizacao</th>
				<th>Opcoes</th>
			</tr>
		<thead>
		<tbody>		
TBL;
}



		?>
<script>
	function botao(age_codigo,age_atendido) {
		
		$.ajax({
			url: "realocar_paciente.php?age_codigo="+age_codigo+'&age_atendido='+age_atendido,
			type: "POST",
			success: function(r){
				alert("Alteracao Realizada com Sucesso!");
				location.reload();
			}
		});		
	}

	function botaoManchester(age_codigo,age_escala_manchester) {
		
		$.ajax({
			url: "defineEscalaManchester.php?age_codigo="+age_codigo+'&age_escala_manchester='+age_escala_manchester,
			type: "POST",
			success: function(r){
				alert("Alteracao Realizada com Sucesso!");
				location.reload();
			}
		});	
	}
</script>		
<style>
	table tbody tr td { cursor: n-resize; }
</style>

<div id="fila">

<?php 
//echo "<pre>".print_r($_REQUEST,1);
$ultimoMedico = 0;
$i=0;
while($r = pg_fetch_array($query)){
	$i++;

	if($ultimoMedico != $r['usr_codigo']){
		if($ultimoMedico){
			echo "</table></div></div>"; // tabela que lista os agendamentos
			echo $common->closeTab();
		}

		$ultimoMedico = $r['usr_codigo'];
		echo "<div class=\"order\">";
		echo $common->menuTab($r['usr_nome'],NULL,"tabs_".$r['usr_codigo'],"abas");
		echo $common->bodyTab('1');
		openTable();
	}

	if($r[age_atendido]=="A") {
		$sts = "<font color=red>Atendido</font>";
		$ck_a[$i] = "checked";
	}
	if($r[age_atendido]=="S") {
		$ck_s[$i] = "checked";
		$sts = "<font>Aguardando Triagem</font>";
	}
	if($r[age_atendido]=="P") {
		$ck_p[$i] = "checked";
		$sts = "<fon>Aguardando Medico</font>";
	}
	if($r[age_atendido]=="F") {
		$sts = "<font>Paciente Ausentou-se</font>";
	}
	if($r[age_atendido]=="M") {
		$sts = "<font>Falta Médica</font>";
	}
	if($r[age_atendido]=="E" or $r[age_atendido]=="I") {
		$sts = "<font>Em atendimento</font>";
	}
	
		$opcoes  = "<form>
		<input type=radio onclick=\"botao('".$r[age_codigo]."','A')\" name='relaloc[".$i."]' value='A' ".$ck_a[$i].">Atendido<br>
		<input type=radio onclick=\"botao('".$r[age_codigo]."','S')\" name='relaloc[".$i."]' value='S' ".$ck_s[$i].">Enfermagem<br>
		<input type=radio onclick=\"botao('".$r[age_codigo]."','P')\" name='relaloc[".$i."]' value='P' ".$ck_p[$i].">Medico</form>";
		// $teste = "<form action=""><p> TEXTO</p></form>";
	echo "	<tr>
				<td style='color:$cor' width=\"40\" class=\"ui-widget ui-widget-content c\">".$r['age_hora']."</td>
				<td style='color:$cor' class=\"ui-widget ui-widget-content\">".$r['usu_nome']."</td>
				<td style='color:$cor' width=\"150\" class=\"ui-widget ui-widget-content\">".$r['datanasc']."</td>
				<td style='color:$cor' width=\"100\" class=\"ui-widget ui-widget-content c a\"><b>$sts</b></td>
				<td width=\"150\" class=\"ui-widget ui-widget-content a\" align='left'>".$opcoes."</td>
			</tr>\n";
	// E = EM ATENDIMENTO
	//P = PRE
	//A =ATENDIDO
}
if($ultimoMedico){
	echo "<tbody></table></div></div>"; // tabela que lista os agendamentos
	echo $common->closeTab();
} else {
	echo "<br /><em>Nenhum paciente na fila de espera.</em>";
}
// /fila
?></div>
