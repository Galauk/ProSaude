 <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"><head>
	<title>SSP Prototype</title>
	<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8">
	<link rel="stylesheet" type="text/css" media="screen" href="css/dock.css">

<noscript>
		<style type="text/css">
			#dock { top: 50 px; }
			a.dock-item { position: relative; float: left; margin-right: 10px; }
			.dock-item span { display: block; }
			.stack { top: 50; }
			.stack ul li { position: relative; }
		</style>
	</noscript>
	<script type="text/javascript" src="js/fisheye-iutil.min.js"></script>
	<script type="text/javascript" src="js/dock.js"></script>
</head>
<body>
		<!-- BEGIN DOCK 1 ============================================================ -->
		<div id="dock">
			<div class="dock-container">
			<?
				if(SelPerm($id_login,'paciente.php') != "0") {
			?>
				<a class="dock-item" href="<?="$PHP_SELF?link=paciente.php&id_login=$id_login"?>">
					<img src="images/dock/cadastro.png" alt="cadastro" title="Cadastro" />
				</a> 
			<?
				}
			?>
			
			<?
				//if(SelPerm($id_login,'familia.php') != "0") {
			?>
				<a class="dock-item" href="<?=$PHP_SELF?>?link=../psf.php&id_login=$id_login?>"><img src="images/dock/familia.png" alt="consultas" title="Fam&iacute;lia" /></a> 
			<?
				//}
			?>
			
			<?
				if(SelPerm($id_login,'medico.php') != "0") {
			?>
				<a class="dock-item" href="<?=$PHP_SELF?>?link=medico.php&id_login=<?=$id_login?>"><img src="images/dock/medicos.png" alt="Medicos" title="M&eacute;dicos"/></a>
				
			<?
				}
			?> 
			
			<?
				if(SelPerm($id_login,'unidade.php') != "0") {
			?>
				<a class="dock-item" href="<?=$PHP_SELF?>?link=unidade.php&id_login=<?=$id_login?>"><img src="images/dock/unidades.png" alt="atendimento" title="Unidades" /></a>
			<?
				}
			?> 
			
			<?
				if(SelPerm($id_login,'agendamento.php') != "0") {
			?>
				<a class="dock-item" href="<?=$PHP_SELF?>?link=agendamento_atd.php&id_login=<?=$id_login?>"><img src="images/dock/consultas.png" alt="materiais" title="Agendamento"/></a> 
			<?
				}
			?>
			
			<?
				if(SelPerm($id_login,'exame/exa_listapedidoexame.php') != "0") {
			?>
				<a class="dock-item" href="<?=$PHP_SELF?>?link=exame/exa_listapedidoexame.php&id_login=<?=$id_login ?>"><img src="images/dock/exames.png" alt="exames" title="Exames"/></a> 
			<?
				}
			?>
			
			<?
				if(SelPerm($id_login,'materiais.php.php') != "0") {
			?>
				<a class="dock-item" href="<?=$PHP_SELF?>?link=materiais.php&id_login=<?=$id_login?>"><img src="images/dock/materiais.png" alt="social" title="Materiais"/></a>
			<?
				}
			?>
			<?
				if(SelPerm($id_login,'farmacia.php') != "0") {
			?> 
				<a class="dock-item" href="<?=$PHP_SELF?>?link=farmacia.php&id_login=<?=$id_login?>"><img src="images/dock/farmacia.png" alt="farmacia" title="Farm&aacute;cia"/></a>
			<?
				}
			?>
			
			<?
				if(SelPerm($id_login,'rel_index.php') != "0") {
			?>
				<a class="dock-item" href="<?=$PHP_SELF?>?link=rel_index.php&id_login=<?=$id_login?>"><img src="images/dock/relatorio.png" alt="relatorios" title="Relat&oacute;rios"/></a> 
			<?
				}
			?>
			
			<?
				if(SelPerm($id_login,'ambulatorio.php') != "0") {
			?>
				<a class="dock-item" href="<?=$PHP_SELF?>?link=ambulatorio.php&id_login=$id_login"><img src="images/dock/pam.png" alt="sistema" title="PAM"/></a> 
			<?
				}
			?>
			
			<?
				if(SelPerm($id_login,'usuarios.php') != "0") {
			?>
				<a class="dock-item" href="<?=$PHP_SELF?>?link=usuarios.php&id_login=<?=$id_login?>"><img src="images/dock/usuario.png" alt="sair" / title="Usu&aacute;rios"></a> 
				<?
				}
				?>
				
				<?
					if(SelPerm($id_login,'mensagem.php') != "0") {
				?>
				<a class="dock-item" href="#" onclick="window.open('mensagem.php?id_login=<?=$id_login?>', 'msg','width=600,height=350,scrollbars=yes,resizable=yes,top=100,left=10')"><img src="images/dock/mensagem.png" alt="mensagem" title="Mensagens"/></a>
				<?
					}
				?> 
				<?
//					if(SelPerm($id_login,'vacina.php') != "0") {
				?>
				<a class="dock-item" href="<?=$PHP_SELF?>?link=Vacina/vacina.php&id_login=<?=$id_login?>" ><img src="images/dock/vacina.png" alt="vacina" title="Vacina"/></a>
				<?
//				}
				?>
				<a class="dock-item" href="<?=$PHP_SELF?>?link=moduloBordo/transporte.php&id_login=<?=$id_login?>"><img src="images/dock/bordo.png" alt="Di&aacute;rio de Bordo" title="Di&aacute;rio de Bordo"></a>
			</div><!-- end div .dock-container -->
		</div><!-- end div .dock #dock -->
		<!-- END DOCK 1 ============================================================ -->

	
	
</body>
</html>
