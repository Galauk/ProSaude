<?php
/**
Busca de Médicos Solicitantes de AIH e de Pacientes
	medico.prestador_servico = 'S'
*/

/**
@brief  Busca de Médicos Solicitantes de AIH
*/
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";

/** verifica se tem aih tem 15 dias */
if( $acao == 'verifica' && ! empty($codigo) )
{
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

	$campo = ( $apac == 'S' ? 'pac_codigo' : 'aih_codigo' );
	$stmt = "SELECT COUNT(aih_codigo)
			   FROM aih 
			  WHERE $campo = $codigo 
			    AND CURRENT_DATE - aih_dt_cadastro < 15";
	$val = intval( db_get($stmt) );
	if( $val > 0 )
		print 'NOK';
	else
		print 'OK';
	die;
}

cabecario(); 


if ($acao == 'solicitante'){


		echo "
			<fieldset>
				<legend>Opções</legend>				
					<form method='get' action='#' onsubmit='return busca_lab( \"buscasolicitante\" )'>
						<input type=hidden name=id_login value=$id_login>
						<table width=100% align=left cellspacing=3 cellpadding=0 border=0>
						<tr>
							<td width=30>Buscar:</td>
							<td width=120><input type='text' name='palavra_chave' id='palavra_chave' class='box' onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
							<td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg></td>
						</tr>
						</table>
					</form>
			</fieldset><br />
		";
	
		$sql = "select * from medico where prestador_servico='H' limit 15";
		$row = pg_query($sql);
	
		echo "<form name='solicitante' method='get' action='$PHP_SELF'>\n"; 
		echo "	<table width='100%' border='0' cellspacing='0' cellpadding='0' class='lista'>";
		echo " 	  <tr bgcolor='#FFFFFF'>
					<th>Nome do Médico</th>
					<th>CNES</th>
					<th>&nbsp;</th>
				  </tr>";

		while (	$res = pg_fetch_array($row) ) {
		
			echo "<tr>";
			echo "	<td width='60%'>$res[med_nome]</td>";
			echo "	<td width='20%'>$res[med_cnes]</td>";
			/*echo "	<td width='20%'><a href='javascript:;' onclick=\"add_conteudo_popup('med_codigo_solicitante', 'med_codigo_solicitante_h', '$res[med_nome]', '$res[med_codigo]' )\" >";*/
			echo "	<td width='20%'><a href='javascript:;' onclick=\"add_conteudo_popup_medsoli('$res[med_nome]', '$res[med_codigo]', '$res[med_cnes]' )\" >";
			echo "	<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_on.jpg' border=0></td></a>";
			echo "</tr>";
		
		}
		echo "	</table>";
		echo "</form>";


}else if($acao == 'executante'){

		echo "
			<fieldset>
				<legend>Opções</legend>				
						<form method='get' action='#' onsubmit='return busca_lab( \"buscaexecutante\" )'>
							<input type=hidden name=id_login value=$id_login>
							<table width=100% align=left cellspacing=3 cellpadding=0 border=0>
								<tr>
									<td width=30>Buscar:</td>
									<td width=120><input type='text' name='palavra_chave' id='palavra_chave' class='box' onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
									<td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg></td>
								</tr>
							</table>
						</form>
			</fieldset><br />
		";
	
		$sql = "select * from medico where prestador_servico='H' limit 15";
		$row = pg_query($sql);
	
		echo "<form name='executante' method='get' action='$PHP_SELF'>\n"; 
		echo "	<table width='100%' border='0' cellspacing='0' cellpadding='0' class='lista'>";
		echo " 	  <tr bgcolor='#FFFFFF'>
					<th>Nome do Médico</th>
					<th>&nbsp;</th>
				  </tr>";

		while (	$res = pg_fetch_array($row) ) {
		
			echo "<tr>";
			echo "	<td width='80%'>$res[med_nome]</td>";
			echo "	<td width='20%'><a href='javascript:;' onclick=\"add_conteudo_popup('med_codigo_executante', 'med_codigo_executante_h', '$res[med_nome]', '$res[med_codigo]' )\" >";
			echo "	<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_on.jpg' border=0></a></td>";
			echo "</tr>";
		
		}
		echo "	</table>";
		echo "</form>";
	
}else if($acao == 'paciente'){
	
		echo "
			<fieldset>
				<legend>Opções</legend>				
						<form method='get' action='#' onsubmit='return busca_lab( \"buscapaciente\" )'>
							<input type=hidden name=id_login value=$id_login>
							<table width=100% align=left cellspacing=3 cellpadding=0 border=0>
								<tr>
									<td width='120'>
											<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg' alt='Adicionar' onclick='form_paci(\"$id_login\",0)' 
												style='cursor:pointer;' />
									</td>
									<td width=30>Buscar:</td>
									<td width=120><input type='text' name='palavra_chave' id='palavra_chave' class='box' onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
									<td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg></td>
								</tr>
							</table>
						</form>
			</fieldset><br />
		";

	$sql = "(SELECT usu_codigo, usu_nome, usu_sexo, to_char (usu_datanasc, 'dd/mm/YYYY' ) as usu_datanasc, ". 
		"usu_cpf, usu_rg, usu_mae, usu_end_rua, usu_end_nr, usu_end_bairro, usu_end_cep, usu_end_cidade, usu_fone ".
		" ,'N' as aih, usu_prontuario ".
		"FROM usuario) ".
		"UNION ".
		"(SELECT pac_codigo, pac_nome, pac_sexo, to_char (pac_dt_nasc, 'dd/mm/YYYY' ) as usu_datanasc, ". 
		"pac_cpf, pac_rg, pac_mae_responsavel_nome, pac_end_rua, pac_end_nr, pac_end_bairro, pac_end_cep, ".
		"pac_end_cidade, pac_telefone ,'S' as aih, pac_prontuario ".		
		"FROM aih_paciente) ".
		"ORDER BY 2 limit 15";
		//echo $sql;
		$row = db_query($sql);
	
		echo "<form name='paciente' method='get' action='$PHP_SELF'>\n"; 
		echo "	<table width='100%' border='0' cellspacing='0' cellpadding='0' class='lista'>";
		echo "<tr bgcolor='#FFFFFF'>
					<th>Nome do Paciente</th>
					<th>Nome da Mae</th>
					<th>Data de Nascimento</th>
					<th>Prontuario</th>
					<th>CPF</th>
					<th>AIH</th>
					<th width='240'>&nbsp;</th>
				  </tr>";

		while (	$res = pg_fetch_array($row) ) {
		
			echo "\n<tr>";
			echo "\n<td>$res[usu_nome]</td>";
			echo "\n<td>$res[usu_mae]</td>";
			echo "\n<td align='center'>$res[usu_datanasc]</td>";
			echo "\n<td align='center'>$res[usu_prontuario]</td>";
			echo "\n<td align='center'>$res[usu_cpf]</td>";
			echo "\n<td class='c' align='center'>$res[aih]</td>";
			echo "\n<td class='c' align='center'><a href='javascript:;' onclick=\"add_conteudo_popup_paciente('$res[usu_nome]', '$res[usu_codigo]', '$res[usu_rg]', '$res[usu_cpf]', '$res[usu_datanasc]', '$res[usu_sexo]', '$res[usu_mae]', '$res[usu_fone]', '$res[usu_end_rua]', '$res[usu_end_nr]', '$res[usu_end_bairro]', '$res[usu_end_cidade]', '$res[usu_end_cep]','$res[aih]' )\" >";
			echo "\n<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_on.jpg' border='0' alt='Selecionar'></a>";
			echo ( $res['aih'] == 'S' ?
					" <img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg' border='0' alt='Editar' style='cursor:pointer;' 
						onclick=\"form_paci('$id_login','$res[0]')\" />
					 <img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg' border='0' alt='Apagar' style='cursor:pointer;'
						onclick=\"apagar_paci('$id_login','$res[0]')\" />" :
					'' );
			echo "\n</td></tr>";
		
		}
		echo "	</table>";
		echo "</form>";
	
}else if( $acao=='prof_soli' ){

		echo "
			<fieldset>
				<legend>Opções</legend>				
						<form method='get' action='#' onsubmit='return busca_lab( \"profissionalsolicitante\" )'>
							<input type=hidden name=id_login value=$id_login>
							<table width=100% align=left cellspacing=3 cellpadding=0 border=0>
								<tr>
									<td width=30>Buscar:</td>
									<td width=120><input type='text' name='palavra_chave' id='palavra_chave' class='box' onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
									<td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg></td>
								</tr>
							</table>
						</form>
			</fieldset><br />
		";
	
		$sql = "select * from medico where prestador_servico='N' limit 15";
		$row = pg_query($sql);
	
		echo "<form name='prof_soli' method='get' action='$PHP_SELF'>\n"; 
		echo "	<table width='100%' border='0' cellspacing='0' cellpadding='0' class='lista'>";
		echo " 	  <tr bgcolor='#FFFFFF'>
					<th>Nome do Profissional</th>
					<th>&nbsp;</th>
				  </tr>";

		while (	$res = pg_fetch_array($row) ) {
		
			echo "<tr>";
			echo "	<td width='80%'>$res[med_nome]</td>";
			echo "	<td width='20%'><a href='javascript:;' onclick=\"add_conteudo_popup('med_solicitante_proc', 'med_solicitante_proc_h', '$res[med_nome]', '$res[med_codigo]' )\" >";
			echo "	<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_on.jpg' border=0></a></td>";
			echo "</tr>";
		
		}
		echo "	</table>";
		echo "</form>";

}else if($acao=='prof_auto'){

		echo "
			<fieldset>
				<legend>Opções</legend>				
						<form method='get' action='#' onsubmit='return busca_lab( \"profissionalautorizador\" )'>
							<input type=hidden name=id_login value=$id_login>
							<table width=100% align=left cellspacing=3 cellpadding=0 border=0>
								<tr>
									<td width=30>Buscar:</td>
									<td width=120><input type='text' name='palavra_chave' id='palavra_chave' class='box' onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
									<td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg></td>
								</tr>
							</table>
						</form>
			</fieldset><br />
		";
	
		$sql = "select * from medico where prestador_servico='N' limit 15";
		$row = pg_query($sql);
	
		echo "<form name='prof_auto' method='get' action='$PHP_SELF'>\n"; 
		echo "	<table width='100%' border='0' cellspacing='0' cellpadding='0' class='lista'>";
		echo " 	  <tr bgcolor='#FFFFFF'>
					<th>Nome do Profissional</th>
					<th>&nbsp;</th>
				  </tr>";

		while (	$res = pg_fetch_array($row) ) {
		
		echo "\n<tr>";
		echo "	<td width='80%'>$res[med_nome]</td>";
		//echo "	<td width='20%'><a href='javascript:;' add_prof_autorizador('$res[med_codigo]', '$res[med_nome]','$res[med_cpf]', true ) >";
		echo "	<td width='20%'><a href='javascript:;' onclick=\"add_conteudo_popup('med_autorizador', 'med_autorizador_h', '$res[med_nome]', '$res[med_codigo]' )\" >";
		echo "	<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_on.jpg' border=0></td></a>";
		echo "</tr>";
		
		}
		echo "	</table>";
		echo "</form>";

}else if($acao == 'cid'){

		echo "
			<fieldset>
				<legend>Opções</legend>
				
					<form method='get' action='#' onsubmit='return busca_lab( \"busca_cid10\" )' />
					<table width=100% align=left cellspacing=3 cellpadding=0 border=0 />
						<tr>					
						<input type='hidden' name='acao' value='busca'>
						<input type='hidden' name='id_login' value='$id_login'>
						<td width=30>Buscar:</td>
						<td width=80><input type='text' name='palavra_chave' id='palavra_chave'
							class='box' /></td>
						<td width=40>
							<select name='busca_cid' id='busca_cid' class=box>
							<option value='1'".( $busca_cid==1 ? ' selected' : '' ).">Código</option>
							<option value='2'".( $busca_cid==2 ? ' selected' : '' ).">Descrição</option>
							</select>
						</td>
						<td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg></td>
						</tr>
					</table>
					</form>
			</fieldset><br />
		";
		
		$sql = "select * from cid10 limit 15";
		$row = pg_query($sql);
	
		echo "<form name='cid' method='get' action='$PHP_SELF'>\n"; 
		echo "	<table width='100%' border='0' cellspacing='0' cellpadding='0' class='lista'>";
		echo " 	  <tr bgcolor='#FFFFFF'>
					<th width='20%'>Código</th>
					<th width='60%'>Descrição do Cid10</th>
					<th width='20%'>&nbsp;</th>
				  </tr>";

		while (	$res = pg_fetch_array($row) ) {
		
			echo "\n<tr>";
			echo "	<td width='20%'>$res[cd10_codigo_cid]</td>";
			echo "	<td width='60%'>$res[cd10_descricao]</td>";
			echo "	<td width='20%'><a href='javascript:;' onclick=\"add_conteudo_popup('aih_cid_cod_princ', 'aih_cid_cod_princ_h', '$res[cd10_descricao]', '$res[cd10_codigo]' )\" >";
			echo "	<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_on.jpg' border=0></a></td>";
			echo "</tr>";
		
		}
		echo "	</table>";
		echo "</form>";


	}else if($acao == 'desc_proc'){

		echo "
			<fieldset>
				<legend>Opções</legend>		
				
					<form method='get' action='#' onsubmit='return busca_lab( \"buscadescproc\" )' />
					<table width=100% align=left cellspacing=3 cellpadding=0 border=0 />
						<tr>					
						<input type='hidden' name='acao' value='busca'>
						<input type='hidden' name='id_login' value='$id_login'>
						<td width=30>Buscar:</td>
						<td width=80><input type='text' name='palavra_chave' id='palavra_chave'
							class='box' /></td>
						<td width=40>
							<select name='busca_procedimento' id='busca_procedimento' class=box>
							<option value='1'".( $busca_procedimento==1 ? ' selected' : '' ).">Código</option>
							<option value='2'".( $busca_procedimento==2 ? ' selected' : '' ).">Descrição</option>
							</select>
						</td>
						<td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg></td>
						</tr>
					</table>
					</form>

				";
						
					/*	<form method='get' action='#' onsubmit='return busca_lab( \"buscadescproc\" )'>
							<input type=hidden name=id_login value=$id_login>
							<table width=100% align=left cellspacing=3 cellpadding=0 border=0>
								<tr>
									<td width=30>Buscar:</td>
									<td width=120><input type='text' name='palavra_chave' id='palavra_chave' class='box' onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
									<td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg></td>
								</tr>
							</table>
						</form>
						*/
						
		echo "				
			</fieldset><br />
		";

		$sql = "select * from procedimento limit 15";
		$row = pg_query($sql);
	
		echo "<form name='desc_proc' method='get' action='$PHP_SELF'>\n"; 
		echo "	<table width='100%' border='0' cellspacing='0' cellpadding='0' class='lista'>";
		echo " 	  <tr bgcolor='#FFFFFF'>
					<th>Código</th>
					<th>Nome do Procedimento</th>
					<th>&nbsp;</th>
				  </tr>";

		while (	$res = pg_fetch_array($row) ) {
		
			echo "\n<tr>";
			echo "	<td width='10%'> $res[proc_classificacao_sus]</td>";
			echo "	<td width='70%'> $res[proc_nome]</td>";
			echo "	<td width='20%'><a href='javascript:;' onclick=\"add_conteudo_popup_procedimento('$res[proc_nome]', '$res[proc_codigo]', '$res[proc_classificacao_sus]' )\" >";
			echo "	<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_on.jpg' border=0></a></td>";
			echo "</tr>";
		
		}
		echo "	</table>";
		echo "</form>";
		
}

	if( $acao == 'busca' && ! empty($palavra_chave) )	
	{
	
		$str = str_replace("+","%%",$palavra_chave);
		
		// arrumando sql
		$resp = '';
		$max 	= 15;
		
		if ($acaoform == 'buscasolicitante'){
			
				$where 	= empty($acao) ? '' : sprintf("WHERE med_nome ILIKE '%%%s%%'", $str);
		
/*				$sql = 'select * from medico '. $where;
				$row = pg_query($sql);*/
		
				$stmt = 'SELECT med_codigo, med_nome FROM medico '. $where;
				$th = 'Nome do Médico';
	
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
								<th>".$th."</th>
								<th>&nbsp;</th>
							  </tr>";
			
						while (	$res = pg_fetch_array($qry) ) {
							
							$onclick = "onclick=\"add_conteudo_popup('med_codigo_solicitante', 'med_codigo_solicitante_h', '$res[med_nome]', '$res[med_codigo]' )\" ";
							
							echo "\n<tr>";
							echo "\n	<td width='80%'>$res[1]</td>";
							echo "\n	<td width='20%'><a href='javascript:;' ".$onclick." >";
							echo "\n	<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_on.jpg' border=0></a></td>";
							echo "\n</tr>";
						
						}
					echo "	</table>";
					echo "</form>
					
						</fieldset><br />"; 
				
		}else if($acaoform == 'buscaexecutante'){
			
				$where 	= empty($acao) ? '' : sprintf("WHERE med_nome ILIKE '%%%s%%'", $str);
		
/*				$sql = 'select * from medico '. $where;
				$row = pg_query($sql);*/
		
				$stmt = 'SELECT med_codigo, med_nome FROM medico '. $where;
				$th = 'Nome do Médico';
	
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
								<th>".$th."</th>
								<th>&nbsp;</th>
							  </tr>";
			
						while (	$res = pg_fetch_array($qry) ) {

							$onclick = "onclick=\"add_conteudo_popup('med_codigo_executante', 'med_codigo_executante_h', '$res[med_nome]', '$res[med_codigo]' )\" ";

							echo "\n<tr>";
							echo "\n	<td width='80%'>$res[1]</td>";
							echo "\n	<td width='20%'><a href='javascript:;' ".$onclick." >";
							echo "\n	<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_on.jpg' border=0></a></td>";
							echo "\n</tr>";
						
						}
					echo "	</table>";
					echo "</form>
					
						</fieldset><br />"; 
				
		}else if($acaoform == 'buscapaciente'){
			
				$where1 	= empty($acao) ? '' : sprintf("WHERE TO_ASCII(usu_nome) ILIKE TO_ASCII('%%%s%%')", $str);
				$where2 	= empty($acao) ? '' : sprintf("WHERE TO_ASCII(pac_nome) ILIKE TO_ASCII('%%%s%%')", $str);
		
/*				$sql = "SELECT usu_codigo, usu_nome, usu_sexo, to_char (usu_datanasc, 'dd/mm/YYYY' ) as usu_datanasc, ". 
						"usu_cpf, usu_rg, usu_mae, usu_end_rua, usu_end_nr, usu_end_bairro, usu_end_cep, usu_end_cidade, usu_fone ".
						"FROM usuario ". $where;
						
				$row = pg_query($sql);*/
		
				//$stmt="SELECT usu_codigo, usu_nome, usu_sexo, to_char (usu_datanasc, 'dd/mm/YYYY' ) as usu_datanasc, ". 
				//"usu_cpf, usu_rg, usu_mae, usu_end_rua, usu_end_nr, usu_end_bairro, usu_end_cep, usu_end_cidade, usu_fone ".
						//"FROM usuario ". $where;
				
			$stmt= "(SELECT usu_codigo, usu_nome, usu_sexo, to_char (usu_datanasc, 'dd/mm/YYYY' ) as usu_datanasc, ". 
			"usu_cpf, usu_rg, usu_mae, usu_end_rua, usu_end_nr, usu_end_bairro, usu_end_cep, usu_end_cidade, usu_fone ".
			" ,'N' as aih, usu_prontuario ".
			"FROM usuario $where1 ) ".
			"UNION ".
			"(SELECT pac_codigo, pac_nome, pac_sexo, to_char (pac_dt_nasc, 'dd/mm/YYYY' ) as usu_datanasc, ". 
			"pac_cpf, pac_rg, pac_mae_responsavel_nome, pac_end_rua, pac_end_nr, pac_end_bairro, pac_end_cep,
				pac_end_cidade, pac_telefone ".
			" ,'S' as aih, pac_prontuario as usu_prontuario ".		
			"FROM aih_paciente $where2 ) ".
			"ORDER BY 2";
						
				$th = 'Nome do Paciente';
	
				$qry = db_query($stmt);
				$num = pg_num_rows($qry);
				
						if( $num == 0 ) 	{ $resp = "Nenhum Registro encontrado com \"$palavra_chave\""; }
						elseif( $num == 1 ) { $resp = "Encontrado <b>$num</b> Registro com \"$palavra_chave\""; }
						elseif( $num > 1) 	{ $resp = "Encontrados <b>$num</b> Registros com \"$palavra_chave\""; }
				
		echo "
			<fieldset>
				<legend>Opções</legend>		
					<form method='get' action='#' onsubmit='return busca_lab( \"buscapaciente\" )'>
						<input type=hidden name=id_login value=$id_login>
						<table width=100% align=left cellspacing=3 cellpadding=0 border=0>
						<tr>
							<td width=150><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg' alt='Adicionar' onclick='form_paci(\"$id_login\",0)' </td>
							<td width=30>Buscar:</td>
							<td width=120><input type='text' name='palavra_chave' id='palavra_chave' class='box' onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
							<td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg></td>
						</tr>
						</table>
					</form>
			</fieldset><br />
		";


				echo "	<fieldset>
							<legend>"; echo $resp; echo"</legend>";		
								
					echo "<form name='processo' method='get' action='$PHP_SELF'>\n"; 
					echo "	<table width='100%' border='0' cellspacing='0' cellpadding='0' class='lista'>";
					echo " 	  <tr bgcolor='#FFFFFF'>
							<th>Nome do Paciente</th>
							<th>Nome da Mae</th>
							<th>Data de Nascimento</th>
							<th>Prontuario</th>
							<th>CPF</th>
							<th>AIH</th>
							<th width='240'>&nbsp;</th>
						</tr>";
			
						while (	$res = pg_fetch_array($qry) ) {
						echo "\n<tr>";
						echo "\n<td>$res[usu_nome]</td>";
						echo "\n<td>$res[usu_mae]</td>";
						echo "\n<td align='center'>$res[usu_datanasc]</td>";
						echo "\n<td align='center'>$res[usu_prontuario]</td>";
						echo "\n<td align='center'>$res[usu_cpf]</td>";
						echo "\n<td class='c' align='center'>$res[aih]</td>";
						echo "\n<td class='c'><a href='javascript:;' onclick=\"add_conteudo_popup_paciente('$res[usu_nome]', '$res[usu_codigo]', '$res[usu_rg]', '$res[usu_cpf]', '$res[usu_datanasc]', '$res[usu_sexo]', '$res[usu_mae]', '$res[usu_fone]', '$res[usu_end_rua]', '$res[usu_end_nr]', '$res[usu_end_bairro]', '$res[usu_end_cidade]', '$res[usu_end_cep]','$res[aih]','$res[prontuario]' )\">";
						echo "\n<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_on.jpg' border='0' alt='Selecionar'></a>";
						echo ( $res['aih'] == 'S' ?
								" <img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg' border='0' alt='Editar' style='cursor:pointer;' 
									onclick=\"form_paci('$id_login','$res[0]')\" />
								 <img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg' border='0' alt='Apagar' style='cursor:pointer;'
									onclick=\"apagar_paci('$id_login','$res[0]')\" />" :
								'' );
						echo "\n</td></tr>";
						}
					echo "	</table>";
					echo "</form>
					
						</fieldset><br />"; 
	
		}else if($acaoform == 'profissionalsolicitante'){
	
				$where 	= empty($acao) ? '' : sprintf("WHERE prestador_servico='N' AND med_nome ILIKE '%%%s%%'", $str);
		
/*				$sql = 'select * from medico '. $where;
				$row = pg_query($sql);*/
		
				$stmt = 'SELECT med_codigo, med_nome FROM medico '. $where;
				$th = 'Nome do Médico';
	
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
								<th>".$th."</th>
								<th>&nbsp;</th>
							  </tr>";
			
						while (	$res = pg_fetch_array($qry) ) {

							$onclick = "onclick=\"add_conteudo_popup('med_solicitante_proc', 'med_solicitante_proc_h', '$res[med_nome]', '$res[med_codigo]' )\" ";
				
							echo "\n<tr>";
							echo "\n	<td width='80%'>$res[1]</td>";
							echo "\n	<td width='20%'><a href='javascript:;' ".$onclick." >";
							echo "\n	<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_on.jpg' border=0></a></td>";
							echo "\n</tr>";
						
						}
					echo "	</table>";
					echo "</form>
					
						</fieldset><br />"; 
	
		}else if($acaoform == 'profissionalautorizador'){
	
				$where 	= empty($acao) ? '' : sprintf("WHERE prestador_servico='N' AND med_nome ILIKE '%%%s%%'", $str);
		
/*				$sql = 'select * from medico '. $where;
				$row = pg_query($sql);*/
		
				$stmt = 'SELECT med_codigo, med_nome FROM medico '. $where;
				$th = 'Nome do Médico';
	
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
								<th>".$th."</th>
								<th>&nbsp;</th>
							  </tr>";
			
						while (	$res = pg_fetch_array($qry) ) {

							$onclick = "onclick=\"add_conteudo_popup('med_autorizador', 'med_autorizador_h', '$res[med_nome]', '$res[med_codigo]' )\" ";

							echo "\n<tr>";
							echo "\n	<td width='80%'>$res[1]</td>";
							echo "\n	<td width='20%'><a href='javascript:;' ".$onclick." >";
							echo "\n	<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_on.jpg' border=0></a></td>";
							echo "\n</tr>";
						
						}
					echo "	</table>";
					echo "</form>
					
						</fieldset><br />"; 
	
		}else if($acaoform == 'buscacid'){

		$where 	= empty($acao) ? '' : sprintf("WHERE cd10_descricao ILIKE '%%%s%%'", $str);
	
/*		$sql = 'select * from cid10 '. $where;
		$row = pg_query($sql);*/
	
		$stmt = 'SELECT cd10_codigo, cd10_descricao FROM cid10 '. $where;
		$th = 'Descrição do Cid10';
	
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
						<th>".$th."</th>
						<th>&nbsp;</th>
						</tr>";
	
				while (	$res = pg_fetch_array($qry) ) {
					
					$onclick = "onclick=\"add_conteudo_popup('aih_cid_cod_princ', 'aih_cid_cod_princ_h', '$res[cd10_descricao]', '$res[cd10_codigo]' )\" ";
					
					echo "\n<tr>";
					echo "\n	<td width='80%'>$res[1]</td>";
					echo "\n	<td width='20%'><a href='javascript:;' ".$onclick." >";
					echo "\n	<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_on.jpg' border=0></a></td>";
					echo "\n</tr>";
				
				}
			echo "	</table>";
			echo "</form>
			
				</fieldset><br />"; 

		}else if($acaoform == 'buscadescproc'){
		
			
			if ($valor_busca==1){
				$campo = 'proc_classificacao_sus';
			}else if($valor_busca==2){
				$campo = 'proc_nome';
			}

		$where 	= empty($acao) ? '' : sprintf("WHERE $campo ILIKE '%%%s%%'", $str);

// 		$sql = 'select * from procedimento '. $where;
// 		$row = pg_query($sql);

		$stmt = 'SELECT * FROM procedimento '. $where;
		$th = 'Descrição do Procedimento';

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
						<th> Código </th>
						<th>".$th."</th>
						<th>&nbsp;</th>
						</tr>";
	
				while (	$res = pg_fetch_array($qry) ) {
					
					$onclick = "onclick=\"add_conteudo_popup_procedimento( '$res[proc_nome]', '$res[proc_codigo]', '$res[proc_classificacao_sus]' )\" ";
					
					echo "\n<tr>";

					echo "\n	<td width='10%'> $res[proc_classificacao_sus]</td>";
					echo "\n	<td width='70%'> $res[proc_nome]</td>";
					echo "\n	<td width='20%'><a href='javascript:;' ".$onclick." >";
					echo "\n	<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_on.jpg' border=0></a></td>";
					echo "\n</tr>";
				
				}
			echo "	</table>";
			echo "</form>
			
				</fieldset><br />"; 

		}else if($acaoform == 'busca_cid10'){
				
			if ($valor_busca==1){
				$campo = 'cd10_codigo_cid';
			}else if($valor_busca==2){
				$campo = 'cd10_descricao';
			}
		
		$where 	= empty($acao) ? '' : sprintf("WHERE $campo ILIKE '%%%s%%'", $str);

// 		$sql = 'SELECT * FROM cid10 '. $where;
// 		$row = db_query($sql);
		
		$stmt = 'SELECT cd10_descricao, cd10_codigo_cid, cd10_codigo FROM cid10 '. $where;
		$th = 'Código';
		$th1 = 'Descrição';

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
						<th width='60%'>".$th1."</th>
						<th width='20%'>&nbsp;</th>
						</tr>";
	
				while (	$res = pg_fetch_array($qry) ) {
					
					$onclick = "onclick=\"add_conteudo_popup('aih_cid_cod_princ', 'aih_cid_cod_princ_h', '$res[cd10_descricao]', '$res[cd10_codigo]' )\" ";
					
					echo "\n<tr>";
					echo "\n	<td width='20%'>$res[1]</td>";
					echo "\n	<td width='60%'>$res[0]</td>";					
					echo "\n	<td width='20%'><a href='javascript:;' ".$onclick." >";
					echo "\n	<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_on.jpg' border=0></a></td>";
					echo "\n</tr>";
				
				}
			echo "	</table>";
			echo "</form>
			
				</fieldset><br />";

		}
		
	}
/**
* Adicionado por Dudu@g1ti.com.br
* 2007-02-24
*/
else if( $acao == 'pac_form_add' || $acao == 'pac_form_edit' )
{
	//var_dump($_GET);
	
	if( $acao == 'pac_form_edit' && $codigo )
	{
		// SQL SELECT
		$stmt = "SELECT pac_nome, pac_sexo, pac_rg, pac_cpf, pac_cns, pac_prontuario, pac_mae_responsavel_nome,
			pac_mae_responsavel_rg, pac_mae_responsavel_cpf, pac_end_rua, pac_end_nr, pac_end_compl,
			pac_end_bairro, pac_end_cep, pac_end_cidade, TO_CHAR(pac_dt_nasc,'dd/mm/yyyy') as pac_dt_nasc, 
			pac_telefone, pac_ibge_codigo
			FROM aih_paciente WHERE pac_codigo=$codigo ";
			
		$row = db_getRow($stmt);
	}
	else
		$row = array();
	
	print "
		<form name=\"aih_form_add_paciente\" action=\"#\" method=\"get\" onsubmit=\"return form_paci_submit('$id_login','$codigo') \">
		<fieldset>
		<legend>Cadastro de Paciente - AIH</legend>
		<table>
			<tr>
				<td width='165'><label for=\"pac_nome\">Nome</label></td>
				<td><input type=\"text\" name=\"pac_nome\" id=\"pac_nome\" size=\"35\"
					 maxlength=\"100\" class=\"box\" value=\"$row[pac_nome]\" />
				</td>
			</tr>
			<tr>
				<td><label for=\"pac_sexo\">Sexo</label></td>
				<td><input type=\"text\" name=\"pac_sexo\" id=\"pac_sexo\" size=\"1\"
					 maxlength=\"1\" class=\"box\" value=\"$row[pac_sexo]\" />
					&nbsp; &nbsp; 'F' Feminino | 'M' Masculino
				</td>
			</tr>
			<tr>
				<td><label for=\"pac_rg\">RG</label></td>
				<td><input type=\"text\" name=\"pac_rg\" id=\"pac_rg\" size=\"15\"
					 maxlength=\"25\" class=\"box\" value=\"$row[pac_rg]\" />
				</td>
			</tr>
			<tr>
				<td><label for=\"pac_cpf\">CPF</label></td>
				<td><input type=\"text\" name=\"pac_cpf\" id=\"pac_cpf\" size=\"15\"
					 maxlength=\"25\" class=\"box\" value=\"$row[pac_cpf]\" />
				</td>
			</tr>
			<tr>
				<td><label for=\"pac_cns\">CNS</label></td>
				<td><input type=\"text\" name=\"pac_cns\" id=\"pac_cns\" size=\"20\"
					 maxlength=\"25\" class=\"box\" value=\"$row[pac_cns]\" />
				</td>
			</tr>
			<!--<tr>
				<td><label for=\"pac_prontuario\">ProntuÃ¡rio</label></td>
				<td><input type=\"text\" name=\"pac_prontuario\" id=\"pac_prontuario\" size=\"15\"
					 maxlength=\"50\" class=\"box\" value=\"$row[pac_prontuario]\" />
				</td>
			</tr>-->
			<tr>
				<td><label for=\"pac_mae_responsavel_nome\">Nome da Mae (responsavel)</label></td>
				<td><input type=\"text\" name=\"pac_mae_responsavel_nome\" id=\"pac_mae_responsavel_nome\" size=\"35\"
					 maxlength=\"255\" class=\"box\" value=\"$row[pac_mae_responsavel_nome]\" />
				</td>
			</tr>
			<tr>
				<td><label for=\"pac_mae_responsavel_rg\">RG da Mae (responsavel)</label></td>
				<td><input type=\"text\" name=\"pac_mae_responsavel_rg\" id=\"pac_mae_responsavel_rg\" size=\"15\"
					 maxlength=\"255\" class=\"box\" value=\"$row[pac_mae_responsavel_rg]\" />
				</td>
			</tr>
			<tr>
				<td><label for=\"pac_mae_responsavel_cpf\">CPF da Mae (responsavel)</label></td>
				<td><input type=\"text\" name=\"pac_mae_responsavel_cpf\" id=\"pac_mae_responsavel_cpf\" size=\"15\"
					 maxlength=\"20\" class=\"box\" value=\"$row[pac_mae_responsavel_cpf]\" />
				</td>
			</tr>
			<tr>
				<td><label for=\"pac_end_rua\">Endereco: Rua</label></td>
				<td><input type=\"text\" name=\"pac_end_rua\" id=\"pac_end_rua\" size=\"35\"
					 maxlength=\"60\" class=\"box\" value=\"$row[pac_end_rua]\" />
				</td>
			</tr>
			<tr>
				<td><label for=\"pac_end_nr\">Endereco: Numero</label></td>
				<td><input type=\"text\" name=\"pac_end_nr\" id=\"pac_end_nr\" size=\"5\"
					 maxlength=\"5\" class=\"box\" value=\"$row[pac_end_nr]\" />
				</td>
			</tr>
			<tr>
				<td><label for=\"pac_end_compl\">Endereco: Complemento</label></td>
				<td><input type=\"text\" name=\"pac_end_compl\" id=\"pac_end_compl\" size=\"35\"
					 maxlength=\"20\" class=\"box\" value=\"$row[pac_end_compl]\" />
				</td>
			</tr>
			<tr>
				<td><label for=\"pac_end_bairro\">Endereco: Bairro</label></td>
				<td><input type=\"text\" name=\"pac_end_bairro\" id=\"pac_end_bairro\" size=\"30\"
					 maxlength=\"30\" class=\"box\" value=\"$row[pac_end_bairro]\" />
				</td>
			</tr>
			<tr>
				<td><label for=\"pac_end_cep\">Endereco: CEP</label></td>
				<td><input type=\"text\" name=\"pac_end_cep\" id=\"pac_end_cep\" size=\"9\"
					 maxlength=\"9\" class=\"box\" value=\"$row[pac_end_cep]\" />
				</td>
			</tr>
			<tr>
				<td><label for=\"pac_end_cidade\">Endereco: Cidade</label></td>
				<td><input type=\"text\" name=\"pac_end_cidade\" id=\"pac_end_cidade\" size=\"35\"
					 maxlength=\"60\" class=\"box\" value=\"$row[pac_end_cidade]\" />
				</td>
			</tr>
			<tr>
				<td><label for=\"pac_dt_nasc\">Data Nascimento</label></td>
				<td><input type=\"text\" name=\"pac_dt_nasc\" id=\"pac_dt_nasc\" size=\"10\" maxlength=\"10\"
					 class=\"box\" value=\"$row[pac_dt_nasc]\" onKeypress=\"return Ajusta_Data(this, event);\" />
				</td>
			</tr>
			<tr>
				<td><label for=\"pac_telefone\">Telefone</label></td>
				<td><input type=\"text\" name=\"pac_telefone\" id=\"pac_telefone\" size=\"10\"
					 maxlength=\"15\" class=\"box\" value=\"$row[pac_telefone]\" />
				</td>
			</tr>
			<tr>
				<td><label for=\"pac_ibge_codigo\">Codigo IBGE</label></td>
				<td><input type=\"text\" name=\"pac_ibge_codigo\" id=\"pac_ibge_codigo\" size=\"20\"
					 maxlength=\"50\" class=\"box\" value=\"$row[pac_ibge_codigo]\" />
				</td>
			</tr>
		</table>
			<table>
				<tr>
					<td width=\"150\">&nbsp;</td>
					<td><input type=\"image\"src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg\" alt=\"Adicionar\" /></td>
				</tr>
			</table>
			
		</fieldset></form>";
}
else if( $acao == 'paci_form_add_sub' )
{
	// SQL INSERT
	$stmt = "INSERT INTO aih_paciente ( 
	pac_nome, 
	pac_sexo, 
	pac_rg, 
	pac_cpf, 
	pac_cns, 
	pac_mae_responsavel_nome, 
	pac_mae_responsavel_rg, 
	pac_mae_responsavel_cpf, 
	pac_end_rua, 
	pac_end_nr, 
	pac_end_compl, 
	pac_end_bairro, 
	pac_end_cep, 
	pac_end_cidade, 
	pac_dt_nasc, 
	pac_telefone, 
	pac_ibge_codigo
	 ) VALUES ( 
	'".trim(strtoupper(substr($pac_nome,0,100)))."', 
	'".trim(strtoupper(substr($pac_sexo,0,1)))."', 
	'".trim(strtoupper(substr($pac_rg,0,25)))."', 
	'".trim(strtoupper(substr($pac_cpf,0,25)))."', 
	'".trim(strtoupper(substr($pac_cns,0,25)))."', 
	'".trim(strtoupper(substr($pac_mae_responsavel_nome,0,255)))."', 
	'".trim(strtoupper(substr($pac_mae_responsavel_rg,0,255)))."', 
	'".trim(strtoupper(substr($pac_mae_responsavel_cpf,0,20)))."', 
	'".trim(strtoupper(substr($pac_end_rua,0,60)))."', 
	'".trim(strtoupper(substr($pac_end_nr,0,5)))."', 
	'".trim(strtoupper(substr($pac_end_compl,0,20)))."', 
	'".trim(strtoupper(substr($pac_end_bairro,0,30)))."', 
	'".trim(strtoupper(substr($pac_end_cep,0,9)))."', 
	'".trim(strtoupper(substr($pac_end_cidade,0,60)))."', 
	'".trim(strtoupper($pac_dt_nasc))."', 
	'".trim(strtoupper(substr($pac_telefone,0,15)))."', 
	'".trim(strtoupper(substr($pac_ibge_codigo,0,50)))."' )";
	
	db_query( $stmt );
	
	print '<p class="aviso ok">Paciente cadastrado !</p>';

}
else if( $acao == 'paci_form_edit_sub' )
{
	// SQL UPDATE
	$stmt = "UPDATE aih_paciente SET 
	pac_nome = '".trim(strtoupper(substr($pac_nome,0,100)))."', 
	pac_sexo = '".trim(strtoupper(substr($pac_sexo,0,1)))."', 
	pac_rg = '".trim(strtoupper(substr($pac_rg,0,25)))."', 
	pac_cpf = '".trim(strtoupper(substr($pac_cpf,0,25)))."', 
	pac_cns = '".trim(strtoupper(substr($pac_cns,0,25)))."', 
	pac_prontuario = '".trim(strtoupper(substr($pac_prontuario,0,50)))."', 
	pac_mae_responsavel_nome = '".trim(strtoupper(substr($pac_mae_responsavel_nome,0,255)))."', 
	pac_mae_responsavel_rg = '".trim(strtoupper(substr($pac_mae_responsavel_rg,0,255)))."', 
	pac_mae_responsavel_cpf = '".trim(strtoupper(substr($pac_mae_responsavel_cpf,0,20)))."', 
	pac_end_rua = '".trim(strtoupper(substr($pac_end_rua,0,60)))."', 
	pac_end_nr = '".trim(strtoupper(substr($pac_end_nr,0,5)))."', 
	pac_end_compl = '".trim(strtoupper(substr($pac_end_compl,0,20)))."', 
	pac_end_bairro = '".trim(strtoupper(substr($pac_end_bairro,0,30)))."', 
	pac_end_cep = '".trim(strtoupper(substr($pac_end_cep,0,9)))."', 
	pac_end_cidade = '".trim(strtoupper(substr($pac_end_cidade,0,60)))."', 
	pac_dt_nasc = '".trim(strtoupper($pac_dt_nasc))."', 
	pac_telefone = '".trim(strtoupper(substr($pac_telefone,0,15)))."', 
	pac_ibge_codigo = '".trim(strtoupper(substr($pac_ibge_codigo,0,50)))."'
	WHERE pac_codigo = ".intval($codigo) ;
	
	db_query( $stmt );
	
	print '<p class="aviso ok">Paciente alterado !</p>';
}
else if( $acao == 'paci_form_del_sub' )
{
	// SQL DELETE
	$stmt = "DELETE FROM aih_paciente WHERE pac_codigo = ".intval($codigo);	

	db_query( $stmt );
	
	print '<p class="aviso ok">Paciente removido !</p>';
}
