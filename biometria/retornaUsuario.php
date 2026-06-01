<html>
<head>

	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<script type="text/javascript" src="/WebSocialComum/library/js/jquery-1.6.2.min.js"></script>
	<script type="text/javascript">
	function atribuiId (id) {
	 	$.ajax({
	              url: "getUsrSistem.php?id="+id,
	              success: function(resultado){
	                  if(resultado == '0') {
	                      alert("Usuario nao encontrado!");
	                  } else {
		            	  t = resultado.split("|");
		            	  alert(t[4]);
		            	
		            	 
		              }
	              }
            });
	}
	
	    </script>
</head>

<applet  code="br.com.elotech.applet.ui.BiometricMainForm.class"		
          archive="EloBiometriaApplet-DigitalPersona.jar"
		   		 mayscript="mayscript">
  <param name="tipooperacao" value="verificar">
</applet>

</body>
</html>
