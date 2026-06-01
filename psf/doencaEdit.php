<link href="../estiloNovo.css" type="text/css" rel="stylesheet">
<script>
function getDoenca() 
{

	var usu_codigo = document.getElementById('usu_codigo').value;
	var doencas = document.getElementsByTagName('input');
	var selecionadas = "";
	for(i=0; i< doencas.length; i++){
		if(doencas[i].getAttribute("type") == "checkbox" && doencas[i].checked == true){
			selecionadas += "|"+doencas[i].value; 
		}
	}
	//alert(selecionadas);
	
   window.opener.editaDoenca(usu_codigo,selecionadas);
   window.close();
}
</script>
<?
session_start();
$usu_codigo = $_GET['usu_codigo'];
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";

require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
cabecario();
$usu_codigo = $_GET['usu_codigo'];

$editDoenca = "select a.cod_doenca, 
					   a.nome_doenca,
					   b.usu_codigo 
				  from doenca as a 
				  join doenca_usuario 
					as b on 
					   a.cod_doenca = b.cod_doenca 
				 where usu_codigo = $usu_codigo";
$qryEdit = pg_query($editDoenca);
$tudoEdit = pg_fetch_array($qryEdit);

echo "
<input type='hidden' id='usu_codigo' name='usu_codigo' value='$usu_codigo'>
<form name='form1' action='javascript://'> 
<table>
	<tr>
		<td>
			<fieldset>
				<legend>Dados</legend>
					<table>
						<tr>
							<td>
								<h1>Dados do Integrante</h1>
							</td>
						</tr>
					</table>
			</fieldset>
			<fieldset>
				<table>
					<tr>
						<td colspan='2'>
							<b>Gestante:</b>
							<select name='gestante' id='gestante' class='box'>
								<option value=''>  </option>
								<option value='S'>Sim</option>
								<option value='N'>N&atilde;o</option>
							</select>
						</td>
					</tr>
					<tr>		
						 <td colspan='2'>
								<b>Doen&ccedil;a:</b>
								</td>
								</tr>
								<tr>";
					$montaDoenca = "select * 
									  from doenca as a 
									  left join doenca_usuario as b 
										on a.cod_doenca = b.cod_doenca 
									   and b.usu_codigo = $usu_codigo";
					$qry = pg_query($montaDoenca);
					$i=0;
					while($campos = pg_fetch_array($qry))
					{
						$i++;
						echo"	
						<td>
							<input type='checkbox' name='doencas' id='{$campos['nome_doenca']}'".($campos['nome_doenca'] == true && $campos['usu_codigo'] == true ? "checked='checked'": '')." value='{$campos['nome_doenca']}'>{$campos['nome_doenca']}
							</td>";
						if ($i % 2 == 0){
							echo "</tr><tr>";
						}
					}
				echo"</tr>
				</table>
			</fieldset>
			<span onClick=\"getDoenca('S','N','$usu_codigo');\"style='cursor: pointer;' id='botaoenviardoenca'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/enviar_dados_on.jpg'>
		</td>
	</tr>
</table>
</form>";

?>