<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-2" />
<meta name="keywords" content="flash,web,design,development,developer,designer,coder,code,actionscript,script,animation,animations,interactive" />
<title>Cadastramento de Foto</title>
<body topmargin="0" leftmargin="0">
<script src="swfobject.js" language="javascript"></script>
</head>
<div id="flashArea" class="flashArea" style="height:100%;"><p align="center">Este aplicativo requer Adobe Flash Player.<br /><a href="http://www.adobe.com/go/getflashplayer">
						<img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" /><br />
    <a href=http://www.macromedia.com/go/getflash/>Baixar o Flash</a></p>
	</div></td>
  </tr>

  <script type="text/javascript">
	var mainswf = new SWFObject("take_picture2.swf", "main", "700", "400", "9", "#ffffff");
	mainswf.addParam("scale", "noscale");
	mainswf.addParam("wmode", "window");
	mainswf.addParam("allowFullScreen", "true");
	mainswf.addVariable("usu_codigo", "<?=$_REQUEST['usu_codigo']?>");
	mainswf.write("flashArea");
	
  </script>

</body>
</html>
