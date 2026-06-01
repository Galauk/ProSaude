<?php
session_start();
include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/debug.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
include_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
include_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/__array.php";

$common = new commonClass();
$table = new tableClass();
$form = new classForm();
echo $common->incJquery();
?>
<style>
.inputTextarea{
	font-family: Verdana, Arial, Helvetica, sans-serif;
	color: #153854;
	font-size: 8pt;
	font-weight: bold;	
	height: 18px;
    border-top: 1px solid #B0CCE5;
    border-left: 1px solid #B0CCE5;
    border-bottom: 1px solid #B0CCE5;
    border-right: 1px solid #B0CCE5;
    background-color:#E8F4FE;
	text-transform: uppercase;
	height:100px;
}
.registros td{
	 background-color:#E8F4FE;
	 	font-family: Verdana, Arial, Helvetica, sans-serif;
	color: #153854;
	border:1px dotted #B0CCE5;
}
</style>
<link rel="stylesheet" href="/WebSocialSaude/lib/themes/base/jquery.ui.all.css">
<link rel="stylesheet" href="/WebSocialSaude/lib/themes/ui-lightness/jquery-ui-1.8.10.custom.css">
<script type="text/javascript" src="/WebSocialComum/library/js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="/WebSocialSaude/lib/ui/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="/WebSocialComum/library/js/jquery.buscar.js"></script>
<script>
function valida() {
	if(document.addmedicamento.pro_codigo.value == '' ) { alert("Preencha o Produto"); document.addmedicamento.pro_codigo.focus(); return false;}
	if(document.addmedicamento.inp_qtde_dose.value == '' ) { alert("Preencha A dose ou Quantidade"); return false;}
	document.addmedicamento.submit()
}
<!--
function Mascara_Hora(Hora){ 
var hora01 = ''; 
hora01 = hora01 + Hora; 
if (hora01.length == 2){ 
hora01 = hora01 + ':'; 
document.forms[0].Hora.value = hora01; 
} 
if (hora01.length == 5){ 
Verifica_Hora(); 
} 
} 
           
function Verifica_Hora(){ 
hrs = (document.forms[0].Hora.value.substring(0,2)); 
min = (document.forms[0].Hora.value.substring(3,5)); 
               
estado = ""; 
if ((hrs < 00 ) || (hrs > 23) || ( min < 00) ||( min > 59)){ 
estado = "errada"; 
} 
               
if (document.forms[0].Hora.value == "") { 
estado = "errada"; 
} 
 
if (estado == "errada") { 
alert("Hora inválida!"); 
document.forms[0].Hora.focus(); 
} 
} 


$(function(){

	$("#buscar").buscar({
		tipo: "medicamentos",
		callback: function(event, ui){
			var pro_codigo = $("#pro_codigo").val();
			var umed_nome = $("#umed_nome").val();

				$("#umed_codigo").val(umed_nome);
				$('#inp_qtde_dose').focus();
				
		}
	});

});

function del_med(inp_codigo) {
  $.ajax({
    type: 'POST',
    dataType: 'json',
    url: 'deleta_prodInternaobs.php?inp_codigo='+inp_codigo,
    async: true,
    success: function(response) {
		alert("Excluido com Sucesso!");
		location.reload(true);
    }
  });
  return false;
}


function removeall(cod) {
  $.ajax({
    type: 'POST',
    dataType: 'json',
    url: 'remove_internacao.php?io_codigo='+cod,
    async: true,
    success: function(response) {
		alert("Excluido com Sucesso!");
		location.reload(true);
    }
  });
  return false;
}


function add_internacao() {
	var age = $('#age_codigo').val();
	var io = $('#io_codigo').val();
	var obs = $('#io_observacao').val();
	var qua = $('#qua_codigo').val();
	
	$('#prescricao').show();
  $.ajax({
    type: 'POST',
    dataType: 'json',
    url: 'add_internacao.php?age_codigo='+age+'&io_codigo='+io+'&io_observacao='+obs+'&qua_codigo='+qua,
    async: true,
    success: function(data) {
		$('#io_codigo').val(data.io_codigo);
		$('#buscar').focus();
		$('#prescrever_btn').hide(1);	

    }
  });
  return false;
}

