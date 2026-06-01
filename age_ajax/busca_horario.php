<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

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
                and b.gra_data  >= current_date
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
                order by b.gra_data, b.gra_hora_ini";



$resultado=pg_query($sql);


$resultado=pg_query($sql); 
$linhas=pg_num_rows($resultado);

if($linhas>0){

    echo "<select name='horario' class='box' onchange=\"atualiza_horario(this.value,'$data')\">";
         echo "<option value='#' selected>...</option>";
    while($pegar=pg_fetch_array($resultado))
         echo "<option value='$pegar[gra_hora_ini]'>$pegar[gra_hora_ini]</option>";

   echo "</select>";
}
else
{
    echo "<strong>[...]</strong>";
}
?> 
