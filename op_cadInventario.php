<?
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
    cabecario();

	$id_login = $_POST["id_login"];
	$data = $_POST["data"];
	$gru_codigo = $_POST["gru_codigo"];
	$set_codigo = $_POST["set_codigo"];
	$responsavel = strtoupper($_POST["responsavel"]);
	$equipe = strtoupper($_POST["equipe"]);

	$select = "select a.inv_codigo 
				 from inventario a 
				where inv_data = '$data' 
				  and gru_codigo = $gru_codigo 
				  and set_codigo = $set_codigo";

	$exec_select = pg_query($select);
	$ip = getenv("REMOTE_ADDR"); //linha que captura o ip do usuario.
	
	if(pg_num_rows($exec_select) == 0)
	{
		//tem que mudar o inv_ip
		//tem que ver se o inv_data_digitacao Ã© o mesmo que o inv_data

		$insert = "insert into inventario 
							(inv_data, 
							 set_codigo, 
							 gru_codigo, 
							 inv_responsavel, 
							 inv_equipe, 
							 usr_codigo, 
							 inv_data_digitacao, 
							 inv_ip) 
						  values 
						    ('$data', 
							 $set_codigo, 
							 $gru_codigo, 
							 '$responsavel', 
							 '$equipe', 
							 $id_login, 
							 '$data', 
							 '$ip')";

		$exec_insert = pg_query($insert);

		$select = "select a.inv_codigo 
					 from inventario a 
					where inv_data = '$data' 
					  and gru_codigo = $gru_codigo 
					  and set_codigo = $set_codigo";

		$exec_select = pg_query($select);

		$linha = pg_fetch_array($exec_select);

		$inv_codigo = $linha[0];

		$sql = "SELECT produto_setor.set_codigo as codsetor, 
					   set_nome as nomesetor, 
					   produto.pro_codigo, 
					   produto.pro_nome, 
					   grupo.gru_codigo,
					   gru_nome
				  FROM produto_setor, 
				  	   produto, 
					   grupo, 
					   setor
				 WHERE produto_setor.pro_codigo = produto.pro_codigo
				   AND produto.gru_codigo = grupo.gru_codigo
				   AND produto_setor.set_codigo = setor.set_codigo
				   AND produto_setor.set_codigo = $set_codigo
				   AND grupo.gru_codigo = $gru_codigo
				 ORDER BY pro_nome";

		$exec = pg_query($sql);


		while($linha = pg_fetch_array($exec))
		{
			//tem que mudar o invp_datahora e o invp_ip

			$sel = pg_query("select now()");
			$l = pg_fetch_array($sel);
			$invp_datahora = $l[0];

			$insert = "insert into inventario_produto 
								(inv_codigo, 
								 pro_codigo, 
								 usr_codigo, 
								 invp_datahora, 
								 invp_ip, 
								 invp_status) 
							  values 
							    ($inv_codigo, 
								 $linha[pro_codigo], 
								 $id_login, 
								 '$invp_datahora', 
								 '$ip', 
								 'A')";

			$exec_insert = pg_query($insert);

		}
		if($exec_insert == true)
		{
			msg($id_login,'add',$exec_insert);
		}
	} else {
		echo "<script>alert('Já existe inventário para esta data, setor e grupo');</script>";
	}
       echo "<SCRIPT LANGUAGE=\"JavaScript\">
                setTimeout(\"location='op_buscarInventario.php?inv_codigo=$inv_codigo&id_login=$id_login'\", 2000);
           </SCRIPT>";

?>
