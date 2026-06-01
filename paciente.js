/**
 *
 * @version Eduardo 2007-05-11 BRT 10:10:11
 * arquivo do paciente.php
*/

function verifica() {
   if(document.paciente.usu_nome.value == '') {
	alert("Por favor Preencha o Nome");
	return false;
   }

   if(document.paciente.usu_mae.value == '') {
	alert("Por favor Preencha o Nome da Mae");
	return false;
   }

   if(document.paciente.usu_datanasc.value == '') {
	alert("Por favor Preencha a Data de Nascimento");
	return false;
   }

   if(document.paciente.usu_end_rua.value == '') {
	alert("Por favor Preencha a Rua");
	return false;
   }

   if(document.paciente.usu_end_nr.value == '') {
	alert("Por favor Preencha o Numero");
	return false;
   }

   if(document.paciente.usu_end_bairro.value == '') {
	alert("Por favor Preencha o Bairro");
	return false;
   }

   //if(document.paciente.usu_end_cidade.value == '') {
   if(document.paciente.muni_cd_cod_ibge_resid.value == '') {
	alert("Por favor Escolha a Cidade");
	return false;
   }

   if(document.paciente.uni_unidade.value == '') {
	alert("Por favor Escolha a Unidade");
	return false;
   }


 return true;
}

// atualiza o usuario/prontuario
function atualizar_prontuario( usu_codigo )
{
    if( ! usu_codigo )
    {
        alert('Usu�rio n�o indicado !');
        return null;
    }

    var Hidden = document.getElementById("usu_prontuario_hidden");
    var Input = document.getElementById('usu_prontuario');
    if( Hidden.value.length > 0 || Input.value.length > 0 )
    {
        alert('Prontu�rio j� atualizado !');
        return null;
    }

    var endereco = 'paciente_op.php?acao=atualiza_prontuario&usu_codigo='+usu_codigo;
    ajax_tudo( endereco, atualizar_prontuario_callback );

    return null;
}

function atualizar_prontuario_callback( txt )
{
    var Resp = eval( txt );
    //alert( txt + ",Resp.ok=" + Resp.ok + ",,Resp.ok=usu_prontuario" + Resp.usu_prontuario );
    if( Resp.ok )
    {
    	var Hidden = document.getElementById("usu_prontuario_hidden");
        var Input = document.getElementById('usu_prontuario');
        Hidden.value = Resp.usu_prontuario;
        Input.value = Resp.usu_prontuario;
    }

    return null;
}

/// puxado do arquivo paciente.php do demonstrativo/gps

function init_ibge(id_login)
{
	var endereco = 'paciente_popup_ibge_resid.php?id_login='+id_login;
	//alert( endereco );
	ajax_tudo( endereco , conteudo_ibge );
	//alert (ajax_tudo);
}

function conteudo_ibge(txt)
{
	document.getElementById('janela_ibge_conteudo').innerHTML = txt;
}

function busca_ibge()
{
	var pc = document.getElementById('palavra_chave');
	var endereco = 'paciente_popup_ibge_resid.php?palavra_chave='+pc.value+'&acao=busca';
	var op = document.getElementById('busca_cidade').value;
	endereco += '&valor_busca='+op;

	//alert(endereco);

	//janela_carregando( 'janela_ibge_conteudo' );
    document.getElementById('janela_ibge_conteudo').innerHTML = 'Carregando...';
	ajax_tudo( endereco, conteudo_ibge );
	return false;
}
function busca_ibge2(txt)
{
	if(txt) alert(txt);
}

function add_cod_ibge_resid(ibge,cidade,uf){

	document.getElementById('muni_cd_cod_ibge_resid').value=ibge;
	document.getElementById('muni_ibge_cidade').value=cidade;
	document.getElementById('muni_ibge_uf').value=uf;

	esconde_janela('janela_ibge');
	//location.hash = '#'+ibge;

}

function selectRaca() {
    console.log("teste");
    
    var recebeCodigoRaca = $("#codigoRaca").val();

    if(recebeCodigoRaca == null){
        console.log("null");
        
    } else{
        console.log("não nulo");
        
    }
    
}

