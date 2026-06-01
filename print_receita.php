<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
echo "<body bgcolor=\"FFFFFF\" onload=\"window.print();\">
      <link href='estilo.css' rel='stylesheet' type='text/css'>";

 $Age = pg_fetch_array(pg_query("select * from agendamento where age_codigo='$age_codigo'"));
 $usu_codigo = $Age[usu_codigo];
 $usr_codigo = $Age[med_codigo];
 $uni_codigo = $Age[uni_codigo];
 $medInfo=pg_fetch_array(pg_query("select * from usuarios where usr_codigo='$usr_codigo'"));
 $uniInfo=pg_fetch_array(pg_query("select * from unidade where uni_codigo='$uni_codigo'"));
 $usuInfo=pg_fetch_array(pg_query("select * from usuario where usu_codigo='$usu_codigo'"));
 
 $end = array();
 $end []= $usuInfo['usu_end_rua'];
 $end []= $usuInfo['usu_end_nr'];
 $end []= $usuInfo['usu_end_compl'];
 $end []= $usuInfo['usu_end_bairro'];
 $end []= $usuInfo['usu_end_cidade'];
 
 foreach($end as $k => $item){
 	if( empty($item) )
 		unset($end[$k]);
 }

 
 $endereco = implode(", ",$end);

echo "<table width=100% cellspacing=0 cellpadding=0 border=0>
<tr>    
 <td width=65><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/logo_papeis.jpg></td>
	     <td valign=top>
	<table width=100% cellspacing=0 cellpadding=0 border=0>
           <tr>
	        <td><font size=4 face=arial>$usuInfo[usu_nome]</font></td>
	       </tr>
           <tr>
	        <td><font size=2 face=arial>$endereco</font></td>
	       </tr>
	</table>
 </td>
 <td>
	<table width=100% cellspacing=0 cellpadding=0 border=0>
	<tr>
	 <td width=74%>&nbsp;</td>
	 <td><img src='codigo.php?id_login=$id_login&age_codigo=$receita&lw=1&hi=18'></td>
	</tr>
	</table>
  </td>
 </tr>
</table>";
 
 echo "<table width=80% cellspcing=0 cellpadding=0 border=0 align=center>
	    <tr>
	     <td><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/tira_papeis.jpg width=500 height=3></td>
	    </tr>
       </table>
	   <table height=454 width=80% cellspcing=0 cellpadding=0 border=0 align=center>
	    <tr>
	     <td background=".$_SESSION[linkroot].$_SESSION[comum]."imgs/fundo_papeis.jpg valign=top>
	  <center><font size=4 face=times><u>Receita</u></font></center><br<br>";
if($tp_action=="externo") {
   $sql = pg_query("select desc_produto,irec_recomendacao,irec_quantidade,irec_codigo
           from itemreceita
           ,receita
           where itemreceita.rec_codigo = receita.rec_codigo
                 and receita.ate_codigo = $ate_codigo
                 and  receita.rec_codigo = $receita
  	         and  receita.rec_tipo = '$tp_action'
                 and  receita.rec_finalizada = 'N'");
} else {
  $sql = pg_query("select irec_codigo, itemreceita.pro_codigo, pro_nome, irec_recomendacao, irec_quantidade
                  from itemreceita, produto, receita
                  where itemreceita.pro_codigo = produto.pro_codigo
                  and  itemreceita.rec_codigo = receita.rec_codigo
                  and  receita.rec_codigo = $receita");
}
     while($row=pg_fetch_array($sql)) {
           $intquantidade = formata_valor0($row['irec_quantidade']);
           echo "<table width=100% cellspacing=0 cellpadding=0 border=0>";
if($tp_action=="externo") {
	   echo "<tr>
	               <td><b>$row[desc_produto]</b>_______________________________________ <font size=2 face=times>$intquantidade</font></td>
	              </tr>";
} else {
	   echo "<tr>
	               <td><b>$row[pro_nome]</b>_______________________________________ <font size=2 face=times>$intquantidade</font></td>
	              </tr>";
}
	   echo "<tr>
	               <td>$row[irec_recomendacao]</td>
	              </tr>";
	   echo "<tr>
	               <td>$row[apre_desc]</td>
	              </tr>
	             </table><br>";
}
echo "<center><br><br><br><br>
      __________________________________________<br>
                Carimbo e Ass. do Médico<br>$medInfo[usr_nome]<br>CRM: $medInfo[usr_num_conselho]</center>";
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
	  </table>";
 $up = pg_query("update receita set rec_finalizada = 'S' where rec_codigo = $receita"); 


