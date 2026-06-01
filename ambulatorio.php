<?php 
	require_once 'global.php';
	include_once COMUM."/library/php/funcoes.inc.php";
	include_once SAUDE . '/__array.php';
	include "authlib.inc.php";
	
?>

<html>
	<head>
<script language="JavaScript" type="text/javascript" src="../WebSocialComum/library/js/jquery-1.6.2.min.js"></script>
<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<script language="JavaScript" type="text/javascript" src="ajax_motor.js"></script>
<?

$common = new commonClass();
$form = new classForm();
$table = new tableClass();
echo $common->incJquery();

?>
<script type="text/javascript" src="/WebSocialSaude/lib/ui/jquery-ui-1.8.10.custom.js"></script>
<script type="text/javascript" src="/WebSocialComum/library/js/jquery.buscar.js"></script>
<script>

	function abreRelatorio(){
		var usu_codigo = $("#usu_codigo").val();
		url = "AtendimentoPaciente.php?usu_codigo="+usu_codigo;
		window.open(url,null,"height=800,width=800,status=yes,toolbar=no,menubar=no,resizable=yes, location=no,scrollbars=yes");
	}

	$(function(){
		$("#usu_nome").buscar();
	});
</script>
<?php 
if(empty($acao)) {
echo $common->menuTab(array("Atendimento Hospitalar por Ficha"));
echo $common->bodyTab("1");

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
			<tr>
	  			<td>
	   				<fieldset>
						<legend>Opcoes</legend>
                        <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
						    <tr>
						        <td width=95>".ChmodBtn($id_login,'adicionar','ambulatorio.php?acao=form')."</td>
                                <form method=post action=$PHP_SELF>
                                    <input type=hidden name=id_login value=$id_login>
                                    <td width=30>Buscar:</td>
                                    <td width=120><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
                                    <td width=120>Buscar Por Data:</td>
                                    <td width=120>
                                        <input type=radio name=sim class=box > SIM
                                        <input type=radio name=nao class=box > NAO
                                    </td>
                                    <td>".ChmodBtn($id_login,'procurar','ambulatorio.php')."
                                </form>
			                </tr>
		                </table>
	   				</fieldset>
	            </td>
	        </tr>
	    </table>
	<br>";

$palavra_chave=strtoupper($palavra_chave);
if(!empty($palavra_chave)) {
    $busca = "and usu_nome like '$palavra_chave%'";
} else {
	$andData = " AND age_data='".date('d/m/Y')."' ";
}
if (!empty($sim)) {
	$filtroPorData = "and age_data = '$palavra_chave'";
	$busca = "--and usu_nome like '%%'";
} #else{
	#$filtroPorData = "-- and age_data = '$palavra_chave'";
	#$busca = "and usu_nome like '%$palavra_chave%'";

#}
#echo '<pre>';
#print_r($_SESSION['logon']['usr']);
#die();
$data = date('d/m/Y');
echo $data;
	$sql= pg_query("SELECT tp_codigo,med_codigo,age.usu_codigo,hora_alta_usuario, data_alta_usuario, age_codigo, age_horario,to_char(age_data, 'dd/mm/yyyy') 
						as age_data, get_medico(med_codigo) 
						as med_nome, age_hora, usu_nome
						from agendamento as age join usuario as usu on age.usu_codigo=usu.usu_codigo
							where age.usu_codigo = age.usu_codigo 
								and tp_codigo is not null and age.uni_codigo = ".$_SESSION['logon']['usr']->uni_codigo."
						$andData
						$busca
						$filtroPorData
						order by age_data desc,age_horario,usu_nome
					");
	
	if(!empty($filtroPorData)){
		$total_reg = "30";
	} else{
		$total_reg = "30"; 	
	}

	$pagina=$_GET['pagina'];
	if (!$pagina) {
	$pc = "1";
	} else {
	$pc = $pagina;
	}

	$inicio = $pc - 1;
	$inicio = $inicio * $total_reg;

	//$limite = pg_query("$sql OFFSET $inicio LIMIT $total_reg") or die(pg_last_error());

	$todos = pg_query("$sql");

	$tr = pg_num_rows($todos); // verifica o número total de registros
	$tp = $tr / $total_reg; // verifica o número total de páginas

	// while ($dados = pg_fetch_array($limite)) {
	// $nome = $dados["usu_nome"];
	// echo "Nome: $nome<br>";
	// }

	$anterior = $pc -1;
	$proximo = $pc +1;

// $num=pg_num_rows($sql);
//   if($num=="0") { $resp = "Nenhum Registro encontrado com \"$palavra_chave\""; }
//   if($num=="1") { $resp = "Encontrado <b>$num</b> Registro com \"$palavra_chave\""; }
//   if($num>"1") { $resp = "Encontrados <b>$num</b> Registros com \"$palavra_chave\""; }

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>"; if(!empty($palavra_chave)) { echo $resp; } echo "</legend>
	     <table width=100% align=center cellspacing=5 cellpadding=4 border=0>
		<td width=6 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>
		<td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Data</td>
		<td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Hora</td>
		<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Paciente</td>

		<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Data Alta</td>
		<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Hora Alta</td>


		<td colspan=5 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";
//die($sql);
$i=0;
     while($row=pg_fetch_array($sql)) {
$i++;

		$recebeData = explode('-', $row[data_alta_usuario]);

		// echo '<pre>';print_r($recebeData);die();

		$recebeDia = $recebeData[2];
		$recebeMes = $recebeData[1];
		$recebeAno = $recebeData[0];

		if ($row[tp_codigo] == 7) {
			// echo "<pre>"var_dump($row);
			echo "<tr style = 'background-color : red; color : white' > 
					<td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$i</td>
					<td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[age_data]</td>
					<td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[age_horario]</td>
					<td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[usu_nome]</td>
					
					";
					if ($row[data_alta_usuario] != '2019-01-01' ) {
						echo "<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>$recebeDia/$recebeMes/$recebeAno</td>
						<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>$row[hora_alta_usuario]</td>";
					} else{
						echo "<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'></td>
							<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'></td>";
					}


					echo "<td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href='#' OnClick=\"window.open('".LINKSAUDE."/zf/relatorio/usuario/guia-diagnostico-sem-historico/usu_codigo/".$row['usu_codigo']."/age_codigo/".$row['age_codigo']."/med_codigo/".$row['med_codigo']."',null,'height=400,width=700,status=yes,toolbar=no,menubar=no,location=no,resizable=yes,scrollbars=yes');\"><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/print_on.jpg' border=0></a></td>
					</td>
				<td width=66>";
		} else{
			echo "<tr> 
                <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$i</td>
                <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>{$row['age_data']}</td>
                <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>{$row['age_horario']}</td>
                <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>{$row['usu_nome']}</td>

                <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'></td>
                <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'></td>
                
                <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href='#' OnClick=\"window.open('".LINKSAUDE."/zf/relatorio/usuario/guia-diagnostico-sem-historico/usu_codigo/".$row['usu_codigo']."/age_codigo/".$row['age_codigo']."/med_codigo/".$row['med_codigo']."',null,'height=400,width=700,status=yes,toolbar=no,menubar=no,location=no,resizable=yes,scrollbars=yes');\"><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/print_on.jpg' border=0></a></td>
            </td><td width=66>";
		}

		if($row[tp_codigo]==7) {
		echo "<a href='".LINKSAUDE."/folhaRostoAih.php?usu_codigo=".$row['usu_codigo']."&age_codigo=".$row['age_codigo']."&med_codigo=".$row['med_codigo']."' target='_blank'>
							<img src='/WebSocialComum/imgs/aih_apac_on.jpg' valign='middle'></a>";
		}

		echo "<td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>
				".ChmodBtn($id_login,'apagar','ambulatorio.php?acao=del&age_codigo='.$row['age_codigo'])."</td>
				<td width=66>".ChmodBtn($id_login,'editar','ambulatorio.php?acao=form&age_codigo='.$row['age_codigo'])."

				</td>";
			
			if($row['tp_codigo']==7 && $row['data_alta_usuario'] == '2019-01-01' && $row['data_alta_usuario'] == '2019-01-01') {
				echo "
                <td width=30 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>
                        <!--<a href='#' OnClick=\"window.open('".LINKSAUDE."/altaUsuario.php/usu_codigo/".$row['usu_codigo']."/age_codigo/".$row['age_codigo']."/med_codigo/".$row['med_codigo']."',null,'height=400,width=700,status=yes,toolbar=no,menubar=no,location=no,resizable=yes,scrollbars=yes');\">-->
                        <a href='#' OnClick=\"newWindow()\">
                            <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/remover_on.jpg' border=0>
                        </a>
                        <div hidden id='alta-dialog' title='Alta do paciente'>
                            <form action=\"#\" method=\"post\" id='formAlta' style='padding: 13px;'>
                                <div style='padding: 4px;'>
                                    <label for=\"dataAlta\" style='font-size: 16px'>Data da alta:</label>
                                    <input id=\"dataAlta\" name=\"dataAlta\" type=\"date\" value=\"\" style='font-size: 16px; padding: 8px; width: 185px; background-color: #fff; outline: none;'>
                                </div>
                                <div style='padding: 4px;'>
                                    <label for=\"horaAlta\" style='font-size: 16px'>Hora da alta:</label>
                                    <input id=\"horaAlta\" name=\"horaAlta\" type=\"time\" value=\"\" style='font-size: 16px; padding: 8px; width: 185px; background-color: #fff; outline: none;'>
                                </div>
                                <input type='hidden' name='usu_codigo' value='{$row['usu_codigo']}'>
                                <input type='hidden' name='age_codigo' value='{$row['age_codigo']}'>
                                <input type='hidden' name='med_codigo' value='{$row['med_codigo']}'>
                            </form>
                        </div>
                    </td>
                </td>";
			}
		}
		
		echo "</tr>
				</table>
				</fieldset>
				</td>
			</tr>
			</table>";
				
			echo $common->closeTab();
		}
		?>
        <script>
            function newWindow(){
                
                $("#alta-dialog").dialog({
                    modal: true,
                    width: 350,
                    height: 220,
                    close: function() {
                        $(this).remove()
                    },
                    buttons: {
                        "Fechar": function(){
                            $(this).dialog('close')
                        },
                        "Ok": function() {
                            let form = $("form#formAlta").serializeArray().reduce((m, o) => {m[o.name] = o.value; return m}, {})
                            $(this).dialog('close')
                            
                            $.post("<?=LINKSAUDE;?>/altaUsuario.php/usu_codigo/"+form.usu_codigo+"/age_codigo/"+form.age_codigo+"/med_codigo/"+form.med_codigo, form, res => {window.location.reload()})
                            
                        }
                    }
                })
            }
        </script>
		<div style = "text-align: center;">
		<?
			if ($pc>1) {
				echo " <a href='?pagina=$anterior' style = 'font-size: 13px;font-weight: bold;'><- Anterior</a> ";
				}
			
				echo "|";
				if ($pc<$tp) {
				echo " <a href='?pagina=$proximo' style = 'font-size: 13px;font-weight: bold;'>Proximo -></a>";
				}
		?>
		</div>
		<?
if($acao=="form") {
	echo $form->openForm("$PHP_SELF","POST","form",null);
	if(!empty($_REQUEST['age_codigo'])) {
		$rr = pg_fetch_array(pg_query("select to_char(age_data,'dd/mm/yyyy') as newdata,* 
		from agendamento as a 
		join usuario as b on a.usu_codigo=b.usu_codigo 
		left join usuarios as usr on usr.usr_codigo=a.med_codigo 
		where age_codigo = ".$_REQUEST['age_codigo']));
		
		$usu_nome = $rr['usu_nome'];
		$data = $rr['newdata'];
		$hora = $rr['age_horario'];
		$med = "and usr_codigo=".$rr['med_codigo'];
		$fone = $rr['usu_fone'];
		$celular = $rr['usu_celular'];
		$tp = (($rr['tp_codigo']==7)?"1":$rr['tp_codigo']);
		
		echo $form->hiddenForm("acao", "edit");
		echo $form->hiddenForm("usu_codigo", $rr['usu_codigo']);
	} else {
		$data = date('d/m/Y');
		$hora = date('H:i');
		echo $form->hiddenForm("acao", "add");
		echo $form->hiddenForm("usu_codigo", "$usu_codigo");
	}
    echo $common->menuTab(array("Atendimento Hospitalar por Ficha"));
    echo $common->bodyTab("1");
	
		$sqlUnidade = "SELECT uni_codigo, uni_desc FROM unidade ORDER BY uni_desc;";
		$sqlMedico = "SELECT usr_codigo,usr_nome FROM usuarios WHERE usr_tipo_medico='M' ORDER BY usr_nome";
		echo $form->inputText("usu_nome",$usu_nome,"Paciente",50);
		echo $form->inputText("age_data",$data,"Data Inicial",null,10,"onKeypress=\"return Ajusta_Data(this,event)\"");
		
		echo $form->inputText("age_horario",$hora,"Hora",null,5);
		echo $form->inputSelect("med_codigo",$med,"M&eacute;dico",$sqlMedico,null,null,null,null,"TODOS");
		echo $form->inputCheckboxRadio("tp_codigo", $tp, "Internado", null, array("0"=>"Nao", "1"=>"Sim"), "radio");
		echo $form->inputText("usu_fone",$fone,"Telefoone",20);
		echo $form->inputText("usu_celular",$celular,"Celular",20);
		echo "<div style='clear:both; width:400px; border:solid 0px;'>";
				echo"<div style='float:right; width:205px;'>";		
					echo $common->commonButton("Gravar e Imprimir","","report.png","onClick=\"form.submit()\"");
				echo"</div>";
				echo"<div style='float:right'>";
					echo $common->commonButton("Voltar", "ambulatorio.php", "voltar.png", null);
				echo"</div>";
		echo"</div>";
		
	echo $form->closeForm();
    echo $common->closeTab();
}

if($acao=="add") {
	
	if($_REQUEST['tp_codigo']==1) { $tp = 7; } else { $tp = 8; }
	 $sql = "insert into agendamento ( age_tipo,age_data,age_horario,med_codigo, usu_codigo, tp_codigo,uni_codigo) 
			values ( '".'AM'."',
            '".($_REQUEST['age_data'] ? $_REQUEST['age_data'] : "null") . "', 
            '".($_REQUEST['age_horario'] ? $_REQUEST['age_horario'] : "null") . "', " .
            ($_REQUEST['med_codigo'] ? $_REQUEST['med_codigo'] : "null") . ", " .
            ($_REQUEST['usu_codigo'] ? $_REQUEST['usu_codigo'] : "null") . ", ". $tp . ", " . $_SESSION['uni_codigo'] .
			")";
	//echo "<pre>";print_r($sql);die();
$query = pg_query($sql) or die(pg_last_error());
//echo "<pre>";var_dump($sql);die();

msg($id_login,$acao,$sql);
}

if($acao=="edit") {
	 if($_REQUEST['tp_codigo']==1) { $tp = 7; } else { $tp = 8; }
    $sql = "update agendamento set 
            age_data = '".$_REQUEST['age_data']."' ,
            age_horario = '".$_REQUEST['age_horario']."', 
            med_codigo = ".($_REQUEST['med_codigo'] ? $_REQUEST['med_codigo'] : "null").",
            usu_codigo = '".$_REQUEST['usu_codigo']."',
            tp_codigo = '".$tp."'
             where age_codigo=".$_REQUEST['age_codigo'];

if($_REQUEST['usu_fone']) {
	 pg_query("update usuario set usu_fone = '".$_REQUEST['usu_fone']."' where usu_codigo=".$_REQUEST['usu_codigo']);
}
if($_REQUEST['usu_celular']) {
	 pg_query("update usuario set usu_celular = '".$_REQUEST['usu_celular']."' where usu_codigo=".$_REQUEST['usu_codigo']);
}

$query = db_query($sql);
msg($id_login,$acao,$sql);
}

 if($acao=="del") {
reglog($id_login,"Apagando atendimento Cod.: $age_codigo");
  $sql = pg_query("delete from agendamento where age_codigo='$age_codigo'");
msg($id_login,$acao,$sql);
}

?>

