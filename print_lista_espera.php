<?
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
?>
<head><title>Lista de Espera</title></head>
        <style type='text/css'>
        .quebra_pagina{
        page-break-before:always;
        }
        tr{
        font-size:12px;
        }
        </style>
<link href="estilo.css" rel="stylesheet" type="text/css">
<script language="JavaScript">
    window.print();
</script>
<?
echo "<table>
                    <tr>
                        <td><big><b>AUTARQUIA MUNICIPAL DE SA&Uacute;DE<br>
                            GEST&Atilde;O P&Uacute;BLICA DE SA&Uacute;DE<br>
                            CADASTRO DE LISTA DE ESPERA</b></big>
                        <td valign='bottom'><big><b>DATA ".date('d/m/Y')."<br>HORA ".date('H:i')."</b></big>
                    </tr>
                 </table>";
                 
            echo "<table cellpading=\"0\" cellspacing=\"0\">";
if ($tipo_busca == 'todos') {                 
    $sql = "SELECT e.lie_codigo, e.lie_data_cad, a.usu_codigo, a.usu_nome, b.uni_codigo,
            b.uni_desc, c.esp_codigo, c.esp_nome, d.med_codigo, d.med_nome, agt_codigo
            FROM usuario a, unidade b, especialidade c,
            medico d, lista_espera e
            WHERE a.usu_codigo = e.usu_codigo
            AND b.uni_codigo = e.uni_codigo
            AND c.esp_codigo = e.esp_codigo
            AND d.med_codigo = e.med_codigo
            AND (e.lie_status <> 'D' OR e.lie_status IS NULL) AND e.lie_data_age IS NULL
            ORDER BY b.uni_desc, c.esp_nome, d.med_nome, e.lie_data_cad, a.usu_nome";
    $query = db_query($sql);
    $resultado = pg_num_rows($query);
        if ($resultado != 0) {
            $auxUni = "";
            $auxEsp = "";
            $auxMed = "";
            $i = 0;
            $n = 0;
            $x = 0;
            while($row = pg_fetch_array($query)) {
                if($row[uni_desc] != $auxUni)
                        {
                            if($i != 0)
                            {
                                echo "<tr>";
                                    echo "<td>";
                                        echo "&nbsp;";
                                    echo "</td>";
                                    echo "<td>";
                                        echo "&nbsp;";
                                    echo "</td>";
                                    echo "<th colspan=\"7\" align=\"left\">";
                                        echo $i." Registros";
                                    echo "</th>";
                                echo "</tr>";
                            }
                            if($x != 0)
                            {
                                echo "<tr>";
                                    echo "<td>";
                                        echo "&nbsp;";
                                    echo "</td>";
                                    echo "<th colspan=\"8\" align=\"left\">";
                                        echo $x." Registros";
                                    echo "</th>";
                                echo "</tr>";
                            }
                            if($n != 0)
                            {
                                echo "<tr>";
                                    echo "<th colspan=\"9\" align=\"left\">";
                                        echo "Total de registros: ".$n;
                                    echo "</th>";
                                echo "</tr>";
                            }
                            echo "<tr>";
                                echo "<th class=\"td\" colspan=\"9\" align=\"left\">";
                                    echo $row[uni_desc];
                                echo "</th>";
                            echo "</tr>";
                            $auxUni = "";
                            $auxEsp = "";
                            $auxMed = "";
                            $i = 0;
                            $n = 0;
                            $x = 0;
                        }
                        if($row[esp_nome] != $auxEsp)
                        {
                            if($i != 0)
                            {
                                echo "<tr>";
                                    echo "<td>";
                                        echo "&nbsp;";
                                    echo "</td>";
                                    echo "<td>";
                                        echo "&nbsp;";
                                    echo "</td>";
                                    echo "<th colspan=\"7\" align=\"left\">";
                                        echo $i." Registros";
                                    echo "</th>";
                                echo "</tr>";
                            }
                            if($x != 0)
                            {
                                echo "<tr>";
                                    echo "<td>";
                                        echo "&nbsp;";
                                    echo "</td>";
                                    echo "<th colspan=\"8\" align=\"left\">";
                                        echo $x." Registros";
                                    echo "</th>";
                                echo "</tr>";
                            }
                            echo "<tr>";
                                echo "<td>";
                                    echo "&nbsp;";
                                echo "</td>";
                                echo "<td class=\"td\" colspan=\"8\">";
                                    echo $row[esp_nome];
                                echo "</td>";
                            echo "</tr>";
                            $i = 0;
                            $x = 0;
                        }
                        if($row[med_nome] != $auxMed)
                        {
                            $pos = 1;
                            if($i != 0)
                            {
                                echo "<tr>";
                                    echo "<td>";
                                        echo "&nbsp;";
                                    echo "</td>";
                                    echo "<td>";
                                        echo "&nbsp;";
                                    echo "</td>";
                                    echo "<th colspan=\"7\" align=\"left\">";
                                        echo $i." Registros";
                                    echo "</th>";
                                echo "</tr>";
                            }
                            echo "<tr>";
                                echo "<td>";
                                    echo "&nbsp;";
                                echo "</td>";
                                echo "<td>";
                                    echo "&nbsp;";
                                echo "</td>";
                                echo "<td class=\"td\" colspan=\"7\">";
                                    echo $row[med_nome];
                                echo "</td>";
                            echo "</tr>";
                            $i = 0;
                        } else {
                            $pos++;
                        }
                        echo "<tr>";
                            echo "<td>";
                                echo "&nbsp;";
                            echo "</td>";
                            echo "<td>";
                                echo "&nbsp;";
                            echo "</td>";
                            echo "<td>";
                                echo "&nbsp;";
                            echo "</td>";
                            echo "<td>";
                                echo "<font color='black'>".$pos."</a>";
                            echo "</td>";
                            echo "<td class=\"td\" width=\"135px\">";
                                $data = explode(" ", $row[lie_data_cad]);
                                list($dia,$mes,$ano,$hr) = split ('[-/ ]',$row[lie_data_cad]);                                
                                /*
                                $dat = explode("-", $data[0]);
                                $da = $dat[2]."/".$dat[1]."/".$dat[0];
                                */
                                $da = sprintf("%02d/%02d/%04d",$dia,$mes,$ano);
                                echo $da." - ".substr($hr, 0, 8);                                
                                //echo $da." - ".substr($data[1], 0, 8);
                            echo "</td>";
                            echo "<td class=\"td\"> $row[lie_codigo]&#176; - ";
                                echo $row[usu_nome];
                            echo "</td>";
                            echo "<td class=\"td\" width=\"180px\">";
                                    if( !empty($row[agt_codigo]) )
                                    {
                                        $row_resp = db_get("SELECT agt_descricao FROM agente
                                                            WHERE agt_codigo = ".$row[agt_codigo]);
                                        echo $row_resp;
                                    }
                                    else
                                    {
                                        echo "&nbsp;";
                                    }
                                echo "</td>";
                        echo "</tr>";
                        $auxUni = $row[uni_desc];
                        $auxEsp = $row[esp_nome];
                        $auxMed = $row[med_nome];
                        $i++;
                        $n++;
                        $x++;
                    }
                    if($n != 0)
                    {
                        echo "<tr>";
                            echo "<th colspan=\"5\" align=\"left\">";
                                echo "Total de registros: ".$n;
                            echo "</th>";
                        echo "</tr>";
                    }
                    echo "</table>";
        } else {
            echo "<center>Nenhum paciente na lista de espera</center>";
        }
} else {
    if($tipo_busca == "unidade") {
            $sql = "(select e.lie_codigo, e.lie_data_cad, a.usu_codigo, a.usu_nome, b.uni_codigo, b.uni_desc,
            c.esp_codigo, c.esp_nome, d.med_codigo, d.med_nome, e.agt_codigo
            from 
            lista_espera e
            
            inner join usuario AS a ON a.usu_codigo = e.usu_codigo
            inner join unidade AS b ON b.uni_codigo = e.uni_codigo
            inner join especialidade AS c ON c.esp_codigo = e.esp_codigo
            inner join medico AS d ON d.med_codigo = e.med_codigo
            
            where 
            b.uni_desc like upper('%$palavra_chave%')
            and (e.lie_status <> 'D' or e.lie_status is null)
            and e.lie_data_age is null
            order by b.uni_desc, c.esp_nome, d.med_nome, e.lie_data_cad, a.usu_nome)
            
            union
            
            (select e.lie_codigo, e.lie_data_cad, a.usu_codigo, a.usu_nome, b.uni_codigo, b.uni_desc, c.esp_codigo,
            c.esp_nome, 0, 'SEM M&Eacute;DICO', e.agt_codigo
            from 
            lista_espera e
            
            inner join usuario AS a ON a.usu_codigo = e.usu_codigo
            inner join unidade AS b ON b.uni_codigo = e.uni_codigo
            inner join especialidade AS c ON c.esp_codigo = e.esp_codigo
            
            where 
            e.med_codigo is null 
            and b.uni_desc like upper('%$palavra_chave%')
            and (e.lie_status <> 'D' or e.lie_status is null)
            and e.lie_data_age is null
            order by b.uni_desc, c.esp_nome, e.lie_data_cad, a.usu_nome)
            
            order by 6, 8, 10, 2, 4";
        }
        else if($tipo_busca == "especialidade")
        {
            $sql = "(select e.lie_codigo, e.lie_data_cad, a.usu_codigo, a.usu_nome, b.uni_codigo, b.uni_desc,
            c.esp_codigo, c.esp_nome, d.med_codigo, d.med_nome, e.agt_codigo
            from 
            lista_espera e
            
            inner join usuario AS a ON a.usu_codigo = e.usu_codigo
            inner join unidade AS b ON b.uni_codigo = e.uni_codigo
            inner join especialidade AS c ON c.esp_codigo = e.esp_codigo
            inner join medico AS d ON d.med_codigo = e.med_codigo
            
            where 
            c.esp_nome like upper('%$palavra_chave%')
            and (e.lie_status <> 'D' or e.lie_status is null)
            and e.lie_data_age is null
            order by b.uni_desc, c.esp_nome, d.med_nome, e.lie_data_cad, a.usu_nome)
            
            union
            
            (select e.lie_codigo, e.lie_data_cad, a.usu_codigo, a.usu_nome, b.uni_codigo, b.uni_desc, c.esp_codigo,
            c.esp_nome, 0, 'SEM M&Eacute;DICO', e.agt_codigo
            from 
            lista_espera e
            
            inner join usuario AS a ON a.usu_codigo = e.usu_codigo
            inner join unidade AS b ON b.uni_codigo = e.uni_codigo
            inner join especialidade AS c ON c.esp_codigo = e.esp_codigo
            
            where 
            e.med_codigo is null 
            and c.esp_nome like upper('%$palavra_chave%')
            and (e.lie_status <> 'D' or e.lie_status is null)
            and e.lie_data_age is null
            order by b.uni_desc, c.esp_nome, e.lie_data_cad, a.usu_nome)
            
            order by 6, 8, 10, 2, 4";
        }
        else if($tipo_busca == "medico")
        {
            $sql = "select e.lie_codigo, e.lie_data_cad, a.usu_codigo, a.usu_nome, b.uni_codigo, b.uni_desc,
            c.esp_codigo, c.esp_nome, d.med_codigo, d.med_nome, e.agt_codigo
            from 
            lista_espera e
            
            inner join usuario AS a ON a.usu_codigo = e.usu_codigo
            inner join unidade AS b ON b.uni_codigo = e.uni_codigo
            inner join especialidade AS c ON c.esp_codigo = e.esp_codigo
            inner join medico AS d ON d.med_codigo = e.med_codigo
            
            where 
            d.med_nome like upper('%$palavra_chave%')
            and (e.lie_status <> 'D' or e.lie_status is null)
            and e.lie_data_age is null
            order by b.uni_desc, c.esp_nome, d.med_nome, e.lie_data_cad, a.usu_nome";
            
        }
        else if($tipo_busca == "paciente")
        {
            $sql = "(select e.lie_codigo, e.lie_data_cad, a.usu_codigo, a.usu_nome, b.uni_codigo, b.uni_desc,
            c.esp_codigo, c.esp_nome, d.med_codigo, d.med_nome, e.agt_codigo
            from 
            lista_espera e
            
            inner join usuario AS a ON a.usu_codigo = e.usu_codigo
            inner join unidade AS b ON b.uni_codigo = e.uni_codigo
            inner join especialidade AS c ON c.esp_codigo = e.esp_codigo
            inner join medico AS d ON d.med_codigo = e.med_codigo
            
            where 
            a.usu_nome like upper('%$palavra_chave%')
            and (e.lie_status <> 'D' or e.lie_status is null)
            and e.lie_data_age is null
            order by b.uni_desc, c.esp_nome, d.med_nome, e.lie_data_cad, a.usu_nome)
            
            union
            
            (select e.lie_codigo, e.lie_data_cad, a.usu_codigo, a.usu_nome, b.uni_codigo, b.uni_desc, c.esp_codigo,
            c.esp_nome, 0, 'SEM M&Eacute;DICO', e.agt_codigo
            from 
            lista_espera e
            
            inner join usuario AS a ON a.usu_codigo = e.usu_codigo
            inner join unidade AS b ON b.uni_codigo = e.uni_codigo
            inner join especialidade AS c ON c.esp_codigo = e.esp_codigo
            
            where 
            e.med_codigo is null 
            and a.usu_nome like upper('%$palavra_chave%')
            and (e.lie_status <> 'D' or e.lie_status is null)
            and e.lie_data_age is null
            order by b.uni_desc, c.esp_nome, e.lie_data_cad, a.usu_nome)
            
            order by 6, 8, 10, 2, 4";
        }
                $exec_sql = db_query($sql);
