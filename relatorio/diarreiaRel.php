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
cabecario_rel("Relatório de Diarréia",$data_inicial,$data_final);
$uni_codigo = $_GET['uni_codigo'];
$sql = "SELECT distinct at.ate_codigo,
		       to_char(ate_data,'DD/MM/YYYY') as data,
		       usu_nome,
		       extract(year from age(usu_datanasc)) as idade,
		       rua_bairro,
		       io_observacao,
		       'N' as situa
		  FROM atendimento at
		  JOIN usuario u
		    ON u.usu_codigo = at.usu_codigo
		  LEFT join domicilio d
		    ON d.dom_codigo = u.dom_codigo
		  LEFT join rua r
		    ON r.rua_codigo = d.rua_codigo
		  LEFT join atendimento_internacao atin 
		    ON atin.ate_codigo = at.ate_codigo
		  LEFT join internacao_observacao io
		    ON io.io_codigo = atin.io_codigo
		 WHERE ate_reclamacao 
		 ILIKE '%GECA%' 
		   AND ate_data >= '$data_inicial' 
		   AND ate_data <= '$data_final'
		
		UNION ALL
		
		SELECT distinct at.ate_codigo,
		       to_char(ate_data,'DD/MM/YYYY') as data,
		       usu_nome,
		       extract(year from age(usu_datanasc)) as idade,
		       rua_bairro,
		       io_observacao,
		       'N' as situa
		  FROM atendimento at
		  JOIN usuario u
		    ON u.usu_codigo = at.usu_codigo
		  LEFT join domicilio d
		    ON d.dom_codigo = u.dom_codigo
		  LEFT join rua r
		    ON r.rua_codigo = d.rua_codigo
		  LEFT join atendimento_internacao atin 
		    ON atin.ate_codigo = at.ate_codigo
		  LEFT join internacao_observacao io
		    ON io.io_codigo = atin.io_codigo
		 WHERE ate_reclamacao ilike '%VOMITO%' 
		   AND ate_reclamacao ilike '%DIARREIA%'
		   AND ate_data >= '$data_inicial' 
		   AND ate_data <= '$data_final'
		   
		   UNION ALL
		
		SELECT distinct at.ate_codigo,
		       to_char(ate_data,'DD/MM/YYYY') as data,
		       usu_nome,
		       extract(year from age(usu_datanasc)) as idade,
		       rua_bairro,
		       io_observacao,
		       'S' as situa
		  FROM atendimento at
		  JOIN usuario u
		    ON u.usu_codigo = at.usu_codigo
		  LEFT join domicilio d
		    ON d.dom_codigo = u.dom_codigo
		  LEFT join rua r
		    ON r.rua_codigo = d.rua_codigo
		  LEFT join atendimento_internacao atin 
		    ON atin.ate_codigo = at.ate_codigo
		  LEFT join internacao_observacao io
		    ON io.io_codigo = atin.io_codigo
		 WHERE ate_reclamacao ilike '%VOMITO%' 
		   AND ate_reclamacao ilike '%DIARREIA%'
		   AND ate_reclamacao ilike '%SANG%'
		   AND ate_data >= '$data_inicial' 
		   AND ate_data <= '$data_final'
		ORDER BY data";

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
			<th>
				PRESCRICAO MEDICA
			</th>
			<th>
				BAIRRO
			</th>
			<th>
				SANGRAMENTO
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
			<td>
				$reg[io_observacao]
				&nbsp;
			</td>
			<td>
				$reg[rua_bairro]
				&nbsp;
			</td>
			<td>
				$reg[situa]
			</td>
		</tr>";
	}
	echo"	
	</table>";
}
rodape_rel();