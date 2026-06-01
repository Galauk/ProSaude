<?php

/**
@Arquivos Relacionados: layout_critica.inc.php layout_critica_op.php layout_critica_popup.php
@Tabelas: familia, usuario, cidade, layout_critica
@Acao: Form para geracao do arquivo de exportacao para o SUS
*/ 

session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."layout_critica.inc.php";

Cabecario( $hotkey = false );

$LC = & new LayoutCritica( $ibge , $debug = ! true ) ;
$LC->id_login 		= $id_login;
$LC->tipo_filtro 	= $tipo_filtro;
$LC->data_ini 		= $data_ini;
$LC->data_fim 		= $data_fim;
$LC->criaArquivo();
print $LC->mensagem;

