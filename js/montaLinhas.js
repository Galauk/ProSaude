/*
	[this is the JS for the main jsPlumb demo.  it is shared between the YUI, jQuery and MooTools
	demo pages.
*/



/*
function teste(origem,destino)
{

	;(function() {
		alert(origem);
		alert(destino);
		window.jsPlumbDemo = {
				
			init : function() {
				
				jsPlumb.DefaultDragOptions = { cursor: "pointer", zIndex:2000 };
				jsPlumb.setMouseEventsEnabled(true);
		
				var connectorStrokeColor = "rgba(50, 50, 200, 1)",
				connectorHighlightStrokeColor = "rgba(180, 180, 200, 1)",
				hoverPaintStyle = { lineWidth:13,strokeStyle:"#7ec3d9" };			
				
				// 
				// connect window1 to window2 with a 13 px wide olive colored Bezier, from the BottomCenter of 
				// window1 to 3/4 of the way along the top edge of window2.  give the connection a 1px black outline,
				// and allow the endpoint styles to derive their color and outline from the connection.
				// label it "Connection One" with a label at 0.7 of the length of the connection, and put an arrow that has a 50px
				// wide tail at a point 0.2 of the length of the connection.  we use 'cssClass' and 'endpointClass' to assign
				// our own css classes, and the Label overlay has three css classes specified for it too.  we also give this
				// connection a 'hoverPaintStyle', which defines the appearance when the mouse is hovering over it. 
				//
				
				
				 var w23Stroke = "rgb(189,11,11)"; 
			        var connection3 = jsPlumb.connect({
							source:origem, 
			      				target:destino, 
			      				paintStyle:{ 
									lineWidth:8,
									strokeStyle:w23Stroke,
									outlineColor:"#666",
									outlineWidth:1 
								},
			      				hoverPaintStyle:hoverPaintStyle, 
			      				anchors:[ [ 0.3 , 1, 0, 1 ], "TopCenter" ], 
			      				endpoint:"Rectangle", 
			      				endpointStyles:[
			      		        	{ gradient : { stops:[[0, w23Stroke], [1, "#558822"]] }},
				    			{ gradient : {stops:[[0, w23Stroke], [1, "#882255"]] }}
				    		]
					});		
				
				
				

				jsPlumb.bind("dblclick", function(connection, originalEvent) { alert("double click on connection from " + connection.sourceId + " to " + connection.targetId); });
				jsPlumb.bind("endpointClick", function(endpoint, originalEvent) { alert("click on endpoint on element " + endpoint.elementId); });
			}
		};	
	})();
}
*/

var v1 = 'window1';
var v2 = 'window2';

;(function() {

	window.jsPlumbDemo = {
			
		init : function() {
			
			jsPlumb.DefaultDragOptions = { cursor: "pointer", zIndex:2000 };
			jsPlumb.setMouseEventsEnabled(true);
	
			var connectorStrokeColor = "rgba(50, 50, 200, 1)",
			connectorHighlightStrokeColor = "rgba(180, 180, 200, 1)",
			hoverPaintStyle = { lineWidth:13,strokeStyle:"#7ec3d9" };			
			
			// 
//			// connect window1 to window2 with a 13 px wide olive colored Bezier, from the BottomCenter of 
//			// window1 to 3/4 of the way along the top edge of window2.  give the connection a 1px black outline,
//			// and allow the endpoint styles to derive their color and outline from the connection.
//			// label it "Connection One" with a label at 0.7 of the length of the connection, and put an arrow that has a 50px
//			// wide tail at a point 0.2 of the length of the connection.  we use 'cssClass' and 'endpointClass' to assign
//			// our own css classes, and the Label overlay has three css classes specified for it too.  we also give this
//			// connection a 'hoverPaintStyle', which defines the appearance when the mouse is hovering over it. 
//			//
///////////////////////#############################################################################//////////////////
			   var conn4Color = "rgba(46,164,26,0.8)";
			jsPlumb.connect({  
				source:'window1', 
				target:'window3', 
				connector:"Flowchart",
				anchors:["Center", "Center"],  
				paintStyle:{ 
					lineWidth:2, 
					strokeStyle:conn4Color, 
					outlineColor:"#666",
						outlineWidth:1
					},
				hoverPaintStyle:hoverPaintStyle,
				endpointsOnTop:false, 
				endpointStyle:{ radius:5, fillStyle:conn4Color },
				labelStyle : { cssClass:"component label" },
		});
			  
			var conn4Color = "rgba(46,164,26,0.8)";
			var connection3 =	jsPlumb.connect({  
				source:'window2', 
				target:'window3', 
				connector:"Flowchart",
				anchors:["Center", "Center"],  
				paintStyle:{ 
					lineWidth:2, 
					strokeStyle:conn4Color, 
					outlineColor:"#666",
						outlineWidth:1
					},
				hoverPaintStyle:hoverPaintStyle,
				endpointsOnTop:false, 
				endpointStyle:{ radius:5, fillStyle:conn4Color },
				labelStyle : { cssClass:"component label" },
		});
			   var conn4Color = "rgba(46,164,26,0.8)";
			var connection2 = jsPlumb.connect({  
				source:'window3', 
				target:'window4', 
				connector:"Flowchart",
				anchors:["Center", "Center"],  
				paintStyle:{ 
					lineWidth:2, 
					strokeStyle:conn4Color, 
					outlineColor:"#666",
						outlineWidth:1
					},
				hoverPaintStyle:hoverPaintStyle,
				endpointsOnTop:false, 
				endpointStyle:{ radius:5, fillStyle:conn4Color },
				labelStyle : { cssClass:"component label" },
		});
			   var conn4Color = "rgba(46,164,26,0.8)";
			jsPlumb.connect({  
				source:'window3', 
				target:'window5', 
				connector:"Flowchart",
				anchors:["Center", "Center"],  
				paintStyle:{ 
					lineWidth:2, 
					strokeStyle:conn4Color, 
					outlineColor:"#666",
						outlineWidth:1
					},
				hoverPaintStyle:hoverPaintStyle,
				endpointsOnTop:false, 
				endpointStyle:{ radius:5, fillStyle:conn4Color },
				labelStyle : { cssClass:"component label" },
		});
			   var conn4Color = "rgba(46,164,26,0.8)";
			jsPlumb.connect({  
				source:'window3', 
				target:'window6', 
				connector:"Flowchart",
				anchors:["Center", "Center"],  
				paintStyle:{ 
					lineWidth:2, 
					strokeStyle:conn4Color, 
					outlineColor:"#666",
						outlineWidth:1
					},
				hoverPaintStyle:hoverPaintStyle,
				endpointsOnTop:false, 
				endpointStyle:{ radius:5, fillStyle:conn4Color },
				labelStyle : { cssClass:"component label" },
				
		});


			
			
///////////////////////#############################################################################//////////////////
			
						        
	       
			
	     
	    
	      
	
			jsPlumb.bind("dblclick", function(connection, originalEvent) { alert("double click on connection from " + connection.sourceId + " to " + connection.targetId); });
			jsPlumb.bind("endpointClick", function(endpoint, originalEvent) { alert("click on endpoint on element " + endpoint.elementId); });
		}
	};	
})();