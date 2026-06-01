<!doctype html> 
<html> 
	<head> 
		<title>jsPlumb 1.3.2 demo - jQuery</title> 
		<meta http-equiv="content-type" content="text/html;charset=utf-8" /> 
		<link rel="stylesheet" href="/mp.css"></link> 
		<link rel="stylesheet" href="css/jsPlumbDemo.css"> 
		<link rel="stylesheet" href="css/demo.css">				
	
	<?php 
	$o =  array("pai",
				"mae");
	$d = array("c",
				"c");
			echo"<script>
			;(function() {
				window.jsPlumbDemo = {
			
		init : function() {
			
			jsPlumb.DefaultDragOptions = { cursor: \"pointer\", zIndex:2000 };
			jsPlumb.setMouseEventsEnabled(true);
	
			var connectorStrokeColor = \"rgba(50, 50, 200, 1)\",
			connectorHighlightStrokeColor = \"rgba(180, 180, 200, 1)\",
			hoverPaintStyle = { lineWidth:13,strokeStyle:\"#7ec3d9\" };		";
			
			for($i=0; $i<2; $i++){
				
				echo "alert('$o[$i] $d[$i]')";
			echo "
			   var conn4Color = \"rgba(46,164,26,0.8)\";
			jsPlumb.connect({  
				source:'$o[$i]', 
				target:'$d[$i]', 
				connector:\"Flowchart\",
				anchors:[\"Center\", \"Center\"],  
				paintStyle:{ 
					lineWidth:2, 
					strokeStyle:conn4Color, 
					outlineColor:\"#666\",
						outlineWidth:1
					},
				hoverPaintStyle:hoverPaintStyle,
				endpointsOnTop:false, 
				endpointStyle:{ radius:5, fillStyle:conn4Color },
				labelStyle : { cssClass:\"component label\" },
		});";
}
		echo "			jsPlumb.bind(\"dblclick\", function(connection, originalEvent) { alert(\"double click on connection from \" + connection.sourceId + \" to \" + connection.targetId); });
			jsPlumb.bind(\"endpointClick\", function(endpoint, originalEvent) { alert(\"click on endpoint on element \" + endpoint.elementId); });
		}
	};	
})();";
		echo "</script>";
		?>	
	</head> 
	<body onunload="jsPlumb.unload();"> 
	
	<a href='#' onclick="teste('window1','window3');">Clika</a>
		<div style="position:absolute"> 
		<div id="demo"> 
			<div class="component window" id="pai"><strong>PAI</strong><br/><br/>João da silva</div> 
			<div class="component window" id="mae"><strong>MAE</strong><br/><br/>Maria Oliveira</div> 
		    <div class="component window" id="c"><strong>CIDADÃO</strong><br/><br/>José Nascimento</div> 
		    <div class="component window" id="window4"><strong>CÔNJUGE</strong><br/><br/>Antonia maria</div> 
		    <div class="component window" id="window5"><strong>FILHO</strong><br/><br/>Gustavo Silva</div> 
		    <div class="component window" id="window6"><strong>FILHO</strong><br/><br/>Matheus Silva</div> 
		 </div>	
		 </div> 
	    
	    <div id="debug"></div> 
	    
	   
	    
	    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.0/jquery.min.js"></script> 
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js"></script> 
		<script type="text/javascript" src="js/jquery.jsPlumb-1.3.2-all-min.js"></script> 
		
		<!--  demo code --> 
<!--		<script type="text/javascript" src="js/demo.js"></script> -->
		<!--<script type="text/javascript" src="js/montaLinhas.js"></script>--> 
		
		<!--  demo helper code --> 
		<script type="text/javascript" src="js/demo-helper-jquery.js"></script>			
 
<!-- Start of StatCounter Code --> 
<script type="text/javascript"> 
var sc_project=6543403; 
var sc_invisible=1; 
var sc_security="b1f05c44"; 
</script> 
 
<script type="text/javascript" src="http://www.statcounter.com/counter/counter.js"></script>
<noscript>
	<div class="statcounter">
		<a title="tumblr page counter" href="http://statcounter.com/tumblr/" target="_blank">
			<img class="statcounter" src="http://c.statcounter.com/6543403/0/5a87615f/1/" alt="tumblr page counter" >
		</a>
	</div>
</noscript> 
<!-- End of StatCounter Code --> 
 
 
<script type="text/javascript"> 
 
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-15400992-4']);
  _gaq.push(['_trackPageview']);
 
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
 
</script> 
 
	</body> 
</html> 
