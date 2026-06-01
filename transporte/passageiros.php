<?php
	session_start(); 
?>
<table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
      <tr>         
      <td> C&oacute;digo do Cidad&atilde;o: </td>
        <td><input name="cdCidadao"  type="text"  size="8" maxlength="8" class="txReadOnly" readonly >
		<input name="nmCidadao"  type="text"  size="40" maxlength="50" class="txReadOnly" readonly >		

		  <a href="" title="Pesquisa de Cidadaos"><img border="0" src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/buscar4_on.jpg" ></a>

        </td>
      </tr>
	  <tr>
		<td>
			Especialidade:
		</td>
		<td>
			<input type="text" name="nmEspecialidade" size="50"  maxlength="50" class="txMaiuscula" > 
		</td>
	  </tr>
	  <tr>
		<td>
			Custo do paciente (exames, etc):
		</td>
		<td>
			<input type="text" name="custoCidadao" size="50"  maxlength="50" class="txMaiuscula" > 
		</td>
	  </tr>
	  <tr>
	  	<td colspan="2" align="center">
	  		<br/>
	  		<div id="listOfPassageiros" style="overflow: auto; height: 200px; width: 560px; border-style: solid; border-width: 1px; scrollbar-face-color: #FFFFFF; scrollbar-track-color: #D0D0D0; scrollbar-arrow-color: #000080; background-color:#FFFFFF"></div>
			<br/>
		</td>
	  </tr>
</table>