function RemoveTableRow(handler,cod) {

    var tr = $(handler).closest('tr');

    tr.fadeOut(400, function(){ 
      tr.remove(); 
    }); 

  $.ajax({
    type: 'POST',
    dataType: 'json',
    url: 'remove_medicamento.php?inp_codigo='+cod,
    async: true,
    success: function(response) {
		
    }
  });	
	
	
    return false;
}
function add_med() {

		var io = $('#io_codigo').val();
		var age = $('#age_codigo').val();		
		var pro = $('#pro_codigo').val();		
		var nprod = $('#buscar').val();
		var dose = $('#inp_qtde_dose').val();
		var umed = $('#umed_codigo').val();
		var velo = $('#inp_velocidade').val();
		var adm = $('#adm_codigo').val();
		var admnome = $( "#adm_codigo option:selected" ).text();
		var frq = $('#frq_codigo').val();
		var frqnome = $( "#frq_codigo option:selected" ).text();
		var hrini = $('#Hora').val();
		var obs = $('#inp_observacao').val();		
		
	 var urlen = 'io_codigo='+io+'&pro_codigo='+pro+'&age_codigo='+age+'&inp_qtde_dose='+dose+'&umed_codigo='+umed+'&inp_velocidade='+velo+'&adm_codigo='+adm+'&frq_codigo='+frq+'&inp_hrini='+hrini+'&inp_observacao='+obs;
		
  $.ajax({
    type: 'POST',
    dataType: 'json',
    url: 'add_prodInternaobs.php?'+urlen,
    async: true,
    success: function(data) {
		
					
			
		
	 var newrow = $("<tr class='registros'>");	
	 var cols = "";
	 
cols += '			 <td height="30">'+nprod+'</td>';
cols += '			 <td>'+dose+'</td>';
cols += '			 <td>'+umed+'</td>';
cols += '			 <td>'+velo+'</td>';
cols += '			 <td>'+admnome+'</td>';
cols += '			 <td>'+frqnome+'</td>';
cols += '			 <td>'+hrini+'</td>';
cols += '			 <td>'+obs+'</td>';
cols += '			 <td><button onclick="RemoveTableRow(this,'+data.inp_codigo+')" type="button">Remover</button></td>';
		
		newrow.append(cols);
		$("#medicamento").append(newrow);
		$('#form_med').each (function(){
			this.reset();
		$('#buscar').focus();
		});
		
    }
  });
  return false;
}

</script><?php 



