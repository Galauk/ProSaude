<?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>

//  session_start();
//	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
//           cabecario();
//------------------------------------------------------------------>


$MES = date('n');
$ANO = date('Y');
$HOJE= date('d');
$EXTENSO = array('Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');
$MES_ATUAL = $HOJE." de ".$EXTENSO[$MES-1]." de ".$ANO;

if (isset($_GET['ms']) && $_GET['ms']>0 && $_GET['ms'] < 13) {
   $MES = $_GET['ms'];
}
if (isset($_GET['an']) && $_GET['an']>0) {
   $ANO = $_GET['an'];
}

$MES_CAL = $EXTENSO[$MES-1]." / ".$ANO;

$MINANO  = 5; //indica a quantidade de anos anterior ao atual o calendário irá abranger
$MAXANO  = 10; //indica a quantidade de anos devem estar disponíveis no formulário
//se maxano for menor ou igual a minano, não será exibido o calendário do ano corrente.
//apenas o dos anos anteriores...



$FORMANO = date('Y')-$MINANO;
$DIAS = date('t', mktime(0,0,0,$MES,1,$ANO));
$SEMANA = date('w', mktime(0,0,0,$MES,1,$ANO));
$DIA = 1;
$ESP = '&nbsp;';
$LINHA = 0;
$DATA = array();
$FONT_OPEN = "<font color=red>";
$FONT_CLOSE = "</font>";
//   echo $SEMANA."<br>".$DIAS;

while ($LINHA <=41) {
   if ($LINHA >= $SEMANA && $LINHA <= 41) {
      if ($DIA > $DIAS) {
         $DATA[$LINHA] = $ESP;
      }
     else {
         $DATA[$LINHA] = $DIA;
         $DIA = $DIA+1;
      }
   }
   if ($LINHA < $SEMANA) {
      $DATA[$LINHA] = $ESP;
   }

  $dia_01 = "$DATA[$LINHA]";
  $mes_ano = "$MES/$ANO";
  $dt_completo = $dia_01."/".$mes_ano;
  $dt_completo_today = date("d")."/".date("m")."/".date("Y");

  $ALLSEMANA = date('w', mktime(0,0,0,$MES,$DATA[$LINHA],$ANO));

  if(($dt_completo==$dt_completo_today OR $ALLSEMANA=="0" OR $ALLSEMANA=="6")) { 
      $DATA[$LINHA] = "<font color=red><b>".$DATA[$LINHA]."</b></font>";
  } else {
if(strlen($MES)<=1) { $NMES = "0".$MES; } else { $NMES = $MES; }
$feriado = pg_fetch_array(pg_query("select *from feriado"));

$fer = explode("-",$feriado[fer_data]);
$fer_dia = $fer[2];
$fer_mes = $fer[1];
$fer_ano = $fer[0];
if(strlen($fer_dia)=="2") { $fer_dia = str_replace("0","",$fer_dia); } else { $fer_dia = $fer_dia; }

$data_A = $ANO."-".$NMES."-".$DATA[$LINHA];
$data_F = $fer_ano."-".$fer_mes."-".$fer_dia;

if($data_A==$data_F) {
    $DATA[$LINHA] = "<b><font color=red>$DATA[$LINHA]</font><b>";
} else {
    $DATA[$LINHA] = "<a href='$PHP_SELF?$SEMANA&uni_codigo=$uni_codigo&med_codigo=$med_codigo&esp_codigo=$esp_codigo&id_dia=$DATA[$LINHA]/$NMES/$ANO' onclick=\"esconde('caixa')\">$DATA[$LINHA]</a>";
}
}
  // echo "Dia ".$DATA[$LINHA]."<BR>";
   $LINHA = $LINHA+1;
}
?>
<html>
<head>
<title>Documento sem t&iacute;tulo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<script language="JavaScript">
function ler() {
    var mes = document.form1.fmmes.options[document.form1.fmmes.selectedIndex].value;
    var ano = document.form1.fmano.options[document.form1.fmano.selectedIndex].value;
    document.location=('manutencaomedicos.php?uni_codigo=<?=$uni_codigo?>&med_codigo=<?=$med_codigo?>&esp_codigo=<?=$esp_codigo?>&type_layer=ok&ms='+mes+'&an='+ano);
}
</script>
<table width="175" border="0" align="center" cellpadding="1" cellspacing="0">
  <tr>
    <td valign="middle">
