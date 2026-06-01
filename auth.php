<?php
	session_start();
	$_SESSION['comum']		= "WebSocialComum/";
	$_SESSION['root'] 		= $_SERVER['DOCUMENT_ROOT']."/";
	$_SESSION['modulo'] 	= "WebSocialSaude/";
	$_SESSION['linkroot'] 	= "http://".$_SERVER['HTTP_HOST']."/";

	include_once $_SESSION['root'].$_SESSION['comum'].'/library/php/funcoes.db.php';
	include_once $_SESSION['root'].$_SESSION['comum']."library/php/db.inc.php";
	function get_client_ip() {
		$ipaddress = '';
		if (isset($_SERVER['HTTP_CLIENT_IP']))
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_X_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		else if(isset($_SERVER['REMOTE_ADDR']))
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;
	}
$ip = get_client_ip();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700,900" rel="stylesheet">
		<link rel="shortcut icon" href="<?=$_SESSION['linkroot'].$_SESSION['comum']?>imgsBotoes/mini_logo_elotech.png">
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
		<title>ProSaude - Sistema de Gerenciamento em Saude</title>
		<style type="text/css">
			input[type=number]::-webkit-inner-spin-button { -webkit-appearance: none;cursor:pointer;display:block;width:8px;color: #333;text-align:center;position:relative;
			}
		    input[type=number] { 
			   -moz-appearance: textfield;
			   appearance: textfield;
			   margin: 0; 
			}

			html { background: url('/<?= $_SESSION['modulo'] ?>login-background.jpg') center center  no-repeat; background-size: cover; height: 100%; }
			html * { font-family: 'Source Sans Pro', sans-serif !important; outline: 0 none !important; }
			form { float: left; width: 340px; padding: 20px; background: rgba(255, 255, 255, 0.5); border-radius: 10px; position: absolute; top: calc( 50% - 195px ); left: calc( 50% - 190px ); box-shadow: 0 0 15px 0 rgba(0, 0, 0, 0.2); }
			form .logo { display: block; width: 215px; height: 56px; background: url('/<?= $_SESSION['modulo'] ?>prosaude-logo.png') center center no-repeat; text-indent: -999px; overflow: hidden; margin: 0 auto; padding: 10px 0; }
			form .login-return { display: block; width: 100%; line-height: 20px; text-align: center; margin: 10px 0 -15px; font-weight: 700; color: #FFF; background: #F4896A; padding: 5px 0 10px; border-top-left-radius: 3px; border-top-right-radius: 3px; }
			form .login-inputs { display: block; width: calc( 100% - 6px ); border: 3px solid rgba(0, 0, 0, 0.15); border-radius: 5px; margin: 10px 0; }
			form .login-inputs input { display: block; width: 100%; height: 45px; background: #FFF; border: 0 none; text-align: center; line-height: 45px; color: #333; font-size: 16px; font-weight: 600; }
			form .login-inputs input:first-child { border-bottom: 1px solid #CCC; }
			form .submit-button { display: block; width: 100%; height: 45px; background: #F4896A; color: #FFF; text-align: center; line-height: 45px; border: 0 none; font-weight: 700; font-size: 20px; border-radius: 3px; }
			form .login-help { font-size: 16px; color: #666; text-align: center; margin: 20px 0; display: block; }
			form .login-infos { display: block; width: 100%; padding: 0; margin: 0; }
			form .login-infos li { float: left; width: 50%; height: 28px; line-height: 28px; text-align: center; list-style-type: none; }
		</style>
	</head>
	<body OnLoad="document.form.user.focus()">
		<form action="auth_pass.php" target="_self" method="post" name="form">
			<h1 class="logo">prosa&uacute;de</h1>
			<input type="hidden" value='1' name="erro-versao" id="erro-versao" />
			<?
			echo $teste;
			if($erno=="1") echo "<span class='login-return'>Usu&aacute;rio/Senha inv&aacute;lidos</span>";
			if($erno=="2") echo "<span class='login-return'>Este usu&aacute;rio j&aacute; est&aacute; em logado no sistema</span>";
			if($erno=="3") echo "<span class='login-return'>Sistema REINICIADO</span>";
			if($erno=="4") echo "<span class='login-return'>TIMEOUT: Conex&atilde;o encerrada por tempo de utiliza&ccedil;&atilde;o</span>";
			if($erno=="6") echo "<span class='login-return'>ERRO: O numero de serie do sistema foi alterado. <br />(Favor contactar o Financeiro)</span>";
			?>
			<div class="login-inputs">
				<input required type="text" name="user" placeholder="Login" />
				<input required type="password" name="pass" placeholder="Senha" />
			</div>
			<button class="submit-button" type="submit">Entrar</button>

			
			<span class="login-help">
				N&atilde;o tem acesso ao sistema? (44) 3305-1354
				<font color='#00000'>
				<br><?php echo 'Acessando pelo IP: <font color=red>'.$ip;?></font>
			</span>

			<ul class="login-infos">
				<li><a href="http://www.ibitech.com.br/"><img src="/<?= $_SESSION['modulo'] ?>ibitech-logo.png" /></a></li>
				<li>Vers&atilde;o <strong><?= file_get_contents("VERSAO") ?></strong></li>
			</ul>
		</form>
	</body>
</html>
