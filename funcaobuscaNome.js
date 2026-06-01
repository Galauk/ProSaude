function passar_usuario(codigo, nome, mae, data_nasc, cidade, prontuario)
{
        validaBusca("pac_codigo").value = codigo;
        validaBusca("pac_nome").value = nome;
        validaBusca("pac_nascimento").value = data_nasc;
		validaBusca("pac_mae").value = mae;       
        if(document.getElementById("pac_prontuario") != null)
        {
                validaBusca("pac_prontuario").value = prontuario;

        }
        validaBusca('lista_nomes').style.display = 'none';
        validaBusca('pac_nome').focus();
}

