<?php
	session_start();
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	//verauth($id_login);
	include_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	
	cabecario();
	$common = new commonClass();
	echo $common->incJquery();
//------------------------------------------------------------------>
?>
<script src="<?=$_SESSION[root].$_SESSION[comum];?>library/js/ajax_motor.js"></script>
<script>
function chama_ver(login)
{
    med = document.getElementById("pro_codigo").value;
    url = "ver_estoque.php?id_login="+login+"&med="+med;
    window.open(url,null,"height=400,width=650,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
}
function verificaEstoque(pro_codigo, id_login){
	url = "../verificarEstoque.php?id_login="+id_login+"&pro_codigo="+pro_codigo;
	ajax_tudo(url, responde);
}
function responde(txt){
	document.getElementById('estoque').innerHTML = "<b>Estoque: </b>"+txt;
}
</script>
<?
$select = "SELECT ate_codigo
			 FROM atendimento 
			WHERE usu_codigo = $usu_codigo 
			  AND age_codigo = $age_codigo";

$query = pg_query($select);
$resultado= pg_fetch_array($query);
$ate_codigo = $resultado['ate_codigo'];

$sql = pg_query("select to_char(rec_data, 'dd/mm/yyyy'), rec_tipo from receita where ate_codigo = $ate_codigo");

$ReceitasAnt = pg_num_rows($sql);
if ($ReceitasAnt == 0) {
	$row = pg_fetch_array($sql);
}
echo $common->menuTab(array('Medicamentos de Posto', 'Medicamentos Controlados', 'Medicamentos Externos'));
echo $common->bodyTab('1');
	$tipo_rec = "posto";
	$receitaExtenso = "Medicamentos de Posto";
	include 'formularioReceita.php';
echo $common->closeTab();
echo $common->bodyTab('2');
	$tipo_rec = "controlados";
	$receitaExtenso = "Medicamentos Controlados";
	include 'formularioReceita.php';
echo $common->closeTab();
echo $common->bodyTab('3');
	$tipo_rec = "externo";
	$receitaExtenso = "Medicamentos Externos";
	include 'formularioReceita.php';
echo $common->closeTab();
?>