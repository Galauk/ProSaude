/** abas */
function tabHandler( abas )
{
	//this.abas = abas;
	this.abas = arguments;
	
	this.show = function( id )
	{
		if( ! id )
		{
			if( this.abas.length == 0 ) return;
			id = this.abas[0];
		}
		
		var a = null;
		var l = null;
		for( var i=0; i < this.abas.length; i++ )
		{
			a = this.abas[i];
			l = this.abas[i] + "_link";
			//document.getElementById( a ).style.visibility = ( id == this.abas[i] ? 'visible' : 'hidden' );
			document.getElementById( a ).style.display = ( id == this.abas[i] ? 'block' : 'none' );
			document.getElementById( l ).className = ( id == this.abas[i] ? 'tabact' : '' );
		}
		
	}
	
	this.show();
}