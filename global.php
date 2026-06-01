<?php
	
/**
 * Este arquivo tenta suprir a necessidade de tantos includes no inicio de cada arquivo.php 
 * e tbm elimina os caminhos salvos em $_SESSIONS
 */

// Habilitar erros*


function setError($mostrar=FALSE){
	if($mostrar){
		error_reporting(E_ALL & ~E_NOTICE );
		ini_set("display_errors",1);
		ini_set("ignore_repeated_errors",0);
	}
	return true;
}


// inicia session
session_start();	

$link = $_SERVER[SERVER_ADDR];
if($_SERVER['SERVER_PORT']!= 80){
	$link .= ":" . $_SERVER['SERVER_PORT'];
}

define("DS", DIRECTORY_SEPARATOR);
define("SOCIAL", dirname(__FILE__) . DS);
define("SAUDE", dirname(__FILE__) . DS);
define("COMUM", dirname( SOCIAL ) . DS . "WebSocialComum" . DS);
define("LINKCOMUM", "/WebSocialComum" );
define("LINKSAUDE", "/WebSocialSaude" );

// Sempre incluir:
require_once COMUM . "/library/php/db.inc.php";


// Autoload para as classes mais comuns
function __autoload($class_name) {
    switch ($class_name) {
    	case "commonClass":
    		require_once COMUM . '/class/commonClass.php';
    	break;
    	case "classForm":
    		require_once COMUM . '/class/formClass.php';
    	break;
    	case "tableClass":
    		require_once COMUM . '/class/tableClass.php';
    	break;
    }
}

function fdebug($var,$label="Debug"){
	//echo "$label: $var\n<br />"; return;
	$fbPath = dirname(SOCIAL) . "/falci/FirePHPCore/fb.php";
    if( !file_exists( $fbPath ))
    	return false;    	
    
    require_once $fbPath;
    fb($var, $label);
}
