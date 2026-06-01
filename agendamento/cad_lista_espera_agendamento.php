<?php
	session_start();
	
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	echo "<fieldset>";
		echo "<legend>Op&ccedil;&otilde;es</legend>";
		echo "<table>";
			echo "<tr>";
				echo "<td width='75px'>";
					echo "<a href='cad_lista_espera.php?id_login=$id_login&acao=listar'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>";
				echo "</td>";
			echo "</tr>";
		echo "</table>";
	echo "</fieldset>";
	echo "<form name='lista_espera' method='post' action='$PHP_SELF' onsubmit='return validar();'>";
		echo "<input type='hidden' name='id_login' value='$id_login'>";
		echo "<input type='hidden' name='acao' value='add'>";
		echo "<table width='760' align='center' cellspacing='0' cellpadding='0' border='0'>";
			echo "<tr>";
				echo "<td>";
					echo "<fieldset>";
						echo "<legend>Prestador</legend>";
						echo "<table width='100%' cellspacing='0' cellpadding='4' border='0'>";
							echo "<tr>";
								echo "<td align='right' width='115px'>Unidade de Sa&uacute;de</td>";
								echo "<td>";
									echo "<select name='uni_codigo' id='uni_codigo' class='boxa' onChange='buscarMedicos(0)'>";
										echo "<option value=''>....</option>";
										$sql = pg_query("select *from unidade order by uni_desc");
										while($uni=pg_fetch_array($sql))
										{
											echo "<option value='$uni[uni_codigo]'>$uni[uni_desc]</option>";
										}
									echo "</select>";
								echo "</td>";
							echo "</tr>";
							echo "<tr>";
								echo "<td align=right>Atividade prof.</td>";
								echo "<td>";
									echo "<select name='esp_codigo' id='esp_codigo' class='boxa' onChange='buscarMedicos(0)'>";
										echo "<option value=''>....</option>";
										$sql = pg_query("select *from especialidade order by esp_nome");
										while($esp=pg_fetch_array($sql))
										{
											echo "<option value='$esp[esp_codigo]'>$esp[esp_nome]</option>";
										}
									echo "</select>";
								echo "</td>";
							echo "</tr>";
							echo "<tr>";
								echo "<td align='right'>Profissional</td>";
								echo "<td>";
									echo "<select name='med_codigo' id='med_codigo' class='boxa'  >";
										echo "<option value=''>....</option>";
									echo "</select>";
								echo "</td>";
							echo "</tr>";
						echo "</table>";
					echo "</fieldset>";
				echo "</td>";
			echo "</tr>";
		echo "</table>";
		echo "<fieldset>";
			echo "<legend>Dados do Paciente</legend>";
			echo "<table width='100%' cellspacing='0' cellpadding='1' border='0'>";
				echo "<tr>";
					echo "<td width='110'>Numero do Paciente</td>";
					echo "<td width='40'>";
						echo "<input type='text' name='pac_codigo' id='pac_codigo' class='boxl' size='10' readonly>";
						echo "</td>";
					echo "<td width='40'>Paciente</td>";
					echo "<td>";
						echo "<input type='text' name='pac_nome' id='pac_nome' class='boxl' size='60' readonly>";
						echo "<a href='#' OnClick=\"window.open('paciente.php?id_login=$id_login&controle=1', null, 'height=460,width=800,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes');\">";
						echo "<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg' align='absmiddle' border='0'></a>";
					//"
					echo "</td>";
					echo "<td>Nascimento</td>";
					echo "<td width='250'>";
						echo "<input type='text' name='pac_nascimento' id='pac_nascimento' class='boxl' size='15' readonly>";
					echo "</td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td width='70'>M&atilde;e</td>";
					echo "<td width='100' colspan='3'>";
						echo "<input type='text' name='pac_mae' id='pac_mae' class='boxl' size='50' readonly>";
					echo "</td>";
					echo "<td width=40>Cidade</td>";
					echo "<td width=60>";
						echo "<input type='text' name='pac_cidade' id='pac_cidade' class='boxl' size='23' readonly>";
					echo "</td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>";
						echo "<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg'>";
					echo "</td>";
				echo "</tr>";
			echo "</table>";
		echo "</fieldset>";
	echo "</form>";
?>