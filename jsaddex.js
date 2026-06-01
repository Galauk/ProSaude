var iCount = 0;

//Função que adiciona os campos;
function addInput(id, pro_codigo, fracionado) {   
	/*qtde = typeof(qtde) != 'undefined' ? qtde : '';
	lote = typeof(lote) != 'undefined' ? lote : '';
	validade = typeof(validade) != 'undefined' ? validade : '';*/
	//Criando uma variável que armazenará as informações da linha que será criada.
	//Os campos estão sendo colocados no interior de uma div, pois a linha contém muitos elementos;
	//Basta excluir a div, para excluir todos os elementos da linha;
	/*var texto = "<div id=adicionado"+iCount+"_"+pro_codigo+"><table cellpadding='0' cellspacing='0'><tr><td style='border:none' width='200'><input type='hidden' name='pro_codigo[]' value='"+pro_codigo+"'><input type='text' class=inputForm name='invp_quantidade["+id+"][]' id='qtde"+iCount+"'></td><td style='border:none' width='210'><input type='text' name='invp_lote["+id+"][]' class=inputForm></td><td style='border:none' width='200'><input type='text' name='invp_validade["+id+"][]' class=inputForm maxlength='10' onkeypress='return Ajusta_Data(this, event);'></td><td style=\"border:none\"><img src=\""+root+comum+"imgs/remove.png\" border=0 onClick='removeInput("+iCount+", "+pro_codigo+");' style='cursor: pointer;'></td></tr></table></div>";

	//Capturando a div principal, na qual os novos divs serão inseridos:
	var camposTexto = document.getElementById('camposTexto'+id);   
	camposTexto.innerHTML += texto;*/

/***********************DAQUI************************/
	fracionado = typeof(fracionado) != 'undefined' ? fracionado : false
	
	var novaDiv = document.createElement("div");
	novaDiv.id = "adicionado"+iCount+"_"+pro_codigo;
	
	var tabela = document.createElement("table");
	tabela.cellPadding = 0;
	tabela.cellSpacing = 0;

	var tr1 = document.createElement("tr");
	var td1 = document.createElement("td");
	td1.style.border = "none";
	td1.width = 204;
	tr1.appendChild(td1);

	var input1 = document.createElement("input");
	input1.type = "hidden";
	input1.name = "pro_codigo[]";
	input1.value = pro_codigo;

	var input2 = document.createElement("input");
	input2.type = "text";
	input2.className = "inputForm";
	input2.name = "invp_quantidade["+id+"][]";
	input2.id = "qtde"+iCount;
	input2.addEventListener("keypress", function(event){	
														if (!SomenteNumero(event)) {			
															//alert('este campo so aceita numeros');
															return false;
														}
													}, false);
	input2.addEventListener("blur", function(event){this.value = this.value.replace( /\D/g,'');},false);

	var input2_2 = document.createElement("input");
	input2_2.className = "inputForm";
	input2_2.name = "invp_dose_lote["+id+"][]";
	input2_2.id = "qtde"+iCount;
	if(!fracionado){
		input2_2.value=1;
		input2_2.type = "hidden";
	}else{
		input2_2.type = "text";
	}
	input2_2.addEventListener("keypress", function(event){	
														if (!SomenteNumero(event)) {			
															//alert('este campo so aceita numeros');
															return false;
														}
													}, false);
	input2_2.addEventListener("blur", function(event){this.value = this.value.replace( /\D/g,'');},false);
	td1.appendChild(input1);
	td1.appendChild(input2);
	
	var td1_1 = document.createElement("td");
	td1_1.style.border = "none";
	td1_1.width = 215;
	tr1.appendChild(td1_1);
	td1_1.appendChild(input2_2);
	
	var td2 = document.createElement("td");
	td2.style.border = "none";
	td2.style.paddingLeft = '5px';
	td2.width = 220;
	tr1.appendChild(td2);
	
	var input2 = document.createElement("input");
	input2.type = "text";
	input2.className = "inputForm";
	input2.name = "invp_lote["+id+"][]";

	td2.appendChild(input2);
	
	var td3 = document.createElement("td");
	td3.style.border = "none";
	td3.width = 210;
	tr1.appendChild(td3);

	var input3 = document.createElement("input");
	input3.type = "text";
	input3.className = "inputForm";
	input3.name = "invp_validade["+id+"][]";
	input3.id = "invp_validade"+iCount;
	input3.maxLength = 10;
	input3.addEventListener("keypress", function(event){return Ajusta_Data(this, event);}, false);
	input3.addEventListener("blur", function(){
										if (!Verifica_Data_Validade(this.value, input3.id)){
											this.value = '';
											this.focus();
										}
/*										if (this.value < Date(year, month, day)){ 
											alert('Data de validade não pode ser menor que a data atual!');
											this.value = '';
											input3.focus();
										}
*/									}, false);

	td3.appendChild(input3);

	var td4 = document.createElement("td");
	td4.style.border = "none";
	
	var input4 = document.createElement("input");
	input4.type = "hidden";
	input4.name = "contador[]";
	input4.value = iCount;
	
	var img = document.createElement("img");
	img.src = linkroot+comum+"imgs/remove.png";
	img.alt = "remove";
	img.border = 0;
	img.addEventListener("click", function(){removeInput(input4.value, pro_codigo)}, false);
	img.style.cursor = "pointer";
	
	td4.appendChild(img);
	
	tr1.appendChild(td4);
	tabela.appendChild(tr1);
	novaDiv.appendChild(tabela);
	
	/*var newdiv=document.createElement("div");
	var newtext=document.createTextNode("Label div :");
	var aTextBox=document.createElement('input');
	aTextBox.type = 'text';
	aTextBox.value = 'Input Element';
	aTextBox.id = 'txt_cell_two_';
	newdiv.appendChild(newtext); //append text to new div
	newdiv.appendChild(aTextBox); //append text to new div*/
//	document.getElementById('camposTexto'+id).appendChild(novaDiv); //append new div to another
/*********************ATÉ AQUI***********************/
	var camposTexto = document.getElementById('camposTexto'+id);   
	camposTexto.appendChild(novaDiv);
	document.getElementById('qtde'+iCount).focus();
	
	iCount++;
}

