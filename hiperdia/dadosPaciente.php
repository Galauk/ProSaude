<?
session_start();
/*cabecario();*/
require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."json.inc.php";

?>


<link href='<?= $_SESSION[linkroot].$_SESSION[modulo]; ?>css/estiloForm.css' rel='stylesheet' type='text/css' />
    <table border='0' cellpadding=0 cellspacing=0 width='100%'>
    	  <tr>
<?
		  	echo"
    	  	    <td colspan='7'><b> Munic&iacute;pio <br>
					<input name='cid_codigo' id='cid_codigo'  type='text' value='' size='8' class='inputForm'>
					<input name='cid_nome' id='cid_nome'  type='text' value='' class='inputForm'>

					<a href='#' onclick=\"buscar_municipio(\$F('cid_nome'), 'buscar_municipio');return false;\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg id=localizar align=absmiddle border=0></a>";
					echo divBuscaMunicipio('../');	
    	  echo"</td>";
				
	
    	 echo" </tr>
          
    	  	    <td><b> Unidade <br>
					<input name='uni_codigo' id='uni_codigo'  type='text' value='' size='30' class='inputForm'>
					<input name='uni_nome' id='uni_nome'  type='text' value='' size='50' class='inputForm'>

					<a href='#' onclick=\"buscar_unidade(\$F('uni_nome'), 'buscar_unidade');return false;\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg id=localizar align=absmiddle border=0></a>";
					echo divBuscaUnidade('../');	
    	  echo"</td>";
?>			
			</td>
            <td width='15%'><b> C&oacute;d. SIA/SUS <br>
				<input name='codSiaSus' type='text' value='' size='8' maxlength='8' class='inputForm'> 
            </td>
	        <td width='15%'><b> Dt. Cadastro <br>
		        <input name='dataCadastro'  type='text' value=''  class='inputForm'> 
          </tr>
          <tr> 
 		    <td width='65%'><b>Atendente <br>
			  <input name='nmUsuario'  type='text' value='' size='60' class='inputForm' > 
			</td>
	        <td width='15%'> <b>Dt. Consulta <br>
		        <input name='dataConsulta'  type='text' value='' size='12' maxlength='10' class='inputForm' > 
  			  </td>
       </tr>
       <tr>
<?
			echo "<td>
						<b>Paciente:<br/>";
						echo "<input type=hidden name='pac_codigo' id='pac_codigo' class=boxl size=10 onchange='buscar_dados_paciente();'>";
						echo "<input type=text name='pac_prontuario' id='pac_prontuario' class=inputForm size=10 onchange='buscar_dados_paciente(this.value);'>&nbsp;
								<input type=text name=pac_nascimento id=pac_nascimento class=inputForm size=15 readonly onfocus='buscar_prontuario();'>&nbsp;&nbsp;";								
						echo "<input type=text size=80 name=pac_nome id=pac_nome value='$pac[usu_nome]' class=inputForm  style=\"text-transform:uppercase;\">&nbsp;&nbsp;";
						echo "<a href='#' onclick=\"buscar_nome(\$F('pac_nome'), 'buscar_nome');return false;\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg id=localizar align=absmiddle border=0></a>";
						
					echo divBuscaPaciente('../');
			echo "</td>";
?>
		</tr>
  </table>