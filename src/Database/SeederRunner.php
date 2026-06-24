<?php

namespace App\Database;

use Exception;

class SeederRunner extends Executable
{
    public function run(): void
    {
        $files = glob(database_path('seeds/*.php'));

        if (empty($files)) {
            echo "Nenhum seeder encontrado em: " . database_path('seeds') . "\n";
            return;
        }

        echo "Encontrados " . count($files) . " seed(s).\n\n";

        foreach ($files as $file) {

            $seederName = basename($file);

            try {

                echo "Executando: {$seederName}\n";

                $seeder = require $file;

                if (!$seeder instanceof Seeder) {
                    echo "{$seederName} não é um Seeder válido.\n";
                    continue;
                }

                $this->invoke($seeder,'run');

                echo "Seeder executado com sucesso: {$seederName}\n\n";

            } catch (Exception $e) {

                echo "Erro no Seeder {$seederName}: "
                    . $e->getMessage()
                    . "\n\n";
            }
        }
    }
}