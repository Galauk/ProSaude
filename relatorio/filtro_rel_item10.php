<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.db.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	include_once $_SESSION[root].$_SESSION[modulo]."json.inc.php";

	cabecario();
	echo monta_calendario();

?>
<script type="text/javascript" src="../funcoes.js"></script>
<script type="text/javascript" src="../ajax_motor.js"></script>
<script type="text/javascript" src="cidades.js"></script>
<script type="text/javascript" src="../json.js"></script>
<script type="text/javascript">
function relatorio()
{
	try
	{
		var cid_codigo = $('cid_codigo').value,
			data_ini = $('data_ini').value,
			data_fin = $('data_fin').value,
			mes = $('mes').value,
			ano = $('ano').value,
			prestador = $('med_codigo_solicitante').value,
			medico = $('med_autorizador').value,
			tipo_p = $('tipo_periodo').checked,
			tipo_c = $('tipo_competencia').checked,
			procedimento = $('proc_codigo').value;
		
		
		if( tipo_p && ( data_ini == "" || data_fin == "" ) )
		{
			alert('Preencha a data corretamente.');
			return false;
		}
		
		var url = "rel_item10.php?cid_codigo="+cid_codigo+"&med_codigo_solicitante="+prestador;
		url += "&proc_codigo="+procedimento+'&med_autorizador='+medico;
		
		if( tipo_p )
			url += "&data_ini="+data_ini+"&data_fin="+data_fin;
		else
			url += "&mes_compet="+mes+'&ano_compet='+ano;
		
		//alert(url);
		
		window.open( url, "relatorio10", "height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes,resizable=yes" );
		return false;
	}
	catch( ex )
	{
		alert( ex );
		return false;
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
/*
  
  NAO PRECISA MAIS
  A TABELA DE MEDICOS ESTÃO PUXANDO A CIDADE DE OUTRO ESQUEMA... (COMMON)

function arruma_medicos()
{
	try
	{
		var Prestador = $("med_autorizador"), Medico = $('med_codigo_solicitante'),
			Cidade = $F('cid_codigo');
		
		Prestador.length = 1;
		Prestador.options[0].value = 0;
		Prestador.options[0].text = "...carregando..." ;
		Prestador.disabled = true;
		
		Medico.length = 1;
		Medico.options[0].value = 0;
		Medico.options[0].text = "...carregando..." ;
		Medico.disabled = true;
		
		alert("op_rel_item10.php?cid="+Cidade+'&prestador=S');
		ajax_tudo( "op_rel_item10.php?cid="+Cidade+'&prestador=S', arruma_medico_callback );
		//ajax_tudo( "op_rel_item10.php?cid="+Cidade+'&prestador=N', arruma_prestador_callback );
	}
	catch( ex )
	{
		alert( ex );
	}
}

function arruma_medico_callback( respTxt )
{
	try
	{
		alert( respTxt );
	}
	catch( ex )
	{
		alert( ex );
	}
}

function arruma_prestador_callback( respTxt )
{
	try
	{
		alert( respTxt );
	}
	catch( ex )
	{
		alert( ex );
	}
}

function arruma_medicos_callback( text )
{
    //alert(text);
    var Medico = ( eval(text) );
    var Sel = document.getElementById("med_codigo");
	
    if( Medico.length == 0 )
    {
        Sel.length = 1;
        Sel.options[ 0 ] = new Option( 'nenhum', 0 );
        Sel.disabled = true;
    }
    else
    {
        Sel.length = 1;
        Sel.options[ 0 ] = new Option( '--Todos--', -1 );
        Sel.disabled = false;
    }
    
    for( var i=0; i < Medico.length; i++ )
    {
        Medico[ i ].med_nome = unescape( Medico[ i ].med_nome );
        Sel.options[ Sel.options.length ]= new Option( Medico[ i ].med_nome,  Medico[ i ].med_codigo );
    }
}
*/
</script>
</head>	
<body>
<form name="form1" method="post" action="<?=$PHP_SELF;?>" onsubmit="return relatorio()">
<fieldset>
<legend>N&uacute;meros de Laudos Solicitados</legend>
<table>
    
	<tr>
		<td width="100">Munic&iacute;pio</td>
		<td>
			<select name="estado" id="estado" class="box" onchange="atualiza_cidade(this,'cid_codigo','cid_codigo')">
				<option value="0">..</option>
				<?php
					$sql = db_query("SELECT DISTINCT uf_sigla FROM cidade ORDER BY 1");
					while ( $uf = pg_fetch_array($sql) )
					{
						echo "\n\t\t\t<option>{$uf[0]}</option>";
					}
				?>
			</select>
			<select name="cid_codigo" id="cid_codigo" class="box" style="width:150px;">
				<option value="-1">...Todos...</option>
			</select>
		</td>
	</tr>
	
    <tr>
        <td><label for="med_autorizador">M&eacute;dico</label></td>
        <td><select name="med_autorizador" id="med_autorizador" class="box">
                <option value=-1>--Todos--</option>
				<?php
                    $qry = db_query( "SELECT med_codigo, med_nome FROM medico WHERE prestador_servico = 'N' ORDER BY med_nome");
                    while ( $proc = pg_fetch_array($qry) )
					{
                        echo "\n\t\t\t<option value='{$proc[0]}'>{$proc[1]}</option>";
                    }
                ?>
		
            </select>
        </td>
    </tr>
	
	<tr>
        <td><label for="med_codigo_solicitante">Prestador</label></td>
        <td><select name="med_codigo_solicitante" id="med_codigo_solicitante" class="box">
                <option value=-1>--Todos--</option>
				<?php
                    $qry = db_query( "SELECT med_codigo, med_nome FROM medico WHERE prestador_servico = 'H' ORDER BY med_nome");
                    while ( $proc = pg_fetch_array($qry) )
					{
                        echo "\n\t\t\t<option value='{$proc[0]}'>{$proc[1]}</option>";
                    }
                ?>
		
            </select>
        </td>
    </tr>
	
    <tr>
        <td><label for="proc_codigo">Procedimento</label></td>
        <td><select name="procedimento" id="proc_codigo" class="box">
                <option value=-1>--Todos--</option>
                <?php
                    $qry = db_query( "SELECT proc_codigo, proc_nome FROM procedimento ORDER BY proc_nome");
                    while ( $proc = pg_fetch_array($qry) )
					{
                        echo "\n\t\t\t<option value='{$proc[0]}'>{$proc[1]}</option>";
                    }
                ?>
		
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
				   onkeypress="Ajusta_Data(this,event)"/><!-- readonly="readonly"-->
			
			entre
			&nbsp;
			<input type="text" class="box" name="data_fin" id="data_fin" size="12" maxlength="10"
				   onkeypress="Ajusta_Data(this,event)"/><!-- readonly="readonly"-->
			
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
		<td><input type="image" src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/gerar_relatorio_on.jpg" name="emitir" value="Emitir" /></td>
		<td><a href="../rel_index.php?opcao=5#tabs-5"><img src='<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/voltar_on.gif' border='0'></a></td>
	</tr>
</table>
</fieldset>
</form>

</body>
</html>
