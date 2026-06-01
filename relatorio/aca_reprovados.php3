<?php
//header('Location: ../home.php?pg=consulta_inclui_pessoa.phtml&dir=academico');
$projeto="aca_reprovados";

# Defining the Parameters

$Parameters[0] = 'periodo';
$Parameters[1] = 'curso';
$Parameters[2] = 'turma';
$sal_tipo_relatorio = 'mesclado';
include("relatorios/relatoriosgenericos.php3");
?>

<script language="javascript">
function versao_para_impressao()    {
        document.myform.target="blank";
        document.myform.action="home.php?pg=aca_reprovados.php3&dir=academico&subdir=relatorios&impressao=1&periodo=<?echo $periodo;?>&curso=<?echo $curso;?>&turma=<?echo $turma;?>";
        document.myform.submit();
alert("A Página sera aberta em uma nova janela");
        }
</script>
<?php
if ($_GET['impressao'] != "1")
{
?>
<form method="post" action="home.php?pg=aca_reprovados.phtml&dir=academico&subdir=relatorios=." name="myform">
<table width="90%" align="center" class="tablemain">
   <tr>
    <td align="center">
     <input type="submit" name="imp" value=" Imprimir " onClick="versao_para_impressao()">
    </td>
   </tr>
</table>
<?}?>
</form>