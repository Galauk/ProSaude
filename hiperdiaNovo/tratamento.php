<script type="text/javascript" src="/WebSocialComum/library/js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="/WebSocialSaude/lib/ui/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="/WebSocialComum/library/js/jquery.buscar.js"></script>
<script><!--
            
    jQuery(function(){
		jQuery('#tabs').tabs(); 
		jQuery("#buscar78").buscar({
          tipo:'usuarios',
          template : function(ul, item) {
                       return $("<li></li>").data("item.autocomplete", item).append(
                               "<a>" + item.label + "</a>").appendTo(ul);
               }
    	});
		jQuery("#hipermed_medicamentoso").change(function() {
			verificaSeDesabilitaOuNao();
		});
		verificaSeDesabilitaOuNao();
    		
    });
	function verificaSeDesabilitaOuNao(){
		if(jQuery('#hipermed_medicamentoso').val() == 'N'){
			//jQuery('#medicamentosBloquear').addClass('ui-state-disabled');
			jQuery('#medicamentosBloquear select')
			.attr('disabled', 'disabled')
			.val('');
		}else{
			jQuery('#medicamentosBloquear select').removeAttr('disabled');
		}
	}
    
	function validaDados(){
		var usr_codigo = document.getElementById("usr_codigo").value;
		var hiper_pa_sistolica = document.getElementById("hiper_pa_sistolica").value;
		var hiper_pa_diastolica = document.getElementById("hiper_pa_diastolica").value;
		if(usr_codigo == ""){
			alert("Preencha o campo responsável");
			exit();
		}
		if(hiper_pa_sistolica == "" || hiper_pa_diastolica == ""){
			alert("Preencha os valores de Pressao");
			exit();
		}
		document.consultaHiper.submit();
	}
        
        
</script>

<?
session_start();
$sqlMedicamentos = "SELECT * FROM hiperdia_medicamentos WHERE hiper_codigo = $hiper_codigo";
$queryMedicamentos = pg_query($sqlMedicamentos);
$linhaMedicamentos = pg_fetch_array($queryMedicamentos);

