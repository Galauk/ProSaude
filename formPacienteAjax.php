<?
	session_start();
	include_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	
	$form = new classForm();
			
	$acao = $_GET['acao'];
	$id = $_GET['id'];
	
	switch ($acao){
		case "cidade":
			$ufSigla = $_GET['codigo'];
			$selectCidade = "SELECT cid_codigo_ibge, 
									cid_nome
							   FROM cidade
							  WHERE uf_sigla = '$ufSigla'
							  ORDER BY cid_nome";
			$formulario = $form->inputSelect("muni_cd_cod_ibge_nasc", null, "Cidade de Nascimento", $selectCidade, "onChange=\"pesquisaRuas(this);\"", null, null, "style=\"width:150px\"");
			break;
		case "estado":
			$pais = $_GET['codigo'];
			if ($pais == '010'){
				$selectUf = "SELECT uf_sigla, 
									uf_nome
	  						   FROM estado
	  						  ORDER BY uf_nome";
				$formulario = $form->inputSelect("muni_ibge_uf_nasc", null, "Estado de Nascimento", $selectUf, "onChange=\"montaForm('cidade', 'select_cidades', this.value);\"", null, null, "style=\"width:150px\"");
				$formulario .= "<div id=select_cidades style='clear:both'></div>";
			}else{
				$formulario = $form->hiddenForm("muni_ibge_uf_nasc", "XX");
				$formulario .=$form->hiddenForm("muni_cd_cod_ibge_nasc", "9999999");
				$formulario .=$form->inputText("nacionalidade", 'ESTRANGEIRO', "Estado de Nascimento", 20, 2, null, "text", "S");
			}
			break;
		case "endereco":
			$dom_codigo = $_GET['codigo'];
			$selectRua = "SELECT dom_codigo,
						         r.rua_cep,
						         co_tipo_logradouro,
						         r.rua_nome,
						         dom_numero,
						         dom_complemento,
						         r.rua_bairro,
						         r.cid_codigo,
						         dom_telefone
						    FROM domicilio AS dom
						    JOIN rua AS r
						      ON r.rua_codigo = dom.rua_codigo
						   WHERE dom_codigo = $dom_codigo ";
			$exec = pg_query($selectRua);
			$numLinhas = pg_num_rows($exec);
			$dados = pg_fetch_array($exec);
			$cid_codigo = $dados['cid_codigo'];
			//echo $cid_codigo."xxx";
			//$estado_fam = $dados['uf_sigla'];
			$rua_nome = $dados['rua_nome'];
			$dom_numero = $dados['dom_numero'];
			$dom_complemento = $dados['dom_complemento'];
			$dom_cep = $dados['rua_cep'];
			$dom_bairro = $dados['rua_bairro'];
			$co_tipo_logradouro = $dados['co_tipo_logradouro'];
			$dom_telefone = $dados['dom_telefone'];
			$selectTipoRua = " SELECT co_tipo_logradouro, 
									  ds_tipo_logradouro
								 FROM tb_ms_tipo_logradouro 
								WHERE co_tipo_logradouro in ('081', '008', '065', '100')
									
								UNION ALL
									
							   SELECT co_tipo_logradouro, 
									  ds_tipo_logradouro
								 FROM tb_ms_tipo_logradouro 
								WHERE co_tipo_logradouro not in ('081', '008', '065', '100')";
			//$executaSelect = pg_query($selectTipoRua);
			$formulario = $form->inputText("dom_cep", $dom_cep, "CEP", 20, 9, "onKeypress=\"return Ajusta_Cep(this, event);\"", "text", "S");
			$formulario .=$form->inputText("usu_fone", $dom_telefone, "Telefone", 20, 13, "onKeypress=\"return soNumeroTelefone(this);\"",null,"S");
			$formulario .= "<div style=\"clear:both\">
				<table border=0 cellpadding=0 cellspacing=0>
					<tr>
						<td>
							<div>
							    <div class=\"cL0\" style=\"width:2px;\"></div>
							    <div class=\"cL1\"><img src=".$_SESSION[linkroot].$_SESSION[modulo]."imgs/cap01.png></div>
							    <div class=\"cL2\"> Endere&ccedil;o:</div>
							    <div class=\"cL3\"><img src=".$_SESSION[linkroot].$_SESSION[modulo]."imgs/cap02.png></div>
							    <div class=\"cL4\"></div>
							</div>
						</td>
						<td width=10>"
							.$form->inputSelect("tipo_logradouro", null, null, $selectTipoRua, null, null, $co_tipo_logradouro, null, "TIPO").
						"</td>
						<td width=300>"
							.$form->inputText("nome_logradouro", $rua_nome, null, 57, null, null, "text", "S", "S").
					   "</td>
					    <td width=80>
							N&ordm; <input type=text name='dom_numero' value='$dom_numero' id='dom_numero' readonly='readonly' size=10 class='inputForm' style=\"text-transform:uppercase;\">
						</td>
					</tr>
				</table></div>";
				$formulario .= $form->inputText("dom_complemento", $dom_complemento, "Complemento", 30, null, null, "text", "S", "S");
				$formulario .= $form->inputText("dom_bairro", ($numLinhas == 1 ? "$dom_bairro" : null), "Bairro", 30, null, null, "text", "S");
				$formulario .= "<table border=0 style='clear:left' cellpadding=0 cellspacing=0>
					<tr>
						<td>
							<div>
							    <div class=\"cL0\" style=\"width:2px;\"></div>
							    <div class=\"cL1\"><img src=".$_SESSION[linkroot].$_SESSION[modulo]."imgs/cap01.png></div>
							    <div class=\"cL2\"> Cidade:</div>
							    <div class=\"cL3\"><img src=".$_SESSION[linkroot].$_SESSION[modulo]."imgs/cap02.png></div>
							    <div class=\"cL4\"></div>
							</div>
						</td>
						<td>";
							$selectMunicipio = "SELECT cid_codigo, 
													   cid_nome||'-'||uf_sigla
												  FROM cidade
												 ORDER BY uf_sigla, 
												 	   cid_nome";
							$formulario .= $form->inputSelect("cid_codigo", null, null, $selectMunicipio, null, null, $cid_codigo, null, "CIDADE").
						
							//<input type=text name='municipio_fam' size=50 readonly='readonly' class='inputForm'".($numLinhas == 1 ? " value='$municipio_fam'" : "").">&nbsp;
						"</td>
					</tr>
				</table>";
			//echo $form->inputSelect("rua_resid", null, null, $selectRua, "onChange=\"pesquisaRuas(this);\"", null, null, "style=\"width:150px\"");
			break;
		case "seleciona_tipo_rua":
			$tipoRua = $_GET['codigo'];
			$selectCidade = "SELECT co_tipo_logradouro, 
									ds_tipo_logradouro
  							   FROM tb_ms_tipo_logradouro
  							  WHERE ds_tipo_logradouro = upper('$tipoRua')
  							     OR ds_tipo_logradouro_abrev = upper('$tipoRua')";
			$executa = pg_query($selectCidade);
			$dados = pg_fetch_array($executa);
			$ds_tipo_logradouro = $dados['ds_tipo_logradouro'];
			$formulario = "<input type=hidden name='tipo_logradouro' value=\"$co_tipo_logradouro\" id='tipo_logradouro' onChange=\"populaTipoRua('$ds_tipo_logradouro');\">";
			$formulario .= "<script>document.getElementById('tipo_rua').value = '$ds_tipo_logradouro';</script>";
			//$formulario = $form->hiddenForm("tipo_logradouro", $dados['co_tipo_logradouro']);
			break;
	}
	echo "|".$id."|".$formulario;
?>