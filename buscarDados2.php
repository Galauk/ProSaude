<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	/*echo "<pre>";
		print_r($_REQUEST);
	echo "</pre>";*/
	$sel = "select (
					select a.qtde
					from view_qtde_grade as a
					where a.med_codigo = '$med_codigo'
					and a.uni_codigo = '$uni_codigo'
					and a.esp_codigo = '$esp_codigo'
					and a.gra_data = '$data'
					and a.gra_hora_ini = '$hora'
					and a.age_tipo = '$age_tipo'
				) as total,
				(
					select qtde
					from view_qtde_medico as c
					where c.med_codigo = '$med_codigo'
					and c.uni_codigo = '$uni_codigo'
					and c.esp_codigo = '$esp_codigo'
					and c.age_data = '$data'
					and c.age_hora = '$hora'
					and c.age_item = '$age_tipo'
					and c.age_tipo = '$age_item'
				) as agendado,
				(
					(
						select a.qtde
						from view_qtde_grade as a
						where a.med_codigo = '$med_codigo'
						and a.uni_codigo = '$uni_codigo'
						and a.esp_codigo = '$esp_codigo'
						and a.gra_data = '$data'
						and a.gra_hora_ini = '$hora'
						and a.age_tipo = '$age_tipo'
					)
				-
					(
						select qtde
						from view_qtde_medico as c
						where c.med_codigo = '$med_codigo'
						and c.uni_codigo = '$uni_codigo'
						and c.esp_codigo = '$esp_codigo'
						and c.age_data = '$data'
						and c.age_hora = '$hora'
						and c.age_item = '$age_tipo'
						and c.age_tipo = '$age_item'
					)
				) as qtde";
	/*echo*/ /*$sel = "select to_char(gra_data, 'DD/MM/YYYY') as dia, gra_qtde as qtde,
				coalesce(gra_qtde, '0') - coalesce(age_qtde, '0') as disponivel
				from ( select gra_data, sum(gra_qtde) as gra_qtde, med_codigo, esp_codigo, uni_codigo
				from grade_medico where med_codigo='$_GET[med_codigo]' and esp_codigo='$_GET[esp_codigo]' and uni_codigo='$_GET[uni_codigo]'
				and gra_data = '$_GET[data]' and gra_hora_ini = '$_GET[hora]' group by gra_data, med_codigo,
				esp_codigo, uni_codigo ) t1 left join ( select age_data, esp_codigo, med_codigo, uni_codigo,
				count(age_codigo) as age_qtde from agendamento where med_codigo='$_GET[med_codigo]' and esp_codigo='$_GET[esp_codigo]'
				and uni_codigo='$_GET[uni_codigo]' and age_data = '$_GET[data]'
				and age_atendido in ('N', 'R', 'S') and (age_status <> 'C' or age_status is null)
				group by age_data, esp_codigo, med_codigo, uni_codigo ) t2 on ( t1.med_codigo = t2.med_codigo
				and t1.esp_codigo = t2.esp_codigo and t1.uni_codigo = t2.uni_codigo
				and t1.gra_data = t2.age_data ) order by t1.gra_data";*/

	$exec_sel = pg_query($sel);
	$disponivel = pg_fetch_array($exec_sel);
	
	//echo pg_last_error($db);
	/*echo "<br>".$sel;*/
	//echo $disponivel[1]."-".$disponivel[2]."-".($disponivel[1]-$disponivel[2]);
	if($disponivel[0] != "" && $disponivel[1] != "" && $disponivel[2] != "")
	{
		echo $disponivel[0]."-".$disponivel[1]."-".$disponivel[2];
	}
?>
