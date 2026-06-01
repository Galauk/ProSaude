<?
echo "
<table border='0' cellpadding=0 cellspacing=0 width='100%'>
  <tr>
    <td> 
       <b> Dt. Vincula&ccedil;&atilde;o <br>
		<input  name='dataVinculacao' type='text' size='10' maxlength='15' value='".date('d/m/Y')."' class='inputForm' readonly>&nbsp;
    </td>
	<td colspan='3'> <b>Vacina Anti-Tet&acirc;nica<br>
        <select name='antitetanica' class='inputForm'>
			<option value='I'>--Selecione--</option>
			<option value='A'>1&ordf; Dose</option>
			<option value='B'>2&ordf; Dose</option>
			<option value='C'>Refor&ccedil;o</option>
			<option value='D'>Imune</option>
		</select>
	</td>
  </tr>
  <tr>
	<td class='txFieldDestaqueMenor_Hiperdia' colspan='4'><b> Exames</b>
	<br/>
		<input name='abo_rh' type='checkbox' value='N'> ABO-Rh
		<input name='vdrl' type='checkbox' value='N'> VDRL
		<input name='urina' type='checkbox' value='N'> Urina
		<input name='glicemia' type='checkbox' value='N'> Glicemia
		<input name='hb_ht' type='checkbox' value='N'> Hb/Ht
		<input name='toxoplasmose' type='checkbox' value='N'> Toxoplasmose
		<input name='hiv' type='checkbox' value='N'> HIV
	 </td>
  </tr>
  <tr>
     <td><b> N. de Gesta&ccedil;&otilde;es<br>
        <input name='numGestacoes'  type='text' value='' size='10' maxlength='2' class='inputForm'>
     </td>
     <td> <b>Parto Vaginal <br>
        <input name='numPartoVaginal'  type='text' value='' size='10' maxlength='2' class='inputForm'>
     </td>
     <td> <b>Cesareas <br>
        <input name='numCesareas'  type='text' value='' size='10' maxlength='2' class='inputForm'>
     </td>
     <td width='25%'><b> Abortos <br>
        <input name='numAbortos'  type='text' value='' size='10' maxlength='2' class='inputForm'>
     </td>
   </tr>
   <tr>
     <td> <b>Filhos Vivos<br>
        <input name='numFilhosVivos'  type='text' value='' size='10' maxlength='2' class='inputForm'>
     </td>
     <td> <b>RN (-2.500g)<br>
        <input name='numRn2'  type='text' value='' size='10' maxlength='2' class='inputForm'>
     </td>
     <td><b> RN (+4.000g)<br>
        <input name='numRn4'  type='text' value='' size='10' maxlength='2' class='inputForm'>
     </td>
     <td>&nbsp; </td>
   </tr>
   <tr>
     <td><b> Peso Anterior<br>
        <input name='pesoAnterior'  type='text' value='' size='10' maxlength='6' class='inputForm'>
     </td>
     <td> <b>Tabagismo<br>
        <select name='tabagismo' class='inputForm'>
            <option value='I'>--Selecione--</option>
            <option value='S'>Sim</option>
            <option value='N'>Nao</option>
        </select>
      </td>
      <td> <b>Estatura <br>
        <input name='estatura'  type='text' value='' size='10' maxlength='4' class='inputForm' >
      </td>
      <td> <b>N&ordm; Cigarros/Dia <br>
        <input name='numCigarros'  type='text' value='' size='10' maxlength='2' class='inputForm' >
      </td>
      </tr>
    </table>";
?>