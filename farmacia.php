<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<script>
var gdtInicial
var gdtFinal
var gmedico
var gespecial
var gunidade
var gTipAgenda
var gMostAgente
var gHoje
var maxDay = new Array(31,28,31,30,31,30,31,31,30,31,30,31);


function CheckDate(d,t) {
   date_array = new Array(3);
   date_array[0]=(String(d).substr(6,2))    // dia
   date_array[1]=(String(d).substr(4,2))    // mes
   date_array[2]=(String(d).substr(0,4))    // ano

   if (date_array[0] > maxDay[date_array[1]-1]) {
       alert ("Dia invalido da data " + t)
       return 1;
   }
   if (date_array[1] > 12) {
       alert ("Mes invalido da data " + t)
       return 1;
   }
   if (date_array[2] < 1990) {
       alert ("Ano invalido da data " + t)
       return 1;
   }
}


function CheckCall() {

   gdtInicial =document.frm_consulta.dt_inicial.value;
   gdtFinal   =document.frm_consulta.dt_final.value;

   if (gdtInicial == '') {
       alert ("Informe Data Inicio");
       document.frm_consulta.dt_inicial.focus();
       return false;
   }
    if (gdtFinal == '') {
       alert ("Informe Data Final");
       document.frm_consulta.dt_final.focus();
       return false;
   }
   var d1=gdtInicial;
   var d2=gdtFinal;
   for (var i = 0; i < d1.length; i++) {
        if (d1.charAt(i) == "-") {
           var dat1=parseInt(d1.split("-")[2].toString()+d1.split("-")[1].toString()+d1.split("-")[0].toString())
        }
        else
        if (d1.charAt(i) == "/") {
           var dat1=parseInt(d1.split("/")[2].toString()+d1.split("/")[1].toString()+d1.split("/")[0].toString())
        }
   }
	for (var i = 0; i < d2.length; i++) {
        if (d2.charAt(i) == "-") {
           var dat2=parseInt(d2.split("-")[2].toString()+d2.split("-")[1].toString()+d2.split("-")[0].toString())
        }
        else
        if (d2.charAt(i) == "/") {
           var dat2=parseInt(d2.split("/")[2].toString()+d2.split("/")[1].toString()+d2.split("/")[0].toString())
        }
   }
   if (CheckDate(dat1,"INICIAL")==1) {
       document.frm_consulta.dt_inicial.focus()
       return false;
   }
	if (CheckDate(dat2,"FINAL")==1) {
       document.frm_consulta.dt_final.focus()
       return false;
   }

  return true
}
</script>
<?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	verauth($id_login);
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
//------------------------------------------------------------------>


 reglog($id_login,"Acessando Materiais");
//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>

$data = date("d/m/Y");

 if(empty($acao)) {

//
//-> Botoes
  $fbPath = $_SESSION[root] . "WebSocialHistorico/index.php";

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
           <td align=left>".ChmodBtn($id_login,'psicotropicos','psico.php?acao=form_psico')."</td>
           <td align=left><a href=zf/farmacia/farmacia><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/dispensacao_on.jpg border=0</td>
           
           <td align=left>".ChmodBtn($id_login,'cotas_pacientes','cota_paciente.php?acao=')."</td>
           <td align=left>".ChmodBtn($id_login,'programa_atendimento','programa_atendimento.php?acao=')."</td>
           <td align=left>".ChmodBtn($id_login,'adm_dispensacao','dispensacao.php?acao=form_dispensa')."</td>
           </tr>
          </table>
          <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
           <tr>
           <td align=left>".ChmodBtn($id_login,'programa_produto','programa_produto.php?acao=')."</td>";
 			if(file_exists( $fbPath )){
 				echo "<td align=left><a href=\"$_SESSION[linkroot]WebSocialHistorico/index.php?link=$link \"><img src=\"../WebSocialComum/imgs/historico_on.jpg\"</a></td>";
  			}
		echo "
";
echo "
         </tr>
</table>
</fieldset>
<br>";
}
/*
//-> Botoes
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
           <td width=60>".ChmodBtn($id_login,'psicotropicos','psico.php?acao=form_psico')."</td>
           <td width=60>".ChmodBtn($id_login,'dispensacao','dispensacao.php?acao=form_entrada')."</td>
           <td width=74 align=right>".ChmodBtn($id_login,'cotas_pacientes','cota_paciente.php?acao=')."</td>
           <td width=74 align=right>".ChmodBtn($id_login,'programa_atendimento','programa_atendimento.php?acao=')."</td>
           <td width=74 align=right>".ChmodBtn($id_login,'programa_produto','programa_produto.php?acao=')."</td>
		   <td width=74 align=right><a href='logoff.php?id_login=$id_login' target='_parent'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/sair.gif border=0></a></td>
         </tr>
</table>";*/


?>

