<?php
/**
 * @author  Leandro 11/07/2007 - 10:41
*/
?>
<script language=javascript>

function imprimir()
{
    window.print();
}
</script>

<body>
<?php
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>

	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

//----------------  Dados Recebidos  ---------------->

$for_codigo=$_GET[for_codigo];

$Tit="FORNECEDOR";    //NOME DO RELATÓRIO
include 'cabecalho.php';

//-------------  Rotina Captação dos Dados  --------------->

$sql = "SELECT for_nome_fantasia, for_responsavel, for_fone, for_fax, for_email,
        for_homepage, for_nome, for_cnpj, for_cpf, for_insc_est, for_rg,
        for_endereco, for_cidade, for_uf, for_cep
        FROM fornecedor WHERE for_codigo =  $for_codigo";

//print $sql;

$query=db_query($sql);

//----------------  Rotina de Impressão  ------------------>

$lin = 999;
$total = 0;

// Zebragem
$controle = 0;

$row=pg_fetch_row($query);

echo "<table style='font-size:11px;font-family:Tahoma,Arial;' border=0 width='100%' align=center cellspacing=0 cellpadding=1 topmargin=0 leftmargin=0>
        <tr>
            <td align='left'>
                Nome Fantasia: <b>$row[0]
            </td>
        </tr>
        <tr>
            <td align='left'>
                Responsavel: <b>$row[1]</b>
                </td>
        </tr>
        <tr>
            <td align='left'>
                Telefone: <b>$row[2]</b>
                </td>
        </tr>
        <tr>
            <td align='left'>
                Fax: <b>$row[3]</b>
                </td>
        </tr>
        <tr>
            <td align='left'>
                E-mail: <b>$row[4]</b>
                </td>
        </tr>
        <tr>
            <td align='left'>
                Site: <b>$row[5]</b>
                </td>
        </tr>
        <tr>
            <td align='left'>
                Empresa: <b>$row[6]</b>
                </td>
        </tr>
        <tr>
            <td align='left'>
                ".( empty($row[8]) ? "CNPJ: <b>$row[7]</b>" : "CPF: <b>$row[8]</b>" )."
                </td>
        </tr>
        <tr>
            <td align='left'>
                ".( empty($row[10]) ? "IE: <b>$row[9]</b>" : "RG: <b>$row[10]</b>" )."
                </td>
        </tr>
        <tr>
            <td align='left'>
                Endereço: <b>$row[11]</b>
                </td>
        </tr>
        <tr>
            <td align='left'>
                Cidade/UF: <b>$row[12] / $row[13]</b>
                </td>
        </tr>
        <tr>
            <td align='left'>
                CEP: <b>$row[14]</b>
            </td>
        </tr>";
echo "</table>";

?>
