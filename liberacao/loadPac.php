<?php
	session_start();
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	if(isset($_GET["cod_lib"])){
		$cod_lib = $_GET["cod_lib"];
		$sql = "select a.libex_codigo,
					   TRANSLATE(d.proc_nome, 'ZZZ-', '') as proc_nome,
        			   to_char (a.libex_data_cad ,'dd/mm/yyyy') as libex_data_cad,
					   a.usu_codigo,
					   b.usu_nome,
					   b.usu_datanasc,
					   b.usu_mae,
					   b.usu_end_cidade 
				  from liberacao_exame a 
				  join usuario b 
				    on a.usu_codigo = b.usu_codigo 
				  join liberacao_exame_lista c
				    on a.libex_codigo = c.libex_codigo
				  join procedimento d
				    on d.proc_codigo = c.proc_codigo
				 where a.libex_codigo = '$cod_lib'
				   and c.libexl_status = 'A'";
		$query = pg_query($sql);
		$linha = pg_fetch_array($query);
	}else if(isset($_GET["data_lib"])){
		$data_lib = $_GET["data_lib"];
		$sql = "select a.libex_codigo,
					   d.proc_nome,
					   a.libex_data_cad,
					   a.usu_codigo,
					   b.usu_nome,
					   b.usu_datanasc,
					   b.usu_mae,
					   b.usu_end_cidade 
				  from liberacao_exame a 
				  join usuario b 
				    on a.usu_codigo = b.usu_codigo 
				  join liberacao_exame_lista c
				    on a.libex_codigo = c.libex_codigo
				  join procedimento d
				    on d.proc_codigo = c.proc_codigo
				 where a.libex_codigo = '$data_lib'";
		$query = pg_query($sql);
		$linha = pg_fetch_array($query);
	}else if(isset($_GET["pac_nome"])){
		$cod_pac = $_GET["pac_nome"];
		$sql = "select a.libex_codigo,
					d.proc_nome,
					a.libex_data_cad,
					a.usu_codigo,
					b.usu_nome,
					b.usu_datanasc,
					b.usu_mae,
					b.usu_end_cidade 
				from liberacao_exame a 
				join usuario b 
				on a.usu_codigo = b.usu_codigo 
				join liberacao_exame_lista c
				on a.libex_codigo = c.libex_codigo
				join procedimento d
				on d.proc_codigo = c.proc_codigo
				where a.libex_codigo = '$pac_nome'";
	}
	$query = pg_query($sql);
	echo "<table class=lista>";
	$i = 0;
		echo "<tr>
			<th>Nome Procedimento</th>
			<th>C&oacute;digo do usu&aacute;rio</th>
			<th>Nome</th>
			<th>Data de Cadastro Libera&ccedil;&atilde;o</th>
		 </tr>";
	while ($linha = pg_fetch_array($query)){
	echo "<input type='hidden' name='usu_codigo' value='{$linha['usu_codigo']}'>
		<input type='hidden' name='usu_nome' value='{$linha['usu_nome']}'>
		<input type='hidden' name='libex_codigo' value='{$linha['libex_codigo']}'>
		<input type='hidden' name='libex_data_cad' value='{$linha['libex_data_cad']}'>
		<input type='hidden' name='usu_datanasc' value='{$linha['usu_datanasc']}'>
		<input type='hidden' name='usu_end_cidade' value='{$linha['usu_end_cidade']}'>
		<input type='hidden' name='usu_mae' value='{$linha['usu_mae']}>";
		$parImpar = (($i % 2) != 0) ? "par":"impar";
		echo "<input type='hidden' name='proc_nome[]' value='{$linha['proc_nome']}'>
			 <tr>
				<td>{$linha['proc_nome']}</td>
				<td>{$linha['usu_codigo']}</td>
				<td>{$linha['usu_nome']}</td>
				<td>{$linha['libex_data_cad']}</td>
			 </tr>";
		$i++;
		$usu_codigo = $linha['usu_codigo'];
	}
	echo "</table>";
	if (pg_num_rows($query) > 0){
		echo "<a href='#' onClick=\"selecionaData();\"><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/agendar_on.jpg'></a>";
	}else{
		echo "N&atilde;o existem procedimentos n&atilde;o agendados nesta libera&ccedil;&atilde;o";
	}
	
?>