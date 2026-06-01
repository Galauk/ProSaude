<script language="JavaScript" type="text/javascript" src="ajax_motor.js"></script>
<script>
	/*function buscarData()
	{
		ano = document.getElementById("ano").value;
		mes = document.getElementById("mes").value;
		id = document.getElementById("id").value;
		url = "cal.inc.php?mes="+mes+"&ano="+ano+"&id="+id;
		ajax_tudo(url, mostrarCalendario);
	}
	function mostrarCalendario( txt )
	{
		document.getElementById("calendario").innerHTML = txt;
	}*/
</script>
<div style="background:#F0FFF0">
<input type="hidden" name="id" id="id" value="<?=$_GET["id"]?>">
<select name="ano" id="ano" onchange="buscarData()" class="box">
	<option value="">Ano</option>
	<?php
		$ano = date("Y");
		$me = date("m");
		for($i = $ano; $i > 1900; $i--)
		{
			echo "<option value=$i ". ($i == $ano ? "selected" : null) .">$i</option>";
		}
	?>
</select>
<select name="mes" id="mes" onchange="buscarData()" class="box">
	<option value="">M&ecirc;s</option>
	<?php
		$mes = array('Janeiro', 'Fevereiro', 'Mar&ccedil;o', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');
		for($i = 0; $i < count($mes); $i++)
		{
			$m = $i+1;
			echo "<option value='".($m < 10 ? "0".$m : $m)."' ". ($m == $me ? "selected" : null) .">$mes[$i]</option>";
		}
	?>
</select>
<hr>
<div id="calendario"></div>
</div>