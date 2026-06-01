/* Javascript do arquivo apac.PHP */

function hotkey(eventname)
{
	// ESC esconde todas as janelas
	if( eventname.keyCode == 27 )
	{
		esconde_janela( 'janela_proc' );
		esconde_janela( 'janela_paci' );
		esconde_janela( 'janela_uni' );
		esconde_janela( 'janela_med' );
		//alert('esconde td mundo');
		return false;
		
	}
}

function soma_data( obj, dias )
{
	var D = new String( obj.value );
	var A = D.split('/');
	
	if( A.length != 3 || A[0].length != 2 || A[1].length != 2 || A[2].length != 4 )
	{
		return null;
	}
	
	var Dt = new Date();
	Dt.setDate( A[0] );
	Dt.setMonth( A[1] );
	Dt.setFullYear( A[2] );
	Dt.setDate(Dt.getDate()+30)
	
	var ds = new String( Dt.getDate() );
	var ms = new String( Dt.getMonth() );
	
	var d = ( ds.length == 1 ? '0' + ds : ds );
	var m = ( ms.length == 1 ? '0' + ms : ms );

	return d + '/' + m + '/' + Dt.getFullYear();;
}

function troca_data( obj, dias, id2 )
{
	var Dt 	= soma_data( obj, dias );
	var T2	= document.getElementById( id2 );
	
	T2.value = Dt;
	T2.select();
	
	return false;	
}

/** APAC ----------------------------------------------------------------------
----------------------------------------------------------------------------- */
function busca_apac( id_login )
{
	var pc = document.getElementById('palavra_chave').value;
	var tp = document.getElementById('busca_tipo').value;
	var endereco = 'apac.php?id_login='+id_login+'&acao=busca&palavra_chave='+pc+'&busca_tipo='+tp;
	document.location.href = endereco;
	return false;

}

function valida_form_apac( id_login )
{
	if( ! valida('paci_codigo','Paciente') ) return false;
	if( ! valida('uni_codigo','Unidade Solicitante') ) return false;
	if( ! valida('med_codigo','Médico Solicitante') ) return false;		
	//if( ! valida('orgao_codigo','Órgao Autorizador') ) return false;
	if( ! valida('uni_prestadora_codigo','Unidade Prestadora de Serviços') ) return false;
	if( ! valida('med_aud_codigo','CPF do Autorizado') ) return false;
	if( ! Verifica_Data( 'periodo_val', 1) ) return false;
	if( ! Verifica_Data( 'periodo_val_fim', 1) ) return false;
	
	if( PROC_LISTA.length < 1 &&  PROC_LISTA_APAC.length < 1 )
	{
		alert('Escolha algum Procedimento');
		return false;
	}
	
	return true;
}
/** PROCEDIMENTOS --------------------------------------------------------------
----------------------------------------------------------------------------- */

function init_proc( id_login )
{
	var endereco = 'apac_procedimento_popup.php?id_login='+id_login;
	//alert(endereco);
	ajax_tudo( endereco, cad_proc );
}

function cad_proc( txt )
{
	document.getElementById( 'janela_proc_conteudo' ).innerHTML = txt;
}

var INIT_PROC_LISTA = false;
var PROC_LISTA = new Array();
var PROC_LISTA_APAC = new Array();

