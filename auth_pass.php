<?php
	// die("oi");
	header ('Content-type: text/html; charset=UTF-8');
	include_once 'global.php'; 
	include_once $_SESSION[root].$_SESSION[comum].'library/php/funcoes.inc.php';
	/*if( $user=="root" and $pass=="saude")
	{
		header("Location: auth_root.php");
		exit;
	}*/

	//uni_codigo
	//$uni_codigo = "SELECT uni.uni_codigo FROM unidade";

	// Consulta validação de registro
	$dadosRegistro = "SELECT * FROM registro WHERE modulo = '014' and situacao = 'R' ORDER BY validade desc";
	$exeDadosRegistro = pg_query($dadosRegistro);
	$resultadoDadosRegistro = pg_fetch_array($exeDadosRegistro);
	
	// Data de liberação sem -
	$datalib = str_replace("-","",$resultadoDadosRegistro[dataliberacao]);
	// Data de Validade sem -
	$validade = str_replace("-","",$resultadoDadosRegistro[validade]);
	// Modúlo
	$mod = "014";
	// Nome.Modulo.Validade.DataLiberacao.Situacao.Codigo.Senha
	$string = $resultadoDadosRegistro[nome].$mod.$validade.$datalib.$resultadoDadosRegistro[situacao].$resultadoDadosRegistro[codigo].$resultadoDadosRegistro[senha];
	// Código hashalt
	$hsaltDb = trim($resultadoDadosRegistro[hashsalt]);
	// Pega a string e criptografa com hash(whirlpool)
	$hs = trim(hash('whirlpool',$string));
	
	// Validação
	
	$data = $resultadoDadosRegistro['validade'];
	$continua = $resultadoDadosRegistro['situacao'];
	//SELECT  PRA VERIFICAR OS DADOS INVALIDOS!
	
	$data = explode("-",$data);		
	$datad = $data[0]."/".$data[1]."/".$data[2];
	$datanova = date('y-m-d');
	$timestamphj = strtotime($datanova);
	$timestampval = strtotime($resultadoDadosRegistro['validade']);
	
	// Validação
	// Se a validação estiver vencida, altera a situação e o hash de comparação 
	// if ($timestamphj > $timestampval) {
	// 	$situacao = 'E';
	// 	$string = $resultadoDadosRegistro[nome].$mod.$validade.$datalib.$situacao.$resultadoDadosRegistro[codigo].$resultadoDadosRegistro[senha];
	// 	$hs = trim(hash( 'whirlpool' , $string ));
	// //	die("asfasdf: ".$string);
	// 	$up = pg_query("update registro set situacao = 'E',hashsalt='$hs'");
	// 	include '../WebSocialComum/autentificacao/autentificacao.php';
	// 	header("Location: auth.php?erno=5");
	// 	exit;	
	// }
	
