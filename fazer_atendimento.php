<script>

  function cid10P(cd10_codigo_cidP,cd10_descricaoP,cd10_codigoP) {
     document.frm_atendi.cd10_codigo_cidP.value = cd10_codigo_cidP;
     document.frm_atendi.cidP.value = cd10_descricaoP;
     document.frm_atendi.cd10_codigoP.value = cd10_codigoP;
  } 
  function cid10S(cd10_codigo_cidS,cd10_descricaoS,cd10_codigoS) {
     document.frm_atendi.cd10_codigo_cidS.value = cd10_codigo_cidS;
     document.frm_atendi.cidS.value = cd10_descricaoS;
     document.frm_atendi.cd10_codigoS.value = cd10_codigoS;
  } 
  function cid10T(cd10_codigo_cidT,cd10_descricaoT,cd10_codigoT) {
     document.frm_atendi.cd10_codigo_cidT.value = cd10_codigo_cidT;
     document.frm_atendi.cidT.value = cd10_descricaoT;
     document.frm_atendi.cd10_codigoT.value = cd10_codigoT;
  } 
  function HouveAlter() {
     document.frm_atendi.cmpAlter.value = 1
  }
  function DeNovo() {
     document.frm_atendi.Sub.value = 1;
  }
  function Volta(ref_pessoa) {
     url = 'agendamento.php?id_login='+id_login+'&acao=mostra_age&age_data='+age_data+'&uni_codigo='+uni_codigo+'&med_codigo='+med_codigo+'med_codigo+med_codigo+&esp_codigo='+esp_codigo+'&age_codigo='+age_codigo;
     self.location=(url);
  }
  function TheEnd() {
     document.frm_atendi.acao.value='final' 
     document.frm_atendi.submit()
  }
  function DesAbiCampos() {
     for (var i=0;i<document.frm_atendi.elements.length;i++) {
        if (document.frm_atendi.elements[i].type=="text") {
            document.frm_atendi.elements[i].disabled=true;
        }
     }  
     for (var i=0;i<document.frm_atendi.elements.length;i++) {
        if (document.frm_atendi.elements[i].type=="textarea") {
            document.frm_atendi.elements[i].disabled=true;
        }
     }
     for (var i = 0; i < document.frm_atendi.elements.length; i++) {
        if (document.frm_atendi.elements[i].tag="select") {
            document.frm_atendi.elements[i].disabled = true;
        }
     }
 }
  function AbiCampos() {
     for (var i=0;i<document.frm_atendi.elements.length;i++) {
        if (document.frm_atendi.elements[i].type=="text") {
            document.frm_atendi.elements[i].disabled=false;
        }
     }  
     for (var i=0;i<document.frm_atendi.elements.length;i++) {
        if (document.frm_atendi.elements[i].type=="textarea") {
            document.frm_atendi.elements[i].disabled=false;
        }
     } 
     for (var i = 0; i < document.frm_atendi.elements.length; i++) {
        if (document.frm_atendi.elements[i].tag="select") {
            document.frm_atendi.elements[i].disabled = false;
        }
     }
  }

    
</script>
<?php

$atendimento_encaminhamento = array(
				'A'=>'ALTA',
				'I' =>'INTERNACAO',
				'S' =>'AMBULATORIO SUS',
				'O' => 'OBITO');
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	$common = new commonClass();
	include_once "authlib.inc.php";
	verauth($id_login);

echo "<link href='estilo.css' rel='stylesheet' type='text/css'>
<link href='css/estiloForm.css' rel='stylesheet' type='text/css' />
<link href='css/estiloCommon.css' rel='stylesheet' type='text/css' />";
echo $common->incJquery();
echo $common->menuTab(Array('Atendimento'));
$common->bodyTab('1');
//------------------------------------------------------------------>

//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
         reglog($id_login,"Entrando em PACIENTE");
