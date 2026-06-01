<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

  $dini = $_POST['data_ini'];
  $dfim = $_POST['data_fim'];
  $set = $_POST['set_codigo'];
  // echo "<pre>";print_r($_POST);die();

  $uni = pg_fetch_array(pg_query("select *from unidade as uni join cidade as cid on cid.cid_codigo_ibge = uni.uni_codigo_ibge
 where uni_codigo = ".$_SESSION['uni_codigo'].""));

  // echo "<pre>";print_r($uni);die();

  $usr = pg_fetch_array(pg_query("select *from usuarios where usr_codigo = ".$_SESSION['id_login'].""));
  
  if($_POST['livro']==1) { 
      $andpsico =  "and pro.psico_codigo in (4,3)"; $n = "A1 E A2";
  }
    
  if($_POST['livro']==2) {
      $andpsico =  "and pro.psico_codigo in (5,93,6)"; $n = "A3, B1 E B2";
  }

  if($_POST['livro']==3) {
    $andpsico =  "and pro.psico_codigo in (1,95)"; $n = "C";
  }

  if($_POST['livro']==4) {
    $andpsico =  "and pro.psico_codigo = 2"; $n = "ANTIBIOTICOS";
  }
  
  $sec = pg_fetch_array(pg_query("select *from secretaria"));

  $sql_1 = pg_query(
      "select distinct(pro_nome),pro.pro_codigo,pro_codigo_dcb,
        (select calcula_estoque4(pro.pro_codigo,".$set.",'".$dini."')) as total 
          from produto as pro 
              join itens_movimento as ite 
                  on ite.pro_codigo = pro.pro_codigo 
              join movimento as m 
                  on m.mov_codigo = ite.mov_codigo 
            where pro.psico_codigo is not null and mov_data <= '".$dfim."' and mov_data >= '".$dini."' and set_saida = '".$set."' ".$andpsico." order by pro_nome
 ") or die(pg_last_error());

// echo "<pre>";print_r(pg_fetch_all($sql_1));die();

  $x=0;
  $t_paginas = pg_num_rows($sql_1);
  while($rq=pg_fetch_array($sql_1)) {
    if($pro_nome) { $x=0;}
       $pro_nome = $rq[pro_nome];
     $query_1 = pg_query("SELECT set_nome,for_nome,ite.ite_quantidade, m.mov_data, m.mov_saida, m.mov_entrada, m.mov_data, m.mov_tipo, usu.usu_nome, pro.pro_codigo_dcb, pro.pro_nome, 
(select sum(sal_qtde) from saldo where pro_codigo = pro.pro_codigo) AS totalsaldo, medin.usr_nome, medex.med_nome, fo.for_nome 
FROM itens_movimento AS ite 
INNER JOIN movimento AS m ON m.mov_codigo=ite.mov_codigo 
LEFT JOIN usuario AS usu ON usu.usu_codigo=m.usu_codigo 
LEFT JOIN setor AS set ON set.set_codigo=m.set_saida 
LEFT JOIN produto AS pro ON ite.pro_codigo=pro.pro_codigo 
LEFT JOIN usuarios AS medin ON medin.usr_codigo=m.med_codigo_interno 
LEFT JOIN medico AS medex ON medex.med_codigo=m.med_codigo_externo 
LEFT JOIN fornecedor AS fo ON fo.for_codigo=m.for_codigo 
WHERE 1=1 ".$andpsico." 
AND (m.mov_data <= '".$dfim."') 
AND (m.mov_data => '".$dini."')
and (set_entrada = '".$set."' OR set_saida = '".$set."')
and pro.pro_codigo = '$rq[pro_codigo]'
ORDER BY pro.pro_nome ASC, mov_data asc");

 while($rx = pg_fetch_array($query_1)) {
  $x++;
      if($x%50==0) { $t_paginas+=1;}
 }
}


$i=0;
$k=0;
?>


<style type="text/css">
<!--
.style5 {font-family: Arial, Helvetica, sans-serif}
.style3 {
  font-family: Arial, Helvetica, sans-serif;
  font-size: 18px;
  font-weight: bold;
}
.style4 {
  font-size: 12px;
  font-style: italic;
}
.break { page-break-before: always; }

-->
</style>
</head>
<body>
<p align="center" class="style3">TERMO DE ABERTURA/ENCERRAMENTO  </p>
<p class="style5"><br />
  <br />
  Este livro cont&eacute;m <strong><?=$t_paginas?></strong> folhas numeradas tipograficamente &agrave;  m&aacute;quina, servir&aacute; para o <br />
  <br />
  <strong>Registro de</strong> substacias psicotropicas das listas <strong><?=$n?></strong> <br />
<br />
da firma <strong><?=$sec[nome_secretaria]?> </strong> <br />
<br />
Farm&aacute;cia <strong><?=$uni[uni_desc]?></strong> <br />
<br />
Farmac&ecirc;utico(a) <strong><?=$usr[usr_nome]?></strong> CRF: <strong><?=$usr[usr_num_conselho]?></strong> <br />
<br />
Estabelecido &agrave; <strong><?=$uni['uni_endereco']?>,<?=$uni['uni_numero']?> <?=$uni['uni_bairro']?></strong> <br />
<br />
Na cidade de <strong><?=$uni['cid_nome']?></strong> Estado de <strong><?=$uni['uf_sigla']?></strong>  <br />
<br />
Inscri&ccedil;&atilde;o Estadual N.&ordm; <strong><?=$uni[uni_cnpj]?></strong><br />
<br />
Inscri&ccedil;&atilde;o no Cadastro Geral do Contribuinte do Minist&eacute;rio da Fazenda <br />
<br />
N.&ordm; _____________________________________</p>
<p class="style5">&nbsp;</p>
<p class="style5">&nbsp;</p>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><div align="center" class="style5">_______________________________________________<br />
      (<span class="style4">Assinatura do Farmac&ecirc;utico</span>) </div></td>
    <td><div align="center"><span class="style5">_______________________________________________<br />
    (<span class="style4">Assinatura e Carimbo da Autoridade Sanit&aacute;ria</span>) </span></div></td>
  </tr>
</table>
<p class="style5">&nbsp;</p>
</body>
</html>

</body>


<div class="break"></div>

<style type="text/css">
<!--
.style1 {
    font-family: Verdana, Arial, Helvetica, sans-serif;
    font-size: 14px;
}
.style2 {
    font-family: Verdana, Arial, Helvetica, sans-serif;
    font-size: 18px;
}
.break { page-break-before: always; }

-->
</style>
</head>

<body>
<?php
function cabeca ($a,$b,$c,$d) {
$caberario = '
  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border: 1px solid;border-color: #000">
  <tr>
    <td class="style2"><strong>'.$b.' - '.$a.'  </strong></td>
    <td align=right><span style="font-size: 10px" align=right>pagina: '.$c.'/'.$d.'</span>&nbsp;</td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="111" class="style1" style="border-left:1px solid;border-bottom: 1px solid;border-color: #000"><table width="111" border="0" cellspacing="0" cellpadding="0" >
      <tr>
        <td width="111" ><div align="center"><strong>Data</strong></div></td>
      </tr>
      <tr>
        <td><table width="100" border="0" cellspacing="0" cellpadding="0" >
          <tr>
            <td width="50" bgcolor="#CCCCCC"><div align="center"><font size=1>Dia</div></td>
            <td width="50" bgcolor="#CCCCCC"><div align="center"><font size=1>Mes</div></td>
            <td width="50" bgcolor="#CCCCCC"><div align="center"><font size=1>Ano</div></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
    <td class="style1" style="border-bottom: 1px solid;border-color: #000">&nbsp;&nbsp;
      <div align="center">
        <strong>
            H&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;I&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;S&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;T&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;O&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;R&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;I&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;C&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;O
        </strong>
      </div>
    </td>
    <td  class="style1" style="border-bottom: 1px solid;border-color: #000"><table width="180" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="111"><div align="center"><strong>MOVIMENTO</strong></div></td>
      </tr>
      <tr>
        <td><table width="200" border="0" cellspacing="1" cellpadding="0">
          <tr>
            <td width="83" bgcolor="#CCCCCC"><div align="center"><font size=1>Entrada</div></td>
            <td width="83" bgcolor="#CCCCCC"><div align="center"><font size=1>Saida</div></td>
            <td width="83" bgcolor="#CCCCCC"><div align="center"><font size=1>Perdas</div></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
    <td width="100" class="style1" style="border-bottom: 1px solid;border-color: #000"><div align="center"><strong>Estoque</strong></div></td>
    <td width="300" class="style1" style="border-bottom: 1px solid;border-right:1px solid;border-color: #000"><div align="center"><strong>Medico</strong></div></td>
  </tr>';

    return $caberario;
}
$sql = pg_query("select distinct(pro_nome),pro.pro_codigo,pro_codigo_dcb,(select calcula_estoque4(pro.pro_codigo,".$set.",'".$dini."')) as total from produto as pro join itens_movimento as ite on ite.pro_codigo = pro.pro_codigo join movimento as m on m.mov_codigo = ite.mov_codigo where pro.psico_codigo is not null and mov_data <= '".$dfim."' and mov_data >= '".$dini."' and set_saida = '".$set."' ".$andpsico." order by pro_nome
 ") or die(pg_last_error());
while($rr = pg_fetch_array($sql)) {
        if($rr[usu_nome] == $usu_nome){
                $saldo = $rr[total];
                $usu_nome = $rr[usu_nome];
                $pagina = $k;
        }   

if($pro_nome) { echo '</table><div class="break"></div>'; $i=0; $k++;}
   $pro_nome = $rr[pro_nome];
    echo cabeca($rr[pro_nome],$rr[pro_codigo_dcb],$k,$t_paginas);

  $i=0;
  $query = pg_query("SELECT med_nome,set_entrada,set_saida,set_nome,for_nome,ite.ite_quantidade, m.mov_data, m.mov_saida, m.mov_entrada, m.mov_data, m.mov_tipo, usu.usu_nome, pro.pro_codigo_dcb, pro.pro_nome, 
(select sum(sal_qtde) from saldo where pro_codigo = pro.pro_codigo) AS totalsaldo, medin.usr_nome, medex.med_nome, fo.for_nome 
FROM itens_movimento AS ite 
INNER JOIN movimento AS m ON m.mov_codigo=ite.mov_codigo 
LEFT JOIN usuario AS usu ON usu.usu_codigo=m.usu_codigo 
LEFT JOIN usuarios as usr on usr.usr_codigo=m.usu_codigo
LEFT JOIN setor AS set ON set.set_codigo=m.set_saida 
LEFT JOIN produto AS pro ON ite.pro_codigo=pro.pro_codigo 
LEFT JOIN usuarios AS medin ON medin.usr_codigo=m.med_codigo_interno 
LEFT JOIN medico AS medex ON medex.med_codigo=m.med_codigo_externo 
LEFT JOIN fornecedor AS fo ON fo.for_codigo=m.for_codigo 
WHERE 1=1 ".$andpsico." 
AND (m.mov_data <= '".$dfim."') 
AND (m.mov_data >= '".$dini."')
and (set_entrada = '".$set."' OR set_saida = '".$set."')
and pro.pro_codigo = '$rr[pro_codigo]'
ORDER BY pro.pro_nome ASC, mov_data asc");




 while($rw = pg_fetch_array($query)) {
   $e = explode("-",$rw[mov_data]);

$i++;

    if($i%50==0) { $k+=1; echo '</table><div class="break"></div>';  echo cabeca($rr[pro_nome],$rr[pro_codigo_dcb],$k,$t_paginas); }

if($rw[usr_nome]=='') {
  $n_medico = $rw[med_nome];
} else {
  $n_medico = $rw[usr_nome];
}
              
switch ($rw[mov_tipo]) {
    case 'E':
        $nome = (empty($rw[for_nome]))?"ENTRADA - FORNECEDOR NAO INFORMADO":"ENTRADA POR FORNECEDOR: ".$rw[for_nome];
        $entrada = $rw[ite_quantidade];
        $saldo += $rw[ite_quantidade];
        $saida = '';
        $medico = $n_medico;
        $perca = '';
        break;
    case 'T';
    if($rw[set_entrada]!=$set) {
        $nome = 'SAIDA POR TRANSFERENCIA - '.$rw[set_nome];
        $entrada = '';
        $saldo -= $rw[ite_quantidade];
        $saida = $rw[ite_quantidade];
        $medico = $n_medico;
        $perca = '';
    } else {
        $nome = 'ENTRADA POR TRANSFERENCIA - '.$rw[set_nome];
        $entrada = $rw[ite_quantidade];
        $saldo += $rw[ite_quantidade];
        $saida = '';
        $medico = $n_medico;
        $perca = '';
    }
        break;
    case 'S';
      if(trim($rw[mov_saida])=='D') {
            $nome = $rw[usu_nome];
            $saida = $rw[ite_quantidade];
            $saldo -= $rw[ite_quantidade];
            $entrada = '';
            $medico = $n_medico;
            $perca = '';
        } else {
          if($rw[pro_nome]=="TIOPENTAL 1GR INJ") {
            $nome = 'SAIDA EXTERNO';
            $medico = 'Wendell Zago Galheira - Medico Veterinario';
          } else {
            $medico = '';            
          }

          if(trim($rw[mov_saida])=="S-VV") {
            $nome = 'SAIDA POR VALIDADE VENCIDA';
            $saida = '';
            $saldo -= $rw[ite_quantidade];
            $entrada = '';
            $perca = $rw[ite_quantidade];
          } else {
            $nome = 'SAIDA EXTERNO';
            $saida = $rw[ite_quantidade];
            $saldo -= $rw[ite_quantidade];
            $entrada = '';
            $perca = '';
      }
        }
        break;
    }

?>

    <tr>
      <td width="150" class="style1"><table width="100" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="50"><div align="center"><font size=1><?=$e[2]?></div></td>
            <td width="50"><div align="center"><font size=1><?=$e[1]?></div></td>
            <td width="50"><div align="center"><font size=1><?=$e[0]?></div></td>
          </tr>
      </table></td>
      <td width="450" class="style1">
       <font size=1><?=$nome?></font>
      </td>
      <td width="350" class="style1"><table width="200" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="110" align='center'><font size=1><?=number_format($entrada, 0, '.', '')?></td>
            <td width="110" align='center'><font size=1><?=number_format($saida, 0, '.', '')?></td>
            <td width="110" align='center'><font size=1><?=number_format($perca, 0, '.', '')?></td>
          </tr>
      </table></td>
      <td width="100" class="style1" align='center'><font size=1><?=$saldo?></td>
      <td class="style1" width="300"><font size=1><?=$medico?></font> </td>
    </tr>

<?php 
 }
}
?>
