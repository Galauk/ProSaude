<?

session_start();

#ini_set("error_display", 1);
#error_reporting(E_ALL);

$unidade = $_POST['uni_codigo'];       

$_SESSION['unidade'] = $unidade;

echo "
    <script>
        window.location.href = 'painel.php'
    </script>
";
    
exit;