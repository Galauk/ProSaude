<link rel="stylesheet" href="h2/progresso.css">
<div class="barra-area">
    <div class="barra">
        <div id='div_progress' class='progresso'>
        </div>
    </div>
</div>
<?php
set_time_limit(100000000000);
ini_set("display_errors",1);
session_start();

include "esus/atividadecoletiva/Types.php";
include "Types.php";
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

use esus\cds\transport\generated\thrift\atividadecoletiva\ParticipanteRowItemThrift as Participante;
use esus\cds\transport\generated\thrift\atividadecoletiva\ProfissionalCboRowItemThrift as Profissionais;
use esus\cds\transport\generated\thrift\atividadecoletiva\FichaAtividadeColetivaThrift as FichaAtividade;
use esus\cds\transport\generated\thrift\atividadecoletiva\CDSTransportThrift as Transportcds;

include "Cidadao.php";

//public static $endereco;

class ThriftAtividadeColetiva {

    public function executeMain() {
        //unlink($_SESSION[root] . $_SESSION[modulo] . "e-sus/arqs/cidadao.zip");
        $ficha = array();
                

        $ficha = $this->createAtividadeColetiva();
        
        
        $zip = new ZipArchive();
        $filename = "./arqs/cidadao.zip";
         if ($zip->open($filename, ZIPARCHIVE::CREATE) !== TRUE) {
            exit("cannot open <$filename>\n");
        }
        $uuid = $this->getGUID();
        $arquivo_zipado = $uuid."esus";
        $serialize = $this->serialize_thrift($ficha);
        //$unserialize = $this->unserialize_thrift($ficha);
        rename("ficha_ativdade.esus", $arquivo_zipado);
        
        $zip->addFile($arquivo_zipado);
     
      
        
    }

    private function createAtividadeColetiva() {

        $participantes_array = array("cns" => "204327468080006",
                                    "dataNascimento" => "23/03/2015",
                                    "avaliacaoAlterada" => "1",
                                    "peso" => "50.0000",
                                    "altura" => "190.0000",
                                    "cessouHabitoFumar" => "null",
                                    "abandonouGrupo" => "null");
        $participantes = $this->getParticipantes($participantes_array);
        //echo "<pre>" . print_r($participantes, 1);
        //die();
        
        $profissionaiscbo_array = array("cns"=>"207288355890009",
                                        "codigoCbo2002"=>'223565');
        $profissionais = $this->getProfissionais($profissionaiscbo_array);
        
        
        $fichaAtividadeColetiva = array("uuidFicha"=>$this->getGUID(),
                                        "dtAtividadeColetiva"=>"30/03/2015",
                                        "numParticipantesProgramados"=>1,
                                        "localAtividade"=>'TESTE',
                                        "horaInicio"=>'08:00',
                                        "horaFim"=>'12:00',
                                        "inep"=>"NULL",
                                        "responsavelCns"=>'207288355890009',
                                        "responsavelCnesUnidade"=>"2754258",
                                        "responsavelNumIne"=>"NULL",
                                        "numParticipantes"=>1,
                                        "numAvaliacoesAlteradas"=>1,
                                        "profissionais"=>array($profissionais),
                                        "atividadeTipo"=>1,
                                        "temasParaReuniao"=>array(1),
                                        "publicoAlvo"=>array(1),
                                        "praticasTemasParaSaude"=>array(1),
                                        "participantes"=>array($participantes),
                                        "tbCdsOrigem"=>1,
                                        "codigoIbgeMunicipio"=>"4118006");
        $ficha = $this->getFicha($fichaAtividadeColetiva);
        $array_fichas = array("atividadeColetivaTransport"=>$ficha);
        $transport = $this->transportCds($array_fichas);
        return $transport;
    }
    
    public function transportCds($vals){
        $transportcds = new Transportcds($vals);
        return $transportcds;
    }

    public function getParticipantes($vals) {
       $participantes = new Participante($vals);

        return $participantes;
    }

    public function getProfissionais($vals) {
       $profissionais = new Profissionais($vals);

        return $profissionais;
    }
    
    public function getFicha($vals) {
       $ficha = new FichaAtividade($vals);

        return $ficha;
    }
    
    public function serialize_thrift($atividade_coletiva = FALSE) {
        
        $tStream = new Thrift\Transport\TPhpStream(2);
        if ($tStream->isOpen() != 1) {
            $tStream->open();
        }
        $tProtocol = new Thrift\Protocol\TBinaryProtocol($tStream, FALSE, TRUE);
        $atividade_coletiva->write($tProtocol);
       
        return $tStream;
        //$tStream->close();
    }

    public function unserialize_thrift($atividade_coletiva) {
        $tStream = new Thrift\Transport\TPhpStream(1);
        if ($tStream->isOpen() != 1) {
            $tStream->open();
           
        }
        $tProtocol = new Thrift\Protocol\TBinaryProtocol($tStream, TRUE, FALSE);
        $teste = $atividade_coletiva->read($tProtocol);
    }

    public function validacoesEsus($cidadao) {
      
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
    
    private function getGUID(){
    if (function_exists('com_create_guid')){
        return com_create_guid();
    }else{
        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        $charid = md5(uniqid(rand(), true));
        $hyphen = chr(45);// "-"
        $uuid = substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12);
        return $uuid;
    }
}

}

$tAtividadeColetiva = new ThriftAtividadeColetiva();
$tAtividadeColetiva->executeMain();
?>