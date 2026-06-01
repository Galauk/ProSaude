<?

function parse() {
    if($_GET){
        $data = json_decode(json_encode($_GET));
        return $data;
    }
    
    $json = file_get_contents('php://input');
    $data = json_decode($json);

    return $data;
}