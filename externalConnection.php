<?
session_start();
require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
$common = new commonClass();
$form = new classForm();
$table = new tableClass();

echo $common->incJquery();
$option = array("mssql_connect"=>"Tributa&ccedil;&atilde;o");
echo $common->menuTab(array('Estabelecendo Conex&atilde;o'));
echo $common->bodyTab('1');
	echo $form->openForm("$PHP_SELF","POST");
		echo $form->inputSelect("tipo",$option,"Tipo");
		echo $form->inputText("host","","Host");
		echo $form->inputText("usuario","","Usu&aacute;rio");
		echo $form->inputText("senha","","Senha");
		echo $form->inputText("base","","Data Base");
		echo $form->inputText("porta","","Porta");
		echo "<br/><div style=\"clear:both; width:310px;\">";
		echo "<div style=\"float:left\" >".$common->commonButton("Testar Conexao","#","teste.png")."</div>";
		echo "<div style=\"float:right\"><input type='image' name='salvar' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/salvar_on.jpg'></div></div>";
	echo $form->closeForm();
echo $common->closeTab();
?>