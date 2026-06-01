<?php
include_once "../global.php";
include_once "../../WebSocialComum/library/php/funcoes.db.php";
?>
<html>
<head>
<link href="/WebSocialSaude/zf/public/css/redmond/jquery-ui-1.8.16.custom.css" media="screen" rel="stylesheet" type="text/css" >
<link href="/WebSocialSaude/zf/public/css/geral.css" media="all" rel="stylesheet" type="text/css" >

	<style type="text/css">
		
a.ui-button{
	padding: 5px 10px 0px;
	background-image: url('/WebSocialSaude/zf/public/images/btn-bg.png');
	background-repeat: round;
    background-size: contain;
	border: 1px solid #ACACAC;
	margin-bottom: 3px;
	color: #000;
}


a.ui-button div{
	float: left;
	background-image: url('/WebSocialSaude/zf/public/images/btn-bg-invert.png');
	background-repeat: no-repeat;
	background-position: right;
	margin: 0 10px 0 3px;
	padding: 1px 0 0 0;
	width: 30px;
}

a.ui-button div img{
	float: left;
	margin: 1px 5px 3px 0px;
}



.ui-button { display: inline-block; position: relative; padding: 0; margin-right: .1em; text-decoration: none !important; cursor: pointer; text-align: center; zoom: 1; overflow: visible; } /* the overflow property removes extra width in IE */
 .ui-button-icon-only { width: 2.2em; } /* to make room for the icon, a width needs to be set here */
 button.ui-button-icon-only { width: 2.4em; } /* button elements seem to need a little more width */
 .ui-button-icons-only { width: 3.4em; }
 button.ui-button-icons-only { width: 3.7em; }
 
 /*button text element */
 .ui-button .ui-button-text { display: block; line-height: 1.4;  }
 .ui-button-text-only .ui-button-text { padding: .4em 1em; }
 .ui-button-icon-only .ui-button-text, .ui-button-icons-only .ui-button-text { padding: .4em; text-indent: -9999999px; }
 .ui-button-text-icon-primary .ui-button-text, .ui-button-text-icons .ui-button-text { padding: .4em 1em .4em 2.1em; }
 .ui-button-text-icon-secondary .ui-button-text, .ui-button-text-icons .ui-button-text { padding: .4em 2.1em .4em 1em; }
 .ui-button-text-icons .ui-button-text { padding-left: 2.1em; padding-right: 2.1em; }
 /* no icon support for input elements, provide padding by default */
 input.ui-button { padding: .4em 1em; }
 
 /*button icon element(s) */
 .ui-button-icon-only .ui-icon, .ui-button-text-icon-primary .ui-icon, .ui-button-text-icon-secondary .ui-icon, .ui-button-text-icons .ui-icon, .ui-button-icons-only .ui-icon { position: absolute; top: 50%; margin-top: -8px; }
 .ui-button-icon-only .ui-icon { left: 50%; margin-left: -8px; }
 .ui-button-text-icon-primary .ui-button-icon-primary, .ui-button-text-icons .ui-button-icon-primary, .ui-button-icons-only .ui-button-icon-primary { left: .5em; }
 .ui-button-text-icon-secondary .ui-button-icon-secondary, .ui-button-text-icons .ui-button-icon-secondary, .ui-button-icons-only .ui-button-icon-secondary { right: .5em; }
 .ui-button-text-icons .ui-button-icon-secondary, .ui-button-icons-only .ui-button-icon-secondary { right: .5em; }
 
 /*button sets*/
 .ui-buttonset { margin-right: 7px; }
 .ui-buttonset .ui-button { margin-left: 0; margin-right: -.3em; }
 
.tipo {
	width:250px !important;
	height:30px !important;
	display:inline-block;
	float: left;
    font-size: 16px !important;
    line-height: 1.25 !important;
  margin-left:5px  !important;
  font-weight: bold !important;
}
select {
  font-family: "Ubuntu", sans-serif  !important;
  font-size: 16px !important;
  border-radius: 5px  !important;
  width:98%  !important;
  height:30px  !important;
  margin-left:5px  !important;
  font-weight: bold !important;
}

input[type="text"] {
  font-family: "Ubuntu", sans-serif  !important;
  font-size: 16px !important;
  border-radius: 5px  !important;
  height:30px  !important;
  margin-left:5px  !important;
  font-weight: bold !important;
  background-color: #d4e4f1 !important;
  border:1px solid #81b7b7 !important;
}
legend {
  font-family: "Ubuntu", sans-serif  !important;
  font-size: 16px !important;
  font-weight: bold !important;
 }

</style>

<title>Porta de Entrada</title>
<?php
	$id_login = $_SESSION["id_login"];
	$usr_codigo = $_SESSION["logon"]["usr"]->usr_codigo;