$tabs = array("EVOLUCAO CLINICA/PRESCRICAO MEDICA - NOVA");
$k = array();
	$q = pg_query("select to_char(io_data_cadastro,'dd/mm/yyyy') as dt,to_char(io_data_cadastro,'dd/mm/yyyy hh24:mi') as dt_internacao,*from internacao_observacao where age_codigo = '".$_REQUEST['age_codigo']."' order by  io_data_cadastro desc");
	$t = pg_num_rows($q);
	$i=1;
	while($r=pg_fetch_array($q)) {
		$i++;
			array_push($k,$r );
		if($i==$t) {
			array_push($tabs,$r[dt_internacao] );
		} else {
			array_push($tabs,$r[dt_internacao] );
		}
		$iocod = $r[io_codigo];
		$data_inter = $r[dt];
	}
	echo $common->menuTab($tabs);
	
	$y=-1;
		for($j=2;$j<=$t+1;$j++) {
			$y++;
		echo $common->bodyTab($j);
	//	echo "<pre>"; print_r($k[$y]); echo "</pre>";
	#	echo "TAB" . $j;
	
	
	$cod = $k[$y][qua_codigo];
			$rw = pg_fetch_array(pg_query("select *from quarto where qua_codigo = $cod"));
			echo "<h1>EVOLUCAO CLINICA</h1><b>Localizacao Paciente:</b> $rw[apt_codigo]<br><br>".nl2br($k[$y][io_observacao])."";
    echo "<br><br><h1>PRESCRICAO MEDICA</h1>
			<table border=0 cellspacing=1 cellpadding=5 border=0>
			<tr>
			 <td>Medicamento</td>
			 <td>Qtde/Dose</td>
			 <td>Unidade</td>
			 <td>Velocidade</td>
			 <td>Via acesso</td>
			 <td>Frequencia</td>
			 <td>Hr Inicio</td>
			 <td>Observacao</td>
			<tr>";
$sql = pg_query("select *from internacao_prescricao as ip join produto as p on p.pro_codigo=ip.pro_codigo join tb_administracao_produto as adp on adp.adm_codigo = ip.adm_codigo join frequencia_medicacao as fr on fr.frq_codigo = ip.frq_codigo join unidmedida as um on um.umed_codigo = p.umed_codigo where io_codigo = ".$k[$y][io_codigo]." ");
while($rr = pg_fetch_array($sql)) {
echo "<tr class='registros' style='height:30px;'>
			 <td>$rr[pro_nome]</td>
			 <td>$rr[inp_qtde_dose]</td>
			 <td>$rr[umed_nome]</td>
			 <td>$rr[inp_velocidade]</td>
			 <td>$rr[adm_sigla]</td>
			 <td>$rr[frq_nome]</td>
			 <td>$rr[inp_hrini]</td>
			 <td>$rr[inp_observacao]</td>
			<tr>";
}
echo "</table>";	

$dt = date("d/m/Y");

if($dt==$data_inter) {
	echo "<br><br><td>".$common->commonButton("Excluir Evolucao",null,"apagar.png","onClick=\"removeall(".$k[$y][io_codigo].")\"")."</td>";
}
	echo "".$common->commonButton("Voltar",null,"voltar.png","onClick=\"javascript:history.go(-1)\"")."";

		echo $common->closeTab();
	}	

	
		echo $common->bodyTab("1");

