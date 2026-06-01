// hotkey
function hotkey(eventname)
{
	// ESC esconde todas as janelas
	if( eventname.keyCode == 27 )
	{
		esconde_janela( 'janela_aih' );
		return false;
		
	}
}

// cadastro dos pacientes
function form_paci( id_login , codigo )
{
	if( ! codigo ) 	acao = 'pac_form_add';
	else				acao = 'pac_form_edit&codigo='+codigo;
	
	var endereco = 'aih_popup.php?id_login='+id_login+'&acao='+acao;
	//alert(endereco);
	janela_carregando("janela_aih");
	ajax_tudo( endereco, busca_lab_cont );
	return false;
}

function form_paci_submit( id_login, codigo )
{

   if(document.aih_form_add_paciente.pac_nome.value == '') {
	alert("Por favor Preencha o Nome do Paciente");
	document.aih_form_add_paciente.pac_nome.focus();
	return false;
   }
 /*
 if(document.aih_form_add_paciente.pac_cpf.value == '') {
	alert("Por favor Preencha o CPF");
	document.aih_form_add_paciente.pac_cpf.focus();
	return false;
   }
 */
   if(document.aih_form_add_paciente.pac_mae_responsavel_nome.value == '') {
	alert("Por favor Preencha o Nome da Mãe/Responsável");
	document.aih_form_add_paciente.pac_mae_responsavel_nome.focus();
	return false;
   }
  /* if(document.aih_form_add_paciente.pac_mae_responsavel_cpf.value == '') {
	alert("Por favor Preencha o CPF da Mãe/Responsável");
	document.aih_form_add_paciente.pac_mae_responsavel_cpf.focus();
	return false;
   }*/
   if(document.aih_form_add_paciente.pac_end_rua.value == '') {
	alert("Por favor Preencha a Rua");
	document.aih_form_add_paciente.pac_end_rua.focus();
	return false;
   }
   if(document.aih_form_add_paciente.pac_end_cidade.value == '') {
	alert("Por favor Preencha a Cidade");
	document.aih_form_add_paciente.pac_end_cidade.focus();
	return false;
   }
   if(document.aih_form_add_paciente.pac_dt_nasc.value == '') {
	alert("Por favor Preencha a Data de Nascimento");
	document.aih_form_add_paciente.pac_dt_nasc.focus();
	return false;
   }
   
	//return true;

	var acao = (  codigo ? 'paci_form_edit_sub&codigo='+codigo : 'paci_form_add_sub' );
	
	var c0 = document.getElementById('pac_nome').value;
	var c1 = document.getElementById('pac_sexo').value;
	var c2 = document.getElementById('pac_rg').value;
	var c3 = document.getElementById('pac_cpf').value;
	var c4 = document.getElementById('pac_cns').value;
	//var c5 = document.getElementById('pac_prontuario').value;
	var c6 = document.getElementById('pac_mae_responsavel_nome').value;
	var c7 = document.getElementById('pac_mae_responsavel_rg').value;
	var c8 = document.getElementById('pac_mae_responsavel_cpf').value;
	var c9 = document.getElementById('pac_end_rua').value;
	var c10 = document.getElementById('pac_end_nr').value;
	var c11 = document.getElementById('pac_end_compl').value;
	var c12 = document.getElementById('pac_end_bairro').value;
	var c13 = document.getElementById('pac_end_cep').value;
	var c14 = document.getElementById('pac_end_cidade').value;
	var c15 = document.getElementById('pac_dt_nasc').value;
	var c16 = document.getElementById('pac_telefone').value;
	var c17 = document.getElementById('pac_ibge_codigo').value;
	var endereco = 'aih_popup.php?id_login='+id_login+'&pac_nome='+c0+'&pac_sexo='+c1+'&pac_rg='+c2;
	//endereco += '&pac_cpf='+c3+'&pac_cns='+c4+'&pac_prontuario='+c5+'&pac_mae_responsavel_nome='+c6;
	endereco += '&pac_cpf='+c3+'&pac_cns='+c4+'&pac_mae_responsavel_nome='+c6;
	endereco += '&pac_mae_responsavel_rg='+c7+'&pac_mae_responsavel_cpf='+c8+'&pac_end_rua='+c9;
	endereco += '&pac_end_nr='+c10+'&pac_end_compl='+c11+'&pac_end_bairro='+c12+'&pac_end_cep='+c13;
	endereco += '&pac_end_cidade='+c14+'&pac_dt_nasc='+c15+'&pac_telefone='+c16+'&pac_ibge_codigo='+c17;
	endereco += '&acao='+acao;

	janela_carregando("janela_aih");
	ajax_tudo( endereco, busca_lab_cont );
	setTimeout( "init('paciente');", 3500 );
	return false;
}

