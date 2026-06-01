<?php
/**
 * alterado na query $select_total_entrada vm.mov_data de >= para apenas >
 * Filtrando os produtos por setor ( X unidade ) quando usuario possui unidade
 * Alterando ordenação da consulta de produtos
 * no SELECT da linha 513 foi adicionado um campo com a data
 * apenas para fazer a ordenação por data.
 * -- Retirado o Botão Fechamento Mensal
 */
?>
<fieldset><legend>MATERIAIS</legend> <script language="JavaScript"
	type="text/javascript" src="funcoes.js"></script> <script
	language="JavaScript" type="text/javascript" src="ajax_motor.js"></script>
<script type='text/javascript'
	src='/WebSocialComum/library/js/jquery-1.6.2.min.js'></script> <script
	type='text/javascript'
	src='/WebSocialComum/library/js/tiny_mce/jquery.tinymce.js'></script> <script>
var gdtInicial
var gdtFinal
var gmedico
var gespecial
var gunidade
var gTipAgenda
var gMostAgente
var gHoje
var maxDay = new Array(31,28,31,30,31,30,31,31,30,31,30,31);


function validaNome(){
	pro_nome = document.getElementById("pro_nome").value;
	if(pro_nome == ''){
		alert('Preencha o nome do produto');
		return false;
	}
	document.getElementById("form").submit();
	
}
function CheckDate(d,t) {
   date_array = new Array(3);
   date_array[0]=(String(d).substr(6,2))    // dia
   date_array[1]=(String(d).substr(4,2))    // mes
   date_array[2]=(String(d).substr(0,4))    // ano

   if (date_array[0] > maxDay[date_array[1]-1]) {
       alert ("Dia invalido da data " + t)
       return 1;
   }
   if (date_array[1] > 12) {
       alert ("Mes invalido da data " + t)
       return 1;
   }
   if (date_array[2] < 1990) {
       alert ("Ano invalido da data " + t)
       return 1;
   }
}


function CheckCall() {

   gdtInicial	=document.frm_consulta.dt_inicial.value;
   gdtFinal	=document.frm_consulta.dt_final.value;
   gProduto	=document.frm_consulta.pro_codigo.value;
   gCE		=document.frm_consulta.centro_estocador.value;

   if (gdtInicial == '') {
       alert ("Informe Data Inicio");
       document.frm_consulta.dt_inicial.focus();
       return false;
   }
    if (gdtFinal == '') {
       alert ("Informe Data Final");
       document.frm_consulta.dt_final.focus();
       return false;
   }
   var d1=gdtInicial;
   var d2=gdtFinal;
   for (var i = 0; i < d1.length; i++) {
        if (d1.charAt(i) == "-") {
           var dat1=parseInt(d1.split("-")[2].toString()+d1.split("-")[1].toString()+d1.split("-")[0].toString())
        }
        else
        if (d1.charAt(i) == "/") {
           var dat1=parseInt(d1.split("/")[2].toString()+d1.split("/")[1].toString()+d1.split("/")[0].toString())
        }
   }
	for (var i = 0; i < d2.length; i++) {
        if (d2.charAt(i) == "-") {
           var dat2=parseInt(d2.split("-")[2].toString()+d2.split("-")[1].toString()+d2.split("-")[0].toString())
        }
        else
        if (d2.charAt(i) == "/") {
           var dat2=parseInt(d2.split("/")[2].toString()+d2.split("/")[1].toString()+d2.split("/")[0].toString())
        }
   }
   if (CheckDate(dat1,"INICIAL")==1) {
       document.frm_consulta.dt_inicial.focus()
       return false;
   }
	if (CheckDate(dat2,"FINAL")==1) {
       document.frm_consulta.dt_final.focus()
       return false;
   }

   return true
}

