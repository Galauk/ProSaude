<script type="text/javascript" src="../ajax_motor.js"></script>
<link href="../estilo.css" type="text/css" rel="stylesheet">
<script>
function editaDados(codigo_ficha_familia)
{

	url = "edit.php?codigo_ficha_familia="+codigo_ficha_familia;
	//alert(url);
	ajax_tudo(url,next);
}

function next(txt)
{

	if(txt == false)
	{
		alert("Nao foi possivel Editar !");	
	}
}

function deletaFamilia(codigo_ficha_familia){
	url = "deletaFamilia.php?codigo_ficha_familia="+codigo_ficha_familia;
	ajax_tudo(url,resultado);
}
function resultado(txt){

	if(txt == ''){
		alert('Excluido com Sucesso');
		window.location.reload();
	}else{
		alert('Existe Componentes Nesta Familia');
	}

}
</script>
<?
session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
verauth($id_login);

include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
cabecario();

$bdr = "style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'";
$bdr2 = "style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'";
echo "
<table width='100%'>
	<tr>
		<td>
			<fieldset>
				<legend>PSF</legend>
				<form action = \"$PHP_SELF\" method='POST'>
				<input type='hidden' name='action' value='efetuar'>
					<table>
						<tr>
							<td>
								<a href='fichaA.php'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg border=0></a>
							</td>
							<td>
								<b>Busca:</b><input type='text' name='busca' id='busca' class='box' value = '$busca'>
								<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/buscar_on.jpg' style='vertical-align:bottom;'>
							</td>
						</tr>
					</table>
					</form>
			</fieldset>
		</td>
	</tr>
	<tr>
		<td>
			<fieldset>
			";

			if($action == "efetuar"){
				$stmt = "SELECT *
						  FROM psf
						 WHERE ((endereco_fam LIKE UPPER('%$busca%')) 
						".(is_numeric($busca) ? "
							OR (codigo_ficha_familia = $busca)
							OR (area_fam = $busca)
							OR (micro_area_fam = $busca)": "").") "."
					   	ORDER BY endereco_fam";
			}else{
			$stmt = "SELECT codigo_ficha_familia,
							endereco_fam,
							numero_fam,
							area_fam,micro_area_fam,
							to_char(data_cadastro_fam,'DD/MM/YYYY') AS data_cadastro_fam
					   FROM psf
				   ORDER BY codigo_ficha_familia desc
					  LIMIT 15";
			}
			$qry = pg_query($stmt);					
			/////////////////////////////////////////////////////////////////////////////													
		echo"
				<legend>Listando Familias</legend>
				<table class=lista>
					<tr>
						<th width=40>Nr.Ficha</th>
						<th width=570>Endere&ccedil;o</th>
						<th width=20>Numero</th>
						<th width=20>Area</th>
						<th width=20>Micro-Area</th>
						<th width=50>Dt.Cadastro</th>
						<th width=270 colspan=2>&nbsp;</th>
					</tr>";
				while ($result = pg_fetch_array($qry)){
				echo"<tr>
						<td align=center>{$result['codigo_ficha_familia']}</td>
						<td width=270>{$result['endereco_fam']}</td>
						<td align=center>{$result['numero_fam']}</td>
						<td align=center>{$result['area_fam']}</td>
						<td align=center>{$result['micro_area_fam']}</td>
						<td>{$result['data_cadastro_fam']}</td>
						<td><a href='edit.php?codigo_ficha_familia={$result['codigo_ficha_familia']}'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg' border=0></a>
							<span style='cursor: pointer;' onclick='deletaFamilia({$result['codigo_ficha_familia']})'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg' alt='Apagar'></span>
						</td>
					</tr>";
				} //<td><a href='deletaFamilia.php?codigo_ficha_familia={$result['codigo_ficha_familia']}'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg' border=0></a></td>
				//<span style='cursor: pointer;' onclick='editaDados({$result['codigo_ficha_familia']})'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg' alt='Apagar'></span>
			echo "</table>	
			 </fieldset>
		</td>
	</tr>
</table>";
?>