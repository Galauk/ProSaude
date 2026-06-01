<link rel="stylesheet" href="h2/progresso.css">
<div class="barra-area">
    <div class="barra">
        <div id='div_progress' class='progresso'>
        </div>
    </div>
</div>
<?php
set_time_limit(100000000000);
//ini_set("display_errors",1);
//error_reporting(E_ALL);
//namespace esus\ThriftCidadao;
session_start();

include "esus/cidadao/Types.php";
include $_SESSION[root] . $_SESSION[modulo] . "e-sus/Thrift/lib/php/lib/Thrift/Base/TBase.php";
include $_SESSION[root] . $_SESSION[modulo] . "e-sus/Thrift/lib/php/lib/Thrift/Transport/TTransport.php";
include $_SESSION[root] . $_SESSION[modulo] . "e-sus/Thrift/lib/php/lib/Thrift/Factory/TStringFuncFactory.php";
include $_SESSION[root] . $_SESSION[modulo] . "e-sus/Thrift/lib/php/lib/Thrift/StringFunc/Core.php";
include_once $_SESSION[root] . $_SESSION[modulo] . "e-sus/Thrift/lib/php/lib/Thrift/Type/TType.php";

use Thrift\Base\TBase;
//include $_SESSION[root].$_SESSION[modulo]."e-sus/Thrift/lib/php/lib/Thrift/Exception/TException.php";
use Thrift\Exception\TException;

include $_SESSION[root] . $_SESSION[modulo] . "e-sus/Thrift/lib/php/lib/Thrift/Protocol/TProtocol.php";

use Thrift\Protocol\TProtocol;

include $_SESSION[root] . $_SESSION[modulo] . "e-sus/Thrift/lib/php/lib/Thrift/Protocol/TBinaryProtocol.php";

use Thrift\Protocol\TBinaryProtocol;

include $_SESSION[root] . $_SESSION[modulo] . "e-sus/Thrift/lib/php/lib/Thrift/Transport/TPhpStream.php";

use Thrift\Transport\TPhpStream;
use esus\cidadao\CidadaoTransportThrift as Cidadao;
use esus\cidadao\EnderecoTransportThrift as Endereco;
use esus\cidadao\SexoThrift;
use esus\cidadao\TipoSanguineoThrift;

include "Cidadao.php";

//public static $endereco;

class ThriftCidadao {

    public function executeMain() {
        unlink($_SESSION[root] . $_SESSION[modulo] . "e-sus/arqs/cidadao.zip");
        $array_cidadao = array();
        $array_cidadao = $this->createCidadao();
        $zip = new ZipArchive();
        $filename = "./arqs/cidadao.zip";

        //echo "<pre>".print_r($array_cidadao,1);die();
        if ($zip->open($filename, ZIPARCHIVE::CREATE) !== TRUE) {
            exit("cannot open <$filename>\n");
        }

        $qtdRegistro = count($array_cidadao);
        $porcentagem = 356 / $qtdRegistro;
        $progresso = 0;
        $i = 0;
        echo "<div class='warning'>GERANDO ARQUIVOS ...</div>";
        header('Content-type: text/html; charset=utf-8');
        
        foreach ($array_cidadao as $cidadao) {
            // Barra de Progresso
            $progresso = $progresso + $porcentagem;
            //sleep(1);
            ?>
            <script language='javascript'>
                document.getElementById('div_progress').style.width = '<?php echo $progresso; ?>px';
            </script>   
            <?php
            echo "<span style='display:none'>" . str_repeat('x', $progresso) . "</span>";
            flush();
            // Fim Barra de Progresso
            // Validação dos registros antes de ser gerados
            if ($this->validacoesEsus($cidadao) == "true") {
                $arquivo_zipado = $i . "-" . ($cidadao->cns == "" ? "null" : "$cidadao->cns") . ($cidadao->numeroProntuario == "" ? "null" : "$cidadao->numeroProntuario") . ".cidadao";
                $this->serialize_thrift($cidadao);
                rename("output.cidadao", $arquivo_zipado);
                $zip->addFile($arquivo_zipado);
                $i++;
            }
        }
        if($i > 0){
            echo "<div class='success'>ARQUIVOS GERADO COM SUCESSO!</div>";
            echo "<a href='download.php?arquivo=" . $_SESSION[root] . $_SESSION[modulo] . "e-sus/arqs/cidadao.zip'><img src=\"../imgs/download.png\" title=\"Baixar arquivo\"></a>";
        }else{
            echo "<div class='error'>N&Atilde;O FOI POSS&Iacute;VEL GERAR OS ARQUIVOS.
                <br/>VERIFIQUE AS INCONSIST&Ecirc;NCIAS<br/>
                Acesse:<a href='".$_SESSION[linkroot] . $_SESSION[modulo] . "zf/programasfederais/esus/importacao-resultado'>Inconsist&ecirc;ncias</a></div>";
        }
        
        $zip->close();
        // Removendo arquivos .cidadao da raiz do projeto
        array_map('unlink', glob($_SESSION[root].$_SESSION[modulo]."e-sus/*.cidadao"));
        //$cidadao_dados->getDadosCidadao();
    }