//Função que adiciona os campos;
function addInputQtde(id, pro_codigo) {   
	/*qtde = typeof(qtde) != 'undefined' ? qtde : '';
	lote = typeof(lote) != 'undefined' ? lote : '';
	validade = typeof(validade) != 'undefined' ? validade : '';*/
	//Criando uma variável que armazenará as informações da linha que será criada.
	//Os campos estão sendo colocados no interior de uma div, pois a linha contém muitos elementos;
	//Basta excluir a div, para excluir todos os elementos da linha;
	var texto = "<div id=adicionado"+iCount+"_"+pro_codigo+"><table cellpadding='0' cellspacing='0'><tr><td style='border:none' width='200'><input type='hidden' name='pro_codigo[]' value='"+pro_codigo+"'><input type='text' class=inputForm name='invp_quantidade["+id+"][]' id='qtde"+iCount+"' onkeypress=\"return Bloqueia_Caracteres(event);\" onblur=\"this.value = this.value.replace( /\D/g,'');\"></td><td style='border:none' width='210'>&nbsp;</td><td style='border:none' width='200'>&nbsp;</td><td style=\"border:none\"></td></tr></table></div>";

	//Capturando a div principal, na qual os novos divs serão inseridos:
	var camposTexto = document.getElementById('camposTexto'+id);   
	camposTexto.innerHTML = camposTexto.innerHTML+texto;
	document.getElementById('qtde'+iCount).focus();
	var idImg = "idImg"+id;
	document.getElementById(idImg).innerHTML = "<img src=\""+linkroot+comum+"imgs/add_off.png\" border=0>";
	iCount++;
}
   
//Função que remove os campos;
function removeInput(id, codigo) {
	var pai = document.getElementById('adicionado'+id+"_"+codigo);
	var removido = pai.parentNode.removeChild(pai);
	iCount--;
}
/*function removeInput(id) {
	var pai = document.getElementById(id);
	var removido = pai.parentNode.removeChild(pai);
	iCount--;
}*/