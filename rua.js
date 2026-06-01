/* Javascript do arquivo rua.php */

function hotkey(eventname)
{
	// ESC esconde todas as janelas
	if( eventname.keyCode == 27 )
	{
		esconde_janela( 'janela_rua_conteudo' );
		//alert('esconde td mundo');
		return false;
	}
}

// < ---------------- >
// < ---- > RUA < --- >
// < ---------------- >

function init_rua(id_login)
{
	var endereco_rua = 'rua_popup.php?id_login='+id_login;
	//alert( endereco_rua );
	ajax_tudo( endereco_rua , conteudo_rua );
}

function conteudo_rua(txt)
{
	//alert(txt);
	document.getElementById('janela_rua_conteudo').innerHTML = txt;
}

function busca_rua(id_login)
{
	var pc_rua = document.getElementById('rua_palavra_chave');
	var endereco_rua = 'rua_popup.php?id_login='+id_login+'&palavra_chave='+pc_rua.value+'&acao=busca';
	
	//alert(endereco_rua);
	//alert("id="+id_login);
	janela_carregando( 'janela_rua_conteudo' );
	ajax_tudo( endereco_rua, conteudo_rua );
	return false;
}
function busca_rua2(txt)
{
	if(txt) alert(txt);
}

function add_cod_rua(cod_rua, rua)
{

	document.getElementById('usu_end_cod_rua').value=cod_rua;
	document.getElementById('usu_end_rua').value=rua;
	
	esconde_janela('janela_rua');
	//location.hash = '#'+rua;

}