    private function createCidadao() {
        echo "<div class='warning'>CARREGANDO DADOS ...</div>";
        $dados_cidadao = new esus\banco_cidadao\BancoCidadao();
        $cidadoes = $dados_cidadao->getDadosCidadao();
        $cidadoes_validados = array();
        $endereco_array = array();

        // Progresso
        $qtdRegistro = count($cidadoes);
        $porcentagem = 356 / $qtdRegistro;
        $progresso = 0;

        $i = 0;
        header('Content-type: text/html; charset=utf-8');
        //echo "<pre>" . print_r($cidadoes , 1);
        //die();
        foreach ($cidadoes as $cidadao) {
            // Barra de Progresso
            $progresso = $progresso + $porcentagem;
            //sleep(1);
            ?>
            <script language='javascript'>
                document.getElementById('div_progress').style.width = '<?php echo $progresso; ?>px';
            </script>   
            <?php
            echo "<span style='display:none'>" . str_repeat('x', $progresso) . "</span>";
            flush();
            // Fim Barra de Progresso

            $endereco_array = array("bairroNome" => $cidadao[rua_bairro],
                "bairroDne" => $cidadao[usu_bairro_dne],
                "localidadeCep" => $cidadao[rua_cep],
                "localidadeDne" => $cidadao[usu_localidade_dne],
                "localidadeIbge" => $cidadao[cid_codigo_ibge],
                "complemento" => $cidadao[dom_complemento],
                "logradouro" => $cidadao[rua_nome],
                "logradouroDne" => $cidadao[usu_logradouro_dne],
                "numero" => $cidadao[dom_numero],
                "pontoReferencia" => NULL,
                "semNumero" => ($cidadao[dom_numero] == "" ? "true" : "false"),
                "ufSigla" => $cidadao[uf_sigla],
                "area" => $cidadao[psf_area],
                "microArea" => $cidadao[psf_micro_area]);

            $endereco = $this->getEnderecos($endereco_array);

            $cidadoes_validados[$i] = array("naoPossuiCns" => ($cidadao[usu_cartao_sus] == "" ? "true" : "false"),
                "cns" => $cidadao[usu_cartao_sus],
                "cpf" => $cidadao[usu_cpf],
                "dataNascimento" => $cidadao[usu_datanasc],
                "desconheceNomeMae" => ($cidadao[usu_mae] == "" ? "true" : "false"),
                "nomeMae" => $cidadao[usu_mae],
                "email" => $cidadao[usu_email],
                "endereco" => $endereco,
                "escolaridadeId" => $cidadao[usu_escolaridade],
                "estadoCivilId" => $cidadao[usu_estado_civil],
                "estrangeiro" => $cidadao[estrangeiro],
                "etniaId" => $cidadao[etn_codigo],
                "faleceu" => $cidadao[usu_obito],
                "municipioNascimentoCep" => $cidadao[rua_cep],
                "municipioNascimentoDne" => $cidadao[usu_localidade_dne],
                "municipioNascimentoIbge" => $cidadao[muni_cd_cod_ibge_nasc],
                "nisPisPasep" => $cidadao[usu_pis_pasep],
                "nomeCompleto" => utf8_decode($cidadao[usu_nome]),
                "nomeSocial" => utf8_decode($cidadao[usu_nome]),
                "numeroProntuario" => $cidadao[usu_prontuario],
                "racaCorId" => $cidadao[rac_codigo],
                "sexo" => $cidadao[usu_sexo],
                "telefoneCelular" => $cidadao[usu_celular],
                "telefoneContato" => $cidadao[usu_fone_recado],
                "telefoneResidencial" => $cidadao[usu_fone],
                "tipoSanguineo" => $cidadao[usu_tipo_sanguineo],
                "cboNumero" => $cidadao[usu_cbo_r]);
            $cidadao_transport[$i] = $this->getCidadoes($cidadoes_validados[$i]);
            $cidadao_transport[$i]->usu_codigo = $cidadao[usu_codigo];
            $i++;
        }
        
        return $cidadao_transport;
    }

    public function getEnderecos($vals) {
        $endereco = new Endereco($vals);
        return $endereco;
    }

    public function getCidadoes($vals) {
        $cidadoes = new Cidadao($vals);
        return $cidadoes;
    }

