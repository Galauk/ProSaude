<?
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	cabecario($hotkey = true);
    echo "<link href=\"../estilo.css\" rel=\"stylesheet\" type=\"text/css\">\n";
?>
<html>
    <head>
	<script language='javascript' src='../funcoes.js'></script>
        <script>
        var maxDay = new Array(31,29,31,30,31,30,31,31,30,31,30,31);
        function CheckDate(d,t) {
        	   date_array = new Array(3);
        	   date_array[0]=(String(d).substr(0,2))    // dia
        	   date_array[1]=(String(d).substr(3,2))    // mes
        	   date_array[2]=(String(d).substr(6,4))    // anoS
        //alert(date_array[0]+"---"+date_array[1]+"---"+date_array[2])
        	   if (date_array[0] > maxDay[date_array[1]-1]) {
        	       alert ("Dia invalido da data! Por favor, verifique!! " + t);
        	       return 1;
        	   }
        	   if (date_array[1] > 12) {
        	       alert ("Mes invalido da data! Por favor, verifique! " + t);
        	       return 1;
        	   }
        	   if (date_array[2] < 2006) {
        	       alert ("Ano invalido da data! Por favor, verifique! " + t);
        	       return 1;
        	   }
        	}
            function emitir_relatorio()
            {
                prg_codigo = document.getElementById('programa').value;
				set_codigo = document.getElementById('centro_estocador').value;
				dt_ini = document.getElementById('data_ini').value;
				dt_fim = document.getElementById('data_fim').value;
			    if (CheckDate(data_ini,"INICIAL")==1) {
				    document.getElementById('data_ini').focus()
			      	return false
				 }
				if (CheckDate(data_fim,"FINAL")==1) {
				    document.getElementById('data_fim').focus()
			      	return false
				 }
				if(dt_ini == "")
				{
					alert("Favor preencher a data inicial.");
					document.getElementById('data_ini').focus();
					return false;
				}
				if(dt_fim == "")
				{
					alert("Favor preencher a data final.");
					document.getElementById('data_fim').focus();
					return false;
				}

                window.open("relatorio/rel_PacientesAtendidosPorPrograma.php?prg_codigo="+prg_codigo+"&set_codigo="+set_codigo+"&data_ini="+dt_ini+"&data_fim="+dt_fim,null,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
                return true;
            }
        </script>
    </head>
    <body>
	<fieldset>
	<legend><b>Pacientes atendidos por programa</b></legend>
	<form method="post" onsubmit="return emitir_relatorio()">
        <table>
            <tr>
                <td width='30'>Programa: </td>
                <td>
                <select name="programa" id="programa" class="box">
                    <?
                    $sql = "SELECT * FROM programa_atendimento ORDER BY prg_nome ASC";
                    $query = pg_query($sql);
                    $resultado = pg_num_rows($query);
                    echo "<option value=''>TODOS</option>";
                        if ($resultado != 0) {
                            while($row = pg_fetch_array($query)) {
                                echo "<option value='$row[prg_codigo]'>$row[prg_nome]</option>";
                            }
                        }
                    ?>
                </select>
                </td>
            </tr>
	    <tr>
		<td><label for='data_ini'>Data Inicial</label></td>
		<td><input type='text' name='data_ini' id='data_ini' class='box' onkeypress='Ajusta_Data(this,event)'></td>
	    </tr>
	    <tr>
		<td><label for='data_fim'>Data Final</label></td>
		<td><input type='text' name='data_fim' id='data_fim' class='box' onkeypress='Ajusta_Data(this,event)'></td>
	    </tr>
	    <tr>
		<td><label for='centro_estocador'>Centro Estocador</label></td>
                <td><select name="centro_estocador" id="centro_estocador" class="box">
                    <?
                    $sql = "SELECT * FROM setor where set_estoque='S' and set_farmacia = 'S' order by set_nome ASC";
                    $query = pg_query($sql);
                    $resultado = pg_num_rows($query);
                    echo "<option value=''>TODOS</option>";
                        if ($resultado != 0) {
                            while($row = pg_fetch_array($query)) {
                                echo "<option value='$row[set_codigo]'>$row[set_nome]</option>";
                            }
                        }
                    ?>
                </select>
		</td>
	    </tr>
            <tr>
                <td><input type="image" src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/gerar_relatorio_on.jpg"></td>
                <td align="right"><a href="../rel_index.php?id_login=<?=$id_login?>&opcao=8"><img src=<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/voltar_on.gif border =0></a></td>
            </tr>
        </table>
        </form>
	</fieldset>
    </body>
</html>
