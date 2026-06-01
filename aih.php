<?php
/**
 * @Modulo: Autorização de Internação Hospitalar ( AIH )
 * @Arquivos Relacionados: aih.js, aih_apac.inc.php, aih_apac_cad_num.php, aih_op.php, aih_popup.php, aih_print.php, aih_print_sesgunda_via.php
 * @Tabelas: aih
 * @Acao: Adiciona as AIH's.
*/ 
session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
//verauth($id_login);
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";
#include_once $_SESSION[root].$_SESSION[modulo]."aih_apac.inc.php";

// ver arquivo aih.js com hotkey
cabecario( $hotkey = true );
//<------------------------------------------------->
?>
<!--<div id="TEMP">TEMP</div>-->

<script type="text/javascript" src="ajax_motor.js"></script>
<script type="text/javascript" src="funcoes.js"></script>
<script type="text/javascript" src="aih.js"></script>

<!-- requisitos da busca genérica -->
<link rel="stylesheet" href="/WebSocialSaude/lib/themes/base/jquery.ui.all.css">
<link rel="stylesheet" href="/WebSocialSaude/lib/themes/ui-lightness/jquery-ui-1.8.10.custom.css">
<script type="text/javascript" src="/WebSocialComum/library/js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="/WebSocialSaude/lib/ui/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="/WebSocialComum/library/js/jquery.buscar.js"></script>


<script type="text/javascript">
  function valida_form_aih() {

	  var validar = {};
	  validar.med_codigo_solicitante = "Por favor Preencha o Nome do Estabelecimento Solicitante";
	  validar.aih_cnes_soli = "Por favor Preencha o CNES";
	  validar.aih_paciente_nome = "Por favor Preencha o Nome do Paciente";
	  validar.aih_prontuario_hospital = "Por favor Preencha o Numero do Prontuário";
	  validar.aih_data_nasc = "Por favor Preencha a Data de Nascimento";
	  validar.aih_sexo = "Por favor Preencha o Sexo";
	  validar.aih_dataini = "Por favor Preencha a Data de Internação";
	  validar.aih_data_alta = "Por favor Preencha a Data da Alta";
	  validar.aih_cid_cod_princ = "Por favor Preencha o Cid 10 Principal";
	  validar.med_solicitante_proc = "Por favor Preencha o Nome do Profissional Solicitante";
	  validar.med_autorizador = "Por favor Preencha o Nome do Profissional Autorizador";
	  validar.aih_n_doc_prof_autorizador = "Por favor Preencha o Número do Documento do Profissional Autorizador";

	  for(var i in validar){
		if(!$("input[name="+i+"]").size()){
			alert(i+" não encontrado!");
			return false;
		}
		if(document.aih_form[i].value == ''){
			alert(validar[i]);
			document.aih_form[i].focus();
			return false;
		}
	  }
   
	return true;	
}

function verifica_campos_alteracao()
{
   if(document.aih_form_altera.aih_dataini.value == '') {
	alert("Por favor Preencha a Data de Internação");
	document.aih_form_altera.aih_dataini.focus();
	return false;
   }
   if(document.aih_form_altera.aih_data_alta.value == '') {
	alert("Por favor Preencha a Data da Alta");
	document.aih_form_altera.aih_data_alta.focus();
	return false;
   }
   if(document.aih_form_altera.aih_desc_proc_soli.value == '') {
	alert("Por favor Preencha a Descrição do Procedimento");
	document.aih_form_altera.aih_desc_proc_soli.focus();
	return false;
   }
   if(document.aih_form_altera.aih_ci.value == '') {
	alert("Por favor Preencha o Caráter de Internação ( C.I. )");
	document.aih_form_altera.aih_ci.focus();
	return false;
   }
   if(document.aih_form_altera.aih_cid_cod_princ.value == '') {
	alert("Por favor Preencha o Cid 10 Principal");
	document.aih_form_altera.aih_cid_cod_princ.focus();
	return false;
   }
   
   return true;
   
}

function buscaferiado( data ) {
    //var url = 'age_ajax/busca_feriado.php?data='+data;
    var url = 'busca_feriado.php?data='+data;
    if(data!='')
    {
		//ajax(url);
        ajax_tudo( url, buscaferiado_cbk );
	 }
}

function buscaferiado_cbk ( txt )
{
    //if( txt.length > 0 )
    //    alert( txt );
}
 

function impressao( numero_aih )
{
    var url = 'aih_print.php?id_login=<?=$id_login;?>&aih_paciente_nome=<?=str_replace('#','',$aih_paciente_nome);?>&aih_numero_aih='+numero_aih+'&aih_classificacao_sus=<?=$aih_classificacao_sus;?>&aih_ibge_codigo=<?=$aih_ibge_codigo;?>&aih_n_doc_prof_autorizador=<?=$aih_n_doc_prof_autorizador;?>&aih_prontuario_hospital=<?=$aih_prontuario_hospital;?>&med_codigo_solicitante_h=<?=$med_codigo_solicitante_h;?>';
    //alert( url );
	document.getElementById('frame_impressao').src = url ;
}

function impressao_segunda_via()
{
	var url = 'aih_print_sesgunda_via.php?id_login=<?=$id_login;?>&aih_codigo=<?=$aih_codigo;?>';
    //alert( url );
    document.getElementById('frame_impressao').src = url;
}

var PAGINA = 'aih_popup.php?id_login=<?=$id_login;?>&acao=';

function busca_lab( acaobusca )
{
	var pc = document.getElementById('palavra_chave').value;
	var endereco = PAGINA+'busca&palavra_chave='+pc+'&acaoform='+acaobusca;
	if(acaobusca == 'busca_cid10'){
		var op = document.getElementById('busca_cid').value;
		endereco += '&valor_busca='+op;
	}
	if(acaobusca == 'buscadescproc'){
		var op = document.getElementById('busca_procedimento').value;
		endereco += '&valor_busca='+op;
	}
	janela_carregando("janela_aih");
	ajax_tudo(endereco,busca_lab_cont);
	return false;
}
function busca_lab_cont( txt )
{
	document.getElementById('janela_aih_conteudo').innerHTML = txt;
}

function compara_datas()
{
	var data_ini = document.getElementById('aih_dataini').value;
	var data_alta = document.getElementById('aih_data_alta').value ;
	
	ajax_tudo('aih_calcula_data.php?data_ini='+data_ini+'&data_alta='+data_alta, compara_data_resposta);

}
function compara_data_resposta( txt )
{
	if (txt.length>0){
		alert(txt);
	}
}
function init(acao)
{
	ajax_tudo( PAGINA+acao, busca_lab_cont )
}
function add_conteudo_popup(id_texto, id_hidden, nome, cod)
{
	var h = document.getElementById(id_hidden);
	var t = document.getElementById(id_texto);
	h.value = cod;
	t.value = nome;
	esconde_janela('janela_aih');
	
	location.hash = '#'+id_texto;
}
function add_conteudo_popup_procedimento(nome, cod, cod_sus)
{
	document.getElementById('aih_desc_proc_soli').value=nome;
	document.getElementById('aih_desc_proc_soli_h').value=cod;
	document.getElementById('aih_classificacao_sus').value=cod_sus;

	esconde_janela('janela_aih');
	//location.hash = '#'+id_texto;

}
function add_conteudo_popup_medsoli(nome, cod_hidden, cnes)
{

	document.getElementById('med_codigo_solicitante').value=nome;
	document.getElementById('med_codigo_solicitante_h').value=cod_hidden;
	document.getElementById('aih_cnes_soli').value=cnes;
	
	esconde_janela('janela_aih');
	//location.hash = '#'+id_texto;

}
function add_conteudo_popup_paciente(nome, cod, rg, cpf, datanasc, sexo, nome_mae, telefone, endereco, numero, bairro, municipio, cep, pac_aih, prontuario)
{
	
	document.getElementById('aih_paciente_nome').value = nome;
	document.getElementById('usu_codigo').value = cod;
	document.getElementById('aih_paciente_rg').value = rg;
	document.getElementById('aih_paciente_cpf').value = cpf;
	document.getElementById('aih_data_nasc').value = datanasc;
	document.getElementById('aih_sexo').value = sexo;
	document.getElementById('aih_mae_responsavel_nome').value = nome_mae;
	document.getElementById('aih_fone').value = telefone;
	document.getElementById('aih_endereco').value = endereco;
	document.getElementById('aih_numero').value = numero;
	document.getElementById('aih_bairro').value = bairro;
	document.getElementById('aih_cidade').value = municipio;
	document.getElementById('aih_cep').value = cep;
	// novos campos
	document.getElementById('pac_aih').value = pac_aih;
	document.getElementById('aih_prontuario').value = prontuario;
	
	det_paci();
	esconde_janela('janela_aih');
	if( cod ) ajax_tudo( 'aih_op.php?acao=verifica&codigo='+cod+'&aih='+pac_aih, add_paci_verifica ); 
}
/** Dudu */
function add_paci_verifica( txt )
{
	
	var Str 	= new String(txt);
	var Dados 	= Str.split(';');

	if( Dados[0] == 'NOK' )
	{
		  var r = confirm("O paciente escolhido possui uma AIH cadastrada há menos de 15 dias. Deseja Associar as duas AIH's ?");
		  if (r)
			{
				// sim.. quero associar as duas aihs.
				// fazer o update da tabela aih no campo aih_ativo com o valor 'N'
				// permitir editar apenas os campos : Data de Internação, Data da Alta, Procedimento, C.I., Cid10
				//alert('efetuar operacao');
				//ajax_tudo( 'aih_op.php?acao=associar&codigo='+Dados[1], form_associa );
				document.location.href="aih.php?id_login=<?=$id_login;?>&acao=form_edit&aih_codigo="+Dados[1];
			}else{
				// retorna falso... continua no cadastro da aih
				//alert('Operacao cancelada');
			}
	}	
}

function form_associa( txt )
{
	document.getElementById('TEMP').innerHTML = txt;
	//alert( txt );
	//document.location.href="aih.php?id_login=<?=$id_login;?>&acao=form_edit&aih_codigo="+txt;
}

/** paciente --------------------------------------------------------------- **/

function busca_cpf_ajax( obj )
{
	var endereco = 'aih_op.php?acao=busca_paci_cpf&cpf=' + obj.value;
	ajax_tudo( endereco, busca_cpf_ajax2 );
}

