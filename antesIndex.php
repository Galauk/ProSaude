<link href='css/estiloForm.css' rel='stylesheet' type='text/css' />
<link href='css/estiloCommon.css' rel='stylesheet' type='text/css' />
<script type="text/javascript" src="/WebSocialComum/library/js/jquery-1.6.2.min.js"></script>
<script>
$(function(){
	atualizaSetor();
});

function atualizaSetor(){
	var set_codigo_log = jQuery("#set_codigo_logado").val();
	var uni_codigo = jQuery("#uni_codigo").val();
	var linkroot = "<?= $_SESSION["linkroot"] ?>";
	var modulo = "<?= $_SESSION["modulo"] ?>";
	var id_login = "<?= $id_login ?>";
	jQuery('#setor option[value!=""]').remove();
	jQuery("#setor").append("<option value='c' readonly>Carregando...</option>");
	jQuery.ajax({
		url: linkroot+modulo+"alteraSetorPorUnidade.php",
		type: "POST",
		data: { uni_codigo: uni_codigo, set_codigo_logado: set_codigo_log, usr_codigo: id_login },
		beforeSend: () => {
			console.log(id_login)
		},
		success: function(txt){
			jQuery('#setor option[value!=""]').remove();
			if (txt.length>0) {
				jQuery('#setor-hidden').remove();
				jQuery('label[for=setor]').show();
				jQuery('#setor').show();
				jQuery("#setor").append(txt);
			} else {
				jQuery('label[for=setor]').hide();
				jQuery('#setor').hide();
				jQuery("#uni_codigo_t").append("<input type='hidden' name='setor' id='setor-hidden' value='0' />");
			}
		}
	});
	
	atualizaEspecialidade();
}

function atualizaEspecialidade(){
	var esp_codigo_log = jQuery("#esp_codigo_logado").val();
	var uni_codigo = jQuery("#uni_codigo").val();
	var linkroot = "<?= $_SESSION["linkroot"] ?>";
	var modulo = "<?= $_SESSION["modulo"] ?>";
	var id_login = "<?= $id_login ?>";
	jQuery('#esp_codigo option[value!=""]').remove();
	jQuery("#esp_codigo").append("<option value='c' readonly>Carregando ...</option>");
	jQuery.ajax({
		url: linkroot+modulo+"alteraEspecialidadePorUnidade.php",
		type: "POST",
		data: { uni_codigo: uni_codigo, esp_codigo_logado: esp_codigo_log, usr_codigo: id_login },
		success: function(txt){
			jQuery("#esp_codigo").html(txt);
		}
	});
}

</script>

<?php	
session_start();
require_once $_SESSION[root] . $_SESSION[comum] . "class/formClass.php";
require_once $_SESSION[root] . $_SESSION[comum] . "class/commonClass.php";
require_once $_SESSION[root] . $_SESSION[comum] . "class/tableClass.php";
include_once $_SESSION[root] . $_SESSION[comum] . "library/php/db.inc.php";
include_once $_SESSION[root] . $_SESSION[comum] . "library/php/debug.inc.php";
//debug($_REQUEST,"antesIndex.php", $id_login);



$form = new classForm();
$common = new commonClass();
$table = new tableClass();
echo $common->incJquery();

// Unidade com CNES, descomentar para utilizar
$sql = "SELECT 
			uni.uni_codigo,uni.uni_desc 
		FROM 
			unidade AS uni
		JOIN unidade_usuarios uu
		  on uu.uni_codigo=uni.uni_codigo
	  where usr_codigo = $id_login
	    AND cnes_ativo = 'A'
		ORDER BY uni_desc";
// die($sql);
$queryUni = pg_query($sql);
// Unidade Antiga SEM CNES

if($queryUni == NULL){
	$sql = "SELECT uni_codigo, uni_desc FROM unidade ORDER BY uni_desc";
	$queryUni = pg_query($sql);
}

//print_r(pg_fetch_assoc($queryUni));

//uni_codigo
//$unidade_codigo = "SELECT uni.uni_codigo FROM unidade";

// Especialidade
$sql = "SELECT me.esp_codigo,esp_nome,* 
		  FROM medico_especialidade me
          JOIN usuarios usr
            ON usr.usr_codigo = me.med_codigo
          JOIN especialidade e
            ON me.esp_codigo = e.esp_codigo
         WHERE usr_codigo = " . $id_login .
		"  AND mes_ativo != 'I'";
