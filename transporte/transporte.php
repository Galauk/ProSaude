<?php
	session_start(); 
?>
<link href="tabela.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="modificando.css" />
<link rel="stylesheet" type="text/css" href="estilo.css" />
			<script type="text/javascript"src="Nova pasta/javascript.js"></script>
			<script type"text/javascript" src="javascript.js"></script>
			<script type="text/javascript" src="sources/jscharts.js"></script>
			<script type="text/javascript"src=""></script>
			<script type"text/javascript" src="javascript.js"></script>
<?php
	echo "<link rel='stylesheet' type='text/css' href='estilo.css' />
	<script type'text/javascript' src='sources/jscharts.js'></script>
			
			<script type''text/javascript' src='javascript.js'></script>";
?>
	
<table border="0">
	<tr>
		<td>
		
		<? 
			echo "<table border='0'>";
				echo "<tr>";
					echo "<td align=right width=50>Motorista:</td>";
					echo "<td>";
					echo"<input type=text name='mototista'  >";
					echo"<a href='#'<img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg border=0></a>";
					 "</td>";
				echo"</tr>";
				
				echo"<tr>";
					echo "<td align=right width=30> Nro Carro:</td>";
					echo"<td>";
					echo"<input type=text name='numeroCarro' >";
					echo"<a href='#'<img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg border=0></a>";;
					echo "</td>";	
			echo"</tr>";
				echo"<tr>";
				echo "<td align=right>Origem:</td>
					  <td><input type=text name=origem size=20></td>";
				echo"</tr>";
				
				echo"<tr>";				
				echo "<td align=right>Destino:</td>
						<td><input type=text name=destino size=20></td>";
				echo"</tr>";
				
				echo"<tr>";
				echo "<td align=right  width=30>Hora Saida:</td>
					  <td><input type=text name=horaSaida size=20></td>";
				echo"</tr>";
			echo"<tr>";	  
				echo "<td align=right>Hora Chegada:</td>
					<td><input type=text name=horaChegada size=20></td>";
			echo"</tr>";
			
			echo"<tr>";	  
				echo "<td align=right>KM Saida:</td>
					<td><input type=text name=kmVeiculoEntrada size=20></td>";
			echo"</tr>";
			
			echo"<tr>";	  
				echo "<td align=right width=100>KM Chegada:</td>
					<td><input type=text name=kmVeiculoChegada size=20></td>";
			echo"</tr>";
			echo"<tr>
				<td align=right width=100>KM Chegada:</td>
					<td align=left>";
				echo"<select name='tipoCombustivel size='30'>
						<option></option>
						<option value='D'>Diesel</option>
						<option value='G'>Gasolina</option>
						<option value='A'>Alcool</option>
						<option value='E'>Etenol</option>
						<option value='Gas'>G&aacute;solina</option>
						
					</select>";	
				echo"</td>
				</tr>";		
			
		echo "</table>";
	?>
		<div id="graph">Carregando Gra&aacute;fico..</div>
		<script type="text/javascript">
	var myChart = new JSChart('graph', 'line');
	
	myChart.setDataArray([[1, 1000],[2, 755],[3, 800],[4, 315],[5, 180],[6, 1070],[7, 830],[8, 2130],[9, 2160],[10, 2970]], 'green');
	myChart.setDataArray([[1, 2150],[2, 1250],[3, 900],[4, 380],[5, 720],[6, 565],[7, 1000],[8, 2155],[9, 3000],[10, 2000]], 'gray');
	myChart.setAxisPaddingBottom(40);
	myChart.setTextPaddingBottom(10);
	myChart.setAxisValuesNumberY(5);
	myChart.setIntervalStartY(0);
	myChart.setIntervalEndY(3000);
	myChart.setLabelX([2,'Maio']);
	myChart.setLabelX([4,'Junho']);
	myChart.setLabelX([6,'Julho']);
	myChart.setLabelX([8,'Agosto']);
	myChart.setLabelX([10,'Setembro']);
	myChart.setAxisValuesNumberX(5);
	myChart.setShowXValues(false);
	myChart.setTitleColor('#EBEBEB');
	myChart.setAxisValuesColor('#454545');
	myChart.setLineColor('#A4D314', 'green');
	myChart.setLineColor('#BBBBBB', 'gray');

	myChart.setFlagColor('#9D16FC');
	myChart.setFlagRadius(4);
	myChart.setSize(300, 300);
	myChart.draw();
</script>
		
			
			
			<table class="tabLegenda" align="center">
				<tr>
					<td colspan="2" class="tituloTabela">Legendas</td>
				</tr>
				<tr>
					<td class="legendaAplicar"></td>
					<td><strong>Carro 00121</strong></td>
				</tr>
			
				<tr>
					
					<td class="legendaPreencher"></td>
					<td><b>Carro 00123</b></td>
				</tr>
			</table>
			<br />
			<table border="0" align="center"> 
				<tr>
					<td colspan="2" class="tituloTabela">
						Impress&atilde;o
					</td>
				</tr>
				<tr>
					<td width="50%">
						<label><input type="radio" name="imprimir" value="im" /> Relat&oacute;rios</label>
					</td>				
				</tr>
				<tr>
					<td colspan="2" align="center">
						<img src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/imprimir_on.jpg" / style="padding-right:0px">
					</td>
				</tr>
			</table>
		</td>
		<td valign="top">
			<table border="1" width="1000" class="tabCarteirinha">
				<tr>
					<td align="center" >
						<iframe width="1000" height="500" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"src="http://www.google.com/maps?f=q&amp;source=s_q&amp;hl=pt-BR&amp;geocode=&amp;q=maringa&amp;sll=37.0625,-95.677068&amp;sspn=29.992289,86.572266&amp;ie=UTF8&amp;hq=&amp;hnear=Maring%C3%A1+-+Paran%C3%A1,+Brasil&amp;z=10&amp;ll=-23.427304,-51.937505&amp;output=embed"></iframe><br /><small><a href="http://www.google.com/maps?f=q&amp;source=embed&amp;hl=pt-BR&amp;geocode=&amp;q=maringa&amp;sll=37.0625,-95.677068&amp;sspn=29.992289,86.572266&amp;ie=UTF8&amp;hq=&amp;hnear=Maring%C3%A1+-+Paran%C3%A1,+Brasil&amp;z=10&amp;ll=-23.427304,-51.937505" style="color:#0000FF;text-align:left">Exibir mapa ampliado</a></small>						
					</td>
				</tr>	
			</table>
		</td>
	</tr>
</table>