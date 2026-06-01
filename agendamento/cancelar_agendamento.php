<?php
	session_start();
    //------------------------------------------------------------------>
    //------------------------------------------------------------------>
    include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	verauth($id_login);
    
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
    //------------------------------------------------------------------>s
?>
<script type="text/javascript" language="javascript">
    function verificar()
    {
        age_observacao = document.getElementById("age_observacao");
        
        if(age_observacao.value == "")
        {
            alert("Por favor preencha o motivo do cancelamento!");
            age_observacao.focus();
            return false;
        }
        //alert(age_observacao.value.length);
        if(age_observacao.value.length < 70)
        {
            alert("Tamanho do texto nao atinge o minimo de caracteres! \n Por Favor preencha com o minimo necessario.");
            age_observacao.focus();
            return false;
        }
        return true;
    }
</script>
<style>
    .tr
    {
            border-bottom:1px solid;
            border-right:1px solid;
            border-color:c9c9c9;
            background:white;
    }
    .td
    {
            border-bottom:1px dotted;
            border-right:1px dotted;
            border-color:c9c9c9;
    }
</style>
<fieldset><legend>CANCELAMENTO DE CONSULTAS</legend>
<?php
    if($acao == "listar" || !$acao)
    {
        echo "<fieldset>";
            echo "<legend>Op&ccedil;&otilde;es</legend>";
            echo "<table>";
                echo "<tr>";
                    echo "<form method=get action=$PHP_SELF>";
                        echo "<td width='75px'>";
                            echo "<a href='#?id_login=$id_login&acao=listar'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>";
                        echo "</td>";
                        echo "<td>";
                            echo "<input type='hidden' name='acao' value='buscar'>";
                            echo "<input type='hidden' name='id_login' value='$id_login'>";
                        echo "<td width='180' align='right'>Buscar</td>";
                        echo "<td width='90'>";
                            echo "<input type='text' name='palavra_chave' class='box'>";
                        echo "</td>";
                        echo "<td>";
                            echo "<select name=\"tipo_busca\" class='box'>";
                                echo "<option value=\"paciente\">Paciente</option>";
                                echo "<option value=\"medico\">M&eacute;dico</option>";
                            echo "</select>";
                        echo "</td>";
                        echo "<td>";
                            $select = "select uni_codigo, uni_desc from unidade order by uni_desc";
                            $exec_select = pg_query($select);
                            echo "<select name='uni_codigo' class='box'>";
                            while($row = pg_fetch_array($exec_select))
                            {
                                echo "<option value='$row[0]'>$row[1]</option>";
                            }
                            echo "</select>";
                        echo "</td>";
                        echo "<td>";
                            echo "<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg'>";
                        echo "</td>";
                    echo "</form>";
                echo "</tr>";
            echo "</table>";
        echo "</fieldset>";
        echo "<fieldset>";
            $select = "select a.age_codigo, a.age_data, to_char(a.age_data, 'dd/mm/yyyy') as age_data_correta, a.age_hora, b.usu_nome, c.med_nome, d.uni_desc
            from agendamento a, usuario b, medico c, unidade d
            where a.usu_codigo = b.usu_codigo
            and a.med_codigo = c.med_codigo
            and a.uni_codigo = d.uni_codigo
            and a.age_data >= current_date
            order by a.age_data
            limit 15";
            $exec_select = pg_query($select);
            echo "<legend>Listando &uacute;ltimos 15 registros</legend>";
            echo "<table>";
                echo "<tr class='tr'>";
                    echo "<th class='tr' width='120px'>";
                        echo "Data/Hora";
                    echo "</th>";
                    echo "<th class='tr' width='280px'>";
                        echo "Paciente";
                    echo "</th>";
                    echo "<th class='tr' width='280px'>";
                        echo "M&eacute;dico";
                    echo "</th>";
                    echo "<th class='tr' width='280px'>";
                        echo "Unidade";
                    echo "</th>";
                    echo "<th class='tr' width='50px'>";
                        echo "&nbsp;";
                    echo "</th>";
                echo "</tr>";
                while($linha = pg_fetch_array($exec_select))
                {
                    echo "<tr>";
                        echo "<td class='td'>";
                            echo $linha[age_data_correta]." - ".$linha[age_hora];
                        echo "</td>";
                        echo "<td class='td'>";
                            echo $linha[usu_nome];
                        echo "</td>";
                        echo "<td class='td'>";
                            echo $linha[med_nome];
                        echo "</td>";
                        echo "<td class='td'>";
                            echo $linha[uni_desc];
                        echo "</td>";
                        echo "<td class='td'>";
                            //echo ChmodBtn($id_login,'apagar','cancelar_agendamento.php?acao=form_del&age_codigo='.$linha[age_codigo]);
                            echo "<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg' onclick=\"location.href='cancelar_agendamento.php?acao=form_del&age_codigo=$linha[age_codigo]&id_login=$id_login'\">";
                        echo "</td>";
                    echo "</tr>";
                }
            echo "</table>";
        echo "</fieldset>";
    } else if($acao == "buscar") {
        echo "<fieldset>";
            echo "<legend>Op&ccedil;&otilde;es</legend>";
            echo "<table>";
                echo "<tr>";
                    echo "<form method=get action=$PHP_SELF>";
                        echo "<td width='75px'>";
                            echo "<a href='#?id_login=$id_login&acao=listar'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>";
                        echo "</td>";
                        echo "<td>";
                            echo "<input type='hidden' name='acao' value='buscar'>";
                            echo "<input type='hidden' name='id_login' value='$id_login'>";
                        echo "<td width='180' align='right'>Buscar</td>";
                        echo "<td width='90'>";
                            echo "<input type='text' name='palavra_chave' class='box'>";
                        echo "</td>";
                        echo "<td>";
                            echo "<select name=\"tipo_busca\" class='box'>";
                                echo "<option value=\"paciente\" ".($tipo_busca == "paciente" ? "selected" : "").">Paciente</option>";
                                echo "<option value=\"medico\" ".($tipo_busca == "medico" ? "selected" : "").">M&eacute;dico</option>";
                            echo "</select>";
                        echo "</td>";
                        echo "<td>";
                            $select = "select uni_codigo, uni_desc from unidade order by uni_desc";
                            $exec_select = pg_query($select);
                            echo "<select name='uni_codigo' class='box'>";
                            while($row = pg_fetch_array($exec_select))
                            {
                                if($row[0] == $uni_codigo)
                                {
                                    echo "<option value='$row[0]' selected>$row[1]</option>";
                                } else {
                                    echo "<option value='$row[0]'>$row[1]</option>";
                                }
                            }
                            echo "</select>";
                        echo "</td>";
                        echo "<td>";
                            echo "<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg'>";
                        echo "</td>";
                    echo "</form>";
                echo "</tr>";
            echo "</table>";
        echo "</fieldset>";
        echo "<fieldset>";
            $palavra_chave = strtoupper($palavra_chave);
            if($tipo_busca == "paciente")
            {
                $andSelect = " and usu_nome ilike upper('$palavra_chave%') ";
            } else if($tipo_busca == "medico") {
                $andSelect = " and med_nome ilike upper('$palavra_chave%') ";
            }
            $select = "select a.age_codigo, a.age_data, to_char(a.age_data, 'dd/mm/yyyy') as age_data_correta, a.age_hora, b.usu_nome, c.med_nome, d.uni_desc
            from agendamento a, usuario b, medico c, unidade d
            where a.usu_codigo = b.usu_codigo
            and a.med_codigo = c.med_codigo
            and a.uni_codigo = d.uni_codigo
            and a.age_data >= current_date
            $andSelect
            and a.uni_codigo = {$uni_codigo}
            order by a.age_data";
            $exec_select = pg_query($select);
            echo "<legend>Listando ".pg_num_rows($exec_select)." registros</legend>";
            echo "<table>";
                echo "<tr class='tr'>";
                    echo "<th class='tr' width='120px'>";
                        echo "Data/Hora";
                    echo "</th>";
                    echo "<th class='tr' width='280px'>";
                        echo "Paciente";
                    echo "</th>";
                    echo "<th class='tr' width='280px'>";
                        echo "M&eacute;dico";
                    echo "</th>";
                    echo "<th class='tr' width='280px'>";
                        echo "Unidade";
                    echo "</th>";
                    echo "<th class='tr' width='50px'>";
                        echo "&nbsp;";
                    echo "</th>";
                echo "</tr>";
                while($linha = pg_fetch_array($exec_select))
                {
                    echo "<tr>";
                        echo "<td class='td'>";
                            echo $linha[age_data_correta]." - ".$linha[age_hora];
                        echo "</td>";
                        echo "<td class='td'>";
                            echo $linha[usu_nome];
                        echo "</td>";
                        echo "<td class='td'>";
                            echo $linha[med_nome];
                        echo "</td>";
                        echo "<td class='td'>";
                            echo $linha[uni_desc];
                        echo "</td>";
                        echo "<td class='td'>";
                            //echo ChmodBtn($id_login,'apagar','cancelar_agendamento.php?acao=form_del&age_codigo='.$linha[age_codigo]);
                            echo "<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg' onclick=\"location.href='cancelar_agendamento.php?acao=form_del&age_codigo=$linha[age_codigo]&id_login=$id_login'\">";
                        echo "</td>";
                    echo "</tr>";
                }
            echo "</table>";
        echo "</fieldset>";
    } else if($acao == "form_del") {
        echo "<fieldset>";
            echo "<legend>Op&ccedil;&otilde;es</legend>";
            echo "<table>";
                echo "<tr>";                
                    echo "<td width='75px'>";
                        echo "<a href='$PHP_SELF?id_login=$id_login&acao=listar'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>";
                    echo "</td>";
                echo "</tr>";
            echo "</table>";
        echo "</fieldset>";
        echo "<fieldset>";
            echo "<legend>Op&ccedil;&otilde;es de cancelamento</legend>";
            echo "<form method='post' action=$PHP_SELF onsubmit='return verificar()'>";
                echo "<table>";
                    echo "<tr>";
                        echo "<td valign=top width='150px'>";
                            echo "<input type=hidden name=acao value=del>";
                            echo "<input type=hidden name=id_login value=$id_login>";
                            echo "<input type=hidden name=age_codigo value=$age_codigo>";
                            echo "Motivo do Cancelamento:";
                        echo "</td>";
                        echo "<td>";
                            echo "<textarea cols=50 rows=5 name='age_observacao' id='age_observacao' class='box'></textarea>";
                        echo "</td>";
                    echo "</tr>";
                    echo "<tr>";
                        echo "<td colspan=2>";
                            echo "<font color=red>*M&iacute;nimo de <b>70</b> Caracteres</font>";
                        echo "</td>";
                    echo "</tr>";
                    echo "<tr>";
                        echo "<td>";
                            echo "<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg'>";
                        echo "</td>";
                    echo "</tr>";
                echo "</table>";
            echo "</form>";
        echo "</fieldset>";
    } else if($acao == "del") {
        pg_query("begin");
        $select = "select * from agendamento where age_codigo = $age_codigo";
        $exec_select = pg_query($select);
        $row = pg_fetch_array($exec_select);
        $insert = "insert into agendamento_excluido values (";
        for($i = 0; $i < pg_num_fields($exec_select); $i++)
        {
            if(pg_field_name($exec_select, $i) == "age_emergencia")
            {
                $insert_aux .= (empty($row[$i]) ? "null" : "'".$row[$i]."'");
            } else {
                /*if($i != pg_num_fields($exec_select))
                {*/
                    $insert .= (empty($row[$i]) ? "null" : "'".$row[$i]."'").",";
                /*} else {
                    $insert .= (empty($row[$i]) ? "null" : "'".$row[$i]."'");
                }*/
            }
        }
        $insert .= "'$id_login', current_timestamp, '$age_observacao',";
        $insert .= $insert_aux.")";
        //echo $insert;
        $exec_insert = pg_query($insert);
        $delete = "delete from agendamento where age_codigo = $age_codigo";
        //echo "<br>".$delete;
        $exec_delete = pg_query($delete);
        pg_query("commit");
        echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
        <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
         <tr bgcolor=f9f9f9><td align=center>";
        if(pg_affected_rows($exec_insert) > 0 && pg_affected_rows($exec_delete) > 0)
        {
            echo "<font size=2 color=green><b>REGISTRO APAGADO COM SUCESSO</b></font>";
        } else {
            echo "<font size=2 color=red><b>ERRO AO APAGAR REGISTRO</b></font>";
        }
        echo "</td></tr>
        </table><br>";
        /*echo "<pre>";
            echo $insert;
            echo "<br>";
            echo $delete;
        echo "</pre>";
        echo pg_last_error($db);
        exit;*/
        //echo pg_last_error($db);   
        echo "<script>setInterval(\"location.href='cancelar_agendamento.php?acao=listar&id_login=$id_login'\",4000)</script>";
    }
?>
</fieldset>