
<link href="../estilo.css" rel="stylesheet" type="text/css">
		<link href="../css/estiloForm.css" rel="stylesheet" type="text/css" />
		<link href="../css/estiloCommon.css" rel="stylesheet" type="text/css" />

<? include '../global.php';
	$array = array("de","DE","E","e","A","a","UMA","uma","um","UM"," ","","S","s","da","DA","C","c","C/","c/","com","COM","h","H","CA","CAR","B","b","CO","UL","COL","%","D","G"," ","A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z","ER","OD","4%","LEITE","GE","AR","1%","SOL","IN","EITE","DO","OS");
	$medicamento = array("LORAZEPAN","RISPIRIDONA","RITALINA","RIVOTRIL","VENLAFAXINA","VENVANSE","ALENIA","ADEROGIL","ALGENAC","PROXIMAX","AMOXILINA","DIPIRONA","AMPOLA","ANTHELOS","CATAFLAN","DOGMATIL","NISTATIM","NAPROXENO","PATANOL","LACRIFILM","ANLODIPINO","BRONCHO","CIDALURD","FENAZOPIRIDENO","GLICOLIVE","MESACOL","NEBIDO","PAROXETINA","CAVERGECT","ABICTIL","ALENTHEUS","ALPRAZOLAN","ALENTUS","ALGENAC","ALGINAC","ALTIVA","ALVESCO","AMATO","AMOXELINA","ILAXOLIN","ANCETILESTEINA","LEOCONGEN","ARPADOL","FIXACAL","ARTICO","ARTICO","ARTOGLICO","ARTROLIVE","ARTROLIVE","ATACAND","ATROGLICO","AVADOS","AVALOX","AVODART","BENICAR","BIOFLAC","BIOLEGE","DROPIPIZINA","CX","AMBROXOL","ANGELIQ","AVODART","FINASTERIDA","ARTROZIL","BIOFENAC","BIPROFENID","CELEBRA","BIPROFINID","BROMOPRIDA","BUSONID","BECLOFEN","BUSONIDA","FLUXONASE","TORSILAX","INDIA","CEFALIUM","CEFAMOX","CELEBRA","QUETIAPINA","CENTRUM","CLOMID","CLORPROMAZEPINA","FRESH","SILODEX","SYSTANE","AZORGA","CODATEN","SYSTANE","ANTIALERGICO","LIMIGAM","ANTIALERGICO","BETOPTIC","CLENIGEL","CICLOPEGICO","CICLOPLEGICO","KAFLEX","CILODEX","SYSTANE","CILODEX","CLAROFENICOL","CRISTALIN","DEXAMETAZONA","FLORATE","RELESTAT","GLAUCOTRAT","LACRIFILM","LUMIGAN","NEVANAC","CLORAFT","OCTOFLEN","OFTPRED","SYSTANE","PATANOL","PRED","AZORGA","ZYLET","MAXIFOR","VIDADEXA","CXS","MIOSAN","DEPAKENE","TROFANIL","DERSANI","DEXADOSE","TANDRILAX","DEXADOZE","DIFOSFATO","DIGEPLUS","FLORATIL","DIOVAN","PLAKETAR","RITMONORT","DONAREN","QUEROPAX","MAXPRAN","DOXEPINA","HCL","DUOMO","DAFORIN","EMODERM","ZYXEN","PREDSM","ENEMA","GUTALAX","ESPRAN","DONAREN","ETUNIX","DIPROGENTA","UREIA","NEXIUM","MOTILIUM","FLIXONASE","NARIDRIN","FORASEG","FRASC","ALIVIUM","LEOCONGEM","FRSC","GABANEURIN","DEPURA","VITERGAN","GABAPENTINE","GALVUS","EPITEGEL","GLICONEURIN","GLICOSAMINA","HEDERAX","IMIPRAMINA","FERMABITAL","IMIPRAMINA","LONGACTIL","INIBINA","ULTRAGESTAN","INIBINA","GLICONEURIN","INSULINA","ITROSPAN","GENICOBEN","KEFLEX","KITAPEN","VENLIFT","RAZAPINA","LEOCONGEM","FINASTERIDA","LUMIGAN","MEMANTINA","MESALAZINA","NEODAZOL","METRONIZADOL","PANTOPRAZOL","MOVATEC","PROFENID","NAPRIX","NEOPINE","NEOZINE","ASSERT","NORIPURUM","VIFERRI","NOVALGINA","FLUIMUCIL","OMEPRAZOL","RISPIRID","CLARITROM","SERTRALINA","METAMUCIL","VITAMINA","CARBONATO","VONAU","TRAMAL","XARELTO","XEFO","ZIRVIT","NASOCLIN","CITOPROFENO","CICLOBENZAPRINA","CLONAZEPAN","SERTRALINA","CERTRALINA","MANELAT","DORMANID","DEPABOTE","NIMODIPINA","BUSONID","01XMCNMC"," CEPACLOR","NORIPURUM","AMPOLAS","FLUXETINA","GARDENAL","BACTROBAN","NEOLIPTIL","NEULIPTIL","POM","POMADA","POMADAS","SONEBON","LORAX","TORVAL","QUADRIDERM","VETIX","FETIZOL","SELOZOCK","DEPAKOTE","TEGRETOL"," PASSIFLORA","BACTRIN","FOLIFER","MUCILOM","TAMARINE","ETOXIN","MEDICAMANTOS","CONDROITINA","BICARBONATO","SPECTRON","CLORIDATRATO","CONDOFLEX","MANIPU","FLUCONAZOL","MASACOL");	
	$sql = "SELECT * from ofertas_solicitacoes  where os_data > '24/08/2012' order by os_observacao";
	$query = pg_query($sql);
	$cont = 1;
	while($r = pg_fetch_array($query)){
	$produto = array();	
?>
	<table class=lista>
		<tr>
			<th width="5">
				Contador
			</th>
			<th width="400">
				Observação
			</th>
			<!-- <th width="200">
				Rascunho
			</th> -->
			<th width="800">
				Provável
			</th>
			<th width="6">
				Código Oferta
			</th>
			
		</tr>
		<tr>
			<td>
				<?=$cont?>
			</td>
			<td>
				<?=$r[os_observacao]?>
			</td>
		<!-- <td>
				
				
				for ($i = 0; $i<count($resul);$i++){
					if(!is_numeric($resul[$i])){
						if(!in_array($resul[$i], $array)){
							//echo (!$resul[$i] ? "Nenhum dado encontrado" : $resul[$i])."<br>";
						}
					}
				}
				
			</td> -->	
			<td>
				<table border="0" width="100%">
				<?
				$resul = explode(" ", $r[os_observacao]);
				for ($i = 0; $i<count($resul);$i++){
					if(!is_numeric($resul[$i])){
						if(!in_array($resul[$i], $array)){
							if(in_array($resul[$i], $medicamento)){
							$resul[$i] = "MEDICAMENTO";
							}
							if($resul[$i] == "FRALDAS"){
							$resul[$i] = "FRALDA";
							}
							$select2  = "SELECT * FROM produtos where produto ilike '%$resul[$i]%'";
							//echo $select2;
							$query2 = pg_query($select2) or die(pg_last_error());
							
							
							//echo "<pre>".print_r($produto,1);
							while ($res2 = pg_fetch_array($query2)){
								//if(!in_array($resul[$i], $produto)){							
								//echo $res2[produto]."<br>";
									array_push($produto, $res2[produto]."==".$res2[cod_prod]);
																 									
								//}		
								
							}							
						
						}
					}
				}
				//echo "<pre>".print_r($produto,1); 
				$produtoUnicos = array_unique($produto);
				//echo "<pre>".print_r($produtoUnicos,1);
			//	for ($i = 0; $i<count($produtoUnicos);$i++){
					//echo $produtoUnicos;
				foreach ($produtoUnicos as $cod => $item){
					echo"<tr><td>";
					//echo "<pre>".print_r($item,1);
					$separa= explode("==", $item);
					 if($separa[0] == ""){
					 	echo "Codigo".$r[cod_os];
					}else{
						echo $separa[0];
					}
					echo"</td>
						<td>
							UPDATE ofertas_solicitacoes set cod_pro = $separa[1] where cod_os = $r[cod_os];
						</td>
					</tr>";				
					
					
						
				}
				//echo "<pre>".print_r($t,1);
				?>
					
			
			</table>
			</td>
				 <td>
				
				
				<?php 
				
				echo  $r[cod_os] 
				?>
				
			</td> 
		</tr>
	</table>

<?php 
$cont++;
	}?>

