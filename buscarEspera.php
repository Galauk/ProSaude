<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
    cabecario();
    if($_GET[controle] == 1)
    {
        $select = "select f.lie_codigo, a.usu_nome, b.med_nome, c.esp_nome, e.uni_desc, f.lie_data_cad
                    from usuario a, medico b, especialidade c, medico_especialidade d,
                    unidade e, lista_espera f
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
                    order by f.lie_data_cad
                    limit $_GET[limit]";
                    
        $exec_select = pg_query($select);
        
        echo "<fieldset>";
            echo "<legend>Lista de Espera</legend>";
            echo "<table>";
                echo "<tr class='tr'>";
                    echo "<td class='td' width='25px'>";
                        echo "N";
                    echo "</td>";
                    echo "<td class='tr' width='135px'>";
                        echo "Data/Hora";
                    echo "</td>";
                    echo "<td class='tr'>";
                        echo "Paciente";
                    echo "</td>";
                    echo "<td class='tr' width='50'>";
                        echo "&nbsp;";
                    echo "</td>";
                echo "</tr>";
                $i = 0;
                while($linha = pg_fetch_array($exec_select))
                {
                    $i++;
                    $data = explode(" ", $linha[lie_data_cad]);
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
                            echo $linha[usu_nome];
                        echo "</td>";
                        echo "<td class='td'>";
                            if($i == 1)
                            {
                                echo "<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/agendar_on.jpg' onclick='abrirAgendar($linha[lie_codigo]);return false;'>";
                            }/* else {
                                echo "<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/agendar_off.jpg' disabled>";
                            }*/
                        echo "</td>";
                    echo "</tr>";
                }
            echo "</table>";
        echo "</fieldset>";
    } else if($_GET[controle] == 2) {
        $select = "select sum(a.qtde) as qtde, a.med_codigo, b.med_nome,
                    (select esp_nome from especialidade where esp_codigo = '$_GET[esp_codigo]') as esp_nome,
                    (select uni_desc from unidade where uni_codigo = '$_GET[uni_codigo]') as uni_desc,
                    ( 
                    select sum(qtde)
                    from view_qtde_medico
                    where uni_codigo = '{$_GET[uni_codigo]}'
                    and esp_codigo = '$_GET[esp_codigo]'
                    and (age_tipo = 'ES' or age_tipo = 'CB')
                    and age_data >= current_date 
                    ) as qtde_agendado,
                    coalesce(sum(qtde),0) -
                    coalesce((select sum(qtde) from view_qtde_medico
                    where uni_codigo = '$_GET[uni_codigo]'
                    and esp_codigo = '{$_GET[esp_codigo]}'
                    and (age_tipo = 'ES' or age_tipo = 'CB')
                    and age_data >= current_date),0) as total
                    from view_qtde_grade a, medico b
                    where a.uni_codigo = '$_GET[uni_codigo]'
                    and a.esp_codigo = '$_GET[esp_codigo]'
                    and (a.age_item = 'ES' or a.age_item = 'CB')
                    and a.gra_data >= current_date
                    and a.med_codigo = b.med_codigo
                    group by b.med_nome, a.med_codigo";
                    
        $exec_select = pg_query($select);
        
        echo "<fieldset>";
            echo "<legend>Lista de M&eacute;dicos</legend>";
            echo "<table>";
                echo "<tr class='tr'>";
                    echo "<td class='td' width='250px'>";
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
                echo "</tr>";
                while($linha = pg_fetch_array($exec_select))
                {
                    echo "<tr>";
                        echo "<td class='td'>";
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
                            echo "<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/agendar_on.jpg' onclick='abrirAgendar2($_GET[lie_codigo], $linha[med_codigo])'>";
                        echo "</td>";
                    echo "</tr>";
                }
            echo "</table>";
        echo "</fieldset>";
    }
?>
