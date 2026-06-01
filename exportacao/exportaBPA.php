<?php

require_once '../global.php';
require_once COMUM . "/library/php/funcoes.inc.php";
include_once SOCIAL . "/exportacao/funcoesBPA.inc.php";

$data_inicial = $_GET['data_inicial'];
$data_final = $_GET['data_final'];
list($mes, $ano) = explode("/", $_GET['mes_ref']);
$uni_codigo = $_GET['uni_codigo'];
$uni = explode("|", $uni_codigo);

if (isset($uni[0])) {
    if ($uni[1] == 1) {
        $sql = "SELECT uni_cnes FROM unidade WHERE uni_codigo=$uni[0];";
        $query = pg_query($sql);
        $row = pg_fetch_array($query);
        $cnes = $row['uni_cnes'];
        $and = " AND uni.uni_cnes='$cnes'";
        if (!isset($cnes)) {
            die("CNES da Unidade n&atilde;o encontrado. Verifique o cadastro da unidade!.");
        }
    }else if ($uni[1] == 0) {
        $sql = "SELECT med_cnes FROM medico WHERE med_codigo=$uni[0];";
        $query = pg_query($sql);
        $row = pg_fetch_array($query);
        $cnes = $row['med_cnes'];
        $and = "AND m.med_cnes='$cnes'";
        if (!isset($cnes)) {
            die("CNES do Prestador n&atilde;o encontrado. Verifique o cadastro da unidade!.");
        }
    }
}

if (!isset($ano) || !isset($mes) || !isset($cnes)) {
    die("Informe o m&ecirc;s, ano e o CNES da unidade para gerar o BPA!");
}

$data1 = $data_inicial;
$data2 = $data_final;
$meses = array(NULL, "JAN", "FEV", "MAR", "ABR", "MAI", "JUN", "JUL", "AGO", "SET", "OUT", "NOV", "DEZ");
$nomeMes = $meses[(int) $mes];

$nome = "PA" . $cnes;
$path = "arquivos/";
$quebra = chr(13) . chr(10);

$folha = 1;
$linhaBPA = 0;
$somaControle = 0;

$registros = 0;