    public function serialize_thrift($cidadao = FALSE) {
        $tStream = new Thrift\Transport\TPhpStream(2);
        if ($tStream->isOpen() != 1) {
            $tStream->open();
        }
        $tProtocol = new Thrift\Protocol\TBinaryProtocol($tStream, FALSE, TRUE);
        $cidadao->write($tProtocol);
        $tStream->close();
    }

    public function unserialize_thrift($cidadao) {
        $tStream = new Thrift\Transport\TPhpStream(1);
        if ($tStream->isOpen() != 1) {
            $tStream->open();
        }
        $tProtocol = new Thrift\Protocol\TBinaryProtocol($tStream, TRUE, FALSE);
        $teste = $cidadao->read($tProtocol);
    }

    public function validacoesEsus($cidadao) {
        $retorno = "";
        $array_inconsistencia = array();
        $mensagem = "";
        // Se cpf estiver errado, erro	
        if ($this->validaCPF($cidadao->cpf) == false) {
            //$array_inconsistencia = array("eir_cpf" => $cidadao[cpf]);
            $mensagem .= '<span style="margin-left: 4px;">Cpf: Inválido </span><br />';
            $retorno = "false";
            // Se data de nascimento vier vazia, erro	
        }
        if (empty($cidadao->dataNascimento)) {
            //$array_inconsistencia = array("eir_data_nascimento" => $cidadao[dataNascimento]);
            $mensagem .= '<span style="margin-left: 4px;">Informe uma data de nascimento </span><br />';
            $retorno = "false";
            // Se tiver ponto ou espaço no fim da palavra, gera um erro	
        }
        if (strripos($cidadao->nomeCompleto, ".") === TRUE || strripos($cidadao->nomeCompleto, "  ") === TRUE) {
            //$array_inconsistencia = array("eir_nome" => $cidadao[nomeCompleto]);
            $mensagem .= '<span style="margin-left: 4px;">Nome inválido </span><br />';
            $retorno = "false";
            // O nome deve conter entre 5 e 500 caracteres, gera um erro	
        }
        if (strlen($cidadao->nomeCompleto) < 5 || strlen($cidadao->nomeCompleto) > 500) {
            $mensagem .= '<span style="margin-left: 4px;">O nome deve conter entre 5 e 500 caracteres! </span><br />';
            $retorno = "false";
            // Se tiver ponto ou espaço no fim da palavra, gera um erro	
        }
        if (strripos($cidadao->nomeMae, ".") === TRUE || strripos($cidadao->nomeMae, "  ") === TRUE) {
            $array_inconsistencia = array("eir_nome_mae" => $cidadao[nomeMae]);
            $mensagem .= '<span style="margin-left: 4px;">Nome da mãe inválido </span><br />';
            $retorno = "false";
            // O nome deve conter entre 5 e 500 caracteres, gera um erro	
        }
        if (strlen($cidadao->nomeMae) < 5 || strlen($cidadao->nomeMae) > 500) {
            $mensagem .= '<span style="margin-left: 4px;">O nome da mãe deve conter entre 5 e 500 caracteres! </span><br />';
            $retorno = "false";
            // Se tiver ponto ou espaço no fim da palavra, gera um erro	
        }
        if (strripos($cidadao->nomeSocial, ".") === TRUE || strripos($cidadao->nomeSocial, "  ") === TRUE) {
            $mensagem .= '<span style="margin-left: 4px;">Nome da Social inválido </span><br />';
            $retorno = "false";
            // O nome deve conter entre 5 e 500 caracteres, gera um erro	
        }
        if (strlen($cidadao->nomeSocial) < 5 || strlen($cidadao->nomeSocial) > 500) {
            $mensagem .= '<span style="margin-left: 4px;">O nome da social deve conter entre 5 e 500 caracteres! </span><br />';
            $retorno = "false";
        }
        if ($cidadao->telefoneResidencial != "" && (strlen($cidadao->telefoneResidencial) < 10 || strlen($cidadao->telefoneResidencial) > 11)) {
            $mensagem .= '<span style="margin-left: 4px;">Telefone residencial deve conter entre 10 e 11 caracteres ou ficar em branco </span><br />';
            $retorno = "false";
        }
        if ($cidadao->telefoneContato != "" && (strlen($cidadao->telefoneContato) < 10 || strlen($cidadao->telefoneContato) > 11)) {
            $mensagem .= '<span style="margin-left: 4px;">Telefone de contato deve conter entre 10 e 11 caracteres ou ficar em branco </span><br />';
            $retorno = "false";
        }
        if ($cidadao->telefoneCelular != "" && (strlen($cidadao->telefoneCelular) < 10 || strlen($cidadao->telefoneCelular) > 11)) {
            $mensagem .= '<span style="margin-left: 4px;">Telefone celular deve conter entre 10 e 11 caracteres ou ficar em branco </span><br />';
            $retorno = "false";
        }
        if ($cidadao->nisPisPasep != "" && (strlen($cidadao->nisPisPasep) < 10 || strlen($cidadao->nisPisPasep) > 11)) {
            $mensagem .= '<span style="margin-left: 4px;">O campo Pis deve conter entre 10 e 11 caracteres </span><br />';
            $retorno = "false";
        }
        if ($cidadao->racaCorId != "" && empty($cidadao->racaCorId)) {
            $mensagem .= '<span style="margin-left: 4px;">Selecione uma raça </span><br />';
            $retorno = "false";
        }

        $data_nasc = ($cidadao->dataNascimento == "" ? "null" : '"' . $cidadao->dataNascimento . '"');
        $data_nascl = str_replace('"', "'", $data_nasc);

        if ($retorno == "false") {
            // Verifica se a inconsistência já não foi gerada
            $sqlConfEir = "SELECT * FROM esus_importacao_resultado WHERE eir_nome = '" . $cidadao->nomeCompleto . "' AND eir_mensagem = '" . $mensagem . "'";
            $queryConfEir = pg_query($sqlConfEir);
            $numConfEir = pg_num_rows($queryConfEir);

            if ($numConfEir == 0) {
                $sqlInsEir = "INSERT INTO esus_importacao_resultado 
											(eir_status,eir_cns,eir_cpf,eir_nome,eir_data_nascimento,eir_nome_mae,eir_mensagem,usu_codigo)
										 VALUES
											('f','" . $cidadao->cns . "','" . $cidadao->cpf . "','" . $cidadao->nomeCompleto . "',$data_nascl,'" . $cidadao->nomeMae . "','" . $mensagem . "','" . $cidadao->usu_codigo . "')";
                $queryInsEir = pg_query($sqlInsEir) or die($sqlInsEir);
            }
        } else {
            $retorno = "true";
        }

        return $retorno;
    }

