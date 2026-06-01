<?php
/**
 * Adicionado mascara nas datas e retirado o calendario 
 */
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.db.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	cabecario();
	echo monta_calendario();
?>
<script src="../ajax_motor.js" type="text/javascript"></script>
<script src="../funcoes.js" type="text/javascript"></script>
<script type="text/javascript">
function Emite_relatorio()
{
	try
	{
		var municipio = $F('municipio'), data_ini = $F('data_ini'), data_fin = $F('data_fin'),
			mes = $F('mes'), ano = $F('ano'),
			tipo_p = $('tipo_periodo').checked, tipo_c = $('tipo_competencia').checked ;
		
	
		if( tipo_p && ( data_ini == "" || data_fin == "" ) )
		{
			alert('Preencha a data corretamente.');
			return false;
		}
		
		var url = "rel_item9.php?municipio="+municipio;
		
		if( tipo_p )
			url += "&data_ini="+data_ini+"&data_fin="+data_fin;
		else
			url += "&mes_compet="+mes+'&ano_compet='+ano;
			
		//alert(url);
		window.open(url,null,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
		return false;
	}
	catch( ex )
	{
		alert( ex );
	}
}

function escolhe( quem )
{
	try
	{
		var p = $('form_periodo'), c = $('form_competencia');
		
		if( quem == 1 )
		{
			p.style.display = 'none';
			c.style.display = 'table-row';
		}
		else
		{
			c.style.display = 'none';
			p.style.display = 'table-row';
		}
	}
	catch( ex )
	{
		alert( ex );
	}
}

</script>
<script src="cidades.js" type="text/javascript"></script>
<script src="../json.js" type="text/javascript"></script>
</head>

<form name="form1" method="post" action="<?php echo $PHP_SELF;?>" onsubmit="return Emite_relatorio()">
<fieldset>
<legend>Reinterna&ccedil;&atilde;o</legend>
<table cellpadding="3">
	<tr>
		<td width="100">Munic&iacute;pio</td>
		<td>
			<select name="estado" id="estado" class="box" onchange="atualiza_cidade(this,'municipio')">
				<option value="0">..</option>
				<?php
					$sql = db_query("SELECT DISTINCT uf_sigla FROM cidade ORDER BY 1");
					while ( $uf = pg_fetch_array($sql) )
					{
						echo "\n\t\t\t<option>{$uf[0]}</option>";
					}
				?>
			</select>
			<select name="municipio" id="municipio" class="box" style="width:150px;">
				<option value="-1">...Todos...</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>
			<label>Per&iacute;odo
					<input type="radio" name="tipo" id="tipo_periodo" checked="true" onchange="escolhe(2)"/></label>
			&nbsp; &nbsp;
			<label>Compet&ecirc;ncia
					<input type="radio" name="tipo" id="tipo_competencia" onchange="escolhe(1)" /></label>
		</td>
	</tr>
	
	<tr id="form_periodo">
		<td><label>Per&iacute;odo </label></td>
		<td>
			<input type="text" class="box" name="data_ini" id="data_ini" size="12" maxlength="10"
				   onkeypress="return Ajusta_Data(this,event)"/>
			&nbsp;
			<!--<input type=image src=<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/calendario.png onclick="abrirCalendario('data_ini');return false;"
				   style='vertical-align: middle;'/>-->
			&nbsp;
			entre
			&nbsp;
			<input type="text" class="box" name="data_fin" id="data_fin" size="12" maxlength="10"
				   onkeypress="return Ajusta_Data(this,event)"/>
			&nbsp;
			<!--<input type='image' src='<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/calendario.png' onclick="abrirCalendario('data_fin');return false;"
				   style='vertical-align: middle;'/>-->
		</td>
	</tr>

	<tr id="form_competencia" style="display:none;">
		<td>Compet&ecirc;ncia</td>
		<td>
			<?php
			
				echo "\n\t\t<select name=\"mes\" id=\"mes\" class=\"box\">";
				echo meses_select( date('m') );
				echo "\n\t\t</select>";
			
				echo "\n\t\t<select name=\"ano\" id=\"ano\" class=\"box\">";
				for( $i = date('Y') - 5; $i < date('Y') + 5; $i++ )
					echo "\n\t\t\t<option".( $i == date('Y') ? ' selected' : '' ).">$i</option>";
				echo "\n\t\t</select>";
			?>
		
		 </td>
	</tr>
	<tr>
		<td colspan="2">
			<input type="image" src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/gerar_relatorio_on.jpg" name="emitir" value="Emitir" />
			&nbsp;&nbsp;
			<a href="../rel_index.php?opcao=5#tabs-5"><img src='<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/voltar_on.gif' border='0'></a>
		</td>
	</tr>
</table>
</fieldset>
</form>

</body>
</html>