for ($x = 0; $x < 2; $x++) {
    $tipoBPA = ($x ? "I" : "C");

    if ($tipoBPA == 'C') {
        //SELECT DO BPA CONSOLIDADO
        $query = "SELECT COUNT(DISTINCT(bpa_codigo)) AS total, 
                        MIN(esp.cod_cbo) AS m_cbo, 
                        p.proc_codigo_sus ,
                        p.proc_codigo
                  FROM bpa 
                  INNER JOIN usuario AS usu ON usu.usu_codigo=bpa.usu_codigo 
                  LEFT JOIN unidade AS uni ON uni.uni_codigo=bpa.uni_codigo 
                  LEFT JOIN medico AS m ON m.med_codigo = bpa.med_codigo 
                  INNER JOIN procedimento AS p ON p.proc_codigo=bpa.proc_codigo 
                  INNER JOIN medico_especialidade AS me ON me.med_codigo=bpa.usr_codigo 
                  INNER JOIN especialidade AS esp ON esp.esp_codigo=me.esp_codigo 
                  INNER JOIN rl_procedimento_registro AS rlr ON rlr.co_procedimento=p.proc_codigo_sus AND rlr.dt_competencia='{$ano}{$mes}' AND rlr.co_registro = 1  
                  WHERE bpa_data BETWEEN '$data_inicial' AND '$data_final' $and AND bpa.bpa_ativo='t' 
                  GROUP BY p.proc_codigo_sus,p.proc_codigo;";
    } else {
        //SELECT DO BPA INDIVIDUALIZADO
		die("cai aqui?");
        $query = "SELECT COUNT(DISTINCT(bpa.bpa_codigo)) as total,
                        proc.proc_codigo_sus, 
                        to_char(bpa.bpa_data,'YYYYMMDD') as bpa_data,
                        usr.usr_medico_cnes as m_cns,
                        esp.cod_cbo as m_cbo,
                        TRANSLATE(usu.usu_cartao_sus, '.', '') as p_cns,
                        usu.usu_sexo as p_sexo,
                        usu.usu_nome,
                        usu.usu_datanasc,
                        usu.rac_codigo as raca,
                        cid.cid_codigo_ibge as ibge,
                        cd10.cd10_codigo_cid as cid,
                        uf.pais_codigo as pais,
                        usu.etn_codigo as etnia,
                        ci.ci_cod,
                        bpa.bpa_autorizacao,
                        DATE_PART('YEAR', AGE(bpa.bpa_data, usu.usu_datanasc)) as idade
                  FROM bpa							     
                  INNER JOIN procedimento proc ON proc.proc_codigo = bpa.proc_codigo
                  INNER JOIN rl_procedimento_ocupacao rlpo ON rlpo.co_procedimento = proc.proc_codigo_sus
                  INNER JOIN especialidade esp ON esp.cod_cbo = rlpo.co_ocupacao
                  INNER JOIN medico_especialidade mesp ON mesp.esp_codigo = esp.esp_codigo
                  INNER JOIN usuarios usr ON mesp.med_codigo = usr.usr_codigo
                  INNER JOIN usuario usu ON bpa.usu_codigo = usu.usu_codigo
                  LEFT JOIN domicilio dom ON usu.dom_codigo = dom.dom_codigo 
                  LEFT JOIN rua ON dom.rua_codigo = rua.rua_codigo 
                  LEFT JOIN cidade cid ON rua.cid_codigo = cid.cid_codigo 
                  LEFT JOIN estado uf ON cid.uf_codigo = uf.uf_codigo 
                  LEFT JOIN cid10 cd10 ON bpa.bpa_cd10_codigo = cd10.cd10_codigo  
                  INNER JOIN ci ON ci.ci_codigo = bpa.ci_codigo
                  INNER JOIN unidade uni ON uni.uni_codigo = bpa.uni_codigo
                  INNER JOIN rl_procedimento_registro rlr ON rlr.co_procedimento = proc.proc_codigo_sus AND rlr.dt_competencia='{$ano}{$mes}' AND rlr.co_registro = 2 
                  WHERE bpa_data BETWEEN '$data_inicial' 
                          AND '$data_final' 
                          AND uni.uni_cnes='$cnes' 
                          AND bpa.bpa_ativo='t'
                  GROUP BY  proc.proc_codigo_sus,
                            bpa.usu_codigo,
                            usr.usr_medico_cnes,
                            esp.cod_cbo,
                            usu.usu_codigo_sus,
                            usu.usu_sexo,
                            usu.usu_nome,
                            usu.usu_datanasc,
                            usu.rac_codigo,
                            cid.cid_codigo_ibge,
                            cd10.cd10_codigo_cid,
                            uf.pais_codigo,
                            usu.etn_codigo,
                            bpa.bpa_data,
                            ci.ci_cod,
                            bpa.bpa_autorizacao,
                            usu.usu_cartao_sus";
        fdebug($query);
    }

    $result = pg_query($query) or die("Erro na query principal: <br />" . pg_last_error());

    while ($linha = pg_fetch_array($result)) {
        $registros++;
        /*
         * Em um arquivo de BPA, as linhas sao numeradas de 01 a 20, ao atingir 20, incrementa-se o numero da folha
         */
        $linhaBPA++;
        if ($linhaBPA > 20) {
            $linhaBPA = 1;
            $folha++;
        }

        $codCnes = corta($cnes, 7, " "); // tamanho 7
        $competencia = $ano . $mes; // tamanho 6
        $cboMedico = corta($linha['m_cbo'], 6, " "); // tamanho 6
        $numFolhaBpa = corta($folha, 3, "0"); // tamanho 3
        $numLinhaBpa = corta($linhaBPA, 2, "0"); // tamanho 2
        $proc_codigo_sus = str_replace(array('.', '-'), array('', ''), $linha['proc_codigo_sus']);
        $codProcAmbulatorial = corta($proc_codigo_sus, 10, "0"); // tamanho 10 (ultimo caractere � o d�gito verificador)
        $qtdeProc = corta($linha['total'], 6, "0");
        $origem = "EXT"; // tamanho 3 - Origem das informa��es (BPA - SIA/SUS; PNI -PROG. NAC. DE IMUNIZA��ES; SIE �SIGAE; SIB �SIGAB; MIN - MATERNO INFANTIL; PAC-PROGRAMA A��O COMUNIT�RIA; SCL-SISCOLO; EXT-OUTROS SISTEMAS)
        $tipoFormulario = $tipoBPA; // tamanho 1 - (I)ndividualizado ou (C)onsolidado

        if ($tipoBPA == "C") {
            $cnsMedico = corta("", 15, " ");
            $dataAtendimento = corta("", 8, " ");
            $cnsPaciente = corta("", 15, " ");
            $pacSexo = corta("", 1, " ");
            $codIbge = corta("", 6, " ");
            $cid = corta("", 4, " ");
            $idade = corta($linha['idade'], 3, "0");
            $caracterAtendimento = corta("", 2, " ");
            $numAutorizacaoEstabelecimento = corta("", 13, " ");
            $nomeUsuario = corta("", 30, " ");
            $dataNascUsuario = corta("", 8, " ");
            $racaUsuario = corta("", 2, " ");
            $etniaUsuario = corta("", 4, " ");
            $nacionalidade = corta("", 3, " ");
        } else {
            $cnsMedico = corta($linha['m_cns'], 15, " "); // tamanho 15
            $dataAtendimento = $linha['bpa_data'];   // tamanho 8 AAAAMMDD
            $cnsPaciente = corta($linha['p_cns'], 15, " "); // tamanho 15
            $pacSexo = corta($linha['p_sexo'], 1, " ");
            $codIbge = corta($linha['ibge'], 6, " ");
            $cid = corta($linha['cid'], 4, " ");
            $idade = corta($linha['idade'], 3, "0"); // tamanho 3
            $caracterAtendimento = corta($linha['ci_cod'], 2, " ");
            $numAutorizacaoEstabelecimento = corta($linha['bpa_autorizacao'], 13, " ");
            $nomeUsuario = corta($linha['usu_nome'], 30, " "); // se menor que 30, preenche com :space:
            $dataNascUsuario = str_replace('-', '', $linha['usu_datanasc']);
            $racaUsuario = ($linha['raca'] < 9 ? "0" . $linha['raca'] : "99");

            if ($racaUsuario == "05") {
                $etniaUsuario = corta($linha['etnia'], 4, "0");
            } else {
                $etniaUsuario = corta("", 4, " ");
            }
            $nacionalidade = corta($linha['pais'], 3, " ");
        }
        /*
         * A variavel $ultimaLinha eh necessaria para passar como parametro para as funcoes contaFolhas() e contaLinhas()
         * em cada passo do loop ela eh sobrescrita, dessa forma, quando terminar o loop seu conteudo sera realmente a ultima linha.
         * A variavel $todasAsLinhas, como o proprio nome diz, eh a concatenacao de todas as linhas.
         */
        $todasAsLinhas .= $codCnes . $competencia . $cnsMedico . $cboMedico . $dataAtendimento . $numFolhaBpa .
                $numLinhaBpa . $codProcAmbulatorial . $cnsPaciente . $pacSexo . $codIbge . $cid . $idade .
                $qtdeProc . $caracterAtendimento . $numAutorizacaoEstabelecimento . $origem . $nomeUsuario .
                $dataNascUsuario . $tipoFormulario . $racaUsuario . $etniaUsuario . $nacionalidade . $quebra;


        $somaControle += ($codProcAmbulatorial + $qtdeProc);
        if ($somaControle > 1111) {
            $somaControle -= 1111;
        }
    }
}

