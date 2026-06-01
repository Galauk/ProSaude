<?php
	session_start(); 
?>
/** Arquivo */

/* eventos */
$("lupa_ibge").onclick = function()
{
	mostra_janela( "jan_cidade" );
	ajax_tudo( "layout_critica_popup.php", conteudo );	
}

$("tipo_filtro").onchange = function()
{
	$("data_fim").disabled = ( this.value != 3 );
}

$("form_filtro").onsubmit = function()
{
	var ibge = $("ibge"), tipo = $("tipo_filtro"), di = $("data_ini"), df = $("data_fim");

	if( ! valida( "ibge", "Codigo IBGE" ) ) return false;	
	if( ! Verifica_Data( "data_ini", 1 ) ) return false;
	if( tipo.value == 3 && ! Verifica_Data( "data_fim", 1 ) ) return false;

	var endereco = "layout_critica_op.php?id_login=<?php print $id_login; ?>&ibge="+ibge.value+"&tipo_filtro="+tipo.value+"&data_ini="+di.value+"&data_fim="+df.value;
	var d = $("resposta");

	d.innerHTML = "<p><img src=\'<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/loading.gif\' alt=\'Loading...\' style=\'vertical-align:middle;\'/> Carregando...</p><p><em>Esta opera&ccedil;&atilde;o pode levar algum tempo...</em></p>"
	ajax_tudo( endereco, form_callback );
	//alert( endereco );

	return false;
}

$("ibge").onchange = function()
{
	var endereco = "layout_critica_popup.php?tipo=cid_codigo_ibge&palavra_chave="+this.value;
	mostra_janela("jan_cidade");
	ajax_tudo( endereco, conteudo );
	return false;
}

/* funcoes */
function form_callback ( txt )
{
	//alert( txt );
	$("resposta").innerHTML = txt;
}

function conteudo( txt )
{
	//alert(txt);
	$("jan_cidade_conteudo").innerHTML = txt;
}

function valida_form_busca()
{
	var tipo = $("tipo_busca"), pc = $("palavra_chave");
	if( ! valida("palavra_chave", "Busca") ) return false;
	var endereco = "layout_critica_popup.php?tipo="+tipo.value+"&palavra_chave="+pc.value;
	//alert( endereco );
	ajax_tudo( endereco, conteudo );
	return false;
}

function add_ibge( ibge, cid_nome )
{
	$("ibge").value = ibge;
	$("cid_nome").innerHTML = cid_nome;
	esconde_janela( "jan_cidade" );
}