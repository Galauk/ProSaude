<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_Agendamento extends Elotech_Db_Table_Abstract {

    protected $_name = 'agendamento';
    protected $_primary = 'age_codigo';
    protected $_sequence = 'seq_age_codigo';
   // protected $_dependentTables = array('Atendimento');
    protected $_referenceMap = array(
        'Medico' => array(
            'columns' => 'med_codigo',
            'refTableClass' => 'Usuarios',
            'refColumns' => 'usr_codigo'
        ),
        'Paciente' => array(
            'columns' => 'usu_codigo',
            'refTableClass' => 'Usuario',
            'refColumns' => 'usu_codigo'
        ),
        'Unidade' => array(
            'columns' => 'uni_codigo',
            'refTableClass' => 'Unidade',
            'refColumns' => 'uni_codigo'
        ),
        'Especialidade' => array(
            'columns' => 'esp_codigo',
            'refTableClass' => 'Especialidade',
            'refColumns' => 'esp_codigo'
        ),
        'UsrCad' => array(
            'columns' => 'usr_codigo_cad',
            'refTableClass' => 'Usuarios',
            'refColumns' => 'usr_codigo'
        ),
        'UsrAlt' => array(
            'columns' => 'usr_codigo_alt',
            'refTableClass' => 'Usuarios',
            'refColumns' => 'usr_codigo'
        )
    );
    /*-----------------------------------------------------------------------/
     * OBS: Este método também é utilizado para salvar os seguintes modúlos: 
     * Atendimento Simplificado 
     * ----------------------------------------------------------------------*/
    public function salvar(array $data) {
        $tbUsr = new Application_Model_Usuarios();
        if (!empty($data['age_codigo'])) {
            $data['dt_atualizacao'] = date("Y-m-d H:i:s");
            $data['usr_codigo_alt'] = $tbUsr->getUsrAtual()->usr_codigo;
        } else {
            $data['dt_cadastro'] = date("Y-m-d H:i:s");
            $data['usr_codigo_cad'] = $tbUsr->getUsrAtual()->usr_codigo;
        }
        
        if(!$data['age_codigo']){
            $this->notEmpty(array("usu_codigo","uni_codigo","esp_codigo","coni_codigo","age_data","age_horario"), $data);
        }
        
        $this->emptyToUnset($data);
        
        try {
            $age_codigo = parent::salvar($data);
        } catch (Exception $exc) {
            throw new Zend_Validate_Exception("Falha ao cadastrar o agendamento: ".$exc->getMessage());
        }
        return $age_codigo;
    }
    
    public function salvarAgendamento($data){
        try {
            $age_codigo = parent::salvar($data);
        } catch (Exception $exc) {
            throw new Zend_Validate_Exception("Falha ao cadastrar o agendamento: ".$exc->getMessage());
        }
        return $age_codigo;
    } 
        

    /**
     * Verifica se já há um paciente sendo atendido*
     * @return object|bool 
     */
    static public function usuEmAberto() {
        $_p = new Zend_Session_Namespace("prontuario");
        //echo "<pre>".print_r($_p);die();
        if (isset($_p->age))
            return $_p->age; // TRUE
        else
            return FALSE;
    }

    static public function cancelarAgendaAtual() {
        $_p = new Zend_Session_Namespace("prontuario");
        $_p->unsetAll();
    }

    public function iniciar($age_codigo) {		 
        $_p = new Zend_Session_Namespace("prontuario");
        $tbInt = new Application_Model_AtendimentoInternacao();
        $_p->age = (object) $tbInt->getInternacaoEAgendamento($age_codigo)->current()->toArray();
        //echo "<pre>".print_r($_SESSION,1)."</pre>";die();
        //echo "<pre>".print_r($tbInt->getInternacaoEAgendamento($age_codigo)->current()->toArray(),1);die();
        //return $_p->age;
    }

    public function finalizar($age_codigo=FALSE) {
        if (!$age_codigo)
            $age_codigo = $this->usuEmAberto()->age_codigo;
        $tbUsr = new Application_Model_Usuarios();
        $age = $this->usuEmAberto();
        $usr = $tbUsr->getUsrAtual();
        if ($tbUsr->fazPreConsulta() && $usr->usr_codigo != $age->med_codigo){
            $tbAte = new Application_Model_Atendimento();
            $ate = $tbAte->temAtendimento($age_codigo);
            $tbPre = new Application_Model_PreConsulta();
            $pc_codigo = $tbPre->buscar($age_codigo);
            /*$dadosPc = array("pc_codigo"=>$pc_codigo[pc_codigo],
                             "pc_hora_final"=>"NOW()"
                             );
            $tbPre->salvar($dadosPc);*/
            if(!$ate[ate_codigo]){
                $this->alteraSituacao("P", $age_codigo); // P = saiu da 'Pre-Consulta'
            }else{
                $this->alteraSituacao("A",$age_codigo);
            }
        }else{
            $this->alteraSituacao("A", $age_codigo,TRUE); // A = Atendido
        }
        
    }

    public function alteraSituacao($age_atendido, $age_codigo=FALSE, $reset=TRUE){
        if (!$age_codigo){
            $age_codigo = $this->usuEmAberto()->age_codigo;
        }
        $age = $this->find($age_codigo)->current();
        $age->age_atendido = $age_atendido;
        $age->save();
        // Validando se o paciente foi recepcionado, coloca a hora que chegou
        if ($age_atendido=="S" && $age_codigo != "") {
            $dadosAgeHora = array(
                "age_codigo" => $age_codigo,
                "age_data_atend" => date("Y-m-d H:i:s")
            );
            $this->salvarAgendamento($dadosAgeHora);
        }
        if ($age_atendido=="N" && $age_codigo != "") {
            $dadosAgeHora = array(
                "age_codigo" => $age_codigo,
                "age_data_atend" => null
            );
            $this->salvarAgendamento($dadosAgeHora);
        }
        // se o atendimento for finalizado (A), registrar a data/hora final
        if($age_atendido == "A"){
            $tbAte = new Application_Model_Atendimento();
            /* LÓGICA ANTIGA, NÃO FUNCIONAVA POIS ATIVA A VARIÁVEL GAMBI
            * E NÃO COMPARA PELOS STATUS CORRETO
            * $ate = $tbAte->temAtendimento($age_codigo,"S");
            */
            $ate = $tbAte->temAtendimentoAgendamento($age_codigo)->ate_codigo;          
            if($ate){
                $dadosAte = array(
                    "ate_codigo" => $ate,
                    "ate_datafinal" => date("Y-m-d"),
                    "ate_horafinal" => date("H:i")
                );
                $tbAte->salvarAtendimento($dadosAte);
                /* LÓGICA ANTIGA, NÃO ATUALIZAVA A HORA E A DATA
                $ate->ate_datafinal = date("Y-m-d");
                $ate->ate_horafinal = date("H:i");
                $ate->save(); // isso não passa pelo salvar()... wherever*/
            }                   
        }
        if ($reset) {       
            // limpa a session();
            $this->cancelarAgendaAtual();
        } else {
            // ou atualiza a session
            $this->usuEmAberto()->age_atendido = $age_atendido;
        }
        return true;
    }
    
    // Pega dados unidade, usuarios, usuarios e os dados do seu domicilio
    public function getDadosAgendamentoUsuario($ageCodigo = false){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("age"=>"agendamento"),array("age_codigo","tat_codigo","age_paciente","esp_codigo"))
                    ->join(array("uni"=>"unidade"),"age.uni_codigo=uni.uni_codigo",array("uni_desc","uni_cnes"))
                    ->join(array("usr"=>"usuarios"),"age.med_codigo=usr.usr_codigo",array("usr_nome"))
                    ->join(array("usu"=>"usuario"),"age.usu_codigo=usu.usu_codigo",array("usu_cartao_sus","usu_nome","usu_datanasc","usu_prontuario","usu_sexo","usu_mae"))
                    ->joinLeft(array("dom"=>"domicilio"),"usu.dom_codigo=dom.dom_codigo",array("dom_telefone","dom_numero"))
                    ->joinLeft(array("rua"=>"rua"),"dom.rua_codigo=rua.rua_codigo",array("rua_nome","rua_cep","rua_bairro"))
                    ->joinLeft(array("cid"=>"cidade"),"rua.cid_codigo=cid.cid_codigo",array("cid_nome","cid_codigo_ibge","uf_sigla"))
                    ->where("age.age_codigo =?",$ageCodigo);
        //die($sql);
         return $this->fetchRow($sql);
    }

    public function getAgenda($usr_codigo=FALSE) {
        $tbUsr = new Application_Model_Usuarios();
        
        // O usr atual faz pre-consulta? (Enfermeiro ou Auxiliar) e a consulta precisa de triagem
        if ($tbUsr->fazPreConsulta()) {
                    
            return $this->getAgendaPreConsulta($usr_codigo);
        } else {
           
            // e se não for médico? #verificar
            return $this->getAgendaMedico();
        }
    }

    public function getAgendaMedico() {
		//die("med");
        $tbUsr = new Application_Model_Usuarios();
        $usr = $tbUsr->getUsrAtual();
		
		if(empty($usr->esp_codigo)){
			throw new Zend_Validate_Exception("É preciso informar o campo especilidade no login");
		}
		
        // Quais tipos de Agendamento esse usr deve atender?
        $age_atendido = array("P"); // prontuário
        // Se sua especialidade não precisa de Pre-Consulta
        if (!$tbUsr->espPreciaDePreConsulta()){
            $age_atendido [] = "S"; // pegar os pacientes recepcionados
        }
        //echo "<pre>".print_r($age_atendido,1); die($age_atendido);
        $tbCon = new Application_Model_Configuracao();
        $tempo = $tbCon->getConfig("TEMPO_ESPERA");
        $where = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("age" => "agendamento"), array("age.age_codigo","age_horario as age_hora","age_atendido","med_codigo"))
                        ->join(array("usu" => "usuario"), "usu.usu_codigo=age.usu_codigo", array("usu_codigo", "usu_nome", "usu_mae", "usu_end_cidade", "usu_datanasc","usu_prontuario"))
                        ->joinLeft(array("p" => "pre_consulta"),"p.age_codigo = age.age_codigo",array("p.pc_clas_risco"))
                        ->where("age.age_atendido IN (?)", $age_atendido)                
                        ->where("age.esp_codigo=?", $usr->esp_codigo)
                        ->where("age.uni_codigo=?", $usr->uni_codigo)
                        ->where("age.med_codigo=$usr->usr_codigo  OR age.med_codigo = 99999")
                                        ->where("age(now(), to_timestamp(age_data || ' ' || age_horario, 'YYYY-MM-DD HH24:MI')) < cast((cast($tempo as char) || ' hours') as interval)")
                                        ->order("p.pc_clas_risco")
                                        ->order("age.age_ordem")
                                        ->order("age.age_horario")
                                        ->order("age.age_codigo");
        
        //die($where);
        $this->getMedicoAgendado();
        return $this->fetchAll($where);
               
    }
    public function getMedicoAgendado(){		
            $tbUsr = new Application_Model_Usuarios();
    $usr = $tbUsr->getUsrAtual();

            $where = $this->select(FALSE)
            ->setIntegrityCheck(FALSE)
            ->from(array("age" => "agendamento"), array("DISTINCT(age.med_codigo) as med_codigo"))
                            ->where("age.esp_codigo=?", $usr->esp_codigo)
            ->where("age.uni_codigo=?", $usr->uni_codigo)
            ->where("age.age_data = CURRENT_DATE")
            ->where("age.age_atendido != 'A'")
            ->where("age.age_atendido != 'E'");
            //die($where);
            return $this->fetchAll($where);
    }
    public function getAgendaPreConsulta($usr_codigo=false) {
        $tbUsr = new Application_Model_Usuarios();
        $usr = $tbUsr->getUsrAtual();
        $tbCon = new Application_Model_Configuracao();
        $tempo = $tbCon->getConfig("TEMPO_ESPERA");
        // Quais tipos de Agendamento esse usr deve atender?
        $age_atendido = array("S"); // recepcionado
        // listar todos atendimentos S (recepcionado) que não precisem de pré-consulta, de hoje
        $wher1 = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("age" => "agendamento"), array("age.age_codigo","age_horario as age_hora","age_atendido","med_codigo","age_ordem","age_horario"))
                ->join(array("usu" => "usuario"), "usu.usu_codigo=age.usu_codigo", array("usu_codigo", "usu_nome", "usu_mae", "usu_end_cidade", "usu_datanasc","usu_prontuario"))
                ->join(array("e" => "especialidade"), "e.esp_codigo=age.esp_codigo AND e.esp_pre_consulta=true", "esp_nome")
                ->join(array("u" => "usuarios"), "u.usr_codigo=age.med_codigo", "usr_nome")
                ->where("age.age_atendido IN (?)", $age_atendido)
                ->where("age.uni_codigo=?", $usr->uni_codigo)               
                ->where("age(now(), to_timestamp(age_data || ' ' || age_horario, 'YYYY-MM-DD HH24:MI')) < cast((cast($tempo as char) || ' hours') as interval)");
        
        
        
        
        $where2 = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("age" => "agendamento"), array("age.age_codigo","age_horario as age_hora","age_atendido","med_codigo","age_ordem","age_horario"))
                ->join(
                        array("usu" => "usuario"), "usu.usu_codigo=age.usu_codigo", array("usu_codigo", "usu_nome", "usu_mae", "usu_end_cidade", "usu_datanasc","usu_prontuario")
                )
                ->join(array("e" => "especialidade"), "e.esp_codigo=age.esp_codigo AND e.esp_pre_consulta=true", "esp_nome")
                ->join(array("u" => "usuarios"), "u.usr_codigo=age.med_codigo", "usr_nome")
                ->where("age.uni_codigo=?", $usr->uni_codigo)
                ->where("med_codigo=$usr->usr_codigo")
                ->where("age_atendido in ('S','P')")
                ->where("age(now(), to_timestamp(age_data || ' ' || age_horario, 'YYYY-MM-DD HH24:MI')) < cast((cast($tempo as char) || ' hours') as interval)");
        
        $where = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->union(array($wher1, $where2), Zend_Db_Select::SQL_UNION)
                        ->order(array("age_ordem", "age_horario","age_codigo"));
        
        if($usr_codigo){
            $wher1->where("med_codigo=$usr_codigo")
                  ->order(array("age_ordem", "age_horario","age_codigo"));
            $registros = $this->fetchAll($wher1);
        }else{
            $registros = $this->fetchAll($where);
        }
        //die($where);
        return $registros;
    }

    public function getAtendidosHoje() {
        $tbUsr = new Application_Model_Usuarios();
        $usr = $tbUsr->getUsrAtual();

        // Quais tipos de Agendamento mostrar?
        $age_atendido = array("A"); // Finalizado
        // listar todos atendimentos A (finalizado), de hoje
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("age" => "agendamento"), array("DISTINCT(age.age_codigo)", "usu_datanasc" => "DATE_PART('YEAR', AGE(CURRENT_DATE, usu.usu_datanasc))"))
                ->join(
                        array("usu" => "usuario"), "usu.usu_codigo=age.usu_codigo", array("usu_codigo", "usu_nome", "usu_mae", "usu_end_cidade")
                )
                 ->join(array("p" => "pre_consulta"),"p.age_codigo = age.age_codigo",array("p.pc_clas_risco"))
                ->order("p.pc_clas_risco")
                ->where("age.age_atendido IN (?)", $age_atendido)
                ->where("age.esp_codigo=?", $usr->esp_codigo)
                ->where("age.uni_codigo=?", $usr->uni_codigo);
        
        if($filtro == null){
            $where->where("age.med_codigo=?", $usr->usr_codigo)
                  ->where("age.age_data = CURRENT_DATE");
        }
        

        return $this->fetchAll($where);
        
        
    }
    public function alteraMedico($age_codigo,$med_codigo,$age_atendido){
        if (!$age_codigo)
            $age_codigo = $this->usuEmAberto()->age_codigo;

        $age = $this->find($age_codigo)->current();
        $age->med_codigo = $med_codigo;
        $age->age_atendido = $age_atendido;
        $age->save();
		return true;
   }
   
    public function getAgendaPerm($age_codigo=FALSE){
       $sql = $this->select(FALSE)
                            ->setIntegrityCheck(FALSE)
                            ->from(array("age"=>"agendamento"))
                            ->where("age.age_codigo=$age_codigo")
                            ->where("age.age_atendido = 'I'");
        return $this->fetchRow($sql);
    }
    
    public function getHistoricoPorUsuario($usu_codigo=FALSE){
        $where = $this->select()
                      ->setIntegrityCheck(FALSE)
                      ->from(array("age"=>"agendamento"),array("age_data","age_atendido","age_horario","age_codigo","age_tipo"))
                      ->joinLeft(array("coni"=>"convenio_itens"),"coni.coni_codigo=age.coni_codigo","")
                      ->joinLeft(array("conv"=>"convenio"),"conv.conv_codigo=coni.conv_codigo","")
                      ->joinLeft(array("usr"=>"usuarios"),"usr.usr_codigo=coni.usr_codigo OR usr.usr_codigo=age.med_codigo",array("usr_nome"))
                      ->joinLeft(array("uni"=>"unidade"),"uni.uni_codigo=conv.uni_codigo OR uni.uni_codigo=age.uni_codigo",array("uni_desc"))
                      ->joinLeft(array("mes"=>"medico_especialidade"),"mes.med_codigo=usr.usr_codigo and age.esp_codigo=mes.esp_codigo","")
                      ->joinLeft(array("esp"=>"especialidade"),"esp.esp_codigo=mes.esp_codigo",array("esp_nome"))
                      ->joinLeft(array("tat"=>"tipo_atendimento"), "tat.tat_codigo=age.tat_codigo","tat_tipo")
                      ->where("usu_codigo=?",$usu_codigo)
                      ->order("age_data DESC");
        //die($where);
        return $this->fetchAll($where);
    }
    
    public function calculaDataFinal(&$data_inicial, $fimDoMes=FALSE) {
        if ($fimDoMes) {
                list($y, $m, $d) = explode("-", $data_inicial);
                $mk = mktime(0, 0, 0, $m, $d, $y);
                return "$y-$m-" . date("t", $mk);
        }
        
        $tbConf = new Application_Model_Configuracao();
        $dias = $tbConf->getConfig('AGENDA_MOSTRAR_N_OPCOES');
        $dtRetro = $tbConf->getConfig('AGENDA_EXAME_DT_RETROATIVA');

        list($y, $m, $d) = explode("-", $data_inicial);

        if(empty($dtRetro)) {
            if ((int) "$y$m$d" < (int) date("Ymd")) {
                    $data_inicial = date("Y-m-d");
                    list($y, $m, $d) = explode("-", $data_inicial);
            }
        }

        //exit;
        $mk = mktime(0, 0, 0, $m, $d + $dias - 1, $y);
        return date("Y-m-d", $mk);
    }
    
    public function getVagas($coni_codigo, $data_inicial, $data_final) {
        $tbConv = new Application_Model_Convenio();
        $tbConI = new Application_Model_ConvenioItens();
        $tbFun = new Application_Model_Funcoes();
        $tbGradeDia = new Application_Model_GradeDia();
        // Verifica os dias que atende e coloca em um array
        $tbConds = new Application_Model_ConvenioDiasSemana();
        // Alterar método pra pegar do dias de semana agendamento
        //$verificaDiasQueAtende = $tbConds->getDiasDeAtendimento($coni_codigo);
        $dias = $tbConds->getDiasDeAtendimento($coni_codigo);
        // Pega o objeto acima e transforma num array de dias
//        $dias = array();
//        foreach($verificaDiasQueAtende as $diaQueAtende){
//            array_push($dias, $diaQueAtende->condi_dia);
//        }
        // Cria um array de datas entre as data inicial e a data final
        $arrDatas = $tbFun->datasToArray($data_inicial, $data_final);
        // Função que pega o número de vagas e joga pra data em que será realizado no agendamento
        $arrDatasQueAtende = array();
        $datasResult = array();
        foreach($arrDatas as $data){
            $atendeQueDia = $tbFun->diaSemana($data);
            // Verifica se o dia que atende existe
            if(in_array($atendeQueDia, $dias)){
                // Pega o número de vagas do dia e incrementa o array
                $vagas = $tbGradeDia->getVagasDia($coni_codigo,$data,$atendeQueDia);
                $datasResult[$data] = $vagas;
            }else{
                // Se não retorna nada não existe mais vaga
                $datasResult[$data] = 0;
            }
        }
        return $datasResult;
    }
    
    public function getAgendamentosPorHorario($horario=FALSE,$coni_codigo=FALSE,$data_selecionada=FALSE){
        $where = $this->select()
                      ->setIntegrityCheck(FALSE)
                      ->from(array("age"=>"agendamento"),array("count(age_codigo) as quantidade","age_paciente"))
                      ->where("coni_codigo=?",$coni_codigo)
                      ->where("age_horario='$horario'")
                      ->where("age_data='$data_selecionada'")
                      ->where("age_atendido != 'F'")
                      ->group(array("age_paciente"));
        return $this->fetchRow($where);
    }
    
    public function imprimePacientesAgendados($agendamentos=FALSE,$uni_codigo=FALSE,$usr_codigo=FALSE,$esp_codigo=FALSE,$age_data=FALSE,$pac=FALSE){
        $where = $this->select()
                      ->setIntegrityCheck(FALSE)
                      ->from(array("age"=>"agendamento"),array("age_ordem","age_codigo","age_horario","age_atendido" => "(CASE WHEN age_atendido='P' THEN 'Pré-Consulta' WHEN age_atendido='S' THEN 'Recepcionado' WHEN age_atendido='A' THEN 'Atendido' WHEN age_atendido='N' THEN 'Agendado' WHEN age_atendido='T' THEN 'Transferido' WHEN age_atendido='F' THEN 'Faltou' WHEN age_atendido='E' THEN 'Em Atendimento' WHEN age_atendido='I' THEN 'Atendimento Incluso' WHEN age_atendido='M' THEN 'Falta Médica' END)","cor" => "(CASE WHEN age_atendido='S' THEN 'blue' WHEN age_atendido='A' THEN '#148e00' WHEN age_atendido='N' THEN '#2e6e9e' END)","age_atendido AS status"))
                      ->join(array("coni"=>"convenio_itens"),"coni.coni_codigo = age.coni_codigo","")
                      ->join(array("conv"=>"convenio"),"coni.conv_codigo = conv.conv_codigo","")
                      ->join(array("usu"=>"usuario"),"usu.usu_codigo = age.usu_codigo",array("usu_nome","extract(year from age(usu.usu_datanasc)) as idade","usu_datanasc","usu_prontuario","usu_mae","usu_fone","usu_fone_recado","usu_celular"))
                      ->joinleft(array("dom"=>"domicilio"),"usu.dom_codigo = dom.dom_codigo",array("dom_telefone"))
                      ->where("conv.uni_codigo=?",$uni_codigo)
                      ->where("coni.usr_codigo='$usr_codigo'")
                      ->where("age.esp_codigo='$esp_codigo'")
                      ->where("age.age_data='$age_data'");
        // Validação de agendamentos selecionados para impressão
        if ($agendamentos) { 
            $arrayAgendamentos = explode("-", $agendamentos); 
            $i = 0;
            foreach($arrayAgendamentos as $item) {   
                if ($i > 0) {
                    $where->orwhere("age_codigo =?",$item);
                } else {
                    $where->where("age_codigo =?",$item);
                }
            $i++;    
            }
        }
        //->where("age.age_atendido ='N'")
        $where->order("age_ordem");
        $pac ? $where->order(array("age.age_atendido DESC")) : $where->order(array("age.age_horario"))
        ->order("age.age_horario");
        return $this->fetchAll($where);
        /*
		S - Recepcionado
		A - Atendido
		N - Agendado
		T - Transferido
		F - Faltoso
		E - Em atendimento
		I - Atendimento Incluso
		M - Falta MÃ©dica
		 */
    }
    
    public function getPacientesAgendados($uni_codigo=FALSE,$usr_codigo=FALSE,$esp_codigo=FALSE,$age_data=FALSE,$pac=FALSE){
        $where = $this->select()
                      ->setIntegrityCheck(FALSE)
                      ->from(array("age"=>"agendamento"),array("age_ordem","age_codigo","age_horario","age_atendido" => "(CASE WHEN age_atendido='P' THEN 'Pré-Consulta' WHEN age_atendido='S' THEN 'Recepcionado' WHEN age_atendido='A' THEN 'Atendido' WHEN age_atendido='N' THEN 'Agendado' WHEN age_atendido='T' THEN 'Transferido' WHEN age_atendido='F' THEN 'Faltou' WHEN age_atendido='E' THEN 'Em Atendimento' WHEN age_atendido='I' THEN 'Atendimento Incluso' WHEN age_atendido='M' THEN 'Falta Médica' END)","cor" => "(CASE WHEN age_atendido='S' THEN 'blue' WHEN age_atendido='A' THEN '#148e00' WHEN age_atendido='N' THEN '#2e6e9e' END)","age_atendido AS status"))
                      ->join(array("coni"=>"convenio_itens"),"coni.coni_codigo = age.coni_codigo","")
                      ->join(array("conv"=>"convenio"),"coni.conv_codigo = conv.conv_codigo","")
                      ->join(array("usu"=>"usuario"),"usu.usu_codigo = age.usu_codigo",array("usu_nome","extract(year from age(usu.usu_datanasc)) as idade","usu_datanasc","usu_prontuario","usu_mae","usu_fone","usu_celular"))
                      ->joinleft(array("dom"=>"domicilio"),"usu.usu_codigo = dom.usu_codigo_responsavel",array("dom_telefone"))->where("conv.uni_codigo=?",$uni_codigo)
                      ->where("coni.usr_codigo='$usr_codigo'")
                      ->where("age.esp_codigo='$esp_codigo'")
                      ->where("age.age_data='$age_data'")
                      //->where("age.age_atendido ='N'")
                      ->order("age_ordem");
					  $pac ? $where->order(array("age.age_atendido DESC")) : $where->order(array("age.age_horario"))
                      ->order("age.age_horario");
                //die($where);
		return $this->fetchAll($where);
        /*
		S - Recepcionado
		A - Atendido
		N - Agendado
		T - Transferido
		F - Faltoso
		E - Em atendimento
		I - Atendimento Incluso
		M - Falta MÃ©dica
		 */
    }
    
    public function getAgendamento($age_codigo=FALSE){
       $where = $this->select(FALSE)
                     ->setIntegrityCheck(FALSE)
                     ->from(array("age"=>"agendamento"))
                     ->join(array("usr"=>"usuarios"), "usr.usr_codigo=age.med_codigo",array("usr_nome","usr_tipo_medico"))
                     ->join(array("esp"=>"especialidade"),"esp.esp_codigo=age.esp_codigo","esp_nome")
                     ->join(array("uni"=>"unidade"),"uni.uni_codigo=age.uni_codigo",array("uni_desc","uni_endereco","uni_numero","uni_cep"))
                     ->join(array("usu"=>"usuario"),"usu.usu_codigo=age.usu_codigo")
                     ->joinLeft(array("dom" => "domicilio"),"dom.dom_codigo=usu.dom_codigo")
                     ->joinLeft("rua","rua.rua_codigo=dom.rua_codigo")
                     ->where("age.age_codigo=$age_codigo");                           
       return $this->fetchRow($where);
    }
