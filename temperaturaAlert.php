<script type="text/javascript" src="funcoes.js"></script>
<?php 
session_start();
require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";

$common = new commonClass();
$form = new classForm();

$id_login = intval($id_login);

	while($umSet = pg_fetch_array($querySetor)){
		$sqlGeladeira = "SELECT * 
						   FROM geladeira as gel
						   JOIN setor as set
							 ON gel.set_codigo = set.set_codigo
						   JOIN usuarios_setores as uset
							 ON uset.set_codigo = set.set_codigo
						  WHERE set.set_codigo = $umSet[set_codigo]";
		$qryGeladeira = pg_query($sqlGeladeira);
		$number = pg_num_rows($qryGeladeira);
		if($number > 0){
			echo $common->incJquery();
			$pegaCod = pg_fetch_array($qryGeladeira);
			$gel_codigo = $pegaCod['gel_codigo'];
			
			echo $common->openModal("Temperatura","800");
				echo $form->openForm("temperaturaGeladeira.php","POST");
				$dataAtual = date('d/m/Y');
					echo $form->hiddenForm("gel_codigo", $gel_codigo);
					echo $form->hiddenForm(acao, "salvar");
					echo $form->hiddenForm("caminho", "index.php");
					//echo $form->hiddenForm("periodo", "$periodo");
					echo $form->inputText("data","$dataAtual","Data Temperatura",null,10,"onKeypress=\"return Ajusta_Data(this,event)\"",null,"S");
					echo $form->inputText("temp_minima","$temp_minima","Temperatura Minima");
					echo $form->inputText("temp_momento","$temp_momento","Temperatura Momento");
					echo $form->inputText("temp_maxima","$temp_maxima","Temperatura Maxima");
					echo $form->inputText("periodo", $periodo,"Periodo",null,null,null,null,"S");
					echo $form->textArea("observacao","$observacao","Observa&ccedil;&atilde;o");
					echo $form->submitButton(null,"".$_SESSION[linkroot].$_SESSION[comum]."imgs/salvar_on.jpg");
				echo $form->closeForm();
			echo $common->closeModal();
		}
	}
?>