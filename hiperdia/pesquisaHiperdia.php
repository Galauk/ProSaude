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
		echo $table->criaLinha(array($common->commonButton("adicionar","pesquisaHiperdia.php?form_add","adicionar.png"),$common->commonButton("buscar",null,"buscar.png","onclick='document.busca.submit()'"),$form->inputText("busca","$valor")),array(130));
	echo $table->closeTable();
echo $form->closeForm();

if($acao == "buscar" || $acao == ""){
	$busca = $_POST['busca'];
	$sql = "SELECT distinct hip.hiper_codigo,
				   to_char(hip.hiper_data,'DD/MM/YYYY') AS hiper_data,
				   med.med_nome,
				   hip.hiper_pa_distolica,
				   hip.hiper_pa_sistolica,
				   hipmed.hipermed_medicamentoso 
			  FROM hiperdia AS hip
			  JOIN medico AS med
				ON hip.med_codigo = med.med_codigo
			  JOIN hiperdia_medicamentos AS hipmed
				ON hipmed.hiper_codigo = hip.hiper_codigo
			  JOIN usuario AS usu
				ON usu.usu_codigo = hip.usu_codigo
			 WHERE (usu.usu_nome LIKE UPPER('%$busca%'))
				OR (med.med_nome LIKE UPPER('%$busca%'))";
	$qry = pg_query($sql);
	echo $table->openTable("lista");
		echo $table->criaLinha(array("C&oacute;digo Hiperdia","Data Hiperdia","Acompanhamentos","M&eacute;dico Responsavel"),null,null,"S");
		while ($linhas = pg_fetch_array($qry)){
			echo $table->criaLinha(array("$linhas[hiper_codigo]","$linhas[hiper_data]","S","$linhas[med_nome]"));			
		}
	echo $table->closeTable();
}

echo $common->closeTab();
?>