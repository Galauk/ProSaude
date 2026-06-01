<?php
include_once $_SESSION['root'].$_SESSION['modulo']."global.php";
//include_once "../../global.php";
class BancoCadastroDomiciliar {

    public function getDadosCadastroDomiciliar() {
        $sql = "
                SELECT DISTINCT
                        co_cds_cad_domiciliar,
                        usr.cnes_cod_cns AS cns_profissional,
                        uni.uni_cnes AS cnes_unidade,
                        uni.uni_codigo_ibge AS codigo_ibge_municipio,
                        tbe.nu_ine,
                        dom.dom_data_cadastro AS data_atendimento,
                        bai.bai_nome AS bairro,
                        REPLACE(rua.rua_cep,'-','') AS cep,
                        cid.cid_codigo_ibge,
                        dom.dom_complemento AS complemento,
                        rua.rua_nome AS nome_logradouro,
                        tpl.co_tipo_logradouro as tipo_logradouro_numero_dne,
                        dom.dom_numero AS numero,
                        uf.uf_codigo AS estado,
                        (SELECT tbpd.co_pergunta_detalhe || ' L'
                         FROM tb_cds_domicilio_resposta AS tbdr
                         INNER JOIN tb_pergunta_detalhe AS tbpd ON tbdr.co_pergunta_detalhe=tbpd.co_pergunta_detalhe
                         WHERE tbdr.co_cds_cad_domiciliar=tdcdr.co_cds_cad_domiciliar AND tbdr.co_pergunta = 57 LIMIT 1)  AS situacao_moradia,
                        (SELECT tbpd.co_pergunta_detalhe || ' L'
                         FROM tb_cds_domicilio_resposta AS tbdr
                         INNER JOIN tb_pergunta_detalhe AS tbpd ON tbdr.co_pergunta_detalhe=tbpd.co_pergunta_detalhe
                         WHERE tbdr.co_cds_cad_domiciliar=tdcdr.co_cds_cad_domiciliar AND tbdr.co_pergunta = 58 LIMIT 1)  AS localizacao
                FROM  tb_cds_domicilio_resposta AS tdcdr
                INNER JOIN domicilio AS dom ON tdcdr.co_cds_cad_domiciliar=dom.dom_codigo
                INNER JOIN usuarios AS usr ON dom.usr_codigo=usr.usr_codigo
                INNER JOIN unidade AS uni ON dom.uni_codigo=uni.uni_codigo
                INNER JOIN rua ON dom.rua_codigo=rua.rua_codigo
                INNER JOIN bairro AS bai ON rua.bai_codigo=bai.bai_codigo
                LEFT JOIN distrito AS dis ON bai.dis_codigo=dis.dis_codigo
                INNER JOIN cidade AS cid ON (bai.cid_codigo=cid.cid_codigo OR dis.cid_codigo=cid.cid_codigo)
                INNER JOIN estado AS uf ON cid.uf_codigo=uf.uf_codigo
                LEFT JOIN tb_equipe AS tbe ON dom.cod_equipe=tbe.co_seq_equipe
                INNER JOIN tb_ms_tipo_logradouro tpl ON tpl.co_tipo_logradouro = rua.co_tipo_logradouro
                WHERE uuid_ficha IS NULL
                ";
        //die($sql);
        // echo "<pre>";print_r($sql);die();
        $query = pg_query($sql) or die(pg_last_error());
        return pg_fetch_all($query);
    }

    public function getNumDadosCadastroDomiciliar() {
        $sql = "SELECT DISTINCT
                    co_cds_cad_domiciliar
                FROM tb_cds_domicilio_resposta AS tdcdr
                INNER JOIN domicilio AS dom ON tdcdr.co_cds_cad_domiciliar = dom.dom_codigo
                INNER JOIN usuarios AS usr ON dom.usr_codigo = usr.usr_codigo
                INNER JOIN unidade AS uni ON dom.uni_codigo = uni.uni_codigo
                INNER JOIN rua ON dom.rua_codigo = rua.rua_codigo
                INNER JOIN bairro AS bai ON rua.bai_codigo = bai.bai_codigo
                LEFT JOIN distrito AS dis ON bai.dis_codigo=dis.dis_codigo
                INNER JOIN cidade AS cid ON (bai.cid_codigo = cid.cid_codigo OR dis.cid_codigo = cid.cid_codigo)
                INNER JOIN estado AS uf ON cid.uf_codigo = uf.uf_codigo
                LEFT JOIN tb_equipe AS tbe ON dom.cod_equipe = tbe.co_seq_equipe
                WHERE
                    uuid_ficha IS NULL OR uuid_ficha = ''";
        $query = pg_query($sql) or die(pg_last_error());
        return pg_num_rows($query);
    }

    public function getDadosFamilia($domCodigo) {
        $sql = "SELECT
                    usu_codigo_responsavel,
                    usu.usu_datanasc,
                    usu.usu_cartao_sus,
                    usu.usu_prontuario,
                    dom.dom_codigo,
                    dom.dom_data_cadastro
                FROM domicilio AS dom
                INNER JOIN usuario AS usu ON usu.usu_codigo=dom.usu_codigo_responsavel
                WHERE
                    dom.dom_codigo = $domCodigo AND
                    usu.usu_cartao_sus <> ''";
        $query = pg_query($sql);
        return pg_fetch_all($query);
    }

    public function atualizaStatus($uuid, $codigo) {
        $sql = "UPDATE tb_cds_domicilio_resposta SET uuid_ficha = '" . $uuid . "' WHERE co_cds_cad_domiciliar = '" . $codigo . "'";
        $query = pg_query($sql);
    }

}
?>
