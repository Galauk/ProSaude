<?php
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";

//cabecario( $hotkey = false );
cabecario( $hotkey = true );

//verauth($id_login);

// busca
	echo "
	<form action='#' method='get' onsubmit='return busca_ibge( \"busca_cidade5\")'/>
	<fieldset>
	<legend>Opcoes de Buscar</legend>
	<table>
	<tr>
		<input type='hidden' name='acao' value='busca'>
		<input type='hidden' name='id_login' value='$id_login'>
		<td width=30>Buscar:</td>
		<td width=80><input type='text' name='palavra_chave' id='palavra_chave' class='box' onChange=\"this.value=this.value.toUpperCase();\" />
		<td width=40>
			<select name='busca_cidade' id='busca_cidade' class='box'>
				<option value='1'".( $busca_cidade==1 ? ' selected' : '' ).">C&oacute;digo IBGE</option>
				<option value='2'".( $busca_cidade==2 ? ' selected' : '' ).">Cidade</option>
			</select>
		</td>
		 
		</td>
		<td><!-- <input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg' onClick='return busca_ibge_nasc(\"$id_login\");'>-->
		 <input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg'></td>
	</tr>
	</table>
	</fieldset>
	</form>";

/** listagem/busca */
if( empty($acao) )
{	
	//-> Subistituindo o + por porcentagem na busca
	$str = str_replace("+","%%",$palavra_chave);
	
	// arrumando sql
	$resp 	= '';
	$max 	= 15;
	$where1 = empty($acao) ? '' : "WHERE cid_nome ILIKE '%$str%' ";
	
	// se NAO for busca, limitar 
	$sql_f 	= empty($acao) ? 'LIMIT '.$max : '';

	if (empty($where1)){
		$stmt = "SELECT  cid_nome, cid_codigo_ibge, uf_sigla FROM cidade ORDER BY 2 ". $sql_f ;
	}else{	
		$stmt = "SELECT  cid_nome, cid_codigo_ibge, uf_sigla FROM cidade ". $where1 ;
		//." ORDER BY 2 ". $sql_f ;
	}
	
	$qry = db_query($stmt);
	$num = pg_num_rows($qry);
	
	if( $acao == 'busca' && ! empty($palavra_chave) )	
	{
		if( $num == 0 ) 	{ $resp = "Nenhum Registro encontrado com \"$palavra_chave\""; }
		elseif( $num == 1 ) { $resp = "Encontrado <b>$num</b> Registro com \"$palavra_chave\""; }
		elseif( $num > 1) 	{ $resp = "Encontrados <b>$num</b> Registros com \"$palavra_chave\""; }
	} else
		//$resp = "Listando os $max primeiros Medicos";
		$resp = "Listando as $max primeiras Cidades";
	
	print "
	<fieldset>
	<legend>$resp</legend>
	<table class='lista'>
	<tr bgcolor='#ffffff'>
		<th width='20%'>C&oacute;digo IBGE</th>
		<th width='40%'>Cidade</th>
		<th width='20%'>UF</th>
		<th width='20%'>&nbsp;</th>
	</tr>
	";
	while($row=pg_fetch_array($qry))
	{
       echo "
       	  <tr>
       	  	<td>$row[1]</td>
	       	<td>$row[0]</td>
	       	<td>$row[2]</td>
	        <td class='c'>
				<a href='javascript:;'
				  onclick=\"add_cod_ibge_resid('$row[1]', unescape('".addslashes($row[0])."'),'$row[2]');\">
				  <img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_on.jpg' border='0' alt='Selecionar'></a>
			</td>
	     </tr>";
     }
	echo "
	</table>
	</fieldset>";

}elseif($acao == 'busca'){

		//var_dump($_GET);
		$str = str_replace("+","%%",$palavra_chave);
				
			if ($valor_busca==1){
				$campo = 'cid_codigo_ibge';
			}else if($valor_busca==2){
				$campo = 'cid_nome';
			}


		$where 	= empty($acao) ? '' : sprintf("WHERE $campo ILIKE '%%%s%%'", $str);
	
		$stmt = 'SELECT  cid_nome, cid_codigo_ibge, uf_sigla FROM cidade '. $where;
		//$stmt .= ' ORDER BY to_ascii( upper( cid_nome ) ) ';
		$th = 'C&oacute;digo IBGE';
		$th1 = 'Cidade';
		$th2 = 'UF';

		$qry = db_query($stmt);
		$num = pg_num_rows($qry);
		
				if( $num == 0 ) 	{ $resp = "Nenhum Registro encontrado com \"$palavra_chave\""; }
				elseif( $num == 1 ) { $resp = "Encontrado <b>$num</b> Registro com \"$palavra_chave\""; }
				elseif( $num > 1) 	{ $resp = "Encontrados <b>$num</b> Registros com \"$palavra_chave\""; }
		
		echo "	<fieldset>
					<legend>"; echo $resp; echo"</legend>";		
						
			echo "<form name='processo' method='get' action='$PHP_SELF'>\n"; 
			echo "	<table width='100%' border='0' cellspacing='0' cellpadding='0' class='lista'>";
			echo " 	  <tr bgcolor='#FFFFFF'>
						<th width='20%'>".$th."</th>
						<th width='40%'>".$th1."</th>
						<th width='20%'>".$th2."</th>
						<th width='20%'>&nbsp;</th>
						</tr>";
	
				while (	$res = pg_fetch_array($qry) ) {
					
			$onclick = "onclick=\"add_cod_ibge_resid('$res[1]', unescape('".addslashes($res[0])."'),'$res[2]');\" ";
					
					echo "\n<tr>";
					echo "\n	<td width='20%'>$res[1]</td>";
					echo "\n	<td width='40%'>$res[0]</td>";				
					echo "\n	<td width='20%'>$res[2]</td>";	
					echo "\n	<td width='20%' class='c'><a href='javascript:;' ".$onclick." >";
					echo "\n	<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_on.jpg' border=0></a></td>";
					echo "\n</tr>";
				
				}
			echo "	</table>";
			echo "</form>
			
				</fieldset><br />";


		}


?>
