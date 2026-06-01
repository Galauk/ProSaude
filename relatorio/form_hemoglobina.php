<?php
session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario($hotkey = true);
echo "<style type='text/css'>
			.quebra_pagina{
			page-break-before:always;
			}
			tr{
			font-size:12px;
			}
			</style>";
echo "<link href='".$_SESSION[modulo]."estilo.css' rel='stylesheet' type='text/css'>\n";			

$sql =  "SELECT * from unidade where cnes_ativo = 'A'";

$recebeQuery = pg_query($sql);

$contador = 0;

while ($contador < pg_num_rows($recebeQuery)) {
	$recebeResultado[$contador] = pg_fetch_array($recebeQuery);
	$contador++;
}

// echo "<pre>";print_r($recebeResultado);die();


?>
<script language="JavaScript" type="text/javascript" src="../funcoes.js"></script>
<script src=script.js></script>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title>GPS - Software de Gestão Pública</title>
    <script src="funcoes.js"></script>
    <script type="text/javascript" src="../ajax_motor.js"></script>
    <script language="JavaScript">
        var maxDay = new Array(31,29,31,30,31,30,31,31,30,31,30,31);
    </script>
</head>
<body>
    <fieldset>
        <legend>Solicita&ccedil;&otilde;oes HEMOGLOBINA </legend>
        <form method="post" action="relatorio/rel_hemoglobina.php" target='_blank'>
            <table>
                <div style="margin: 5px;">
                    <label style="font-size: 14px">Unidade:&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;</label>
                    <select id="unidadeId" name="unidadeId" style="padding: 5px;">
                        <? for ($i=0; $i < count($recebeResultado) ; $i++) { ?>
                            <option id="<?=$recebeResultado[$i][uni_codigo]?>" value = "<?=$recebeResultado[$i][uni_codigo]?>"><?=$recebeResultado[$i][uni_desc]?></option>			
                        <? } ?>
                    </select>
                </div>          
                <div style="margin: 5px;">
                    <label style="font-size: 14px">Data Inicial:&nbsp;</label>
                    <input type="date" name="dataInicial" style="padding: 5px;">
                </div>

                <div style="margin: 5px;">
                    <label style="font-size: 14px">Data Final:&nbsp;&nbsp;&nbsp;</label>
                    <input type="date" name="dataFinal" style="padding: 5px;">
                </div>

                <tr>
                    <td><input type="image" src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/gerar_relatorio_on.jpg"></td>
                </tr>

            </table>

        </form>
    </fieldset>
</body>