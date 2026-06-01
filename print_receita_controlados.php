<script language=javascript>
	function imprimir() {
		window.print();
	}
</script>

<!-- <body onload='imprimir()'> -->
<?
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
echo "<body bgcolor=FFFFFF>
      <link href='estilo.css' rel='stylesheet' type='text/css'>";

 $Age = pg_fetch_array(pg_query("select *from agendamento where age_codigo='$age_codigo'"));
 $usu_codigo = $Age[usu_codigo];
 $med_codigo = $Age[med_codigo];
 $uni_codigo = $Age[uni_codigo];
 $medInfo=pg_fetch_array(pg_query("select *from medico where med_codigo='$med_codigo'"));
 $uniInfo=pg_fetch_array(pg_query("select *from unidade where uni_codigo='$uni_codigo'"));

 echo "<table width=80% cellspcing=0 cellpadding=0 border=0 align=center>
	    <tr>
	     <td width=65><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/logo_papeis.jpg></td>
	     <td valign=top>
	      <table width=100% cellspacing=0 cellpadding=0 border=0>	
           <tr>
	        <td><font size=4 face=arial>$medInfo[med_nome]</font></td>
	       </tr>
           <tr>
	        <td><font size=2 face=arial>$medInfo[med_endereco]</font></td>
	       </tr>
           <tr>
	        <td>CRM:<font size=2 face=arial>$medInfo[med_crm]</font></td>
	       </tr>
	      </table>
         </td>
	    </tr>
	   </table>
	   <table width=80% cellspcing=0 cellpadding=0 border=0 align=center>
	    <tr>
	     <td><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/tira_papeis.jpg width=500 height=3></td>
	    </tr>
       </table>
	   <table height=454 width=80% cellspcing=0 cellpadding=0 border=0 align=center>
	    <tr>
	     <td background=".$_SESSION[linkroot].$_SESSION[comum]."imgs/fundo_papeis.jpg valign=top>
	  <center><font size=4 face=times><u>Receita</u></font></center><br<br>";
   $sql = pg_query("select desc_produto,irec_recomendacao,irec_quantidade,irec_codigo
           from itemreceita
           ,receita
           where itemreceita.rec_codigo = receita.rec_codigo
                 and receita.ate_codigo = $ate_codigo
                 and  receita.rec_codigo = $receita
  	         and  receita.rec_tipo = '$tp_action'
                 and  receita.rec_finalizada = 'N'");

/*
  $sql="select irec_codigo, itemreceita.pro_codigo, pro_nome, irec_recomendacao, irec_quantidade
                  from itemreceita, produto, receita
                  where itemreceita.pro_codigo = produto.pro_codigo
                  and  itemreceita.rec_codigo = receita.rec_codigo
                  and  receita.ate_codigo = $ate_codigo
		  and  receita.rec_tipo = '$tp_action'";
Vsql($sql,"1");
*/
     while($row=pg_fetch_array($sql)) {
           $intquantidade = formata_valor0($row['irec_quantidade']);
           echo "<table width=100% cellspacing=0 cellpadding=0 border=0>
	              <tr>
	               <td>$row[irec_recomendacao]</td>
	              </tr>
	              <tr>
	               <td><b>$row[pro_nome]</b>_______________________________________ <font size=2 face=times>$intquantidade</font></td>
	              </tr>
	              <tr>
	               <td>$row[apre_desc]</td>
	              </tr>
	             </table><br>";
}
echo "<center>
      __________________________________________<br>
              Carimbo e Ass. do M�dico $medInfo[med_nome]<br>CRM: $medInfo[med_crm]</center>";
 echo "</td>
	</tr>
       </table>
	<table width=80% cellspacing=0 cellpadding=0 border=0 align=center>
	<tr>
	 <td><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/tira_papeis.jpg width=500 height=3></td>
	</tr>
           <tr>
	    <td align=center><b>$uniInfo[uni_desc]</b>&nbsp;&nbsp;$uniInfo[uni_localizacao]</td>
	   </tr>
           <tr>
	    <td align=center><b>ATEN��O</b></td>
	   </tr>
           <tr>
	    <td align=center>Este documento n�o poder� ser rasurado. Deve ser entregue na sua empresa dentro de 24 horas.<br>
			     N�o podendo ser concedido neste atestado afastamento superior a 15(quinze) dias, nem retroativo.
	    </td>
	   </tr>
	  </table>";

?>
