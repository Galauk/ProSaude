<?php 

include("../global.php");

?><link rel="stylesheet" href="/WebSocialSaude/lib/themes/base/jquery.ui.all.css">
<link rel="stylesheet" href="/WebSocialSaude/lib/themes/ui-lightness/jquery-ui-1.8.10.custom.css">
<script type="text/javascript" src="/WebSocialComum/library/js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="/WebSocialSaude/lib/ui/jquery-ui-1.8.16.custom.min.js"></script>


<?
/**
 * @version Anderson 14/04/2011
 * @author Anderson <anderson@elotech.com.br>
 *
*/

	$common = new commonClass();
	$form = new classForm();
	$table = new tableClass();

	?><script>

	$(function(){
		$("a.modal").click(function(e){
			var title = $(this).attr("title");
			if(!title)
				title = document.title;
			
			var rel = $(this).attr("rel").split("x");
			var url = $(this).attr("href");
			e.preventDefault();
			e.stopPropagation();
			$("#sys").append("<div id=\"modal-dialog\" title=\""+title+"\"></div>");
			$("#modal-dialog")
			.load(url)
			.dialog({
				modal: true,
				width: rel[0],
				height: rel[1],
				close: function(){ return $(this).remove();},
				buttons:{
					Fechar: function(){
						fecharModal();
						return false;
					}
				}
			});
		});
	});

  function fecharModal(){
		$(".ui-icon-closethick").click();
  }
	
     </script><?

  
 
reglog($id_login,"Acessando LISTA DE EXAMES");

//echo $common->menuTab(array('Mapa de Ocupacao','Dispensacao Paciente','Maternidade'));
//echo $common->menuTab(array('Leitos'));


echo $common->bodyTab('1');
/*echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
         <tr>
          <td>
          	<fieldset>
            	<legend>Mapa de Ocupacao dos Leitos</legend>
            		<form method=post action='$PHP_SELF' name='form'>
					    <input type=hidden name=acao value=listar> 
					    <input type=hidden name=id_login value=$id_login>"; 
            		echo $form->openForm("$PHP_SELF","POST","form");
						 $sql = "select qua_codigo,apt_codigo from quarto group by qua_codigo,apt_codigo";
					echo $form->inputSelect('med_codigo',null,'Ala',"$sql","Onchange='mostraQuarto(this.value);'",null,null,'style=width:250px'); 
												
					echo $form->closeForm();
            	
    echo"</form>
	    </fieldset>
	   </td>
	  </tr>
         </table>";*/


echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
         <tr>
          <td>
           <fieldset>
            <legend>Quartos</legend>";

echo "<table width=100% border='0'>           
			<tr>";
  $sql = pg_query("select *from quarto");
  while($qt = pg_fetch_array($sql)) {
  	
  	echo "<td><a href='$PHP_SELF?qua_codigo=$qt[qua_codigo]'>
  	<table width=100 cellspacing=0 cellpadding=4 border=0 style='border-top:1px dotted;border-bottom:1px dotted;border-left:1px dotted;border-right:1px dotted;'>
  			<tr>
  				<td style='width:100px;' align='center'><img src=./img/ocupado.png></td>
  			</tr>
  			<tr>
  			  <td align=center><font size=3><b>$qt[qua_numero]</b></font></td>
  			</tr>
  			<tr>
  			  <td align='center'><img src=./img/disponivel2.png>&nbsp;&nbsp;Dispon&iacute;vel</td>
  			</tr>
  		  </table></a></td>";
  }          
  
  	echo "<td width=100%></td></tr>
		   </table>";
  
/* echo "<table style='width:600px;' border='0'>           
			<tr>
				
				<td style='width:100px;' align='center'><img src=./img/disponivel.png></td>
				<td style='width:100px;' align='center'><img src=./img/reservado.png></td>
			
			</tr>
			<tr>
				<td align='center'><img src=./img/ocupado2.png>&nbsp;&nbsp;Ocupado</td>
				<td align='center'><img src=./img/disponivel2.png>&nbsp;&nbsp;Dispon&iacute;vel</td>
				<td align='center'><img src=./img/reservado2.png>&nbsp;&nbsp;Reservado</td>
				
			</tr>
		   </table>";*/

