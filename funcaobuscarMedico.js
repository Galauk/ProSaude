function buscar_medico(valor, acao)
{
	url = "../buscar_medicos.php?palavra="+valor+"&acao="+acao;
	ajax_tudo(url, popular_medico);
	validaBusca('lista_medico').style.display = '';
	validaBusca('table_medico').innerHTML = '';
	validaBusca('lista_medico_carregando').style.display = '';
}
function popular_medico(txt)
{
	try {
			t = validaBusca('table_medico');
			validaBusca("lista_medico_carregando").style.display = 'none';
			t.innerHTML = txt;
	} catch(e) {
			alert(e);
	}
}

function passar_medico(medico,codigo)
{	
	validaBusca("med_nome").value = medico;
	validaBusca("med_codigo").value = codigo;
	validaBusca('lista_medico').style.display = 'none';
	validaBusca('med_nome').focus();
}