$sqlPermissao = "SELECT * from usuarios_permissoes as usrp
JOIN permissoes as perm 
ON usrp.perm_codigo = perm.perm_codigo 
WHERE usr_codigo = $id_login
AND perm_descricao = 'PORTA DE ENTRADA'
";
					$queryPermissao = pg_query($sqlPermissao);
					$rr = pg_fetch_array($queryPermissao);
/*					if ($rr['perm_set'] != 'S') {
						die('<br><b>USUÁRIO SEM PERMISSÃO</b>
							<br>
							<a target="_top" href="/WebSocialSaude/auth.php" class="ui-button login">Entrar com outro usuário</a><br />');
						}*/

//echo "<pre>".print_r($_REQUEST,1);
	
	$sqlAgenda = "SELECT *,
						 to_char(usu_datanasc,'DD/MM/YYYY')as data 
  				    FROM agendamento a
  				    JOIN usuario u 
  				      ON u.usu_codigo = a.usu_codigo
  			        JOIN especialidade e
  			          ON e.esp_codigo = a.esp_codigo
  				   WHERE age_codigo = $age_codigo";
	$queryAgenda = pg_query($sqlAgenda);
	$regAgenda = pg_fetch_array($queryAgenda);
	
	$common = new commonClass();
	$form = new classForm();
	$table = new tableClass();
	echo "<link type=\"text/css\" href=\"".LINKSAUDE."/estiloPE.css\" rel=\"stylesheet\"/>";
	echo $common->incJquery();
	//echo "<pre>".print_r($_SESSION,1);
		$sqlMedico = "SELECT DISTINCT(u.usr_codigo),u.usr_nome

		-- coalesce((usr_codsis),'') || ' - ' || u.usr_nome as nome
						FROM usuarios AS u
						JOIN medico_especialidade AS me
					  	  ON me.med_codigo=u.usr_codigo
						
					   WHERE 
							u.usr_tipo_medico IN ('M','E','D','A','P')	AND
							u.usr_ativo = 'S'
							AND me.uni_codigo = ".$_SESSION['uni_codigo']."
					   ORDER BY u.usr_nome;";
		//echo $sqlMedico;
		$optionEsp = array(
			"nome" => "especialidade",
			"valor" => $regAgenda[esp_codigo],
			"option" => ($regAgenda[esp_codigo]== null ? "Selecione um m�dico" : "$regAgenda[esp_nome]"),
			"disabledFirst" => ($regAgenda[esp_codigo]== null ? "S" : ""),
			"idDiv"=>null,
			"sel"=>$regAgenda[esp_codigo]
			
		);
		$optionMed = array(
			"nome" => "medico",
			"valor" => NULL,
			"sql" => $sqlMedico,
			"js" => NULL,
			"idDiv"=>NULL,
			"sel"=>$regAgenda[med_codigo]
		);
		

		$selectEspecialidade = $form->inputSelect($optionEsp);
		$selectMedico = $form->inputSelect($optionMed);
		
	
?>
<link rel="stylesheet" href="/WebSocialSaude/lib/themes/base/jquery.ui.all.css">
<link rel="stylesheet" href="/WebSocialSaude/lib/themes/ui-lightness/jquery-ui-1.8.10.custom.css">
<script type="text/javascript" src="/WebSocialComum/library/js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="/WebSocialSaude/lib/ui/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="/WebSocialComum/library/js/jquery.buscar.js"></script>
<script type="text/javascript">
function guiaDeComparecimento(age_codigo){
	var url ="guiaDeComparecimento.php?age_codigo="+age_codigo;
	window.open(url,null,'height=750,width=750,status=yes,toolbar=no,menubar=no,location=no');
}

function abrepaciente() {
        var usu_codigo = $("#usu_codigo").val();
        var link = "";


        // if(cadastro_aise == 1){
        link = "../zf/paciente/form-paciente/pessoa/" + usu_codigo + "/poupup/1";
        //}else{
        // link = "../../../../WebSocialSaude/paciente.php?acao=form&poupup=1&usu_codigo="+usu_codigo;
        //}
        window.open(link, "_blank", "scrollbars=1,height=800,width=900", 'width=850,height=700');
    }

//function editarPaciente(id){	
//	usu_codigo = document.getElementById('usu_codigo').value;	
//	var url ="../paciente.php?acao=form&usu_codigo="+usu_codigo+"&id_login="+id+"&porta=S";
//	window.open(url,null,'height=750,width=750,status=yes,toolbar=no,menubar=no,location=no');
//}
function wbio() {
	window.open( '../biometria/validar.php',
			 null,
			 'height=268,width=230,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes');
	}


