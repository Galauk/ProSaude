<?php
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
//verauth($id_login);
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";

cabecario();
?>

<script src="script.js" language="javascript" type="text/javascript"></script>
<script>
    function ajaxInit() {
        var req;
        try {
            req = new ActiveXObject("Microsoft.XMLHTTP");
        } 
        catch(e) {
                try {
                    req = new ActiveXObject("Msxml2.XMLHTTP");
                } 
            catch(ex) {
            try {
                    req = new XMLHttpRequest();
            } 
            catch(exc) {
             alert("Esse browser não tem recursos para uso do Ajax");
              req = null;
            }
 
        }  
 
        }
    return req;
}
    
    function alteragrm(proc_codigo, old_qtde, gex_qtde, gex_codigo)

     { 
       
        if (Number(gex_qtde) <= 0) { 
            
            alert("Quantidade deve ser maior que zero!"); 
            document.getElementById('frm_gex_qtde' + gex_codigo).value = old_qtde; 
            document.getElementById('frm_gex_qtde' + gex_codigo).focus(); 
            return false; 
           
        } 
        var endereco='ajax/update/agendamento/grade_exame_ajax.php?gex_codigo='+ gex_codigo +'&proc_codigo='+proc_codigo+'&gex_qtde='+ gex_qtde; 
          // alert(endereco) 
        ajax = ajaxInit();
        
        if(ajax) {
            ajax.open("GET", endereco , true);

            ajax.onreadystatechange = function() {
            if(ajax.readyState == 4) {
                if(ajax.status == 200) {
                   
                    //document.getElementById('hf').innerHTML = ajax.responseText;
                    //document.getElementById('usr').innerHTML = ajax.responseText;
                                      
                } else {
                    alert(ajax.statusText);
                }           
            }
        }    
   ajax.send(null);
    
    }
 }

</script>

<?php

if( ! $id_dia || ! $med_codigo || ! $agt_codigo )// $uni_codigo )//|| !)
{
	//die("Escolha um <strong>Laboratório</strong> e uma <strong>Data</strong> antes de prosseguir !");
	print ("Escolha <strong>todos</strong> os campos antes de prosseguir !");
    exit(1);
}

//------------------------------------------------------------------>

$med_row = db_getRow("SELECT proc_tipo_manut FROM medico WHERE med_codigo = $med_codigo");

if( $med_row['proc_tipo_manut'] == 1 )
{
	print "<p>Obs: Este laborat&oacute;rio est&aacute; configurado para manuten&ccedil;&atilde;o 
		<strong>por procedimento (unit&aacute;rio)</strong>.</p>";
}
else
{
	print "<p>Obs: Este laborat&oacute;rio est&aacute; configurado para manuten&ccedil;&atilde;o 
		<strong>por per&iacute;odo</strong>.</p>";

}

		
$Data = explode("/",$id_dia);

if( $med_row['proc_tipo_manut'] == 2 )
{
	$sql = "SELECT MAX( gem_periodo ) as gex_periodo, gem_periodo + 29 > '$Data[2]-$Data[1]-$Data[0]' as valido, 
		gem_valor
		from grade_exame_mensal_manut
		WHERE gem_periodo  <= '$Data[2]-$Data[1]-$Data[0]' 
		AND med_codigo = '$med_codigo' AND agt_codigo = '$agt_codigo'
		GROUP BY gem_periodo, gem_valor
		ORDER BY to_char(gem_periodo,'YYYY-mm-dd') desc";

}
else
{
	$sql = "SELECT MAX( gex_periodo ) as gex_periodo, gex_periodo + 29 > '$Data[2]-$Data[1]-$Data[0]' as valido, gex_qtde, gex_valor, gex_tipo, proc_codigo
		from grade_exame_mensal
		WHERE gex_periodo  <= '$Data[2]-$Data[1]-$Data[0]' 
		AND med_codigo = '$med_codigo' AND agt_codigo = $agt_codigo
		GROUP BY gex_periodo, gex_qtde, gex_valor, gex_tipo, proc_codigo
		ORDER BY to_char(gex_periodo,'YYYY-mm-dd') desc";
}
//print $sql;
$query = db_query($sql);
$row0 = pg_fetch_array($query);

$gex_periodo = $row0['gex_periodo'];

