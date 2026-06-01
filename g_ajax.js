// Ajax ------------------------------------------------------------------------

/**
 * Inicia o Ajax
 * ARQUIVO PARA USO GENERICO, PARA CONSTRUCAO DE JANELAS USO O AJAX_TUDO()
 * MARCELO
*/

function ajaxInit()
{
	var req;
	try
	{
		req = new ActiveXObject("Microsoft.XMLHTTP");
	} 
	catch(e) 
	{
		try
		{
			req = new ActiveXObject("Msxml2.XMLHTTP");
		} 
		catch(ex)
		{
			try
			{
				req = new XMLHttpRequest();
			} 
			catch(exc)
			{
				alert("Esse browser não tem recursos para uso do Ajax");
				req = null;
			}
		} 
	}
    return req;
}


function exec_ajax(endereco,id_div)
{ 

	ajax = ajaxInit();
	if(ajax)
	{
		ajax.open("GET", endereco , true);
		ajax.onreadystatechange = function()
		{
			if(ajax.readyState == 4)
			{
				if(ajax.status == 200)
				{
					document.getElementById(id_div).innerHTML = ajax.responseText;
					//content = ajax.responseText
					//content = unescape( ajax.responseText );
					//content = content.replace(/\+/g," ");
					//func_name( content );
				}
				else
				{
					alert('Arquivo:'+endereco+'\nErroNo.:' + ajax.status + '\nMsg:' + ajax.statusText);
					//document.getElementById('resposta').innerHTML = ajax.responseText;
					//document.location.href = endereco;
				}
			}
		}
		ajax.send(null);
	}
}


function imprimeTXTajax(endereco,id)
{ 

	ajax = ajaxInit();
	if(ajax)
	{
		ajax.open("GET", endereco , true);
		ajax.onreadystatechange = function()
		{
			if(ajax.readyState == 4)
			{
				if(ajax.status == 200)
				{
					document.getElementById(id).value = ajax.responseText;
				}
				else
				{
					alert('Arquivo:'+endereco+'\nErroNo.:' + ajax.status + '\nMsg:' + ajax.statusText);
					//document.getElementById('resposta').innerHTML = ajax.responseText;
					//document.location.href = endereco;
				}
			}
		}
		ajax.send(null);
	}
}