<form name="form1" method="post" action="">
        <table width="175" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td width="200">
                <select name="fmmes" id="select2" onChange="javascript: ler();" class=boxn>
                  <option value="1"  <? if ($MES==1)  { echo "selected"; }?>>Janeiro</option>
                  <option value="2"  <? if ($MES==2)  { echo "selected"; }?>>Fevereiro</option>
                  <option value="3"  <? if ($MES==3)  { echo "selected"; }?>>Mar&ccedil;o</option>
                  <option value="4"  <? if ($MES==4)  { echo "selected"; }?>>Abril</option>
                  <option value="5"  <? if ($MES==5)  { echo "selected"; }?>>Maio</option>
                  <option value="6"  <? if ($MES==6)  { echo "selected"; }?>>Junho</option>
                  <option value="7"  <? if ($MES==7)  { echo "selected"; }?>>Julho</option>
                  <option value="8"  <? if ($MES==8)  { echo "selected"; }?>>Agosto</option>
                  <option value="9"  <? if ($MES==9)  { echo "selected"; }?>>Setembro</option>
                  <option value="10" <? if ($MES==10) { echo "selected"; }?>>Outubro</option>
                  <option value="11" <? if ($MES==11) { echo "selected"; }?>>Novembro</option>
                  <option value="12" <? if ($MES==12) { echo "selected"; }?>>Dezembro</option>
                </select>
              </td>
            <td width="200" align=right>
	    <select name="fmano" id="select" onChange="javascript: ler();" class=boxn>
                  <?
            $ESCREVE = $FORMANO;
            while ($ESCREVE < $FORMANO+$MAXANO) {
            ?>
                  <option value="<? echo $ESCREVE; ?>"  <? if ($ANO==$ESCREVE)  { echo "selected"; }?>><? echo $ESCREVE; ?></option>
                  <?
            $ESCREVE = $ESCREVE+1;
                }
            ?>
                </select></td></form>
          </tr></table>
     </td>
  </tr>
