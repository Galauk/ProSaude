<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>
<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<body>
<?
	$aplica_dia = $_POST['aplica_dia'];
	$aplica_mes = $_POST['aplica_mes'];
	$aplica_ano = $_POST['aplica_ano'];
	$id = $_POST['id'];
	$unidade = $_POST['unidade'];
	$data = $aplica_dia."/".$aplica_mes."/".$aplica_ano;
	echo "<script>
			executaAcao('$id', '$data', '$unidade');
			window.close();
	      </script>
	";
?>
</body>
</html>