// pergunta se o periodo é valido
// retorna T para true ( verdadeiro )
// retorna F para false ( falso )
if($row0['valido'] == 't')
{
	/*
	ver se ja exite a data criada dentro do periodo ... se existir fazer um select nela para que possamos
	fazer um update no botao gravar
	se nao existir a data no periodo fazer um insert para que possamos fazer um update no botao gravar
	*/
	$sql_consulta = " SELECT DISTINCT graex_data
	FROM grade_exame 
	WHERE graex_data='$Data[2]-$Data[1]-$Data[0]' AND med_codigo=$med_codigo AND agt_codigo=$agt_codigo";
	//and proc_codigo=$proc_codigo";
	
	$query_consulta = db_query($sql_consulta);
	$row_consulta = pg_fetch_array($query_consulta);
	//echo "sql_consulta : ".$sql_consulta."<br />";
	//echo "row_consulta[graex_data] : ".$row_consulta['graex_data']."<br />";

	if ($row_consulta['graex_data'] == '')
	{
		//$stmt_p = "SELECT * FROM procedimento WHERE med_codigo=$med_codigo";
		/***
		$stmt_p = "SELECT * FROM procedimento AS p
		NATURAL JOIN laboratorio_procedimento AS lp WHERE lp.med_codigo=$med_codigo";
		$qry_p  = db_query( $stmt_p );
		
		while( $row_p = pg_fetch_array($qry_p) )
		{
	
			$sql_insert="INSERT INTO grade_exame
			(med_codigo,proc_codigo,graex_qtde,graex_data,graex_valor,usr_codigo_cad)  
			VALUES  ( '$med_codigo','$row_p[proc_codigo]','0','$id_dia','0.00','$id_login')";
			
			$query_insert = db_query($sql_insert);
			
		}
		****/
		// verifica se cada um dos procedimentos deste Laboratorio possui uma "entrada" neste periodo
		// "facilita" quando ha a insercao de um procedimento, apos o "fechamento" de uma 
		if ( ! empty($id_dia) )
		{
			$stmt_proc = "
			SELECT proc_codigo, 
			 (SELECT COUNT(proc_codigo) FROM grade_exame AS g 
			 	WHERE p.proc_codigo = g.proc_codigo AND med_codigo = {$med_codigo} AND g.graex_data='{$id_dia}' AND agt_codigo = {$agt_codigo}) as total
			 FROM laboratorio_procedimento AS p
			 WHERE med_codigo = {$med_codigo}";

			$qry_proc = db_query( $stmt_proc );

			db_query('begin');

			while( $row_proc = pg_fetch_array($qry_proc) )
			{
				// se nao houver, insere !
				if(! $row_proc['total'] )
				{
					$stmt_proc_i = "INSERT INTO grade_exame
					(med_codigo,proc_codigo,graex_qtde,graex_data,graex_valor,usr_codigo_cad, agt_codigo)  
					VALUES  ( '$med_codigo','$row_proc[proc_codigo]','0','$id_dia','0.00','$id_login',$agt_codigo)";
					db_query($stmt_proc_i);	
				}
			}
			db_query('commit');

		} // empty($id_dia)	

	
	}
	
    $sql_select="SELECT graex_codigo, ge.med_codigo, p.proc_codigo, graex_qtde, graex_data, graex_valor,
		p.proc_nome, p.gex_tipo, u1.usr_nome AS usr_cad, u2.usr_nome AS usr_alt, lp.*
		FROM grade_exame AS ge
		NATURAL JOIN laboratorio_procedimento AS lp
		LEFT JOIN procedimento AS p ON ge.proc_codigo = p.proc_codigo
		LEFT JOIN usuarios AS u1 ON ge.usr_codigo_cad = u1.usr_codigo
		LEFT JOIN usuarios AS u2 ON ge.usr_codigo_alt = u2.usr_codigo
			WHERE graex_data='$Data[2]-$Data[1]-$Data[0]'
			AND ge.med_codigo='$med_codigo'
			AND ge.agt_codigo='$agt_codigo'
		ORDER BY p.proc_nome"; 

	$query_select = db_query($sql_select);
	//$row = pg_fetch_array($query_select);
 
} else { // if ($row0['valido'] == 'f'){

	// se a data nao for em um periodo valido exibir um alert falando q o periodo nao é valido 
	echo "<script type=\"text/javascript\">
				alert('Essa data não pertence a um período válido');
				//document.location.href='manutencao_exame_iframe.php';
		  </script>";

}

//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>
echo "<div style='font-weight:bold;' id='upd'>Atualiza&ccedil;&atilde;o:<label style='font-weight:bold;color:#10d' id='usr'></label></div>"; 

echo "
<table class='lista'>
	<tr bgcolor='#FFFFFF'>
	     <th>Procedimento</th>";
