<?php
	
 require_once 'global.php';
 
 $age_codigo = $_GET['age_codigo'];
 
 $query = pg_query("SELECT usr.usr_nome, 
						   usu.usu_nome,
						   usu.usu_codigo,
						   usu.usu_end_rua,
						   usu.usu_end_nr,
						   usu.usu_end_compl,
						   usu.usu_end_cidade,
						   to_char(age.age_data,'dd/mm/yyyy') as age_data,
						   esp.esp_nome,
						   uni.uni_desc,
						   uni.uni_endereco,
						   sec.nome_secretaria FROM agendamento AS age
				      JOIN usuarios AS usr
						ON usr.usr_codigo=age.med_codigo
				      JOIN usuario AS usu
				        ON usu.usu_codigo=age.usu_codigo
				      JOIN unidade AS uni
				        ON uni.uni_codigo=age.uni_codigo
				      JOIN especialidade AS esp
				        ON esp.esp_codigo=age.esp_codigo
				      JOIN secretaria AS sec
				        ON 1=1
			 		 WHERE age.age_codigo=$age_codigo");
					 
 $row = pg_fetch_array($query);
 
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
	 		<title>Agendamento</title>
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
	 				<img src="codigo.php?id_login=<?=$id_login;?>&age_codigo=<?=$age_codigo;?>&lw=1&hi=18" alt="Código de Barras: <?=$age_codigo;?>" title="Código de Barras: <?=$age_codigo;?>" />
	 			</div>
	 			<div class="clear"></div>
	 		</div>
	 		
	 		<div id="dados_pac">
	 			<div id="pac_nome"><?=$row['usu_nome'];?></div>
	 			<div id="pac_end"><?=$endereco;?></div>
	 		</div>
	 		
	 		<div id="receita">
				<h2 class="titulo">Agendamento</h2>
				<label>Especilidade:</label><span><?=$row['esp_nome'];?></span><br />
				<label>Data:</label><span><?=$row['age_data'];?></span><br />
				<label>Profissional:</label><span><?=$row['usr_nome'];?></span><br />
				<label>Procedimento:</label><span>Consulta médica</span><br />
				<label>Num. Paciente:</label><span><?=$row['usu_codigo'];?></span><br />
				<div style="margin: 30px 0 0 0;"></div>
				<h2 class="titulo">Observações</h2>
				<p>O não comparecimento no dia e hora marcado invalidará a sua consulta.</p>
	 		
	 		</div>
	 		
	 		<div id="footer">
	 			<?=$row['uni_desc'];?> - <?=$row['uni_endereco'];?>
	 		</div>
	 	</div>
	 </body>
 </html>
 