function apagar_paci( id_login, codigo )
{
	if( ! confirm("Deseja apagar o paciente "+codigo+" da AIH ?") ) return false;
	
	var endereco = 'aih_popup.php?id_login='+id_login+'&acao=paci_form_del_sub&codigo='+codigo;
	
	janela_carregando("janela_aih");
	ajax_tudo( endereco, busca_lab_cont );
	setTimeout( "init('paciente');", 3500 );
	return false;
}

function atualiza_prontuario( valor )
{
	var cod = document.getElementById('usu_codigo').value;
	var aih = document.getElementById('pac_aih').value;
	if( ! cod || ! aih ) return;
	var endereco = 'aih_op_prontuario.php?acao=atualiza_prontuario&codigo='+cod+'&aih='+aih+'&prontuario='+valor;
	ajax_tudo( endereco, atualiza_prontuario2 );
}
function atualiza_prontuario2(txt)
{
	if(txt) alert(txt);
}

function atualiza_IBGE( valor )
{
	var cod = document.getElementById('usu_codigo').value;
	var aih = document.getElementById('pac_aih').value;
	if( ! cod || ! aih ) return;
	var endereco = 'aih_op_ibge.php?acao=atualiza_ibge&codigo='+cod+'&aih='+aih+'&numero_ibge='+valor;
	ajax_tudo( endereco, atualiza_IBGE2 );
}
function atualiza_IBGE2(txt)
{
	if(txt) alert(txt);
}
function busca_doc_ajax_soli( doc )
{
	var doc		= document.getElementById('aih_n_doc_prof_solicitante').value;
	var cns 	= document.getElementById('aih_tipo_cod_prof_soli_cns').checked;
	var op  	= ( cns ? 'cns' : 'cpf' );
	
	var endereco = 'aih_op.php?acao=busca_med_doc&doc='+doc+'&op='+op;
	//alert (endereco);
	ajax_tudo( endereco, busca_doc_ajax_soli2 );
}

function busca_doc_ajax_soli2( txt )
{
	var Txt		= document.getElementById('doc_result_soli');
	//Txt.innerHTML = txt;
	
	var Str 	= new String(txt);
	var Dados 	= Str.split(';');
		
	if( Dados[0] == 'NOK' )
	{ 
		Txt.innerHTML = '<em><strong>Nenhum m&eacute;dico encontrado com este documento</strong></em>';
		add_prof_soli( '', '', Dados[1] );
	}
	else
	{
		Txt.innerHTML = '<em>M&eacute;dico encontrado, atualizando...</em>';
		add_prof_soli( Dados[0], Dados[1], Dados[2] );
	}
	
}
function add_prof_soli( codigo, nome, doc, mantem )
{
	document.getElementById('med_solicitante_proc_h').value 		= codigo;
	document.getElementById('med_solicitante_proc').value			= nome;
	document.getElementById('aih_n_doc_prof_solicitante').value 	= doc;
	
	if( mantem )
	{
		document.getElementById('aih_tipo_doc_proc_soli_cns').checked = false;
		document.getElementById('aih_tipo_doc_proc_soli_cpf').checked = true;
	}
	
	esconde_janela('janela_aih');
}
// -------- SELECIONANDO E APAGANDO NÚMEROS -----------------------------------------------

function init_numeros(id_login)
{
	ajax_tudo( 'aih_numeros_popup.php?id_login='+id_login, cad_numeros );
}
function add_numero( codigo, numero )
{
	document.getElementById( 'aih_numero_aih_h' ).value = numero;

	esconde_janela('janela_numeros');
}
function apagar_numero( tipo, codigo )
{

	if( ! confirm('Deseja apagar o Número ?') ) return false;

	var endereco = 'aih_numeros_popup.php?acao=del&tipo='+tipo+'&codigo='+codigo;
	ajax_tudo( endereco, cad_numeros );
	setTimeout( "init_numeros('"+id_login+"','"+tipo+"')" , 3000 );
	return false;

}
function busca_numeros(id_login,tipo)
{
	var pc = document.getElementById('num_palavra_chave');
	var endereco = 'aih_numeros_popup.php?id_login='+id_login+'&tipo='+tipo+'&acao=busca';
	endereco +='&palavra_chave='+pc.value;

	janela_carregando( 'janela_numeros' );
	ajax_tudo( endereco, cad_numeros );
	return false;
}
function cad_numeros( txt )
{
	document.getElementById( 'janela_numeros_conteudo' ).innerHTML = txt;
}
