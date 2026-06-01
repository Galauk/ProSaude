<?php

/*
 * @author: Leonardo Abreu.
 * @since : 03/03/2016.
 * @description: Script de correção da tabela esus_atendimento_individual, pois a trigger estava desabilitada.
 * 
 */
session_start();
require_once '../global.php';
define("DS", DIRECTORY_SEPARATOR);
define("COMUM", dirname(SOCIAL) . DS . "WebSocialComum" . DS);
define("LINKCOMUM", "/WebSocialComum");

$base = bd_base();

echo "Conectando com a base de dados de: " . $base . "!";
echo "..<br>";
echo "Começando conversão de dados..";
echo "<br>";

criaFuncaoNoBanco();
executa();
alterarStatusTrigger();
dropFunction();

//Percorre tudo
function executa() {
    // die("chega aqui primeiro");
    $sql = "SELECT DISTINCT
                ate.ate_codigo,
                age.tat_codigo AS eai_tipo_atendimento,
                tbl.co_local_atend,
                usr.cnes_cod_cns AS eai_profissional_cns,
                esp.cod_cbo AS eai_cbo_codigo_2002,
                uni.uni_cnes AS eai_cnes,
                ate.ate_data AS eai_dtatendimento,
                uni.uni_codigo_ibge AS eai_codigo_ibge_mun,
                usu.usu_cartao_sus AS eai_num_cartao_sus,
                usu.usu_datanasc AS eai_dtnascimento,
                usu.usu_prontuario AS eai_numprontuario, 
                usu.usu_sexo
        FROM  atendimento ate
        INNER JOIN agendamento age ON ate.age_codigo = age.age_codigo
        INNER JOIN especialidade esp ON age.esp_codigo = esp.esp_codigo
        INNER JOIN usuarios usr ON ate.med_codigo = usr.usr_codigo
        INNER JOIN usuario usu ON ate.usu_codigo = usu.usu_codigo
        INNER JOIN unidade uni ON ate.uni_codigo = uni.uni_codigo
        INNER JOIN tb_local_atend tbl ON ate.co_local_atend = tbl.co_local_atend
        INNER JOIN rl_cds_atend_individual_ciap rlai ON ate.ate_codigo = rlai.ate_codigo
        INNER JOIN rl_cds_atend_individual_condut rlaic ON ate.ate_codigo = rlaic.ate_codigo
        ORDER BY ate.ate_codigo";

    $result = pg_query($sql);

    if (!$result) {
        die("Erro ao ler as Tabelas Principais!");
    }

    $registros = pg_fetch_array($result);
    $quantidadeRegistro = pg_num_rows($result);

    while ($registros) {
        $dadosRetorno[] = $registros;
        $registros = pg_fetch_array($result);
    }

    for ($i = 0; $i < $quantidadeRegistro; $i++) {

        $codigoAtendimento = $dadosRetorno[$i][ate_codigo];

        $verificaSeJaTemRegistro = "SELECT * FROM esus_atendimento_individual WHERE ate_codigo = $codigoAtendimento";
        $resultVerifica = pg_query($verificaSeJaTemRegistro);

        if (pg_affected_rows($resultVerifica) == 0) {
            $update = "SELECT atualizaAtendimentoIndividualEsus($codigoAtendimento)";

            try {
                $result = pg_query($update);
            } catch (Exception $exc) {
                die("Erro ao Executar a Função 'atualizaAtendimentoIndividualEsus'!" . $exc);
            }
            echo "Código do Atendimento Inserido na Tabela do Esus: " . $codigoAtendimento . "<br>";
        }
    }
}

function bd_base() {
    $arquivoXml = COMUM . "library/conf/dbConfig.xml";
    $xml = simplexml_load_file($arquivoXml);
    $dbname = base64_decode($xml->conexao->dbname);
    return $dbname;
}

