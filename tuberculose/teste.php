<link href='../css/estiloForm.css' rel='stylesheet' type='text/css' />
<link href='../css/estiloCommon.css' rel='stylesheet' type='text/css' />
<script>
function mostraCompleto(hiper_codigo,usu_codigo){
	location.href="historicoCompleto.php?hiper_codigo="+hiper_codigo+"&usu_codigo="+usu_codigo;	
}
</script>
<?
	
	$common = new commonClass();
	$table = new tableClass();
	echo $common->incJquery();
	echo "PQ NAO?";
	echo $form->openForm();
		echo $table->openTable("lista");
			echo $table->criaLinha(array("Nome Paciente","Data Cadastro","Tratamento Supervisionado","Usa Drogas","Nome Investigador"),null,null,"S");
				$sql = "";
				$qry = pg_query($sql);
				while($row = pg_fetch_array($qry)){
					echo $table->criaLinha(array(""),null,null,"N","onclick=\"mostraCompleto('$row[hiper_codigo]','$usu_codigo')  \"");
				}

		echo $table->closeTable();
?>
