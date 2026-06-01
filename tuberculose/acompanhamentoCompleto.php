<link href='../css/estiloForm.css' rel='stylesheet' type='text/css' />
<link href='../css/estiloCommon.css' rel='stylesheet' type='text/css' />
<script>
function retornaHistorico(usu_codigo){
	location.href="geralTuberculose.php?acao=retorno&usu_codigo_retorno="+usu_codigo;		
}
</script>

<?
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	
	$form = new classForm();
	$common = new commonClass();
	$table = new tableClass();
	echo $common->incJquery();
	echo $common->menuTab(array("Historico Tuberculose"));
		echo $common->bodyTab("1");
		$select = "select tub.*,tuind.*,tudr.*,usu.usu_nome,med.med_nome from tuberculose as tub
					 join tuberculose_drogas as tudr
					   on tub.tub_codigo = tudr.tub_codigo
					 join tuberculose_individualidades as tuind
					   on tub.tub_codigo = tuind.tub_codigo
					 join usuario as usu
					   on tub.usu_codigo = usu.usu_codigo
					 join medico as med
					   on med.med_codigo = tub.med_codigo
					where tub.usu_codigo = 302673";
		$query = pg_query($select);
		$umaLinha = pg_fetch_array($query);
		echo"<table class='lista'>
			 	<tr>
					<td width='170'>
						<b>Nome:</b>
					</td>
					<td>
						$umaLinha[usu_nome]
					</td>
				</tr>
				<tr>
					<td>
						<b>Data Cadastro:</b>
					</td>
					<td>
						$umaLinha[tub_data_cadastro]
					</td>
				</tr>
				<tr>
					<td>
						<b>Nome do M&eacute;dico:</b>
					</td>
					<td>
						$umaLinha[med_nome]
					</td>
				</tr>
				<tr>
					<td>
						<b>Nome Investigador:</b>
					</td>
					<td>
						$umaLinha[tub_nome_investigador]
					</td>
				</tr>
				<tr>
					<td>
						<b>Utiliza Drogas:</b>
					</td>
					<td>
						$umaLinha[tub_drogas]
					</td>
				</tr>
				<tr>
					<td>
						<b>Tipos de Drogas:</b>
					</td>
					<td>";
						$selectDrogas = "select * from tuberculose_drogas where tub_codigo = 3";
						$queryDrogas = pg_query($selectDrogas);
						while($linha = pg_fetch_array($queryDrogas)){
							echo $linha['tub_dro_tipo']."<br/>";
						}
					echo"
					</td>
				</tr>
				<tr>
					<td>
						<b>Outras Drogas:</b>
					</td>
					<td>
						$umaLinha[tub_dro_outros]
					</td>
				</tr>
				<tr>
					<td>
						<b>Forma:</b>
					</td>
					<td>
						$umaLinha[tub_ind_forma]
					</td>
				</tr>
				<tr>
					<td>
						<b>Agravo:</b>
					</td>
					<td>
						$umaLinha[tub_ind_agravo]
					</td>
				</tr>
				<tr>
					<td>
						<b>Baciloscopia de Escarro</b>
					</td>
					<td>
						$umaLinha[tub_ind_baciloscopia_escarro]
					</td>
				</tr>
				<tr>
					<td>
						<b>Outros:</b>
					</td>
					<td>
						$umaLinha[tub_ind_cultura_escarro]
					</td>
				</tr>
				<tr>
					<td>
						<b>HIV:</b>
					</td>
					<td>
						$umaLinha[tub_ind_hiv]
					</td>
				</tr>
				<tr>
					<td>
						<b>Tuberculinico:</b>
					</td>
					<td>
						$umaLinha[tub_ind_tuberculinico]
					</td>
				</tr>
				<tr>
					<td>
						<b>Extrapulmonar:</b>
					</td>
					<td>
						$umaLinha[tub_ind_extrapulmonar]
					</td>
				</tr>
				<tr>
					<td>
						<b>Histopatologia:</b>
					</td>
					<td>
						$umaLinha[tub_ind_histopatologia]
					</td>
				</tr>
				<tr>
				<tr>
					<td>
						<b>Retorno:</b>
					</td>
					<td>
						$umaLinha[tub_dro_retorno]
					</td>
				</tr>
				<tr>
					<td>
						<b>Retorno:</b>
					</td>
					<td>
						$umaLinha[tub_dro_retorno]
					</td>
				</tr>
					<td>
						<input type='image' name='voltar' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.jpg' onClick=\"retornaHistorico('$usu_codigo')\">
					</td>
					<td>
						<a href='confirmDel.php?tub_codigo=$umaLinha[tub_codigo]&usu_codigo=$usu_codigo'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg' style='border:0px'> </a>
					</td>
				</tr>
			 </table>";
		echo $common->closeTab();
?>