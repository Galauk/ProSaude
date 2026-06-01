<script>
	function imprimir(){
		var fins = $("#fins").val();
		$("#fins").hide();
		$("#show").html("<font size=2>"+fins+"</font>");
		window.print();
		window.location.reload();
	}
</script>
<?php

require_once '../global.php';
include_once COMUM ."/library/php/funcoes.inc.php";	

$common = new commonClass();
$form = new classForm();
$table = new tableClass();

$data= date("d/m/Y");
echo $common->incJquery();

$age_codigo = $_GET['age_codigo'];
echo "
<div style=\"text-align:center\">";
	echo "<center>".$common->commonButton("Imprimir", null, "print.png", "onclick=\"this.style.display='none';imprimir()\"")."</center>";
echo "
</div>";
cabecario_rel("Guia de Comparecimento");

/*
$sqlAgendamento = "SELECT age_paciente,
						  uni_desc,
						  to_char(age_data,'DD/MM/YYYY') as age_data,
						  age_horario 
					 FROM agendamento a
					 JOIN unidade u
					   ON u.uni_codigo = a.uni_codigo
					WHERE age_codigo = $age_codigo";
$queryAgendamento = pg_query($sqlAgendamento) or die ($sqlAgendamento.pg_last_error());
$regAgendamento = pg_fetch_array($queryAgendamento);

$sqlUltimoAtendimento = "SELECT to_timestamp(ate_data || ' ' || ate_hora, 'YYYY-MM-DD HH24:MI') as datas
  FROM atendimento ate
  JOIN agendamento age
    ON age.age_codigo = ate.age_codigo
 WHERE age.age_codigo = $age_codigo 
UNION ALL
SELECT pc_data as datas
  FROM pre_consulta pre
  JOIN agendamento age
    ON age.age_codigo = pre.age_codigo
WHERE age.age_codigo = $age_codigo
order by datas desc";
$queryUltimoAtendimento = pg_query($sqlUltimoAtendimento) or die ($sqlUltimoAtendimento.pg_last_error());
$regAtendimento = pg_fetch_array($queryUltimoAtendimento);

$divisao = explode(" ", $regAtendimento[datas]);
echo "<pre>".print_r($divisao);die();
$hora = substr($divisao[1], 0,5);
$dividirData = explode("-",$divisao[0]);

$dataAte = $dividirData[2]."/".$dividirData[1]."/".$dividirData[0];
*/

$sqlAgendamento = "SELECT 
						age_paciente,
						uni_desc,
						to_char(age_data_atend,'HH24:MI') AS hora_atendimento,
						to_char(age_data_atend,'DD/MM/YYYY') as data_atendimento,
						to_char(ate_datafinal,'DD/MM/YYYY') as ate_datafinal,
						ate_horafinal,
						usr_num_conselho,
						cnes_sigla_est,
						con_descricao
					 FROM 
						agendamento a
					 INNER JOIN 
						unidade u ON u.uni_codigo = a.uni_codigo
					 INNER JOIN
						atendimento ate ON ate.age_codigo = a.age_codigo
				     INNER JOIN usuarios usr
					    ON a.med_codigo=usr.usr_codigo
					 INNER JOIN conselho c
					    ON c.con_codigo=usr.con_codigo
					WHERE a.age_codigo = $age_codigo";
$queryAgendamento = pg_query($sqlAgendamento) or die (pg_last_error());
$regAgendamento = pg_fetch_array($queryAgendamento);

echo "<table border=0 width=100%>
	<tr>
		<td align=center>
			<font size=3><b>DECLARA&Ccedil;&Atilde;O DE COMPARECIMENTO</b></font>
		</td>
	</tr>
	<tr>
		<td>
			<br /><br /><br />
		 	<font size=2>Declaramos para os devidos fins, que <b>$regAgendamento[age_paciente]</b> compareceu no(a) <b>$regAgendamento[uni_desc]</b> dia $regAgendamento[data_atendimento] às $regAgendamento[hora_atendimento] e permaneceu até o dia $regAgendamento[ate_datafinal] às $regAgendamento[ate_horafinal] para o fim de:</font>
		 	<br/><br/>
		 	<textarea id=fins rows=10 cols=100> 
		 	</textarea>
		 	<div id=show> </div>
		</td>
	</tr>
</table>";
echo "
<br /><br /><br /><br /><br /><br /><br /><br />";
echo "
<div align=center>
	____________________________________________________<br/>
	$regAgendamento[con_descricao]: $regAgendamento[usr_num_conselho] - $regAgendamento[cnes_sigla_est]
</div>";
rodape_rel();