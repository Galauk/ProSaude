<?

//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();

//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
         <tr>
          <td>
           <fieldset>
            <legend>Opções</legend>
             <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
              <tr>
               <td width=79><a href=ambulatorio.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
               <td width=480 align=center>&nbsp;</font></td>
               <td width=107><a href='logoff.php?id_login=$id_login' target='_parent'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/sair.gif border=0></a></td>
              </tr>
             </table>
           </fieldset>
          </td>
         </tr>
        </table><br>";

//
//->   ( add )

 if (empty($action) OR ($acao == 'form_inclui_item')) {

  echo "<table width=100% align=center cellspacing=0 cellpadding=0 border=0>
	    <form name=form_add  method=post action=''>
         <input type=hidden name=action value=insert>
         <input type=hidden name=acao value=>
         <tr>
	      <td>
	       <fieldset>
	        <legend>Inclui Texto</legend>
	         <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
              <tr>
               <td width=12%>&nbsp;&nbsp;</td>
               <td align=right width=15%>Tipo Texto&nbsp;&nbsp;</td>
               <td><input name=txt_ident  type=text class=box>&nbsp;</td>
              </tr>
              <tr>
               <td width=12%>&nbsp;&nbsp;</td>
               <td align=right>Texto&nbsp;&nbsp;</td>
              </tr>
              <tr>
               <td width=12%>&nbsp;&nbsp;</td>
               <td>&nbsp;</td>
               <td><textarea name=txt_desc cols=80 rows=20 class=box></textarea></td>
              </tr>
              <tr>
              <td>&nbsp;&nbsp;</td>
              <td>&nbsp;&nbsp;</td>
	          <td width=60><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/add_on.gif '></td>
	         </tr></form>
	        </table>
	       </fieldset>
	      </td>
	     </tr>
        </table><br>";
}
//
//->   ( edit )

 if ($acao == 'form_edit') {
  echo "<table width=80% align=center cellspacing=0 cellpadding=0 border=0>
	     <form name=form_edit method=post action=''>
          <input type=hidden  name=action value='edit'>
          <input type=hidden  name=acao   value=>
 	      <tr>
	       <td>
	        <fieldset>
	         <legend>Altera Texto</legend>
	          <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
              <tr>";
        $sql = pg_query("select * from texto where  txt_codigo = '$txt_codigo'");
        $Txt=pg_fetch_array($sql);
  echo "       <td width=12%>&nbsp;&nbsp;</td>
               <td align=right width=15%>Tipo Texto</td>
               <td><input name=txt_ident  type=text class=box value=$Txt[txt_ident]>&nbsp;</td>
              </tr>
               <tr>
                <td width=12%>&nbsp;&nbsp;</td>
		        <td align=right>Texto</td>
               </tr>
               <tr>
                <td width=12%>&nbsp;&nbsp;</td>
                <td>&nbsp;</td>
                <td><textarea name=txt_desc cols=80 rows=20 class=box>$Txt[txt_desc]</textarea></td>
               </tr>
               <tr>
               <tr>
                <td>&nbsp;&nbsp;</td>
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
  echo "<table width=40% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Listando Itens Cadastradros para a Apresentaçao do Produto</legend>
	     <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
	      <tr bgcolor=F9f9f9>
           <td width=20 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Codigo</td>
           <td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Tipo Texto</td>
           <td width=15 colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";

         $sql = pg_query("select * from texto  order by txt_desc");
         while($row=pg_fetch_array($sql)) {
  echo "
          <tr style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>
           <td>$row[0]</td>
           <td>$row[1]</td>
	       <td width=10 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href=$PHP_SELF?acao=form_edit&txt_codigo=$row[txt_codigo]&txt_ident=$row[pro_codigo]&action=form_altera_item><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg border=0></a></td>
	       <td width=10 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href=$PHP_SELF?action=delete&txt_codigo=$row[txt_codigo]&txt_ident=$row[txt_ident]><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg border=0></a></td>
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
    $sql=pg_query("insert into texto ( " .
                               "txt_ident,              " .
                               "txt_desc                " .
                   ") values ( " .
                               ($txt_ident  ? "'$txt_ident'"   : "null") . ", " .
                               ($txt_desc   ? "'$txt_desc'"    : "null") . "  " . 
                   ")");
    msg($id_login,$acao,$sql);
    echo "<SCRIPT LANGUAGE=\"JavaScript\">
                 setTimeout(\"location='texto.php?id_login=$id_login&acao=form_inclui_item&action='\", 0);
          </SCRIPT>";
 }

/*
   EDIT   <---------------------------------------------------------->
*/

 if($action=="edit") {
    $sql=pg_query("update texto set " .
                          ($txt_ident      ? "txt_ident         ='$txt_ident'"      : "txt_ident=null") 
                 . ", " . ($txt_desc       ? "txt_desc          ='$txt_desc' "      : "txt_desc =null")
                 . "  where txt_ident='$txt_ident'");
    msg($id_login,$acao,$sql);
    echo "<SCRIPT LANGUAGE=\"JavaScript\">
                 setTimeout(\"location='texto.php?id_login=$id_login&action='\", 0);
          </SCRIPT>";
 }

/*
   DEL   <---------------------------------------------------------->
*/

 if($action=="delete") {
    $sql = pg_query("delete from texto where txt_codigo='$txt_codigo'");
    msg($id_login,$acao,$sql);
    echo "<SCRIPT LANGUAGE=\"JavaScript\">
                 setTimeout(\"location='texto.php?id_login=$id_login&action='\", 0);
          </SCRIPT>";
 }

?>

