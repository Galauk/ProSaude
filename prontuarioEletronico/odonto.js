// odonto
function hotkey(eventname)
{
	// ESC esconde todas as janelas
	if( eventname.keyCode == 27 )
	{
		return fechar();
		
	}
}

function fechar()
{
	//if( ! confirm('Fechar janela ?') ) return false;
	var div = document.getElementById( 'dente_faces' );
	div.style.display = 'none';
	return false;
}


// globais
var DENTES_ADULTO = new Array( '', 'Incisivo Central', 'Incisivo Lateral', 'Canino', '1&ordm; Premolar', '2&ordm; Premolar', '1&ordm; Molar', '2&ordm; Molar', '3&ordm; Molar');

var DENTES_CRIANCA = new Array( '', 'Incisivo Central', 'Incisivo Lateral', 'Canino', '1&ordm; Molar', '2&ordm; Molar' );

/** chama a janela para o ajax */
//function mapa_dentes( id_login, age_codigo, obj )
function mapa_dentes( id_login, age_codigo, n )
{
	//var n 	= obj.alt;
	var endereco = 'odonto_popup.php?id_login='+id_login+'&age_codigo='+age_codigo+'&dente_num='+n;
	
	var div = document.getElementById( 'dente_faces' );
	div.innerHTML = 'Carregando <img src="'+root+comum+'imgs/loading.gif" alt="Carregando" align="absmiddle" />';
	div.style.display = 'block';
	
	ajax_tudo( endereco, cad_dente );
}

/** atualiza o conteudo da janela ajax */
function cad_dente( txt )
{
	var div = document.getElementById( 'dente_faces' );
	div.style.display = 'block';
	div.innerHTML = txt;	
}

/** atualiza o conteudo da janela ajax */
function atualiza()
{
	var Aux = document.location.href;
	document.location.href = Aux;
}

/** mostra o nome / numero do dente no div abaixo da imagem */
//function mostra_dente( obj, op )
function mostra_dente( n, op )
{
	//var n 	= obj.alt;
	var ht	= document.getElementById( 'dentes' );
	
	if( op == 1 )
		ht.innerHTML = '('+ n +') ' + pega_nome_dente( n );
	else
		ht.innerHTML = '&nbsp;';
}

/** descobre qual dente eh */
function pega_nome_dente( n )
{
	var q 	= n[0];
	var d 	= n[1];
	// adulto
	if( q <= 4 )
	{
		var qs  = ( q == 1 || q == 2 ? 'superior' : 'inferior' );
		var qp	= ( q == 1 || q == 4 ? 'direito' : 'esquerdo' );
		return DENTES_ADULTO[d] + ' ' + qs + ' ' + qp;
	}
	// crianca
	else
	{
		var qs  = ( q == 5 || q == 6 ? 'superior' : 'inferior' );
		var qp	= ( q == 5 || q == 8 ? 'direito' : 'esquerdo' );
		return DENTES_CRIANCA[d] + ' ' + qs + ' ' + qp;
	}
}

/** mostra nome da face */
function mostra_face( obj, num, op )
//function mostra_face( f, num, op )
{
	var f 	= obj.alt;
	var ht	= document.getElementById( 'face' );
	
	if( op == 1 )
		ht.innerHTML = pega_nome_face( num, f );
	else
		ht.innerHTML = '&nbsp;';
}

/** descobre qual face eh */
function pega_nome_face( dente, face )
{
	var quad = new String( dente );
	
	//return '>' + quad[0] + ':' + face + '<';
	f = pega_letra_face( dente, face );
	return '('+ f + ') ' + pega_nome_face_txt( f );
}

/** descobre qual face eh (letra) */
function pega_letra_face( dente, face )
{
	var quad = new String( dente );
	switch( parseInt(quad[0]) )
	{
		case 1:
		case 5:
			var Faces = new Array( '', 'V', 'M', 'L', 'D', 'O' );
			break;
		case 2:
		case 6:
			var Faces = new Array( '', 'V', 'D', 'L', 'M', 'O' );
			break;
		case 3:
		case 7:
			var Faces = new Array( '', 'L', 'D', 'V', 'M', 'O' );
			break;
		case 4:
		case 8:
			var Faces = new Array( '', 'L', 'M', 'V', 'D', 'O' );
			break;
	}
	return Faces[ face ];
}

