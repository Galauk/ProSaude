<?php

class CadastroFamiliarController extends Zend_Controller_Action {

	public function init(){
		$this->_helper->acl->allow(NULL);
	}

	public function indexAction() {
		$this->view->title = "Familia";
		$recebeGrauParentesco = array();
		$tbGrauParentesco = new Application_Model_TbGrauParentesco();
		$familias = new Application_Model_CadastroFamiliar();

		$recebeGrauParentesco = $tbGrauParentesco->getDescricao();
		$recebeFamilias = $familias->recuperaFamilias();

		$this->view->recebeGrauParentesco = $recebeGrauParentesco;
		$this->view->recebeFamilias = $recebeFamilias;
	}

	public function salvarAction(){
		$this->_helper->layout->disableLayout();
		$familia = array();
		$tbCadastroFamiliar = new Application_Model_CadastroFamiliar(); 
		$dadosDaFamilia = $tbCadastroFamiliar->salvar($familia);
		echo json_decode($dadosDaFamilia);
		exit();
		// echo "<pre>";print_r($dadosDaFamilia);die();
		// $this->view->dadosDaFamilia = $dadosDaFamilia;
		return $this->render("dados", NULL, TRUE);

	}

	public function excluirFamiliaAction(){
		$this->_helper->layout->disableLayout();
		$recebeProntuarioFamiliar = $this->_getParam("numeroDoProntuarioFamiliar",null);
		$tbCadastroFamiliar = new Application_Model_CadastroFamiliar();
		$tbComposicaoFamiliar = new Application_Model_TbComposicaoFamiliar();

		$apagarMembrosDaFamilia = $tbComposicaoFamiliar->excluirMembrosDaFamilia($recebeProntuarioFamiliar);
		$apagarFamilia = $tbCadastroFamiliar->excluirFamilia($recebeProntuarioFamiliar);
		// echo "<pre>";print_r($apagarFamilia);die();
		exit();
		return $this->render("dados", NULL, TRUE);
	}

	public function salvarResponsavelFamiliarAction(){
		$this->_helper->layout->disableLayout();
		$responsavelFamiliar = True;
		$recebeDadosDoFormulario = $this->_getParam("formulario");
		$recebeProntuarioFamiliar = $this->_getParam("numeroDoProntuarioFamiliar");
		$idDoPaciente = $this->_getParam("codigoUsuario");

		$converteValor =  str_replace('.', "",$recebeDadosDoFormulario[rendaMensal]);
		$converteValor =  str_replace(',', ".",$converteValor);

		$tbComposicaoFamiliar = new Application_Model_TbComposicaoFamiliar();

		// echo "<pre>";var_dump($converteValor);die();

		$dadosDaComposicaoFamiliar = array(
			"tcomf_renda_mensal_usuario" => $converteValor,
			"tgp_grau_parentesco" => $recebeDadosDoFormulario[descricaoDoGrauParentesco],
			"tcf_numero_prontuario_familiar" => $recebeProntuarioFamiliar,
			"usu_codigo" => $idDoPaciente,
			"tcomf_responsavel" => $responsavelFamiliar
		);

		$tbComposicaoFamiliar->salvar($dadosDaComposicaoFamiliar);

		return $this->render("dados", NULL, TRUE);

	}

	public function salvarMembroAction(){
		$this->_helper->layout->disableLayout();
		
		$recebeDadosDoFormulario = $this->_getParam("formulario");
		$recebeProntuarioFamiliar = $this->_getParam("numeroDoProntuarioFamiliar");
		$idDoPaciente = $this->_getParam("codigoUsuario");

		$converteValor =  str_replace('.', "",$recebeDadosDoFormulario[rendaMensalDoNovoMembro]);
		$converteValor =  str_replace(',', ".",$converteValor);

		$tbComposicaoFamiliar = new Application_Model_TbComposicaoFamiliar();


		$dadosDaComposicaoFamiliar = array(
			"tcomf_renda_mensal_usuario" => $converteValor,
			"tgp_grau_parentesco" => $recebeDadosDoFormulario[descricaoDoGrauParentesco],
			"tcf_numero_prontuario_familiar" => $recebeProntuarioFamiliar,
			"usu_codigo" => $idDoPaciente
		);

		$tbComposicaoFamiliar->salvar($dadosDaComposicaoFamiliar);
		
		return $this->render("dados", NULL, TRUE);
	}

