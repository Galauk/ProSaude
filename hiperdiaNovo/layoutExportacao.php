<script>
	function download(nome){
	//alert('asd');
		window.location = "../lib/baixarArquivo.php?arquivo=../hiperdiaNovo/arquivosExportacao/"+nome+".apl";
	}
</script>
<?
session_start();
require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

include_once $_SESSION[root].$_SESSION[modulo]."hiperdiaNovo/funcaoBuscaUsuario.php";	
$common = new commonClass();
$form = new classForm();
$table = new tableClass();

echo $common->incJquery();
echo $common->menuTab(array("Exporta&ccedil;&atilde;o"));
	echo $common->bodyTab();
		echo $form->openForm("$PHP_SELF","POST","busca");
		echo $form->hiddenForm("acao","buscar");
		echo $table->openTable();
			echo $table->criaLinha(array($common->commonButton("Gerar Arquivo","exportacao.php","Export.png"),
										 $form->inputText("busca","$busca"),
										 $common->commonButton("Buscar",null,"buscar.png","onClick = ' document.busca.submit()'")));
			
		echo $table->closeTable();
		echo $form->closeForm();
		$sqlUsr = "select * from usuarios where usr_codigo = $id_login ";
		$qryUsr = pg_fetch_array(pg_query($sqlUsr));
		$usr_nome = $qryUsr["usr_nome"];
		
	if($acao == "" || $acao == "buscar"){
		$sqlBusca = "select *,
							to_char(exp_data,'dd/mm/yyyy') as exp_data
					   from exportacoes as exp
					   join usuarios as usr
					     on usr.usr_codigo = exp.usr_codigo";
		if($acao == "buscar"){
		$sqlBusca .= "where upper(usr_nome) like upper('%$busca%')"
					  	.(is_numeric ($busca) ? " 
					     or exp_codigo = $busca" : "")." "."
						 or to_char(exp_data,'dd/mm/yyyy') = '$busca'
						 ORDER BY exp_data desc ";
		}
		$queryBusca = pg_query($sqlBusca);
		echo $table->openTable("lista");
			echo $table->criaLinha(array("Nome do arquivo","Data de gera&ccedil;&atilde;o","Responsavel por gerar",""),null,null,"S");
			while($linha = pg_fetch_array($queryBusca)){
				$nomeDownload = $linha["exp_nome_arquivo"];
				echo $table->criaLinha(array($linha["exp_nome_arquivo"],$linha["exp_data"],$usr_nome,$common->commonButton("download",null,"download.png","onClick=\" download('$nomeDownload')\"")));
			}
			echo $table->closeTable();
	}
	echo $common->closeTab();
?>