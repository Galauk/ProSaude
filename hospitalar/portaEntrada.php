<script language="JavaScript" type="text/javascript" src="../exa_exame_hotkeys.js"></script>
<script language="JavaScript" type="text/javascript" src="../funcoes_busca.js"></script>
<script language="JavaScript" type="text/javascript" src="../ajax_motor.js"></script>
<script language="JavaScript" type="text/javascript" src="../funcaobuscarMedico.js"></script>
<?php 
	session_start();
    require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
    require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
    require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";	
    require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
    require_once '../json.inc.php';
    require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
    require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.db.php";
    require_once 'funcaoBuscaUsuario.php';	
    
    $common = new commonClass();
    $table = new tableClass();	
    $form = new classForm();

    echo $common->incJquery();
    echo $common->menuTab(array('Porta de Entrada'));
	echo $common->bodyTab('1');
		

			
switch ($acao) {		
		case "" :
			echo $form->openForm("$PHP_SELF","POST","busca");
				echo $form->hiddenForm("acao","buscar");
				echo $table->openTable();
				echo $table->criaLinha(array($common->commonButton("Cadastro de Quartos","cadquarto.php","adicionar.png",null,null),$common->commonButton("Cadastro de Leitos","cadleito.php","adicionar.png",null,null),$common->commonButton("buscar",null,"buscar.png","onclick='document.busca.submit()'"),$form->inputText("busca","$valor")),array(230));
				echo $table->closeTable();
			echo $form->closeForm();		
			include ('hospitalar.php');
			break;
		
		case "add_leito" :
			include('cadleito.php');
		break;

		}		

	

	echo $common->closeTab();

		
		
		
 ?>