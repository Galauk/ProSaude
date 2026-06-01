<?php 
	require_once 'global.php';
	
?><script language="JavaScript" type="text/javascript" src="ajax_motor.js"></script>
<script>
function selecionar_tudo(){
   for (i=0;i<document.exames.elements.length;i++)
      if(document.exames.elements[i].type == "checkbox")
         document.exames.elements[i].checked=1
} 

function deselecionar_tudo(){
   for (i=0;i<document.exames.elements.length;i++)
      if(document.exames.elements[i].type == "checkbox")
         document.exames.elements[i].checked=0
}

function acoesDoBanco(){
	var mlz_datadacoleta = document.getElementById('mlz_datadacoleta').value;
	var mlz_quantidade = document.getElementById('mlz_quantidade').value;
	var mlz_bioquimico = document.getElementById('mlz_bioquimico').value;
	var labm_codigo = document.getElementById('labm_codigo').value;
	var id_login = document.getElementById('id_login').value;
	var mlz_coletado = document.getElementById('mlz_coletado').value;
	var usu_codigo = document.getElementById('usu_codigo').value;
	var cad_exame = document.getElementById('cad_exame').value;
	var checkbox = document.getElementsByName('check[]');
	var checkout = "";
	for (i=0;i<checkbox.length;i++){
		if (checkbox[i].getAttribute("type") == "checkbox" && checkbox[i].checked == true) {
			var checkout = checkout + checkbox[i].value + "|";
		}	
	}
	url = "salvaAnalise.php?mlz_datadacoleta="+mlz_datadacoleta+"&mlz_quantidade="+mlz_quantidade+"&mlz_bioquimico="+mlz_bioquimico+"&checkout="+checkout+"&labm_codigo="+labm_codigo+"&id_login="+id_login+"&mlz_coletado="+mlz_coletado+"&cad_exame="+cad_exame+"&usu_codigo="+usu_codigo;
	ajax_tudo(url,retorno);
}

function retorno(txt){
	var arrayList = txt.split("|");
	if(arrayList[1] == "e"){
		alert('Erro ao Inserir!');
		window.location = "exa_materialdeanalise_iframeAGE.php?usu_codigo="+arrayList[0];
	}else{
		alert('Salvo com Sucesso!');
		window.location = "exa_materialdeanalise_iframeAGE.php?usu_codigo="+arrayList[0];
	}
	//window.location = "exa_materialdeanalise_iframeAGE.php?txt";
}
</script>
<?php
session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
verauth($id_login);
require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

cabecario( $hotkey = true);
$common = new commonClass();
echo $common->incJquery();

