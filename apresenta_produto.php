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

 if (empty($action) OR ($acao == 'form_inclui_item') OR ($acao == 'form_edit')) {

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	     <form name=dados_nota method=post action=''>
	      <tr>
	       <td>
	        <fieldset>
	         <legend>Dados da Entrada</legend>
	          <table width=100% align=center cellspacing=3 cellpadding=0 border=0>";
               $sql = pg_query("select pro_nome from produto  where  pro_codigo = '$pro_codigo'");
               $row = pg_fetch_array($sql);
  echo "       <tr> 
	            <td align=right>Produto&nbsp;&nbsp;</td>
	            <td colspan=5><input type=text readonly name=pro_nome size=100 value='$row[pro_nome]'></td> 
               </tr>
	          </table>
	        </fieldset>
	       </td>
	      </tr>
         </form>
        </table><br>";
}

//
//->   Middle Screen  ( add )

 if (empty($action) OR ($acao == 'form_inclui_item')) {

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	    <form name=form_add  method=post action=''>
         <input type=hidden name=action value=insert>
         <input type=hidden name=acao value=>
         <tr>
	      <td>
	       <fieldset>
	        <legend>Inclui Produto</legend>
	         <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	          <tr>
		       <td>DESCRI&Ccedil;&Atilde;O</td>
		       <td> <input type=text name=apre_desc class=box size=100></td>
              </tr>
              <tr>
     	       <td>FATOR MULT.</td>
               <td><input type=text name=apre_fatormultiplic class=box size=4></td>
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

 if ($acao == 'form_edit') {

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	     <form name=form_edit  method=post action=''>
          <input type=hidden name=action value=edit>
          <input type=hidden name=acao value=>
 	      <tr>
	       <td>
	        <fieldset>
	         <legend>Altera Produto</legend>
	          <table width=100% align=center cellspacing=3 cellpadding=0 border=0>";
        $sql = pg_query("select * from apresentacao_produto where  apre_codigo = '$apre_codigo' order by apre_desc");
        $row=pg_fetch_array($sql);
echo "         <tr>
		        <td>DESCRI&Ccedil;&Atilde;O</td>
		        <td> <input type=text name=apre_desc value='$row[apre_desc]' class=box size=100></td>
               </tr>
               <tr>
     		    <td>FATOR MULT.</td>
    		    <td><input type=text name=apre_fatormultiplic value='$row[apre_fatormultiplic]' class=box size=4></td>
               </tr>   
               <tr>
                <td>&nbsp;&nbsp;</td>
<!--	        <td width=100 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href=$PHP_SELF?action=edit&apre_codigo=$row[apre_codigo]&pro_codigo=$row[pro_codigo]&action=form_altera_item><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg border=0></a></td> -->
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
	    <legend>Listando Itens Cadastrados para a Apresentaçao do Produto</legend>
	     <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
	      <tr bgcolor=F9f9f9>
           <td width=20 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Produto</td>
           <td width=200 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Descricao</td>
           <td width=40  style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Fator Multiplica</td>
           <td width=5 colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";

         $sql = pg_query("select * from apresentacao_produto where  pro_codigo = '$pro_codigo' order by apre_desc");
         while($row=pg_fetch_array($sql)) {
           echo "
          <tr style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>
           <td>$row[0]</td>
           <td>$row[2]</td>
           <td align=center>$row[3]</td>
	       <td width=100 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href=$PHP_SELF?acao=form_edit&apre_codigo=$row[apre_codigo]&pro_codigo=$row[pro_codigo]&action=form_altera_item><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg border=0></a></td>
	       <td width=100 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href=$PHP_SELF?action=delete&apre_codigo=$row[apre_codigo]&pro_codigo=$row[pro_codigo]><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg border=0></a></td>
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
    $sql=pg_query("insert into apresentacao_produto ( " .
                               "pro_codigo,              " .
                               "apre_desc,               " .
                               "apre_fatormultiplic      " .
                   ") values ( " .
                               ($pro_codigo          ? "'$pro_codigo'"          : "null") . ", " .
                               ($apre_desc           ? "'$apre_desc'"           : "null") . ", " . 
                               ($apre_fatormultiplic ? "'$apre_fatormultiplic'" : "null") . "  " .
                   ")");
   msg($id_login,$acao,$sql);
   echo "<SCRIPT LANGUAGE=\"JavaScript\">
                setTimeout(\"location='apresenta_produto.php?id_login=$id_login&pro_codigo=$pro_codigo&acao=form_inclui_item&action='\", 0);
         </SCRIPT>";
}

/*
   EDIT   <---------------------------------------------------------->
*/

 if($action=="edit") {
    $sql=pg_query("update apresentacao_produto set " .
                          ($pro_codigo      ? "pro_codigo         ='$pro_codigo'"      : "pro_codigo=null") 
                 . ", " . ($apre_desc       ? "apre_desc          ='$apre_desc' "      : "apre_desc =null")
                 . ", " . ($apre_fatormultiplic ? "apre_fatormultiplic='$apre_fatormultiplic'" : "apre_fatormultiplic=null")
                 . "  where apre_codigo='$apre_codigo'");
  msg($id_login,$acao,$sql);
  echo "<SCRIPT LANGUAGE=\"JavaScript\">
                setTimeout(\"location='apresenta_produto.php?id_login=$id_login&pro_codigo=$pro_codigo&action='\", 0);
        </SCRIPT>";
}

/*
   DEL   <---------------------------------------------------------->
*/

 if($action=="delete") {
    $sql = pg_query("delete from apresentacao_produto where apre_codigo='$apre_codigo'");
    msg($id_login,$acao,$sql);
    echo "<SCRIPT LANGUAGE=\"JavaScript\">
                setTimeout(\"location='apresenta_produto.php?id_login=$id_login&pro_codigo=$pro_codigo&action='\", 0);
        </SCRIPT>";
}

?>

