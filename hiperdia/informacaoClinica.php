<?
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."json.inc.php";
?>
<script>
function buscar_medico(valor, acao)
{
	url = "../buscar_medicos.php?palavra="+valor+"&acao="+acao;
	ajax_tudo(url, popular_medico);
	validaBusca('lista_medico').style.display = '';
	validaBusca('table_medico').innerHTML = '';
	validaBusca('lista_medico_carregando').style.display = '';
}
function popular_medico(txt)
{
	try {
			t = validaBusca('table_medico');
			validaBusca("lista_medico_carregando").style.display = 'none';
			t.innerHTML = txt;
	} catch(e) {
			alert(e);
	}
}

function passar_medico(medico,codigo)
{	
	validaBusca("med_nome").value = medico;
	validaBusca("med_codigo").value = codigo;
	validaBusca('lista_medico').style.display = 'none';
	validaBusca('med_nome').focus();
}

</script>
<link href="../css/estiloForm.css" rel="stylesheet" type="text/css" />
	<table border="0" cellpadding=0 cellspacing=0 width="100%">
       <tr> 
 		   <td class="txFieldDestaqueMenor_Hiperdia" colspan="5">
				<b>RESPONS&Aacute;VEIS PELO PACIENTE
		   </td>
	   </tr>
       <tr>
<?   
echo"	
	      <td width='33%'> <b>M&eacute;dico Respons&aacute;vel <br>
		  		<input type=hidden name='med_codigo' id='med_codigo' class=boxl size=10 >
				<input name='med_nome' id='med_nome'  type='text' value='' size='50' class='inputForm'>
				<a href='#' onclick=\"buscar_medico(\$F('med_nome'), 'buscar_medico');return false;\"'><img border='0' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/buscar4_on.jpg' style='vertical-align:bottom;'></a>";
				 echo divBuscaMedico();
		 echo"</td>";	
		 echo "
	      <td width='33%'><b> Enfermeiro Respons&aacute;vel <br>

				<input type=hidden name='enf_codigo' id='enf_codigo' class=boxl size=10 >
				<input name='enfermeiro_nome' id='enfermeiro_nome'  type='text' value='' size='50' class='inputForm'>
				<a href='#' onclick=\"buscar_enfermeiro(\$F('enfermeiro_nome'), 'buscar_enfermeiro');return false;\"'><img border='0' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/buscar4_on.jpg' style='vertical-align:bottom;'></a>";
				echo divBuscaEnfermeiro();	
				echo"
		  </td>
		  <td>
		  	 <b>Data Cadastro:
		  	 <input type='text' class='inputForm' name='data' id='data' onkeyup='mascaraData(this)' maxlength='10'>
		  </td>
	   </tr>
    </table>

	<table border='0' cellpadding=0 cellspacing=0 width='100%'>
       <tr> 
 		   <td class='txFieldDestaqueMenor_Hiperdia' colspan='8'>
				<b>DADOS CL&Iacute;NICOS DO PACIENTE
		   </td>
	   </tr>
          <tr> 
 		    <td><b> PA Sist&oacute;lica  <br>
				<input name='hiper_pa_sistolica'  type='text' value='' size='8' maxlength='3' class='inputForm'> 
			</td>
 		    <td> <b>PA Diast&oacute;lica  <br>
				<input name='hiper_pa_distolica'  type='text' value='' size='8' maxlength='3' class='inputForm'> 
			</td>
 		    <td><b> Cintura (cm) <br>
				<input name='hiper_cintura'  type='text' value='' size='8' maxlength='6' class='inputForm'>
			</td>
 		    <td><b> Peso (Kg)  <br>
				<input name='hiper_peso'  type='text' value='' size='8' maxlength='6' class='inputForm'> 
			</td>
 		    <td><b> Altura(m)  <br>
				<input name='hiper_altura'  type='text' value='' size='8' maxlength='4' class='inputForm'> 
			</td>
	        <td><b>Glic. Cap.(mg/d) <br>
				<input name='hiper_glicemia_capilar'  type='text' value='' size='8' maxlength='6' class='inputForm'> 
		    </td>
	        <td>
				<input type='radio' name='hiper_glicemia_realizada' value='jejum' checked><b>Em Jejum <br>
		        <input type='radio' name='hiper_glicemia_realizada' value='prandial'><b>P&oacute;s Prandial
		    </td>
		  </tr>
    </table>";
?>