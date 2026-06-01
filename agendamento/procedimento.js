/********
 * @brief Arquivo auxiliar das mensagens
*/

// 'binda' os eventos dos objetos com as funcoes de callback
window.onload = function()
{
    if( document.getElementById('form_msg') )
    {
        //$('tipo').onchange = atualiza_dest;
        $('add_dest').onclick = add_dest;
        $('add_dest_all').onclick = add_dest_all;
        $('rem_dest').onclick = rem_dest;
        $('rem_dest_all').onclick = rem_dest_all;
        $('form_msg').onsubmit = form_submit;
    }
    if( document.getElementById('cx_sel_all') )
    {
        $('cx_sel_all').onclick = cx_sel_all_click;
        $('cx_del_sel').onclick = cx_del_sel_click;
    }
}

// atualiza a lista de destinatarios
function atualiza_dest()
{
    var Dest = $('dest'), Tipo = $('tipo'), DestL = $('dest_list');

    DestL.length = 1;
    DestL.options[0].text = '... Escolha um Destinatario antes...';
    DestL.options[0].value = '0';


    if( Tipo.value == '0' )
    {
        Dest.length = 1;
        Dest.options[0].text = '... Escolha um Tipo antes...';
        Dest.options[0].value = '0';

    }
    else
    {
        Dest.length = 1;
        Dest.options[0].text = '... carregando ...';
        Dest.options[0].value = '0';
        
        var acao = 'busca_' + Tipo.value;
        var endereco = 'mensagem_op.php?acao='+acao+'&codigo='+Tipo.value;
        ajax_tudo( endereco, atualiza_dest_cbk );
    }
}

// callback do ajax
function atualiza_dest_cbk( Str )
{
    var Obj = eval(Str), Dest = $('dest');
    
    Dest.length = 0;
    
    for( var i = 0; i < Obj.length; i++ )
    {
        Dest.length++;
        Dest.options[ Dest.length-1 ].text = Obj[i].nome;
        Dest.options[ Dest.length-1 ].value = Obj[i].cod;
    }
}

// adiciona da 'Lista de Destinatarios' -> 'Destinatarios'
function add_dest()
{
    return altera_destinatario( $('dest'), $('dest_list') );
}
function add_dest_all()
{
    var D = $('dest'), L = $('dest_list'), T = $('tipo');
    if( ! T.value ) return 0;
    if( D.length > 0 && D.options[0].value == '0' ) return 0;
        
    D.selectedIndex = 0;
    do {} while( add_dest() > 0 );

}

// adiciona dos 'Destinatarios' -> 'Lista de Destinatarios'
function rem_dest()
{
    return altera_destinatario( $('dest_list'), $('dest') );
}

function rem_dest_all()
{
    //var D = $('dest'), L = $('dest_list'), T = $('tipo');
    // soh inverti a ordem !!!
    var D = $('dest_list'), L = $('dest'), T = $('tipo');
    
    if( ! T.value ) return 0;
    if( D.length > 0 && D.options[0].value == '0' ) return 0;
        
    D.selectedIndex = 0;
    do {} while( rem_dest() > 0 );

}
// funcao 'generica' para transferir 'options'
function altera_destinatario( From, To )
{
    
    if( From.selectedIndex == -1 || ! From[ From.selectedIndex ].value ) return 0;
    
    if( To.length > 0 && To.options[0].value == '0' ) To.options[0] = null;
    
    To.length++;
    To[ To.length-1 ].text = From[ From.selectedIndex ].text;
    To[ To.length-1 ].value = From[ From.selectedIndex ].value;
    
    From.focus();
    var sel = From.selectedIndex;
    From[ From.selectedIndex ] = null;
    if( sel >= 1 && From.length >= 1 )
        From.options[ sel - 1 ].selected = true;
    else if( sel >= 0 && From.length >= 1 )
        From.options[ sel ].selected = true;
        
    return ( From.length == 1 ? From.options[ 0 ].value : From.length );
}

// envia o form
function form_submit()
{
    var DestL = $('dest_list'), Dest = $('dest'), Tipo = $('tipo');
    
    if( ! Tipo.value || Tipo.value == '0')
    {
        alert( 'Escolha um tipo de lista de Destinarios !' );
        Tipo.focus();
        return false;
    }
    
    if( DestL.length == 0 || DestL.options[0].value == '0' )
    {
        alert( 'Escolha pelo menos 1 Destinario !' );
        DestL.focus();
        return false;
    }
    
    if( ! valida('msg_titulo', 'Titulo') ) return false;
    if( ! valida('msg_conteudo', 'Conteudo') ) return false;
    
    DestL.multiple = true;
    for( var i = 0; i < DestL.length; i++ )
        DestL.options[ i ].selected = true;
    
    return true;
}

// FUNCOES PARA LISTA DE MENSAGENS ! -------------------------------------------
function cx_sel_all_click()
{
    var C = document.getElementsByTagName('input');
    for( var i = 0;  i < C.length; i++ )
    {
        if( C[i].name != 'chk_msg' ) continue;
        C[i].checked = true;
    }
}

function cx_del_sel_click()
{
    var C = document.getElementsByTagName('input'), Apagar = [];
    for( var i = 0;  i < C.length; i++ )
    {
        if( C[i].name == 'chk_msg' && C[i].checked )
            Apagar.push( C[i].value );
    }
    if( ! Apagar.length )
    {
        alert('Selecione pelo menos uma mensagem !');
        return false;
    }
    
    if( ! confirm("Deseja apagar a(s) mensagem(ns) selecionada(s) ?") )
        return null;
    
    var endereco = 'mensagem_op.php?acao=apagar&codigos='+Apagar.join(',');
    ajax_tudo( endereco, cx_del_sel_callback );
}
function cx_del_sel_callback( txt )
{
    if( txt.length > 0 )
    {
        alert(txt);
        return false;
    }
    alert( 'Mensagens apagadas !');
    setTimeout( "document.location.href = document.location.href", 500 );
    
}

function trim(sInString) {
  sInString = sInString.replace( /^\s+/g, "" );// strip leading
  return sInString.replace( /\s+$/g, "" );// strip trailing
}
