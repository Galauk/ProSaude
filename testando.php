<?php
//require_once("lib/utils/dompdf/dompdf_config.inc.php");
//$html = file_get_contents("modelo_pdf.php");
// 
//$dompdf = new DOMPDF();
//$dompdf->load_html($html);
//$dompdf->set_paper("A4", "portrait"); // aqui você pode configurar o layout da página! :)
//$dompdf->render();
//$dompdf->stream("pdf/meu_arquivo.pdf");
 
?>


<?php
require_once "lib/utils/dompdf/dompdf_config.inc.php";
$html ="<html><body>
Put your html here, or generate it with your favourite 
templating system
</body></html>";
$dompdf = new DOMPDF();
$dompdf->load_html($html);
$dompdf->render();
$dompdf->stream("sample.pdf");

?>