	public function salvarMembroBotaoAction(){
		$this->_helper->layout->disableLayout();
		
		$recebeDadosDoFormulario = $this->_getParam("formulario");
		$recebeProntuarioFamiliar = $this->_getParam("numeroDoProntuarioFamiliar");
		$idDoPaciente = $this->_getParam("codigoUsuario");

		$converteValor =  str_replace('.', "",$recebeDadosDoFormulario[rendaMensalBotao]);
		$converteValor =  str_replace(',', ".",$converteValor);


		$tbComposicaoFamiliar = new Application_Model_TbComposicaoFamiliar();

		$rendaTotalFamiliar = $tbComposicaoFamiliar->recuperaRendaTotalFamiliar($recebeProntuarioFamiliar);
		$numeroTotalDeMembros = $tbComposicaoFamiliar->recuperaNumeroTotalDeMembros($recebeProntuarioFamiliar);

		$dadosDaComposicaoFamiliar = array(
			"tcomf_renda_mensal_usuario" => $converteValor,
			"tgp_grau_parentesco" => $recebeDadosDoFormulario[descricaoDoGrauParentescoBotao],
			"tcf_numero_prontuario_familiar" => $recebeProntuarioFamiliar,
			"usu_codigo" => $idDoPaciente
		);

		$novaRenda = array(
			"tcf_renda_familiar" => $rendaTotalFamiliar[sum],
			"tcf_numero_membros" => $numeroTotalDeMembros[count]
		);

		$tbComposicaoFamiliar->salvar($dadosDaComposicaoFamiliar);
		
		return $this->render("dados", NULL, TRUE);
	}

	public function atualizarRendaFamiliarENumeroDeMembrosAction(){
		$this->_helper->layout->disableLayout();
		$recebeProntuarioFamiliar = $this->_getParam("numeroDoProntuarioFamiliar");
		
		$tbComposicaoFamiliar = new Application_Model_TbComposicaoFamiliar();
		$tbCadastroFamiliar = new Application_Model_CadastroFamiliar();

		$rendaTotalFamiliar = $tbComposicaoFamiliar->recuperaRendaTotalFamiliar($recebeProntuarioFamiliar);
		$numeroTotalDeMembros = $tbComposicaoFamiliar->recuperaNumeroTotalDeMembros($recebeProntuarioFamiliar);

		$novaRenda = array(
			"tcf_renda_familiar" => $rendaTotalFamiliar[sum],
			"tcf_numero_membros" => $numeroTotalDeMembros[count]
		);

		$tbCadastroFamiliar->salvarRendaENumeroDeMembros($novaRenda, $recebeProntuarioFamiliar);

		return $this->render("dados", NULL, TRUE);
	}

	public function carregarFormularioDosDemaisMembrosAction(){
		// error_reporting(E_ALL);
		$this->_helper->layout->disableLayout();
		$grauParentesco = new Application_Model_TbGrauParentesco();
		$recebeGrauParentesco = $grauParentesco->getDescricao();

		echo'
			<form id="formularioDosNovosIntegrantes" class="" name="formularioDosNovosIntegrantes">
    			<fieldset class="fieldsetDoCadastro" >
	       			<div class="divFormularioFamiliar">
	            		<label for="novoMembro">Novo Integrante : </label>
						<input onkeypress="buscarMembro(this)" type="text" id="novoMembro" name="novoMembro" class="inputsFormularioFamiliar" >
	            		<input type="hidden" value="" id="usuCodigoNovoMembro" name="usuCodigoNovoMembro" class="usuCodigoNovoMembro">
	        		</div> 

			        <div class="divFormularioFamiliar">
			            <select name="descricaoDoGrauParentesco" id="descricaoDoGrauParentesco">';
			                    foreach ($recebeGrauParentesco as $grauParentesco) { 
			                        echo "<option id='$grauParentesco[tgp_codigo]' name='$grauParentesco[tgp_codigo]' value='$grauParentesco[tgp_codigo]'> $grauParentesco[tgp_descricao]
			                            
			                        </option>";
			                	}               
			            echo ' </select>
			        </div>
        
			        <div class="divFormularioFamiliar">
			            <label for="rendaMensalDoNovoMembro">Renda Mensal : </label>
			            <input onkeypress="return(moeda(this, \'.\', \',\', event))" type="text" id="rendaMensalDoNovoMembro" name="rendaMensalDoNovoMembro" class="inputsFormularioFamiliar" >
			        </div>

        			<button style="
							padding: 8px 13px 4px;
		    				background-image: url(/WebSocialSaude/zf/public/images/btn-bg.png);
		    				background-repeat: round;
		    				background-size: contain;
		    				border: 1px solid #ACACAC;
							margin-bottom: 3px;
		    				color: #000;
		    				border-radius: 5px;
	    				"  
        			id="botaoSalvarMembro" name="botaoSalvarMembro"type="button"onclick="salvarNovoMembroFamiliar($(this).parent().parent())">Salvar membro</button>

    			</fieldset>
			</form> 
			<br>		
		';
		exit();
		return $this->render("dados", NULL, TRUE);
	}

