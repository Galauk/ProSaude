<?php
/**
 * @brief Autorização de Procedimento de Alta Complexidade ( APAC )
 * - Arquivos Relacionados: apac.js, apac_medico_popup.php, apac_paciente_popup.php, apac_print.php, apac_print_sesgunda_via.php, apac_procedimento_popup.php, apac_unidade_popup.php
 * - Tabelas: aih
 * Adiciona as APAC.
*/ 

/**
 Cadastro do APAC 
 "Autorizacao de Procedimentos Ambulatoriais de Alta Complexidade/Custo"
*/

/**
@brief  Inclusao principal para montagem do sistema
*/
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";

Cabecario( $hotkey = true );

verauth($id_login);

// log 
reglog($id_login,"Acessando o Cadastro de APAC");

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

<?

/** opções */
if(chmodbtn($id_login, "procurar_if", "apac.php"))
{
	echo "<form action='#' method='get' onsubmit='return busca_apac(\"$id_login\");'>";
}
echo "<fieldset>
	<legend>APAC - Autoriza&ccedil;&atilde;o de Procedimentos Ambulatoriais de Alta Complexidade/Custo</legend>
	<table>
	<tr>
		<td width='80'>";
		echo ChmodBtn($id_login,'adicionar',"apac.php?id_login=$id_login&acao=form_add");
			/*<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg' alt='Adicionar' style='cursor:pointer;'
				onclick=\"document.location.href='$_SERVER[PHP_SELF]?id_login=$id_login&acao=form_add'\" />*/
		echo "</td>
		<td width='10'>Buscar:</td>
		<td width='70'>
			<input type='hidden' name='acao' value='busca'>";
			if(ChmodBtn($id_login,'procurar_if','apac.php'))
			{
				echo "<input type='text' name='palavra_chave' id='palavra_chave' class='box' value='$palavra_chave'
					onChange=\"this.value=this.value.toUpperCase();busca_apac('$id_login')\" size='17' />";
			} else {
				echo "<input type='text' name='palavra_chave' id='palavra_chave' class='box' value='$palavra_chave'
					onChange=\"this.value=this.value.toUpperCase();\" size='17' />";
			}
		echo "</td>
		<td width='80'>
			<select name='busca_tipo' id='busca_tipo' class='box'>
				<option value='1'".( $busca_tipo==1 ? ' selected' : '' ).">Paciente</option>
				<option value='2'".( $busca_tipo==2 ? ' selected' : '' ).">Médico Auditor</option>
				<option value='3'".( $busca_tipo==3 ? ' selected' : '' ).">Unidade Sol.</option>
				<option value='4'".( $busca_tipo==4 ? ' selected' : '' ).">N&uacute;mero</option>
			</select>
		</td>
		<td width='20'>".ChmodBtn($id_login,'procurar','apac.php')."</td>
		<td>";
			//echo chmodbtn($id_login,'cadastrar_n_apac',"aih_apac_cad_num.php?tipo=APAC'");
			//echo chmodbtn($id_login,'aih_apac',"aih_apac_visualizacao.php?");
			if(chmodbtn($id_login, 'adicionar_if', 'aih_apac_cad_num.php'))
			{
				echo "<a href='aih_apac_cad_num.php?id_login=$id_login&tipo=APAC'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/cadastrar_n_apac_on.jpg' border='0'></a>";
			} else {
				echo "<a href='#'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/cadastrar_n_apac_off.jpg' border='0'></a>";
			}
			if(chmodbtn($id_login, 'listar_if', 'aih_apac_visualizacao.php'))
			{
				echo "<a href='aih_apac_visualizacao.php?id_login=$id_login'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/aih_apac_on.jpg' alt='AIH/APAC' border='0' /></a>";
			} else {
				echo "<a href='#'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/aih_apac_off.jpg' alt='AIH/APAC' border='0' /></a>";
			}
		echo "</td>
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
	monta_janela( 'janela_med', 'Médico') ;