/**
     * Exclusão Lógica;
     * @param type $conv_codigo
     * @return type 
     */
    public function excluir($age_codigo) {           
            $item = $this->fetchRow("age_codigo=$age_codigo");
             if ($item)
                 $item->delete();
            return true;
    }

    
    public function verificaSeTemAgendamento($coni_codigo=FALSE,$data=FALSE,$usu_codigo=FALSE){
        $sql = $this->select()
                    ->setIntegrityCheck(FALSE)
                    ->from(array("age"=>"agendamento"),"count(age_codigo) as quantidade")
                    ->where("coni_codigo=?",$coni_codigo)
                    ->where("age_data=?",$data)
                    ->where("usu_codigo=?",$usu_codigo);
        return $this->fetchRow($sql); 
    }
    
    public function getAgendamentos($coni_codigo,$dia,$codsAge){
        $sql = $this->select()
                    ->setIntegrityCheck(FALSE)
                    ->from(array("age"=>"agendamento"),array("age_codigo","age_data","age_horario"))
                    ->join(array("usu"=>"usuario"), "usu.usu_codigo=age.usu_codigo",array("usu_nome","COALESCE(usu_celular,NULL,'----') as usu_celular","usu_codigo"))
                    ->joinLeft(array("dom"=>"domicilio"),"dom.dom_codigo=usu.dom_codigo",array("COALESCE(dom_numero,NULL,0) as dom_numero","COALESCE(dom_telefone,NULL,'----') as dom_telefone"))
                    ->joinLeft("rua", "rua.rua_codigo=dom.rua_codigo"," COALESCE(rua_nome,NULL,'S/N') as rua_nome")
                    ->where("coni_codigo=$coni_codigo")
                    ->where("age_data='$dia'")
                    ->where("age.age_atendido ='N'");
        $i = 0;  
        if(!empty($codsAge)) {
            foreach ($codsAge as $age) {
                if ($i == 0) {
                    $sql->where("age_codigo =?",$age);
                } 
                if ($i > 0){
                    $sql->orwhere("age_codigo =?",$age);
                }
                $i++;
            }
        }
        return $this->fetchAll($sql);
    }
    
    public function getAgendamentoPorAtendimento(){
        
    }
    
    public function getConsultasFuturas($usr_codigo = false){
        $where = $this->select(FALSE)
                      ->setIntegrityCheck(FALSE)
                      ->from(array("age"=>"agendamento"),"count(*) as qtde")
                      ->where("age_data >= CURRENT_DATE")
                      ->where("med_codigo = $usr_codigo");
        
        return $this->fetchRow($where);
    }
    public function getAgendamentosUsuario($usu_codigo = false){
        $retorno = array();
        if($usu_codigo){
            $sql = $this->select()
                      ->setIntegrityCheck(FALSE)
                      ->from(array('age'=>'agendamento'), array('age.age_codigo', 'age.age_data', 'age.age_atendido'))
                      ->joinLeft(array('usr'=>'usuarios'), 'usr.usr_codigo=age.med_codigo', array('usr.usr_nome'))
                      ->where("age.usu_codigo=$usu_codigo")
                      ->where("age.age_data >= current_date")
                      ->order("age.age_data DESC");
            $retorno = $this->fetchAll($sql)->toArray();
            $status = array(
                'S'=>'Recepcionado',
                'A'=>'Atendido',
                'N'=>'Agendado',
                'T'=>'Transferido',
                'F'=>'Faltoso',
                'E'=>'Em atendimento',
                'I'=>'Atendimento Incluso',
                'M'=>'Falta M&eacute;dica',
                'P'=>'Pr&eacute;-Consulta',
            );
            foreach ($retorno as &$agendamento) {
                $opcoes = $status;
                unset($opcoes[$agendamento['age_atendido']]);
                $agendamento['status'] = !empty($agendamento['age_atendido']) ? $status[$agendamento['age_atendido']] : 'Desconhecido';
                $agendamento['opcoes'] = $opcoes;
                $agendamento['age_data'] = date('d/m/Y', strtotime($agendamento['age_data']));
            }
        }
        return $retorno;
    }
    public function realocarPaciente($dados){
        $retorno = false;
        if((array_key_exists('age_codigo', $dados) && !empty($dados['age_codigo'])) && (array_key_exists('age_atendido', $dados) && !empty($dados['age_atendido']))){
            $retorno = $this->update(array('age_atendido'=>$dados['age_atendido']), "age_codigo = " . $dados['age_codigo']);
        }
        return $retorno;
    }
   
 
}
