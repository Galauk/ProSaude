<?

session_start();

ini_set("error_display", 1);
error_reporting(E_ALL);

require_once($_SESSION['root'].$_SESSION['comum']."library/php/db.inc.php");

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    
    if(isset($_SESSION['unidade'])){
        $sql = pg_query("SELECT * FROM unidade WHERE uni_codigo = ".$_SESSION['unidade']) or die(pg_last_error());
        
        echo json_encode(pg_fetch_object($sql));
    } else {
        echo "false";
    }
} else {
    echo "error";
}

exit;