function atualiza_proc( num, proc, lab, apac, id )
{
	if( in_array( PROC_LISTA, id ) || in_array( PROC_LISTA_APAC, id ) )
	{
		alert('O procedimento '+ proc +' já está na lista !');
		return false;
	}
	
	if( PROC_LISTA.length + PROC_LISTA_APAC.length >= 7 )
	{
		alert('Maximo de 7 procedimentos cadastrados !');
		return false;
	}
	
	if( apac == 'N' )
	{
		PROC_LISTA.push( id );
		//alert( "APAC=N\n"+ PROC_LISTA );
	}
	else
	{
		PROC_LISTA_APAC.push( id );
		//alert( "APAC=S\n"+ PROC_LISTA_APAC );		
	}
	
	var Proc = document.getElementById('procedimento_lista');
	if( ! INIT_PROC_LISTA )
	{
		Proc.innerHTML = '';
		INIT_PROC_LISTA = true;
	}
	
	Proc.innerHTML += "<tr id='proc_tr_"+id+apac+"'><td>"+proc+"</td><td width='220'>"+lab+"</td>"+
	"<td width='15' class='c'>"+apac+"</td>"+"<td width='60'>"+num+"</td>"+
	"<td width='80'>"+
	"<img src='"+root+comum+"imgs/apagar_on.jpg' alt='deletar' onclick='remove_proc("+id+",\""+apac+"\")' style='cursor:pointer;' /></td>"+
	"</tr>"+
	( apac == 'N' ?
		"<input type='hidden' name='proc_lista[]' value='"+id+"' id='proc_h_"+id+"N' />" :
		"<input type='hidden' name='proc_lista_apac[]' value='"+id+"' id='proc_h_"+id+"S' />"
	);
	
	//var M = ( apac == 'N' ? 'proc_h_'+id+'N' : 'proc_h_'+id+'S' );
	//alert( 'valor='+ $F(M) ); 	
	
}
// remove da lista JAVASCRIPT
function remove_proc( id, apac )
{
	//alert(id + ':' + apac ); return false;
	
	if( ! confirm('Remover o Procedimento '+id+' ?') ) return false;
	var tr = document.getElementById( 'proc_tr_'+id+apac );
	var hi = document.getElementById( 'proc_h_'+id+apac );
	//alert( tr.id +"\n"+ tr.innerHTML +"\n"+ hi.value );
	tr.style.display = 'none';
	hi.value = 0;
	if( apac == 'N' )
		PROC_LISTA = remove( PROC_LISTA, id );	
	else
		PROC_LISTA_APAC = remove( PROC_LISTA_APAC, id );	
}

function busca_proc( id_login )
{
	var pc = document.getElementById('proc_palavra_chave');
	var endereco = 'apac_procedimento_popup.php?id_login='+id_login+'&acao=busca&palavra_chave='+pc.value;
	janela_carregando( 'janela_proc' );
	ajax_tudo( endereco, cad_proc );
	return false;
}

function form_proc( id_login, codigo )
{

	if( codigo != null )
		acao 	= 'form_edit';
	else
	{
		codigo 	= 0;
		acao 	= 'form_add';
	}
	
	janela_carregando( 'janela_proc' );
	var endereco = 'apac_procedimento_popup.php?id_login='+id_login+'&acao='+acao+'&codigo='+codigo;
	ajax_tudo( endereco, cad_proc );
}

function form_proc_submit( id_login, acao )
{
	var c0 = document.getElementById('codigo').value;
	var c1 = document.getElementById('proc_nome').value;
	var c2 = document.getElementById('proc_numero').value;
	var c3 = document.getElementById('med_codigo').value;
	var c4 = document.getElementById('proc_valor').value;
	var c5 = document.getElementById('proc_tipo').value;
	var endereco = 'apac_procedimento_popup.php?id_login='+id_login+'&codigo='+c0+'&proc_nome='+c1+'&proc_numero='+c2;
	endereco 	+= '&med_codigo='+c3+'&proc_valor='+c4+'&proc_tipo='+c5+'&acao='+acao;
	//alert(endereco);
	ajax_tudo( endereco, cad_proc );
	//setTimeout( "init_proc('"+id_login+"')" , 3000 );
	return false;
}

// remove do BANCO (via popup)
function apagar_proc( id_login, codigo )
{
	if( ! confirm('Deseja apagar o Procedimento ?') ) return false;
	
	var endereco = 'apac_procedimento_popup.php?id_login='+id_login+'&acao=del&codigo='+codigo;
	//alert(endereco);
	ajax_tudo( endereco, cad_proc );
	//setTimeout( "init_proc('"+id_login+"','"+tipo+"')" , 3000 );
	return false;

}
/** PACIENTES ------------------------------------------------------------------
----------------------------------------------------------------------------- */
function init_paci(id_login)
{
	ajax_tudo( 'apac_paciente_popup.php?id_login='+id_login, cad_paci );
}
function cad_paci( txt )
{
	document.getElementById( 'janela_paci_conteudo' ).innerHTML = txt;
}

// function add_paci( codigo, nome, cpf, apac )
// {
// 	var endereco = 'apac_paciente_popup.php?acao=verifica&codigo='+codigo+'&apac='+apac;
// 	ajax_tudo( endereco, cad_paci );
// }

