<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	cabecario();

?>
<html>
<head>
<title>Relat&oacute;rio Falta de Profissional</title>
<script src="funcoes.js"></script>
<script type="text/javascript">
var gdtInicial
var gdtFinal
var ghrInicial
var ghrFinal
var gUnidade
var gHora
var gHoje
var maxDay = new Array(31,29,31,30,31,30,31,31,30,31,30,31);
function CheckDate(d,MSG) {
	   date_array = new Array(3);
	   date_array[0]=(String(d).substr(6,2))    // dia
	   date_array[1]=(String(d).substr(4,2))    // mes
	   date_array[2]=(String(d).substr(0,4))    // ano

	   if (date_array[0] > maxDay[date_array[1]-1]) {
	       alert ("Dia invalido da data " + MSG)
	       return 1
	   }
	   if (date_array[1] > 12) {
	       alert ("Mes invalido da data " + MSG)
	       return 1
	   }
	   if (date_array[2] < 1998) {
	       alert ("Ano invalido da data " + MSG)
	       return 1
	   }
	}

	function CheckCall() {
		 var dtini = document.getElementById('dtini').value;
		    var dtfim = document.getElementById('dtfim').value;
		    var codmed = document.getElementById('medico').value;

	   gHoje = new Date();
	   gHoje = gHoje.getDate() + "-" + (gHoje.getMonth() + 1) + "-" + gHoje.getFullYear()

	   gdtInicial=document.getElementById('dtini').value;
	   gdtFinal  =document.getElementById('dtfim').value;
	  
	   gUnidade  =document.getElementById('medico').value;


	   if (gdtInicial == '') {
	       alert ("Informe Data Inicial");
	       document.getElementById('dtini').focus();
	       return false;
	   }
	   if (gdtFinal == '') {
		   alert ("Informe Data Final");
	       document.getElementById('dtfim').focus();
	       return false;
	       
	   }
	   var d1=gdtInicial
	   var d2=gdtFinal
	   for (var i = 0; i < d1.length; i++) {
	        if (d1.charAt(i) == "-") {
	           var dat1=parseInt(d1.split("-")[2].toString()+d1.split("-")[1].toString()+d1.split("-")[0].toString())
	           var dat2=parseInt(d2.split("-")[2].toString()+d2.split("-")[1].toString()+d2.split("-")[0].toString())
	        }
	        else 
	        if (d1.charAt(i) == "/") {
	           var dat1=parseInt(d1.split("/")[2].toString()+d1.split("/")[1].toString()+d1.split("/")[0].toString())
	           var dat2=parseInt(d2.split("/")[2].toString()+d2.split("/")[1].toString()+d2.split("/")[0].toString()) 
	        }
	   }
	   if (CheckDate(dat1,"INICIAL")==1) {
	      document.getElementById('dtini').focus()
	       return false
	   }
	   if (CheckDate(dat2,"FINAL")==1) { 
		   document.getElementById('dtfim').focus()()
	       return false
	   }
	   if  (dat1 > dat2) { 
	        alert("Data Inicial(" + gdtInicial + ") maior que Final(" + gdtFinal + ")")
	        document.getElementById('dtini').focus()
	        return false
	   }
	/*
	   var Hoje = new Date();
	   Hoje     = Hoje.getDate() + "-" + (Hoje.getMonth() + 1) + "-" + Hoje.getFullYear();
	   if (!CompData(gdtInicial,Hoje)) {
	       alert("Data Inicial(" + gdtInicial + ") maior que Hoje(" + Hoje + ")");
	       document.frm_AtPAM.dt_inicial.focus();
	       return false;
	   }
	   if (CompData(ghrInicial,ghrFinal)) {
	       alert("Hora Inicial(" + ghrInicial + ") maior que Final(" + ghrFinal + ")");
	       return false;
	   }
	*/
	 window.open("relatorio/rel_falta_profissional.php?dtini="+dtini+"&dtfim="+dtfim+"&codmed="+codmed,null,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes")
	}
function Emitir_relatorio(){
    var dtini = document.getElementById('dtini').value;
    var dtfim = document.getElementById('dtfim').value;
    var codmed = document.getElementById('medico').value;

    if ((document.getElementById('dtini').value == 0) && (document.getElementById('dtfim').value== 0))
    {
        alert("O periodo deve ser preenchido");
        return false;
    }
    window.open("relatorio/rel_falta_profissional.php?dtini="+dtini+"&dtfim="+dtfim+"&codmed="+codmed,null,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes")
    return true;              
}
    
</script>
</head>
<body>
<FIELDSET>
<LEGEND>M&eacute;dicos Faltosos</LEGEND>
<form name="frm1" method="post" action="<?=$PHP_SELF;?>" onsubmit="return CheckCall()">
<table>
	<tr>
		<td width="10"><label for="medico">M&eacute;dico</label></td>
		<td><select name="medico" id="medico" class="box">
			<option value=-1>---Todos---</option>
                        <?php
                            $select = "select med_codigo, med_nome from medico order by 2";
                            $exec = db_query($select);
                            while ($reg = pg_fetch_array($exec))
                            {
                                echo "<option value='$reg[med_codigo]'>$reg[med_nome]</option>";                                
                            }
                        ?>
			</select>
		</td>
	</tr>
	<tr>
                <td width="10"><label for="dtini">Data Inicial</label></td>
                <td><input type="text" name="dtini" id="dtini"  maxlength=10 class="box" onkeypress="return Ajusta_Data(this,event);"></td>
        </tr>
        <tr>
                <td width="10"><label for="dtfim">Data Final</label></td>
                <td><input type="text" name="dtini" id="dtfim"  maxlength=10 class="box" onkeypress="return Ajusta_Data(this,event);"></td>
        </tr>
        <tr>
            <td><input src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/gerar_relatorio_on.jpg" name="emitir" value="Emitir" type="image"></td>
            <td align="left"><a href="rel_index.php?id_login=$id_login&amp;opcao=5#tabs-2"><img src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/voltar_on.gif" border="0"></a></td>
        </tr>
</table>
</form>
</FIELDSET>
</body>
</html>

