<?php
/**
@brief  Inclusao principal para montagem do sistema
*/
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";

include_once $_SESSION[root].$_SESSION[modulo]."json.inc.php";

cabecario( $hotkey = true );

//------------------------------------------------------------------>
/*
echo "
	<fieldset>
	<legend>Op��es</legend>";
		if(SelPerm($id_login,'agendar_exame.php') != "0")
		{
			echo ChmodBtn($id_login,'fazeragendamento','agendar_exame.php?');
		} else {
			echo "<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/fazeragendamento_off.jpg' />";
		}
		if(SelPerm($id_login,'manutencaoagendaexame.php') != "0")
		{
			echo ChmodBtn($id_login,'manutencao_agenda_exames','manutencaoagendaexame.php?');
		} else {
			echo "<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/manutencao_agenda_exames_off.jpg' />";
		}
		if(SelPerm($id_login,'manutencao_exame_mensal.php') != "0")
		{
			echo ChmodBtn($id_login,'manutencao_exames','manutencao_exame_mensal.php?');
		} else {
			echo "<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/manutencao_exames_off.jpg' />";
		}
		if(SelPerm($id_login,'procedimento.php') != "0")
		{
			echo ChmodBtn($id_login,'procedimento','procedimento.php?');
		} else {
			echo "<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/procedimento_off.jpg' />";
		}
		if(SelPerm($id_login,'laboratorio.php') != "0")
		{
			echo ChmodBtn($id_login,'laboratorio','laboratorio.php?');
		} else {
			echo "<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/laboratorio_off.jpg' />";
		}
	echo "</fieldset>";
*/

?>
<!-- -->
<script type="text/javascript" src="ajax_motor.js"></script>
<script type="text/javascript" src="funcoes.js"></script>

<script type='text/javascript'>

function hotkey(eventname)
{
	if( eventname.keyCode == 27 )
		return esconde_janela( 'janela_lab' );

	if( eventname.keyCode == 114 )
		return redir_form_add();

}
var PAGINA = 'procedimento_laboratorio_popup.php?id_login=<?=$id_login;?>&proc_codigo=<?=$proc_codigo?>';

function cad_laboratorio( txt )
{
	document.getElementById('janela_lab_conteudo').innerHTML = txt;
}


function busca_lab()
{
	var pc = document.getElementById('palavra_chave').value;
	var endereco = PAGINA+'&acao=busca&palavra_chave='+pc;
	ajax_tudo(endereco,busca_lab_cont);
	return false;
}

function busca_lab_cont( txt )
{
	document.getElementById('janela_lab_conteudo').innerHTML = txt;
}

function apagar( med_codigo, proc_nome )
{
	var c = confirm("Remover o Procedimento "+ proc_nome +" ?");
	if( c )
	{
		var endereco = PAGINA+'&acao=del&med_codigo='+med_codigo;
		ajax_tudo(endereco, apagar2);
	}
}

function apagar2( txt )
{
	busca_lab_cont( txt );
	setTimeout('init()', 3*1000);
}

function init()
{
	ajax_tudo( PAGINA, cad_laboratorio )
}

function form_laboratorio( med_codigo )
{
	//$PHP_SELF?id_login=$id_login&med_codigo=$med_codigo&popup=$popup

	var acao 	= document.getElementById('acao').value;
 	var crm 	= document.getElementById('med_crm').value;
	var uf_crm 	= document.getElementById('uf_codigo_crm').value;
 	var nome 	= document.getElementById('med_nome').value;
	var email 	= document.getElementById('med_email').value;
	var end 	= document.getElementById('med_endereco').value;
	var cid 	= document.getElementById('cid_codigo').value;
	var cpf		= document.getElementById('med_cpf').value;
	var rg 		= document.getElementById('med_rg').value;

		if( ! valida('med_nome','Nome') )
	{
		return false;
	}

	var endereco = PAGINA+'&acao='+acao+'&med_crm='+crm+'&uf_codigo_crm='+uf_crm+'&med_nome='+nome+'&med_email='+email+'&med_endereco='+end+'&cid_codigo='+cid+'&med_cpf='+cpf+'&med_rg='+rg+'&med_codigo='+med_codigo;

	ajax_tudo( endereco, form_laboratorio2 );

	return false;
}

