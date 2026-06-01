<?php
	session_start(); 
?>
function buscar_especialidade(controle)
{

	var uni_codigo = $('uni_codigo').value;
	var med_codigo = $('med_codigo').value;
	
	if(uni_codigo == "" || med_codigo == "")
	{
		desabilitar();
		$('esp_codigo').options[0] = new Option("---", "");
	}
	
	url = "recepcao.ajax.php?acao=especialidade&med_codigo="+med_codigo+"&controle="+controle;
	
	url = ( controle == 0 ? url + "&uni_codigo="+uni_codigo : url );
	
	if(med_codigo != "")
	{
		$('esp_codigo').options[0] = new Option("Carregando", "");
		ajax_tudo(url, popular_especialidade);
		return true;
	} else {
		return false;
	}
	
}

function popular_especialidade( txt )
{
//	alert(txt);
	txt = eval(txt);
	$('esp_codigo').innerHTML = '';
	for(i = 0; i < txt.length; i++)
	{
		esp_codigo = txt[i].esp_codigo;
		esp_nome   = txt[i].esp_nome;
		$('esp_codigo').options[i] = new Option(esp_nome, esp_codigo);
	}
	
	habilitar();
	
}

function habilitar()
{
	if($('esp_codigo').value != "" && $('uni_codigo').value != "")
	{
		$('btn_lista_paciente').src = '<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/listarpacientes_on.jpg';
		$('age_data').setAttribute("onkeypress","if(event.keyCode == 13){buscar_agendados();}return Ajusta_Data(this, event);");
		$('btn_lista_paciente').setAttribute("onclick","buscar_agendados();")
	}
}

function desabilitar()
{
	$('btn_lista_paciente').src = '<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/listarpacientes_off.jpg';
	$('btn_lista_paciente').setAttribute("onclick","")
	$('age_data').setAttribute("onkeypress","return Ajusta_Data(this, event);");
}

function buscar_agendados()
{
	$('nome_cadastrador').innerHTML = '';
	$('cadastrado_por').innerHTML = '';
	$('nome_alterador').innerHTML = '';
	$('alterado_por').innerHTML = '';
	if(arguments.length > 0)
	{
		aux = arguments[0];
		if(aux == "false")
		{
			alert("Operacao nao permitida, pois a quantidade de vagas para esse medico ja foi preenchida!");
		}
	}
	if($('btn_lista_paciente').src != '<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/listarpacientes_off.jpg')
	{
		var uni_codigo = $('uni_codigo').value;
		var med_codigo = $('med_codigo').value;
		var esp_codigo = $('esp_codigo').value;
		var age_data = $('age_data').value;
		
		url = "recepcao.ajax.php?acao=agendados&uni_codigo="+uni_codigo+"&med_codigo="+med_codigo+"&esp_codigo="+esp_codigo;
		url = (age_data != "" ? url + "&age_data="+age_data : url);
		
		var dataReplace = age_data.split("/");//.replace("/","|"); 
		var dataParametro = dataReplace[0]+"|"+dataReplace[1]+"|"+dataReplace[2];
		
		$('btn_lista_paciente2').src = '<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/imprimir.jpg';
		$('btn_lista_paciente2').setAttribute("onclick","imprimirProntuario("+uni_codigo+",'"+dataParametro+"',"+med_codigo+","+esp_codigo+");")
		
		$('carregando').style.display = '';
		ajax_tudo(url, popular_lista);
		return true;
	} else {
		
		return false;
	
	}
}

function popular_lista(txt){
	$('agendados').innerHTML = txt;
	$('carregando').style.display = 'none';
	arrastarESoltarListaDePacientes();
	
}

function arrastarESoltarListaDePacientes(){
	jQuery( "table tbody" ).sortable({
		revert: true,
		axis: "y",
		stop: function(e,u){
			var arr = jQuery("input[name^=ordem]");
			var ordem = [];
			arr.each(function(){
				ordem.push(this.value);
			});

			window.console && console.log('enviando...');
			jQuery.ajax({
				url: 'portadeEntrada/ordem.php',
				type: 'post',
				data: {
					ordem: ordem
				},
				success: function() {
					window.console && console.log('reordenado!');
				}
			});

		}
	});
	jQuery( "td, th" ).disableSelection();
}

function imprimirProntuario(uni_codigo,data,med_codigo,espe){
	var dataReplace = data.split("|");
	var dataSql = dataReplace[0]+"/"+dataReplace[1]+"/"+dataReplace[2];
		
	url = "imprimir_agenda.php?acao=imprimir&uni_codigo="+uni_codigo+"&med_codigo="+med_codigo+"&esp_codigo="+espe+"&age_data="+dataSql;
	window.open(url, null,"height=800,width=1200,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
	
}

function opcao(op, age_codigo)
{
	var age_data = $('age_data').value;
	url = "recepcao.ajax.php?acao=opcao&opcao="+op+"&age_codigo="+age_codigo;
	url = (age_data != "" ? url + "&age_data="+age_data : url);
	ajax_tudo(url, buscar_agendados);
}

function buscar_agendados2(txt)
{
	$('agendados').innerHTML = txt;
	//alert(txt);
}

function salvarFaltaMedico(med_codigo, age_codigo, id_login, tipo)
{   
    if(tipo == 'salvar')
    {
        aux = confirm('Deseja Salvar Falta para este medico?');
    } else {
        aux = confirm('Deseja Retirar Falta para este medico?');
    }
    if(aux)
    {
		url = "recepcao.ajax.php?acao=opcao&opcao=M&age_codigo="+age_codigo;
		ajax_tudo(url, buscar_agendados);
	}
}

function colocar_informacao(usu_cad, usu_alt)
{
	$('nome_cadastrador').innerHTML = usu_cad;
	$('cadastrado_por').innerHTML = 'Cadastrado por:';
	$('nome_alterador').innerHTML = usu_alt;
	$('alterado_por').innerHTML = 'Alterado por:';
}