/* DEFAULT LANGUAGE 
================================================== */
if(typeof VMM != 'undefined' && typeof VMM.Language == 'undefined') {
	VMM.Language = {
		lang: "pt-br",
		api: {
			wikipedia: "pt-br"
		},
		date: {
			month: ["Janeiro", "Favereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"],
			month_abbr: ["Jan.", "Fev.", "Março", "Abril", "Maio", "Junho", "Julho", "Agos.", "Set.", "Out.", "Nov.", "Des."],
			day: ["Domingo","Segunda", "Terça", "Quarta", "Quinta", "Sexta", "Sábado"],
			day_abbr: ["Dom.","Seg.", "Ter.", "Qua.", "Qui.", "Sex.", "Sab."]
		}, 
		dateformats: {
			year: "yyyy",
			month_short: "mmm",
			month: "mmmm yyyy",
			full_short: "mmm d",
			full: "mmmm d',' yyyy",
			time_no_seconds_short: "h:MM TT",
			time_no_seconds_small_date: "h:MM TT'<br/><small>'mmmm d',' yyyy'</small>'",
			full_long: "mmm d',' yyyy 'at' hh:MM TT",
			full_long_small_date: "hh:MM TT'<br/><small>mmm d',' yyyy'</small>'"
		},
		messages: {
			loading_timeline: "Carregando Linha do tempo... ",
			return_to_title: "Retornar ao título",
			expand_timeline: "Exapandir Linha do tempo",
			contract_timeline: "Contract Linha do tempo",
			wikipedia: "From Wikipedia, the free encyclopedia",
			loading_content: "Carregando Conteúdo",
			loading: "Carregando"
		}
	}
};