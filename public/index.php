<?php
// public/index.php

require_once __DIR__ . '/../vendor/autoload.php';

// 1. O Config lê o .env da raiz do projeto
\App\Core\Config::load(__DIR__ . '/../.env');

// 2. Daqui para frente, qualquer Controller que instanciar um Repository 
// poderá passar o PDO chamando: \App\Core\Database::getConnection();