//------------------------------------------------------------------>
$select =     "SELECT a.ate_finalizado,
                      a.cd10_codigo as codigo1,
					  c.cd10_descricao as descricao1,
				  	  c.cd10_codigo_cid as cc,
					  a.cd10_codigos as codigo2,
					  c2.cd10_descricao as descricao2,
					  c2.cd10_codigo_cid as cc2,
					  a.cd10_codigot as codigo3,
					  c3.cd10_descricao as descricao3,
					  c3.cd10_codigo_cid as cc3, *
				 FROM atendimento a
				 LEFT JOIN cid10 c
				   ON a.cd10_codigo =c.cd10_codigo  
			     LEFT JOIN cid10 c2
				   ON a.cd10_codigo =c2.cd10_codigo  
			     LEFT JOIN cid10 c3
				   ON a.cd10_codigo =c3.cd10_codigo 
			    WHERE age_codigo='$age_codigo'";

$sql=pg_query($select);
$ate=pg_fetch_array($sql);
if (pg_num_rows($sql) > 0)  {
    $JaTemAtend='S';
} else { 
    $JaTemAtend='N';
}
if (trim($ate[ate_finalizado])=='S') {
    echo "<body onLoad=DesAbiCampos()>";
}else {
    echo "<body onLoad=AbiCampos()>";
}
//
//-> Botoes
 ///////////////////////////VALIDACAO VACINA///////////////////////////////
 $sqlVacinaAtrasada = "SELECT * 
						    FROM vacina_usuario 
						   WHERE vac_acao = 'Z' 
						     AND usu_codigo = $usu_codigo
						     AND vac_data < CURRENT_DATE";
	$queryVacinaAtrasada = pg_query($sqlVacinaAtrasada);
	$num = pg_num_rows($queryVacinaAtrasada);
	if ($num > 0){
		$msg = "Paciente est&aacute; com a(s) vacina(s) em atraso:<br/><br/><br/>";
		while($registro = pg_fetch_array($queryVacinaAtrasada)){
			$sqlNomeVacina = "select * from produto where pro_codigo = $registro[pro_codigo]";
			$queryNomeVacina = pg_query($sqlNomeVacina);
			$regs = pg_fetch_array($queryNomeVacina);
			if($registro[vac_dose] == 1){
				$dose = "Primeira Dose";
			}else if($registro[vac_dose] == 2){
				$dose = "Segunda Dose";
			}else if($registro[vac_dose] == 3){
				$dose = "Terceira Dose";
			}else if($registro[vac_dose] == 4){
				$dose = "Quarta Dose";
			}else if($registro[vac_dose] == 5){
				$dose = "Quinta Dose";
			}else if($registro[vac_dose] == 6){
				$dose = "Refor&ccedil;o";
			}
			$msg .= "- $dose de $regs[pro_nome];<br>";
		}
		$msg = substr($msg, 0, -5).".";
		echo $common->modalMsg("ALERTA", $msg);
	}

///////////////////////////FIM DA VALIDADAO DA VACINA//////////////////////
echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
          <input type=hidden name=id_login   value=$id_login>
       <tr> ";
          /*<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
           <tr>
            <td width=72>
            <a href='#' OnClick='window.open(\"pre_consulta.php?id_login=$id_login&age_codigo=$age_codigo\",null,\"height=450,width=510,status=yes,toolbar=no,menubar=no,location=no\");'>
					<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/pre_consultaNovo.jpg' alt='Pre Consulta' border='0' />
				</a>
			</td>
			<td width=72>
           <a href='#' OnClick='window.open(\"anamnese_medico.php?id_login=$id_login&age_codigo=$age_codigo\",null,\"height=450,width=510,status=yes,toolbar=no,menubar=no,location=no\");'>
					<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/anamneseNovojpg.jpg' alt='Anamnese Medico' border='0' />
				</a>
			</td>
			<td width=72>

<a href=itens_receita.php?id_login=$id_login&age_codigo=$age_codigo&ate_codigo=$ate[ate_codigo]><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/receitaNovo.jpg border=0></a></td>
            <td width=72><a href='#' OnClick='window.open(\"print_atestado.php?age_codigo=$age_codigo&id_login=$id_login&ate_codigo=$ate[ate_codigo]\",null,\"height=600,width=550,status=yes,toolbar=no,menubar=no,location=no\");'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/atestadoNovo.jpg border=0></a></td>
            <td><a href='#' OnClick='window.open(\"requisicao_exames.php?age_codigo=$age_codigo&id_login=$id_login&ate_codigo=$ate[ate_codigo]\",null,\"height=400,width=780,status=yes,toolbar=no,menubar=no,location=no\");'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/requisitar_examesNovo.jpg border=0></a></td>";*/