function add_paci( id_login, codigo, nome, cpf, apac )
{
	document.getElementById( 'paci_codigo' ).value 	= codigo;
	document.getElementById( 'paci_nome_r' ).value 	= nome;
	document.getElementById( 'paci_cpf_r' ).value 	= cpf;
	document.getElementById( 'paci_cpf' ).value 	= cpf;
	document.getElementById( 'apac_paci' ).value 	= apac;
	esconde_janela('janela_paci');
	ajax_tudo( 'apac_paciente_popup.php?id_login='+id_login+'&acao=verifica&codigo='+codigo+'&apac='+apac, add_paci_verifica );
}

function add_paci_verifica( txt )
{
	if( txt == 'NOK' )
	{
		alert("O paciente escolhido possui uma APAC cadastrada há menos de 30 dias !");
	}	
}

function busca_paci(id_login)
{
	var pc = document.getElementById('pac_palavra_chave');
	var endereco = 'apac_paciente_popup.php?id_login='+id_login+'&palavra_chave='+pc.value+'&acao=busca';
	
	//alert(endereco);
	janela_carregando( 'janela_paci' );
	ajax_tudo( endereco, cad_paci );
	return false;
}

function form_paci( id_login, codigo )
{
	if( codigo != null )
		acao 	= 'form_edit';
	else
	{
		codigo 	= 0;
		acao 	= 'form_add';
	}
	
	var endereco = 'apac_paciente_popup.php?id_login='+id_login+'&acao='+acao+'&codigo='+codigo;
	ajax_tudo( endereco, cad_paci );
}

function form_paci_submit( id_login, acao )
{
	var cod = document.getElementById('codigo').value;	
	var no 	= document.getElementById('pac_nome').value;
	
	var sxM	= document.getElementById('pac_sexo_m').checked;
	var sx	= ( ! sxM ? 'F' : 'M' );
	
	var cpf	= document.getElementById('pac_cpf_cns').value;
	var mr 	= document.getElementById('pac_mae_responsavel').value;
	var er 	= document.getElementById('pac_end_rua').value;
	var en  = document.getElementById('pac_end_nr').value;	
	var ec 	= document.getElementById('pac_end_compl').value;
	var eb 	= document.getElementById('pac_end_bairro').value;
	var ece	= document.getElementById('pac_end_cep').value;
	var eci	= document.getElementById('pac_end_cidade').value;
	var dn 	= document.getElementById('pac_dt_nasc').value;
	var tf	= document.getElementById('pac_telefone').value;
	var pai	= document.getElementById('pac_pai').value;
	var tco	= document.getElementById('pac_tem_convenio').value;
	var con	= document.getElementById('pac_convenio_nome').value;
	
	var endereco = 'apac_paciente_popup.php?id_login='+id_login+'&acao='+acao+'&codigo='+cod;
	endereco += '&pac_nome='+no+'&pac_sexo='+sx+'&pac_cpf_cns='+cpf+'&pac_mae_responsavel='+mr;
	endereco += '&pac_end_rua='+er+'&pac_end_nr='+en+'&pac_end_compl='+ec+'&pac_end_bairro='+eb;
	endereco += '&pac_end_cep='+ece+'&pac_end_cidade='+eci+'&pac_dt_nasc='+dn+'&pac_telefone='+tf;
	endereco += '&pac_pai='+pai+'&pac_tem_convenio='+tco+'&pac_convenio_nome='+con;

	ajax_tudo( endereco, cad_paci );
	setTimeout( "init_paci('"+id_login+"')" , 3000 );
	return false;
}

function apagar_paci( id_login, codigo )
{
	if( ! confirm('Deseja apagar o Paciente ?') ) return false;
	
	var endereco = 'apac_paciente_popup.php?id_login='+id_login+'&acao=del&codigo='+codigo;
	alert(endereco);
	ajax_tudo( endereco, cad_paci );
	setTimeout( "init_paci('"+id_login+"','"+tipo+"')" , 3000 );
	return false;

}

function pac_atualiza_cpf( id_login, obj )
{
	var apac 	 = $F('apac_paci');
	var cod 	 	 = $F('paci_codigo');
	var endereco = 'apac_paciente_popup.php?id_login='+id_login+'&codigo='+cod+'&cpf='+obj.value+'&apac='+apac+'&acao=atualiza_cpf';
	ajax_tudo( endereco, pac_atualiza_cpf_2 );
}
function pac_atualiza_cpf_2( c )
{
	if( c.length > 0 )
		alert(c);
}
// -------- SELECIONANDO E APAGANDO NÚMEROS -----------------------------------------------

