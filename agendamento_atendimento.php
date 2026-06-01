<?php
	session_start();
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	verauth($id_login);
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();

?>
<script language='JavaScript' type='text/javascript' src='abas.js'></script>
<link rel='stylesheet' type='text/css' href='estilo_abas.css' title='Abas'>
<style type="text/css">
.borda {
	border-bottom: 1px solid;
	border-top: 1px solid;
	border-left: 1px solid;
	border-right: 1px solid;
	border-color: #909090;
}
.borda2 {
	border-bottom: 1px solid;
	border-top: 1px solid;
	border-left: 1px solid;
	border-right: 1px solid;
	border-color: #CCCCCC;
}
.borda3 {
    border-bottom: 1px solid;
    border-right: 1px solid;
    border-color: #C9C9C9;
}
.borda4 {
    border-bottom: 1px dotted;
    border-right: 1px dotted;
    border-color: #C9C9C9;
}
</style>
    
<?php
    
    if( $acao == "addagendamento" )
    {
        
        reglog($id_login,"Agendando Paciente: $age_paciente");
        
        $sql = "select *
                from agendamento
                where age_data = '$age_data'
                and age_hora = '$age_hr'
                and med_codigo = $med_codigo
                and usu_codigo = $usu_codigo
                and age_item = '$age_tipo'
                and uni_codigo = $uni_codigo
                and age_tipo = '$age_item'
                and esp_codigo = $esp_codigo";
        
        $exec_sql = pg_query($sql);
        
        if(pg_num_rows($exec_sql) != "0")
        {
            echo "
                <font color='red' size='2'>
                    <center><b>Este paciente j&aacute; est&aacute; agendado para esta data</b></center>
                </font><br><br>";
        } else {
            
            $sql = "select a.med_codigo, a.uni_codigo, a.qtde, a.gra_hora_ini, a.gra_data
                    from view_qtde_grade as a
                    where a.med_codigo = $med_codigo
                    and a.uni_codigo = $uni_codigo
                    and a.age_tipo = '$age_tipo'
                    and a.gra_data = '$age_data'
                    and a.gra_hora_ini = '$age_hr'";
                    
            $query = pg_query($sql);
            
            $sql = "select grm_periodo
                    from grade_mensal
                    where med_codigo = $med_codigo
                    and esp_codigo = $esp_codigo
                    and agt_codigo = $agt_codigo
                    and age_item = '$age_item'
                    and grm_periodo <= '{$age_data}'
                    order by 1 desc limit 1";
                
            $exec_sql = pg_query($sql);
	
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
                    and '$age_data'
                    between grm_periodo
                    and (grm_periodo + interval '$quantidade[0] day')";
            
            $query_grm = pg_query($sql);
            
            $grm_mensal = pg_fetch_array($query_grm);
            
            $sql = "select a.med_codigo, a.uni_codigo, a.qtde, a.age_hora, a.age_data
                    from view_qtde_medico as a
                    where a.med_codigo = $med_codigo
                    and a.uni_codigo = $uni_codigo
                    and a.age_data = '$age_data'
                    and a.age_hora = '$age_hr'";
            $agemed = pg_query($sql);

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
                        (select SUM(qtde)
                        from view_qtde_medico as c
                        where c.med_codigo = '$med_codigo'
                        and c.uni_codigo = '$uni_codigo'
                        and c.esp_codigo = '$esp_codigo'
                        and c.age_data = b.gra_data
                        and c.age_tipo = '$age_item'
                        and c.age_item = '$age_tipo'
                        and c.age_hora = b.gra_hora_ini
						and age_atendido in ('N', 'R', 'S')
						limit 1),0) as calc_qtde
                    from view_qtde_grade as b
                    where b.med_codigo = '$med_codigo'
                    and b.uni_codigo = '$uni_codigo'
                    and b.age_tipo = '$age_tipo'
                    and b.esp_codigo = '$esp_codigo'
                    and b.age_item = '$age_item'
                    and b.age_tipo = '$age_tipo'
                    and b.gra_data = '{$age_data}'
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
                        (select SUM(qtde)
                        from view_qtde_medico as c
                        where c.med_codigo = '$med_codigo'
                        and c.uni_codigo = '$uni_codigo'
                        and c.esp_codigo = '$esp_codigo'
                        and c.age_data = b.gra_data
                        and c.age_tipo = '$age_item'
                        and c.age_item = '$age_tipo'
                        and c.age_hora = b.gra_hora_ini
						and age_atendido in ('N', 'R', 'S')
						limit 1),0)) > 0
                    and gra_hora_ini = '{$age_hr}'
                    order by b.gra_data, b.gra_hora_ini
                    limit 1";
           
            $exec_sql = pg_query($sql);
            $rowVerif = pg_fetch_array($exec_sql);
            
            if($rowVerif["calc_qtde"] > 0)
            {
                $sql = "select nextval('seq_age_codigo')";
                $exec_sql = pg_query($sql);
                $nli = pg_fetch_array($exec_sql);
                $age_codigo = $nli[0];
                $sql = "insert into agendamento
                        (age_codigo, age_data, med_codigo, age_hora, usu_codigo,
                        age_tipo, age_paciente, uni_codigo, age_item, esp_codigo,
                        agt_codigo, usr_codigo_cad, dt_cadastro )
                        values
                        ($age_codigo, '$age_data', $med_codigo, '$age_hr', $usu_codigo,
                        '$age_item', '$age_paciente', $uni_codigo, '$age_tipo', $esp_codigo,
                        $agt_codigo, $id_login, NOW())";
                //echo $sql;exit;
                $sql = pg_query($sql);
                echo "
                    <script>
                        url = \"print_guia2.php?age_codigo=$age_codigo\";
                        window.open(url, null, \"height=465,width=605,status=yes,toolbar=no,menubar=no,location=no\");
                        parent.document.getElementById('iframe_esq').setAttribute('src', 'msg_agendado.php');
						setTimeout(\"document.location.href='{$PHP_SELF}?usu_codigo={$usu_codigo}&id_login={$id_login}'\", 750);
                    </script>";
            } else {
                echo "
                    <b><center>
                        <font color='red' size='2'>ERRO:<br></font>
                        <font color='#000000'>Este M&eacute;dico N&atilde;o Possui mais vagas dipon&iacute;veis.</font>
                    </center></b><br><br>";
            }
        }      
    }
    
    if($action == "delage")
    {
        $sql = "delete from agendamento where age_codigo = $age_codigo";
        $sql = pg_query($sql);
        reglog($id_login,"Excluindo Agendamento: $age_paciente");
        echo "
            <script>
                setTimeout(\"location='$PHP_SELF?id_login=$id_login&acao=&usu_codigo=$usu_codigo'\", 0);
            </script>";
    }

    if($action == "del_exame")
    {
	$query = pg_query("select *from agendamento_exame_lista where agex_codigo = '$agex_codigo'");
  $num = pg_num_rows($query);
if(pg_num_rows($query)==1) {
        $sql1 = pg_query("delete from agendamento_exame_lista where agexl_codigo = $agexl_codigo") or die(pg_last_error());
        $sql2 = pg_query("delete from agendamento_exame where agex_codigo = $agex_codigo") or die(pg_last_error());
}

if(pg_num_rows($query)>=2) {
        $sql3 = pg_query("delete from agendamento_exame_lista where agexl_codigo = $agexl_codigo") or die(pg_last_error());
}
        reglog($id_login,"Excluindo Agendamento de Exame: $age_paciente");
        echo "
            <script>
                setTimeout(\"location='$PHP_SELF?id_login=$id_login&acao=&usu_codigo=$usu_codigo&tpaba=form_d'\", 0);
            </script>";
    }
    
    ################
    # Listagem
    ################
    
    if( ! chmodbtn($id_login, "listar_if", "agendamento_atendimento.php"))
    {
	exit( "<p><strong>Você não possui permissão para listagem do Histórico.<br />
		Contatar o responsável pelo sistema para esclarecimentos.</strong></p>" );
    }
    
	echo "
		<div class='tabbar'>
			<ul>
				<li><a href='#' id='form_a_link'>Consultas</a></li>
				<li><a href='#' id='form_b_link'>Dispensa&ccedil;&atilde;o</a></li>
				<li><a href='#' id='form_c_link'>PAM</a></li>
				<li><a href='#' id='form_d_link'>Exames</a></li>
			</ul>
		</div>
	";
	
	// ABA: CONSULTAS ----------------------------------------------------------
	echo "\n<div id='form_a'>\n";
	
    $sql = "select to_char(dt_cadastro,'YYYY-MM-DD') as dt_cadastro,
            usr_codigo_alt, usr_codigo_cad, agt_codigo,
            to_char(age_data,'DD/MM/YYYY') as age_data,
            age_codigo, med_codigo, age_hora, usu_codigo, age_tipo, age_atendido,
            age_paciente, uni_codigo, age_item, esp_codigo
            from agendamento
            where usu_codigo = $usu_codigo
            order by to_char(age_data,'YYYY') desc,
            to_char(age_data,'MM') desc,
            to_char(age_data,'DD') desc";
    
    $sql_busca = db_query($sql);

    echo "
        <table width='900' cellspacing='1' cellpadding='4' border='0'>
            <tr bgcolor='#CCCCCC' style='white-space:nowrap;'>
                <td colspan='2' class='borda'>&nbsp;</td>
                <td class='borda'><font color='red'>Data</font></td>
                <td class='borda'><font color='red'>Hora</font></td>
                <td width='100' class='borda'><font color='red'>Tipo</font></td>
                <td class='borda'><font color='red'>Especialidade</font></td>
                <td width='250' class='borda'><font color='red'>M&eacute;dico</font></td>
                <td width='400' class='borda'><font color='red'>Unidade</font></td>
                <td width='100' class='borda'><font color='red'>Usu&aacute;rio Cadastro</font></td>
            </tr>";
            while($row = pg_fetch_array($sql_busca))
            {
                
                if($row[age_atendido] == "S")
                {
                    $bold_font_open = "<font color='blue'><b>Recepcionado</font></b>";
                } else if($row[age_atendido] == "N") {
                    $bold_font_open = "Agendado";
                } else if($row[age_atendido] == "F") {
                    $bold_font_open = "<font color='red'><b>Faltou</font></b>";
                } else if($row[age_atendido] == "T") {
                    $bold_font_open = "<font color='orange'><b>Transferido</font></b>";
                }
                
                $sql = "select * from especialidade where esp_codigo = $row[esp_codigo]";
                $exec_sql = pg_query($sql);
                $esp=pg_fetch_array($exec_sql);
                
                $sql = "select * from usuarios where usr_codigo = $row[med_codigo]";
                $exec_sql = pg_query($sql);
                $med=pg_fetch_array($exec_sql);
                
                $sql = "select * from unidade where uni_codigo = $row[uni_codigo]";
                $exec_sql = pg_query($sql);
                $uni=pg_fetch_array($exec_sql);
                
                $sql = "select * from usuarios where usr_codigo = $row[usr_codigo_cad]";
                $exec_sql = pg_query($sql);
                $pacCad = pg_fetch_array($exec_sql);
                
                $data_hoje = date('Y-m-d');
                echo "
                    <tr bgcolor='FFFFFF' style='white-space:nowrap;'>
                        <td class='borda2'>
                            <a href='#' onclick='window.open(\"print_guia2.php?age_codigo=$row[age_codigo]\",null,\"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no\");'>
                                <img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/print_prontuario.jpg' border='0'>
                            </a>
                        </td>";
                        if($row["dt_cadastro"] == date("Y-m-d"))
                        {
                            echo "<td align='center' class='borda2'>";
                                echo ChmodBtn($id_login,'delpront','agendamento_atendimento.php?usu_codigo='.$usu_codigo.'&action=delage&age_codigo='.$row[age_codigo]);
                            echo "</td>";
                        } else {
                            echo "<td><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/delpront_off.jpg'></td>";
                        }
                        echo "
                            <td class='borda2'>$row[age_data]</td>
                            <td class='borda2'>$row[age_hora]</td>
                            <td class='borda2'>$bold_font_open</td>
                            <td class='borda2'>$esp[esp_nome]</td>
                            <td class='borda2'>$med[usr_nome]</td>
                            <td class='borda2'>$uni[uni_desc]</td>
                            <td class='borda2'>$pacCad[usr_nome]</td>
                    </tr>";
            }
        echo "</table>";
		
	echo "\n</div>";
	// /ABA: CONSULTAS ---------------------------------------------------------
	
	// ABA: DISPENSACAO --------------------------------------------------------
	echo "\n<div id='form_b'>\n";
	
	//echo "DISPENSACAO";
    
    //formata data para 6 meses atrás
    $ano_inicial = date("Y");
    $mes_inicial = date("n");
    
    for ($i=1; $i<=5; $i++)
    {
        $mes_inicial = $mes_inicial - 1;
        if ($mes_inicial == 0)
        {
            $mes_inicial = 12;
            $ano_inicial = $ano_inicial - 1;
        }
    }
    $dt_inicial = $ano_inicial.'-'.$mes_inicial."-01";

	//-> Listando
    
    $sql = "SELECT to_char(v_movimentacao.mov_data, 'DD/MM/YYYY'), v_movimentacao.pro_nome, 
            v_movimentacao.ite_quantidade, v_movimentacao.setor, 
            CASE WHEN v_movimentacao.tipomovim = 'S' THEN 'Saida de Consumo' 
            WHEN v_movimentacao.tipomovim = 'I' THEN 'Inventario' 
            WHEN v_movimentacao.tipomovim = 'M' THEN 'Emprestimo' 
            WHEN v_movimentacao.tipomovim = 'P' THEN 'Permuta' 
            WHEN v_movimentacao.tipomovim = 'R' THEN 'Perdas' 
            WHEN v_movimentacao.tipomovim = 'O' THEN 'Outras Saidas' 
            WHEN v_movimentacao.tipomovim = 'E' THEN 'Nota Fiscal de Compra' 
            WHEN v_movimentacao.tipomovim = 'A' THEN 'Ajuste' 
            WHEN v_movimentacao.tipomovim = 'D' THEN 'Doacao' 
            WHEN v_movimentacao.tipomovim = 'V' THEN 'Devol. Setor' 
            WHEN v_movimentacao.tipomovim = 'T' THEN 'Transferência' 
            ELSE 'Indefinido' 
            END AS tipo_consumo,
            itens_movimento.ite_qtde_dia, itens_movimento.ite_posologia,
            itens_movimento.ite_detalhes_tratamento, itens_movimento.ite_observacoes
            FROM v_movimentacao 
            LEFT JOIN itens_movimento 
                ON itens_movimento.mov_codigo = v_movimentacao.mov_codigo
            WHERE v_movimentacao.usu_codigo = ".$usu_codigo." 
            AND v_movimentacao.mov_data > '".$dt_inicial."' 
            AND itens_movimento.pro_codigo = v_movimentacao.pro_codigo
			ORDER BY mov_data";
    

    echo "
        <table width='98%' align='center' cellspacing='0' cellpadding='0' border='0'>
            <tr>
                <td>
                    <fieldset>
                        <legend>Medicamentos Retirados pelo Paciente nos Últimos 6 Meses</legend>
                        <table width='100%' align='center' cellspacing='2' cellpadding='4' border='0'>
                            <tr bgcolor='#F9F9F9' style='white-space:nowrap;'>
                                <td width='110' class='borda3' style='font-weight:bold'>Data</td>
                                <td width='270' class='borda3' style='font-weight:bold'>Produto</td>
                                <td width='50' class='borda3' style='font-weight:bold'>Qtd</td>
                                <td width='90' class='borda3' style='font-weight:bold'>Setor</td>
                                <td width='130' class='borda3' style='font-weight:bold'>Tipo de Consumo</td>
                                <td width='90' class='borda3' style='font-weight:bold'>Qtd Dias</td>
                                <td width='90' class='borda3' style='font-weight:bold'>Total de Dias</td>
                                <td width='90' class='borda3' style='font-weight:bold'>Próxima Liberação</td>
                            </tr>";
                            $res_sql = pg_query($sql);
                            $cor = 0;
                            $cor1 = "#F2F5F3";
                            while($row=pg_fetch_array($res_sql))
                            {
                                $cor++;
                                //separa a data da movimentacao para criar a data final, 
                                //de acordo com a qtd de dias que o medicamento sera utilizado
                                $temp_dia = substr($row[0],0,2);
                                $temp_mes = substr($row[0],3,2);
                                $temp_ano = substr($row[0],6,4);
                                $dt_liberacao = date("d/m/Y", mktime(0, 0, 0, $temp_mes, $temp_dia+((int)$row[2]/$row[5]), $temp_ano));
                                echo "<tr style='white-space:nowrap;' ";
                                if( $cor%2 == 0 ){ echo "bgcolor='$cor1'"; }
                                echo ">
                                    <td align='center' class='borda4'>$row[0]</td>
                                    <td width='270' class='borda4'>$row[1]</td>
                                    <td align='right' class='borda4'>".number_format($row[2],0,',','.')."</td>
                                    <td class='borda4'>$row[3]</td>
                                    <td class='borda4'>$row[4]&nbsp;</td>
                                    <td class='borda4' align='right'>$row[5]&nbsp;</td>
                                    <td class='borda4' align='right'>".(int)($row[2]/$row[5])."&nbsp;</td>
                                    <td class='borda4'>$dt_liberacao</td>
                                </tr>";
                                /*echo "<tr ";
                                if( $cor%2 == 0 ){ echo "bgcolor='$cor1'"; }
                                echo ">
                                    <td align='left' class='borda4' colspan='2'><b>Posologia:</b><br />".$row[6]."</td>
                                    <td align='left' class='borda4' colspan='3'><b>Detalhes do tratamento:</b><br />".$row[7]."</td>
                                    <td align='left' class='borda4' colspan='3'><b>Obsevações:</b><br />".$row[8]."</td>
                                </tr>";*/
                            }
                    echo "
                        </table>
                    </fieldset>
                </td>
            </tr>
        </table>";
    
	echo "\n</div>";
	// /ABA: DISPENSACAO -------------------------------------------------------

	// ABA: PAM ----------------------------------------------------------------
	echo "\n<div id='form_c'>\n";
	
	$stmt = "SELECT to_char(a.ate_data,'DD/MM/YYYY') as ate_data, a.ate_hora,
				uni.uni_desc
			FROM atendimento AS a
			NATURAL JOIN usuario AS u 
			LEFT JOIN unidade AS uni ON uni.uni_codigo = u.uni_unidade
			WHERE usu_codigo='$usu_codigo'
			ORDER BY ate_data, ate_hora DESC";
			
	$query = db_query($stmt);
	
	echo "
	<table cellspacing='2' cellpadding='4'>
		<tr bgcolor='#F9F9F9'>
			<td class='borda3 c' style='font-weight:bold' width='75'>Data</td>
			<td class='borda3 c' style='font-weight:bold' width='75'>Hora</td>
			<td class='borda3' style='font-weight:bold'>Unidade</td>
		</tr>
	";
	
	while( $rr = pg_fetch_array($query ))
	{
 
		echo "
		<tr>
			<td class='borda4 c'>$rr[ate_data]</td>
			<td class='borda4 c'>$rr[ate_hora]</td>
			<td class='borda4'>$rr[uni_desc]</td>
        </tr>";
	}
	
	echo "\n\t</table>";
	
	echo "\n</div>";
	// /ABA: PAM ---------------------------------------------------------------
	


	// ABA: EXAMES ----------------------------------------------------------------
	echo "\n<div id='form_d'>\n";
	
        $stmt = "select *,to_char(lst.agexl_data,'DD/MM/YYYY') as data_age,TRANSLATE(proc_nome, 'ZZZ-', '') as proc_nome,lst.agexl_codigo,agt.usu_codigo from agendamento_exame as agt left join agendamento_exame_lista as lst on lst.agex_codigo = agt.agex_codigo
                 left join procedimento as proc on proc.proc_codigo = lst.proc_codigo left join medico as med on med.med_codigo = lst.med_codigo 
		 left join usuarios as usu on usu.usr_codigo = lst.usr_codigo_cad where agt.usu_codigo = $usu_codigo order by lst.agexl_data";
	$query = db_query($stmt);

     echo "
        <table width='900' cellspacing='1' cellpadding='4' border='0'>
		<tr bgcolor='#CCCCCC' style='white-space:nowrap;'>
                <td colspan='2' class='borda'>&nbsp;</td>
                <td class='borda'><font color='red'>Data</font></td>
                <td class='borda'><font color='red'>Procedimento</font></td>
                <td width='400' class='borda'><font color='red'>Laboratorio</font></td>
                <td width='100' class='borda'><font color='red'>Usuario Cadastro</font></td>
            </tr>";
	
	
	while( $rr = pg_fetch_array($query ))
	{
     echo "<tr bgcolor='#CCCCCC' style='white-space:nowrap;'>
		<tr bgcolor='#FFFFFF' style='white-space:nowrap;'>
                <td colspan='2' class='borda'><a href=agendamento_atendimento.php?usu_codigo=$rr[usu_codigo]&action=del_exame&agexl_codigo=$rr[agexl_codigo]&agex_codigo=$rr[agex_codigo]><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/delpront_on.jpg border=0></td>
                <td class='borda'><font color='red'>$rr[data_age]</font></td>
                <td class='borda'><font color='red'>$rr[proc_nome]</font></td>
                <td width='250' class='borda'><font color='red'>$rr[med_nome]</font></td>
                <td width='100' class='borda'><font color='red'>$rr[usr_nome]</font></td>
            </tr>";
	
	}
	
	echo "\n\t</table>";
	
	echo "\n</div>";
	// /ABA: EXAMES ---------------------------------------------------------------
	
	echo "
	<script type='text/javascript'>";
if(empty($tpaba)) { $tpaba = "form_a"; $tpaba2 = "form_d";} 
if($tpaba=="form_d") { $tpaba2 = "form_a"; } 
	echo "	var TAB = new tabHandler( '$tpaba',  'form_b', 'form_c', '$tpaba2' );
		with( document )
		{
			getElementById('form_a_link').onclick = function() { TAB.show('form_a') };
			getElementById('form_b_link').onclick = function() { TAB.show('form_b') };
			getElementById('form_c_link').onclick = function() { TAB.show('form_c') };
			getElementById('form_d_link').onclick = function() { TAB.show('form_d') };
		}
	</script>
	";
    
?>