/** pega o nome da face por extenso */
function pega_nome_face_txt( c )
{
	switch( c )
	{
		case 'V': return 'Vestibular';
		case 'M': return 'Mesial';
		case 'L': return 'Lingual';
		case 'D': return 'Distal';
		case 'O': return 'Oclusal';
		
		default: return ' ERROR ';
	}
}

/** escolhe a face para as anotacoes */
var FACES 	= new Array();

function escolhe_face( obj, dente )
//function escolhe_face( face, dente )
{
	var face 	= obj.alt;
	var fh 		= document.getElementById( 'face_escolhida' );
	var ft 		= document.getElementById( 'face_escolhida_t' );
	
	if( ! in_array( FACES, face ) )
	{
		FACES.push( face );
	}
	else
	{
		FACES = remove( FACES, face );
		//alert( 'jah tem:'+ pega_letra_face( dente, face ) );
	}
	
	
	if( FACES.length == 0 )
	{	
		ft.innerHTML 	= '<em>nenhuma</em>';
		fh.value	 	= 'N';
	}
	else
	{
		ft.innerHTML 	= '';
		fh.value 		= '';
	
		for( i=0; i < FACES.length; i++ )
		{
			ft.innerHTML 	+= pega_nome_face( dente, FACES[i] ) + ' ';
			fh.value		+= pega_letra_face( dente, FACES[i] ) + ';';
		}
	}	

}

/** valida e seta os forms do popup */
function form_submit( id_login, age_codigo, dente )
{//alert('1212');
	var face = document.getElementById('face_escolhida');
	var sit = document.getElementById('situacao');
	var anot = document.getElementById('anotacoes');
	var final = document.getElementById('finalizado');
		
	//if( ! dente || ! anot.value || ! face.value || ! sit.value )
	if( ( ! dente || ! sit.value) && ! final.checked )
	{
		var msg = 'Erro: Escolha ao menos o procedimento do dente '+dente;
		//msg += '\n\nDente:'+dente+'\nFace:'+face.value+'\nAnotações:'+anot.value+'\nSituação:'+sit.value;
		alert( msg );
		return false;
	}
	
	//alert( 'sit='+sit.value+';face='+face.value);
	
	if( sit.value < 4 && ( ! face.value || face.value == 'N' ) && ! final.checked )
	{
		var msg = 'Erro: Para este procedimento, a face e obrigatoria ! ';
		alert( msg );
		return false;
	}
	
	var endereco = '../odonto_op.php?id_login='+id_login+'&age_codigo='+age_codigo+'&dente_num='+dente;
		endereco += '&face='+face.value+'&sit='+sit.value+'&anot='+escape( anot.value ) + '&finalizado='+final.checked;
		
	//alert(endereco);

	var div = document.getElementById( 'dente_faces' );
	div.innerHTML = 'Carregando <img src="'+root+comum+'imgs/loading.gif" alt="Carregando" align="absmiddle" />';
	
	ajax_tudo( endereco, cad_dente );
	
	setTimeout( "document.getElementById( 'dente_faces' ).style.display='none';atualiza()", 1500 );

	return false;	
}
/** limpa os forms */
function limpar()
{
	if( ! confirm('Limpar o formulário atual ?') ) return false;

	var sit = document.getElementById('situacao');
	var anot = document.getElementById('anotacoes');
	var fh = document.getElementById( 'face_escolhida' );
	var ft = document.getElementById( 'face_escolhida_t' );
	
	sit.value			= '';
	sit.selectedIndex 	= -1;
	anot.value			= '';
	ft.innerHTML 		= '<em>nenhuma</em>';
	fh.value			= 'N';
	
	return false;
}
