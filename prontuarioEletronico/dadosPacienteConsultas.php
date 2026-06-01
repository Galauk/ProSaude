<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
  
 echo"<link href='estilo.css' rel='stylesheet' type='text/css'>
      <link href='css/estiloForm.css' rel='stylesheet' type='text/css' />
 	  <link href='css/estiloCommon.css' rel='stylesheet' type='text/css' />";
 
$form = new classForm();
$common = new commonClass();
echo $common->incJquery();
echo $common->menuTab(array('Consultas'));
echo"<table>
	<tr>
		<td>".
		 $common->bodyTab('1')."	
	
	\n<div id='form_a'>\n";
	
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
        <table width='900' class='lista'>
            <tr>
                <th colspan='1' class='borda'>&nbsp;</th>
                <th>Data</th>
                <th>Hora</th>
                <th>Tipo</th>
                <th>Especialidade</th>
                <th>M&eacute;dico</th>
                <th>Unidade</th>
                
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
                
                $sql = "select * from medico where med_codigo = $row[med_codigo]";
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
                            <a href='#' onclick='window.open(\"print_guia_2via.php?uni_codigo=$row[uni_codigo]&esp_codigo=$row[esp_codigo]&agt_codigo=$row[agt_codigo]&usu_codigo=$row[usu_codigo]&age_codigo=$row[age_codigo]&med_codigo=$row[med_codigo]&id_login={$id_login}\",null,\"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no\");'>
                                <img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/print_prontuario.jpg' border='0'>
                            </a>
                        </td>";
                       
                        echo "
                            <td>$row[age_data]</td>
                            <td>$row[age_hora]</td>
                            <td>$bold_font_open</td>
                            <td>$esp[esp_nome]</td>
                            <td>$med[med_nome]</td>
                            <td>$uni[uni_desc]</td>
                           
                    </tr>";
            }
        echo "</table>";
		
	echo "\n</div>".
	// /ABA: CONSULTAS ---------------------------------------------------------</td>
	$common->closeTab();
?>