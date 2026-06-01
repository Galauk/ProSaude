<?
error_reporting(E_ALL & ~E_NOTICE );
		ini_set("display_errors",1);
		ini_set("ignore_repeated_errors",0);
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	require_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";
	verauth($id_login);
	cabecario();
	
	
	function todosIguais($vagas,$proc){		
		if(count($vagas) != count($proc))
			return FALSE;
		
		$last = "elo";
		foreach($proc as $proc_codigo){
			if($last == "elo"){
				$last = $vagas[$proc_codigo];
				continue;
			}
			
			if($vagas[$proc_codigo] != $last)
				return false;
		}
		
		return true;
	}
?>

        <style>
                .borda {
                        border-bottom: 1px solid;
                        border-top: 1px solid;
                        border-left: 1px solid;
                        border-right: 1px solid;
                        border-color: #cccccc;
                }
                .borda2 {
                        border-bottom: 1px solid;
                        border-top: 1px solid;
                        border-left: 1px solid;
                        border-right: 1px solid;
                        border-color: #909090;
                }
                .bordaN {
                        border-bottom: 1px solid;
                        border-right: 1px solid;
                        border-color: #909090;
                        background: #f9f9f9;
                        text-align: right;
                }
        </style>
<script language="JavaScript" src="<?=$_SESSION[linkroot].$_SESSION[comum]?>library/js/atalhos.js"></script>
<script language="JavaScript" type="text/javascript" src="<?=$_SESSION[linkroot].$_SESSION[comum]?>library/js/funcoes.js"></script>
<script language="JavaScript" type="text/javascript" src="../g_ajax.js"></script>
<script language="JavaScript" type="text/javascript" src="<?=$_SESSION[linkroot].$_SESSION[comum]?>library/js/ajax_motor.js"></script>
<script type="text/javascript" src="exa_agendamento.js.php?id_login=<?=$id_login?>"></script>
<script language="JavaScript" type="text/javascript" src="procedimento.js"></script>
<script>
	function carrega() {
		document.getElementById('campo_1').focus();
		document.getElementById('campo_1').checked = true;
	}  
	shortcut.add("Ctrl+F12",function()
	{
		document.addexame.submit();
	}
	);
	shortcut.add("Esc",function()
	{
		window.close();
	}
	);
