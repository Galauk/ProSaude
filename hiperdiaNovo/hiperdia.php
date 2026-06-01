<?
session_start();
require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

include_once "funcaoBuscaUsuario.php";	
$common = new commonClass();
$form = new classForm();
$table = new tableClass();
$data= date("d/m/Y");
?>

<script language="JavaScript" type="text/javascript" src="../funcoes_busca.js"></script>
<script language="JavaScript" type="text/javascript" src="../ajax_motor.js"></script>
<script language="JavaScript" type="text/javascript" src="../funcaobuscarMedico.js"></script>
<script language="JavaScript" type="text/javascript" src="../zf/public/js/jquery-1.6.2.min.js"></script>
<script language="JavaScript" type="text/javascript" src="../zf/public/js/jquery-ui-1.8.16.custom.min.js"></script>

<link rel="stylesheet" href="/WebSocialSaude/lib/themes/base/jquery.ui.all.css">
<link rel="stylesheet" href="/WebSocialSaude/lib/themes/ui-lightness/jquery-ui-1.8.10.custom.css">

<script><!--

jQuery(function(){
	jQuery('#tabs').tabs();
	jQuery("#hipertenso").change(function() {
		verificaSeDesabilitaOuNao();
	});
	jQuery("#diabetico").change(function() {
		verificaSeDesabilitaOuNao();
	});
	verificaSeDesabilitaOuNao();

	jQuery("#dialog-acompanhamento").dialog({
		modal: true,
		width: 1200,
		//height: y,
		//beforeClose: function(event, ui) { return false; },
		close: function(){
			location.href="hiperdia.php?hiper_codigo="+$('#hiper_codigo').val()+"&acao=form_add";
		},
		buttons: {
			"Ok": function(){
				document.formModal.submit();
			},
			"Voltar": function(){
				$('#hiper_codigo').val();
				location.href="hiperdia.php?hiper_codigo="+$('#hiper_codigo').val()+"&acao=form_add";
				jQuery(this).dialog('close');
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
function verificaSeDesabilitaOuNao(){
	
	if(!jQuery("#hipertenso").is(":checked")){
		jQuery('.hipertenso').attr('disabled', 'disabled')
		.val('');
		
	}else{
		jQuery('.hipertenso').removeAttr('disabled');
	}


	if(!jQuery("#diabetico").is(":checked")){
		jQuery('.diabetico').attr('disabled', 'disabled')
		.val('');
	}else{
		 if(!jQuery("#diabetico").is(":checked")){
			jQuery('.diabetico').attr('disabled', 'disabled')
			.val('');
		 }else{
			 jQuery('.diabetico').removeAttr('disabled');
		 }
	}
}

function calculaImc(){
	var altura = document.getElementById("altura").value;
	var peso = document.getElementById("peso").value;
	
	var quadrado = (altura * altura);
    var calculo = (peso/quadrado);
}
</script>
<?
	$sqlUnidade = "select * from logon where id_login = $id_login";
	$queryUnidade = pg_query($sqlUnidade);
	$regUnidade = pg_fetch_array($queryUnidade);
	$unidade = $regUnidade[uni_codigo];
	
	$sqlTudo = "SELECT *,
					   to_char(usu_datanasc,'dd/mm/yyy') AS data
				  FROM hiperdia as hip
				  LEFT JOIN hiperdia_medicamentos AS hipmed
					ON hip.hiper_codigo = hipmed.hiper_codigo
				  JOIN usuario AS usu
				    ON usu.usu_codigo = hip.usu_codigo
				  LEFT JOIN usuarios AS usr
				    ON usr.usr_codigo = hip.usr_codigo
				 WHERE hip.hiper_codigo = $hiper_codigo";
	$queryTudo = pg_query($sqlTudo);
	$regs = pg_fetch_array($queryTudo);
	
	echo $common->menuTab(array("Dados Clinicos","Tratamento"));
	if($acao == "form_add"){
		echo $form->openForm("$PHP_SELF","POST","consultaHiper");
		if($hiper_codigo == " " || $hiper_codigo == null){
			echo $form->hiddenForm("acao","add");
		}else{
			echo $form->hiddenForm("acao","alteraHiperdia");
			echo $form->hiddenForm("hiper_codigo","$hiper_codigo");
		}
		echo "
   			 <table border='0' cellpadding=0 cellspacing=0 width='100%'>
			 	 <tr>
					   <td>
						<b>Prontuario:&nbsp;&nbsp;";
				  echo "<input type=hidden name='usu_codigo' value='$regs[usu_codigo]' id='usu_codigo'>";
				  echo "<input type=text name='pac_prontuario' id='pac_prontuario' value='$regs[usu_codigo]' class=inputForm size=10 ".($hiper_codigo != "" ? "readonly=readonly" : "").">&nbsp;
					  </td>
					  <td>
						<b>Nascimento:
						<input type=text name=pac_nascimento id=pac_nascimento value='$regs[data]' class=inputForm size=15 ".($hiper_codigo != "" ? "readonly=readonly" : "").">&nbsp;&nbsp;
						<a href='#' onclick=\"buscar_nome(\$F('pac_nascimento'), 'buscar_data');return false;\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg id=localizar align=absmiddle border=0></a>";
				echo divBuscaPaciente('../');
				echo"
					  </td>
					  <td>";									
				  echo "<b>Nome:
						<input type=text size=80 name=pac_nome id=pac_nome value='$regs[usu_nome]' class=inputForm  style=\"text-transform:uppercase;\" ".($hiper_codigo != "" ? "readonly=readonly" : "").">&nbsp;&nbsp;";
				  echo "<a href='#' onclick=\"buscar_nome(\$F('pac_nome'), 'buscar_nome');return false;\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg id=localizar align=absmiddle border=0></a>
					</td>
				</tr>
				<tr>
					<td colspan='3'>
						<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp
						M&atilde;e:
						<input type=text name='pac_mae' id='pac_mae' value='$regs[usu_mae]' class=inputForm size=80' ".($hiper_codigo != "" ? "readonly=readonly" : "").">
					</td>
				</tr>
  			</table>
				<br/>";
			echo $common->bodyTab("1");
				echo $form->openForm(null,null,"form1");
				include "dadosClinicos.php";
			echo $common->closeTab();
			echo $common->bodyTab("2");
				include "tratamento.php";	
			echo $common->closeTab();
			echo "<div style='clear:both; width:400px; border:solid 0px;'>";
					echo"<div style='float:right; width:205px;'>";		
						

					echo"</div>";
					echo"<div style='float:right'>";
					
					
					echo"</div>";
				echo"</div>";
			//echo $common->commonButton("Salvar",null,"salvar.gif","onclick=\"document.consultaHiper.submit();\"");
			//echo $common->commonButton("voltar", "pesquisaHiperdia.php?id_login=$id_login", "voltar.png");
			echo $table->openTable("lista");
				echo $table->criaLinha(array("Acompanhamentos"),null,array(4),"S");
				
				$sqlAcompanhamentos = "SELECT *,to_char(hiperac_data_consulta,'dd/mm/yyyy') as data_acompanhamento,UPPER(usr_nome) as nome
										 FROM hiperdia_acompanhamentos AS hiperac
										 LEFT JOIN usuarios AS usr
										   ON usr.usr_codigo = hiperac.usr_codigo
										WHERE hiper_codigo = $hiper_codigo
										ORDER BY hiperac_data_consulta";
				//echo $sqlAcompanhamentos;
				$queryAcompanhamentos = pg_query($sqlAcompanhamentos);
				
				while ($linhas = pg_fetch_array($queryAcompanhamentos) ){
					echo $table->criaLinha(array($linhas["data_acompanhamento"],$linhas["nome"],$common->commonButton("editar","hiperdia.php?acao=modal&hiperac_codigo=$linhas[hiperac_codigo]","editar_on.png")),array("100",null,));	
				}
				
			echo $table->closeTable();
			echo $form->closeForm();
			$pac_codigo = $_GET["usu_codigo"];
			$hiper_codigo = $_GET["hiper_codigo"];
			
			if ($hiper_codigo == ""){
				$link = "hiperdia.php?acao=modal&usu_codigo=$pac_codigo&hiper_codigo=$hiper_codigo";
				$imagem = "acompanhamento_off.png";
			}else{
				$link = "hiperdia.php?acao=modal&usu_codigo=$pac_codigo&hiper_codigo=$hiper_codigo";
				$imagem =  "acompanhamento.png";
			}
			echo $table->openTable();
				echo $table->criaLinha(array(
					 $common->commonButton("Acompanhamento", $link, $imagem),
				));
			echo $table->closeTable();
		
	}
	
	if($acao == "modal"){
		$arrayGlicemia = array("J"=>"Em Jejum","P"=>"Pos Prandial");
		if($hiperac_codigo != ""){
			$sqlModal = "SELECT *,
								to_char(hiperac_data_consulta,'dd/mm/yyyy') AS hiperac_data_consulta2 
						   FROM hiperdia_acompanhamentos 
						  WHERE hiperac_codigo = $hiperac_codigo";
		}else{
			$sqlModal = "SELECT hiperac_codigo,
								hiper_codigo,
								hiperac_cintura,
								hiperac_peso,
								hiperac_altura,
								hiperac_exame_glicemia,
								hiperac_tipo_exame_glicemia,
								hiperac_sem_complicacoes,
								hiperac_angina,
								hiperac_iam,
								hiperac_avc,
								hiperac_amputacao_diabetes,
								hiperac_doenca_renal,
								hiperac_retinopatia,
								hiperac_pe_diabetico,
								hiperac_data_consulta,
								hiperac_status_exportacao,
								hiperac_riscos,
								hiperac_hipertenso,
								hiperac_diabetico,
								usr_codigo 
						   FROM hiperdia_acompanhamentos
						  WHERE hiper_codigo = $hiper_codigo
						  ORDER BY hiperac_data_consulta 
						   DESC LIMIT 1";
		}
		$queryModal = pg_query($sqlModal);
		$numLinhas = pg_num_rows($queryModal);
		$linhaModal = pg_fetch_array($queryModal);
		
		if($numLinhas  == 0){
			$sqlConsulta = "SELECT hiper_cintura AS hiperac_cintura,
							       hiper_peso AS hiperac_peso,
							       hiper_altura AS hiperac_altura,
							       hiper_glicemia_capilar AS hiperac_exame_glicemia,
							       hiper_infarto AS hiperac_iam,
							       hiper_avc AS hiperac_avc,
							       hiper_pe_diabetico AS hiperac_pe_diabetico,
							       hiper_amputacao AS hiperac_amputacao_diabetes,
							       hiper_doenca_renal AS hiperac_doenca_renal,
							       hiper_hipertensao AS hiperac_hipertenso,
							       hiper_diabetes_1 AS hiperac_diabetico,
							       hiper_diabetes_2 AS hiperac_diabetico
							  FROM hiperdia
							 WHERE hiper_codigo = $hiper_codigo";
			$querySqlConsulta = pg_query($sqlConsulta);
			$linhaModal = pg_fetch_array($querySqlConsulta);
		}
		$sqlHiperdiaTotal = "SELECT * FROM hiperdia WHERE hiper_codigo = $hiper_codigo";
		$queryHipediaTotal = pg_query($sqlHiperdiaTotal);
		$regHiperdiaTotal = pg_fetch_array($queryHipediaTotal);
		
		$conteudoModal = "<b>Hipertenso <input type='checkbox' name='doencas[]' id='hipertenso' style='vertical-align:bottom' value='1' ".($linhaModal["hiperac_hipertenso"] == "S" ? "checked=checked" : "").">&nbsp;
						  <b>Diabetico <input type='checkbox' name='doencas[]' id='diabetico' style='vertical-align:bottom' value='2'  ".($linhaModal["hiperac_diabetico"] == "S" ? "checked=checked" : "").">".
						  $form->inputText("hiperac_pasistolica",$linhaModal["hiperac_pasistolica"],"PA Sist&oacute;lica",null,null,"style='text-align:right'").
						   $form->inputText("hiperac_padiastolica",$linhaModal["hiperac_padiastolica"],"PA Diast&oacute;lica",null,null,"style='text-align:right'").
						   $form->inputText("hiperac_cintura",$linhaModal["hiperac_cintura"],"Cintura(cm)",null,null,"style='text-align:right'").
						   $form->inputText("hiperac_peso",$linhaModal["hiperac_peso"],"Peso(Kg)",null,null,"style='text-align:right'").
						   $form->inputText("hiperac_altura",$linhaModal["hiperac_altura"],"Altura(cm)",null,null,"style='text-align:right'").
						   $form->inputText("hiperac_exame_glicemia",$linhaModal["hiperac_exame_glicemia"],"Exame de Glicemia(mg/dll)",null,null,"style='text-align:right'").
						   $form->inputSelect("hiperac_tipo_exame_glicemia",$arrayGlicemia,"Tipo Glicemia",null,null,null,$linhaModal["hiperac_tipo_exame_glicemia"]);
						   
		$arraySimNao = array("N"=>"N&atilde;o","S"=>"Sim");
		
		$conteudoModal2 = $form->inputSelect("hiperac_sem_complicacoes",$arraySimNao,"Sem complica&ccedil;&otilde;es",null,null,null,$linhaModal["hiperac_sem_complicacoes"],null,NULL,null,'N','S').
						  $form->inputSelect("hiperac_angina",$arraySimNao,"Angina",null,null,null,$linhaModal["hiperac_angina"],null,NULL,null,'N','S').
						  $form->inputSelect("hiperac_iam",$arraySimNao,"IAM",null,null,null,$linhaModal["hiperac_iam"],null,NULL,null,'N','S').
						  $form->inputSelect("hiperac_avc",$arraySimNao,"AVC",null,null,null,$linhaModal["hiperac_avc"],null,NULL,null,'N','S').
						  $form->inputSelect("hiperac_pe_diabetico",$arraySimNao,"P&eacute; Diabetico",null,null,null,$linhaModal["hiperac_pe_diabetico"],null,NULL,null,'N','S').
						  $form->inputSelect("hiperac_amputacao_diabetes",$arraySimNao,"Amputa&ccedil;&atilde;o por Diabetes",null,null,null,$linhaModal["hiperac_amputacao_diabetes"],null,NULL,null,'N','S').
						  $form->inputSelect("hiperac_doenca_renal",$arraySimNao,"Doen&ccedil;a Renal",null,null,null,$linhaModal["hiperac_doenca_renal"],null,NULL,null,'N','S').
						  $form->inputSelect("hiperac_retinopatia",$arraySimNao,"Retinopatia",null,null,null,$linhaModal["hiperac_retinopatia"],null,NULL,null,'N','S');
						  //$form->inputSelect("hiperac_pe_diabetico",$arraySimNao,"Pe Diabetico",null,null,$linhaModal["hiperac_pe_diabetico"]);
			
					if($hiperac_codigo != ""){
						$sqlModalMedicamentos = "select * from hiperdia_medicamentos_acompanhamento where hiperac_codigo = $hiperac_codigo";
					}else{
						$sqlModalMedicamentos = "select * 
												   from hiperdia as h
												   join hiperdia_acompanhamentos as ha
												     on h.hiper_codigo = ha.hiper_codigo
												   join hiperdia_medicamentos_acompanhamento as hma
												     on hma.hiperac_codigo = ha.hiperac_codigo
												  WHERE h.hiper_codigo = $hiper_codigo
						  						  ORDER BY hiperac_data_consulta 
						   						   DESC LIMIT 1";
					}
					$queryModalMedicamentos = pg_query($sqlModalMedicamentos);
					$linhaModalMedicamentos = pg_fetch_array($queryModalMedicamentos);
					
		$conteudoModal3 = "
			<b>Medicamentoso:
			<select name='hipermedac_medicamentoso' class='inputForm'>
				<option value='N' ".($linhaModalMedicamentos[hipermedac_medicamentoso] == "N" ? "selected=selected" : "").">Nao</option>
				<option value='S' ".($linhaModalMedicamentos[hipermedac_medicamentoso] == "S" ? "selected=selected" : "").">Sim</option>
			</select>
			<b>Outros:
			<select name='hipermedac_outros' class='inputForm'>
							<option value='N' ".($linhaModalMedicamentos[hipermedac_outros] == "N" ? "selected=selected" : "")."> <b>Nao</option>
							<option value='S' ".($linhaModalMedicamentos[hipermedac_outros] == "S" ? "selected=selected" : "")."> <b>Sim</option>
						</select>
			<table border='0' cellpadding=5 cellspacing=1>
				<tr> 
					<td align='center' colspan='2' bgcolor='#E1F5FF'>
						 <b>MEDICAMENTOS
					</td>
				</tr>
				<tr> 
					<td bgcolor='#E1F5FF' width='80%'>
						 <b>Tipo
					</td>
					<td bgcolor='#E1F5FF' width='20%' class=''>
						 <b>Comprimidos/dia
					</td>
				</tr>
				<tr> 
					<td width='80%' class='inputForm'>
						HIDROCLOROTIAZIDA 25MG
					</td>
					<td>
						<input type='hidden' name='proc_codigo[]' value='01'>";
						if($hiperac_codigo != ""){
							$sqlModalMedicamentosHidro = "select * from hiperdia_medicamentos_acompanhamento where hiperac_codigo = $hiperac_codigo and pro_codigo = '01'";
						}else{
							$sqlModalMedicamentosHidro = "SELECT hipermedac_dosagem,
															     pro_codigo 
														    FROM hiperdia AS h
														    JOIN hiperdia_acompanhamentos AS ha
														      ON ha.hiper_codigo = h.hiper_codigo
														    JOIN hiperdia_medicamentos_acompanhamento AS hma
														      ON hma.hiperac_codigo = ha.hiperac_codigo
														   WHERE pro_codigo = '01' AND h.hiper_codigo = $hiper_codigo
														   ORDER BY hiperac_data_consulta DESC LIMIT 1";
						}
						$queryModalMedicamentosHidro = pg_query($sqlModalMedicamentosHidro);
						$numLinhasModalMedicamentosHidro = pg_num_rows($queryModalMedicamentosHidro);
						if($numLinhasModalMedicamentosHidro == 0){
							$queryModalMedicamentosHidro = pg_query("SELECT *,hipermed_dosagem as hipermedac_dosagem FROM hiperdia_medicamentos where hiper_codigo = $hiper_codigo and pro_codigo = '01'");
						}
						$linhaModalMedicamentosHidro = pg_fetch_array($queryModalMedicamentosHidro);
					   $conteudoModal3 .="
					   <select name='medicamento[]'class='inputForm hipertenso'>
						   <option value=''>  </option>
						   <option value='0,5' ".($linhaModalMedicamentosHidro['hipermedac_dosagem'] == '0,5' ? "selected='selected'": '').">Meio</option>
						   <option value='1,0' ".($linhaModalMedicamentosHidro['hipermedac_dosagem'] == '1,0' ? "selected='selected'": '').">Um</option>
						   <option value='2,0' ".($linhaModalMedicamentosHidro['hipermedac_dosagem'] == '2,0' ? "selected='selected'": '').">Dois</option>
						   <option value='3,0' ".($linhaModalMedicamentosHidro['hipermedac_dosagem'] == '3,0' ? "selected='selected'": '').">Tres</option>
					   </select>		  
					</td>
				</tr>
				<tr> 
					<td width='80%' bgcolor='#FFFFFF' class='inputForm'>
						PROPANOLOL 40MG
					</td>
					<td width='20%'>
					<input type='hidden' name='proc_codigo[]' value='02'>";
					if($hiperac_codigo != ""){
						$sqlModalMedicamentosPropanolol = "select * from hiperdia_medicamentos_acompanhamento where hiperac_codigo = $hiperac_codigo and pro_codigo = '02'";
					}else{
						$sqlModalMedicamentosPropanolol = "SELECT hipermedac_dosagem,
														     pro_codigo 
													    FROM hiperdia AS h
													    JOIN hiperdia_acompanhamentos AS ha
													      ON ha.hiper_codigo = h.hiper_codigo
													    JOIN hiperdia_medicamentos_acompanhamento AS hma
													      ON hma.hiperac_codigo = ha.hiperac_codigo
													   WHERE pro_codigo = '02' AND h.hiper_codigo = $hiper_codigo
													   ORDER BY hiperac_data_consulta DESC LIMIT 1";
					}
					$queryModalMedicamentosPropanolol = pg_query($sqlModalMedicamentosPropanolol);
					$numLinhasModalMedicamentosPropanolol = pg_num_rows($queryModalMedicamentosPropanolol);
					if($numLinhasModalMedicamentosPropanolol == 0){
						$queryModalMedicamentosPropanolol = pg_query("SELECT *,hipermed_dosagem as hipermedac_dosagem FROM hiperdia_medicamentos where hiper_codigo = $hiper_codigo and pro_codigo = '02'");
					}
					$linhaModalMedicamentosPropanolol = pg_fetch_array($queryModalMedicamentosPropanolol);
					
					$conteudoModal3 .="
					   <select name='medicamento[]' class='inputForm hipertenso'>
						   <option value=''>  </option>
						   <option value='0,5'".($linhaModalMedicamentosPropanolol['hipermedac_dosagem'] == '0,5' ? "selected='selected'": '').">Meio</option>
						   <option value='1,0'".($linhaModalMedicamentosPropanolol['hipermedac_dosagem'] == '1,0' ? "selected='selected'": '').">Um</option>
						   <option value='2,0'".($linhaModalMedicamentosPropanolol['hipermedac_dosagem'] == '2,0' ? "selected='selected'": '').">Dois</option>
						   <option value='3,0'".($linhaModalMedicamentosPropanolol['hipermedac_dosagem'] == '3,0' ? "selected='selected'": '').">Tres</option>
						   <option value='4,0'".($linhaModalMedicamentosPropanolol['hipermedac_dosagem'] == '4,0' ? "selected='selected'": '').">Quatro</option>
						   <option value='5,0'".($linhaModalMedicamentosPropanolol['hipermedac_dosagem'] == '5,0' ? "selected='selected'": '').">Cinco</option>
						   <option value='6,0'".($linhaModalMedicamentosPropanolol['hipermedac_dosagem'] == '6,0' ? "selected='selected'": '').">Seis</option>
					   </select>			  
					</td>
				</tr>
				<tr> 
					<td width='80%' bgcolor='#FFFFFF' class='inputForm'>
						CAPTOPRIL 25 MG
					</td>
					<td width='20%'>
					<input type='hidden' name='proc_codigo[]' value='03'>";
					if($hiperac_codigo != ""){
						$sqlModalMedicamentosCaptopril = "select * from hiperdia_medicamentos_acompanhamento where hiperac_codigo = $hiperac_codigo and pro_codigo ='03'";
					}else{
						$sqlModalMedicamentosCaptopril = "SELECT hipermedac_dosagem,
														     pro_codigo 
													    FROM hiperdia AS h
													    JOIN hiperdia_acompanhamentos AS ha
													      ON ha.hiper_codigo = h.hiper_codigo
													    JOIN hiperdia_medicamentos_acompanhamento AS hma
													      ON hma.hiperac_codigo = ha.hiperac_codigo
													   WHERE pro_codigo = '03' AND h.hiper_codigo = $hiper_codigo
													   ORDER BY hiperac_data_consulta DESC LIMIT 1";
					}
					$queryModalMedicamentosCaptopril = pg_query($sqlModalMedicamentosCaptopril);
					$numLinhasModalMedicamentosCaptopril = pg_num_rows($queryModalMedicamentosCaptopril);
					if($numLinhasModalMedicamentosCaptopril == 0){
						$queryModalMedicamentosCaptopril = pg_query("SELECT *,hipermed_dosagem as hipermedac_dosagem FROM hiperdia_medicamentos where hiper_codigo = $hiper_codigo and pro_codigo = '03'");
					}
					$linhaModalMedicamentosCaptopril = pg_fetch_array($queryModalMedicamentosCaptopril);
					
					$conteudoModal3 .="
					   <select name='medicamento[]' class='inputForm hipertenso'>
						   <option value=''>  </option>
						   <option value='0,5'".($linhaModalMedicamentosCaptopril['hipermedac_dosagem'] == '0,5' ? "selected='selected'": '').">Meio</option>
						   <option value='1,0'".($linhaModalMedicamentosCaptopril['hipermedac_dosagem'] == '1,0' ? "selected='selected'": '').">Um</option>
						   <option value='2,0'".($linhaModalMedicamentosCaptopril['hipermedac_dosagem'] == '2,0' ? "selected='selected'": '').">Dois</option>
						   <option value='3,0'".($linhaModalMedicamentosCaptopril['hipermedac_dosagem'] == '3,0' ? "selected='selected'": '').">Tres</option>
						   <option value='4,0'".($linhaModalMedicamentosCaptopril['hipermedac_dosagem'] == '4,0' ? "selected='selected'": '').">Quatro</option>
						   <option value='5,0'".($linhaModalMedicamentosCaptopril['hipermedac_dosagem'] == '5,0' ? "selected='selected'": '').">Cinco</option>
						   <option value='6,0'".($linhaModalMedicamentosCaptopril['hipermedac_dosagem'] == '6,0' ? "selected='selected'": '').">Seis</option>
					   </select>			  
					</td>
				</tr>		
				<tr> 
					<td width='80%' bgcolor='#FFFFFF' class='inputForm'>
						GLIBENCLAMIDA 5 MG
					</td>
					<td width='20%' >
					<input type='hidden' name='proc_codigo[]' value='04'>";
					if($hiperac_codigo != ""){
						$sqlModalMedicamentosGliben = "select * from hiperdia_medicamentos_acompanhamento where hiperac_codigo = $hiperac_codigo and pro_codigo = '04'";
					}else{
						$sqlModalMedicamentosGliben = "SELECT hipermedac_dosagem,
														     pro_codigo 
													    FROM hiperdia AS h
													    JOIN hiperdia_acompanhamentos AS ha
													      ON ha.hiper_codigo = h.hiper_codigo
													    JOIN hiperdia_medicamentos_acompanhamento AS hma
													      ON hma.hiperac_codigo = ha.hiperac_codigo
													   WHERE pro_codigo = '04' AND h.hiper_codigo = $hiper_codigo
													   ORDER BY hiperac_data_consulta DESC LIMIT 1";
					}
					$queryModalMedicamentosGliben = pg_query($sqlModalMedicamentosGliben);
					$numLinhasModalMedicamentosGliben = pg_num_rows($queryModalMedicamentosGliben);
					if($numLinhasModalMedicamentosGliben == 0){
						$queryModalMedicamentosGliben = pg_query("SELECT *,hipermed_dosagem as hipermedac_dosagem FROM hiperdia_medicamentos where hiper_codigo = $hiper_codigo and pro_codigo = '04'");
					}
					$linhaModalMedicamentosGliben = pg_fetch_array($queryModalMedicamentosGliben);
					
					$conteudoModal3 .="
					   <select name='medicamento[]' class='inputForm diabetico'>
						   <option value=''>  </option>
						   <option value='0,5'".($linhaModalMedicamentosGliben['hipermedac_dosagem'] == '0,5' ? "selected='selected'": '').">Meio</option>
						   <option value='1,0'".($linhaModalMedicamentosGliben['hipermedac_dosagem'] == '1,0' ? "selected='selected'": '').">Um</option>
						   <option value='2,0'".($linhaModalMedicamentosGliben['hipermedac_dosagem'] == '2,0' ? "selected='selected'": '').">Dois</option>
						   <option value='3,0'".($linhaModalMedicamentosGliben['hipermedac_dosagem'] == '3,0' ? "selected='selected'": '').">Tres</option>
						   <option value='4,0'".($linhaModalMedicamentosGliben['hipermedac_dosagem'] == '4,0' ? "selected='selected'": '').">Quatro</option>
					   </select>			  
					</td>
				</tr>
				
				<tr> 
					<td width='80%' class='inputForm'>
						METFORMINA 850MG
					</td>
					<td width='20%' >
					<input type='hidden' name='proc_codigo[]' value='05'>";
					if($hiperac_codigo != ""){
						$sqlModalMedicamentosMetformina = "select * from hiperdia_medicamentos_acompanhamento where hiperac_codigo = $hiperac_codigo and pro_codigo = '05'";
					}else{
						$sqlModalMedicamentosMetformina = "SELECT hipermedac_dosagem,
														     pro_codigo 
													    FROM hiperdia AS h
													    JOIN hiperdia_acompanhamentos AS ha
													      ON ha.hiper_codigo = h.hiper_codigo
													    JOIN hiperdia_medicamentos_acompanhamento AS hma
													      ON hma.hiperac_codigo = ha.hiperac_codigo
													   WHERE pro_codigo = '05' AND h.hiper_codigo = $hiper_codigo
													   ORDER BY hiperac_data_consulta DESC LIMIT 1";
					}
					$queryModalMedicamentosMetformina = pg_query($sqlModalMedicamentosMetformina);
					$numLinhasModalMedicamentosMetformina= pg_num_rows($queryModalMedicamentosMetformina);
					if($numLinhasModalMedicamentosMetformina == 0){
						$queryModalMedicamentosMetformina = pg_query("SELECT *,hipermed_dosagem as hipermedac_dosagem FROM hiperdia_medicamentos where hiper_codigo = $hiper_codigo and pro_codigo = '05'");
					}
					$linhaModalMedicamentosMetformina = pg_fetch_array($queryModalMedicamentosMetformina);
					
					$conteudoModal3 .="
					   <select name='medicamento[]' class='inputForm diabetico'>
						   <option value=''>  </option>
						   <option value='0,5'".($linhaModalMedicamentosMetformina['hipermedac_dosagem'] == '0,5' ? "selected='selected'": '').">Meio</option>
						   <option value='1,0'".($linhaModalMedicamentosMetformina['hipermedac_dosagem'] == '1,0' ? "selected='selected'": '').">Um</option>
						   <option value='2,0'".($linhaModalMedicamentosMetformina['hipermedac_dosagem'] == '2,0' ? "selected='selected'": '').">Dois</option>
						   <option value='3,0'".($linhaModalMedicamentosMetformina['hipermedac_dosagem'] == '3,0' ? "selected='selected'": '').">Tres</option>
						   <option value='4,0'".($linhaModalMedicamentosMetformina['hipermedac_dosagem'] == '4,0' ? "selected='selected'": '').">Quatro</option>
						   <option value='5,0'".($linhaModalMedicamentosMetformina['hipermedac_dosagem'] == '5,0' ? "selected='selected'": '').">Cinco</option>
					   </select>			  
					</td>
				</tr>
				<tr> 
					<td >  <b>Insulina: Unidades/dia </td>
					<td><input name='hipermedac_insulina_dia'  type='text' value='$linhaModalMedicamentos[hipermedac_insulina_dia]' size='10' maxlength='6' class='inputForm'> </td>	  
				</tr>
			</table>";
			if($hiperac_codigo != ""){
				$sqlModalExames = "SELECT * FROM hiperdia_exames WHERE hiperac_codigo = $hiperac_codigo";
			}else{
				$sqlModalExames = "SELECT *
									 FROM hiperdia AS h
									 JOIN hiperdia_acompanhamentos AS ha
									   ON ha.hiper_codigo = h.hiper_codigo
									 JOIN hiperdia_exames AS he
									   ON he.hiperac_codigo = ha.hiperac_codigo
									WHERE h.hiper_codigo = $hiper_codigo
									ORDER BY hiperac_data_consulta DESC LIMIT 1";
			}
			$queryModalExames = pg_query($sqlModalExames);
			$linhaModalExames = pg_fetch_array($queryModalExames);
			
			$conteudo4 = "<tr> 
							<td	bgcolor='#E1F5FF'> EXAMES </td>
						</tr>
						<table border='0'>
						<tr>
							<td>
								<input name='examesCheck[]' type='checkbox' id='checkbox' value='1' ".($linhaModalExames['hiperac_hb_glicosada'] == 'S' ? "checked=checked" : '')."> 
							</td>
							<td>
								<b> HB Glicosilada
							</td>
							<td>
								<input name='examesCheck[]' type='checkbox' id='checkbox' value='2' ".($linhaModalExames['hiperac_creatinina_serica'] == 'S' ? "checked=checked" : '').">  
							</td>
							<td>
								<b> Creatinina Serica
							</td>
							<td>
								<input name='examesCheck[]' type='checkbox' id='checkbox' value='3' ".($linhaModalExames['hiperac_colesterol_total'] == 'S' ? "checked=checked" : '')."> 
							</td>
							<td> 
								<b> Colesterol Total
							</td>
							<td>
								<input name='examesCheck[]' type='checkbox' id='checkbox' value='4' ".($linhaModalExames['hiperac_ecg'] == 'S' ? "checked=checked" : '')."> 
							</td>
							<td> 
								 <b>ECG
							</td>
						</tr>
						<tr>
							<td>
								<input name='examesCheck[]' type='checkbox' id='checkbox' value='5' ".($linhaModalExames['hiperac_triglicerides'] == 'S' ? "checked=checked" : '')."> 
							</td>
							<td>  
								<b> Triglic&eacute;rides
							</td>
							<td> 
								<input name='examesCheck[]' type='checkbox' id='checkbox' value='6' ".($linhaModalExames['hiperac_urina'] == 'S' ? "checked=checked" : '')."> 
							</td>
							<td> 
								<b> Parcial de Ur&iacute;na
							</td>
							<td>
								<input name='examesCheck[]' type='checkbox' id='checkbox' value='7' ".($linhaModalExames['hiperac_micro_albuminuria'] == 'S' ? "checked=checked" : '')."> 
							</td>
							<td> 
								<b> Micro Albumin&uacute;ria
							</td>
						</tr>
				</table>
						<tr>
							<td>
								<b>Tipo de Risco:
								<select name='risco'class='inputForm' >
								   <option value='B'".($linhaModal['hiperac_riscos'] == 'B' ? "selected='selected'": '').">Baixo</option>
								   <option value='M'".($linhaModal['hiperac_riscos'] == 'M' ? "selected='selected'": '').">Medio</option>
								   <option value='A'".($linhaModal['hiperac_riscos'] == 'A' ? "selected='selected'": '').">Alto</option>
								   <option value='MA'".($linhaModal['hiperac_riscos'] == 'MA' ? "selected='selected'": '').">Muito Alto</option>
								   <option value='N'".($linhaModal['hiperac_riscos'] == 'N' ? "selected='selected'": '').">Nenhum</option>
							   </select>
							</td>
						</tr>
						<tr>
							<td>
								<br/>
								<br/>
								Data do Atendimento:  <input type='text' name='data_acompanhamento' class='inputForm data' value='".($linhaModal[hiperac_data_consulta2] == "" ? "$data" : "$linhaModal[hiperac_data_consulta2]")."'>
							</td>
						</tr>";
		//echo $common->openModal("Acompanhamento","1200","Salvar",null,"onclick=document.formModal.submit()",null,null,false);
		echo "<div id='dialog-acompanhamento'>";
			echo $table->openTable(null,null,null,0);
				echo $form->openForm("hiperdia.php","POST","formModal");
				$sql1 = "select * from hiperdia_acompanhamentos as hiperac
								 join hiperdia as hip
								   on hip.hiper_codigo = hiperac.hiper_codigo
								 where hiperac_codigo = $hiperac_codigo";				 
				$query1 = pg_query($sql1);
				$umReg1 = pg_fetch_array($query1);
					if($hiperac_codigo == "" || $hiperac_codigo == null){
						echo $form->hiddenForm("acao","form_add_acompanhamento");
						echo $form->hiddenForm("hiper_codigo","$hiper_codigo");
						echo $form->hiddenForm("usu_codigo","$usu_codigo");
						
					}else{
						echo $form->hiddenForm("acao","form_upd_acompanhamento");
						echo $form->hiddenForm("usu_codigo","$umReg1[usu_codigo]");
						echo $form->hiddenForm("hiper_codigo","$umReg1[hiper_codigo]");
						echo $form->hiddenForm("hiperac_codigo","$hiperac_codigo");
					}
					echo $table->criaLinha(array($conteudoModal,$conteudoModal2,$conteudoModal3));
					echo $table->criaLinha(array($conteudo4));
					echo $table->criaLinha(array());
				echo $form->closeForm();
			echo $table->closeTable();
		echo "</div>";
		
		//echo $common->closeModal();
	}
	
	if($acao == "add"){
		if($hiper_pa_sistolica >= 140 || $hiper_pa_diastolica){
			$hipertensao_arterial = "S";
		}else{
			$hipertensao_arterial = "N";
		}

		//echo $unidade;exit();
		$stmt = "INSERT INTO hiperdia ( 
								usu_codigo, 
								hiper_pa_sistolica, 
								hiper_pa_diastolica, 
								hiper_cintura, 
								hiper_altura, 
								hiper_peso, 
								hiper_glicemia_capilar, 
								hiper_glicemia_realizada, 
								hiper_data, 
								hiper_antecedentes_familiares, 
								hiper_diabetes_1, 
								hiper_diabetes_2, 
								hiper_tabagismo, 
								hiper_sedentarismo, 
								hiper_sobrepeso, 
								hiper_infarto, 
								hiper_outras_coronariopatias, 
								hiper_avc, 
								hiper_pe_diabetico, 
								hiper_amputacao, 
								hiper_doenca_renal,
								hiper_status,
								usr_codigo,
								hiper_hipertensao,
								uni_codigo)
					 VALUES ( 
								'$usu_codigo', 
								".($hiper_pa_sistolica != null ? "$hiper_pa_sistolica" : "null").", 
								".($hiper_pa_diastolica != null ? "$hiper_pa_diastolica" : "null").",
								".($hiper_cintura != null ? "$hiper_cintura" : "null").",
								".($hiper_altura != null ? "$hiper_altura" : "null").",
								".($hiper_peso != null ? "$hiper_peso" : "null").",
								".($hiper_glicemia_capilar != null ? "$hiper_glicemia_capilar" : "null").",
								".($hiper_glicemia_realizada != null ? "'$hiper_glicemia_realizada'" : "null").",
								'$data', 
								".($hiper_antecedentes_familiares != null ? "'$hiper_antecedentes_familiares'" : "null").",
								".($hiper_diabetes_1 != null ? "'$hiper_diabetes_1'" : "null").",
								".($hiper_diabetes_2 != null ? "'$hiper_diabetes_2'" : "null").",
								".($hiper_tabagismo != null ? "'$hiper_tabagismo'" : "null").", 
								".($hiper_sedentarismo != null ? "'$hiper_sedentarismo'" : "null").", 
								".($hiper_sobrepeso != null ? "'$hiper_sobrepeso'" : "null").", 
								".($hiper_infarto != null ? "'$hiper_infarto'" : "null").",  
								".($hiper_outras_coronariopatias != null ? "'$hiper_outras_coronariopatias'" : "null").", 
								".($hiper_avc != null ? "'$hiper_avc'" : "null").",   
								".($hiper_pe_diabetico != null ? "'$hiper_pe_diabetico'" : "null").", 
								".($hiper_amputacao != null ? "'$hiper_amputacao'" : "null").",
								".($hiper_doenca_renal != null ? "'$hiper_doenca_renal'" : "null").", 
								'H',
								".($usr_codigo != null ? "'$usr_codigo'" : "null").", 
								".($hipertensao_arterial != null ? "'$hipertensao_arterial'" : "null").",
								$unidade)";
		pg_query($stmt);
       
		
		$pegaCodigo = "select * from hiperdia where usu_codigo = $usu_codigo";
		$qryPega = pg_query($pegaCodigo);
		$posi = pg_fetch_array($qryPega);
		$hiper_codigo = $posi["hiper_codigo"];
		
           for($i=0;$i<count($_POST["medicamento"]);$i++){
			   $valor = $_POST["proc_codigo"][$i];
			   $dosagem = $_POST["medicamento"][$i];
				if($_POST["medicamento"][$i] != ""){
					$stmt2 = "  INSERT INTO hiperdia_medicamentos (  
										hiper_codigo, 
										hipermed_medicamentoso, 
										pro_codigo, 
										hipermed_insulina_dia, 
										hipermed_nome_outros,
										hipermed_dosagem,
										hipermed_outros
							) VALUES ( 
										'$hiper_codigo', 
										'$hipermed_medicamentoso', 
										'$valor', 
										".($hipermed_insulina_dia == '' ? "null" : $hipermed_insulina_dia)." , 
										UPPER('$hipermed_nome_outros'),
										'$dosagem',
										'$hipermed_outros')";
					//echo $stmt2;
					//exit;
					if($exec = pg_query($stmt2)){
						echo $common->modalMsg("OK","Consulta de Hiperdia Salva com Sucesso!","hiperdia.php?acao=form_add&hiper_codigo=$hiper_codigo&usu_codigo=$usu_codigo");
					}else{
						echo $common->modalMsg("ERRO","Erro ao salvar!","hiperdia.php?acao=form_add",$stmt2);
				    }
		
				}
		   }
	
		   $verificaMedicamentosInc = "select * from hiperdia_medicamentos where hiper_codigo = $hiper_codigo";
		   $queryMedicamentosInc = pg_query($verificaMedicamentosInc);
		   $verificaNumeroLinhasInc = pg_num_rows($queryMedicamentosInc);
		   
		   
		   if($verificaNumeroLinhasInc == 0){
				$stmts = "  INSERT INTO hiperdia_medicamentos (  
                                                        hiper_codigo, 
                                                        hipermed_medicamentoso, 
                                                        hipermed_insulina_dia, 
                                                        hipermed_outros,
                                                        hipermed_nome_outros
                                              ) VALUES ( 
                                                        '$hiper_codigo', 
                                                        '$hipermed_medicamentoso', 
                                                        ".($hipermed_insulina_dia == '' ? "null" : $hipermed_insulina_dia)." , 
                                                        UPPER('$hipermed_outros'),
                                                        '$hipermed_nome_outros')";
				//echo $stmts;
		   		if($execs = pg_query($stmts)){
					echo $common->modalMsg("OK","Consulta de Hiperdia Salvas com Sucesso!","hiperdia.php?acao=form_add&hiper_codigo=$hiper_codigo&usu_codigo=$usu_codigo");
				}else{
					echo $common->modalMsg("ERRO","Erro ao salvar!","hiperdia.php?acao=form_add&hiper_codigo=$hiper_codigo&usu_codigo=$usu_codigo");
			    }
			}
		   
		   
		  
	}
	
	if($acao == "alteraHiperdia"){
		if($hiper_pa_sistolica >= 140 || $hiper_pa_diastolica){
			$hipertensao_arterial = "S";
		}else{
			$hipertensao_arterial = "N";
		}
		 $stmtAlteraHip = "UPDATE hiperdia SET 
									usu_codigo = '$usu_codigo', 
									hiper_pa_sistolica = '$hiper_pa_sistolica', 
									hiper_pa_diastolica = '$hiper_pa_diastolica', 
									hiper_cintura = '$hiper_cintura', 
									hiper_altura = $hiper_altura, 
									hiper_peso = $hiper_peso, 
									hiper_glicemia_capilar = '$hiper_glicemia_capilar', 
									hiper_glicemia_realizada = '$hiper_glicemia_realizada', 
									hiper_status = 'A', 
									hiper_diabetes_1 = '$hiper_diabetes_1', 
									hiper_diabetes_2 = '$hiper_diabetes_2', 
									hiper_tabagismo = '$hiper_tabagismo', 
									hiper_sedentarismo = '$hiper_sedentarismo', 
									hiper_sobrepeso = '$hiper_sobrepeso', 
									hiper_infarto = '$hiper_infarto', 
									hiper_outras_coronariopatias = '$hiper_outras_coronariopatias', 
									hiper_avc = '$hiper_avc', 
									hiper_pe_diabetico = '$hiper_pe_diabetico', 
									hiper_amputacao = '$hiper_amputacao', 
									hiper_doenca_renal = '$hiper_doenca_renal', 
									hiper_hipertensao = '$hipertensao_arterial', 
									usr_codigo = '$usr_codigo',
									uni_codigo = $unidade
									WHERE hiper_codigo = $hiper_codigo";
		$queryAlteraHip = pg_query($stmtAlteraHip);
		$stmtDeletaTratamento = "DELETE from hiperdia_medicamentos where hiper_codigo = $hiper_codigo";
		$queryDeletaTratamento = pg_query($stmtDeletaTratamento);
		for($j=0;$j<count($_POST["medicamento"]);$j++){

			if($_POST["medicamento"][$j] != "") { // SE TIVER ALGUM MEDICAMENTO SELECIONADO ELE CAI NO IF E FAZ O LOOP DOS QUE FORAM SELECIONADOS INSERINDO ELES
				   //$teste = $_POST["medicamento"][$i]."|".$_POST["proc_codigo"][$i]."<br/>";
				   $valor = $_POST["proc_codigo"][$j];
				   $dosagem = $_POST["medicamento"][$j];
					$stmt2 = "  INSERT INTO hiperdia_medicamentos (  
										hiper_codigo, 
										hipermed_medicamentoso, 
										pro_codigo, 
										hipermed_insulina_dia, 
										hipermed_nome_outros,
										hipermed_dosagem,
										hipermed_outros
							) VALUES ( 
										'$hiper_codigo', 
										'$hipermed_medicamentoso', 
										'$valor', 
										".($hipermed_insulina_dia == '' ? "null" : $hipermed_insulina_dia)." , 
										UPPER('$hipermed_nome_outros'),
										'$dosagem',
										'$hipermed_outros')";
					$exec = pg_query($stmt2);
			}
		}
		  $verificaMedicamentos = "select * from hiperdia_medicamentos where hiper_codigo = $hiper_codigo";
		  $queryMedicamentos = pg_query($verificaMedicamentos);
		  $verificaNumeroLinhas = pg_num_rows($queryMedicamentos);
		  
			if($verificaNumeroLinhas == 0){
				$stmts = "  INSERT INTO hiperdia_medicamentos (  
																hiper_codigo, 
																hipermed_medicamentoso, 
																hipermed_insulina_dia, 
																hipermed_nome_outros,
																hipermed_outros
													) VALUES ( 
																'$hiper_codigo', 
																'$hipermed_medicamentoso', 
																".($hipermed_insulina_dia == '' ? "null" : $hipermed_insulina_dia)." , 
																UPPER('$hipermed_nome_outros'),
																'$hipermed_outros')";
				$execs = pg_query($stmts);
			}
		  echo $common->modalMsg("OK","Consulta de Hiperdia Alterada com Sucesso!","hiperdia.php?acao=form_add&hiper_codigo=$hiper_codigo");
	}
		
          
	
	
///////////////////////////////////FIM DO HIPERDIA INICIO DO ACOMPANHAMENTO///////////////////////////////////////
	if($acao == "form_add_acompanhamento"){
		
		$arrayTudo = array("1","2");
		$selecionadoDoenca = array_intersect($_POST["doencas"],$arrayTudo); 
		
		$data= date("d/m/Y");
		$stmt3 = "INSERT INTO hiperdia_acompanhamentos ( 
															hiper_codigo, 
															hiperac_pasistolica, 
															hiperac_padiastolica, 
															hiperac_cintura, 
															hiperac_peso, 
															hiperac_altura, 
															hiperac_exame_glicemia, 
															hiperac_tipo_exame_glicemia, 
															hiperac_sem_complicacoes, 
															hiperac_angina, 
															hiperac_iam, 
															hiperac_avc, 
															hiperac_amputacao_diabetes, 
															hiperac_doenca_renal, 
															hiperac_retinopatia,
															hiperac_pe_diabetico,
															hiperac_data_consulta,
															hiperac_status_exportacao,
															hiperac_riscos,
															usr_codigo,
															hiperac_hipertenso,
															hiperac_diabetico,
															uni_codigo
												 ) VALUES ( 
															'$hiper_codigo',
															".($hiperac_pasistolica != null ? "$hiperac_pasistolica" : "null").",
															".($hiperac_padiastolica != null ? "$hiperac_padiastolica" : "null").",
															".($hiperac_cintura != null ? "$hiperac_cintura" : "null").",
															".($hiperac_peso != null ? "$hiperac_peso" : "null").",
															".($hiperac_altura != null ? "$hiperac_altura" : "null").", 
															".($hiperac_exame_glicemia != null ? "$hiperac_exame_glicemia" : "null").",
															".($hiperac_tipo_exame_glicemia != null ? "'$hiperac_tipo_exame_glicemia'" : "null").",
															".($hiperac_sem_complicacoes != null ? "'$hiperac_sem_complicacoes'" : "null").", 
															".($hiperac_angina != null ? "'$hiperac_angina'" : "null").",    
															".($hiperac_iam != null ? "'$hiperac_iam'" : "null").",     
															".($hiperac_avc != null ? "'$hiperac_avc'" : "null").",
															".($hiperac_amputacao_diabetes != null ? "'$hiperac_amputacao_diabetes'" : "null").",
															".($hiperac_doenca_renal != null ? "'$hiperac_doenca_renal'" : "null").",
															".($hiperac_retinopatia != null ? "'$hiperac_retinopatia'" : "null").",
															".($hiperac_pe_diabetico != null ? "'$hiperac_retinopatia'" : "null").",
															".($data_acompanhamento == "" ? "'$data'" : "'$data_acompanhamento'").",
															'H',
															".($risco != null ? "'$risco'" : "null").", 
															$id_login,
															".(in_array(1,$selecionadoDoenca) ? "'S'" : "'N'" ).",
															".(in_array(2,$selecionadoDoenca) ? "'S'" : "'N'" ).",
															$unidade)";
															
		$query3 = pg_query($stmt3);
		
		$pegaCod = "SELECT max(hiperac_codigo) as acompanhamento 
					  FROM hiperdia_acompanhamentos";
		$queryPega = pg_query($pegaCod);
		$registro = pg_fetch_array($queryPega);
		$codigo_acompanhamento = $registro["acompanhamento"];
		
		//if(isset($_POST["medicamento"])) {
		
		for($i=0;$i<count($_POST["medicamento"]);$i++){
			$valor = $_POST["proc_codigo"][$i];
			$dosagem = $_POST["medicamento"][$i];
			
			if($_POST["medicamento"][$i] != ""){
				
				$stmt2 = "  INSERT INTO hiperdia_medicamentos_acompanhamento (  
																					hiperac_codigo, 
																					hipermedac_medicamentoso, 
																					pro_codigo, 
																					hipermedac_insulina_dia, 
																					hipermedac_outros,
																					hipermedac_dosagem
																		) VALUES ( 
																					'$codigo_acompanhamento', 
																					'$hipermedac_medicamentoso', 
																					'$valor', 
																					".($hipermedac_insulina_dia == '' ? "null" : $hipermedac_insulina_dia)." , 
																					UPPER('$hipermedac_outros'),
																					'$dosagem')";
				$query2 = pg_query($stmt2);
			}
   		}
		
   		$x = "select * from hiperdia_medicamentos_acompanhamento where hiperac_codigo = $codigo_acompanhamento";
   		$queryx = pg_query($x);
   		$numLinhasx = pg_num_rows($queryx);
   		//echo $numLinhasx;
   		if($numeroLinhasMedicamentosModal == 0){
   			$stmt2 = "  INSERT INTO hiperdia_medicamentos_acompanhamento (  
																					hiperac_codigo, 
																					hipermedac_medicamentoso, 
																					hipermedac_insulina_dia, 
																					hipermedac_outros
																		) VALUES ( 
																					'$codigo_acompanhamento', 
																					'$hipermedac_medicamentoso', 
																					".($hipermedac_insulina_dia == '' ? "null" : $hipermedac_insulina_dia)." , 
																					UPPER('$hipermedac_outros'))";
   			$query2 = pg_query($stmt2);
   				
   			
   		}
   		
			$arrayTodos = array("1","2","3","4","5","6","7");
			$selecionados = array_intersect($_POST["examesCheck"],$arrayTodos);
			$naoSelecionados = array_diff($arrayTodos,$_POST["examesCheck"]);
			
			$insertExame = "INSERT INTO hiperdia_exames ( 
								hiperac_codigo, 
								hiperac_hb_glicosada, 
								hiperac_creatinina_serica, 
								hiperac_colesterol_total, 
								hiperac_ecg, 
								hiperac_triglicerides, 
								hiperac_urina, 
								hiperac_micro_albuminuria
					) VALUES ( 
								'$codigo_acompanhamento',
								".(in_array(1,$selecionados) ? "'S'" : "'N'" ).",
								".(in_array(2,$selecionados) ? "'S'" : "'N'" ).",
								".(in_array(3,$selecionados) ? "'S'" : "'N'" )." ,
								".(in_array(4,$selecionados) ? "'S'" : "'N'" ).",
								".(in_array(5,$selecionados) ? "'S'" : "'N'" ).",
								".(in_array(6,$selecionados) ? "'S'" : "'N'" ).",
								".(in_array(7,$selecionados) ? "'S'" : "'N'" ).")";
			$queryExames =pg_query($insertExame);
			echo $common->modalMsg("OK","Acompanhamento salvo com sucesso!","hiperdia.php?acao=form_add&hiper_codigo=$hiper_codigo");
			
	}
	if($acao == "form_upd_acompanhamento"){
		
//		echo "<pre>".print_r($_POST,true)."</pre>";
//		exit;
		$arrayTudo = array("1","2");
		$selecionadoDoenca = array_intersect($_POST["doencas"],$arrayTudo); 
		 $stmt10 = "UPDATE hiperdia_acompanhamentos
		 			   SET hiper_codigo = '$hiper_codigo', 
						   hiperac_pasistolica = '$hiperac_pasistolica', 
						   hiperac_padiastolica = '$hiperac_padiastolica', 
						   hiperac_cintura = '$hiperac_cintura', 
						   hiperac_peso = '$hiperac_peso', 
						   hiperac_altura = '$hiperac_altura', 
						   hiperac_exame_glicemia = '$hiperac_exame_glicemia', 
						   hiperac_tipo_exame_glicemia = '$hiperac_tipo_exame_glicemia', 
						   hiperac_sem_complicacoes = '$hiperac_sem_complicacoes', 
						   hiperac_angina = '$hiperac_angina', 
						   hiperac_iam = '$hiperac_iam', 
						   hiperac_avc = '$hiperac_avc', 
						   hiperac_amputacao_diabetes = '$hiperac_amputacao_diabetes', 
						   hiperac_doenca_renal = '$hiperac_doenca_renal', 
						   hiperac_retinopatia = '$hiperac_retinopatia', 
						   hiperac_pe_diabetico = '$hiperac_pe_diabetico', 
						   hiperac_data_consulta = ".($data_acompanhamento == "" ? "'$data'" : "'$data_acompanhamento'").", 
						   hiperac_status_exportacao = 'H', 
						   usr_codigo = '$id_login',
						   hiperac_riscos = '$risco',
						   hiperac_hipertenso = ".(in_array(1,$selecionadoDoenca) ? "'S'" : "'N'" ).",
						   hiperac_diabetico  = ".(in_array(2,$selecionadoDoenca) ? "'S'" : "'N'" ).",
						   uni_codigo = $unidade
					 WHERE hiperac_codigo = '$hiperac_codigo' ";
		//echo $stmt10;exit();
		 $query10 = pg_query($stmt10);
		
		//echo "<pre>".print_r($_POST["examesCheck"])."</pre>";
		$arrayTodos = array("1","2","3","4","5","6","7");
		$selecionados = array_intersect($_POST["examesCheck"],$arrayTodos);
		$naoSelecionados = array_diff($arrayTodos,$_POST["examesCheck"]);
		
		$stmt11 = "UPDATE hiperdia_exames SET
								hiperac_codigo = $hiperac_codigo,
								hiperac_hb_glicosada = ".(in_array(1,$selecionados) ? "'S'" : "'N'" ).",
								hiperac_creatinina_serica = ".(in_array(2,$selecionados) ? "'S'" : "'N'" ).",
								hiperac_colesterol_total =".(in_array(3,$selecionados) ? "'S'" : "'N'" )." ,
								hiperac_ecg = ".(in_array(4,$selecionados) ? "'S'" : "'N'" ).",
								hiperac_triglicerides = ".(in_array(5,$selecionados) ?  "'S'" : "'N'" ).",
								hiperac_urina = ".(in_array(6,$selecionados) ? "'S'" : "'N'" ).",
								hiperac_micro_albuminuria = ".(in_array(7,$selecionados) ? "'S'" : "'N'" )."
						  WHERE hiperac_codigo = '$hiperac_codigo'";
		$query11 = pg_query($stmt11);
	
	
	//if(isset($_POST["medicamento"])) {
		   $sqlDeletaTodosMedicamentos = "DELETE from hiperdia_medicamentos_acompanhamento WHERE hiperac_codigo = $hiperac_codigo";
		   $queryDeletaTodosMedicamentos = pg_query($sqlDeletaTodosMedicamentos);
           for($i=0;$i<count($_POST["medicamento"]);$i++){
			   $teste = $_POST["medicamento"][$i]."|".$_POST["proc_codigo"][$i]."<br/>";

			   $valor = $_POST["proc_codigo"][$i];
			   $dosagem = $_POST["medicamento"][$i];
				if($_POST["medicamento"][$i] != ""){
					$stmt13 = "  INSERT INTO hiperdia_medicamentos_acompanhamento (  
										hiperac_codigo, 
										hipermedac_medicamentoso, 
										pro_codigo, 
										hipermedac_insulina_dia, 
										hipermedac_outros,
										hipermedac_dosagem
							) VALUES ( 
										'$hiperac_codigo', 
										'$hipermedac_medicamentoso', 
										'$valor', 
										".($hipermedac_insulina_dia == '' ? "null" : $hipermedac_insulina_dia)." , 
										UPPER('$hipermedac_outros'),
										'$dosagem')";
					$query13 = pg_query($stmt13);
				
				$updtStatus = "UPDATE hiperdia_acompanhamentos SET hiperac_status_exportacao = 'A' WHERE hiperac_codigo = $hiperac_codigo";
				$queryUpdtStatus = pg_query($updtStatus);
				echo $stmt13."<br/>";
				echo $common->modalMsg("OK","Acompanhamento alterado com Sucesso!","hiperdia.php?hiper_codigo=$hiper_codigo&acao=form_add");
				
				}
		  	 }
	     	$x = "select * from hiperdia_medicamentos_acompanhamento where hiperac_codigo = $hiperac_codigo";
	   		$queryx = pg_query($x);
	   		$numLinhasx = pg_num_rows($queryx);
	   		if($numLinhasx == 0){
	   			$stmt2 = "  INSERT INTO hiperdia_medicamentos_acompanhamento (  
																					hiperac_codigo, 
																					hipermedac_medicamentoso, 
																					hipermedac_insulina_dia, 
																					hipermedac_outros
																		) VALUES ( 
																					'$hiperac_codigo', 
																					'$hipermedac_medicamentoso', 
																					".($hipermedac_insulina_dia == '' ? "null" : $hipermedac_insulina_dia)." , 
																					UPPER('$hipermedac_outros'))";
   				$query2 = pg_query($stmt2);
   				echo $common->modalMsg("OK","Acompanhamento alterado com Sucesso!","hiperdia.php?hiper_codigo=$hiper_codigo&acao=form_add");
	   		}
	   		
	}
?>