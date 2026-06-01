<?php
	
 require_once 'global.php';
 setError(1);
 
 $query = pg_query("SELECT * FROM secretaria WHERE tipo_secretaria = 'SAU'");
 $sec = pg_fetch_array($query);
 
 $sql = "SELECT * FROM usuario WHERE usu_codigo='$usu_codigo'";
 $query = pg_query($sql);
 $usu=pg_fetch_array($query);
 
 $sql = "SELECT usr_nome FROM usuarios WHERE usr_codigo=".$_SESSION['id_login'];
 $query = pg_query($sql);
 $usr = pg_fetch_array($query);
 
 $sql = "SELECT uni_desc,uni_endereco 
           FROM unidade AS uni
           JOIN logon AS log
             ON log.uni_codigo=uni.uni_codigo
            AND id_login=".$_SESSION['id_login'];

 $query = pg_query($sql);
 $uni = pg_fetch_array($query);
 
 $end = array();
 $end []= $usuInfo['usu_end_rua'];
 $end []= $usuInfo['usu_end_nr'];
 $end []= $usuInfo['usu_end_compl'];
 $end []= $usuInfo['usu_end_bairro'];
 $end []= $usuInfo['usu_end_cidade'];
 
 foreach($end as $k => $item){
 	if( empty($item) )
 		unset($end[$k]);
 }

 $endereco = implode(", ",$end);
 
 ?><html>
	 <head>
	 		<title>Atestado de Vacinas</title>
	 		<link href='receita.css' rel='stylesheet' type='text/css'>
	 </head>
	 <body onload="window.print();">
	 	<div id="page">
	 		<div id="header">
	 			<div id="header_logo">
	 				<img src="<?=LINKSAUDE?>/imgs/brasao.jpg" title="Logo Prefeitura" />
	 			</div>
	 			<div id="header_dados">
	 				<div id="sec_nome">Prefeitura Municipal de <?=$sec['nome_cidade'];?></div>
	 				<div id="pref_nome"><?=$sec['nome_secretaria'];?></div>
	 			</div>
	 			<div id="header_barcode">
	 				
	 			</div>
	 			<div class="clear"></div>
	 		</div>
	 		
	 		<div id="dados_pac">
	 			<div id="pac_nome"><?=$usu['usu_nome'];?></div>
	 			<div id="pac_end"><?=$endereco;?></div>
	 		</div>
	 		
	 		<div id="receita">
	 			<div id="rec_titulo">
	 				<div class="left">Atestado de Vacinas</div>
	 				<div class="clear"></div>
	 			</div>
	 			
	 			<div class="medItem">
	 				<p>Atestamos para os devidos fins que de acordo com o calendário vacinal vigente o usuário 
	 				<strong><?=$usu['usu_nome'];?></strong> está com suas vacinas em dia até: <strong><?=$data?></strong></p>
	 			</div>		
	 		</div>
	 		
	 		<div id="medico">
	 			<h2><?=$usr['usr_nome'];?></h2>
	 		</div>
	 		
	 		<div id="footer">
	 			<?=$uni['uni_desc'];?> - <?=$uni['uni_endereco'];?>
	 		</div>
	 	</div>
	 </body>
 </html>