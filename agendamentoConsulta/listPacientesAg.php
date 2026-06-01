<?php
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>

session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
//	 verauth($_GET[id_login]);
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
cabecario();
//------------------------------------------------------------------>


//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
reglog($id_login,"Entrando em LIST_PACIENTES");
//------------------------------------------------------------------>
$id = $_GET['id'];
print '
<script>
function getpaciente(nome,codigo,id) 
{
   window.opener.addPacientesAg(nome,codigo,id);
   window.close();
}
</script>
';

// se a pagina for redirecionada com uma '$palavra_chave', então fazer a busca...
if( ! empty($palavra_chave) && empty($acao) )
{
	$acao = 'busca';
}

if(empty($acao)) {

//
//-> Botoes
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <form method=post action='$PHP_SELF?$QUERY_STRING'>
		<input type=hidden name=acao value=busca>
		<input type=hidden name=id_login value='$id_login'>
		<input type=hidden name=id_login value='$id_login'>
	       <td width=30>Buscar:</td>
	       <td width=80><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
    		  <td width=40><select name=tpbusca class=box>
                   <option value=dt>Data de Nascimento</option>
                   <option value=sbr>Sobrenome</option>
                   <option value=pr>Prontuario</option>
                   <option value=n selected>Nome</option>
                   <option value=nm>Nome da Mãe</option>
                   </select></td>
	       <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg></td></form>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";

//
//-> Listando

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Listando Últimos <b>15</b> Pacientes Cadastrados</legend>
	     <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
	      <tr bgcolor=F9f9f9>
		<td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Prontuário</td>
		<td width=270 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Nome</td>
		<td width=20 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Sexo</td>
		<td width=50 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Dt. Nasc.</td>
		<td width=270 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>N. Mãe</td>
		<td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";

   $sql=db_query("select usu_prontuario,usu_codigo,usu_nome,to_char(usu_datanasc,'DD/MM/YYYY') as usu_datanasc,usu_mae,usu_sexo,usu_end_cidade from usuario order by usu_codigo desc limit 15");
     while($row=pg_fetch_array($sql)) {
       echo "<tr>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[usu_prontuario]</td>
	       <td width=270 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href='paciente_edit.php?acao=form_edit&from=list&usu_codigo=$row[usu_codigo]&id_login=$id_login&palavra_chave=$palavra_chave'>$row[usu_nome]</a></td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[usu_sexo]</td>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[usu_datanasc]</td>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[usu_mae]&nbsp;</td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href=\"javascript: window.open('infopaciente.php?id_login=$id_login&usu_codigo=$row[usu_codigo]',null,'height=400,width=700,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes');\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/historico_on.jpg border=0></a></td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href=\"javascript: getpaciente('$row[usu_codigo]','$row[usu_nome]','$id');\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_on.jpg border=0></a></td>
	     </tr>";
     }
	echo "</tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table>";
}


//------------------------------------------------------------------>
//-> Mostrando o resultado da busca
//------------------------------------------------------------------>

 if($acao=="busca") {
//
//-> Verificando Busca
 reglog($id_login,"Buscando em LIST_PACIENTES: $palavra_chave ");

/*
if(strlen($palavra_chave)<="3") {
        echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
                <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                 <tr bgcolor=f9f9f9>
                   <td align=center><font color=red size=2><b>ERRO</b></font><br>Busca com menos de <b>3</b> caracteres não permitida</td>
                 </tr>
                </table><br>";
        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='$PHP_SELF?id_login=$id_login'\", 2000);
              </SCRIPT>";
 exit;
}
*/
//
//-> Subistituindo o + por porcentagem na busca
   $str = str_replace("+","%",$palavra_chave);
   $pos = strpos($palavra_chave,"+");
  if($pos=="0") {
     $v1=1;
  } else {
     $v1=2;
  }
//
//-> Botoes
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <form method=post action='$PHP_SELF?$QUERY_STRING'>
		<input type=hidden name=acao value=busca>
	<input type=hidden name=id_login value=$id_login>
		<input type=hidden name=id_login value=$id_login>
	       <td width=30>Buscar:</td>
	       <td width=80><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
    	       <td width=40><select name=tpbusca class=box>
                   <option value=dt>Data de Nascimento</option>
                   <option value=sbr>Sobrenome</option>
                   <option value=pr>Prontuario</option>
                   <option value=n selected>Nome</option>
                   <option value=nm>Nome da Mãe</option>
                   </select></td>
	       <td>".ChmodBtn($id_login,'procurar','list_pacientes.php')."</td></form>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";
$palavra_chave = strtoupper($palavra_chave);

if($tpbusca=="dt") {
   $sql=db_query("select usu_prontuario,usu_codigo,usu_nome,to_char(usu_datanasc,'DD/MM/YYYY') as usu_datanasc,usu_mae,usu_sexo from usuario where to_char(usu_datanasc,'DD/MM/YYYY')='$palavra_chave' order by usu_nome");
}

if($tpbusca=="sbr") {
   $sql=db_query("select usu_prontuario,usu_codigo,usu_nome,to_char(usu_datanasc,'DD/MM/YYYY') as usu_datanasc,usu_mae,usu_sexo from usuario where usu_nome like '%$palavra_chave' order by usu_nome");
}

if($tpbusca=="pr") {
   $sql=db_query("select usu_prontuario,usu_codigo,usu_nome,to_char(usu_datanasc,'DD/MM/YYYY') as usu_datanasc,usu_mae,usu_sexo from usuario where usu_prontuario like '%$palavra_chave' order by usu_prontuario");
}

if($tpbusca=="n") {
   $sql=db_query("select usu_prontuario,usu_codigo,usu_nome,to_char(usu_datanasc,'DD/MM/YYYY') as usu_datanasc,usu_mae,usu_sexo from usuario where usu_nome like '$palavra_chave%' order by usu_nome");
}

if($tpbusca=="nm") {
   $sql=db_query("select usu_prontuario,usu_codigo,usu_nome,to_char(usu_datanasc,'DD/MM/YYYY') as usu_datanasc,usu_mae,usu_sexo from usuario where usu_mae like '$palavra_chave%' order by usu_mae");
}



$num=pg_num_rows($sql);
  if($num=="0") { $resp = "Nenhum Registro encontrado com \"$palavra_chave\""; }
  if($num=="1") { $resp = "Encontrado <b>$num</b> Registro com \"$palavra_chave\""; }
  if($num>"1") { $resp = "Encontrados <b>$num</b> Registros com \"$palavra_chave\""; }

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>$resp</legend>
	     <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
	      <tr bgcolor=F9f9f9>
		<td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Prontuário</td>
		<td width=270 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Nome</td>
		<td width=20 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Sexo</td>
		<td width=50 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Dt. Nasc.</td>
		<td width=270 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>N. Mãe</td>
		<td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";

     while($row=pg_fetch_array($sql)) {
       echo "<tr>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[usu_prontuario]</td>
	       <td width=270 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href='paciente_edit.php?acao=form_edit&from=list&usu_codigo=$row[usu_codigo]&id_login=$id_login&palavra_chave=$row[usu_nome]'>$row[usu_nome]</a></td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[usu_sexo]</td>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[usu_datanasc]</td>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[usu_mae]&nbsp;</td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href=\"infopaciente.php?id_login=$id_login&usu_codigo=$row[usu_codigo]\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/historico_on.jpg border=0></a></td>";
		   echo"
		   <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href=\"javascript: getpaciente('$row[usu_codigo]','$row[usu_nome]','$row[usu_mae]');\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_on.jpg border=0></a></td>
	     </tr>";
     }
	echo "</tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table>";
}
?>