function busca_cpf_ajax2( txt )
{
	var Str 	= new String(txt);
	var Dados 	= Str.split(';');
	var Txt		= document.getElementById('cpf_result');

	if( Dados[0] == 'NOK' )
	{ 
		Txt.innerHTML = '<em><strong>Nenhum paciente encontrado com este CPF</strong></em>';
		add_conteudo_popup_paciente( '', '', '', Dados[1], '', '', '', '', '', '', '', '', '', '', ''  );

	}
	else
	{
		Txt.innerHTML = '<em>Paciente encontrado, atualizando...</em>';
		add_conteudo_popup_paciente( Dados[0], Dados[1], Dados[2], Dados[3], Dados[4], Dados[5], Dados[6], Dados[7], Dados[8], Dados[9], Dados[10], Dados[11], Dados[12], Dados[13], Dados[14] );
	}
}
/** autorizador ------------------------------------------------------------ **/

function busca_doc_ajax( doc )
{
	var doc		= document.getElementById('aih_n_doc_prof_autorizador').value;
	var cns 	= document.getElementById('aih_tipo_doc_autorizacao_cns').checked;
	var op  	= ( cns ? 'cns' : 'cpf' );
	
	var endereco = 'aih_op.php?acao=busca_med_doc&doc='+doc+'&op='+op;
	ajax_tudo( endereco, busca_doc_ajax2 );
}

function busca_doc_ajax2( txt )
{
	var Txt		= document.getElementById('doc_result');
	//Txt.innerHTML = txt;
	
	var Str 	= new String(txt);
	var Dados 	= Str.split(';');
		
	if( Dados[0] == 'NOK' )
	{ 
		Txt.innerHTML = '<em><strong>Nenhum m&eacute;dico encontrado com este documento</strong></em>';
		add_prof_autorizador( '', '', Dados[1] );
	}
	else
	{
		Txt.innerHTML = '<em>M&eacute;dico encontrado, atualizando...</em>';
		add_prof_autorizador( Dados[0], Dados[1], Dados[2] );
	}
	
}

function add_prof_autorizador( codigo, nome, doc, mantem )
{
	document.getElementById('med_autorizador_h').value 				= codigo;
	document.getElementById('med_autorizador').value				= nome;
	document.getElementById('aih_n_doc_prof_autorizador').value 	= doc;
	
	if( mantem )
	{
		document.getElementById('aih_tipo_doc_autorizacao_cns').checked = false;
		document.getElementById('aih_tipo_doc_autorizacao_cpf').checked = true;
	}
	
	esconde_janela('janela_aih');
}

/** detalhes do paciente */
function det_paci()
{
	var St = document.getElementById('det_paci_tbody').style;
	var Sp = document.getElementById('det_paci_span');
	
	if( St.display == 'none' )
	{
		St.display = 'table-row-group';
		Sp.innerHTML = 'Menos';
	} else {
		St.display = 'none';
		Sp.innerHTML = 'Mais';
	}
}

$(function(){

	$("#med_nome").buscar({
		tipo: 'prestador', // tudo que não seja prestador_servico=M
		template : function(ul, item) {
		return $("<li></li>").data("item.autocomplete", item).append(
			"<a>" + item.label + "</a>").appendTo(ul);
		}
	});// aih_paciente_nome

	$("#usu_nome").buscar({
		tipo: 'usuario_aih'
	});

	$("#cid1").buscar({
		tipo: 'cd10',
		template : function(ul, item) {
			return $("<li></li>").data("item.autocomplete", item).append(
				"<a>" + item.label + "</a>").appendTo(ul);
		}
	});

	$("#cid2").buscar({
		tipo: 'cd10',
		suffix: '_2',
		template : function(ul, item) {
			return $("<li></li>").data("item.autocomplete", item).append(
				"<a>" + item.label + "</a>").appendTo(ul);
		}
	});

	$("#cid3").buscar({
		tipo: 'cd10',
		suffix: '_3',
		template : function(ul, item) {
			return $("<li></li>").data("item.autocomplete", item).append(
				"<a>" + item.label + "</a>").appendTo(ul);
		}
	});

	$("#proc_nome").buscar({
		tipo: 'procedimento',
		template : function(ul, item) {
			return $("<li></li>").data("item.autocomplete", item).append(
				"<a>" + item.label + "</a>").appendTo(ul);
		}
	}); 

	$("#usr_nome").buscar({
		tipo: 'usuarios',
		template : function(ul, item) {
			return $("<li></li>").data("item.autocomplete", item).append(
				"<a>" + item.label + "</a>").appendTo(ul);
		}
	}); 

	$("#usr_nome_2").buscar({
		tipo: 'usuarios',
		suffix: '_2',
		template : function(ul, item) {
			return $("<li></li>").data("item.autocomplete", item).append(
				"<a>" + item.label + "</a>").appendTo(ul);
		}
	}); 
	
});
</script>

<?php

//<----------------------------------------------->
//<-> Secao Vazia, mostrando registros e botoes <->
	 reglog($id_login,"Entrando em AIH");
//<----------------------------------------------->

if( empty($acao) )
{
 
//<------------------------------------------------------->
//<-> Abaixo sao os botoes de voltar / cadastro simples <->
//<------------------------------------------------------->

  echo "<fieldset>
			<legend>Opções de Cadastro</legend>
			".ChmodBtn($id_login,'adicionar','aih.php?acao=form_add')."
            ".ChmodBtn($id_login,'ci','ci.php?')."
			".ChmodBtn($id_login,'hospital','hospital.php?')."
			".ChmodBtn($id_login,'clinica','clinica.php?');
			/*<a href='aih_apac_cad_num.php?id_login=$id_login&tipo=AIH'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/cadastrar_n_aih_on.jpg' border='0'></a>

            <a href='aih_apac_visualizacao.php?id_login=$id_login'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/aih_apac_on.jpg' alt='AIH/APAC' border='0' /></a>*/
			if(chmodbtn($id_login, 'adicionar_if', 'aih_apac_cad_num.php'))
			{
				echo "<a href='aih_apac_cad_num.php?id_login=$id_login&tipo=AIH'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/cadastrar_n_apac_on.jpg' border='0'></a>";
			} else {
				echo "<a href='#'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/cadastrar_n_apac_off.jpg' border='0'></a>";
			}
			if(chmodbtn($id_login, 'listar_if', 'aih_apac_visualizacao.php'))
			{
				echo "<a href='aih_apac_visualizacao.php?id_login=$id_login'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/aih_apac_on.jpg' alt='AIH/APAC' border='0' /></a>";
			} else {
				echo "<a href='#'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/aih_apac_off.jpg' alt='AIH/APAC' border='0' /></a>";
			}
			if(chmodbtn($id_login, 'listar_if', 'aih_apac_lib_num.php'))
			{
				echo "<a href='aih_apac_lib_num.php?id_login=$id_login'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/aih_apac_on.jpg' alt='AIH/APAC' border='0' /></a>";
			} else {
				echo "<a href='#'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/aih_apac_off.jpg' alt='AIH/APAC' border='0' /></a>";
			}

			echo "<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
				<tr><td>";
				if(chmodbtn($id_login, "procurar_if", "aih.php"))
				{
					echo "<form method='post' action='$PHP_SELF?$_SERVER[QUERY_STRING]'>";
				}
						echo "<input type='hidden' name='acao' value='busca' />
						<td width=30>Buscar:</td>
						<td width=120><input type='text' name='palavra_chave' class='box' onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
						<td>".ChmodBtn($id_login,'procurar','aih.php')."</td>
					</form>
					</td>
				</tr>
			</table>
			
		</fieldset>";

	$sql  = "SELECT a.aih_codigo, a.usu_codigo, a.aih_numero_aih, a.aih_segunda_via, to_char(aih_data_autorizacao, 'dd/mm/YYYY') as aih_data_autorizacao,  
		
			( CASE WHEN a.usu_codigo IS NOT NULL THEN p0.usu_nome
			WHEN a.pac_aih_codigo IS NOT NULL THEN p1.pac_nome
			ELSE 'none' END ) as pac_nome, 

			COALESCE ( a.usu_codigo, a.pac_aih_codigo ) AS paciente
						
			FROM aih AS a
			
			LEFT JOIN usuario AS p0 ON p0.usu_codigo = a.usu_codigo
			LEFT JOIN aih_paciente AS p1 ON p1.pac_codigo = a.pac_aih_codigo
		
			WHERE aih_ativo='S' ORDER BY aih_codigo DESC LIMIT 20";
	
	
	if(chmodbtn($id_login, "listar_if", "aih.php"))
	{
		$row = pg_query($sql);
	}

	$num	=	pg_num_rows($row);
	
	if($num=="0") { $resp = "Nenhum Registro na Base de Dados"; }
	if($num=="1") { $resp = "<b>$num</b> Registro na Base de Dados"; }
	if($num>"1") { $resp = "Listando Ultimos <b>$num</b> Registros"; }


  echo "

  <table width='98%' align='center' cellspacing='0' cellpadding='0' border='0'>
		<tr>
			<td>
				<fieldset>
					<legend>".$resp."</legend>
					<table width='100%' align='center' cellspacing='2' cellpadding='4' border='0' class='lista'>
					<tr bgcolor='#ffffff'>
						<th>Nome Paciente</th>
						<th>N&uacute;m. AIH</th>
						<th>Data da Autoriza&ccedil;&atilde;o</th>
						<th>&nbsp;</th>
					</tr>";
					while( $res = pg_fetch_array($row))	{
					echo"
						  <form method='post' action='$PHP_SELF'>

							<input type='hidden' name='acao' value='segundavia' />
							<input type='hidden' name='id_login' value='$id_login' />
							<!-- wtf ?
                                <input type='hidden' name='aih_codigo' value='$aih_codigo' />
                             -->   
                           <input type='hidden' name='aih_codigo' value='$res[aih_codigo]' /> 
						<tr>
							<td>$res[pac_nome]</td>
							<td align='center'>$res[aih_numero_aih]</td>
							<td align='center'>$res[aih_data_autorizacao]</td>
							<td width='350' align='center'>
							<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/segunda_via_on.jpg' border='0' />";
							/*
							if ($res['aih_segunda_via'] == 'N'){
								echo "<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/segunda_via_on.jpg' border='0' />";
							}else{
								echo "<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/segunda_via_off.jpg' border='0' />";
							}
							*/							
							echo "
<!-- <a href='$PHP_SELF?id_login=$id_login&acao=form_edit&aih_codigo=$res[aih_codigo]'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg' border='0' /></a> -->
<!-- <a href='aih_edit.php?aih_codigo=$res[aih_codigo]'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg' border='0' /></a> -->
<a href='aih_del.php?aih_codigo=$res[aih_codigo]&tipo=AIH&numero=$res[aih_numero_aih]'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg' border='0' onClick=\"if (!confirm('Deseja realmente apagar essa AIH ?')) return false\" /></a>

	    ".ChmodBtn($id_login,'editar','aih_edit.php?aih_codigo='.$res[aih_codigo])."
				<a href='$PHP_SELF?id_login=$id_login&acao=form_agrupa&aih_codigo=$res[aih_codigo]&usu_codigo=$res[paciente]'>
				<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/agrupar_on.jpg' border='0' /></a>
							</td>
						</tr>
						</form>
						";
					
					}
		echo"	</fieldset>
			</td>
		</tr>
		</table>";
}


