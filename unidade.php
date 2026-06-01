<style>

</style>
<?
$PHP_SELF = $_SERVER['PHP_SELF'];
require_once "global.php";

$form = new classForm();
$common = new commonClass();
$table = new tableClass();
echo $common->incJquery();
echo $common->menuTab(array('Cadastro Unidade de Sa&uacute;de'));
echo $common->bodyTab('1');
	if($acao == ""){
		echo $common->commonButton("Adicionar",$PHP_SELF."?acao=form_add","adicionar.png");
			echo $table->openTable("lista");
				echo $table->criaLinha(array("C&oacute;digo","Unidade","Cnes","&nbsp;"),null,array("","","","2"),"S");
				$sqlSec = "SELECT * FROM unidade";
				$qrySec = pg_query($sqlSec);
				while($linha = pg_fetch_array($qrySec)){
					echo $table->criaLinha(array("$linha[uni_codigo]","$linha[uni_desc]",(empty($linha[uni_cnes]))?"&nbsp;":"$linha[uni_cnes]",
					$common->commonButton("Editar",$PHP_SELF."?acao=form_edit&uni_codigo=$linha[uni_codigo]","editar_on.png"),
					$common->commonButton("Apagar",$PHP_SELF."?acao=deletar&uni_codigo=$linha[uni_codigo]","apagar.png")));
				}
			echo $table->closeTable();
	}
	if(($acao == "form_add" OR $acao == "form_edit")){
		echo '<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC3EYBpyHkTMoCtj8ZsEHgprsFzP-Vs0qg&amp;libraries=places"></script>';
		echo $form->openForm($PHP_SELF,'POST','form');
		if($acao=="form_add") {
		  echo $form->hiddenForm("acao", "salvar");
		} else {
		  echo $form->hiddenForm("acao", "edita");
		  echo $form->hiddenForm("uni_codigo", $uni_codigo);
		  $rr = pg_fetch_array(pg_query("select *from unidade where uni_codigo = '$uni_codigo'"));
		}
				echo $form->inputText('uni_cnes',$rr[uni_cnes],'CNES',10,10,'');
				echo $form->inputText('uni_desc',$rr[uni_desc],'Nome da unidade',50,50,'');
				echo $form->inputText('uni_endereco',$rr[uni_endereco],'Endereco',60,60,'');
				echo $form->inputText('uni_numero',$rr[uni_numero],'N�mero',12,9,'');
				echo $form->inputText('uni_cep',$rr[uni_cep],'CEP',12,9,'');
				echo $form->inputText('uni_bairro',$rr[uni_bairro],'Bairro',30,30,'');
				?>

				<script type="text/javascript">
					<?
					$coordJs = "var coordenadas = false;";
					if(!empty($rr['uni_coordenadas'])){
						$coordJs = "var coordenadasDb = ".$rr['uni_coordenadas']."; var coordenadas = new google.maps.LatLng(coordenadasDb.lat,coordenadasDb.lng);";
					}
					echo $coordJs;
					?>
					</script>
					<div id="mapa-container">
						<div id="google-maps"></div>
						<div id="mapa-centro"></div>
					</div>
					<input type="hidden" name="uni_coordenadas" id="mapa-latlng" value="<?= $rr['uni_coordenadas'] ?>">
				<?
				echo $form->inputText('cnes_telefone',$rr[cnes_telefone],'Telefone',30,30,'');
				echo $form->inputText('uni_responsavel',$rr[uni_responsavel],'Responsavel',30,30,'');
				
				echo"<br><br><div style='float:left;width:98px;'>&nbsp;</div><div style='float:left;'>";		
				echo $common->commonButton("voltar",$PHP_SELF,"voltar.png");
				echo"</div>";
				echo"<div style='float:left;'>";
				echo $common->commonButton("Salvar","","report.png","onClick=\"return validaCampos();\"");
				//echo $common->commonButton("Salvar","","report.png","onclick='document.form.submit();'");
				echo"</div><br><br>";
				
				echo $form->closeForm();
				?>
<script type="text/javascript">
function formataEndereco(endereco){
  var arrayEndereco = endereco.split(', ');
  arrayEndereco.pop();
  return arrayEndereco.join(', ');
}
var map;
window.onload = function(){
	$(function(){
	  var
	  centro,
	  centroMapa = coordenadas ? coordenadas : new google.maps.LatLng(-14.2200833,-54.0212282),
	  zoom = coordenadas ? 15 : 4,
	  inputMapaEndereco = $("#mapa-endereco"),
	  btnMapaBusca = $('#mapa-busca'),
	  inputMapaLatLng = $('#mapa-latlng'),
	  inputEndereco = $('#npt-endereco'),
	  enderecoRetornado = false,
	  geocoder= new google.maps.Geocoder(),
	  input = document.getElementById('mapa-endereco');
	  map = new google.maps.Map(
	    document.getElementById("google-maps"), {
	      center: centroMapa,
	      zoom: zoom,
	      mapTypeId: 'roadmap',
	      scrollwheel: false,
	      streetViewControl: false
	    }
	  );
	  if(coordenadas){
	    var marker = new google.maps.Marker({
	      position: map.getCenter(),
	      map: map,
	      title: 'Local atual'
	    });
	  }

	  var autocomplete = new google.maps.places.Autocomplete(input);
	  autocomplete.bindTo('bounds', map);

	  google.maps.event.addListener(autocomplete, 'place_changed', function(){
	    var place = autocomplete.getPlace();

	    if (place.hasOwnProperty('geometry')) {
	      map.fitBounds(place.geometry.viewport);
	    } else {
	      map.setCenter(place.geometry.location);
	    }

	    var address = '';

	    if (place.address_components) {
	      address = [
	        (place.address_components[0] && place.address_components[0].short_name || ''),
	        (place.address_components[1] && place.address_components[1].short_name || ''),
	        (place.address_components[2] && place.address_components[2].short_name || '')
	      ].join(' ');
	    }
	  });

	  google.maps.event.addListener(map, 'dragend', function(){
	    centro = map.getCenter();
	    inputMapaLatLng.val('{"lat":"'+centro.lat()+'", "lng":"'+centro.lng()+'"}');
	    geocoder.geocode({'latLng': map.getCenter()}, function(results, status) {
	      if (status == google.maps.GeocoderStatus.OK) {
	        if (results[0]) {
	          var endereco = formataEndereco(results[0].formatted_address);
	          inputEndereco.val(endereco);
	          enderecoRetornado = endereco;
	        } else {
	          alert('No results found');
	        }
	      }
	    });
	  });
	  $('#mapa-busca').click(function(e){
	    var address = inputMapaEndereco.val();
	    geocoder.geocode({'address': address}, function(results, status){
	      if (status == google.maps.GeocoderStatus.OK) {
	        inputEndereco.val(formataEndereco(results[0].formatted_address));
	        map.setCenter(results[0].geometry.location);
	        map.setZoom(14);
	        inputMapaLatLng.val('{lat:'+results[0].geometry.location.lat()+', lng:'+results[0].geometry.location.lng()+'}');
	      } else {
	        alert("Endere�o n�o encontrado!");
	        inputEndereco.val('');
	        inputMapaEndereco.focus();
	      }
	    });
	    e.preventDefault();
	  });
	  inputMapaEndereco.keypress(function(e){
	    if((e.keyCode ? e.keyCode : e.which) == 13){
	      btnMapaBusca.trigger('click');
	      e.preventDefault();
	    }
	  });
	});
};
</script> 
<?
	}
	if($acao == "salvar"){
		 $sql = "INSERT INTO unidade ( 
						uni_cnes, 
						uni_desc, 
						uni_endereco, 
						uni_cep, 
						uni_responsavel,
						cnes_telefone,
						uni_bairro,
						uni_numero
					 ) VALUES ( 
						'$uni_cnes', 
						UPPER('$uni_desc'), 
						UPPER('$uni_endereco'), 
						UPPER('$uni_cep'), 
						UPPER('$uni_responsavel'),
						UPPER('$cnes_telefone'),
						UPPER('$uni_bairro'),
						UPPER('$uni_numero')
					)";
					
 			$query = pg_query($sql) or die(pg_last_error());
			echo $common->modalMsg("OK","Unidade Salva Com Sucesso!",$PHP_SELF);	
	}
	if($acao == "edita"){
		$sql = "UPDATE unidade SET
					uni_cnes = '$uni_cnes', 
					uni_desc = UPPER('$uni_desc'), 
					uni_endereco = UPPER('$uni_endereco'), 
					uni_cep = UPPER('$uni_cep'), 
					uni_responsavel = UPPER('$uni_responsavel'),
					cnes_telefone = UPPER('$cnes_telefone'),
					uni_bairro = UPPER('$uni_bairro'),
					uni_numero = UPPER('$uni_numero'),
					uni_coordenadas = '$uni_coordenadas'
				WHERE uni_codigo = $uni_codigo";
		$query = pg_query($sql);
		echo $common->modalMsg("OK","Unidade Salva Com Sucesso!",$PHP_SELF);	
	}
	if($acao == "deletar") {
		$getQuery = pg_query("select * from unidade where uni_codigo = $uni_codigo");
		$getName = pg_fetch_array($getQuery);
		echo $common->modalConfirm("Deseja deletar a unidade $getName[uni_desc]","unidade.php?acao=del&uni_codigo=$uni_codigo","unidade.php");
	}	
	
	if($acao == "del") {
		$sqlDel = "delete from unidade where uni_codigo = $uni_codigo";
		$qryDel = pg_query($sqlDel);
		echo $common->modalMsg("OK","unidade Excluida com Sucesso!","unidade.php");
	}
echo $common->closeTab();


?>
<script type="text/javascript">
function validaCampos(){
	uni_cnes = document.getElementById('uni_cnes');
	uni_desc = document.getElementById('uni_desc');
	if (uni_desc.value.length == 0){
		alert('O campo unidade � obrigat�rio.');
		uni_desc.focus();
		return false;
	}
	if (uni_cnes.value.length == 0){
		alert('O campo CNES � obrigat�rio.');
		uni_cnes.focus();
		return false;
	}
	document.form.submit();
	//document.form_paciente.submit();
}
</script>