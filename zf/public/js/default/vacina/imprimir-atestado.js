$(function(){
	
	var dataAtestado = $("#data-atestado").val();
	var dataSolicitado = $("#data-solicitada").val();
	
	var dif = compararDatas(dataAtestado, dataSolicitado);
	
	if(dif == 2)  // solicitado > atestado
		mensagem("Data inválida!","Não é possível gerar o atestado até a data solicitada pois existem aprazamentos.<br />O atestado será gerado com a data do próximo aprazamento.")
	
});