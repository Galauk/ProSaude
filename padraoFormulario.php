<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
 <title>Formulario Padrao</title>
 <?php 
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
 ?>
 <meta name="Dilee C. pacheco - dilee@elotech.com.br" content="" />
 <link href="css/estiloForm.css" rel="stylesheet" type="text/css" />
 <link href="css/estiloCommon.css" rel="stylesheet" type="text/css" />
 </head>

 <body bgcolor="#E8F4F">
 <?php 
   		$form = new classForm();
		$common = new commonClass();
		echo $common->incJquery();
	
//echo $common->commonButton('botao');


//echo $common->openModal('600','Cadastro de Paciente');

  echo $common->menuTab(array('Selecione uma Senha Para Atendimento'));

echo $common->bodyTab('1');

  echo $common->commonButton('farmacia','index.php',null,'300','100');

echo $common->closeTab();	



/*
echo $common->bodyTab('1');
	echo $form->openForm($PHP_SELF);
	echo $form->inputText('nome',$_POST['nome'],'Nome Completo');
	echo $form->inputText('nome',$_POST['nome'],'Nome Completo');
	echo $form->inputText('nome',$_POST['nome'],'Nome Completo');
	echo $form->inputText('nome',$_POST['nome'],'Nome Completo');
	echo $form->inputText('nome',$_POST['nome'],'Nome Completo');
	echo $form->inputText('nome',$_POST['nome'],'Nome Completo');
echo $common->closeTab();	
	
echo $common->bodyTab('2');
echo $form->inputText('nome',$_POST['nome'],'Nome Completo');
	echo $form->inputText('nome',$_POST['nome'],'Nome Completo');
	echo $form->inputText('nome',$_POST['nome'],'Nome Completo');
	echo $form->inputText('nome',$_POST['nome'],'Nome Completo');
	echo $form->inputText('nome',$_POST['nome'],'Nome Completo');
	echo $form->inputText('nome',$_POST['nome'],'Nome Completo');
	echo $form->inputText('nome',$_POST['nome'],'Nome Completo');
	echo $form->inputText('nome',$_POST['nome'],'Nome Completo');
    echo $form->inputSelect('nome',array('1'=>'Valor 01' , '2'=>'Valor 02'),'Campor Select','200');
	echo $form->submitForm('Cadastrar',$_SESSION[linkroot].$_SESSION[comum]."imgs/icoteste.png");
	echo $form->closeForm();
echo $common->closeTab();	

echo $common->modalMsg('teste');
*/
 ?>
 </body>
</html>
