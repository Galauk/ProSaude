<?
	$folder = explode("/", $_SERVER['REQUEST_URI']);
	$raiz = $_SERVER['DOCUMENT_ROOT']."/$folder[1]";	
	echo "<input type='hidden' name='caminhoraiz' id='caminhoraiz' value='$raiz'>";
?>