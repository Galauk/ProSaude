
<!-- ---------------------------------------------------------------
       Funções javascript
--------------------------------------------------------------- --->

<script language=javascript>

function imprimir() {
       window.print();
}

</script>

<body onload='imprimir()'>

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
//$ate_codigo=2003325;

$Ate=pg_fetch_array(pg_query("SELECT usu_codigo, uni_codigo, ate_data, med_codigo FROM atendimento WHERE atendimento.ate_codigo = $ate_codigo"));
$Usu=pg_fetch_array(pg_query("SELECT usu_nome FROM usuario WHERE usuario.usu_codigo = $Ate[usu_codigo]"));
$Uni=pg_fetch_array(pg_query("SELECT uni_desc FROM unidade WHERE unidade.uni_codigo = $Ate[uni_codigo]"));
$Med=pg_fetch_array(pg_query("SELECT med_nome FROM medico  WHERE medico.med_codigo  = $Ate[med_codigo]"));

$Hoje = date("n");
switch ($Hoje) 
     {
       case  1:   $mes = "janeiro";    break;
       case  2:   $mes = "fevereiro";  break;
       case  3:   $mes = "março";      break;
       case  4:   $mes = "abril";      break;
       case  5:   $mes = "maio";       break;
       case  6:   $mes = "junho";      break;
       case  7:   $mes = "julho";      break;
       case  8:   $mes = "agosto";     break;
       case  9:   $mes = "setembro";   break;
       case 10:   $mes = "outubro";    break;
       case 11:   $mes = "novembro";   break;
       case 12:   $mes = "dezembro";   break;
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
         <tr><td>&nbsp;<br><br><br><br><br><br><br><br><br><br><br><br><br><br></td></tr>
         <tr><td> <center><h1>ATESTADO</h1></center> </td></tr>
         <tr><td>&nbsp;</td></tr>
         <tr><td>&nbsp;</td></tr>
         <tr><td>&nbsp;</td></tr>
         <tr><td>&nbsp;</td></tr>
         <tr><td>&nbsp;</td></tr>
         <tr><td>&nbsp;</td></tr>
         <tr><td><p>Declaramos para fins, que o paciente $Usu[usu_nome], esteve presente em consulta m&eacute;dica na unidade de saúde";echo ($Uni[uni_desc]!='')?  ", ".$Uni[uni_desc]." ,": "";
echo " no dia $DiaAtesta. </p></td></tr>
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

/*

de cadastramento em Agentes de IntegraÃ§Ã£o de EstÃ¡gio, que <?php if ($se='M') echo('o acadÃªmico'); if ($sexo=='F') echo('a acadÃªmica');?> <?php echo ($nome)?>,  <?php if ($sexo=='M') echo(' portar '); if ($sexo=='F') echo(' portadora ');?> do RG  <?php echo Edita_RG($rg)?><?php if ($rg_orgao!='') echo ('/'.$rg_orgao)?> ,   <?php if ($sexo=='M') echo(' filho '); if ($sexo=='F') echo(' filha ');?> de <?php echo ($pai_nome . ' e de ' . $mae_nome)?> estÃ¡ regularmente   <?php if ($sexo=='M') echo(' matriculado '); if ($sexo=='F') echo(' matriculada ');?> no <?php echo $rows)?>o. semestre do curso de <?php echo ($curso)?>, para o 1o. semestre letivo de 2006, nesta InstituiÃ§Ã£o de Ensino Supior.</p>
*/

?>