function form_laboratorio2( txt )
{
	busca_lab_cont( txt );
	setTimeout('init()', 3*1000);
}


function redir_form_add()
{
	var endereco = PAGINA+'&acao=form_add';
	ajax_tudo( endereco, cad_laboratorio );
	return false;
}

function redir_form_edit( med_codigo )
{
	var endereco = PAGINA+'&acao=form_edit&med_codigo='+med_codigo;
	ajax_tudo( endereco, cad_laboratorio );
	return false;
}

/** Quando adicionado um laboratorio a este procedimento, ele adiciona nesta pilha  */
var Laboratorios = [];

function etapa2( med_codigo, med_nome )
{
	var endereco = PAGINA+'&acao=etapa2&med_codigo='+med_codigo+'&med_nome='+med_nome;
	ajax_tudo( endereco, cad_laboratorio );
}

function form_etapa2( med_codigo, med_nome )
{

	for( i=0; i < Laboratorios.length; i++ )
		if( Laboratorios[i].med_codigo == med_codigo && ! Laboratorios[i].removido )
		{
			alert('Laboratorio '+med_nome+' ja inserido !');
			return false;
		}

	var tipo = $F('proc_tipo');

	Laboratorios.push(
		{
			'med_codigo'	: med_codigo,
			'med_nome' 	: med_nome,
//			'proc_tipo' 	: tipo,
			'removido'		: false
		}
	);

	esconde_janela('janela_lab');
	atualiza_select();
	return false;
}

function atualiza_select()
{
	var Sel =$('med_lista');
	Sel.length = 0;

	for( i=0; i < Laboratorios.length; i++ )
	{
		if( Laboratorios[i].removido ) continue;
		Sel.length++;
		Sel.options[ Sel.length-1 ].text 		= Laboratorios[i].med_nome;
		Sel.options[ Sel.length-1 ].value 	= Laboratorios[i].med_codigo;
	}
}

function sel_todos()
{
	var Sel =$('med_lista');
	for( i=0; i < Sel.length; i++ )
		Sel.options[i].selected = true;
}

function sel_remove()
{
	var Sel =$('med_lista'), i =0;

	while( i < Sel.length )
	{
		if( Sel.options[i].selected )
		{
			remove_lab( Sel.options[i].value );
			Sel.remove( i );
			i = 0;
		}
		else
			i++;
	}

}

function remove_lab( med_codigo )
{
	for( var i=0; i < Laboratorios.length; i++ )
	{
		if( Laboratorios[i].med_codigo == med_codigo )
		{
			Laboratorios[i].removido = true;
			return false;
		}
	}
}

function valida_proc()
{
	if( Laboratorios.length == 0 )
		if( ! confirm('Nenhum Laboratorio associado. Deseja continuar ?') )
			return false;

	var L = $('med_lista_hid'), Objetos = [], Result = '';
	for( i=0; i < Laboratorios.length; i++ )
	{

		if( Laboratorios[i].removido ) continue;
		Objetos.push(
			"{ 'med_codigo' : "+ Laboratorios[i].med_codigo + "," +
				" 'proc_tipo' : '" + Laboratorios[i].proc_tipo + "'}" );
	}


	Result = "[" + Objetos.join(',') + "]";
	L.value = Result;

	return true;
}
</script>
<!-- -->
<?php

/**
 * @brief Secao Vazia, mostrando registros e botoes
*/
// ------------------------------------------------------------------>

