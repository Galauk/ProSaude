<?php

include_once('../class/tcpdf/tcpdf.php');
include_once("../class/PHPJasperXML.inc.php");
include_once("../parametros_db.php");

$server = $host;
$user = $usuario;
$pass = $senha;
$db = $banco;
$port = $porta;
$asaeCodigo = $_GET["asae_codigo"];

$mes = $_GET["mes"];
$ano = $_GET["ano"];
$uniCodigo = $_GET["uni_codigo"];

$PHPJasperXML = new PHPJasperXML();
$PHPJasperXML->connect($server,$user,$pass,$db,$port,$cndriver="psql");

$caminho = "../../zf/public/images/brasao.jpg";

$sqlUnidade = "SELECT uni_desc FROM unidade WHERE uni_codigo = $uniCodigo";
$queryUnidade = pg_query($sqlUnidade);
$regUnidade = pg_fetch_array($queryUnidade);

$sqlSecretaria = "SELECT
          sec.nome_secretaria,
          sec.cnpj_secretaria,
          sec.endereco_secretaria,
          sec.numero_end_secretaria,
          sec.sec_bairro,
          sec.telefone_secretaria,
          sec.nome_cidade
          FROM
          secretaria AS sec
          WHERE
          sec.sec_as = 't'";
$querySecretaria = pg_query($sqlSecretaria);
$regSecretaria = pg_fetch_array($querySecretaria);
$dadosEndSecretaria =  $regSecretaria[endereco_secretaria]." ".$regSecretaria[numero_end_secretaria].", ".$regSecretaria[sec_bairro];

$sql = "SELECT
      sec.nome_secretaria,
      sec.cnpj_secretaria,
      sec.endereco_secretaria,
      sec.numero_end_secretaria,
      sec.sec_bairro,
      sec.telefone_secretaria
      FROM
      secretaria AS sec
      WHERE
      sec.sec_as = 't'";

//Total de cidadãos cadastrados no mês
$sqlTotMes = "SELECT
          COUNT(DISTINCT usu.usu_codigo) AS tot_mes
        FROM
          usuario AS usu
        WHERE
          to_char(usu.usr_cad_dt,'MM/YYYY') = '$mes/$ano'";
$queryTotMes = pg_query($sqlTotMes);
$regTotMes = pg_fetch_array($queryTotMes);

//Total de cadastros no sistema
$sqlTotCad = "SELECT
          COUNT(DISTINCT usu.usu_codigo) AS tot_cad
        FROM
          usuario AS usu";
$queryTotCad = pg_query($sqlTotCad);
$regTotCad = pg_fetch_array($queryTotCad);

//$PHPJasperXML->debugsql=true;
$PHPJasperXML->arrayParameter=array(
  "tot_mes" => $regTotMes["tot_mes"],
  "tot_cad" => $regTotCad["tot_cad"],
  "mes" => $mes,
  "ano" => $ano,
  "caminho_img"=>$caminho,
  "nome_secretaria"=>$regSecretaria[nome_secretaria],
  "nome_cidade"=>$regSecretaria[nome_cidade],
  "nome_unidade"=>$regUnidade[uni_desc],
  "cnpj_secretaria"=> "CNPJ: ".$regSecretaria[cnpj_secretaria],
  "dados_endsecretaria"=>$dadosEndSecretaria,
  "sql"=>$sql
);

$PHPJasperXML->load_xml_file("RelatorioTotalCadastros.jrxml");

$PHPJasperXML->transferDBtoArray($server,$user,$pass,$db,$cndriver="psql");
$PHPJasperXML->outpage("I");    //page output method I:standard output  D:Download file

?>