function init_numeros(id_login)
{
	ajax_tudo( 'apac_numeros_popup.php?id_login='+id_login, cad_numeros );
}
function add_numero( codigo, numero, numero_volta, numero_vai )
{
	document.getElementById( 'apac_num_h' ).value = numero;
	document.getElementById( 'apac_num_r' ).value = numero;

//	var endereco = 'apac_update_numeros_resto.php?numero_volta='+numero_volta+'&numero_vai='+numero_vai;
//	ajax_tudo(endereco, cad_numeros );
	
	esconde_janela('janela_numeros');
}
function apagar_numero( tipo, codigo )
{

	if( ! confirm('Deseja apagar o Número ?') ) return false;

	var endereco = 'apac_numeros_popup.php?acao=del&tipo='+tipo+'&codigo='+codigo;
	ajax_tudo( endereco, cad_numeros );
	setTimeout( "init_numeros('"+tipo+"')" , 3000 );
	return false;

}
function busca_numeros(id_login,tipo)
{
	var pc = document.getElementById('num_palavra_chave');
	var endereco = 'apac_numeros_popup.php?id_login='+id_login+'&tipo='+tipo+'&acao=busca';
	endereco +='&palavra_chave='+pc.value;

	janela_carregando( 'janela_numeros' );
	ajax_tudo( endereco, cad_numeros );
	return false;
}
function cad_numeros( txt )
{
	document.getElementById( 'janela_numeros_conteudo' ).innerHTML = txt;
}


// ----------------------------------------------------------------------------------------

/*1234567890* UNIDADES -------------------------------------------------------------------
----------------------------------------------------------------------------- */

var UNIDADES = new Array('', 'Unidade Solicitante','Órgão Autorizador','Unidade Prestadora de Serviços');

// ids das unidades
var U_IDS = new Array();
U_IDS[1] = new Array( 'uni_codigo_r', 'uni_codigo', 'uni_nome_r', 'apac_uni_sol' );
U_IDS[2] = new Array( 'orgao_codigo_r', 'orgao_codigo', 'orgao_nome_r', 'apac_orgao' );
U_IDS[3] = new Array( 'uni_prestadora_codigo_r', 'uni_prestadora_codigo', 'uni_prestadora_nome_r', 'apac_uni_pres', 'uni_prestadora_cnpj_r' );

function init_uni( id_login, tipo )
{
	//alert(id_login)
	document.getElementById('janela_uni_titulo_txt').innerHTML = UNIDADES[ tipo ];
	ajax_tudo( 'apac_unidade_popup.php?id_login='+id_login+'&tipo='+tipo, cad_uni )
}
function cad_uni( txt )
{
	document.getElementById( 'janela_uni_conteudo' ).innerHTML = txt;
}
function busca_uni(id_login,tipo)
{
	var pc = document.getElementById('uni_palavra_chave');
	var endereco = 'apac_unidade_popup.php?id_login='+id_login+'&tipo='+tipo+'&acao=busca';
	endereco +='&palavra_chave='+pc.value;

	janela_carregando( 'janela_uni' );
	ajax_tudo( endereco, cad_uni );
	return false;
}
function add_unidade( codigo, nome, tipo, apac, cnpj )
{
	document.getElementById( U_IDS[tipo][0] ).value = codigo;
	document.getElementById( U_IDS[tipo][1] ).value = codigo;
	document.getElementById( U_IDS[tipo][2] ).value = nome;
	document.getElementById( U_IDS[tipo][3] ).value = apac;

	//if( cnpj != '' )
	if( tipo == 3 )
		document.getElementById( U_IDS[tipo][4] ).value = ( cnpj ? cnpj : '000.000.000/00' );
	esconde_janela('janela_uni');
}

function form_uni( id_login, tipo, codigo )
{
	if( codigo != null )
	{
		acao 	= 'form_edit';
	}
	else
	{
		codigo 	= 0;
		acao 	= 'form_add';
	}

	var endereco = 'apac_unidade_popup.php?id_login='+id_login+'&acao='+acao+'&tipo='+tipo+'&codigo='+codigo;
	ajax_tudo( endereco, cad_uni );
}

