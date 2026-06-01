function buscar_municipio(valor, acao)
{
	
        url = "buscar_municipio.php?palavra="+valor+"&acao="+acao;
        ajax_tudo(url, popular_municipio);
        validaBusca('lista_municipios').style.display = '';
        validaBusca('table_municipios').innerHTML = '';
        validaBusca('lista_municipios_carregando').style.display = '';
}
function popular_municipio(txt)
{
        try {
                t = validaBusca('table_municipios');
                validaBusca("lista_municipios_carregando").style.display = 'none';
                t.innerHTML = txt;
        } catch(e) {
                alert(e);
        }
}
function passar_cidade(ibge, cidade, uf)
{
        validaBusca("cid_codigo").value = ibge;
        validaBusca("cid_nome").value = cidade;
        validaBusca('lista_municipios').style.display = 'none';
        validaBusca('cid_nome').focus();
}