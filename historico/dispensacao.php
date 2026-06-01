<?php

@header('Content-Type: text/html; charset=ISO-8859-1');
require_once '../global.php';

$usu_codigo = $_REQUEST['usu_codigo'];
$includes = (isset($_REQUEST['includes']) && $_REQUEST['includes']);
$dias = $_REQUEST['dias'];
if(!empty($dias)){
	$condicao = " AND m.mov_data > CURRENT_DATE - $dias";
	$msg = "O paciente retirou medicamento(s) nos ultimos $dias dias !!";
}
$sql = "SELECT TO_CHAR(m.mov_data,'DD/MM/YYYY') AS mov_data,
			   u.uni_desc,
		       p.pro_nome,
		       i.ite_quantidade,
		       usr.usr_nome,
		       TO_CHAR(m.mov_data + i.ite_duracao,'DD/MM/YYYY')  AS duracao
		  FROM movimento AS m
		  JOIN itens_movimento AS i
		    ON i.mov_codigo=m.mov_codigo
		  JOIN produto AS p
		    ON p.pro_codigo=i.pro_codigo
		  JOIN setor AS S
		    ON s.set_codigo=m.set_saida
		  JOIN unidade AS u
		    ON u.uni_codigo=s.uni_codigo
		  JOIN usuarios AS usr
		    ON usr.usr_codigo=m.usr_codigo
		 WHERE mov_tipo='S'
		   AND usu_codigo=$usu_codigo
		   $condicao
		 ORDER BY m.mov_data DESC,
		          m.mov_codigo DESC";
$query = pg_query($sql);
//die($sql);
fdebug($sql);
$common = new commonClass($includes);
if($includes){
	echo "<link type=\"text/css\" href=\"".LINKSAUDE."/estiloPE.css\" rel=\"stylesheet\"/>";
	echo $common->incJquery();
}

    	if(pg_num_rows($query)): ?>
<?=$msg ?>
<table class="grid ui-widget ui-widget-content ui-corner-all">
	<tr class="ui-widget-header">
		<th>Data</th>
		<th>Produto</th>
		<th>Quant.</th>
		<th>Unidade</th>
		<th>Usuário</th>
		<th>Previsão de Termino</th>
	</tr>
	<?php while($r = pg_fetch_array($query)): ?>
	<tr>
		<td class="ui-widget ui-widget-content"><?=$r['mov_data'];?></td>
		<td class="ui-widget ui-widget-content"><?=$r['pro_nome'];?></td>
		<td class="ui-widget ui-widget-content" align="right"><?=number_format($r['ite_quantidade'],0,",",".");?></td>
		<td class="ui-widget ui-widget-content"><?=$r['uni_desc'];?></td>
		<td class="ui-widget ui-widget-content"><?=$r['usr_nome'];?></td>
		<td class="ui-widget ui-widget-content"><?=$r['duracao'];?></td>
	</tr>
	<?php endwhile; ?>
</table>
	<?php else: ?>
<em>Não há histórico de dispensação de medicamentos</em>
	<?php endif;?>