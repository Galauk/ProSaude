$(function(){
	
	$("#grade tr td[data-dia]").hover(function(){
		var data = $(this).data("dia");
		$("td[data-dia="+data+"]").addClass("destaque");
		
	}, function(){
		var data = $(this).data("dia");
		$("td[data-dia="+data+"]").removeClass("destaque");
		
	})
	.click(marcarDia)
	.disableSelection();
	
	$(".com-vaga").each(function(){
		var obj = $(this);
		var html = "<div><strong>Data: </strong>"+dataToBr(obj.data("dia"))+"<br /><strong>Vagas: </strong>"+obj.data("vagas")+"</div>";
		
		obj.easyTooltip({
			content: html
		});
	});
	
});

function marcarDia(){
	if($(this).hasClass("sem-vaga"))
		return;
	
	if($(this).hasClass("com-vaga")){
		var coni = $(this).data("coni");
		var data = $(this).data("dia");
		
		$("[data-coni="+coni+"]").html("&nbsp;");
		$("#coni_"+coni).val(data);
		
		$(this).html("<img src=\""+baseUrl+"/public/images/icons/accept.png\" width=\"12px\" />");
		
	}
		
}