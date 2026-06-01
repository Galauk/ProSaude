<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
#  require_once $_SESSION[root].$_SESSION[comum]."library/css/estilo.css";

  $dini = $_REQUEST['data_ini'];
  $dfim = $_REQUEST['data_fim'];
  $set = $_REQUEST['set_codigo'];

 function imc($altura, $peso){
$altura = str_replace(',', '.', $altura);
$altura = $altura * $altura;
$result = $peso / $altura;
return $result;}

cabecario('Relatorio de IMC',$dini,$dfim,null);

$sql = pg_query("select to_char(usu_datanasc,'DD/MM/YYYY') as datanasc,calcula_idade(usu.usu_codigo) as idade,*from pre_consulta as pre join agendamento as age on pre.age_codigo=age.age_codigo join usuario as usu on usu.usu_codigo = age.usu_codigo and pc_peso is not null and pc_altura is not null order by usu_sexo,idade") or die(pg_last_error());

echo "<table class='lista' width='900'cellspacing=3 cellpadding=5 border=1>
  <tr bgcolor='ebebeb'>
   <td>Paciente</td>
   <td>Idade</td>
   <td>Data de Nascimento</td>
   <td>Sexo</td>
   <td>Peso</td>
   <td>Altura</td>
   <td>IMC</td>
   </tr>";

while($rr=pg_fetch_array($sql)) {

$imc = imc($rr[pc_altura],$rr[pc_peso]);
$sexo = ($rr[usu_sexo]=='F')?"Fem.":"Masc.";
  if($imc>=30) {
    echo "<tr>
           <td>$rr[usu_nome]</td>
           <td>$rr[idade]</td>
           <td>$rr[datanasc]</td>
           <td>$sexo</td>
           <td>$rr[pc_altura]</td>
           <td>$rr[pc_peso]</td>
           <td>".number_format($imc)."</td>
           </tr>";
  }
}
echo "</table>";


?>