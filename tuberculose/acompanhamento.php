<link href='../css/estiloForm.css' rel='stylesheet' type='text/css' />
<link href='../css/estiloCommon.css' rel='stylesheet' type='text/css' />
<script>
function mostraCompleto(hiper_codigo,usu_codigo){
	location.href="acompanhamentoCompleto.php?usu_codigo="+usu_codigo;	
}
</script>
<?
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	
	$common = new commonClass();
	$table = new tableClass();
	echo $common->incJquery();
	echo $form->openForm();
		echo $table->openTable("lista");
			echo $table->criaLinha(array("Nome Paciente","Data Cadastro","Tratamento Supervisionado","Usa Drogas","Nome Investigador"),null,null,"S");
				$sql = "select usu.usu_nome,
							   tub_data_cadastro,
							   tub_tratamento_supervisionado,
							   tub_drogas,
							   tub_nome_investigador 
						  from tuberculose as tub
						  join usuario as usu
							on usu.usu_codigo = tub.usu_codigo
						 where tub.usu_codigo = $usu_codigo";
				$qry = pg_query($sql);
				while($row = pg_fetch_array($qry)){
					echo $table->criaLinha(array("$row[usu_nome]","$row[tub_data_cadastro]","$row[tub_tratamento_supervisionado]","$row[tub_drogas]","$row[tub_nome_investigador]"),null,null,"N","onclick=\"mostraCompleto('$row[hiper_codigo]','$usu_codigo')  \"");
				}
		echo $table->closeTable();
	echo $form->closeForm();
?>	
