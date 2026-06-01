<?php
	session_start(); 
?>
<link href="<?= $_SESSION[linkroot].$_SESSION[modulo];?>css/estiloForm.css" rel="stylesheet" type="text/css" />
	<center>	
	  <table width="90%" height="23">
	    <tr class='Titulo'>
			<td><font size="+1"><b> Consulta do Cadastro de Hipertensos e Diab&eacute;ticos</font></td>
		</tr>				
	</table>
	</center>
	  
	<center>
	  <table width="90%">
		<tr class='Titulo'>
		  <td align="center">&nbsp; </td>
		</tr>
	  </table>
	</center>
	  
	<center>	 
		<table border="0" width="90%">
			<tr class='subTitulo'> 
				<td width="100%" colspan="2"> Pesquisar por...</td>
			</tr>
			<tr>
				<td width="5%" align="center" > <input type="radio" name="idTipoPesquisaHiperdia" value="1" class="inputForm"> </td>
				<td width="80%">
					<table border="0" width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td width="20%">Cod. do Cidad&atilde;o:</td>
							<td width="80%" ><input name="cdCidadaoHiperdia" type="text" size="8" maxlength="8" class="inputForm"></td>
						</tr>
					</table>
			</tr>
			<tr>
				<td width="5%" align="center"> <input type="radio" name="idTipoPesquisaHiperdia" value="3" > </td>
				<td width="80%">
					<table border="0" width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td width="20%">N&ordm; CNS:</td>
							<td width="80%" ><input name="nrCnsHiperdia" type="text" size="15" maxlength="15"  value="" class="inputForm" ></td>
						</tr>
					</table>
			</tr>
			<tr> 
				<td width="5%" align="center" > <input type="radio" name="idTipoPesquisaHiperdia" value="2" > </td>
				<td width="80%">
					<table border="0" width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td width="20%">Nome do Cidad&atilde;o:</td>
							<td width="80%" ><input name="nmCidadaoHiperdia" type="text" value="" size="30" maxlength="40" class="inputForm" ></td>
						</tr>
						<tr>
							<td width="20%">Nome da m&atilde;e:</td>
							<td width="80%" ><input name="nmMaeHiperdia" type="text" value="" size="30" maxlength="40" class="inputForm"></td>
						</tr>
						<tr>
							<td width="20%">Data Nasc.:</td>
							<td width="80%" ><input name="dataNascHiperdia" type="text" id="dataNasc" size="10" maxlength="10" value="" class="inputForm">
							<img src="<?= $_SESSION[linkroot].$_SESSION[comum]; ?>imgs/calendario_on.jpg" width="34" height="21" border="no" title='Calendario'></a>
							</td>
						</tr>
					</table>
				</td>
			</tr>
	      <tr> 
	        <td colspan="2" align="center">
	           <img src="<?= $_SESSION[linkroot].$_SESSION[comum]; ?>imgs/buscar_on.jpg">
	            <img src="<?= $_SESSION[linkroot].$_SESSION[comum]; ?>imgs/limpar_on.jpg">
	            <img src="<?= $_SESSION[linkroot].$_SESSION[comum]; ?>imgs/finalizar_on.jpg" />
	        </td>
	      </tr>
	    </table>
		</center>	
		
		<br>   
	<center>
		<div id="resultBuscaHiperdia" style="overflow: auto; height: 120px; width:580px; border-style: solid; border-width: 1px; scrollbar-face-color: #FFFFFF; scrollbar-track-color: #D0D0D0; scrollbar-arrow-color: #000080; background-color:#FFFFFF"></div>
	</center>	

