<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<script>

  
    function ajaxInit() {
        var req;

        try {
            req = new ActiveXObject("Microsoft.XMLHTTP");
        } catch(e) {
        try {
 
        req = new ActiveXObject("Msxml2.XMLHTTP");
 
        } catch(ex) {
 
        try {
 
        req = new XMLHttpRequest();
 
        } catch(exc) {
 
 alert("Esse browser não tem recursos para uso do Ajax");
 
    req = null;
 
        }
 
    }
 
 }

    return req;

}


function changeLocation(menuObj)
{
   var i = menuObj.selectedIndex;

   if(i > 0)
   {
      window.location = menuObj.options[i].value;
   }
}
</script>
<?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
//------------------------------------------------------------------>




//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>

reglog($id_login,"Acessando Manutencao de Agentes");

if($act=="newdate") {
	reglog($id_login,"Adicionando nova data para a Manutencao do Exame Cod: $proc_codigo");
	$sql=pg_query("select *from procedimento where proc_exame='S'");
	while($agt=pg_fetch_array($sql)) {
  		$q = "insert into grade_exame ( " .
            "med_codigo, " .
            "gex_qtde, " .
            "proc_codigo, " .
            "gex_periodo, " .
            "age_item " .
            ") values ( " .
            "'$med_codigo', " .
            "'0', " .
            "'$esp_codigo', " .
            "'$agt[agt_codigo]', " .
            "'$nvdata', " .
            "'$agt_item' " .
            ")";
  		$rq = pg_query($q);
  }
  	/* ???
     echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='$PHP_SELF?id_login=$id_login&med_codigo=$med_codigo&esp_codigo=$esp_codigo&agt_item=$agt_item'\", 0);
           </SCRIPT>";
	*/
	     
	     echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='$PHP_SELF?id_login=$id_login&med_codigo=$med_codigo&gex_tipo=$gex_tipo'\", 0);
           </SCRIPT>";

} //if($act=="newdate") {

