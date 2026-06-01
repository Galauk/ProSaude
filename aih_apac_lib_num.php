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


reglog($id_login,"Acessando Grupo em Materiais");
//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>

// if(empty($acao)) {
 if(empty($acao) OR $acao=='form_grupo') {

//
//-> Botoes
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=79><a href=aih.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
	       <td width=200>".ChmodBtn($id_login,'adicionar','aih_apac_lib_num.php?acao=add')."</td>";
	       if (chmodbtn($id_login,"procurar_if","aih_apac_lib_num.php"))
	       {	       
		  echo "<form method=post action=$PHP_SELF>";
	       }
	      echo "
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";

//
//-> Listando
  
  if (chmodbtn($id_login,"listar_if","aih_apac_lib_num.php"))
  {
	echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	       <tr>
		<td>
		 <fieldset>
		  <legend>Listando Numeros Bloqueados</legend>
		   <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
		    <tr bgcolor=F9f9f9>
		      <td width=10 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Tipo</td>
		      <td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Numero</td>
		      <td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";
      
	 $sql=pg_query("select * from aih_apac_numeros_resto 
	                where aan_emuso = 'S'
	                order by aan_tipo, aan_numero_resto ");
	   while($row=pg_fetch_array($sql)) {
	     echo "<tr>
		     <td width=10 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[aan_tipo]</td>
		     <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[aan_numero_resto]</td>
		     <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'editar','aih_apac_lib_num.php?acao=edit&aan_codigo='.$row[aan_codigo])."</td>
		   </tr>";
	   }
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


//------------------------------------------------------------------>
//-> Formulario de Edicao de Conteudo
//------------------------------------------------------------------>

//
//-> EDIT <--------------------------------------------------------->

 if($acao=="edit") {
	$sql = "update aih_apac_numeros_resto set " .
            "aan_emuso = null 
            where aan_codigo='$aan_codigo'";
	echo $sql;
	$sql = pg_query("update aih_apac_numeros_resto set " .
            "aan_emuso = null 
            where aan_codigo='$aan_codigo'");
reglog($id_login,"Editando Liberando numeros apac/aih $aan_codigo");
msg($id_login,$acao,$sql);
}
 if($acao=="add") {
	$sql = pg_query("update aih_apac_numeros_resto set " .
            "aan_emuso = null 
            ");
reglog($id_login,"Editando Liberando todos os numeros apac/aih ");
msg($id_login,$acao,$sql);
}


?>

