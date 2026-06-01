    public function buscarIdadeSexoAction() {
        $this->_helper->layout->disableLayout();
        $tbUsu = new Application_Model_Usuario();
        $id = $this->_getParam("idUsuario", FALSE);
        $datanascimento = $this->_getParam("dataNascimento", FALSE);
        $idade = null;
        $resultado = $tbUsu->validaSexo($id);
        // echo "<pre>";print_r($resultado);die();
        if ($resultado != null) {
            // Separa em dia, mês e ano
            list($dia, $mes, $ano) = explode('/', $datanascimento);
           
            // Descobre que dia é hoje e retorna a unix timestamp
            $hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
            // Descobre a unix timestamp da data de nascimento do fulano
            $nascimento = mktime( 0, 0, 0, $mes, $dia, $ano);
           
            $idade = floor((((($hoje - $nascimento) / 60) / 60) / 24) / 365.25);
            // echo "<pre>";print_r($idade);die();
            if ($idade > 9 && $idade <= 60) {
                echo json_encode('true');
            } else{
                echo json_encode('false');
            }
        } 
        exit();
        return $this->render("dados", NULL, TRUE);
    }