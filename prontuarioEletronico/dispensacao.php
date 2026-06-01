<link href="css/estiloForm.css" rel="stylesheet" type="text/css" />
<link href="css/estiloCommon.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" type="text/javascript" src="relatorio/funcoes.js"></script>
<script src=relatorio/script.js></script>
<?
session_start();
require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

$form = new classForm();
$common = new commonClass();
$table = new tableClass();
//$_REQUEST['age_codigo'] = 321951;
echo $common->incJquery();
echo $common->menuTab(array('Dispensar Medicamentos'));
echo $common->bodyTab('1');

	if($_REQUEST['acao']=='novo'){
echo "<br><br><h1>DISPENSAR MEDICACAO</h1>
			<table cellspacing='1' cellpadding='5' border=0 style='border:1px solid' class='table'>
			<tr>
			 <td>&nbsp;</td>
			 <td>Medicamento</td>
			 <td>Qtde/Dose</td>
			 <td>Unidade</td>
			 <td>Velocidade</td>
			 <td>Via acesso</td>
			 <td>Frequencia</td>
			 <td>Hr Inicio</td>
			 <td>Observacao</td>
			 <td><b>Data da Prescricao</b></td>
			<tr><form method=post action=dispensacao.php name='formulario'>
			<input type=hidden name='acao' value='dsp'>";		
$sq = pg_query("select to_char(data,'dd/mm/yyyy') as dia,*from internacao_prescricao as ip join produto as p on p.pro_codigo=ip.pro_codigo join tb_administracao_produto as adp on adp.adm_codigo = ip.adm_codigo join frequencia_medicacao as fr on fr.frq_codigo = ip.frq_codigo join unidmedida as um on um.umed_codigo = p.umed_codigo where (inp_dispensado is null OR inp_dispensado='N') and age_codigo = ".$_REQUEST['age_codigo']."");
while($rr=pg_fetch_array($sq)) {
	echo "<tr class='registros'>
			 <td><input type='checkbox' value='$rr[pro_codigo]|$rr[age_codigo]|$rr[io_codigo]' name='pro_codigo[]' checked></td>
			 <td>$rr[pro_nome]</td>
			 <td>$rr[inp_qtde_dose]</td>
			 <td>$rr[umed_nome]</td>
			 <td>$rr[inp_velocidade]</td>
			 <td>$rr[adm_sigla]</td>
			 <td>$rr[frq_nome]</td>
			 <td>$rr[inp_hrini]</td>
			 <td>$rr[inp_observacao]</td>
			 <td>$rr[dia]</td>
			<tr>";
 }	
echo "</table>";
echo $common->commonButton("Dispensar Medicamentos",null,"delete.png","onClick=\"document.formulario.submit()\"")."</form>";
	} else {
//		var_dump($_REQUEST['pro_codigo']);
		for($k=0;$k<=count($_REQUEST['pro_codigo']);$k++){
			if($pro_codigo[$k]!='') {
				$e = explode('|',$pro_codigo[$k]);
		$sql = pg_query("update internacao_prescricao set inp_dispensado='S',inp_data_dispensado=NOW() where pro_codigo = $e[0] and age_codigo = $e[1] and io_codigo = $e[2]");
			echo $common->modalMsg("OK","Dispensado com Sucesso","../internacao.php");	
		 }
		}
	}
	
		echo "".$common->commonButton("Voltar",null,"voltar.png","onClick=\"javascript:history.go(-1)\"")."";

echo $common->closeTab();


?>

