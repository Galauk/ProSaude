<?

include('includes.php');

function build_query($query){

    $query_array = array();

    foreach( $query as $key => $key_value ){
        $query_array[] = urlencode( $key ) . '=' . urlencode( $key_value );
    }

    return implode( '&', $query_array );
}

function getHashCode(){
    $h = sha1(time());

    return substr($h, 0, 6).'.'.substr($h, 7, 6);
}

function tirarAcentos($string){
    return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"), explode(" ","a A e E i I o O u U n N"), $string);
}

if($_SERVER['REQUEST_METHOD'] == "GET"){
    echo json_encode(
        array(
            "sync" => true
        )
    );

    exit;
}

if($_SERVER['REQUEST_METHOD'] == "POST"){
    ini_set('display_errors', 1);
    //Report runtime errors
    error_reporting(E_ERROR | E_WARNING | E_PARSE);
    //error_reporting(E_ALL & ~E_NOTICE);
    // Tell php where your custom php error log is
    ini_set('error_log', 'php_error.log');
    
    $dados = parse();
    // error_reporting(E_ALL);
    // ini_set("display_errors", 1);
    
    $inserts = [];

    $hashCode = getHashCode();

    if(count($dados->domicilios) > 0){
        
        foreach($dados->domicilios as $val){    // insert domicilio
            
            $insertKey = NULL;
            $insertValues = NULL;

            $update = NULL;

            if(isset($val->domicilio->chave) && $val->domicilio->chave != ""){
                foreach($val->domicilio as $key => $value){
                    if($key != 'exportar' && $key != "chave" && $key != "obervacao"){
                        
                        $update .= $key."='".trim(tirarAcentos($value))."', ";
                    }
                }
                
                $update = substr($update, 0, strlen($update)-2);

                $sqlUpdate = "UPDATE app_domicilio SET $update WHERE controle = '{$val->domicilio->chave}'";
                echo $sqlInsert; die;
                $resultUpdate = pg_query($sqlUpdate) or die(pg_last_error());

            } else {
                foreach($val->domicilio as $key => $value){
                    if($key != 'exportar' && $key != "chave" && $key != "obervacao"){
                        
                        $insertKey.=$key.', ';
                        
                        $value = tirarAcentos($value);
                        
                        $insertValues.="'$value', ";
                    }
                }

                $insertKey.='controle, ';
                $insertValues.="'$hashCode', ";

                $insertKey = substr($insertKey, 0, strlen($insertKey)-2);
                $insertValues = substr($insertValues, 0, strlen($insertValues)-2);
            
                $sqlInsert = "INSERT INTO app_domicilio ($insertKey) VALUES ($insertValues)";
                // echo $sqlInsert;
                $insert = pg_query($sqlInsert) or die(pg_last_error());
            }
        }
    }

    if(count($dados->usuarios) > 0){
        foreach($dados->usuarios as $val){
            $insertKey = NULL;
            $insertValues = NULL;


            $update = NULL;

            if(isset($val->usuario->chave) && $val->usuario->chave != ""){
                foreach($val->usuario as $key => $value){
                    if($key != 'exportar' && $key != "chave" && $key != "obervacao"){
                        $update .= $key."='".trim(tirarAcentos($value))."', ";
                    }
                }
                
                $update = substr($update, 0, strlen($update)-2);

                $sqlUpdate = "UPDATE app_usuario SET $update WHERE controle = '{$val->domicilio->chave}'";
                // echo $sqlInsert;
                $resultUpdate = pg_query($sqlUpdate) or die(pg_last_error());
            } else {
                foreach($val->usuario as $key => $value){
                    if($key != 'exportar' && $key != "chave" && $key != "obervacao"){
                        if($key == "usu_frenquencia_escolar"){
                            $key = "usu_frequencia_escolar";
                        }

                        $insertKey.=$key.', ';
                        
                        $value = tirarAcentos($value);
                        $insertValues.="'$value', ";
                    }
                }
                
                $insertKey.='controle, ';
                $insertValues.="'$hashCode', ";

                $insertKey = substr($insertKey, 0, strlen($insertKey)-2);
                $insertValues = substr($insertValues, 0, strlen($insertValues)-2);
                
                // echo $where;
                $select = "SELECT * FROM app_usuario WHERE usu_cartao_sus = '{$val->usuario->usu_cartao_sus}'";

                $resultado = pg_query($select) or die(pg_last_error());
                $sqlInsert = NULL;
                $numRows = pg_num_rows($resultado);
                if($numRows == 0){
                    // INSERT
                    $sqlInsert = "INSERT INTO app_usuario ($insertKey) VALUES ($insertValues)";
                    
                    $insert = pg_query($sqlInsert) or die(pg_last_error());
                }
            }
        }
    }
    
    if(count($dados->visitas) > 0){
        foreach($dados->visitas as $val){
            $insertKey = NULL;
            $insertValues = NULL;

            $update = NULL;

            if(isset($val->visita->chave) && $val->visita->chave != ""){
                foreach($val->visita as $key => $value){
                    if($key != 'exportar' && $key != "chave" && $key != "visita" && $key != "obervacao"){
                        if($key == "obervacao"){
                            $key = "observacao";
                        }

                        $update .= $key."='".trim(tirarAcentos($value))."', ";
                    }
                }
                
                $update = substr($update, 0, strlen($update)-2);

                $sqlUpdate = "UPDATE app_visita SET $update WHERE controle = '{$val->visita->chave}'";
                // echo $sqlInsert;
                $resultUpdate = pg_query($sqlUpdate) or die(pg_last_error());
            } else {
                foreach($val->visita_domiciliar as $key => $value){
                    
                    if($key != 'exportar' && $key != "chave" && $key != "visita" && $key != "obervacao"){
                        

                        $insertKey.=$key.', ';

                        if(is_array($value)){
                            $value = implode(",", $value);
                            
                            $value = tirarAcentos($value);
                            $insertValues.="'$value', ";
                        } else {
                            $value = tirarAcentos($value);
                            $insertValues.="'$value', ";
                        }
                    }
                }
                
                $insertKey.='controle, ';
                $insertValues.="'$hashCode', ";

                $insertKey = substr($insertKey, 0, strlen($insertKey)-2);
                $insertValues = substr($insertValues, 0, strlen($insertValues)-2);
                
                // INSERT
                $sqlInsert = NULL;
                $sqlInsert = "INSERT INTO app_visita ($insertKey) VALUES ($insertValues)";
    
                $insert = pg_query($sqlInsert) or die(pg_last_error());
            }
        }
    }
    
    
    $size = strlen(json_encode($resultado, JSON_PRETTY_PRINT));
    header("Content-type: json");
    header("Content-length: $size");
    
    echo json_encode(
        array(
            "chave" => $hashCode,
            "data" => date("Y-m-d H:i:s")
        )
    );
    exit;
    
}