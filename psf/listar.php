<?
session_start();
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
$codigo_ficha_familia = $_GET['codigo_ficha_familia'];
$numero_fam = $_GET['numero_fam'];

	$familia = "select * from psf where codigo_ficha_familia = $codigo_ficha_familia and numero_fam = $numero_fam";
	$qryFam = pg_query($familia);
	$umaLinha = pg_fetch_array($qryFam);
	$codigo_fam = $umaLinha['codigo_fam'];

	$sqlInteg = "SELECT b.usu_codigo,
					    b.usu_nome,
					    b.usu_sexo,
					    b.usu_datanasc as data, 
					    b.usu_mae 
				   FROM integrantes_familia as a
				   JOIN usuario  as b
				     ON b.usu_codigo = a.usu_codigo
				  WHERE codigo_fam = $codigo_fam";
				 
				$qryInteg = pg_query($sqlInteg);
				
				echo "<table border='0'>
						<tr bgcolor=F9f9f9>
		
							<td width=40 $bdr>Prontu&aacute;rio</td>
							<td width=270 $bdr>Nome</td>
							<td width=20 $bdr>Sexo</td>
							<td width=100 $bdr>Idade</td>
							<td width=270 $bdr>N. M&atilde;e</td>
							<td width=270 $bdr>Doen&ccedil;as</td>
							<td width=100 $bdr>Gestante</td>
							<td $bdr2></td>
							<td $bdr2></td>
							<td $bdr2></td>
						</tr>";
	
	while ($result = pg_fetch_array($qryInteg)){      
		$sqlDoenca = "select * from doenca_usuario where usu_codigo = {$result['usu_codigo']} ";
		$queryAcima = pg_query($sqlDoenca);
		$linha = pg_fetch_array($queryAcima);
		$cod_usu_doenca = $linha['cod_usu_doenca'];
		
		$sqlGest = "select * from integrantes_familia where usu_codigo = {$result['usu_codigo']} and gestante = 'S'";
		$qryGest = pg_query($sqlGest);
		$registro = pg_fetch_array($qryGest);
		$gestanteConfirm = $registro['gestante'];
		$idade = $result['data']; 
		$teste = verIdadeII($idade);
		
		echo "<tr>
				<td align=center $bdr2>{$result['usu_codigo']}</td>
				<td width=270 $bdr2>{$result['usu_nome']}</td>
				<td align=center $bdr2>{$result['usu_sexo']}</td>
				<td $bdr2>{$teste}</td>
				<td $bdr2>{$result['usu_mae']}</td>";
				if($cod_usu_doenca == "")
				{
					echo "<td $bdr2>N</td>";
				}else{
					echo "<td $bdr2>S</td>";	
				}
				if($gestanteConfirm == 'S')
				{
					echo "<td $bdr2>S</td>";
				}else{
					echo "<td $bdr2>N</td>";	
				}
	
		   echo"
				<td $bdr2><span style='cursor: pointer;' onclick='deletaPac({$result['usu_codigo']},$codigo_fam)'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg' alt='Apagar'></span></td>
				<td><td $bdr2><span style='cursor: pointer;' onclick='doenca({$result['usu_codigo']})'> <img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/doenca.png'> </span> </td>
			</tr>
		";
	}
		echo "</table>";
?>