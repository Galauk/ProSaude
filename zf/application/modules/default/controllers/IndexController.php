<?php

class IndexController extends Zend_Controller_Action {

	public function init() {
		$this->_helper->acl->allow(NULL);
		$this->view->title = "Página Inicial";
	}

	public function indexAction() {
        $session = new Zend_Session_Namespace();
        include_once $_SESSION['root'].$_SESSION['modulo'].'__array.php';
        $this->view->tipoAtendimento = json_encode($arrayTipoAtendimento);

        $this->view->headScript()->appendFile('https://www.gstatic.com/charts/loader.js');
		$this->_helper->layout->setLayout("simples");

        $hoje = date('d/m/Y');
        $seisMesesAtras = date('d/m/Y', strtotime('26 months ago'));
        $mesAtual = date('m/Y');

        //recupera o usuario logado
        $tbUsu = new Application_Model_Usuarios();
        $usu = $tbUsu->getUsrAtual();

        //recupera os profissionais
        $tbConi = new Application_Model_ConvenioItens();
        $profissionais = $tbConi->getNomeProfissionaisPorUnidadeConveniado($usu->uni_codigo)->toArray();
        $profissionaisIds = [];
        foreach($profissionais as $profissional){
            $profissionaisIds[] = $profissional['usr_codigo'];
        }

        if(!empty($profissionaisIds)){
            //lista a agenda do dia
            $tbAge = new Application_Model_Agendamento();
            $this->view->agendaDoDia = $tbAge->getPacientesAgendados($usu->uni_codigo, $profissionaisIds, false, $hoje)->toarray();
        } else {
            $this->view->agendaDoDia = [];
        }

        $queryProcedimentos = $tbUsu->select(FALSE)
            ->setIntegrityCheck(FALSE)
            ->from(array("ate" => "atendimento"), ['total'=>"count(ate.ate_codigo)", 'ate.ate_tipo', "to_char(date(ate.ate_data), 'YYYYMM') as mes" ])
            ->join(array("uni" => "unidade"), "uni.uni_codigo=ate.uni_codigo", FALSE)
            ->where("ate_tipo != ''")
            ->where("ate.ate_data >= '$seisMesesAtras'")
            ->where("ate.ate_data <= '$hoje'")
            ->where("uni.uni_codigo = ?", $usu->uni_codigo)
            ->group(["ate_tipo", "mes"])
            ->order("ate_tipo");
        $procedimentos = $tbUsu->fetchAll($queryProcedimentos)->toArray();
        $resultado = [];
        foreach ($procedimentos as $procedimento) {
            $resultado[$procedimento['mes']][$procedimento['ate_tipo']] = $procedimento['total'];
        }

        $this->view->procedimentos = json_encode($resultado);



        $where1 = $tbUsu->select(FALSE)
            ->setIntegrityCheck(FALSE)
            ->from(['pat'=>'procedimento_atendimento'], ['total'=>'count(pat.proc_codigo)', 'esp_nome'=>'trim(retira_acentos(esp.esp_nome))'])
            ->join(['pc'=>'pre_consulta'], 'pc.pc_codigo=pat.pc_codigo', FALSE)
            ->join(['age'=>'agendamento'], 'age.age_codigo=pc.age_codigo', FALSE)
            ->join(['mes'=>'medico_especialidade'], 'mes.med_codigo=pc.usr_codigo', FALSE)
            ->join(['esp'=>'especialidade'], 'esp.esp_codigo=mes.esp_codigo', FALSE)
            ->where("age.uni_codigo = ?", $usu->uni_codigo)
            ->where("to_char(age.age_data,'mm/yyyy') = '$mesAtual'")
            ->group(["esp.esp_nome"]);

        $where2 = $tbUsu->select(FALSE)
            ->setIntegrityCheck(FALSE)
            ->from(['pat'=>'procedimento_atendimento'], ['total'=>'count(pat.proc_codigo)', 'esp_nome'=>'trim(retira_acentos(esp.esp_nome))'])
            ->join(['ate'=>'atendimento'], 'ate.ate_codigo=pat.ate_codigo', FALSE)
            ->join(['age'=>'agendamento'], 'age.age_codigo=ate.age_codigo', FALSE)
            ->join(['esp'=>'especialidade'], 'esp.esp_codigo=age.esp_codigo', FALSE)
            ->where("age.uni_codigo = ?", $usu->uni_codigo)
            ->where("to_char(age.age_data,'mm/yyyy') = '$mesAtual'")
            ->group(["esp.esp_nome"]);

                
        $where = $tbUsu->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->union(array($where1,$where2), Zend_Db_Select::SQL_UNION_ALL);               

        $var = $tbUsu->fetchAll($where)->toArray();
        $enc = json_encode($var);

        $this->view->especialidades = $enc;

	}

}