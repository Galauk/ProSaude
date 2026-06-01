<link href='../css/estiloForm.css' rel='stylesheet' type='text/css' />
<link href='../css/estiloCommon.css' rel='stylesheet' type='text/css' />
<script>
function retornaHistorico(usu_codigo){
	location.href="geralHiperdia.php?acao=retorno&usu_codigo_retorno="+usu_codigo;		
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
	echo $common->menuTab(array("Historico Hiperdia"));
		echo $common->bodyTab("1");
		$select =  "SELECT hip.hiper_codigo,
						   to_char(hip.hiper_data,'DD/MM/YYYY') AS hiper_data,
						   med.med_nome,
						   hip.hiper_pa_distolica,
						   hip.hiper_pa_sistolica,
						   hipmed.hipermed_medicamentoso, 
						   hiper_altura,
						   hiper_peso,
						   hiper_glicemia_capilar,
						   hiper_enfermeiro,
						   proc.proc_nome, 
						   prod.pro_nome,
						   hipermed_dosagem,
						   usu.usu_nome    
					  FROM hiperdia AS hip
					  JOIN medico AS med
						ON hip.med_codigo = med.med_codigo
					  JOIN hiperdia_medicamentos AS hipmed
						ON hipmed.hiper_codigo = hip.hiper_codigo
					  JOIN hiperdia_exames AS hipexa
						ON hipexa.hiper_codigo = hip.hiper_codigo
					  JOIN procedimento AS proc
						ON hipexa.proc_codigo = proc.proc_codigo
					  JOIN produto AS prod
						ON prod.pro_codigo = hipmed.pro_codigo
					  JOIN usuario  AS usu
    					ON usu.usu_codigo = hip.usu_codigo
					 WHERE hip.usu_codigo = $usu_codigo 
					   AND hip.hiper_status = 'A'";
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
						<b>Data Hiperdia:</b>
					</td>
					<td>
						$umaLinha[hiper_data]
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
						<b>PA.Sist&oacute;lica:</b>
					</td>
					<td>
						$umaLinha[hiper_pa_sistolica]
					</td>
				</tr>
				<tr>
					<td>
						<b>PA.Diast&oacute;lica:</b>
					</td>
					<td>
						$umaLinha[hiper_pa_distolica]
					</td>
				</tr>
				<tr>
					<td>
						<b>Altura:</b>
					</td>
					<td>
						$umaLinha[hiper_altura]
					</td>
				</tr>
				<tr>
					<td>
						<b>Peso:</b>
					</td>
					<td>
						$umaLinha[hiper_peso]
					</td>
				</tr>
				<tr>
					<td>
						<b>Glicemia:</b>
					</td>
					<td>
						$umaLinha[hiper_glicemia_capilar]
					</td>
				</tr>
				<tr>
					<td>
						<b>Exames Realizados:</b>
					</td>
					<td>";
						$selectExames = "select * from hiperdia_exames as hipexa
												  join procedimento as proc
													on proc.proc_codigo = hipexa.proc_codigo
												 where hiper_codigo=$hiper_codigo";
						$queryExames = pg_query($selectExames);
						while($linha = pg_fetch_array($queryExames)){
							echo $linha['proc_nome']."<br/>";
						}
					echo"
					</td>
				</tr>
				<tr>
					<td>
						<b>Medicamentos Ministrados:</b>
					</td>
					<td>";
						$selectMedicamentos = "select * from hiperdia_medicamentos as rem
												   join produto as pro
												     on rem.pro_codigo = pro.pro_codigo
											      where hiper_codigo = $hiper_codigo";
						$queryMedicamentos = pg_query($selectMedicamentos);
						$linha = pg_fetch_array($queryMedicamentos);
						$numLinhas = pg_num_rows($queryMedicamentos);
						for($i=0;$i<$numLinhas;$i++){
							echo $linha['pro_nome']."/"."<b style='color:#F00'>".$linha['hipermed_dosagem']."</b>"."<br/>";
						}
					echo"
					</td>
				</tr>
				<tr>
					<td>
						<b>Outros:</b>
					</td>
					<td>";
						
						echo $linha['hipermed_outros'];
					echo"
					</td>
				</tr>
				<tr>
					<td>
						<input type='image' name='voltar' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.jpg' onClick=\"retornaHistorico('$usu_codigo')\">
					</td>
					<td>
						<a href='confirmDelete.php?hiper_codigo=$umaLinha[hiper_codigo]&usu_codigo=$usu_codigo'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg' style='border:0px'> </a>
					</td>
				</tr>
			 </table>";
		echo $common->closeTab();
	//
	//<a href='geralHiperdia.php?usu_codigo=$usu_codigo'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.jpg' style='border:0px'></a>	
?>