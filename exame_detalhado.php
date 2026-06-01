<script src="script.js" language="javascript" type="text/javascript"></script>
<?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();

//------------------------------------------------------------------>
		   
if($acao=="falta") {
 
	$status = 'F';
	
    //reglog($id_login,"Cliente Faltou ao Exame.: $agexl_codigo");
	$sql_f = "UPDATE agendamento_exame_lista ".
			 "SET agexl_status='$status' ".
			 "WHERE agexl_codigo='$_GET[agexl_codigo]'";
			 
	$query = pg_query($sql_f);
	//echo $sql_f;
	
		echo "<SCRIPT LANGUAGE=\"JavaScript\">			  setTimeout(\"location='$PHP_SELF?acao=&acao&usu_codigo=$_GET[usu_codigo]&data=$_GET[data]&agexl_codigo=$_GET[agexl_codigo]&med_codigo=$_GET[med_codigo]&id_login=$id_login'\", 0);
		  </SCRIPT>";			

}else if($acao=="transferencia"){

	$status = 'T';

    reglog($id_login,"Transferencia de Exame.: $agexl_codigo");
	$sql_t = "UPDATE agendamento_exame_lista ".
			 "SET agexl_status='$status' ".
			 "WHERE agexl_codigo='$_GET[agexl_codigo]'";
			 
	$query = pg_query($sql_t);
	//echo $sql_t;


	echo "<SCRIPT LANGUAGE=\"JavaScript\">
setTimeout(\"location='$PHP_SELF?acao=&acao&usu_codigo=$_GET[usu_codigo]&data=$_GET[data]&agexl_codigo=$_GET[agexl_codigo]&med_codigo=$_GET[med_codigo]&id_login=$id_login'\", 0);
		  </SCRIPT>";			

}else if($acao=="recepcionado"){

	$status = 'R';

    reglog($id_login,"Exame Recepcionado.: $agexl_codigo");
	$sql_r = "UPDATE agendamento_exame_lista ".
			 "SET agexl_status='$status' ".
			 "WHERE agexl_codigo='$_GET[agexl_codigo]'";
			 
	$query = pg_query($sql_r);
	//echo $sql_r;
	

	echo "<SCRIPT LANGUAGE=\"JavaScript\">
setTimeout(\"location='$PHP_SELF?acao=&acao&usu_codigo=$_GET[usu_codigo]&data=$_GET[data]&agexl_codigo=$_GET[agexl_codigo]&med_codigo=$_GET[med_codigo]&id_login=$id_login'\", 0);
		  </SCRIPT>";			

}

//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>
echo "<div style='font-weight:bold;'  id='upd'><label style='font-weight:bold;color:#10d' id='usr'></label></div>"; 
echo "<table width=100% higth=80%  cellspacing=1 cellpadding=4 border=0>\n
		<tr bgcolor=CCCCCC>\n
			 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>\n
			 <font color=red>Pocedimento</font></td>
			 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>\n
			 <font color=red>Data</font></td>
			 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>\n
			 <font color=red>Status</font></td>
	  	     <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>&nbsp;</td>
		</tr>\n";
	
$Data = explode("/",$id_dia);

$sql =	"select ag.agexl_codigo, ag.agexl_status, ag.agex_codigo, ag.usu_codigo, ag.med_codigo, ag.proc_codigo, p.proc_nome, to_char(ag.agexl_data,'dd-mm-YYYY') as data ".
		"from agendamento_exame_lista as ag, agendamento_exame as a, procedimento as p ".
		"where ag.agex_codigo=a.agex_codigo ".
		"and ag.proc_codigo=p.proc_codigo ". 
		"and ag.usu_codigo = '$_GET[usu_codigo]' ". 
		"and ag.agexl_data = '$_GET[data]' ".
		"and ag.med_codigo = '$_GET[med_codigo]' ";

$query = pg_query($sql);

echo "<form name='ff' method='get' action='$PHP_SELF'>\n"; 

   while($row_=pg_fetch_array($query)) {

	$usu_cod = $row_['usu_codigo'];

	if ($row_['agexl_status'] == 'A'){
		$status_agendamento = '<span style="color: #000000;font-weight: bold;">Agendado</span>';
	}else if ($row_['agexl_status'] == 'F'){
		$status_agendamento = '<span style="color: #FF0000;font-weight: bold;">Falta</span>';
	}else if ($row_['agexl_status'] == 'T'){
		$status_agendamento = '<span style="color: #FF9900;font-weight: bold;">Trasferencia</span>';
	}else if ($row_['agexl_status'] == 'R'){
		$status_agendamento = '<span style="color: #00FF00;font-weight: bold;">Recepcionado</span>';
	}
	
	echo "<tr bgcolor=FFFFFF>
		
			<td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$row_[proc_nome]</td>\n
			<td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$row_[data]</td>\n
			<td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$status_agendamento</td>\n";
	
			// botoes
	echo "<td align=center style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>
			<a href='$PHP_SELF?id_login=$id_login&acao=falta&agexl_codigo=$row_[agexl_codigo]&usu_codigo=$_GET[usu_codigo]&data=$_GET[data]&med_codigo=$_GET[med_codigo]'>
			<img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/btn_falta.gif border=0></a>&nbsp;
			<a href='$PHP_SELF?id_login=$id_login&acao=transferencia&agexl_codigo=$row_[agexl_codigo]&usu_codigo=$_GET[usu_codigo]&data=$_GET[data]&med_codigo=$_GET[med_codigo]'>
			<img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/btn_transferencia.gif border=0></a>&nbsp;
			<a href='$PHP_SELF?id_login=$id_login&acao=recepcionado&agexl_codigo=$row_[agexl_codigo]&usu_codigo=$_GET[usu_codigo]&data=$_GET[data]&med_codigo=$_GET[med_codigo]'>
			<img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/btn_recepcionado.gif border=0></a>&nbsp;</td>
		  </tr>"; 
	
}

echo "</form>\n
      </table>\n"; 

?>
