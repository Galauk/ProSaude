<script type="text/javascript" src="funcoes.js"></script>
<script type="text/javascript" src="ajax_motor.js"></script>
<script language='JavaScript' type='text/javascript' src='paciente.js'></script>
<script type="text/javascript">
function card(u) {
window.open( 'pdf/geraCartao.php?usu_codigo='+u,
                 null,
                 'height=40,width=530,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes');
}

	function validaDigitoVerificador(id){
		input = document.getElementById(id);
		if (input.value.length > 0 && input.value.length < 6){
			alert('O campo c�digo do cadastrador deve ter 6 d�gitos');
			document.getElementById(id).focus();
			return false;
		}
		return true;
	}
	function mostraCampos(id){
		div = document.getElementById(id);
		div.style.display = "block";
	}
	function montaForm(acao, id, input){
		url = "formPacienteAjax.php?acao="+acao+"&codigo="+input+"&id="+id;
		ajax_tudo(url, resultMontaForm);
	}
	function resultMontaForm(txt){
		array = txt.split('|');
		id = array[1];
		resposta = array[2];
		div = document.getElementById(id);
		div.innerHTML = resposta;
	}
	function populaTipoRua(input){
		document.getElementById('tipo_rua').value = input;
	}
	function mostraDataObito(input, id){
		if (input == "S"){
			div = document.getElementById(id);
			div.style.display = "block";
		}else{
			div = document.getElementById(id);
			div.style.display = "none";
		}
	}
	function validaCampos(){
		usu_nome = document.getElementById('usu_nome');
		usu_mae = document.getElementById('usu_mae');
		usu_datanasc = document.getElementById('usu_datanasc');
		if (usu_nome.value.length == 0){
			alert('O campo nome do paciente � obrigat�rio.');
			usu_nome.focus();
			return false;
		}
		if (usu_datanasc.value.length == 0){
			alert('O campo data de nascimento do paciente � obrigat�rio.');
			usu_datanasc.focus();
			return false;
		}
		if (usu_mae.value.length == 0){
			alert('O campo nome da m�e do paciente � obrigat�rio.');
			usu_mae.focus();
			return false;
		}
		document.form_paciente.submit();
	}
	//fun��o criada para retirar a barra que a fun��o Ajusta_Data() coloca.
	function removeBarras(){
		busca = document.getElementById('busca');
		busca.value = busca.value.replace( '/', "" );//retira a barra
		return true;
	}
	function verificaTipo(input, event){
		busca = document.getElementById('busca');
		busca.value = '';
		busca.focus();

		var aux = function(event){Ajusta_Data(this, event);};
		if (input == "usu_datanasc"){
			busca.maxLength = 10;
			busca.addEventListener("keypress", aux, false);
		}else{
			busca.maxLength = 200;
			//como n�o foi poss�vel utilizar o removeEventListener, a solu��o foi chamar a fun��o removeBarras pra "tirar a m�scara".
			busca.addEventListener("keypress", function(event){removeBarras();}, false);
		}
	}
