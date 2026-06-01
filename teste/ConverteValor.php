
<link href="../estilo.css" rel="stylesheet" type="text/css">
		<link href="../css/estiloForm.css" rel="stylesheet" type="text/css" />
		<link href="../css/estiloCommon.css" rel="stylesheet" type="text/css" />

<? include '../global.php';
	$sql = "SELECT * from produto  where pro_codigo in(50011,50006,50012,50014,50015,50016,50017,50018,50022,50023,50030,50037,50041,50057,50073,50082,50105,50116,50119,50120,50123,50131,50164,50169,50170,50172,50173,50174,50175,50176,50180,50181,50188,50195,50199,50215,50231,50240,50263,50274,50277,50278,50281,50289,50321,50333,50334,50353,50354,50355,50360,50361,50362,50363,50364,50368,50371,50372,50373,50356,50357,50019,50020)";
	$query = pg_query($sql);
		
	while($r = pg_fetch_array($query)){
		
		$sqlProdutos = "select * from produtos where cod_prod = $r[cod_prod]";
		$queryProdutos = pg_query($sqlProdutos);
		while ($res = pg_fetch_array($queryProdutos)){
			if($res[valor]){
			$update = "UPDATE oferta_solicitacao_itens set osi_valor = '$res[valor]' where pro_codigo = $r[pro_codigo] and osi_valor is null;";
			echo $update."<br>";
			}
		}
	
	}
	
	echo $cont;
?>
	