if( empty($acao) || $acao == 'busca' )
{

	/**
	 * @brief Formul�rio de Busca
	*/
	// ------------------------------------------------------------------>
	echo "
	<fieldset>
	<legend>Op��es do Procedimento</legend>
	<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	<tr>
		<td width=95><a href=procedimento.php?id_login=$id_login&acao=form_add><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.png border=0></a>";
		//ChmodBtn($id_login,'adicionar',"procedimento.php?id_login=$id_login&acao=form_add")
		echo "</td>";
		if(chmodbtn($id_login, "procurar_if", "procedimento.php"))
		{
			echo "<form action='procedimento.php?id_login=$id_login&acao=busca' method='post'>";
		}
		echo "<input type='hidden' name='acao' value='busca'>
		<td width=30>Buscar:</td>
		<td width=120>
			<input type=text name=palavra_chave class=box
				onChange=\"this.value=this.value.toUpperCase();\">
		</td>
        <td>
            <select name='campo' class='box'>
                <option value='proc_nome'>Nome</option>
                <option value='proc_classificacao_sus'>C&oacute;digo SUS</option>
                <option value='proc_ativo'>Status</option>
            </select>
        </td>
		<td>".ChmodBtn($id_login,'procurar','procedimento.php')."</td>
		</form>
	</tr>
	</table>
	</fieldset>
	</form>";

	//-> Subistituindo o + por porcentagem na busca
	$str = str_replace("+","%%",$palavra_chave);

	// arrumando sql
	$resp = '';
	$max 	= 15;

        $campo  = empty($campo) ? 'proc_nome' : $campo;
	if ($campo == 'proc_nome') $where = "WHERE proc_nome like '%$palavra_chave%'";
	if ($campo == 'proc_classificacao_sus') $where = "WHERE proc_classificacao_sus  = '$palavra_chave'";
	if ($campo == 'proc_ativo') $where = "WHERE proc_ativo  = '$palavra_chave'";
	$sql_f 	= empty($acao) ? 'ORDER BY proc_codigo DESC LIMIT '.$max : 'ORDER BY proc_nome';
        $stmt = 	"SELECT proc_codigo, proc_nome, proc_valor, proc_classificacao_sus, proc_ativo
			FROM procedimento AS p
			$where $sql_f";

	// se NAO for busca, limitar

#if(chmodbtn($id_login, "listar_if", "procedimento.php"))
#{
#	$qry = db_query($stmt);
#}
	$qry = db_query($stmt);
	$num = pg_num_rows($qry);

	if( $acao == 'busca' && ! empty($palavra_chave) )
	{
		if( $num == 0 ) 	{ $resp = "Nenhum Registro encontrado com \"$palavra_chave\""; }
		elseif( $num == 1 ) { $resp = "Encontrado <b>$num</b> Registro com \"$palavra_chave\""; }
		elseif( $num > 1) 	{ $resp = "Encontrados <b>$num</b> Registros com \"$palavra_chave\""; }
	} else
		$resp = "Listando os $max �ltimos Procedimentos Cadastrados";

	/**
	 * @brief Listando / Buscando
	*/
	// ------------------------------------------------------------------>
  	echo "
	<fieldset>
	<legend>$resp</legend>
		<table width=100% align=center cellspacing=2 cellpadding=4 border=0 class='lista'>
		<tr bgcolor='#ffffff'>
		<th width='50'>C&oacute;digo</th>
		<th>Procedimento</th>
		<th width='115'>Classifica&ccedil;&atilde;o SUS</th>
		<th width='80'>Valor (R$)</th>
		<th width='160'>&nbsp;</th>
	</tr>
	"; // se estiver aberto como um popup, ele vai deixar 'selecionar' um novo !

	while($row=pg_fetch_array($qry))
	{
       echo "
       	  <tr>
	       	<td align='center'>$row[proc_codigo]</td>
	       	<td>$row[proc_nome]</td>
			<td>$row[proc_classificacao_sus]</td>
			<td align='center'>".number_format($row['proc_valor'],2)."</td>
	        <td class='c'>";
echo "<a href=procedimento.php?id_login=$id_login&acao=form_edit&proc_codigo=$row[proc_codigo]><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.png border=0></a>";
echo "<a href=procedimento.php?id_login=$id_login&acao=del&proc_codigo=$row[proc_codigo]><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.png border=0></a>";

#echo ChmodBtn($id_login,'editar',"procedimento.php?id_login=$id_login&acao=form_edit&proc_codigo=$row[proc_codigo]");
#echo ChmodBtn($id_login,'apagar',"procedimento.php?id_login=$id_login&acao=del&proc_codigo=$row[proc_codigo]");
/*<a href=$PHP_SELF?id_login=$id_login&acao=form_edit&proc_codigo=$row[proc_codigo]>
	       			<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg' border=0></a>

	       		<a href='$PHP_SELF?id_login=$id_login&acao=del&proc_codigo=$row[proc_codigo]'
	       			onclick=\"return confirm('Remover o �tem \'$row[proc_nome]\' ?')\">
	       			<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg' border='0'></a>*/
	       	echo "</td>
	     </tr>";
     }
	echo "
	</table>
	</fieldset>";
}
//------------------------------------------------------------------>
/**
 @brief Formulario de Adicao/Edicao de Conteudo
*/
//------------------------------------------------------------------>
elseif($_REQUEST['acao']=="form_add" || $_REQUEST['acao']=="form_edit")
{
	// -> Formulario propriamente dito
	// quando a acao NAO FOR form_edit, o row sera vazio
	if( $acao != 'form_edit' )
		$row = array();
	else
	{
		$stmt = "SELECT *
				   FROM procedimento
				  WHERE proc_codigo = $proc_codigo";
		$row = db_getRow($stmt);
	}

	if( $acao == 'form_add' )
		$acao_form = 'add';
	elseif( $acao == 'form_edit' )
		$acao_form = 'edit';

	echo
		monta_janela('janela_lab','Laborat�rio').
	"
	<form method='post' action='procedimento.php?id_login=$id_login&proc_codigo=$proc_codigo'
		onsubmit=\"return valida_proc()\">
	<input type='hidden' name='acao' value='$acao_form'>
	<fieldset>
	<legend>Cadastro de Procedimento</legend>
	<p>Os campos em <span class='destaque'>destaque</span> s�o obrigat�rio !</p>
	<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	<tr>
		<td width='180' class='destaque'>Nome</td>
		<td><input type='text' id='proc_nome' name='proc_nome' class='box' size='50'
			onchange='this.value=this.value.toUpperCase();' value='$row[proc_nome]'></td>
	</tr>

	<tr>
		<td width='180' class='destaque'>Beneficio Concedido</td>
			<td>
				<input type='radio' checked value='t' id='beneficio_concedido' name='beneficio_concedido' class='box' size='50'>Sim
				<input type='radio' value='f' id='beneficio_concedido' name='beneficio_concedido' class='box' size='50'>Nao
			</td>
	</tr>

	<tr>
		<td style='vertical-align:top'>
			<span class='destaque'>Laborat&oacute;rio(s)</span><br />

			<br />

			[ <a href='javascript:void(0);' onclick='mostra_janela(\"janela_lab\"),init();'>Procurar
				<!--<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg' alt='Localizar' style='border:0;vertical-align:middle;'/>--></a> ]

			<br />

			[ <a href='javascript:void(0);' onclick='sel_remove()'>Del. Selecionados
				<!--<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagarlinha.jpg' alt='Remover' style='border:0;vertical-align:middle;'/>--></a> ]

			<br />

			[ <a href='javascript:void(0);' onclick='sel_todos()'>Selecionar Todos</a> ]

		</td>
		<td>
		<input type='hidden' id='med_lista_hid' name='med_lista_hid' />
		<select name='med_lista' id='med_lista' class='box' multiple='multiple' size='8' style='width:330px;'>
		</select>
		</td>
	</tr>

	<tr>
		<td><label for=\"proc_classificacao_amb\">Classifica&ccedil;&atilde;o Amb.</label></td>
		<td><input type=\"text\" name=\"proc_classificacao_amb\" id=\"proc_classificacao_amb\" size=\"15\"
			 maxlength=\"15\" class=\"box\" value=\"$row[proc_classificacao_amb]\" />
		</td>
	</tr>
	<tr>
		<td><label for=\"proc_classificacao_sus\">Classifica&ccedil;&atilde;o SUS</label></td>
		<td><input type=\"text\" name=\"proc_classificacao_sus\" id=\"proc_classificacao_sus\" size=\"15\"
			 maxlength=\"15\" class=\"box\" value=\"$row[proc_classificacao_sus]\" />
		</td>
	</tr>
	<tr>
		<td>Exame</td>
		<td><select name='proc_exame' class='box'>
				<option value='S'".( $row['proc_exame']=='S' ? ' selected' : '' ).">SIM</option>
				<option value='N'".( $row['proc_exame']=='N' ? ' selected' : '' ).">N�O</option>
			</select>
		</td>
	</tr>
	<tr>
		<td><label for=\"proc_valor\">Valor (R$)</label></td>
		<td><input type=\"text\" name=\"proc_valor\" id=\"proc_valor\" size=\"7\"
			 class=\"box\" value=\"$row[proc_valor]\" />
		</td>
	</tr>
	<tr>
		<td><label for=\"proc_valor_custo\">Valor Custo (R$)</label></td>
		<td><input type=\"text\" name=\"proc_valor_custo\" id=\"proc_valor_custo\" size=\"7\"
			 class=\"box\" value=\"$row[proc_valor_custo]\" />
		</td>
	</tr>
	<tr>
		<td><label for=\"proc_intervalo_min\">Intervalo m&iacute;nimo</label></td>
		<td><input type=\"text\" name=\"proc_intervalo_min\" id=\"proc_intervalo_min\" size=\"3\"
			 class=\"box\" value=\"$row[proc_intervalo_min]\" />
		</td>
	</tr>
	<tr>
		<td><label for=\"proc_val11\">Valor SH (R$)</label></td>
		<td><input type=\"text\" name=\"proc_vlsh\" id=\"proc_vlsh\" size=\"7\"
			 class=\"box\" value=\"$row[proc_vlsh]\" />
		</td>
	</tr>
	<tr>
		<td><label for=\"proc_val12\">Valor SP (R$)</label></td>
		<td><input type=\"text\" name=\"proc_vlsp\" id=\"proc_vlsp\" size=\"7\"
			 class=\"box\" value=\"$row[proc_vlsp]\" />
		</td>
	</tr>
	<tr>
		<td><label for=\"proc_val13\">Valor SADT (R$)</label></td>
		<td><input type=\"text\" name=\"proc_vlsa\" id=\"proc_vlsa\" size=\"7\"
			 class=\"box\" value=\"$row[proc_vlsa]\" />
		</td>
	</tr>
	<tr>
		<td><label for=\"proc_tempo\">Tempo de Perman&ecirc;ncia</label></td>
		<td><input type=\"text\" name=\"proc_tempo\" id=\"proc_tempo\" size=\"4\"
			 class=\"box\" value=\"$row[proc_tempo]\" />
		</td>
	</tr>
	<tr>
		<td><label for=\"proc_ptosato\">Pontos dos atos para o cirurgi&atilde;o</label></td>
		<td><input type=\"text\" name=\"proc_ptosato\" id=\"proc_ptosato\" size=\"3\"
			 class=\"box\" value=\"$row[proc_ptosato]\" />
		</td>
	</tr>
	<tr>
		<td><label for=\"proc_ptosanest\">Ponto do anestesista</label></td>
		<td><input type=\"text\" name=\"proc_ptosanest\" id=\"proc_ptosanest\" size=\"3\"
			 class=\"box\" value=\"$row[proc_ptosanest]\" />
		</td>
	</tr>
	<tr>
		<td><label for=\"proc_versao\">Vers&atilde;o</label></td>
		<td><input type=\"text\" name=\"proc_versao\" id=\"proc_versao\" size=\"3\"
			 class=\"box\" value=\"$row[proc_versao]\" />
		</td>
	</tr>
	<tr>
		<td><label for=\"proc_result\">Resultado</label></td>
		<td><input type=\"text\" name=\"proc_result\" id=\"proc_result\" size=\"30\"
			 maxlength=\"255\" class=\"box\" value=\"$row[proc_result]\" />
		</td>
	</tr>
	<tr>
		<td><label for=\"proc_adperm\">Admiss&atilde;o de perman&ecirc;ncia a maior</label></td>
		<td><select name='proc_adperm' class='box'>
				<option value='1'".( $row['proc_adperm']=='1' ? ' selected' : '' ).">Admite</option>
				<option value='2'".( $row['proc_adperm']=='2' ? ' selected' : '' ).">N&atilde;o Admite</option>
			</select>
		</td>
	</tr>
	<tr>
		<td><label for=\"proc_grupo\">Grupo</label></td>
		<td><input type=\"text\" name=\"proc_grupo\" id=\"proc_grupo\" size=\"7\"
			 class=\"box\" value=\"$row[proc_grupo]\" />
		</td>
	</tr>
		<tr>
		<td>Sexo</td>
		<td><select name='proc_sexo_novo' class='box'>
				<!--<option value='5'".( $row['proc_sexo']=='5' ? ' selected' : '' ).">Ambos</option
				<option value='1'".( $row['proc_sexo']=='1' ? ' selected' : '' ).">Masculino</option>
				<option value='3'".( $row['proc_sexo']=='3' ? ' selected' : '' ).">Feminino</option>-->
				<option value='I'".( $row['proc_sexo_novo']=='I' ? ' selected' : '' ).">Ambos</option
				<option value='M'".( $row['proc_sexo_novo']=='M' ? ' selected' : '' ).">Masculino</option>
				<option value='F'".( $row['proc_sexo_novo']=='F' ? ' selected' : '' ).">Feminino</option>
				<option value='N'".( $row['proc_sexo_novo']=='N' ? ' selected' : '' ).">N&atilde;o Informado</option>
			</select>
		</td>
	</tr>
	<tr>
		<td><label for=\"proc_idade_minima\">Idade m&iacute;nima</label></td>
		<td><input type=\"text\" name=\"proc_idade_minima\" id=\"proc_idade_minima\" size=\"3\"
			 class=\"box\" value=\"$row[proc_idade_minima]\" />
		</td>
	</tr>
	<tr>
		<td><label for=\"proc_idade_maxima\">Idade m&aacute;xima</label></td>
		<td><input type=\"text\" name=\"proc_idade_maxima\" id=\"proc_idade_maxima\" size=\"3\"
			 class=\"box\" value=\"$row[proc_idade_maxima]\" />
		</td>
	</tr>
	<tr>
		<td valign='top'>SIPAC</td>
		<td>
		<label><input type='radio' name='proc_sipac' class='box' value='S'
		".( $row['proc_sipac'] == 'S' || empty ($row['proc_sipac']) ? 'checked' : '')." > Sim </label>
		<label><input type='radio' name='proc_sipac' class='box' value='N'
		".( $row['proc_sipac'] == 'N' || empty ($row['proc_sipac']) ? 'checked' : '')." > N&atilde;o </label>
		</td>
	</tr>
        <tr>
		<td>Status</td>
		<td><select name='proc_ativo' class='box'>
				<option value='A'".( $row['proc_ativo']=='A' ? ' selected' : '' ).">Ativo</option
				<option value='I'".( $row['proc_ativo']=='I' ? ' selected' : '' ).">Inativo</option>
			</select>
		</td>
	</tr>

	<tr>
		<td><label for=\"proc_instrucoes\">Instru&ccedil;&otilde;es Procedimento</label></td>
		<td><textarea name=\"proc_instrucoes\" id=\"proc_instrucoes\" cols=80 rows=2
			 class=\"box\"  >$row[proc_instrucoes]</textarea>
		</td>
	</tr>

	<tr>
		<td>&nbsp;</td>
		<td><input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/".
		( $acao == 'form_add' ? 'adicionar_on.jpg' : 'editar_on.jpg').
		"'></td>
	</tr>
	</table>
	</fieldset>
	</form>";

	if( $acao_form == 'edit' )
	{
		// o JS vem depois do form
		print '
		<script type="text/javascript">
		window.onload = function() {
			var Lista = $("med_lista");
		';

		//FROM laboratorio_procedimento_temp AS lp
		$stmt_p = "SELECT lp.med_codigo, l.med_nome, proc_tipo
			FROM laboratorio_procedimento AS lp
			NATURAL JOIN medico AS l
			WHERE proc_codigo = $proc_codigo";

		$qry_p = db_query($stmt_p);

		while( $row = pg_fetch_row($qry_p) )
		{
			print "
			Laboratorios.push(
				{
					'med_codigo'	: $row[0],
					'med_nome'	: '$row[1]',
					'proc_tipo'		: '$row[2]',
					'removido'		: false
				}
			);";
		}

		print '
			atualiza_select();
		}
		</script>';
	}

}//fechamento do if

//------------------------------------------------------------------>
//-> SQL's
//------------------------------------------------------------------>

//-> ADD <---------------------------------------------------------->
elseif($_REQUEST['acao']=="add")
{
	db_query('BEGIN');

	$proc_codigo = db_get("SELECT NEXTVAL('seq_proc_codigo')");

	// SQL INSERT
	//$stmt = "INSERT INTO procedimento_temp (
	$stmt = "INSERT INTO procedimento (
	proc_codigo,
	proc_nome,
	proc_classificacao_amb,
	proc_classificacao_sus,
	proc_exame,
	proc_valor,
	proc_valor_custo,
	proc_intervalo_min,
	proc_vlsh,
	proc_vlsp,
	proc_vlsa,
	proc_tempo,
	proc_ptosato,
	proc_ptosanest,
	proc_versao,
	proc_result,
	proc_adperm,
	proc_grupo,
	proc_sexo_novo,
	proc_ativo,
	proc_idade_minima,
	proc_idade_maxima,
	proc_sipac,
	proc_instrucoes,
	procedimento_tipo_beneficio
	 ) VALUES (
	$proc_codigo,
	'".trim(strtoupper(substr($proc_nome,0,255)))."',
	'".trim(strtoupper(substr($proc_classificacao_amb,0,15)))."',
	'".trim(strtoupper(substr($proc_classificacao_sus,0,15)))."',
	'".trim(strtoupper(substr($proc_exame,0,1)))."',
	".floatval($proc_valor).",
	".floatval($proc_valor_custo).",
	".intval($proc_intervalo_min).",
	".floatval($proc_vlsh).",
	".floatval($proc_vlsp).",
	".floatval($proc_vlsa).",
	".intval($proc_tempo).",
	".intval($proc_ptosato).",
	".intval($proc_ptosanest).",
	".intval($proc_versao).",
	'".trim(strtoupper(substr($proc_result,0,255)))."',
	".intval($proc_adperm).",
	".intval($proc_grupo).",
	'$proc_sexo_novo',
	'$proc_ativo',
	".intval($proc_idade_minima).",
	".intval($proc_idade_maxima).",
	'$proc_sipac',
	'".trim($proc_instrucoes)."',
	'$beneficio_concedido')";


	$sql = db_query($stmt);

	
	$procs 	= stripslashes($_POST['med_lista_hid']);
	$json 	= new Services_JSON();
	$array 	= $json->decode($procs);

	for( $i=0; $i < count($array); $i++ )
	{
			//$stmt_0 = "INSERT INTO laboratorio_procedimento_temp
			$stmt_0 = "INSERT INTO laboratorio_procedimento
				(proc_codigo, med_codigo)
				VALUES
				($proc_codigo, {$array[$i]->med_codigo})";
			$sql = db_query($stmt_0);
	}

	db_query('COMMIT');

	msg($id_login,$acao,$sql);
}
//
//-> EDIT <--------------------------------------------------------->
elseif($_REQUEST['acao']=="edit")
{
	db_query('BEGIN');

	// SQL UPDATE
	//$stmt = "UPDATE procedimento_temp SET
	$stmt = "UPDATE procedimento SET
	proc_nome = '".trim(strtoupper(substr($proc_nome,0,255)))."',
	proc_classificacao_amb = '".trim(strtoupper(substr($proc_classificacao_amb,0,15)))."',
	proc_classificacao_sus = '".trim(strtoupper(substr($proc_classificacao_sus,0,15)))."',
	proc_exame = '".trim(strtoupper(substr($proc_exame,0,1)))."',
	proc_valor = ".floatval($proc_valor).",
	proc_valor_custo = ".floatval($proc_valor_custo).",
	proc_intervalo_min = ".intval($proc_intervalo_min).",
	proc_vlsh = ".floatval($proc_vlsh).",
	proc_vlsp = ".floatval($proc_vlsp).",
	proc_vlsa = ".floatval($proc_vlsa).",
	proc_tempo = ".intval($proc_tempo).",
	proc_ptosato = ".intval($proc_ptosato).",
	proc_ptosanest = ".intval($proc_ptosanest).",
	proc_versao = ".intval($proc_versao).",
	proc_result = '".trim(strtoupper(substr($proc_result,0,255)))."',
	proc_adperm = ".intval($proc_adperm).",
	proc_grupo = ".intval($proc_grupo).",
	proc_sexo_novo = '$proc_sexo_novo',
	proc_ativo = '$proc_ativo',
	proc_idade_minima = ".intval($proc_idade_minima).",
	proc_idade_maxima = ".intval($proc_idade_maxima).",
	proc_sipac = '$proc_sipac',
	proc_instrucoes = '$proc_instrucoes'

	WHERE proc_codigo = ".intval($proc_codigo) ;
	$sql = db_query( $stmt );

	// limpando..
	//$stmt = "DELETE FROM laboratorio_procedimento_temp WHERE proc_codigo=$proc_codigo";
	$stmt = "DELETE FROM laboratorio_procedimento WHERE proc_codigo = $proc_codigo";
	db_query($stmt);

	// JSON -> PHP
	$procs 	= stripslashes($_POST['med_lista_hid']);
	$json 	= new Services_JSON();
	$array 	= $json->decode($procs);

	for( $i=0; $i < count($array); $i++ )
	{
			//$stmt_0 = "INSERT INTO laboratorio_procedimento_temp
			$stmt_0 = "INSERT INTO laboratorio_procedimento
				(proc_codigo, med_codigo)
				VALUES
				($proc_codigo, {$array[$i]->med_codigo})";
			$sql = db_query($stmt_0);
	}


	db_query('COMMIT');

	msg($id_login,$acao,$sql);
}
//
//-> DEL <---------------------------------------------------------->
else if($_REQUEST['acao']=="del")
{
	db_query('BEGIN');

	// limpando..
	//$stmt = "DELETE FROM laboratorio_procedimento_temp WHERE proc_codigo=$proc_codigo";
	$stmt = "DELETE FROM laboratorio_procedimento WHERE proc_codigo = $proc_codigo";
	db_query($stmt);

	$stmt = "DELETE FROM procedimento WHERE proc_codigo = $proc_codigo";

	$sql = db_query($stmt);

	msg($id_login,$acao,$sql);

	db_query('COMMIT');
}

?>

</body>
</html>
