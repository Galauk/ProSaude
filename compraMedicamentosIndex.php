<script type="text/javascript" src="/WebSocialComum/library/js/jquery-1.6.2.min.js"></script>
<script src='/WebSocialComum/library/js/jquery.maskedinput-1.3.min.js'></script>
<script type='text/javascript' src='/WebSocialComum/library/js/tiny_mce/tiny_mce.js'></script>
<script type="text/javascript" src="/WebSocialSaude/lib/ui/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="/WebSocialComum/library/js/jquery.buscar.js"></script>
<script type="text/javascript" src="/WebSocialSaude/zf/public/js/jquery.validate.min.js"></script>
<link rel="stylesheet" href="/WebSocialSaude/lib/themes/base/jquery.ui.all.css">
<link rel="stylesheet" href="/WebSocialSaude/lib/themes/ui-lightness/jquery-ui-1.8.10.custom.css">
<link rel="stylesheet" href="/WebSocialSaude/lib/demos.css">

<?php
	include "global.php";
	$form = new classForm();
	$common = new commonClass();
	$table = new tableClass();
	
	echo $common->incJquery();
	echo $common->menuTab(ARRAY("Compra de produto"));
	echo $common->bodyTab('1');
		if($acao == "busca"){
			$where = "where usu_nome ilike '%$palavra%' OR to_char(comp_data,'DD/MM/YYYY') = '$palavra' OR for_nome ilike '%$palavra%'";
		}else{
			
		}
			$select = "SELECT comp_codigo,
							  for_nome,
							  usu_nome,
							  TO_CHAR(comp_data,'dd/mm/yyyy') as comp_data,
							  comp_data as comp_data2,
							  usr_nome
						 FROM compra_produto c
						 JOIN fornecedor f
						   ON c.for_codigo = f.for_codigo
						 JOIN usuario u
						   ON u.usu_codigo = c.usu_codigo
						 JOIN usuarios us
						   ON us.usr_codigo = c.usr_codigo
					   $where
						ORDER BY comp_codigo desc
						limit 15";
				$query = pg_query($select);
			//echo $select;
			
			echo $form->openForm("$PHP_SELF","POST","busca");
			echo $form->hiddenForm("acao","busca");
			echo $table->openTable();
				echo $table->criaLinha(array($common->commonButton("Adicionar","compraMedicamentos.php","adicionar.png"),
											 $common->commonButton("buscar",null,"buscar.png","onclick='document.busca.submit()'"),
											 $form->inputText("palavra","$valor",null,null,null,null,null,null,"S")),array(130));
			echo $table->closeTable();
				echo"
				<table class=lista>
						<tr>
							<th>Data</th>
							<th>Paciente</th>
							<th>Fornecedor</th>
							<th>Usuário</th>
							<th colspan=3>Opções</th>";
							echo"
						</tr>";
				$num = pg_num_rows($query);
				if($num >0){
				while($res=pg_fetch_array($query)){
					echo"
						<tr>
							<td>$res[comp_data]</td>
							<td>$res[usu_nome]</td>
							<td>$res[for_nome]</td>
							<td>$res[usr_nome]</td>
							<td width=30>"; echo $common->commonButton("Editar","compraMedicamentos.php?acao=addItem&comp_codigo=$res[comp_codigo]","editar_on.png"); echo"</td>
							<td width=30>"; echo $common->commonButton("Apagar","compraMedicamentosIndex.php?comp_codigo=$res[comp_codigo]&acao=del","apagar.png"); echo"</td>";
						echo"</tr>";
				}}
				else{
					echo"
						<tr>
							<td colspan='3'>Nenhum registro encontrado</td>						
						</tr>";
				}
				echo "</table>
			</form>";

			if($acao=="del") {
				echo $common->modalConfirm("Deseja realmente apagar todos os dados desde item?", "compraMedicamentosIndex.php?comp_codigo=$comp_codigo&acao=delete","compraMedicamentosIndex.php");
			}
			if($acao=="delete") {
				$del_1 = pg_query("delete from compra_produto_itens where comp_codigo = '$comp_codigo'");
				$del_2 = pg_query("delete from compra_produto where comp_codigo = '$comp_codigo'");
				
				if($del_1){
					echo $common->modalMsg("OK","Excluido com Sucesso","compraMedicamentosIndex.php");
				}else{
					echo $common->modalMsg("ERRO","Erro ao excluir","compraMedicamentosIndex.php",$del_1);
				}				
			}			
			
				
	echo $common->closeTab();	
?>