else if ($acao=='busca')
{

	$palavra_chave = strtoupper($palavra_chave);
//-> Subistituindo o + por porcentagem na busca
	$str = str_replace("+","%",$palavra_chave);
	$pos = strpos($palavra_chave,"+");

	if($pos=="0") {
	 $v1=1;
	} else {
	 $v1=2;
	}

    echo "<fieldset>
		<legend>Opções de Cadastro</legend>
		<a href=aih.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>		
		".ChmodBtn($id_login,'adicionar','aih.php?acao=form_add')."
	  </fieldset>";


	/*$sql	=	"SELECT aih_codigo, aih_paciente_nome, aih_numero_aih, aih_segunda_via, to_char(aih_data_autorizacao, 'dd/mm/YYYY') as aih_data_autorizacao ".
				"FROM aih ".
				"WHERE (aih_paciente_nome LIKE '%$palavra_chave%') ";*/
				
		$sql  = "SELECT a.aih_codigo, a.aih_numero_aih, a.aih_segunda_via, to_char(aih_data_autorizacao, 'dd/mm/YYYY') as aih_data_autorizacao,  
		
			( CASE WHEN a.usu_codigo IS NOT NULL THEN p0.usu_nome
			WHEN a.pac_aih_codigo IS NOT NULL THEN p1.pac_nome
			ELSE 'none' END ) as pac_nome
			
			FROM aih AS a
			
			LEFT JOIN usuario AS p0 ON p0.usu_codigo = a.usu_codigo
			LEFT JOIN aih_paciente AS p1 ON p1.pac_codigo = a.pac_aih_codigo
		
			WHERE aih_ativo='S' AND 
						(TO_ASCII(p0.usu_nome) iLIKE '%$palavra_chave%' OR 
						TO_ASCII(p1.pac_nome) iLIKE '%$palavra_chave%') ";
	if(chmodbtn($id_login, "listar_if", "aih.php"))
	{
		$query 	=	db_query($sql);
	}
	$num	=	pg_num_rows($query);

	if($num=="0") { $resp = "Nenhum Registro encontrado com \"$palavra_chave\""; }
	if($num=="1") { $resp = "Encontrado <b>$num</b> Registro com \"$palavra_chave\""; }
	if($num>"1") { $resp = "Encontrados <b>$num</b> Registros com \"$palavra_chave\""; }

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
		<tr>
			<td>
				<fieldset>
	   				<legend>".$resp."</legend>
					<table width='100%' align='center' cellspacing='2' cellpadding='4' border='0' class='lista'>
					<tr bgcolor='#FFFFFF'>
						<th>Nome Paciente</th>
						<th>N&uacute;m. AIH</th>
						<th>Data da Autoriza&ccedil;&atilde;o</th>
						<th>&nbsp;</th>
					</tr>";
					
			   while($row=pg_fetch_array($query)) {
					
					echo"
						  <form method='post' action='$PHP_SELF'>

							<input type=hidden name=acao value='segundavia' />
							<input type='hidden' name='id_login' value='$id_login' />
                            <!-- wtf ?
							    <input type=hidden name=aih_codigo value=$aih_codigo />
                            -->
                            <input type='hidden' name='aih_codigo' value='$row[aih_codigo]' />

						<tr>
							<td>$row[pac_nome]</td>
							<td>$row[aih_numero_aih]</td>
							<td>$row[aih_data_autorizacao]</td>
							<td width='280' align='center'>
							<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/segunda_via_on.jpg' />";
/*							if ($row['aih_segunda_via'] == 'N'){
								echo "<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/segunda_via_on.jpg' />";
							}else{
								echo "<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/segunda_via_off.jpg' />";
							}
*/
							echo "
                                	    ".ChmodBtn($id_login,'editar','aih_edit.php?aih_codigo='.$row[aih_codigo])."
                                            ".ChmodBtn($id_login,'apagar','aih_del.php?aih_codigo='.$row[aih_codigo].'&tipo=AIH&numero='.$row[aih_numero_aih])."
					<!--	<a href='aih_edit.php?aih_codigo=$res[aih_codigo]'>
							<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg' border='0' /></a>
							<a href='aih_del.php?aih_codigo=$res[aih_codigo]&tipo=AIH&numero=$res[aih_numero_aih]'>
							<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg' border='0' onClick=\"if (!confirm('Deseja realmente apagar essa AIH ?')) return false\" /></a>
						-->
							</td>
						</tr>
						</form>
					 ";
				 }
				 
			   echo "</table>
					
				</fieldset>
			</td>
		</tr>
		</table>";
}
//<--------------------------------------------------------------------------------------------------->
//<-> Formulario de Adicao de Laudo para Internação de Autorização de Internação Hospitalar ( AIH ) <->
//<--------------------------------------------------------------------------------------------------->
else if($acao=="form_add")
{
	 reglog($id_login,"Laudo para Internação de Autorização de Internação Hospitalar ( AIH )");

  echo "<fieldset>
  	<legend>Opções de Cadastro</legend>
  	    <a href=aih.php?id_login=$id_login><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif' border='0'></a>
		<a href='aih_apac_cad_num.php?id_login=$id_login&tipo=AIH'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/cadastrar_n_aih_on.jpg' border='0'></a>
  	</fieldset>";
//<------------------------------------------------------------------------------------------------------------->
//<-> Este if está dizendo que se $acao for igual a form_add vai entrar na condigo e exibir o form de adição <->
//<------------------------------------------------------------------------------------------------------------->

	if( ! empty($med_codigo_sol) )
	{
		$med_nome_sol = db_get("SELECT med_nome FROM medico ".
							   "WHERE med_codigo=".intval($med_codigo_sol));
	}
	else
		$med_nome_sol = '';
	
	echo monta_janela('janela_aih','');

//---------------------------------------------------------------------------------------------------------------------------------------------------------
	
    /**
    $tem_numero = true;
	
	$aih_num_arr = aih_apac_proximo_num( 'AIH' );
	if( $aih_num_arr[0] == 0 )
	{
		$tem_numero = false;
	}
	
	if( !($tem_numero) )
	{
		$sql = "SELECT aan_numero_resto FROM aih_apac_numeros_resto";
		$aih_num_arr = db_getrow($sql);

			//if( $aih_num_arr[0] == 0 )
			if( !empty ($aih_num_arr[0]) )
			{
					$tem_numero = true;
			}
	}
	
	if($tem_numero)
	{
				$func = " valida_form_aih(".intval($id_login).")";	
	}else{
		print '
		<script type="text/javascript">
			alert("A AIH nao pode ser cadastrada!\nNao existe mais numeros disponiveis");
		</script>';
		$func = 'cancela_form()'; 
	}
    **/

	$func = " valida_form_aih(".intval($id_login).")";	
	#$tem_numero = true;
	#$usa_resto	= false;


	/*
	$aih_num_arr = aih_apac_proximo_num( 'AIH' );
    
	if( $aih_num_arr[0] == 0 )
	{
		$tem_numero = false;
	}

	if( ! $tem_numero )
    {
	*/ 
	//db_query("begin");

		$sql = "SELECT MIN(aan_numero_resto) FROM aih_apac_numeros_resto 
		        WHERE aan_tipo = 'AIH' 
			AND   aan_emuso is null"; 
		$aih_num_arr = db_getrow($sql);

        if( ! empty ($aih_num_arr[0]) )
        {               
			$tem_numero = true;
			//$usa_resto = true;
		}
		else
		{
	    	print '
        	<script type="text/javascript">
        		alert("A AIH nao pode ser cadastrada!\nNao existe mais numeros disponiveis");
	        </script> 
			<p class="aviso">AIH nao inserida !</p>
			</body></html>';
			die();
		}
	        $stmt_1 = "UPDATE aih_apac_numeros_resto set aan_emuso = 'S'
		           WHERE aan_numero_resto = '$aih_num_arr[0]' ";
                db_query( $stmt_1 );
		//MARCO - ALTERADO PARA DUAS PESSOAS PODEREM DIGITAR AO MESMO TEMPO
    

//-------------------------------------------------
/*
if( ! $usa_resto )
{

	$sql_iresto = "INSERT INTO aih_apac_numeros_resto (aan_numero_resto, aan_tipo)
		VALUES ('$aih_num_arr[0]', 'AIH')";

	$query_iresto = db_query($sql_iresto);

	$stmt1 = "UPDATE aih_apac_numero SET num_prox = num_prox + 1
		WHERE codigo  = $aih_num_arr[1] ";

	db_query( $stmt1 );

	db_query('COMMIT');
}
*/
//---------------------------------------------------------------------------------------------------------------------------------------------------------

echo 
   "<form name='aih_form' method='post' action='$PHP_SELF?acao=add&id_login=$id_login' onSubmit='return $func'>

	<h3>Laudo para Internação de Autorização de Internação Hospitalar ( AIH )</h3>

	<fieldset>
		<legend>Identificação do Estabelecimento de Saúde</legend>
		
		<table width='100%' align='center' cellspacing='2' cellpadding='0' border='0'>
		
		<tr>
			<td width='25%'>Nome do Estabelecimento Solicitante/Executante:</td>
			<td whidth='45%'>
				<input type='text' name='med_codigo_solicitante' id='med_nome' class='box' size='60' value='$med_nome'>
				<input type='hidden' name='med_codigo_solicitante_h' id='med_codigo' value='$med_codigo'>
			</td>
			<td whidth='30%'>CNES:<input type='text' name='aih_cnes_soli' id='med_cnes' class='box' size='20' maxlength='7' onKeyPress='apenasNumero(this)'
			onKeyUp='apenasNumero(this)' /></td>
		</tr>
		<tr>
			<td whidth='25%'>Compet&ecirc;ncia</td>
			<td colspan='2'>
				<select id='aih_mes_compet' name='aih_mes_compet' class='box' onchange='document.getElementById('ano_comp').select();'> ";
					print meses_select( date('m') ); 
	   echo"    	</select>
				/
				<input type='text' name='aih_ano_compet' id='aih_ano_compet' class='box' size='4' maxlength='4' value='"; print date('Y'); echo "' />			
			</td>
		</tr>";
		
/*		<tr>
			<td width='25%' height='22'>Nome do Estabelecimento Executante:</td>
			<td width='45%'><input type='text' name='med_codigo_executante' id='med_codigo_executante' class='box' size='60'>
							<input type='hidden' name='med_codigo_executante_h' id='med_codigo_executante_h'>
			<a href='javascript:;'onclick=\"mostra_janela('janela_aih');init('executante');document.getElementById('janela_aih_titulo_txt').innerHTML='Nome do Estabelecimento Executante'\"><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg' alt='Localizar' align='absmiddle' border='0' /></a>							</td>


			<td width='30%'>CNES:<input type='text' name='aih_cnes_exec' class='box' size='20' maxlength='5' onKeyPress='apenasNumero(this)'
				onKeyUp='apenasNumero(this)' /></td>
		</tr>*/
echo "
		</table>
	</fieldset>

	<fieldset>
		<legend>Identificação do Paciente</legend>
			
		<table width='100%' align='center' cellspacing='2' cellpadding='0' border='0'>
			<tr>
				<td width='25%'>Nome do Paciente:</td>
				<td width='75%'>
					<input type='text' name='aih_paciente_nome' id='usu_nome' class='box' size='60' />
					<input type='hidden' name='usu_codigo' id='usu_codigo' value='$usu_codigo' />
					<input type='hidden' name='pac_aih' id='pac_aih' value='N' />							
				</td>
			</tr>
			<tr>
				<td width='25%'>RG:</td>
				<td width='75%'><input type='text' name='aih_paciente_rg' id='usu_rg' class='box' size='30' maxlength='11' onKeyPress='apenasNumero(this)' onKeyUp='apenasNumero(this)' /></td>
			</tr>
			<tr>
				<td>CPF: </td>";

			      //<td width='75%'><input type='text' name='aih_paciente_cpf' id='aih_paciente_cpf' class='box' size='30' maxlength='14' onKeyPress='apenasNumero(this)' onKeyUp='apenasNumero(this)' onchange='busca_cpf_ajax(this)' />
echo "				<td width='75%'><input type='text' name='aih_paciente_cpf' id='usu_cpf' class='box' size='30' maxlength='14' onKeyPress='apenasNumero(this)' onKeyUp='apenasNumero(this)' />
				
				<span id='cpf_result'>&nbsp;</span></td>
			</tr>
			<tr>
				<td>N&deg; do Prontuario: </td>
				<td width='75%'>
				<input type='text' name='aih_prontuario' id='usu_prontuario' class='box' size='30' maxlength='10' onchange='atualiza_prontuario(this.value)' disabled='disabled'/>
				</td>
			</tr>
			<tr>
				<td width='25%'>Data da Interna&ccedil;&atilde;o: </td>
				<td width='75%'><input type='text' name='aih_dataini' id='aih_dataini' class='box' size='30' maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\" onchange=\"buscaferiado(this.value);\" /></td>
			</tr>
			<tr>
				<td width='25%'>Data Alta: </td>
				<td width='75%'><input type='text' name='aih_data_alta' id='aih_data_alta' class='box' size='30' maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\" onchange=\"buscaferiado(this.value); compara_datas();\"  /></td>
			</tr>
			<tr>
				<td colspan='2'>
					<a href='javascript:;' id='det_paci_link' onclick='det_paci()'>
					<span id='det_paci_span'>Mais</span> Detalhes
					</a>
				</td>
			</tr>
			<tbody id='det_paci_tbody' style='display:none;'>
			<tr>
				<td width='25%'>Cart&atilde;o Nacional de Sa&uacute;de - CNS:</td>
				<td width='75%'><input type='text' id='usu_cartao_sus' name='aih_cns' class='box' size='30' maxlength='15' onKeyPress='apenasNumero(this)' onKeyUp='apenasNumero(this)' /></td>
			</tr>
			<tr>
				<td width='25%'>Data de Nascimento: </td>
				<td width='75%'><input type='text' name='aih_data_nasc' id='usu_datanasc' class='box' size='30' maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\" onchange=\"buscaferiado(this.value);\" /></td>
			</tr>
			<tr>
				<td width='25%' valign='top'>Sexo: </td>
				<td width='75%'><input type='text' name='aih_sexo' id='usu_sexo' class='box' size='30' maxlength='1' />
			</tr>
			<tr>
				<td width='25%'>Nome da M&atilde;e ou Respons&aacute;vel:</td>
				<td width='75%'><input type='text' name='aih_mae_responsavel_nome' id='usu_mae' class='box' size='60' /></td>
			</tr>
			<tr>
				<td width='25%'>Telefone de Contato:</td>
				<td width='75%'><input type='text' name='aih_fone' id='usu_fone_recado' class='box' size='30' maxlength='13' onKeyPress='soNumeroTelefone(this,23)' onKeyUp='soNumeroTelefone(this,23)' /></td>
			</tr>
			<tr>
				<td width='25%'>Endere&ccedil;o:</td>
				<td width='75%'><input type='text' name='aih_endereco' id='rua_nome' class='box' size='60' /></td>
			</tr>
			<tr>
				<td width='25%'>N&uacute;mero: </td>
				<td width='75%'><input type='text' name='aih_numero' id='dom_numero' class='box' size='30' maxlength='10' /></td>
			</tr>
			<tr>
				<td width='25%'>Bairro: </td>
				<td width='75%'><input type='text' name='aih_bairro' id='rua_bairro' class='box' size='30' maxlength='60' /></td>
			</tr>
			<tr>
				<td width='25%'>Munic&iacute;pio de Resid&ecirc;ncia:</td>
				<td width='75%'><input type='text' name='aih_cidade' id='cid_nome' class='box' size='30' maxlength='60' /></td>
			</tr>
			<tr>
				<td width='25%'>C&oacute;d. IBGE Munic&iacute;pio: </td>
				<td width='75%'><input type='text' name='aih_ibge_codigo' id='cid_codigo_ibge' class='box' size='30' /></td>
			</tr>
			<tr>
				<td width='25%'>UF: </td>
				<td width='75%'><input type='text' name='aih_uf' id='uf_sigla' class='box' size='30' maxlength='2' /></td>
			</tr>
			<tr>
				<td width='25%'>CEP: </td>
				<td width='75%'><input type='text' name='aih_cep' id='rua_cep' class='box' size='30' maxlength='9' /></td>
			</tr>
			</tbody>
		</table>
	</fieldset>
	
	<fieldset>
		<legend>Justificativa da Interna&ccedil;&atilde;o</legend>
		
		<table width='100%' align='center' cellspacing='2' cellpadding='0' border='0'>

			<tr>
				<td>N&deg; do Prontuario Hospital: </td>
				<td width='75%'>
				<input type='text' name='aih_prontuario_hospital' id='aih_prontuario_hospital' class='box' size='30' maxlength='30'>
				</td>
			</tr>



<!--			<tr>
				<td width='25%' valign='top'>Principais Sinais e Sintomas Cl&iacute;nicos:</td>
				<td width='75%'><textarea name='aih_principais_sintomas' id='aih_principais_sintomas' cols='57' rows='3' class='box'></textarea></td>			
			</tr>
			<tr>
				<td width='25%' valign='top'>Condi&ccedil;&otilde;es que Justificam a Interna&ccedil;&atilde;o:</td>
				<td width='75%'><textarea name='aih_justificativa_internacao' cols='57' rows='3' class='box'></textarea></td>			
			</tr>	
			<tr>
				<td width='25%' valign='top'>Principais Resultados de Provas Diagn&oacute;sticas:</td>
				<td width='75%'><textarea name='aih_principais_resultados' cols='57' rows='3' class='box'></textarea></td>			
			</tr>					
			<tr>
				<td width='25%' valign='top'>Diagn&oacute;stico Inicial:</td>
				<td width='75%'><textarea name='aih_diag_ini' id='aih_diag_ini' cols='57' rows='3' class='box' ></textarea></td>
			</tr>-->
<input type=hidden name=aih_principais_sintomas value=''>
<input type=hidden name=aih_justificativa_internacao value=''>
<input type=hidden name=aih_principais_resultados value=''>
<input type=hidden name=aih_diag_ini value=''>

			<tr>
				<td width='25%'>Cid. 10 Principal:</td>
				<td width='75%'><input type='text' name='aih_cid_cod_princ' id='cid1' size='60' maxlength='150' class='box' >
								<input type='hidden' name='aih_cid_cod_princ_h' id='cd10_codigo'>
				</td>	
			</tr>
			<tr>
				<td width='25%'>Cid. 10 Secundário:</td>
				<td width='75%'><input type='text' name='aih_cid_cod_princ' id='cid2' size='60' maxlength='150' class='box' >
								<input type='hidden' name='aih_cid_cod_secun' id='cd10_codigo_2'>
				</td>	
			</tr>
			<tr>
				<td width='25%'>Cid. 10 Terciário:</td>
				<td width='75%'><input type='text' name='aih_cid_cod_princ' id='cid3' size='60' maxlength='150' class='box' >
								<input type='hidden' name='aih_cid_cod_terc' id='cd10_codigo_3'>
				</td>	
			</tr>
		</table>
	</fieldset>
	
	<fieldset>
		<legend>Procedimento Solicitado</legend>
		
		<table width='100%' align='center' cellspacing='2' cellpadding='0' border='0'>

			<tr>
				<td width='25%' valign='top'>Descri&ccedil;&atilde;o do Procedimento:</td>
				<td width='75%'><input type='text' name='aih_desc_proc_soli' id='proc_nome' class='box' size='60' maxlength='255' />
								<input type='hidden' name='aih_desc_proc_soli_h' id='proc_codigo' />
								<input type='hidden' name='aih_classificacao_sus' id='proc_bpa_tipo' />
				</td>
			</tr>";
			/* <tr>
				<td width='25%'>C&oacute;d. do Procedimento:</td>
				<td width='75%'><input type='text' name='aih_proc_codigo' size='30' maxlength='15' class='box' onKeyPress='apenasNumero(this)' onKeyUp='apenasNumero(this)' /></td>	
			</tr> */

					$sql_busca_clinica = 'SELECT * FROM clinica ORDER BY cli_descricao';
					$query_clinica 	   = pg_query($sql_busca_clinica);
				echo"<tr>
						<td width='25%'>Cl&iacute;nica:</td>
						<td width='75%'>";
					/*	<input type='text' name='aih_clinica' size='30' maxlength='50' class='box' > */
				echo"
						<select name='aih_clinica' id='aih_clinica' class='box'>
						";
							while( $res_clinica = pg_fetch_array($query_clinica) ){ 
						echo "	
								<option value="; echo $res_clinica['cli_codigo']; echo" > ";  echo $res_clinica['cli_descricao']; echo" </option>"; 
							} 
					echo "		
						</select>
						  </td>
					</tr>";
				
				
					$sql_buscaci = "select ci_codigo, ci_cod || ' ' || ci_descricao as ci_descricao FROM ci WHERE ci_ativo='S' order by ci_cod";
					$query_ci	 = pg_query($sql_buscaci);
				echo"<tr>
						<td width='25%'>C.I.:</td>
						<td width='75%'>";
					/*	<input type='text' name='aih_ci' size='30' maxlength='10' class='box' onKeyPress='apenasNumero(this)' onKeyUp='apenasNumero(this)' />&nbsp;*/
				echo"
						<select name='aih_ci' id='aih_ci' class='box'>
						";
							while( $res_ci = pg_fetch_array($query_ci) ){ 
						echo "	
								<option value="; echo $res_ci['ci_codigo']; echo" > ";  echo $res_ci['ci_descricao']; echo" </option>"; 
							} 
					echo "		
						</select>
						  </td>
					</tr>
			<tr>
			<tr>
				<td width='25%'>Nome do Profissional Solicitante:</td>
				<td width='75%'><input type='text' name='med_solicitante_proc' id='usr_nome' size='60' maxlength='255' class='box' >
								<input type='hidden' name='med_solicitante_proc_h' id='usr_codigo' size='60' maxlength='255' class='box'>
				</td>
			</tr>
				<td width='25%'>Núm. Conselho:</td>
				<td width='75%'>
						<input type='text' name='aih_n_doc_prof_solicitante' size='30' maxlength='20'
 					 	class='box' onchange='busca_doc_ajax_soli()' id='usr_num_conselho' 
						onkeypress='apenasNumero(this)' onkeyup='apenasNumero(this)'  />				
						<span id='doc_result_soli'>&nbsp;</span>
				</td>
			</tr>
			<tr>
				<td width='25%'>Data da Solicita&ccedil;&atilde;o:</td>
				<td width='75%'><input type='text' value=\"".date("d/m/Y")."\" name='aih_data_solicitacao' id='aih_data_solicitacao' size='30' maxlength='10' class='box' onKeypress=\"return Ajusta_Data(this, event);\" onchange=\"buscaferiado(this.value);\" /></td>
			</tr>
	</table>
	</fieldset>

	<fieldset>
		<legend>Preencher em caso de causas externas ( Acidentes ou Viol&ecirc;ncias ) </legend>
		
			<table width='100%' align='center' cellspacing='2' cellpadding='0' border='0'>
			
			  <tr>
				  <td whidth='25%' valign='top'>Tipo de Acidente: </td>
				  <td whidth='75%'>				  
						<label><input type='radio' name='aih_tipo_acidente' value='Transito'> Acidente de Tr&acirc;nsito</label><br />
						<label><input type='radio' name='aih_tipo_acidente' value='Tipico'> Acidente de Trabalho T&iacute;pico</label><br />
						<label><input type='radio' name='aih_tipo_acidente' value='Trajeto'> Acidente de Trabalho Trajeto</label><br />	 
						<label><input type='radio' name='aih_tipo_acidente' value='Outros'> Outros</label>				 
				  </td>
			  </tr>
			  <tr>
				<td whidth='25%' valign='top'>Observações:</td>
				<td whidth='75%'>
				<textarea name='aih_observacao' id='aih_observacao' cols='57' rows='3' class='box'></textarea>
				</td>
			  </tr>

			  <tr>	
				  <td whidth='25%'>CNPJ da Seguradora: </td>
				  <td whidth='75%'><input type='text' name='aih_cnpj_seguradora' id='aih_cnpj_seguradora' class='box' size='30' maxlength='17' /></td />
			  </tr>
				<tr>
				  <td whidth='25%'>N&ordm; do Bilhete: </td>
				  <td whidth='75%'><input type='text' name='aih_n_bilhete' id='aih_n_bilhete' class='box' size='30' maxlength='20' /></td>
			  </tr>
				<tr>
				  <td whidth='25%'>S&eacute;rie:</td>
				  <td whidth='75%'><input type='text' name='aih_serie' id='aih_serie' class='box' size='30' maxlength='15' /></td>
			  </tr>
				<tr>
				  <td whidth='25%'>CNPJ da Empresa: </td>
				  <td whidth='75%'><input type='text' name='aih_cnpj_da_empresa' id='aih_cnpj_da_empresa' class='box' size='30' maxlength='17' /></td>
			  </tr>
				<tr>
				  <td whidth='25%'>CNAE da Empresa: </td>
				  <td whidth='75%'><input type='text' name='aih_cnae_da_empresa' id='aih_cnae_da_empresa' class='box' size='30' maxlength='50' /></td>
			  </tr>
				<tr>
				  <td whidth='25%'>CBOR:</td>
				  <td whidth='75%'><input type='text' name='aih_cbor' id='aih_cbor' class='box' size='30' maxlength='50' /></td>
			  </tr>
				<tr>
					<td width='25%' valign='top'>V&iacute;nculo com a Previd&ecirc;ncia:</td>
					<td whidth='75%'>
						<label><input type='radio' name='aih_vinculo_previdencia' value='Empregado'> Empregado</label><br />
						<label><input type='radio' name='aih_vinculo_previdencia' value='Empregador'> Empregador</label><br />
						<label><input type='radio' name='aih_vinculo_previdencia' value='Aut&ocirc;nomo'> Aut&ocirc;nomo</label><br />
						<label><input type='radio' name='aih_vinculo_previdencia' value='Desempregado'> Desempregado</label><br /> 
						<label><input type='radio' name='aih_vinculo_previdencia' value='Aposentado'> Aposentado</label><br />
						<label><input type='radio' name='aih_vinculo_previdencia' value='N&atilde;o Segurado'> N&atilde;o Segurado</label></td>
				</tr>
			</table>
	</fieldset>

	<fieldset>
		<legend>Autoriza&ccedil;&atilde;o</legend>
		
			<table width='100%' align='center' cellspacing='2' cellpadding='0' border='0'>
			
				<tr>
					<td width='25%'>Nome do Profissional Autorizador:</td>
					<td whidth='75%'><input type='text' name='med_autorizador' id='usr_nome_2' size='60' maxlength='255' class='box'>
									 <input type='hidden' name='med_autorizador_h' id='usr_codigo_2' size='60' maxlength='255' class='box'>
					</td>
				</tr>

				<tr>
					<td width='25%'>
                        <strong>N&deg; da Autoriza&ccedil;&atilde;o de Interna&ccedil;&atilde;o Hospitalar:</strong>
                    </td>
					<td whidth='75%'>
			<input type='hidden' name='aih_num_h' value='"; print $aih_num_arr[0]; echo "' />					
			<input type='text' name='aih_numero_aih_h' value='"; print $aih_num_arr[0]; echo"' readonly class='box' size='30' />
					</td>
				</tr>
			

				<tr>
					<td width='25%'>Núm. Conselho:</td>
					<td whidth='75%'>
						<input type='text' name='aih_n_doc_prof_autorizador' size='30' maxlength='20'
							class='box' id='usr_num_conselho_2' readonly='readonly'  />
					</td>
				</tr>

				<tr>
					<td width='25%'>Data da Autoriza&ccedil;&atilde;o:</td>
					<td whidth='75%'><input type='text' name='aih_data_autorizacao' id='aih_data_autorizacao' size='30' maxlength='10' class='box' onKeypress=\"return Ajusta_Data(this, event);\" onchange=\"buscaferiado(this.value);\" /></td>
				</tr>
				
				<tr>
					<td width='25%'>&nbsp;</td>
					<td width='75%'><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg /></td>
				</tr>
			</table>
	</fieldset>

	</form>";

 		
 //fechamento do if 

}
else if($acao=="form_agrupa")
{

			$sql = "SELECT a.aih_codigo, a.aih_numero_aih,
		
			( CASE WHEN a.usu_codigo IS NOT NULL THEN p0.usu_nome
			WHEN a.pac_aih_codigo IS NOT NULL THEN p1.pac_nome
			ELSE 'none' END ) as pac_nome
			
			FROM aih AS a
			
			LEFT JOIN usuario AS p0 ON p0.usu_codigo = a.usu_codigo
			LEFT JOIN aih_paciente AS p1 ON p1.pac_codigo = a.pac_aih_codigo
			
			WHERE aih_codigo=$aih_codigo AND aih_ativo='S' ";
			
			$query = db_query($sql);
			$res = pg_fetch_array($query);
			
		echo"
		<fieldset>
			<legend>Paciente selecionado para agrupar AIH.</legend>
				<strong>Paciente</strong> : ".$res['pac_nome']." <br />
				<strong>Nº AIH</strong> : ".$res['aih_numero_aih']."
		</fieldset>
		";

  echo "<fieldset>
			<legend>Opções de Busca</legend>
			<a href=aih.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>					
			<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
			  <tr><td>			
					<form method='GET' action='$PHP_SELF?$_SERVER[QUERY_STRING]' />
					<table width=100% align=left cellspacing=3 cellpadding=0 border=0 />
						<tr>					
						<input type='hidden' name='aih_codigo' id='aih_codigo' value='$aih_codigo' />
						<input type='hidden' name='usu_codigo' id='usu_codigo' value='$usu_codigo' />
						<input type='hidden' name='acao' value='busca_aih' />
						<input type='hidden' name='id_login' value='$id_login' />
						<td width=30>Buscar:</td>
						<td width=80><input type='text' name='palavra_chave' id='palavra_chave' class='box' /></td>
						<td width=40>
							<select name='tipo_busca_aih' id='tipo_busca_aih' class='box'>
							<option value='1'".( $tipo_busca_aih==1 ? ' selected' : '' ).">Número AIH</option>
							<option value='2'".( $tipo_busca_aih==2 ? ' selected' : '' ).">Nome do Paciente</option>
							</select>
						</td>
						<td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg></td>
						</tr>
					</table>
					</form>			  
				</td></tr>
			 </table>
		</fieldset>";	

}
else if($acao=='busca_aih')
{

			echo	"<input type='hidden' name='aih_codigo' id='aih_codigo' value='$aih_codigo' />
				 	 <input type='hidden' name='usu_codigo' id='usu_codigo' value='$usu_codigo' />";
			
			
			$sql = "SELECT a.aih_codigo, a.aih_numero_aih,  
		
			( CASE WHEN a.usu_codigo IS NOT NULL THEN p0.usu_nome
			WHEN a.pac_aih_codigo IS NOT NULL THEN p1.pac_nome
			ELSE 'none' END ) as pac_nome
			
			FROM aih AS a
			
			LEFT JOIN usuario AS p0 ON p0.usu_codigo = a.usu_codigo
			LEFT JOIN aih_paciente AS p1 ON p1.pac_codigo = a.pac_aih_codigo
			
			WHERE aih_codigo=$aih_codigo AND aih_ativo='S' ";
			
			$query = db_query($sql);
			$res = pg_fetch_array($query);
			
		echo"
		<fieldset>
			<legend>Paciente selecionado para agrupar AIH.</legend>
				<strong>Paciente</strong> : ".$res['pac_nome']." <br />
				<strong>Nº AIH</strong> : ".$res['aih_numero_aih']."
		</fieldset>
		";
		
		
  echo "<fieldset>
			<legend>Opções de Busca</legend>
			<a href=aih.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>					
			<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
			  <tr><td>			
					<form method='GET' action='$PHP_SELF?$_SERVER[QUERY_STRING]' />
					<table width=100% align=left cellspacing=3 cellpadding=0 border=0 />
						<tr>					
						<input type='hidden' name='aih_codigo' id='aih_codigo' value='$aih_codigo' />
						<input type='hidden' name='usu_codigo' id='usu_codigo' value='$usu_codigo' />						
						<input type='hidden' name='acao' value='busca_aih' />
						<input type='hidden' name='id_login' value='$id_login' />
						<td width=30>Buscar:</td>
						<td width=80><input type='text' name='palavra_chave' id='palavra_chave' class='box' /></td>
						<td width=40>
							<select name='tipo_busca_aih' id='tipo_busca_aih' class='box'>
							<option value='1'".( $tipo_busca_aih==1 ? ' selected' : '' ).">Número AIH</option>
							<option value='2'".( $tipo_busca_aih==2 ? ' selected' : '' ).">Nome do Paciente</option>
							</select>
						</td>
						<td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg></td>
						</tr>
					</table>
					</form>			  
				</td></tr>
			 </table>
		</fieldset>";	
	
	$palavra_chave = strtoupper($palavra_chave);
//-> Subistituindo o + por porcentagem na busca
	$str = str_replace("+","%",$palavra_chave);
	$pos = strpos($palavra_chave,"+");

	if( ! empty($str) )
	{
		$where 		= 'WHERE ';
		$where_c 	= "ILIKE TO_ASCII('%$palavra_chave%')";
		
		switch( $tipo_busca_aih )
		{
			case 1:
				$where .= "(aih_numero_aih $where_c)";
				break;
								
			case 2:
				$where .="(p0.usu_nome $where_c OR p1.pac_nome $where_c)";						
				break;
				
			default:
				$where = '';
		}
	}

	$sql 	= "SELECT a.aih_codigo, a.usu_codigo, a.aih_numero_aih, a.aih_segunda_via, to_char(aih_data_autorizacao, 'dd/mm/YYYY') as aih_data_autorizacao,  
		
			( CASE WHEN a.usu_codigo IS NOT NULL THEN p0.usu_nome
			WHEN a.pac_aih_codigo IS NOT NULL THEN p1.pac_nome
			ELSE 'none' END ) as pac_nome
			
			FROM aih AS a
			
			LEFT JOIN usuario AS p0 ON p0.usu_codigo = a.usu_codigo
			LEFT JOIN aih_paciente AS p1 ON p1.pac_codigo = a.pac_aih_codigo
			
			$where AND (a.usu_codigo = $usu_codigo OR a.pac_aih_codigo = $usu_codigo) AND a.aih_numero_aih <> '$res[aih_numero_aih]' AND aih_ativo='S' ";
			
	$query 	=	db_query($sql);
	$num	=	pg_num_rows($query);

	if($num=="0") { $resp = "Nenhum Registro encontrado com \"$palavra_chave\""; }
	if($num=="1") { $resp = "Encontrado <b>$num</b> Registro com \"$palavra_chave\""; }
	if($num>"1") { $resp = "Encontrados <b>$num</b> Registros com \"$palavra_chave\""; }
	

echo"		
		<table width='98%' align='center' cellspacing='0' cellpadding='0' border='0'>
		<tr>
			<td>
				<fieldset>
	   				<legend>".$resp."</legend>
					<table width='100%' align='center' cellspacing='2' cellpadding='4' border='0' class='lista'>
					<tr bgcolor='#FFFFFF'>
						<th>Nome Paciente</th>
						<th>N&uacute;m. AIH</th>
						<th>Data da Autoriza&ccedil;&atilde;o</th>
						<th>&nbsp;</th>
					</tr>";
					
			   while($row=pg_fetch_array($query)) {
					
					echo"
						  <form method='post' action='$PHP_SELF'>
	
							<input type='hidden' name='id_login' value='$id_login' />
							<input type='hidden' name='aih_codigo' value='$aih_codigo' />

						<tr>
							<td>$row[pac_nome]	<input type=hidden name=aih_codigo value=$res[aih_codigo] /> </td>
							<td align='center'>$row[aih_numero_aih]</td>
							<td align='center'>$row[aih_data_autorizacao]</td>
							<td width='80' align='center'>
							<a href='$PHP_SELF?id_login=$id_login&acao=efetiva_agrupa&usu_codigo=$usu_codigo&aih_codigo=$aih_codigo&cod_para_agrupar=$row[aih_codigo]&num_aih_resto=$row[aih_numero_aih]'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/agrupar_on.jpg' border='0' /></a>
							</td>
						</tr>
						</form>
					 ";
				 }
				 
			   echo "</table>
					
				</fieldset>
			</td>
		</tr>
		</table>					
";
}
else if ($acao=='efetiva_agrupa')
{

	  echo monta_janela('janela_aih','');

	$sql = "SELECT a.aih_codigo, a.usu_codigo, a.aih_numero_aih, a.aih_segunda_via, to_char(aih_data_autorizacao, 'dd/mm/YYYY') as aih_data_autorizacao,  
		
			( CASE WHEN a.usu_codigo IS NOT NULL THEN p0.usu_nome
			WHEN a.pac_aih_codigo IS NOT NULL THEN p1.pac_nome
			ELSE 'none' END ) as pac_nome
			
			FROM aih AS a
			
			LEFT JOIN usuario AS p0 ON p0.usu_codigo = a.usu_codigo
			LEFT JOIN aih_paciente AS p1 ON p1.pac_codigo = a.pac_aih_codigo
			
			WHERE aih_codigo=$aih_codigo AND aih_ativo='S' ";
			
			$query = db_query($sql);
			$res = pg_fetch_array($query);
			
		echo"
		<fieldset>
			<legend>Paciente selecionado para agrupar AIH.</legend>
				<strong>Paciente :</strong> ".$res['pac_nome']." <br />
				<strong>Nº AIH :</strong> ".$res['aih_numero_aih']." <br /> 
				<strong>Nº de AIH pode ser usado novamente :</strong> ".$num_aih_resto."
		</fieldset>
	<input type='hidden' name='cod_para_agrupar' id='cod_para_agrupar' value='$cod_para_agrupar' /><BR />
	<input type='hidden' name='aih_codigo' id='aih_codigo' value='$aih_codigo' />	
	<input type='hidden' name='num_aih_resto' id='num_aih_resto' value='$num_aih_resto' />	
	";
			
  echo "<fieldset>
		<legend>Opções de Cadastro</legend>
			<a href=aih.php?id_login=$id_login><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif' border='0'></a>
			<a href='aih_apac_cad_num.php?id_login=$id_login&tipo=AIH'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/cadastrar_n_aih_on.jpg' border='0'></a>
		</fieldset>";

$sql = 	"SELECT ".
			"TO_CHAR(aih_dataini, 'dd/mm/YYYY') as aih_dataini, ".
			"TO_CHAR(aih_data_alta, 'dd/mm/YYYY') as aih_data_alta, ".
			"aih_desc_proc_soli, aih_ci, aih_cid_cod_princ, ci_cod, cd10_codigo, cd10_descricao, proc_codigo, proc_nome, proc_classificacao_sus ".
		"FROM aih AS a ".
			"LEFT JOIN ci AS c ON a.aih_ci=c.ci_codigo ".
			"LEFT JOIN procedimento AS p ON a.aih_desc_proc_soli=p.proc_codigo ".
			"LEFT JOIN cid10 ci ON a.aih_cid_cod_princ=ci.cd10_codigo ".
			"WHERE aih_codigo=$aih_codigo AND aih_ativo='S'";
			
	$res = pg_query($sql);
	$row = pg_fetch_array($res);

  echo "<form name='aih_form_altera' method='post' action='$PHP_SELF?id_login=$id_login&codigo=$aih_codigo&cod_para_agrupar=$cod_para_agrupar&num_aih_resto=$num_aih_resto' onSubmit='return verifica_campos_alteracao()'>
		<input type='hidden' name='acao' value='edit_agrupar'>
		<input type='hidden' name='aih_codigo' value='$aih_codigo'>
		
	   <fieldset>
	    <legend>Agrupar AIH</legend>
	     <table width='100%' align='center' cellspacing='3' cellpadding='0' border='0'>
	      <tr>
			<td width='25%'>Data de Internação:</td>
			<td width='75%'><input type='text' name='aih_dataini' class='box' size='30' maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\" onchange=\"buscaferiado(this.value);\" value='$row[aih_dataini]' /></td>
	      </tr>
	      <tr>
		<td width='25%'>Data da Alta: </td>
		<td width='75%'><input type='text' name='aih_data_alta' class='box' size='30' maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\" onchange=\"buscaferiado(this.value);\" value='$row[aih_data_alta]' /></td> 
	      </tr>
			<tr>
		<td width='25%' valign='top'>Descri&ccedil;&atilde;o do Procedimento:</td>
		<td width='75%'><input name='aih_desc_proc_soli' id='aih_desc_proc_soli' class='box' size='60' maxlength='255' value='$row[proc_nome]' />
				<input type='hidden' name='aih_desc_proc_soli_h' id='aih_desc_proc_soli_h' value='$row[proc_codigo]' />
				<input type='hidden' name='aih_classificacao_sus' id='aih_classificacao_sus' value='$row[proc_classificacao_sus]' />
				<a href='#janela_aih' onclick=\"mostra_janela('janela_aih');init('desc_proc');document.getElementById('janela_aih_titulo_txt').innerHTML='Descri&ccedil;&atilde;o do Procedimento'\">
				<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg' alt='Localizar' align='absmiddle' border='0' /></a></td>
			</tr>";
					  
			//$sql_buscaci = 'SELECT ci_codigo, ci_cod FROM ci WHERE ci_codigo='.$row['aih_ci'];
			$sql_buscaci = 'SELECT ci_codigo, ci_cod FROM ci';
			$query_ci	 = pg_query($sql_buscaci);
				echo"<tr>
						<td width='25%' height='28'>C.I.:</td>
						<td width='75%'>";
				echo"
						<select name='aih_ci' id='aih_ci' class='box'>
						";
							while( $res_ci = pg_fetch_array($query_ci) ){ 
						echo "	
								<option value='$res_ci[0]'"; 
									if ( $res_ci['ci_codigo'] == $row['aih_ci'] ){
										echo "selected";
									}else{
										echo "";
									}
								echo"
								 > ";  echo $res_ci[1]; echo" </option>"; 
							} 
					echo "		
						</select>
				  </td>
					</tr>
					
	      <tr>
		<td width='25%'>Cid 10 Principal:</td>
		<td width='75%'><input type='text' name='aih_cid_cod_princ' id='aih_cid_cod_princ' size='60' maxlength='150' class='box' value='$row[cd10_descricao]' />
			<input type='hidden' name='aih_cid_cod_princ_h' id='aih_cid_cod_princ_h' value='$row[cd10_codigo]' />
			<a href='#janela_aih' onclick=\"mostra_janela('janela_aih');init('cid');document.getElementById('janela_aih_titulo_txt').innerHTML='Cid. 10 Principal'\"><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg' alt='Localizar' align='absmiddle' border='0' /></a></td>
	      </tr>
	      <tr>
	       <td width='25%'>&nbsp;</td>
	       <td width='75%'><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg></td>
	      </tr>
       </table>
	   </fieldset>
	   <br />
		</form>";

	
}