$id = pg_fetch_array( pg_query("select max(cad_exame) as cad_exame from cadastrodoexame where usu_codigo = $usu_codigo"));
//echo "select max(cad_exame) as cad_exame from cadastrodoexame where usu_codigo = $usu_codigo";
$cad_exame = $id[cad_exame];
$executo = pg_fetch_array(pg_query("select * from cadastrodoexame where cad_exame = $cad_exame"));
$labm_codigo = $executo['labm_codigo'];
$acao = $_GET['acao'];
if(empty($cad_exame)) {
	exit;
}
if(empty($acao)) {

	echo "<table class='lista'>
        <tr>
         <th width=40%>Exames</th>
         <th colspan=3>Situa&ccedil;&atilde;o</th>
        </tr>";

	$sql = "SELECT *
			  FROM itensdoexame as item
			  JOIN procedimento as proc
				ON item.proc_codigo = proc.proc_codigo
			 WHERE cad_exame = $cad_exame";
	$query = pg_query($sql);
	while($row=pg_fetch_array($query)) {
		$proc = pg_fetch_array(pg_query("select TRANSLATE(proc_nome, 'ZZZ-', '') as nome,*from procedimento where proc_codigo = $row[proc_codigo]"));
		if(trim($row[itx_status])=="C") {
			$tpcoleta = "<font color=green><b>Coletado</b></font>";
		} else {
			$tpcoleta = "<font color=red><b>N&atilde;o Coletado</b></font>";
		}
		echo "<tr>
         <td width=50%>$proc[nome]</td>
         <td width=50%>$tpcoleta</td>
		 <td width=10><a href=\"exa_individualidades_coleta.php?acao=edit_ind&itx_codigo=$row[itx_codigo]&proc_nome=$proc[nome]&labm_codigo=$labm_codigo&cad_exame=$cad_exame&usu_codigo=$usu_codigo\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/btnedit_on.png border=0></a>
         <td width=10><a href=\"$PHP_SELF?acao=del&mlz_codigo=$row[mlz_codigo]&labm_codigo=$labm_codigo&cad_exame=$cad_exame&usu_codigo=$usu_codigo\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/btndel_on.png border=0></a 
	</td>
        </tr>";
	}
	echo "
		<tr>
			 <td width=10><a href=\"exa_materialdeanalise_iframeAGE.php?acao=form_edit&cad_exame=$cad_exame&labm_codigo=$labm_codigo&usu_codigo=$usu_codigo\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.png border=0></a></td>
			 <td colspan=3>".$common->commonButton("Imprimir Etiquetas", null, "label.png", "onClick=\"window.open('imprimirEtiquetas.php?cad_exame=$cad_exame&usu_codigo=$usu_codigo','nv','width=750,height=400');\"")."</td>
		</tr>
	</table><br>";
}
if(($acao=="form_add" OR $acao=="form_edit")) {
	$verifica = pg_query("select i.itx_codigo,i.cad_exame,i.proc_codigo,i.itx_observacao,i.itx_urgente,i.itx_status,p.proc_nome from itensdoexame as i left join procedimento as p on p.proc_codigo = i.proc_codigo where i.cad_exame = $cad_exame");
	if(pg_num_rows($verifica)==0) {
		echo "<br><br>
                <table height=100 width=50% align=center cellspacing=0cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:red;'>
                 <tr bgcolor=f9f9f9>
                   <td align=center><font size=2 color=red><b>ERRO<br>Nao e possivel cadastrar pois nao foram selecionados os ITENS DE EXAME</b></font></td>
                 </tr>
                </table><br>";
		echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='exa_materialdeanalise_iframeAGE.php?id_login=$id_login&cad_exame=$cad_exame&labm_codigo=$labm_codigo'\", 4000);
              </SCRIPT>";
		exit;
	}

	echo "<form method=post action=$PHP_SELF name='exames'>";
	if($acao=="form_edit") {
		$row = pg_fetch_array(pg_query("SELECT med_codigo, mlz_codigo, itx_codigo, tma_codigo, mlz_coletado, to_char(mlz_datadacoleta,'DD/MM/YYYY') as mlz_datadacoleta, mlz_quantidade, id_login, labm_codigo, mlz_conservacao, mlz_observacao, mlz_motivo FROM materialdeanalise WHERE mlz_codigo = $mlz_codigo"));
		if($row[mlz_coletado]=="S") {
			$it_1 = "selected";
			$it_2 = "";
		} else {
			$it_1 = "";
			$it_2 = "selected";
		}
		echo "<input type=hidden name=acao value=edit>";
		echo "<input type=hidden name=mlz_codigo value=$mlz_codigo>";
		echo "<input type=hidden name=cad_exame id='cad_exame' value=$cad_exame>";
		echo "<input type=hidden name=labm_codigo id='labm_codigo' value=$labm_codigo>";
		echo "<input type='hidden' name='id_login' id='id_login' value='$id_login'>";
		echo "<input type='hidden' name='usu_codigo' id='usu_codigo' value='$usu_codigo'>";
		$btn = "editar";
	} else {
		echo "<input type=hidden name=acao value=addi>";
		echo "<input type=hidden name=cad_exame id='cad_exame' value=$cad_exame>";
		echo "<input type=hidden name=labm_codigo id='labm_codigo' value=$labm_codigo>";
		$btn = "adicionar";
	}
	if($row[mlz_quantidade]=="") { $mlz_quantidade = 1; } else { $mlz_quantidade = $row[mlz_quantidade]; }
	if ($row[mlz_datadacoleta]=="") { $data = date("d/m/Y"); } else { $data = $row[mlz_datadacoleta]; }
	echo "<table width=100% cellspacing=2 cellpadding=0 border=0>
    	<tr>
         <td align=right width=20%>Data da Coleta:</td>
         <td><input type=text name=mlz_datadacoleta id='mlz_datadacoleta' size=12 class=box onkeypress='Ajusta_Data(this, event);' maxlength=10 value='$data'></td>
        </tr>
	   <tr>
	    <td align=right width=100>Quantidade:</td>
	    <td><input type=text name=mlz_quantidade id='mlz_quantidade' class=box size=2 value='$mlz_quantidade'></td>
	   </tr>
           <tr>
             <td width=10 align=right>Coletado:</td>
             <td><select name=mlz_coletado id='mlz_coletado' class=box>
                <option value='N' $it_1>NAO</option>
                <option value='S' $it_2>SIM</option>
             </select></td>
           </tr>
           <tr>
            <td colspan=2>Bioquimico:</td>
	   </tr>
	   <tr>
            <td colspan=2><select name=mlz_bioquimico id='mlz_bioquimico' class=box style='width:420px'>
             <option value=0>..:: Selecione o Bioquimico ::..</option>";
	$sql = pg_query("select *from medico_especialidade as esp left join medico as m on esp.med_codigo=m.med_codigo where esp_codigo='1023'");
	while($rr = pg_fetch_array($sql)) {
		echo ($rr[med_codigo]==$row[med_codigo])?"<option value='$rr[med_codigo]' selected>$rr[med_nome]</option>":"<option value='$rr[med_codigo]'>$rr[med_nome]</option>";
	}
	echo "</select></td>
           </tr>
           <tr>
            <td colspan=2>Exames a Editar:</td>
	   </tr>
	   <tr>";
	$sql = "select i.itx_codigo,i.cad_exame,i.proc_codigo,i.itx_observacao,i.itx_urgente,i.itx_status,TRANSLATE(proc_nome, 'ZZZ-', '') as procnome from itensdoexame as i left join procedimento as p on p.proc_codigo = i.proc_codigo where i.cad_exame = $cad_exame";
	$qq = db_query($sql);
	$qq2 = db_query($sql);
	$qq3 = db_query($sql);

	$i = 0;
	while($rr = pg_fetch_array($qq)) {
		echo"<td>
		$rr[procnome]<input type='checkbox' name='check[]' id='check[]' value='$rr[itx_codigo]' ".($rr["itx_status"] == "C" ? "checked=checked" : "").">
		 </td>";
		$i++;
		if(($i%3) == 0)
		{
			echo "</tr>
			  <tr>";	
		}

	}

	echo "</tr>
		  <tr>
		 	<td>
				<span onClick=\"selecionar_tudo()\" style='cursor:pointer'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_mini_todos_on.jpg'></span>
				<span onClick=\"deselecionar_tudo()\" style='cursor:pointer'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/deletar_on.jpg'></span>				
			</td>
		  </tr>";
	$gb = pg_fetch_array($qq2);

	$sqlOrientacoes = "SELECT *
    					 FROM procedimento_orientacoes AS p
    					 JOIN orientacoes_exames AS oe
    					   ON p.ori_exa_codigo = oe.ori_exa_codigo
    					WHERE proc_codigo = $gb[proc_codigo]";
	$queryOrientacoes = pg_query($sqlOrientacoes);
	$numRows = pg_num_rows($queryOrientacoes);
	if($numRows > 0){
		echo "
    	  <tr>
		  	<td>
		  		<fieldset>
		  			<legend>Orienta&ccedil;&otilde;es</legend>
		  			<table border=0>";
		while($gb3 = pg_fetch_array($qq3)){
			echo "<tr>
		  							<td>
		  							$gb3[procnome]
		  							</td>
		  							<td>";
		  							while($regs = pg_fetch_array($queryOrientacoes)){
		  								echo "<b>$regs[ori_exa_orientacoes]</b> <br/>";
		  							}
		  							echo"
		  							</td>
		  						</tr>";
		}

		echo"
		  			</table>
		  			
		  		</fieldset>
		  	</td>
		  </tr>";
	}
	echo"
		  <tr>
		  	<td>".
	//<span onClick=\"acoesDoBanco()\" style='cursor:pointer'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg'></span>
	$common->commonButton("continuar",null,"editar_on.png","onClick=\"acoesDoBanco()\"")
	."</td>
		  </tr>
          </table></form>";
}



