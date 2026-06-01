<?php

	require_once "global.php";
	require_once SOCIAL . "/codigobarras.php";
	
	$lw = ($_GET[lw] != "" ? $_GET[lw] : 1); //Se receber algo, obedece tamanho recebido, caso contrário, tamanho padrão.
	$hi = ($_GET[hi] != "" ? $_GET[hi] : 40);//Se receber algo, obedece tamanho recebido, caso contrário, tamanho padrão.
	
	$cod_bar = str_pad($age_codigo, 11, 0, STR_PAD_LEFT);

	CodigoBarras($cod_bar, $lw, $hi);