</script>
<body onload = "carrega();">
<?
//AO ATIVAR ESSE sql TERA QUE DISTRIBUIR VAGAS PARA AS UNIDADES, NO MOMENTO ELE DEIXA TODOS AGENDAREM PARA O LABORATORIO
/*$sqlLogon = "select * from logon where id_login = $id_login";
$queryLogon = pg_query($sqlLogon);
$linhaLogon = pg_fetch_array($queryLogon);
$uni_codigo = $linhaLogon[uni_codigo];*/
//
if($acao=="") {
	echo "<fieldset>
	<legend>SELECIONE UMA DATA PARA AGENDAR</legend>";
	echo "<table width=100% cellspacing=0 cellpadding=5 border=0>";
	echo "<form name='addexame' method=post action=$PHP_SELF>
			 <input type=hidden name=acao value=addexame>
			 <input type=hidden name=id_login value=$id_login>
			 <input type=hidden name=usu_codigo value=$usu_codigo>
			 <input type=hidden name=lab_codigo value=$lab_codigo>
			 <input type=hidden name=med_codigo value=$med_codigo>
			 <input type=hidden name=esp_codigo value=$esp_codigo>
			 <input type=hidden name=uni_codigo value=$uni_codigo>";
	$proc = explode(",",$proc_codigo);
	array_pop($proc);

	$hidden_proc .= "<input type=hidden name=proc_codigo value=";
	for($i=0;($i<count($proc));$i++) {
		if(!empty($proc[$i])) {
			$hidden_proc .= "$proc[$i],";
			if(($uni_codigo == 0) || empty($uni_codigo)){
				$q = "SELECT X.DISPONIVEL,
						     X.GRAEX_DATA,
						     X.PROC_CODIGO,
						     X.MED_CODIGO
					    FROM (SELECT sum(graex_qtde) - 
						             COALESCE((SELECT COUNT(*)
												 FROM AGENDAMENTO_EXAME_LISTA 
											    WHERE MED_CODIGO = A.MED_CODIGO
												  AND PROC_CODIGO = A.PROC_CODIGO
												  AND AGEXL_DATA = A.GRAEX_DATA
											    GROUP BY AGEXL_DATA
											    ORDER BY AGEXL_DATA DESC), 0)AS DISPONIVEL,
								     GRAEX_DATA, 
								     PROC_CODIGO, 
								     MED_CODIGO
							    FROM GRADE_EXAME AS A 
							   WHERE MED_CODIGO = '$lab_codigo' 
							     AND GRAEX_DATA >= CURRENT_DATE
							     AND GRAEX_QTDE > 0
							     AND (PROC_CODIGO = '$proc[$i]')
							   GROUP BY GRAEX_DATA, PROC_CODIGO, MED_CODIGO
							   ORDER BY GRAEX_DATA ASC
							 ) AS X
					   WHERE X.DISPONIVEL > 0";
			} else {
				$q = "SELECT X.DISPONIVEL,
						     X.GRAEXUNI_DATA as GRAEX_DATA,
						     X.PROC_CODIGO,
						     X.MED_CODIGO
					    FROM (SELECT sum(graexuni_qtde) - 
									 COALESCE((SELECT COUNT(*)
												 FROM AGENDAMENTO_EXAME_LISTA 
											    WHERE MED_CODIGO = A.MED_CODIGO
												  AND PROC_CODIGO = A.PROC_CODIGO
												  AND AGEXL_DATA = A.GRAEXUNI_DATA
											    GROUP BY AGEXL_DATA
											    ORDER BY AGEXL_DATA DESC), 0)AS DISPONIVEL,
								     GRAEXUNI_DATA, 
								     PROC_CODIGO, 
								     MED_CODIGO
							    FROM GRADE_EXAME_UNIDADE AS A 
							   WHERE MED_CODIGO = '$lab_codigo' 
							     AND UNI_CODIGO = '$uni_codigo'	
							     AND GRAEXUNI_DATA >= CURRENT_DATE
							     AND GRAEXUNI_QTDE > 0
							     AND (PROC_CODIGO = '$proc[$i]')
							   GROUP BY GRAEXUNI_DATA, PROC_CODIGO, MED_CODIGO
							   ORDER BY GRAEXUNI_DATA ASC
							 ) AS X
					   WHERE X.DISPONIVEL > 0";		
			}
			$qq = pg_query($q);

			while($aux = pg_fetch_array($qq)){
				$arraycod[$i][] = $aux['graex_data'];
				$per = explode("-",$aux['graex_data']);
				$perDATA = $per[1]."/".$per[0];
			}
			if(!empty($uni_codigo)) {
				/*$sql3 = pg_query("SELECT count(graexuni_qtde) as cont,
										 graexuni_qtde
									FROM grade_exame_unidade
								   WHERE to_char(graexuni_data, 'mm/yyyy') = '$perDATA'
									 AND uni_codigo = '$uni_codigo'
								   GROUP BY graexuni_qtde
								   ORDER BY cont desc");
				
				$result2 = pg_fetch_array($sql3);
				
				$sql4 = pg_query("SELECT *FROM AGENDAMENTO_EXAME_LISTA WHERE  TO_CHAR(AGEXL_DATA, 'MM/YYYY') = '$perDATA' AND UNI_CODIGO = '$uni_codigo'");
				$result4 = pg_num_rows($sql4);

				$total = $result2[graexuni_qtde] - $result4;*/
				
				$sqlVagasDia = "select proc_codigo,
								       uni_codigo,
								       graexuni_data,
								       graexuni_qtde,
								       (select count(*) 
								          FROM agendamento_exame_lista ael 
								         where ael.agexl_data = geu.graexuni_data
								           AND ael.proc_codigo=geu.proc_codigo
								           AND ael.uni_codigo=geu.uni_codigo ) as total
								  from grade_exame_unidade geu
								 where uni_codigo = $uni_codigo
								   AND proc_codigo in (".implode(",",$proc).")
								   and graexuni_data > CURRENT_DATE
								   AND (select count(*) 
								          FROM agendamento_exame_lista ael 
								         where ael.agexl_data = geu.graexuni_data
								           AND ael.proc_codigo=geu.proc_codigo
								           AND ael.uni_codigo=geu.uni_codigo )  < graexuni_qtde
								  order by graexuni_data ASC";
				$queryVagasDia = pg_query($sqlVagasDia);
				//echo $sqlVagasDia;
				$vagas = array();
				$opcoes = 0;
				while($regVagasDia = pg_fetch_array($queryVagasDia)){
					$proc_codigo = $regVagasDia['proc_codigo'];
					$data = $regVagasDia['graexuni_data'];
					$vagas[$proc_codigo] = $data;
					if(todosIguais($vagas,$proc)){
						$opcoes++;
						$vagas = array();
						echo "<tr>
							      <td width=10%><input id='campo_".$opcoes."' type=radio name=\"graex_data\" value=\"$data\"></td>
							      <td><font size=3><label for=\"campo_".$opcoes."\">".formatarData($data)."</label></font></td>
							     </tr>";
					}					
				}
				if(!$opcoes){
					echo "<center><font color=red><b>Esta unidade nao possui vagas.<br><br><br><a href='#' onclick='window.close()'>FECHAR</a></b></font></center>";
					exit;					
				}
				echo "<input type=\"hidden\" name=\"proc_codigo\" value=\"".implode(",",$proc)."\" />";
				
			}

		}
	}
	/*$hidden_proc .= ">";
	echo $hidden_proc;

	if (count($arraycod) > 1){
		$result = "array_intersect(";
		for($cont = 0; $cont<count($arraycod);$cont++){
			$compare .= "\$arraycod[$cont], ";
		}
		$compare = substr($compare,"0","-2");//tira a última vírgula e espaço
		$result .= $compare;
		$result .= ");";
		eval("\$a = ".$result);
	}else{
		$a = $arraycod[0];
	}
	$j = 0;
	foreach($a as $key => $valor) {
		$j++;
		echo "<tr>
		      <td width=10%><input id='campo_".$j."'type=radio name=graex_data value=$valor></td>
		      <td><font size=3>".formatarData($valor)."</font></td>
		     </tr>";
	
	}*/




   echo "</table>
	</fieldset></form>";
}

