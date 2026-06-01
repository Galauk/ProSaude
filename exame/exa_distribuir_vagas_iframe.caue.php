<?php
/**
 * Arquivo iframe da "Manutencao de Grupos de Agenda"
 * @author      Dilee
 * @version     2007-04-25 @ 10:30
 * @warning
*/

//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
session_start();
include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
cabecario();
//------------------------------------------------------------------>

reglog($id_login,"Abrindo Lista de Agentes");

if(empty($proc_codigo)) { exit; }
?>
<script src="../script.js" language="javascript" type="text/javascript"></script>
<script src="../ajax_motor.js" language="javascript" type="text/javascript"></script>
<script>
	var qtdeAntiga = 0;

	function preenche(txt){
		alert('entrou aqui: '+txt);
		qtdeAntiga = txt;
	}	

    function ajaxInit() {
        var req;
        try {
            req = new ActiveXObject("Microsoft.XMLHTTP");
        } 
		catch(e) {
			try {
				req = new ActiveXObject("Msxml2.XMLHTTP");
			} 
			catch(ex) {
				try {
					req = new XMLHttpRequest();
				} 
				catch(exc) {
					alert("Esse browser não tem recursos para uso do Ajax");
					req = null;
				}
				
			}  
			
		}
    	return req;
	}
    

	
	function alteraqtduni(uni_codigo, old_qtde, uni_qtde, max_qtde, id_medico, periodo, id_login, gex_codigo, proc_codigo, total,res)
	{
		
		if (Number(uni_qtde) <= -1) { 
    
			alert("Quantidade deve ser maior que zero!"); 
			document.getElementById('frm_uni_qtde' + uni_codigo).value = old_qtde; 
			document.getElementById('frm_uni_qtde' + uni_codigo).focus(); 
			return false; 

		}
		var endereco='../ajax/update/agendamento/grade_exame_unidade_ajax.php?uni_codigo='+uni_codigo+
											'&uni_qtde='+uni_qtde+
											'&periodo='+periodo+
											'&cod_medico='+id_medico+
											'&proc_codigo='+proc_codigo+
											'&id_login='+id_login+
											'&gex_codigo='+gex_codigo+ 
											'&total='+total;
		var endereco2 = '../exa_distribuicao_vagas_estouradas.php?uni_codigo='+uni_codigo+
																 '&periodo='+periodo+
																 '&proc_codigo='+proc_codigo;
		ajax_tudo(endereco2, preenche);
											
		if(document.getElementById('teste').innerHTML < '0') {
 		   document.getElementById('frm_uni_qtde' + uni_codigo).value = ''; 
		   alert('Quantidade limitada pela unidade esgotada!');
		}

		ajax = ajaxInit();

		if(ajax) {
			ajax.open("GET", endereco, true);
			
			ajax.onreadystatechange = function() {
				if(ajax.readyState == 4) {
					if(ajax.status == 200) {
						document.getElementById('teste').innerHTML = ajax.responseText;
						if(document.getElementById('teste').innerHTML < document.getElementById('frm_uni_qtde'+uni_codigo).value || document.getElementById('teste').innerHTML < 0 )
						{
							y = document.getElementById('frm_uni_qtde'+uni_codigo);
	//						y.value = qtdeAntiga;
							y.value = "";
							y.focus();								
							
						}            
					}
				}    
			ajax.send(null);
			}
		}else {
			alert(ajax.statusText);
		}
	}


function validarVagas(){
	alert('entrou aqui');
	var qtdes;
//	var frm_uni_qtde = new array();
	for (i=0; i < document.form.elements.length; i++){
		if (document.form.elements[i].type="text"){
			alert("Isso aqui é um text e eu posso fazer as minhas rotinas aqui");
		}
	}
	/*
	frm_uni_qtde = document.getElementsByName('frm_uni_qtde[]');
	for (var i in frm_uni_qtde){
//		var aux = frm_uni_qtde[i];
		alert(i+": "+frm_uni_qtde[i]);
//		document.getElementById(frm_uni_qtde[i]).value;
	}
/*	for (var j in qtdes){
		alert(qtdes[j]);// = document.getElementById("frm_uni_qtde[]").value;
	}*/
}