	public function carregarFormularioDosDemaisMembrosBotaoAction(){
		// error_reporting(E_ALL);
		$this->_helper->layout->disableLayout();
		$grauParentesco = new Application_Model_TbGrauParentesco();
		$recebeGrauParentesco = $grauParentesco->getDescricao();
		
		echo'
			<form id="formularioDosNovosIntegrantes" class="" name="formularioDosNovosIntegrantes">
    			<fieldset class="fieldsetDoCadastro" >
	       			<div class="divFormularioFamiliar">
	            		<label for="novoMembro">Novo Integrante : </label>
						<input onkeypress="buscarMembro(this)" type="text" id="novoMembro" name="novoMembro" class="inputsFormularioFamiliar" >
	            		<input type="hidden" value="" id="usuCodigoNovoMembro" name="usuCodigoNovoMembro" class="usuCodigoNovoMembro">
	        		</div> 

			        <div class="divFormularioFamiliar">
			            <select class = "descricaoDoGrauParentescoBotaoPHP" name="descricaoDoGrauParentescoBotao" id="descricaoDoGrauParentescoBotao">';
			                    foreach ($recebeGrauParentesco as $grauParentesco) { 
			                        echo "<option id='$grauParentesco[tgp_codigo]' name='$grauParentesco[tgp_codigo]' value='$grauParentesco[tgp_codigo]'> $grauParentesco[tgp_descricao]
			                            
			                        </option>";
			                	}               
			            echo ' </select>
			        </div>
        
			        <div class="divFormularioFamiliar">
			            <label for="rendaMensalBotao">Renda Mensal : </label>
			            <input onkeypress="return(moeda(this, \'.\', \',\', event))" type="text" id="rendaMensalBotao" name="rendaMensalBotao" class="inputsFormularioFamiliar" >
			        </div>
					
			        <a href="#" 
			        	style="
							padding: 8px 13px 4px;
		    				background-image: url(/WebSocialSaude/zf/public/images/btn-bg.png);
		    				background-repeat: round;
		    				background-size: contain;
		    				border: 1px solid #ACACAC;
							margin-bottom: 3px;
		    				color: #000;
		    				border-radius: 5px;
	    				"
	    				onclick="salvarNovoMembroFamiliarBotao($(this).parent().parent())" class="ui-button novo ui-corner-bl ui-corner-tr">Adicionar Integrante</a>

    			</fieldset>
			</form> 
			<br>
		';
		exit();
		return $this->render("dados", NULL, TRUE);
	}

	public function carregarIntegrantesAction(){
		$this->_helper->layout->disableLayout();
		$recebeIntegrantes = array();
		$recebe_prontuario_familiar = $this->_getParam("recebe_prontuario_familiar");
		$tbComposicaoFamiliar = new Application_Model_TbComposicaoFamiliar();

		$recebeIntegrantes = $tbComposicaoFamiliar->getIntegrantes($recebe_prontuario_familiar);

		echo json_encode($recebeIntegrantes);
		exit();
		return $this->render("dados", NULL, TRUE);

	}

	public function decrementaRendaFamiliarAction(){
		$recebeProntuarioFamiliar = $this->_getParam("prontuarioFamiliar");
		$recebeCodigoIntegrante = $this->_getParam("codigoIntegrante");

		$tbCadastroFamiliar = new Application_Model_CadastroFamiliar();
		$tbComposicaoFamiliar = new Application_Model_TbComposicaoFamiliar();

		$recebeRendaFamiliar = $tbCadastroFamiliar->recuperaRendaFamiliar($recebeProntuarioFamiliar);
		$recebeRendaIndividual = $tbComposicaoFamiliar->recuperaRendaIndividual($recebeCodigoIntegrante);

		$dados = $tbCadastroFamiliar->decrementaRendaFamiliar($recebeRendaFamiliar, $recebeRendaIndividual, $recebeProntuarioFamiliar);
		
		$tbCadastroFamiliar->excluirIntegrante($recebeCodigoIntegrante);

		exit();
	}

	public function apagarComposicaoFamiliarAction(){
		$recebeProntuarioFamiliar = $this->_getParam("prontuarioFamiliar");

		$tbCadastroFamiliar = new Application_Model_CadastroFamiliar();

		$tbCadastroFamiliar->excluirComposicaoFamiliar($recebeProntuarioFamiliar);

		exit();
	}
}