/** Listando/Buscando */
if( empty($acao) || $acao == 'busca' )
{
	
	/** Construindo a busca */
	
	//-> Subistituindo o + por porcentagem na busca
	$str = str_replace("+","%%",$palavra_chave);
	
	// arrumando sql
	$resp 	= '';
	$max 	= 15;
	
	if( ! empty($str) )
	{
		$where 		= 'WHERE ';
		$where_c 	= "ILIKE TO_ASCII('%$palavra_chave%')";
		
		switch( $busca_tipo )
		{
			case 1:
				$where .= "(CASE WHEN a.pac_codigo IS NOT NULL THEN p0.usu_nome $where_c
							WHEN a.pac_apac_codigo IS NOT NULL THEN p1.pac_nome $where_c END)";
				break;
			
			case 2:
				$where .= "(CASE WHEN a.med_aud_codigo IS NOT NULL THEN m2.med_nome $where_c
							WHEN a.med_aud_apac_codigo IS NOT NULL THEN m3.med_nome $where_c END)";
				break;	
				
			case 3:
				$where .= "(CASE WHEN a.uni_sol_codigo IS NOT NULL THEN u0.uni_desc $where_c
							WHEN a.uni_sol_apac_codigo IS NOT NULL THEN u1.uni_desc $where_c END)";
				break;
			
			case 4:
				$where .= "apac_num LIKE '%$palavra_chave%'";
				break;
			