if($acao=="addi") {
	$ver = pg_query("select *from materialdeanalise where itx_codigo = $itx_codigo");
	if(pg_num_rows($ver)>="1") {
		echo "<br><br>
                <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                 <tr bgcolor=f9f9f9>
                   <td align=center><font size=2 color=red><b>Exame Duplicado</b></font></td>
                 </tr>
                </table><br>";
		echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='exa_materialdeanalise_iframeAGE.php?id_login=$id_login&cad_exame=$cad_exame&labm_codigo=$labm_codigo'\", 2000);
              </SCRIPT>";
		exit;
	}
	if($itx_codigo=="0") {
		echo "<br><br>
                <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                 <tr bgcolor=f9f9f9>
                   <td align=center><font size=2 color=red><b>Selecione o Item</b></font></td>
                 </tr>
                </table><br>";
		echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='exa_materialdeanalise_iframeAGE.php?id_login=$id_login&cad_exame=$cad_exame&labm_codigo=$labm_codigo'\", 2000);
              </SCRIPT>";
		exit;
	}

	$stmt = "INSERT INTO materialdeanalise (med_codigo,
											cad_exame,
											mlz_datadacoleta,
											itx_codigo, 
											tma_codigo, 
											mlz_coletado, 
											mlz_quantidade, 
											id_login, 
											labm_codigo, 
											mlz_conservacao, 
											mlz_observacao, 
											mlz_motivo
								 ) VALUES ( 
											".intval($med_codigo).", 
											".intval($cad_exame).", 
											'".trim(strtoupper($mlz_datadacoleta))."', 
											".intval($itx_codigo).", 
											".intval($tma_codigo).", 
											'$mlz_coletado', 
											".intval($mlz_quantidade).", 
											".intval($id_login).", 
											".intval($labm_codigo).", 
											'".trim(strtoupper($mlz_conservacao))."', 
											'".trim(strtoupper($mlz_observacao))."', 
											'".trim(strtoupper($mlz_motivo))."' )";
	$sql = pg_query($stmt);
	echo "<br><br>
                <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                 <tr bgcolor=f9f9f9>
                   <td align=center><font size=2 color=green><b>INCLUSO com Sucesso</b></font></td>
                 </tr>
                </table><br>";
	echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='exa_materialdeanalise_iframeAGE.php?id_login=$id_login&cad_exame=$cad_exame&labm_codigo=$labm_codigo'\", 2000);
              </SCRIPT>";
}
if($acao=="edit") {
	$stmt = "UPDATE materialdeanalise 
				SET med_codigo = ".intval($med_codigo).", 
					itx_codigo = ".intval($itx_codigo).", 
					tma_codigo = ".intval($tma_codigo).", 
					mlz_coletado = '$mlz_coletado', 
					mlz_datadacoleta = '".trim(strtoupper($mlz_datadacoleta))."', 
					mlz_quantidade = ".intval($mlz_quantidade).", 
					id_login = ".intval($id_login).", 
					labm_codigo = ".intval($labm_codigo).", 
					mlz_conservacao = '".trim(strtoupper($mlz_conservacao))."', 
					mlz_observacao = '".trim(strtoupper($mlz_observacao))."', 
					mlz_motivo = '".trim(strtoupper($mlz_motivo))."'
					WHERE mlz_codigo = ".intval($mlz_codigo) ;

	$sql = pg_query($stmt);
	echo "<br><br>
                <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                 <tr bgcolor=f9f9f9>
                   <td align=center><font size=2 color=green><b>EDITADO com Sucesso</b></font></td>
                 </tr>
                </table><br>";
	echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='exa_materialdeanalise_iframeAGE.php?id_login=$id_login&cad_exame=$cad_exame&labm_codigo=$labm_codigo'\", 2000);
              </SCRIPT>";
}


if($acao=="del") {
	$stmt = "DELETE FROM materialdeanalise WHERE mlz_codigo = ".intval($mlz_codigo);
	$sql = pg_query($stmt);

	echo "<br><br>
                <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                 <tr bgcolor=f9f9f9>
                   <td align=center><font size=2 color=green><b>APAGADO com Sucesso</b></font></td>
                 </tr>
                </table><br>";
	echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='exa_materialdeanalise_iframeAGE.php?id_login=$id_login&cad_exame=$cad_exame&labm_codigo'\", 2000);
              </SCRIPT>";
}



