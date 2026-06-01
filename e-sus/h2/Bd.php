<?php
//set_time_limit(1000000000000000000);
//ini_set("display_errors",1);
//$connection = pg_connect("host='localhost' dbname='C:/e-SUS-AB/e-SUS-AB PEC/bin/database/esus-h2;AUTO_SERVER=TRUE' port='5435' user='sa' password='sa'") or die("Erro"); 

$arquivoXml = $_SERVER['DOCUMENT_ROOT']."/WebSocialComum/library/conf/dbConfig.xml";
$xml = simplexml_load_file($arquivoXml);
$host = base64_decode($xml->conexao->host);
$banco = base64_decode($xml->conexao->dbname);
$usuario = base64_decode($xml->conexao->user);
$porta = base64_decode($xml->conexao->porta);
$senha = base64_decode($xml->conexao->password);
$connectionPg = pg_connect("host=$host dbname=$banco user=$usuario port=$porta password=$senha");
//echo $_SERVER['DOCUMENT_ROOT'];

function getGUID(){
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

// Retira acentos, deixa, tudo em maiusculo e retira espa�o em branco
function trataDados($string){
	$string = preg_replace("/[�����]/", "a", $string);
	$string = preg_replace("/[�����]/", "A", $string);
	$string = preg_replace("/[���]/", "e", $string);
	$string = preg_replace("/[���]/", "E", $string);
	$string = preg_replace("/[��]/", "i", $string);
	$string = preg_replace("/[��]/", "I", $string);
	$string = preg_replace("/[�����]/", "o", $string);
	$string = preg_replace("/[�����]/", "O", $string);
	$string = preg_replace("/[���]/", "u", $string);
	$string = str_replace("�", "c", $string);
	$string = str_replace("�", "C", $string);
	$string = str_replace("/", "", $string);
	$string = str_replace('"\"', "", $string);
	$string = str_replace("'", "", $string);
	return trim(strtoupper(utf8_encode($string)));
}

function removeCaracterAcento($string){
	$string = preg_replace("/[�����]/", "", $string);
	$string = preg_replace("/[�����]/", "", $string);
	$string = preg_replace("/[���]/", "", $string);
	$string = preg_replace("/[���]/", "", $string);
	$string = preg_replace("/[��]/", "", $string);
	$string = preg_replace("/[��]/", "", $string);
	$string = preg_replace("/[�����]/", "", $string);
	$string = preg_replace("/[�����]/", "", $string);
	$string = preg_replace("/[���]/", "", $string);
	$string = str_replace("�","", utf8_decode($string));
	$string = str_replace("�","", $string);
	$string = str_replace("�","", $string);
	$string = str_replace("/","", $string);
	$string = str_replace('"\"',"", $string);
	$string = str_replace("'","", $string);
	return trim(strtoupper(utf8_decode($string)));
}

function codificacao($string) {
	return mb_detect_encoding($string.'x', 'UTF-8, ISO-8859-1');
}

// Remove caracteres especiais da string
function trataCaracteres($string){
	$string = str_replace("'", "", $string);
	$string = str_replace("S/N", "null", $string);
	return $string;
}

?>
