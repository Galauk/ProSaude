<?php

	include_once 'global.php'; 
	include_once $_SESSION[root].$_SESSION[comum].'library/php/funcoes.inc.php';
	if( $user=="root" and $pass=="saude")
	{
		header("Location: auth_root.php");
		exit;
	}
	$dadosRegistro = "SELECT * 
						FROM registro 
					   WHERE modulo = '014' and situacao = 'R'";
	$exeDadosRegistro = pg_query($dadosRegistro);
	$resultadoDadosRegistro = pg_fetch_array($exeDadosRegistro);
	$datalib = str_replace("-","",$resultadoDadosRegistro[dataliberacao]);
	$validade = str_replace("-","",$resultadoDadosRegistro[validade]);
	$mod = "014";
	$string = $resultadoDadosRegistro[nome].$mod.$validade.$datalib.$resultadoDadosRegistro[situacao].$resultadoDadosRegistro[codigo].$resultadoDadosRegistro[senha];
	$hsaltDb = trim($resultadoDadosRegistro[hashsalt]);
	$hs = trim(hash( 'whirlpool' , $string ));
	
	
	/*if($hs!=$hsaltDb) {
		include '../WebSocialComum/autentificacao/autentificacao.php';
		header("Location: auth.php?erno=6");
		exit;	
	}*/
	
	$data = $resultadoDadosRegistro['validade'];
	$continua = $resultadoDadosRegistro['situacao'];
	//SELECT  PRA VERIFICAR OS DADOS INVALIDOS!
	
	$data = explode("-",$data);		
	$datad = $data[0]."/".$data[1]."/".$data[2];
	$datanova = date('y-m-d');
	$timestamphj = strtotime($datanova);
	$timestampval = strtotime($resultadoDadosRegistro['validade']);
	/*if ($timestamphj > $timestampval)
	{
		$situacao = 'E';
		$string = $resultadoDadosRegistro[nome].$mod.$validade.$datalib.$situacao.$resultadoDadosRegistro[codigo].$resultadoDadosRegistro[senha];
		$hs = trim(hash( 'whirlpool' , $string ));
		
		$up = pg_query("update registro set situacao = 'E',hashsalt='$hs'");
		include '../WebSocialComum/autentificacao/autentificacao.php';
		header("Location: auth.php?erno=5");
		exit;	
	}*/
	
if($model=="bio") {
	$seleciona = "SELECT * 
					FROM usuarios 
				   WHERE usr_login = '$user' 
				     AND usr_senha = '$pass'";
} else {
	$seleciona = "SELECT * 
					FROM usuarios 
				   WHERE usr_login = '$_POST[user]' 
				     AND usr_senha = MD5('$_POST[pass]')";
}
	$sql = pg_query($seleciona);
	
	if( pg_num_rows($sql) == 0 )
	{	
	   header("Location: auth.php?erno=1".(isset($_GET['popup'])?"&popup=1":""));
	   exit;
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
		
		if($_POST[setor] == "")
		{	
			$id_login = $rr[usr_codigo];
			$usr_login=$user;
			$usr_senha=$pass;
			
			include 'antesIndex.php';
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
			session_register("login");
			$_SESSION[md5("id")] = base64_encode($rr[usr_codigo]);
			//fim
		
			reglog($rr[usr_codigo],"Logando no Sistema");
			
			if(!empty($tp)) {
				$newlink = "zf/prontuario";
			} else {
				$newlink = $link2;
			}
	
			if( empty($open) ){
				echo "
		         <script type=\"text/javascript\">
		            //self.location.href=\"index.php?id_login=$rr[usr_codigo]\";
		            self.location.href='auth.php';
		            var w = screen.width;
		            var h = screen.heigth;
		            var j = window.open('index.php?id_login=$rr[usr_codigo]','Elotech','width=w,heigth=h,status=no,scrollbars=no,resizable=yes,location=no,fullscreen=yes,menubar=no');
		            setTimeout('j.focus()', 1);
		         </script>";
			}else{
				//echo "'index.php?id_login=$rr[usr_codigo]&link=$link'";
				
					
				echo "
		         <script type=\"text/javascript\">
		            document.location.href = 'index.php?id_login=$rr[usr_codigo]&link=$newlink".(isset($_GET['popup'])?"&popup=1":"")."';
		         </script>";
			}
		} else {
			$id = $rr[usr_codigo];
			 $updateLogon = "UPDATE logon
                                           SET dt_entrada = 'NOW()',
                                               esp_codigo =  ".intval($_POST[esp_codigo]).",
                                               cod_setor = ".intval($setor).",
                                               uni_codigo =  ".intval($_POST[uni_codigo])."
                                         WHERE id_login = $rr[usr_codigo]";
			if($queryUpdateLogon = pg_query($updateLogon)){
			session_register("login");
                        $_SESSION[md5("id")] = base64_encode($rr[usr_codigo]);
              

			echo"	 <script type=\"text/javascript\">
                            document.location.href = 'index.php?id_login=$rr[usr_codigo]&link=$newlink".(isset($_GET['popup'])?"&popup=1":"")."';
                         </script>";
			}
			

		}
	}

?>
