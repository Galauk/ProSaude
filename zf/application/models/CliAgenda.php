<?php

class Application_Model_CliAgenda extends Application_Model_DbTable_Usuarios {
    
    public function buscarAgenda($procedimento){
        $sql = $this->select(FALSE)
            ->setIntegrityCheck(FALSE)
            ->distinct()
            ->from(array("age" => "cli_agenda"), array("age.id_cli_medicos"))
            ->join(array("cli" => "cli_medicos"), "cli.id_cli_medicos = age.id_cli_medicos", array("cli.nome"))
            ->where("cli.cbos = '2253'")
            ->where("cli.nome ilike '%$procedimento%'");
        $all = $this->fetchAll($sql);
        $out = array();

        foreach ($all as $procedimentos) {
            $out [] = array(
                "id" => $procedimentos->id_cli_medicos,
                "label" => $procedimentos->nome,
                "data" => $procedimentos->toArray()
            );
        }

        // echo "<pre>";var_dump($out);die();

        if (!count($out)) {
            $out [] = array(
                "id" => 0,
                "label" => "Nenhum item encontrado",
                "data" => array("usr_codigo" => "0", "usr_nome" => "", "categoria" => "Nenhum médico encontrado")
            );
        }

        return $out;


    }

    public function buscarPaciente($procedimento){
        $sql = $this->select(FALSE)
            ->setIntegrityCheck(FALSE)
            ->distinct()
            ->from(array("age" => "cli_agenda"), array("age.id_cli_medicos","usu.usu_codigo"))
            ->join(array("usu" => "usuario"), "usu.usu_codigo = age.id_cli_clientes", array("usu.usu_nome"))
            ->where("usu.usu_nome ilike '%$procedimento%'");
            //die($sql);
        $all = $this->fetchAll($sql);
        
        $out = array();

        foreach ($all as $procedimentos) {
            $out [] = array(
                "id" => $procedimentos->usu_codigo,
                "label" => $procedimentos->usu_nome,
                "data" => $procedimentos->toArray()
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
}
