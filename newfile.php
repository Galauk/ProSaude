<script>
shortcut.add("F2",function() 
		{
		 buscar_nome($F('pac_nome'), 'buscar_nome');return false;link_f7();
		});
</script>


<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<script language="JavaScript" type="text/javascript" src="ajax_motor.js"></script>



<?php

session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
verauth($id_login);
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
cabecario();
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."json.inc.php";


echo "<fieldset>";
				echo "<legend>Dados do Pacientes</legend>";
				echo "<table width=100% cellspacing=0 cellpadding=1 border=0>";
					echo "<tr>";
						echo "<td width=110>Prontu&aacute;rio</td>";
						echo "<td width=40>";
							//*echo "<input type=text name='pac_codigo' id='pac_codigo' class=boxl size=10 readonly>";
                            echo "<input type=hidden name='pac_codigo' id='pac_codigo' class=boxl size=10 onchange='buscar_dados_paciente();'>";
                            echo "<input type=text name='pac_prontuario' id='pac_prontuario' class=boxl size=10                 onchange='buscar_dados_paciente(this.value);'>";
							
							echo "</td>";
						echo "<td width=40>Paciente: </td>";
						echo "<td>";
								echo "<input type=text size=80 name=pac_nome id=pac_nome value='$pac[usu_nome]' class=box  style=\"text-transform:uppercase;\">&nbsp;";
						echo "<a href='#' onclick=\"buscar_nome(\$F('pac_nome'), 'buscar_nome');return false;\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg id=localizar align=absmiddle border=0></a>";
						
					



						echo divBuscaPaciente();
						echo "</td>";
						echo "<td>Nascimento:</td>";
						echo "<td width=230>";
						echo "<input type=text name=pac_nascimento id=pac_nascimento class=boxl size=15 readonly onfocus='buscar_prontuario();'>";
						/*echo "<td width=40>Paciente</td>";
						echo "<td>";
							echo "<input type=text name=pac_nome id=pac_nome class=boxl size=60><a href='#' OnClick='window.open(\"list_pacientes.php?id_login=$id_login&from=list\",null,\"height=460,width=600,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes\");'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg align=absmiddle border=0 id='localizar'></a>";
							/*echo "<input type=text name=pac_nome id=pac_nome class=boxl size=60 readonly><a href='#' OnClick='window.open(\"paciente.php?id_login=$id_login&controle=1\",null,\"height=460,width=800,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes\");'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg align=absmiddle border=0></a>";*/
							/*echo "<a href='#' OnClick='window.open(\"paciente.php?acao=form_add&id_login=$id_login&controle=1\",null,\" height=460,width=800,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes\");'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/ficha_on.jpg align=absmiddle border=0></a>";
							echo "<a href='#' OnClick='window.open(\"paciente_ficha.php?acao=form_add&type=c&id_login=$id_login&controle=1&from=list\",null,\" height=460,width=800,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes\");'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/ficha_on.jpg align=absmiddle border=0 id='ficha'></a>";
						echo "</td>";
						echo "<td>Nascimento</td>";
						echo "<td width=230>";
							echo "<input type=text name=pac_nascimento id=pac_nascimento class=boxl size=15 readonly onfocus='buscar_prontuario();'>";*/
							
					echo "</tr>";
				//echo "</table>";
				//echo "<table width=100% cellspacing=0 cellpadding=4 border=0>";
					echo "<tr>";
						echo "<td width=70 >Mãe</td>";
						echo "<td width=100 colspan=3>";
							echo "<input type=text name=pac_mae id=pac_mae class=boxl size=50 readonly>";
						echo "</td>";
						echo "<td width=20>Cidade</td>";
						echo "<td width=80>";
							echo "<input type=text name=pac_cidade id=pac_cidade class=boxl size=23 readonly>";
						echo "</td>";
					echo "</tr>";
				echo "</table>";
			echo "</fieldset>";
?>