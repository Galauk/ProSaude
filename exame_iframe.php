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
			 "WHERE usu_codigo='$_GET[usu_codigo]'";
			 
	$query = pg_query($sql_f);

		echo "<SCRIPT LANGUAGE=\"JavaScript\">			  setTimeout(\"location='$PHP_SELF?acao=&acao&id_dia=$id_dia&med_codigo=$med_codigo&proc_codigo=$proc_codigo&usu_codigo=$_GET[usu_codigo]&id_login=$id_login'\", 0);
		  </SCRIPT>";			

}else if($acao=="transferencia"){

	$status = 'T';

    reglog($id_login,"Transferencia de Exame.: $agexl_codigo");
	$sql_t = "UPDATE agendamento_exame_lista ".
			 "SET agexl_status='$status' ".
			 "WHERE usu_codigo='$_GET[usu_codigo]'";
			 
	$query = pg_query($sql_t);

	echo "<SCRIPT LANGUAGE=\"JavaScript\">
			  setTimeout(\"location='$PHP_SELF?acao=&acao&id_dia=$id_dia&med_codigo=$med_codigo&proc_codigo=$proc_codigo&usu_codigo=$_GET[usu_codigo]&id_login=$id_login'\", 0);
		  </SCRIPT>";			

}else if($acao=="recepcionado"){

	$status = 'R';

    reglog($id_login,"Exame Recepcionado.: $agexl_codigo");
	$sql_r = "UPDATE agendamento_exame_lista ".
			 "SET agexl_status='$status' ".
			 "WHERE usu_codigo='$_GET[usu_codigo]'";
			 
	$query = pg_query($sql_r);

	echo "<SCRIPT LANGUAGE=\"JavaScript\">
			  setTimeout(\"location='$PHP_SELF?acao=&acao&id_dia=$id_dia&med_codigo=$med_codigo&proc_codigo=$proc_codigo&usu_codigo=$_GET[usu_codigo]&id_login=$id_login'\", 0);
		  </SCRIPT>";			

}

//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>
echo "<table width=100% higth=80%  cellspacing=1 cellpadding=4 border=0>\n
		<tr bgcolor=CCCCCC>\n
			 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>\n
			 <font color=red>Nome do Paciente</font></td>
			 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>\n
			 <font color=red>Data</font></td>
			 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>\n
			 <font color=red>Status</font></td>
	  	     <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>&nbsp;</td>
		</tr>\n";
	
//$i=0;

//$sql_ = pg_query("select usu_nome from usuario where usu_codigo=".intval($row_[usu_codigo])." order by usu_nome") or die (pg_last_error());

$Data = explode("/",$id_dia);

$sql = 	"select ag.*, u.usu_nome, to_char(ag.agexl_data,'dd-mm-YYYY') as data from agendamento_exame_lista as ag, usuario as u ".
		"where ag.agexl_data = '$Data[2]-$Data[1]-$Data[0]' ".
		"and ag.med_codigo = '$med_codigo' ".
//        "and ag.proc_codigo = '$proc_codigo' ".
		"and u.usu_codigo = ag.usu_codigo ".
		"order by u.usu_nome ";
//		order by to_char(gex_periodo,'YYYY-mm-dd') desc


/*
$sql = "select distinct agex_codigo,(select usu_nome from usuario where usu_codigo = ag.usu_codigo) as usu_nome,
	ag.agex_codigo,ag.usu_codigo,ag.med_codigo,ag.agexl_data,ag.agexl_hora,ag.usr_codigo_alt,
	ag.usr_codigo_cad,ag.agexl_dt_cadastro,ag.agexl_dt_atualizacao
        from agendamento_exame_lista as ag
	where ag.agexl_data = '$Data[2]-$Data[1]-$Data[0]' 
	      and ag.med_codigo = '$med_codigo'";
*/
//echo $sql;
$query = pg_query($sql);
//$row_ = pg_fetch_array($query);


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
		
		<td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>
		<a href='#' OnClick=\"window.open('exame_detalhado.php?usu_codigo=$row_[usu_codigo]&data=$row_[data]&med_codigo=$med_codigo',null,'height=300,width=900,status=yes,toolbar=no,menubar=no,location=no,scrollbar=yes');\">
		$row_[usu_nome]</a>
		</td>\n
		<td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$row_[data]</td>\n
		<td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>$status_agendamento</td>\n";
	
			// botoes
	echo "<td align=center style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>
			<a href='$PHP_SELF?id_login=$id_login&acao=falta&up=ok&med_codigo=$med_codigo&id_dia=$id_dia&usu_codigo=$usu_cod'>
			<img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/btn_falta.gif border=0></a>&nbsp;
			<a href='$PHP_SELF?id_login=$id_login&acao=transferencia&med_codigo=$med_codigo&id_dia=$id_dia&usu_codigo=$usu_cod'>
			<img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/btn_transferencia.gif border=0></a>&nbsp;
			<a href='$PHP_SELF?id_login=$id_login&acao=recepcionado&med_codigo=$med_codigo&id_dia=$id_dia&usu_codigo=$usu_cod'>
			<img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/btn_recepcionado.gif border=0></a>&nbsp;</td>
		  </tr>"; 
		  
	
}

echo "</form>\n
      </table>\n"; 
	  

?>
