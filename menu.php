<?php
	session_start();
	require_once $_SESSION['root'].$_SESSION['comum']."library/php/db.inc.php";
	function SelPerm7($id_login,$arq) {
		$sql = pg_query ("select p.perm_descricao,p.perm_programa,up.nivel_i,up.nivel_a,up.nivel_d,up.nivel_l,up.nivel_b,up.perm_set from usuarios_permissoes as up left join permissoes as p on up.perm_codigo=p.perm_codigo where up.usr_codigo = '$id_login' and p.perm_programa = '$arq' and up.perm_set = 'S'");
		$perm_set = pg_num_rows($sql);
		return $perm_set;
	}

	$recebeMenuInferiorClass = verificaMenuInferior($id_login);
?>
<style>
.icon { color:#1eacd7;}
</style>
<script type="text/JavaScript">
	var _popup = null;
	function abrirChat(){
		<?php $sql = "SELECT usr_nome, usr_email, uni_desc
		                FROM usuarios usr
		                JOIN logon l
		                  ON l.id_login=usr.usr_codigo
		                 AND l.id_login={$_SESSION['id_login']}
		                JOIN unidade uni
		                  ON uni.uni_codigo=l.uni_codigo";

		$query = pg_query($sql);// or die("$sql<br />".pg_last_error());
		$r = pg_fetch_array($query);
		$empresa = $r['uni_desc']. " - ".getConfig("NOME_CIDADE");
		?>
		var nome = "<?=$r['usr_nome'];?>";
		var email = "<?=$r['usr_email'];?>";
		var empresa = "<?=$empresa;?>";
		var url = "http://www.ibitech.com.br/suporte/chat.php?nome="+nome+"&email="+email+"&nome_empresa="+empresa;

		if (_popup==null || _popup.closed)
	  		_popup = window.open(url, "chat","height=600,width=450,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");

		if (_popup!=null && !_popup.closed)
			_popup.focus();

	}

	function abrirNovidades(){
		var url = "http://www.ibitech.com.br/novidadesdaversao/saude/4.3.7";
//http://www.elotech.com.br/divulgacao/?modulo=14&versao=<?php echo $_SESSION['versao']; ?>";
	  	window.open(url);
	}
</script>
<body leftmargin="0" topmargin="0" onLoad="MM_preloadImages('<?= $_SESSION['linkroot'].$_SESSION['comum'];?>imgs/pacientes_on.png','<?= $_SESSION['linkroot'].$_SESSION['comum'];?>imgs/familia_on.png','<?= $_SESSION['linkroot'].$_SESSION['comum'];?>imgs/laboratorio_on.png','<?= $_SESSION['linkroot'].$_SESSION['comum'];?>imgs/consultas_on.png','<?= $_SESSION['linkroot'].$_SESSION['comum'];?>imgs/farmacia_on.png','<?= $_SESSION['linkroot'].$_SESSION['comum'];?>imgs/materiais_on.png','<?= $_SESSION['linkroot'].$_SESSION['comum'];?>imgs/leitos_on.png','<?= $_SESSION['linkroot'].$_SESSION['comum'];?>imgs/vacinas_on.png','<?= $_SESSION['linkroot'].$_SESSION['comum'];?>imgs/transporte_on.png','<?= $_SESSION['linkroot'].$_SESSION['comum'];?>imgs/atendimento_on.png','<?= $_SESSION['linkroot'].$_SESSION['comum'];?>imgs/psf_on.png','<?= $_SESSION['linkroot'].$_SESSION['comum'];?>imgs/genograma_on.png','<?= $_SESSION['linkroot'].$_SESSION['comum'];?>imgs/emergencia_on.png','<?= $_SESSION['linkroot'].$_SESSION['comum'];?>imgs/usuarios_on.png','<?= $_SESSION['linkroot'].$_SESSION['comum'];?>imgs/email_on.png','<?= $_SESSION['linkroot'].$_SESSION['comum'];?>imgs/chat_on.png','<?= $_SESSION['linkroot'].$_SESSION['comum'];?>imgs/novidades_on.png','<?= $_SESSION['linkroot'].$_SESSION['comum'];?>imgs/sistema_on.png')">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td background="<?= $_SESSION['linkroot'].$_SESSION['comum'];?>imgs/fundo_menu.png">
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr class="menu-icons">
						<td <?=$recebeMenuInferiorClass->menu_paciente == "f"? 'style = "display : none"': ''?>>
							<a href="<?=$PHP_SELF?>?link=paciente.php">
								<i class="icon icon-user"></i>
								<span class="link-label">Pacientes</span>
							</a>
						</td>
						<td <?=$recebeMenuInferiorClass->menu_esf == "f"? 'style = "display : none"': ''?>>
							<a href="<?=$PHP_SELF?>?link=domicilio.php">
								<i class="icon icon-users"></i>
								<span class="link-label">ESF</span>
							</a>
						</td>
						<td <?=$recebeMenuInferiorClass->menu_laboratorio == "f"? 'style = "display : none"': ''?>>
							<a href="<?=$PHP_SELF?>?link=exame/exa_listapedidoexame.php&id_login=<?=$id_login; ?>">
								<i class="icon icon-beaker"></i>
								<span class="link-label">Laborat&oacute;rio</span>
							</a>
						</td>						
						<td <?=$recebeMenuInferiorClass->menu_internacao == "f"? 'style = "display : none"': ''?>>
							<a href="<?=$PHP_SELF?>?link=internacao.php&id_login=<?=$id_login; ?>">
								<i class="icon icon-ambulance"></i>
								<span class="link-label">Interna&ccedil&atildeo</span>
							</a>
						</td>
						<td <?=$recebeMenuInferiorClass->menu_agendamento == "f"? 'style = "display : none"': ''?>>
							<a href="<?=$PHP_SELF?>?link=zf/agendamento">
								<i class="icon icon-stethoscope"></i>
								<span class="link-label">Agendamento</span>
							</a>
						</td>
						<td <?=$recebeMenuInferiorClass->menu_farmacia == "f"? 'style = "display : none"': ''?>>
							<a href="<?=$PHP_SELF?>?link=zf/farmacia/farmacia&id_login=<?=$id_login; ?>">
								<i class="icon icon-plus-squared"></i>
								<span class="link-label">Farm&aacute;cia</span>
							</a>
						</td>
						<td <?=$recebeMenuInferiorClass->menu_materiais == "f"? 'style = "display : none"': ''?>>
							<a href="<?=$PHP_SELF?>?link=materiais.php&id_login=<?=$id_login; ?>">
								<i class="icon icon-cubes"></i>
								<span class="link-label">Materiais</span>
							</a>
						</td>
						 <!--<td>
							<a href="<?=$PHP_SELF?>?link=zf/leito/internacao/index">
								<i class="icon icon-bed"></i>
								<span class="link-label">Leitos</span>
							</a>
						</td>-->
						<td <?=$recebeMenuInferiorClass->menu_vacinas == "f"? 'style = "display : none"': ''?>>
							<a href="<?=$PHP_SELF?>?link=zf/default/vacina/">
								<i class="icon icon-eyedropper"></i>
								<span class="link-label">Vacinas</span>
							</a>
						</td>
						<td <?=$recebeMenuInferiorClass->menu_relatorios == "f"? 'style = "display : none"': ''?>>
							<a href="<?=$PHP_SELF?>?link=rel_index.php&id_login=<?=$id_login; ?>">
								<i class="icon icon-docs"></i>
								<span class="link-label">Relat&oacute;rios</span>
							</a>
						</td>
						<td <?=$recebeMenuInferiorClass->menu_prontuario == "f"? 'style = "display : none"': ''?>>
							<a href="<?=$PHP_SELF?>?link=zf/prontuario/">
								<i class="icon icon-doc-text-inv"></i>
								<span class="link-label">Prontu&aacute;rio</span>
							</a>
						</td>
						<td <?=$recebeMenuInferiorClass->menu_usuarios == "f"? 'style = "display : none"': ''?>>
							<a href="<?=$PHP_SELF?>?link=zf/usuarios/usuarios">
								<i class="icon icon-user-md"></i>
								<span class="link-label">Usu&aacute;rios</span>
							</a>
						</td>
						<td <?=$recebeMenuInferiorClass->menu_email == "f"? 'style = "display : none"': ''?>>
							<a href="<?=$PHP_SELF?>?link=mensagem.php">
								<i class="icon icon-mail-alt"></i>
								<span class="link-label">E-Mail</span>
							</a>
						</td>
						<td <?=$recebeMenuInferiorClass->menu_chat == "f"? 'style = "display : none"': ''?>>
							<a href="javascript:abrirChat();">
								<i class="icon icon-chat"></i>
								<span class="link-label">Chat</span>
							</a>
						</td>
						<!-- <td>
							<a href="javascript:abrirNovidades();">
								<i class="icon icon-newspaper"></i>
								<span class="link-label">Novidades</span>
							</a>
						</td> -->
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>

