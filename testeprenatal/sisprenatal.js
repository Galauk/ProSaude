function marcaVacina(){
	dose = "";
	var aplicando = document.getElementsByTagName('input');
	for(x = 0; x < aplicando.length; x++){
		if (aplicando[x].getAttribute("type") == "radio" && aplicando[x].checked == true) 
		{
			dose = aplicando[x].value;
        }
	}
	var id = "vacina"+9+dose;
	var pro_codigo = document.getElementById("pro_codigo").value;
	var usu_codigo_prontuario = document.getElementById("usu_codigo").value;
	var id_login = document.getElementById("id_login").value;
	//alert(usu_codigo_prontuario);
	url = "../marcaData.php?id="+id+"&resposta=P&para=para&pro_codigo="+pro_codigo+"&usu_codigo_prontuario="+usu_codigo_prontuario+"&id_login="+id_login;
	window.open(url, null,"height=150,width=450,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");	
}

function mostraParto(){
	var valida = document.getElementById("sispn_tipo_consulta").value;
	if(valida == 3){
		document.getElementById('oculta').style.display="block";
	}
	if(valida == 9){
		document.getElementById('interrupcao').style.display="block";
	}
	if(valida == 1 || valida == 5){
		document.getElementById('interrupcao').style.display="none";
		document.getElementById('oculta').style.display="none";
	}
	
}

function mostraInterrupcao(){
	resposta = "";
	var aplicando = document.getElementsByTagName('input');
	for(x = 0; x < aplicando.length; x++){
		if (aplicando[x].getAttribute("type") == "radio" && aplicando[x].checked == true) 
		{
			resposta = aplicando[x].value;
			break;
        }
	}
	if(resposta == "S"){
		document.getElementById('interrupcao').style.display="block";
	}
}
/*FUNCAO QUE MARCA E DESMARCA TODOS OS CHECKBOX DO EXAME*/
function selecionar_tudo(){
	var j = -1;
	for (i = 0; i < document.prenatal.elements.length; i++){
		if(document.prenatal.elements[i].type == "checkbox"){
			if (j == -1){
				j = i; //seleciona o primeiro checkbox para saber qual operação realizar
				marcar = !document.prenatal.elements[j].checked; //marcar recebe o contrário do primeiro checkbox
			}
			document.prenatal.elements[i].checked = marcar;// marca todos os checkbox com o contrário do primeiro
		}
	}
} 

/*function deselecionar_tudo(){
   for (i=0;i<document.prenatal.elements.length;i++){
   }
   var x = document.getElementById("trocaBotao");
  	x.innerHTML = "<span onClick=\"selecionar_tudo()\" style='cursor:pointer'><img src='../imgsBotoes/selecionar.png'></span>";
}*/

function validadorPrenatal(){
	var sispn_tipo_consulta = document.getElementById("sispn_tipo_consulta").value;
	var ate_peso = document.getElementById("ate_peso").value;
	var sispn_classificacao_risco = document.getElementById.value;
	if(ate_peso == ""){
		alert('Peso nao informado na pre-consulta');
		exit();
		return false;
	}
	if(sispn_tipo_consulta == ""){
		alert('Informe o tipo de consulta');
		exit();
		return false;
	}
	if(sispn_classificacao_risco == ""){
		alert(' necessario informar um tipo de risco!' );
		exit();
		return false();
	}
	
	document.prenatal.submit();	
}
function deletaSolicitacaoExames() {
	
	var objCheckBox = document.getElementsByTagName('input');
	var selecionadas = "";
	var sel = "";
	var usu_codigo = document.getElementById("usu_codigo").value;
	var med_codigo = document.getElementById("med_codigo").value;
	var age_codigo = document.getElementById("age_codigo").value;
	var uni_codigo = document.getElementById("uni_codigo").value;
	var sispn_codigo = document.getElementById("sispn_codigo").value;
	var ate_codigo = document.getElementById("ate_codigo").value;
	        
	    for (i=0; i < objCheckBox.length; i++) {
	       if (objCheckBox[i].type == "checkbox" && objCheckBox[i].checked) {
	       //alert(objCheckBox[i].value);
	       sel++
//	       selecionadas += objCheckBox[i].value+"|";
	       selecionadas += objCheckBox[i].value+",";
	       }
	    }
	    selec = selecionadas.substr(0, selecionadas.length-1);
	        url = "../preNatal/sqlPrenatal.php?acao=deletaExames&selec="+selec+"&usu_codigo="+usu_codigo+"&med_codigo="+med_codigo+"&age_codigo="+age_codigo+"&uni_codigo="+uni_codigo+"&sispn_codigo="+sispn_codigo+"&ate_codigo="+ate_codigo;
	       // alert('xxx');
	        ajax_tudo(url,deletaExamesTxt);
	        
}
function deletaExamesTxt(txt){
	//alert(txt);
	location.href=txt;
}

function chamarHistorico(usu_codigo, sispn_codigo,age_codigo,ate_codigo,med_codigo,uni_codigo,age_data){
	location.href="prontuario.php?pagina=17&acao=hist&acaoModal=atendimento&sispn_codigo="+sispn_codigo+"&usu_codigo="+usu_codigo+"&age_codigo="+age_codigo+"&ate_codigo="+ate_codigo+"&med_codigo="+med_codigo+"&uni_codigo="+uni_codigo+"&age_data="+age_data;	
	//url = "prontuario.php?pagina=17&acao=hist&sispn_codigo="+sispn_codigo+"&usu_codigo="+usu_codigo+"&age_codigo="+age_codigo+"&ate_codigo="+ate_codigo+"&med_codigo="+med_codigo+"&uni_codigo="+uni_codigo+"&age_data="+age_data;
	//alert(url);
}
function puerperal(){
	var amamentacao = document.getElementById('ava_amamentacao').value;
	if(amamentacao == "N"){
		document.getElementById('amamentacao').style.display="block";
	}
	
}
