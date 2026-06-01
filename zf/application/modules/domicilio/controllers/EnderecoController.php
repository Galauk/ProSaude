<?php
class Domicilio_EnderecoController extends Zend_Controller_Action {
    
    public function init() {
    }
    
    public function indexAction(){
        
    }
    
    public function formAction(){
        $this->view->title = "Cadastro de Endereço";
    }
    
    public function buscarAction(){
        $tbTbe = new Application_Model_Endereco();
        $this->view->dados = $tbTbe->buscar($this->_request->getParam("term")); 
        return $this->render("dados",NULL,TRUE);
    }
    
    public function salvarAction(){
        Zend_Db_Table::getDefaultAdapter()->beginTransaction();
        try{
            // Validação Municipios
            
            // Validação Bairros
            $bairro = $this->_request->getPost("bairro");
            $bairro_codigo = $this->_request->getPost("bairro_codigo");
            if (empty($bairro_codigo))
                $this->validaBairro($bairro,$bairro_codigo);
            
            Zend_Db_Table::getDefaultAdapter()->commit();
        } catch (Exception $exc) {
            Zend_Db_Table::getDefaultAdapter()->roolBack();
            $this->view->dados = $exc->getMessage();
            return $this->render("dados",NULL,TRUE);
        }
    }
    
    /*public function validaBairro($bairro,$bairro_codigo) {
        // Verifica se bairro já não esta cadastrado
        $tbBai = new Application_Model_TbBairro();
        $bairro_codigo = $tbBai->getDadosBairro($bairro)->co_bairro;
        // Se bairro não existir, insere
        if (empty($bairro_codigo)){
            $dados_bairro = array(
                "co_localidade" => $municipio_codigo,
                "no_bairro" => $bairro,
                "no_bairro_filtro" => strtolower($bairro)
            );
            $bairro_codigo = $tbBai->salvar($dados_bairro);
        }
        return $bairro_codigo;
    }
    
    public function validaMunicipio($municipio,$municipio_codigo) {
        // Verifica se bairro já não esta cadastrado
        $tbLoc = new Application_Model_TbLocalidade();
        $municipio_codigo = $tbLoc->getDadosMunicipio($municipio)->co_localidade;
        // Se bairro não existir, insere
        if (empty($municipio_codigo)){
            $dados_municipio = array(
                "co_localidade" => $municipio_codigo,
                "no_localidade" => $municipio,
                "no_localidade_abreviatura" => $municipio,
                "no_localidade_filtro" => strtolower($municipio)
            );
            $bairro_codigo = $tbBai->salvar($dados_bairro);
            return $bairro_codigo;
        }
    }*/
    
}
?>
