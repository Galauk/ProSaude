<?php

require_once("../global.php");
require_once COMUM . "/library/php/funcoes.db.php"; // getConfig();

$file = $_GET['file'];
$fileName = end(explode("/",$file));
list(,$nomeMes) = explode(".",$fileName);

$registros = $_GET['registros'];
$controle = $_GET['controle'];

// Dados do usuário:
$sql = "SELECT usr_nome, usr_cpf FROM usuarios WHERE usr_codigo=".$_SESSION['id_login'];
//die($sql);
$query = pg_query($sql);
$dados = pg_fetch_object($query);


// Sigla: http://dtr2001.saude.gov.br/sas/PORTARIAS/Port99/PT-%200139.html
$sigla = "PA"; // Produção Ambulatorial
$sigla .= "SM"; // Secretaria Municipal
$sigla .= getConfig("SIGLA_MUNICIPIO"); // 3 letras (ex.: SMI = São Miguel)
$sigla .= ".$nomeMes"; // JAN, FEV, MAR [...]

?>
<pre>*******************************************************************Versao: <?=$_SESSION['versao']."\n";?>
MS/SAS/DATASUS/     SISTEMA DE INFORMACOES AMBULATORIAIS            DATA COMP.
<?=date("d/m/Y");?>            RELATORIO DE CONTROLE DE REMESSA                <?=$nomeMes;?>/<?=$anoRef."\n";?>
************************************************************<!-- Versao banco :201205b -->


 ORGAO RESPONSAVEL PELA INFORMACAO

 NOME   : <?=$dados->usr_nome."\n";?>

 SIGLA  : <?=$sigla."\n";?>

 CGC/CPF: <?=$dados->usr_cpf."\n";?>


 Carimbo e
 Assinatura : ___________________



 SECRETARIA DE SAUDE DESTINO DOS B.P.A.(s)

 NOME  : asdfasd fasdf

 ORGAO (M)UNICIPAL OU (E)STADUAL : M


 Setor de                                       Carimbo e
 Recebimento : ____________ Data : ___/___/___  Assinatura : ________________



 ARQUIVO DE BPA(s) GERADO

               NOME : <?=$fileName;?>

 REGISTROS GRAVADOS : <?=str_pad($registros, 6, 0, STR_PAD_LEFT);?>

             BPA(s) : 000001

  CAMPO DE CONTROLE : <?=str_pad($controle, 4, 0, STR_PAD_LEFT);?>





    (ENCAMINHAR ESTE RELATORIO JUNTAMENTE COM O ARQUIVO DE BPA(s) GERADO.)</pre>
    
    <script>
    	window.open('<?=$file?>');
    </script>