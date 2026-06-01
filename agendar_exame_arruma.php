<?php
/**
 * tenta agendar menos datas possíveis para os exames já agendados
 */ 
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	cabecario();
	include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";
	include_once $_SESSION[root].$_SESSION[modulo]."funcoes.agendar_exame.php";

//reglog($id_login,"Acessando Agendamento de Exames");

// TENTA ATUALIZAR TODOS OS AGENDAMENTOS !
if( ! empty($submit) )
{
     $grava = " UPDATE agendamento_exame_lista 
	               SET agexl_data='$data_nova'
		       WHERE agex_codigo = $agex_codigo 
		       and   med_codigo =  $med_codigo";   
     $gravareg = db_query($grava);
     $stmt_lab = "SELECT  usu_codigo, agex_codigo, med_codigo_responsavel, esp_codigo_responsavel, agt_codigo, agex_data_cad
                  FROM agendamento_exame 
                  WHERE agex_codigo= $agex_codigo";
     $manut_row = db_getRow($stmt_lab);
     $usu_codigo = $manut_row[0];
     $med_codigo_resp = $manut_row[2];
     $esp_codigo_resp = $manut_row[3];
     $agente = $manut_row[4];
     $data = $manut_row[5];

     reglog($id_login,"Gravando Alteracao de Data de Agendamento de Exames - nova data - $data_nova - exame - $agex_codigo");
     if ($gravareg) 
         print " 
		<script type=\"text/javascript\">
			alert(\"Dados gravados com sucesso\")
			setTimeout(\"location='agendar_exame_iframe1.php?id_login=$id_login&agex_codigo=$agex_codigo&med_codigo=$med_codigo&proc_codigo=$proc_codigo&usu_codigo=$usu_codigo&data_cad=$data&r_med_codigo=$med_codigo_resp&r_esp_codigo=$esp_codigo_resp'\", 0); window.close();
		</script>";
}
$stmt_lab = "SELECT med_tipoagendamento as proc_tipo_manut FROM medico 
             WHERE med_codigo= $med_codigo";
$manut_row = db_getRow($stmt_lab);
$proc_tipo = $manut_row[0];
        	
// axa as datas / procedimentos agendados
$stmt1 	= "SELECT al.proc_codigo, agexl_data, p.med_codigo, usu_codigo, TO_CHAR(agexl_data,'DD/MM/YYYY') as 
			dataf, agexl_codigo, med_tipoagendamento, med_nome, proc_nome
			FROM agendamento_exame_lista AS al, medico as p, procedimento as pr
			WHERE agex_codigo = $agex_codigo
			AND   al.med_codigo = p.med_codigo
			AND   al.proc_codigo = pr.proc_codigo
			AND   al.med_codigo = $med_codigo";

/*$stmt1 = "SELECT agexl_codigo, TO_CHAR(agexl_dt_cadastro,'dd/mm/yyyy') as data_cad,
				TO_CHAR(agexl_data,'dd/mm/YYYY') as data, proc_nome, p.proc_codigo, l.med_codigo,
				agexl_data, p.proc_codigo, p.gex_tipo, usu_codigo, m.med_nome, proc_tipo
			FROM agendamento_exame_lista AS l
			LEFT JOIN procedimento AS p ON p.proc_codigo = l.proc_codigo
			LEFT JOIN laboratorio_procedimento AS lp ON ( p.proc_codigo = lp.proc_codigo AND l.med_codigo = lp.med_codigo )
			LEFT JOIN medico AS m ON lp.med_codigo = m.med_codigo
			WHERE agex_codigo = $agex_codigo
            AND agexl_status = 'A'
            GROUP BY agexl_codigo, data_cad, data, proc_nome, p.proc_codigo, l.med_codigo, agexl_data, p.proc_codigo, p.gex_tipo, usu_codigo, med_nome, proc_tipo 
			ORDER BY to_char(agexl_data,'YYYY/mm/dd')";*/
			
$qry1 	= db_query($stmt1);
$conta = 1;

while( $row1 = pg_fetch_array($qry1) )
{
	if ($med_codigo == '2165') {
	   $stmt2 = 
	   "SELECT graex_data, TO_CHAR(graex_data,'DD/MM/YYYY') as dataf
	    FROM grade_exame AS ge
	    LEFT JOIN procedimento AS p ON p.proc_codigo = ge.proc_codigo
	    WHERE graex_data > CURRENT_DATE
	    AND ge.proc_codigo = '$row1[proc_codigo]'
	    AND ge.med_codigo = '$row1[med_codigo]' 
	    AND exame_dt_valida( $row1[usu_codigo], graex_data, '$row1[proc_codigo]', 
   		'$row1[med_codigo]', '$row1[med_tipoagendamento]' ) IN (0,1)
            AND exame_qtde_vagas( graex_data,'$row1[proc_codigo]','$row1[med_codigo]', 
             '$row1[med_tipoagendamento]') > 0 ";
           $usu_codigo = empty($usu_codigo) ? $row1['usu_codigo'] : $usu_codigo;
       }
	if ($med_codigo != '2165') {
	   $stmt2 = "SELECT graex_data, TO_CHAR(graex_data,'DD/MM/YYYY') as dataf
	    FROM grade_exame AS ge
	    WHERE graex_data > CURRENT_DATE
	    AND ge.med_codigo = '$row1[med_codigo]' 
	    AND exame_dt_valida( $row1[usu_codigo], graex_data, '$row1[proc_codigo]','$row1[med_codigo]', '$row1[med_tipoagendamento]' ) IN (1,0)
            AND exame_qtde_vagas( graex_data,'$row1[proc_codigo]','$row1[med_codigo]','$row1[med_tipoagendamento]') > 0";
        #   $usu_codigo = empty($usu_codigo) ? $row1['usu_codigo'] : $usu_codigo;
       }
}
$stmt2 .= " order by graex_data";
$qry2 	= db_query($stmt2);
$dt = pg_fetch_array($qry2);


echo "
<fieldset>
<legend>Exames Agendados</legend>

<p>Este procedimento tenta agrupar os exames no menor número de dias.</p>

<p>Página carregada em <strong>".date('d/m/Y \a\s H:i')."</strong></p>

<table class='lista'>

	<form action='$PHP_SELF?id_login=$id_login&agex_codigo=$agex_codigo&usu_codigo=$usu_codigo'&med_codigo=$med_codigo method='post'>
	<input type='hidden' value='True' name='submit' />
	<tr bgcolor='#ffffff'>
		<th>Procedimento</th>
		<th>Laboratório</th>
		<th width='80' class='c'>Agendado</th>
		<th width='80' class='c'>Sugestão</th>
	</tr>\n";
        print "
		<input type='hidden' name='data_nova' value='$dt[1]' />
		<input type='hidden' name='agex_codigo' value=$agex_codigo />
		<input type='hidden' name='med_codigo' value=$med_codigo />";

/*$stmt1 	= "SELECT al.proc_codigo, agexl_data, p.med_codigo, usu_codigo, TO_CHAR(agexl_data,'DD/MM/YYYY') as dataf, 
                  agexl_codigo, med_tipoagendamento, med_nome, proc_nome
           FROM agendamento_exame_lista AS al, medico as p, procedimento as pr
           WHERE agex_codigo = $agex_codigo
           AND   al.med_codigo = $med_codigo 
           AND   al.med_codigo = p.med_codigo
           AND   al.proc_codigo = pr.proc_codigo";*/

$stmt1 = "SELECT al.proc_codigo, agexl_data, p.med_codigo, usu_codigo, TO_CHAR(agexl_data,'DD/MM/YYYY') as dataf, agexl_codigo, 
	  med_tipoagendamento, med_nome, proc_nome 
	  FROM agendamento_exame_lista AS al
	  left join medico as p on al.med_codigo = p.med_codigo
	  left join procedimento as pr on pr.proc_codigo = al.proc_codigo
	  WHERE al.med_codigo = $med_codigo";


$qry1 	= db_query($stmt1);
	
while( $row1 = pg_fetch_array($qry1) )
{
	echo "
	<tr>
		<td>$row1[proc_nome] {$codigos[$proc_codigo]}</td>
		<td>$row1[med_nome]</td>
		<td class='lt'>$row1[dataf]</td>
		<td class='c b'>$dt[1]</td>
	</tr>";
	
}
	
echo "

</table>

	<br>
	<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_on.jpg' alt='Selecionar' />

	</form>

</fieldset>
</body>
</html>
";
