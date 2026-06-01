function buscar_enfermeiro(valor,acao)
{
	url = "../busca_enfermeiros.php?palavra="+valor+"&acao="+acao;
	ajax_tudo(url, popular_enfermeiro);
	validaBusca('lista_enfermeiro').style.display = '';
	validaBusca('table_enfermeiro').innerHTML = '';
	validaBusca('lista_enfermeiro_carregando').style.display = '';
}
function popular_enfermeiro(txt)
{
	try {
			t = validaBusca('table_enfermeiro');
			validaBusca("lista_enfermeiro_carregando").style.display = 'none';
			t.innerHTML = txt;
	} catch(e) {
			alert(e);
	}
}
function passar_enfermeiro(enfermeiro,enf_codigo)
{
	validaBusca("enfermeiro_nome").value = enfermeiro;
	validaBusca("enf_codigo").value = enf_codigo;
	validaBusca('lista_enfermeiro').style.display = 'none';
	validaBusca('enfermeiro_nome').focus();
}