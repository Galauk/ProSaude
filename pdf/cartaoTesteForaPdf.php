<SCRIPT Language="Javascript">
function imprimir()
{
	window.print() ;
}
</script>
<?php

require_once "barcode.inc.php";
require_once "funcoes.inc.php";
require_once "../global.php";

$sql = pg_query("select to_char(usu_datanasc,'DD/MM/YYYY') as data,*from usuario where usu_codigo = '$_REQUEST[usu_codigo]'");
$rr = pg_fetch_array($sql);

new barCodeGenrator(str_pad($rr[usu_codigo], '19', '0', STR_PAD_LEFT),1,'cod.gif', 190, 130, true); 

?>
<html>

	<style>
body {
    background-image: url("cartao_municipal.png");
    background-repeat: no-repeat;
    background-size: 653px 184px;
}
.nome_completo {
	font-family: "source_sans_proregular", Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif;
    font-size:13px;
	font-weight: bold;
	position: absolute;
	top:53px;
	left:105px;
}
.nome_mae {
	font-family: "source_sans_proregular", Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif;
    font-size:13px;
	font-weight: bold;
	position: absolute;
	top:80px;
	left:105px;
}
.data_nascimento {
	font-family: "source_sans_proregular", Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif;
    font-size:13px;
	font-weight: bold;
	position: absolute;
	top:108px;
	left:105px;
}
.cartao_sus {
	font-family: "source_sans_proregular", Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif;
    font-size:27px;
	font-weight: bold;
	position: absolute;
	top:130px;
	left:33px;
}
.codigobarras {
	font-family: "source_sans_proregular", Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif;
    font-size:10px;
	font-weight: bold;
	position: absolute;
	top:80px;
	left:526px;
}
</style>
<body onload="imprimir()">
<div class='codigobarras'><img src='cod.gif' width='100' height='70'></div>
<?php
echo "<div class='nome_mae'>".abr ($rr[usu_mae])."</div>";
echo "<div class='nome_completo'>".abr ($rr[usu_nome])."</div>";
echo "<div class='data_nascimento'>$rr[data]</div>";
echo "<div class='cartao_sus'>$rr[usu_cartao_sus]</div>";

echo "</body></html>";
?>