// 			case 4:
// 				$where .= "(CASE WHEN a.orgao_codigo IS NOT NULL THEN u2.uni_desc $where_c
// 							WHEN a.orgao_apac_codigo IS NOT NULL THEN u3.uni_desc $where_c END)";
// 				break;
				
			default:
				$where = '';
		}
	}
	// se NAO for busca, limitar 
	$sql_f 	= empty($acao) ? 'LIMIT '.$max : '';

	$stmt = "SELECT a.apac_codigo,

		(CASE WHEN a.pac_codigo IS NOT NULL THEN p0.usu_nome
		WHEN a.pac_apac_codigo IS NOT NULL THEN p1.pac_nome
		ELSE 'none' END) as pac_nome,
	
		(CASE WHEN a.uni_sol_codigo IS NOT NULL THEN u0.uni_desc
		WHEN a.uni_sol_apac_codigo IS NOT NULL THEN u1.uni_desc
		ELSE 'none' END) as uni_sol_desc,
	
		(CASE WHEN a.med_sol_codigo IS NOT NULL THEN m0.med_nome
		WHEN med_sol_apac_codigo IS NOT NULL THEN m1.med_nome
		ELSE 'none' END) as med_nome,
	
		(CASE WHEN orgao_codigo IS NOT NULL THEN u2.uni_desc
		WHEN orgao_apac_codigo IS NOT NULL THEN u3.uni_desc
		ELSE 'none' END) as orgao_desc,
	
		(CASE WHEN uni_pres_codigo IS NOT NULL THEN u4.uni_desc
		WHEN uni_pres_apac_codigo IS NOT NULL THEN u5.uni_desc
		ELSE 'none' END) as uni_pres_desc,
	
		(CASE WHEN med_aud_codigo IS NOT NULL THEN m2.med_nome
		WHEN med_aud_apac_codigo IS NOT NULL THEN m3.med_nome
		ELSE 'none' END) as med_aud_nome,
	
		TO_CHAR(apac_periodo_validade,'DD/MM/YYYY'),
		
		apac_segunda_via, apac_num, TO_CHAR(apac_dt_cadastro,'DD/MM/YYYY') AS cad
	
	FROM apac AS a
	
	LEFT JOIN usuario AS p0 ON p0.usu_codigo = a.pac_codigo
	LEFT JOIN apac_paciente AS p1 ON p1.pac_codigo = a.pac_apac_codigo
	
	LEFT JOIN unidade AS u0 ON u0.uni_codigo = a.uni_sol_codigo
	LEFT JOIN apac_unidade AS u1 ON u1.uni_codigo = a.uni_sol_apac_codigo
	
	LEFT JOIN medico AS m0 ON m0.med_codigo = a.med_sol_codigo
	LEFT JOIN apac_medico AS m1 ON m1.med_codigo = a.med_sol_apac_codigo
	
	LEFT JOIN unidade AS u2 on u2.uni_codigo = a.orgao_codigo
	LEFT JOIN apac_unidade AS u3 on u3.uni_codigo = a.orgao_apac_codigo
	
	LEFT JOIN unidade AS u4 ON u4.uni_codigo = a.uni_pres_codigo
	LEFT JOIN apac_unidade AS u5 ON u5.uni_codigo = a.uni_pres_apac_codigo
	
	LEFT JOIN medico AS m2 ON m2.med_codigo = a.med_aud_codigo
	LEFT JOIN apac_medico AS m3 ON m3.med_codigo = a.med_aud_apac_codigo
	
	$where	ORDER BY pac_nome $sql_f";
	
	//print '<pre>'.$stmt.'</pre>';
	
	if(chmodbtn($id_login, "listar_if", "apac.php"))
	{
		$qry = db_query($stmt);
	}
	$num = pg_num_rows($qry);
	
	if( $acao == 'busca' && ! empty($palavra_chave) )	
	{
		if( $num == 0 ) 	{ $resp = "Nenhum Registro encontrado com \"$palavra_chave\""; }
		elseif( $num == 1 ) { $resp = "Encontrado <b>$num</b> Registro com \"$palavra_chave\""; }
		elseif( $num > 1) 	{ $resp = "Encontrados <b>$num</b> Registros com \"$palavra_chave\""; }
	} else
		$resp = "Listando os $max primeiros Registros";
	
	echo "
	<fieldset>
	<legend>$resp</legend>
	
	<table class='lista'>
	<tr bgcolor='#ffffff'>
		<th width='95' style='text-align:center;'>N&ordm; APAC</th>
		<th>Paciente</th>
		<th>Unidade Sol.</th>
                <th width='85' style='text-align:center;'>Dt. Cadastro</th>
		<th>Médico Sol.</th>
		<th>&nbsp;</th>
	</tr>
	";
	
	while( $row = pg_fetch_array($qry) )
	{
		echo "
		
		<form method=post action=$PHP_SELF>

		<input type=hidden name=acao value=segundavia />
		<input type=hidden name=id_login value=$id_login />
		<input type=hidden name=apac_codigo value=$row[apac_codigo] />

		<tr>
			<td class='c'>$row[apac_num]</td>
			<td>$row[pac_nome]</td>
			<td>$row[uni_sol_desc]</td>
                        <td class='c'>$row[cad]</td>
			<td>$row[med_nome]</td>
			<td width='270' align='center'>
			<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/segunda_via_on.jpg' />";
/*				if ($row['apac_segunda_via'] == 'N')
				{
					echo "<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/segunda_via_on.jpg' style='pointer:cursor'
						onclick='impressao_segunda_via($row[0])' />";
					echo "<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/segunda_via_on.jpg' />";
				}
				else
				{
					echo "<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/segunda_via_off.jpg' />";
				}
*/
  	  echo "
		".ChmodBtn($id_login,'editar','apac_edit.php?apac_codigo='.$row[apac_codigo])."
		".ChmodBtn($id_login,'apagar','apac_del.php?apac_codigo='.$row[apac_codigo].'&tipo=APAC&numero='.$row[apac_num])."
			
	  </td>
		  </tr>
		</form>
		";
	}

	print "
	</table>
	</fieldset>";

