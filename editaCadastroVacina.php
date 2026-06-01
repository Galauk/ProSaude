<link href="estiloNovo.css" type="text/css" rel="stylesheet">
<script language="JavaScript" type="text/javascript" src="ajax_motor.js"></script>
<script>
function editaTudo()
{
	var nome_vac = document.getElementById('nome_vac').value;
	var num_doses = document.getElementById('num_doses').value;	
	var marca = document.getElementById('marca').value;	
	var dataVal = document.getElementById('dataVal').value
	var dose_um = document.getElementById('dose_um').value;	
	var dose_dois = document.getElementById('dose_dois').value;
	var dose_tres = document.getElementById('dose_tres').value;
	var dose_quatro = document.getElementById('dose_quatro').value;
	var dose_cinco = document.getElementById('dose_cinco').value;
	var reforco = document.getElementById('reforco').value;
	var tempo = document.getElementById('tempo').value;
	if(nome_vac == '')
	{
		alert('Preencha o campo Nome');
		exit();
	}if(num_doses == ''){
		alert('Preencha o campo Numero de Doses');	
		exit();
	}if(marca == ''){
		alert('Preencha o campo marca')	;
		exit();
	}if(dose_um == ''){
		alert('Preencha o campo Primeira Dose')	;
		exit();
	}if(dose_dois == ''){
		alert('Preencha o campo Segunda Dose')	;
		exit();
	}if(dose_tres == ''){
		alert('Preencha o campo Terceira Dose')	;
		exit();
	}if(dose_quatro == ''){
		alert('Preencha o campo Segunda Dose')	;
		exit();
	}if(reforco == ''){
		alert('Preencha o campo Reforco')	;
		exit();
	}if(tempo == ''){
		alert('Preencha o campo Quarta Tempo Util')	;
		exit();
	}else{

	url = "alteraVacinas.php?nome_vac="+nome_vac+"&num_doses="+num_doses+"&marca="+marca+"&dataVal="+dataVal+"&dose_um="+dose_um+"&dose_dois="+dose_dois+"&dose_tres="+dose_tres+"&dose_quatro="+dose_quatro+"&dose_cinco="+dose_cinco+"&reforco="+reforco+"&tempo="+tempo;
	ajax_tudo(url,resposta);
	}
}
function resposta(txt)
{
	alert(txt);
	exit();
	window.location.href=window.location.href
	if(txt == '')
	{
		alert('Erro ao Adicionar a Vacina');
	}
}
</script>
<?php
session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
verauth($id_login);
	
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
cabecario();
include_once $_SESSION[root].$_SESSION[modulo]."funcao.calendario.php";
$pro_codigo = $_GET['pro_codigo'];

$slqValue = "select a.pro_codigo,
				    a.doses_vac,
					a.tempo_vida_vac,
					to_char (a.validade_vac,'dd/mm/yyyy') as validade_vac,
					a.marca_vac,
					b.pro_nome 
			   from vacinas_part as a
			   join produto as b 
			     on a.pro_codigo = b.pro_codigo
			  where a.pro_codigo = $pro_codigo";
