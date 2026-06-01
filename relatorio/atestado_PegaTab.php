<!-- ---------------------------------------------------------------
       Funções javascript
--------------------------------------------------------------- --->

<script language=javascript>

function imprimir() {
       window.print();
}

</script>

<!--<body onload='imprimir()'>-->

<?php

function inv_data($dat) {
   $d=explode("-",$dat);
   $dat=$d[2]."-".$d[1]."-".$d[0]."<br>";
   return "$dat";
}

//------------------------------------------------------------------>
// -> Includes
//------------------------------------------------------------------>
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

echo "<body>  <link href='../estilo.css' rel='stylesheet' type='text/css'>";

//----------------  Monta Dados Recebidos  ---------------->
/*
    Este programa pega dados da tabela -> texto 
            txt_codigo int8 NOT NULL DEFAULT nextval(('seq_texto'::text)::regclass),
            txt_ident varchar(6) NOT NULL,   -> identifica o texto ( atestado="atesta", ... , etc )
            txt_desc varchar(600) NOT NULL,  -> o texto a ser impresso

   Dados de entrada do programa:  para atestado ( ident do texto   e   cod do atendimento )
*/

$txt_ident="atesta";
$ate_codigo=2003336;

$Ate=pg_fetch_array(pg_query("SELECT usu_codigo, uni_codigo, ate_data, med_codigo FROM atendimento WHERE atendimento.ate_codigo=$ate_codigo"));
$Med=pg_fetch_array(pg_query("SELECT med_nome FROM medico  WHERE medico.med_codigo=$Ate[med_codigo]"));
$Txt=pg_fetch_array(pg_query("SELECT    *     FROM texto   WHERE texto.txt_ident='$txt_ident'"));
$Hoje = date("n");
switch ($Hoje) 
     {
      case 1: $mes = "janeiro";   break;       case 5: $mes = "maio";   break;       case  9: $mes = "setembro"; break;   
      case 2: $mes = "fevereiro"; break;       case 6: $mes = "junho";  break;       case 10: $mes = "outubro";  break;
      case 3: $mes = "março";     break;       case 7: $mes = "julho";  break;       case 11: $mes = "novembro"; break;
      case 4: $mes = "abril";     break;       case 8: $mes = "agosto"; break;       case 12: $mes = "dezembro"; break;
     }
$Hoje = date("d")." de ".$mes. " de ".date("Y");
$DiaAtesta=$Ate[ate_data];

echo "<html>
       <head>   <title> Atestado Medico </title>    </head>
       <body>   
        <style>   p {font-size:12pt;       line-height: 1.8; letter-spacing: 0.001cm;  
                     word-spacing: 0.01px; text-indent: 4cm; text-align: justify     }
        </style>
        <table width=98% align=center cellspacing=0 cellpadding=0 border=0>
         <tr><td>&nbsp;<br><br><br><br><br><br><br><br><br><br><br><br></td></tr>
         <tr><td> <center><h1>ATESTADO</h1></center> </td></tr>
         <tr><td>&nbsp;</td></tr>
         <tr><td>&nbsp;</td></tr>
         <tr><td>&nbsp;</td></tr>
         <tr><td>&nbsp;</td></tr>
         <tr><td>&nbsp;</td></tr>
         <tr><td>&nbsp;</td></tr>
         <tr><td><p>";
$lenghtText=strlen($Txt[txt_desc]);
$i=0;
$f=$Txt[txt_largurafolha] - 1;

while ($lenghtText > 0) {         
      echo substr($Txt[txt_desc], $i, $f)."<br>";
      $lenghtText = $lenghtText - $Txt[txt_largurafolha];
      $i=$i + $Txt[txt_largurafolha];
      $f=$f + $Txt[txt_largurafolha];
}
echo "           </p></td></tr>
         <tr><td><p> RECOMENDAÇÕES MÉDICAS </p></td></tr>
         <tr><td>&nbsp;</td></tr>
         <tr><td>&nbsp;</td></tr>
         <tr><td><p>Por ser esta a expressão da verdade, firmamos a presente.</p></td></tr>
         <tr><td>&nbsp;</td></tr>
         <tr><td>&nbsp;</td></tr>
         <tr><td>&nbsp;</td></tr>
         <tr><td>&nbsp;</td></tr>
         <tr><td>&nbsp;</td></tr>
         <tr><td><p align=center>Maring&aacute;, $Hoje. </p></td></tr>
         <tr><td>&nbsp;</td></tr>
         <tr><td>&nbsp;</td></tr>
         <tr><td>&nbsp;</td></tr>
         <tr><td>&nbsp;</td></tr>
         <tr><td><p style line-height:0 align=center>$Med[med_nome] </p></td></tr>
         <tr><td><p style line-height:0 align=center><font size=2 face=arial> o seu médico para qualquer eventualidade </font></p></td></tr>
        </table>
       </body>
      </html>";


?>
