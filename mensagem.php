<?php
session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
verauth($id_login);
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
Cabecario();

$_SESSION[modulo] = "WebSocialSaude/"; $_SESSION[root] = $_SERVER[DOCUMENT_ROOT] . "/"; $_SESSION[linkroot] = "http://" . $_SERVER[HTTP_HOST] . "/"; $_SESSION[comum] = "WebSocialComum/"; $_SESSION[modulo] = "WebSocialSaude/"; require_once $_SESSION[root].$_SESSION[modulo]."sessao_controller.php";

$sessao = new TempoSessao();
$sessao->primeiraPagina();

// OPCOES ----------------------------------------------------------------------
print "
<script type='text/javascript' src='ajax_motor.js'></script>
<script type='text/javascript' src='mensagem.js'></script>

<fieldset>
<legend>Op&ccedil;&otilde;es</legend>
<a href='mensagem.php?id_login={$id_login}&amp;acao=cx_entrada' title='Caixa de Entrada'>
    <img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/caixa_de_entrada_on.jpg' alt='' border='0' />
</a>

<a href='mensagem.php?id_login={$id_login}&amp;acao=form_nova' title='Nova Mensagem'>
    <img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/nova_mensagem_on.jpg' alt='' border='0' />
</a>

<a href='mensagem.php?id_login={$id_login}&amp;acao=cx_enviada' title='Mensagens Enviadas'>
    <img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/itens_enviados_on.jpg' alt='' border='0' />
</a>

</fieldset>
";

