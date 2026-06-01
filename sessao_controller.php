<?php

/**
 * Class limiteTempoSessao classe responsavel por verificar o tempo de sessao entre as paginas
 */
class TempoSessao
{

    private $anexo = FALSE;

    /**
     * Abre a conexao com o banco de dados e faz select pela chave obtendo o tempo de sessao,
     * para que funcione deve setar o tempo de 2hrs no arquivo php.ini
     * limiteTempoSessao constructor.
     * @param string $chave padrao saude
     */
    public function __construct($chave = 'TEMPO_SESSAO_SAUDE')
    {
        $arquivoXml = __DIR__ . "/../WebSocialComum/library/conf/dbConfig.xml";

        //carrega o arquivo XML e retornando um Array
        $xml = simplexml_load_file($arquivoXml);
        $nome = base64_decode($xml->conexao->nome);
        $host = base64_decode($xml->conexao->host);
        $banco = base64_decode($xml->conexao->dbname);
        $usuario = base64_decode($xml->conexao->user);
        $porta = base64_decode($xml->conexao->porta);
        $senha = base64_decode($xml->conexao->password);

        $conexaoString = "host=$host dbname=$banco port=$porta user=$usuario password=$senha options='--client_encoding=UTF8'";

        $conexao = pg_connect($conexaoString);
        pg_set_client_encoding($conexao, 'UNICODE');

        $sql = "SELECT conf_valor_int FROM config WHERE conf_chave='$chave' LIMIT 1";
        $result = pg_query($conexao, $sql);

        $this->anexo = pg_fetch_array($result);
    }

    public function primeiraPagina($primeira = FALSE)
    {

        try {
            session_start();
            $tempo_sessao = ($this->anexo['conf_valor_int'] ? $this->anexo['conf_valor_int'] : 30);
            $horaAtual = new DateTime();
            $horaFinal = clone  $horaAtual;
            $horaFinal->add(new DateInterval('PT' . $tempo_sessao . 'M'));

            if (isset($_SESSION['hora_inicio']) && isset($_SESSION ['hora_fim'])) {
                $horaFimSessao = $_SESSION['hora_fim'];
                if($horaAtual > $horaFimSessao){
                    session_unset();
                    session_destroy(md5("id"));
                    setcookie("PHPSESSID", "", time() - 1);
                    echo "<SCRIPT LANGUAGE=\"JavaScript\">
                                parent.location.reload();
                              </SCRIPT>";
                }
                else {
                    $_SESSION['hora_fim'] = $horaFinal;
                   // die($_SESSION['hora_fim']);
                }
            } else {
                $_SESSION['hora_inicio'] = $horaAtual;
                $_SESSION['hora_fim'] = $horaFinal;
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}