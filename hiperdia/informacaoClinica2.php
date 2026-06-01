<?php
	session_start(); 
?>
<html>
<head>
<link href="<?= $_SESSION[linkroot].$_SESSION[modulo]; ?>css/estiloForm.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?
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
			<select name='hipermed_medicamentoso' class='inputForm'>
				<option value='N' selected>Nao</option>
				<option value='S'>Sim</option>
			</select>
			<br>
			<table border='0' cellpadding=5 cellspacing=1 width='100%' >
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
					<td width='80%' bgcolor='#FFFFFF' class='inputForm'>
						GLIBENCLAMIDA 5 MG
					</td>
					<td width='20%' >
					   <select name='medicamento[]' class='inputForm'>
					  <option>  </option>
					   <option value='meio/296'>Meio</option>
					   <option value='um/296'>Um</option>
					   <option value='dois/296'>Um e meio</option>
					   </select>			  
					</td>
				</tr>
				<tr> 
					<td width='80%' class='inputForm'> 
						METILDOPA 500MG
					</td>
					<td width='20%'>
					   <select name='medicamento[]' class='inputForm'>
					 	<option>  </option>
					   <option value='meio/323'>Meio</option>
					   <option value='um/323'>Um</option>
					   <option value='dois/323'>Dois</option>
					   <option value='tres/323'>Tres</option>
					   <option value='quatro/323'>Quatro</option>
					   <option value='cinco/323'>Cinco</option>
					   <option value='seis/323'>Seis</option>
					   </select>			  
					</td>
				</tr>
				<tr> 
					<td width='80%' class='inputForm'>
						HIDROCLOROTIAZIDA 25MG
					</td>
					<td width='20%'>
					   <select name='medicamento[]'class='inputForm' >
					  <option>  </option>
					   <option value='meio/1203'>Meio</option>
					   <option value='um/1203'>Um</option>
					   <option value='dois/1203'>Dois</option>
					   <option value='tres/1203'>Tres</option>
					   <option value='quatro/1203'>Quatro</option>
					   <option value='cinco/1203'>Cinco</option>
					   <option value='seis/1203'>Seis</option>
					   </select>			  
					</td>
				</tr>
				<tr> 
					<td width='80%' class='inputForm'>
						METFORMINA 850MG
					</td>
					<td width='20%' >
					   <select name='medicamento[]' class='inputForm'>
					   <option>  </option>
					   <option value='meio/1262'>Meio</option>
					   <option value='um/1262'>Um</option>
					   <option value='dois/1262'>Dois</option>
					   <option value='tres/1262'>Tres</option>
					   <option value='quatro/1262'>Quatro</option>
					   </select>			  
					</td>
				</tr>
				<tr> 
					<td width='80%'  class='inputForm'>
						ENALAPRIL 10MG 
					</td>
					<td width='20%' >
					   <select name='medicamento[]' class='inputForm'>
					   <option>  </option>
					   <option value='meio/3273'>Meio</option>
					   <option value='um/3273'>Um</option>
					   <option value='dois/3273'>Dois</option>
					   <option value='tres/3273'>Tres</option>
					   <option value='quatro/3273'>Quatro</option>
					   <option value='cinco/3273'>Cinco</option>
					   </select>			  
					</td>
				</tr>
				<tr> 
					<td width='80%' bgcolor='#FFFFFF' class='inputForm'>
						CAPTOPRIL 25 MG
					</td>
					<td width='20%'>
					   <select name='medicamento[]' class='inputForm'>
					   <option>  </option>
					   <option value='meio/327'>Meio</option>
					   <option value='um/327'>Um</option>
					   <option value='dois/327'>Dois</option>
					   <option value='tres/327'>Tres</option>
					   <option value='quatro/327'>Quatro</option>
					   <option value='cinco/327'>Cinco</option>
   					   <option value='seis/327'>Seis</option>
					   </select>			  
					</td>
				</tr>
								<tr> 
					<td width='80%' bgcolor='#FFFFFF' class='inputForm'>
						PROPANOLOL 40MG
					</td>
					<td width='20%'>
					   <select name='medicamento[]' class='inputForm'>
					   <option>  </option>
					   <option value='meio/3730'>Meio</option>
					   <option value='um/3730'>Um</option>
					   <option value='dois/3730'>Dois</option>
					   <option value='tres/3730'>Tres</option>
					   <option value='quatro/3730'>Quatro</option>
					   <option value='cinco/3730'>Cinco</option>
					   <option value='seis/3730'>Seis</option>
					   </select>			  
					</td>
				</tr>				
			</table>
			<table border='0' cellpadding=0 cellspacing=0 width='100%'>
				<tr> 
					<td colspan='2'>&nbsp;  </td>
				</tr>
				<tr> 
					<td >  <b>Insulina: Unidades/dia </td>
					<td><input name='insulina'  type='text' value='' size='10' maxlength='6' class='inputForm'> </td>	  
				</tr>
				<tr>
					<td> <b>Outros Medicamentos? </td>
					<td>
						<select name='outrosMedi' class='inputForm'>
							<option value='N' selected> <b>Nao</option>
							<option value='S'> <b>Sim</option>
						</select>
					</td>
				</tr>
			</table>
			<br>
			<table border='0' cellpadding=0 cellspacing=0 width='100%'>
				<tr> 
					<td	bgcolor='#E1F5FF'> EXAMES </td>
				</tr>
				<tr>
					<td>
						<input name='examesCheck[]' type='checkbox' id='checkbox' value='3229'> <b> HB Glicosilada
						<input name='examesCheck[]' type='checkbox' id='checkbox' value='20134'> <b> Creatinina Serica
						<input name='examesCheck[]' type='checkbox' id='checkbox' value='20132'> <b> Colesterol Total
						<input name='examesCheck[]' type='checkbox' id='checkbox' value='20809'> <b> ECG
					</td>
				</tr>
				<tr>
					<td>
						<input name='examesCheck[]' type='checkbox' id='checkbox' value='1789'> <b> Triglic&eacute;rides
						<input name='examesCheck[]' type='checkbox' id='checkbox' value='16938'> <b> Parcial de Ur&iacute;na
						<input name='examesCheck[]' type='checkbox' id='checkbox' value='20375'> <b> Micro Albumin&uacute;ria
					</td>
				</tr>
			</table>
		</td>
		<td width='2%'>&nbsp;
			
		</td>
		<td width='49%' valign='top'>
			<table border='0' cellpadding=0 cellspacing=0 width='100%'>
  			  <tr> 
				 <td>  <b>Nomes de outros medicamentos <br>
					  <textarea name='relacaoOutros' rows='7' cols='65' class='txArea' ></textarea>
				 </td>	  
			  </tr>
  			  <tr> 
				 <td> <b> Les&otilde;es de &oacute;rg&atilde;o-alvo <br>
					  <textarea name='lesoes' rows='7' cols='65' class='txArea'></textarea>
				 </td>	  
			  </tr>
			</table>
		</td>
	</tr>
</table>
<input type='hidden' name='pac_codigo' id='pac_codigo' value='$usu_codigo'>";


?>
<center>
<br>
    <input type="image" src="<?= $_SESSION[linkroot].$_SESSION[comum]; ?>imgs/finalizar_on.jpg">
</center>
</body>
</html>