    public function validaCPF($cpf = null) {
        // Verifica se um número foi informado
        if (empty($cpf)) {
            return false;
        }
        // Elimina possivel mascara
        $cpf = ereg_replace('[^0-9]', '', $cpf);
        $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);

        // Verifica se o numero de digitos informados é igual a 11 
        if (strlen($cpf) != 11) {
            return false;
        }
        // Verifica se nenhuma das sequências invalidas abaixo 
        // foi digitada. Caso afirmativo, retorna falso
        else if ($cpf == '00000000000' ||
                $cpf == '11111111111' ||
                $cpf == '22222222222' ||
                $cpf == '33333333333' ||
                $cpf == '44444444444' ||
                $cpf == '55555555555' ||
                $cpf == '66666666666' ||
                $cpf == '77777777777' ||
                $cpf == '88888888888' ||
                $cpf == 'null' ||
                $cpf == '99999999999') {
            return false;
            // Calcula os digitos verificadores para verificar se o
            // CPF é válido
        } else {

            for ($t = 9; $t < 11; $t++) {

                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf{$c} * (($t + 1) - $c);
                }
                $d = ((10 * $d) % 11) % 10;
                if ($cpf{$c} != $d) {
                    return false;
                }
            }

            return true;
        }
    }

    private function forcaDownload($arquivo) {
        if (isset($arquivo) && file_exists($arquivo)) { // faz o teste se a variavel não esta vazia e se o arquivo realmente existe
            switch (strtolower(substr(strrchr(basename($arquivo), "."), 1))) { // verifica a extensão do arquivo para pegar o tipo
                case "pdf": $tipo = "application/pdf";
                    break;
                case "exe": $tipo = "application/octet-stream";
                    break;
                case "zip": $tipo = "application/zip";
                    break;
                case "doc": $tipo = "application/msword";
                    break;
                case "xls": $tipo = "application/vnd.ms-excel";
                    break;
                case "ppt": $tipo = "application/vnd.ms-powerpoint";
                    break;
                case "gif": $tipo = "image/gif";
                    break;
                case "png": $tipo = "image/png";
                    break;
                case "jpg": $tipo = "image/jpg";
                    break;
                case "mp3": $tipo = "audio/mpeg";
                    break;
                case "php": // deixar vazio por seurança
                case "htm": // deixar vazio por seurança
                case "html": // deixar vazio por seurança
            }
            header("Content-Type: " . $tipo); // informa o tipo do arquivo ao navegador
            header("Content-Length: " . filesize($arquivo)); // informa o tamanho do arquivo ao navegador
            header("Content-Disposition: attachment; filename=" . basename($arquivo)); // informa ao navegador que é tipo anexo e faz abrir a janela de download, tambem informa o nome do arquivo
            readfile($arquivo); // lê o arquivo
            //exit; // aborta pós-ações
        }
    }

}

$tCidadao = new ThriftCidadao();
$tCidadao->executeMain();
?>