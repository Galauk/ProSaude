<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<script language="javascript" src="../funcoes.js"></script>
       <script> 
function Hora(obj)
{
    dtH = obj.value;
    dtH=dtH.replace(/\D/g,"")  //permite digitar apenas números
    dtH=dtH.replace(/[0-9]{5}/,"")   //limita pra máximo 11:11
    dtH=dtH.replace(/(\d{2})(\d{1})/,"$1: $2")
    obj.value = dtH;
}

       </script>
</head>

<body>
<?
session_start();
echo "<form action='exa_listadt_liberacaoagendamento.php' method='post'> ";
require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."funcao.calendario.php";
$codigoLiberacao = $_GET['cod_liberacao'];
$usu_codigo = $_GET['usu_codigo'];
$mesAno =date(m).'/'.date(Y);
$codigoLiberacao = $_GET['cod_liberacao'];
$toalAgendados = 0;
$sql = "select * from liberacao_exame_lista where libex_codigo = $codigoLiberacao";
$consultaDadosLiberacao = pg_query($sql);
$diaMesAno =date(d).'/'.date(m).'/'.date(Y);
/*
APARTIR DAQUI ELE ESTA PEGANDO VALORES PRO_COD E MED_COD JOGANDO EM VARIAVEIS PARA QUE FAÇA O SELECT DA QUANTIDADE DE AGENDADOS
E DO VALOR DOS EXAMES... FAZER UM WHILE PARA O SELECT DAS VARIAVEIS E UM WHILE PARA . O SELECT DA QUANTIDADE DOS AGENDADOS.
*/

/*echo $resultado;
echo "<br>".$quotaMes;*/

echo "
	<table>
		<tr>
			<td>
				<input type='hidden' id='codigoLib' name='codigoLib' value='$codigoLiberacao'>
				Data Agendamento:";
				campoData("lib_dia_agendamento", "lib_mes_agendamento", "lib_ano_agendamento");
		echo "</td>
		</tr>
		<tr>
			  <td>
			  		Horario da Coleta: <input type='text' name='hora' maxlength='6' id='hora' value='$horario' onKeyUp = 'Hora(this);'>
			  </td>
		</tr>
		<tr>
			<td>
				<input type='image' alt='Enviar' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/enviar_dados_on.jpg'>
			</td>
		</tr>
	</table>
</form>
";

?>
</body>
</html>