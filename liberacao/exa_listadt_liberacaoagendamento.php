<?
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	verauth($id_login);
	include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";
	cabecario();
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
<script language="JavaScript" src="../atalhos.js"></script>
<script language="JavaScript" type="text/javascript" src="../funcoes.js"></script>
<script language="JavaScript" type="text/javascript" src="../g_ajax.js"></script>
<script language="JavaScript" type="text/javascript" src="../ajax_motor.js"></script>
<script type="text/javascript" src="exa_agendamento.js.php?id_login=<?=$id_login?>"></script>
<script language="JavaScript" type="text/javascript" src="procedimento.js"></script>
<script>
	function carrega() {
		document.getElementById('campo_1').focus();
		document.getElementById('campo_1').checked = true;
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
$lib_dia_agendamento = $_POST['lib_dia_agendamento'];
$lib_mes_agendamento = $_POST['lib_mes_agendamento'];
$lib_ano_agendamento = $_POST['lib_ano_agendamento'];
$data = $lib_dia_agendamento."/".$lib_mes_agendamento."/".$lib_ano_agendamento;
$dataAtual =date('d/m/Y');
$libex_codigo = $_POST['codigoLib'];
$hora = $_POST['hora'];

	$sql = "SELECT a.libex_codigo,
				   a.med_codigo_responsavel,
				   a.esp_codigo_responsavel,
				   d.proc_nome,
				   d.proc_codigo,
				   a.libex_data_cad,
				   a.usu_codigo,
				   b.usu_nome,
				   b.usu_datanasc,
				   b.usu_mae,
				   b.usu_end_cidade 
			  FROM liberacao_exame a 
			  JOIN usuario b 
				ON a.usu_codigo = b.usu_codigo 
			  JOIN liberacao_exame_lista c
				ON a.libex_codigo = c.libex_codigo
			  JOIN procedimento d
				ON d.proc_codigo = c.proc_codigo
			 WHERE a.libex_codigo = '$libex_codigo'";
	$consulta = pg_query($sql);
	
	
	
	$i = 0;
	while($linha = pg_fetch_array($consulta)){
		$proc[$i] = $linha['proc_codigo']	;
		$lab_codigo = $linha['med_codigo_responsavel'];
		$esp_codigo = $linha['esp_codigo_responsavel'];
		$usu_codigo = $linha['usu_codigo'];
		$i++;
	}
	
	
	$sqlCauculaData = "SELECT (current_date - (SELECT med_validade_lib FROM medico WHERE med_codigo = $lab_codigo)) as data";
	$queryCalculaData =pg_query ($sqlCauculaData);
	$umaLinha = pg_fetch_array($queryCalculaData);
	$validade = $umaLinha['data'];
	
	$sqlVerifica = "SELECT (current_date - 
							(SELECT med_validade_lib 
							   FROM medico 
							  WHERE med_codigo = $lab_codigo)) as data, 
							*
					  FROM liberacao_exame_lista lels
					  JOIN medico med
					    ON med.med_codigo = lels.med_codigo
				     WHERE lels.libex_codigo = $libex_codigo 
					   AND lels.libexl_status = 'A' 
					   AND lels.libexl_dt_cadastro > '$validade'";
	$queryVerifica = pg_query($sqlVerifica);
	$pega = pg_num_rows($queryVerifica);
	
	$sqlStatus = "SELECT * FROM liberacao_exame_lista WHERE libex_codigo = $libex_codigo";
	$queryStatus = pg_query($sqlStatus);
	$linhaStatus = pg_fetch_array($queryStatus);
	
	if($linhaStatus['libexl_status'] == 'I')
	{
		echo "<script>
					alert($sqlVerifica);
					alert('Esta Liberacao ja foi agendada');
					window.close();
			  </script>";
			  exit();	
	}
	if($pega == 0)
	{
		echo "<script>
					alert('Esta Liberacao com prazo de Validade Vencida');
					window.close();
			  </script>";
		$atualiza = "update liberacao_exame 
					 SET libex_status = 'I' 
				   where libex_codigo = $libex_codigo";
		$qryAtualiza = pg_query($atualiza);
			  exit();
			  
	}else{
	echo $usu_codigo;
	db_query('begin');
		$stmt = "INSERT INTO agendamento_exame
					(usu_codigo, agex_data_cad, med_codigo_responsavel, esp_codigo_responsavel)
				VALUES ('$usu_codigo', CURRENT_DATE, '$lab_codigo','$esp_codigo') ";
	db_query($stmt);
		$agex_codigo = db_get('SELECT MAX(agex_codigo) FROM agendamento_exame');
	db_query('commit');
	
	
	  $sqlUni = "SELECT uni_codigo from usuarios where usr_codigo = $id_login";
	  $row = pg_fetch_array(pg_query($sqlUni));
	  
	  
	for($i=0;($i<count($proc));$i++) {
		if(!empty($proc[$i])) {
			$stmw = "INSERT INTO agendamento_exame_lista
							(agex_codigo, 
							 usu_codigo, 
							 med_codigo, 
							 proc_codigo, 
							 agexl_data, 
							 agexl_status,
							 usr_codigo_cad, 
							 agexl_dt_cadastro)
						 VALUES
							('$agex_codigo', 
							 '$usu_codigo', 
							 '$lab_codigo', 
							 '$proc[$i]', 
							 '$data', 
							 'A', 
							 '$id_login', 
							  CURRENT_DATE)";
	
			db_query($stmw);
		}
	}
	 
	 $alteraSql = "update liberacao_exame_lista 
					 SET libexl_status = 'I' 
				   where libex_codigo = $libex_codigo";
	db_query($alteraSql);
	   echo "
		 <br><br><br><br><br><br>
		 <br><br><br><br><br><br>
		 <br><br><br>
		 <center><font size=6 color=green><b>AGENDADO</b></font></center>";
	}
//}
 ?>
<script>
 function pause( iMilliseconds )
 {
   var sDialogScript = 'window.setTimeout( function () { window.close(); }, ' + iMilliseconds + ');';
   window.open("../agendar_exame_print.php?acao=form_imprime&imprimir=a&agex_codigo=<?=$agex_codigo?>&hora=<?=$hora?>&usu_codigo=<?=$usu_codigo?>&lab=<?=$med_codigo?>&id_login=<?=$id_login?>","nv","width=750,height=400");
   window.close();
 }	
pause(1000);
</script>

</body>
