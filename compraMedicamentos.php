<script type="text/javascript" src="/WebSocialComum/library/js/jquery-1.6.2.min.js"></script>
<script src='/WebSocialComum/library/js/jquery.maskedinput-1.3.min.js'></script>
<script type='text/javascript' src='/WebSocialComum/library/js/tiny_mce/tiny_mce.js'></script>
<script type="text/javascript" src="/WebSocialSaude/lib/ui/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="/WebSocialComum/library/js/jquery.buscar.js"></script>
<script type="text/javascript" src="/WebSocialSaude/zf/public/js/jquery.validate.min.js"></script>
<link rel="stylesheet" href="/WebSocialSaude/lib/themes/base/jquery.ui.all.css">
<link rel="stylesheet" href="/WebSocialSaude/lib/themes/ui-lightness/jquery-ui-1.8.10.custom.css">
<link rel="stylesheet" href="/WebSocialSaude/lib/demos.css">
<script src="ajax_motor.js"></script>
<script>
function calcula(){
	var compi_quantidade = document.getElementById("compi_quantidade").value;     
    var compi_valor = document.getElementById("compi_valor").value;     
      
    if((compi_quantidade == "" || compi_quantidade == null) && (compi_valor == "" || compi_valor == null))  
        return false;  
  
    //while(compi_quantidade.indexOf(',') != -1)  
    	compi_quantidade = compi_quantidade.replace(',','.');  
  
    //while(compi_valor.indexOf(',') != -1)  
    	compi_valor = compi_valor.replace(',','.');  
    	//num.toFixed(2)
    var valor_total = parseFloat(compi_quantidade*compi_valor);     
    document.getElementById("compi_valor_total").value = valor_total;  
	//alert(valor_total);
}
$(function(){
	$("#btnsave").hide();
	$("#tabs").tabs();
	$("#for_nome").buscar({
		tipo:"fornecedor",
		template: function(ul, item){
			return jQuery("<li></li>").data("item.autocomplete", item).append(
					"<a><strong>" + item.label + "</strong>"						
							+ "</a>&nbsp;").appendTo(ul);
		}
	}),
	$("#usu_nome").buscar({
			tipo:'usuario'

	})
	$("#usu_nome").buscar({
		callback: function(event, ui){
			var usu_codigo = $("#usu_codigo").val();

			if(ui.item){
				$("#btnsave").show("normal");
			}
		}
	});
	$.datepicker.regional['pt-BR'] = {
        closeText: 'Fechar',
        prevText: '&#x3c;Anterior',
        nextText: 'Pr&oacute;ximo&#x3e;',
        currentText: 'Hoje',
        monthNames: ['Janeiro','Fevereiro','Mar&ccedil;o','Abril','Maio','Junho',
        'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
        monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun',
        'Jul','Ago','Set','Out','Nov','Dez'],
        dayNames: ['Domingo','Segunda-feira','Ter&ccedil;a-feira','Quarta-feira','Quinta-feira','Sexta-feira','Sabado'],
        dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sab'],
        dayNamesMin: ['Dom','Seg','Ter','Qua','Qui','Sex','Sab'],
        weekHeader: 'Sm',
        dateFormat: 'dd/mm/yy',
        firstDay: 0,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''
};
$.datepicker.setDefaults($.datepicker.regional['pt-BR']);
$("input.data").datepicker();
$("input.data-mes-ano").datepicker( {
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        dateFormat: 'mm/yy',
onClose: function(dateText, inst) { 
    var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
    var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
    $(this).datepicker('setDate', new Date(year, month, 1));
}
});
});
function formata_moeda(campo,tammax,teclapres,decimal)
{
	var tecla = teclapres.keyCode;
	vr = limpar_campo_moeda(campo.value,"0123456789");
	tam = vr.length;
	dec=decimal;

	if (tam < tammax && tecla != 8)
	{
		tam = vr.length + 1 ;
	}
	
	if (tecla == 8 )
	{
		tam = tam - 1 ;
	}
	
	if ( tecla == 8 || tecla >= 48 && tecla <= 57 || tecla >= 96 && tecla <= 105 )
	{
	
		if ( tam <= dec )
		{
			campo.value = vr ;
		}
		
		if ( (tam > dec) && (tam <= 5) )
		{
			campo.value = vr.substr( 0, tam - 2 ) + "," + vr.substr( tam - dec, tam ) ;
		}
		if ( (tam >= 6) && (tam <= 8) )
		{
			campo.value = vr.substr( 0, tam - 5 ) + "." + vr.substr( tam - 5, 3 ) + "," + vr.substr( tam - dec, tam ) ;
		}
		if ( (tam >= 9) && (tam <= 11) )
		{
			campo.value = vr.substr( 0, tam - 8 ) + "." + vr.substr( tam - 8, 3 ) + "." + vr.substr( tam - 5, 3 ) + "," + vr.substr( tam - dec, tam ) ;
		}
		if ( (tam >= 12) && (tam <= 14) )
		{
			campo.value = vr.substr( 0, tam - 11 ) + "." + vr.substr( tam - 11, 3 ) + "." + vr.substr( tam - 8, 3 ) + "." + vr.substr( tam - 5, 3 ) + "," + vr.substr( tam - dec, tam ) ;
		}
		if ( (tam >= 15) && (tam <= 17) )
		{
			campo.value = vr.substr( 0, tam - 14 ) + "." + vr.substr( tam - 14, 3 ) + "." + vr.substr( tam - 11, 3 ) + "." + vr.substr( tam - 8, 3 ) + "" + vr.substr( tam - 5, 3 ) + "," + vr.substr( tam - 2, tam ) ;
		}
	}	
}
function limpar_campo_moeda(valor, validos)
{
	// retira caracteres invalidos da string
	var result = "";
	var aux;
		for (var i=0; i < valor.length; i++) {
			aux = validos.indexOf(valor.substring(i, i+1));
			if (aux>=0) {
				result += aux;
			}
		}
	return result;
}
function deleteItem(comp_codigo,compi_codigo){
	url = "compraMedicamentoDelItem.js.php?comp_codigo=" + comp_codigo + "&compi_codigo=" + compi_codigo;
	ajax_tudo(url, responde);
}
function responde(txt){
	resp = txt.split('|');
	comp_codigo = resp[0];
	compi_codigo = resp[1];
	alert('Exluido com Sucesso!');
	url = "compraMedicamentos.php?acao=addItem&comp_codigo=" + comp_codigo + "&compi_codigo=" + compi_codigo;
	setTimeout("location='" + url + "'", 0)
}
</script>

