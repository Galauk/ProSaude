<link href='../css/estiloForm.css' rel='stylesheet' type='text/css' />
<link href='../css/estiloCommon.css' rel='stylesheet' type='text/css' />
<script>
function mostraCompleto(hiper_codigo,usu_codigo){
	location.href="historicoCompleto.php?hiper_codigo="+hiper_codigo+"&usu_codigo="+usu_codigo;	
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
	
	echo $form->openForm();
		echo $table->openTable("lista");
			echo $table->criaLinha(array("Data Hiperdia","M&eacute;dico","PA. Sist&oacute;lica","PA.Diast&oacute;lica","Medicamentoso",""),null,null,"S");
				$sql = "select distinct hip.hiper_codigo,
							   to_char(hip.hiper_data,'DD/MM/YYYY') as hiper_data,
							   med.med_nome,
							   hip.hiper_pa_distolica,
							   hip.hiper_pa_sistolica,
							   hipmed.hipermed_medicamentoso 
						  from hiperdia as hip
						  join medico as med
							on hip.med_codigo = med.med_codigo
						  join hiperdia_medicamentos as hipmed
							on hipmed.hiper_codigo = hip.hiper_codigo
						 where hip.usu_codigo = $usu_codigo
						   and hip.hiper_status = 'A' ";
				$qry = pg_query($sql);
				while($row = pg_fetch_array($qry)){
					echo $table->criaLinha(array("$row[hiper_data]","$row[med_nome]","$row[hiper_pa_sistolica]","$row[hiper_pa_distolica]","$row[hipermed_medicamentoso]"),null,null,"N","onclick=\"mostraCompleto('$row[hiper_codigo]','$usu_codigo')  \"");
				}

		echo $table->closeTable();
	echo $form->closeForm();
?>
<tr onclick="">