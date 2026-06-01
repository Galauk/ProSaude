<?
	session_start(); 
?>
	<table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
    	<tr>
    		<td>
    			<table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
    				<input type="hidden" name="idDiagnostico" value="">
					<tr>
    					<td width="2%">&nbsp;</td>
    					<td width="19%">Data do Diagn&oacute;stico</td>
    					<td>
    						<input type="text" name="dataDiagnostico" value=""  size="10" maxlength="10" class="inputForm">
							<a href="#"><img src="<?= $_SESSION[linkroot].$_SESSION[comum]?>imgs/calendario_on.jpg" width="34" height="21" border="no" title='Calend&aacute;rio' style="vertical-align:bottom;"></a>
						</td>
    				</tr>
    				<tr>
    					<td width="2%">&nbsp;</td>
    					<td width="19%">Atendent:</td>
    					<td>
    						<input type="text" name="cdAtendente" value="" size="10" maxlength="10" class="inputForm" readonly>
    						<input type="text" name="nmAtendente" value="" size="50" maxlength="50" class="inputForm" readonly>
    					</td>
    				</tr>
    				<tr>
    					<td width="2%">&nbsp;</td>
    					<td width="19%">Medico:</td>
    					<td>
    						<input type="text" name="cdMedico" value="" size="10" maxlength="10" class="inputForm" readonly>
    						<input type="text" name="nmMedico" value="" size="50" maxlength="50" class="inputForm" readonly>
    						<a href="javaScript:showFindMedico(document.formHanseniase)" title="Pesquisar Municipio/Unidade de saude"><img border="0" src="<?= $_SESSION[linkroot].$_SESSION[comum]?>imgs/buscar4_on.jpg" ></a>
    					</td>
    				</tr>
    				<tr>
    					<td width="2%">&nbsp;</td>
    					<td width="19%">Enfermeiro:</td>
    					<td>
    						<input type="text" name="cdEnfermeiro" value="" size="10" maxlength="10" class="inputForm" readonly>
    						<input type="text" name="nmEnfermeiro" value="" size="50" maxlength="50" class="inputForm" readonly>
    						<a href="#" title="Pesquisar Municipio/Unidade de saude"><img border="0" src="<?= $_SESSION[linkroot].$_SESSION[comum]?>imgs/buscar4_on.jpg" ></a>
    					</td>
    				</tr>
    				<tr>
    					<td width="2%">&nbsp;</td>
    					<td width="19%">Outros Profissionais:</td>
    					<td>
    						<input type="text" name="cdProfissionalFora" value="" size="10" maxlength="10" class="inputForm">
    						<input type="text" name="nmProfissionalFora" value="" class="inputForm">
                            <img border="0" src="<?= $_SESSION[linkroot].$_SESSION[comum]?>imgs/buscar4_on.jpg" ></a>
    					</td>
    				</tr>
    				<tr>
    					<td width="2%">&nbsp;</td>
    					<td width="19%">Nome Investigador:</td>
    					<td>
    						<input type="text" name="nmInvestigador" value="" size="35" maxlength="50" class="inputForm">
    						&nbsp;
    						Fun&ccedil;&atilde;o:
    						<input type="text" name="funcInvestigador" value="" size="35" maxlength="50" class="inputForm">
    					</td>
    				</tr>
    			</table>
    		</td>
    	</tr>
    </table>

    <table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
    	<tr>
    		<td>
    			<table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
    				<tr>
    					<td width="2%">&nbsp;</td>
    					<td width="19%">N&ordm; les&ocirc;es cut&acirc;neas:</td>
    					<td>
                <input type="text" name="numLesoesCutaneas" value="" size="10" maxlength="10" class="inputForm">
    						&nbsp;
    						Nº de troncos nervosos acometidos:
                <input type="text" name="numTroncosAcometidos" value="" size="10" maxlength="10" class="inputForm">
    					</td>
    				</tr>
    				<tr>
    					<td width="2%">&nbsp;</td>
    					<td width="19%">Forma Cl&iacute;nica:</td>
    					<td>
    						<select name="formaClinica" class="inputForm">
    							<option value="0">[ Selecione ]</option>
    							<option value="1">1-I</option>
    							<option value="2">2-T</option>
    							<option value="3">3-D</option>
    							<option value="4">4-V</option>
    							<option value="5">5-N/C</option>
    						</select>
    					</td>
    				</tr>
    				<tr>
    					<td width="2%">&nbsp;</td>
    					<td width="19%">Avalia&ccedil;&atilde;o da Incapacidade no Diagn&oacute;stico:</td>
    					<td>
    						<select name="avIncapacidadeDiagnostico" class="inputForm">
    							<option value="0">[ Selecione ]</option>
    							<option value="1">1-Grau Zero</option>
    							<option value="2">2-Grau I</option>
    							<option value="3">3-Grau II</option>
    							<option value="4">4-Grau III</option>
    							<option value="5">5-N&atilde;o Avaliado</option>
    							<option value="9">9-Ignorado</option>
    						</select>
    						&nbsp;
    						Classifica&ccedil;&atilde;o Operacional:
    						&nbsp;
    						<select name="classificacaoOperacional" class="inputForm">
    							<option value="0">[ Selecione ]</option>
    							<option value="1">1-Paucibacilar</option>
    							<option value="2">2-Multibacilar</option>
    							<option value="9">9-Ignorado</option>
    						</select>
    					</td>
    				</tr>
    			</table>
    		</td>
    	</tr>
    </table>

    <table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
    	<tr>
    		<td>
    			<table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
    				<tr>
    					<td width="2%">&nbsp;</td>
    					<td width="19%">Modo de entrada:</td>
    					<td>
    						<select name="mdEntrada" class="inputForm">
    							<option value="0">[ Selecione ]</option>
    							<option value="1">1-Caso Novo</option>
    							<option value="2">2-Transferencia do mesmo Municipio</option>
    							<option value="3">3-Transferencia de outro municipio (mesmo UF)</option>
    							<option value="4">4-Transferencia de outro estado</option>
    							<option value="5">5-Transferencia de outro pais</option>
    							<option value="6">6-Recidiva</option>
    							<option value="7">7-Outros reingressos</option>
    							<option value="9">9-Ignorado</option>
    						</select>
    					</td>
    				</tr>
    				<tr>
    					<td width="2%">&nbsp;</td>
    					<td width="19%">Modo de detec&ccedil;&atilde;o caso novo:</td>
    					<td>
    						<select name="mdDetecacaoCasoNovo" class="inputForm">
    							<option value="0">[ Selecione ]</option>
    							<option value="1">1-Encaminhamento</option>
    							<option value="2">2-Demanda espontanea</option>
    							<option value="3">3-Exame de coletividade</option>
    							<option value="4">4-Exame de contatos</option>
    							<option value="5">5-Outros modos</option>
    							<option value="9">9-Ignorado</option>
    						</select>
    					</td>
    				</tr>
    			</table>
    		</td>
    	</tr>
    </table>


	<table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
    	<tr>
    		<td>
    			<table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
    				<tr>
    					<td width="2%">&nbsp;</td>
    					<td width="19%">Baciloscopia:</td>
    					<td>
    						<select name="baciloscopia" class="inputForm">
    							<option value="0">[ Selecione ]</option>
    							<option value="1">1-Positiva</option>
    							<option value="2">2-Negativa</option>
    							<option value="3">3-Não Realizada</option>
    							<option value="9">9-Ignorado</option>
    						</select>
    					</td>
    				</tr>
    			</table>
    		</td>
    	</tr>
    </table>

    <table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
    	<tr>
    		<td>
    			<table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
    				<tr>
    					<td width="2%">&nbsp;</td>
    					<td width="19%">Data inicio tratamento:</td>
    					<td>
			                <input type="text" name="dataInicioTratamento" value="" size="10" maxlength="10" class="inputForm"  />
							<a href="#"><img src="<?= $_SESSION[linkroot].$_SESSION[comum]?>imgs/calendario_on.jpg" width="34" height="21" border="no" title='Calend&aacute;rio' align="absmiddle"></a>	    	        											                
    						&nbsp;
    						Esquema Terapeutico Inicial:
    						&nbsp;
    						
    					</td>
    				</tr>
    			</table>
    		</td>
    	</tr>
    </table>

    <table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
    	<tr>
    		<td>
    			<table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
    				<tr>
    					<td width="2%">&nbsp;</td>
    					<td width="19%">Numero contatos registrados</td>
    					<td>
                <input type="text" name="numeroContatosReg" value="" size="10" maxlength="10" class="inputForm" >
    						&nbsp;
    						Doen&ccedil;a relacionada ao trabalho:
    						&nbsp;
    						<select name="doencaRelacionada" class="inputForm">
    							<option value="0">[ Selecione ]</option>
    							<option value="1">1-Sim</option>
    							<option value="2">2-Não</option>
    							<option value="3">9-Ignorado</option>
    						</select>
    					</td>
    				</tr>
    			</table>
    		</td>
    	</tr>
    </table>

    <table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
    	<tr>
    		<td>
    			<table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
    				<tr valign="top">
    					<td width="2%">&nbsp;</td>
    					<td width="19%">OBS:</td>
    					<td>
    						<textarea cols="50" rows="2" name="obs" class=""></textarea>
    					</td>
    				</tr>
    			</table>
    		</td>
    	</tr>
    </table>

    <table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
    	<tr>
    		<td>
    			<center>
    			<table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
    				<tr valign="top">
    					<td>
    					<br>
    					<center>
    						<img src="<?= $_SESSION[linkroot].$_SESSION[comum]?>imgs/adicionar_on.jpg" />
							<img src="<?= $_SESSION[linkroot].$_SESSION[comum]?>imgs/salvar_on.jpg" />
							<img src="<?= $_SESSION[linkroot].$_SESSION[comum]?>imgs/apagar_on.jpg" />
    					</center>
    					<br>
    					</td>
    				</tr>
    			</table>
    			</center>
    		</td>
    	</tr>
    </table>
