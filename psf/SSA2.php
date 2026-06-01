<!-- Inclusão do Jquery -->
<script type="text/javascript" src="../js/jquery-1.5.2.min.js" ></script>
<!-- Inclusão do Jquery Validate -->
<script type="text/javascript" src="../js/jquery-validation-1.8.0/jquery.validate.min.js" ></script>

<link href='../css/estiloForm.css' rel='stylesheet' type='text/css' />
<link href='../css/estiloCommon.css' rel='stylesheet' type='text/css' />
<script type="text/javascript">
    $(document).ready(function(){
        $('#crianca').validate({
            rules:{
        	cri_nas_vivos:{        			
                    required: true                    

                    }
               
            },
            messages:{
            	cri_nas_vivos:{
                    required: "O campo cri_nas_vivos é obrigatorio"
                    
                }
                
            }
 
        });
    });
</script>
<?php
$input = Array(
"NASCIDOS VIVOS NO MÊS",
"<input type='text' name='nascidosVivos' class='inputForm'>"
);
session_start();
require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

$form = new classForm();
$table = new tableClass();
$common = new commonClass();

echo $common->incJquery();
echo $common->menuTab(Array("Crianças","Gestantes","Hospital","Controlados","Óbitos"));

	echo $common->bodyTab('1');
		$select = "select * from ssa2_crianca where ssa2_codigo = $_GET[ssa2_codigo] ";
		$query = pg_query($select);
		$res_query = pg_fetch_array($query);
		
		$cri_nas_vivos2 =  $res_query['cri_nas_vivos'];
		$cri_peso_acima_dois_quinhentos2 = $res_query['cri_peso_acima_dois_quinhentos'];
		$cri_peso_nascimento2 = $res_query['cri_peso_nascimento'];
		$cri_tres_meses_vinte_e_nove_dias2 = $res_query['cri_tres_meses_vinte_e_nove_dias'];
		$cri_mamando_peito2 = $res_query['cri_mamando_peito'];
		$cri_aleitamento2 = 	$res_query['cri_aleitamento'];
		$cri_onze_meses_e_vinte_e_nove_dias2 = $res_query['cri_onze_meses_e_vinte_e_nove_dias'];
		$cri_vacina_em_dia2 = $res_query['cri_vacina_em_dia'];
		$cri_pesadas2 = $res_query['cri_pesadas'];
		$cri_desnutridas2 = $res_query['cri_desnutridas'];
		$cri_doze_meses_e_vinte_e_nove_dias2 = $res_query['cri_doze_meses_e_vinte_e_nove_dias'];
		$cri_doze_vacina_em_dia2 = $res_query['cri_doze_vacina_em_dia'];
		$cri_doze_pesadas2 = $res_query['cri_doze_pesadas'];
		$cri_doze_desnutridas2= $res_query['cri_doze_desnutridas'];
		$cri_menores_dois_anos2 = $res_query['cri_menores_dois_anos'];
		$cri_diarreia2 = $res_query['cri_diarreia'];
		$cri_tro2 = $res_query['cri_tro'];
		$cri_ira2 = $res_query['cri_ira'];
		$cri_pneumunia2 = $res_query['cri_pneumunia'];
		$cri_menores_cinco_anos2 = $res_query['cri_menores_cinco_anos'];
		//echo"<pre>".print_r($_REQUEST,true)."</pre>";
		echo $form->openForm("","POST","crianca",NULL,"crianca");
			echo $form->hiddenForm("acao", "addCrianca");
			echo $form->hiddenForm("id_login", "$_GET[id_login]");
			echo $form->hiddenForm("ssa2_codigo", "$_GET[ssa2_codigo]");
			echo $form->hiddenForm("numlinhas", pg_num_rows($query));
			
			echo $form->inputText("cri_nas_vivos", $cri_nas_vivos2,"Nascidos vivos no mês","40");
			echo $form->inputText("cri_peso_nascimento", $cri_peso_nascimento2,"RN pesado ao nascer","40");
			echo $form->inputText("cri_peso_acima_dois_quinhentos", $cri_peso_acima_dois_quinhentos2,"RN com peso < 2.500G","40");
			echo $form->inputText("cri_tres_meses_vinte_e_nove_dias", $cri_tres_meses_vinte_e_nove_dias2,"<b>De 0 a 3 meses e 29 dias</b>","40");
			echo $form->inputText("cri_mamando_peito", $cri_mamando_peito2,"Só mamando no peito","40");
			echo $form->inputText("cri_aleitamento", $cri_aleitamento2,"Aleitamento misto","40");
			echo $form->inputText("cri_onze_meses_e_vinte_e_nove_dias", $cri_onze_meses_e_vinte_e_nove_dias2,"<b>De 11 meses e 29 dias</b>","40");
			echo $form->inputText("cri_vacina_em_dia", $cri_vacina_em_dia2,"Com vacinas em dia","40");
			echo $form->inputText("cri_pesadas", $cri_pesadas2,"Pesadas","40");
			echo $form->inputText("cri_desnutridas", $cri_desnutridas2,"Desnutridas","40");
			echo $form->inputText("cri_doze_meses_e_vinte_e_nove_dias", $cri_doze_meses_e_vinte_e_nove_dias2,"<b>De 12 a 23 meses e 29 dias</b>","40");
			echo $form->inputText("cri_doze_vacina_em_dia", $cri_doze_vacina_em_dia2,"Com vacinas em dia","40");
			echo $form->inputText("cri_doze_pesadas", $cri_doze_pesadas2,"Pesadas","40");
			echo $form->inputText("cri_doze_desnutridas", $cri_doze_desnutridas2,"Desnutridas","40");			
			echo $form->inputText("cri_menores_dois_anos", $cri_menores_dois_anos2,"<b>Menores que 2 anos</b>","40");
			echo $form->inputText("cri_diarreia", $cri_diarreia2,"Diarréia","40");
			echo $form->inputText("cri_tro", $cri_tro2,"Que usaram TRO","40");
			echo $form->inputText("cri_ira", $cri_ira2,"Que tiveram IRA","40");
			echo $form->inputText("cri_pneumunia", $cri_pneumunia2,"Que tiveram pneumunia","40");
			echo $form->inputText("cri_menores_cinco_anos", $cri_menores_cinco_anos2,"Menores de 5 Anos","40");
			echo "<br><br><br>";
			//echo $form->submitButton("Salvar",$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg");
			if($res_query[cri_codigo ]== ""){
					echo $form->submitButton("Adicionar", $_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg");	
				}else{
					echo $form->submitButton("Adicionar", $_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg");
				}
			echo $common->commonButton("Voltar","adicionarFichaPsf.php",$_SESSION[linkroot].$_SESSION[comum]."imgsBotoes/voltar.png");
		echo $form->closeForm();
		if($acao == "addCrianca"){
		//echo"<pre>".print_r($_POST,true)."</pre>";
	
			if($_POST[numlinhas] == 0){
			
				$stmt = "INSERT INTO ssa2_crianca (ssa2_codigo, 
														cri_nas_vivos, 
														cri_peso_acima_dois_quinhentos, 
														cri_tres_meses_vinte_e_nove_dias, 
														cri_mamando_peito, 
														cri_aleitamento, 
														cri_onze_meses_e_vinte_e_nove_dias, 
														cri_vacina_em_dia, 
														cri_pesadas, 
														cri_desnutridas, 
														cri_doze_meses_e_vinte_e_nove_dias, 
														cri_doze_vacina_em_dia, 
														cri_doze_pesadas, 
														cri_doze_desnutridas, 
														cri_menores_dois_anos, 
														cri_diarreia, 
														cri_tro, 
														cri_ira, 
														cri_pneumunia, 
														cri_menores_cinco_anos, 
														cri_peso_nascimento
														 ) VALUES ( 
														$ssa2_codigo,
														".intval($cri_nas_vivos).", 
														".intval($cri_peso_acima_dois_quinhentos).", 
														".intval($cri_tres_meses_vinte_e_nove_dias).", 
														".intval($cri_mamando_peito).", 
														".intval($cri_aleitamento).", 
														".intval($cri_onze_meses_e_vinte_e_nove_dias).",
														".intval($cri_vacina_em_dia).",
														".intval($cri_pesadas).",
														".intval($cri_desnutridas).",
														".intval($cri_doze_meses_e_vinte_e_nove_dias).",
														".intval($cri_doze_vacina_em_dia).",
														".intval($cri_doze_pesadas).",
														".intval($cri_doze_desnutridas).",
														".intval($cri_menores_dois_anos).",
														".intval($cri_diarreia).",
														".intval($cri_tro).",
														".intval($cri_ira).",
														".intval($cri_pneumunia).",
														".intval($cri_menores_cinco_anos).",
														".intval($cri_peso_nascimento)." )";
				
				
				$exe = pg_query($stmt);
				if ($exe){
					echo $common->modalMsg("OK", "Inserido com sucesso","SSA2.php?id_login=$id_login&ssa2_codigo=$ssa2_codigo#tabs-2");
				}/*else{
					echo $common->modalMsg("ERRO", "Houve um erro e os dados do SSA2 - Crian&ccedil;a n&atilde;o foram salvos, tente novamente!<br>$stmt", "SSA2.php?id_login=$id_login&ssa2_mes=$mes&ssa2_ano=$ano&cid_codigo_ibge=$cidade&ssa2_codigo=$ssa2_codigo&cri_nas_vivos=$cri_nas_vivos#tabs-1");
				}*/
			}else{
				 $stmt = "UPDATE ssa2_crianca SET 
											cri_nas_vivos = '$cri_nas_vivos', 
											cri_peso_acima_dois_quinhentos = '$cri_peso_acima_dois_quinhentos', 
											cri_tres_meses_vinte_e_nove_dias = '$cri_tres_meses_vinte_e_nove_dias', 
											cri_mamando_peito = '$cri_mamando_peito', 
											cri_aleitamento = '$cri_aleitamento', 
											cri_onze_meses_e_vinte_e_nove_dias = '$cri_onze_meses_e_vinte_e_nove_dias', 
											cri_vacina_em_dia = '$cri_vacina_em_dia', 
											cri_pesadas = '$cri_pesadas', 
											cri_desnutridas = '$cri_desnutridas', 
											cri_doze_meses_e_vinte_e_nove_dias = '$cri_doze_meses_e_vinte_e_nove_dias', 
											cri_doze_vacina_em_dia = '$cri_doze_vacina_em_dia', 
											cri_doze_pesadas = '$cri_doze_pesadas', 
											cri_doze_desnutridas = '$cri_doze_desnutridas', 
											cri_menores_dois_anos = '$cri_menores_dois_anos', 
											cri_diarreia = '$cri_diarreia', 
											cri_tro = '$cri_tro', 
											cri_ira = '$cri_ira', 
											cri_pneumunia = '$cri_pneumunia', 
											cri_menores_cinco_anos = '$cri_menores_cinco_anos', 
											cri_peso_nascimento = '$cri_peso_nascimento'
									  WHERE ssa2_codigo = $ssa2_codigo" ;
				
				$exe = pg_query($stmt);
				if ($exe){
					echo $common->modalMsg("OK", "Alterado com sucesso","SSA2.php?id_login=$id_login&ssa2_codigo=$ssa2_codigo#tabs-1");
				}else{
					echo $common->modalMsg("ERRO", "Houve um erro e os dados do SSA2 - Crian&ccedil;a n&atilde;o foram salvos, tente novamente!<br>$stmt", "SSA2.php?id_login=$id_login&ssa2_mes=$mes&ssa2_ano=$ano&cid_codigo_ibge=$cidade&ssa2_codigo=$ssa2_codigo&cri_nas_vivos=$cri_nas_vivos#tabs-1");
				}
			
			}	
			
		}	
	echo $common->closeTab();
	
	echo $common->bodyTab('2');
	$select = "select * from ssa2_gestante where ssa2_codigo = $_GET[ssa2_codigo] ";
	$query = pg_query($select);
	$res_query = pg_fetch_array($query);
	$ges_cadastrada2 =  $res_query['ges_cadastrada'];
	$ges_acompanhada2 = $res_query['ges_acompanhada'];
	$ges_com_vacinas_em_dia2 = $res_query['ges_com_vacinas_em_dia'];
	$ges_consulta_pre_natal2 = $res_query['ges_consulta_pre_natal'];
	$ges_pre_natal_primeiro_tri2 = $res_query['ges_pre_natal_primeiro_tri'];
	$ges_menores_vinte_anos2 = $res_query['ges_menores_vinte_anos'];
		

		echo $form->openForm("$PHP_SELF","POST");
				echo $form->hiddenForm("acao", "addGestante");
				echo $form->hiddenForm("id_login", "$_GET[id_login]");
				echo $form->hiddenForm("ssa2_codigo", "$_GET[ssa2_codigo]");
				echo $form->hiddenForm("numlinhas", pg_num_rows($query));
				
				echo $form->inputText("ges_cadastrada", $ges_cadastrada2,"Cadastradas");
				echo $form->inputText("ges_acompanhada", $ges_acompanhada2,"Acompanhadas");
				echo $form->inputText("ges_com_vacinas_em_dia", $ges_com_vacinas_em_dia2,"Com vacinas Em dia");
				echo $form->inputText("ges_consulta_pre_natal", $ges_consulta_pre_natal2,"Fez Consulta P.N no mês");
				echo $form->inputText("ges_pre_natal_primeiro_tri", $ges_pre_natal_primeiro_tri2,"Com pré natal no 1º TRI");
				echo $form->inputText("ges_menores_vinte_anos", $ges_menores_vinte_anos2,"Menores de vinte anos");
				echo "<br><br><br>";
			//echo $form->submitButton("Salvar",$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg");
				if($res_query[ges_codigo ]== ""){
					echo $form->submitButton("Adicionar", $_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg");	
				}else{
					echo $form->submitButton("Adicionar", $_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg");
				}
				echo $common->commonButton("Voltar","adicionarFichaPsf.php",$_SESSION[linkroot].$_SESSION[comum]."imgsBotoes/voltar.png");
		echo $form->closeForm();
		if($acao == "addGestante")
		{
			if($_POST[numlinhas] == 0){
				$stmt = "INSERT INTO ssa2_gestante ( 
															ssa2_codigo, 														
															ges_cadastrada, 
															ges_acompanhada, 
															ges_com_vacinas_em_dia, 
															ges_consulta_pre_natal, 
															ges_menores_vinte_anos, 
															ges_pre_natal_primeiro_tri
															 ) VALUES ( 
															".intval($ssa2_codigo).", 														
															".intval($ges_cadastrada).", 
															".intval($ges_acompanhada).", 
															".intval($ges_com_vacinas_em_dia).", 
															".intval($ges_consulta_pre_natal).", 
															".intval($ges_menores_vinte_anos).", 
															".intval($ges_pre_natal_primeiro_tri).")";
					
				$exe2 = pg_query($stmt);
				if ($exe2){
				echo $common->modalMsg("OK", "Inserido com sucesso","SSA2.php?id_login=$id_login&ssa2_codigo=$ssa2_codigo#tabs-3");
				}else{
					echo $common->modalMsg("ERRO", "Houve um erro e os dados do SSA2 - Gestantes n&atilde;o foram salvos, tente novamente!", "SSA2.php?id_login=$id_login&ssa2_mes=$mes&ssa2_ano=$ano&cid_codigo_ibge=$cidade&ssa2_codigo=$ssa2_codigo&cri_nas_vivos=$cri_nas_vivos#tabs-2");
				}
			}else{
				 $stmt = "UPDATE ssa2_gestante SET 										
											ges_codigo = ".intval($ges_codigo).", 
											ges_cadastrada = '$ges_cadastrada', 
											ges_acompanhada = '$ges_acompanhada', 
											ges_com_vacinas_em_dia = '$ges_com_vacinas_em_dia', 
											ges_consulta_pre_natal = '$ges_consulta_pre_natal', 
											ges_menores_vinte_anos = '$ges_menores_vinte_anos', 
											ges_pre_natal_primeiro_tri = '$ges_pre_natal_primeiro_tri'
											WHERE ssa2_codigo = ".intval($ssa2_codigo) ;
			$exe = pg_query($stmt);
				if ($exe){
					echo $common->modalMsg("OK", "Alterado com sucesso","SSA2.php?id_login=$id_login&ssa2_codigo=$ssa2_codigo#tabs-2");
				}else{
					echo $common->modalMsg("ERRO", "Houve um erro e os dados do SSA2 - Crian&ccedil;a n&atilde;o foram salvos, tente novamente!<br>$stmt", "SSA2.php?id_login=$id_login&ssa2_mes=$mes&ssa2_ano=$ano&cid_codigo_ibge=$cidade&ssa2_codigo=$ssa2_codigo&cri_nas_vivos=$cri_nas_vivos#tabs-2");
				}
														
			}
			
		}
	echo $common->closeTab();
	
	
	echo $common->bodyTab('3');
		$select = "select * from ssa2_hospital where ssa2_codigo = $_GET[ssa2_codigo] ";
		$query = pg_query($select);
		$res_query = pg_fetch_array($query);
		
		$hos_de_cinco_anos_pneumonia2 =  $res_query['hos_de_cinco_anos_pneumonia'];
		$hos_menores_cinco_anos_desidratacao2 =  $res_query['hos_menores_cinco_anos_desidratacao'];
		$hos_abuso_de_alcool2 =  $res_query['hos_abuso_de_alcool'];
		$hos_por_diabetes2 =  $res_query['hos_por_diabetes'];
		$hos_outras_causas2 =  $res_query['hos_outras_causas'];
		$hos_internamento_hosp_psiqui2 =  $res_query['hos_internamento_hosp_psiqui'];
		
		echo $form->openForm("$PHP_SELF","POST");
			
			echo $form->hiddenForm("acao", "addHospital");
			echo $form->hiddenForm("id_login", "$_GET[id_login]");
			echo $form->hiddenForm("ssa2_codigo", "$_GET[ssa2_codigo]");
			echo $form->hiddenForm("numlinhas", pg_num_rows($query));
			
			echo $form->inputText("hos_de_cinco_anos_pneumonia", $hos_de_cinco_anos_pneumonia2,"< De 5 Anos por pneumunia");
			echo $form->inputText("hos_menores_cinco_anos_desidratacao", $hos_menores_cinco_anos_desidratacao2,"< De 5 anos por desidratação");
			echo $form->inputText("hos_abuso_de_alcool", $hos_abuso_de_alcool2,"por abuso se alcool");
			echo $form->inputText("hos_por_diabetes", $hos_por_diabetes2,"Por comp. do diabéte");
			echo $form->inputText("hos_outras_causas", $hos_outras_causas2,"Por outras causas");
			echo $form->inputText("hos_internamento_hosp_psiqui", $hos_internamento_hosp_psiqui2,"Internação em hospital psiqui");
			echo "<br><br><br>";
			//echo $form->submitButton("Salvar",$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg");
			if($res_query[hos_codigo ]== ""){
				echo $form->submitButton("Adicionar", $_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg");	
			}else{
				echo $form->submitButton("Adicionar", $_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg");
			}
			echo $common->commonButton("Voltar","adicionarFichaPsf.php",$_SESSION[linkroot].$_SESSION[comum]."imgsBotoes/voltar.png");
		echo $form->closeForm();
		if($acao == "addHospital"){
			if($_POST[numlinhas] == 0){
				$stmt = "INSERT INTO ssa2_hospital ( 
															ssa2_codigo, 														
															hos_de_cinco_anos_pneumonia, 
															hos_menores_cinco_anos_desidratacao, 
															hos_abuso_de_alcool, 
															hos_por_diabetes, 
															hos_outras_causas, 
															hos_internamento_hosp_psiqui
															 ) VALUES ( 
															".intval($ssa2_codigo).", 
															".intval($hos_de_cinco_anos_pneumonia).", 
															".intval($hos_menores_cinco_anos_desidratacao).", 
															".intval($hos_abuso_de_alcool).", 
															".intval($hos_por_diabetes).", 
															".intval($hos_outras_causas).", 
															".intval($hos_internamento_hosp_psiqui)." )";
				$exe = pg_query($stmt);
				if ($exe){
					echo $common->modalMsg("OK", "Inserido com sucesso","SSA2.php?id_login=$id_login&ssa2_codigo=$ssa2_codigo#tabs-4");
				}else{
					echo $common->modalMsg("ERRO", "Houve um erro e os dados do SSA2 - Hospital n&atilde;o foram salvos, tente novamente!<br>$stmt", "SSA2.php?id_login=$id_login&ssa2_mes=$mes&ssa2_ano=$ano&cid_codigo_ibge=$cidade&ssa2_codigo=$ssa2_codigo#tabs-3");
				}
			}else{
				  $stmt = "UPDATE ssa2_hospital SET				
										hos_de_cinco_anos_pneumonia = '$hos_de_cinco_anos_pneumonia', 
										hos_menores_cinco_anos_desidratacao = '$hos_menores_cinco_anos_desidratacao', 
										hos_abuso_de_alcool = '$hos_abuso_de_alcool', 
										hos_por_diabetes = '$hos_por_diabetes', 
										hos_outras_causas = '$hos_outras_causas', 
										hos_internamento_hosp_psiqui = '$hos_internamento_hosp_psiqui'
										WHERE ssa2_codigo = ".intval($ssa2_codigo) ;
				
			$exe = pg_query($stmt);
				if ($exe){
					echo $common->modalMsg("OK", "Alterado com sucesso","SSA2.php?id_login=$id_login&ssa2_codigo=$ssa2_codigo#tabs-3");
				}else{
					echo $common->modalMsg("ERRO", "Houve um erro e os dados do SSA2 - Crian&ccedil;a n&atilde;o foram salvos, tente novamente!<br>$stmt", "SSA2.php?id_login=$id_login&ssa2_mes=$mes&ssa2_ano=$ano&cid_codigo_ibge=$cidade&ssa2_codigo=$ssa2_codigo&cri_nas_vivos=$cri_nas_vivos#tabs-3");
				}
														
			}
			
		}
			
	
	echo $common->closeTab();
	
		
	echo $common->bodyTab('4');
		$select = "select * from ssa2_controlados where ssa2_codigo = $_GET[ssa2_codigo] ";
		$query = pg_query($select);
		$res_query = pg_fetch_array($query);
		
		$con_diabetico_cadastrado2 =  $res_query['con_diabetico_cadastrado'];
		$con_diabetico_acompanhado2 =  $res_query['con_diabetico_acompanhado'];
		$con_hipertensos_cadastrado2 =  $res_query['con_hipertensos_cadastrado'];
		$con_hipertensos_acompanhado2 =  $res_query['con_hipertensos_acompanhado'];
		$con_tuberculose_cadastrado2 =  $res_query['con_tuberculose_cadastrado'];
		$con_tuberculose_acompanhado2 =  $res_query['con_tuberculose_acompanhado'];
		$con_hanseniase_cadastrado2 =  $res_query['con_hanseniase_cadastrado'];
		$con_hanseniase_acompanhado2 =  $res_query['con_hanseniase_acompanhado'];
		echo $form->openForm("$PHP_SELF","POST");
			
			echo $form->hiddenForm("acao", "addControlado");
			echo $form->hiddenForm("id_login", "$_GET[id_login]");
			echo $form->hiddenForm("ssa2_codigo", "$_GET[ssa2_codigo]");
			echo $form->hiddenForm("numlinhas", pg_num_rows($query));
			
			echo $form->inputText("con_diabetico_cadastrado", $con_diabetico_cadastrado2,"Diab&eacute;ticos cadastrados");
			echo $form->inputText("con_diabetico_acompanhado", $con_diabetico_acompanhado2,"Diab&eacute;ticos acompanhados ");
			echo $form->inputText("con_hipertensos_cadastrado", $con_hipertensos_cadastrado2,"Hipertensos cadastrados");
			echo $form->inputText("con_hipertensos_acompanhado", $con_hipertensos_acompanhado2,"Hipertensos acompanhados");
			echo $form->inputText("con_tuberculose_cadastrado", $con_tuberculose_cadastrado2,"Tuberlosos cadastrados");
			echo $form->inputText("con_tuberculose_acompanhado", $con_tuberculose_acompanhado2,"Tuberlosos acompanhados");
			echo $form->inputText("con_hanseniase_cadastrado", $con_hanseniase_cadastrado2,"Hanseniase cadastrados");
			echo $form->inputText("con_hanseniase_acompanhado", $con_hanseniase_acompanhado2,"Hanseniase acompanhados");
			echo "<br><br><br>";
			//echo $form->submitButton("Salvar",$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg");
			if($res_query[con_codigo ]== ""){
				echo $form->submitButton("Adicionar", $_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg");	
			}else{
				echo $form->submitButton("Adicionar", $_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg");
			}
			echo $common->commonButton("Voltar","adicionarFichaPsf.php",$_SESSION[linkroot].$_SESSION[comum]."imgsBotoes/voltar.png");
		echo $form->closeForm();
		if($acao == "addControlado"){
			if($_POST[numlinhas] == 0){
				$stmt = "INSERT INTO ssa2_controlados ( 
																	ssa2_codigo,
																	con_diabetico_cadastrado, 
																	con_diabetico_acompanhado, 
																	con_hipertensos_cadastrado, 
																	con_hipertensos_acompanhado, 
																	con_tuberculose_cadastrado, 
																	con_tuberculose_acompanhado, 
																	con_hanseniase_cadastrado, 
																	con_hanseniase_acompanhado
																	 ) VALUES ( 
																	".intval($ssa2_codigo).", 
																	".intval($con_diabetico_cadastrado).", 
																	".intval($con_diabetico_acompanhado).", 
																	".intval($con_hipertensos_cadastrado).", 
																	".intval($con_hipertensos_acompanhado).", 
																	".intval($con_tuberculose_cadastrado).", 
																	".intval($con_tuberculose_acompanhado).", 
																	".intval($con_hanseniase_cadastrado).",
																	".intval($con_hanseniase_acompanhado)." )";
				$exe = pg_query($stmt);
					if ($exe){
					echo $common->modalMsg("OK", "Inserido com sucesso","SSA2.php?id_login=$id_login&ssa2_codigo=$ssa2_codigo#tabs-5");
					}else{
						echo $common->modalMsg("ERRO", "Houve um erro e os dados do SSA2 - controlados n&atilde;o foram salvos, tente novamente!", "SSA2.php?id_login=$id_login&ssa2_mes=$mes&ssa2_ano=$ano&cid_codigo_ibge=$cidade&ssa2_codigo=$ssa2_codigo#tabs-4");
					}
			}else{
$stmt = "UPDATE ssa2_controlados SET 							
							con_diabetico_cadastrado = '$con_diabetico_cadastrado', 
							con_diabetico_acompanhado = '$con_diabetico_acompanhado', 
							con_hipertensos_cadastrado = '$con_hipertensos_cadastrado', 
							con_hipertensos_acompanhado = '$con_hipertensos_acompanhado', 
							con_tuberculose_cadastrado = '$con_tuberculose_cadastrado', 
							con_tuberculose_acompanhado = '$con_tuberculose_acompanhado', 
							con_hanseniase_cadastrado = '$con_hanseniase_cadastrado', 
							con_hanseniase_acompanhado = '$con_hanseniase_acompanhado' 							
							WHERE ssa2_codigo = ".intval($ssa2_codigo) ;
								
			$exe = pg_query($stmt);
				if ($exe){
					echo $common->modalMsg("OK", "Alterado com sucesso","SSA2.php?id_login=$id_login&ssa2_codigo=$ssa2_codigo#tabs-4");
				}else{
					echo $common->modalMsg("ERRO", "Houve um erro e os dados do SSA2 - Crian&ccedil;a n&atilde;o foram salvos, tente novamente!<br>$stmt", "SSA2.php?id_login=$id_login&ssa2_mes=$mes&ssa2_ano=$ano&cid_codigo_ibge=$cidade&ssa2_codigo=$ssa2_codigo&cri_nas_vivos=$cri_nas_vivos#tabs-4");
				}
														
			}
			
		}
		
		echo $common->closeTab();
		
	echo $common->bodyTab('5');
		$select = "select * from ssa2_obito where ssa2_codigo = $_GET[ssa2_codigo] ";
		$query = pg_query($select);
		$res_query = pg_fetch_array($query);
		
		$obt_menores_vinte_e_oito_dias2 =  $res_query['obt_menores_vinte_e_oito_dias'];
		$obt_diarreia2 =  $res_query['obt_diarreia'];
		$obt_ira2 =  $res_query['obt_ira'];
		$obt_outras_causas2 =  $res_query['obt_outras_causas'];
		$obt_vinte_e_oito_dias_a_onze_meses2 =  $res_query['obt_vinte_e_oito_dias_a_onze_meses'];
		$obt_onze_diarreia2 =  $res_query['obt_onze_diarreia'];
		$obt_onze_ira2 =  $res_query['obt_onze_ira'];
		$obt_onze_outras_causas2 =  $res_query['obt_onze_outras_causas'];
		$obt_menores_um_ano2 =  $res_query['obt_menores_um_ano'];
		$obt_menores_um_ano_diarreia2 =  $res_query['obt_menores_um_ano_diarreia'];
		$obt_menores_um_ano_ira2 =  $res_query['obt_menores_um_ano_ira'];
		$obt_menores_um_ano_outras_causas2 =  $res_query['obt_menores_um_ano_outras_causas'];
		$obt_mulheres_idade_fertil2 =  $res_query['obt_mulheres_idade_fertil'];
		$obt_mulheres_dez_a_quatorze2 =  $res_query['obt_mulheres_dez_a_quatorze'];
		$obt_mulheres_quinze_a_quarenta_e_nove2 =  $res_query['obt_mulheres_quinze_a_quarenta_e_nove'];
		$obt_mulheres_outros_obitos2 =  $res_query['obt_mulheres_outros_obitos'];
		
		
		echo $form->openForm("$PHP_SELF","POST");
		
			echo $form->hiddenForm("acao", "addObito");
			echo $form->hiddenForm("id_login", "$_GET[id_login]");
			echo $form->hiddenForm("ssa2_codigo", "$_GET[ssa2_codigo]");
			echo $form->hiddenForm("numlinhas", pg_num_rows($query));
			
			echo $form->inputText("obt_menores_vinte_e_oito_dias", $obt_menores_vinte_e_oito_dias2,"<b>DE MENORES DE 28 DIAS</b>");
			echo $form->inputText("obt_diarreia", $obt_diarreia2,"Por diarréia");
			echo $form->inputText("obt_ira", $obt_ira2,"por ira");
			echo $form->inputText("obt_outras_causas", $obt_outras_causas2,"Por outras causas");
			echo $form->inputText("obt_vinte_e_oito_dias_a_onze_meses", $obt_vinte_e_oito_dias_a_onze_meses2,"<b>De 28 dias a 11 meses/29 dias</b>");			
			echo $form->inputText("obt_onze_diarreia", $obt_onze_diarreia2,"Por diarréia");
			echo $form->inputText("obt_onze_ira", $obt_onze_ira2,"por ira");
			echo $form->inputText("obt_onze_outras_causas", $obt_onze_outras_causas2,"Por outras causas");
			echo $form->inputText("obt_menores_um_ano", $obt_menores_um_ano2,"<b>De menores de 1 ano</b>");
			echo $form->inputText("obt_menores_um_ano_diarreia", $obt_menores_um_ano_diarreia2,"Por diarréia");
			echo $form->inputText("obt_menores_um_ano_ira", $obt_menores_um_ano_ira2,"por ira");
			echo $form->inputText("obt_menores_um_ano_outras_causas", $obt_menores_um_ano_outras_causas2,"Por outras causas");
			echo $form->inputText("obt_mulheres_idade_fertil", $obt_mulheres_idade_fertil2,"<b>Mulheres em idade fértil</b>");
			echo $form->inputText("obt_mulheres_dez_a_quatorze", $obt_mulheres_dez_a_quatorze2,"De 10 14 anos");
			echo $form->inputText("obt_mulheres_quinze_a_quarenta_e_nove", $obt_mulheres_quinze_a_quarenta_e_nove2,"De 15 a 49 anos");
				echo $form->inputText("obt_mulheres_outros_obitos", $obt_mulheres_outros_obitos2,"Outros Obito");
			echo "<br><br><br>";
			//echo $form->submitButton("Salvar",$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg");
			if($res_query[obt_codigo ]== ""){
				echo $form->submitButton("Adicionar", $_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg");	
			}else{
				echo $form->submitButton("Adicionar", $_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg");
			}
			echo $common->commonButton("Voltar","adicionarFichaPsf.php",$_SESSION[linkroot].$_SESSION[comum]."imgsBotoes/voltar.png");
		echo $form->closeForm();
		if($acao == "addObito"){
				if($_POST[numlinhas] == 0){
					$stmt = "INSERT INTO ssa2_obito ( 
															ssa2_codigo, 
															obt_diarreia, 
															obt_ira, 
															obt_vinte_e_oito_dias_a_onze_meses, 
															obt_menores_vinte_e_oito_dias, 
															obt_onze_diarreia, 
															obt_onze_ira, 
															obt_onze_outras_causas, 
															obt_outras_causas, 
															obt_menores_um_ano, 
															obt_menores_um_ano_diarreia, 
															obt_menores_um_ano_ira, 
															obt_menores_um_ano_outras_causas, 
															obt_mulheres_idade_fertil, 
															obt_mulheres_dez_a_quatorze, 
															obt_mulheres_quinze_a_quarenta_e_nove, 
															obt_mulheres_outros_obitos
															 ) VALUES ( 
															".intval($ssa2_codigo).", 
															".intval($obt_diarreia).", 
															".intval($obt_ira).", 
															".intval($obt_vinte_e_oito_dias_a_onze_meses).", 
															".intval($obt_menores_vinte_e_oito_dias).", 
															".intval($obt_onze_diarreia).", 
															".intval($obt_onze_ira).", 
															".intval($obt_onze_outras_causas).", 
															".intval($obt_outras_causas).", 
															".intval($obt_menores_um_ano).", 
															".intval($obt_menores_um_ano_diarreia).", 
															".intval($obt_menores_um_ano_ira).", 
															".intval($obt_menores_um_ano_outras_causas).", 
															".intval($obt_mulheres_idade_fertil).", 
															".intval($obt_mulheres_dez_a_quatorze).", 
															".intval($obt_mulheres_quinze_a_quarenta_e_nove).", 
															".intval($obt_mulheres_outros_obitos)." )";
					$exe = pg_query($stmt);
						if ($exe){
						echo $common->modalMsg("OK", "Inserido com sucesso","SSA2.php?id_login=$id_login&ssa2_codigo=$ssa2_codigo#tabs-5");
						}else{
							echo $common->modalMsg("ERRO", "Houve um erro e os dados do SSA2 - controlados n&atilde;o foram salvos, tente novamente!<br>$stmt", "SSA2.php?id_login=$id_login&ssa2_mes=$mes&ssa2_ano=$ano&cid_codigo_ibge=$cidade&ssa2_codigo=$ssa2_codigo#tabs-5");
						}
				}else{
				  $stmt = "UPDATE ssa2_obito SET
										obt_diarreia = '$obt_diarreia', 
										obt_ira = '$obt_ira', 
										obt_vinte_e_oito_dias_a_onze_meses = '$obt_vinte_e_oito_dias_a_onze_meses', 
										obt_menores_vinte_e_oito_dias = '$obt_menores_vinte_e_oito_dias', 
										obt_onze_diarreia = '$obt_onze_diarreia', 
										obt_onze_ira = '$obt_onze_ira', 
										obt_onze_outras_causas = '$obt_onze_outras_causas', 
										obt_outras_causas = '$obt_outras_causas', 
										obt_menores_um_ano = '$obt_menores_um_ano', 
										obt_menores_um_ano_diarreia = '$obt_menores_um_ano_diarreia', 
										obt_menores_um_ano_ira = '$obt_menores_um_ano_ira', 
										obt_menores_um_ano_outras_causas = '$obt_menores_um_ano_outras_causas', 
										obt_mulheres_idade_fertil = '$obt_mulheres_idade_fertil', 
										obt_mulheres_dez_a_quatorze = '$obt_mulheres_dez_a_quatorze', 
										obt_mulheres_quinze_a_quarenta_e_nove = '$obt_mulheres_quinze_a_quarenta_e_nove', 
										obt_mulheres_outros_obitos = '$obt_mulheres_outros_obitos'
										WHERE ssa2_codigo = ".intval($ssa2_codigo) ;
					
				
			$exe = pg_query($stmt);
				if ($exe){
					echo $common->modalMsg("OK", "Alterado com sucesso","SSA2.php?id_login=$id_login&ssa2_codigo=$ssa2_codigo#tabs-5");
				}else{
					echo $common->modalMsg("ERRO", "Houve um erro e os dados do SSA2 - Crian&ccedil;a n&atilde;o foram salvos, tente novamente!<br>$stmt", "SSA2.php?id_login=$id_login&ssa2_mes=$mes&ssa2_ano=$ano&cid_codigo_ibge=$cidade&ssa2_codigo=$ssa2_codigo&cri_nas_vivos=$cri_nas_vivos#tabs-5");
				}
														
			}
			
		
			
		}
		
	echo $common->closeTab();

?>