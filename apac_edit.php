<?php
/**
 * @brief Autorização de Procedimento de Alta Complexidade ( APAC )
 * - Arquivos Relacionados: apac.js, apac_medico_popup.php, apac_paciente_popup.php, apac_print.php, apac_print_sesgunda_via.php, apac_procedimento_popup.php, apac_unidade_popup.php
 * - Tabelas: apac
 * Edita as APAC.
*/ 

/**
 Edição de APAC 
 "Autorizacao de Procedimentos Ambulatoriais de Alta Complexidade/Custo"
*/

/**
@brief  Inclusao principal para montagem do sistema
*/
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."aih_apac.inc.php";

Cabecario( $hotkey = true );

verauth($id_login);

// log
reglog($id_login,"Acessando Formulário de alteração de APAC");

?>

<script type="text/javascript">
function impressao(){
	document.getElementById('frame_impressao').src = 'apac_print.php?id_login=<?=$id_login;?>&apac_num_r=<?=$apac_num_r;?>&paci_nome=<?=$paci_nome;?>&paci_cpf_r=<?=$paci_cpf_r;?>&uni_nome_r=<?=$uni_nome_r;?>&uni_codigo_r=<?=$uni_codigo_r;?>&med_cpf_r=<?=$med_cpf_r;?>&med_nome_r=<?=$med_nome_r;?>&orgao_nome_r=<?=$orgao_nome_r;?>&orgao_codigo_r=<?=$orgao_codigo_r;?>&uni_prestadora_nome_r=<?=$uni_prestadora_nome_r;?>&uni_prestadora_cnpj_r=<?=$uni_prestadora_cnpj_r;?>&uni_prestadora_codigo_r=<?=$uni_prestadora_codigo_r;?>&periodo_val=<?=$periodo_val;?>&periodo_val_fim=<?=$periodo_val_fim;?>&med_aud_cpf_r=<?=$med_aud_cpf_r;?>&proc_lista=<?=join($proc_lista,',');?>&proc_lista_apac=<?=join($proc_lista_apac,',');?>';
}

function impressao_segunda_via( apac_codigo ){
	document.getElementById('frame_impressao').src = 'apac_print_sesgunda_via.php?id_login=<?=$id_login;?>&apac_codigo='+apac_codigo;
}

function atualiza_uni_cnpj( valor )
{
	var cod = document.getElementById('uni_prestadora_codigo').value;
	var apac = document.getElementById('apac_uni_pres').value;
	if( ! cod || ! apac ) return;
	var endereco = 'apac_op.php?acao=atualiza_uni_cnpj&codigo='+cod+'&apac='+apac+'&cnpj='+valor;
	ajax_tudo( endereco, atualiza_uni_cnpj2 );
}
function atualiza_uni_cnpj2(txt)
{
	if(txt) alert(txt);
}
</script>
<?php

