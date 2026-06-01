<?php
session_start();
include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/debug.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
include_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
include_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/__array.php";

$common = new commonClass();
$table = new tableClass();
$form = new classForm();
echo $common->incJquery();
?>

<link rel="stylesheet" href="/WebSocialSaude/lib/themes/base/jquery.ui.all.css">
<link rel="stylesheet" href="/WebSocialSaude/lib/themes/ui-lightness/jquery-ui-1.8.10.custom.css">
<script type="text/javascript" src="/WebSocialComum/library/js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="/WebSocialSaude/lib/ui/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="/WebSocialComum/library/js/jquery.buscar.js"></script>

<?php

 $sql = pg_query("select *from produto where gru_codigo = '99482' order by pro_nome ") or die(pg_last_error());
 
 echo "<table width=100% cellspadding=0 cellspadding=0 border=1>
   <tr>
    <td align=center bgcolor='#e9e9e9' height='30' width=80><b>Codigo</b></td>
    <td bgcolor='#e9e9e9' height='30' width=400><b>Produto</td>
    <td bgcolor='#e9e9e9'><b>Valor Unitario</td>
	<tr><form method=post action=atualiza_preco.php>
	<input type=hidden name=acao value=ok>";
 while($rr = pg_fetch_array($sql)) {
	 $pr = pg_fetch_array(pg_query("select *from itens_movimento where pro_codigo = $rr[pro_codigo]"));
   echo "<input type=hidden name=pro_codigo[] value=$rr[pro_codigo]>
   <tr>
    <td height='30' align=center bgcolor='#f9f9f9'>$rr[pro_codigo]</td>
    <td>$rr[pro_nome]</td>
    <td width=100><input type=text name=ite_vlrunit[] value='".$pr['ite_custo_medio']."'></td>
	<tr>";
}
echo "</table><center><input type=submit value='[ cadastrar precos ]' class=box style='width:200px;height:50px'></center></form><br><br>"; 
 if($_REQUEST['acao']=="ok") {
	 for($i=0;$i<count($ite_vlrunit);$i++) {
		 if($ite_vlrunit[$i]=="") { $vlr = '0.00'; } else { $vlr = $ite_vlrunit[$i]; }
		$q .= "update itens_movimento set ite_custo_medio='".$vlr."' where pro_codigo = ".$pro_codigo[$i].";";
	 }
	 $sql = pg_query($q) or die(pg_last_error());
?><script>
alert('Operacao realizada com Sucesso!.');
setTimeout("window.location.href='atualiza_preco.php';",100);
</script><?php			
 }
?>