var id = '';
function atualiza_produtos( obj, idparam )
{
   
	try
	{
                id = idparam;
		if( obj.value == 0 || obj.value == -1 || ! obj.value )
		{
			$( id ).length = 1;
			$( id ).disabled = false;
			$( id ).options[0].text = '------TODOS------';
			$( id ).options[0].value = '-1';
			
			return false;
		}
		
		var url = '../produto_.php?grupo_codigo='+obj.value;
		
		//alert(url);
		
		$( id ).length = 1;
		$( id ).disabled = true;
		$( id ).options[0].text = 'Carregando...';
		ajax_tudo( url, atualiza_produtos_resp );
		return true;
	}
	catch( ex )
	{
		alert( ex );
	}
}


function atualiza_produtos_resp(respTxt)
{    
    	try
	{
		//alert( respTxt );
		
		var Resp = respTxt.parseJSON();
		if( ! Resp || typeof Resp == 'undefined' || Resp == null )
		{
			alert( "Erro..." );
			return false;
		}
		
		var p = $( id );

		for( var i = 0; i < Resp.length; i++ )
		{
			p.length = i+1;
			p.options[ i ].value = Resp[i].pro_codigo;
			p.options[ i ].text = Resp[i].pro_nome;
		}
		
		p.disabled = false;
		
		return true;
		
		
	}
	catch( ex )
	{               
		alert( ex );
        }    
}

    
    

