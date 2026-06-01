function buscar_unidade(valor, acao)
{
        url = "buscar_unidade.php?palavra="+valor+"&acao="+acao;
        ajax_tudo(url, popular_unidade);
        validaBusca('lista_unidade').style.display = '';
        validaBusca('table_unidade').innerHTML = '';
        validaBusca('lista_unidade_carregando').style.display = '';
}
function popular_unidade(txt)
{
        try {
                t = validaBusca('table_unidade');
                validaBusca("lista_municipios_carregando").style.display = 'none';
                t.innerHTML = txt;
        } catch(e) {
                alert(e);
        }
}
function passar_unidade(uni_desc, uni_loc)
{
	
        validaBusca("uni_codigo").value = uni_loc;
        validaBusca("uni_nome").value = uni_desc;
        validaBusca('lista_unidade').style.display = 'none';
        validaBusca('uni_nome').focus();
}