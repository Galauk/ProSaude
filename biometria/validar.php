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
 function sendValue(a){
		 window.opener.buscarPorUsuCodigo(a);
			 
	 window.close();
} 
 
        function atribuiId (id) {
        
        	var idBio = document.formBiometrico.idCadastro.value = id;
        	if(idBio == 0 ) {
				alert('Digital Nao Encontrada / Nao Cadastrada');
				
        	}
			if(idBio != '') {
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
  <param name="tipooperacao" value="verificar">
</applet>
</center>
<form name="formBiometrico" >
<input name="cadatro" id="idCadastro" type="hidden">
<input name="acao" value='ok' type="hidden">
</form>
</body>
</html>
<?php 
  if($acao == "ok") {
		$rr = pg_fetch_array(pg_query("select *from usuario where idbio = '$cadatro'"));
		?><script> sendValue('<?=$rr[usu_codigo]?>'); window.close();</script> <?
  }
?>