/*
if($acao=="form_edit")
{

  echo monta_janela('janela_aih','');
  echo "<fieldset>
		<legend>Opções de Cadastro</legend>
			<a href=aih.php?id_login=$id_login><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif' border='0'></a>
			<a href='aih_apac_cad_num.php?id_login=$id_login&tipo=AIH'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/cadastrar_n_aih_on.jpg' border='0'></a>
		</fieldset>";

	$sql = 	"SELECT ".
				"TO_CHAR(aih_dataini, 'dd/mm/YYYY') as aih_dataini, ".
				"TO_CHAR(aih_data_alta, 'dd/mm/YYYY') as aih_data_alta, ".
				"aih_desc_proc_soli, aih_ci, aih_cid_cod_princ, ci_cod, cd10_descricao, proc_nome ".
			"FROM aih AS a ".
				"LEFT JOIN ci AS c ON a.aih_ci=c.ci_codigo ".
				"LEFT JOIN procedimento AS p ON a.aih_desc_proc_soli=p.proc_codigo ".
				"LEFT JOIN cid10 ci ON a.aih_cid_cod_princ=ci.cd10_codigo ".
			"WHERE aih_codigo=$aih_codigo AND aih_ativo='S' ";
			
	$res = pg_query($sql);
	$row = pg_fetch_array($res);

  echo "<form name='aih_form_altera' method='post' action='$PHP_SELF?id_login=$id_login&codigo=$aih_codigo' onSubmit='return verifica_campos_alteracao()'>
		<input type='hidden' name='acao' value='edit'>
		<input type='hidden' name='med_codigo' value='$med_codigo'>
		<input type='hidden' name='aih_codigo' value='$aih_codigo'>
		

	   <fieldset>
	    <legend>Alteração de AIH</legend>
	     <table width='100%' align='center' cellspacing='3' cellpadding='0' border='0'>
	      <tr>
			<td width='25%'>Data de Internação:</td>
			<td width='75%'><input type='text' name='aih_dataini' class='box' size='30' maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\" onchange=\"buscaferiado(this.value);\" value='$row[aih_dataini]' /></td>
	      </tr>
	      <tr>
		<td width='25%'>Data da Alta: </td>
		<td width='75%'><input type='text' name='aih_data_alta' class='box' size='30' maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\" onchange=\"buscaferiado(this.value);\" value='$row[aih_data_alta]' /></td> 
	      </tr>
			<tr>
				<td width='25%' valign='top'>Descri&ccedil;&atilde;o do Procedimento:</td>
				<td width='75%'><input name='aih_desc_proc_soli' id='aih_desc_proc_soli' class='box' size='60' maxlength='255' value='$row[proc_nome]' />
								<input type='hidden' name='aih_desc_proc_soli_h' id='aih_desc_proc_soli_h' size='60' maxlength='255' class='box'>
				<a href='#janela_aih' onclick=\"mostra_janela('janela_aih');init('desc_proc');document.getElementById('janela_aih_titulo_txt').innerHTML='Descri&ccedil;&atilde;o do Procedimento'\">
				<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg' alt='Localizar' align='absmiddle' border='0' /></a></td>
			</tr>";
					  
			//$sql_buscaci = 'SELECT ci_codigo, ci_cod FROM ci WHERE ci_codigo='.$row['aih_ci'];
			$sql_buscaci = 'SELECT ci_codigo, ci_cod FROM ci';
			$query_ci	 = pg_query($sql_buscaci);
				echo"<tr>
						<td width='25%' height='28'>C.I.:</td>
						<td width='75%'>";
				echo"
						<select name='aih_ci' id='aih_ci' class='box'>
						";
							while( $res_ci = pg_fetch_array($query_ci) ){ 
						echo "	
								<option value='$res_ci[0]'"; 
									if ( $res_ci['ci_codigo'] == $row['aih_ci'] ){
										echo "selected";
									}else{
										echo "";
									}
								echo"
								 > ";  echo $res_ci[1]; echo" </option>"; 
							} 
					echo "		
						</select>
				  </td>
					</tr>
					
	      <tr>
		<td width='25%'>Cid 10 Principal:</td>
		<td width='75%'><input type='text' name='aih_cid_cod_princ' id='aih_cid_cod_princ' size='60' maxlength='150' class='box' value='$row[cd10_descricao]' />
			<input type='hidden' name='aih_cid_cod_princ_h' id='aih_cid_cod_princ_h' size='60' maxlength='255' class='box'>
			<a href='#janela_aih' onclick=\"mostra_janela('janela_aih');init('cid');document.getElementById('janela_aih_titulo_txt').innerHTML='Cid. 10 Principal'\"><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg' alt='Localizar' align='absmiddle' border='0' /></a></td>
	      </tr>
	      <tr>
	       <td width='25%'>&nbsp;</td>
	       <td width='75%'><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg></td>
	      </tr>
       </table>
	   </fieldset>
	   <br />
		</form>";
}
else */

