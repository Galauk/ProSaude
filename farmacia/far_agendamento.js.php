<?php
session_start();
echo "var id_login = '{$_GET[id_login]}'";
echo "var path = ".$_SESSION[linkroot].$_SESSION[comum];
?>

/**
 * Hotkey 'binding' do body
 *
 * @param {Event} Evt
*/
function hotkey(evt)
{
	var code = evt.keyCode || evt.which;

	if( code == 113 )
	{
		link_f2();
		return false;
	}
	// apertou o F7
	if( code == 118 )
	{
		link_f7();
		return false;
	}
	// apertou o F8
	if( code == 119 )
	{
		link_f8();
		return false;
	}
}

/**
 * Dispara quando apertado F2
*/
function link_f2( )
{
	var age_tipo = $F('age_tipo'),
		med_codigo = $F('med_codigo'),
		uni_codigo = $F('uni_codigo'),
		esp_codigo = $F('esp_codigo'),
		age_item = $F('age_item');
	
	if( age_tipo == 0 || med_codigo == 0 || uni_codigo == 0 || esp_codigo == 0 || age_item == 0 )
	{
		alert("Configuração incompleta !\nSelecione todos os dados antes de consultar as vagas !");
		return false;
	
	}
		
	var params = "age_tipo="+age_tipo+"&id_login="+id_login+"&uni_codigo="+uni_codigo+
		"&esp_codigo="+esp_codigo+"&med_codigo="+med_codigo+"&age_item="+age_item;
		
	window.open("vagas_medico.php?"+params,
				 null,
				 "height=250,width=250,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
	return false;
}

/**
 * Dispara quando apertado F7
*/
function link_f7( )
{
	buscar_nome($F('pac_nome'), 'buscar_nome');
	/*window.open( 'list_pacientes.php?id_login='+id_login+'&controle=1',
				 null,
				 'height=460,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes');*/
	return false;
}

/**
 * Dispara quando apertado F8
*/
function link_f8( )
{
	//window.open( 'paciente_ficha.php?acao=form_add&id_login='+id_login+'&controle=1',
	//			 null,
	//			 'height=460,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes');

	window.open( 'paciente.php?acao=form_add&id_login='+id_login+'&controle=1',
				 null,
				 'height=460,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes');


	return false;
}


/**
 * Atualiza o SELECT dos medicos...
*/
function at_medico()
{
	var esp = $('esp_codigo'), med = $('med_codigo');

	med.length = 1;
	med.disabled = true;
	
	if( esp.value == 0 )
	{
		med.options[0].value = 0;
		med.options[0].text = '...';
		return false;
	}

	med.options[0].text = 'CARREGANDO...';
	
	var url = 'exa_agendamento.ajax.php?acao=busca_medico&esp_codigo='+esp.value;
	ajax_tudo( url, at_medico_cb );
	return null;
}

/**
 * (CALLBACK) Atualiza o SELECT dos medicos...
*/
function at_medico_cb( resp )
{
	var med = $('med_codigo'), obj = eval( resp );
	
	med.length = 1;
	
	if( obj.length == 0 )
	{
		med.disabled = true;
		med.options[0].text = '...';
		return null;
	}
	
	for( var i = 0; i < obj.length; i++ )
	{
		med.length = i+1;
		med.options[ i ].text = obj[i].med_nome;
		med.options[ i ].value = obj[i].med_codigo;
	}
	med.disabled = false;
	
	/*at_iframe_esq(); */
	
	return null;
}

/**
 * Manipula preferencia dos dia/horário
*/
function preferencia_dia( msg )
{
	// tenta atualizar o iframe
	at_iframe_esq();
	
	// evita os avisos...
	if( ! $F('pref_dia') ) return false;
	
	if( msg && ! Verifica_Data( 'pref_dia', 1) ) return false;
	
	var data = $F('pref_dia'),
		med = $F('med_codigo'),
		esp = $F('esp_codigo'),
		uni = $F('uni_codigo');
	
	if( med == 0 || esp == 0 || uni == 0 || data == 0 )
	{
		if( msg )
			alert("Preencha corretamente todos os campos !");
		return false;
	}
	
	var hora = $('pref_horario');
	
	hora.options[0].text = 'BUSCANDO...';
	
	var url = 'fazer_agendamento.ajax.php?acao=busca_horario&data='+data+
				'&med_codigo='+med+'&esp_codigo='+esp+'&uni_codigo='+uni;
				
	ajax_tudo( url, preferencia_dia_cb );
	return false;
}

/**
 * (CALLBACK) Manipula preferencia dos dia/horário
*/
function preferencia_dia_cb( resp )
{
	try
	{
		var obj = eval( resp ), hora = $('pref_horario');

		hora.length = 1;
		
		if( obj.length == 0 )
		{
			hora.disabled = true;
			hora.options[0].text = 'NENHUM !';
			return false;
		}
		
		for( var i = 0; i < obj.length; i++ )
		{
			hora.length = i+1;
			hora.options[ i ].text = obj[i].gra_hora_ini;
			hora.options[ i ].value = obj[i].gra_hora_ini;
		}
		
		hora.disabled = false;
		
		// tenta atualizar o iframe
		at_iframe_esq();
		
	}
	catch( e )
	{
		alert( e );
	}
}


/**
 * Atualiza as informações do agente
*/
function at_agente()
{
	var agt = $('agt_codigo');
	
	if( agt.value == 0 ) return false;
	
	var url = 'fazer_agendamento.ajax.php?acao=busca_agente&agt_codigo='+agt.value;
	ajax_tudo( url, at_agente_cb );
	return null;
}

/**
 * (CALLBACK) Atualiza as informações do agente
*/
function at_agente_cb( resp )
{
	var num = $('agt_numero'), respon = $('agt_responsavel'), obj = eval( resp );
	
	num.value = obj.agt_numero;
	respon.value = obj.agt_responsavel;
	
	at_iframe_esq();
	
	return null;
}

/**
 * Atualiza os campos a partir do 'selecionar' via popup
*/
function pacientes( codigo, nome, nascimento, mae, cidade )
{
	// as demais atualizações não serve para mais nada !
	// ele chama o 'ajax'
	
	$('pac_codigo').value 		= codigo;
	$('pac_nome').value 		= nome;
	$('pac_nascimento').value 	= nascimento;
	$('pac_mae').value 			= mae;
	$('pac_cidade').value 		= cidade;
	$('pac_prontuario').value 	= '';
	
	try
	{
		setTimeout( "busca_pac_prontuario( $F('pac_codigo') )", 1 );
		return false;
	}
	catch( e )
	{
		alert( e );
	}
}

/**
 * Busca o paciente por um prontuário digitado ou pelo código (pk), quando
 * acionado pela fç pacientes()
*/
function busca_pac_prontuario( codigo )
{
	var p = $('pac_prontuario'), s = $('pac_busca_status');
	
	if( ! p.value && ! codigo ) return false;
	
	try
	{
		s.innerHTML = ' ( Procurando... ) ';
		var campo 	= ( codigo ? 'usu_codigo' 		: 'usu_prontuario' );
		var acao 	= ( codigo ? 'pega_pac_dados' 	: 'busca_pac_prontuario' );
		var valor 	= ( codigo ? codigo 			: p.value );
		var url 	= 'fazer_agendamento.ajax.php?acao='+acao+'&'+campo+'='+valor;
		
		//alert(url);
		ajax_tudo( url, busca_pac_prontuario_cb );	
		return false;
	}
	catch( e )
	{
		alert(e);
	}
}

/**
 * (CALLBACK) Atualiza os dados do paciente
*/
function busca_pac_prontuario_cb( resp )
{
	var s = $('pac_busca_status');
	s.innerHTML = '&nbsp;';
	try
	{
		var obj = eval( resp );
		//obj = { ok : false, usu_prontuario : 111111111111111 }
		if( ! obj.ok )
		{
			alert( 'Prontuário "'+obj.usu_prontuario+'" não encontrado !' );
			obj.usu_prontuario = '';
		}
		
		//alert(resp);
		
		$('pac_codigo').value 		= obj.usu_codigo;
		$('pac_nome').value 		= obj.usu_nome;
		$('pac_nascimento').value 	= obj.data;
		$('pac_mae').value 			= obj.usu_mae;
		$('pac_cidade').value 		= obj.cidade;
		$('pac_prontuario').value 	= obj.usu_prontuario;
		
		// tenta atualizar os iframe
		setTimeout( 'at_iframe_esq()', 250 );
		setTimeout( 'at_iframe_dir()', 750 );
	}
	catch( e )
	{
		alert(e);
	}
}

/**
 * Tenta atualizar o iframe da esquerda !
*/
function at_iframe_esq( msg )
{
	var data = $F('pref_dia'),
		med = $F('med_codigo'),
		esp = $F('esp_codigo'),
		uni = $F('uni_codigo'),
		agt = $F('agt_codigo'),
		age_item = $F('age_item'),
		age_tipo = $F('age_tipo'),
		pac_codigo = $F('pac_codigo'),
		pac_nome = $F('pac_nome'),
		hora = $F('pref_horario');
	
	if( med == 0 || esp == 0 || uni == 0 || agt == 0 || age_tipo == 0 ||
	    age_item == 0 || ! pac_codigo || ! pac_nome )
	{
		//alert( 'at_iframe_esq erro' );
		if( msg ) alert('Preencha todos os campos antes de prosseguir !');
		$('btn_enviar').src = path+'imgs/enviar_off.jpg';

		return false;
	}
	
	$('btn_enviar').src = path+'imgs/enviar_on.jpg';
		
	var url = 'agendamento_fazer.php'+
				'?age_data='+data+
				'&agt_codigo='+ agt +
				'&id_login='+ id_login +
				'&med_codigo='+ med +
				'&uni_codigo='+ uni +
				'&esp_codigo='+ esp +
				'&usu_codigo='+ pac_codigo +
				'&age_paciente='+ pac_nome +
				'&age_tipo='+ age_tipo +
				'&age_item='+ age_item +
				'&age_hora='+ hora;

	//alert(url);
	$('iframe_esq').src = url;
	
	if( ! ATUALIZOU_DIR ) at_iframe_dir();
	
	return false;
}

var ATUALIZOU_DIR = false;

/**
 * Tenta atualizar o iframe da direita !
*/
function at_iframe_dir( )
{
	var med = $F('med_codigo'),
		esp = $F('esp_codigo'),
		uni = $F('uni_codigo'),
		agt = $F('agt_codigo'),
		age_item = $F('age_item'),
		age_tipo = $F('age_tipo'),
		pac_codigo = $F('pac_codigo'),
		pac_nome = $F('pac_nome');
	
	if( med == 0 || esp == 0 || uni == 0 || agt == 0 || age_tipo == 0 ||
	    age_item == 0 || ! pac_codigo || ! pac_nome )
	{
		//alert( 'at_iframe_dir erro' );
		return false;
	}

	ATUALIZOU_DIR = true;

	var url = 'agendamento_atendimento.php'+
				'?id_login='+ id_login +
				'&usu_codigo='+ pac_codigo;

	//alert(url);

	$('iframe_dir').src = url;
	
	return false;
}
