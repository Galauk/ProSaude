<?php
session_start();
#
# REDIRECIONAR PARA A AREA DE EXAMES
#

 if($age_tipo=="EX") { 
     header("Location: exa_agendamento.php?agt_codigo=$agt_codigo&id_login=$id_login&med_codigo=$med_codigo&uni_codigo=$uni_codigo&esp_codigo=$esp_codigo&usu_codigo=$usu_codigo&age_paciente=$age_paciente&age_tipo=$age_tipo&age_item=$age_item");
 }

#
#  FINAL DO REDIRECIONAMENTO
#
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	verauth($id_login);

	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
    cabecario();



?>

	<style>
		.borda {
			border-bottom: 1px solid;
			border-top: 1px solid;
			border-left: 1px solid;
			border-right: 1px solid;
			border-color: #cccccc;
		}
		.borda2 {
			border-bottom: 1px solid;
			border-top: 1px solid;
			border-left: 1px solid;
			border-right: 1px solid;
			border-color: #909090;
		}
	</style>

<?php

	# VARIAVEIS PARA TESTE
	#########################
	/*$med_codigo = 756;
	$esp_codigo = 212;
	$uni_codigo = 560405;
	$agt_codigo = 10;
    $usu_codigo = 306224;
    $age_hora = "08:00";
    $age_item = "CB";
    $age_tipo = "PC";
    $age_data = "12/06/2007";*/
    #########################
    
	if(empty($age_data))
	{
		$sql = "select a.med_codigo, a.uni_codigo, a.qtde, a.gra_hora_ini,
				to_char(a.gra_data, 'dd/mm/yyyy') as gra_data
				from view_qtde_grade as a
				where a.med_codigo = $med_codigo
				and a.uni_codigo = $uni_codigo
                and gra_data >= current_date";
	} else {
        
        $d = explode("/", $age_data);
        if($d[2]."-".$d[1]."-".$d[0] < date("Y-m-d"))
        {
            echo "
                <tr bgcolor='#FFFFFF'>
                    <td colspan='5'>
                        <font color=red> * </font> &nbsp; <b> Data inv&aacute;lida para agendamento. </b>
                    </td>
                </tr>";
            exit;
        } else {
            $sql = "select a.med_codigo, a.uni_codigo, a.qtde, a.gra_hora_ini,
                    to_char(a.gra_data, 'dd/mm/yyyy') as gra_data
                    from view_qtde_grade as a
                    where a.med_codigo = $med_codigo
                    and a.uni_codigo = $uni_codigo
                    and a.gra_data = '{$age_data}'
                    and a.gra_hora_ini = '{$age_hora}'";
        }
	}
	
	$exec_sql = pg_query($sql);
	
	echo "
		<form method='post' action='agendamento_atendimento.php' target='iframe_dir'>
			<input type='hidden' name='id_login' value='$id_login'>
			<input type='hidden' name='esp_codigo' value='$esp_codigo'>
			<input type='hidden' name='age_item' value='$age_item'>
			<input type='hidden' name='uni_codigo' value='$uni_codigo'>
			<input type='hidden' name='med_codigo' value='$med_codigo'>
			<input type='hidden' name='agt_codigo' value='$agt_codigo'>
			<input type='hidden' name='age_tipo' value='$age_tipo'>
			<input type='hidden' name='age_vaga' value='$age_vaga'>
			<input type='hidden' name='usu_codigo' value='$usu_codigo'>
			<input type='hidden' name='age_paciente' value='$age_paciente'>
			<input type='hidden' name='acao' value='addagendamento'>
			<input type='hidden' name='grm_mensal' value='$grm_mensal[grm_qtde]'>
			<table width='100%' cellspacing='1' cellpadding='4' border='0'>
				<tr bgcolor='#CCCCCC'>
                    <td width='10' class='borda2'>&nbsp;</td>
                    <td width='10' class='borda2'><font color='red'>Tp.Age.</font></td>
                    <td width='10' class='borda2'><font color='red'>Semana</font></td>
                    <td class='borda2'><font color='red'>&nbsp;</font></td>
                </tr>";

	if($acao=="age")
	{
		echo "
			<tr bgcolor='#FFFFFF'>
				<td colspan='5'>
					<font color=red> * </font> &nbsp; <b> Agendado </b>
				</td>
			</tr>";
	}
	
	reglog($id_login,"Entrando em Fazer Agendamento");
	
	if(!empty($age_hora))
	{
		$and_hora = " and gra_hora_ini = '{$age_hora}' ";
	}
	
    if(empty($age_data))
    {
    	$and_data = " and b.gra_data  >= current_date ";
    } else {
        $and_data =  " and b.gra_data = '{$age_data}' ";
    }
	
	$sql = "select to_char(b.gra_data,'DD/MM/YYYY') as gra_data, b.gra_hora_ini, 
		coalesce(
			(select a.qtde
			from view_qtde_grade as a
			where a.age_tipo = '$age_tipo'
			and a.med_codigo = '$med_codigo'
			and a.uni_codigo = '$uni_codigo'
			and a.esp_codigo = '$esp_codigo'
			and a.gra_data >= b.gra_data
			and a.age_item = '$age_item'
			and a.age_tipo = '$age_tipo'
			and a.gra_hora_ini = b.gra_hora_ini
			order by gra_data limit 1),0)
			-
		coalesce(
			(select qtde
			from view_qtde_medico as c
			where c.med_codigo = '$med_codigo'
			and c.uni_codigo = '$uni_codigo'
			and c.esp_codigo = '$esp_codigo'
			and c.age_data = b.gra_data
			and c.age_tipo = '$age_item'
			and c.age_item = '$age_tipo'
			and c.age_hora = b.gra_hora_ini limit 1),0) as calc_qtde
		from view_qtde_grade as b
		where b.med_codigo = '$med_codigo'
		and b.uni_codigo = '$uni_codigo'
		and b.age_tipo = '$age_tipo'
		and b.esp_codigo = '$esp_codigo'
		and b.age_item = '$age_item'
		and b.age_tipo = '$age_tipo'
		$and_data
		and
		(coalesce(
			(select a.qtde
			from view_qtde_grade as a
			where a.age_tipo = '$age_tipo'
			and a.med_codigo = '$med_codigo'
			and a.uni_codigo = '$uni_codigo'
			and a.esp_codigo = '$esp_codigo'
			and a.age_item = '$age_item'
			and a.age_tipo = '$age_tipo'
			and a.gra_data >= b.gra_data
            and a.gra_hora_ini = b.gra_hora_ini
            order by gra_data limit 1),0)
			-
		coalesce(
			(select qtde
			from view_qtde_medico as c
			where c.med_codigo = '$med_codigo'
			and c.uni_codigo = '$uni_codigo'
			and c.esp_codigo = '$esp_codigo'
			and c.age_data = b.gra_data
			and c.age_tipo = '$age_item'
			and c.age_item = '$age_tipo'
			and c.age_hora = b.gra_hora_ini limit 1),0)) > 0
		$and_hora
		order by b.gra_data, b.gra_hora_ini
		limit 1";

	$exec_sql = pg_query($sql);
	
	$row = pg_fetch_array($exec_sql);
	
	if(($row["calc_qtde"] <= 0 and $age != ""))
	{
		echo "
			<tr bgcolor='#FFFFFF'>
				<td colspan='5'>
					<font color='red'> * </font> &nbsp; M&eacute;dico sem vaga.
				</td>
			</tr>";
		exit;
	}
	
	if(!empty($age_data) && $age_data)
	{
		$and_sql = " and grm_periodo <= '{$age_data}' ";
	} else {
		$and_sql = " and grm_periodo <= current_date ";
	}
	
	$sql = "select grm_periodo
			from grade_mensal
			where med_codigo = $med_codigo
			and esp_codigo = $esp_codigo
			and agt_codigo = $agt_codigo
			and age_item = '$age_item'
			$and_sql
			order by 1 desc limit 1";
		
	$exec_sql = pg_query($sql);
	
    if(pg_num_rows($exec_sql) == 0 && empty($age_data))
    {
        $sql = "select grm_periodo
        from grade_mensal
        where med_codigo = $med_codigo
        and esp_codigo = $esp_codigo
        and agt_codigo = $agt_codigo
        and age_item = '$age_item'
        and grm_periodo >= current_date
        order by 1 desc limit 1";
        $exec_sql = pg_query($sql);
    }
    
	$data_periodo = pg_fetch_array($exec_sql);
	
	$sql = "select (('{$data_periodo[0]}'::date + interval '1 month') - interval '1 day')::date - ('{$data_periodo[0]}')";
	
	$exec_sql = pg_query($sql);
	
	$quantidade = pg_fetch_array($exec_sql);
	
	$sql = "select *
				  from grade_mensal
				  where med_codigo = $med_codigo
				  and esp_codigo = $esp_codigo
				  and agt_codigo = $agt_codigo
				  and age_item = '$age_item'
				  and '$row[gra_data]'
				  between grm_periodo
				  and (grm_periodo + interval '$quantidade[0] day')";
	
	$query_grm = pg_query($sql);
	
	$grm_mensal = pg_fetch_array($query_grm);
	
	$sql = "select *
			from agendamento
			where agt_codigo = $agt_codigo
			and med_codigo = $med_codigo
			and esp_codigo = $esp_codigo
			and uni_codigo = $uni_codigo
			and age_data
			between '$grm_mensal[grm_periodo]'
			and ('$grm_mensal[grm_periodo]'::date + interval '$quantidade[0] day')";
	
	$query_age = pg_query($sql);
    
	if($row["calc_qtde"] == "0" || $row["calc_qtde"] == "")
	{ 
		echo "
			<tr bgcolor='#FFFFFF' id='mensagem'>
				<td colspan='5'>
					<font color='red'> * </font> &nbsp; M&eacute;dico Sem Vaga Dispon&iacute;vel
				</td>";
				if($age_item == "ES")
				{
					echo "
						<td>
							<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/lista_espera_on.jpg' onclick='parent.salvarLista();return false;'>
						</td>";
				}
		echo "</tr>";
		exit;
	}
	
	if($grm_mensal["grm_qtde"] <= pg_num_rows($query_age))
	{ 
		echo "
			<tr bgcolor='#FFFFFF'>
				<td colspan='5'>
					<font color='red'> * </font> &nbsp; Esta Unidade N&atilde;o Possui Vaga Dispon&iacute;vel
				</td>
			</tr>";
		exit;
	}
	
	if(!empty($row[gra_data]))
	{
		echo "
			<td width='10' class='borda'>
				<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/agendar_on.jpg'  style='border: 0;'>
			</td>
			<td width='10' align=center class='borda'>$age_tipo</td>
			<td width='110' align=center class='borda'>";
	}
	
	echo "
		<input type='hidden' name='age_data' value='$row[gra_data]'>
		<input type='hidden' name='age_hr' value='$row[gra_hora_ini]'>";
		
	if(empty($row["gra_data"]))
	{
		echo "
			<td colspan='4'>
				<b> => <font color='red'> M&eatilde;dico n&atilde;o possui vagas. </font> </b>
			</td>";
	} else {
		echo "
				<b> <font color=blue> $row[gra_hora_ini] </font> $row[gra_data] (<font color='red'> $row[calc_qtde] </font>) </b>";
	}
	
	$exp = explode("/",$row[gra_data]);
	$allsemana = date('w', mktime(0,0,0,$exp[1],$exp[0],$exp[2]));
	switch($allsemana)
	{
		case 1:
		  $dia_da_semana = "Segunda-Feira";
		break;
		case 2:
		  $dia_da_semana = "Ter&ccedil;a-Feira";
		break;
		case 3:
		  $dia_da_semana = "Quarta-Feira";
		break;
		case 4:
		  $dia_da_semana = "Quinta-Feira";
		break;
		case 5:
		  $dia_da_semana = "Sexta-Feira";
		break;
		case 6:
		  $dia_da_semana = "S&atilde;bado";
		break;
		case 0:
		  $dia_da_semana = "Domingo";
		break;
	}
	
	if(!empty($row["gra_data"]))
	{
		echo "<td width='90' class='borda'>$dia_da_semana</td>";
	}
	 
	echo "</tr>";
	echo "</table>";
	echo "</form>";
	 
		
?>
