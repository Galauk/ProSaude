<?php
  error_reporting(E_ALL) ;
  session_start();
  require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
  require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

  $di = $_REQUEST['di'];
  $df = $_REQUEST['df'];

cabecario_rel('Relatorio por Faixa Etaria de Idade Feminino',$di,$df,null);


$sql = pg_query("SELECT usu_nome, calcula_idade(usu_codigo) as idade, CONCAT_WS(' ',rua_nome,dom_numero,bai_nome) as Endereco from usuario 
LEFT JOIN domicilio on usuario.dom_codigo = domicilio.dom_codigo
LEFT JOIN rua on domicilio.rua_codigo = rua.rua_codigo
LEFT JOIN bairro on rua.bai_codigo = bairro.bai_codigo
WHERE usu_sexo = '1' and calcula_idade(usu_codigo) >= '".$di."' and calcula_idade(usu_codigo) <= '".$df."' group by usu_nome, idade, endereco order by idade") or die (pg_last_error());

echo "<table width=100% cellspacing=5 cellpadding=5 border=1>
<tr>
   <td>Paciente</td>
   <td>Idade</td>
   <td>Endereco</td>
</tr>";
while($rr=pg_fetch_array($sql)) {
  echo "<tr>
     <td>$rr[usu_nome]</td>
     <td>$rr[idade]</td>
     <td>$rr[endereco]</td>
  </tr>";
}
echo "</table>";
?>