//                echo "<legend>Listando ".pg_num_rows($exec_sql)." registros</legend>";
                $auxUni = "";
                $auxEsp = "";
                $auxMed = "";
                $i = 0;
                $n = 0;
                $x = 0;
                echo "<table cellpading=\"0\" cellspacing=\"0\">";
                while($row = pg_fetch_array($exec_sql))
                {
                    // Implementado para buscar a posicao na lista do médico  {Marcos Ramos}                   
                    $contador = 0;
                    $posicao = 0;
                    $sub = "select usu_codigo
                                from lista_espera
                                where uni_codigo = $row[uni_codigo]
                                and esp_codigo = $row[esp_codigo]
                                and med_codigo = $row[med_codigo]
                                and (lie_status <> 'D' or lie_status is null)
                                and lie_data_age is null
                                order by lie_codigo";
                    $st = db_query($sub);
                    while ($reg = pg_fetch_array($st))                    
                    {
                        $contador+=1;
                        if ($reg[usu_codigo] == $row[usu_codigo])
                        {
                            $posicao = $contador;
                        }                      
                    }
                    // fim Implementacao para buscar a posicao na lista do medico (Marcos Ramos}                                        
                    if($row[uni_desc] != $auxUni)
                    {
                        if($i != 0)
                        {
                            echo "<tr>";
                                echo "<td>";
                                    echo "&nbsp;";
                                echo "</td>";
                                echo "<td>";
                                    echo "&nbsp;";
                                echo "</td>";
                                echo "<th colspan=\"5\" align=\"left\">";
                                    echo $i." Registros";
                                echo "</th>";
                            echo "</tr>";
                        }
                        if($x != 0)
                        {
                            echo "<tr>";
                                echo "<td>";
                                    echo "&nbsp;";
                                echo "</td>";
                                echo "<th colspan=\"6\" align=\"left\">";
                                    echo $x." Registros";
                                echo "</th>";
                            echo "</tr>";
                        }
                        if($n != 0)
                        {
                            echo "<tr>";
                                echo "<th colspan=\"7\" align=\"left\">";
                                    echo "Total de registros: ".$n;
                                echo "</th>";
                            echo "</tr>";
                        }
                        echo "<tr>";
                            echo "<th class=\"td\" colspan=\"7\" align=\"left\">";
                                echo $row[uni_desc];
                            echo "</th>";
                        echo "</tr>";
                        $auxUni = "";
                        $auxEsp = "";
                        $auxMed = "";
                        $i = 0;
                        $n = 0;
                        $x = 0;
                    }
                    if($row[esp_nome] != $auxEsp)
                    {
                        if($i != 0)
                        {
                            echo "<tr>";
                                echo "<td>";
                                    echo "&nbsp;";
                                echo "</td>";
                                echo "<td>";
                                    echo "&nbsp;";
                                echo "</td>";
                                echo "<th colspan=\"5\" align=\"left\">";
                                    echo $i." Registros";
                                echo "</th>";
                            echo "</tr>";
                        }
                        if($x != 0)
                        {
                            echo "<tr>";
                                echo "<td>";
                                    echo "&nbsp;";
                                echo "</td>";
                                echo "<th colspan=\"6\" align=\"left\">";
                                    echo $x." Registros";
                                echo "</th>";
                            echo "</tr>";
                        }
                        echo "<tr>";
                            echo "<td>";
                                echo "&nbsp;";
                            echo "</td>";
                            echo "<td class=\"td\" colspan=\"6\">";
                                echo $row[esp_nome];
                            echo "</td>";
                        echo "</tr>";
                        $i = 0;
                        $x = 0;
                    }
                    if($row[med_nome] != $auxMed)
                    {
                        $pos = 1;
                        if($i != 0)
                        {
                            echo "<tr>";
                                echo "<td>";
                                    echo "&nbsp;";
                                echo "</td>";
                                echo "<td>";
                                    echo "&nbsp;";
                                echo "</td>";
                                echo "<th colspan=\"5\" align=\"left\">";
                                    echo $i." Registros";
                                echo "</th>";
                            echo "</tr>";
                        }
                        echo "<tr>";
                            echo "<td>";
                                echo "&nbsp;";
                            echo "</td>";
                            echo "<td>";
                                echo "&nbsp;";
                            echo "</td>";
                            echo "<td class=\"td\" colspan=\"5\">";
                                echo $row[med_nome];
                            echo "</td>";
                        echo "</tr>";
                        $i = 0;
                    } else {
                        $pos++;
                    }
                    echo "<tr>";
                        echo "<td>";
                            echo "&nbsp;";
                        echo "</td>";
                        echo "<td>";
                            echo "&nbsp;";
                        echo "</td>";
                        echo "<td>";
                            echo "&nbsp;";
                        echo "</td>";
                        echo "<td>";
//                            echo "<font color='red'>".$pos."</a>";
                            echo "<font color='red'>".$posicao."&ordm;</a>";                            
                        echo "</td>";
                        echo "<td class=\"td\" width=\"135px\">";
                            $data = explode(" ", $row[lie_data_cad]);
                            list($dia,$mes,$ano,$hr) = split ('[-/ ]',$row[lie_data_cad]);                                
                            /*
                            $dat = explode("-", $data[0]);
                            $da = $dat[2]."/".$dat[1]."/".$dat[0];
                            */
                            $da = sprintf("%02d/%02d/%04d",$dia,$mes,$ano);
                            echo $da." - ".substr($hr, 0, 8);
                            /*
                            $dat = explode("-", $data[0]);
                            $da = $dat[2]."/".$dat[1]."/".$dat[0];
                            echo $da." - ".substr($data[1], 0, 8);
                           */
                        echo "</td>";
//                        echo "<td class=\"td\">$row[lie_codigo]&#176; - ";
                        echo "<td class=\"td\">$row[usu_nome]</td>";
                        echo "<td class=\"td\" width=\"180px\">";
                                if( !empty($row[agt_codigo]) )
                                {
                                    $row_resp = db_get("SELECT agt_descricao FROM agente
                                                        WHERE agt_codigo = ".$row[agt_codigo]);
                                    echo $row_resp;
                                }
                                else
                                {
                                    echo "&nbsp;";
                                }
                            echo "</td>";
                        echo "<td class='td' width=\"60px\">&nbsp;";
                        echo "</td>";
                        echo "<td class='td' width=\"60px\">&nbsp;";
                        echo "</td>";
                    echo "</tr>";
                    $auxUni = $row[uni_desc];
                    $auxEsp = $row[esp_nome];
                    $auxMed = $row[med_nome];
                    $i++;
                    $n++;
                    $x++;
                }
                if($i != 0)
                {
                    echo "<tr>";
                        echo "<td>";
                            echo "&nbsp;";
                        echo "</td>";
                        echo "<td>";
                            echo "&nbsp;";
                        echo "</td>";
                        echo "<th colspan=\"5\" align=\"left\">";
                            echo $i." Registros";
                        echo "</th>";
                    echo "</tr>";
                }
                if($x != 0)
                {
                    echo "<tr>";
                        echo "<td>";
                            echo "&nbsp;";
                        echo "</td>";
                        echo "<th colspan=\"6\" align=\"left\">";
                            echo $x." Registros";
                        echo "</th>";
                    echo "</tr>";
                }
                if($n != 0)
                {
                    echo "<tr>";
                        echo "<th colspan=\"5\" align=\"left\">";
                            echo "Total de registros: ".$n;
                        echo "</th>";
                    echo "</tr>";
                }
                echo "<tr>";
                    echo "<td colspan=\"7\"><b>";
                        echo "Total geral: ".pg_num_rows($exec_sql);
                    echo "</b></td>";
                echo "</tr>";
            echo "</table>";
}
?>
