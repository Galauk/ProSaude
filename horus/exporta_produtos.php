<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
$db = pg_connect("host='".$host_correto."' port='".$porta_correto."' dbname='".$banco_correto."' user='".$usuario_correto."' password='".$senha_correto."'") or die ("Não foi possivel conectar ao servidor");pg_query("SET CLIENT_ENCODING=UTF8");
function remover_acentos($string) {
    $string = preg_replace("/[ÃƒÂ‡ÃƒÂƒ]/", "ÇÃ", $string);
    $string = preg_replace("/[áàâãä]/", "a", $string);
    $string = preg_replace("/[ÁÀÂÃÄ]/", "A", $string);
    $string = preg_replace("/[éèê]/", "e", $string);
    $string = preg_replace("/[ÉÈÊ]/", "E", $string);
    $string = preg_replace("/[íì]/", "i", $string);
    $string = preg_replace("/[ÍÌ]/", "I", $string);
    $string = preg_replace("/[óòôõö]/", "o", $string);
    $string = preg_replace("/[ÓÒÔÕÖ]/", "O", $string);
    $string = preg_replace("/[úùü]/", "u", $string);
    $string = preg_replace("/[ÚÙÜ]/", "U", $string);
    //$string = preg_replace("/?/", "ÇÃ", $string);
    $string = preg_replace("/ç/", "c", $string);
    $string = preg_replace("/Ç/", "C", $string);
    $string = preg_replace("/[][><}{)(:;,!?*%~^`&#@]/", "", $string);
    return $string;
}
// Exemplo de scrip para exibir os nomes obtidos no arquivo CSV de exemplo
$delimitador = ';';
$cerca = '"';
// Abrir arquivo para leitura
$f = fopen('produtos_horus.csv', 'r');
if ($f) { 
    // Ler cabecalho do arquivo
    $cabecalho = fgetcsv($f, 0, $delimitador, $cerca);
    // Enquanto nao terminar o arquivo
    while (!feof($f)) { 
        // Ler uma linha do arquivo
        $linha = fgetcsv($f, 0, $delimitador, $cerca);
        if (!$linha) {
            continue;
        }
        // Montar registro com valores indexados pelo cabecalho
		$sqlConf = "";
        $registro = array_combine($cabecalho, $linha);
		$sqlInsert = "INSERT INTO horus (hor_codigo,hor_descricao,hor_concentracao,hor_forma_farmaceutica,hor_volume,hor_un_fornecimento) VALUES 
					('".trim($registro["hor_codigo"])."','".trim(utf8_encode(remover_acentos($registro["hor_descricao"])))."','".trim(utf8_encode($registro["hor_concentracao"]))."','".trim(utf8_encode(remover_acentos($registro["hor_forma_farmaceutica"])))."','".trim(utf8_encode($registro["hor_volume"]))."','".trim(utf8_encode(remover_acentos($registro["hor_un_fornecimento"])))."');";
		$queryInsert = pg_query($sqlInsert);
    }
    fclose($f);
}
echo "Exportação realizada com sucesso!";
?>