// if($model=="bio") {
// 	$seleciona = "SELECT * 
// 					FROM usuarios 
// 				   WHERE usr_login = '$user' 
// 				     AND usr_senha = '$pass'";
// } else {
// }
	
	$loginUsr = $user;
	// die($loginUsr);
	$senhaAtual = strlen($_POST['pass']);
	// $cpfAtual = $user;

	// die($cpfAtual);
	// $recuperaId = null;

	// $recuperaId = "SELECT * FROM usuarios where usr_cpf = '$cpfAtual'";
	// echo "<pre>";var_dump($recuperaId->usr_codigo);die();
	
	$seleciona = "SELECT * FROM usuarios WHERE usr_login = '$_POST[user]' AND usr_senha = MD5('$_POST[pass]')";
	// echo "<pre>";var_dump($seleciona);die();


	$sql = pg_query($seleciona);
	
	if( pg_num_rows($sql) == 0 )
	{	
	   header("Location: auth.php?erno=1".(isset($_GET['popup'])?"&popup=1":""));
	   exit;
	}

	if ($senhaAtual <= 3) {
		include 'validarNivelDaSenha.php';
		exit();
	}

	if( pg_num_rows($sql) == 1 ){
		$rr = pg_fetch_array($sql);
		$Ver1 = pg_query("SELECT * FROM logon WHERE id_login = '$rr[usr_codigo]' AND dt_atualizacao>NOW()");
		//$res=pg_fetch_array($Ver1);
		$select = "SELECT * 
					 FROM usuarios_setores 
					WHERE usr_codigo = $rr[usr_codigo]";
		$query = pg_query($select);
		//Verifica de ele é ligado mais de um setor.
		

		if ($rr["usr_data_validade"] ==  date('Y-m-d', strtotime('today')) ){
			include 'validarNivelDaSenha.php';
		}
	

		if($_POST[setor] == "" && $_POST[uni_codigo] == "")
		{	
			$id_login = $rr[usr_codigo];
			$usr_login=$user;
			$usr_senha=$pass;
			include 'antesIndex.php';
				$sqlInsertLog = "INSERT INTO log (usr_codigo, log_data) VALUES ($id_login, NOW())";	
				pg_query($sqlInsertLog);
				//die("asfasd");
			exit;		
		}
		
		if(pg_num_rows($Ver1)=="0"){
			$sqlInsert = "INSERT INTO logon (id_login,
											 dt_entrada,
											 dt_atualizacao,
											 cod_setor,
											 esp_codigo,
											 uni_codigo) 
									 VALUES ($rr[usr_codigo],
									 		 NOW(),
									 		 NOW()+interval '120 minute',
									 		 ".intval($setor).",
									 		 ".intval($_POST[esp_codigo]).",
									 		 ".intval($_POST[uni_codigo]).")";
			$inslogon = pg_query($sqlInsert);
			//reglog($rr[usr_codigo],"Logando no Sistema");
	  
			//sessao
			//session_start();
			$_SESSION["login"];
			$_SESSION[md5("id")] = base64_encode($rr[usr_codigo]);
			//fim
		
			reglog($rr[usr_codigo],"Logando no Sistema");
			
			if(!empty($tp)) {
				$newlink = "zf/prontuario";
			} else {
				$newlink = $link2;
			}
	
			/*if( empty($open) ){
				echo "
		         <script type=\"text/javascript\">
		            //self.location.href=\"index.php?id_login=$rr[usr_codigo]\";
		            self.location.href='auth.php';
		            var w = screen.width;
		            var h = screen.heigth;
		            var j = window.open('index.php?id_login=$rr[usr_codigo]','Elotech','width=w,heigth=h,status=no,scrollbars=no,resizable=yes,location=no,fullscreen=yes,menubar=no');
		            setTimeout('j.focus()', 1);
		         </script>";
			}else{*/
				//echo "'index.php?id_login=$rr[usr_codigo]&link=$link'";
				
					
				echo "
		         <script type=\"text/javascript\">
		            document.location.href = 'index.php?id_login=$rr[usr_codigo]&link=$newlink".(isset($_GET['popup'])?"&popup=1":"")."';
		         </script>";
			#}
		} else {
			$id = $rr[usr_codigo];
			 $updateLogon = "UPDATE logon
                                           SET dt_entrada = 'NOW()',
                                               esp_codigo =  ".intval($_POST[esp_codigo]).",
                                               cod_setor = ".intval($setor).",
                                               uni_codigo =  ".intval($_POST[uni_codigo])."
                                         WHERE id_login = $rr[usr_codigo]";
			if($queryUpdateLogon = pg_query($updateLogon)){
			$_SESSION["login"];
                        $_SESSION[md5("id")] = base64_encode($rr[usr_codigo]);
              

			echo"	 <script type=\"text/javascript\">
                            document.location.href = 'index.php?id_login=$rr[usr_codigo]&link=$newlink".(isset($_GET['popup'])?"&popup=1":"")."';
                         </script>";
			}

		}
		$_SESSION['idUsuario'] = $rr[usr_codigo];
		$id = $_SESSION['idUsuario'];
		// die($id);
		// echo "<pre>"; print_r($_SESSION);

		//Pegar Ip do Visitante.
		$pegar_ip = $_SERVER['REMOTE_ADDR'];

		date_default_timezone_set('America/Sao_Paulo');
		
		//Dia da visita
		$dia_visita = date("d-m-Y");

		//Horario da visita
		$horas_visita = date("H:i:s");

		//Dados a ser inserido
		$dados = "O Ip: $pegar_ip visitou o site no dia $dia_visita as $horas_visita Horas. ";

		// echo "<pre>";var_dump($dados);die();

		//Inserindo Dados
		
		// echo "<pre>";var_dump($idUsuario);die();
		// $teste = "INSERT INTO logon usr_ip_acesso VALUES '$dados' where id_login = '$id' ";
		// pg_query($teste);
		// die($id);
		$usr_ip_acesso = "UPDATE logon set usr_ip_acesso = '$dados'  where id_login = '$id'";
		// echo "<pre>";print_r($usr_ip_acesso);die();
		$executa_usr_ip_acesso = pg_query($usr_ip_acesso);
	}

//$rr = pg_fetch_array($sql);

?>
