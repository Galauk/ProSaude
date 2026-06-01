function gerarRelatorioRecepcaoDeExames() {

	var recebeCodigoLocal = $("#codigoDoLocal").val()
	var recebeDataInicial = $("#dataInicial").val()
	var recebeDataFinal = $("#dataFinal").val()

	window.open(baseUrl+'/relatorio/recepcao-de-exames/gerar-relatorio-recepcao-de-exames?recebeCodigoLocal='+recebeCodigoLocal+'&recebeDataInicial='+recebeDataInicial+'&recebeDataFinal='+recebeDataFinal,'_blank')

	// $.ajax({
	// 	url: baseUrl+'/relatorio/recepcao-de-exames/gerar-relatorio-recepcao-de-exames',
	// 	type: 'GET',
	// 	data: {
	// 		recebeCodigoLocal: recebeCodigoLocal,
	// 		recebeDataInicial: recebeDataInicial,
	// 		recebeDataFinal: recebeDataFinal
	// 	},
	// })
	
}