<?
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	require_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";
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
        shortcut.add("Ctrl+F12",function()
{
        document.addexame.submit();
});
</script>
<body onload = "carrega();">
<?
if($acao=="") {
    echo "<fieldset>
	<legend>DESCREVA A QUANTIDADE E A QUANTIDADE DIARIA</legend>";
    echo "<table width=100% cellspacing=0 cellpadding=5 border=0>";
    echo "<form name='addexame' method=post action=$PHP_SELF>
        <input type=hidden name=acao value=addexame>
        <input type=hidden name=id_login value=$id_login>
        <input type=hidden name=usu_codigo value=$usu_codigo>
        <input type=hidden name=med_codigo value=$med_codigo>
        <input type=hidden name=esp_codigo value=$esp_codigo>
        ";
    $hidden_proc .= "<input type=hidden name=proc_codigo value=";
    $proc = explode(",",$proc_codigo);
    $sql = "select *from produtos";
    for($i=0;($i<count($proc));$i++) {
        if(!empty($proc[$i])) {
            $hidden_proc .= "$proc[$i]";
            echo "$proc[$i]<br>";

        }
    }
    $hidden_proc .= ">";
    echo $hidden_proc;
/*
    $query = pg_query($sql);
    $i=0;

    while($row = pg_fetch_array($query)) {
     if($row[disponivel]==0) { 
        $i++;
       if($i=="1") {
	$dataoff .= " and graex_data != '$row[graex_data]' ";
       } else {
	$dataoff .= " and graex_data != '$row[graex_data]' ";
       }
     }
}
    $q = "select distinct(graex_data),to_char(graex_data, 'DD/MM/YYYY') as dataf
	from grade_exame as a 
	where med_codigo = $lab_codigo AND (";
for($i=0;($i<count($proc));$i++) {
 if(!empty($proc[$i])) {

      $q3 .= "proc_codigo = '$proc[$i]' OR ";
  }
}
    $q .= substr($q3,"0","-3");
    $q .= ") ";	
    $q .= "and graex_data >= CURRENT_DATE ";
if(!empty($dataoff)) {
 $q .=	$dataoff;
}
 $q .=	"order by graex_data";
 $qq = pg_query($q);
  $j=0;
//echo $q; 
 while($rr = pg_fetch_array($qq)) {
        $j++;
       echo "<tr>
	      <td width=10%><input id='campo_".$j."'type=radio name=graex_data value=$rr[graex_data]></td>
	      <td><font size=3>$rr[dataf] $rr[proc_codigo]</font></td>
	     </tr>";
 }

   echo "</table>
	</fieldset></form>";
}

if($acao=="addexame") {
   $proc = explode(",",$proc_codigo);
   $data_atual = date("Y-m-d 00:00:00");
   $sq = pg_query("select *from agendamento_exame where usu_codigo = '$usu_codigo' and 
	 	   agex_data_cad = '$data_atual' and med_codigo_responsavel = '$lab_codigo'");
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
                        (usu_codigo, agex_data_cad, med_codigo_responsavel, esp_codigo_responsavel, agt_codigo)
                    VALUES ('$usu_codigo', CURRENT_DATE, '$lab_codigo','$esp_codigo', '$agt_codigo') ";
db_query($stmt);
           $agex_codigo = db_get('SELECT MAX(agex_codigo) FROM agendamento_exame');
db_query('commit');

for($i=0;($i<count($proc));$i++) {
 if(!empty($proc[$i])) {
     $stmw = "INSERT INTO agendamento_exame_lista
                        (agex_codigo, usu_codigo, med_codigo, proc_codigo, agexl_data, agexl_status,
                                usr_codigo_cad, agexl_dt_cadastro )
                        VALUES('$agex_codigo', '$usu_codigo', '$lab_codigo', '$proc[$i]', '$graex_data', 'A', $id_login, CURRENT_DATE)";

db_query($stmw);
   }
 }
   echo "
	 <br><br><br><br><br><br>
	 <br><br><br><br><br><br>
	 <br><br><br>
	 <center><font size=6 color=green><b>AGENDADO</b></font></center>";
*/
 ?>
<!--<script>
 function pause( iMilliseconds )
 {
   var sDialogScript = 'window.setTimeout( function () { window.close(); }, ' + iMilliseconds + ');';
   window.open("../agendar_exame_print.php?acao=form_imprime&imprimir=a&agex_codigo=<?=$agex_codigo?>&usu_codigo=<?=$usu_codigo?>&lab=<?=$med_codigo?>","nv","width=750,height=400");
   window.close();
 }	
pause(1000);
</script>-->
<?
}



?>
</body>
