<?php
	session_start(); 
?>
<script>
function buscar_enfermeiro(valor,acao)
{
	url = "../busca_enfermeiros.php?palavra="+valor+"&acao="+acao;
	ajax_tudo(url, popular_enfermeiro);
	validaBusca('lista_enfermeiro').style.display = '';
	validaBusca('table_enfermeiro').innerHTML = '';
	validaBusca('lista_enfermeiro_carregando').style.display = '';
}
function popular_enfermeiro(txt)
{
	try {
			t = validaBusca('table_enfermeiro');
			validaBusca("lista_enfermeiro_carregando").style.display = 'none';
			t.innerHTML = txt;
	} catch(e) {
			alert(e);
	}
}
function passar_enfermeiro(enfermeiro,enf_codigo)
{
	validaBusca("enfermeiro_nome").value = enfermeiro;
	validaBusca("enf_codigo").value = enf_codigo;
	validaBusca('lista_enfermeiro').style.display = 'none';
	validaBusca('enfermeiro_nome').focus();
}
</script>
<?
echo"   
         <table border='0' cellpadding=0 cellspacing=0 width='100%' class='b5'>
			</td>
                <!-- Codigo e nome do Medico -->
                <tr>";
					echo"	
					<td width='33%'> <b>M&eacute;dico Respons&aacute;vel <br>
						<input type=text readonly='readonly' name='med_codigo' id='med_codigo' class=inputForm size=10 value=''>
						<input name='med_nome' id='med_nome'  type='text' value='' size='50' class='inputForm'>
						<a href='#' onclick=\"buscar_medico(\$F('med_nome'), 'buscar_medico');return false;\"'><img border='0' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/buscar4_on.jpg' style='vertical-align:bottom;'></a>";
					echo divBuscaMedico();
				echo"</td>		
				</tr>	
				<tr>";
					echo "
					<td width='33%'><b> Enfermeiro Respons&aacute;vel <br>
					
						<input type=text readonly='readonly' name='enf_codigo' id='enf_codigo' value='' class=inputForm size=10 >
						<input name='enfermeiro_nome' id='enfermeiro_nome'  type='text' value='' size='50' class='inputForm'>
						<a href='#' onclick=\"buscar_enfermeiro(\$F('enfermeiro_nome'), 'buscar_enfermeiro');return false;\"'><img border='0' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/buscar4_on.jpg' style='vertical-align:bottom;'></a>";
					echo divBuscaEnfermeiro();
					echo"
					</td>
                </tr>
 

                <tr>
                  <td width='10%'><b>Nome Investigador<br/>

                    <input type='text' name='nmInvestigador' value='' class='inputForm' size='70' maxlength='70'>
				  </td>

                </tr>
             	<tr>
                  <td width='10%'><b>Data Cadastro<br/>
                     <input type='text' class='inputForm' name='data' id='data' onkeyup='mascaraData(this)' maxlength='10'>
                  </td>
                </tr>
     		</table>
			<table border='0' cellpadding=0 cellspacing=0 width='100%' class='b5'>
					<tr>
					  <td width='20%'><b>Tratamento Supervisionado</td>
					  <td width='5%'>
						<select name='tratSupervisionado' class='inputForm'>
						  <option value='N' selected>N&atilde;o</option>
						  <option value='S'>Sim</option>
						</select>
					  </td>
					  <td ><b>Drogas&nbsp;&nbsp;
						<select name='usaDrogas' class='inputForm'>
						  <option value='N' selected>N&atilde;o</option>
						  <option value='S'>Sim</option>
						</select>
					  </td>
					 </tr>
			</table>
			<table border='0' cellpadding=0 cellspacing=0 width='100%' class='b5'>
				<tr>
				  <td>
					<div id='drogasH' style='display: ;'>
						<table border='0' cellpadding=0 cellspacing=0 width='50%'>
						  <tr>
							<td bgcolor='#D3E8FE'><b>DROGAS</td>
						  </tr>
						  <tr>
							<td><input name='drogas[]' type='checkbox' value='rifampicina' > Rifampicina &nbsp; </input></td>
						  </tr>
						  <tr>
							<td><input name='drogas[]' type='checkbox' id='checkbox' value='etambutol'> Etambutol &nbsp;</input></td>
						  </tr>
						  <tr>
							<td><input name='drogas[]' type='checkbox' id='checkbox' value='isoniazida'> Isoniazida &nbsp;</input></td>
						  </tr>
						  <tr>
							<td><input name='drogas[]' type='checkbox' id='checkbox' value='estreptomicina'> Estreptomicina &nbsp;</input></td>
						  </tr>
						  <tr>
							<td><input name='drogas[]' type='checkbox' id='checkbox' value='pirazinamida'> Pirazinamida &nbsp;</input></td>
						  </tr>
						  <tr>
							<td><input name='drogas[]' type='checkbox' id='checkbox' value='etionamida'> Etionamida &nbsp;</input></td>
						  </tr>
						  <tr>
							<td><input name='drogas[]' type='checkbox' id='checkbox' value='outros' ><b> outros</input></td>
						  </tr>
						<tr>
						  <td bgcolor='#D3E8FE' colspan='2'><b>ESQUEMA</td>
						</tr>
			
						<tr>
						  <td>
							<select name='esquema' class='inputForm'>
							  <option value='-1'>-- ESCOLHA UM ESQUEMA--</option>
							  <option value='1'>1. ESQUEMA I</option>
							  <option value='2'>2. ESQUEMA II</option>
							  <option value='3'>3. ESQUEMA III</option>
							  <option value='4'>4. ESQUEMA IV</option>
							</select>
						  </td>
						</tr>
						<tr>
						  <td bgcolor='#D3E8FE' colspan='2'><b>RETORNO</td>
						</tr>
			
						<tr>
						  <td>
							<select name='retorno' class='inputForm'>
							  <option value='SEM RETORNO' selected>SEM RETORNO</option>
							  <option value='1 MES'>1 MES</option>
							  <option value='2 MESES'>2 MESES</option>
							  <option value='3 MESES'>3 MESES</option>
							  <option value='4 MESES'>4 MESES</option>
							  <option value='5 MESES'>5 MESES</option>
							  <option value='6 MESES'>6 MESES</option>
							  <option value='7 MESES'>7 MESES</option>
							  <option value='8 MESES'>8 MESES</option>
							  <option value='9 MESES'>9 MESES</option>
							  <option value='10 MESES'>10 MESES</option>
							  <option value='11 MESES'>11 MESES</option>
							  <option value='12 MESES'>12 MESES</option>
							</select>
						  </td>
						</tr>
						</div>
							<div id='drogasD' style='display:'>
						</div>
					  </td>
					</tr>
				  </table>
			<tr>
				<td>
					<b>Outros:<br/>
					<textarea name='outrasDrogas' rows='6' cols='110' class='txArea'></textarea>
				</td>
			</tr>
			<tr>
				<td>
					<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/salvar_on.jpg'>
				</td>
			</tr>
		 </table>";
 ?>