if (trim($ate[ate_finalizado])!='S') {
   // echo "<td width=60><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/finalizar_on.jpg onClick='javascript:TheEnd()'></td>";
} else {
    echo "<td width=60>&nbsp;</td>";
}
/*echo "     </tr>
          </table>*/
 echo" 
       </tr>
      </table>";
/*
if ($age_codigo == null)  {
    echo "<SCRIPT LANGUAGE=\"JavaScript\">
                alert (\"Paciente / Agendamento nao informado\")
         </SCRIPT>";
    echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=recepcionado_medico.php?id_login=$id_login&age_codigo=$age_codigo\">";    //exit();
}*/
$AgeSql = pg_query("select * from agendamento where age_codigo='$age_codigo'");
//$AgeSql = pg_query("select * from agendamento where age_codigo='$age_codigo' and age_atendido='S'");
$Age = pg_fetch_array($AgeSql);
/*if (pg_num_rows($AgeSql) == 0)  {
    echo "<SCRIPT LANGUAGE=\"JavaScript\">
                 alert (\"Paciente não foi Recepcionado\")
          </SCRIPT>";
    echo "<META HTTP-EQUIV='Refresh' CONTENT='0;URL=recepcionado_medico.php?id_login=$id_login&age_codigo=$age_codigo'>";
    //exit();
}*/

$usu_codigo = $Age[usu_codigo];
$med_codigo = $Age[med_codigo];

$atdi = explode("-",$ate[ate_data]);
$atdf = explode("-",$ate[ate_datafinal]);

$ate_dati = $atdi[2]."/".$atdi[1]."/".$atdi[0];
$ate_datf = $atdf[2]."/".$atdf[1]."/".$atdf[0];

// listagem da pre consulta deste atendimento*
print "<fieldset>
<legend>Historico de Pre Consultas</legend>
";

$stmt = "SELECT pc_codigo, TO_CHAR(pc_data,'DD/MM/YYYY as HH24:MI') as data 
FROM pre_consulta AS pc
WHERE pc.age_codigo = $age_codigo
 order by pc_data desc";

$qry = db_query( $stmt );

if( pg_num_rows($qry) == 0 ) print "<strong>nenhuma...</strong>";
$consultas = array();
while( $row = pg_fetch_array($qry) )
{
	//http://localhost/WebSocialSaude/prontuarioEletronico/prontuario.php?pagina=4&id_login=340&usu_codigo=302673&age_codigo=2548681
	//$consultas[] = "<a href='#' onclick=\"javascript:window.open('../pre_consulta_popup.php?id_login=$id_login&codigo=$row[0]', '__pc__', 'width=475,height=275,top=150,left=140')\">$row[1]</a>";
	$consultas[] = "<a href='prontuario.php?pagina=4&modal=true&id_login=$id_login&ate_codigo=$ate[ate_codigo]&age_codigo=$age_codigo&usu_codigo=$usu_codigo&uni_codigo=$uni_codigo&med_codigo=$med_codigo&esp_codigo=$esp_codigo&age_data=$age_data&codigo=$row[0]'>$row[1]</a>";
}
if ($modal == "true"){
	$id_login = $_GET['id_login'];
	$codigo = $_GET['codigo'];
	include_once $_SESSION[root].$_SESSION[modulo]."pre_consulta_popup.php";
}
print join( ",&nbsp;<br /> ", $consultas );
print "</fieldset>";

$sqlAgrecao = "SELECT * 
				 FROM atendimento_municipe 
				WHERE pes_codigo = $usu_codigo";

$queryAgrecao = pg_query($sqlAgrecao);

while($resQuery = pg_fetch_array($queryAgrecao)){
	if($resQuery[tps_codigo] != ""){
		echo $common->modalMsg("OK", "Usuário com histórico de maus tratos","#");
		break;
		
	}
}
//// outra coisa aqui...

