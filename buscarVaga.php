<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
    
    if($_GET[med_codigo])
    {
        $select = "select sum(qtde) as qtde,
                    (select med_nome from medico where med_codigo = '{$_GET[med_codigo]}') as med_nome,
                    (select esp_nome from especialidade ".($_GET[esp_codigo] ? "where esp_codigo = '{$_GET[esp_codigo]}'" : "").") as esp_nome,
                    (select uni_desc from unidade where uni_codigo = '{$_GET[uni_codigo]}') as uni_desc,
                    ( 
                        select sum(qtde)
                        from view_qtde_medico
                        where med_codigo = '{$_GET[med_codigo]}'
                        and uni_codigo = '{$_GET[uni_codigo]}'
                        and esp_codigo = '{$_GET[esp_codigo]}'
                        and age_tipo = '{$_GET[tipoconsulta]}' 
                        and age_atendido in ('N', 'R', 'S')
                        and age_data >= current_date 
                    ) as qtde_agendado,
                    coalesce(sum(qtde),0) -
                    coalesce((select sum(qtde) from view_qtde_medico
                        where med_codigo = '{$_GET[med_codigo]}'
                        and uni_codigo = '{$_GET[uni_codigo]}'
                        and esp_codigo = '{$_GET[esp_codigo]}'
                        and age_tipo = '{$_GET[tipoconsulta]}' 
                        and age_atendido in ('N', 'R', 'S')
                        and age_data >= current_date),0) as total
                    from view_qtde_grade
                    where med_codigo = '{$_GET[med_codigo]}'
                    and uni_codigo = '{$_GET[uni_codigo]}'
                    and esp_codigo = '{$_GET[esp_codigo]}'
                    and age_item = '{$_GET[tipoconsulta]}' 
                    and gra_data >= current_date
                    order by med_nome";
    } else {
        $select = "select a.lie_codigo, b.usu_nome, c.uni_desc, a.usu_codigo
                    from lista_espera a, usuario b, unidade c
                    where ".($_GET[esp_codigo] ? "esp_codigo = '{$_GET[esp_codigo]}' and " : "")."
                    a.uni_codigo = c.uni_codigo
                    and a.usu_codigo = b.usu_codigo
                    and lie_data_age is null
                    and med_codigo is null
                    and (a.lie_status <> 'D' or a.lie_status is null)
                    order by a.lie_data_cad";
    }
    
    $exec_select = db_query($select);
    
    if(pg_num_rows($exec_select) > 0)
    {
        echo "<table class='lista' width='100%'>";
        if($_GET[med_codigo])
        {
            /*echo "<tr class='tr'>";
                echo "<td class='tr' width='250px'>";
                    echo "M&eacute;dico";
                echo "</td>";
                echo "<td class='tr' width='250px'>";
                    echo "Especialidade";
                echo "</td>";
                echo "<td class='tr' width='250px'>";
                    echo "Unidade";
                echo "</td>";
                echo "<td class='tr' width='50'>";
                    echo "Vagas";
                echo "</td>";
                echo "<td class='tr' width='50'>";
                    echo "&nbsp;";
                echo "</td>";
            echo "</tr>";*/
        } else {
            echo "<tr class='tr'>";
                echo "<th width='25px'>";
                    echo "N";
                echo "</th>";
                echo "<th width='75px'>";
                    echo "C&oacute;d. Paciente";
                echo "</th>";
                echo "<th width='250px'>";
                    echo "Paciente";
                echo "</th>";
                echo "<th width='250px'>";
                    echo "Unidade";
                echo "</th>";
                echo "<th width='50'>";
                    echo "&nbsp;";
                echo "</th>";
            echo "</tr>";
        }
    }
    $i = 0;
    while($linha = pg_fetch_array($exec_select))
    {
        $i++;
        echo "<tr>";
        if($_GET[med_codigo])
        {
            if($linha[total] > 0)
            {
                /*echo "<td class='td'>";
                    echo $linha[med_nome];
                echo "</td>";
                echo "<td class='td'>";
                    echo $linha[esp_nome];
                echo "</td>";
                echo "<td class='td'>";
                    echo $linha[uni_desc];
                echo "</td>";
                echo "<td class='td'>";
                    echo $linha[total];
                echo "</td>";
                echo "<td class='td'>";
                    echo "<input type='hidden' name='limit' id='limit' value='{$linha[total]}'>";
                    echo "<input type='button' value='Abrir Lista' onclick='agendarComMedico($linha[total], $_GET[esp_codigo], $_GET[uni_codigo], $_GET[med_codigo])'>";*/
                
                
/*                
                $selecti = "select f.lie_codigo, a.usu_nome, b.med_nome, c.esp_nome, e.uni_desc, f.lie_data_cad
                    , g.agt_descricao
                    from usuario a, medico b, especialidade c, medico_especialidade d,
                    unidade e, lista_espera f, agente g
                    where a.usu_codigo = f.usu_codigo
                    and b.med_codigo = f.med_codigo
                    and b.med_codigo = d.med_codigo
                    and c.esp_codigo = f.esp_codigo
                    and c.esp_codigo = d.esp_codigo
                    and e.uni_codigo = f.uni_codigo
                    and b.med_codigo = '{$_GET[med_codigo]}'
                    and c.esp_codigo = '{$_GET[esp_codigo]}'
                    and e.uni_codigo = '{$_GET[uni_codigo]}'
                    and lie_data_age is null
                    and (lie_status <> 'D' or lie_status is null)                    
                    order by f.lie_data_cad";
                    
                */
                $selecti = "select * from(
                        	select su.usu_nome,le.agt_codigo,le.lie_codigo, to_char(le.lie_data_cad, 'yyyy-mm-dd hh24:mi') as lie_data_cad
                                    from lista_espera as le
                                inner join usuario as su on su.usu_codigo = le.usu_codigo
                        	where le.esp_codigo = '{$_GET[esp_codigo]}'
                                and le.med_codigo = '{$_GET[med_codigo]}'
                        	and le.uni_codigo = '{$_GET[uni_codigo]}'
                                
                                and le.lie_data_age is null
                                and (le.lie_status <> 'D' or le.lie_status is null)                    
                                order by le.lie_data_cad                                
                            ) as aux
                            left join (select agt_descricao,agt_codigo from agente) as xua on xua.agt_codigo = aux.agt_codigo ";

                
                    
                $exec_selecti = db_query($selecti);
//                    and g.agt_codigo = f.agt_codigo                 
                //echo "<fieldset>";
                    //echo "<legend>Lista de Espera</legend>";
                    echo "<table class='lista' width='100%'>";
                        echo "<tr>";
                            echo "<th width='25px'>";
                                echo "N";
                            echo "</th>";
                            echo "<th width='135px'>";
                                echo "Data/Hora";
                            echo "</th>";
                            echo "<th>";
                                echo "Paciente";
                            echo "</th>";
                            echo "<th width='250'>";
                                echo "Responsável";
                            echo "</th>";
                            echo "<th width='50'>";
                                echo "&nbsp;";
                            echo "</th>";
                        echo "</tr>";
                        $i = 0;
                        while($lin = pg_fetch_array($exec_selecti))
                        {
                            $i++;
                            $data = explode(" ", $lin[lie_data_cad]);
                            $dat = explode("-", $data[0]);
                            $da = $dat[2]."/".$dat[1]."/".$dat[0];
                            echo "<tr>";
                                echo "<td class='td' width='5px'>";
                                    echo "<font color=red><b>".$i."</b></font>";
                                echo "</td>";
                                echo "<td class='td' width='135px'>";
                                    echo $da." - ".substr($data[1], 0, 8);
                                echo "</td>";
                                echo "<td class='td'>";
                                    echo $lin[usu_nome];
                                echo "</td>";
                                echo "<td class='td'>";
                                    echo $lin[agt_descricao];
                                echo "</td>";
                                echo "<td class='td'>";
                                    if($i == 1)
                                    {
                                        echo "<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/agendar_on.jpg' onclick='abrirAgendar($lin[lie_codigo]);return false;'>";
                                    }/* else {
                                        echo "<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/agendar_off.jpg' disabled>";
                                    }*/
                                echo "</td>";
                            echo "</tr>";
                        }
                    echo "</table>";
                //echo "</fieldset>";
                echo "</td>";
            } else {
                echo "<td colspan=5 class='td'>";
                    echo "Este m&eacute;dico n&atilde;o possui vagas<br>";
				echo "</td>";
/*
                    $selecti = "select f.lie_codigo, a.usu_nome, b.med_nome, c.esp_nome, e.uni_desc, f.lie_data_cad
                    , a.usu_resp_nome, g.agt_descricao
                    from usuario a, medico b, especialidade c, medico_especialidade d,
                    unidade e, lista_espera f, agente g
                    where a.usu_codigo = f.usu_codigo
                    and b.med_codigo = f.med_codigo
                    and b.med_codigo = d.med_codigo
                    and c.esp_codigo = f.esp_codigo
                    and c.esp_codigo = d.esp_codigo
                    and e.uni_codigo = f.uni_codigo
                    and b.med_codigo = '{$_GET[med_codigo]}'
                    and c.esp_codigo = '{$_GET[esp_codigo]}'
                    and e.uni_codigo = '{$_GET[uni_codigo]}'
                    and lie_data_age is null
                    and (lie_status <> 'D' or lie_status is null)
                    order by f.lie_data_cad";
                   */
                    
                    
                $selecti = "select * from(
                        	select su.usu_nome,le.agt_codigo,le.lie_codigo, to_char(le.lie_data_cad, 'yyyy-mm-dd hh24:mi') as lie_data_cad
                                    from lista_espera as le
                                inner join usuario as su on su.usu_codigo = le.usu_codigo
                        	where le.esp_codigo = '{$_GET[esp_codigo]}'
                                and le.med_codigo = '{$_GET[med_codigo]}'
                        	and le.uni_codigo = '{$_GET[uni_codigo]}'
                                
                                and le.lie_data_age is null
                                and (le.lie_status <> 'D' or le.lie_status is null)                    
                                order by le.lie_data_cad                                                                
                            ) as aux
                            left join (select agt_descricao,agt_codigo from agente) as xua on xua.agt_codigo = aux.agt_codigo ";                    
                    
                    
                    
                $exec_selecti = db_query($selecti);
                
                //echo "<fieldset>";
                    //echo "<legend>Lista de Espera</legend>";
                    echo "<table class='lista' width='100%'>";
                        echo "<tr class='tr'>";
                            echo "<th width='25px'>";
                                echo "N";
                            echo "</th>";
                            echo "<th width='135px'>";
                                echo "Data/Hora";
                            echo "</th>";
                            echo "<th>";
                                echo "Paciente";
                            echo "</th>";
                            echo "<th width='250'>";
                                echo "Responsável";
                            echo "</th>";
                            echo "<th width='50'>";
                                echo "&nbsp;";
                            echo "</th>";
                        echo "</tr>";
                        $i = 0;
                        while($lin = pg_fetch_array($exec_selecti))
                        {
                            $i++;
                            $data = explode(" ", $lin[lie_data_cad]);
                            $dat = explode("-", $data[0]);
                            $da = $dat[2]."/".$dat[1]."/".$dat[0];
                            echo "<tr>";
                                echo "<td class='td' width='5px'>";
                                    echo "<font color=red><b>".$i."</b></font>";
                                echo "</td>";
                                echo "<td class='td' width='135px'>";
                                    echo $da." - ".substr($data[1], 0, 8);
                                echo "</td>";
                                echo "<td class='td'>";
                                    echo $lin[usu_nome];
                                echo "</td>";
                                echo "<td class='td'>";
                                    echo $lin[agt_descricao];
                                echo "</td>";
                                echo "<td class='td'>";
                                    if($i == 1)
                                    {
                                        echo "&nbsp;";//"<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/agendar_on.jpg' onclick='abrirAgendar($lin[lie_codigo]);return false;'>";
                                    }/* else {
                                        echo "<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/agendar_off.jpg' disabled>";
                                    }*/
                                echo "</td>";
                            echo "</tr>";
                        }
                    echo "</table>";
            }
        } else {
            echo "<td class='td' width='25px'>";
                echo "<font color=red><b>".$i."</b></font>";
            echo "</td>";
            echo "<td class='td'>";
                echo $linha[3];
            echo "</td>";
            echo "<td class='td'>";
                echo $linha[1];
            echo "</td>";
            echo "<td class='td'>";
                echo $linha[2];
            echo "</td>";
            echo "<td class='td'>";
                if($i == 1)
                {
                    echo "<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/agendar_on.jpg' onclick='agendarSemMedico($linha[lie_codigo], $_GET[esp_codigo], $_GET[uni_codigo]);return false;'>";
                } else {
                    echo "&nbsp;";
                }
            echo "</td>";
        }
        echo "</tr>";
    }
    
    echo "</table>";
    
?>