if($acao=="addexame") {
   $proc = explode(",",$proc_codigo);
   $data_atual = date("Y-m-d 00:00:00");
   $sq = pg_query("select *from agendamento_exame where usu_codigo = '$usu_codigo' and 
	 	   agex_data_cad = '$data_atual' and med_codigo_responsavel = '$med_codigo'");
   if(pg_num_rows($sq)>=1) {
	echo "<script> 
		alert('ERRO: Ja existe agendamento com estas configuracoes para este Paciente!');
		window.close();
	      </script>";
	exit;
   }

   if(pg_num_rows($sq)>=1) {
for($i=0;($i<count($proc));$i++) {
   $stmt_int = "SELECT COUNT(agexl_codigo), intervalo
                                FROM agendamento_exame_lista,
                                        ( SELECT COALESCE(proc_intervalo_min,0) AS intervalo FROM procedimento
                                          WHERE proc_codigo = {$proc[$i]} ) AS teste
                                WHERE agexl_data between '{$graex_data}'::date - intervalo AND '{$graex_data}'::date + intervalo AND
                                proc_codigo = {$proc[$i]} AND usu_codigo = {$usu_codigo}
                                GROUP BY intervalo
                        ";
			$nproc = pg_fetch_array(pg_query("select *from procedimento where proc_codigo = $proc[$i]"));
                        $row_teste = db_getRow( $stmt_int );
                        if( $row_teste[1] > 0 && $row_teste[0] > 0 )
                        {
                                print '
                                <script type="text/javascript">
                                        alert("ALERTA:  O paciente ja possui um agendamento de um '.$nproc[proc_nome].' num intervalo minimo de '.$row_teste[1].' dias!, SOMENTE O AUDITOR PODE LIBERAR ESTE PROCEDIMENTO FORA DO PRAZO DE RE-EXAME.");
			  		window.close();
                                </script>';
exit;
                        }
 }
}

db_query('begin');
           $stmt = "INSERT INTO agendamento_exame
                        (usu_codigo, agex_data_cad, med_codigo_responsavel, esp_codigo_responsavel)
                    VALUES ('$usu_codigo', CURRENT_DATE, '$lab_codigo','$esp_codigo') ";
db_query($stmt);
           $agex_codigo = db_get('SELECT MAX(agex_codigo) FROM agendamento_exame');
db_query('commit');

for($i=0;($i<count($proc));$i++) {
 if(!empty($proc[$i])) {
     $stmw = "INSERT INTO agendamento_exame_lista
                        (agex_codigo, usu_codigo, med_codigo, proc_codigo, agexl_data, agexl_status,
                                usr_codigo_cad, agexl_dt_cadastro, uni_codigo )
                        VALUES('$agex_codigo', '$usu_codigo', '$lab_codigo', '$proc[$i]', '$graex_data', 'A', '$id_login', CURRENT_DATE, '$uni_codigo')";

db_query($stmw);
   }
 }
   echo "
	 <br><br><br><br><br><br>
	 <br><br><br><br><br><br>
	 <br><br><br>
	 <center><font size=6 color=green><b>AGENDADO</b></font></center>";

 ?>
<script>
 function pause( iMilliseconds )
 {
   var sDialogScript = 'window.setTimeout( function () { window.close(); }, ' + iMilliseconds + ');';
   window.open("../agendar_exame_print.php?acao=form_imprime&imprimir=a&agex_codigo=<?=$agex_codigo?>&usu_codigo=<?=$usu_codigo?>&lab=<?=$med_codigo?>","nv","width=750,height=400");
   window.close();
 }	
pause(1000);
</script>
<?
}


?>
</body>
