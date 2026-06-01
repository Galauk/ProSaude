<?php header ('Content-type: text/html; charset=UTF-8'); ?>
<?php  
	// die($loginUsr);
?>
<!DOCTYPE html>
<html lang="en" style="background: url('/WebSocialSaude/login-background.jpg') center center  no-repeat; background-size: cover; height: 100%;">
<head>
	<meta charset="UTF-8">
	<title>Alteração de senha - ProSaude - Sistema de Gerenciamento em Saúde</title>
	<link rel="shortcut icon" href="http://localhost:8083/WebSocialComum/imgsBotoes/mini_logo_elotech.png">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>
<body>
	<div id="modal" name = "modal" class="modal" style="">
		<form action="" method="POST" style="float: left; width: 340px;padding: 20px;background: rgba(255, 255, 255, 0.5);border-radius: 10px;position: absolute;top: calc( 50% - 195px );left: calc( 50% - 190px );box-shadow: 0 0 15px 0 rgba(0, 0, 0, 0.2)">
		<h1 class="logo" style="display: block;width: 215px;height: 56px;background: url(/WebSocialSaude/prosaude-logo.png) center center no-repeat;text-indent: -999px;overflow: hidden;margin: 0 auto;padding: 10px 0;">prosaúde</h1>
			<input type="password" id="senhaUm" placeholder="Nova senha" style="width: 100%;height: 45px;background: #FFF;border: 0 none;text-align: center;line-height: 45px;color: #333;font-size: 16px;font-weight: 600;border-bottom: 1px black solid;">
			<input type="password" id="senhaDois" placeholder="Repetir nova senha" style="display: block;width: 100%;height: 45px;background: #FFF;border: 0 none;text-align: center;line-height: 45px;color: #333;font-size: 16px;font-weight: 600;">
			<!-- <input type="hidden" value=""> -->
			<button style="width: 100%;height: 45px;background: #F4896A;color: #FFF;text-align: center;line-height: 45px;border: 0 none;font-weight: 700;font-size: 20px;border-radius: 3px;" onclick="validarSenha()" type="button">Salvar Alteração</button>

		</form>
	</div>

	<script>
		function validarSenha () {
			var senhaUm = document.getElementById("senhaUm").value;
			var senhaDois = document.getElementById("senhaDois").value;
			var regex = '(?:([a-zA-Z]+|)\\d+([a-zA-Z\\d]+|))';
			var reg = new RegExp(regex);
			
			if (senhaUm != senhaDois) {
				alert("Senhas divergentes")
				return false;
			} else{
				if (senhaUm.length < 6) {
			 		alert("A senha deve conter no minímo 6 digitos!");
				} else if(reg.test(senhaUm) == false){
				    alert("A senha deve conter no mínimo um número !");
				    return false;
				} else{
					$.ajax({
			            url: 'salvarNovaSenha.php',
			            type : "POST",
			            data: {novaSenha : senhaUm, usr_login : '<?=$loginUsr?>'},
			            success: function (r) {
							console.log(JSON.parse(r).URL);
							var url = JSON.parse(r).URL
							if(url) {
								alert("Nova senha alterada com sucesso !");
								window.location.href = url
							} else {
								alert("Houve um problema com a alteração da senha. \n Tente novamente.")
							}

	    		        }
        			});
				}
			}
		}

	</script>

</body>
</html>
