<link href="../estiloNovo.css" type="text/css" rel="stylesheet">
<script>
function getDoenca() 
{
	var gestante = document.getElementById('gestante').value;
	var usu_codigo = document.getElementById('usu_codigo').value;
	var doencas = document.getElementsByTagName('input');
	var selecionadas = "";
	for(i=0; i< doencas.length; i++){
		if(doencas[i].getAttribute("type") == "checkbox" && doencas[i].checked == true){
			selecionadas += "|"+doencas[i].value;
			
		}
	}

   window.opener.salvaDoenca(usu_codigo,selecionadas,gestante);
   window.close();
}
</script>
<?
session_start();
$usu_codigo = $_GET['usu_codigo'];
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";

include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
cabecario();
$usu_codigo = $_GET['usu_codigo'];
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
						<td>
							<b>Gestante:</b>
							<select name='gestante' id='gestante' class='box'>
								<option value=''>  </option>
								<option value='S'>Sim</option>
								<option value='N'>N&atilde;o</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							<b>Doen&ccedil;a:</b>
						</td>
					</tr>
					<tr>";
					$sql = "select * from doenca";
					$query = pg_query($sql);
					$arrayTeste = pg_fetch_all($query);
	/*				echo "<pre>".print_r($arrayTeste, true)."</pre>";
					
					$sqlTwo = "select dousu.cod_doenca as codigo, dousu.nome_doenca from doenca_usuario as dousu
					 join doenca as doe
					   on doe.cod_doenca = dousu.cod_doenca
					where dousu.usu_codigo = $usu_codigo";
					//echo $sqlTwo;
					$queryTwo = pg_query($sqlTwo);
					$arrayTeste2 = pg_fetch_all($queryTwo);
					echo "<pre>".print_r($arrayTeste2, true)."</pre>";					
					*/
					$i = 1;
					while($linha = pg_fetch_array($query)){
						
						$sqlTwo = "select *,dousu.cod_doenca as codigo from doenca_usuario as dousu
									 join doenca as doe
									   on doe.cod_doenca = dousu.cod_doenca
								    where dousu.usu_codigo = $usu_codigo";
						//echo $sqlTwo;
						$queryTwo = pg_query($sqlTwo);
						$umaLinha = pg_fetch_array($queryTwo);

						echo "<td><input type='checkbox' name=doencas id=$linha[nome_doenca] value=$linha[cod_doenca]". ($umaLinha['codigo'] == $linha['cod_doenca'])."><b>$linha[nome_doenca]</td>";
						if($i%2 == 0){
							echo "</tr>
								  <tr>
								  	";	
						}
						$i++;
					}

					echo "
					</tr>
				</table>
			</fieldset>
			<span onClick=\"getDoenca('$usu_codigo');\"style='cursor: pointer;' id='botaoenviardoenca'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/enviar_dados_on.jpg'>
		</td>
	</tr>
</table>
</form>";
?>