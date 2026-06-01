<?php
session_start();
set_time_limit(0);
include_once $_SESSION[root] . $_SESSION[comum] . "library/php/db.inc.php";
require_once 'descompacta.php';
require_once 'functions.php';

$_SESSION['susUpdate']['dhInicio'] = time();

$zipFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . "file.zip";

if (!file_exists(dirname($zipFile))) {
    if (!mkdir(dirname($zipFile))) {
        die("Falha ao criar pasta tempor&aacute;ria.<br />Crie uma pasta \"tmp\" em \"" . dirname(dirname($zipFile)) . "\".");
    }
}
//die("here");
$query = pg_query("SELECT * FROM log_sus_update ORDER BY lsu_dh_fim DESC LIMIT 1");
$ultimo = pg_fetch_array($query);

$md5 = md5(file_get_contents($_FILES['arquivo']['tmp_name']));

if ($md5 == $ultimo['lsu_md5']) {
    $_SESSION['susUpdate']['dhFim'] = time();
    $_SESSION['susUpdate']['cod'] = 1;
    header("location: mensagem.php?id_login=$id_login");
    exit;
}


if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $zipFile)) {
    // se moveu, salva o MD5 na session
    $_SESSION['susUpdate']['md5'] = $md5;

    $zip = unzip($zipFile);
    if (strlen($zip)) {
        die("falha: " . $zip);
    } else {
        @unlink($zipFile); // deleta o arquivo enviado;
        header("location: dados.php?id_login=$id_login");
    }
} else {
    die("Falha no upload");
}