</script>
<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/debug.inc.php";
	
	$common = new commonClass();
	echo $common->incJquery();
	$form = new classForm();
	$table = new tableClass();
	$usu_codigo = $_GET['usu_codigo'];
	$id_login = $_REQUEST['id_login'];
	$acao = $_REQUEST['acao'];
	$somente_ativos = $_REQUEST['somente_ativos'];
	$arraySimNao = array("S"=>"Sim", "N"=>"N&atilde;o");
	switch ($acao){
		case "busca":
			$palavraChave = $_POST['busca'];
			$tipo_busca = $_POST['tipo_busca'];
			if (($tipo_busca == "usu_prontuario") || ($tipo_busca == "usu_datanasc")){
				$where = "$tipo_busca = '$palavraChave'";
			}else{
				$where = "$tipo_busca like upper('%$palavraChave%')";
			}
			
			if ($somente_ativos == "S"){
				$and = " AND usu_ativacao = 'S' ";
			}
			
			$sql = "SELECT usu_prontuario, 
						   usu_nome,
						   to_char(usu_datanasc,'DD/MM/YYYY') as usu_datanasc, 
						   usu_mae, 
						   coalesce(usu_sexo, '&nbsp;') as usu_sexo, 
						   coalesce(usu_rg, '&nbsp;') as usu_rg, 
						   coalesce(usu_cpf, '&nbsp;') as usu_cpf,
						   usu_codigo
					  FROM usuario AS u
					 WHERE $where
					  $and
					 ORDER BY usu_codigo DESC 
					 LIMIT 15 ";
			$execSql = pg_query($sql);
			echo $common->menuTab(array("Pacientes"));
				echo $common->bodyTab('1');
				echo $table->openTable();
					$arrayTipoBusca = array("usu_nome"=>"NOME","usu_mae"=>"NOME DA M&Atilde;E","usu_datanasc"=>"DATA DE NASCIMENTO","usu_prontuario"=>"PRONTU&Aacute;RIO","usu_rg"=>"RG","usu_cpf"=>"CPF");
					$arrayAddBusca = array($common->commonButton("Adicionar", "paciente_novo.php?id_login=$id_login&acao=form", "adicionar.png", null), $form->openForm("paciente_novo.php?id_login=$id_login", "POST", "formBusca").$form->hiddenForm("acao", "busca").$form->inputText("busca", null, "Buscar", 48, null, null, "text", "N", "S"),$form->inputSelect("tipo_busca", $arrayTipoBusca, null, null, "onChange=\"verificaTipo(this.value);\"", null, "usu_nome", "style=\"width:250px;\""), $form->inputCheckboxRadio("somente_ativos", "S", null, null, array("S"=>"Somente Ativos")), $common->commonButton("Buscar", null, "buscar.png", "onClick=\"document.formBusca.submit();\"").$form->closeForm());
					echo $table->criaLinha($arrayAddBusca);
				echo $table->closeTable();
				
				echo $table->openTable("lista");
					echo $table->criaLinha(array("PRONTU&Aacute;RIO","NOME","DATA NASCIMENTO","NOME DA M&Atilde;E","SEXO","RG","CPF","&nbsp;"),null,array(1,1,1,1,1,1,1,4),"S");
					while ($result = pg_fetch_row($execSql)){
						$usu_codigo = $result[7];
						array_pop($result);
						array_push($result, $common->commonButton("Etiqueta", null, "label.png", "OnClick=\"window.open('relatorio/EtiquetaUsuario.php?id_login=$id_login&pes_codigo=$usu_codigo',null,'height=400,width=550,status=yes,toolbar=no,menubar=no,location=no');\""), 
											$common->commonButton("Hist&oacute;rico", null, "folderHistory.png", "OnClick=\"window.open('infopaciente_2.php?id_login=$id_login&usu_codigo=$usu_codigo',null,'height=400,width=500,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes');\""), 
											$common->commonButton("Editar", "paciente_novo.php?id_login=$id_login&acao=form&usu_codigo=$usu_codigo", "editar_on.png"),
											$common->commonButton("Apagar", "paciente_novo.php?id_login=$id_login&acao=del&usu_codigo=$usu_codigo", "apagar.png"));
						echo $table->criaLinha($result);
					}
				echo $table->closeTable();
			echo $common->closeTab();
			
			break;
		case "":
			echo $common->menuTab(array("Pacientes"));
				echo $common->bodyTab('1');
				echo $table->openTable();
					$arrayTipoBusca = array("usu_nome"=>"NOME","usu_mae"=>"NOME DA M&Atilde;E","usu_datanasc"=>"DATA DE NASCIMENTO","usu_prontuario"=>"PRONTU&Aacute;RIO","usu_rg"=>"RG","usu_cpf"=>"CPF");
					$arrayAddBusca = array($common->commonButton("Adicionar", "paciente_novo.php?id_login=$id_login&acao=form", "adicionar.png", null), $form->openForm("paciente_novo.php?id_login=$id_login", "POST", "formBusca").$form->hiddenForm("acao", "busca").$form->inputText("busca", null, "Buscar", 48, null, null, "text", "N", "S"),$form->inputSelect("tipo_busca", $arrayTipoBusca, null, null, "onChange=\"verificaTipo(this.value);\"", null, "usu_nome", "style=\"width:250px;\""), $form->inputCheckboxRadio("somente_ativos", "S", null, null, array("S"=>"Somente Ativos")), $common->commonButton("Buscar", null, "buscar.png", "onClick=\"document.formBusca.submit();\"").$form->closeForm());
					echo $table->criaLinha($arrayAddBusca);
				echo $table->closeTable();
			echo $common->closeTab();
			$sql = "SELECT usu_prontuario, 
						   usu_nome,
						   to_char(usu_datanasc,'DD/MM/YYYY') as usu_datanasc, 
						   usu_mae, 
						   coalesce(usu_sexo, '&nbsp;') as usu_sexo, 
						   coalesce(usu_rg, '&nbsp;') as usu_rg, 
						   coalesce(usu_cpf, '&nbsp;') as usu_cpf,
						   usu_codigo
					  FROM usuario AS u
					 WHERE usu_ativacao = 'S'
					 ORDER BY usu_codigo DESC 
					 LIMIT 15 ";
			$execSql = pg_query($sql);
			echo $table->openTable("lista");
				echo $table->criaLinha(array("Prontu&aacute;rio","Nome","Dt Nascimento","Nome da M&atilde;e","Sexo","RG","CPF","&nbsp;"),array(50, 250, 100, 250, 10, 60, 70, 100),array(1,1,1,1,1,1,1,4),"S");
				while ($result = pg_fetch_row($execSql)){
					$usu_codigo = $result[7];
					array_pop($result);
    if($result[5]=="") {
       $card = "<a href='#' onClick=\"alert('**ATENCAO** CADASTRE O CARTAO SUS PARA HABILITAR HA IMPRESSAO');\"><img src='imgs/btn_cartao.png' border=0></a>";
                               } else {
                                                $card = "<a href='#' onclick='card(\"$usu_codigo\");'><img src='imgs/btn_cartao.png' border=0></a>";
                                        }

					array_push($result, $common->commonButton("Etiqueta", null, "label.png", "OnClick=\"window.open('relatorio/EtiquetaUsuario.php?id_login=$id_login&pes_codigo=$usu_codigo',null,'height=400,width=550,status=yes,toolbar=no,menubar=no,location=no');\""), 
										$common->commonButton("Hist&oacute;rico", null, "folderHistory.png", "OnClick=\"window.open('infopaciente_2.php?id_login=$id_login&usu_codigo=$usu_codigo',null,'height=400,width=500,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes');\""), 
										$common->commonButton("Editar", "paciente_novo.php?id_login=$id_login&acao=form&usu_codigo=$usu_codigo", "editar_on.png"),$card
/*										$common->commonButton("Apagar", "paciente_novo.php?id_login=$id_login&acao=del&usu_codigo=$usu_codigo", "apagar.png")
*/);
					echo $table->criaLinha($result);
				}
			echo $table->closeTable();
			break;
		case "form":
			$usu_codigo = $_GET['usu_codigo'];
			$select = "SELECT * FROM usuario WHERE usu_codigo = $usu_codigo";
			/*$select = "SELECT usu_prontuario, 
							  usu_nome, 
							  to_char(usu_datanasc, 'dd/mm/yyyy') usu_datanasc, 
							  usu_mae, 
							  usu_pai, 
							  usu_sexo, 
							  usu_estado_civil, 
							  usu_conjuge, 
							  pais_codigo, 
							  muni_cd_cod_ibge_nasc, 
							  id_domicilio, 
							  usu_rg, 
							  usu_rg_emissor, 
							  uf_sigla_rg, 
							  usu_rg_compl, 
							  usu_rg_dt_emissao, 
							  usu_email, 
							  usu_celular, 
							  usu_fone, 
							  usu_fone_recado, 
							  usu_obito, 
							  usu_dt_obito, 
							  usu_cpf, 
							  usu_cnh_numero, 
							  usu_cnh_categoria, 
							  usu_ctps, 
							  usu_ctps_serie, 
							  usu_ctps_dt_emissao, 
							  uf_sigla_ctps, 
							  usu_pis_pasep, 
							  usu_cartao_p_sus, 
							  usu_cartao_sus, 
							  usu_cert_emissao_nasc, 
							  usu_cert_cartorio_nasc, 
							  usu_cert_livro_nasc, 
							  usu_cert_lv_fls_nasc, 
							  usu_cert_termo_nasc, 
							  usu_tipo_certidao, 
							  usu_cert_emissao, 
							  usu_cert_cartorio, 
							  usu_cert_livro, 
							  usu_cert_lv_fls, 
							  usu_cert_termo, 
							  usu_cert_civil_emissor, 
							  usu_uf_cert_civil, 
							  usu_tit_eleitor, 
							  usu_tit_eleitor_zona, 
							  usu_tit_eleitor_secao, 
							  usu_cisvir, 
							  usu_same, 
							  usu_transporte_publico, 
							  usu_freq_escolar, 
							  uni_origem, 
							  uni_unidade, 
							  nr_portaria_naturalizacao, 
							  dt_naturalizacao, 
							  dt_preenchimento_form, 
							  usu_dt_entrada_pais, 
							  usu_cbo_r, 
							  rac_codigo, 
							  nr_uso_municipal, 
							  ecd_codigo, 
							  usu_sit_familiar, 
							  usu_situacao_emprego, 
							  usu_cadastrador, 
							  usu_bolsa_alimentacao, 
							  usu_bolsa_familia, 
							  usu_prodea, 
							  usu_renda_media, 
							  usu_nr_lote, 
							  usu_doencas, 
							  usu_habitos_vida, 
							  usu_observacao, 
							  usr_alt, 
							  usr_alt_dt, 
							  usr_cad, 
							  usr_cad_dt, 
							  usu_codigo_sus, 
							  usu_codigo, 
							  usu_gestor, 
							  codigo_secretaria 
						 FROM usuario 
						WHERE usu_codigo = $usu_codigo";*/
			//echo $select;
			$execSelect = pg_query($select);
			$row = pg_fetch_array($execSelect);
			echo $form->openForm("paciente_novo.php", "POST", "form_paciente");
				echo $form->hiddenForm("acao", "salvar");
				echo $form->hiddenForm("id_login", $id_login);
				echo $form->hiddenForm("usu_codigo", $usu_codigo);
				echo $form->hiddenForm("usu_prontuario_hidden", trim($row['usu_prontuario']), "usu_prontuario_hidden");
				echo $common->menuTab(array("Dados Pessoais"));
				echo $common->bodyTab('1');
					echo $form->inputText("usu_nome", $row['usu_nome'], "Nome", 70, 60, null, "text", "N", "S");
					echo $form->inputText("usu_mae", $row['usu_mae'], "Nome da m&atilde;e", 70, 60, null, "text", "N", "S");
					echo $form->inputText("usu_datanasc", formatarData($row['usu_datanasc']), "Data de Nascimento", 20, 10, "onKeypress=\"return Ajusta_Data(this, event);\"");
					echo $form->inputText("usu_cartao_sus", $row['usu_cartao_sus'], "Cartao SUS", 70, 60, null, "text", "N", "S");
					echo $form->inputText("usu_prontuario", $row['usu_prontuario'], "Numero do Prontuario", 40, 40, null, "text", "N", "S");
					echo $form->inputText("usu_cidade_nasc", $row['usu_cidade_nasc'], "Cidade de Nascimento", 40, 40, null, "text", "N", "S");
					echo $form->inputText("usu_fone", $row['usu_fone'], "Telefone", 20, 60, null, "text", "N", "S");
					//echo $form->inputText("usu_prontuario", $row['usu_prontuario'], "N. Cartao Municipal", 20, 20, ((empty($row['usu_prontuario']) && (!empty($row['usu_codigo'])))? "onDblClick=\"atualizar_prontuario($row[usu_codigo])\"" : ""), "text", "S", "S", ((empty($row['usu_prontuario']) && (!empty($row['usu_codigo']))) ? "Clique duplo para gerar o pr&oacute;ximo n&uacute;mero de prontu&aacute;rio e atualizar o Paciente." : ""));
					echo $form->inputCheckboxRadio("usu_sexo", $row['usu_sexo'], "Sexo", "checked", array("M"=>"Masculino", "F"=>"Feminino"), "radio");
					$selectEstCivil = "SELECT estc_codigo, 
											  estc_descricao
		  								 FROM estado_civil
		  								ORDER BY estc_descricao";
					/*echo $form->inputSelect("usu_estado_civil", $row['usu_estado_civil'], "Estado Civil", $selectEstCivil, "onChange=\"mostraCampos('nomeConjuge')\"", null, $row['usu_estado_civil'], "style=\"width:150px\"");
					echo "<div id=nomeConjuge style='clear:both; display:none;'>".$form->inputText("usu_conjuge", $usu_conjuge, "C&ocirc;njuge", 70, 60)."</div>";
					$selectPais = "SELECT pais_codigo, 
										  pais_nome
		  							 FROM pais
		  							ORDER BY pais_nome";
					echo $form->inputSelect("pais_codigo", $pais_codigo, "Pa&iacute;s de Nascimento", $selectPais, "onChange=\"montaForm('estado', 'select_estado', this.value);\"", null, $row['pais_codigo'], "style=\"width:150px\"");
					if ($row['usu_codigo'] != ""){
						if ($row['pais_codigo'] == '010'){
							$selecionadoUF = "SELECT uf_sigla 
												FROM cidade
											   WHERE cid_codigo_ibge = '".$row['muni_cd_cod_ibge_nasc']."'";
							$exec = pg_query($selecionadoUF);
							$dado = pg_fetch_array($exec);
							$selectUf = "SELECT uf_sigla, 
												uf_nome
				  						   FROM estado
				  						  ORDER BY uf_nome";
							$estado = $form->inputSelect("muni_ibge_uf_nasc", null, "Estado de Nascimento", $selectUf, "onChange=\"montaForm('cidade', 'select_cidades', this.value);\"", null, $dado['uf_sigla'], "style=\"width:150px\"");
							
							$ufSigla = $dado['uf_sigla'];
							$selectCidade = "SELECT cid_codigo_ibge, 
													cid_nome
											   FROM cidade
											  WHERE uf_sigla = '$ufSigla'
											  ORDER BY cid_nome";
							$cidade = $form->inputSelect("muni_cd_cod_ibge_nasc", null, "Cidade de Nascimento", $selectCidade, "onChange=\"pesquisaRuas(this);\"", null, $row['muni_cd_cod_ibge_nasc'], "style=\"width:150px\"");
							
							$estado .= "<div id=select_cidades style='clear:both'>$cidade</div>";
						}else{
							$estado = $form->hiddenForm("muni_ibge_uf_nasc", "XX");
							$estado .=$form->hiddenForm("muni_cd_cod_ibge_nasc", "9999999");
							$estado .=$form->inputText("nacionalidade", 'ESTRANGEIRO', "Estado de Nascimento", 20, 2, null, "text", "S");
						}
					}
					echo "<div id=select_estado style='clear:both'>$estado</div>";
					echo $form->inputText("id_domicilio", $row['id_domicilio'], "C&oacute;digo Domic&iacute;lio", 20, 9, "onKeypress=\"return Bloqueia_Caracteres(event);\" onChange=\"montaForm('endereco', 'select_endereco', this.value);\"");			echo "<div id=select_endereco style='clear:both'></div>";
					echo $form->inputText("usu_rg", $row['usu_rg'], "RG", 20, 10, "onKeypress=\"return Bloqueia_Caracteres(event);\"");
					echo $form->inputText("usu_rg_emissor", $row['usu_rg_emissor'], "Emissor RG", 20, 10, "onKeypress=\"return Bloqueia_Caracteres(event);\"");
					$selectUf = "SELECT uf_sigla, 
										uf_nome
								   FROM estado
								  ORDER BY uf_nome";
					echo $form->inputSelect("uf_sigla_rg", null, "Estado RG", $selectUf, null, null, $row['uf_sigla_rg'], "style=\"width:150px\"");
					echo $form->inputText("usu_rg_compl", $row['usu_rg_compl'], "Complemento RG", 20, 10, null, "text", "N", "S");
					echo $form->inputText("usu_rg_dt_emissao", formatarData($row['usu_rg_dt_emissao']), "Data Emiss&atilde;o RG", 20, 10, "onKeypress=\"return Ajusta_Data(this, event);\"");
					echo $form->inputText("usu_email", $row['usu_email'], "E-mail", 70, 100, "onChange=\"return Verifica_Email('usu_email', 0)\"");
					echo $form->inputText("usu_celular", $row['usu_celular'], "Celular", 20, 13, "onKeypress=\"return soNumeroTelefone(this);\"");
					echo $form->inputText("usu_fone", $row['usu_fone'], "Telefone", 20, 13, "onKeypress=\"return soNumeroTelefone(this);\"");
					echo $form->inputText("usu_fone_recado", $row['usu_fone_recado'], "Telefone de Recado", 20, 13, "onKeypress=\"return soNumeroTelefone(this);\"");
					echo $form->inputCheckboxRadio("usu_obito", $row['usu_obito'], "&Oacute;bito", "OnChange=\"mostraDataObito(this.value, 'data_obito')\"", $arraySimNao, "radio");
					echo "<div id=data_obito ".((($row['usu_obito'] == "N" && $row['usu_codigo'] != "") || ($row['usu_codigo'] == "")) ? "style=\"display:none\"" : "style=\"display:block\"").">".$form->inputText("usu_dt_obito", formatarData($row['usu_dt_obito']), "Data do &Oacute;bito", 20, 10, "onKeypress=\"return Ajusta_Data(this, event);\"")."</div>";
				echo $common->closeTab();
				echo $common->bodyTab('2');
					echo $form->inputText("usu_cpf", $row['usu_cpf'], "CPF", 20, 14, "onKeypress=\"return soNumeroCPF(this, event);\"");
					echo $form->inputText("usu_cnh_numero", $row['usu_cnh_numero'], "CNH", 20, 30, "onKeypress=\"return Bloqueia_Caracteres(event);\"");
					echo $form->inputText("usu_cnh_categoria", $row['usu_cnh_categoria'], "CNH Categoria", 20, 2, null, "text", "N", "S");
					echo $form->inputText("usu_ctps", $row['usu_ctps'], "Carteira de Trabalho", 20, 10, "onKeypress=\"return Bloqueia_Caracteres(event);\"");
					echo $form->inputText("usu_ctps_serie", $row['usu_ctps_serie'], "Cart. Trabalho - S&eacute;rie", 20, 7, null, "text", "N", "S");
					echo $form->inputText("usu_ctps_dt_emissao", formatarData($row['usu_ctps_dt_emissao']), "Cart. Trabalho - Data Emiss&atilde;o", 20, 10, "onKeypress=\"return Ajusta_Data(this, event);\"");
					echo $form->inputSelect("uf_sigla_ctps", null, "Estado Carteira de Trabalho", $selectUf, null, null, $row['uf_sigla_ctps'], "style=\"width:150px\"");
					echo $form->inputText("usu_pis_pasep", $row['usu_pis_pasep'], "PIS/PASEP", 20, 20);
					echo $form->inputText("usu_cartao_p_sus", $row['usu_cartao_p_sus'], "Cart&atilde;o SUS (Temp.)", 20, 15, "onkeypress=\"return Bloqueia_Caracteres(event)\"");
					echo $form->inputText("usu_cartao_sus", $row['usu_cartao_sus'], "Cart&atilde;o SUS", 20, 15, "onkeypress=\"return Bloqueia_Caracteres(event)\"");
					echo $form->inputText("usu_cert_emissao_nasc", formatarData($row['usu_cert_emissao_nasc']), "Data Emiss&atilde;o Cert. Nasc.", 20, 10, "onkeypress=\"return Ajusta_Data(this, event)\"");
					echo $form->inputText("usu_cert_cartorio_nasc", $row['usu_cert_cartorio_nasc'], "Cart&oacute;rio Nasc.", 30, 60, null, "text", "N", "S");
					echo $form->inputText("usu_cert_livro_nasc", $row['usu_cert_livro_nasc'], "Livro Nasc.", 20, 10, null, "text", "N", "S");
					echo $form->inputText("usu_cert_lv_fls_nasc", $row['usu_cert_lv_fls_nasc'], "Folha Nasc.", 20, 4, null, "text", "N", "S");
					echo $form->inputText("usu_cert_termo_nasc", $row['usu_cert_termo_nasc'], "Termo Nasc.", 20, 10, null, "text", "N", "S");
					$arrayOpcoes = array("92"=>"Certid&atilde;o de Casamento", "93"=>"Certid&atilde;o de Averba&ccedil;&atilde;o de Div&oacute;rcio", "95"=>"Certid&atilde;o de &Iacute;ndio");
					echo $form->inputSelect("usu_tipo_certidao", $arrayOpcoes, "Tipo Certid&atilde;o", null, "onChange=\"mostraCampos('certidao');\"", null, $row['usu_tipo_certidao'], "style=\"width:200px\"", smartyUpper("Selecione uma Op&ccedil;&atilde;o"));
					echo "<div id=certidao style='clear:both;".($row['usu_tipo_certidao'] == '' ? "display:none" : "")."'>".
						$form->inputText("usu_cert_emissao", formatarData($row['usu_cert_emissao']), "Data Emiss&atilde;o Certid&atilde;o", 20, 10, "onkeypress=\"return Ajusta_Data(this, event)\"").
						$form->inputText("usu_cert_cartorio", $row['usu_cert_cartorio'], "Cart&oacute;rio", 30, 60, null, "text", "N", "S").
						$form->inputText("usu_cert_livro", $row['usu_cert_livro'], "Livro", 20, 10, null, "text", "N", "S").
						$form->inputText("usu_cert_lv_fls", $row['usu_cert_lv_fls'], "Folha", 20, 4, null, "text", "N", "S").
						$form->inputText("usu_cert_termo", $row['usu_cert_termo'], "Termo", 20, 10, null, "text", "N", "S").
						$form->inputText("usu_cert_civil_emissor", $row['usu_cert_civil_emissor'], "Emissor Cert. Civil", 70, 60, null, "text", "N", "S").
						$form->inputSelect("usu_uf_cert_civil", null, "Estado Cert. Civil", $selectUf, null, null, $row['usu_uf_cert_civil'], "style=\"width:150px\"")
					."</div>";
					echo $form->inputText("usu_tit_eleitor", $row['usu_tit_eleitor'], "T&iacute;tulo de Eleitor", 20, 14, "onkeypress=\"return SomenteNumero(event)\"");
					echo $form->inputText("usu_tit_eleitor_zona", $row['usu_tit_eleitor_zona'], "T&iacute;tulo de Eleitor - Zona", 20, 4, "onkeypress=\"return SomenteNumero(event)\"");
					echo $form->inputText("usu_tit_eleitor_secao", $row['usu_tit_eleitor_secao'], "T&iacute;tulo de Eleitor - Se&ccedil;&atilde;o", 20, 4, "onkeypress=\"return SomenteNumero(event)\"");
					echo $form->inputText("usu_cisvir", $row['usu_cisvir'], "C&oacute;digo do Cons&oacute;rcio", 20, 15, "onkeypress=\"return SomenteNumero(event)\"");
					echo $form->inputText("usu_same", $row['usu_same'], "SAME", 20, 15, null, "text", "N", "S");
				echo $common->closeTab();
				echo $common->bodyTab('3');	
					echo $form->inputCheckboxRadio("usu_transporte_publico", $row['usu_transporte_publico'], "Transporte P&uacute;blico", null, $arraySimNao, "radio");
					echo $form->inputCheckboxRadio("usu_freq_escolar", $row['usu_freq_escolar'], "Frequ&ecirc;ncia Escolar", null, $arraySimNao, "radio");
					$selectUnidade = "SELECT uni_codigo,
											 uni_desc 
										FROM unidade";
					echo $form->inputSelect("uni_origem", null, "Unidade (Origem Paciente)", $selectUnidade, null, null, $row['uni_origem'], "style=\"width:150px\"");
					echo $form->inputSelect("uni_unidade", null, "Unidade (Prontu&aacute;rio)", $selectUnidade, null, null, $row['uni_unidade'], "style=\"width:150px\"");
					$selectEsgoto = "SELECT co_esgoto_sanitario, 
											ds_esgoto_sanitario
		  							   FROM tb_ms_esgoto_sanitario";
					echo $form->inputSelect("usu_tipo_esgoto", null, "Tipo de rede de esgoto", $selectEsgoto, null, null, $row['usu_tipo_esgoto'], "style=\"width:150px\"");
					$selectTipoDomicilio = "SELECT co_tipo_domicilio, 
												   ds_tipo_domicilio
		  									  FROM tb_ms_tipo_domicilio";
					echo $form->inputSelect("usu_tipo_const_casa", null, "Tipo de constru&ccedil;&atilde;o (Resid.)", $selectTipoDomicilio, null, null, $row['usu_tipo_const_casa'], "style=\"width:150px\"");
					echo $form->inputText("usu_qtd_comodos", $row['usu_qtd_comodos'], "Quantidade de C&ocirc;modos", 20, 3, "onkeypress=\"return SomenteNumero(event)\"");
					echo $form->inputCheckboxRadio("usu_rede_eletrica", $row['usu_rede_eletrica'], "Rede el&eacute;trica", null, array("S"=>"Sim", "N"=>"N&atilde;o"), "radio");
					$selectTipoAbastecimentoAgua = "SELECT co_abastecimento_agua, 
														   ds_abastecimento_agua
		  											  FROM tb_ms_abastecimento_agua";
					echo $form->inputSelect("usu_tipo_abast_agua", $usu_tipo_abast_agua, "Abastecimento de &Aacute;gua", $selectTipoAbastecimentoAgua, null, null, $row['usu_tipo_abast_agua'], "style=\"width:150px\"");
					echo $form->inputText("nr_portaria_naturalizacao", $row['nr_portaria_naturalizacao'], "N&ordm; Portaria de Naturaliza&ccedil;&atilde;o", 20, 16, "onkeypress=\"return SomenteNumero(event)\"", "text", "N", "S");
					echo $form->inputText("dt_naturalizacao", formatarData($row['dt_naturalizacao']), "Data de Naturaliza&ccedil;&atilde;o", 20, 10, "onKeypress=\"return Ajusta_Data(this, event);\"");
					echo $form->inputText("dt_preenchimento_form", formatarData($row['dt_preenchimento_form']), "Data de Preench. Formul&aacute;rio", 20, 10, "onKeypress=\"return Ajusta_Data(this, event);\"");
					echo $form->inputText("usu_dt_entrada_pais", formatarData($row['usu_dt_entrada_pais']), "Data de Entrada no Pa&iacute;s", 20, 10, "onKeypress=\"return Ajusta_Data(this, event);\"");
					$selectCbor = "SELECT co_sgrp_cbo, 
										  ds_sgrp_cbo
								     FROM cbor
								    ORDER BY ds_sgrp_cbo";
					echo $form->inputSelect("usu_cbo_r", $usu_cbo_r, "Profiss&atilde;o", $selectCbor, null, null, $row['usu_cbo_r'], "style=\"width:650px\"", "SELECIONE", "style=\"width:650px\"");
					$selectRaca = "SELECT rac_codigo, 
										  rac_descricao
		  							 FROM raca
		 							ORDER BY rac_codigo";
					echo $form->inputSelect("rac_codigo", $rac_codigo, "Ra&ccedil;a", $selectRaca, null, null, $row['rac_codigo'], "style=\"width:150px\"");
					echo $form->inputText("nr_uso_municipal", $row['nr_uso_municipal'], "N&uacute;mero de uso Municipal", 20, 20, "onKeypress=\"return SomenteNumero(event);\"");
					$selectEscolaridade = "SELECT ecd_codigo, 
												  ecd_descricao
		  									 FROM escolaridade
		  									ORDER BY ecd_descricao";
					echo $form->inputSelect("usu_escolaridade", null, "Escolaridade", $selectEscolaridade, null, null, $row['usu_escolaridade'], "style=\"width:150px\"");
					$selectSitFamiliar = "SELECT sitf_codigo, 
												 sitf_descricao
											FROM situacao_familiar";
					echo $form->inputSelect("usu_sit_familiar", null, "Situa&ccedil;&atilde;o Familiar", $selectSitFamiliar, null, null, $row['usu_sit_familiar'], "style=\"width:150px\"");
					$arraySitEmprego = array("01"=>"Empregado","02"=>"Desempregado","03"=>"Aut&ocirc;nomo", "99"=>"Sem Informa&ccedil;&atilde;o");
					echo $form->inputSelect("usu_situacao_emprego", $arraySitEmprego, "Situa&ccedil;&atilde;o do Emprego", $usu_situacao_emprego, null, null, $row['usu_situacao_emprego'], "style=\"width:150px\"");
					echo $form->inputText("usu_cadastrador", $row['usu_cadastrador'], "C&oacute;digo do Cadastrador", 20, 6, "onKeypress=\"return SomenteNumero(event);\" onChange=\"return validaDigitoVerificador('usu_cadastrador')\"");
					echo $form->inputCheckboxRadio("usu_bolsa_alimentacao", $row['usu_bolsa_alimentacao'], "Bolsa Alimenta&ccedil;&atilde;o", null, $arraySimNao, "radio");			
					echo $form->inputCheckboxRadio("usu_bolsa_familia", $row['usu_bolsa_familia'], "Bolsa Fam&iacute;lia", null, $arraySimNao, "radio");			
					echo $form->inputCheckboxRadio("usu_prodea", $row['usu_prodea'], "Prodea", null, $arraySimNao, "radio");			
					echo $form->inputText("usu_renda_media", moeda($row['usu_renda_media']), "Renda M&eacute;dia", 20, 20, "onKeyDown=\"return formata_moeda(this, 18, event, 2);\"", "text", "N", "S");
					echo $form->inputText("usu_nr_lote", $row['usu_nr_lote'], "N&uacute;mero Lote", 20, 5);
					echo $form->textArea("usu_doencas", $row['usu_doencas'], "Doen&ccedil;as Pr&eacute;-existentes");
					echo $form->textArea("usu_habitos_vida", $row['usu_habitos_vida'], "H&aacute;bitos de Vida");
					$readOnly = ($row['usu_codigo'] != "" ? "S" : "");
					echo $form->textArea("usu_observacao", $row['usu_observacao'], "Observa&ccedil;&atilde;o", null, null, null, $readOnly);			
				*/echo $common->closeTab();
				echo $table->openTable();
					$width = array(210, 200);
					echo $table->criaLinha(array("<div align=right>".$common->commonButton("Voltar", "paciente_novo.php?id_login=$id_login&acao=", "voltar.png", null)."</div>", $common->commonButton("Salvar", null, "salvar.gif", "onClick=\"return validaCampos();\"")), $width);
				echo $table->closeTable();
				//echo $common->commonButton("Salvar", "#", "salvar.gif");
			echo $form->closeForm();			
			break;
		case "salvar":
			$usu_codigo = $_POST['usu_codigo'];
			$usu_prontuario_hidden = $_POST['usu_prontuario_hidden'];			
			$usu_nome = $_POST['usu_nome']; 
			$usu_datanasc = $_POST['usu_datanasc']; 
			$usu_mae = $_POST['usu_mae']; 
			$usu_pai = $_POST['usu_pai']; 
			$usu_sexo = $_POST['usu_sexo']; 
			$usu_estado_civil = $_POST['usu_estado_civil']; 
			$usu_conjuge = $_POST['usu_conjuge']; 
			$pais_codigo = $_POST['pais_codigo']; 
			$muni_cd_cod_ibge_nasc = $_POST['muni_cd_cod_ibge_nasc']; 
			$id_domicilio = $_POST['id_domicilio']; 
			$usu_rg = $_POST['usu_rg']; 
			$usu_rg_emissor = $_POST['usu_rg_emissor']; 
			$uf_sigla_rg = $_POST['uf_sigla_rg']; 
			$usu_rg_compl = $_POST['usu_rg_compl']; 
			$usu_rg_dt_emissao = $_POST['usu_rg_dt_emissao']; 
			$usu_email = $_POST['usu_email']; 
			$usu_celular = $_POST['usu_celular']; 
			$usu_fone = $_POST['usu_fone']; 
			$usu_cidade_nasc = $_POST['usu_cidade_nasc'];

			$usu_fone_recado = $_POST['usu_fone_recado']; 
			$usu_obito = $_POST['usu_obito']; 
			$usu_dt_obito = $_POST['usu_dt_obito']; 
			$usu_cpf = $_POST['usu_cpf']; 
			$usu_cnh_numero = $_POST['usu_cnh_numero']; 
			$usu_cnh_categoria = $_POST['usu_cnh_categoria']; 
			$usu_ctps = $_POST['usu_ctps']; 
			$usu_ctps_serie = $_POST['usu_ctps_serie']; 
			$usu_ctps_dt_emissao = $_POST['usu_ctps_dt_emissao']; 
			$uf_sigla_ctps = $_POST['uf_sigla_ctps']; 
			$usu_pis_pasep = $_POST['usu_pis_pasep']; 
			$usu_cartao_p_sus = $_POST['usu_cartao_p_sus']; 
			$usu_cartao_sus = $_POST['usu_cartao_sus']; 
			$usu_cert_emissao_nasc = $_POST['usu_cert_emissao_nasc']; 
			$usu_cert_cartorio_nasc = $_POST['usu_cert_cartorio_nasc']; 
			$usu_cert_livro_nasc = $_POST['usu_cert_livro_nasc']; 
			$usu_cert_lv_fls_nasc = $_POST['usu_cert_lv_fls_nasc']; 
			$usu_cert_termo_nasc = $_POST['usu_cert_termo_nasc']; 
			$usu_tipo_certidao = $_POST['usu_tipo_certidao']; 
			$usu_cert_emissao = $_POST['usu_cert_emissao']; 
			$usu_cert_cartorio = $_POST['usu_cert_cartorio']; 
			$usu_cert_livro = $_POST['usu_cert_livro']; 
			$usu_cert_lv_fls = $_POST['usu_cert_lv_fls']; 
			$usu_cert_termo = $_POST['usu_cert_termo']; 
			$usu_cert_civil_emissor = $_POST['usu_cert_civil_emissor']; 
			$usu_uf_cert_civil = $_POST['usu_uf_cert_civil']; 
			$usu_tit_eleitor = $_POST['usu_tit_eleitor']; 
			$usu_tit_eleitor_zona = $_POST['usu_tit_eleitor_zona']; 
			$usu_tit_eleitor_secao = $_POST['usu_tit_eleitor_secao']; 
			$usu_cisvir = $_POST['usu_cisvir']; 
			$usu_same = $_POST['usu_same']; 
			$usu_transporte_publico = $_POST['usu_transporte_publico']; 
			$usu_freq_escolar = $_POST['usu_freq_escolar']; 
			$uni_origem = $_POST['uni_origem']; 
			$uni_unidade = $_POST['uni_unidade']; 
			$usu_tipo_esgoto = $_POST['usu_tipo_esgoto'];
			$usu_tipo_const_casa = $_POST['usu_tipo_const_casa'];
			$usu_qtd_comodos = $_POST['usu_qtd_comodos'];
			$usu_rede_eletrica = $_POST['usu_rede_eletrica'];
			$nr_portaria_naturalizacao = $_POST['nr_portaria_naturalizacao']; 
			$dt_naturalizacao = $_POST['dt_naturalizacao']; 
			$dt_preenchimento_form = $_POST['dt_preenchimento_form']; 
			$usu_dt_entrada_pais = $_POST['usu_dt_entrada_pais']; 
			$usu_cbo_r = $_POST['usu_cbo_r']; 
			$rac_codigo = $_POST['rac_codigo']; 
			$nr_uso_municipal = $_POST['nr_uso_municipal']; 
			$ecd_codigo = $_POST['ecd_codigo']; 
			$usu_sit_familiar = $_POST['usu_sit_familiar']; 
			$usu_situacao_emprego = $_POST['usu_situacao_emprego']; 
			$usu_cadastrador = $_POST['usu_cadastrador']; 
			$usu_bolsa_alimentacao = $_POST['usu_bolsa_alimentacao']; 
			$usu_bolsa_familia = $_POST['usu_bolsa_familia']; 
			$usu_prodea = $_POST['usu_prodea']; 
			$usu_renda_media = moeda2($_POST['usu_renda_media']); 
			$usu_nr_lote = $_POST['usu_nr_lote']; 
			$usu_doencas = $_POST['usu_doencas']; 
			$usu_habitos_vida = $_POST['usu_habitos_vida']; 
			$usu_observacao = $_POST['usu_observacao']; 
			$id_login = $_REQUEST['id_login']; 
			$usr_cad_dt = $usr_alt_dt = date("d/m/Y");
			if (empty($usu_codigo)){
				/*$sql = " INSERT INTO usuario (usu_nome, 
													usu_datanasc, 
													usu_mae, 
													usu_pai, 
													usu_sexo, 
													usu_estado_civil, 
													usu_conjuge, 
													pais_codigo, 
													muni_cd_cod_ibge_nasc, 
													usu_rg, 
													usu_rg_emissor, 
													uf_sigla_rg, 
													usu_rg_compl, 
													usu_rg_dt_emissao, 
													usu_email, 
													usu_celular, 
													usu_fone, 
													usu_fone_recado, 
													usu_obito, 
													usu_dt_obito, 
													usu_cpf, 
													usu_cnh_numero, 
													usu_cnh_categoria, 
													usu_ctps, 
													usu_ctps_serie, 
													usu_ctps_dt_emissao, 
													uf_sigla_ctps, 
													usu_pis_pasep, 
													usu_cartao_p_sus, 
													usu_cartao_sus, 
													usu_cert_emissao_nasc, 
													usu_cert_cartorio_nasc, 
													usu_cert_livro_nasc, 
													usu_cert_lv_fls_nasc, 
													usu_cert_termo_nasc, 
													usu_tipo_certidao, 
													usu_cert_emissao, 
													usu_cert_cartorio, 
													usu_cert_livro, 
													usu_cert_lv_fls, 
													usu_cert_termo, 
													usu_cert_civil_emissor, 
													usu_uf_cert_civil, 
													usu_tit_eleitor, 
													usu_tit_eleitor_zona, 
													usu_tit_eleitor_secao, 
													usu_cisvir, 
													usu_same, 
													usu_transporte_publico, 
													usu_freq_escolar, 
													uni_origem, 
													uni_unidade, 
													usu_tipo_esgoto,
													usu_tipo_const_casa,
													usu_qtd_comodos,
													usu_rede_eletrica,
													nr_portaria_naturalizacao, 
													dt_naturalizacao, 
													dt_preenchimento_form, 
													usu_dt_entrada_pais, 
													usu_cbo_r, 
													rac_codigo, 
													nr_uso_municipal, 
													ecd_codigo, 
													usu_sit_familiar, 
													usu_situacao_emprego, 
													usu_cadastrador, 
													usu_bolsa_alimentacao, 
													usu_bolsa_familia, 
													usu_prodea, 
													usu_renda_media, 
													usu_nr_lote, 
													usu_doencas, 
													usu_habitos_vida, 
													usu_observacao, 
)
										    VALUES (upper('$usu_nome'), 
													'$usu_datanasc', 
													upper('$usu_mae'), 
													upper('$usu_pai'), 
													'$usu_sexo', 
													'$usu_estado_civil', 
													'$usu_conjuge', 
													'$pais_codigo', 
													'$muni_ibge_uf_nasc', 
													'$usu_rg', 
													".(intval($usu_rg_emissor) == 0 ? 'null' : $usu_rg_emissor).", 
													'$uf_sigla_rg', 
													'$usu_rg_compl', 
													".(intval($usu_rg_dt_emissao) == 0 ? 'null' : '$usu_rg_dt_emissao').", 
													'$usu_email', 
													'$usu_celular', 
													'$usu_fone', 
													'$usu_fone_recado', 
													'$usu_obito', 
													".(intval($usu_dt_obito) == 0 ? 'null' : '$usu_dt_obito').", 
													'$usu_cpf', 
													'$usu_cnh_numero', 
													'$usu_cnh_categoria', 
													'$usu_ctps', 
													'$usu_ctps_serie', 
													".(intval($usu_ctps_dt_emissao) == 0 ? 'null' : '$usu_ctps_dt_emissao').", 
													'$uf_sigla_ctps', 
													'$usu_pis_pasep', 
													'$usu_cartao_p_sus', 
													'$usu_cartao_sus', 
													'$usu_cert_emissao_nasc', 
													'$usu_cert_cartorio_nasc', 
													'$usu_cert_livro_nasc', 
													'$usu_cert_lv_fls_nasc', 
													'$usu_cert_termo_nasc', 
													".(intval($usu_tipo_certidao) == 0 ? 'null' : $usu_tipo_certidao).",
													".(intval($usu_cert_emissao) == 0 ? 'null' : '$usu_cert_emissao').",
													'$usu_cert_cartorio', 
													'$usu_cert_livro', 
													".(intval($usu_cert_lv_fls) == 0 ? 'null' : $usu_cert_lv_fls).",
													'$usu_cert_termo', 
													'$usu_cert_civil_emissor', 
													'$usu_uf_cert_civil', 
													'$usu_tit_eleitor', 
													'$usu_tit_eleitor_zona', 
													'$usu_tit_eleitor_secao', 
													'$usu_cisvir', 
													'$usu_same', 
													'$usu_transporte_publico', 
													'$usu_freq_escolar', 
													".(intval($uni_origem) == 0 ? 'null' : $uni_origem).",
													".(intval($uni_unidade) == 0 ? 'null' : $uni_unidade).",
													'$usu_tipo_esgoto',
													'$usu_tipo_const_casa',
													'$usu_qtd_comodos',
													'$usu_rede_eletrica',
													'$nr_portaria_naturalizacao', 
													".(intval($dt_naturalizacao) == 0 ? 'null' : '$dt_naturalizacao').",
													".(intval($dt_preenchimento_form) == 0 ? 'null' : '$dt_preenchimento_form').",
													".(intval($usu_dt_entrada_pais) == 0 ? 'null' : '$usu_dt_entrada_pais').",
													".(intval($usu_cbo_r) == 0 ? 'null' : $usu_cbo_r).",
													".($rac_codigo == "" ? 'null' : $rac_codigo).",
													'$nr_uso_municipal', 
													".(intval($ecd_codigo) == 0 ? 'null' : $ecd_codigo).",
													'$usu_sit_familiar', 
													'$usu_situacao_emprego', 
													'$usu_cadastrador', 
													'$usu_bolsa_alimentacao', 
													'$usu_bolsa_familia', 
													'$usu_prodea', 
													".(intval($usu_renda_media) == 0 ? 'null' : $usu_renda_media).",
													'$usu_nr_lote', 
													'$usu_doencas', 
													'$usu_habitos_vida', 
													'$usu_observacao', 
													'643', 
													'$usr_cad_dt')";

*/
if(empty($usu_prontuario)) {
 $p = pg_fetch_array(pg_query(" select last_value from seq_usu_codigo" ));
 $pront = $p[last_value];
} else {
 $pront = $usu_prontuario;
}
$name = strtoupper($usu_nome);
$mae = strtoupper($usu_mae);
$sqq = pg_query("select *from usuario where usu_nome ilike '%".$name."%' AND usu_mae ilike '%".$mae."%' AND usu_datanasc ='".$usu_datanasc."'") or die(pg_last_error());
$sel = pg_num_rows($sqq);
if($sel>=1) {
	echo "<br><br><br><br><br><Br><br><br><br><br><br><br><br><br><Br><center><font size=5 color=red>USUARIO JA EXISTE FAVOR VERIFICAR</center>";
	 ?><script> setTimeout("location='paciente_novo.php?id_login=$id_login&acao=form'", 2000); </script> <? 
	exit;
}
$sql = "insert into usuario (usu_fone,usu_nome,usu_mae,usu_datanasc,usu_cartao_sus,usu_prontuario,usu_sexo,usu_cidade_nasc) values('$usu_fone',upper('$usu_nome'),upper('$usu_mae'),'$usu_datanasc','$usu_cartao_sus','$pront','$usu_sexo',upper('$usu_cidade_nasc'))";



			}else{
$sql = "update usuario set 
		usu_nome = upper('$usu_nome'),
		usu_mae = upper('$usu_mae'),
		usu_datanasc = '$usu_datanasc',
		usu_cartao_sus = '$usu_cartao_sus',
		usu_prontuario = '$usu_prontuario',
		usu_fone = '$usu_fone',
		usu_sexo = '$usu_sexo',
		usu_cidade_nasc = '$usu_cidade_nasc'
		WHERE usu_codigo = ".intval($usu_codigo);
				
				
				
				/*$sql =" UPDATE usuario 
						   SET usu_prontuario = '$usu_prontuario', 
							   usu_nome = upper('$usu_nome'), 
							   usu_datanasc = '$usu_datanasc', 
							   usu_mae = upper('$usu_mae'), 
							   usu_pai = upper('$usu_pai'), 
							   usu_sexo = '$usu_sexo', 
							   usu_estado_civil = '$usu_estado_civil', 
							   usu_conjuge = '$usu_conjuge', 
							   pais_codigo = '$pais_codigo', 
							   muni_cd_cod_ibge_nasc = '$muni_cd_cod_ibge_nasc', 
							   usu_rg = '$usu_rg', 
							   usu_rg_emissor = ".(intval($usu_rg_emissor) == 0 ? 'null' : $usu_rg_emissor).", 
							   uf_sigla_rg = '$uf_sigla_rg', 
							   usu_rg_compl = '$usu_rg_compl', 
							   usu_rg_dt_emissao = ".(intval($usu_rg_dt_emissao) == 0 ? 'null' : "'".$usu_rg_dt_emissao."'").", 
							   usu_email = '$usu_email', 
							   usu_celular = '$usu_celular', 
							   usu_fone = '$usu_fone', 
							   usu_fone_recado = '$usu_fone_recado', 
							   usu_obito = '$usu_obito', 
							   usu_dt_obito = ".(intval($usu_dt_obito) == 0 ? 'null' : "'".$usu_dt_obito."'").", 
							   usu_cpf = '$usu_cpf', 
							   usu_cnh_numero = '$usu_cnh_numero', 
							   usu_cnh_categoria = '$usu_cnh_categoria', 
							   usu_ctps = '$usu_ctps', 
							   usu_ctps_serie = '$usu_ctps_serie', 
							   usu_ctps_dt_emissao = ".(intval($usu_ctps_dt_emissao) == 0 ? 'null' : "'".$usu_ctps_dt_emissao."'").", 
							   uf_sigla_ctps = '$uf_sigla_ctps', 
							   usu_pis_pasep = '$usu_pis_pasep', 
							   usu_cartao_p_sus = '$usu_cartao_p_sus', 
							   usu_cartao_sus = '$usu_cartao_sus', 
							   usu_cert_emissao_nasc = '$usu_cert_emissao_nasc', 
							   usu_cert_cartorio_nasc = '$usu_cert_cartorio_nasc', 
							   usu_cert_livro_nasc = '$usu_cert_livro_nasc', 
							   usu_cert_lv_fls_nasc = '$usu_cert_lv_fls_nasc', 
							   usu_cert_termo_nasc = '$usu_cert_termo_nasc', 
							   usu_tipo_certidao = ".(intval($usu_tipo_certidao) == 0 ? 'null' : $usu_tipo_certidao).", 
							   usu_cert_emissao = ".(intval($usu_cert_emissao) == 0 ? 'null' : "'".$usu_cert_emissao."'").", 
							   usu_cert_cartorio = '$usu_cert_cartorio', 
							   usu_cert_livro = '$usu_cert_livro', 
							   usu_cert_lv_fls = ".(intval($usu_cert_lv_fls) == 0 ? 'null' : $usu_cert_lv_fls).", 
							   usu_cert_termo = '$usu_cert_termo', 
							   usu_cert_civil_emissor = '$usu_cert_civil_emissor', 
							   usu_uf_cert_civil = '$usu_uf_cert_civil', 
							   usu_tit_eleitor = '$usu_tit_eleitor', 
							   usu_tit_eleitor_zona = '$usu_tit_eleitor_zona', 
							   usu_tit_eleitor_secao = '$usu_tit_eleitor_secao', 
							   usu_cisvir = '$usu_cisvir', 
							   usu_same = '$usu_same', 
							   usu_transporte_publico = '$usu_transporte_publico', 
							   usu_freq_escolar = '$usu_freq_escolar', 
							   uni_origem = ".(intval($uni_origem) == 0 ? 'null' : $uni_origem).", 
							   uni_unidade = ".(intval($uni_unidade) == 0 ? 'null' : $uni_unidade).",
							   usu_tipo_esgoto = '$usu_tipo_esgoto',
							   usu_tipo_const_casa = '$usu_tipo_const_casa',
							   usu_qtd_comodos = '$usu_qtd_comodos',
							   usu_rede_eletrica = '$usu_rede_eletrica',
							   nr_portaria_naturalizacao = '$nr_portaria_naturalizacao', 
							   dt_naturalizacao = ".(intval($dt_naturalizacao) == 0 ? 'null' : "'".$dt_naturalizacao."'").", 
							   dt_preenchimento_form = ".(intval($dt_preenchimento_form) == 0 ? 'null' : "'".$dt_preenchimento_form."'").", 
							   usu_dt_entrada_pais = ".(intval($usu_dt_entrada_pais) == 0 ? 'null' : "'".$usu_dt_entrada_pais."'").", 
							   usu_cbo_r = ".(intval($usu_cbo_r) == 0 ? 'null' : $usu_cbo_r).", 
							   rac_codigo = ".($rac_codigo == "" ? 'null' : $rac_codigo).", 
							   nr_uso_municipal = '$nr_uso_municipal', 
							   ecd_codigo = ".(intval($ecd_codigo) == 0 ? 'null' : $ecd_codigo).", 
							   usu_sit_familiar = '$usu_sit_familiar', 
							   usu_situacao_emprego = '$usu_situacao_emprego', 
							   usu_cadastrador = '$usu_cadastrador', 
							   usu_bolsa_alimentacao = '$usu_bolsa_alimentacao', 
							   usu_bolsa_familia = '$usu_bolsa_familia', 
							   usu_prodea = '$usu_prodea', 
							   usu_renda_media = ".(intval($usu_renda_media) == 0 ? 'null' : $usu_renda_media).", 
							   usu_nr_lote = '$usu_nr_lote', 
							   usu_doencas = '$usu_doencas', 
							   usu_habitos_vida = '$usu_habitos_vida', 
							   usu_observacao = '$usu_observacao', 
							   usr_alt = '643', 
							   usr_alt_dt = '$usr_alt_dt' 
						 WHERE usu_codigo = ".intval($usu_codigo) ;
						 */
			}
//die($sql);
			$executa = pg_query($sql) or die (pg_last_error());
			if ($executa){
				echo $common->modalMsg("OK", "Usu&aacute;rio salvo com sucesso!", "paciente_novo.php?id_login=$id_login");
				//echo "Usu�rio salvo com sucesso!";
			}
			break;
		case "del":
			echo $common->modalConfirm("Deseja realmente apagar o paciente selecionado?", "paciente_novo.php?id_login=$id_login&acao=deletar&usu_codigo=$usu_codigo", "paciente_novo.php?id_login=$id_login&acao=&usu_codigo=$usu_codigo");
			break;
		case "deletar":
			$delete = "UPDATE usuario
						  SET usu_ativacao = 'N',
						  	  usu_dt_ativacao_desativacao = CURRENT_DATE 
						WHERE usu_codigo = ".intval($usu_codigo);
			$execDel = pg_query($delete) or die($delete);
			if ($execDel){
				echo $common->modalMsg("OK", "Paciente desativado com sucesso", "paciente_novo.php?id_login=$id_login&acao=&usu_codigo=$usu_codigo");
			}else{
				echo $common->modalMsg("ERRO", "Houve um erro durante a desativa&ccedil;&atilde;o do paciente, tente novamente.", "paciente_novo.php?id_login=$id_login&acao=&usu_codigo=$usu_codigo");
			}
			break;
	}
?>
