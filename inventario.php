<?
	include_once "authlib.inc.php";
	verauth($id_login);

	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
	
	$common = new commonClass();
	echo $common->incJquery();
	$form = new classForm();
	$table = new tableClass();
//------------------------------------------------------------------>


	reglog($id_login,"Acessando Materiais");
//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>

	$data = date("d/m/Y");
	
	echo $common->menuTab(array("Opções de Inventário"));
	echo $common->bodyTab('1');
	echo "
		<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
			<tr>
				<td width=74>";
					if(chmodbtn($id_login, "listar_if", 'inventario.php'))
					{
						//echo ChmodBtn($id_login,'lista_proc_contagem','relatorio/EstInventario.php?acao=estoque_inventario');
						echo "<a href='relatorio/EstInventario.php?acao=estoque_inventario&id_login=$id_login'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/lista_proc_contagem_on.jpg' border='0'></a>";
						//echo chmodbtn($id_login, 'lista_proc_contagem', 'inventario.php');
					} else {
						echo "<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/lista_proc_contagem_off.jpg'>";
					}
			echo "
				</td>
				<td width=101>";
					if(chmodbtn($id_login, "adicionar_if", 'inventario.php'))
					{
						//echo ChmodBtn($id_login,'adicionar_inventario','cadInventario.php?acao=inserir');
						echo "<a href='cadInventario.php?acao=inserir&id_login=$id_login'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_inventario_on.jpg' border='0'></a>";
						//echo chmodbtn($id_login, 'adicionar_inventario', 'inventario.php');
					} else {
						echo "<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_inventario_off.jpg'>";
					}
			echo "
				</td>
				<td width=58>";
					if(chmodbtn($id_login, "editar_if", 'inventario.php'))
					{
						//echo ChmodBtn($id_login,'digitacao_contagem','cadInventario.php?acao=buscar');
						echo "<a href='cadInventario.php?acao=buscar&id_login=$id_login'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/digitacao_contagem_on.jpg' border='0'></a>";
						//echo chmodbtn($id_login, 'digitacao_contagem', 'inventario.php');
					} else {
						echo "<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/digitacao_contagem_off.jpg'>";
					}
			echo "
				</td>
				<td width=101>";
					if(chmodbtn($id_login, "listar_if", 'inventario.php'))
					{
						//echo ChmodBtn($id_login,'rel_inventario','cadInventario.php?acao=relatorio');
						echo "<a href='cadInventario.php?acao=relatorio&id_login=$id_login'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/rel_inventario_on.jpg' border='0'></a>";
						//echo chmodbtn($id_login, 'rel_inventario', 'inventario.php');
					} else {
						echo "<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/rel_inventario_off.jpg'>";
					}
			echo "
				</td>
			</tr>
			<tr><td width=89>";
				if(chmodbtn($id_login, "listar_if", 'inventario.php'))
					{
						echo "<a href='cadInventario.php?acao=acuracia&id_login=$id_login'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/rel_acuracia_on.jpg' border='0'></a>";
					} else {
						echo "<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/rel_acuracia_off.jpg'>";
					}
				echo "<!--<td width=89>".ChmodBtn($id_login,'gerar_movimentacao','movimentacao_inventario.php?acao=form_consolid')."</td>-->
           		";
					//echo ChmodBtn($id_login,'rel_acuracia','movimentacao_inventario.php?acao=form_consolid');
			echo "
				</td>
				<td width=79><a href=materiais.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
			</tr>
		</table>";
	echo $common->closeTab();
?>

