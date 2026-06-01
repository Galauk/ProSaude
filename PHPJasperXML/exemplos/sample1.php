<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
include_once('class/tcpdf/tcpdf.php');
include_once("class/PHPJasperXML.inc.php");
include_once ('setting.php');

$server = "localhost";
$user = "postgres";
$pass = "123";
$db = "bdIreport";

$PHPJasperXML = new PHPJasperXML();
$PHPJasperXML->connect($server,$user,$pass,$db,$cndriver="psql"); 
//$PHPJasperXML->debugsql=true;
$PHPJasperXML->arrayParameter=array("parameter1"=>1);
$PHPJasperXML->load_xml_file("sample1.jrxml");

$PHPJasperXML->transferDBtoArray($server,$user,$pass,$db,$cndriver="psql");
$PHPJasperXML->outpage("I");    //page output method I:standard output  D:Download file


?>