/** Editando uma APAC */
if($acao=='edit')
{
	reglog($id_login,"Alteração de APAC, apac_num: {$apac_num_h}");

/*
	$stmt = "UPDATE apac ".
			"SET apac_periodo_validade = '$periodo_val', ".
			"apac_periodo_validade_fim = '$periodo_val_fim', ".
			"apac_segunda_via = 'N' ";
*/

	// SQL UPDATE
	db_query('BEGIN');
	
    $stmt = "UPDATE apac SET 
			apac_codigo = ".intval($apac_codigo).", 
			pac_codigo = ".($apac_paci == 'N' ? $paci_codigo : 'null' ).",
			pac_apac_codigo = ".($apac_paci == 'S' ? $paci_codigo : 'null' ).", 
			uni_sol_codigo = ".($apac_uni_sol == 'N' ? $uni_codigo : 'null' ).", 
			uni_sol_apac_codigo = ".($apac_uni_sol == 'S' ? $uni_codigo : 'null' ).",
			med_sol_codigo = ".($apac_med_sol == 'N' ? intval($med_codigo) : 'null' ).",
			med_sol_apac_codigo = ".($apac_med_sol == 'S' ? intval($med_codigo) : 'null' ).",
			orgao_codigo = null,
			orgao_apac_codigo = null,
			uni_pres_codigo = ".($apac_uni_pres == 'N' ? intval($uni_prestadora_codigo): 'null' ).",
			uni_pres_apac_codigo = ".($apac_uni_pres == 'S' ? intval($uni_prestadora_codigo): 'null' ).", 
			med_aud_codigo = ".($apac_med_aud == 'N' ? intval($med_aud_codigo) : 'null' ).",
			med_aud_apac_codigo = ".($apac_med_aud == 'S' ? intval($med_aud_codigo) : 'null' ).", 
			apac_periodo_validade = '".$periodo_val."',
			apac_periodo_validade_fim = '".$periodo_val_fim."',
			apac_mes_competencia = '".intval( $mes_comp ). "',
			apac_ano_competencia = '".intval( $ano_comp ). "',
			apac_dt_cadastro = ".CURRENT_DATE.", 
			apac_segunda_via = 'N', 
			apac_num = '$apac_num_r'
			WHERE apac_codigo = ".intval($apac_codigo) ;

	db_query($stmt);
	
	if( $apac_num_r != $apac_num_volta ){

		$sql	= "INSERT INTO aih_apac_numeros_resto (aan_numero_resto, aan_tipo) VALUES ('$apac_num_volta', 'APAC')";
		$qry	= db_query($sql);
		//print $sql;
		//echo "<br><br>";

		$del 	= "DELETE FROM aih_apac_numeros_resto WHERE aan_numero_resto='$apac_num_r' AND aan_tipo='APAC' ";
		$query 	= db_query($del);
		//print $stmt;
	}
	
	//-- DELETANDO LISTA DE PROCEDIMENTOS RELACIONADAS A ESTA APAC.
	$del = 'DELETE FROM apac_procedimento WHERE apac_codigo='.$apac_codigo;
	db_query($del);
	
	#$pk = db_get('SELECT MAX(apac_codigo) FROM apac' );
	$pk = $apac_codigo;
	
	for( $i=0; $i < count( $proc_lista ); $i++ )
	{
		if( empty($proc_lista[$i]) ) continue;
		$stmt1 = 'INSERT INTO apac_procedimento (apac_codigo,proc_codigo,proc_apac_codigo)
			VALUES ('.$pk.','.intval($proc_lista[$i]).', null)';
		db_query( $stmt1 );
	}

	for( $i=0; $i < count( $proc_lista_apac ); $i++ )
	{
		if( empty($proc_lista_apac[$i]) ) continue;
		$stmt2 = 'INSERT INTO apac_procedimento (apac_codigo,proc_codigo,proc_apac_codigo)
			VALUES ('.$pk.', null, '.intval($proc_lista_apac[$i]).')';
		db_query( $stmt2 );
	}
	
	db_query('COMMIT');
	//db_query('ROLLBACK');
	
	print "
		<p class='aviso ok'>APAC alterada !</p>
		<script type=\"text/javascript\">
			 //setTimeout(\"location='apac.php'\", 3000);
		</script>";
}else{


/** opções */
echo "
	<fieldset>
	<legend>APAC - Autoriza&ccedil;&atilde;o de Procedimentos Ambulatoriais de Alta Complexidade/Custo</legend>
	<table>
	<tr>
		<td>
			<a href='apac.php?id_login=$id_login'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif' alt='Voltar' style='cursor:pointer;' border='0' /></a>
		</td>
	</tr>
	</table>
	</fieldset>
	</form>";

/** Incluindo o javascript */

echo '
	<script type="text/javascript" src="funcoes.js"></script>
	<script type="text/javascript" src="ajax_motor.js"></script>
	<script type="text/javascript" src="apac.js"></script>'.
	monta_janela( 'janela_proc', 'Procedimentos', 'janela') .
	monta_janela( 'janela_paci', 'Pacientes').
	monta_janela( 'janela_uni', 'Unidade').
	monta_janela( 'janela_numeros', 'Números').
	monta_janela( 'janela_med', 'Médico') ;

		// SQL SELECT
	$stmt = 

"SELECT a.apac_codigo, a.pac_codigo, a.pac_apac_codigo, a.uni_sol_codigo, a.uni_sol_apac_codigo, a.med_sol_codigo,
	a.med_sol_apac_codigo, a.orgao_codigo, a.orgao_apac_codigo, a.uni_pres_codigo, a.uni_pres_apac_codigo, 
	a.med_aud_codigo, a.med_aud_apac_codigo, a.cd10_codigo, 
	to_char(a.apac_periodo_validade, 'dd/mm/YYYY') as apac_periodo_validade, a.apac_convenio, a.apac_convenio_nome, 
	a.apac_hipotese, a.apac_resumo_exame_fisico, a.apac_segunda_via, a.apac_mes_competencia, 
	to_char(a.apac_periodo_validade_fim, 'dd/mm/YYYY') as apac_periodo_validade_fim, 
	a.apac_ano_competencia, to_char(a.apac_dt_cadastro, 'dd/mm/YYYY') as apac_dt_cadastro, a.apac_num,
	uS.usu_nome, uS.usu_cpf, uN.uni_desc, mE.med_nome, mE.med_cpf, uSOL.uni_desc as unidade_prestadora,
	uSOL.uni_cnpj, mA.med_cpf as cpf_medico_auditor, aMA.med_cpf as cpf_medico_auditor_apac
	
FROM apac AS a 
	
	LEFT JOIN usuario AS uS ON uS.usu_codigo=a.pac_codigo
	LEFT JOIN unidade AS uN ON uN.uni_codigo=a.uni_sol_codigo
	LEFT JOIN unidade AS uSOL ON uSOL.uni_codigo=a.uni_pres_codigo
	LEFT JOIN medico AS mE ON mE.med_codigo=a.med_sol_codigo
	LEFT JOIN medico AS mA ON mA.med_codigo=a.med_aud_codigo
	LEFT JOIN apac_medico AS aMA ON aMA.med_codigo=a.med_aud_codigo

WHERE apac_codigo=$apac_codigo ";
		$query = db_query($stmt);
		$res   = pg_fetch_array($query);

	# -- VALIDANDO E BUSCANDO O NOME DO PACIENTE ( SE CONSTA EM USUARIO OU SE CONSTA EM APAC_PACIENTE	)
		if ($res['pac_codigo'] != '') {
			$c0d = $res['pac_codigo'];
		}elseif($res['pac_apac_codigo'] != ''){
			$c0d = $res['pac_apac_codigo'];
		}else{
			$c0d = '0';
		}

	$stmt_2 = "(SELECT u.usu_codigo, u.usu_nome, u.usu_mae, u.usu_cpf, 'N' ".
			  "FROM usuario AS u where u.usu_codigo = $c0d ) ".
			  "UNION ".
			  "(SELECT p.pac_codigo, p.pac_nome, p.pac_mae_responsavel, p.pac_cpf_cns, 'S' ".
			  "FROM apac_paciente AS p where p.pac_codigo = $c0d )";  
	$query_np = db_query($stmt_2);
	$n_p = pg_fetch_array($query_np);
	
	# -- VALIDANDO E BUSCANDO O NOME DO MÉDICO ( SE CONSTA EM MEDICO OU SE CONSTA EM APAC_MEDICO )
		if ($res['cpf_medico_auditor'] != '') {
			$c_med = $res['cpf_medico_auditor'];
		}elseif($res['cpf_medico_auditor_apac'] != ''){
			$c_med = $res['cpf_medico_auditor_apac'];
		}else{
			$c_med = '0';
		}

	$stmt_3 = "(SELECT m.med_codigo, m.med_nome, m.med_cpf, m.med_crm, 'N' ".
			  "FROM medico AS m WHERE prestador_servico = 'N' AND m.med_cpf = '$c_med' ) ".
			  "UNION ".
			  "(SELECT a.med_codigo, a.med_nome, a.med_cpf, a.med_crm, 'S' ".
			  "FROM apac_medico AS a WHERE a.med_cpf = '$c_med' ) ";
	$query_md = db_query($stmt_3);
	$md	= pg_fetch_array($query_md);
	
		# -- VALIDANDO E BUSCANDO UNIDADE SOLICITANTE ( SE CONSTA EM UNIDADE OU SE CONSTA EM APAC_UNIDADE )
		if ($res['uni_sol_codigo'] != '') {
			$c0d_uni = $res['uni_sol_codigo'];
		}elseif($res['uni_sol_apac_codigo'] != ''){
			$c0d_uni = $res['uni_sol_apac_codigo'];
		}else{
			$c0d_uni = '0';
		}

	$stmt_4 = 	"(SELECT u.uni_codigo, u.uni_desc, u.uni_responsavel, u.uni_cnpj, 'N' ".
				"FROM unidade AS u WHERE u.uni_codigo = $c0d_uni) ".
				"UNION ".
				"(SELECT u2.uni_codigo, u2.uni_desc, u2.uni_responsavel, u2.uni_cnpj, 'S' ".
				"FROM apac_unidade AS u2 WHERE u2.uni_codigo = $c0d_uni ) ";
	
	$query_ms = db_query($stmt_4);
	$ms = pg_fetch_array($query_ms);

		# -- VALIDANDO E BUSCANDO MEDICO SOLICITANTE ( SE CONSTA EM MEDICO OU SE CONSTA EM APAC_MEDICO )
		if ($res['med_sol_codigo'] != '') {
			$c = $res['med_sol_codigo'];
		}elseif($res['med_sol_apac_codigo'] != ''){
			$c = $res['med_sol_apac_codigo'];
		}else{
			$c = '0';
		}

	$stmt_5 = "(SELECT m.med_codigo, m.med_nome, m.med_cpf, m.med_crm, 'N' ".
			  "FROM medico AS m WHERE prestador_servico = 'N' AND m.med_codigo = $c ) ".
			  "UNION ".
			  "(SELECT a.med_codigo, a.med_nome, a.med_cpf, a.med_crm, 'S' ".
			  "FROM apac_medico AS a WHERE a.med_codigo = $c ) ";
	
	$query_mEd = db_query($stmt_5);
	$mEd = pg_fetch_array($query_mEd);

		# -- VALIDANDO E BUSCANDO UNIDADE PRESTADORA DE SERVIÇO( SE CONSTA EM UNIDADE OU SE CONSTA EM APAC_UNIDADE )
		if ($res['uni_pres_codigo'] != '') {
			$uni_cod = $res['uni_pres_codigo'];
		}elseif($res['uni_pres_apac_codigo'] != ''){
			$uni_cod = $res['uni_pres_apac_codigo'];
		}else{
			$uni_cod = '0';
		}

	$stmt_6 = 	"(SELECT u.uni_codigo, u.uni_desc,  u.uni_responsavel, u.uni_cnpj, 'N' ".
				"FROM unidade AS u WHERE u.uni_codigo = $uni_cod) ".
				"UNION ".
				"(SELECT u2.uni_codigo, u2.uni_desc, u2.uni_responsavel, u2.uni_cnpj, 'S' ".
				"FROM apac_unidade AS u2 WHERE u2.uni_codigo = $uni_cod ) ";

	$query_UnI = db_query($stmt_6);
	$UnI = pg_fetch_array($query_UnI);

?>

<form action="?acao=edit&apac_codigo=<?=$apac_codigo?>" method="post" onsubmit="return valida_form_apac('<?=$id_login;?>')">
<input type="hidden" name="apac_codigo" value="<?=$apac_codigo?>" />
<fieldset>
<legend>Alteração de APAC</legend>
<table border="0">

	<tr>
		<td>N&uacute;mero da APAC</td>
	  <td>
			<input type='hidden' name='apac_num_volta' id='apac_num_volta' value='<?=$res['apac_num'];?>' />
			<input type='hidden' name='apac_num_h' id='apac_num_h' value='<?=$res['apac_num'];?>' />
		    <input type='text' name='apac_num_r' id='apac_num_r' value='<?=$res['apac_num'];?>' class='box' readonly />
		    <a href='javascript:;'
				onclick="mostra_janela('janela_numeros');init_numeros('<?=$id_login;?>');">
	      <img src='<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/localizar.jpg' alt='Localizar'xalign='absmiddle' border='0' /></a>		</td>
		<td>Competência</td>
		<td colspan="">
		<select id="mes_comp" name="mes_comp" class="box" onchange="document.getElementById('ano_comp').select();">
			<?php 
				//print meses_select( date('m') );
				print meses_select( $res['apac_mes_competencia'] );  
			?>
		</select>
		/
		<input type="text" name="ano_comp" id="ano_comp" class="box" size="4" maxlength="4" value="<?=$res['apac_ano_competencia'];?>" />		</td>
	</tr>


	<tr>
		<td width="120">Nome do Paciente</td>
		<td>
			<input type="hidden" name="apac_paci" id="apac_paci" value="<?=$n_p[4];  ?>" />
			<input type="hidden" name="paci_codigo" id="paci_codigo" value="<?=$n_p[0]; ?>" />
			<input type="text" name="paci_nome" id="paci_nome_r" class="box" size="50" readonly
			value="<?=$n_p['usu_nome']; ?>" />
			<a href='javascript:;'
				onclick="mostra_janela('janela_paci');init_paci('<?=$id_login;?>');">
				<img src='<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/localizar.jpg' alt='Localizar' align='absmiddle' border='0' /></a>		</td>
		<td>CPF do Paciente</td>
		<td>
			<input type="hidden" name="paci_cpf" id="paci_cpf" value="<?=$n_p[3]; ?>" />
			<input type="text" name="paci_cpf_r" id="paci_cpf_r" class="box" maxlength="15"
				onchange="pac_atualiza_cpf(<?=$id_login;?>,this)" value="<?=$n_p[3]; ?>"
				onKeyPress="apenasNumero(this)"
				onKeyUp="apenasNumero(this)" />		</td>
	</tr>
</table>

<fieldset>
<legend>Solicitação</legend>
<table border="0">
	<tr>
		<td width="120">Unidade Solicitante</td>
		<td>
			<input type="text" name="uni_nome_r" id="uni_nome_r" class="box" size="50" value="<?=$ms[1]; ?>" />
			<a href='javascript:;'
				onclick="mostra_janela('janela_uni');init_uni('<?=$id_login;?>',1);">
				<img src='<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/localizar.jpg' alt='Localizar' align='absmiddle' border='0' /></a>
		</td>
		<td width="110">Código</td>
		<td width="115">
			<input type="hidden" name="apac_uni_sol" id="apac_uni_sol" value="<?=$ms[5]; ?>" />
			<input type="hidden" name="uni_codigo" id="uni_codigo" value="<?=$ms[0]; ?>" />
			<input type="text" name="uni_codigo_r" id="uni_codigo_r" value="<?=$ms[0]; ?>"
			class="box" size="15" readonly />
		</td>
	</tr>
	<tr>
		<td>Médico Solicitante</td>
		<td>
		    <input type="hidden" name="apac_med_sol" id="apac_med_sol" value="<?=$mEd[4]; ?>" />
			<input type="hidden" name="med_codigo" id="med_codigo" value="<?=$mEd[0]; ?>" />
			<input type="text" name="med_nome_r" id="med_nome_r" value="<?=$mEd[1]; ?> " 
			class="box" size="50" readonly />
			<a href='javascript:;'
				onclick="mostra_janela('janela_med');init_med('<?=$id_login;?>',1);">
				<img src='<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/localizar.jpg' alt='Localizar' align='absmiddle' border='0' /></a>
		</td>
		<td>CPF do Médico</td>
		<td>
			<input type="text" name="med_cpf_r" id="med_cpf_r" class="box" value="<?=$mEd[2]; ?>" 
			size="15" readonly />
		</td>
	</tr>
</table>	
</fieldset>

<fieldset>
<legend>Autorização</legend>
<table border="0" class="lista">
<thead>
	<tr bgcolor="#ffffff">
		<th>
			Proced./Medicamento(s) Autorizado(s)
			<a href='javascript:;'
		 	onclick="mostra_janela('janela_proc');init_proc('<?=$id_login;?>');">
			<img src='<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/localizar.jpg' alt='Localizar' align='absmiddle' border='0' /></a>
		</th>
		<th width="220">Laboratório</th>
		<th width="15"><div class="c">APAC</div></th>
		<th width="60"><div class="c">Código</div></th>
		<th width="80">&nbsp;</th>
	</tr>
</thead>
<?
 $sql_consulta = "SELECT B.apac_codigo, POG.* FROM apac_procedimento AS B,
					((SELECT p.proc_classificacao_sus, p.proc_nome, m.med_nome, p.proc_valor, 'N' AS apac, p.proc_codigo
					FROM procedimento AS p 
					LEFT JOIN medico AS m ON m.med_codigo = p.med_codigo 
					)
					UNION
					(SELECT a.proc_numero, a.proc_nome, m1.med_nome, a.proc_valor, 'S' AS apac, a.proc_codigo
					FROM apac_procedimento_cad AS a
					LEFT JOIN medico AS m1 ON m1.med_codigo = a.med_codigo 
					)) AS POG
					WHERE B.apac_codigo=$apac_codigo AND (B.proc_codigo=POG.proc_codigo OR B.proc_apac_codigo = POG.proc_codigo )";

   $qry_consulta = db_query($sql_consulta);
	//$res_consulta = pg_fetch_array($qry_consulta);
	
	echo "<tbody id='procedimento_lista'>";
	
		while ($res_consulta = pg_fetch_array($qry_consulta)) {
			//if( $res_consulta['apac'] == 'S' )
				print "
				<script type='text/javascript'>
				//INIT_PROC_LISTA = true;
				//".( $res_consulta['apac'] == 'S' ? 'PROC_LISTA_APAC' : 'PROC_LISTA' ).".push({$res_consulta[0]});
				atualiza_proc( '".$res_consulta['1']."', '".$res_consulta['2']."', '".$res_consulta['3']."', '".$res_consulta['5']."', '".$res_consulta['6']."' );
				</script>";
	}
	
	echo"</tbody>";


?>
</table>

<table border="0">
	<tr>
		<td width="180">Órgão Autorizador</td>
		<td colspan="3">
			<input type="text" name="orgao_nome_r" id="orgao_nome_r" class="box" size="50" 
				value="Autarquia Municipal de Saúde" readonly style="font-weight:bold;" />
			<!--<a href='javascript:;'
				onclick="mostra_janela('janela_uni');init_uni('<?=$id_login;?>',2);">
				<img src='<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/localizar.jpg' alt='Localizar' align='absmiddle' border='0' /></a>-->
		</td>
		<td width="20">Código</td>
		<td>
			<input type="hidden" name="apac_orgao" id="apac_orgao" value="S" />
			<input type="hidden" name="orgao_codigo" id="orgao_codigo" value="-1" />
			<input type="text" name="orgao_codigo_r" id="orgao_codigo_r" class="box" size="10" readonly />
		</td>
	</tr>
	<tr>
		<td>Unidade Prestadora de Serviços</td>
		<td>
			<input type="text" name="uni_prestadora_nome_r" id="uni_prestadora_nome_r" value="<?=$UnI[1]; ?>"
			class="box" size="50" />
			<a href='javascript:;' onclick="mostra_janela('janela_uni');init_uni('<?=$id_login;?>',3);">
				<img src='<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/localizar.jpg' alt='Localizar' align='absmiddle' border='0' /></a>
		</td>
		<td width="50">CNPJ</td>
		<td>
			<input type="text" name="uni_prestadora_cnpj_r" id="uni_prestadora_cnpj_r" class="box" size="15" 
					onchange="atualiza_uni_cnpj(this.value)" value="<?=$UnI[4]; ?>" />
		</td>
		<td>Código</td>
		<td>
			<input type="hidden" name="apac_uni_pres" id="apac_uni_pres" value="<?=$UnI[5]; ?>" />
			<input type="hidden" name="uni_prestadora_codigo" id="uni_prestadora_codigo" value="<?=$UnI[0]; ?>" />
			<input type="text" name="uni_prestadora_codigo_r" id="uni_prestadora_codigo_r" value="<?=$UnI[0]; ?>"
			class="box" size="10" />
		</td>
	</tr>
</table>

<table border="0">
	<tr>
		<td class="r">Período de Validade</td>
		<td><input type="text" name="periodo_val" id="periodo_val" class="box" size="15" maxlength="10"
			onKeypress="return Ajusta_Data(this,event)" onchange="troca_data(this,30,'periodo_val_fim')" 
			value="<?=$res['apac_periodo_validade'];?>" />
			à
			<input type="text" name="periodo_val_fim" id="periodo_val_fim" class="box" size="15" maxlength="10"
			onKeypress="return Ajusta_Data(this,event)" value="<?=$res['apac_periodo_validade_fim'];?>" />
	  </td>
		<td class="r">CPF do Autorizado</td>
		<td>
			<input type="hidden" name="apac_med_aud" id="apac_med_aud" value="<?=$md[4]; ?>" />
			<input type="hidden" name="med_aud_codigo" id="med_aud_codigo" value="<?=$md[0]; ?>" />
			<input type="text" name="med_aud_cpf_r" id="med_aud_cpf_r" value="<?=$md[2]; ?>" 
			class="box" readonly />
			<a href='javascript:;' onclick="mostra_janela('janela_med');init_med('<?=$id_login;?>',2);">
				<img src='<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/localizar.jpg' alt='Localizar' align='absmiddle' border='0' /></a>
		</td>
	</tr>
</table>

</fieldset>


<p><input type='image' src='<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/salvar_on.jpg' alt='Enviar' /></p>

</form>
<?
	}
?>

<iframe id='frame_impressao' width='0' height='0' frameborder='0'>
</iframe>
</body>
</html>
