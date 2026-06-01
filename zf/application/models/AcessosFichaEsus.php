<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

/**
 * Essa agenda é resposável pelo novo agendamento
 * tod o agendar exames e consultas, por quantidade (cota) e valor
 */
class Application_Model_AcessosFichaEsus extends Elotech_Db_Table_Abstract {

    protected $_name = 'acessos_ficha_esus';
    protected $_primary = 'afe_codigo';
    protected $_dependentTables = array();

    public function salvar(array $data) {
       //throw new Zend_Validate_Exception("<pre>".print_r($data,1)."</pre>");
        return parent::salvar($data);
    }

    
    /**
     * Recebe um array de dados, e insere cada um deles como um item do agendamento
     * @param array $arr
     * @param int $age_codigo código da agenda (pai)
     */
    public function salvarDoArray($arr, $age_codigo) {
            $this->salvar($dados);
    }


    public function getAcessosFichaEsus($cbo = FALSE,$ficha = FALSE) {
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->distinct()
                ->from(array("afe" => "acessos_ficha_esus"),array("afe_codigo"))
                ->where("afe_cbo=?",$cbo)
                ->where("afe_ficha=?",$ficha);
        return $this->fetchRow($where);
    }

    public function getBioquimicosResponsavelAgendamento($age_codigo = FALSE) {
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->distinct()
                ->from(array("agebr" => "agenda_bioquimicos_responsavel"), array("usr_codigo"))
                ->join(array("usr" => "usuarios"), "agebr.usr_codigo=usr.usr_codigo", array("usr_nome", "usr_num_conselho", "cnes_sigla_est"))
                ->join(array("con" => "conselho"), "con.con_codigo=usr.con_codigo", "con_descricao")
                ->where("age_codigo=$age_codigo");

        return $this->fetchAll($where);
    }

    public function getBioquimicosResponsavelPorUnidade($idUnidade) {
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->distinct()
                ->from(array("agebr" => "agenda_bioquimicos_responsavel"), array("usr_codigo"))
                ->join(array("usr" => "usuarios"), "agebr.usr_codigo=usr.usr_codigo", array("usr_nome", "usr_num_conselho", "cnes_sigla_est"))
                ->join(array("con" => "conselho"), "con.con_codigo=usr.con_codigo", "con_descricao")
                ->join(array("uu" => "unidade_usuarios"), "uu.usr_codigo=usr.usr_codigo", "uu.uni_codigo")
                ->where("uu.uni_codigo=$idUnidade");

        return $this->fetchAll($where);
    }

    public function getQtdAtendimentosDiaConvItem($coni_codigo, $dia) {
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("agei" => "agenda_itens"), array("count(agei_codigo) as quantidade"))
                ->where("agei.agei_data = '$dia'")
                ->where("agei.agei_status NOT IN ('F', 'C')")
                ->where("agei.coni_codigo = $coni_codigo");

