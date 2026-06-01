$(function(){
	
	$(".novo").click(function(e){
		e.preventDefault();
		$("#buscar").val("0");
		$(this).parents("form").trigger("submit");			
	});
	
});