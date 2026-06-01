<?php
/**
 * @Modulo: Autorização de Internação Hospitalar ( AIH )
 * @Arquivos Relacionados: aih.js, aih_apac.inc.php, aih_apac_cad_num.php, aih_op.php, aih_popup.php, aih_print.php, aih_print_sesgunda_via.php
 * @Responsavel: André Filipe, Eduardo Bruno
 * @Tabelas: aih
 * @Acao: Edita as AIH's.
*/  

session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
verauth($id_login);
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."aih_apac.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."__array.php";

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
 
   if(document.aih_form.med_codigo_solicitante.value == '') {
	alert("Por favor Preencha o Nome do Estabelecimento Solicitante");
	document.aih_form.med_codigo_solicitante.focus();
	return false;
   }
   if(document.aih_form.aih_cnes_soli.value == '') {
	alert("Por favor Preencha o CNES");
	document.aih_form.aih_cnes_soli.focus();
	return false;
   }
   if(document.aih_form.aih_paciente_nome.value == '') {
	alert("Por favor Preencha o Nome do Paciente");
	document.aih_form.aih_paciente_nome.focus();
	return false;
   }
   if(document.aih_form.aih_prontuario_hospital.value == '') {
	alert("Por favor Preencha o Numero do Prontuário");
	document.aih_form.aih_prontuario_hospital.focus();
	return false;
   }
   if(document.aih_form.aih_data_nasc.value == '') {
	alert("Por favor Preencha a Data de Nascimento");
	document.aih_form.aih_data_nasc.focus();
	return false;
   }
   if(document.aih_form.aih_sexo.value == '') {
	alert("Por favor Preencha o Sexo");
	document.aih_form.aih_sexo.focus();
	return false;
   }
   if(document.aih_form.aih_dataini.value == '') {
	alert("Por favor Preencha a Data de Internação");
	document.aih_form.aih_dataini.focus();
	return false;
   }
   if(document.aih_form.aih_data_alta.value == '') {
	alert("Por favor Preencha a Data da Alta");
	document.aih_form.aih_data_alta.focus();
	return false;
   }
   if(document.aih_form.aih_cid_cod_princ.value == '') {
	alert("Por favor Preencha o Cid 10 Principal");
	document.aih_form.aih_cid_cod_princ.focus();
	return false;
   }
   if(document.aih_form.med_solicitante_proc.value == '') {
	alert("Por favor Preencha o Nome do Profissional Solicitante");
	document.aih_form.med_solicitante_proc.focus();
	return false;
   }
   if(document.aih_form.med_autorizador.value == '') {
	alert("Por favor Preencha o Nome do Profissional Autorizador");
	document.aih_form.med_autorizador.focus();
	return false;
   }
   if(document.aih_form.aih_n_doc_prof_autorizador.value == '') {
	alert("Por favor Preencha o Número do Documento do Profissional Autorizador");
	document.aih_form.aih_n_doc_prof_autorizador.focus();
	return false;
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
    if( txt.length > 0 )
        alert( txt );
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

	$("#med_codigo_solicitante").buscar({
		tipo: 'prestador', // tudo que não seja prestador_servico=M
		template : function(ul, item) {
		return $("<li></li>").data("item.autocomplete", item).append(
			"<a>" + item.label + "</a>").appendTo(ul);
		}
	});// aih_paciente_nome

	$("#aih_paciente_nome").buscar({
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

	$("#aih_desc_proc_soli").buscar({
		tipo: 'procedimento',
		template : function(ul, item) {
			return $("<li></li>").data("item.autocomplete", item).append(
				"<a>" + item.label + "</a>").appendTo(ul);
		}
	}); 

	$("#med_solicitante_proc").buscar({
		tipo: 'usuarios',
		template : function(ul, item) {
			return $("<li></li>").data("item.autocomplete", item).append(
				"<a>" + item.label + "</a>").appendTo(ul);
		}
	}); 

	$("#med_autorizador").buscar({
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

//<--------------------------------------------------------------------------------------------------->
//<-> Formulario de Edicao de Laudo para Internação de Autorização de Internação Hospitalar ( AIH ) <->
//<--------------------------------------------------------------------------------------------------->
	 reglog($id_login,"Entrando em Edição do Laudo para Internação de Autorização de Internação Hospitalar ( AIH )");


	if( ! empty($med_codigo_sol) )
	{
		$med_nome_sol = db_get("SELECT med_nome FROM medico ".
							   "WHERE med_codigo=".intval($med_codigo_sol));
	}
	else
		$med_nome_sol = '';

	echo monta_janela('janela_aih','');
	echo monta_janela('janela_numeros','Números');

	$func = " valida_form_aih(".intval($id_login).")";


	if ( $acao == 'edit') {

        	reglog($id_login,"Alterando de AIH, aih_numero_aih: {$aih_numero_aih_h}");

		// inicio da transacao
		db_query("BEGIN");

		$stmt2 = "UPDATE aih SET
				med_codigo_solicitante=".intval($med_codigo_solicitante_h).",
				aih_cnes_soli=".intval($aih_cnes_soli).",
				aih_mes_compet=".intval($aih_mes_compet).",
				aih_ano_compet=".intval($aih_ano_compet).",
				usu_codigo=".( $pac_aih == 'N' ? intval($usu_codigo) : 'null' ).",
				pac_aih_codigo=".( $pac_aih == 'S' ? intval($usu_codigo) : 'null' ).",
				aih_dataini='".( empty($aih_dataini) ? 'NULL' : trim(strtoupper($aih_dataini)) )."',
				aih_data_alta='".( empty($aih_data_alta) ? 'NULL' : trim(strtoupper($aih_data_alta)) )."',
				aih_principais_sintomas='".trim(strtoupper($aih_principais_sintomas))."',
				aih_justificativa_internacao='".trim(strtoupper($aih_justificativa_internacao))."',
				aih_principais_resultados='".trim(strtoupper($aih_principais_resultados))."',
				aih_diag_ini='".$aih_diag_ini."',
				aih_cid_cod_princ=".($aih_cid_cod_princ_h?"'$aih_cid_cod_princ_h'":"NULL").",
				aih_cid_cod_secun='".($aih_cid_cod_secun?"'$aih_cid_cod_secun'":"NULL")."',
				aih_cid_cod_terc='".($aih_cid_cod_terc?"'$aih_cid_cod_terc'":"NULL")."',
				aih_desc_proc_soli='".$aih_desc_proc_soli_h."',
				aih_clinica='".$aih_clinica."',
				aih_ci=".intval($aih_ci).",
				aih_tipo_doc_proc_soli='".$aih_tipo_doc_proc_soli."',
				aih_n_doc_prof_solicitante='".$aih_n_doc_prof_solicitante."',
				med_solicitante_proc='".$med_solicitante_proc_h."',
				aih_data_solicitacao='".( $aih_data_solicitacao != '' ? $aih_data_solicitacao : '01/01/1900' )."',
				aih_tipo_acidente='".$aih_tipo_acidente."',
				aih_observacao='".$aih_observacao."',
				aih_cnpj_seguradora=".intval($aih_cnpj_seguradora).",
				aih_n_bilhete=".intval($aih_n_bilhete).",
				aih_serie='".$aih_serie."',
				aih_cnpj_da_empresa=".intval($aih_cnpj_da_empresa).",
				aih_cnae_da_empresa=".intval($aih_cnae_da_empresa).",
				aih_vinculo_previdencia='".$aih_vinculo_previdencia."',
				med_autorizador='".$med_autorizador_h."',
				aih_numero_aih='".$aih_numero_aih_h."',
				aih_tipo_doc_autorizacao='".$aih_tipo_doc_autorizacao."',
				aih_n_doc_prof_autorizador='".$aih_n_doc_prof_autorizador."',
				aih_data_autorizacao='".( $aih_data_autorizacao != '' ? $aih_data_autorizacao : '01/01/1900' )."',
				usr_codigo=".intval($id_login).",
				aih_segunda_via='N',
				aih_ativo='S',
				aih_dt_cadastro=CURRENT_DATE,
				aih_prontuario_hospital='".$aih_prontuario_hospital."',
				aih_alteradopor=".intval($id_login)."
				WHERE aih_codigo=$aih_codigo
				 ";
		// 
		//print $stmt2;
		db_query($stmt2);

		if( $apac_num_r != $aih_numero_volta ){

			$sql	= "INSERT INTO aih_apac_numeros_resto (aan_numero_resto, aan_tipo) VALUES ('$aih_numero_volta', 'AIH')";
			$qry	= db_query($sql);
			//print $sql;
			//echo "<br><br>";

			$del 	= "DELETE FROM aih_apac_numeros_resto WHERE aan_numero_resto='$aih_numero_aih_h' AND aan_tipo='AIH' ";
			$query 	= db_query($del);
			//print $stmt;
		}

		// fim da transacao
		db_query("COMMIT");

		echo "
        <div class='aviso ok'>AIH Atualizada...</div>
		<script TYPE='text/javascript'>
    		setTimeout(\"document.location.href='aih.php?id_login=$id_login'\",3000);
		</script>
		";




}elseif( $acao == "" ){




//---------------------------------------------------------------------------------------------------------------------------------

 		$stmt = "SELECT a.aih_codigo, a.med_codigo_solicitante, a.aih_cnes_soli, a.usr_codigo, a.aih_dataini, ".
				"a.aih_data_alta, a.aih_principais_sintomas, a.aih_justificativa_internacao, a.aih_principais_resultados, ".
				"a.aih_diag_ini, a.aih_cid_cod_princ, a.aih_cid_cod_secun, a.aih_cid_cod_terc, a.aih_desc_proc_soli, ".
				"a.aih_clinica, a.aih_ci, a.aih_tipo_doc_proc_soli, a.aih_n_doc_prof_solicitante, a.med_solicitante_proc, ".
				"to_char(a.aih_data_solicitacao, 'dd/mm/YYYY') as aih_data_solicitacao, a.aih_vinculo_previdencia, ".
				"a.med_autorizador, a.aih_tipo_doc_autorizacao, ".
				"a.aih_n_doc_prof_autorizador, to_char(a.aih_data_autorizacao, 'dd/mm/YYYY') as aih_data_autorizacao, ".
				"a.aih_numero_aih, a.aih_segunda_via, a.pac_aih_codigo, ".
				"a.usu_codigo, a.aih_tipo_acidente, a.aih_cnpj_seguradora, a.aih_n_bilhete, a.aih_serie, a.aih_cnpj_da_empresa, ".
				"a.aih_cnae_da_empresa, a.aih_mes_compet, a.aih_ano_compet, a.aih_dt_cadastro, a.aih_alteradopor, ".
				"a.aih_observacao, m.med_nome, uS.usu_nome, uS.usu_rg, uS.usu_cpf, uS.usu_prontuario, ".
				"to_char(uS.usu_datanasc, 'dd/mm/YYYY') as usu_datanasc, uS.usu_sexo, uS.usu_mae, uS.usu_fone, uS.usu_end_rua, ".
				"uS.usu_end_nr, uS.usu_end_bairro, uS.muni_cd_cod_ibge_resid, uS.usu_end_cep, cD.cid_nome, cD.uf_sigla, ".
				"cI.cd10_descricao as cid1, cI2.cd10_descricao as cid2, cI3.cd10_descricao as cid3, pR.proc_nome, pR.proc_classificacao_sus, mED.usr_nome as medico_solicitante, ".
				"mEA.usr_nome as medico_autorizador, ".
				"to_char(a.aih_dataini, 'dd/mm/YYYY')as aih_dataini, ".
				"to_char(a.aih_data_alta, 'dd/mm/YYYY')as aih_data_alta , a.aih_prontuario_hospital ".
				"FROM aih AS a ".
				"LEFT JOIN medico AS m ON m.med_codigo = a.med_codigo_solicitante ".
				"LEFT JOIN usuarios AS mED ON mED.usr_codigo = a.med_solicitante_proc ".
				"LEFT JOIN usuarios AS mEA ON mEA.usr_codigo = a.med_autorizador ".
				"LEFT JOIN usuario AS uS ON uS.usu_codigo = a.usu_codigo ".
				"LEFT JOIN cidade AS cD ON cD.cid_codigo_ibge = uS.muni_cd_cod_ibge_resid ".
				"LEFT JOIN cid10 AS cI ON cI.cd10_codigo = a.aih_cid_cod_princ ".
				"LEFT JOIN cid10 AS cI2 ON cI2.cd10_codigo = a.aih_cid_cod_secun ".
				"LEFT JOIN cid10 AS cI3 ON cI3.cd10_codigo = a.aih_cid_cod_terc ".
				"LEFT JOIN procedimento AS pR ON pR.proc_codigo = a.aih_desc_proc_soli ".
				"WHERE aih_codigo=$aih_codigo";

		$query = pg_query($stmt) or die(pg_last_error());
		$res = pg_fetch_array($query);

	# -- VALIDANDO E BUSCANDO O NOME DO PACIENTE ( SE CONSTA EM USUARIO OU SE CONSTA EM APAC_PACIENTE	)
		if ($res['usu_codigo'] != '') {
			$c0d = $res['usu_codigo'];
	                $stmt_2 = "(SELECT u.usu_codigo, u.usu_nome, u.usu_mae, u.usu_cpf, 'N', u.usu_rg, u.usu_cpf, 
			                   u.usu_prontuario, ".
             			           "to_char (u.usu_datanasc, 'dd/mm/YYYY') as usu_datanasc, u.usu_sexo ".
			           "FROM usuario AS u where u.usu_codigo = $c0d ) ";
		}elseif($res['pac_aih_codigo'] != ''){
			$c0d = $res['pac_aih_codigo'];
			$stmt_2 =  "(SELECT p.pac_codigo, p.pac_nome, p.pac_mae_responsavel_nome, p.pac_mae_responsavel_cpf, 'S', p.pac_rg, p.pac_cpf, p.pac_prontuario, ".
			  "to_char(p.pac_dt_nasc, 'dd/mm/YYYY') as pac_dt_nasc, p.pac_sexo ".
			  "FROM aih_paciente AS p where p.pac_codigo = $c0d )";  
		}else{
			$c0d = '0';
			$stmt_2="";
		}
	$query_np = pg_query($stmt_2) or die(pg_last_error());
	$n_p = pg_fetch_array($query_np);
	

//---------------------------------------------------------------------------------------------------------------------------------


echo
   "<form name='aih_form' method='post' action='$PHP_SELF?acao=edit' onSubmit='return $func'>
   	<input type='hidden' name='aih_codigo' value='$_REQUEST[aih_codigo]' />

	<h3>Laudo para Internação de Autorização de Internação Hospitalar ( AIH )</h3>
	<fieldset>
		<legend>Identificação do Estabelecimento de Saúde</legend>
		<table width='100%' align='center' cellspacing='2' cellpadding='0' border='0'>
		<tr>
			<td width='25%'>Nome do Estabelecimento Solicitante/Executante:</td>
			<td whidth='45%'>
		<input type='text' name='med_codigo_solicitante' id='med_codigo_solicitante' class='box' size='60'
		value='"; print $res['med_nome']; echo "'>
		<input type='hidden' name='med_codigo_solicitante_h' id='med_codigo_solicitante_h'
		value='"; print $res['med_codigo_solicitante']; echo "'>
			</td>
			<td whidth='30%'>CNES:<input type='text' name='aih_cnes_soli' id='aih_cnes_soli' class='box' size='20' maxlength='7' onKeyPress='apenasNumero(this)'
			onKeyUp='apenasNumero(this)' value='"; print $res['aih_cnes_soli']; echo "' /></td>
		</tr>
		<tr>
			<td whidth='25%'>Compet&ecirc;ncia</td>
			<td colspan='2'>
				<select id='aih_mes_compet' name='aih_mes_compet' class='box' onchange='document.getElementById('ano_comp').select();'> ";
					//print meses_select( date('m') );
					print meses_select( $res['aih_mes_compet'] );
	   echo"    	</select>
				/
				<input type='text' name='aih_ano_compet' id='aih_ano_compet' class='box' size='4' maxlength='4' value='"; print $res['aih_ano_compet']; echo "' />
			</td>
		</tr>
		</table>
	</fieldset>


	<fieldset>
		<legend>Identificação do Paciente</legend>
		<table width='100%' align='center' cellspacing='2' cellpadding='0' border='0'>
			<tr>
				<td width='25%'>Nome do Paciente:</td>
				<td width='75%'>
				<input type='text' name='aih_paciente_nome' id='aih_paciente_nome' class='box' size='60' value='"; print $n_p[1]; echo "' />
				<input type='hidden' name='usu_codigo' id='usu_codigo' value='"; print $n_p[0]; echo "' />
				<input type='hidden' name='pac_aih' id='pac_aih' value='"; echo $n_p[4]; echo "' />
				</td>
			</tr>
			<tr>
				<td width='25%'>RG:</td>
				<td width='75%'><input type='text' name='aih_paciente_rg' id='usu_rg' class='box' size='30' maxlength='11' onKeyPress='apenasNumero(this)' onKeyUp='apenasNumero(this)' value='"; print $n_p[5]; echo "' /></td>
			</tr>
			<tr>
				<td>CPF: </td>
				<td width='75%'><input type='text' name='aih_paciente_cpf' id='usu_cpf' class='box' size='30' maxlength='14' onKeyPress='apenasNumero(this)' onKeyUp='apenasNumero(this)' value='"; print $n_p[6]; echo "' />

				<span id='cpf_result'>&nbsp;</span></td>
			</tr>
			<tr>
				<td>N&deg; do Prontuario: </td>
				<td width='75%'>
				<input type='text' name='aih_prontuario' id='usu_prontuario'  class='box' size='30' maxlength='10' onchange='atualiza_prontuario(this.value)'  disabled='disabled' value='"; print $n_p[7]; echo "' />
				</td>
			</tr>
			<tr>
				<td width='25%'>Data da Interna&ccedil;&atilde;o: </td>
				<td width='75%'><input type='text' name='aih_dataini' id='aih_dataini' class='box' size='30' maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\" onchange=\"buscaferiado(this.value);\" value='"; print $res['aih_dataini']; echo "' /></td>
			</tr>
			<tr>
				<td width='25%'>Data Alta: </td>
				<td width='75%'><input type='text' name='aih_data_alta' id='aih_data_alta' class='box' size='30' maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\" onchange=\"buscaferiado(this.value); compara_datas();\" value='"; print $res['aih_data_alta']; echo "' /></td>
			</tr>
			<tr>
				<td colspan='2'>
					<a href='javascript:;' id='det_paci_link' onclick='det_paci()'>
					<span id='det_paci_span'>Menos</span> Detalhes
					</a>
				</td>
			</tr>
			<tbody id='det_paci_tbody' style='display:table-row-group;'>
			<tr>
				<td width='25%'>Cart&atilde;o Nacional de Sa&uacute;de - CNS:</td>
				<td width='75%'><input type='text' name='aih_cns' id='usu_cartao_sus' class='box' size='30' maxlength='15' onKeyPress='apenasNumero(this)' onKeyUp='apenasNumero(this)' /></td>
			</tr>
			<tr>
				<td width='25%'>Data de Nascimento: </td>
				<td width='75%'><input type='text' name='aih_data_nasc' id='usu_datanasc' class='box' size='30' maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\" onchange=\"buscaferiado(this.value);\" value='"; print $n_p[8]; echo "'/></td>
			</tr>
			<tr>
				<td width='25%' valign='top'>Sexo: </td>
				<td width='75%'><input type='text' name='aih_sexo' id='usu_sexo' class='box' size='30' maxlength='1' value='"; print $arraySexo[$n_p[9]]; echo"' /></td>
			</tr>
			<tr>
				<td width='25%'>Nome da M&atilde;e ou Respons&aacute;vel:</td>
				<td width='75%'>
				<input type='text' name='aih_mae_responsavel_nome' id='usu_mae' class='box' size='60' value='"; print $n_p[2]; echo "' />
				</td>
			</tr><!--
			<tr>
				<td width='25%'>RG: </td>
				<td width='75%'>
				<input type='text' name='aih_mae_responsavel_rg' id='aih_mae_responsavel_rg' class='box' size='30' maxlength='11' />
				</td>
			</tr>
			<tr>
			<td width='25%'>CPF:</td>
			<td width='75%'><input type='text' name='aih_mae_responsavel_cpf' id='aih_mae_responsavel_cpf' class='box' size='30' maxlength='14' /></td>
			</tr>-->
			<tr>
				<td width='25%'>Telefone de Contato:</td>
				<td width='75%'><input type='text' name='aih_fone' id='usu_fone_recado' class='box' size='30' maxlength='13' onKeyPress='soNumeroTelefone(this,23)' onKeyUp='soNumeroTelefone(this,23)' value='"; print $res['usu_fone']; echo "'/></td>
			</tr>
			<tr>
				<td width='25%'>Endere&ccedil;o:</td>
				<td width='75%'>
				<input type='text' name='aih_endereco' id='rua_nome' class='box' size='60' value='"; print $res['usu_end_rua']; echo "' /></td>
			</tr>
			<tr>
				<td width='25%'>N&uacute;mero: </td>
				<td width='75%'>
				<input type='text' name='aih_numero' id='dom_numero' class='box' size='30' maxlength='10' value='"; print $res['usu_end_nr']; echo "' /></td>
			</tr>
			<tr>
				<td width='25%'>Bairro: </td>
				<td width='75%'>
				<input type='text' name='aih_bairro' id='rua_bairro' class='box' size='30' maxlength='60'  value='"; print $res['usu_end_bairro']; echo "' /></td>
			</tr>
			<tr>
				<td width='25%'>Munic&iacute;pio de Resid&ecirc;ncia:</td>
				<td width='75%'>
				<input type='text' name='aih_cidade' id='cid_nome' class='box' size='30' maxlength='60' value='"; print $res['cid_nome']; echo "' />
				</td>
			</tr>
			<tr>
				<td width='25%'>C&oacute;d. IBGE Munic&iacute;pio: </td>
				<td width='75%'><input type='text' id='cid_codigo_ibge' name='aih_ibge_codigo' class='box' size='30' value='"; print $res['muni_cd_cod_ibge_resid']; echo "' /></td>
			</tr>
			<tr>
				<td width='25%'>UF: </td>
				<td width='75%'><input type='text' name='aih_uf' id='uf_sigla' class='box' size='30' maxlength='2' value='"; print $res['uf_sigla']; echo "' /></td>
			</tr>
			<tr>
				<td width='25%'>CEP: </td>
				<td width='75%'><input type='text' name='aih_cep' id='rua_cep' class='box' size='30' maxlength='9' value='"; print $res['usu_end_cep']; echo "' /></td>
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
				<input type='text' name='aih_prontuario_hospital' id='aih_prontuario_hospital'  class='box' size='30' maxlength='10'  value='"; print $res['aih_prontuario_hospital']; echo "' />
				</td>
			</tr>
			<tr>
				<td width='25%' valign='top'>Principais Sinais e Sintomas Cl&iacute;nicos:</td>
				<td width='75%'>
				<textarea name='aih_principais_sintomas' id='aih_principais_sintomas' cols='57' rows='3' class='box'>"; 
	   	print $res['aih_principaid_sintomas']; echo "</textarea>
				</td>
			</tr>
			<tr>
				<td width='25%' valign='top'>Condi&ccedil;&otilde;es que Justificam a Interna&ccedil;&atilde;o:</td>
				<td width='75%'><textarea name='aih_justificativa_internacao' cols='57' rows='3' class='box'>"; 
	   	print $res['aih_justificativa_internacao']; echo "</textarea></td>
			</tr>
			<tr>
				<td width='25%' valign='top'>Principais Resultados de Provas Diagn&oacute;sticas:</td>
				<td width='75%'><textarea name='aih_principais_resultados' cols='57' rows='3' class='box'>"; 
	   	print $res['aih_principais_resultados']; echo "</textarea></td>
			</tr>
			<tr>
				<td width='25%' valign='top'>Diagn&oacute;stico Inicial:</td>
				<td width='75%'><textarea name='aih_diag_ini' id='aih_diag_ini' cols='57' rows='3' class='box' >"; 
	   	print $res['aih_diag_ini']; echo "</textarea></td>
			</tr>
			
			
			<tr>
				<td width='25%'>Cid. 10 Principal:</td>
				<td width='75%'><input type='text' name='aih_cid_cod_princ' id='cid1' size='60' maxlength='150' class='box' value='"; print $res['cid1']; echo "' >
								<input type='hidden' name='aih_cid_cod_princ_h' id='cd10_codigo' value='"; print $res['aih_cid_cod_princ']; echo "'>
				</td>	
			</tr>
			<tr>
				<td width='25%'>Cid. 10 Secundário:</td>
				<td width='75%'><input type='text' name='aih_cid_cod_princ' id='cid2' size='60' maxlength='150' class='box' value='"; print $res['cid2']; echo "' >
								<input type='hidden' name='aih_cid_cod_secun' id='cd10_codigo_2' value='"; print $res['aih_cid_cod_secun']; echo "'>
				</td>	
			</tr>
			<tr>
				<td width='25%'>Cid. 10 Terciário:</td>
				<td width='75%'><input type='text' name='aih_cid_cod_princ' id='cid3' size='60' maxlength='150' class='box' value='"; print $res['cid3']; echo "' >
								<input type='hidden' name='aih_cid_cod_terc' id='cd10_codigo_3' value='"; print $res['aih_cid_cod_terc']; echo "'>
				</td>	
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>Procedimento Solicitado</legend>

		<table width='100%' align='center' cellspacing='2' cellpadding='0' border='0'>
			<tr>
				<td width='25%' valign='top'>Descri&ccedil;&atilde;o do Procedimento:</td>
				<td width='75%'>
				<input type='text' name='aih_desc_proc_soli' id='aih_desc_proc_soli' class='box' size='100' maxlength='255' value='"; print trim($res['proc_nome']); echo "' />
				<input type='hidden' name='aih_desc_proc_soli_h' id='aih_desc_proc_soli_h' value='"; print $res['aih_desc_proc_soli']; echo "' />
				<input type='hidden' name='aih_classificacao_sus' id='aih_classificacao_sus' value='"; print $res['proc_classificacao_sus']; echo "' />
				</td>
			</tr>";
/*
					$sql_busca_clinica = 'SELECT * FROM clinica';
					$query_clinica 	   = pg_query($sql_busca_clinica);
				echo"<tr>
						<td width='25%'>Cl&iacute;nica:</td>
						<td width='75%'>";
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
*/
			$sql_busca_clinica = 'SELECT * FROM clinica order by 3';
			$query_clinica 	   = pg_query($sql_busca_clinica);
				echo"<tr>
						<td width='25%'>Cl&iacute;nica:</td>
						<td width='75%'>";
				echo"
						<select name='aih_clinica' id='aih_clinica' class='box'>
						";
							while( $res_clinica = pg_fetch_array($query_clinica) ){
						echo "
								<option value='$res_clinica[0]'";
								if ($res_clinica['cli_codigo'] == $res['aih_clinica'] ){
									echo "selected";
								}else{
									echo "";
								}
								echo ">"; echo $res_clinica[2]; echo "</option>";
							}
					echo "
						</select>
						  </td>
					</tr>";
/*					$sql_buscaci = 'SELECT ci_codigo, ci_cod FROM ci';
					$query_ci	 = pg_query($sql_buscaci);
				echo"<tr>
						<td width='25%'>C.I.:</td>
						<td width='75%'>";
				echo"
						<select name='aih_ci' id='aih_ci' class='box'>
						";
							while( $res_ci = pg_fetch_array($query_ci) ){
						echo "
								<option value="; echo $res_ci['ci_codigo']; echo" > ";  echo $res_ci['ci_cod']; echo" </option>";
							}
					echo "
						</select>
						  </td>
					</tr>*/

			$sql_buscaci = "select ci_codigo, ci_cod || ' ' || ci_descricao as ci_descricao FROM ci WHERE ci_ativo='S' order by ci_cod";
			//$sql_buscaci = 'SELECT ci_codigo, ci_cod FROM ci';
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
									if ( $res_ci['ci_codigo'] == $res['aih_ci'] ){
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
<!--
			<tr>
				<td width='25%' valign='top'>Tipo de Doc.:</td>
				<td width='75%' valign='middle'>
				<input type='radio' name='aih_tipo_doc_proc_soli' value='cns' id='aih_tipo_cod_prof_soli_cns'
				onchange='busca_doc_ajax_soli()' ";
					if ($res['aih_tipo_doc_proc_soli'] == 'cns' ){
							echo "checked='checked'";
						}else{
							echo "";
					}
				echo " />
				<label for='aih_tipo_cod_prof_soli_cns' /> CNS </label>
					<br />
				<input type='radio' name='aih_tipo_doc_proc_soli' value='cpf' id='aih_tipo_doc_proc_soli_cpf'
				onchange='busca_doc_ajax_soli()' ";
					if ($res['aih_tipo_doc_proc_soli'] == 'cpf' ){
							echo "checked='checked'";
						}else{
							echo "";
					}
				echo " />
				<label for='aih_tipo_doc_proc_soli_cpf' /> CPF </label>
				</td>
			</tr>
-->
			<tr>
				<td width='25%'>Nome do Profissional Solicitante:</td>
				<td width='75%'>
				<input type='text' name='med_solicitante_proc' id='med_solicitante_proc' size='60' maxlength='255' class='box' value='"; print $res['medico_solicitante']; echo "' />
				<input type='hidden' name='med_solicitante_proc_h' id='med_solicitante_proc_h' size='60' maxlength='255' class='box' value='"; print $res['med_solicitante_proc']; echo "' />
				</td>
			</tr>
			<tr>
				<td width='25%'>Núm. Conselho:</td>
				<td width='75%'>
						<input type='text' name='aih_n_doc_prof_solicitante' size='30' maxlength='20'
 					 	class='box' onchange='busca_doc_ajax_soli()' id='aih_n_doc_prof_solicitante'
						onkeypress='apenasNumero(this)' onkeyup='apenasNumero(this)'
						value='"; print $res['aih_n_doc_prof_solicitante']; echo "' />
						<span id='doc_result_soli'>&nbsp;</span>
				</td>
			</tr>
			<tr>
				<td width='25%'>Data da Solicita&ccedil;&atilde;o:</td>
				<td width='75%'><input type='text' name='aih_data_solicitacao' id='aih_data_solicitacao' size='30' maxlength='10' class='box' onKeypress=\"return Ajusta_Data(this, event);\" onchange=\"buscaferiado(this.value);\" value='"; print $res['aih_data_solicitacao']; echo "' /></td>
			</tr>
	</table>
	</fieldset>


	<fieldset>
		<legend>Preencher em caso de causas externas ( Acidentes ou Viol&ecirc;ncias ) </legend>

			<table width='100%' align='center' cellspacing='2' cellpadding='0' border='0'>

			  <tr>
				  <td whidth='25%' valign='top'>Tipo de Acidente: </td>
				  <td whidth='75%'>
					<label>
					<input type='radio' name='aih_tipo_acidente' value='Transito' ";
						if($res['aih_tipo_acidente'] == 'Transito'){
							echo "checked='checked'"; 					
						}else{
							echo "";					
						}
					echo "> Acidente de Tr&acirc;nsito</label><br />
					<label>
					<input type='radio' name='aih_tipo_acidente' value='Tipico' ";			
						if($res['aih_tipo_acidente'] == 'Tipico'){
							echo "checked='checked'"; 					
						}else{
							echo "";					
						}
					echo "> Acidente de Trabalho T&iacute;pico</label><br />
					<label>
					<input type='radio' name='aih_tipo_acidente' value='Trajeto' ";
						if($res['aih_tipo_acidente'] == 'Trajeto'){
							echo "checked='checked'"; 					
						}else{
							echo "";					
						}
					echo "> Acidente de Trabalho Trajeto</label><br />	 
					<label>
					<input type='radio' name='aih_tipo_acidente' value='Outros' ";
						if($res['aih_tipo_acidente'] == 'Outros'){
							echo "checked='checked'"; 					
						}else{
							echo "";					
						}
					echo "> Outros</label>				 
				  </td>
			  </tr>
			  <tr>
				<td whidth='25%' valign='top'>Observações:</td>
				<td whidth='75%'>
				<textarea name='aih_observacao' id='aih_observacao' cols='57' rows='3' class='box'>"; 
					print $res['aih_observacao']; echo "</textarea>
				</td>
			  </tr>

			  <tr>	
				  <td whidth='25%'>CNPJ da Seguradora: </td>
				  <td whidth='75%'>
				  <input type='text' name='aih_cnpj_seguradora' id='aih_cnpj_seguradora' class='box' size='30' 
				  maxlength='17' value='"; print $res['aih_cnpj_seguradora']; echo "' /></td />
			  </tr>
				<tr>
				  <td whidth='25%'>N&ordm; do Bilhete: </td>
				  <td whidth='75%'>
				  <input type='text' name='aih_n_bilhete' id='aih_n_bilhete' class='box' size='30' 
				  maxlength='20' value='"; print $res['aih_n_bilhete']; echo "' /></td>
			  </tr>
				<tr>
				  <td whidth='25%'>S&eacute;rie:</td>
				  <td whidth='75%'>
				  <input type='text' name='aih_serie' id='aih_serie' class='box' size='30' 
				  maxlength='15' value='"; print $res['aih_serie']; echo "' /></td>
			  </tr>
				<tr>
				  <td whidth='25%'>CNPJ da Empresa: </td>
				  <td whidth='75%'>
				  <input type='text' name='aih_cnpj_da_empresa' id='aih_cnpj_da_empresa' class='box' size='30' 
				  maxlength='17' value='"; print $res['aih_cnpj_da_empresa']; echo"' /></td>
			  </tr>
				<tr>
				  <td whidth='25%'>CNAE da Empresa: </td>
				  <td whidth='75%'>
				  <input type='text' name='aih_cnae_da_empresa' id='aih_cnae_da_empresa' class='box' size='30'
				  maxlength='50' value='"; print $res['aih_cnae_da_empresa']; echo "' /></td>
			  </tr>
				<tr>
				  <td whidth='25%'>CBOR:</td>
				  <td whidth='75%'>
				  <input type='text' name='aih_cbor' id='aih_cbor' class='box' size='30' 
				  maxlength='50' value='"; print $res['aih_cbor']; echo "' /></td>
			  </tr>
				<tr>
				<td width='25%' valign='top'>V&iacute;nculo com a Previd&ecirc;ncia:</td>
				  <td whidth='75%'>
				  <label>
				  <input type='radio' name='aih_vinculo_previdencia' value='Empregado'";
						if($res['aih_vinculo_previdencia'] == 'Empregado'){
							echo "checked='checked'"; 					
						}else{
							echo "";					
						}
				  echo " /> Empregado</label><br />
				  <label>
				  <input type='radio' name='aih_vinculo_previdencia' value='Empregador'";
						if($res['aih_vinculo_previdencia'] == 'Empregador'){
							echo "checked='checked'"; 					
						}else{
							echo "";					
						}
				  echo" /> Empregador</label><br />
				  <label>
				  <input type='radio' name='aih_vinculo_previdencia' value='Aut&ocirc;nomo'"; 
						if($res['aih_vinculo_previdencia'] == 'Aut&ocirc;nomo'){
							echo "checked='checked'"; 					
						}else{
							echo "";					
						}
				  echo" /> Aut&ocirc;nomo</label><br />
				  <label>
				  <input type='radio' name='aih_vinculo_previdencia' value='Desempregado'";
						if($res['aih_vinculo_previdencia'] == 'Desempregado'){
							echo "checked='checked'"; 					
						}else{
							echo "";					
						}
				  echo" /> Desempregado</label><br /> 
				  <label>
				  <input type='radio' name='aih_vinculo_previdencia' value='Aposentado'";
						if($res['aih_vinculo_previdencia'] == 'Aposentado'){
							echo "checked='checked'"; 					
						}else{
							echo "";					
						}
				  echo" /> Aposentado</label><br />
			 	  <label>
				  <input type='radio' name='aih_vinculo_previdencia' value='N&atilde;o Segurado'";
						if($res['aih_vinculo_previdencia'] == 'N&atilde;o Segurado'){
							echo "checked='checked'"; 					
						}else{
							echo "";					
						}
				  echo" /> N&atilde;o Segurado</label>
				</td>
				</tr>
			</table>
	</fieldset>

	<fieldset>
		<legend>Autoriza&ccedil;&atilde;o</legend>
		
			<table width='100%' align='center' cellspacing='2' cellpadding='0' border='0'> ";
					
		echo"	<tr>
					<td width='25%'>Nome do Profissional Autorizador:</td>
					<td whidth='75%'>
						<input type='text' name='med_autorizador' id='med_autorizador' size='60' maxlength='255' class='box' value='"; print $res['medico_autorizador']; echo "' />
						<input type='hidden' name='med_autorizador_h' id='med_autorizador_h' size='60' maxlength='255' class='box' value='"; print $res['med_autorizador']; echo "' />
					</td>
				</tr>

				<tr>
					<td width='25%'>
                        <strong>N&deg; da Autoriza&ccedil;&atilde;o de Interna&ccedil;&atilde;o Hospitalar:</strong>
                    </td>
					<td whidth='75%'>";
/*
			<input type='hidden' name='aih_num_h' value='"; print $aih_num_arr[0]; echo "' />
			<input type='text' name='aih_numero_aih_h' value='"; print $aih_num_arr[0]; echo"' readonly class='box' size='30' />
*/
				echo "<!--	<input type='hidden' name='aih_num_h' value='' /> -->
						<input type='hidden' name='aih_numero_volta' id='aih_numero_volta' value='"; print $res['aih_numero_aih']; echo "' />
						<input type='text' name='aih_numero_aih_h' id='aih_numero_aih_h' value='"; print $res['aih_numero_aih']; echo "' class='box' size='30' readonly />
					</td>
				</tr>

				<tr>
					<td width='25%'>Núm. Conselho:</td>
					<td whidth='75%'>
						<input type='text' name='aih_n_doc_prof_autorizador' size='30' maxlength='20'
							class='box' onchange='busca_doc_ajax()' id='aih_n_doc_prof_autorizador'
							onkeypress='apenasNumero(this)' onkeyup='apenasNumero(this)'
							value='"; print $res['aih_n_doc_prof_autorizador']; echo "' />
							<span id='doc_result'>&nbsp;</span>
					</td>
				</tr>

				<tr>
					<td width='25%'>Data da Autoriza&ccedil;&atilde;o:</td>
					<td whidth='75%'>
					<input type='text' name='aih_data_autorizacao' id='aih_data_autorizacao' size='30'
					maxlength='10' class='box' onKeypress=\"return Ajusta_Data(this, event);\"
					onchange=\"buscaferiado(this.value);\" value='"; print $res['aih_data_autorizacao']; echo "'/></td>
				</tr>

				<tr>
					<td width='25%'>&nbsp;</td>
					<td width='75%'><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/salvar_on.jpg /></td>
				</tr>
			</table>
	</fieldset>

	</form>";


}

?>
<iframe id='frame_impressao' width='0' height='0' frameborder='0'>
</iframe>
</body>
</html>