        //die($where);
        return $this->fetchRow($where);
    }

    /**
     * @param $coni_codigo
     * @param $conv_codigo
     * @param $dia
     * @return mixed
     */
    public function getQtdAtendimentosMesConvItem($coni_codigo, $conv_codigo, $dia) {
        $tbConv = new Application_Model_Convenio();
        $diaMes = $tbConv->getDados($conv_codigo)->dia_mes;

        $dataCompleta = explode('-', $dia);
        $dataDia = $dataCompleta[2];
        $dataMes = $dataCompleta[1];
        $dataAno = $dataCompleta[0];

        if ($dataDia >= $diaMes) {
            $mesInicio = $dataMes;
            $mesFim = $dataMes + 1;
        } else {
            $mesInicio = $dataMes - 1;
            $mesFim = $dataMes;
        }
        $anoInicio = $dataAno;
        if ($mesInicio < 1) {
            $mesInicio = 12;
            $anoInicio = $dataAno - 1;
        } elseif ($mesInicio > 12) {
            $mesInicio = 1;
            $anoInicio = $dataAno + 1;
        }
        $anoFim = $dataAno;
        if ($mesFim > 12) {
            $mesFim = 1;
            $anoFim = $dataAno + 1;
        }
        
        $ultimoDia = $diaMes;
        if ($diaMes == 1) {
            $mesFim = $mesFim - 1;
            if ($mesFim == 0) {
                $mesFim = 12;
            }
            if ($mesFim > 12) {
                $mesFim = 1;
                $anoFim = $anoFim + 1;
            }
            $ultimoDia = date("t", strtotime("$anoFim-$mesFim-01"));
        } else {
            $ultimoDia = $ultimoDia - 1;
        }

        
        $temp = date("t", strtotime("$anoInicio-$mesInicio-01"));
        if ($temp < $diaMes) {
            $diaMes = $temp;
        }

        $periodoInicio = $anoInicio . "-" . str_pad($mesInicio, 2, "0", STR_PAD_LEFT) . "-" . str_pad($diaMes, 2, "0", STR_PAD_LEFT);
        $periodoFim = $anoFim . "-" . str_pad($mesFim, 2, "0", STR_PAD_LEFT) . "-" . str_pad($diaMes - 1, 2, "0", STR_PAD_LEFT);

        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("agei" => "agenda_itens"), array("count(agei_codigo) as quantidade"))
                ->join(array("coni" => "convenio_itens"), "agei.coni_codigo = coni.coni_codigo", "sum(coni_valor) as valor")
                ->where("agei.agei_data BETWEEN '$periodoInicio' AND '$periodoFim'")
                ->where("agei.agei_status NOT IN ('F', 'C')")
                ->where("agei.coni_codigo = $coni_codigo");
        //die($where);
        return $this->fetchRow($where);
    }

    public function getQtdAtendimentosMesConvenio($conv_codigo, $dia) {
        $tbConv = new Application_Model_Convenio();
        $diaMes = $tbConv->getDados($conv_codigo)->dia_mes;

        $dataCompleta = explode('-', $dia);
        $dataDia = $dataCompleta[2];
        $dataMes = $dataCompleta[1];
        $dataAno = $dataCompleta[0];

        if ($dataDia >= $diaMes) {
            $mesInicio = $dataMes;
            $mesFim = $dataMes + 1;
        } else {
            $mesInicio = $dataMes - 1;
            $mesFim = $dataMes;
        }
        $anoInicio = $dataAno;
        if ($mesInicio < 1) {
            $mesInicio = 12;
            $anoInicio = $dataAno - 1;
        } elseif ($mesInicio > 12) {
            $mesInicio = 1;
            $anoInicio = $dataAno + 1;
        }
        $anoFim = $dataAno;
        if ($mesFim > 12) {
            $mesFim = 1;
            $anoFim = $dataAno + 1;
        }
        $ultimoDia = $diaMes;
        if ($diaMes == 1) {
            $mesFim = $mesFim - 1;
            if ($mesFim == 0) {
                $mesFim = 12;
            }
            if ($mesFim > 12) {
                $mesFim = 1;
                $anoFim = $anoFim + 1;
            }
            $ultimoDia = date("t", strtotime("$anoFim-$mesFim-01"));
        } else {
            $ultimoDia = $ultimoDia - 1;
        }

        
        $temp = date("t", strtotime("$anoInicio-$mesInicio-01"));
        if ($temp < $diaMes) {
            $diaMes = $temp;
        }

        
        $periodoInicio = $anoInicio . "-" . str_pad($mesInicio, 2, "0", STR_PAD_LEFT) . "-" . str_pad($diaMes, 2, "0", STR_PAD_LEFT);
        $periodoFim = $anoFim . "-" . str_pad($mesFim, 2, "0", STR_PAD_LEFT) . "-" . str_pad($ultimoDia, 2, "0", STR_PAD_LEFT);

        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("agei" => "agenda_itens"), array("count(agei_codigo) as quantidade"))
                ->join(array("coni" => "convenio_itens"), "agei.coni_codigo = coni.coni_codigo", array("sum(coni_valor) as valor"))
                ->where("agei.agei_data BETWEEN '$periodoInicio' AND '$periodoFim'")
                ->where("agei.agei_status NOT IN ('F', 'C')")
                ->where("coni.conv_codigo = $conv_codigo");
        //die($where);
        return $this->fetchRow($where);
    }

    public function getQtdAtendimentosDiaConvenio($conv_codigo, $dia) {
        $subSelect = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("agei" => "agenda_itens"), array("count(agei_codigo) as quantidade", "agei_data"))
                ->join(array("coni" => "convenio_itens"), "agei.coni_codigo = coni.coni_codigo", "coni.coni_codigo")
                ->group(array("agei_data", "coni.coni_codigo"))
                ->where("agei.agei_data = '$dia'")
                ->where("agei.agei_status NOT IN ('F', 'C')")
                ->where("coni.conv_codigo = $conv_codigo");

        $subSelectString = '(' . $subSelect->__toString() . ')';

        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("temp" => new Zend_Db_Expr($subSelectString)), array("sum(quantidade) as quantidade", "agei_data"))
                ->group(array("agei_data"));

        //die($where);
        return $this->fetchRow($where);
    }

    public function getQtdAtendimentosTotalConvenio($conv_codigo) {
        $tbConv = new Application_Model_Convenio();
        $dadosConvenio = $tbConv->getDados($conv_codigo);

        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("agei" => "agenda_itens"), "")
                ->join(array("coni" => "convenio_itens"), "agei.coni_codigo = coni.coni_codigo", "sum(coni_valor) as valor")
                ->join(array("conv" => "convenio"), "coni.conv_codigo = conv.conv_codigo", array("count(conv.conv_codigo) as quantidade"))
                ->where("conv.conv_codigo = $conv_codigo")
                ->where("agei.agei_status NOT IN ('F', 'C')");
        if ($dadosConvenio->data_inicial != '' && $dadosConvenio->data_final != '') {
            $where->where("agei.agei_data BETWEEN conv.data_inicial AND conv.data_final");
        }
        //die($where);
        return $this->fetchRow($where);
    }

}