echo "</fieldset>
          </td>
         </tr>
      </table>";
	  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
         <tr>
          <td>
           <fieldset>
            <legend>Leitos</legend>";

	  
	  echo "<table width=100% border='0'>           
			<tr>";
  $sql = pg_query("select *from leito where qua_codigo = '$qua_codigo'");
  while($rr = pg_fetch_array($sql)) {
  	 $rq = pg_query("select *from paciente_leito where lei_codigo = '$rr[lei_codigo]'");
      $rw = pg_fetch_array($rq);
  	 $num = pg_num_rows($rq);
	  $usu = pg_fetch_array(pg_query("select *,to_char(usu_datanasc,'dd/mm/YYYY') as usu_datanasc from usuario where usu_codigo = '$rw[usu_codigo]'"));
  	 if($num>='1') {
  	 	$tp = "<td align='center'><img src=./img/ocupado2.png>&nbsp;&nbsp;Ocupado</td>";
  	 	$ahr = "<a href='opt_pac.php?lei_codigo=$rr[lei_codigo]' class='modal' rel='618x500' title='Ações ao Paciente'>";
  	 	
  	 	$ahr2 = "</a>";
  	 	$info = "<br><table width='100%' cellspacing='2' cellpadding='5' border='0' class='grid ui-widget ui-widget-content ui-corner-all' style='background-color:#ff9033'>
  	 			  <tr>
  	 			  	<td height=20 class='ui-widget ui-widget-content' align=right><b>Nome:&nbsp;</b></td>
  	 			  	<td class='ui-widget ui-widget-content'>$usu[usu_nome]</td>
  	 			  </tr>
  	 			  <tr>
  	 			  	<td height=20 class='ui-widget ui-widget-content' align=right><b>Dt. Nasc:</b></td>
  	 			  	<td height=20 class='ui-widget ui-widget-content'>$usu[usu_datanasc]</td>
  	 			  </tr>
  	 			  <tr>
  	 			  	<td height=20 class='ui-widget ui-widget-content' align=right><b>Nome Mae:</b></td>
  	 			  	<td height=20 class='ui-widget ui-widget-content'>$usu[usu_mae]</td>
  	 			  </tr>
  	 			 </table>";
  	 } else {
  	 	$info = "";
  	 	$tp = "<td align='center'><img src=./img/disponivel2.png>&nbsp;&nbsp;Dispon&iacute;vel</td>";
  	 	$ahr = "<a href='pacleito.php?lei_codigo=$rr[lei_codigo]' class='modal' rel='618x500' title='Selecionar Paciente'>";
  	 	$ahr2 = "</a>";
  	 }
  	 
 echo "<td valign=top>$ahr
  	<table width=100% cellspacing=0 cellpadding=4 border=0 style='border-top:1px dotted;border-bottom:1px dotted;border-left:1px dotted;border-right:1px dotted;'>
  			<tr>
  				<td style='width:100px;' align='center'><img src=./img/cama2.png></td>
  			</tr>
  			<tr>
  			  <td align=center><font size=3><b>$rr[lei_numero]</b></font></td>
  			</tr>
  			<tr>
  			  $tp
  			</tr>
  			<tr>
  			  <td align=center>
  			   $info
  			</tr>
  		  </table>$ahr2</td>";
  }          
  
  	echo "<td>&nbsp;</td></tr>
		   </table>";          
            
/*	    $sql = pg_query("select *from leito where qua_codigo = '$qua_codigo'");
  while($rr = pg_fetch_array($sql)) {          
            
       echo "<table style='width:300px;' border='0'>
		   			<tr>
						<td style='width:100px;' align='center'><img src=./img/cama2.png></td>
						<td style='width:100px;' align='center'><img src=./img/cama2.png></td>
					</tr>
					<tr>
				<td align='center'><img src=./img/ocupado2.png>&nbsp;&nbsp;Ocupado</td>
				<td align='center'><img src=./img/disponivel2.png>&nbsp;&nbsp;Dispon&iacute;vel</td>
				
			</tr>
					
			 </table>";
  }			 
	*/		 
echo "</fieldset>
			 <td>
		 <tr>
		</table>";
	  echo $common->closeTab();
		
//}
  echo "<div id='sys'></div>";
