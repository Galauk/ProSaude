function validaBusca( )
{
	var A  = new Array;
	for( var i = 0; i < arguments.length; i++ )
	{
		var obj = document.getElementById( arguments[i] );
		if( ! obj )
		{
			alert("O elemento '" + arguments[i] + "' nao foi encontrado !");
			return null;
		}
		A.push( obj );
	}
	return ( A.length == 1 ? A[0] : A ); 
}
function $F( )
{
	var A = new Array;
	for( var i = 0; i < arguments.length; i++ )
	{
		var obj = validaBusca( arguments[i] );
		if( ! obj ) continue;
		A.push( obj.value );
	}
	return ( A.length == 1 ? A[0] : A ); 
}



function popular_nome(txt)
{
        try {
                t = validaBusca('table_nomes');
                validaBusca("lista_carregando").style.display = 'none';
                t.innerHTML = txt;
        } catch(e) {
                alert(e);
        }
}
function trocar_cor(id, id2)
{
        campo = validaBusca(id);
        campo.style.background = "#ABCDEF";
        if(id2 != null)
        {
                validaBusca(id2).style.display = '';
        }
}

function retirar_cor(id, id2)
{
        campo = validaBusca(id);
        campo.style.background = "#FFFFFF";
        if(id2 != null)
        {
                validaBusca(id2).style.display = 'none';
        }
}
//function passar_usuario(codigo, nome, mae, data_nasc, cidade, prontuario)
function passar_cidade(ibge, cidade, uf)
{
        validaBusca("cid_codigo").value = ibge;
        validaBusca("cid_nome").value = cidade;
        
        
        
        validaBusca('lista_nomes').style.display = 'none';
        validaBusca('cid_nome').focus();
}