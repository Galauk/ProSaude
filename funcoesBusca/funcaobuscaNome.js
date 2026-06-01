function passar_usuario(codigo, nome, mae, data_nasc, cidade, prontuario)
{
		
        validaBusca("pac_codigo").value = codigo;
        validaBusca("pac_nome").value = nome;
        validaBusca("pac_nascimento").value = data_nasc;        
        if(document.getElementById("pac_prontuario") != null)
        {
                validaBusca("pac_prontuario").value = prontuario;

        }
        validaBusca('lista_nomes').style.display = 'none';
        validaBusca('pac_nome').focus();
}

function buscar_nome(valor, acao)
{
        url = "../buscar_nomes.php?palavra="+valor+"&acao="+acao;
        ajax_tudo(url, popular_nome);
        validaBusca('lista_nomes').style.display = '';
        validaBusca('table_nomes').innerHTML = '';
        validaBusca("lista_carregando").style.display = '';
}