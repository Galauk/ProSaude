<?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>

	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	verauth($id_login);
	cabecario();
//------------------------------------------------------------------>
?><script language="JavaScript" type="text/javascript" src="funcoes.js"></script><?

//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
         reglog($id_login,"Entrando em Nova Data para Manutenção de Médicos");
//------------------------------------------------------------------>
if($acao=="") {
    echo "<br><form method=post action=$PHP_SELF>
	  <input type=hidden name=acao value=newline>
	  <input type=hidden name=id_login value=$id_login>

	  <input type=hidden name=med_codigo value=$med_codigo>
	  <input type=hidden name=uni_codigo value=$uni_codigo>
	  <input type=hidden name=esp_codigo value=$esp_codigo>
	  <input type=hidden name=age_tipo value=$age_tipo>
	  <input type=hidden name=grahora value='$grahora'>
	  <input type=hidden name=age_item value=$age_item>
	  <input type=hidden name=age_tipo value=$age_tipo>
	  <input type=hidden name=gra_obs value=$gra_obs>
	  <table width=100% cellspacing=0 cellpadding=0 border=0>
	   <tr>
	    <td colspan=2><font color=blue><b>Copiar Dados para Data:</b></td>
	   </tr>
	   <tr>
	    <td width=80><input type=text name=newdate class=box size=12 id='data' maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\"></td>
	    <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/add_on.gif></td>
	   </tr>
	  </table></form>";
}

 if($acao=="newline") {
reglog($id_login,"Copiando Data Medico Cod: $med_codigo");
 $Data = explode("/",$newdate);
 $data = "$Data[2]-$Data[1]-$Data[0]";
 $sql = "insert into grade_medico (gra_data,med_codigo,uni_codigo,esp_codigo,gra_tipo,gra_hora_ini,age_item,age_tipo,gra_obs,usr_codigo_cad) values('$data','$med_codigo','$uni_codigo','$esp_codigo','$age_tipo','$grahora','$age_item','$age_tipo','$gra_obs','$id_login')";
 $query = pg_query($sql);
        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  window.close();
              </SCRIPT>";

}


?>