//<------------------------------------->
//<-> INSERÇÃO DE DADOS NA TABELA ADD <->
//<------------------------------------->
else if( $acao=='add' ){
	
	$aih_dt_cadastro = date('d/m/Y');
	reglog($id_login,"Adicionando Registro em AIH");

	// o numero existe ?

	//db_query("BEGIN");
	//db_query("LOCK TABLE aih_apac_numeros_resto IN SHARE MODE");

	$stmt_resto = "SELECT aan_codigo FROM aih_apac_numeros_resto WHERE aan_numero_resto='$aih_num_h'";
	$tem_numero = db_get( $stmt_resto );

	//$apac_num_arr = aih_apac_proximo_num( 'APAC' );
	//if( $apac_num_arr[0] == 0 )
	if( ! $tem_numero )
	{
		print '
		<script type="text/javascript">
			alert("A AIH nao pode ser cadastrada!\nNao existe mais numeros disponiveis");
		</script>
		<p class="aviso">AIH nao inserida !</p>
		</body></html>';
		die();
	}
	

//	$stmt_1 = "DELETE FROM aih_apac_numeros_resto WHERE aan_numero_resto = '$aih_numero_aih_h' ";
	//$stmt_1 = "DELETE FROM aih_apac_numeros_resto WHERE aan_numero_resto = '$aih_num_h' ";
    //db_query( $stmt_1 );

	//db_query("COMMIT");

    // INSERINDO NA AIH !
	db_query("begin");

 	$stmt = "INSERT INTO aih ( 
			med_codigo_solicitante, 
			aih_cnes_soli, 
			usr_codigo, 
			aih_dataini, 
			aih_data_alta, 
			aih_principais_sintomas, 
			aih_justificativa_internacao, 
			aih_principais_resultados, 
			aih_diag_ini, 
			aih_cid_cod_princ, 
			aih_cid_cod_secun, 
			aih_cid_cod_terc, 
			aih_desc_proc_soli, 
			aih_clinica, 
			aih_ci, 
			aih_tipo_doc_proc_soli, 
			aih_n_doc_prof_solicitante, 
			med_solicitante_proc, 
			aih_data_solicitacao, 
			aih_vinculo_previdencia, 
			med_autorizador, 
			aih_tipo_doc_autorizacao, 
			aih_n_doc_prof_autorizador, 
			aih_data_autorizacao, 
			aih_numero_aih, 
			aih_segunda_via, 
			pac_aih_codigo, 
			usu_codigo, 
			aih_tipo_acidente, 
			aih_observacao,
			aih_cnpj_seguradora, 
			aih_n_bilhete, 
			aih_serie, 
			aih_cnpj_da_empresa, 
			aih_cnae_da_empresa, 
			aih_mes_compet, 
			aih_ano_compet, 
			aih_ativo, 
			aih_prontuario_hospital, 
			aih_dt_cadastro
	 ) VALUES ( 
			".intval($med_codigo_solicitante_h).", 
			".intval($aih_cnes_soli).", 
			".intval($id_login).", 
			'".( empty($aih_dataini) ? 'NULL' : trim(strtoupper($aih_dataini)) )."', 
			'".( empty($aih_data_alta) ? 'NULL' : trim(strtoupper($aih_data_alta)) )."', 
			'".trim(strtoupper($aih_principais_sintomas))."', 
			'".trim(strtoupper($aih_justificativa_internacao))."', 
			'".trim(strtoupper($aih_principais_resultados))."', 
			'".$aih_diag_ini."', 
			".($aih_cid_cod_princ_h?"'".$aih_cid_cod_princ_h."'":"NULL").", 
			".($aih_cid_cod_secun?"'".$aih_cid_cod_secun."'":"NULL").", 
			".($aih_cid_cod_terc?"'".$aih_cid_cod_terc."'":"NULL").",
			'".$aih_desc_proc_soli_h."', 
			'".$aih_clinica."', 
			".intval($aih_ci).", 
			'".$aih_tipo_doc_proc_soli."', 
			'".$aih_n_doc_prof_solicitante."', 
			'".$med_solicitante_proc_h."', 
			'".( $aih_data_solicitacao != '' ? $aih_data_solicitacao : '01/01/1900' )."', 
			'".$aih_vinculo_previdencia."', 
			'".$med_autorizador_h."', 
			'".$aih_tipo_doc_autorizacao."', 
			'".$aih_n_doc_prof_autorizador."', 
			'".( $aih_data_autorizacao != '' ? $aih_data_autorizacao : '01/01/1900' )."', 
			'".$aih_num_h."', 
			'N', 
			".( $pac_aih == 'S' ? intval($usu_codigo) : 'null' ).", 
			".( $pac_aih == 'N' ? intval($usu_codigo) : 'null' ).", 
			'".$aih_tipo_acidente."', 
			'".$aih_observacao."',
			".intval($aih_cnpj_seguradora).", 
			".intval($aih_n_bilhete).", 
			'".$aih_serie."', 
			".intval($aih_cnpj_da_empresa).", 
			".intval($aih_cnae_da_empresa).", 
			".intval($aih_mes_compet).", 
			".intval($aih_ano_compet).", 
			'S', 
			'".$aih_prontuario_hospital."', 
			CURRENT_DATE)";
	//die($stmt);
	$query = db_query($stmt);
	$stmt_1 = "DELETE FROM aih_apac_numeros_resto WHERE aan_numero_resto = '$aih_num_h' ";
        db_query( $stmt_1 );
	
	db_query('COMMIT');


	// PAGINA DE IMPRESSAO DE AIH.

	echo "
        <p class='aviso ok'>AIH Inserida</p>
        <p><a href='$PHP_SELF?id_login=$id_login'>&lt; Voltar</a></p>
        <script type=\"text/javascript\">
    		setTimeout('impressao(\"$aih_num_h\")');
            //setTimeout(\"location='$PHP_SELF?acao=form_add&id_login=$id_login&med_codigo_sol=$med_codigo_solicitante_h'\", 3000);
        </script>";

}else if ($acao == 'segundavia'){

	//$sql =  "UPDATE aih ".
	//		"SET aih_segunda_via='S' ".
	//		"WHERE aih_codigo=$aih_codigo";
			
	//$query = pg_query($sql);

    echo "
        <p class='aviso ok'>Imprimindo...</p>
        <p><a href='$PHP_SELF?id_login=$id_login'>&lt; Voltar</a></p>
        <script type=\"text/javascript\">
            setTimeout('impressao_segunda_via()');
        </script>";

    /*
	echo "<SCRIPT LANGUAGE=\"JavaScript\">
			 setTimeout('impressao_segunda_via()');
 			 setTimeout(\"location='$PHP_SELF?acao=&id_login=$id_login'\", 3000);
		  </SCRIPT>";
                                  */
}else if($acao == 'edit_agrupar'){
	
	// atualiza a aih antiga que vai perder o numero. e dar o numero para ser usado novamente.
	$stmt0 = "UPDATE aih SET aih_ativo = 'N' WHERE aih_codigo=$cod_para_agrupar";
	db_query($stmt0);
	
	$stmt = "UPDATE aih SET ".
			"aih_dataini='$aih_dataini', ".
			"aih_data_alta='$aih_data_alta', ".
			"aih_desc_proc_soli=$aih_desc_proc_soli_h, ".
			"aih_ci=$aih_ci, ".
			"aih_cid_cod_princ=$aih_cid_cod_princ_h ".
			"WHERE aih_codigo=$codigo";

	$query_2 = db_query($stmt);
			
	$sql = "INSERT INTO aih_apac_numeros_resto (
				aan_codigo_fk, 
				aan_numero_resto
			) VALUES (
				'$codigo',
				'$num_aih_resto'
			)"; 
	
	$query = db_query($sql);
	
		echo "
        <div class='aviso ok'>AIH Agrupada...</div>
        <script type='text/javascript'>
            setTimeout(\"document.location.href='$_SERVER[PHP_SELF]?id_login=$id_login'\",3000);
        </script>
	";
}

