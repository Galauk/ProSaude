<?php
	
 require_once 'global.php';
 
 $enc_codigo = $_GET['enc_codigo'];
 
 $query = pg_query("SELECT ate.ate_codigo,
 						   usr.usr_nome, 
 						   usr.usr_num_conselho,
 						   esp.esp_nome, 
 						   enc.enc_descricao,
 						   usu.usu_nome,
 						   usu.usu_sexo,
 						   usu.usu_end_rua,
 						   usu.usu_end_nr,
 						   usu.usu_end_compl,
 						   usu.usu_end_cidade,
 						   to_char(usu_datanasc,'dd/mm/yyyy') as usu_datanasc,
 						   uni.uni_desc,
 						   uni.uni_endereco,
 						   sec.nome_secretaria
 					  FROM encaminhamento AS enc
					  JOIN atendimento AS ate
					    ON ate.ate_codigo=enc.ate_codigo
					  JOIN usuarios AS usr
					    ON usr.usr_codigo=ate.med_codigo
					  JOIN especialidade AS esp
					    ON esp.esp_codigo=enc.esp_codigo
					  JOIN usuario AS usu
					    ON usu.usu_codigo=ate.usu_codigo
					  JOIN unidade AS uni
					    ON uni.uni_codigo=ate.uni_codigo
					  JOIN secretaria AS sec
					    ON 1=1
					 WHERE enc.enc_codigo=$enc_codigo");
 
 $row = pg_fetch_array($query);
 
 $genero = $row['usu_sexo']=="M"?"o":"a";
 
 $end = array();
 $end []= $row['usu_end_rua'];
 $end []= $row['usu_end_nr'];
 $end []= $row['usu_end_compl'];
 $end []= $row['usu_end_bairro'];
 $end []= $row['usu_end_cidade'];
 
 foreach($end as $k => $item){
 	if( empty($item) )
 		unset($end[$k]);
 }

 $endereco = implode(", ",$end); 
 
 ?><html>
	 <head>
	 		<title>Encaminhamento</title>
	 		<link href='receita.css' rel='stylesheet' type='text/css'>
	 </head>
	 <body onload="exit;window.print();">
	 	<div id="page">
	 		<div id="header">
	 			<div id="header_logo">
	 				<img src="<?=LINKSAUDE?>/imgs/brasao.jpg" title="Logo Prefeitura" />
	 			</div>
	 			<div id="header_dados">
	 				<div id="sec_nome"><?=$row['uni_desc'];?></div>
	 				<div id="pref_nome"><?=$row['nome_secretaria'];?></div>
	 			</div>
	 			<div id="header_barcode">
	 				<img src="codigo.php?id_login=<?=$id_login;?>&age_codigo=<?=$enc_codigo;?>&lw=1&hi=18" alt="Código de Barras: <?=$receita;?>" title="Código de Barras: <?=$receita;?>" />
	 			</div>
	 			<div class="clear"></div>
	 		</div>
	 		
	 		<div id="dados_pac">
	 			<div id="pac_nome"><?=$row['usu_nome'];?></div>
	 			<div id="pac_end"><?=$endereco;?></div>
	 		</div>
	 		
	 		<div id="receita">
				<h2 class="titulo">Encaminhamento</h2>
				<p>Encaminhamento d<?=$genero;?> paciente: <strong><?=$row['usu_nome'];?></strong>, nascid<?=$genero;?> em: <strong><?=$row['usu_datanasc'];?></strong> para a especialidade de: <strong><?=$row['esp_nome'];?></strong></p>
				
				<?php if( !empty($row['enc_descricao'])) :?>
				<h2 class="titulo">Observações</h2>
				<p><?=$row['enc_descricao'];?></p>
				<?php endif; ?>
	 		
	 		</div>
	 		
	 		<div id="medico">
	 			<h2><?=$row['usr_nome']?></h2>
	 			<h3>CRM: <?=$row['usr_num_conselho']?></h3>
	 		</div>
	 		
	 		<div id="footer">
	 			<?=$row['uni_desc'];?> - <?=$row['uni_endereco'];?>
	 		</div>
	 	</div>
	 </body>
 </html>
 