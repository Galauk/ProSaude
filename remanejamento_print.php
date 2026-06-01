<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<?

//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	verauth($id_login);
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
//------------------------------------------------------------------>


//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
	reglog($id_login,"Imprimindo uma TRANSFERENCIA");
//------------------------------------------------------------------>
?>
<script language="javascript">
	window.print();
</script>
<?
$sql = "SELECT a.set_entrada AS codigo_entrada, a.set_saida AS codigo_saida, b.set_nome AS setor_entrada,
		b.set_nome AS setor_saida, to_char(a.mov_data,'dd/mm/yyyy') AS data_saida, a.mov_codigo AS numero_transf,
		a.mov_observacao AS observacao
		FROM movimento a, setor b
		WHERE a.mov_codigo = $mov_codigo
		AND b.set_codigo = a.set_entrada
		AND b.set_codigo = a.set_saida";
$query = db_query($sql);
$row = pg_fetch_array($query);
?>
<body>
<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2">
  <tr>
    <td colspan="2" align="center"><strong><u>Transferencia de materiais</u><br>
      <br>
    </strong></td>
  </tr>
  <tr>
    <td width="21%" valign="top"><strong>Centro Est. Entrada </strong></td>
    <td width="79%"><?=$row[setor_entrada]?></td>
  </tr>
  <tr>
    <td valign="top"><strong>Centro Est. Sa&iacute;da </strong></td>
    <td><?=$row[setor_saida]?></td>
  </tr>
  <tr>
    <td valign="top"><strong>Data de Sa&iacute;da </strong></td>
    <td><?=$row[data_saida]?></td>
  </tr>
  <tr>
    <td valign="top"><strong>N&uacute;mero da Transfer&ecirc;ncia </strong></td>
    <td><?=$row[numero_transf]?></td>
  </tr>
  <tr>
    <td valign="top"><strong>Observa&ccedil;&atilde;o</strong></td>
    <td><?=$row[observacao]?></td>
  </tr>
</table>
</body>
</html>
