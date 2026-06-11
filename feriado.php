<?php
require_once "global.php";

echo $common->incJquery();
?>
<script src='/WebSocialComum/library/js/jquery.maskedinput-1.3.min.js'></script>
<script>
	jQuery(function(jQuery){
		
		jQuery.mask.definitions['d']='[0-3]';
		jQuery.mask.definitions['m']='[01]';
		jQuery.mask.definitions['a']='[12]';
	    // Mascaras
	    $('#usu_rg').mask('99.999.999-9'); //RG
	    jQuery("input.mask").each(function(){
	        jQuery(this).mask( jQuery(this).attr("rel") );
	    });

	});
	function swap(i,j,v){
		if(v == 'S') {
			document.getElementById(i).style.display = "none";
			document.getElementById(j).style.display = "block";
		} else {
			document.getElementById(i).style.display = "block";
			document.getElementById(j).style.display = "none";
		}
	}
</script>
<?php 
echo $common->menuTab(array('Cadastro Feriados'));
echo $common->bodyTab('1');
	if($acao == ""){
		echo $common->commonButton("Adicionar",$PHP_SELF."?acao=form_add","adicionar.png");
			echo $table->openTable("lista");
				echo $table->criaLinha(array("C&oacute;digo","Feriado","Data","&nbsp;"),null,array("","","","2"),"S");
				$sqlSec = "select *, to_char(fer_data, 'dd/mm') as new_data,to_char(fer_data, 'dd/mm/yyyy') as fer_data,to_char(fer_data, 'yyyy') as ano from feriado  order by fer_codigo desc";
				$qrySec = pg_query($sqlSec);
				while($linha = pg_fetch_array($qrySec)){
					if($linha[ano]=="9999") { 
						$dt = $linha[new_data]; 
					} else { 
						$dt = $linha[fer_data]; 
					}
			   echo $table->criaLinha(array("$linha[fer_codigo]","$linha[fer_nome]","$dt",
					$common->commonButton("Editar",$PHP_SELF."?acao=form_edit&fer_codigo=$linha[fer_codigo]","editar_on.png"),
					$common->commonButton("Apagar",$PHP_SELF."?acao=deletar&fer_codigo=$linha[fer_codigo]","apagar.png")));
				}
			echo $table->closeTable();
	}
	if(($acao == "form_add" OR $acao == "form_edit")){
   echo $form->openForm($PHP_SELF,'POST','form');
		if($acao=="form_add") {
		  echo $form->hiddenForm("acao", "salvar");
				$campo_data .= "<div id=cmp style='display:none'>".$form->inputText('fer_data_nova', $rr['fer_data'], "Data", 10, 10, NULL, NULL, NULL, NULL, NULL, NULL, "mask inputForm", "d9/m9/a999")."</div>";
				$campo_data .= "<div id=cmp2>".$form->inputText('fer_data', $rr['fer_data'], "Data", 6, 5, NULL, NULL, NULL, NULL, NULL, NULL, "mask inputForm", "d9/m9")."</div>";
		} else {
		  echo $form->hiddenForm("acao", "edita");
		  echo $form->hiddenForm("fer_codigo", $fer_codigo);
		  $rr = pg_fetch_array(pg_query("select *, to_char(fer_data, 'dd/mm/yyyy') as fer_data from feriado where fer_codigo = '$fer_codigo'"));
		  	if($rr['fer_facultativo']=="S") {			
				$campo_data .= "<div id=cmp>".$form->inputText('fer_data_nova', $rr['fer_data'], "Data", 10, 10, NULL, NULL, NULL, NULL, NULL, "dt1", "mask inputForm", "d9/m9/a999")."</div>";
				$campo_data .= "<div id=cmp2 style='display:none'>".$form->inputText('fer_data', $rr['fer_data'], "Data", 6, 5, NULL, NULL, NULL, NULL, NULL, "dt2", "mask inputForm", "d9/m9")."</div>";
		  	} else {
				$campo_data .= "<div id=cmp style='display:none'>".$form->inputText('fer_data_nova', $rr['fer_data'], "Data", 10, 10, NULL, NULL, NULL, NULL, NULL, "dt1", "mask inputForm", "d9/m9/a999")."</div>";
		  		$campo_data .= "<div id=cmp2>".$form->inputText('fer_data', $rr['fer_data'], "Data", 6, 5, NULL, NULL, NULL, NULL, NULL, "dt2", "mask inputForm", "d9/m9")."</div>";
			}			
		}
				echo $form->inputText('fer_nome',$rr[fer_nome],'Nome da feriado',30,30,'');
				echo $form->inputCheckboxRadio("fer_facultativo", ($rr['fer_facultativo'] == "S" ? "S" : "N"), "Facultativo", "onChange=\"swap('cmp2','cmp',this.value)\"", array("S"=>"Sim", "N"=>"Nao"), "radio");
				echo $campo_data;
				echo"<br><br><div style='float:left;width:98px;'>&nbsp;</div><div style='float:left;'>";		
				echo $common->commonButton("voltar",$PHP_SELF,"voltar.png");
				echo"</div>";
				echo"<div style='float:left;'>";
				echo $common->commonButton("Salvar","","report.png","onclick='document.form.submit();'");
				echo"</div><br><br>";
				
				echo $form->closeForm();    
	}
	if($acao == "salvar"){
		 if($fer_facultativo == "N") { 
		 	$f_data = $fer_data."/9999"; 
		 } else {
		 	$f_data = $fer_data_nova;
		 }
		 $sql = "INSERT INTO feriado ( 
					fer_data, 
					fer_nome, 
					fer_facultativo 
					 ) VALUES ( 
					'$f_data', 
					UPPER('$fer_nome'), 
					'$fer_facultativo')";

		 		 $query = pg_query($sql) or die(pg_last_error());
			echo $common->modalMsg("OK","Feriado Salva Com Sucesso!",$PHP_SELF);	

	}
	if($acao == "edita"){
		 if($fer_facultativo=="N") { 
		 	$f_data = $fer_data."/9999"; 
		 } else {
		 	$f_data = $fer_data_nova;
		 }
		$sql = "UPDATE feriado SET
					fer_data = '$f_data', 
					fer_nome = UPPER('$fer_nome'), 
					fer_facultativo = '$fer_facultativo'
				WHERE fer_codigo = $fer_codigo";
		$query = pg_query($sql);
		echo $common->modalMsg("OK","feriado Salva Com Sucesso!",$PHP_SELF);	
	}
	if($acao == "deletar") {
		$getQuery = pg_query("select * from feriado where fer_codigo = $fer_codigo");
		$getName = pg_fetch_array($getQuery);
		echo $common->modalConfirm("Deseja deletar a feriado $getName[fer_nome]","feriado.php?acao=del&fer_codigo=$fer_codigo","feriado.php");
	}	
	
	if($acao == "del") {
		$sqlDel = "delete from feriado where fer_codigo = $fer_codigo";
		$qryDel = pg_query($sqlDel);
		echo $common->modalMsg("OK","feriado Excluida com Sucesso!","feriado.php");
	}
echo $common->closeTab();


?>

