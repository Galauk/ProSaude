<h2>Documentação do Sotware de Saude - GPS</h2>

<table width=100% cellspacing=1 cellpadding=4 border=0>
<tr bgcolor=F9F9E8>
 <td><a href=<?=$PHP_SELF?>?view=see&acao=apresentacao.php>Apresentação</a></td>
 <td><a href=<?=$PHP_SELF?>?view=see&acao=composicao.php>Composição</a></td>
 <td><a href=<?=$PHP_SELF?>?view=see&acao=funcionamento.php>Funcionamento</a></td>
 <td><a href=<?=$PHP_SELF?>?view=see&acao=programacao.php>Programando</a></td>
</tr>
</table><br><br>
<?
if($view=="see") {
    echo "<center><iframe name=frameprincipal src=$acao frameborder=10 marginheight=0 marginwidth=0 scrolling=yes width=780 height=400></iframe></center>";
}
?>
<h2>Documentação de alterações no sistema</h2>
<?
$dir = "./";
if (is_dir($dir)) {
   if ($dh = opendir($dir)) {
    echo "<table width=100% cellspacing=0 cellpadding=4 border=1>";
       while (($file = readdir($dh)) !== false) {
	$bgcolor=($bgcolor=="E1E1E1")?"E1E1E1":"EEEEEE";
	if(($file!="." AND $file!=".." AND $file!="index.php" AND $file!="apresentacao.php" AND $file!="composicao.php" AND $file!="funcionamento.php" AND $file!="programacao.php")) {
           echo "<tr bgcolor=EEEEEE>
	          <td><a href=$PHP_SELF?files=$file&view=file>".str_replace(".php","",$file)."<br></a></td>
	         </tr>";
	}
    echo "</table>";
       }
       closedir($dh);
   }
}

if($view=="file") {
    echo "<iframe name=frameprincipal src=$files frameborder=10 marginheight=0 marginwidth=0 scrolling=yes width=780 height=400></iframe>";
}

?>
