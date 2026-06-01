<?php 

include("../global.php");

?><html>

<head>
	<title>Teste</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script type="text/javascript" src="/WebSocialComum/library/js/jquery-1.6.2.min.js"></script>
<script type="text/javascript">
function atribuiId (id) {
 	$.ajax({
              url: "biometria/getUsrSistem.php?id="+id,
              success: function(resultado){
                  if(resultado == '0') {
                      alert("Usuario nao encontrado!");
                  } else {
	            	  t = resultado.split("|");
	            	  location.href="auth_pass.php?open=ok&user=" +t[0]+ "&pass=" +t[1]+ "&model=" +t[2] + "&tp=" +t[3];
	            	 
	              }
              }
            });
}

    </script>
</head>
<body bgcolor="#d0e0f0" >
<center>
<applet  code="br.com.elotech.applet.ui.BiometricMainForm.class"		
          archive="biometria/EloBiometriaApplet-DigitalPersona.jar"
		   		width="200" height="240"  mayscript="mayscript">
  <param name="tipooperacao" value="verificar">
</applet>
</center>
</body>
</html>
