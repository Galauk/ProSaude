function buscar_endereco(valor,acao)
{
	url = "busca_endereco.php?palavra="+valor+"&acao="+acao;
	ajax_tudo(url, popular_endereco);
	validaBusca('lista_endereco').style.display = '';
	validaBusca('table_endereco').innerHTML = '';
	validaBusca('lista_endereco_carregando').style.display = '';
}
function popular_endereco(txt)
{
	try {
			t = validaBusca('table_endereco');
			validaBusca("lista_endereco_carregando").style.display = 'none';
			t.innerHTML = txt;
	} catch(e) {
			alert(e);
	}
}
function passar_endereco(endereco,enf_codigo)
{
	validaBusca("endereco_fam").value = endereco;
	validaBusca("end_codigo").value = end_codigo;
	validaBusca('lista_endereco').style.display = 'none';
	validaBusca('endereco_fam').focus();
}