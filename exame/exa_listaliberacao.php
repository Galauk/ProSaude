<?
   	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	require_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";
	verauth($id_login);
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
<script language="JavaScript" src="<?=$_SESSION[linkroot].$_SESSION[comum]?>library/js/atalhos.js"></script>
<script language="JavaScript" type="text/javascript" src="<?=$_SESSION[linkroot].$_SESSION[comum]?>library/js/funcoes.js"></script>
<script language="JavaScript" type="text/javascript" src="../g_ajax.js"></script>
<script language="JavaScript" type="text/javascript" src="<?=$_SESSION[linkroot].$_SESSION[comum]?>library/js/ajax_motor.js"></script>
<script type="text/javascript" src="exa_agendamento.js.php?id_login=<?=$id_login?>"></script>
<script language="JavaScript" type="text/javascript" src="procedimento.js"></script>
<script>
	shortcut.add("Esc",function()
	{
		window.close();
	}
	);
</script>
<body>
<?
$proc_codigo = $_GET['proc_codigo'];
$proc = explode(",",$proc_codigo);
echo "<pre>".print_r($_GET)."</pre>";
   $data_atual = date("Y-m-d 00:00:00");
   $sq = pg_query("select *from liberacao_exame where usu_codigo = '$_GET[usu_codigo]' and 
	 	   libex_data_cad = '$data_atual' and med_codigo_responsavel = '$lab_codigo'");
   if(pg_num_rows($sq)>=1) {
	echo "<script> 
		alert('ERRO: Ja existe liberacao com estas configuracoes para este Paciente!');
		window.close();
	      </script>";
	exit;
   }

   if(pg_num_rows($sq)>=1) {
for($i=0;($i<count($proc));$i++) {
   $stmt_int = "SELECT COUNT(libexl_codigo), intervalo
                                FROM liberacao_exame_lista,
                                        ( SELECT COALESCE(proc_intervalo_min,0) AS intervalo FROM procedimento
                                          WHERE proc_codigo = {$proc[$i]} ) AS teste
                                WHERE libexl_data between '$graex_data'::date - intervalo AND '$graex_data'::date + intervalo AND
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
           $stmt = "INSERT INTO liberacao_exame
                        (usu_codigo, libex_data_cad, med_codigo_responsavel, esp_codigo_responsavel)
                    VALUES ('$usu_codigo', CURRENT_DATE, '$lab_codigo','0') ";
db_query($stmt);
           $agex_codigo = db_get('SELECT MAX(libex_codigo) FROM liberacao_exame');
db_query('commit');

for($i=0;($i<count($proc));$i++) {
 //if(!empty($proc[$i])) {
     $stmw = "INSERT INTO liberacao_exame_lista
                        (libex_codigo, usu_codigo, med_codigo, proc_codigo, libexl_status,
                                usr_codigo_cad, libexl_dt_cadastro, uni_codigo)
                        VALUES('$agex_codigo', '$usu_codigo', '$lab_codigo', '$proc[$i]', 'A', '$id_login', CURRENT_DATE, ".($uni_codigo != "" ? "null" : "$uni_codigo")."";

db_query($stmw);
  // }
 }
 	 $alteraSql = "update liberacao_exame 
					 SET libex_status = 'I' 
				   where libex_codigo = $agex_codigo";
	db_query($alteraSql);
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
   window.open("../liberacao_print_exames.php?acao=form_imprime&imprimir=a&libex_codigo=<?=$agex_codigo?>&usu_codigo=<?=$usu_codigo?>&lab=<?=$med_codigo?>&id_login=<?=$id_login?>","nv","width=750,height=400");
   window.close();
 }	
pause(1000);
</script>
</body>
