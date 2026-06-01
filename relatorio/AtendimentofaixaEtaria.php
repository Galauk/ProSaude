<?php

require_once '../global.php';
include_once COMUM ."/library/php/funcoes.inc.php";	

$common = new commonClass();
$form = new classForm();
$table = new tableClass();

$data= date("d/m/Y");
echo $common->incJquery();
$tp_rel = $_GET["tp_rel"];

cabecario_rel("Faixa Etaria dos Pacientes",$data_inicial,$data_final);

if($_REQUEST['usr_codigo']!=0){
	$andMedico = " AND ate.med_codigo=".$_REQUEST['usr_codigo'];
}

if($_REQUEST['di']=="" OR $_REQUEST['df']==""){
	echo "FAVOR INFORMAR O PERIODO<br><br><br><a href='javscript:histori.go(-1)'>VOLTAR</a>";
} else {
	$andData = " AND ate.ate_data >= '".$_REQUEST['di']."' and ate.ate_data <= '".$_REQUEST['df']."'";
}


$calculoIdade = " SELECT calcula_idade (usu.usu_codigo) AS idade,count(calcula_idade (usu.usu_codigo)) AS total FROM atendimento as ate join usuario as usu on usu.usu_codigo = ate.usu_codigo where 1=1 $andData $andMedico GROUP BY calcula_idade (usu.usu_codigo) ORDER BY idade";

//echo $calculoIdade;

// $sql = "select usr_nome,count(age.age_codigo) as total from agendamento as age join usuarios as usr on usr.usr_codigo = age.med_codigo
// where 1=1 		 	   $andData
// 	           $andUnidade group by med_codigo,usr_nome order by usr_nome";
	           
$query=pg_query($calculoIdade) or die(pg_last_error());

if(!pg_num_rows($query)){
	echo "<em>Nenhum resultado encontrado.</em>";
} else {
		
	echo "<table class=\"lista\">";
	
	if($tp_rel == 0){
		echo "  <tr>
				  <th>Idade</th>
				  <th>N&uacute;mero de pessoas atendidas.</th>
				</tr>";
				
		while($r = pg_fetch_array($query)){
			if ($r['idade'] >= 0 && $r['idade'] < 119) {
				echo "<tr>";
				echo "  <td>{$r['idade']}</td>";
				echo "  <td class=\"d\">{$r['total']}</td>";
				echo "</tr>";	
			}
		}
	} 
	
	echo "</table>";
}
rodape_rel();