$qry = pg_query($slqValue);
$resultado = pg_fetch_array($qry);
echo "
<table>
	<tr>
		<td>
			<fieldset>
				<legend>Cadastro de Vacina</legend>
					<table>
						<tr>
							<td align='center'>
								<h1>Vacinas</h1>
							</td>	
						</tr>	
					</table>
			</fieldset>
			<fieldset>
				<table border='0'>
					<tr>
						<td width='150px'>
							<b>Nome:</b>
							<input type='text' name='nome_vac' id='nome_vac' class='boxTexto' value='{$resultado['pro_nome']}'>
						</td>
						<td width=5px>
						</td>
					</tr>
					<tr>
						<td>
							<b>Numero de Doses:</b>
							<input type='text' class='boxNumero' name='num_doses' id='num_doses' value='{$resultado['doses_vac']}'>	
						</td>
					</tr>
					<tr>
						<td width='150px'>
							<b>Marca:</b>
							<input type='text' name='marca' id='marca' class='boxTexto' value='{$resultado['marca_vac']}'>
						</td>
					</tr>
					<tr>
						<td width='150px'>
							<b>Tempo de Vida:</b>
							<input type='text' name='tempo' id='tempo' class='boxData' value='{$resultado['tempo_vida_vac']}'>
						</td>
					</tr>
					<tr>
						<td>
							<b>Validade:";
								echo "<input type='text' name='dataVal' id='dataVal' class='boxNumero' value='{$resultado['validade_vac']}'>";
								$sqlCarteirinha = "select * from carteirinha where pro_codigo = $pro_codigo";	
								$qryCarteirinha = pg_query($sqlCarteirinha);
								$umRegistroCarteirinha = pg_fetch_array($qryCarteirinha);

				   echo"</td>
					</tr>
					<tr>
						<td width='100px'>
							<b>Primeira Dose:
							<select name='dose_um' id='dose_um' class='boxNumero'>
								<option value=''> </option>
								<option value='S'".($umRegistroCarteirinha['dose_um'] == 'S' ? "selected='selected'": '').">Sim</option>
								<option value='N'".($umRegistroCarteirinha['dose_um'] == 'N' ? "selected='selected'": '').">N&atilde;o </option>
							</select>	 

							<b>Segunda Dose:
							<select name='dose_dois' id='dose_dois' class='boxNumero'>
								<option value=''> </option>
								<option value='S'".($umRegistroCarteirinha['dose_dois'] == 'S' ? "selected='selected'": '').">Sim</option>
								<option value='N'".($umRegistroCarteirinha['dose_dois'] == 'N' ? "selected='selected'": '').">N&atilde;o </option>
							</select>	 
						</td>							
					</tr>
					<tr>
						<td width='100px'>
							<b>Terceira Dose:
							<select name='dose_tres' id='dose_tres' class='boxNumero'>
								<option value=''> </option>
								<option value='S'".($umRegistroCarteirinha['dose_tres'] == 'S' ? "selected='selected'": '').">Sim</option>
								<option value='N'".($umRegistroCarteirinha['dose_tres'] == 'N' ? "selected='selected'": '').">N&atilde;o </option>
							</select>	 

							<b>Quarta Dose:
							<select name='dose_quatro' id='dose_quatro' class='boxNumero'>
								<option value=''> </option>
								<option value='S'".($umRegistroCarteirinha['dose_quatro'] == 'S' ? "selected='selected'": '').">Sim</option>
								<option value='N'".($umRegistroCarteirinha['dose_quatro'] == 'N' ? "selected='selected'": '').">N&atilde;o </option>
							</select>	 
						</td>							
					</tr>
					<tr>
						<td width='100px'>
							<b>Quinta Dose:
							<select name='dose_cinco' id='dose_cinco' class='boxNumero'>
								<option value=''> </option>
								<option value='S'".($umRegistroCarteirinha['dose_cinco'] == 'S' ? "selected='selected'": '').">Sim</option>
								<option value='N'".($umRegistroCarteirinha['dose_cinco'] == 'N' ? "selected='selected'": '').">N&atilde;o </option>
							</select>	 

							<b>Refor&ccedil;o:
							<select name='reforco' id='reforco' class='boxNumero'>
								<option value=''> </option>
								<option value='S'".($umRegistroCarteirinha['reforco'] == 'S' ? "selected='selected'": '').">Sim</option>
								<option value='N'".($umRegistroCarteirinha['reforco'] == 'N' ? "selected='selected'": '').">N&atilde;o </option>
							</select>	 
						</td>							
					</tr>
					<tr>
						<td>
							<span onClick=\"editaTudo();\"style='cursor: pointer;' id='botaoenviar'>
								<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/salvar_on.jpg' alt='salvar'>
							</span>
						</td>	
					</tr>
				</table>
			</fieldset>
		</td>
	</tr>
</table>";
?>