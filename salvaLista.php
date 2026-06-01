<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
    
    pg_query("begin");
    
    $sql = "insert into agendamento
            (age_data, med_codigo, age_hora, usu_codigo, age_tipo, uni_codigo, age_item, esp_codigo, agt_codigo,
            usr_codigo_cad, dt_cadastro)
            values
            ('$_GET[age_data]', '$_GET[med_codigo]', '$_GET[age_hora_ini]', '$_GET[usu_codigo]', 'ES', '$_GET[uni_codigo]', '$_GET[age_item]', '$_GET[esp_codigo]', '$_GET[agt_codigo]', '$_GET[id_login]', NOW())";
            
    $exec_sql = pg_query($sql);
    
    $update = "update lista_espera set lie_data_age = now() where lie_codigo = {$_GET[lie_codigo]}";
    
    $exec_update = pg_query($update);
    
    $select = "select age_codigo
                from agendamento
                where age_data = '$_GET[age_data]'
                and med_codigo = '$_GET[med_codigo]'
                and age_hora = '$_GET[age_hora_ini]'
                and usu_codigo = '$_GET[usu_codigo]'
                and uni_codigo = '$_GET[uni_codigo]'
                and agt_codigo = '$_GET[agt_codigo]'
                and age_item = '$_GET[age_item]'
                and dt_cadastro = now()";
                
    $exec_select = pg_query($select);
    
    $row = pg_fetch_array($exec_select);
    
    $age_codigo = $row[0];
    
    pg_query("commit");
    
    $link = "print_guia.php?uni_codigo=$_GET[uni_codigo]&esp_codigo=$_GET[esp_codigo]&agt_codigo=$_GET[agt_codigo]&usu_codigo=$_GET[usu_codigo]&age_codigo=$age_codigo&med_codigo=$_GET[med_codigo]";
    if($exec_sql == true)
    {
        echo "Registro inserido com sucesso-$link";    
    } else {
        echo "Erro ao inserir registro-1";
    }
    
?>