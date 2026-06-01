<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
$limite=8;
switch($_GET['acao']){
	case 'buscar':

		$sql="SELECT rua_nome,rua_codigo
			  FROM rua
			  WHERE upper(rua_nome) like upper(to_ascii('%{$_GET['palavra']}%'))";
		
		$exec_sql = pg_query($sql);
		if(!$exec_sql){
			echo "Erro ao executar a query: $sql";
		}
		
		$linha = pg_num_rows($exec_sql);
		$pagina = ceil($linha/$limite); 
		
		$sql="SELECT rua_nome,rua_codigo
			  FROM rua
			  WHERE upper(rua_nome) like upper(to_ascii('%{$_GET['palavra']}%'))
              ORDER BY rua_nome
			  LIMIT $limite OFFSET {$_GET['deslocamento']}";

		$exec_sql=pg_query($sql);		
		if(!$exec_sql){
			echo "Erro ao executar a query: $sql";
		}
		break;
		
	case 'excluir':

		$sql="DELETE FROM rua
		      WHERE rua_codigo={$_GET['idi_codigo']}";
		$exec_sql=pg_query($sql);
		if(!$exec_sql){
			echo "Erro ao executar a query: $sql";
		}		
		
		if(!$exec_sql){
			alerta('ESTE CADASTRO N&Atilde;O PODE SER EXCLU&Iacute;DO  POR ESTAR SENDO USANDO EM OUTRO REGISTRO','erro','imgs/erro_on.gif');
		}
		else{
			alerta( "REGISTRO EXCLU&Iacute;DO! COM SUCESSO!",'ok','imgs/alerta.png');
		} 
		break;
}

echo '
<html>
<head>
 <meta http-equiv="Content-Type" content="text/html; charset=iso-UTF-8" /> 
 <link href="estilo_janela.css" rel="stylesheet" type="text/css"> 
</head>
<body>
 <div id="pesquisa"> ';
echo "<table border=\"0\" class=\"tb_pesquisa\" width=\"40%\">";
echo "  <tr>";
echo "      <td colspan=\"3\" width=\"3%\"id=\"local\"><label class=\"legd\">Localizar</label></td>";
echo "      <td><input class=\"box\" type=\"text\" size=\"85\" name=\"localiza\" OnChange=\"pesqCad(this.value,'rua_popup_jean.php','localiza_rua')\" id=\"txt_busca\"></td>";
echo "  </tr>";
echo "  <tr style='background:#fff;'>";
echo "      <td colspan=\"3\"><label class=\"legd\">Rua</label></td>";
echo "      <td>&nbsp;</td>";
echo "  </tr>";
echo "</table>";
echo "<div id=busca>";
echo "<table border=\"0\" cellspacing=\"0\"  width=\"60%\" class=\"lista\">";
 
	while($linha = pg_fetch_array($exec_sql)){
		$cont += 1;
    
	    if($cont%2==0){
	      $class="tb_list1";
	    }
		else{
	      $class="tb_list2";
	    }
        echo "<tr >";
        echo "<td class=\"$class\" width=\"80%\"><span id=\"lg\">{$linha['rua_nome']}</span></td>";
       echo "<td class=\"$class\"><input type=\"image\" name=\"btnAlterar\" value=\"Alterar\" title=\"Alterar Registro\" src=\"imgs/selecionar_on.jpg\" 
                onclick=\"insere_rua('{$linha['rua_codigo']}','".( htmlentities($linha[rua_nome],ENT_QUOTES) )."')\"></td>";
     /*   echo "  <td class=\"$class\"><input type=\"image\" name=\"btnExcluir\" value=\"Excluir\" title=\"Excluir Registro\" src=\"imgs/apagar_on.jpg\" onclick=\"deleta_cadIdioma('{$linha['idi_codigo']}','{$linha['idi_descricao']}')\"></td>";*/
        echo "</tr>";
    }
        echo "</table>";
        echo "<div id=\"navegacao\">";

       if($atual <= 1) {
			$link_back = '<img src="imgs/back_off.gif" border="0"  \>';
        }
		else{
			$link_back ="<a href=\"javascript:voltarPagina('{$_GET['palavra']}',{$_GET['atual']},{$_GET['deslocamento']},$limite,'rua_popup_jean.php','localiza_rua')\" >".'<img src="imgs/back.gif" border="0"  \>'."</a>";
        }
       if($atual >= $pagina) {
           $link_next = " <img src=\"imgs/next_off.gif\" border=\"0\" \> ";
       }
	   else{
            $link_next = "<a href=\"javascript:irPagina('{$_GET['palavra']}',{$_GET['atual']},{$_GET['deslocamento']},$limite,'rua_popup_jean.php','localiza_rua')\" >". '<img src="imgs/next.gif" border="0"   \>'. "</a>";
       }
        
		echo "$link_back $atual $link_next";

        echo "</div>" ;

echo ' 

</body>
</html>';

?>
