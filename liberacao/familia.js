/* Javascript do arquivo apac.PHP */

function hotkey(eventname)
{
	// ESC esconde todas as janelas
	if( eventname.keyCode == 27 )
	{
		esconde_janela( 'janela_paci_conteudo' );
		//alert('esconde td mundo');
		return false;
	}
}

/** PACIENTES ------------------------------------------------------------------
----------------------------------------------------------------------------- */
function init_paci(id_login)
{
	ajax_tudo( 'familia_popup.php?id_login='+id_login, conteudo );
}

function conteudo(txt)
{
	//alert(txt);
	//document.getElementById('janela_paci_conteudo').innerHTML = txt;
	document.getElementById('janela_paci_conteudo').innerHTML = txt;
}

function busca_ibge(id_login)
{
	var pc = document.getElementById('pac_palavra_chave');
	var endereco = 'familia_popup.php?id_login='+id_login+'&palavra_chave='+pc.value+'&acao=busca';
	
	//alert(endereco);
	janela_carregando( 'janela_paci_conteudo' );
	/*ajax_tudo( endereco, busca_ibge2 );*/
	ajax_tudo( endereco, conteudo );
	return false;
}
function busca_ibge2(txt)
{
	if(txt) alert(txt);
}


// -------------


function conteudo_nasc(txt)
{
	//alert(txt);
	document.getElementById('janela_paci_conteudo').innerHTML = txt;
}

function busca_ibge2_nasc(txt)
{
	if(txt) alert(txt);
}

function add_cod_ibge(cid_nome,cid_codigo,ibge)
{

	document.familia.cid_codigo_ibge.value=ibge;
	document.familia.cid_codigo.value=cid_codigo;
	document.familia.cid_nome.value=cid_nome;
	esconde_janela('janela_paci');
	//location.hash = '#'+ibge;

}