	function changeColor(elemento){
		if (elemento.className == 'par'){
			elemento.className = 'parSobre';
		}else if(elemento.className == 'parSobre'){
			elemento.className = 'par';
		}else if(elemento.className == 'impar'){
			elemento.className = 'imparSobre';
		}else if(elemento.className == 'imparSobre'){
			elemento.className = 'impar';
		}
	}
