<?php 
	session_start();
?>
    <table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
    	<tr>
    		<td>
    			<center>
    			<table border="0" cellpadding=0 cellspacing=0 width="70%" class="b5">
    				<tr>
    					<td>
    						<br>
    						Pessoas que residem com o Examinado:
    					</td>
    				</tr>
    				<tr>
    					<td bgcolor="#000000"></td>
    				</tr>
    			</table>

    			<br>

    			<table border="0" cellpadding=0 cellspacing=0 width="70%" class="b5">
    				<input type="hidden" name="idRes" value="">
            <input type="hidden" name="residem" value="">
    				<tr>
    					<td width="19%">
    						Nome:
    					</td>
    					<td>
                <input type="text" name="resCdPessoa" value="" size="10" maxlength="10" class="inputForm" readonly>
                &nbsp;&nbsp;&nbsp;&nbsp;
    						<input type="text" name="resNmPessoa" value="" size="50" maxlength="50" class="inputForm" >
                <a href="#" title="Pesquisar Residente"><img border="0" src="<?= $_SESSION[linkroot].$_SESSION[comum] ?>imgs/buscar4_on.jpg" style="vertical-align:bottom" ></a>
    					</td>
    				</tr>
    				<tr>
    					<td width="19%">
    						Parentesco:
    					</td>
    					<td>
    						<input type="text" name="resParentesco" value="" size="20" maxlength="20" class="inputForm" onKeyPress="return validaCampo(event, this, 20, jsValidCharsWithoutNumbers);">
    						&nbsp;
    						Idade:
    						&nbsp;
    						<input type="text" name="resIdade" value="" size="4" maxlength="4" class="inputForm" >
    						&nbsp;
                Resultado:
                &nbsp;
                <select name="resResultado" id="resResultado" class="inputForm">
                  <option value="XX" selected>[-- SELECIONE --]</option>
                  <option value="POSITIVO">POSITIVO</option>
                  <option value="NEGATIVO">NEGATIVO</option>
                </select>
    					</td>
    				</tr>  
  
  					<tr>
	   					<td width="19%">
    					Recebeu vacina BCG:
    					</td>
    					<td>
                

                <select name="cdBcg" id="cdBcg" class="inputForm">
                  <option value="XX" selected> [-- SELECIONE --]</option>
                  <option value="S">SIM</option>
                  <option value="N">N&Atilde;O</option>
                  <option value="I">N&Atilde;O INFORMADO</option>
                </select>
                &nbsp; 
                <span class="titulo" id="spanBCG"></span>
                
    					</td>
    				</tr>

    				<tr>
    					<td colspan="2">
    						<br>
    						<center>
    							<img src="<?= $_SESSION[linkroot].$_SESSION[comum] ?>imgs/adicionar_on.jpg" />
								<img src="<?= $_SESSION[linkroot].$_SESSION[comum] ?>imgs/salvar_on.jpg" />
								<img src="<?= $_SESSION[linkroot].$_SESSION[comum] ?>imgs/apagar_on.jpg" />
    						</center>
    						<br>
    					</td>
    				</tr>

    				<tr>
    					<td colspan="2">
    						<center>
							<div id="listaRes" style="overflow: auto; height: 60px; width:550px; border-style: solid; border-width: 1px; scrollbar-face-color: #FFFFFF; scrollbar-track-color: #D0D0D0; scrollbar-arrow-color: #000080; background-color: #ffffff" >
								<center>
								
								</center>
							</div>
							</center>
    					</td>
    				</tr>
    			</table>

    			<center>
    			<table border="0" cellpadding=0 cellspacing=0 width="70%" class="b5">
    				<tr>
    					<td>
    						<br>
    						Investigação Epidemiológica: Residências Anteriores
    					</td>
    				</tr>
    				<tr>
    					<td bgcolor="#000000"></td>
    				</tr>
    			</table>
    			<br>

				<table border="0" cellpadding=0 cellspacing=0 width="70%" class="b5">
    				<input type="hidden" name="idResAnt" value="">
    				<tr>
    					<td>Do ano
    						<input type="text" name="resAntAno" value="" size="4" maxlength="4" class="inputForm">
    						&nbsp
    						ao ano de
    						&nbsp;
    						<input type="text" name="resAntAnoDe" value="" size="4" maxlength="4" class="inputForm" >
    						&nbsp;
    						em
    						&nbsp;
							<input name="resAntCodIbge" type="hidden" value="">			
					      	<input name="resAntNmMunicipio" size="35" maxlength="50" value="" class="inputForm" readOnly >						
					      	<input name="resAntUfMunicipio" size="2" maxlength="2" value="" class="inputForm" readOnly >											      	
							<a id="wndDomicilio" href="javascript:findCodIbge();" title="Pesquisa de Municipio de Nascimento">
								<img border="0" src="<?= $_SESSION[linkroot].$_SESSION[comum] ?>imgs/buscar4_on.jpg">
							</a>		
    					</td>
    				</tr>
    				<tr>
    					<td>
    						<br>
    						<center>
    							<img src="<?= $_SESSION[linkroot].$_SESSION[comum] ?>imgs/adicionar_on.jpg" />
								<img src="<?= $_SESSION[linkroot].$_SESSION[comum] ?>imgs/salvar_on.jpg" />
								<img src="<?= $_SESSION[linkroot].$_SESSION[comum] ?>imgs/apagar_on.jpg" />
    						</center>
    						<br>
    					</td>
    				</tr>
    				<tr>
    					<td colspan="2">
    						<center>
							<div id="listaResAnt" style="overflow: auto; height: 60px; width:500px; border-style: solid; border-width: 1px; scrollbar-face-color: #FFFFFF; scrollbar-track-color: #D0D0D0; scrollbar-arrow-color: #000080; background-color: #ffffff" >
								<center>
									
								</center>
							</div>
							</center>
							<br>
    					</td>
    				</tr>
    			</table>
    		</td>
    	</tr>
    </table>