// caixa de entrada ------------------------------------------------------------
if( empty($_REQUEST['acao']) || $_REQUEST['acao'] == 'cx_entrada' || $_REQUEST['acao'] == 'cx_enviada' )
{
    
    if( $_REQUEST['acao'] == 'cx_enviada' )
    {
        $titulo     = 'Mensagens Enviadas';
        $msg_copy   = 'S';
    }
    else
    {
        $titulo = 'Caixa de Entrada';
        $msg_copy   = 'N';
    }
    
    reglog( $id_login, "Entrando em mensagem '$titulo'" );
    
    print "
    <fieldset>
    <legend>{$titulo}</legend>
    
    <img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_todos_on.png' id='cx_sel_all' alt='Selecionar Todas'
        style='cursor:pointer; vertical-align: middle;' />
    <img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagarlinha.jpg' id='cx_del_sel' alt='Apagar Selecionados'
        style='cursor:pointer; vertical-align: middle;' />    
    <!--<input type='button' value='Selecionar Todos' id='cx_sel_all' />
    <input type='button' value='Apagar Selecionados' id='cx_del_sel' />-->
    
    <table class='lista'>
    <tr style='background:#fff'>
        <th style='width:30px;text-align:center;'>&nbsp;</th>
        <th>De</th>
        <th>Assunto</th>
        <th style='width:130px;text-align:center;'>Data</th>
    </tr>
    ";
    
    $stmt_msg = "SELECT msg_codigo, msg_titulo, remet.usr_nome as de,
        to_char( msg_dt_envio, 'dd/mm/yyyy HH24:MI') as data,
        ( CASE WHEN msg_dt_lida IS NULL THEN 'nova' ELSE 'velha' END) as status
    FROM  mensagem AS msg
    INNER JOIN usuarios AS remet ON remet.usr_codigo = msg.usr_codigo_from
    WHERE usr_codigo_to = {$id_login} AND msg_copy = '{$msg_copy}' ORDER BY msg_dt_envio DESC";

    $qry_msg = db_query($stmt_msg);
    
    if( pg_num_rows($qry_msg) == 0 ) print "<tr><td colspan='4'>Nenhuma mensagem !</td></tr>";

    while( $row_msg = pg_fetch_array($qry_msg) )
    {
        print "
        <tr ".( $row_msg['status'] == 'nova' ? ' style="font-weight:bold;"' : '' ).">
            <td class='c'><input type='checkbox' name='chk_msg' value='$row_msg[0]' /></td>
            <td>{$row_msg[de]}</td>
            <td><a href='{$PHP_SELF}mensagem.php?id_login={$id_login}&amp;acao=form_ler&amp;msg={$row_msg[0]}'>$row_msg[msg_titulo]</a></td>
            <td class='c'>{$row_msg[data]}</td>
        </tr>";
    }

    print "</table>
    </fieldset>";
    
}
// lendo mensagem --------------------------------------------------------------
else if( $_REQUEST['acao'] == 'form_ler' )
{
 
    reglog( $id_login, "Lendo mensagem $msg" );
    
    $stmt = "SELECT msg_codigo, msg_titulo, remet.usr_nome as de,
        to_char( msg_dt_envio, 'dd/mm/yyyy HH:MI') as data, msg_conteudo
    FROM  mensagem AS msg
    INNER JOIN usuarios AS remet ON remet.usr_codigo = msg.usr_codigo_from
    WHERE msg_codigo=$msg";
    
    $row = db_getRow($stmt);
    
    $stmt_up = sprintf("UPDATE mensagem SET msg_dt_lida = NOW() WHERE msg_codigo = %d", $msg );
    db_query( $stmt_up );
    
    $row['msg_titulo']      = stripslashes( $row['msg_titulo'] );
    $row['msg_conteudo']    = nl2br( stripslashes( $row['msg_conteudo'] ) ); 
    
    print "
    <fieldset>
    <legend>Lendo Mensagem \"$msg\"</legend>
    <table>
        <tr >
            <th width='115'>De</th>
            <td>{$row[de]}</td>
        </tr>
        <tr>
            <th>Data</th>
            <td>{$row[data]}</td>
        </tr>
        <tr>
            <th>Assunto</th>
            <td>{$row[msg_titulo]}
        </tr>
        <tr>
            <th style='vertical-align:top;'>Mensagem</th>
            <td>$row[msg_conteudo]</td>
        </tr>
    </table>
    </fieldset>
    ";
}
// form nova mensagem ----------------------------------------------------------
else if( $_REQUEST['acao'] == 'form_nova' )
{
    
    reglog( $id_login, "Formulario de nova mensagem" );
    
    print "
    <form action='mensagem.php?id_login={$id_login}&amp;acao=nova' method='post' id='form_msg'>
    <fieldset>
    <legend>Nova Mensagem</legend>
    <table cellpadding='4' cellspacing='2'>
    <tr>
        <td width='150'><label for='tipo'>Tipo de Filtro</label></td>
        <td>
            <select class='box' name='tipo' id='tipo'>
                <option value='0'>... Escolha um Tipo antes...</option>
                <option value='usr'>Usu&aacute;rio</option>
                <option value='uni'>Unidade</option>
            </select>
        </td>
    </tr>
    <tr>
        <td style='vertical-align:top;'>
            <label for='dest'>Lista de Destinat&aacute;rio(s)</label>
            <br />
            <img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_on.png' id='add_dest' title='Selecionar'
                style='cursor:pointer; vertical-align: middle;'/>
            <img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_todos_on.png' id='add_dest_all' title='Selecionar Todos'
                style='cursor:pointer; vertical-align: middle;'/>
            
            <!--<input type='button' id='add_dest' value='ADD' class='btn' />
            <input type='button' id='add_dest_all' value='ADD ALL' class='btn' />-->
        </td>
        <td>
            <select id='dest' name='dest' class='box' size='4' style='width:400px'>
                <option value='0'>... Escolha um Tipo antes...</option>
            </select>
        </td>
    </tr>
    <tr>
        <td style='vertical-align:top;'>
            <label for='dest'>Destinat&aacute;rio(s)</label>
            <br />
            <img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/remover_on.png' id='rem_dest' title='Remover'
                style='cursor:pointer; vertical-align: middle;'/>
            <img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/remover_todos_on.png' id='rem_dest_all' title='Remover Todos'
                style='cursor:pointer; vertical-align: middle;'/>
                
            <!--<input type='button' id='rem_dest' value='REM' class='btn' />
            <input type='button' id='rem_dest_all' value='REM ALL' class='btn' />-->
        </td>
        <td>
            <select id='dest_list' name='dest_list[]' class='box' size='4' style='width:400px'>
                <option value='0'>... Escolha um Destinat&aacute;rio antes...</option>
            </select>
        </td>
    </tr>
    <tr>
        <td><label for='msg_titulo'>T&iacute;tulo</label></td>
        <td>
            <input type='text' class='box' name='msg_titulo' id='msg_titulo' size='50' maxlength='99' />
        </td>
    </tr>
    <tr>
        <td style='vertical-align:top;'><label for='msg_conteudo'>Conte&uacute;do</label></td>
        <td>
            <textarea class='box' name='msg_conteudo' id='msg_conteudo' rows='10' cols='60'></textarea>
        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>
            <label><input type='checkbox' name='salvar' value='1' checked='checked' />
                Salvar uma c&oacute;pia em 'Mensagens Enviadas'
            </label>
        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>
            <!--<input type='submit' value='Enviar' />-->
            <input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/enviar_on.jpg' title='Enviar' />
        </td>
    </tr>
    </table>
    </fieldset>
    </form>
    ";
}
// acao nova mensagem ----------------------------------------------------------
else if( $_REQUEST['acao'] == 'nova' )
{
    $msg_titulo     = substr( trim( addslashes($_POST['msg_titulo'])), 0, 99 );
    $msg_conteudo   = trim( addslashes($_POST['msg_conteudo']));
 
    reglog( $id_login, "Enviando mensagem: '$msg_titulo'" );
     
    $stmt_gen =
        "\nINSERT INTO mensagem ( ".
            "msg_titulo, msg_conteudo, msg_dt_envio, msg_dt_lida, usr_codigo_from, ".
            "usr_codigo_to ".
            " ) VALUES ( ".
            "'$msg_titulo', '$msg_conteudo', NOW(), null, $id_login, %d );";
    
    $stmt_final = 'BEGIN;';
    
    if( $tipo == 'uni' )
    {
        $unidades = join( ',', $dest_list );
        $stmt_usr = "SELECT usr_codigo FROM usuarios WHERE uni_codigo IN ($unidades)";
        $qry_usr = db_query($stmt_usr);
        while( $row_usr = pg_fetch_array($qry_usr) )
        {
            $stmt_final .= sprintf( $stmt_gen, $row_usr[0] );
        }    
        
    }
    else
    {
        foreach( $dest_list as $usu )
        {    
            $stmt_final .= sprintf( $stmt_gen, $usu );
        }    
    }
    
    if( ! empty($_POST['salvar']) )
    {
        $stmt_final .=
        "\nINSERT INTO mensagem ( ".
            "msg_titulo, msg_conteudo, msg_dt_envio, msg_dt_lida, usr_codigo_from, ".
            "usr_codigo_to, msg_copy ".
            " ) VALUES ( ".
            "'$msg_titulo', '$msg_conteudo', NOW(), NOW(), $id_login, $id_login, 'S' );";
    }
    
    //$stmt_final .= "\nROLLBACK;";
    $stmt_final .= "\nCOMMIT;";
    
    db_query( $stmt_final );
    
    print "<p class='aviso ok'>Mensagem enviada !</p>
    <script type='text/javascript'>
        setTimeout( \"document.location.href='{$PHP_SELF}mensagem.php?id_login={$id_login}'\", 3000 );
    </script>
    ";
    
}

// fim -------------------------------------------------------------------------
print "</body></html>";
