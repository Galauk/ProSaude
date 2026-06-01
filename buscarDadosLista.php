<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
    cabecario();

    $select = "select * from lista_espera where lie_codigo = $_GET[lie_codigo] and (lie_status <> 'D' or lie_status is null)";
    
    $exec_select = pg_query($select);
    
    $linha = pg_fetch_array($exec_select);
    
    if($_GET[controle] == 1)
    {
    
        echo "<fieldset>";
            echo "<legend>Dados do paciente</legend>";
            echo "<input type='hidden' name='lie_codigo' id='lie_codigo' value='$_GET[lie_codigo]' />";
            
            echo "<table width='100%' cellspacing='2' cellpadding='2' border='0'>";
                echo "<tr>";
                    echo "<td width='110'>Numero do Paciente</td>";
                    echo "<td width='40'>";
                        $sel = pg_query("select usu_nome, to_char(usu_datanasc, 'dd/mm/yyyy') as usu_datanasc, usu_mae, usu_end_cidade from usuario where usu_codigo = $linha[usu_codigo]");
                        $usu = pg_fetch_array($sel);
                        echo "<input type='text' name='usu_codigo' id='usu_codigo' class='boxl' size='10' value='$linha[usu_codigo]' readonly>";
                        echo "</td>";
                    echo "<td width='40'>Paciente</td>";
                    echo "<td>";
                        echo "<input type='text' name='pac_nome' id='pac_nome' class='boxl' size='60' value='$usu[usu_nome]' readonly>";
                    //"
                    echo "</td>";
                    echo "<td>Nascimento</td>";
                    echo "<td width='250'>";
                        echo "<input type='text' name='pac_nascimento' id='pac_nascimento' class='boxl' size='15' value='$usu[usu_datanasc]' readonly>";
                    echo "</td>";
                echo "</tr>";
                echo "<tr>";
                    echo "<td width='70'>M&atilde;e</td>";
                    echo "<td width='100' colspan='3'>";
                        echo "<input type='text' name='pac_mae' id='pac_mae' class='boxl' size='50' value='$usu[pac_mae]' readonly>";
                    echo "</td>";
                    echo "<td width=40>Cidade</td>";
                    echo "<td width=60>";
                        echo "<input type='text' name='pac_cidade' id='pac_cidade' class='boxl' size='23' value='$usu[usu_end_ciade]' readonly>";
                    echo "</td>";
                echo "</tr>";
            echo "</table>";
        echo "</fieldset>";
        echo "<fieldset>";
            echo "<legend>Prestador</legend>";
            echo "<table>";
                echo "<tr>";
                    echo "<td width='120px'>Unidade</td>";
                    echo "<td>";
                        echo "<input type='hidden' name='uni_codigo' id='uni_codigo' value='$linha[uni_codigo]' class='box' />";
                        $sel = pg_query("select uni_desc from unidade where uni_codigo = $linha[uni_codigo]");
                        $uni_desc = pg_fetch_array($sel);
                        echo "<input type='text' name='uni_desc' id='uni_desc' value='$uni_desc[0]' class='box' size='110' readonly/>";
                    echo "</td>";
                echo "</tr>";
                echo "<tr>";
                    echo "<td>Especialidade</td>";
                    $sel = pg_query("select esp_nome from especialidade where esp_codigo = $linha[esp_codigo]");
                    $esp_nome = pg_fetch_array($sel);            
                    echo "<td>";
                        echo "<input type='hidden' name='esp_codigo' id='esp_codigo' value='$linha[esp_codigo]' class='box' />";
                        echo "<input type='text' name='esp_nome' id='esp_nome' value='$esp_nome[0]' class='box' size='110' readonly/>";
                    echo "</td>";
                echo "</tr>";
                echo "<tr>";
                    echo "<td>M&eacute;dico</td>";
                    echo "<td>";
                        $sel = pg_query("select med_nome from medico where med_codigo = $linha[med_codigo]");
                        $med_nome = pg_fetch_array($sel);
                        echo "<input type='hidden' name='med_codigo' id='med_codigo' value='$linha[med_codigo]' class='box' />";
                        echo "<input type='text' name='med_nome' id='med_nome' value='$med_nome[0]' class='box' size='110' readonly/>";
                    echo "</td>";
                echo "</tr>";
                echo "<tr>";
                    echo "<td>";
                        echo "Data";
                    echo "</td>";
                    echo "<td>";
                        $select = "select * from view_qtde_grade
                                    where med_codigo = '{$linha[med_codigo]}'
                                    and uni_codigo = '{$linha[uni_codigo]}'
                                    and esp_codigo = '{$linha[esp_codigo]}'
                                    and (age_item = 'ES' or age_item = 'CB')
                                    and gra_data >= current_date 
                                    order by med_codigo, gra_data, gra_hora_ini";
                        $exec_select = pg_query($select);
                        //echo "<pre>".$select."</pre>";
                        echo "<select name='age_data' id='age_data' class='box'>";
                            echo "<option value=''>...</option>";
                            while($linha = pg_fetch_array($exec_select))
                            {
                                $sql = "select sum(qtde) as qtde, gra_hora_ini, age_tipo,
                                ( 
                                    select sum(qtde)
                                    from view_qtde_medico
                                    where med_codigo = '{$linha[med_codigo]}'
                                    and uni_codigo = '{$linha[uni_codigo]}'
                                    and esp_codigo = '{$linha[esp_codigo]}'
                                    and (age_tipo = 'ES' or age_tipo = 'CB')
                                    and age_atendido in ('N', 'R', 'S')
                                    and age_item = '$linha[age_tipo]'
                                    and age_data = '{$linha[gra_data]}'
                                    and age_hora = '{$linha[gra_hora_ini]}'
                                ) as qtde_agendado,
                                coalesce(sum(qtde),0) -
                                coalesce((select sum(qtde) from view_qtde_medico
                                    where med_codigo = '{$linha[med_codigo]}'
                                    and uni_codigo = '{$linha[uni_codigo]}'
                                    and esp_codigo = '{$linha[esp_codigo]}'
                                    and (age_tipo = 'ES' or age_tipo = 'CB')
                                    and age_atendido in ('N', 'R', 'S')
                                    and age_item = '$linha[age_tipo]'
                                    and age_data = '{$linha[gra_data]}'
                                    and age_hora = '{$linha[gra_hora_ini]}'),0) as total
                                from view_qtde_grade
                                where med_codigo = '{$linha[med_codigo]}'
                                and uni_codigo = '{$linha[uni_codigo]}'
                                and esp_codigo = '{$linha[esp_codigo]}'
                                and (age_item = 'ES' or age_item = 'CB')
                                and age_tipo = '$linha[age_tipo]'
                                and gra_data = '{$linha[gra_data]}'
                                and gra_hora_ini = '{$linha[gra_hora_ini]}'
                                group by gra_hora_ini, age_tipo";
                                $exec_sql = pg_query($sql);
                                $row = pg_fetch_array($exec_sql);                        
                                if($row[total] > 0)
                                {
                                    $data = explode("-", $linha[gra_data]);
                                    $data = $data[2]."/".$data[1]."/".$data[0];
                                    echo "<option value='$linha[gra_data]*$row[gra_hora_ini]*$linha[age_tipo]'>$data - ($row[total] - $row[gra_hora_ini]) - $linha[age_tipo]</option>";
                                }
                            }
                        echo "</select>";
                        //echo "<pre>".$sql."</pre>";
                    echo "</td>";
                echo "</tr>";
            echo "</table>";
        echo "</fieldset>";
        echo "<fieldset>";
            echo "<legend>Respons&aacute;vel T&eacute;cnico";
            echo "<table>";
                echo "<tr>";
                    echo "<td width='120px'>";
                        echo "Resp. T&eacute;cnico:";
                    echo "</td>";
                    echo "<td width='50px'>";
                        echo "<select name='agt_codigo' id='agt_codigo' class='box'>";
                            echo "<option value=''>....</option>";
                            $uni_ = pg_fetch_array( pg_query("select *from usuarios where usr_codigo = '$id_login'"));
                            $unidade_usuario = $uni_[uni_codigo];
                            if(!empty($unidade_usuario))
                            {
                                $and_SelectAg = "where uni_codigo = '$unidade_usuario' or agt_codigo = '384931' or agt_codigo = '393519'";
                            }
                            $sql = pg_query("select *from agente $and_SelectAg order by agt_responsavel");
                            while($row = pg_fetch_array($sql))
                            {
                                echo "<option value='$row[agt_codigo]'>$row[agt_descricao]</option>";
                            }
                    echo "</td>";
                    echo "<td>";
                        echo "<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/agendar_on.jpg' onclick='agendar(1)'>";
                    echo "</td>";
                echo "</tr>";
            echo "</table>";
        echo "</fieldset>";
    } else if($_GET[controle] == 2) {
        echo "<fieldset>";
            echo "<legend>Dados do paciente</legend>";
            echo "<input type='hidden' name='lie_codigo' id='lie_codigo' value='$_GET[lie_codigo]' />";
            
            echo "<table width='100%' cellspacing='2' cellpadding='2' border='0'>";
                echo "<tr>";
                    echo "<td width='110'>Numero do Paciente</td>";
                    echo "<td width='40'>";
                        $sel = pg_query("select usu_nome, to_char(usu_datanasc, 'dd/mm/yyyy') as usu_datanasc, usu_mae, usu_end_cidade from usuario where usu_codigo = $linha[usu_codigo]");
                        $usu = pg_fetch_array($sel);
                        echo "<input type='text' name='usu_codigo' id='usu_codigo' class='boxl' size='10' value='$linha[usu_codigo]' readonly>";
                        echo "</td>";
                    echo "<td width='40'>Paciente</td>";
                    echo "<td>";
                        echo "<input type='text' name='pac_nome' id='pac_nome' class='boxl' size='60' value='$usu[usu_nome]' readonly>";
                    //"
                    echo "</td>";
                    echo "<td>Nascimento</td>";
                    echo "<td width='250'>";
                        echo "<input type='text' name='pac_nascimento' id='pac_nascimento' class='boxl' size='15' value='$usu[usu_datanasc]' readonly>";
                    echo "</td>";
                echo "</tr>";
                echo "<tr>";
                    echo "<td width='70'>M&atilde;e</td>";
                    echo "<td width='100' colspan='3'>";
                        echo "<input type='text' name='pac_mae' id='pac_mae' class='boxl' size='50' value='$usu[pac_mae]' readonly>";
                    echo "</td>";
                    echo "<td width=40>Cidade</td>";
                    echo "<td width=60>";
                        echo "<input type='text' name='pac_cidade' id='pac_cidade' class='boxl' size='23' value='$usu[usu_end_ciade]' readonly>";
                    echo "</td>";
                echo "</tr>";
            echo "</table>";
        echo "</fieldset>";
        echo "<fieldset>";
            echo "<legend>Prestador</legend>";
            echo "<table>";
                echo "<tr>";
                    echo "<td width='120px'>Unidade</td>";
                    echo "<td>";
                        echo "<input type='hidden' name='uni_codigo' id='uni_codigo' value='$linha[uni_codigo]' class='box' />";
                        $sel = pg_query("select uni_desc from unidade where uni_codigo = $linha[uni_codigo]");
                        $uni_desc = pg_fetch_array($sel);
                        echo "<input type='text' name='uni_desc' id='uni_desc' value='$uni_desc[0]' class='box' size='110' readonly/>";
                    echo "</td>";
                echo "</tr>";
                echo "<tr>";
                    echo "<td>Especialidade</td>";
                    $sel = pg_query("select esp_nome from especialidade where esp_codigo = $linha[esp_codigo]");
                    $esp_nome = pg_fetch_array($sel);            
                    echo "<td>";
                        echo "<input type='hidden' name='esp_codigo' id='esp_codigo' value='$linha[esp_codigo]' class='box' />";
                        echo "<input type='text' name='esp_nome' id='esp_nome' value='$esp_nome[0]' class='box' size='110' readonly/>";
                    echo "</td>";
                echo "</tr>";
                echo "<tr>";
                    echo "<td>M&eacute;dico</td>";
                    echo "<td>";
                        $sel = pg_query("select med_nome from medico where med_codigo = $_GET[med_codigo]");
                        $med_nome = pg_fetch_array($sel);
                        echo "<input type='hidden' name='med_codigo' id='med_codigo' value='$_GET[med_codigo]' class='box' />";
                        echo "<input type='text' name='med_nome' id='med_nome' value='$med_nome[0]' class='box' size='110' readonly/>";
                    echo "</td>";
                echo "</tr>";
                echo "<tr>";
                    echo "<td>";
                        echo "Data";
                    echo "</td>";
                    echo "<td>";
                        $select = "select * from view_qtde_grade
                                    where med_codigo = '{$_GET[med_codigo]}'
                                    and uni_codigo = '{$linha[uni_codigo]}'
                                    and esp_codigo = '{$linha[esp_codigo]}'
                                    and (age_item = 'ES' or age_item = 'CB')
                                    and gra_data >= current_date 
                                    order by med_codigo, gra_data, gra_hora_ini";
                        $exec_select = pg_query($select);
                        echo "<select name='age_data' id='age_data' class='box'>";
                            echo "<option value=''>...</option>";
                            while($linha = pg_fetch_array($exec_select))
                            {
                                $sql = "select sum(qtde) as qtde, gra_hora_ini,
                                ( 
                                    select sum(qtde)
                                    from view_qtde_medico
                                    where med_codigo = '{$_GET[med_codigo]}'
                                    and uni_codigo = '{$linha[uni_codigo]}'
                                    and esp_codigo = '{$linha[esp_codigo]}'
                                    and (age_tipo = 'ES' or age_tipo = 'CB')
                                    and age_atendido in ('N', 'R', 'S')
                                    and age_item = '$linha[age_tipo]'
                                    and age_data = '{$linha[gra_data]}'
                                    and age_hora = '{$linha[gra_hora_ini]}'
                                ) as qtde_agendado,
                                coalesce(sum(qtde),0) -
                                coalesce((select sum(qtde) from view_qtde_medico
                                    where med_codigo = '{$_GET[med_codigo]}'
                                    and uni_codigo = '{$linha[uni_codigo]}'
                                    and esp_codigo = '{$linha[esp_codigo]}'
                                    and (age_tipo = 'ES' or age_tipo = 'CB')
                                    and age_atendido in ('N', 'R', 'S')
                                    and age_item = '$linha[age_tipo]'
                                    and age_data = '{$linha[gra_data]}'
                                    and age_hora = '{$linha[gra_hora_ini]}'),0) as total
                                from view_qtde_grade
                                where med_codigo = '{$_GET[med_codigo]}'
                                and uni_codigo = '{$linha[uni_codigo]}'
                                and esp_codigo = '{$linha[esp_codigo]}'
                                and (age_item = 'ES' or age_item = 'CB')
                                and age_tipo = '$linha[age_tipo]'
                                and gra_data = '{$linha[gra_data]}'
                                and gra_hora_ini = '{$linha[gra_hora_ini]}'
                                group by gra_hora_ini";
                                $exec_sql = pg_query($sql);
                                $row = pg_fetch_array($exec_sql);                        
                                if($row[total] > 0)
                                {
                                    $data = explode("-", $linha[gra_data]);
                                    $data = $data[2]."/".$data[1]."/".$data[0];
                                    echo "<option value='$linha[gra_data]*$row[gra_hora_ini]*$linha[age_tipo]'>$data - ($row[total] - $row[gra_hora_ini]) - $linha[age_tipo]</option>";
                                }
                            }
                        echo "</select>";
                           // echo $sql;
                    echo "</td>";
                echo "</tr>";
            echo "</table>";
        echo "</fieldset>";
        echo "<fieldset>";
            echo "<legend>Respons&aacute;vel T&eacute;cnico";
            echo "<table>";
                echo "<tr>";
                    echo "<td width='120px'>";
                        echo "Resp. T&eacute;cnico:";
                    echo "</td>";
                    echo "<td width='50px'>";
                        echo "<select name='agt_codigo' id='agt_codigo' class='box'>";
                            echo "<option value=''>....</option>";
                            $uni_ = pg_fetch_array( pg_query("select *from usuarios where usr_codigo = '$id_login'"));
                            $unidade_usuario = $uni_[uni_codigo];
                            if(!empty($unidade_usuario))
                            {
                                $and_SelectAg = "where uni_codigo = '$unidade_usuario' or agt_codigo = '384931' or agt_codigo = '393519'";
                            }
                            $sql = pg_query("select *from agente $and_SelectAg order by agt_responsavel");
                            while($row = pg_fetch_array($sql))
                            {
                                echo "<option value='$row[agt_codigo]'>$row[agt_descricao]</option>";
                            }
                    echo "</td>";
                    echo "<td>";
                        echo "<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/agendar_on.jpg' onclick='agendar(2)'>";
                    echo "</td>";
                echo "</tr>";
            echo "</table>";
        echo "</fieldset>";
    }
    
?>
