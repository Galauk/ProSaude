/*
 *
 * @version Renato 30/5/2007 - 9:45
*/


function buscaCad(arquivo,div){
    url = arquivo+'?acao=buscar&deslocamento=0&atual=1';
	exec_ajax(url,div+'_conteudo');
	document.getElementById(div).style.top=window.scrollY;
	mostra_janela(div);
	
}

function buscaCad2(arquivo,div,parametro){
    url = arquivo+'?acao=buscar&deslocamento=0&atual=1&parametro='+parametro;
	exec_ajax(url,div+'_conteudo');
	document.getElementById(div).style.top=window.scrollY;
	mostra_janela(div);
	
}

function pesqCad(palavra,arquivo,div){
	url = arquivo+'?palavra='+palavra+'&acao=buscar&deslocamento=0&atual=1';
	exec_ajax(url,div+'_conteudo');
}

function pesqCad2(palavra,arquivo,div,parametro){
	url = arquivo+'?palavra='+palavra+'&acao=buscar&deslocamento=0&atual=1&parametro='+parametro;
	exec_ajax(url,div+'_conteudo');
}

function voltarPagina(palavra,atual,deslocamento,limite,arquivo,div)
{
	atual--;
	deslocamento-=limite;
	url=arquivo+'?palavra='+palavra+'&atual='+atual+'&deslocamento='+deslocamento+'&acao=buscar';
	exec_ajax(url,div+'_conteudo');
}

function irPagina(palavra,atual,deslocamento,limite,arquivo,div)
{	
	atual++;
	deslocamento+=limite;
	url=arquivo+'?palavra='+palavra+'&atual='+atual+'&deslocamento='+deslocamento+'&acao=buscar';
	exec_ajax(url,div+'_conteudo');
}
