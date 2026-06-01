<script>
	function chamaHiper(usu_codigo, hiper_codigo){
		location.href="hiperdia.php?acao=form_add"+"&hiper_codigo="+hiper_codigo+"&usu_codigo="+usu_codigo;	
	}
</script>
<?
session_start();
require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	
$common = new commonClass();
$form = new classForm();
$table = new tableClass();
echo $common->incJquery();

echo $common->menuTab(array("Hiperdia"));
echo $common->bodyTab("1");


	echo $form->openForm("$PHP_SELF","POST","busca");
		echo $form->hiddenForm("acao","buscar");
		echo $table->openTable();
			echo $table->criaLinha(array($common->commonButton("adicionar","hiperdia.php?acao=form_add","adicionar.png"),$common->commonButton("buscar",null,"buscar.png","onclick='document.busca.submit()'"),$form->inputText("busca","$valor")),array(130));
		echo $table->closeTable();
	echo $form->closeForm();

if($acao == "buscar" || $acao == ""){
	$busca = $_POST['busca'];
	$sql = "SELECT distinct hip.hiper_codigo,
				   to_char(hip.hiper_data,'DD/MM/YYYY') as hiper_data,
				   hip.hiper_pa_diastolica,
				   hip.hiper_pa_sistolica,
				   hipmed.hipermed_medicamentoso,
				   usu.usu_nome,
				   usu.usu_codigo
			  FROM hiperdia as hip
			  LEFT JOIN hiperdia_medicamentos as hipmed
				ON hipmed.hiper_codigo = hip.hiper_codigo
			  JOIN usuario as usu
				ON usu.usu_codigo = hip.usu_codigo
			 WHERE (usu.usu_nome like UPPER('%$busca%'))
			 LIMIT 10";
	$qry = pg_query($sql);
	echo $table->openTable("lista");
		echo $table->criaLinha(array("C&oacute;digo Hiperdia","Data Hiperdia","Nome do paciente"),null,null,"S");
		while ($linhas = pg_fetch_array($qry)){
			echo $table->criaLinha(array("$linhas[hiper_codigo]","$linhas[hiper_data]","$linhas[usu_nome]"),null,null,"N","onClick=\"chamaHiper($linhas[usu_codigo],$linhas[hiper_codigo]) \"");			
		}
	echo $table->closeTable();
}

echo $common->closeTab();
?>