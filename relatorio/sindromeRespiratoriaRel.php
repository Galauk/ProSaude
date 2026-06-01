<?php

require_once '../global.php';
include_once COMUM ."/library/php/funcoes.inc.php";	
set_time_limit(0);
$common = new commonClass();
$form = new classForm();
$table = new tableClass();
echo $common->incJquery();

$data_inicial = $_GET["di"];
$data_final = $_GET["df"];
cabecario_rel("Sindrome Respiratória",$data_inicial,$data_final);
$uni_codigo = $_GET['uni_codigo'];
$sql = "select to_char(pc_data,'DD/MM/YYYY') as data,
		       usu_nome,
		       extract(year from age(usu_datanasc)) as idade
		  from pre_consulta pc
		  join agendamento ag
		    on pc.age_codigo = ag.age_codigo
		  join usuario u
		    on u.usu_codigo = ag.usu_codigo
		 where pc_dados ilike '%tosse%'
		   and pc_temperatura >= 38.0
		   and pc_data >= '$data_inicial' 
		   and pc_data <= '$data_final'
		
		UNION ALL 
		
		select to_char(pc_data,'DD/MM/YYYY') as data,
		       usu_nome,
		       extract(year from age(usu_datanasc)) as idade
		  from pre_consulta pc
		  join agendamento ag
		    on pc.age_codigo = ag.age_codigo
		  join usuario u
		    on u.usu_codigo = ag.usu_codigo
		 where 
		   pc_dados ilike '%gargan%'
		   and pc_temperatura >= 38.0
		   and pc_data >= '$data_inicial' 
		   and pc_data <= '$data_final'
		 order by data";

//die($sql);
$query = pg_query($sql) or die($sql.pg_last_error());

if(!pg_num_rows($query)){
	echo "<em>Nenhum resultado encontrado.</em>";
} else {
		
	echo 
	"<table class=\"lista\">
		<tr>
			<th>
				DATA
			</th>
			<th>
				NOME
			</th>
			<th>
				NOME ABREVIADO
			</th>
			<th>
				IDADE
			</th>
		</tr>";
	
	while($reg = pg_fetch_array($query)){
		$exp = explode(" ", $reg[usu_nome]);
		$nome = "";
		foreach($exp as $val){
			$first = substr($val, 0, 1);
			$nome .= $first; 
		}
		echo 
		"<tr>
			<td>
				$reg[data]
			</td>
			<td>
				$reg[usu_nome]
			</td>
			<td>
				$nome
			</td>
			<td>
				$reg[idade]
			</td>
		</tr>";
	}
	echo"	
	</table>";
}
rodape_rel();