/*
 * MONTANDO O CABECALHO - O cabecalho deve ser montado por �ltimo por causa da quantidade de linhas, de folhas e do n�mero de controle.
 */
$sqlHead = "SELECT u.uni_desc
                FROM unidade u
               WHERE u.uni_cnes = '$cnes'";
$queryHead = pg_query($sqlHead);
$linhaHead = pg_fetch_array($queryHead);

$querySec = pg_query("SELECT * FROM secretaria WHERE tipo_secretaria = 'SAU' LIMIT 1;");
$linhaSec = pg_fetch_array($querySec);


$linhas = (($folha - 1) * 20) + $linhaBPA;
$numlinhas = corta($linhas, 6, "0");  // retorno "000053" // tamanho 6
$numfolhas = corta($folha, 6, "0");  // retorno "000003" // tamanho 6
$controle = ($somaControle % 1111) + 1111;
$controle = corta($controle, 4, "0");
$orgaoDestino = corta($linhaSec['nome_secretaria'], 40, " "); // tamanho 30
$siglaOrgaoOrigem = corta($linhaHead['uni_desc'], 6, " "); // tamanho 6
$cgc = preg_replace("/[^0-9]+/", "", $linhaSec['cnpj_secretaria']);
$cgc = corta($cgc, 14, "0"); // tamanho 14
$orgaoOrigem = corta($linhaHead['uni_desc'], 30, " "); // tamanho 40
$municipalEstadual = "M"; //tamanho 1
$versaoSistema = corta($_SESSION['versao'], 10, " "); // tamanho 10

$escrever = "#BPA#" . $ano . $mes . $numlinhas . $numfolhas . $controle . $orgaoOrigem . $siglaOrgaoOrigem . $cgc . $orgaoDestino . $municipalEstadual . $versaoSistema . $quebra;
/*
 * CABECALHO MONTADO
 */
$msg = $escrever . $todasAsLinhas;

criaArquivo($nome, $msg, $path, "." . $nomeMes);
$link = $path . $nome . "." . $nomeMes;
header("Content-Disposition: attachment; filename=" . $nome . "." . $nomeMes . "");
header("Content-Type: application/plain");

readfile($link);
header("location: relatorio.php?file=$path/$nome.$nomeMes&registros=$registros&controle=$controle&anoRef=$ano");

