<style>
div{
  width: 750px;
  margin: 10px auto;
  text-align: center;
}

table{
  border: 1px solid #333;
  border-collapse: collapse;
  margin: 10px;
}

table tr th,
table tr td{
  border: 1px solid #333;
  padding: 4px 4px;
  text-align: center;
}



</style><?php 

$sexo = $_GET['sexo'];

$sql = "SELECT ite_itemdoexame,
		       vlr_valordereferencia,
		       ite_tipo_medida,
		       vlr_faixa_etaria 
		  FROM valoresdereferencia AS v
		  JOIN itensanalise AS i
		    ON i.ite_codigo=v.ite_codigo
		 WHERE v.txa_codigo=1
		   AND (vlr_sexo='$sexo' or vlr_sexo is null or vlr_sexo='')
		 ORDER BY ite_itemdoexame,
			  vlr_faixa_etaria_inicio nulls first,
			  vlr_faixa_etaria_fim";

$query = pg_query($sql);

echo "<div><h2>Valores de Referência para Hemograma - ".($sexo=="F"?"Feminino":"Masculino")."</h2>";
echo "<table>
		<tr>
			<th>Itens do Exame</th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
		</tr>\n";

$last = "";
while($r = pg_fetch_object($query)){
	if($last != $r->ite_itemdoexame){
		if(!empty($last))
			echo "\t</tr><tr>\n";
		
		$last = $r->ite_itemdoexame;
		echo "\t\t<th align=\"left\">".trim($last)." <small>({$r->ite_tipo_medida})</small></th>\n";
	} else {
		
	}
	echo "\t\t<td>{$r->vlr_valordereferencia}<input type=\"hidden\" value=\"{$r->vlr_faixa_etaria}\" /></td>\n";
}
echo "</table></div>";

?><script>

$("table tr").slice(2,3).find("td").each(function(index){
	var inicio = index+1;
	var fim = inicio+1;
	if(index == 5)
		fim = undefined;

	var vlr = $(this).find("input").val();
	
	$("table tr:first th").slice( inicio, fim ).html(vlr);
}); 

$("table tr:odd").css("background-color","#EEE");


</script>