/*

#############################################
##                                         ##
##     Alterado por André @ 2007-03-06     ##
##                                         ##
#############################################

else if($acao == 'edit')
{
		// inicio da transacao
		db_query("BEGIN");
		
		// atualiza a aih antiga
		$stmt0 = "UPDATE aih SET aih_ativo = 'N' WHERE aih_codigo=$codigo";
		db_query($stmt0);
		
		// seleciona os dados
		$stmt1 =	
		"SELECT med_codigo_solicitante, aih_cnes_soli, usr_codigo, aih_dataini, aih_data_alta,
			aih_principais_sintomas, aih_justificativa_internacao, aih_principais_resultados, aih_diag_ini, aih_cid_cod_princ, 
			aih_cid_cod_secun, aih_cid_cod_terc, aih_desc_proc_soli, aih_clinica, aih_ci, aih_tipo_doc_proc_soli, 
			aih_n_doc_prof_solicitante, med_solicitante_proc, aih_data_solicitacao, aih_vinculo_previdencia, med_autorizador, 
			aih_tipo_doc_autorizacao, aih_n_doc_prof_autorizador, aih_data_autorizacao, aih_numero_aih, aih_segunda_via, 
			pac_aih_codigo, usu_codigo, aih_tipo_acidente, aih_observacao, aih_cnpj_seguradora, aih_n_bilhete, aih_serie, aih_cnpj_da_empresa, 
			aih_cnae_da_empresa, aih_mes_compet, aih_ano_compet, aih_ativo, aih_dt_cadastro, aih_alteradopor
		FROM aih WHERE aih_codigo = $codigo ";
		
		$row = db_getRow($stmt1);
		
		// insere uma nova
		$stmt2 = "INSERT INTO aih ( 
				med_codigo_solicitante, 
				aih_cnes_soli, 
				usr_codigo, 
				aih_dataini, 
				aih_data_alta, 
				aih_principais_sintomas, 
				aih_justificativa_internacao, 
				aih_principais_resultados, 
				aih_diag_ini, 
				aih_cid_cod_princ, 
				aih_cid_cod_secun, 
				aih_cid_cod_terc, 
				aih_desc_proc_soli, 
				aih_clinica, 
				aih_ci, 
				aih_tipo_doc_proc_soli, 
				aih_n_doc_prof_solicitante, 
				med_solicitante_proc, 
				aih_data_solicitacao, 
				aih_vinculo_previdencia, 
				med_autorizador, 
				aih_tipo_doc_autorizacao, 
				aih_n_doc_prof_autorizador, 
				aih_data_autorizacao, 
				aih_numero_aih, 
				aih_segunda_via, 
				pac_aih_codigo, 
				usu_codigo, 
				aih_tipo_acidente, 
				aih_observacao,
				aih_cnpj_seguradora, 
				aih_n_bilhete, 
				aih_serie, 
				aih_cnpj_da_empresa, 
				aih_cnae_da_empresa, 
				aih_mes_compet, 
				aih_ano_compet, 
				aih_ativo, 
				aih_dt_cadastro,
				aih_alteradopor
		 ) VALUES ( 
				{$row['med_codigo_solicitante']}, 
				{$row['aih_cnes_soli']}, 
				{$row['usr_codigo']}, 
				'{$aih_dataini}', 
				'{$aih_data_alta}', 
				'{$row['aih_principais_sintomas']}', 
				'{$row['aih_justificativa_internacao']}', 
				'{$row['aih_principais_resultados']}', 
				'{$row['aih_diag_ini']}', 
				'{$aih_cid_cod_princ_h}', 
				'{$row['aih_cid_cod_secun']}', 
				'{$row['aih_cid_cod_terc']}', 
				'{$aih_desc_proc_soli_h}', 
				'{$row['aih_clinica']}', 
				{$aih_ci}, 
				'{$row['aih_tipo_doc_proc_soli']}', 
				'{$row['aih_n_doc_prof_solicitante']}', 
				'{$row['med_solicitante_proc']}', 
				'{$row['aih_data_solicitacao']}', 
				'{$row['aih_vinculo_previdencia']}', 
				'{$row['med_autorizador']}', 
				'{$row['aih_tipo_doc_autorizacao']}', 
				'{$row['aih_n_doc_prof_autorizador']}', 
				'{$row['aih_data_autorizacao']}', 
				'{$row['aih_numero_aih']}', 
				'N', 
				".( empty($row['pac_aih_codigo']) ? 'null' : $row['pac_aih_codigo'] ).", 
				".( empty($row['usu_codigo']) ? 'null' : $row['usu_codigo'] ).", 
				'{$row['aih_tipo_acidente']}', 
				'{$row['aih_observacao']}',
				{$row['aih_cnpj_seguradora']}, 
				{$row['aih_n_bilhete']}, 
				'$row[aih_serie]', 
				{$row['aih_cnpj_da_empresa']}, 
				{$row['aih_cnae_da_empresa']}, 
				{$row['aih_mes_compet']}, 
				{$row['aih_ano_compet']}, 
				'S',  
				current_date,
				".intval($id_login)." ) ";
								
		
		db_query($stmt2);
		
		// fim da transacao
		db_query("COMMIT");
		
		
		echo "
        <div class='aviso ok'>AIH Atualizada...</div>
		<script TYPE='text/javascript'>
    		setTimeout(\"document.location.href='$_SERVER[PHP_SELF]?id_login=$id_login'\",3000);
		</script>
		";

}

*/

?>
<iframe id='frame_impressao' width='0' height='0' frameborder='0'>
</iframe>
</body>
</html>
