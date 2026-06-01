<?

//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();


/*
      ( add )
*/
  echo "<fieldset>
	    <legend>Opções de Cadastro</legend>
	       <a href=medico.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>
	   </fieldset>
	  <br>";


 if (empty($action) OR ($acao == 'form_inclui_item')) {

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	    <form name=form_add  method=post action=''>
         <input type=hidden name=action value=insert>
         <input type=hidden name=acao value=>
         <tr><td>&nbsp;</td></tr>
         <tr><td>&nbsp;</td></tr>
         <tr>
	      <td>
	       <fieldset>
	        <legend>Inclui Recomenda&ccedil;&atilde;o</legend>
	         <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	          <tr>
		       <td>TEXTO</td>
		       <td> <input type=text name=recm_texto class=box size=100></td>
              </tr>
              <tr>
              <td>&nbsp;&nbsp;</td>
	          <td width=60><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/add_on.gif '></td>
	         </tr></form>
	        </table>
	       </fieldset>
	      </td>
	     </tr>
        </table><br>";
}


/*
      (edit) 
*/

 if ($acao == 'form_edit') {

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	     <form name=form_edit  method=post action=''>
          <input type=hidden name=action value=edit>
          <input type=hidden name=acao value=>
          <tr><td>&nbsp;</td></tr>
          <tr><td>&nbsp;</td></tr>
 	      <tr>
	       <td>
	        <fieldset>
	         <legend>Altera Recomenda&ccedil;&atilde;o</legend>
	          <table width=100% align=center cellspacing=3 cellpadding=0 border=0>";
        $sql = pg_query("select * from recomendacao where recm_codigo = '$recm_codigo' order by recm_texto");
        $row=pg_fetch_array($sql);
echo "         <tr>
		        <td>DESCRI&Ccedil;&Atilde;O</td>
		        <td> <input type=text name=recm_texto value='$row[recm_texto]' class=box size=100></td>
               </tr>
               <tr>
                <td>&nbsp;&nbsp;</td>
 	            <td width=60><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/add_on.gif '></td>
	           </tr>
	          </table>
	        </fieldset>
	       </td>
	      </tr>
         </form>
        </table><br>";
}


 if (empty($action) OR ($acao == 'form_inclui_item') OR ($acao == 'form_edit')) {
//
//-> Listando
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	     <tr>
	      <td>
	       <fieldset>
	        <legend>Listando Recomenda&ccedil;&otilde;es</legend>
	         <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
	          <tr bgcolor=F9f9f9>
               <td width=20 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Codigo</td>
               <td width=200 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Texto</td>
               <td width=5 colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";
         $sql = pg_query("select * from recomendacao order by recm_texto");
         while($row=pg_fetch_array($sql)) {
           echo "
              <tr style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>
               <td>$row[0]</td>
               <td>$row[1]</td>
	           <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href=$PHP_SELF?acao=form_edit&recm_codigo=$row[recm_codigo]&action=form_altera_item><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg border=0></a></td>
	           <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href=$PHP_SELF?action=delete&recm_codigo=$row[recm_codigo]><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg border=0></a></td>
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
//-> SQL's
//------------------------------------------------------------------>
/*
   ADD   <---------------------------------------------------------->
*/

 if($action=="insert") {
    $sql=pg_query("insert into recomendacao ( " .
                               "recm_texto          " .
                   ") values ( " .
                               ($recm_texto   ? "'$recm_texto'"  : "null") . "  " . 
                   ")");
   msg($id_login,$acao,$sql);
   echo "<SCRIPT LANGUAGE=\"JavaScript\">
                 setTimeout(\"location='recomendacao.php?id_login=$id_login&acao=form_inclui_item&action='\", 0);
         </SCRIPT>";
}

/*
   EDIT   <---------------------------------------------------------->
*/

 if($action=="edit") {
    $sql=pg_query("update recomendacao set " .
                          ($recm_texto ? "recm_texto='$recm_texto'" : "recm_texto=null")
                 . "  where recm_codigo='$recm_codigo'");
    msg($id_login,$acao,$sql);
    echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='recomendacao.php?id_login=$id_login&acao=form_inclui_item&action='\", 0);
          </SCRIPT>";
}

/*
   DEL   <---------------------------------------------------------->
*/

 if($action=="delete") {
    $sql = pg_query("delete from recomendacao where recm_codigo='$recm_codigo'");
    msg($id_login,$acao,$sql);
    echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='recomendacao.php?id_login=$id_login&acao=form_inclui_item&action='\", 0);
        </SCRIPT>";
}

?>

