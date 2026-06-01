/* Javascript do arquivo anamnese.PHP */

// function hotkey(eventname)
// {
// 	// ESC esconde todas as janelas
// 	if( eventname.keyCode == 27 )
// 	{
// 		//esconde_janela( 'janela_proc' );
// 		//alert('esconde td mundo');
// 		return false;
// 		
// 	}
// }

// altera o form/tabela 
function tp_resposta( obj ){
	for( i=2; i <= 4 ; i++ ){
		if( i == obj.value )
			document.getElementById('tr_opt_'+i).style.display = 'table-row';
		else 
			document.getElementById('tr_opt_'+i).style.display = 'none';
	}
	
}
// --
function sel_todas_relacao( inv ){
	// relacoes
	var Sel = document.getElementById('ana_tp_codigo');
	for( i=0; i < Sel.options.length; i++ ){
		op = inv ? ! Sel.options[i].selected : true;
		Sel.options[i].selected = op;
	}
}
// --
var NOME;
function add_relacao(){
	var nome = prompt('Entre com o nome da Nova Rela��o', 'nome');
	
	if( ! nome ){
		if( ! confirm('Nenhum nome foi digitado, deseja inserir uma nova rela��o ?') )
			return false;
		add_relacao();
	}
	
	if( ! nome ) return;
	
	NOME = nome;
	
	ajax_tudo('anamnese_op.php?acao=add_rel&desc='+nome, add_relacao2);
}

function add_relacao2( txt ){
	var Sel = document.getElementById('ana_tp_codigo');
	var t = Sel.length;
	Sel.length = t+1;
	Sel.options[ t ].text = NOME;
	Sel.options[ t ].selected = true;
	Sel.options[ t ].value = txt;
	
	var Sel2 = document.getElementById('busca_tipo');
	var t2 = Sel2.length;
	Sel2.length = t2+1;
	Sel2.options[ t2 ].text = NOME;
	Sel2.options[ t2 ].selected = true;
	Sel2.options[ t2 ].value = txt;
	//alert(txt);
}

function valida_form_ana( id_login ){
	// questao
	if( ! valida('ana_questao','Quest�o') ) return false;

	// extras
	var Ext = document.getElementById('ana_tp_resposta');
	if( Ext.value > 1 ){
		//var Opt = document.getElementById('ana_tp_resposta_opt_' + Ext.value);
		if( ! valida( 'ana_tp_resposta_opt_' + Ext.value , 'Extras') ) return false;
	}
	
	
	// relacoes
	var Sel = document.getElementById('ana_tp_codigo');
	var teste = false;
	// pelo menos 1 opcao de relacao selecionada ?
	for( i=0; i < Sel.options.length; i++ ){
		if( Sel.options[i].selected ){
			teste = true;
			break;
		}
	}
	
	if( ! teste ){
		alert('Escolha ao menos 1 op��o da Rela��o');
		Sel.focus();
	}
	return teste;
}

function del_relacao(){
	var Sel 	= document.getElementById('ana_tp_codigo');
	var Op 		= new Array();
	
	// pelo menos 1 opcao de relacao selecionada ?
	for( i=0; i < Sel.options.length; i++ ){
		if( Sel.options[i].selected ){
			Op.push( Sel.options[i].value );
		}
	}
	
	if( Op.length < 1 ){
		alert('Escolha ao menos uma Rela��o para remover !');
		return false;
	}
	
	if( ! confirm('Deseja remover esta(s) Rela��o(�es) ?') )
		return false;
	
	var endereco = 'anamnese_op.php?acao=del_rel&id='+Op.join(',');
	ajax_tudo( endereco, edit_relacao2);

	// arrumando o select de busca (deixa apenas o gen�rico)
	// ele tem uma opcao antes (todas)
	var Sel2 = document.getElementById('busca_tipo');
	Sel2.selectedIndex = 0;
	Sel2.disabled = true;
	
	// removendo do select
	remove_sel( Sel );
}

function remove_sel( Sel ){
	for( i=0; i < Sel.options.length; i++ )	{
		if( Sel.options[i].selected ){
			Sel.remove( i );
			remove_sel( Sel );
		}
	}

}

function edit_relacao(){
	var Sel 	= document.getElementById('ana_tp_codigo');
	var Op 		= new Array();
	var Op2		= new Array();

	// pelo menos 1 opcao de relacao selecionada ?
	for( i=0; i < Sel.options.length; i++ )	{
		if( Sel.options[i].selected ){
			Op.push( Sel.options[i].value );
			Op2.push( Sel.options[i].text );
		}
	}

	if( Op.length != 1 ){
		alert('Escolha uma Rela��o (e somente uma) antes de editar !');
		return false;
	}
	
	var nome = prompt('Entre com o novo nome da Rela��o', Op2[0] );
	
	if( ! nome )	{
		alert("Nenhum nome digitado !\nSaindo...");
		return false;
	}
	
	Sel.options[ Sel.selectedIndex ].text = nome;
	ajax_tudo('anamnese_op.php?acao=edit_rel&desc='+nome+'&id='+Op[0], edit_relacao2);
	
	// arrumando o select de busca
	// ele tem uma opcao antes (todas)
	var Sel2 	= document.getElementById('busca_tipo');
	Sel2.options[ Sel.selectedIndex + 1 ].text = nome;
}

function edit_relacao2( txt ){
	if( txt.length > 0 )
		alert(txt);
}

function editar( id_login, id ){
	document.location.href = 'anamnese.php?id_login='+id_login+'&acao=form_edit&id='+id;
}

// adiciona valores padrao
function add_sim_nao(){
	document.getElementById('ana_tp_resposta_opt_3').value = "Sim\nN�o";
}

function add_normal_anormal(){
	document.getElementById('ana_tp_resposta_opt_3').value = "Normal\nAnormal";
}