<?php	
session_start();
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

require_once 'dompdf/lib/html5lib/Parser.php';
require_once 'dompdf/lib/php-font-lib/src/FontLib/Autoloader.php';
require_once 'dompdf/lib/php-svg-lib/src/autoload.php';
require_once 'dompdf/src/Autoloader.php';

require_once "barcode.inc.php";
require_once "funcoes.inc.php";

$sql = pg_query("select to_char(usu_datanasc,'DD/MM/YYYY') as data,*from usuario where usu_codigo = '$_REQUEST[usu_codigo]'");
$rr = pg_fetch_array($sql);

new barCodeGenrator(str_pad($rr[usu_prontuario], '19', '0', STR_PAD_LEFT),1,'cod.gif', 190, 130, true); 

Dompdf\Autoloader::register();

// reference the Dompdf namespace
use Dompdf\Dompdf;

$html = '';
$html .= <<<'ENDHTML'
<html>

	<style>
body {
    background-image: url("cartao_municipal.png");
    background-repeat: no-repeat;
}
.nome_completo {
	font-family: "source_sans_proregular", Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif;
    font-size:12;
	font-weight: bold;
	position: absolute;
	top:75px;
	left:140px;
}
.nome_mae {
	font-family: "source_sans_proregular", Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif;
    font-size:12;
	font-weight: bold;
	position: absolute;
	top:110px;
	left:140px;
}
.data_nascimento {
	font-family: "source_sans_proregular", Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif;
    font-size:12;
	font-weight: bold;
	position: absolute;
	top:146px;
	left:140px;
}
.cartao_sus {
	font-family: "source_sans_proregular", Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif;
    font-size:40px;
	font-weight: bold;
	position: absolute;
	top:177px;
	left:33px;
}
.codigobarras {
	font-family: "source_sans_proregular", Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif;
    font-size:40px;
	font-weight: bold;
	position: absolute;
	top:120px;
	left:724px;
}
</style>

<body>
<div class='codigobarras'><img src='cod.gif' width='149' height='89'></div>
ENDHTML;
$html .= "<div class='nome_mae'>".abr ($rr[usu_mae])."</div>";
$html .= "<div class='nome_completo'>".abr ($rr[usu_nome])."</div>";
$html .= "<div class='data_nascimento'>$rr[data]</div>";
$html .= "<div class='cartao_sus'>$rr[usu_cartao_sus]</div>";

$html .= "</body></html>";


// instantiate and use the dompdf class
$dompdf = new Dompdf();
$dompdf->loadHtml($html);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser

$dompdf->stream();

?>
