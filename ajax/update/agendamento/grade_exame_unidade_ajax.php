<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	
	if(empty($_GET['cod_medico'])){
		$med_codigo = $regGradeUnidade['med_codigo'];
		//echo $med_codigo;
	}else{
		$med_codigo   = $_GET['cod_medico'];
	}
	
	if(empty($_GET['uni_qtde'])){
		$uni_qtde = $regGradeUnidade['graexuni_qtde'];
	}else{
		$uni_qtde = intval(abs($_GET['uni_qtde']));
	}
	
	if(empty($_GET['gex_codigo'])){
		$gex_codigo = $regGradeUnidade['gex_codigo'];
	}else{
		$gex_codigo = $_GET['gex_codigo'];
	}
	
	if(empty($_GET['periodo'])){
		$periodo = $gex_periodo;
		$per = explode("/",$periodo);
		$perint = $per[0]."/".$per[1]."/".$per[2];
		$mesano = $per[1]."/".$per[2];
	}else{
		$periodo   = $_GET['periodo'];
		$per = explode("-",$periodo);
		$perint = $per[2]."/".$per[1]."/".$per[0];
		$mesano = $per[1]."/".$per[0];
	}
	
	if(empty($_GET['id_login'])){
		$id_login = $id_login;
	}else{
		$id_login   = $_GET['id_login'];
	}
	
	if(empty($_GET['uni_codigo'])){
		$uni_codigo = $regGradeUnidade['uni_codigo'];
	}else{
		$uni_codigo = $_GET['uni_codigo'];
	}
	
	if(empty($proc_codigo)){
		$proc_codigo = $rr[proc_codigo];
	}
	
	//$med_codigo   = $_GET['cod_medico'];
	//$uni_qtde     = intval(abs($_GET['uni_qtde']));
	//$gex_codigo   = $_GET['gex_codigo'];
	//$periodo   = $_GET['periodo'];
	//$id_login = $_GET['id_login']; 
	//$uni_codigo = $_GET['uni_codigo'];
	$q = "BEGIN; ";
	$totalVagas = $_GET['total'];
	
	
	$conta=1;

	

	$dataini = $perint;
	$datainc = $perint;
	$qtde = contaDiasMes($dataini);

     $sql2 = pg_query(" select count(graex_qtde) as cont,
                                                          graex_qtde
                                                 from grade_exame
                                                where to_char(graex_data, 'mm/yyyy') = '$mesano'
                                                  and proc_codigo = '$proc_codigo'
                                                group by graex_qtde
                                                order by cont desc") or die(pg_last_error());
        $result2 = pg_fetch_array($sql2);
      $sql4 = pg_query("SELECT SUM(X.MAX) as total
                                                FROM (
                                                         SELECT MAX(GRAEXUNI_QTDE) AS MAX
                                                           FROM GRADE_EXAME_UNIDADE
                                                          WHERE TO_CHAR(GRAEXUNI_DATA, 'MM/YYYY') = '$mesano'
                                                                AND PROC_CODIGO = '$proc_codigo'
                                                          GROUP BY UNI_CODIGO
                                                          ) AS X") or die(pg_last_error());
        $result4 = pg_fetch_array($sql4);
		$sql = "select count(graexuni_qtde) as cont,
					  graexuni_qtde
				 from grade_exame_unidade
				where to_char(graexuni_data, 'mm/yyyy') = '$mesano'
				  and uni_codigo = '$uni_codigo'
				and proc_codigo  = $proc_codigo
				group by graexuni_qtde
				order by cont desc";
		$exe_sql = pg_query($sql);
		$res_exe_sql = pg_fetch_array($exe_sql);
		$qtde_uni_banco = $res_exe_sql["graexuni_qtde"];
		if(($qtde_uni_banco > $uni_qtde ) || (($qtde_uni_banco > 0 ))){
			$total = $result2[graex_qtde] - $result4[total] + $qtde_uni_banco - $uni_qtde;
		}else{
			$total = $result2[graex_qtde] - $result4[total] - $uni_qtde;
		}
	if($total < 0) {
		$echo = $result2[graex_qtde] - $result4 [total];
		exit;
	}

	while ($conta <= $qtde)
	{
		if($med_codigo != ""){
			$vediafer = "SELECT count(*) from feriado WHERE fer_data = '$datainc'";
			$vefer = db_getRow($vediafer);
		
			if ($vefer[0] == 0) {
				//verificar dia da semana
				$vediasem = "SELECT EXTRACT(dow from TO_DATE('$datainc', 'dd/mm/yyyy'))";
				$vedia = db_getRow($vediasem);
				if (($vedia[0] <> 0) && ($vedia[0]<>6)) {
					//gravar dados
					if ($manut_row['proc_tipo_manut'] == 'P') 
						$proc_codigo = 0;
					
					$select = "select graex_qtde 
								 from grade_exame 
								where graex_data = '$datainc' 
								  and proc_codigo = '$proc_codigo' 
								  and med_codigo = '$med_codigo'";
					$linha = pg_fetch_array(pg_query($select));
					$graex_qtde = $linha[graex_qtde];
					
					if ($graexuni_qtde > $graex_qtde){
						$graexuni_qtde = $graex_qtde;	
					}
					$sq = "select * from grade_exame_unidade where med_codigo = '$med_codigo' and proc_codigo = '$proc_codigo' and uni_codigo = '$uni_codigo' and to_char(graexuni_data,'MM/YYYY') = '$mesano' and gex_codigo = '$gex_codigo' limit 1";
					$sq = pg_query($sq);
						if (pg_num_rows($sq) == 0){
							$q .= "INSERT INTO grade_exame_unidade
										(med_codigo, proc_codigo, uni_codigo, graexuni_data, graexuni_qtde, usr_codigo_cad, gex_codigo)
								   VALUES
										($med_codigo, $proc_codigo, $uni_codigo, '$datainc', $uni_qtde, $id_login, $gex_codigo);";
						
						}else{
							$q .= "UPDATE grade_exame_unidade
									  SET 
										  med_codigo = '$med_codigo', 
									  	  proc_codigo = '$proc_codigo', 
										  uni_codigo = '$uni_codigo', 
										  graexuni_qtde = '$uni_qtde', 
										  usr_codigo_alt = '$id_login', 
										  gex_codigo = '$gex_codigo'
									  WHERE 
										  med_codigo = '$med_codigo' 
									  	  AND proc_codigo = '$proc_codigo' 
										  AND uni_codigo = '$uni_codigo' 
										  AND gex_codigo = '$gex_codigo';";
						}
						
						$sqldata = "select to_char(to_date('$datainc', 'dd/mm/yyyy')+1, 'dd/mm/yyyy')";
						$rowdata = db_getRow($sqldata);
						$conta = $conta + 1;
						$datainc = $rowdata[0];
					} //fim if diasemana
					else
					{
						$sqldata = "select to_char(to_date('$datainc', 'dd/mm/yyyy')+1, 'dd/mm/yyyy')";
						$rowdata = db_getRow($sqldata);
						$conta = $conta + 1;
						$datainc = $rowdata[0];
					} //fim do else - final de semana
				} // fim if feriado
				else
				{
					$sqldata = "select to_char(to_date('$datainc', 'dd/mm/yyyy')+1, 'dd/mm/yyyy')";
					$rowdata = db_getRow($sqldata);
					$conta = $conta + 1;
					$datainc = $rowdata[0];
				} //fim do else - feriado
			}
		} //fim while
		$q .= " COMMIT;";
		$rq = db_query($q);

     $sql2 = pg_query(" select count(graex_qtde) as cont,
							  graex_qtde
					 from grade_exame
					where to_char(graex_data, 'mm/yyyy') = '$mesano'
					  and proc_codigo = '$proc_codigo'
					group by graex_qtde
					order by cont desc");
        $result2 = pg_fetch_array($sql2);

      $sql4 = pg_query("SELECT SUM(X.MAX) as total
							FROM (
								 SELECT MAX(GRAEXUNI_QTDE) AS MAX
								   FROM GRADE_EXAME_UNIDADE
								  WHERE TO_CHAR(GRAEXUNI_DATA, 'MM/YYYY') = '$mesano'
										AND PROC_CODIGO = '$proc_codigo'
								  GROUP BY UNI_CODIGO
								  ) AS X");
        $result4 = pg_fetch_array($sql4);

        $total = $result2[graex_qtde] - $result4[total];
		
			

//echo $total;	
?>
