<link href="styleRelatorio.css" rel="stylesheet" type="text/css">
<?
	session_start();
 	require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
 	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

$common = new commonClass();
$form = new classForm();
$table = new tableClass();
$data= date("d/m/Y");
echo $common->incJquery();

$data_inicial = $_GET["di"];
$data_final = $_GET["df"];

$cid_codigo = $_GET["cid_codigo"];

$sqlCidade = "select * from cidade where cid_codigo = $cid_codigo";
$queryCidade = pg_query($sqlCidade);
$linhaCidade = pg_fetch_array($queryCidade);


echo "<link href=\"../estilo.css\" rel=\"stylesheet\" type=\"text/css\">";
	
	echo "<center>".$common->commonButton("Imprimir", null, "print.png", "onclick=\"javascript:window.print();this.style.display='none';\"")."</center>";

	echo "<table class=table style='font-size:14px;font-family:verdana' border=0>
			<tr>
				<td width=130><b>GEST&Atilde;O P&Uacute;BLICA DE SA&Uacute;DE</b></td>
				<td width=10 align=right>".date("d/m/Y h:i:s")."</td>
			</tr>
			<tr>
				<td colspan=2>".strtoupper(html_entity_decode($Tit))."</td>
			</tr>
			<tr>
				<td><b> Cidade: </b> $linhaCidade[cid_nome]</td>
				<td><b> cod.IBGE: </b>: $linhaCidade[cid_codigo_ibge]</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>";
		
		echo "<table style='border: 1px solid black;' align='Center' border='1' cellpadding='2' cellspacing='0' width='100%'>
            <tbody><tr>

			  
				  <td colspan='3' class='TabTitulo' align='left' width='38'>
				    <span class='TitRel'>SEXO</span>
				  </td>
			  

	          <td colspan='2' class='TabTitulo' align='center' width='40'><span class='TitRel'>14 e -</span></td>

			  <td colspan='2' class='TabTitulo' align='center' width='40'><span class='TitRel'>15-19</span></td>
			  <td colspan='2' class='TabTitulo' align='center' width='40'><span class='TitRel'>20-24</span></td>
			  <td colspan='2' class='TabTitulo' align='center' width='40'><span class='TitRel'>25-29</span></td>
			  <td colspan='2' class='TabTitulo' align='center' width='40'><span class='TitRel'>30-34</span></td>
			  <td colspan='2' class='TabTitulo' align='center' width='40'><span class='TitRel'>35-39</span></td>
			  <td colspan='2' class='TabTitulo' align='center' width='40'><span class='TitRel'>40-44</span></td>

			  <td colspan='2' class='TabTitulo' align='center' width='40'><span class='TitRel'>45-49</span></td>
			  <td colspan='2' class='TabTitulo' align='center' width='40'><span class='TitRel'>50-54</span></td>
			  <td colspan='2' class='TabTitulo' align='center' width='40'><span class='TitRel'>55-59</span></td>
			  <td colspan='2' class='TabTitulo' align='center' width='40'><span class='TitRel'>60-64</span></td>
			  <td colspan='2' class='TabTitulo' align='center' width='40'><span class='TitRel'>65-69</span></td>
			  <td colspan='2' class='TabTitulo' align='center' width='40'><span class='TitRel'>70-74</span></td>

			  <td colspan='2' class='TabTitulo' align='center' width='40'><span class='TitRel'>75-79</span></td>
			  <td colspan='2' class='TabTitulo' align='center' width='40'><span class='TitRel'>80 e +</span></td>
              <td colspan='2' class='TabTitulo' align='center' width='40'><span class='TitRel'>Total</span></td>
			</tr>
		</table>";
		if($data_inicial == ""){
			$whereData = "where hiper_data = '$data_final'";
		}
		if($data_inicial == "" && $data_final == ""){
			$whereData = "";	
		}
		if($data_inicial == true && $data_final == true){
			$whereData = "where hiper_data between '$data_inicial' and '$data_final'";	
		}
		
		$sqlRelatorio = "select *,to_char(usu_datanasc,'dd/mm/yyyy') as data from hiperdia as hip
								  join usuario as usu
								    on usu.usu_codigo = hip.usu_codigo
									$whereData";
		$queryRelatorio = pg_query($sqlRelatorio);
		$i14M = 0;
		$i15M = 0;
		$i20M = 0;
		$i25M = 0;
		$i30M = 0;
		$i35M = 0;
		$i40M = 0;
		$i45M = 0;
		$i50M = 0;
		$i55M = 0;
		$i60M = 0;
		$i65M = 0;
		$i70M = 0;
		$i75M = 0;
		$i80M = 0;
		
	///////ZERANDO O DE MULHER////////
	
		$i14F = 0;
		$i15F = 0;
		$i20F = 0;
		$i25F = 0;
		$i30F = 0;
		$i35F = 0;
		$i40F = 0;
		$i45F = 0;
		$i50F = 0;
		$i55F = 0;
		$i60F = 0;
		$i65F = 0;
		$i70F = 0;
		$i75F = 0;
		$i80F = 0;
		while($linhaRelatorio = pg_fetch_array($queryRelatorio)){
			$datadd = $linhaRelatorio[data];
			$idade = verIdade($datadd);
			if($idade < 14){
				if($linhaRelatorio["usu_sexo"] == "M"){
					$i14M++;	
				}else{
					$i14F++;	
				}
			}
			if($idade > 15 && $idade < 20){
				if($linhaRelatorio["usu_sexo"] == "M"){
					$i15M++;	
				}else{
					$i15F++;	
				}	
			}
			if($idade > 20 && $idade < 25){
				if($linhaRelatorio["usu_sexo"] == "M"){
					$i20M++;	
				}else{
					$i20F++;	
				}
			}
			if($idade > 25 && $idade < 30){
				if($linhaRelatorio["usu_sexo"] == "M"){
					$i20M++;	
				}else{
					$i20F++;	
				}
			}
			if($idade > 30 && $idade < 35){
				if($linhaRelatorio["usu_sexo"] == "M"){
					$i30M++;	
				}else{
					$i30F++;	
				}
			}
			if($idade > 35 && $idade < 40){
				if($linhaRelatorio["usu_sexo"] == "M"){
					$i35M++;	
				}else{
					$i35F++;	
				}
			}
			if($idade > 40 && $idade < 45){
				if($linhaRelatorio["usu_sexo"] == "M"){
					$i40M++;	
				}else{
					$i40F++;	
				}
			}
			if($idade > 45 && $idade < 50){
				if($linhaRelatorio["usu_sexo"] == "M"){
					$i45M++;	
				}else{
					$i45F++;	
				}
			}
			if($idade > 50 && $idade < 55){
				if($linhaRelatorio["usu_sexo"] == "M"){
					$i50M++;	
				}else{
					$i50F++;	
				}
			}
			if($idade > 55 && $idade < 60){
				if($linhaRelatorio["usu_sexo"] == "M"){
					$i55M++;	
				}else{
					$i55F++;	
				}
			}
			if($idade > 60 && $idade < 65){
				if($linhaRelatorio["usu_sexo"] == "M"){
					$i60M++;	
				}else{
					$i60F++;	
				}
			}
			if($idade > 65 && $idade < 70){
				if($linhaRelatorio["usu_sexo"] == "M"){
					$i65M++;	
				}else{
					$i65F++;	
				}
			}
			if($idade > 70 && $idade < 75){
				if($linhaRelatorio["usu_sexo"] == "M"){
					$i70M++;	
				}else{
					$i70F++;	
				}
			}
			if($idade > 75 && $idade < 80){
				if($linhaRelatorio["usu_sexo"] == "M"){
					$i75M++;	
				}else{
					$i75F++;	
				}
			}
			if($idade > 80 ){
				if($linhaRelatorio["usu_sexo"] == "M"){
					$i80M++;	
				}else{
					$i80F++;	
				}
			}
		}
		
		$totalFeminino = $i14F + $i15F + $i20F + $i25F + $i30F + $i35F + $i40F + $i45F + $i50F + $i55F + $i60F + $i65F + $i70F + $i75F + $i80F;
		$totalMasculino = $i14M + $i15M + $i20M + $i25M + $i30M + $i35M + $i40M + $i45M + $i50M + $i55M + $i60M + $i65M + $i70M + $i75M + $i80M;
		echo "
		<table width=100% border=0  class='lista'>
			<tr>
				<td width='40' align='center' class='Corpoazul'>Masc</td>
				<td width='40' align='center' class='Corpoazul'>	$i14M </td>
				<td width='40' align='center' class='Corpoazul'>	$i15M</td>
				<td width='40' align='center' class='Corpoazul'>	$i20M </td>
				<td width='40' align='center' class='Corpoazul'>	$i25M </td>
				<td width='40' align='center' class='Corpoazul'>	$i30M </td>
				<td width='40' align='center' class='Corpoazul'>	$i35M </td>
				<td width='40' align='center' class='Corpoazul'>	$i40M </td>
				<td width='40' align='center' class='Corpoazul'>	$i45M </td>
				<td width='40' align='center' class='Corpoazul'>	$i50M </td>
				<td width='40' align='center' class='Corpoazul'>	$i55M </td>
				<td width='40' align='center' class='Corpoazul'>  $i60M </td>
				<td width='40' align='center' class='Corpoazul'>	$i65M </td>
				<td width='40' align='center' class='Corpoazul'>	$i70M </td>
				<td width='40' align='center' class='Corpoazul'>	$i75M </td>
				<td width='40' align='center' class='Corpoazul'>	$i80M </td>
				<td width='40' align='center'class='TitRel'>	$totalMasculino </td>
			</tr>
			<tr>
				<td width='40' align='center' class='Corpoazul'>Fem</td>
				<td width='40'align='center' class='Corpoazul'>	$i14F </td>
				<td width='40'align='center' class='Corpoazul'>	$i15F</td>
				<td width='40'align='center'class='Corpoazul'>	$i20F </td>
				<td width='40'align='center' class='Corpoazul'>	$i25F </td>
				<td width='40'align='center' class='Corpoazul'>	$i30F </td>
				<td width='40'align='center' class='Corpoazul'>	$i35F </td>
				<td width='40'align='center' class='Corpoazul'>	$i40F </td>
				<td width='40'align='center' class='Corpoazul'>	$i45F </td>
				<td width='40'align='center' class='Corpoazul'>	$i50F </td>
				<td width='40' align='center' class='Corpoazul'>	$i55F </td>
				<td width='40' align='center' class='Corpoazul'> $i60F </td>
				<td width='40' align='center' class='Corpoazul'>	$i65F </td>
				<td width='40' align='center' class='Corpoazul'>	$i70F </td>
				<td width='40' align='center' class='Corpoazul'>	$i75F </td>
				<td width='40' align='center' class='Corpoazul'>	$i80F </td>
				<td width='40' align='center' class='TitRel'>	$totalFeminino </td>
			</tr>
		</table>";
		
?>