</table>
<table width="175" border="1" align="center" cellpadding="1" cellspacing="0">
  <tr bgcolor="#FFCC00">
    <td colspan="7"><div align="center"><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong><? echo $MES_CAL; ?></strong></font></div></td>
  </tr>
  <tr bgcolor="#99CC00">
    <td width="25" bgcolor="#CCCC00">
      <div align="center"><font color="#FFFFFF"><strong>D</strong></font></div></td>
    <td width="25">
      <div align="center"><font color="#000000"><strong>S</strong></font></div></td>
    <td width="25">
      <div align="center"><font color="#000000"><strong>T</strong></font></div></td>
    <td width="25">
      <div align="center"><font color="#000000"><strong>Q</strong></font></div></td>
    <td width="25">
      <div align="center"><font color="#000000"><strong>Q</strong></font></div></td>
    <td width="25">
      <div align="center"><font color="#000000"><strong>S</strong></font></div></td>
    <td width="25" bgcolor="#CCCCCC">
      <div align="center"><font color="#000000"><strong>S</strong></font></div></td>
  </tr>
  <tr>
    <td bgcolor="#CCCC00"><div align="center"><font color="#FFFFFF"><? echo $DATA[0]; ?></font></div></td>
    <td><div align="center"><font color="#000000"><? echo $DATA[1]; ?> </font></div></td>
    <td><div align="center"><font color="#000000"><? echo $DATA[2]; ?> </font></div></td>
    <td><div align="center"><font color="#000000"><? echo $DATA[3]; ?> </font></div></td>
    <td><div align="center"><font color="#000000"><? echo $DATA[4]; ?> </font></div></td>
    <td><div align="center"><font color="#000000"><? echo $DATA[5]; ?> </font></div></td>
    <td bgcolor="#CCCCCC"><div align="center"><font color="#000000"><? echo $DATA[6]; ?>
        </font></div></td>
  </tr>
  <tr>
    <td bgcolor="#CCCC00"><div align="center"><font color="#FFFFFF"><? echo $DATA[7]; ?></font></div></td>
    <td><div align="center"><font color="#000000"><? echo $DATA[8]; ?>  </font></div></td>
    <td><div align="center"><font color="#000000"><? echo $DATA[9]; ?>  </font></div></td>
    <td><div align="center"><font color="#000000"><? echo $DATA[10]; ?> </font></div></td>
    <td><div align="center"><font color="#000000"><? echo $DATA[11]; ?> </font></div></td>
    <td><div align="center"><font color="#000000"><? echo $DATA[12]; ?> </font></div></td>
    <td bgcolor="#CCCCCC"><div align="center"><font color="#000000"><? echo $DATA[13]; ?>
        </font></div></td>
  </tr>
  <tr>
    <td bgcolor="#CCCC00"><div align="center"><font color="#FFFFFF"><? echo $DATA[14]; ?></font></div></td>
    <td><div align="center"><font color="#000000"><? echo $DATA[15]; ?> </font></div></td>
    <td><div align="center"><font color="#000000"><? echo $DATA[16]; ?> </font></div></td>
    <td><div align="center"><font color="#000000"><? echo $DATA[17]; ?> </font></div></td>
    <td><div align="center"><font color="#000000"><? echo $DATA[18]; ?> </font></div></td>
    <td><div align="center"><font color="#000000"><? echo $DATA[19]; ?> </font></div></td>
    <td bgcolor="#CCCCCC"><font color="#000000"><? echo $DATA[20]; ?> </font>
      <div align="center"></div></td>
  </tr>
  <tr>
    <td bgcolor="#CCCC00"><div align="center"><font color="#FFFFFF"><? echo $DATA[21]; ?></font></div></td>
    <td><div align="center"><font color="#000000"><? echo $DATA[22]; ?> </font></div></td>
    <td><div align="center"><font color="#000000"><? echo $DATA[23]; ?> </font></div></td>
    <td><div align="center"><font color="#000000"><? echo $DATA[24]; ?> </font></div></td>
    <td><div align="center"><font color="#000000"><? echo $DATA[25]; ?> </font></div></td>
    <td><div align="center"><font color="#000000"><? echo $DATA[26]; ?> </font></div></td>
    <td bgcolor="#CCCCCC"><div align="center"><font color="#000000"><? echo $DATA[27]; ?>
        </font></div></td>
  </tr>
  <tr>
    <td bgcolor="#CCCC00"><div align="center"><font color="#FFFFFF"><? echo $DATA[28]; ?></font></div></td>
    <td><div align="center"><font color="#000000"><? echo $DATA[29]; ?> </font></div></td>
    <td><div align="center"><font color="#000000"><? echo $DATA[30]; ?> </font></div></td>
    <td><div align="center"><font color="#000000"><? echo $DATA[31]; ?> </font></div></td>
    <td><div align="center"><font color="#000000"><? echo $DATA[32]; ?> </font></div></td>
    <td><div align="center"><font color="#000000"><? echo $DATA[33]; ?> </font></div></td>
    <td bgcolor="#CCCCCC"><div align="center"><font color="#000000"><? echo $DATA[34]; ?>
        </font></div></td>
  </tr>
  <tr>
    <td bgcolor="#CCCC00"><div align="center"><font color="#FFFFFF"><? echo $DATA[35]; ?></font></div></td>
    <td><div align="center"><font color="#000000"><? echo $DATA[36]; ?> </font></div></td>
    <td><div align="center"><font color="#000000"><? echo $DATA[37]; ?> </font></div></td>
    <td><div align="center"><font color="#000000"><? echo $DATA[38]; ?> </font></div></td>
    <td><div align="center"><font color="#000000"><? echo $DATA[39]; ?> </font></div></td>
    <td><div align="center"><font color="#000000"><? echo $DATA[40]; ?> </font></div></td>
    <td bgcolor="#CCCCCC"><div align="center"><font color="#000000"><? echo $DATA[41]; ?>
        </font></div></td>
  </tr>
</table>
</body>
</html>
