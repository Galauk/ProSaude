<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Listagem de tabelas inconsistentes.</title>
<link rel="stylesheet" type="text/css" href="index.css" />
</head>
<form name="conexao" method="post" action="alinha_bancos.php" target="_blank" />
	<table width="630" border="1">
	  <tr>
		<td colspan="5" height="30" class="center"><strong>SISTEMA DE ALINHAMENTO DE BASES SOCIAL</strong></td>
	  </tr>
	  <tr>
		<td colspan="5" height="30"><strong>INFORME OS DADOS DA BASE CORRETA E INCONSISTENTE, PARA REALIZAR A VERIFICA&Ccedil;&Atilde;O.</strong></td>
	  </tr>
	  <tr>
		<td colspan="2" height="30"><strong>CONEX&Atilde;O BASE CORRETA</strong></td>
		<td width="150" rowspan="6">&nbsp;</td>
		<td colspan="2" height="30"><strong>CONEX&Atilde;O BASE INCONSISTENTE</strong></td>
	  </tr>
	  <tr>
		<td width="80" class="left">HOST:</td>
		<td width="202"><input type="text" name="host_correto" size="30" /></td>
		<td width="87" class="left">HOST:</td>
		<td width="170"><input type="text" name="host_incosistente" size="35" /></td>
	  </tr>
	  <tr>
		<td class="left">PORTA:</td>
		<td><input type="text" name="porta_correto" size="30" /></td>
		<td class="left">PORTA:</td>
		<td><input type="text" name="porta_incosistente" size="35" /></td>
	  </tr>
	  <tr>
		<td class="left">BANCO:</td>
		<td><input type="text" name="banco_correto" size="30" /></td>
		<td class="left">BANCO:</td>
		<td><input type="text" name="banco_incosistente" size="35" /></td>
	  </tr>
	  <tr>
		<td class="left">USU&Aacute;RIO:</td>
		<td><input type="text" name="usuario_correto" size="30" /></td>
		<td class="left">USU&Aacute;RIO:</td>
		<td><input type="text" name="usuario_incosistente" size="35" /></td>
	  </tr>
	  <tr>
		<td class="left">SENHA:</td>
		<td><input type="password" name="senha_correto" size="30" /></td>
		<td class="left">SENHA:</td>
		<td><input type="password" name="senha_incosistente" size="35" /></td>
	  </tr>
	  <tr>
		<td colspan="5"  height="40" class="center"><input name="" type="submit" value="Alinhar Bancos" /></td>
	  </tr>
	</table>
</form>
</body>
</html>
