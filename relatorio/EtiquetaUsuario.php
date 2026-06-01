<html>
<head>
<link href="estilo_etiqueta.css" rel="stylesheet" type="text/css" >

<head>

<body>
<script language=javascript>

function imprimir() {
       window.print();
}
</script>
<body onload='imprimir()'>
<?php

function CalcIdade($data_nasc) { // YYYY-MM-DD
      $h_ano=date("Y");
      $h_mes=date("m");
      $h_dia=date("d");

      $n_ano=substr($data_nasc, 0, 4);
      $n_mes=substr($data_nasc, 5, 2);
      $n_dia=substr($data_nasc, 8, 2);
      return ($h_mes>$n_mes || ($n_mes==$h_mes && $h_dia>=$n_dia) ) ? $h_ano - $n_ano : $h_ano - $n_ano - 1;
}


//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
//----------------  Dados Recebidos  ---------------->


//echo "Login-->".$id_login."<br>";
//echo "Pessoa---->".$pes_codigo."<br>";

//--------------  Captação dos Dados  --------------->

//$titulo="Etiqueta Paciente";    //       NOME DO RELATÓRIO


$sql = "SELECT usu_codigo, usu_nome, " .  //       Pega Usuario
       "to_char(usu_datanasc, 'dd/mm/yyyy') as usu_datanasc, usu_pai, usu_end_rua, usu_end_cidade,
                usu_end_cep, usu_mae, to_char(usu_datanasc, 'yyyy/mm/dd') as usu_datanasc2,
                calcula_idade(usu_codigo) as idade, usu_prontuario" .
       "  FROM usuario " .
       " WHERE usuario.usu_codigo = $pes_codigo";
$query=pg_query($sql);
while($rowPaciente=pg_fetch_array($query)) {
      $codusuario=$rowPaciente[0];
      $nomepaciente=$rowPaciente[1];
      $usu_datanasc=$rowPaciente[2];
      $usu_pai=$rowPaciente[3];
      $usu_end_rua=$rowPaciente[4];
      $usu_end_cidade=$rowPaciente[5];
      $usu_end_cep=$rowPaciente[6];
      $usu_mae=$rowPaciente[7];
      $idade=$rowPaciente[9];
      $prontuario=$rowPaciente[10];
}
//---> BOTÃO DE impressão

/* echo "<table  cellspacing=3 cellpadding=0 border=0  height=20 topmargin=0 leftmargin=0>
       <tr>
        <td width=100%><font size=1><b>".strtoupper($titulo)."</b></font></td>
        <td><a href='#' OnClick='imprimir()'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/print_on.jpg border=0></a></td>
       </tr>
       </table><br>"; */

echo "<table  width=90% cellspacing=3 cellpadding=0 border=0  height=20 topmargin=0  leftmargin=0>";
echo "<tr>
      <td></td><td></td><font size=1><td  width=300 align = right>Código Prontuário: $prontuario </td>
      <td  width=20><td>
      <td></td><td></td><font size=1><td  width=300 align = right>Código Prontuario: $prontuario </td>
      <tr>
      <td  width=100 align = right>Paciente:</td>
      <td  width=350 colspan=2 align = left>$nomepaciente </td>
      <td  width=20><td>
      <td  width=150 align = right>Paciente:</td>
      <td  width=350 colspan=2 align = left>$nomepaciente </td>
      </tr>
      <tr>
      <td  width=100 align = right>Mãe:</td>
      <td  width=350 colspan=2 align = left>$usu_mae </td>
      <td  width=180><td>
      <td  width=150 align = right>Mãe:</td>
      <td  width=350 colspan=2 align = left>$usu_mae </td>
      </tr>
      <td  width=100 align = right>Pai:</td>
      <td  width=350 colspan=2 align = left>$usu_pai </td>
      <td  width=20><td>
      <td  width=150 align = right>Pai:</td>
      <td  width=350 colspan=2 align = left>$usu_pai </td>
      </tr>
      <td  width=100 align = right>Endereço:</td>
      <td  width=350 colspan=2 align = left>$usu_end_rua </td>
      <td  width=20><td>
      <td  width=150 align = right>Endereço:</td>
      <td  width=350 colspan=2 align = left>$usu_end_rua </td>
      </tr>
      <tr></tr>
      <td  width=100 align = right>Nascimento:</td>
      <td  width=350 colspan=2 align = left>$usu_datanasc </td>
      <td  width=20><td>
      <td  width=150 align = right>Nascimento:</td>
      <td  width=350 colspan=2 align = left>$usu_datanasc </td>
      </tr>
       </table><br><br>";

echo "<table  width=90% cellspacing=3 cellpadding=0 border=0  height=20 topmargin=0  leftmargin=0>";
echo "<tr>
      <td></td><td></td><font size=1><td  width=300 align = right>Código Prontuário: $prontuario </td>
      <td  width=20><td>
      <td></td><td></td><font size=1><td  width=500 align = right>&nbsp;</td>
      <tr>
      <td  width=100 align = right>Paciente:</td>
      <td  width=350 colspan=2 align = left>$nomepaciente </td>
      <td  width=20><td>
      <td  width=150 align = right>&nbsp;</td>
      <td  width=350 colspan=2 align = left>&nbsp;</td>
      </tr>
      <tr>
      <td  width=100 align = right>Mãe:</td>
      <td  width=350 colspan=2 align = left>$usu_mae </td>
      <td  width=180><td>
      <td  width=150 align = right>&nbsp;</td>
      <td  width=350 colspan=2 align = left>&nbsp;</td>
      </tr>
      <td  width=100 align = right>Pai:</td>
      <td  width=350 colspan=2 align = left>$usu_pai </td>
      <td  width=20><td>
      <td  width=150 align = right>&nbsp;</td>
      <td  width=350 colspan=2 align = left>&nbsp; </td>
      </tr>
      <td  width=100 align = right>Endereço:</td>
      <td  width=350 colspan=2 align = left>$usu_end_rua </td>
      <td  width=20><td>
      <td  width=150 align = right>&nbsp;</td>
      <td  width=350 colspan=2 align = left>&nbsp; </td>
      </tr>
      <tr></tr>
      <td  width=100 align = right>Nascimento:</td>
      <td  width=350 colspan=2 align = left>$usu_datanasc </td>
      <td  width=20><td>
      <td  width=150 align = right>&nbsp;</td>
      <td  width=350 colspan=2 align = left>&nbsp;</td>
      </tr>
       </table><br>";


?>
</body>
</html>
