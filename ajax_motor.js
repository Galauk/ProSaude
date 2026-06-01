/** 
 * Mostra a janela
 * Procura uma ocorrencia na var Janelas - da id - e se encontrado, ele 'esconde'
 * @var id String id do elemento
*/
var Janelas = new Array();
var Zindex	= 0;

function mostra_janela( id )
{
	var J =  document.getElementById( id );
	if( ! J || ! id )
	{
		alert("Elemento '"+ id +"' não encontrado !");
		return;
	}
	
	if( in_array( Janelas, id ) )
	{
		Janelas = remove( Janelas, id );
		return esconde_janela( id );
	}
	
	Janelas.push( id );
	
	J.style.display = 'block';
	Zindex++;
	J.style.zIndex = Zindex;

	var sel = document.getElementsByTagName('select');
}

function in_array( Arr, id )
{
	for( i=0; i < Arr.length; i++ )
	{
		if( Arr[i] == id ) return true;
	}
	return false; 
}
/**
 * Remove um 'id' do Arr
 * @return Array
*/
function remove( Arr, id )
{
	var Aux = new Array();
	for( i=0; i < Arr.length; i++ )
	{
		if( Arr[i] == id ) continue ;
		Aux.push( Arr[i] );
	}
	return Aux;
} 
 
/** 
 * Esconde a janela 
*/
function esconde_janela( id )
{
	var J =  document.getElementById( id );
	if( ! J || ! id )
	{
		alert("Elemento '"+ id +"' não encontrado !");
		return;
	}
	J.style.display = 'none';
	
	// arrumando (IEca safado)
	var sel = document.getElementsByTagName('select');
	for( i=0; i < sel.length; i++ )
		sel[i].style.display = 'inline';
	
	Janelas = remove( Janelas, id );
	// sempre que ele abrir novamente, deve estar maximizado
	var C = document.getElementById( id + '_conteudo' );
	if ( C.className == 'conteudo cont_min' )
		mm_janela( id, true );
		
	//
	C.innerHTML = 'Carregando <img src=\"'+root+comum+'imgs/loading.gif\" alt=\"Carregando\" align=\"absmiddle\" />';
}

/**
 * Maximiza/Minimiza a janela (shade)
*/
function mm_janela( id, show )
{
	var J = document.getElementById( id );
	var C = document.getElementById( id + '_conteudo' );
	var I = document.getElementById( id + '_mm' );
	// arrumando (IEca safado)
	var sel = document.getElementsByTagName('select');
	
	// maximizado
	if( J.className == 'janela jan_min' || C.className == 'conteudo cont_min' )
	{
		J.className = 'janela';
		C.className = 'conteudo';
		I.src 		= root+comum+'imgs/jan_min.jpg';
		
		// arrumando (IEca safado)
		//COMENTADO POR RENATO
		//if( ! show )
			//for( i=0; i < sel.length; i++ )
				//sel[i].style.display = 'none';
 	}
 	// minimizado
 	else
 	{
		J.className = 'janela jan_min';
 		C.className = 'conteudo cont_min';
 		I.src 		= root+comum+'imgs/jan_max.jpg';
 		
 		// arrumando (IEca safado)
		//COMENTADO POR RENATO
		//for( i=0; i < sel.length; i++ )
			//sel[i].style.display = 'inline';
 	}
}
/**
 * Mostra o 'loading'
*/
function janela_carregando( id )
{
	//var J = document.getElementById( id + '_conteudo' );	
	//J.innerHTML = 'Carregando <img src=\"'+root+comum+'imgs/loading.gif\" alt=\"Carregando\" align=\"absmiddle\" />';
}
// Ajax ------------------------------------------------------------------------

/**
 * Inicia o Ajax
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

function ajax_tudo( endereco, func_name )
{ 

	//var endereco = 'acao.php';

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
					//document.getElementById('resposta').innerHTML = ajax.responseText;
					content = ajax.responseText
					//content = unescape( ajax.responseText );
					//content = content.replace(/\+/g," ");
					func_name( content );
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
