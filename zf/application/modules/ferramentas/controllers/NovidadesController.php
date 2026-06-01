<?php

class Ferramentas_NovidadesController extends Zend_Controller_Action {
    
    public function init(){
        $this->view->title = "Novidades da Versão";
    }

    public function novidadesFormAction(){

    }

    public function novidadesAjaxAction(){
        $dir    = '../novidades/';
        $files1 = scandir($dir);
        //die(var_dump($files1));

        $this->view->dados = $files1;

        return $this->render("dados", NULL, TRUE);
    }
    
    public function indexAction(){
        $arq = $this->_request->getParam("arquivo");
        if($arq == null){
            $tbconf = new Application_Model_Configuracao();
            $versao = $tbconf->getVersaoSaude();
            $arqAux = $versao[0]->conf_valor_string;
            $arq = "WebSocialSaude_" . str_replace(".", "_", $arqAux) . ".xml";         
        }
        //die(var_dump($arq));
        //die(var_dump(file_exists('../novidades/WebSocialSaude_4_5_9.xml')));
        $xml = simplexml_load_file('../novidades/' . $arq);
        $this->view->xml= $xml;
        $dir    = '../novidades/';
        $files1 = scandir($dir);
        unset($files1[0]); //remove "."
        unset($files1[1]); //remove ".."
        $this->view->dados = $files1;
    }

    public function editarAction(){
        $nov_nome = $this->_request->getParam("nov_nome");
        $xml = simplexml_load_file("../novidades/" . $nov_nome);
        //header("Content-Type: application/json", true);
        return $this->_helper->json($xml);    
    }

    public function deletarAction(){
        $nov_nome = $this->_request->getParam("nov_nome");
        unlink('../novidades/'.$nov_nome);

        return $this->_redirect("/ferramentas/novidades/novidades-form");
    }

    public function salvarAction(){
        $versao = $this->_request->getParam("versao");
        $autor = $this->_request->getParam("autor");
        $desc = $this->_request->getParam("desc");
        date_default_timezone_set('America/Sao_Paulo');
        $date = date("d/m/Y H:i:s");
        
        $nome = "../novidades/WebSocialSaude_" . str_replace(".", "_", $versao) . ".xml";
        $newXml = fopen($nome, "w");
        fclose($newXml);
        //die("here");
        
        $versao = htmlentities($versao, ENT_COMPAT, 'UTF-8', false);
        $date = htmlentities($date, ENT_COMPAT, 'UTF-8', false);
        $autor = htmlentities($autor, ENT_COMPAT, 'UTF-8', false);
        $desc = htmlentities($desc, ENT_COMPAT, 'UTF-8', false);
        
        $xml = new SimpleXMLElement('<document></document>');
        //die(var_dump($desc));
        $desc = str_replace("&lt;", "", $desc);
        $desc = str_replace("p&gt;", "", $desc);
        $desc = str_replace("/", "", $desc);
        $desc = str_replace("&", "&amp;", $desc);
        //die(var_dump($desc));
        $xml->addChild('versao', $versao);
        $xml->addChild('date', $date);
        $xml->addChild('autor', $autor);
        $arrayDesc = explode("\n",$desc);
        $tag = 0;
        //die(var_dump($arrayDesc));
        foreach($arrayDesc as $aDesc){
            $xml->addChild('desc'. $tag, $aDesc);
            $tag++;
        }  
        
        $doc = $xml->asXML();
        file_put_contents($nome, $doc);
        //die(var_dump($xml->asXML()));
        // $doc = new DOMDocument('1.0');
        // $doc->formatOutput = true;
        // $doc->preserveWhiteSpace = true;
        // $doc->loadXML($xml->asXML(), LIBXML_NOBLANKS);
        // $doc->save($nome);

        //die(var_dump($doc));
        return $this->_redirect("/ferramentas/novidades/novidades-form");
    }

}