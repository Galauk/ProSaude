<?
	session_start();
	include_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	
	$form = new classForm();
	$requisicoes = $_GET[requisicoes];
	$med_codigo = $_GET[med_codigo];
	$uni_codigo = $_GET[uni_codigo];
	
	$select = "SELECT proc_codigo 
			  	 FROM requisicao_exames 
			    WHERE req_codigo IN ($requisicoes)";
	$query = pg_query($select);
	$i = 0;
	while ($linha = pg_fetch_array($query)){
	/*	CASO SEJA A VAGA DA UNIDADE, DEVE SER ESSE SELECT!
	 *  $sql = "SELECT x.disponivel,
					   x.graexuni_data as graex_data,
					   x.proc_codigo,
					   x.med_codigo
				  FROM (SELECT sum(graexuni_qtde) - COALESCE((SELECT count(*)
																FROM agendamento_exame_lista 
															   WHERE med_codigo = a.med_codigo
																 AND proc_codigo = a.proc_codigo
																 AND agexl_data = a.graexuni_data
															   GROUP BY agexl_data
															   ORDER BY agexl_data DESC), 0) AS disponivel,
							   graexuni_data, 
							   proc_codigo, 
							   med_codigo
					  	  FROM grade_exame_unidade AS a 
					 	 WHERE med_codigo = '$med_codigo' 
						   AND uni_codigo = '$uni_codigo'	
						   AND graexuni_data >= CURRENT_DATE
						   AND graexuni_qtde > 0
						   AND proc_codigo = $linha[proc_codigo]
						 GROUP BY graexuni_data, proc_codigo, med_codigo
						 ORDER BY graexuni_data ASC
						) AS x
				 WHERE x.disponivel > 0";*/
		
		$sql = "SELECT x.disponivel,
					   x.graex_data,
					   x.proc_codigo,
					   x.med_codigo
				  FROM (SELECT sum(graex_qtde) - COALESCE((SELECT count(*)
															 FROM agendamento_exame_lista 
														    WHERE med_codigo = a.med_codigo
															  AND proc_codigo = a.proc_codigo
															  AND agexl_data = a.graex_data
														    GROUP BY agexl_data
														    ORDER BY agexl_data DESC), 0) AS disponivel,
							   graex_data, 
							   proc_codigo, 
							   med_codigo
						  FROM grade_exame AS a 
						 WHERE med_codigo = '$med_codigo' 
						   AND graex_data >= CURRENT_DATE
						   AND graex_qtde > 0
						   AND proc_codigo = $linha[proc_codigo]
						 GROUP BY graex_data, proc_codigo, med_codigo
						 ORDER BY graex_data ASC
					   ) AS x
				 WHERE x.disponivel > 0";
		
		$exec = pg_query($sql);
		while ($dados = pg_fetch_array($exec)){
			$array[$i][] = formatarData($dados['graex_data']);
		}
		$i++;
	}
	if (count($array) > 1){
		$result = "array_intersect(";
		for($cont = 0; $cont<count($array);$cont++){
			$compare .= "\$array[$cont], ";
		}
		$compare = substr($compare,"0","-2");//tira a última vírgula e espaço
		$result .= $compare;
		$result .= ");";
		eval("\$a = ".$result);
	}else if (count($array) == 1){
		$a = $array[0];
	}else{
		$msg = "N&atilde;o existem datas com vagas dispon&iacute;veis!";
		$a = array("$msg");
	}
	$j = 0;

	//echo $form->inputCheckboxRadio("data", null, "Data", null, $a, "radio");
	echo $form->inputLabel("Data");
	echo "<table>";
	foreach ($a as $val){
		echo "<tr>
				<td>";
			if ($msg != ""){
				echo $msg;
			}else{
				echo "<input type='radio' name='agexl_data' id='agexl_data' value='$val'> $val";
			}
		echo "</td>
			</tr>";
	}
	echo "</table>";
?>