</script>
<?

//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>
       $per = explode("-",$gex_periodo);
       $period = $per[1]."/".$per[0];

	$sql2 = pg_query(" select count(graex_qtde) as cont, 
							  graex_qtde
						 from grade_exame 
						where to_char(graex_data, 'mm/yyyy') = '$period' 
						  and proc_codigo = '$proc_codigo'
						group by graex_qtde
						order by cont desc");
	$result2 = pg_fetch_array($sql2);

	$sql4 = pg_query("SELECT SUM(X.MAX) as total
						FROM (
							 SELECT MAX(GRAEXUNI_QTDE) AS MAX
							   FROM GRADE_EXAME_UNIDADE
							  WHERE TO_CHAR(GRAEXUNI_DATA, 'MM/YYYY') = '$period'
								AND PROC_CODIGO = '$proc_codigo'
							  GROUP BY UNI_CODIGO
							  ) AS X");
	$result4 = pg_fetch_array($sql4);
	
	$total = $result2[graex_qtde] - $result4[total];
/*	echo $result2[graex_qtde] ."<br>";
	echo $result4[total] ."<br>";
	echo $total ."<br>";*/
	$res1 = $result4[total];
	//echo $total;
	if($total < $result4[total]){
	//echo"quantidade ultrapassada";
	
	}
	

 echo "<table width=100% higth=80%  cellspacing=1 cellpadding=4 border=0>\n
      <tr>
	<td bgcolor=#000000 colspan=2 align=right><font color=white size=3><b>Vagas Disponiveis:</b> <label style='font-weight:bold;color:red;font-size:20px' id='teste'>$total</label></font></td>
      </tr>
	<tr bgcolor=CCCCCC>\n
		<td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>\n
        <font color=red>Unidade</font></td>\n
	    <td width=100 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Vagas p/ Unidade</font></td>\n
	</tr>\n";
$i=0;
$dt = mesAno($periodo);

echo "<form name='ff' method='get' action='$PHP_SELF'>\n
      <input type=hidden name='gex_codigo' id='gex_codigo' value='' \>\n
      <input type=hidden name='id_login' id='$id_login' value='' \>\n
	  <input type='hidden' name='total' id='total' value='$total'>";
	  

$sql = pg_query("select uni_desc, uni_codigo from unidade");

while ($result = pg_fetch_array($sql)){
echo "<tr bgcolor=FFFFFF>\n
		<td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'>&nbsp;";
             echo ($result[0] == "" ? "&nbsp;" : $result[0]);
echo "</td>";

	$sql3 = pg_query("select count(graexuni_qtde) as cont, 
							 graexuni_qtde
						from grade_exame_unidade
					   where to_char(graexuni_data, 'mm/yyyy') = '$period' 
						 and proc_codigo = '$proc_codigo'
						 and uni_codigo = '$result[uni_codigo]'
					   group by graexuni_qtde
					   order by cont desc");
	$result3 = pg_fetch_array($sql3);
	
echo "<td width=40 style='border:1px solid #cccccc'>\n";
echo "<input type=text id='frm_uni_qtde$result[uni_codigo]' name=uni_qtde value='$result3[graexuni_qtde]' defautValue='$result3[graexuni_qtde]' class=boxagente onchange=\"alteraqtduni('$result[uni_codigo]','$result3[graexuni_qtde]',this.value,'$total','$med_codigo','$periodo','$id_login', '$gex_codigo','$proc_codigo','$total',this.defautValue)\" ><b><font color=$cor size=2></b>\n";
echo "</td></tr>";	
}
echo "</form>\n
      </table>\n";
