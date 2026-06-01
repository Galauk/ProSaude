<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario($hotkey = true);

echo "<style type='text/css'>
	tr{ font-size:12px; }
</style>";
echo "<link href=\"../estilo.css\" rel=\"stylesheet\" type=\"text/css\">\n";			

?>
<script language="JavaScript" type="text/javascript" src="../funcoes.js"></script>
<script src=script.js></script>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>GPS - Software de Gest&atilde;o P&uacute;blica</title>
<script src="funcoes.js"></script>
<script type="text/javascript" src="../ajax_motor.js"></script>
<script language="JavaScript">

var maxDay = new Array(31,29,31,30,31,30,31,31,30,31,30,31);


function CheckDate(d,t) {
   date_array = new Array(3);
   date_array[0]=(String(d).substr(6,2))    // dia
   date_array[1]=(String(d).substr(4,2))    // mes
   date_array[2]=(String(d).substr(0,4))    // ano

   if (date_array[0] > maxDay[date_array[1]-1]) {
       alert ("Dia invalido da data! Por favor, verifique!! " + t)
       return 1;
   }
   if (date_array[1] > 12) {
       alert ("Mes invalido da data! Por favor, verifique! " + t)
       return 1;
   }
   if (date_array[2] < 2006) {
       alert ("Ano invalido da data! Por favor, verifique! " + t)
       return 1;
   }
}


function Emite_relatorio(){
	cod_pro  = document.getElementById('programa').value;
	cod_ce	 = document.getElementById('c_estocador').value;
	data_ini = document.getElementById('data_ini').value;
	data_fim = document.getElementById('data_fim').value;

	if (CheckDate(data_ini,"INICIAL")==1) {
	    document.getElementById('data_ini').focus()
      	return false
	 }
	if (CheckDate(data_fim,"FINAL")==1) {
	    document.getElementById('data_fim').focus()
      	return false
	 }
	if (data_ini.length < 10 || data_fim.length < 10) {
		alert('Informe as datas corretamente!');
	return false;
	} else {
	window.open("rel_PacientesPorPrograma.php?cod_pro="+cod_pro+"&cod_ce="+cod_ce+"&data_ini="+data_ini+"&data_fim="+data_fim,null,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
	//return true;
	}
}

function pacientes(codigo,nome,nascimento,mae,cidade) {
	document.getElementById("pac_nome").value = codigo;
}

function hotkey(eventname) {
	if( eventname.keyCode == 118 ) {
		window.open('../list_pacientes.php?id_login=$id_login',null,'height=460,width=800,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes');
        return false;
	}
}
</script>
</head>
<body>
<fieldset>
<legend>Relat&oacute;rio de Pacientes por Programa</legend>
<form method="post" action="<?php echo $PHP_SELF.'?id_login='.$id_login;?>" onSubmit="return Emite_relatorio()">
	<table>
		
		<tr>
			<td width='120'>Programas: </td>
			<td>
            <select name="programa" id="programa" class="box">
                <option value="">----Todos----</option>
            <?
            $sql = "SELECT * FROM programa_atendimento ORDER BY prg_nome ASC";
            $query = pg_query($sql);
            $resultado = pg_num_rows($query);
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
			<td width="120">Data Inicial: </td>
			<td><input type="text" name="data_ini" id="data_ini" class="box" size="15" maxlength="10" onKeypress="return Ajusta_Data(this, event);"></td>
		</tr>
		
		<tr>
			<td width="120">Data Final: </td>
			<td><input type="text" name="data_fim" id="data_fim" class="box" size="15" maxlength="10" onKeypress="return Ajusta_Data(this, event);"></td>
		</tr>
		<tr>
			<td width='120'>Centro Estocador: </td>
			<td>
            <select name="c_estocador" id="c_estocador" class="box">
                <option value="">----Todos----</option>
            <?
            $sql = "SELECT * FROM setor WHERE set_estoque='S' and set_farmacia = 'S' ORDER BY set_nome ASC";
            $query = pg_query($sql);
            $resultado = pg_num_rows($query);
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
		  <td width="120"><input type="image" src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/gerar_relatorio_on.jpg"></td>
			<td align="right"><a href="../rel_index.php?id_login=<?=$id_login?>&opcao=8#tabs-4"><img src=<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/voltar_on.gif border =0></a>
			</td>
		</tr>
		
  </table>

</form>
</fieldset>
</body>