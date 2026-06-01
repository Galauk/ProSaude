<?php
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

$verificaColeta = "select * from itensdoexame where itx_codigo = $itx_codigo";
$queryColeta = pg_query($verificaColeta);
$reg = pg_fetch_array($queryColeta);
$itx_status = $reg['itx_status'];

if(trim($itx_status) == "P"){

	echo "<script>
			 alert('Nao foi efetuado coleta para esse exame!');
			 window.location = 'exa_materialdeanalise_iframeAGE.php?usu_codigo='+$usu_codigo;
		  </script>";	
}else{
	if($acao == "edit_ind"){
		echo "<form action=$PHP_SELF method='post'>
				<input type='hidden' name='acao' id='acao' value='update'>
				<input type='hidden' name='itx_codigo' id='itx_codigo' value='$itx_codigo'>
					<table>
						<tr>
							<td>
								<font color='#FF0000'>$proc_nome</font>
							</td>
						</tr>
						<tr>
							<td>
								<b>Conserva&ccedil;&atilde;o:
							</td>
						</tr>
						<tr>
							<td>
							<textarea name='mlz_conservacao' id='mlz_conservacao' value='$mlz_conservacao' cols='40' rows='3'>						
							</textarea>
							</td>
						</tr>
						<tr>
							<td>
								<b>Observa&ccedil;&atilde;o:
							</td>
						</tr>
						<tr>
							<td>
								<textarea name='mlz_observacao' id='mlz_observacao' value='$mlz_observacao' cols='40' rows='3'>
								</textarea>
							</td>
						</tr>
						<tr>
							<td>
								<b>Motivo:
							</td>
						</tr>
						<tr>
							<td>
								<textarea name='mlz_motivo' id='mlz_motivo' value='$mlz_motivo' cols='40' rows=3> 
								</textarea>
							</td>
						</tr>
						<tr>
							<td>
								<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/salvar_on.jpg'>
							</td>
						</tr>
					</table>
				</form>";	
	}	
	$acao = $_POST['acao'];
	if($acao == 'update'){
		$itx_codigo = $_POST['itx_codigo'];
		$mlz_conservacao = $_POST['mlz_conservacao'];
		$mlz_motivo = $_POST['mlz_motivo'];
		$mlz_observacao = $_POST['mlz_observacao'];
		
		$atualiza = "UPDATE materialdeanalise 
						SET mlz_observacao = '$mlz_observacao',
							mlz_motivo = '$mlz_motivo',
							mlz_conservacao = '$mlz_conservacao '
					  WHERE itx_codigo = $itx_codigo";	
		echo $atualiza;
		exit;
		$query = pg_query($atualiza);
		
		if (pg_affected_rows($query) == 0)
		{
			echo "<script>
					alert('Houve um Erro ao atualizar, tente novamente!');
				  </script>";	
		}else{
		echo "<script>
				alert('Modificado com Sucesso');
				window.location = 'exa_materialdeanalise_iframeAGE.php?usu_codigo='+$usu_codigo;
			  </script>";
		}
	}
}
?>