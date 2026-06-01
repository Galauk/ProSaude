<?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();

//
//------------------------------------------------------------------>
?>
<html>
<head>
<style>
	#teste{
		text-align:center;
	}
	#image{
		position:fixed;
		vertical-align:bottom;
	}
	#logos{
		padding-top:310px;
		clear:both;
		float:right;
		
	}
	#onda{
		margin-top:185px;
		margin-left:0px;
		position:fixed;
		
	}
	#centro{
		position: fixed;
		padding: 0px;
		width: 500px;
		height: 439px;
		left: 50%;
		margin-left: -250px;
	}
</style>
</head>
	<body>
	<div>
            <img src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/fundoSistema.jpg" width="600" height="400"/>
    </div>
</body>
<!--<body>
	<div id="centro">
        <div id="image">
            <img src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/sus.png" />
        </div>
    </div>
    <div id="logos">
        <img src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/logo_elotech.png" />
    </div>
</body>
-->
</html>

