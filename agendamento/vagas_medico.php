<?php
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>

    include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
    verauth($id_login);

   	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
    cabecario();

    reglog($id_login, "Entrando em Verificar Vagas do Médico $med_codigo");
    
?>

    <script>
        function hotkey(eventname)
        {
            if(eventname.keyCode == 113)
            {
               window.close()
            }
        }
    </script>
    
    <style>
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
            border-color: #E3E3E3;
        }
    </style>
    
    <title>Vagas para o Médico</title>
    <body topmargin="0" leftmargin="0" onkeydown="hotkey(event);">
    
<?php

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
			(select qtde
			from view_qtde_medico as c
			where c.med_codigo = '$med_codigo'
			and c.uni_codigo = '$uni_codigo'
			and c.esp_codigo = '$esp_codigo'
			and c.age_data = b.gra_data
			and c.age_tipo = '$age_item'
			and c.age_item = '$age_tipo'
			and c.age_hora = b.gra_hora_ini limit 1),0)) > 0
		order by b.gra_data, b.gra_hora_ini";
    
    $exec_sql = pg_query($sql);
    echo "
        <table width='100%' cellspacing='1' cellpadding='4' border='0'>
            <tr bgcolor='#d9d9d9'>
                <td class='borda'>Data</td>
                <td class='borda'>Hora</td>
                <td class='borda'>Vagas</td>
            </tr>";
            while($row = pg_fetch_array($exec_sql))
            {
                if($row["calc_qtde"] <= 2)
                {
                    $qtd = "<font color='red' size='2'>$row[calc_qtde]</font>";
                } else {
                    $qtd = "<font size='2'>$row[calc_qtde]</font>";
                }
                $bgcolor = ($bgcolor == "#EDECEC") ? "#FFFFFF" : "#EDECEC";
                if($row[qtdegeral]!="0")
                {
                    echo "
                        <tr bgcolor='$bgcolor'>
                            <td width='10%' class='borda2'>$row[gra_data]</td>
                            <td width='4%' align='center' class='borda2'>$row[gra_hora_ini]</td>
                            <td align='center' class='borda2'>$qtd</td>
                        </tr>";
                 }
            }
    echo "</table>";

?>