/** Formulário */
} else if( $acao == 'form_add' )
{
/*
$apac_num_arr = aih_apac_proximo_num( 'APAC' );
if( $apac_num_arr[0] == 0 )
{
	print '
	<script type="text/javascript">
		alert("A APAC nao pode ser cadastrada!\nNao existe mais numeros disponiveis");
	</script>';
	$func = 'cancela_form()'; 
}
else{
	$func = " valida_form_apac('$id_login')";
}
*/

// ADICIONADO POR ANDRE FILIPE/DUDU - 02/04/2007
/*
	$tem_numero = true;
	$usa_resto	= false;

	db_query("begin");

	$apac_num_arr = aih_apac_proximo_num( 'APAC' );
    
	if( $apac_num_arr[0] == 0 )
	{
		$tem_numero = false;
	}

	if( ! $tem_numero )
    { */
		$sql = "SELECT MIN(aan_numero_resto) FROM aih_apac_numeros_resto WHERE aan_tipo = 'APAC'"; 
		$apac_num_arr = db_getrow($sql);

        if( ! empty ($apac_num_arr[0]) )
        {               
			$tem_numero = true;
			//$usa_resto = true;
		}
		else
		{
	    	print '
        	<script type="text/javascript">
        		alert("A APAC nao pode ser cadastrada!\nNao existe mais numeros disponiveis");
	        </script> 
			<p class="aviso">APAC nao inserida !</p>
			</body></html>';
			db_query("rollback");
			die();
		}
    #}

	db_query('COMMIT');


//-------------------------------------------------
/*
if( ! $usa_resto )
{

	$sql_iresto = "INSERT INTO aih_apac_numeros_resto (aan_numero_resto, aan_tipo)
		VALUES ('$apac_num_arr[0]', 'APAC')";

	$query_iresto = db_query($sql_iresto);

	$stmt1 = "UPDATE aih_apac_numero SET num_prox = num_prox + 1
		WHERE codigo  = $apac_num_arr[1] ";

	db_query( $stmt1 );

	db_query('COMMIT');
}
*/
?>

<form action="?id_login=<?=$id_login;?>&acao=add" method="post" onsubmit="return valida_form_apac('<?=$id_login;?>')">
<fieldset>
<legend>Cadastro de APAC</legend>
<table border="0">

	<tr>
		<td>N&uacute;mero da APAC</td>
		<td>
			<input type='hidden' name='apac_num_h' value='<?=$apac_num_arr[0];?>' />
			<input type='text' name='apac_num_r' value='<?=$apac_num_arr[0];?>' readonly class='box' />
		
		</td>
		<td>Competência</td>
		<td colspan="">
		<select id="mes_comp" name="mes_comp" class="box" onchange="document.getElementById('ano_comp').select();">
			<?php print meses_select( date('m') ); ?>
		</select>
		/
		<input type="text" name="ano_comp" id="ano_comp" class="box" size="4" maxlength="4" value="<?=date('Y');?>" />
		</td>
	</tr>


	<tr>
		<td width="120">Nome do Paciente</td>
		<td>
			<input type="hidden" name="apac_paci" id="apac_paci" />
			<input type="hidden" name="paci_codigo" id="paci_codigo" />
			<input type="text" name="paci_nome" id="paci_nome_r" class="box" size="50" readonly />
			<a href='javascript:;'
				onclick="mostra_janela('janela_paci');init_paci('<?=$id_login;?>');">
				<img src='<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/localizar.jpg' alt='Localizar' align='absmiddle' border='0' /></a>
		</td>
		<td>CPF do Paciente</td>
		<td>
			<input type="hidden" name="paci_cpf" id="paci_cpf" />
			<input type="text" name="paci_cpf_r" id="paci_cpf_r" class="box" maxlength="15" 
				onchange="pac_atualiza_cpf(<?=$id_login;?>,this)"
				onKeyPress="apenasNumero(this)"
				onKeyUp="apenasNumero(this)" />
		</td>
	</tr>
</table>

<fieldset>
<legend>Solicitação</legend>
<table border="0">
	<tr>
		<td width="120">Unidade Solicitante</td>
		<td>
			<input type="text" name="uni_nome_r" id="uni_nome_r" class="box" size="50" />
			<a href='javascript:;'
				onclick="mostra_janela('janela_uni');init_uni('<?=$id_login;?>',1);">
				<img src='<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/localizar.jpg' alt='Localizar' align='absmiddle' border='0' /></a>
		</td>
		<td width="110">Código</td>
		<td width="115">
			<input type="hidden" name="apac_uni_sol" id="apac_uni_sol" />
			<input type="hidden" name="uni_codigo" id="uni_codigo" />
			<input type="text" name="uni_codigo_r" id="uni_codigo_r" class="box" size="15" readonly />
		</td>
	</tr>
	<tr>
		<td>Médico Solicitante</td>
		<td>
		    <input type="hidden" name="apac_med_sol" id="apac_med_sol" value="" />
			<input type="hidden" name="med_codigo" id="med_codigo" />
			<input type="text" name="med_nome_r" id="med_nome_r" class="box" size="50" readonly />
			<a href='javascript:;'
				onclick="mostra_janela('janela_med');init_med('<?=$id_login;?>',1);">
				<img src='<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/localizar.jpg' alt='Localizar' align='absmiddle' border='0' /></a>
		</td>
		<td>CPF do Médico</td>
		<td>
			<input type="text" name="med_cpf_r" id="med_cpf_r" class="box" size="15" readonly />
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
			<img src='<?= $_SESSION[linkroot].$_SESSION[comum];?>/localizar.jpg' alt='Localizar' align='absmiddle' border='0' /></a>
		</th>
		<th width="220">Laboratório</th>
		<th width="15"><div class="c">APAC</div></th>
		<th width="60"><div class="c">Código</div></th>
		<th width="80">&nbsp;</th>
	</tr>
</thead>
<tbody id="procedimento_lista">
	<tr><td colspan="5" class="c"><em>[ vazio ]</em></td></tr>
</tbody>
</table>

<table border="0">
	<tr>
		<td width="180">Órgão Autorizador</td>
		<td colspan="3">
			<input type="text" name="orgao_nome_r" id="orgao_nome_r" class="box" size="50" 
				value="Autarquia Municipal de Saúde" readonly style="font-weight:bold;" />
			<!--<a href='javascript:;'
				onclick="mostra_janela('janela_uni');init_uni('<?=$id_login;?>',2);">
				<img src='<?= $_SESSION[linkroot].$_SESSION[comum];?>/localizar.jpg' alt='Localizar' align='absmiddle' border='0' /></a>-->
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
			<input type="text" name="uni_prestadora_nome_r" id="uni_prestadora_nome_r" class="box" size="50" />
			<a href='javascript:;' onclick="mostra_janela('janela_uni');init_uni('<?=$id_login;?>',3);">
				<img src='<?= $_SESSION[linkroot].$_SESSION[comum];?>/localizar.jpg' alt='Localizar' align='absmiddle' border='0' /></a>
		</td>
		<td width="50">CNPJ</td>
		<td>
			<input type="text" name="uni_prestadora_cnpj_r" id="uni_prestadora_cnpj_r" class="box" size="15" 
				onchange="atualiza_uni_cnpj(this.value)" />
		</td>
		<td>Código</td>
		<td>
			<input type="hidden" name="apac_uni_pres" id="apac_uni_pres" />
			<input type="hidden" name="uni_prestadora_codigo" id="uni_prestadora_codigo" />
			<input type="text" name="uni_prestadora_codigo_r" id="uni_prestadora_codigo_r" class="box" size="10" />
		</td>
	</tr>
</table>

<table border="0">
	<tr>
		<td class="r">Período de Validade</td>
		<td><input type="text" name="periodo_val" id="periodo_val" class="box" size="15" maxlength="10"
			onKeypress="return Ajusta_Data(this,event)" onchange="troca_data(this,30,'periodo_val_fim')" />
			à
			<input type="text" name="periodo_val_fim" id="periodo_val_fim" class="box" size="15" maxlength="10"
			onKeypress="return Ajusta_Data(this,event)" />
			</td>
		<td class="r">CPF do Autorizado</td>
		<td>
			<input type="hidden" name="apac_med_aud" id="apac_med_aud" value="" />
			<input type="hidden" name="med_aud_codigo" id="med_aud_codigo" />
			<input type="text" name="med_aud_cpf_r" id="med_aud_cpf_r" class="box" readonly />
			<a href='javascript:;' onclick="mostra_janela('janela_med');init_med('<?=$id_login;?>',2);">
				<img src='<?= $_SESSION[linkroot].$_SESSION[comum];?>/localizar.jpg' alt='Localizar' align='absmiddle' border='0' /></a>
		</td>
	</tr>
</table>

</fieldset>

</fieldset>

<p><input type='image' src='<?= $_SESSION[linkroot].$_SESSION[comum];?>/adicionar_on.jpg' alt='Enviar' /></p>

</form>

<?php

}
/*
else if($acao == 'form_edit')
{
  echo "<fieldset>
		<legend>Opções de Cadastro</legend>
			<a href=apac.php?id_login=$id_login><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif' border='0'></a>
		</fieldset>";

	$sql = 	"SELECT ".
				"TO_CHAR(apac_periodo_validade, 'dd/mm/YYYY') as data_ini, ".
				"TO_CHAR(apac_periodo_validade_fim, 'dd/mm/YYYY') as data_fim ".
			"FROM apac ".
			"WHERE apac_codigo=$apac_codigo";
			
	$res = pg_query($sql);
	$row = pg_fetch_array($res);

  echo "<form name='aih_form_altera' method='post' action='$PHP_SELF?id_login=$id_login' onSubmit='return verifica_campos_alteracao()'>
		<input type='hidden' name='acao' value='edit'>
		<input type='hidden' name='apac_codigo' value='$apac_codigo'>
		
	   <fieldset>
	    <legend>Alteração de APAC</legend>
			 <table width='100%' align='center' cellspacing='3' cellpadding='0' border='0'>
				<tr>
					<td class='r' width='130'>Período de Validade:</td>
					<td><input type='text' name='periodo_val' id='periodo_val' class='box' size='15' maxlength='10'
						onKeypress='return Ajusta_Data(this,event)' onchange='troca_data(this,30,'periodo_val_fim')' value='$row[data_ini]' />
						à
						<input type='text' name='periodo_val_fim' id='periodo_val_fim' class='box' size='15' maxlength='10'
						onKeypress='return Ajusta_Data(this,event)' value='$row[data_fim]' />
					</td>
				</tr>
			    <tr>
			         <td width='130'>&nbsp;</td>
			   		 <td><br /><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg></td>
			    </tr>	
			 </table>
	   </fieldset>
	   <br />
		</form>";
}*/
/** Inserindo novo APAC */
else if( $acao == 'add' )
{
	db_query("BEGIN");
	db_query("LOCK TABLE aih_apac_numeros_resto IN SHARE MODE");

	$stmt_resto = "SELECT aan_codigo FROM aih_apac_numeros_resto WHERE aan_numero_resto='$apac_num_h'";
	$tem_numero = db_get( $stmt_resto );

	//$apac_num_arr = aih_apac_proximo_num( 'APAC' );
	//if( $apac_num_arr[0] == 0 )
	if( ! $tem_numero )
	{
		print '
		<script type="text/javascript">
			alert("A APAC nao pode ser cadastrada!\nNao existe mais numeros disponiveis");
		</script>
		<p class="aviso">APAC nao inserida !</p>
		</body></html>';
		die();
	}
	

	$stmt_1 = "DELETE FROM aih_apac_numeros_resto WHERE aan_numero_resto = '$apac_num_h' ";
    db_query( $stmt_1 );

	db_query("COMMIT");

	/// ---
	db_query('BEGIN');

	/**
        if (!$tem_numero)
        {
                $resto = true;
        
                $sql = "SELECT aan_numero_resto FROM aih_apac_numeros_resto";
                $apac_num_arr = db_getrow($sql);
        
                        if( !empty ($apac_num_arr[0]) )
                        {
                                $tem_numero = true;
                        }
        }	
        if(!$tem_numero)
        {
                print '
                <script type="text/javascript">
                        alert("A APAC nao pode ser cadastrada!\nNao existe mais numeros disponiveis 2");
                </script>
                <p class="aviso">APAC nao inserida !</p>
                </body></html>';
                db_query("ROLLBACK");
                die();
        }
	*/

	// log
	reglog($id_login,"Cadastro de APAC, apac_num: {$apac_num_h}");

	$apac_codigo = db_get("SELECT NEXTVAL('apac_apac_codigo_seq')");	
	
	// SQL INSERT
	$stmt = "INSERT INTO apac (
	apac_codigo,
	pac_codigo, 
	pac_apac_codigo, 
	uni_sol_codigo, 
	uni_sol_apac_codigo, 
	med_sol_codigo, 
	med_sol_apac_codigo, 
	orgao_codigo, 
	orgao_apac_codigo, 
	uni_pres_codigo, 
	uni_pres_apac_codigo, 
	med_aud_codigo, 
	med_aud_apac_codigo, 
	apac_periodo_validade,
	apac_periodo_validade_fim,
	apac_mes_competencia,
	apac_ano_competencia,
	apac_dt_cadastro,
	apac_segunda_via,
	apac_num
	 ) VALUES (
	 $apac_codigo, 
	".($apac_paci == 'N' 	? intval($paci_codigo) 			: 'null' ).", 
	".($apac_paci == 'S' 	? intval($paci_codigo) 			: 'null' ).", 
	".($apac_uni_sol == 'N' ? intval($uni_codigo) 			: 'null' ).", 
	".($apac_uni_sol == 'S' ? intval($uni_codigo) 			: 'null' ).", 
	".($apac_med_sol == 'N' ? intval($med_codigo) 			: 'null' ).", 
	".($apac_med_sol == 'S' ? intval($med_codigo) 			: 'null' ).", 
	"./*($apac_orgao == 'N' 	? intval($orgao_codigo) 		: 'null' )*/'null'.", 
	"./*($apac_orgao == 'S' 	? intval($orgao_codigo)			: 'null' )*/'null'.",
	".($apac_uni_pres == 'N'? intval($uni_prestadora_codigo): 'null' ).", 
	".($apac_uni_pres == 'S'? intval($uni_prestadora_codigo): 'null' ).", 
	".($apac_med_aud == 'N'	? intval($med_aud_codigo)		: 'null' ).", 
	".($apac_med_aud == 'S'	? intval($med_aud_codigo)		: 'null' ).", 
	'".$periodo_val."', '".$periodo_val_fim."',".
	intval( $mes_comp ). ",".
	intval( $ano_comp ). ",".
	"CURRENT_DATE,
	'N', 
	'$apac_num_h' ) ";
	//'$apac_num_arr[0]' ) ";

	db_query( $stmt );

	$pk = db_get('SELECT MAX(apac_codigo) FROM apac' );

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
	
	/*if(!$resto)
	{
		$stmt1 = "UPDATE aih_apac_numero SET num_prox = num_prox + 1
         WHERE codigo  = $apac_num_arr[1] ";
        db_query( $stmt1 );
    }
	else
	{
        $stmt_1 = "DELETE FROM aih_apac_numeros_resto WHERE aan_numero_resto = '$apac_num_h' ";
        db_query( $stmt_1 );
    }*/
 

	db_query('COMMIT');
	//db_query('ROLLBACK');

	echo "
		<p class='aviso'>APAC inserida !</p>
		<p><a href='$PHP_SELF?id_login=$id_login'>&lt; Voltar</a></p>
		<script type=\"text/javascript\">
			setTimeout('impressao_segunda_via($apac_codigo)');
		</script>";	

}
else if ($acao == 'segundavia')
{
	//$sql =  "UPDATE apac ".
	//		"SET apac_segunda_via='S' ".
	///	 "WHERE apac_codigo=$apac_codigo";

	//$query = pg_query($sql);

	echo "
		<p class='aviso ok'>Imprimindo...</p>
        <p><a href='$PHP_SELF?id_login=$id_login'>&lt; Voltar</a></p>
		<script type=\"text/javascript\">
			 setTimeout('impressao_segunda_via($apac_codigo)');
		</script>";
		//setTimeout(\"location='$PHP_SELF?acao=&id_login=$id_login'\", 3000);

}
else if($acao=='edit')
{

	$stmt = "UPDATE apac ".
			"SET apac_periodo_validade = '$periodo_val', ".
			"apac_periodo_validade_fim = '$periodo_val_fim', ".
			"apac_segunda_via = 'N' ";
	
	$query = db_query($stmt);
	
				 
	
	print "
		<p class='aviso ok'>APAC alterada !</p>
		<script type=\"text/javascript\">
			 setTimeout(\"location='$PHP_SELF?acao=&id_login=$id_login'\", 3000);
		</script>";
}

?>
<iframe id='frame_impressao' width='0' height='0' frameborder='0'>
</iframe>
</body>
</html>
