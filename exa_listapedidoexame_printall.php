<?
session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
verauth($id_login);
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
cabecario( $hotkey = true);
require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
$common = new commonClass();
echo $common->incJquery();
?>
<script>
	function imprimir(){
		camposMarcados = new Array();
		$("input[type=checkbox]:checked").each(function(){
		    camposMarcados.push($(this).val());
		});
		var link = "./zf/laboratorio/laudos/imprimir/proc_codigos/"+camposMarcados+"/age_codigo/"+<?=$age_codigo?>;
		//var link = "./zf/laboratorio/laudos/";
		window.open(link, "name", "scrollbars=1,height=800,width=900",'width=850,height=700');
	}
</script>
<?php 
reglog($id_login,"Acessando Digitacao do Resultado");

if(empty($acao)) {
$sql = "SELECT 
	i.proc_codigo as proc_c, 
	proc_nome, 
	c.med_codigo medico, 
	a.usu_codigo, 
	u.usu_nome, 
	ai.agei_data, 
	ai.agei_status, 
	a.age_codigo, 
	a.med_codigo, 
	a.usr_codigo_medico, 
	ai.agei_codigo, 
	TO_CHAR(col.col_data_coleta,'DD/MM/YYYY') as col_data_coleta2, 
	* 
	FROM medico m 
	JOIN convenio c 
	  ON c.med_codigo = m.med_codigo 
	JOIN convenio_itens i 
	  ON i.conv_codigo = c.conv_codigo 
	JOIN agenda_itens ai 
	  ON ai.coni_codigo = i.coni_codigo 
	JOIN agenda a 
	  ON a.age_codigo = ai.age_codigo 
	JOIN usuario u 
	  ON u.usu_codigo = a.usu_codigo 
	JOIN procedimento proc 
	  ON proc.proc_codigo = i.proc_codigo 
	JOIN coleta col 
	  ON col.agei_codigo = ai.agei_codigo 
	JOIN tipodeexame as tp 
	  ON tp.proc_codigo = i.proc_codigo 
	WHERE ai.agei_codigo = (select DISTINCT resu.agei_codigo from resultadoexame  as resu where ai.agei_codigo = resu.agei_codigo)   
	AND a.age_codigo = $age_codigo
    and ai.usr_codigo_bioquimico is not null	
ORDER BY proc_nome";
//echo $sql;
$querySql = pg_query($sql);
//echo $sql;
	//$sql = pg_query("select *from materialdeanalise as mlz left join itensdoexame as itx on itx.itx_codigo = mlz.itx_codigo left join procedimento as proc on proc.proc_codigo = itx.proc_codigo left join tipodeexame as tp on tp.proc_codigo = itx.proc_codigo join resultadoexame as r on itx.itx_codigo = r.itx_codigo where mlz.cad_exame = $cad_exame");

echo "<table width='100%' align='center' cellspacing='2' cellpadding='4' border='0' class='lista'>
       <tr bgcolor=FFFFFF>
	  <td>Cod</td>
	  <td>Exame</td>
	  <td>Dt.Coleta</td>
	  <td>&nbsp;</td>
	</tr>";
while($row=pg_fetch_array($querySql)) {
	$res = pg_query("select *from resultadoexame where agei_codigo= '$row[agei_codigo]' and proc_codigo = '$row[proc_codigo]'");
	$tp = pg_num_rows($res);
	$datadacoleta =$row[col_data_coleta2];
		//echo $row[res_codigo];
		 echo "<tr>
			  <td align=center>$row[proc_codigo]</td>
			  <td width=90%>$row[proc_nome]</td>
			  <td width=90%>$datadacoleta</td>
			  <input type=hidden id=teste value=1>
			  <input type=hidden name=datacoleta id=datacoleta value=$row[col_data_coleta]>
			  <input type=hidden name=p_codigo[] id=p_codigo[] value=$row[proc_codigo]>
			  <input type=hidden name=age_codigo id=age_codigo value=$row[age_codigo]>
			  <input type=hidden name=proc_nome value=$row[proc_nome]>
			  <td><input type=checkbox name='proc_codigo[$row[proc_codigo]]' value=$row[proc_codigo]></td>
			</tr>";
 echo "</tr>";
}
 echo "</table>";
 echo "<table width=100% cellspacing=0 cellpadding=0 border=0>
	<tr>
	 <td align=center><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/imprimir.jpg onclick=\"imprimir()\"></td>
	 <td>";
 
 echo"
	 </td>
	</tr>
	</table>";


}	

?>