//if(empty($acao)) {
if(empty($act)) {

/*
  echo "<table width=733 align=center cellspacing=0 cellpadding=0 border=0>\n
         <tr>\n
          <td>\n
           <fieldset>\n
            <legend>Opções</legend>\n
             <table width=100% align=center cellspacing=3 cellpadding=0 border=0>\n
              <tr>\n
               <td width=79><a href=agendamento.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>\n
               <td>&nbsp;</td>\n
              </tr>\n
             </table>\n
           </fieldset>\n
          </td>\n
         </tr>\n
        </table>\n
        <br>\n";
*/
//
//-> Botoes

 echo "<table width=733 align=center cellspacing=2 cellpadding=4 border=0 style='border-top:1px solid;border-left:1px solid;border-right:1px solid;border-bottom:1px solid;border-color:909090'>\n
         <tr>\n
          <td>\n
             <table width=100% align=center cellspacing=3 cellpadding=0 border=0>\n
              <tr>\n
		<td width=165 align=right>Laboratório</td>\n
		<td><select name=med_codigo class=boxr onChange=\"javascript:changeLocation(this)\">\n"; //onChange=\"javascript:changeLocation(this)\"
	     echo "<option>...</option>\n";
	$sql = pg_query("select *from medico where prestador_servico='S' order by med_nome");
	  while($med=pg_fetch_array($sql)) {
	   echo ($med_codigo==$med['med_codigo'])?
	   		"<option value='manutencaoexames.php?id_login=$id_login&med_codigo=$med[med_codigo]&gex_tipo=$med[gex_tipo]' selected>$med[med_nome]</option>":
	   		"<option value='manutencaoexames.php?id_login=$id_login&med_codigo=$med[med_codigo]&gex_tipo=$med[gex_tipo]'>$med[med_nome]</option>\n";
	  }
	echo "</select></td>\n
              </tr>\n
             </table>\n
             <table width=100% align=center cellspacing=3 cellpadding=0 border=0>\n
              <tr>\n
		<td width=142>&nbsp;</td>\n
		<td width=20 align=right>Data</td>\n
		<td width=15><select name='gex_periodo' class='boxl' onChange=\"javascript:changeLocation(this)\">\n";
		echo "<option selected>...</option>\n"; 

   $query = pg_query("select to_char(gex_periodo,'DD/MM/YYYY') as gex_periodo,gex_periodo as gex_periodo2 from grade_exame where med_codigo='$med_codigo' group by gex_periodo") or print (pg_last_error());

while($dt=pg_fetch_array($query)) {
echo ($gex_periodo==$dt[gex_periodo2])?"<option value=manutencaoexames.php?id_login=$id_login&gex_periodo=$dt[gex_periodo2]&med_codigo=$med_codigo&select=periodo selected>$dt[gex_periodo]</option>":"<option value=manutencaoexames.php?id_login=$id_login&gex_periodo=$dt[gex_periodo2]&med_codigo=$med_codigo&select=periodo>$dt[grm_periodo]</option>\n";
}
echo "</select>\n";
   $sql = pg_query ("select distinct gex_periodo from grade_exame where med_codigo = '$med_codigo' and gex_periodo='$gex_periodo' order by gex_periodo");
    while ($linha = pg_fetch_row($sql)) {
        $tmp = mktime("12", "0", "0", substr($linha[0], 5, 2), substr($linha[0], 8, 2), substr($linha[0], 0, 4));
        $per = date("d/m/Y", $tmp + (date("t", $tmp) - 1) * 86400);
        $periodo[date("Y-m-d", $tmp)] = $per;
    }
	  //echo "</td><form method=post action=manutencaoagentes.php>\n
	  echo "</td><form method=post action='manutencaoexames.php?id_login=$id_login&med_codigo=$med_codigo&gex_tipo=$gex_tipo'>\n
		<input type=hidden name=act value=newdate>\n
		<input type=hidden name=med_codigo value=$med_codigo>\n
		<input type=hidden name=gex_periodo value=$gex_periodo>\n
		<input type=hidden name=id_login value=$id_login>\n
	        <td width=60><input type=text size=12 class=boxl value='$per' readonly></td>\n
		<td width=10 align=right>&nbsp;</td>\n
		<td width=67>Novo Periodo: </td>\n
		<td width=70><input type=text name=nvdata size='12' class='boxl' id='data' maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\"></td>\n
		<td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/add_on.gif></td>\n
	      </tr></form>\n
	     </table>\n
          </td>\n
         </tr>\n
        </table>\n";
/*
if($gex_periodo!="") {
$grmdiv = explode("-",$gex_periodo);
$perdiv = explode("/",$per);
  $qt = pg_query("select *from grade_exame where gex_periodo >= '$gex_periodo' and gex_periodo <= '$grmdiv[0]-$grmdiv[1]-30' and med_codigo='$med_codigo' and esp_codigo='$esp_codigo'");
  $qtd_rows=pg_num_rows($qt);
  $qtd_search=($grmdiv[2]-$qtd_rows);
//echo $qtd_search;
for($i=$grmdiv[2];$i<=30;$i++ ) {
    $qq = pg_query("select *from grade_medico where gra_data = '$grmdiv[0]-$grmdiv[1]-$i' and med_codigo='$med_codigo' and esp_codigo='$esp_codigo'");
  if(pg_num_rows($qq)=="0") {
     $sql = "insert into grade_medico (gra_data,med_codigo,uni_codigo,esp_codigo,gra_tipo,gra_hora_ini,age_item,age_tipo) values ('$grmdiv[0]-$grmdiv[1]-$i','$med_codigo','$uni_codigo','$esp_codigo','PC','8:00','CB','PC')";
 } else {
     $sql = " ";
 }
 $exec = pg_query($sql);
 vSQL($sql,"1");
}

for($i=1;$i<=$perdiv[0];$i++ ) {
    $qq = pg_query("select *from grade_medico where gra_data = '$perdiv[2]-$perdiv[1]-$i' and med_codigo='$med_codigo' and esp_codigo='$esp_codigo'");
  if(pg_num_rows($qq)=="0") {
    $sql = "insert into grade_medico (gra_data,med_codigo,uni_codigo,esp_codigo,gra_tipo,gra_hora_ini,age_item,age_tipo) values ('$perdiv[2]-$perdiv[1]-$i','$med_codigo','$uni_codigo','$esp_codigo','PC','8:00','CB','PC')";
 } else {
     $sql = " ";
 }
 $exec = pg_query($sql);
 vSQL($sql,"1");
}
}
*/
  echo "<table width=760 cellspacing=0 cellpadding=0 border=0 align=center>\n
         <tr>\n
          <td align=center>\n
           <iframe name=frameprincipal src='manutencao_exame_iframe.php?id_login=$id_login&periodo=$per&grm_periodo=$grm_periodo&med_codigo=$med_codigo&gex_tipo=$gex_tipo' frameborder=no marginheight=0 marginwidth=0 scrolling=yes width=734 height=320>\n</iframe>\n
	  </td>\n
	 </tr>\n
	</table>\n";
}
?>
