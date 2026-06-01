<link href='css/estiloForm.css' rel='stylesheet' type='text/css' />
<link href='css/estiloCommon.css' rel='stylesheet' type='text/css' />
<script>
$(function(){

});

function validaForm(){
	if($("#temp_maxima").val() == ""){
		alert("Informe a temperatura maxima");
		setTimeout(function() { $("#temp_maxima").focus(); }, 500);
		return false;
	}
	
	if($("#temp_minima").val() == ""){
		alert("Informe a temperatura minima");
		setTimeout(function() { $("#temp_minima").focus(); }, 500);
		return false;
	}
	
	if($("#temp_momento").val() == ""){
		alert("Informe a temperatura momento");
		setTimeout(function() { $("#temp_momento").focus(); }, 500);
		return false;
	}
	
	$("#form_temp").submit();
}

function verificaValoresTemp(){
	//alert($("#temp_minima").val() "> $("#temp_momento").val());
	if(parseInt($("#temp_minima").val()) > parseInt($("#temp_momento").val()) && $("#temp_momento").val() != ""){
		alert("Temperatura momento nao pode ser menor que a temperatura minima");
		$("#temp_momento").val("");
		setTimeout(function() { $("#temp_momento").focus(); }, 500);
	}
	
	if(parseInt($("#temp_minima").val()) > parseInt($("#temp_maxima").val()) && $("#temp_maxima").val() != ""){
		alert("Temperatura maxima nao pode ser menor que a temperatura minima");
		$("#temp_maxima").val("");
		setTimeout(function() { $("#temp_maxima").focus(); }, 500);
	}
	
	if(parseInt($("#temp_momento").val()) > parseInt($("#temp_maxima").val()) && $("#temp_maxima").val() != ""){
		alert("Temperatura maxima nao pode ser menor que a temperatura momento");
		$("#temp_maxima").val("");
		setTimeout(function() { $("#temp_maxima").focus(); }, 500);
		
	}
	
	
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
echo $common->menuTab(array('Controle de Temperatura'));
echo $common->bodyTab('1');

if($acao == ""){

		echo $table->openTable('lista');
		echo"<tr>
				<td>
					<a href='$PHP_SELF?acao=form_add'><input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg'></a>
				</td>
		     </tr>";
		echo $table->criaLinha(array("Data","Geladeira","Setor","Minima","Momento","Maxima"),null,null,'S');
		$cons = "SELECT 
					temp_minima,
					temp_maxima,
					temp_momento,
					gel_marca,
					set_nome,
					to_char(temp_data,'dd/mm/yyyy')as data_temp 
				FROM 
					temperatura_geladeira AS temp
				INNER JOIN 
					geladeira AS gel ON temp.gel_codigo=gel.gel_codigo
				INNER JOIN 
					setor AS set ON gel.set_codigo=set.set_codigo
				INNER JOIN 
					usuarios_setores AS usus ON set.set_codigo=usus.set_codigo 
				WHERE
					usus.usr_codigo = '".$_SESSION["id_login"]."'
				ORDER BY 
					set_nome, temp_data DESC limit 15";
		$qy = pg_query($cons);
		while($linha = pg_fetch_array($qy)){
			echo $table->criaLinha(array("$linha[data_temp]","$linha[gel_marca]","$linha[set_nome]","$linha[temp_minima]","$linha[temp_momento]","$linha[temp_maxima]") );
		}
}
//$pegaGel = pg_query("select * from geladeira");
//$gel_um = pg_fetch_array($pegaGel);
//$gel_codigo = $gel_um['gel_codigo'];
if($acao == "salvar"){
	//R de registrada em temp_preen....
	$stmt = "INSERT INTO temperatura_geladeira ( 
						 gel_codigo, 
						 temp_minima, 
						 temp_maxima, 
						 temp_momento, 
						 temp_data,
						 observacoes,
						 temp_periodo,
						 temp_preenchida_dia
			 ) VALUES ( 
						'$gel_codigo', 
						'$temp_minima', 
						'$temp_maxima', 
						'$temp_momento', 
						'$data',
						'$observacao',
						'$periodo',
						'R')";

	$query = pg_query($stmt) or die (pg_last_error());
	echo $common->modalMsg("OK","Temperatura Salva com Sucesso!",$caminho);
	echo "<script>
			window.location.href = 'temperaturaGeladeira.php';
		 <script>";
}
if($acao == "form_add")
{
		$sqlGeladeira = "SELECT DISTINCT 
						 gel_codigo,
						 gel_marca 
						FROM 
						 geladeira AS gel
						INNER JOIN 
						  setor AS set ON gel.set_codigo=set.set_codigo
						INNER JOIN 
						  usuarios_setores AS usus ON set.set_codigo=usus.set_codigo 
						WHERE
						  usus.usr_codigo = '".$_SESSION["id_login"]."'";
		//die($sqlGeladeira);
		$queryGeladeira = pg_query($sqlGeladeira);
		$linhaGeladeira = pg_fetch_array($queryGeladeira);
		
		echo $form->openForm("$PHP_SELF","POST","null","","form_temp");
		$dataAtual = date('d/m/Y');
		echo $form->hiddenForm("gel_codigo", $gel_codigo);
		echo $form->hiddenForm(acao, "salvar");
		echo $form->hiddenForm("caminho", "temperaturaGeladeira.php");
		$arrayPeriodo = array("1"=>"Madrugada","2"=>"Manha","3"=>"Tarde","4"=>"Noite");
		echo $form->inputSelect("gel_codigo",null,"Geladeira",$sqlGeladeira,null,null,$linhaGeladeira[gel_codigo],"style=width:200px");
		echo $form->inputText("data","$dataAtual","Data Temperatura",null,10,"onKeypress=\"return Ajusta_Data(this,event)\"",null);
		echo $form->inputText("temp_minima","$temp_minima","Temperatura Minima",null,null,"onChange=\"verificaValoresTemp()\"");
		echo $form->inputText("temp_momento","$temp_momento","Temperatura Momento",null,null,"onChange=\"verificaValoresTemp()\"");
		echo $form->inputText("temp_maxima","$temp_maxima","Temperatura Maxima",null,null,"onChange=\"verificaValoresTemp()\"");
		echo $form->inputSelect(periodo, $arrayPeriodo,"Periodo");
		echo $form->textArea("observacao","$observacao","Observa&ccedil;&atilde;o");
		//echo $form->submitButton(null,"".$_SESSION[linkroot].$_SESSION[comum]."imgs/salvar_on.jpg");
		echo $common->commonButton("Salvar","","salvar.gif","onClick=\"validaForm()\"");
		echo $form->closeForm();

}
?>