function form_uni_submit( id_login, tipo, acao )
{
	var d 	= document.getElementById('uni_desc').value;
	var l 	= document.getElementById('uni_localizacao').value;
	var r 	= document.getElementById('uni_responsavel').value;
	var c 	= document.getElementById('uni_cnpj').value;
	var cod = document.getElementById('codigo').value;

	var endereco = 'apac_unidade_popup.php?id_login='+id_login+'&acao='+acao+'&tipo='+tipo+'&uni_desc='+d;
	endereco += '&uni_localizacao='+l+'&uni_responsavel='+r+'&uni_cnpj='+c+'&codigo='+cod;

	ajax_tudo( endereco, cad_uni );
	setTimeout( "init_uni('"+id_login+"','"+tipo+"')" , 3000 );
	return false;
}

function apagar_uni( id_login, tipo, codigo )
{

	if( ! confirm('Deseja apagar a Unidade ?') ) return false;

	var endereco = 'apac_unidade_popup.php?id_login='+id_login+'&acao=del&tipo='+tipo+'&codigo='+codigo;
	ajax_tudo( endereco, cad_uni );
	setTimeout( "init_uni('"+id_login+"','"+tipo+"')" , 3000 );
	return false;

}
/** MÉDICOS --------------------------------------------------------------------
----------------------------------------------------------------------------- */
var MEDICOS = new Array('','Médico Solicitante', 'Médico Auditor');

function init_med( id_login, tipo )
{
	document.getElementById('janela_med_titulo_txt').innerHTML = MEDICOS[ tipo ];
	ajax_tudo( 'apac_medico_popup.php?id_login='+id_login+'&tipo='+tipo, cad_med );
}
function cad_med( txt )
{
	document.getElementById( 'janela_med_conteudo' ).innerHTML = txt;
}
function busca_med(id_login,tipo)
{
	var pc = document.getElementById('med_palavra_chave');
	var endereco = 'apac_medico_popup.php?id_login='+id_login+'&tipo='+tipo+'&acao=busca&palavra_chave='+pc.value;
	//alert(endereco);
	janela_carregando( 'janela_med' );
	ajax_tudo( endereco, cad_med );
	return false;
}
function add_medico( codigo, nome, cpf, tipo, apac )
{
	if( tipo == 1 )
	{
		document.getElementById( 'apac_med_sol' ).value = apac;
		document.getElementById( 'med_cpf_r' ).value 	= cpf;
		document.getElementById( 'med_codigo' ).value 	= codigo;
		document.getElementById( 'med_nome_r' ).value 	= nome;
	}
	else
	{
		document.getElementById( 'apac_med_aud' ).value = apac;
		document.getElementById( 'med_aud_codigo' ).value = codigo;
		document.getElementById( 'med_aud_cpf_r' ).value = cpf;
	}
	
	esconde_janela('janela_med');
}

function form_med( id_login, tipo, codigo )
{
	if( codigo != null )
	{
		acao 	= 'form_edit';
	}
	else
	{
		codigo 	= 0;
		acao 	= 'form_add';
	}
	
	var endereco = 'apac_medico_popup.php?id_login='+id_login+'&acao='+acao+'&tipo='+tipo+'&med_codigo='+codigo;
	ajax_tudo( endereco, cad_med );
}

function form_med_submit( id_login, tipo, acao )
{
	var c0 = document.getElementById('med_codigo').value;
	var c1 = document.getElementById('med_crm').value;
	var c2 = document.getElementById('med_nome').value;
	var c3 = document.getElementById('med_cpf').value;
	var c4 = document.getElementById('med_rg').value;
	var endereco = 'apac_medico_popup.php?id_login='+id_login+'&tipo='+tipo+'&acao='+acao+'&med_codigo='+c0+'&med_crm='+c1+'&med_nome='+c2+'&med_cpf='+c3+'&med_rg='+c4;
	//alert(endereco);
	janela_carregando( 'janela_med' );
	ajax_tudo( endereco, cad_med );
	setTimeout( "init_med('"+id_login+"','"+tipo+"')" , 3000 );
	return false;
}

function apagar_med( id_login, tipo, codigo )
{
	if( ! confirm('Deseja apagar o Médico ?') ) return false;
	
	var endereco = 'apac_medico_popup.php?id_login='+id_login+'&acao=del&tipo='+tipo+'&med_codigo='+codigo;
	//alert(endereco);
	ajax_tudo( endereco, cad_med );
	setTimeout( "init_med('"+id_login+"','"+tipo+"')" , 3000 );
	return false;

}