echo "
<table border='0' cellpadding=0 cellspacing=0 width='100%' class='tabela'>
    <tr> 
		<td  colspan='3'>
			 <b>MEDICAMENTOS / EXAMES / OBSERVA&Ccedil;&Otilde;ES<br><br>
		</td>
    </tr>
	<tr>
		<td width='49%'>
			 <b>N&atilde;o Medicamentoso &nbsp;
			<select name='hipermed_medicamentoso' id='hipermed_medicamentoso' class='inputForm'>
				<option value='N'".($linhaMedicamentos['hipermed_medicamentoso'] == 'N' ? "selected='selected'": '').">Nao</option>
				<option value='S'".($linhaMedicamentos['hipermed_medicamentoso'] == 'S' ? "selected='selected'": '').">Sim</option>
			</select>
			<br>
			<table border='0' cellpadding=5 cellspacing=1 width='100%' id='medicamentosBloquear' >
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
					<td width='20%'>
					<input type='hidden' name='proc_codigo[]' value='01'>
					<select name='medicamento[]'class='inputForm'>";
					$sqlMedicamentosHidroclorotiazida = "SELECT * FROM hiperdia_medicamentos WHERE hiper_codigo = $hiper_codigo and pro_codigo = '01'";
					$queryMedicamentosHidroclorotiazida = pg_query($sqlMedicamentosHidroclorotiazida);
					$linhaMedicamentosHidroclorotiazida = pg_fetch_array($queryMedicamentosHidroclorotiazida);
					echo "
					   <option value=''>  </option>
					   <option value='0,5'".($linhaMedicamentosHidroclorotiazida['hipermed_dosagem'] == '0,5' ? "selected='selected'": '').">Meio</option>
					   <option value='1,0'".($linhaMedicamentosHidroclorotiazida['hipermed_dosagem'] == '1,0' ? "selected='selected'": '').">Um</option>
					   <option value='2,0'".($linhaMedicamentosHidroclorotiazida['hipermed_dosagem'] == '2,0' ? "selected='selected'": '').">Dois</option>
					   <option value='3,0'".($linhaMedicamentosHidroclorotiazida['hipermed_dosagem'] == '3,0' ? "selected='selected'": '').">Tres</option>
					 </select>			  
					</td>
				</tr>
				<tr> 
					<td width='80%' bgcolor='#FFFFFF' class='inputForm'>
						PROPANOLOL 40MG
					</td>
					<td width='20%'>
					<input type='hidden' name='proc_codigo[]' value='02'>
					<select name='medicamento[]' class='inputForm'>";
					$sqlMedicamentosPropanolol = "SELECT * FROM hiperdia_medicamentos WHERE hiper_codigo = $hiper_codigo and pro_codigo = '02'";
					$queryMedicamentosPropanolol = pg_query($sqlMedicamentosPropanolol);
					$linhaMedicamentosPropanolol = pg_fetch_array($queryMedicamentosPropanolol);
					echo "
					   <option value=''>  </option>
					   <option value='0,5'".($linhaMedicamentosPropanolol['hipermed_dosagem'] == '0,5' ? "selected='selected'": '').">Meio</option>
					   <option value='1,0'".($linhaMedicamentosPropanolol['hipermed_dosagem'] == '1,0' ? "selected='selected'": '').">Um</option>
					   <option value='2,0'".($linhaMedicamentosPropanolol['hipermed_dosagem'] == '2,0' ? "selected='selected'": '').">Dois</option>
					   <option value='3,0'".($linhaMedicamentosPropanolol['hipermed_dosagem'] == '3,0' ? "selected='selected'": '').">Tres</option>
					   <option value='4,0'".($linhaMedicamentosPropanolol['hipermed_dosagem'] == '4,0' ? "selected='selected'": '').">Quatro</option>
					   <option value='5,0'".($linhaMedicamentosPropanolol['hipermed_dosagem'] == '5,0' ? "selected='selected'": '').">Cinco</option>
					   <option value='6,0'".($linhaMedicamentosPropanolol['hipermed_dosagem'] == '6,0' ? "selected='selected'": '').">Seis</option>
					</select>";
					
					//echo $sqlMedicamentosPropanolol;
					echo "		  
					</td>
				</tr>
				<tr> 
					<td width='80%' bgcolor='#FFFFFF' class='inputForm'>
						CAPTOPRIL 25 MG
					</td>
					<td width='20%'>
					<input type='hidden' name='proc_codigo[]' value='03'>
					<select name='medicamento[]' class='inputForm'>";
					
					$sqlMedicamentosCaptopril = "SELECT * FROM hiperdia_medicamentos WHERE hiper_codigo = $hiper_codigo and pro_codigo = '03'";
					$queryMedicamentosCaptopril = pg_query($sqlMedicamentosCaptopril);
					$linhaMedicamentosCaptopril = pg_fetch_array($queryMedicamentosCaptopril);
					
					echo "
					   <option value=''>  </option>
					   <option value='0,5'".($linhaMedicamentosCaptopril['hipermed_dosagem'] == '0,5' ? "selected='selected'": '').">Meio</option>
					   <option value='1,0'".($linhaMedicamentosCaptopril['hipermed_dosagem'] == '1,0' ? "selected='selected'": '').">Um</option>
					   <option value='2,0'".($linhaMedicamentosCaptopril['hipermed_dosagem'] == '2,0' ? "selected='selected'": '').">Dois</option>
					   <option value='3,0'".($linhaMedicamentosCaptopril['hipermed_dosagem'] == '3,0' ? "selected='selected'": '').">Tres</option>
					   <option value='4,0'".($linhaMedicamentosCaptopril['hipermed_dosagem'] == '4,0' ? "selected='selected'": '').">Quatro</option>
					   <option value='5,0'".($linhaMedicamentosCaptopril['hipermed_dosagem'] == '5,0' ? "selected='selected'": '').">Cinco</option>
					   <option value='6,0'".($linhaMedicamentosCaptopril['hipermed_dosagem'] == '6,0' ? "selected='selected'": '').">Seis</option>
				   </select>			  
					</td>
				</tr>		
				<tr> 
					<td width='80%' bgcolor='#FFFFFF' class='inputForm'>
						GLIBENCLAMIDA 5 MG
					</td>
					<td>
					<input type='hidden' name='proc_codigo[]' value='04'>
					<select name='medicamento[]' class='inputForm' style=\"width:55px;\">";
					
					$sqlMedicamentosGlibenclamida = "SELECT * FROM hiperdia_medicamentos WHERE hiper_codigo = $hiper_codigo and pro_codigo = '04'";
					$queryMedicamentosGlibenclamida = pg_query($sqlMedicamentosGlibenclamida);
					$linhaMedicamentosGlibenclamida = pg_fetch_array($queryMedicamentosGlibenclamida);
					
					echo "
					   <option value=''>  </option>
					   <option value='0,5'".($linhaMedicamentosGlibenclamida['hipermed_dosagem'] == '0,5' ? "selected='selected'": '').">Meio</option>
					   <option value='1,0'".($linhaMedicamentosGlibenclamida['hipermed_dosagem'] == '1,0' ? "selected='selected'": '').">Um</option>
					   <option value='2,0'".($linhaMedicamentosGlibenclamida['hipermed_dosagem'] == '2,0' ? "selected='selected'": '').">Dois</option>
                                           <option value='3,0'".($linhaMedicamentosGlibenclamida['hipermed_dosagem'] == '3,0' ? "selected='selected'": '').">Tres</option>
					   <option value='4,0'".($linhaMedicamentosGlibenclamida['hipermed_dosagem'] == '4,0' ? "selected='selected'": '').">Quatro</option>
					</select>			  
					</td>
				</tr>
				
				<tr> 
					<td width='80%' class='inputForm'>
						METFORMINA 850MG
					</td>
					<td width='20%' >
					<input type='hidden' name='proc_codigo[]' value='05'>";
					
					$sqlMedicamentosMetformina = "SELECT * FROM hiperdia_medicamentos WHERE hiper_codigo = $hiper_codigo and pro_codigo = '05'";
					$queryMedicamentosMetformina = pg_query($sqlMedicamentosMetformina);
					$linhaMedicamentosMetformina = pg_fetch_array($queryMedicamentosMetformina);
					
					echo "
					   <select name='medicamento[]' class='inputForm'>
					   <option value=''>  </option>
					   <option value='0,5'".($linhaMedicamentosMetformina['hipermed_dosagem'] == '0,5' ? "selected='selected'": '').">Meio</option>
					   <option value='1,0'".($linhaMedicamentosMetformina['hipermed_dosagem'] == '1,0' ? "selected='selected'": '').">Um</option>
					   <option value='2,0'".($linhaMedicamentosMetformina['hipermed_dosagem'] == '2,0' ? "selected='selected'": '').">Dois</option>
					   <option value='3,0'".($linhaMedicamentosMetformina['hipermed_dosagem'] == '3,0' ? "selected='selected'": '').">Tres</option>
					   <option value='4,0'".($linhaMedicamentosMetformina['hipermed_dosagem'] == '4,0' ? "selected='selected'": '').">Quatro</option>
                                           <option value='5,0'".($linhaMedicamentosMetformina['hipermed_dosagem'] == '5,0' ? "selected='selected'": '').">Cinco</option>
					   </select>			  
					</td>
				</tr>		
			</table>
			<table border='0' cellpadding=0 cellspacing=0 width='100%'>
				<tr> 
					<td colspan='2'>&nbsp;  </td>
				</tr>
				<tr> ";
				$sqlInsulina = "SELECT distinct hipermed_insulina_dia FROM hiperdia_medicamentos WHERE hiper_codigo = $hiper_codigo";
				$queryInsulina = pg_query($sqlInsulina);
				$linhaInsulina = pg_fetch_array($queryInsulina);
					
					echo"
					<td >  <b>Insulina: Unidades/dia </td>
					<td><input name='hipermed_insulina_dia'  type='text' value='$linhaInsulina[hipermed_insulina_dia]' size='10' maxlength='6' class='inputForm'> </td>	  
				</tr>
				<tr>
					<td> <b>Outros Medicamentos? </td>
					<td>
						<select name='hipermed_outros' class='inputForm'>
							 <option value='S'".($linhaMedicamentos['hipermed_outros'] == 'S' ? "selected='selected'": '').">Sim</option>
							 <option value='N'".($linhaMedicamentos['hipermed_outros'] == 'N' ? "selected='selected'": '').">Nao</option>
						</select>
					</td>
				</tr>
			</table>
			<br>
			<table border='0' cellpadding=0 cellspacing=0 width='100%'>
				<tr>";
                                      echo $form->inputText('buscar78', $valor,'Buscar','60');
                                      //echo $form->inputText('med_codigo', $valor,'Prontuario','60',NULL,null,NULL,"S",NULL,NULL,NULL,"inputForm ocultar");
                                      echo $form->hiddenForm("usr_codigo","$regs[usr_codigo]");
                                      echo $form->inputText("usr_nome", $regs[usr_nome],"Respons&aacute;vel", 60, NULL, "onChange=\"return buscaDispensados(this.value)\"",null,"S");
                                     
                                  
                        
                echo"
				</tr>
			</table>";echo"<br><br>";
		 echo "<div style='clear:both; width:400px; border:solid 0px;'>";
					echo"<div style='float:right; width:205px;'>";		
						if($hiper_codigo == "" || $hiper_codigo == null){
							echo $common->commonButton("Salvar",null,"salvar.gif","onclick=\"validaDados();\"");
						}else{
							echo $common->commonButton("Editar",null,"editar_on.png","onclick=\"validaDados();\"");
						}
					echo"</div>";
					echo"<div style='float:right'>";
						echo $common->commonButton("voltar", "pesquisaHiperdia.php?id_login=$id_login", "voltar.png");
					echo"</div>";
				echo"</div>";
		 		
		 		
		 echo"
			
		</td>
		<td width='2%'>&nbsp;
			
		</td>
		<td width='49%' valign='top'>
			<table border='0' cellpadding=0 cellspacing=0 width='100%'>
			  <tr>
			  	<td>
			  		&nbsp;
			  	</td>
			  </tr>
  			  <tr> 
				 <td>  <b>Nomes de outros medicamentos <br>
					  <textarea name='hipermed_nome_outros' class='inputForm' style=\"width:400px;height:115px;\">$linhaMedicamentos[hipermed_nome_outros]</textarea>
				 </td>	  
			  </tr> ";
					
		  echo "
			</table>";
		echo"</td>
	</tr>
</table>
<input type='hidden' name='pac_codigo' id='pac_codigo' value='$usu_codigo'>";
?>