//die($sql);

$queryEsp = pg_query($sql);

// Setor
$sql = "SELECT * FROM setor s
		  JOIN usuarios_setores us
		    ON s.set_codigo = us.set_codigo
	     WHERE us.usr_codigo = {$id_login}
		 ORDER BY set_nome";
$querySet = pg_query($sql);

// Logon
$sql = "SELECT * FROM logon WHERE id_login=" . $id_login;
$queryLog = pg_query($sql);
$logon = pg_fetch_array($queryLog);

$tabela .= $form->openForm("auth_pass.php" . (isset($_GET['popup']) ? "?popup=1" : ""), "POST", "formSetor");

// último setor logado
$tabela .= $form->hiddenForm("set_codigo_logado", $logon['cod_setor'], "set_codigo_logado");
$tabela .= $form->hiddenForm("esp_codigo_logado", $logon['esp_codigo'], "esp_codigo_logado");

// Unidade
if (pg_num_rows($queryUni) > 1) {
	$tabela .= $form->inputSelect("uni_codigo", null, "Estabelecimento", $queryUni, "onchange='atualizaSetor()'", "uni_codigo_t", $logon['uni_codigo'], "style=width:320px;", 'Selecione', null, 'N', 'S');
} else {
	$unidade = pg_fetch_array($queryUni);
	
	$tabela .= $form->hiddenForm("uni_codigo", $unidade['uni_codigo']);
	$tabela .= $form->inputText("uni_desc", $unidade['uni_desc'], "Unidade", 58, null, null, 'text', 'S');
}

// Especilidade
/*if(pg_num_rows($queryEsp) > 1){
	$tabela .=$form->inputSelect("esp_codigo", NULL,"Especialidade",null,null,"esp_codigo",$logon['esp_codigo'],"style=width:320px;",'Selecione',NULL,'N','S');
} else{
	if(pg_num_rows($queryEsp)){
		$especialidade = pg_fetch_array($queryEsp);
		$tabela .=$form->hiddenForm("esp_codigo", $especialidade['esp_codigo']);
		$tabela .=$form->inputText("esp_nome",null,"Especilidade",58,null,null,'text','S');		
	} else {
	// se n�o houver especialidade, n�o solicitar	
		$tabela .=$form->hiddenForm("esp_codigo", 0);
	}
}*/

$tabela .= $form->inputSelect("esp_codigo", null, "Especialidade", null, null, "esp_codigo", $logon['esp_codigo'], "style=width:320px;", 'Selecione', null, 'N', 'S');

$tabela .= $form->inputSelect("setor", null, "Setor", null, null, "setor", $logon['cod_setor'], "style=width:320px;", 'Selecione', null, 'N', 'S');
// Setor
/*if(pg_num_rows($querySet) > 1){
	$tabela .=$form->inputSelect("setor", NULL,"Setor",$querySet,null,"setor",$logon['cod_setor'],"style=width:320px;",'Selecione',NULL,'N','S');
} else{
	if(pg_num_rows($querySet)){
		$setor = pg_fetch_array($querySet);
		$tabela .=$form->hiddenForm("setor", $setor['set_codigo']);
		$tabela .=$form->inputText("set_nome",trim($setor['set_nome']),"Setor",58,null,null,'text','S');		
	} else {
		// se não houver setor, não solicitar	
		$tabela .=$form->hiddenForm("setor", 0);		
	}
*/

$tabela .= $form->hiddenForm("user", "$usr_login");
$tabela .= $form->hiddenForm("pass", "$usr_senha");
$tabela .= $form->hiddenForm("model", "$model");
$tabela .= $form->hiddenForm("open", "$open");
$tabela .= $form->hiddenForm("tp", "$tp");
$tabela .= $table->openTable('lista', '100%');
$tabela .= $table->closeTable();



$options = array(
	"titulo" => "Escolha seu estabelecimento/setor",
	"size" => 700,
	"botao" => "OK",
	"js" => "onClick=document.formSetor.submit()",
	"canClose" => false
);
echo $common->openModal($options);
echo $tabela;
//echo $tabela2; 
echo $common->closeModal();

$tabela .= $form->closeForm();

?>