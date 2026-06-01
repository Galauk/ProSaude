<?php

class Elotech_View_Helper_Html2Pdf extends Zend_View_Helper_Abstract {

    function html2Pdf($dados) {
	$html = '
        <link rel="stylesheet" type="text/css" href="css/exemploPdf.css" />

        <div id="logo"></div>
        <span id="texto">HTML2PDF</span>

        <table>
                <tr>
                        <td>
                                OKAAKKAK
                        </td>
                        <td>
                                OKAAKKAK
                        </td>
                        <td>
                                OKAAKKAK
                        </td>
                </tr>
        </table>';

        try
        {
            $html2pdf = new Application_Model_Html2Pdf('P','A4','pt', true, 'UTF-8', 2);
            $html2pdf->writeHTML($html);

            $html2pdf->Output('exemploPdf.pdf', 'I');
        }
        catch(HTML2PDF_exception $e)
        {
         echo $e;
        }
    }

} 