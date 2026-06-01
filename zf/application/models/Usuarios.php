<?php

class Application_Model_Usuarios extends Application_Model_DbTable_Usuarios {

    protected $_name = 'usuarios';
    protected $_primary = 'usr_codigo';
    protected $_sequence = 'seq_usr_codigo_9041';

    public function salvar(array $data) {

        $this->emptyToUnset($data);
        try {
            return parent::salvar($data);
        } catch (Exception $exc) {
            throw new Zend_Validate_Exception("Falha ao cadastrar o usuário: " . $exc->getMessage() . "!.");
        }
    }

    public function verificaSeTemAgendamentoFuturo($usr_codigo = FALSE) {
        if (empty($usr_codigo)) {
            return false;
        }
        $tbAgenda = new Application_Model_Agendamento();
        return $tbAgenda->getConsultasFuturas($usr_codigo)->qtde;
    }

    public function inativaUsuarios(array $data) {
        try {
            $where['usr_login != ? '] = 'admin';
            return $this->update($data, $where);
        } catch (Exception $exc) {
            throw new Zend_Validate_Exception("Falha ao inativar Usuários: " . $exc->getMessage());
        }
    }

    /**
     * Pega o usuário na session e busca em logon
     * @throw Zend_Exception (cód: 1)caso não aja $_SESSION
     * @throw Zend_Exception (cód: 2) caso não haja informação sobre o setor escolhido
     * @return Object (Zend_Db_Table_Row->toArray())
     */
    public function getUsrAtual() {

        $logon = new Zend_Session_Namespace("logon");
        if (!isset($logon->usr->usr_codigo) || empty($logon->usr->usr_codigo)) {
            if (empty($_SESSION['id_login']))
                throw new Zend_Exception("É necessário fazer login", 1);

        
            $id_login = $_SESSION['id_login'];
            $where = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("u" => "usuarios"))
                    ->join(array("l" => "logon"), "l.id_login=u.usr_codigo", array("cod_setor"))
                    ->joinLeft(array("e" => "especialidade"), "e.esp_codigo=l.esp_codigo", array("esp_codigo", "esp_nome", "esp_pre_consulta"))
                    ->join(array("uni" => "unidade"), "uni.uni_codigo=l.uni_codigo", array("uni_codigo", "uni_desc", "uni_cnpj", "uni.cnes_tp_unid_id","uni_codigo_ibge", "uni_endereco", "uni_numero", "cnes_sigestgest", "cnes_telefone", "uni_bairro", "uni_tipo"))
                    ->joinLeft(array("cid" => "cidade"), "cid.cid_codigo_ibge=uni.uni_codigo_ibge", array("cid_nome", "uf_sigla"))
                    ->joinLeft(array("s" => "setor"), "s.set_codigo=l.cod_setor", array("set_codigo", "set_nome"))
                    ->joinLeft(array("con" => "conselho"), "con.con_codigo=u.con_codigo", "con_descricao")
                    ->where("id_login=?", $id_login);
            $usr = $this->fetchRow($where);
            if (!$usr) {
                throw new Zend_Exception("É necessário informar a unidade no login", 2);
            }
            $logon->usr = (object) $usr->toArray();
        }
//        echo "<pre>".print_r($logon->usr,1);die();
        return $logon->usr;
    }

    public function getDadosCidadeUsrLogado($usr_codigo = FALSE) {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("u" => "usuarios"), "")
                ->join(array("l" => "logon"), "u.usr_codigo=l.id_login", "")
                ->join(array("uni" => "unidade"), "l.uni_codigo=uni.uni_codigo", array("uni_codigo_ibge"))
                ->where("u.usr_codigo =?", $usr_codigo);
        return $this->fetchRow($sql);
    }

    public function isMedico() {
        return in_array($this->getUsrAtual()->usr_tipo_medico, array("M", "D"));
    }

    /**
     * Buscar os usuários
     * usado para alimentar o plugin de busca (jquery)
     * @return json
     */
    public function buscar($term = FALSE, $incluirExterno = FALSE, $conveniado = FALSE) {
        if ($term)
            $where = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("usr" => "usuarios"), array("usr_codigo", "usr_nome", "interno" => "('1')", "categoria" => "('Interno')"))
                    ->where("retira_acentos(usr_nome) ilike retira_acentos('%$term%')")
                    ->where("usr_ativo = 'S'");
        if ($incluirExterno) {
            $medico = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("med" => "medico"), array("med_codigo", "med_nome", "interno" => "('0')", "categoria" => "('Externo')"))
                    ->where("retira_acentos(med_nome) ilike retira_acentos('%$term%')");
            if ($conveniado) {
                $where->join(array("coni" => "convenio_itens"), "usr.usr_codigo=coni.usr_codigo", "")
                        ->joinLeft(array("mesp" => "medico_especialidade"), "mesp.med_codigo=usr.usr_codigo", "")
                        ->joinLeft(array("esp" => "especialidade"), "esp.esp_codigo=mesp.esp_codigo", "esp_codigo")
                        ->where("conv_codigo=?", $conveniado);

                $medico->joinLeft(array("mesp" => "medico_especialidade"), "mesp.med_codigo=med.med_codigo", "esp_codigo");
            }
            $union = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->union(array($where, $medico), Zend_Db_Select::SQL_UNION_ALL)
                    ->order(array("categoria DESC", "usr_nome DESC"))
                    ->limit(15);

            $all = $this->fetchAll($union);
        } else {
            $where->order(array("usr_nome"))
                    ->limit(15);
            $all = $this->fetchAll($where);
        }
        $out = array();
        foreach ($all as $usr) {
            $out [] = array(
                "id" => $usr->usr_codigo,
                "label" => $usr->usr_nome,
                "data" => $usr->toArray()
            );
        }

        if (!count($out)) {
            $out [] = array(
                "id" => 0,
                "label" => "Nenhum item encontrado",
                "data" => array("usr_codigo" => "0", "usr_nome" => "", "categoria" => "Nenhum médico encontrado")
            );
        }

        return $out;
    }

    public function fazPreConsulta() {
        // Tipos de usr que fazem pré-consulta:
        $fazPC = array("E", "A"); // (E)nfermeiro e (A)ux. de Enfermagem
        $usr = $this->getUsrAtual();
        return in_array($usr->usr_tipo_medico, $fazPC);
    }

    public function espPreciaDePreConsulta() {
        $usr = $this->getUsrAtual();
        return $usr->esp_pre_consulta;
    }

    public function estaLogado($usr_codigo, $min = 100) {
        $where = $this->select()
                ->setIntegrityCheck(FALSE)
                ->from(array("u" => "usuarios"), "usr_codigo")
                ->join(array("l" => "logon"), "l.id_login=u.usr_codigo", "")
                ->where("id_login=?", $usr_codigo)
                ->where("dt_atualizacao >= NOW()+interval '$min minute'");

        $usr = $this->fetchRow($where);
        return (BOOL) $usr;
    }

    public function getInfoUsr($usr_codigo = FALSE) {
        if (!$usr_codigo){
            return false;
        }

        return $this->fetchRow("usr_codigo = $usr_codigo");
    }

    public function recuperaCrm($med_codigo){
        $recebeCodigo = intval($med_codigo);
        $sql = $this->getDefaultAdapter()->query("SELECT med_crm from medico where med_codigo = $med_codigo")->fetchAll();

        return $sql;
    }

    /** Método Copiado do ação social
     * Buscar os profissionais da saúde que não está ligados naquele convenio
     * usado para alimentar o plugin de busca (jquery)
     * @return json
     */
    public function buscarProfissionais($term = FALSE, $conv_codigo = FALSE) {
        if ($term)
            $where = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("usr" => "usuarios"), array("usr_codigo", "usr_nome"))
                    ->where("retira_acentos(usr_nome) ilike retira_acentos('%$term%')")
                    ->where("usr_modulos in ('A','T')");
        // ->where("usr.usr_codigo not IN (select usr_codigo from convenio_itens where conv_codigo = $conv_codigo )");

        $where->order(array("usr_nome"))
                ->limit(15);

        $all = $this->fetchAll($where);


        $out = array();
        foreach ($all as $usr) {
            $out [] = array(
                "id" => $usr->usr_codigo,
                "label" => $usr->usr_nome,
                "data" => $usr->toArray()
            );
        }

        if (!count($out)) {
            $out [] = array(
                "id" => 0,
                "label" => "Nenhum item encontrado",
                "data" => array("usr_codigo" => "0", "usr_nome" => "")
            );
        }

        return $out;
    }

    /**
     * Buscar os profissionais da saúde que não está ligados naquele convenio
     * usado para alimentar o plugin de busca (jquery)
     * @return json
     */
    public function buscarProfissionaisSaude($term = FALSE, $conv_codigo = FALSE) {
        if ($term){
            $where = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->distinct()
                    ->from(array("usr" => "usuarios"), array("usr_codigo", "usr_nome"))
                    ->join(array("coni" => "convenio_itens"), "coni.usr_codigo=usr.usr_codigo", "")
                    ->where("retira_acentos(usr_nome) ilike retira_acentos('%$term%')")
                    ->where("usr_tipo_medico in('M','E','A','D','P','O')")
                    ->where("coni_ativo <> 'N'")
                    ->where("usr_ativo <> 'N'");
                    // ->where("usr.usr_codigo not IN (select usr_codigo from convenio_itens where conv_codigo = $conv_codigo )");
            
            if($conv_codigo){
                $where->where("conv_codigo=$conv_codigo");
            }  
            
        }

        $where->order(array("usr_nome"))
                ->limit(15);

        $all = $this->fetchAll($where);

        $out = array();
        foreach ($all as $usr) {
            $out [] = array(
                "id" => $usr->usr_codigo,
                "label" => $usr->usr_nome,
                "data" => $usr->toArray()
            );
        }

        if (!count($out)) {
            $out [] = array(
                "id" => 0,
                "label" => "Nenhum item encontrado",
                "data" => array("usr_codigo" => "0", "usr_nome" => "")
            );
        }

        return $out;
    }

    /**
     * Buscar os profissionais da saúde que não está ligados naquele convenio
     * usado para alimentar o plugin de busca (jquery)
     * @return json
     */
    public function buscarUsuariosSaude($term = FALSE) {

        if ($term)
            $where = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->distinct()
                    ->from(array("usr" => "usuarios"), array("usr_codigo", "usr_nome", "cnes_numero"))
                    ->join(array("mede" => "medico_especialidade"), "mede.med_codigo = usr.usr_codigo", "")
                    ->joinLeft(array("usue" => "usuarios_equipe"), "usr.usr_codigo=usue.usr_codigo", array(""))
                    ->joinLeft(array("tbe" => "tb_equipe"), "usue.co_equipe=tbe.co_seq_equipe", array("nu_ine"))
                    ->where("retira_acentos(usr_nome) ilike retira_acentos('%$term%')")
                    ->where("usr_tipo_medico in('M','E','A','D','P','C','O')");
        // ->where("usr.usr_codigo not IN (select usr_codigo from convenio_itens where conv_codigo = $conv_codigo )");

        $where->order(array("usr_nome"))
                ->limit(15);

        $all = $this->fetchAll($where);
        //  die($where);


        $out = array();
        foreach ($all as $usr) {
            $out [] = array(
                "id" => $usr->usr_codigo,
                "label" => $usr->usr_nome,
                "data" => $usr->toArray()
            );
        }

        if (!count($out)) {
            $out [] = array(
                "id" => 0,
                "label" => "Nenhum item encontrado",
                "data" => array("usr_codigo" => "0", "usr_nome" => "")
            );
        }

        return $out;
    }

    public function buscarUsuariosUnidadeEquipe($term = FALSE) {

        if ($term)
            $where = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->distinct()
                    ->from(array("usr" => "usuarios"), array("usr_codigo", "usr_nome", "cnes_numero"))
                    ->join(array("mede" => "medico_especialidade"), "mede.med_codigo = usr.usr_codigo", "")
                    ->join(array("unu" => "unidade_usuarios"), "unu.usr_codigo=usr.usr_codigo", "")
                    ->join(array("uni" => "unidade"), "unu.uni_codigo=uni.uni_codigo", "")
                    ->joinLeft(array("usue" => "usuarios_equipe"), "usr.usr_codigo=usue.usr_codigo", array(""))
                    ->joinLeft(array("tbe" => "tb_equipe"), "usue.co_equipe=tbe.co_seq_equipe", array("nu_ine"))
                    ->where("retira_acentos(usr_nome) ilike retira_acentos('%$term%')")
                    ->where("uni_cnes is not null")
                    ->where("usr_ativo = 'S'")
                    ->where("usr_tipo_medico in('M','E','A','D','P','C','F')");
        // ->where("usr.usr_codigo not IN (select usr_codigo from convenio_itens where conv_codigo = $conv_codigo )");

        $where->order(array("usr_nome"))
                ->limit(15);

        $all = $this->fetchAll($where);

        $out = array();
        foreach ($all as $usr) {
            $out [] = array(
                "id" => $usr->usr_codigo,
                "label" => $usr->usr_nome,
                "data" => $usr->toArray()
            );
        }

        if (!count($out)) {
            $out [] = array(
                "id" => 0,
                "label" => "Nenhum item encontrado",
                "data" => array("usr_codigo" => "0", "usr_nome" => "")
            );
        }

        return $out;
    }

    public function getUsuariosPorCpf($usuCpf) {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("usr" => "usuarios"), array("usr_codigo"))
                ->where("usr.usr_cpf =?", $usuCpf);
        //die($sql);
        return $this->fetchRow($sql);
    }

    public function getUsrPorTipo($usr_tipo_medico = FALSE) {
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("usr" => "usuarios"))
                ->where("usr_tipo_medico='$usr_tipo_medico'");

        return $this->fetchAll($where);
    }

    public function getDadosPeloCodigo($usrCodigo = FALSE) {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("usr" => "usuarios"), array("usr_tipo_medico"))
                ->where("usr.usr_codigo =?", $usrCodigo);
        return $this->fetchRow($sql);
    }

    public function getQtdUsuariosAtivosCnes() {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("usr" => "usuarios"), array("COUNT(usr_codigo) AS qtd_usr"))
                ->where("cnes_ativo = 'S'");
        return $this->fetchRow($sql);
    }

    public function verificaLoginExistente($term = FALSE) {
        if ($this->executaSqlVerificaLoginExistente($term) > 0) {
            return false;
        } else {
            return true;
        }
    }

    public function getUsuariosModulo() {
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("usr" => "usuarios"))
                ->where("usr_ativo != 'N'")
                ->where("usr_ativo is not null")
                ->where("cnes_cod_cns is not null")
                ->order("usr_nome");
        return $this->fetchAll($where);
    }

    public function getUsuariosBuscaForm($term = FALSE) {
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("usr" => "usuarios"), array("usr_codigo", "usr_nome", "usr_tipo_medico" => "(CASE WHEN usr_tipo_medico = 'M' THEN 'Médico'
                        WHEN usr_tipo_medico = 'E' THEN 'Enfermeiro(a)'
                        WHEN usr_tipo_medico = 'D' THEN 'Dentista'
                        WHEN usr_tipo_medico = 'P' THEN 'Psicólogo(a)'
                        WHEN usr_tipo_medico = 'C' THEN 'Comum'
                        WHEN usr_tipo_medico = 'F' THEN 'Farmáceutico(a)'
                        WHEN usr_tipo_medico = 'B' THEN 'Bioquímico(a)'
                        WHEN usr_tipo_medico = 'A' THEN 'Aux. Enfermagem'
                        WHEN usr_tipo_medico = 'G' THEN 'Suporte'
                    END)",
                    "usr_login",
                    "usr_ativo" => "(CASE WHEN usr_ativo = 'S' THEN 'Ativo'
                        WHEN usr_ativo = 'N' THEN 'Inativo(a)'
                           ELSE 'Sem Status'
                            END)",
                        "( CASE WHEN dt_atualizacao > NOW() THEN 'on' ELSE 'off' END ) AS status"))
                ->joinLeft(array("log" => "logon"), "usr.usr_codigo=log.id_login", array("TO_CHAR(log.dt_entrada, 'DD/MM/YYYY HH24:MI:SS') as dt_entrada"))
                //->limit(15)
                ->order("usr_codigo DESC")
        ;
        if ($term) {
            $where->where("usr_nome ilike '%$term%' or usr_login ilike '%$term%'");
        } else {
            //$where->where("usr_modulos = 'A'")
            $where->where("usr_ativo = 'S'");
        }
            $where->where("usr_tipo_medico != 'G' or usr_tipo_medico is null");
        //die($where);
        return $this->fetchAll($where);
    }

    public function getGridResource($page = 1, $limit = FALSE, $sidx = NULL, $sord = "ASC", $where = NULL) {
        $this->setFields(array("usr_codigo", "usr_nome"));

        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("usr" => "usuarios"), array("usr_codigo", "usr_nome"));

        return parent::getGridResource($page, $limit, $sidx, $sord, $where);
    }

    public function buscaUsuariosPorNome($nome_prof = FALSE, $nome_prof_quebrado = FALSE) {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("usr" => "usuarios"), array("usr_codigo", "usr_nome"));
        foreach ($nome_prof_quebrado as $nome) {
            $nomes_provavel .= $nome . " ";
            $sql->orWhere("usr.usr_nome ilike retira_acentos('%" . trim($nomes_provavel) . "%')");
        }
        return $this->fetchAll($sql);
    }

    public function listaBioquimicos() {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("usr" => "usuarios"), array("usr_codigo", "usr_nome"))
                ->where("usr.usr_tipo_medico = 'B'");
        return $this->fetchAll($sql);
    }

    public function atualizaStatusGeral() {
        $where = $this->select()->where("(usr_mestre = 'N' OR usr_mestre is null OR usr_modulos != 'A' )and usr_mestre != 'S'")->getPart(Zend_Db_Table_Select::WHERE);
        $where = $where[0];
        $data = array('usr_ativo' => "N");
        return $this->update($data, $where);
    }

    public function importProfissionais($data) {

        if (empty($data)){
            return false;
        }

        try {
            $lotacoes = $data[lotacoes];
            unset($data[lotacoes]);

            //echo "<pre>".print_r($data,1);die();
            $usr_valida = $this->verificaSeJáExiste($data[usr_cpf]);
            if ($usr_valida->qtd > 0) {
                $data["usr_codigo"] = $usr_valida->usr_codigo;
                unset($data["usr_senha"]);
                unset($data["usr_login"]);
            }
            $usr_codigo = $this->salvar($data);

            $this->salvaDependenciasUsuarios($usr_codigo, $data['lotacoes']);
        } catch (Exception $exc) {
            throw new Zend_Validate_Exception("Erro ao importar usuarios" . $exc->getMessage(), 1000);
        }
        return true;
    }

    private function salvaDependenciasUsuarios($usr_codigo = FALSE, $lotacao = FALSE) {
        $tbUni = new Application_Model_Unidade();
        $tbEqp = new Application_Model_TbEquipe();
        $tbMesp = new Application_Model_MedicoEspecialidade();
        $tbUniUsr = new Application_Model_UnidadeUsuarios();
        $tbUsrEqp = new Application_Model_UsuariosEquipe();
        $tbEsp = new Application_Model_Especialidade();
        // echo $usr_codigo."<pre>".print_r($lotacao,1);die();
        foreach ($lotacao as $lot) {
            $uni_codigo = $tbUni->getUnidadePorCnes($lot['cnes'])->uni_codigo;
            if ($uni_codigo) {
                $array_uni_usr = array("uni_codigo" => $uni_codigo,
                                       "usr_codigo" => $usr_codigo);
                // die("aaaaaaaaeeeeee");
                $tbUniUsr->salvar($array_uni_usr);
                // echo 'AQUI->'.$uni_codigo . "-" . $usr_codigo . "<br/>";
            }
            $tbMesp->salvar($array_uni_usr);

            if ($lot[co_ine] != "") {
                $co_equipe = $tbEqp->getEquipePorIne($lot['co_ine'])->co_seq_equipe;
                $array_usr_equipe = array(
                    "co_equipe" => $co_equipe,
                    "usr_codigo" => $usr_codigo
                );

                $tbUsrEqp->salvar($array_usr_equipe);
            }

            $esp_codigo = $tbEsp->getEspecialidadePorCbo($lot['co_cbo'])->esp_codigo;
            //echo "a".$esp_codigo."<br/>";
            $array_med_esp = array("med_codigo" => $usr_codigo,
                "esp_codigo" => $esp_codigo,
                "mes_ativo" => "A",
                "uni_codigo" => $uni_codigo);
            $mes_valida = $tbMesp->verificaSeJáExiste($usr_codigo, $esp_codigo, $uni_codigo);
            
            if ($mes_valida->qtd > 0) {
                $array_med_esp["mes_codigo"] = $mes_valida->mes_codigo;
            }
        }
        return true;
    }

    public function verificaSeJáExiste($usr_cpf = FALSE) {
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("usr" => "usuarios"), array("qtd" => "count(*)", "usr_codigo"))
                ->where("replace(replace(replace( usr_cpf, '.', ''),'-',''),'.','') = '$usr_cpf'")
                ->group("usr_codigo");

        return $this->fetchRow($where);
    }

    public function UsuariosSemCpf() {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("usr" => "usuarios"))
                ->where("usr_cpf is null or usr_cpf = ''")
                ->where("usr_ativo != 'N'")
                ->where("(usr_modulos not in ('A','T') OR USR_MODULOS IS NULL)")
                ->where("(USR_MESTRE IS NULL OR USR_MESTRE = 'N')")
                ->order("usr_nome");

        return $this->fetchAll($sql);
    }

    public function getUsuariosComUnidades() {
        $where = $this->select(FALSE)
                ->distinct()
                ->setIntegrityCheck(FALSE)
                ->from(array("usr" => "usuarios"), array("usr_nome", "usr_codigo"))
                ->join(array("unu" => "unidade_usuarios"), "unu.usr_codigo=usr.usr_codigo", "")
                ->join(array("uni" => "unidade"), "uni.uni_codigo=unu.uni_codigo", "")
                ->where("uni_cnes is not null")
                ->where("usr_ativo = 'S'")
                ->order("usr_nome");

        return $this->fetchAll($where);
    }

    public function usuariosEquipes($usrCodigo = FALSE, $uniCodigo = FALSE) {
        
        $sql = $this->getDefaultAdapter()->query(
            "SELECT distinct(u.usr_codigo, usr_nome, cnes_numero,nu_ine), nu_ine from usuarios as u 
                join medico_especialidade as m 
                    on m.med_codigo = u.usr_codigo 
                join unidade_usuarios as uu 
                    on uu.usr_codigo = u.usr_codigo
                join unidade as un 
                    on un.uni_codigo = uu.uni_codigo 
                join usuarios_equipe as ue 
                    on ue.usr_codigo = ue.usr_codigo 
                join tb_equipe as te 
                    on ue.co_equipe = te.co_seq_equipe 
            where uni_cnes is not null 
                and usr_ativo = 'S'
                and usr_tipo_medico in('M','E','A','D','P','C','F')
                and u.usr_codigo = $usrCodigo"
        )->fetchAll();
        // echo "<pre>";print_r($sql);die();
        return ($sql[0]['nu_ine']);
    }
    
    public function getUsuariosEquipes($usrCodigo = FALSE, $uniCodigo = FALSE) {
        
        $sql = $this->getDefaultAdapter()->query(
            "SELECT distinct(u.usr_codigo, usr_nome, cnes_numero,nu_ine), nu_ine from usuarios as u 
                join medico_especialidade as m 
                    on m.med_codigo = u.usr_codigo 
                join unidade_usuarios as uu 
                    on uu.usr_codigo = u.usr_codigo
                join unidade as un 
                    on un.uni_codigo = uu.uni_codigo 
                join usuarios_equipe as ue 
                    on ue.usr_codigo = ue.usr_codigo 
                join tb_equipe as te 
                    on ue.co_equipe = te.co_seq_equipe 
            where uni_cnes is not null 
                and usr_ativo = 'S'
                and usr_tipo_medico in('M','E','A','D','P','C','F')
                and u.usr_codigo = $usrCodigo"
        )->fetchAll();
        // echo "<pre>";print_r($sql);die();
        return ($sql);
    }

    public function getNomeProfissional($usr_codigo=FALSE){
        $where = $this->select()
                      ->setIntegrityCheck(FALSE)
                      ->from(array("usr" => "usuarios"),array("usr_codigo","usr_nome"))
                      ->where("usr_codigo=?",$usr_codigo);
        return $this->fetchRow($where);
    }

    public function getAssinaturaByUsuario($usr_codigo=FALSE){
        
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("usr" => "usuarios"), array("usr_assinatura"))
                ->where("usr_codigo='$usr_codigo'");
        return $this->fetchRow($where);
    }

    public function getUsuarios(){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("u" => "usuarios"), array("u.usr_codigo", "u.usr_nome"))
                    ->order("u.usr_nome");
        return $this->fetchAll($sql);
    }

    public function getCRM($nomeMed){
        $sql = $this->select(FALSE)
            ->setIntegrityCheck(FALSE)
            ->from(array("u" => "usuarios"), array("crm"=>"u.usr_num_conselho"))
            ->where("usr_nome ilike '%$nomeMed%'")
            ->where("usr_ativo = 'S'");
        // die($sql);
        $dados = $this->fetchRow($sql);
                
        return $dados->toArray();
    }

    // public function novaSenha($novaSenha, $usr_cpf){
    //     $sql = $this
    //     ->getDefaultAdapter()
    //     ->query("UPDATE usuarios SET usr_senha = $novaSenha where usr_cfp = $usr_cpf ")->fetchRow();
    //     // echo "<pre>";print_r($sql);die();
    //     return $sql;
    // }
}