<?php
	include "global.php";
	$form = new classForm();
	$common = new commonClass();
	
	echo $common->menuTab(ARRAY("Cadastro de compra de produto"));
	echo $common->bodyTab('1');
if(!empty($_GET[comp_codigo])) {
	$rr = pg_fetch_array(pg_query("select to_char(comp_data,'dd/mm/YYYY') as dt,*from compra_produto as cp join fornecedor as f on f.for_codigo = cp.for_codigo join usuario as usu on usu.usu_codigo = cp.usu_codigo where comp_codigo = '$comp_codigo'"));
		$for_nome = $rr[for_nome];
		$for_codigo = $rr[for_codigo];
		$usu_nome = $rr[usu_nome];
		$usu_codigo = $rr[usu_codigo];
		$dt = $rr[dt];
}		
		echo $form->openForm('','POST','compra');
			echo $form->hiddenForm('acao', 'add');
			     $dt = date('d/m/Y');
			echo $form->inputText('comp_data',$dt,'Data','12','10',null,null,null,null,null,null,'inputForm data');
			echo $form->inputText('for_nome',$for_nome,'Fornecedor','60');
			echo $form->hiddenForm('for_codigo', $for_codigo);
		
			echo $form->inputText('usu_nome',$usu_nome,'Paciente','60');
			echo $form->hiddenForm('usu_codigo', $usu_codigo);
			if($acao == "add"){
				$select = pg_query("select * from nextval('compra_produto_comp_codigo_seq') as comp_codigo");
				$res = pg_fetch_array($select);
				$comp_codigo = $res['comp_codigo'];
			}
			if($acao == "add" || $acao == "addItem" ){	
					
				echo $form->hiddenForm('comp_codigo', $comp_codigo);
				echo $form->hiddenForm('acao', 'addItem');
				echo $form->inputText('pro_nome','','Produto','60');
				echo $form->inputText('compi_valor',NULL,'Valor <small>(R$ unitário)</small>','60',NULL,"onKeyDown='return formata_moeda(this, 20, event, 2)'");
				echo $form->inputText('compi_quantidade',NULL,'Quantidade','60',NULL,"onChange='return calcula()'");
				echo $form->inputText('compi_valor_total',NULL,'Valor Total','60','4',"onChange='return formata_moeda(this, 20, event, 2)'");
			
			}
			echo "
			<div id='btnsave' style='clear:both; width:400px; border:solid 0px;'>";
				echo"<div style='float:right; width:205px;'>";		
					echo $common->commonButton("Salvar", null, "salvar.gif", "onclick=\"document.compra.submit();\"");
				echo"</div>";
				echo"<div style='float:right'>";
					echo $common->commonButton("voltar","compraMedicamentosIndex.php","voltar.png");
				echo"</div>";
			echo"</div>";
