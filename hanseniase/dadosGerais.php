<?php 
	session_start();
?>
     <table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
    	<tr>
    		<td>
    			<table border="0" cellpadding=1 cellspacing=4 width="100%" class="b5">
    				<tr>
    					<td width="2%">&nbsp;</td>
    					<td width="19%"><b>Código do Controle</td>
    					<td>
    						<input type="text" name="idHanseniase" value="" size="10" maxlength="10" class="inputForm" readonly>
    						&nbsp;&nbsp;
    						Data de Notifica&ccedil;&atilde;o&nbsp;
    						<input type="text" name="dataNotificacao" value="" size="10" maxlength="10" class="inputForm" readonly>
    					</td>
    				</tr>
    				<tr>
    					<td width="2%">&nbsp;</td>
    					<td width="19%"><b>Municipio de Notifica&ccedil;&atilde;o</td>
    					<td>
    						<input type="text" name="cdIbgeNotificacao" value="" size="10" maxlength="10" class="inputForm" readonly>
    						<input type="text" name="nmMunicipioNotificacao" value="" size="50" maxlength="50" class="inputForm" readonly>
    					</td>
    				</tr>

 				   <tr>
    					<td width="2%">&nbsp;</td>
    					<td width="19%"><b>Unidade de Sa&uacute;de:</td>
    					<td>
    						<input type="text" name="cdUnidadeSaude" value="" size="10" maxlength="10" class="inputForm" readonly>
    						<input type="text" name="nmUnidadeSaude" value="" size="50" maxlength="50" class="inputForm" readonly>
    					</td>
    				</tr>
    			</table>
    		</td>
    	</tr>
    </table>

	<table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
    	<tr>
    		<td>

          <table border="0" cellpadding=1 cellspacing=4 width="100%" class="b5">
            <tr>
              <td width="2%">&nbsp;</td>
              <td width="19%"><b>Numero SINAN</td>
              <td valign="bottom">
                <input type="text" name="cdSinan" value="" class="inputForm" size="15" maxlength="15" readonly>
                <a href="javaScript:showFindCdSinan(  )" title="Pesquisar Paciente"><img border="0" src="<?= $_SESSION[linkroot].$_SESSION[comum]; ?>imgs/buscar4_on.jpg" ></a>
              </td>
            </tr>
          </table>

    			<table border="0" cellpadding=1 cellspacing=4 width="100%" class="b5">
    				<tr>
    					<td width="2%">&nbsp;</td>
    					<td width="19%"><b>Paciente</td>
    					<td>
    						<input type="text" name="cdPaciente" value="" class="inputForm" size="10" maxlength="10" readonly>
    						&nbsp;
    						<input type="text" name="nmPaciente" value="" class="inputForm" size="50" maxlength="50" readonly>

    					</td>
    				</tr>
    				<tr>
    					<td width="2%">&nbsp;</td>
    					<td width="19%"><b>Data Nascimento:</td>
    					<td>
    						<input type="text" name="dataNascPaciente" value="" class="inputForm" size="10" maxlength="10" readonly>
    						&nbsp;
    						Idade:
    						&nbsp;
    						<input type="text" name="idadePaciente" value="" class="inputForm" size="3" maxlength="3" readonly>
    					</td>
    				</tr>
    				<tr>
    					<td width="2%">&nbsp;</td>
    					<td width="19%"><b>Cart&atilde;o SUS:</td>
    					<td>
    						<input type="text" name="cartaoSus" value="" class="inputForm" size="15" maxlength="15" readonly>
    					</td>
    				</tr>
    				<tr>
    					<td width="2%">&nbsp;</td>
    					<td width="19%"><b>Nome da m&atilde;e:</td>
    					<td>
    						<input type="text" name="nmMae" value="" class="inputForm" size="50" maxlength="50" readonly>
    					</td>
    				</tr>
    				<tr>
    					<td width="2%">&nbsp;</td>
    					<td width="19%"><b>Ocupação:</td>
    					<td>
    						<input type="text" name="ramoAtividade" value="" size="60" maxlength="60" class="inputForm" readonly>
    					</td>
    				</tr>
    				<tr>
    					<td width="2%">&nbsp;</td>
    					<td width="19%"><b>Situa&ccedil;&atilde;o do Paciente:</td>
    					<td>
    					<select name="statusHanse" class="inputForm">
		                    <option value="I">--&gt; EM TRATAMENTO &lt;--</option>		                    
		                    <option value="C">1. ENCERRADO - CURA</option>
		                    <option value="A">2. ENCERRADO - ABANDONO</option>
		                    <option value="T">3. ENCERRADO - TRANSFER&Ecirc;NCIA</option>
		                    <option value="B">4. ENCERRADO - &Oacute;BITO</option>
		                    <option value="U">5. ENCERRADO - MUDAN&Ccedil;A DE DIAGN&Oacute;STICO</option>
		                    <option value="E">6. ENCERRADO - OUTRAS</option>
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
    			<br>
    			<center>
    			<table border="0" width="85%">
    			<tr>
    				<td>
    				LISTA DE DIAGNOSTICOS:
    				</td>
    			</tr>
    			</table>
				<div id="listaProntuariosHanseniase" style="overflow: auto; height: 120px; width:650px; border-style: solid; border-width: 1px; scrollbar-face-color: #FFFFFF; scrollbar-track-color: #D0D0D0; scrollbar-arrow-color: #000080; background-color: #ffffff" >
					<center>
							
					</center>
				</div>
				</center>
    			<br>
    		</td>
    	<tr>
    </table>