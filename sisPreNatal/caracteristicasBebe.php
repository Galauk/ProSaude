<table border="0" cellpadding=0 cellspacing=0 width="100%">
  <tr>
	<td colspan=2 class="txFieldDestaqueMenor_Hiperdia">CARACTER&Iacute;STICAS DO BEB&Ecirc; </td>
  </tr>
  <tr>
    <td> Dt. Cadastro <br>
		<input name="dataBebe" type="text" size="10" maxlength="10"  value='' class="inputForm">&nbsp;
		<a href="javascript:void(0);">
			<img src="../imgs/calendario_on.jpg" border="no" title='Calend&aacute;rio' style="vertical-align:bottom">
		</a>
    </td>
    <td> Dt. Nascimento <br>
		<input name="dataNascBebe" type="text" size="10" maxlength="10"  value='' class="inputForm">&nbsp;
		<a href="javascript:void(0);">
			<img src="../imgs/calendario_on.jpg" border="no" title='Calend&aacute;rio' style="vertical-align:bottom"> 
		</a>
    </td>
</tr>
</table>

<table border="0" cellpadding=0 cellspacing=0 width="100%">
	<tr>
 		   <td class="txFieldDestaqueMenor_Hiperdia" colspan="5">
				CARACTER&Iacute;STICAS DO BEB&Ecirc;
		   </td>
	</tr>
	<tr>
   <td width="15%" > Recem-Nato  <br>
	<select name="recemNasc" class="inputForm">
		<option value="F" selected="selected">Selecione</option>
		<option value="S">Sim</option>
		<option value="N">Nao</option>
	</select>
    </td>
	<td width="15%"> Sexo  <br>
	<select name="sexo" class="inputForm">
		<option value="S" selected="selected">Selecione</option>
		<option value="M">Masculino</option>
		<option value="F">Feminino</option>
	</select>
    </td>
	<td width="15%"> Peso Beb&ecirc; (kg) <br>
		<input name="pesoBebe" type="text" size="10" maxlength="7" value='' class="inputForm">
    </td>
	<td width="15%"> Tamanho (cm) <br>
		<input name="tamanho" type="text" size="10" maxlength="6" value='' class="inputForm">
    </td>
	<td width="15%"> Apgar 1&ordm; minuto<br>
		<input name="apgar1" type="text" size="10" maxlength="6" value='' class="inputForm">
    </td>
  </tr>
  <tr>
	<td>  Ra&ccedil;a / Cor  <br>
		  <select name="racaCor" class="inputForm">
               <option value="F" selected="selected">Selecione</option>
               <option value="B">Branco</option>
               <option value="N">Negro</option>
               <option value="M">Mulato</option>
               <option value="C">Cafuso</option>
          </select>
	</td>

	<td>   Nativivo/Natimorto  <br>
		  <select name="nativivo" class="inputForm">
               <option value="F" selected="selected">Selecione</option>
               <option value="V">Nativivo</option>
               <option value="M">Natimorto</option>
          </select>
	</td>
	<td width="15%"> Apgar 5&ordm; minuto<br>
		<input name="apgar5" type="text" size="10" maxlength="6" value='' class="inputForm">
    </td>
	<td width="15%"colspan = "2"> Parkim <br>
		<input name="parkim" type="text" size="10" maxlength="6" value='' class="inputForm">
    </td>
  </tr>
</table>
<table border="0" cellpadding=0 cellspacing=0 width="100%">
	<tr>
    <td >Implica&ccedil;&otilde;es e problemas espec&iacute;ficos
		<br> <textarea name="impliBebe" rows="2" cols="100" class="textArea" ></textarea>
		<input name="btnPesquisar" type="button" style="border: 1px solid #808080; background-color: #FFFFFF; width:110; height:40" title="Pesquisar" value='Pesquisar CID'>
	</td>
  </tr>
  <tr>
    <td >Obs. e recomenda&ccedil;&otilde;es
		<br> <textarea name="obserBebe" rows="2" cols="100" class="textArea"></textarea>
	</td>
  </tr>
</table>
<center>
	<div id="tabelaBebe" style="margin-top:20px; overflow: auto; height: 110px; width:580px; border-style: solid; border-width: 1px; scrollbar-face-color: #FFFFFF; scrollbar-track-color: #D0D0D0; scrollbar-arrow-color: #000080; background-color:#FFFFFF"></div>
</center>