function criaFuncaoNoBanco() {
    $body = "BODY";
    $funcao = " CREATE OR REPLACE FUNCTION atualizaAtendimentoIndividualEsus(bigint)
                RETURNS void AS 
                $" . $body . "$
                DECLARE
                     ate_codigo_recebido alias for $1;
                     linha RECORD;
                     eai_sexo VARCHAR;
                BEGIN
                    FOR linha IN
                        SELECT DISTINCT
                                ate.ate_codigo,
                                age.tat_codigo AS eai_tipo_atendimento,
                                tbl.co_local_atend,
                                usr.cnes_cod_cns AS eai_profissional_cns,
                                esp.cod_cbo AS eai_cbo_codigo_2002,
                                uni.uni_cnes AS eai_cnes,
                                ate.ate_data AS eai_dtatendimento,
                                uni.uni_codigo_ibge AS eai_codigo_ibge_mun,
                                usu.usu_cartao_sus AS eai_num_cartao_sus,
                                usu.usu_datanasc AS eai_dtnascimento,
                                usu.usu_prontuario AS eai_numprontuario, 
                                usu.usu_sexo
                        FROM  atendimento ate
                        INNER JOIN agendamento age ON ate.age_codigo = age.age_codigo
                        INNER JOIN especialidade esp ON age.esp_codigo = esp.esp_codigo
                        INNER JOIN usuarios usr ON ate.med_codigo = usr.usr_codigo
                        INNER JOIN usuario usu ON ate.usu_codigo = usu.usu_codigo
                        INNER JOIN unidade uni ON ate.uni_codigo = uni.uni_codigo
                        INNER JOIN tb_local_atend tbl ON ate.co_local_atend = tbl.co_local_atend
                        INNER JOIN rl_cds_atend_individual_ciap rlai ON ate.ate_codigo = rlai.ate_codigo
                        INNER JOIN rl_cds_atend_individual_condut rlaic ON ate.ate_codigo = rlaic.ate_codigo
                        WHERE ate.ate_codigo = ate_codigo_recebido LOOP
                        IF (linha.usu_sexo='M') THEN 
                            eai_sexo = '0 L';
                        ELSE
                            eai_sexo = '1 L';
                        END IF;
                        INSERT INTO esus_atendimento_individual(
                                ate_codigo,
                                co_local_atend,
                                eai_profissional_cns,
                                eai_cbo_codigo_2002,
                                eai_cnes,
                                eai_dtatendimento,
                                eai_codigo_ibge_mun,
                                eai_dtnascimento,
                                eai_num_cartao_sus,
                                eai_numprontuario,
                                eai_tipo_atendimento,
                                eai_sexo,
                                eai_tipo_dado_serializado
                        ) VALUES (
                                linha.ate_codigo,
                                linha.co_local_atend,
                                linha.eai_profissional_cns,
                                linha.eai_cbo_codigo_2002,
                                linha.eai_cnes,
                                linha.eai_dtatendimento,
                                linha.eai_codigo_ibge_mun,
                                linha.eai_dtnascimento,
                                linha.eai_num_cartao_sus,
                                linha.eai_numprontuario,
                                linha.eai_tipo_atendimento,
                                eai_sexo,
                                '4 L'
                        );
                    END LOOP;
                END;
                $" . $body . "$
                LANGUAGE plpgsql VOLATILE
                COST 100;";
    $result = pg_query($funcao);

    if (!$result) {
        die("Falha em criar a Função!");
    }
}

function alterarStatusTrigger() {
    $trigger = "ALTER TABLE rl_cds_atend_individual_condut ENABLE TRIGGER atualiza_esus_atendimento_individual";
    pg_query($trigger);
}

function dropFunction() {
    $function = "DROP FUNCTION IF EXISTS atualizaAtendimentoIndividualEsus(bigint)";
    pg_query($function);

    die("Script de Atualização de Atendimento Invidicual Esus Executado com Sucesso!");
}
