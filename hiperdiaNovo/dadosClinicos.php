<?
echo $table->openTable("lista");
				echo $table->criaLinha(array("Informa&ccedil;&otilde;es Gerais do Paciente"),null,null,"S");
			echo $table->closeTable();
			$arrayGlicemia = array("J"=>"Em Jejum","P"=>"Pos Prandial");
			$table1 = 
				  $form->inputText("hiper_pa_sistolica",$regs["hiper_pa_sistolica"],"PA Sist&oacute;lica",null,null,"style='text-align:right'").
				   $form->inputText("hiper_pa_diastolica",$regs["hiper_pa_diastolica"],"PA Diast&oacute;lica",null,null,"style='text-align:right'").
				   $form->inputText("hiper_cintura",$regs["hiper_cintura"],"Cintura(cm)",null,null,"style='text-align:right'").
				   $form->inputText("hiper_peso",$regs["hiper_peso"],"Peso(Kg)",null,null,"style='text-align:right'").
				   $form->inputText("hiper_altura",$regs["hiper_altura"],"Altura(cm)",null,null,"style='text-align:right'").
				   $form->inputText("hiper_glicemia_capilar",$regs["hiper_glicemia_capilar"],"Exame de Glicemia(mg/dll)",null,null,"style='text-align:right'").
				   $form->inputSelect("hiper_glicemia_realizada",$arrayGlicemia,"Tipo Glicemia",null,null,null,$regs["hiper_glicemia_realizada"]);
			
			$arraySimNao = array("N"=>"N&atilde;o","S"=>"Sim");
			
			$table2 = 
				$form->inputSelect("hiper_antecedentes_familiares",$arraySimNao,"Antecedentes Cardiacos",null,null,null,$regs["hiper_antecedentes_familiares"],null,NULL,null,'N','S').
				$form->inputSelect("hiper_diabetes_1",$arraySimNao,"Diabetes 1",null,null,null,$regs["hiper_diabetes_1"],null,NULL,null,'N','S').
				$form->inputSelect("hiper_diabetes_2",$arraySimNao,"Diabetes 2",null,null,null,$regs["hiper_diabetes_2"],null,NULL,null,'N','S').
				$form->inputSelect("hiper_tabagismo",$arraySimNao,"Tabagismo",null,null,null,$regs["hiper_tabagismo"],null,NULL,null,'N','S').
				$form->inputSelect("hiper_sedentarismo",$arraySimNao,"Sedentarismo",null,null,null,$regs["hiper_sedentarismo"],null,NULL,null,'N','S').
				$form->inputSelect("hiper_sobrepeso",$arraySimNao,"Sobrepeso/Obesidade",null,null,null,$regs["hiper_sobrepeso"],null,NULL,null,'N','S').
				$form->inputSelect("hiper_hipertensao",$arraySimNao,"Hipertens&atilde;o Arterial",null,null,null,$regs["hiper_hipertensao"],null,NULL,null,'N','S');
				
			$table3 = $form->inputSelect("hiper_infarto",$arraySimNao,"Infarto Agudo",null,null,null,$regs["hiper_infarto"],null,NULL,null,'N','S').
					  $form->inputSelect("hiper_outras_coronariopatias",$arraySimNao,"Outras Coronariopatias",null,null,null,$regs["hiper_outras_coronariopatias"],null,NULL,null,'N','S').
					  $form->inputSelect("hiper_avc",$arraySimNao,"AVC",null,null,null,$regs["hiper_avc"],null,NULL,null,'N','S').
					  $form->inputSelect("hiper_pe_diabetico",$arraySimNao,"P&eacute; Diabetico",null,null,null,$regs["hiper_pe_diabetico"],null,NULL,null,'N','S').
					  $form->inputSelect("hiper_amputacao",$arraySimNao,"Amputa&ccedil;&atilde;o por Diabetes",null,null,null,$regs["hiper_amputacao"],null,NULL,null,'N','S').
					  $form->inputSelect("hiper_doenca_renal",$arraySimNao,"Doen&ccedil;a Renal",null,null,null,$regs["hiper_doenca_renal"],null,NULL,null,'N','S');
					  
			echo $table->openTable(null,"100%",null);
				echo $table->criaLinha(array($table1,$table2,$table3));
				echo $table->criaLinha(array($common->commonButton("voltar", "pesquisaHiperdia.php?id_login=$id_login", "voltar.png")));
			echo $table->closeTable();
			
			
?>