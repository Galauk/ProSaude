function init_paci(id_login)
{
	ajax_tudo( 'rua_popup_jean.php?id_login='+id_login+'&acao=buscar&deslocamento=0&atual=1', conteudo );
}
function conteudo(txt)
{
	document.getElementById('localiza_rua_conteudo').innerHTML = txt;
}
function pesqCad(palavra,arquivo,div){
	url = arquivo+'?palavra='+palavra+'&acao=buscar&deslocamento=0&atual=1';
	exec_ajax(url,div+'_conteudo');
}
function insere_rua(codigo,nome)
{
	document.getElementById('rua_codigo').value = codigo;
	document.getElementById('med_endereco').value = nome;
	esconde_janela('localiza_rua');
}
function init_med(id_login)
{
	ajax_tudo( 'rua_popup_jean.php?id_login='+id_login+'&acao=buscar&deslocamento=0&atual=1', conteudo );
}