function CheckCall2() {

   gdtInicial	=document.frm_consulta.dt_inicial.value;
   gdtFinal	=document.frm_consulta.dt_final.value;
   gProduto	=document.frm_consulta.pro_codigo.value;
   gCE		=document.frm_consulta.centro_estocador.value;

   if (gdtInicial == '') {
       alert ("Informe Data Inicio");
       document.frm_consulta.dt_inicial.focus();
       return false;
   }
    if (gdtFinal == '') {
       alert ("Informe Data Final");
       document.frm_consulta.dt_final.focus();
       return false;
   }
   var d1=gdtInicial;
   var d2=gdtFinal;
   for (var i = 0; i < d1.length; i++) {
        if (d1.charAt(i) == "-") {
           var dat1=parseInt(d1.split("-")[2].toString()+d1.split("-")[1].toString()+d1.split("-")[0].toString())
        }
        else
        if (d1.charAt(i) == "/") {
           var dat1=parseInt(d1.split("/")[2].toString()+d1.split("/")[1].toString()+d1.split("/")[0].toString())
        }
   }
	for (var i = 0; i < d2.length; i++) {
        if (d2.charAt(i) == "-") {
           var dat2=parseInt(d2.split("-")[2].toString()+d2.split("-")[1].toString()+d2.split("-")[0].toString())
        }
        else
        if (d2.charAt(i) == "/") {
           var dat2=parseInt(d2.split("/")[2].toString()+d2.split("/")[1].toString()+d2.split("/")[0].toString())
        }
   }
   if (CheckDate(dat1,"INICIAL")==1) {
       document.frm_consulta.dt_inicial.focus()
       return false;
   }
	if (CheckDate(dat2,"FINAL")==1) {
       document.frm_consulta.dt_final.focus()
       return false;
   }

  window.open('materiais_itens_rel.php?dt_inicial='+gdtInicial+'&dt_final='+gdtFinal+'&pro_codigo='+gProduto+'&centro_estocador='+gCE,
		null,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
   return false
}
function validaForm(){
	codProduto = document.getElementById('pro_codigo').value;
	codSetor = document.getElementById('set_codigo').value;

	url = "validaProdutoSetor.php?pro_codigo="+codProduto+"&set_codigo="+codSetor;

	ajax = ajaxInit();

	if(ajax)
	{	
		ajax.open("GET", url, true);
		ajax.onreadystatechange = function()
		{
			if(ajax.readyState == 4)
			{
				if(ajax.status == 200)
				{
					txt = ajax.responseText;
					
					if (txt == '0'){
						document.form.submit();
						return true;
					}else{
						alert('Este produto já está registrado no setor escolhido.');
						return false;
					}
				}
				else
				{
					alert('Arquivo:'+url+'\nErroNo.:' + ajax.status + '\nMsg:' + ajax.statusText);
				}
			}
		}
		ajax.send(null);
	}
}

// quando pro_tipo for "M" (medicamento) deve aparecer o textarea (TinyMCE) para preencher a bula
 function mostrarBula(tipo){
	if(tipo == "M"){
		document.getElementById("bula").style.display = 'table-row';
		return true;
	} else {
		document.getElementById("bula").style.display = 'none';
		return false;
	}
}

jQuery(function(){
	jQuery('textarea.tinymce').tinymce({
		// Location of TinyMCE script
		script_url : '/WebSocialComum/library/js/tiny_mce/tiny_mce.js',

		// General options
		//theme : "../css/tinymce/advanced",
		theme : "advanced",		
		skin : "o2k7",
		language : 'pt',
		//plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

		// Theme options
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "",
		theme_advanced_buttons3 : "",
		theme_advanced_buttons4 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true
	});

	mostrarBula(jQuery("#pro_tipo").val());
});

</script> <?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>


include_once "authlib.inc.php";

session_start();
include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";


verauth($id_login);
require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";


cabecario();

$common = new commonClass();

//------------------------------------------------------------------>


$stmt = "SELECT uni_codigo FROM logon WHERE id_login = $id_login";

$stmt = db_query($stmt);
$dados = pg_fetch_array($stmt);

//------------------------------------------------------------------>

reglog($id_login,"Acessando Materiais");

//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>

if(empty($acao)) {

$data = date("d/m/Y");
	//
	//-> Botoes
	echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
			<form method=post action=$PHP_SELF>
				<input type=hidden name=id_login value=$id_login>
		     
	     		<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
					<tr>
						<td width=10 align=right>Buscar:</td>
						<td width=120>
							<input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\">
						</td>
						<td><input type=submit value='Localizar' class='box'></td>
					</tr>
				</table>
			</form>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";

	//
	//-> Listando
	/*
	 * Retirada a opcao de listar diretamente os dados de produtos
	 */
		echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
                   <tr>
                    <td>
                     <fieldset>
                      <legend>Listando os 15 Ultimos Produtos Cadastrados</legend>
                       <table class=lista border=0>
                        <tr bgcolor=F9f9f9>
                          <th width=40>Codigo</th>
                          <th width=500>Nome</th>
                          <th colspan=5>&nbsp;</th>";

if(!empty($palavra_chave)) {
	$stmt = "SELECT 
				pro_codigo, pro_nome from produto ".
            "WHERE 
				(retira_acentos(pro_nome) ilike retira_acentos('%$palavra_chave%')".
				(is_numeric($palavra_chave)? "OR pro_codigo = $palavra_chave" : "").") AND 
				(pro_situacao = '0' OR pro_situacao = '')".
	        "ORDER BY 
				pro_nome";
} else {
		$stmt = "SELECT pro_codigo, pro_nome,pro_situacao FROM produto WHERE (pro_situacao = '0' OR pro_situacao = '')  ORDER BY pro_codigo DESC limit 15 ";
}
	$sql = pg_query($stmt);
	$num = pg_num_rows($sql);
	if($num=="0") { $resp = "Nenhum Registro encontrado com \"$palavra_chave\""; }
	if($num=="1") { $resp = "Encontrado <b>$num</b> Registro com \"$palavra_chave\""; }
	if($num>"1") { $resp = "Encontrados <b>$num</b> Registros com \"$palavra_chave\""; }

		while($row=pg_fetch_array($sql))
		{
			echo "<tr>
                         <td align=center>$row[pro_codigo]</td>
                         <td>$row[pro_nome]</td>
                         <td width=10><a href=./produtosinativados.php?acao=add&pro_codigo=$row[pro_codigo]><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/ativar_produto.png border=0></a></td>
                       </tr>";
		}
	echo "</tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table>";
}
//die(var_dump($_GET['acao']));
if($acao=="add") {
	//die(var_dump('here'));
	$sql = pg_query("UPDATE produto set pro_situacao = 'A' WHERE pro_codigo='$pro_codigo'");
	reglog($id_login,"Re-Ativando Material Cod.: $pro_codigo");
	msg($id_login,$acao,$sql);
}		
		
		
?>