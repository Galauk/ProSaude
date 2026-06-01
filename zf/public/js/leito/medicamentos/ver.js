$(function(){
	$("img.a").click(function(){
		var id = $(this).data("id");		
		var msg = "Deseja realmente cancelar esta grade?";
		var h = 120;
		var ha = haReservas(id);
		if(ha){
			msg += "<br /><br />Há medicamentos que já foram reservados para esta grade. Eles serão devolvidos ao estoque."
			h = 170;
		}
		
		confirme("Confirme", msg, 330, h, function(){
			cancelarGrade(id);
		});
	});
	
});

function haReservas(lgra){
	mensagemSemOk("msg-reserva","Aguarde","Verificando reservas de medicamentos", 330, 120);
	var ret = false;
	
	$.ajax({
		async: false, // <= importante!
		url: baseUrl+'/leito/medicamentos/ha-reservas/lgra/'+lgra,
		dataType: 'json',
		success: function(r){
			ret = r.reservas;
		}
	});
	fecharMensagemSemOk("msg-reserva");
	return ret;
}

function cancelarGrade(id){
	$.ajax({
		url: baseUrl+'/leito/medicamentos/cancelar-grade',
		type:'post',
		data:{
			lgra_codigo: id
		},
		success: function(r){
			if(r.success)
				window.location.href = window.location.href;
		}
	});
}