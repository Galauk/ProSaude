$(function (){

	$("#pro_nome").buscar({
        url: baseUrl + '/default/cli-agenda/buscar',
        template: function(ul, item) {
            return $("<li/>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function() {
            return true;
        }
    });

})

$(function (){

	$("#usr_nome").buscar({
        url: baseUrl + '/default/cli-agenda/buscar-paciente',
        template: function(ul, item) {
            return $("<li/>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function() {
            return true;
        }
    });

})

function pesquisaPorProcesimento () {
	if ( $('#validaTipoPaciente') ) {
		
		$("#botaoBuscarPaciente").hide();
		$("#buscaPorPaciente").hide();
		$("#buscaPorProcedimento").show();
		$("#botaoBuscarProcedimento").show();

	}
}

function pesquisaPorPaciente () {
	if ( $('#validaTipoPaciente') ) {

		$("#botaoBuscarProcedimento").hide();
		$("#buscaPorProcedimento").hide();
		$("#buscaPorPaciente").show();
		$("#botaoBuscarPaciente").show();

	}
}

function recuperaAgendamentoPorPeriodo() {
	var recebeDataInicial = $("#dataInicial").val();
	var recebeDataFinal = $("#dataFinal").val();
	var recebeIdDoUsuario = $("#id_cli_medicos").val();
	
	if (!recebeIdDoUsuario) {
		alert("Informe um Procedimento");
		return 0
	}

	var verificaDatas =	validaDatas();

	if (!verificaDatas) {
		return 0
	}

	$.ajax({
		url: baseUrl +'/default/agendamento-anterior/recupera-agendamento-por-periodo',
		type: 'POST',
		data: {
			recebeDataInicial : recebeDataInicial,
			recebeDataFinal : recebeDataFinal,
			recebeIdDoUsuario : recebeIdDoUsuario
		},
		success : function(agendaRetorno){
			// console.log(agendaRetorno);
			var recebeResultado = JSON.parse(agendaRetorno)
			// console.log(recebeResultado[0].usr_nome);
			var dadosDaTabela = '';

			for(i = 0 ; i < recebeResultado.length ; i++){
				dadosDaTabela+=
				`<tr>
					<td class = 'dadosDaAgenda' >${recebeResultado[i].nome}</td>
					<td class = 'dadosDaAgenda' >${recebeResultado[i].usu_nome}</td>
					<td class = 'dadosDaAgenda' >${recebeResultado[i].data}</td>
				</tr>
				`
			}

			$('#resultadoAgendaMigrate').empty().append(
				`
				<table id="tableResultado">
					<tr>
						<th class = "titulos"> Procedimentos </th>
						<th class = "titulos"> Paciente </th> 
						<th class = "titulos"> Data </th>
					</tr>
					${dadosDaTabela}
				</table>
				`				 	
			);
		}
	})
}

function validaDatas(){
    var dataInicial = new Date($("#dataInicial").val() );
    var dataFinal = new Date($("#dataFinal").val() );

    if (!dataInicial || !dataFinal) {
    	alert("Existe algum campo vazio !");
    	return 0;
    }

    if (dataInicial >= dataFinal) {
        alert("Data Inicial maior que final!");
        return 0;
    } else{
    	return 1
    }

}

function recuperaAgendamentoPorPeriodoPaciente() {
	var recebeDataInicial = $("#dataInicial").val();
	var recebeDataFinal = $("#dataFinal").val();
	var recebeIdDoUsuario = $("#usu_codigo").val();
	
	if (!recebeIdDoUsuario) {
		alert("Informe um Procedimento");
		return 0
	}

	var verificaDatas =	validaDatas();

	if (!verificaDatas) {
		return 0
	}

	$.ajax({
		url: baseUrl +'/default/agendamento-anterior/recupera-agendamento-por-periodo-paciente',
		type: 'POST',
		data: {
			recebeDataInicial : recebeDataInicial,
			recebeDataFinal : recebeDataFinal,
			recebeIdDoUsuario : recebeIdDoUsuario
		},
		success : function(agendaRetorno){
			// console.log(agendaRetorno);
			var recebeResultado = JSON.parse(agendaRetorno)
			// console.log(recebeResultado[0].usr_nome);
			var dadosDaTabela = '';

			for(i = 0 ; i < recebeResultado.length ; i++){
				dadosDaTabela+=
				`<tr>
					<td class = 'dadosDaAgenda' >${recebeResultado[i].nome}</td>
					<td class = 'dadosDaAgenda' >${recebeResultado[i].usu_nome}</td>
					<td class = 'dadosDaAgenda' >${recebeResultado[i].data}</td>
				</tr>
				`
			}

			$('#resultadoAgendaMigrate').empty().append(
				`
				<table id="tableResultado">
					<tr>
						<th class = "titulos"> Procedimentos </th>
						<th class = "titulos"> Paciente </th> 
						<th class = "titulos"> Data </th>
					</tr>
					${dadosDaTabela}
				</table>
				`				 	
			);
		}
	})
}

function validaDatas(){
    var dataInicial = new Date($("#dataInicial").val() );
    var dataFinal = new Date($("#dataFinal").val() );

    if (!dataInicial || !dataFinal) {
    	alert("Existe algum campo vazio !");
    	return 0;
    }

    if (dataInicial >= dataFinal) {
        alert("Data Inicial maior que final!");
        return 0;
    } else{
    	return 1
    }

}