if($_REQUEST['acao']!="novo") {
	$rw = pg_fetch_array(pg_query("select *from internacao_observacao where age_codigo = '".$_REQUEST['age_codigo']."' order by  io_data_cadastro desc"));
	$qua = $rw[qua_codigo];
} else {
	$rr = pg_fetch_array(pg_query("select *from internacao_observacao where age_codigo = '".$_REQUEST['age_codigo']."' order by  io_data_cadastro desc"));
	$qua = $rr[qua_codigo];
}
			echo "<h1>EVOLUCAO CLINICA</h1>"; 
						echo "Localizacao Paciente:&nbsp;<select name=qua_codigo class='inputForm' id='qua_codigo'>";
			$qqq = pg_query("select *from quarto");
			while($rw = pg_fetch_array($qqq)) {
				echo ($qua==$rw[qua_codigo])?"<option value='$rw[qua_codigo]' selected>$rw[apt_codigo]</option>":"<option value='$rw[qua_codigo]'>$rw[apt_codigo]</option>";
			}
			echo "</select><br><br>";

			echo "<textarea name='io_observacao' id='io_observacao' class='inputTextarea' cols='125' rows='8'>".$rw[io_observacao]."</textarea>";
			
	   echo "<div id='prescrever_btn'><td colspan='7' align='center'".$common->commonButton("Adicionar Prescricao",null,"Export.png","onClick=\"add_internacao()\"")."</td></div>";
			

		echo "<form method=post action='".$PHP_SELF."' name='addmedicamento' id='form_med'>";
    echo "<div id='prescricao' style='display:none'><br><br><h1>PRESCRICAO MEDICA</h1>
		  <input type=hidden name=umed_nome id=umed_nome>
			<table border=0 cellspacing=1 cellpadding=5 border=0 id='medicamento'>
			<tr>
			 <td>Medicamento</td>
			 <td>Qtde/Dose</td>
			 <td>Unidade</td>
			 <td>Velocidade</td>
			 <td>Via acesso</td>
			 <td>Frequencia</td>
			 <td>Hr Inicio</td>
			 <td>Observacao</td>
			 <td width=100>&nbsp;</td>
			<tr>";
	echo "
			<input type=hidden name=io_codigo id=io_codigo value='".$rw['io_codigo']."'>
			<input type=hidden name=pro_codigo id=pro_codigo>
			<input type=hidden name=med_codigo value='".$rr['med_codigo']."'>
			<input type=hidden name=uni_codigo value='".$rr['uni_codigo']."'>
			<input type=hidden name=age_codigo id='age_codigo' value='".$_REQUEST['age_codigo']."'>
			<input type=hidden name='action' id='action' value='addmed'>
			<td><input type=text name=med_nome class='inputForm' size='30' id='buscar'></td>";		
	echo "<td><input id='inp_qtde_dose' type=text name=inp_qtde_dose class='inputForm' size='10' maxlength='4'></td>";		
	echo "<td><input type=text name='umed_codigo' id='umed_codigo' class='inputForm' size='10' maxlength='4' readonly></td>";		
	
	echo "<td><input type=text name=inp_velocidade id='inp_velocidade' class='inputForm' size='10' ></td>";		
	echo "<td><select name='adm_codigo' class='inputForm' id='adm_codigo'>";
		$quni=pg_query("select *from tb_administracao_produto where adm_sigla !='' order by adm_sigla");
		while($un=pg_fetch_array($quni)) {
			echo "<option value='$un[adm_codigo]'>$un[adm_sigla]</option>";
		}
	echo "</select></td>";		
	echo "<td><select name='frq_codigo' class='inputForm' id='frq_codigo'>";
		$quni=pg_query("select *from frequencia_medicacao order by frq_nome");
		while($un=pg_fetch_array($quni)) {
			echo "<option value='$un[frq_codigo]'>$un[frq_nome]</option>";
		}
	echo "</select></td>";		
	echo "<td><input type=text name='inp_hrini' id='Hora' OnKeyUp='Mascara_Hora(this.value)' maxlength='5' class='inputForm' size='6'></td>";		
	echo "<td><input type=text id='inp_observacao' name=inp_observacao class='inputForm' size='30' style='text-transform: uppercase;' onBlur='add_med()'></td>";
	echo "<td>".$common->commonButton("Add",null,"Export.png","onClick=\"add_med()\"")."</td>";
echo "</tr></form>";






$sql = pg_query("select *from internacao_prescricao as ip join produto as p on p.pro_codigo=ip.pro_codigo join tb_administracao_produto as adp on adp.adm_codigo = ip.adm_codigo join frequencia_medicacao as fr on fr.frq_codigo = ip.frq_codigo join unidmedida as um on um.umed_codigo = p.umed_codigo where io_codigo = ".$rw['io_codigo']." ");
while($rr = pg_fetch_array($sql)) {
echo "<tr class='registros'>
			 <td>$rr[pro_nome]</td>
			 <td>$rr[inp_qtde_dose]</td>
			 <td>$rr[umed_nome]</td>
			 <td>$rr[inp_velocidade]</td>
			 <td>$rr[adm_sigla]</td>
			 <td>$rr[frq_nome]</td>
			 <td>$rr[inp_hrini]</td>
			 <td>$rr[inp_observacao]</td>
			 <th>"; echo $common->commonButton("Del",null,"delete.png","onClick=\"del_med($rr[inp_codigo])\""); echo "</th>
			<tr>";
}

echo "</table>";	

echo "<table><tr><td colspan='7' align='center'>".$common->commonButton("FINALIZAR E SAIR",null,"delete.png","onClick=\"document.location.href='../internacao.php'\"")."</td></tr></table></div>";
	echo "".$common->commonButton("Voltar",null,"voltar.png","onClick=\"javascript:history.go(-1)\"")."";


	
		echo $common->closeTab();
		
		
		
		
?>