<link href='css/estiloForm.css' rel='stylesheet' type='text/css' />
<link href='css/estiloCommon.css' rel='stylesheet' type='text/css' />
<script language='JavaScript' type='text/javascript' src='funcoes.js'></script>
<?
session_start();
require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

$form = new classForm();
$common = new commonClass();
$table = new tableClass();
echo $common->incJquery();

	echo $common->menuTab(array('Geladeira'));
	echo $common->bodyTab('1');
if($acao == ""){
	echo $common->closeTab();
	echo $form->openForm("$PHP_SELF","POST");
		echo $table->openTable('lista');
			echo $form->hiddenForm("acao", "form_add");
			echo"<tr>
			 	<td>
			 		<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg'>
			 	</td>
			</tr>";
			echo $table->criaLinha(array("Patrimonio","Marca Geladeira","Minima","M&aacute;xima",""),null,array(1,1,1,3),'S');
			$sql = "select * from geladeira limit 10";
			$query = pg_query($sql);
			while($linha = pg_fetch_array($query)){
				
				echo $table->criaLinha(array("$linha[gel_patrimonio]",
											 "$linha[gel_marca]",
											 "$linha[gel_minima]",
											 "$linha[gel_maxima]",
											 $common->commonButton("Editar","$PHP_SELF?acao=form_add&gel_codigo=$linha[gel_codigo]","editar_on.png"),
											 "<a href='geladeiraVacina.php?acao=deletar&id=$linha[gel_patrimonio]&gel_codigo=$linha[gel_codigo]'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg' style='border:0px'></a>"));
			}
		echo $table->closeTable();
	echo $form->closeForm();
}
if($acao == "form_add"){
		echo $form->openForm("$PHP_SELF","POST");
			echo "<table>
				  	<tr>
				  		<td>";
				if(empty($gel_codigo)){
					echo $form->hiddenForm("acao", "salvar");
				}else{
					echo $form->hiddenForm("acao", "editar");
					echo $form->hiddenForm("gel_codigo", "$gel_codigo");
					$sqlTudo = "SELECT * from geladeira where gel_codigo = $gel_codigo";
					$queryTudo = pg_query($sqlTudo);
					$regTudo = pg_fetch_array($queryTudo);
				}
				echo $form->inputText("gel_patrimonio","$regTudo[gel_patrimonio]","Patrimonio",null,null,"onKeyPress=\"return apenasNumero(this)\" onKeyUp=\"return apenasNumero(this)\" ");
					echo "</td>
					</tr>
				  	<tr>
				  		<td>";
				echo $form->inputText("gel_marca","$regTudo[gel_marca]","Marca");
					echo "</td>
					</tr>
				  	<tr>
				  		<td>";
				echo $form->inputText("gel_minima","$regTudo[gel_minima]","Minima");
					echo "</td>
						</tr>
					  	<tr>
					  		<td>";
				echo $form->inputText("gel_maxima","$regTudo[gel_maxima]","M&aacute;xima");
					echo "</td>
					</tr>
					<tr>
						<td>";
							$sql = "select * from setor order by set_nome";
							echo $form->inputSelect("setor",null,"Setor",$sql,null,null,$regTudo[set_codigo]);
					echo"</td>
					</tr>
				  	<tr>
				  		<td colspan='2'>";
				echo "<a href=\"geladeiraVacina.php\"><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.jpg'></a>
						<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/salvar_on.jpg')";
				echo "</td>
					</tr>
				  	<tr>
				  		<td>";
			echo "</table>";
		echo $form->closeForm();
}
		if($acao == 'salvar'){
			$gel_marca = $_POST['gel_marca'];
			$gel_minima = $_POST['gel_minima'];
			$gel_maxima = $_POST['gel_maxima'];
			$gel_patrimonio = $_POST['gel_patrimonio'];
			$set_codigo = $_POST['set_codigo'];
			
			$stmt = "INSERT INTO geladeira ( 
								 gel_marca, 
								 gel_minima, 
								 gel_maxima, 
								 gel_patrimonio,
								 set_codigo
					 ) VALUES ( 
								 UPPER('$gel_marca'), 
								 $gel_minima, 
								 $gel_maxima, 
								'$gel_patrimonio',
								$setor
								)";

			if(pg_query($stmt)){
				echo $common->modalMsg("OK","Geladeira Salva com Sucesso!","geladeiraVacina.php");
			}else{
				echo $common->modalMsg("ERRO","Erro ao salvar!","geladeiraVacina.php",$stmt);
			}
		}
		
		if($acao == "editar"){
			$sqlUpdate = "UPDATE geladeira
							   SET gel_marca = UPPER('$gel_marca'),
							   	   gel_minima = ".($gel_minima == "" ? "null" : "$gel_minima").",
							   	   gel_maxima = ".($gel_maxima == "" ? "null" : "$gel_maxima").",
							   	   gel_patrimonio = '$gel_patrimonio',
							   	   set_codigo = $setor
							 WHERE gel_codigo = '$gel_codigo'";
			if(pg_query($sqlUpdate)){
				echo $common->modalMsg("OK","Geladeira Editada com Sucesso!","geladeiraVacina.php");
			}else{
				echo $common->modalMsg("ERRO","Erro ao editar!","geladeiraVacina.php",$sqlUpdate);
			}		
		}
		
		if($acao == 'deletar'){
			$s = "select * from temperatura_geladeira where gel_codigo = $gel_codigo";
			$q = pg_query($s);
			$line = pg_num_rows($q);
			if ($line != 0)
			{
				echo $common->modalMsg("ERRO","Existem registros vinculados com essa Geladeira!","geladeiraVacina.php");
			}else{ 
				$sqlDel = "delete from geladeira where gel_codigo = $gel_codigo";
			    if(pg_query($sqlDel)){
					echo $common->modalMsg("OK","Deletado Com Sucesso","geladeiraVacina.php");
			    }else{
			    	echo $common->modalMsg("ERRO","Erro ao deletar","geladeiraVacina.php",$sqlDel);
			    }
			}
		}
?>