if(empty($acao)) {
   echo "<form name='frm_atendi' method=post action='prontuario.php?pagina=4&id_login=$id_login&age_codigo=$age_codigo&ate_codigo=$ate[ate_codigo]&usu_codigo=$usu_codigo&uni_codigo=$uni_codigo&med_codigo=$med_codigo&esp_codigo=$esp_codigo&age_data=$age_data'>
          <input type=hidden name=cmpAlter   value=0>
          <input type=hidden name=Sub        value=0>
          <input type=hidden name=id_login   value=$id_login>
          <input type=hidden name=age_codigo value=$age_codigo>
          <input type=hidden name=ate_codigo value=$ate[ate_codigo]>
          <input type=\"hidden\" name=\"age_atendido\" value=\"{$Age['age_atendido']}\" />
          <input type=hidden name=med_codigo value=$med_codigo>";
   
   if($JaTemAtend=="N") {
      echo "<input type=hidden name=acao value=add>";
   } else {
      echo "<input type=hidden name=acao value=edit>";
      echo "<input type=hidden name=ate_codigo value=$ate[ate_codigo]>";
   }

   echo "<table cellspacing=0 cellpadding=2 border=0 >
         	<tr>
           		<td width=15% align=right>Inicio:";
   echo ($ate[ate_data]=="")?
         		"<td width=20% colspan=3><input type=text size=12 name=ate_data class='inputForm' value=".date('d/m/Y').">"
        	 	:"<td width=20% colspan=3><input type=text size=12 name=ate_data class='inputForm' value='$ate_dati'>";
   				echo " &agrave;s " ;
   echo ($ate[ate_hora]=="")?
          		 "<input type=text class='inputForm' size=06 name=ate_hora value=".date('h:i').">"
          		 :"<input type=text class='inputForm' size=06 name=ate_hora value='$ate[ate_hora]'>";
  				 echo "<input type=hidden size=12 name=ate_datafinal class=box>
         		<input type=hidden size=06 name=ate_horafinal class=box></td>";

   $Usu = pg_fetch_array(pg_query("select usu_nome from usuario where usu_codigo = '$Age[usu_codigo]'"));

   			echo " </tr>
          	<tr>
          		<td>&nbsp;</td>
          	</tr>
          	<tr>
           		<td width=15% align=right>Nome:
           		<td width=85% colspan=3><input type=text name=usu_nome value='$Usu[usu_nome]' class='inputForm' size=58></td>
           </tr>
    </table>";

    echo "<table width=100% cellspacing=0 cellpadding=2 border=0>
           <tr>
           		<td>&nbsp;</td>
           </tr>
           <tr>
            	<td width=15%>&nbsp;</td>
            	<td>Descrição do Paciente(reclamação)</td>
           </tr>
           <tr>
	            <td width=15%>&nbsp;</td>
	            <td width=85%><textarea name=ate_reclamacao cols=80 rows=10 class='textArea' onChange='javascript:HouveAlter()'>".trim($ate[ate_reclamacao])."</textarea></td
           </tr>
    </table>";

    echo "<table width=100% cellspacing=0 cellpadding=2 border=0>
           	<tr>
           		<td>&nbsp;</td>
           	</tr>
           	<tr>
	            <td width=15%>&nbsp;</td>
	            <td>Exame Fisico</td>
           </tr>
           <tr>
	            <td width=15%>&nbsp;</td>
	            <td width=85%><textarea name=ate_exame_fisico cols=80 rows=10 class='textArea' onChange='javascript:HouveAlter()'>".trim($ate[ate_exame_fisico])."</textarea></td
           </tr>
    </table>";

//$cd=pg_fetch_array(pg_query("select *from cid10 where cd10_codigo_cid='$ate[cd10_codigo]'"));

    echo "<table width=100% cellspacing=0 cellpadding=2 border=0>
           	<tr>
           		<td>&nbsp;</td>
           	</tr>
           	<tr>
            	<td width=15% align=right>CID:</td>
	            <td width= 3%><input type=text class='inputForm' onChange='javascript:HouveAlter()' name=cd10_codigoP value='$ate[codigo1]' size= 2></td>
	            <td width= 7%><input type=text class='inputForm' onChange='javascript:HouveAlter()' name=cd10_codigo_cidP value='$ate[cc]' size=10></td>
	            <td><input type=text name=cidP class='inputForm' onChange='javascript:HouveAlter()' size=53 value='$ate[descricao1]'>&nbsp;&nbsp;<b>[</b>";
    if (trim($ate[ate_finalizado])!='S') {
        echo "<a href='#' OnClick=\"window.open('../cidchoiceP.php?id_login=$id_login',null,'height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes');\"> Escolher </a>";
    }
    echo "<b>]</b>
    			</td>
           </tr>
    </table>";



    echo "<table width=100% cellspacing=0 cellpadding=2 border=0>
    	<tr>
    		<td>&nbsp;</td>
    	</tr>
        <tr>
            <td width=15%>&nbsp;</td>
            <td>Diagnóstico(Descricao)</td>
        </tr>
        <tr>
            <td width=15%>&nbsp;</td>
            <td width=85%><textarea name=ate_diagnostico class='textArea' cols=80 rows=10 class=box onChange='javascript:HouveAlter()'>".trim($ate[ate_diagnostico])."</textarea></td>
        </tr>
    </table>";

echo "<table width=100% cellspacing=0 cellpadding=2 border=0>
	           <tr>
	           		<td>&nbsp;</td>
	           </tr>
	           <tr>
		            <td width=15%>&nbsp;</td>
		            <td>Conduta/Tratamento</td>
	           </tr>
	           <tr>
		            <td width=15%>&nbsp;</td>
		            <td width=85%><textarea name=ate_tratamento cols=80 rows=10 class='textArea' onChange='javascript:HouveAlter()'>".trim($ate[ate_tratamento])."</textarea></td>
           		</tr>
    </table>";
    echo "<table width=100% cellspacing=0 cellpadding=2 border=0>
          	<tr>
          		<td>&nbsp;</td>
          	</tr>
           	<tr>
	            <td width=15%>&nbsp;</td>
	            <td>Curativos/Motivo do Procedimento</td>
           	</tr>
           	<tr>
	            <td width=15%>&nbsp;</td>
	            <td width=85%><textarea name=ate_curativos cols=80 rows=10 class='textArea' onChange='javascript:HouveAlter()'>".trim($ate[ate_curativos])."</textarea></td>
    		</tr>
    </table>";
    /*
    echo "<table width=100% cellspacing=0 cellpadding=2 border=0>
	      	<tr>
	         	<td>&nbsp;</td>
	        </tr>
	        <tr>
	            <td>&nbsp;</td>
	            <td width=30>Encaminhamento Esp.:</td>
	            <td width=85%>
	            	<select name=ate_encaminhamento_esp class='inputForm' onChange='javascript:HouveAlter()'>
             			<option>---<option>";
            			$sql = pg_query("select *from especialidade order by esp_nome");
           		  while($esp=pg_fetch_array($sql)) {
                  	echo ($ate[esp_codigo_encaminhamento]==$esp[esp_codigo])?
                             "<option value=$esp[esp_codigo] selected>$esp[esp_nome]</option>"  :
                             "<option value=$esp[esp_codigo]>$esp[esp_nome]</option>";  
           		  }
    if (trim($ate[ate_finalizado])!='S') {
        echo "      </select>
            </td>
           </tr>
          </table><br><br>
          <table width=100% cellspacing=0 cellpadding=2 border=0>
           <tr><td>&nbsp;</td></tr>
           <tr>
            <td width=15%>&nbsp;</td>
            <td><input type=submit value='Gravar Atendimento' class='inputForm' onClick='javascript:DeNovo()'></td>
           </tr>
          </table></form> ";
    } */
    echo "<br><br>
          <table width=100% cellspacing=0 cellpadding=2 border=0>
           <tr><td>&nbsp;</td></tr>
           <tr>
            <td width=15%>&nbsp;</td>
            <td><input type=submit value='Gravar Atendimento' class='inputForm' onClick='javascript:DeNovo()'></td>
           </tr>
          </table></form>";
}

if ($JaTemAtend=='S' && $acao!='final') {
	//echo $common->modalMsg("OK", "teste","prontuario.php?pagina=99&id_login=$id_login&age_codigo=$age_codigo&usu_codigo=$usu_codigo&uni_codigo=$uni_codigo&med_codigo=$med_codigo&esp_codigo=$esp_codigo&age_data=$age_data");
    if ($cmpAlter == 1) {
        $acao="edit" ;
    } else {
        $acao="nada";
        if ($Sub == 1) {
        	echo $common->modalMsg("OK", "ATENDIDO com Sucesso","prontuario.php?pagina=99&id_login=$id_login&age_codigo=$age_codigo&ate_codigo=$ate[ate_codigo]&usu_codigo=$usu_codigo&uni_codigo=$uni_codigo&med_codigo=$med_codigo&esp_codigo=$esp_codigo&age_data=$age_data");
          /*  echo "<SCRIPT LANGUAGE=\"JavaScript\">
                      setTimeout(\"location='fazer_atendimento.php?id_login=$id_login&age_codigo=$age_codigo'\", 1);
                  </SCRIPT>";*/
        }
    

    }
}

if($acao=="add") {

$cd10_codigoT=($cd10_codigoT=="")?"0":$cd10_codigoT;
$cd10_codigoS=($cd10_codigoS=="")?"0":$cd10_codigoS;
	
	$query1 = pg_query($select1);
	$resultado1= pg_fetch_array($query1);
	$ate_codigo = $resultado1['ate_codigo'];
	
	$ate_encaminhamento_esp = "---"; // encaminhamentos devem ser gerados na tela correta, ou  seja no item 'Encaminhamento' do prontuário

$select = "SELECT nextval ('seq_ate_codigo') as ate_codigo";
	$exec_select = pg_query($select);
	$linha = pg_fetch_array($exec_select);
	$ate_codigo = $linha['ate_codigo'];
   // $sql = "insert into atendimento (pc_pressao_diastolica,pc_pressao_sistolica,pc_freq_cardiaca,pc_freq_respiratoria,ate_peso,ate_altura,ate_hora,med_codigo,usu_codigo,age_codigo,ate_pressao,ate_temperatura,cd10_codigo,ate_encaminhamento,ate_data,ate_reclamacao,ate_exame_fisico,ate_diagnostico,ate_tratamento,cd10_codigoS,cd10_codigoT,esp_codigo_encaminhamento,ate_curativos) values ('$pc_pressao_diastolica','$pc_pressao_sistolica','$pc_freq_cardiaca','$pc_freq_respiratoria','$ate_peso','$ate_altura','$ate_hora','$med_codigo','$usu_codigo','$age_codigo','$ate_pressao','$ate_temperatura',".($cd10_codigoP ? "'$cd10_codigoP'" : "'0'").",".($ate_encaminhamento=='---' ? "null" : "'$ate_encaminhamento'").",NOW(),'$ate_reclamacao','$ate_exame_fisico','$ate_diagnostico','$ate_tratamento','$cd10_codigoS','$cd10_codigoT','$ate_encaminhamento','$ate_curativos')";
    $q = "INSERT INTO atendimento (ate_codigo,
    								 ate_hora,
									 med_codigo,
									 usu_codigo,
									 age_codigo,
									 cd10_codigo,										 
									 ate_data,
									 ate_reclamacao,
									 ate_exame_fisico,
									 ate_diagnostico,
									 ate_tratamento,
									 esp_codigo_encaminhamento,
									 ate_curativos) 
						    VALUES ('$ate_codigo',
						    		'$ate_hora',
								    '$med_codigo',
								    '$usu_codigo',
								    '$age_codigo',
								    ".($cd10_codigoP ? "'$cd10_codigoP'" : "'0'").",
								    NOW(),
								    '$ate_reclamacao',
								    '$ate_exame_fisico',
								    '$ate_diagnostico',
								    '$ate_tratamento',
								    ".($ate_encaminhamento_esp=='---' ? "null" : "'$ate_encaminhamento_esp'").",
								  	'$ate_curativos')";

   
    $sql = pg_query($q) or die (pg_last_error());
  
        if($ate_encaminhamento_esp!= "---") { 
        echo "<script> window.open(\"../print_encaminhamento.php?uni_codigo=$uni_codigo&esp_codigo=$ate_encaminhamento_esp&agt_codigo=$agt_codigo&usu_codigo=$usu_codigo&age_codigo=$age_codigo&med_codigo=$med_codigo\",null,\"height=600,width=550,status=yes,toolbar=no,menubar=no,location=no\"); </script>";
    }
if($sql){
	echo $common->modalMsg("OK", "ATENDIDO com Sucesso","prontuario.php?pagina=99&id_login=$id_login&age_codigo=$age_codigo&usu_codigo=$usu_codigo&uni_codigo=$uni_codigo&med_codigo=$med_codigo&esp_codigo=$esp_codigo&age_data=$age_data&ate_codigo=$ate_codigo&ate_hora=$ate_hora");
}else{
	echo $common->modalMsg("ERRO", "ERRO","prontuario.php?pagina=99&id_login=$id_login&age_codigo=$age_codigo&usu_codigo=$usu_codigo&uni_codigo=$uni_codigo&med_codigo=$med_codigo&esp_codigo=$esp_codigo&age_data=$age_data&ate_codigo=$ate[ate_codigo]&ate_hora=$ate_hora");
}
/*   echo "<br><br><br><br><br><br><br><br><br>
          <table height=100 width=100% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
            <tr bgcolor=f9f9f9>
	             <td align=center><font size=2 color=green><b>ATENDIDO com Sucesso</b></font></td>
            </tr>
           </table><br>";
    echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='prontuario.php?pagina=99&id_login=$id_login&age_codigo=$age_codigo&usu_codigo=$usu_codigo'\", 2000);
          </SCRIPT>"*/;
    $acao=null; $cmpAlter=0;
   
}

 if($acao=="edit" and $cmpAlter==1) {
    $ate_pressao = $pressao1."-".$pressao2."-".$pressao3;
    $update = "update atendimento set ate_data='$ate_dati',
    											  ate_hora='$ate_hora',
    											  med_codigo='$med_codigo',
    											  usu_codigo='$usu_codigo',
    											  ate_observacao='$ate_observacao',
    											  ate_pressao='$ate_pressao',
    											  ate_temperatura='$ate_temperatura',
    											  cd10_codigo =".($cd10_codigoP ? "'$cd10_codigoP'" : "'0'").",
    											  ate_reclamacao='$ate_reclamacao',
    											  ate_exame_fisico='$ate_exame_fisico',
    											  ate_diagnostico='$ate_diagnostico',
    											  esp_codigo_encaminhamento = ".($ate_encaminhamento_esp=='---' ? "null" : "'$ate_encaminhamento_esp'").",
    											  ate_tratamento='$ate_tratamento',  
    											  ate_curativos='$ate_curativos'
    											  where age_codigo='$age_codigo'";

    $sql = pg_query($update); 
    
    if($_POST['age_atendido'] == 'P'){
    	// passa o agendamento para "E" (em atendimento)
    	pg_query("UPDATE agendamento SET age_atendido='E' WHERE age_codigo='$age_codigo'");
    }
    if($sql){
    	echo $common->modalMsg("OK", "EDITADO ATENDIMENTO com Sucesso","prontuario.php?pagina=99&id_login=$id_login&age_codigo=$age_codigo&usu_codigo=$usu_codigo&uni_codigo=$uni_codigo&med_codigo=$med_codigo&esp_codigo=$esp_codigo&age_data=$age_dat&ate_codigo=$ate[ate_codigo]&ate_hora=$ate_hora");
    }else{
    	echo $common->modalMsg("ERRO","ERRO ao Editar","prontuario.php?pagina=99&id_login=$id_login&age_codigo=$age_codigo&usu_codigo=$usu_codigo&uni_codigo=$uni_codigo&med_codigo=$med_codigo&esp_codigo=$esp_codigo&age_data=$age_dat&ate_codigo=$ate[ate_codigo]a&ate_hora=$ate_hora");
    }
    /*echo "<br><br><br><br><br><br><br><br><br>
          <table height=100 width=100% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
            <tr bgcolor=f9f9f9>
              <td align=center><font size=2 color=green><b>EDITADO ATENDIMENTO com Sucesso</b></font></td>
            </tr>
          </table><br>";
    echo "<SCRIPT LANGUAGE=\"JavaScript\">
                setTimeout(\"location='fazer_atendimento.php?id_login=$id_login&age_codigo=$age_codigo'\", 2000);
         </SCRIPT>";*/
    $acao=null; $cmpAlter=0;
}
/*if($acao == "final"){
	
	echo $common->modalConfirm("Deseja finalizar o atendimento?", "prontuario.php?pagina=4&acao=finalmente&id_login=$id_login&ate_codigo=$ate_codigo&age_codigo=$age_codigo&usu_codigo=$usu_codigo&uni_codigo=$uni_codigo&med_codigo=$med_codigo&esp_codigo=$esp_codigo&age_data=$age_data&ate_codigo=$ate_codigo&ate_codigo=$ate_codigo","prontuario.php?pagina=99&id_login=$id_login&ate_codigo=$ate_codigo&age_codigo=$age_codigo&usu_codigo=$usu_codigo&uni_codigo=$uni_codigo&med_codigo=$med_codigo&esp_codigo=$esp_codigo&age_data=$age_data&ate_codigo=$ate_codigo&ate_codigo=$ate_codigo");
}*/
 if($acao=="final") {
 	echo $common->modalMsg("OK", "Finalizado Com Sucesso","prontuario.php?pagina=1&id_login=$id_login&age_codigo=$age_codigo");
    $ate_datf = date("Y/m/d");
    $ate_horf = date("h:i");
    $ate_pressao = $pressao1."-".$pressao2."-".$pressao3;
	$mudaStatus = pg_query("UPDATE alerta_usuario SET alepac_status = 'A' WHERE  usu_codigo = $usu_codigo");
    $sql = pg_query("UPDATE atendimento 
    					SET ate_peso='$ate_peso',
    						ate_altura='$ate_altura',
    						ate_data='$ate_dati',
    						ate_hora='$ate_hora',
    						med_codigo='$med_codigo',
    						usu_codigo='$usu_codigo',
    						ate_observacao='$ate_observacao',
    						age_codigo='$age_codigo',
    						ate_pressao='$ate_pressao',
    						ate_temperatura='$ate_temperatura',
    						cd10_codigo=".($cd10_codigo ? "'$cd10_codigo'" : "'0'").",
    						ate_encaminhamento=".($ate_encaminhamento!='---' ? "'$ate_encaminhamento'" : "null").",
    						ate_reclamacao='$ate_reclamacao',
    						ate_exame_fisico='$ate_exame_fisico',
    						ate_diagnostico='$ate_diagnostico',
    						ate_tratamento='$ate_tratamento',
    						ate_datafinal='$ate_datf',
    						ate_horafinal='$ate_horf',
    						ate_finalizado='S'  
    				  WHERE ate_codigo=$ate_codigo"); 

	
    $sqlTipoMedico = "SELECT * 
    					FROM usuarios
    				   WHERE usr_codigo = $id_login";
    $queryTipoMedico = pg_query($sqlTipoMedico);
	$reg = pg_fetch_array($queryTipoMedico);
	
	if($reg['usr_tipo_medico'] == 'E' || $reg['usr_tipo_medico'] == 'A'){
		$finaliza = "UPDATE agendamento 
 					SET age_atendido = 'E'  
 				  WHERE age_codigo = $age_codigo";
	}else{
	 	$finaliza = "UPDATE agendamento 
	 					SET age_atendido = 'A'  
	 				  WHERE age_codigo = $age_codigo";
	}
	$qryFinaliza = pg_query($finaliza);
	
	echo $common->modalMsg("OK", "Finalizado Com Sucesso","prontuario.php?pagina=1&id_login=$id_login&age_codigo=$age_codigo");
 
    $acao=null; $cmpAlter=0;

}
 echo $common->closeTab();
?>
