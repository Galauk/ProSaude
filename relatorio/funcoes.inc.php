<?
//
// -> Arquivos de Fun��es
//

//------------------------------------------------------------------>
//-> Cabecario das telas
//------------------------------------------------------------------>
function cabecario() {
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
   echo "<body bgcolor='#F4F2E7'>
	        <link href='estilo.css' rel='stylesheet' type='text/css'>";

//Criar o objeto XMLHttpRequest - para possibilitar o uso do AJAX
?>

<script>
function createRequestObject()
{
   var ro;
   var browser = navigator.appName;
   if (browser == "Microsoft Internet Explorer"){
      ro = new ActiveXObject("Microsoft.XMLHTTP");
   } else {
        ro = new XMLHttpRequest();
   }
   return ro
}

var http = createRequestObject();


</script>

<?
} //-> FECHAMENTO DA FUNCAO CABECARIO

//------------------------------------------------------------------>
//-> Mensagens dos registros
//------------------------------------------------------------------>

function msg($id_login,$acao,$sql) {
  //var_dump($acao);
$GetNameFile=str_replace("/","",$_SERVER["SCRIPT_NAME"]);
//var_dump($GetNameFile);
     switch ($acao) {
              case "add":
	   	$resp_ok = "<font size=2 color=green><b>INCLUSO com Sucesso</b></font>";
	   	$resp_erno = "<font size=2 color=red><b>ERRO ao INCLUIR</b></font>";
	      break;
              case "edit":
		$resp_ok = "<font size=2 color=green><b>EDITADO com Sucesso</b></font>";
		$resp_erno = "<font size=2 color=red><b>ERRO ao EDITAR</b></font>";
	      break;
              case "del":
	   	$resp_ok = "<font size=2 color=green><b>APAGADO com Sucesso</b></font>";
		$resp_erno = "<font size=2 color=red><b>ERRO ao APAGAR</b></font>";
	      break;
}

     if($sql) {
	  echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
	        <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
	         <tr bgcolor=f9f9f9>
	           <td align=center>$resp_ok</td>
	         </tr>
	        </table><br>";
	echo "<SCRIPT LANGUAGE=\"JavaScript\">
	          setTimeout(\"location='$GetNameFile?id_login=$id_login'\", 2000);
	      </SCRIPT>";
     } else {
	  echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
	        <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
	         <tr bgcolor=f9f9f9>
	           <td align=center>$resp_erno</td>
	         </tr>
	        </table><br>";
	echo "<SCRIPT LANGUAGE=\"JavaScript\">
	          setTimeout(\"location='$GetNameFile?id_login=$id_login'\", 2000);
	      </SCRIPT>";
     }
}

//------------------------------------------------------------------>
//-> Cabecario de Relatorios
//------------------------------------------------------------------>
function cabecario_rel($titulo,$dt_i,$dt_f,$unidade) {

	include_once $_SESSION[root].$_SESSION[comum]."db.inc.php";
   echo "<body bgcolor=FFFFFF topmargin=0 leftmargin=0>
	  <script>
	   function imprimir() {
	   window.print();
	</script>";
 echo "<table width=100% cellspacing=3 cellpadding=0 border=0 bgcolor=eeeeee height=40>
        <tr>
         <td width=90%><font size=3><b>".strtoupper($titulo)."</b></font></td>
         <td><a href='#' OnClick='imprimir()'><img src=../imgs/print_on.jpg border=0></a></td>
        </tr>
        </table><br>";
echo "<table width=100% cellspacing=0 cellpadding=2 border=0 align=center>
		<tr>
	     <td width=200><font size=2 face=courier>GEST�O P�BLICA DE SA�DE</font></td>
	     <td><font size=2 face=courier>".date("d/m/Y h:i:s")."</font></td>
	    </tr>
	    <tr>
	     <td colspan=2><font size=2 face=courier>".strtoupper($titulo)."</font></td>
	    </tr>
	    <tr>
	     <td colspan=2><font size=2 face=courier>PERIODO: $dt_i A $dt_f</font></td>
	    </tr>
	    <tr>
	     <td colspan=2><font size=2 face=courier>UNIDADE: $unidade</font></td>
	    </tr>
	   </table>";

}

//------------------------------------------------------------------>
//-> Formatacao de Valor
//------------------------------------------------------------------>
 //rotina que formata o valor para sair com duas casas decimais 
 function formata_valor($valor) {
   $sep_valor=explode(".",$valor);
   if($sep_valor[1]=="") { $zero_2="00"; }
   if(strlen($sep_valor[1])=="1") { $zero_2="$sep_valor[1]0"; }
   if(strlen($sep_valor[1])>="2") { $zero_2=substr($sep_valor[1],0,2); }
   return "$sep_valor[0].$zero_2";
 }
 //rotina que formata o valor para sair com quatro casas decimais 
 function formata_valor4($valor) {
   $sep_valor=explode(".",$valor);
   if($sep_valor[1]=="") { $zero_2="0000"; }
//   if(strlen($sep_valor[1])=="1") { $zero_2="$sep_valor[1]000"; }
//   if(strlen($sep_valor[1])>="2") { $zero_2=substr($sep_valor[1],0,2); }
   $zero_2=$sep_valor[1];
   return "$sep_valor[0].$zero_2";
 }
 //rotina que formata o valor para sair inteiro, sem casas decimais
 function formata_valor0($valor) {
   $sep_valor=explode(".",$valor);
   if($sep_valor[1]=="") { $zero_2="0000"; }
   if(strlen($sep_valor[1])=="1") { $zero_2="$sep_valor[1]0"; }
   if(strlen($sep_valor[1])>="2") { $zero_2=substr($sep_valor[1],0,2); }
   return "$sep_valor[0]";
 }

//------------------------------------------------------------------>
//-> Valor por extenso
//------------------------------------------------------------------>
function extenso($valor=0, $maiusculas=false){
global $rt;
    // verifica se tem virgula decimal
    if (strpos($valor,",") > 0)
    {
      // retira o ponto de milhar, se tiver
      $valor = str_replace(".","",$valor);

      // troca a virgula decimal por ponto decimal
      $valor = str_replace(",",".",$valor);
    }
    $singular = array("Centavo", "Real", "Mil", "Milh�o", "Bilh�o", "Trilh�o", "Quatrilh�o");
    $plural = array("Centavos", "Reais", "Mil", "Milh�es", "Bilh�es", "Trilh�es","Quatrilh�es");
    $c = array("", "Cem", "Duzentos", "Trezentos", "Quatrocentos","Quinhentos", "Seiscentos", "Setecentos", "Oitocentos", "Novecentos");
    $d = array("", "Dez", "Vinte", "Trinta", "Quarenta", "Cinquenta","Sessenta", "Setenta", "Oitenta", "Noventa");
    $d10 = array("Dez", "Onze", "Doze", "Treze", "Quatorze", "Quinze","Dezesseis", "Dezesete", "Dezoito", "Dezenove");
    $u = array("", "Um", "Dois", "Tr�s", "Quatro", "Cinco", "Seis","Sete", "Oito", "Nove");
    $z=0;
    $valor = number_format($valor, 2, ".", ".");
    $inteiro = explode(".", $valor);
    for($i=0;$i<count($inteiro);$i++)
        for($ii=strlen($inteiro[$i]);$ii<3;$ii++)
            $inteiro[$i] = "0".$inteiro[$i];
    $fim = count($inteiro) - ($inteiro[count($inteiro)-1] > 0 ? 1 : 2);
    for ($i=0;$i<count($inteiro);$i++) {
        $valor = $inteiro[$i];
        $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
        $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
        $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";
        $r = $rc.(($rc && ($rd || $ru)) ? " e " : "").$rd.(($rd && $ru) ? " e " : "").$ru;
        $t = count($inteiro)-1-$i;
        $r .= $r ? " ".($valor > 1 ? $plural[$t] : $singular[$t]) : "";
        if ($valor == "000")$z++; elseif ($z > 0) $z--;
        if (($t==1) && ($z>0) && ($inteiro[0] > 0)) $r .= (($z>1) ? " de " : "").$plural[$t];
        if ($r) $rt = $rt . ((($i > 0) && ($i <= $fim) &&
           ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
    }
         if(!$maiusculas){
          return (strtolower($rt) ? strtolower($rt) : "zero");
         } else {
          return($rt ? $rt : "Zero");
         }

}

//
// FUNCAO PARA GRAVACAO DE LOG
//
function reglog($id_login,$click) {
  $row=pg_fetch_array(pg_query("select *from usuarios where usr_codigo='$id_login'"));
  $data=date("d_m_Y");
  $fp=fopen("log/gps_".$data.".log","ab");
  $dt=date("d/m/Y H:i");
  fputs($fp,"<font color=blue>$dt</font> - <font color=red>$row[usr_nome]</font> :: $click <br>\n");
  fclose($fp);
}


function vSQL($sql,$print) {
 if($print>="1") {
   echo "------------------------------------------------------------------------------------<br><br>";
   echo "DUMP DA SQL<br><br>";
   echo $sql;
   echo "<br><br>------------------------------------------------------------------------------------";
   }
  if(!($db = pg_connect("host=10.0.0.201 dbname=yingming user=yingming password=b41&23gB")))
   die("pg_connect");

  if(!pg_send_query($db, $sql))
   die("pg_send_query");

  if(!($result = pg_get_result($db)))
   die("pg_get_result");

  echo "<br>".(pg_result_error($result) . "<br />\n");


}

?>


