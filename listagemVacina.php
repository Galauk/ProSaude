<script type="text/javascript" src="ajax_motor.js"></script>
<script>
function editaDados(codigo_ficha_familia)
{

	url = "edit.php?codigo_ficha_familia="+codigo_ficha_familia;
	alert(url);
	ajax_tudo(url,next);
}

function next(txt)
{

	if(txt == false)
	{
		alert("Nao foi possivel Editar !");	
	}
}

</script>
<?
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
verauth($id_login);
cabecario();

$bdr = "style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'";
$bdr2 = "style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'";
echo "
<table>
	<tr>
		<td>
			<fieldset>
				<legend>Vacina</legend>
					<table>
						<tr>
							<td>
								<a href='cadastroVacina.php'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg border=0 alt='adicinar'></a>
							</td>
						</tr>
					</table>
			</fieldset>
			<fieldset>
			";
			//////////////////////////// parte listada//////////////////////////////////
		
			/////////////////////////////////////////////////////////////////////////////
										//Começo dos SQL//
										
			$stmt = "select a.pro_codigo,
							b.pro_nome,
							a.doses_vac,
							to_char(a.validade_vac,'DD/MM/YYYY') as validade_vac,
							b.gru_codigo	
					  from vacinas_part as a
					  join produto as b
						on a.pro_codigo = b.pro_codigo
					 where b.gru_codigo = 100002";	
			$qry = pg_query($stmt);					
			/////////////////////////////////////////////////////////////////////////////	
			//echo $stmt;
												
		echo"
				<legend>Listando Vacinas Cadastradas</legend>
				<table>
					<tr bgcolor=F9f9f9>
	
						<td width=40 $bdr>Cod.Produto</td>
						<td width=270 $bdr>Nome Vacina</td>
						<td width=20 $bdr>Quantidade Doses</td>
						<td width=50 $bdr>Validade</td>
						<td width=270 $bdr></td>
						<td width=270 $bdr></td>
					</tr>";
				while ($result = pg_fetch_array($qry)){
				echo"<tr>
						<td align=center $bdr2>{$result['pro_codigo']}</td>
						<td width=270 $bdr2>{$result['pro_nome']}</td>
						<td align=center $bdr2>{$result['doses_vac']}</td>
						<td $bdr2>{$result['validade_vac']}</td>
						<td $bdr2><a href='editaCadastroVacina.php?pro_codigo={$result['pro_codigo']}'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg' border=0></a></td>
						<td $bdr2><a href='edit.php?codigo_ficha_familia={$result['codigo_ficha_familia']}'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg' border=0></a></td>
					</tr>";
				} //<span style='cursor: pointer;' onclick='editaDados({$result['codigo_ficha_familia']})'><img src='../".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg' alt='Apagar'></span>
			echo "</table>	
			 </fieldset>
		</td>
	</tr>
</table>";
?>