<?php
	
 require_once 'global.php';

 $Age = pg_fetch_array(pg_query("select * from agendamento where age_codigo='$age_codigo'"));
 $usu_codigo = $Age[usu_codigo];
 $usr_codigo = $Age[med_codigo];
 $uni_codigo = $Age[uni_codigo];
 $medInfo=pg_fetch_array(pg_query("select * from usuarios where usr_codigo='$usr_codigo'"));
 $uniInfo=pg_fetch_array(pg_query("select * from unidade where uni_codigo='$uni_codigo'"));
 $usuInfo=pg_fetch_array(pg_query("select * from usuario where usu_codigo='$usu_codigo'"));
 $secInfo=pg_fetch_array(pg_query("select * from secretaria WHERE tipo_secretaria = 'SAU' limit 1"));
 
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
 
 if($tp_action=="externo") {
   $sql = pg_query("select desc_produto AS pro_nome,irec_recomendacao,irec_quantidade,irec_codigo
           from itemreceita
           ,receita
           where itemreceita.rec_codigo = receita.rec_codigo
                 and receita.ate_codigo = $ate_codigo
                 and  receita.rec_codigo = $receita
  	         and  receita.rec_tipo = '$tp_action'
                 and  receita.rec_finalizada = 'N'");
} else {
  $sql = pg_query("select irec_codigo, itemreceita.pro_codigo, pro_nome, irec_recomendacao, irec_quantidade
                  from itemreceita, produto, receita
                  where itemreceita.pro_codigo = produto.pro_codigo
                  and  itemreceita.rec_codigo = receita.rec_codigo
                  and  receita.rec_codigo = $receita");
}
 
 ?><html>
	 <head>
	 		<title>Receita de Medicamentos</title>
	 		<link href='receita.css' rel='stylesheet' type='text/css'>
	 </head>
	 <body onload="window.print();">
	 	<div id="page">
	 		<div id="header">
	 			<div id="header_logo">
	 				<img src="<?=LINKSAUDE?>/imgs/brasao.jpg" title="Logo Prefeitura" />
	 			</div>
	 			<div id="header_dados">
	 				<div id="sec_nome"><?=$uniInfo['uni_desc'];?></div>
	 				<div id="pref_nome"><?=$secInfo['nome_secretaria'];?></div>
	 			</div>
	 			<div id="header_barcode">
	 				<img src="codigo.php?id_login=<?=$id_login;?>&age_codigo=<?=$receita;?>&lw=1&hi=18" alt="Código de Barras: <?=$receita;?>" title="Código de Barras: <?=$receita;?>" />
	 			</div>
	 			<div class="clear"></div>
	 		</div>
	 		
	 		<div id="dados_pac">
	 			<div id="pac_nome"><?=$usuInfo['usu_nome'];?></div>
	 			<div id="pac_end"><?=$endereco;?></div>
	 		</div>
	 		
	 		<div id="receita">
	 			<div id="rec_titulo">
	 				<div class="left">Medicamento</div>
	 				<div class="right">Quant.</div>
	 				<div class="clear"></div>
	 			</div>
	 			
	 			<?php while($row=pg_fetch_array($sql)):?>
	 			<div class="medItem">
 					<div class="medTitulo"><?=$row['pro_nome'];?></div>
 					<div class="medQtd"><?=$row['irec_quantidade'];?></div>	
	 				<div class="clear"></div>
 					<div class="medUso"><?=nl2br($row['irec_recomendacao']);?></div>
	 			</div>
	 			<?php endwhile;?>	 		
	 		</div>
	 		
	 		<div id="medico">
	 			<h2><?=$medInfo['usr_nome']?></h2>
	 			<h3>CRM: <?=$medInfo['usr_num_conselho']?></h3>
	 		</div>
	 		
	 		<div id="footer">
	 			<?=$uniInfo['uni_desc'];?> - <?=$uniInfo['uni_endereco'];?>
	 		</div>
	 	</div>
	 </body>
 </html>
 