function buscaEsp(){
	var url = "../selectEspDoMed.ajax.php?usr_codigo=" + $("#medico").val();
	
	$("#td-esp select")
	.html("<option>Carregando...</option>")
	.attr("disabled","disabled")
	.parents("div#td-esp")
	.load(url,function(){
		$("#td-esp select").removeAttr("disabled");

		if( $("#especialidade option").size() == 2 ){
			if($("#usu_codigo").val())
				$("#final a").focus();
			else
				$("#buscar").select();
		} else {
			$("#especialidade").focus();
		}
	});
}
$(function(){

	$("#medico").change(function(){
		buscaEsp();		
	}).focus();

	if($("#age_codigo").val() != ""){
		buscaEsp();
	}

	$("#buscar").buscar({
		callback: function(event, ui){
			var usu_codigo = $("#usu_codigo").val();

			if(ui.item){
				$("#iframe").show("normal").find("iframe").attr("src","historico.php?usu_codigo="+usu_codigo);
				$("#final a").focus();
			}
		}
	});

	$("#tabs").bind("tabsselect", function(event, ui) {
		var selectedTab = $("#tabs").tabs('option', 'selected');
		
		if(selectedTab == 1){
			window.location.href = window.location.href;
			event.preventDefault();
			return false;
		}
		  	
	});
			

	$("#final a").click(function(){
		if(!validarDados())
			return false;

		var array_valores = "";
		$.ajax({
			url: "registrar.php",
			type: "POST",
			data:{ 
				usu_codigo: $("#usu_codigo").val(),
				usr_codigo: $("#medico").val(),
				esp_codigo: $("#especialidade").val(),
				age_codigo: $("#age_codigo").val(),
				tpatend: $("#tpatend").val() 
			},
			success: function(r){
				window.console && console.log("registrar.php: "+r);
				if(r == 0)
					alert("Este paciente ja possui agendamento para hoje");

				$('#tabs').tabs( "select" , 1 );
				array_valores = r.split('-');
				if($("#cnes_tp_unid_id").val() == '05'){
					window.open('../zf/relatorio/usuario/guia-diagnostico-sem-historico/usu_codigo/'+$("#usu_codigo").val()+"/age_codigo/"+array_valores[2]+"/med_codigo/"+$("#medico").val(),'_blank');
				}
			}
		});
		
		return false;
	});

	function validarDados(){
		if($("#medico").val() == 0){
			alert("Selecione o m�dico.");
			$("#medico").focus();
			return false;
		}
		if(!$("#especialidade").val()){
			alert("Selecione a especialidade do m�dico.");
			$("#especialidade").focus();
			return false;
		}
		if(!$("#usu_codigo").val()){
			alert("Selecione um paciente.");
			$("#buscar").select();
			return false;
		}
		return true;
	}

});

function buscarPorUsuCodigo(usu_codigo){
	window.console && console.log("recebido: "+usu_codigo);
	$.ajax({
		url: '/WebSocialSaude/buscaGenerica.php?tipo=usu_cod_bio',
		datatype: 'JSON',
		type: 'GET',
		data:{
			term: usu_codigo
		},
		success: function(json){
			if (json && json[0].id) {
				for ( var i in json[0].data) {
					$("#" + i).val(json[0].data[i]);
				}
				window.console && console.log("achou: "+usu_codigo);
				var usu_codigo = $("#usu_codigo").val();
				
				$("#hist").show();
				$("#iframe").show("normal").find("iframe").attr("src","historico.php?usu_codigo="+usu_codigo);
				$("#final a").focus();
				
			} else {

			}
		}
	});
}

function teste() {
	var recebeValor = $("#usu_codigo").val();

	var url ="rel_prontuarioconsolidado.php?usu_codigo="+recebeValor;
	window.open(url,null,'height=750,width=750,status=yes,toolbar=no,menubar=no,location=no')
}

</script>
</head>
	<body style="background-color: #daebf9;">
	<br>
	<br>
	<br>
	<br>
	<?=$common->bodyTab('1'); ?>
	
	<form method="POST" style="margin-top: 48px;" action="" style="height:580px">
		<input type="hidden" name="usu_codigo" id="usu_codigo" value="<?=$regAgenda[usu_codigo];?>"/>
		<input type="hidden" name="age_codigo" id="age_codigo" value="<?=$regAgenda[age_codigo];?>"/>

<fieldset style="height: 149px;width: 700px;margin-left: 30%;border-radius: 7px;">
<legend style="color:#3E88C5">
    Buscar Paciente
</legend>
	<div style="display: inline-block; width: 500px!important">
		<label class="" style="font-size: 20px;">Nome: </label>	
		<input type="text" name="buscar" id="buscar" value="<?=$regAgenda[usu_nome]?>" style="">
	</div>
	<div style="display: inline-block;">
		<button  style="padding: 5px;border-radius: 4px;" type="button" onclick="teste()">Gerar relatório</button>
	</div>

</fieldset>


	</form>


	</body>
</html>
