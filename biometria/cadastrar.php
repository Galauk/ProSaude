<?php 

include("../global.php");

?><html>

<head>
	<title>Teste</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"><style type="text/css">
<!--
body {
	background-color: #FFFFFF;
}
-->
</style>
 <script type="text/javascript">
        
        function atribuiId (id) {
        
	        var idBio = document.formBiometrico.idCadastro.value = id;
	        var idUsu = document.formBiometrico.iduser.value;

			if(idBio != '' && idBio != 0) {
				document.formBiometrico.submit();
			}
	            
	    }
    </script>
</head>
<body>
<center>
<applet  code="br.com.elotech.applet.ui.BiometricMainForm.class"	
          archive="EloBiometriaApplet-DigitalPersona.jar"
		   		width="200" height="240"  mayscript="mayscript">
  <param name="tipooperacao" value="cadastrar">
</applet>
</center>
<form name="formBiometrico" >
<input name="acao" value="add" type=hidden>
<input name="cadatro" id="idCadastro" type=hidden>
<input name="iduser" id="iduser" value='<?=$usu_codigo?>' type=hidden>
</form>
</body>
</html>
<?php 
if($acao=='add') {
			   $sql = pg_query("UPDATE usuario SET idbio = '$cadatro' where usu_codigo = '$iduser'") or die (pg_last_error());
			   ?> <script> window.parent.close(); </script><?
}
?>
