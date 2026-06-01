/**
 *
 * @version Eduardo (dudu@g1ti.com.br) 2007-07-04 BRT 10:05:37
 * @author Dudu
 *
 * Arquivo para a busca dinâmica das cidades
*/

function atualiza_cidade( obj, id, campo )
{
	try
	{
		if( obj.value == 0 || obj.value == -1 || ! obj.value )
		{
			$( id ).length = 1;
			$( id ).disabled = false;
			$( id ).options[0].text = '...Todos...';
			$( id ).options[0].value = '-1';
			
			return false;
		}
		
		var url = 'cidades.php?uf='+obj.value+'&id='+id+'&campo='+( ! campo ? 'cid_codigo_ibge' : campo );
		
		//alert(url);
		
		$( id ).length = 1;
		$( id ).disabled = true;
		$( id ).options[0].text = 'Carregando...';
		
		ajax_tudo( url, atualiza_cidade_cb );
		
		return true;
	}
	catch( ex )
	{
		alert( ex );
	}
}

function atualiza_cidade_cb( respTxt )
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
		
		var c = $( Resp.id );
		//c.length = 0;
		
		
		// wtf ?
		if( Resp.total == 0 )
		{
			return false;
		}
		
		for( var i = 0; i < Resp.dados.length; i++ )
		{
			c.length = i+1;
			c.options[ i ].value = Resp.dados[i].cid_codigo;
			c.options[ i ].text = Resp.dados[i].cid_nome;
		}
		
		c.disabled = false;
		
		//alert( Resp.total + "\n" + Resp.id + "\n" + Resp.dados );
		return true;
		
		
	}
	catch( ex )
	{
		alert( ex );
	}
}