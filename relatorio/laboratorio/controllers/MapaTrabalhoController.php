<?php

class Laboratorio_MapaTrabalhoController extends Zend_Controller_Action {

    public function init(){

    }

    public function indexAction(){



    }

    public function imprimirAction(){

        $this->_helper->layout->setLayout("simples");
        $proc_codigos = $this->_request->getParam("proc_codigos",FALSE);
        $age_codigo = $this->_request->getParam("age_codigo",FALSE);
        $tbCat = new Application_Model_CategoriaDeExames();
        $tbIte = new Application_Model_ItensAnalise();

        $categorias = $tbCat->getCaregorias($age_codigo)->toArray();
        $array_mapa = array();
        $pro_codigo = "";

        foreach($categorias as $categoria){
            $itens_caregoria = array();

            $itens = $tbCat->getMapaDeTrabalho($categoria[cte_codigo],$age_codigo)->toArray();

            foreach($itens as $item){
                $itens_caregoria[$item[proc_codigo]] = array("proc_nome"=>$item[proc_nome],
                                                             "proc_codigo"=>$item[proc_codigo],

                                                             "itens_analise"=>$tbIte->getItens($item[txa_codigo])->toArray());
            }

            $array_mapa[$categoria["cte_codigo"]] = array("cte_cargo"=>$categoria[cte_cargo],
                                                        "cte_codigo"=>$categoria[cte_codigo],
                                                        "usu_codigo"=>$categoria[usu_codigo],
                                                        "age_codigo"=>$item[age_codigo],
                                                        "usu_nome"=>$categoria[usu_nome],
                                                        "usu_datanasc"=>$categoria[usu_datanasc],
                                                        "usu_sexo"=>$categoria[usu_sexo],
                                                        "col_data_coleta"=>$item[col_data_coleta],
                                                        "med_nome"=>($categoria[med_nome] ? $categoria[med_nome] : $categoria[usr_nome] ),
                                                        "itens"=>$itens_caregoria);

            if(!count($itens))
                unset ($array_mapa[$categoria[cte_codigo]]);
        }
        $this->view->itens = $array_mapa;

    }


}