if(($acao=="add" OR $acao == "addItem")) {
			echo "
			<div id='btnsave' style='clear:both; width:400px; border:solid 0px;'>";
				echo"<div style='float:right; width:205px;'>";		
					echo $common->commonButton("Salvar", null, "salvar.gif", "onclick=\"document.compra.submit();\"");
				echo"</div>";
				echo"<div style='float:right'>";
					echo $common->commonButton("voltar","compraMedicamentosIndex.php","voltar.png");
				echo"</div>";
			echo"</div>";
}
				
		echo $form->closeForm();
	
		echo "</table><br><br><br>";
		if($acao == "add"){			
			//echo "<pre>".print_r($_REQUEST,1);
			$insert = "INSERT 
						 INTO compra_produto(comp_codigo,
						 					 for_codigo,
						 					 usu_codigo,
						 					 usr_codigo,
						 					 comp_data)
						 			 VALUES($comp_codigo,
											$for_codigo,
						 			 		$usu_codigo,
						 			 		$id_login,
						 			 		'$comp_data')";
			//echo $insert."asdfasf";exit;
			pg_query($insert) or die(pg_last_error());
			
			echo $form->hiddenForm('comp_codigo', $comp_codigo);	
			
		}

		if($acao == "addItem"){
	if(!empty($pro_nome)) {
			$compi_valor = str_replace(",",".",$compi_valor);
			$insertItem = "INSERT 
			 				 INTO compra_produto_itens(comp_codigo,
			 				       pro_nome,
								   compi_quantidade,
								   compi_valor)
							VALUES($comp_codigo,
								   '$pro_nome',
								   $compi_quantidade,
								   $compi_valor)";
								   
			//echo $insertItem;
			pg_query($insertItem) or die(pg_last_error());
	}
			//$pro_codigo = '';
			//$compi_quantidade = '';
			//$compi_valor = '';
			//exit;
			//echo "<pre>".print_r($_REQUEST,1);
		}

		if($acao == "add" || $acao == "addItem" ){
			$select = "SELECT compi_codigo,compi_valor,
						      compi_quantidade,
							  pro_nome,
							  comp_codigo
						 FROM compra_produto_itens c
						WHERE comp_codigo = $comp_codigo";
			
			$query = pg_query($select);
			//echo $select;
			echo "<form><table class=lista>
					<tr>
						<th>Nome</th>
						<th>Quantidade</th>
						<th>Valor</th>
						<th colspan=3>Opções</th>
					</tr>";
			$num = pg_num_rows($query);
			if($num >0){
			while($res=pg_fetch_array($query)){
				echo"
					<tr>
						<td>$res[pro_nome]</td>
						<td>$res[compi_quantidade]</td>
						<td>$res[compi_valor]</td>
						<td width=30>"; echo $common->commonButton("Apagar",null,"apagar.png","OnClick=\"deleteItem('$res[comp_codigo]','$res[compi_codigo]');\""); echo"</td>
					</tr>";
			}}
			else{
				echo"
					<tr>
						<td colspan='3'>Nenhum registro encontrado</td>						
					</tr>";
			}
			echo "</table>
					</form>";
		}
		
		
		
		
	echo $common->closeTab();	
	
	?>