//----------------------------------------------------------------->

// if($row_['gex_tipo']=="Q")
// {
	echo "<th width='90' class='c'>Qtde./Pre&ccedil;o</th>\n";
	echo "<th width='150' class='c'>Limite do M&ecirc;s/Restante</th>\n";	
// }
// else if($row0['gex_tipo']=="V")
// {
//   	echo "<th width='90'>Qtde. Preço</th>\n";
// 	echo "<th width='100'>Limite do Mês</th>\n";
// }

//----------------------------------------------------------------->

  echo "
		<!--<th>Cadastrado Por</th>
		<th>Alterado Por</th>-->
		<th>&nbsp;</th>
	</tr>\n";

$i=0;

// MANUTENCAO 'UNITARIA'
if( $med_row['proc_tipo_manut'] == 1 )
{
		while( $row = pg_fetch_array($query_select) )
		{		 

				//$row['gex_tipo'] = trim($row['gex_tipo']);
				$row['gex_tipo'] = trim($row['proc_tipo']);

				echo "
						<form name='ff' method='post' action='$PHP_SELF?id_login=$id_login&med_codigo=$med_codigo&id_dia=$id_dia&agt_codigo=$agt_codigo'>\n
						<input type='hidden' name='acao' value='gravar'>
						<input type='hidden' name='graex_codigo' value='$row[graex_codigo]'>
						<input type='hidden' name='gex_tipo' value='$row[gex_tipo]'>
						<tr>
						<td>\n $row[proc_nome]</td>
						";

				$linkline2 = "<a href='$PHP_SELF?id_login=$id_login&graex_codigo=$row[graex_codigo]&acao=delline".
						"&id_dia=$id_dia&med_codigo=$med_codigo&proc_codigo=$proc_codigo&id_login=$id_login&uni_codigo=$uni_codigo&agt_codigo=$agt_codigo'>
						<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagarlinha.jpg' border=0></a>";

				if($row['gex_tipo']=="Q")
				{

						$sql_valor = "SELECT gex_qtde FROM grade_exame_mensal 
								WHERE proc_codigo={$row[proc_codigo]}
                                AND gex_periodo = '{$row0[gex_periodo]}'
                                AND agt_codigo = {$agt_codigo}";
						$valor = db_get( $sql_valor );

						// soma todos os outros, menos o dele !
						$sql_valor_resto = "SELECT SUM(graex_qtde) FROM grade_exame 
								WHERE proc_codigo = $row[proc_codigo]
								AND graex_codigo <> $row[graex_codigo]
                                AND agt_codigo = {$agt_codigo}
								AND graex_data BETWEEN DATE '$row0[gex_periodo]' AND DATE '$row0[gex_periodo]' + INTEGER '29'";
						$valor_resto_q = db_get( $sql_valor_resto );
						$valor_resto = intval( $valor - $valor_resto_q );
				
						echo "
								<td class='c'>
								<input type=text id='cx_qtd' name=gex_qtde value='$row[graex_qtde]' class='boxagente'>
								(UN)
								</td>
								<td class='c'>
								<input type='text' id='cx_qtd' name='gex_qtde_readonly' value='$valor' class='boxagente b'
								readonly='readonly'>
								/
								<input type='text' id='cx_valor_rest' name='gex_valor_rest_readonly' value='$valor_resto'
								class='boxagente b' readonly='readonly' style='font-weight:bold'>
								</td>";

				}
				else if($row['gex_tipo']=="V")
				{
						$sql_valor = "SELECT gex_valor FROM grade_exame_mensal 
								WHERE proc_codigo=$row[proc_codigo]
                                AND gex_periodo = '$row0[gex_periodo]'
                                AND agt_codigo = {$agt_codigo}";
						$valor = db_get( $sql_valor );

						// soma todos os outros, menos o dele !
						$sql_valor_resto = "SELECT SUM(graex_valor) FROM grade_exame 
								WHERE proc_codigo = $row[proc_codigo]
								AND graex_codigo <> $row[graex_codigo]
                                AND agt_codigo = {$agt_codigo}
								AND graex_data BETWEEN DATE '$row0[gex_periodo]' AND DATE '$row0[gex_periodo]' + INTEGER '29'";
						$valor_resto_q = db_get( $sql_valor_resto );
						$valor_resto = number_format($valor - $valor_resto_q,2);

						echo "
								<td class='c'>
								<input type=text id='cx_valor' name=gex_valor value='$row[graex_valor]' class='boxagente'>
								(R$)
								</td>
								<td class='c'>
								<input type='text' id='cx_valor' name='gex_valor_readonly' value='$valor'
								class='boxagente b' readonly='readonly'>
								/
								<input type='text' id='cx_valor_rest' name='gex_valor_rest_readonly' value='$valor_resto'
								class='boxagente b' readonly='readonly'> 
								</td>";
				}

				echo "
						<!--<td>$row[usr_cad] &nbsp;</td>
						<td>$row[usr_alt] &nbsp;</td>-->
						<td class='c'>$linkline2 &nbsp;  <input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/gravar.jpg' border='0'> </td>
						</tr>
						<input type='hidden' name='limite' value='$valor_resto' />	
						</form>
						"; //$rr[usr_login_alt]&nbsp;


		} // while row

// MANUTENCAO POR PERIODO !
} else {

	// O RESTANTE VAI SER O MESMO !!!
	/* $stmt_manut =
		"SELECT gem_codigo, gem_valor , total, gem_valor - total AS diferenca
	          FROM 
	          (SELECT COALESCE(SUM(p.proc_valor),0), SUM() AS total
	              FROM grade_exame AS g
	              INNER JOIN procedimento AS p ON g.proc_codigo = p.proc_codigo
	              WHERE g.med_codigo=$med_codigo AND 
				  	graex_data BETWEEN '$gex_periodo' and '$gex_periodo'::date + 29) AS totaltb,
	          grade_exame_mensal_manut  AS g1
	          WHERE g1.med_codigo=$med_codigo AND g1.gem_periodo = '$gex_periodo'";
	*/
	if( $gex_periodo )
	{
		$stmt_manut = 
		"SELECT gem_codigo, gem_valor, total, gem_valor - total AS diferenca
		FROM 
			( SELECT laboratorio_calcula_custo_agt( {$med_codigo}::int8, '{$gex_periodo}'::date, 30::int2, {$agt_codigo}::int8 ) AS total ) AS total_temp,
			grade_exame_mensal_manut AS g1 
		WHERE g1.med_codigo={$med_codigo} AND g1.agt_codigo = {$agt_codigo} AND g1.gem_periodo = '{$gex_periodo}'";

		$row_manut = db_getRow($stmt_manut);
	}

	$valor = $row_manut[1];
	$valor_resto = $row_manut[3];



		while( $row = pg_fetch_array($query_select) )
		{		 

				//$row['gex_tipo'] = trim($row['gex_tipo']);
				$row['gex_tipo'] = trim($row['proc_tipo']);

				echo "
						<form name='ff' method='post' action='$PHP_SELF?id_login=$id_login&med_codigo=$med_codigo&id_dia=$id_dia&agt_codigo=$agt_codigo'>\n
						<input type='hidden' name='acao' value='gravar'>
						<input type='hidden' name='graex_codigo' value='$row[graex_codigo]'>
						<input type='hidden' name='gex_tipo' value='$row[gex_tipo]'>
						<tr>
						<td>\n $row[proc_nome]</td>
						";

				$linkline2 = "<a href='$PHP_SELF?id_login=$id_login&graex_codigo=$row[graex_codigo]&acao=delline".
						"&id_dia=$id_dia&med_codigo=$med_codigo&proc_codigo=$proc_codigo&id_login=$id_login".
                        "&uni_codigo=$uni_codigo&agt_codigo=$agt_codigo'>
						<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagarlinha.jpg' border=0></a>";


				if($row['gex_tipo']=="Q")
				{

						//$sql_valor = "SELECT gex_qtde FROM grade_exame_mensal 
						//		WHERE proc_codigo=$row[proc_codigo] AND gex_periodo = '$row0[gex_periodo]'";
						//$valor = db_get( $sql_valor );

						// soma todos os outros, menos o dele !
						//$sql_valor_resto = "SELECT SUM(graex_qtde) FROM grade_exame 
						//		WHERE proc_codigo = $row[proc_codigo]
						//		AND graex_codigo <> $row[graex_codigo]
						//		AND graex_data BETWEEN DATE '$row0[gex_periodo]' AND DATE '$row0[gex_periodo]' + INTEGER '29'";
						
						$st = "SELECT proc_valor FROM procedimento WHERE proc_codigo=$row[proc_codigo]";

						$valor_proc 		= db_get( $st  );
						$valor_qtde 		= intval( $valor / $valor_proc );
						$valor_resto_qtde 	= intval( $valor_resto / $valor_proc );

						echo "
								<input type='hidden' name='limite' value='$valor_resto_qtde' />	
								<td class='c'>
								<input type=text id='cx_qtd' name=gex_qtde value='$row[graex_qtde]' class='boxagente'>
								(UN)
								</td>
								<td class='c'>
								<input type='text' id='cx_qtd' name='gex_qtde_readonly' value='$valor_qtde' class='boxagente b'
								readonly='readonly'>
								/
								<input type='text' id='cx_valor_rest' name='gex_valor_rest_readonly' value='$valor_resto_qtde'
								class='boxagente b' readonly='readonly' style='font-weight:bold'>
								(UN)
								</td>";

				}
				else if($row['gex_tipo']=="V")
				{
						echo "
								<input type='hidden' name='limite' value='$valor_resto' />	
								<td class='c'>
								<input type=text id='cx_valor' name=gex_valor value='$row[graex_valor]' class='boxagente'>
								(R$)
								</td>
								<td class='c'>
								<input type='text' id='cx_valor' name='gex_valor_readonly' value='$valor'
								class='boxagente b' readonly='readonly'>
								/
								<input type='text' id='cx_valor_rest' name='gex_valor_rest_readonly' value='$valor_resto'
								class='boxagente b' readonly='readonly'> 
								(R$)
								</td>";
				}

				echo "
						<!--<td>$row[usr_cad] &nbsp;</td>
						<td>$row[usr_alt] &nbsp;</td>-->
						<td class='c'>$linkline2 &nbsp;  <input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/gravar.jpg' border='0'> </td>
						</tr>
						</form>
						"; //$rr[usr_login_alt]&nbsp;

		} // while

}

	echo " </table>";


	/*
	fazer a  seguinte verificação, se a quantidade digitada for maior que o permitido ... dar um alert
	avisando que extrapolou o limite...
	$sql_confere_valor = "SELECT gex_valor FROM grade_exame_mensal WHERE med_codigo=$med_codigo AND
	proc_codigo=$proc_codigo AND gex_periodo=$id_dia";
	*/

	if($acao=="gravar")
	{

    reglog($id_login,"Gravando Grade Medico Med.Cod.: $med_codigo - Qtde: $gra_qtde");

		//if($row['gex_tipo']=="Q") {
		if($gex_tipo=="Q") {
		
			//if ( $gex_qtde > $row0['gex_qtde'] ){
			if ( $gex_qtde > $limite ){
			
				echo "<SCRIPT LANGUAGE=\"JavaScript\">
							alert('A Quantidade digitada extrapolou o Limite');
					  </SCRIPT>";
				
			}else{

				$sql_1 = 
					"UPDATE grade_exame SET graex_qtde='$gex_qtde',
						usr_codigo_alt='$id_login' 
					WHERE graex_codigo='$graex_codigo'";
						 
				//echo $sql_1;
				$query = pg_query($sql_1);

			 }

		}
		elseif($gex_tipo=="V")
		{
		
			//if ( $gex_valor > $row0['gex_valor'] ){
			if ( $gex_valor > $limite ){
			
				echo "<SCRIPT LANGUAGE=\"JavaScript\">
							alert('O valor digitado extrapolou o Limite');
					  </SCRIPT>";
			
			}else{
			
				$sql_1 = 
					"UPDATE grade_exame SET graex_valor='$gex_valor',
						usr_codigo_alt='$id_login' 
					WHERE graex_codigo='$graex_codigo'";
						 
				//echo $sql_1;
				$query = pg_query($sql_1);

			}
		
		}

        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                 setTimeout(\"location='$PHP_SELF?acao=&id_dia=$id_dia&med_codigo=$med_codigo&proc_codigo=$proc_codigo&id_login=$id_login&agt_codigo=$agt_codigo'\", 0);
              </SCRIPT>";			  
	}


	if($acao=="delline") {

		reglog($id_login,"Apagando Horario do Medico Cod: $med_codigo");
		$data = "$Data[2]-$Data[1]-$Data[0]";
		$sql = "delete from grade_exame where graex_codigo='$graex_codigo'";

		$query = pg_query($sql);
		
		echo "<SCRIPT LANGUAGE=\"JavaScript\">
				setTimeout(\"location='$PHP_SELF?acao=&id_dia=$id_dia&uni_codigo=$uni_codigo&med_codigo=$med_codigo&esp_codigo=$esp_codigo&id_login=$id_login&uni_codigo=$uni_codigo&agt_codigo=$agt_codigo'\", 0);
			  </SCRIPT>";

	}




?>
