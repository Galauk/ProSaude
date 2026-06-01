<?php

//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>

session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
verauth($id_login);
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."anamnese.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";

echo "
<body bgcolor='#E6E6E6'>
<link href='estilo.css' rel='stylesheet' type='text/css'>";

?>
<script type="text/javascript">
function TheEnd()
{
		document.location.href = "<?="$_SERVER[PHP_SELF]?id_login=$id_login&age_codigo=$age_codigo&acao=fim"?>";
}
</script>
<?php
//------------------------------------------------------------------>

//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
         reglog($id_login,"Entrando em ODONTO-ATENDIMENTO");
//------------------------------------------------------------------>

//$sql=pg_query("select * from atendimento where age_codigo='$age_codigo'");
$sql=pg_query("select * from odonto where age_codigo='$age_codigo'");
$ate=pg_fetch_array($sql);

if (pg_num_rows($sql) > 0)  
{
	$JaTemAtend='S';
} 
else 
{ 
	$JaTemAtend='N';
}


//
//-> Botoes
 
echo "
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	<input type=hidden name=id_login   value=$id_login>
	<tr>
		<td>
		<fieldset>
		<legend>Op��es</legend>
		<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
		<tr>
			<td width='30'>
				<a href='odontograma.php?id_login=$id_login&age_codigo=$age_codigo'>
					<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/odontograma_on.jpg' alt='Odontograma' border='0' />
				</a>
			</td>
			<td width=75>
				<a href='pre_consulta.php?id_login=$id_login&age_codigo=$age_codigo'>
					<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/pre_consulta_on.jpg' alt='Pre Consulta' border='0' />
				</a>
			</td>
            <!--<td width=67><a href=itens_receita.php?id_login=$id_login&age_codigo=$age_codigo&ate_codigo=$ate[ate_codigo]><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/receita_on.jpg border=0></a></td>-->
            <td width=72><a href='#' OnClick='window.open(\"print_atestado_odonto.php?age_codigo=$age_codigo&id_login=$id_login&ate_codigo=$ate[ate_codigo]\",null,\"height=600,width=550,status=yes,toolbar=no,menubar=no,location=no\");'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/atestado_on.jpg border=0></a></td>
            <!--<td><a href='#' OnClick='window.open(\"requisicao_exames.php?age_codigo=$age_codigo&id_login=$id_login&ate_codigo=$ate[ate_codigo]\",null,\"height=600,width=560,status=yes,toolbar=no,menubar=no,location=no\");'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/requisitar_exames_on.jpg border=0></a></td>-->";

if( trim($ate['od_finalizado']) != 'S' )
{
	echo "<td align='right'><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/finalizar_on.jpg onClick='javascript:TheEnd()'></td>";
} 
else 
{
	echo "<td>&nbsp;</td>";
}
echo "
		</tr>
		</table>
		</fieldset>
        </td>
	</tr>
	</table>";

if( $age_codigo == null )
{
	echo "
	<SCRIPT LANGUAGE=\"JavaScript\">
		alert (\"Paciente / Agendamento nao informado\")
	</SCRIPT>
	<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=recepcionado_medico.php?id_login=$id_login&age_codigo=$age_codigo\">";    
	exit();
}

$AgeSql = pg_query("select * from agendamento where age_codigo='$age_codigo' and age_atendido='S'");
$Age = pg_fetch_array($AgeSql);
if( pg_num_rows($AgeSql) == 0 ) 
{
	echo "
	<SCRIPT LANGUAGE=\"JavaScript\">
		alert (\"Paciente nao foi Recepcionado\")
	</SCRIPT>
	<META HTTP-EQUIV='Refresh' CONTENT='0;URL=recepcionado_medico.php?id_login=$id_login&age_codigo=$age_codigo'>";
    exit();
}


$usu_codigo = $Age["usu_codigo"];
$med_codigo = $Age["med_codigo"];

$atdi = explode( "-", $ate["od_data"] );
$atdf = explode( "-", $ate["od_datafinal"] );

$ate_dati = $atdi[2]."/".$atdi[1]."/".$atdi[0];
$ate_datf = $atdf[2]."/".$atdf[1]."/".$atdf[0];


// listagem das pre consultas deste paciente
print "<fieldset>
<legend>Historico de Pre Consultas</legend>
";

$stmt = "SELECT pc_codigo, TO_CHAR(pc_data,'DD/MM/YYYY as HH24:MI') as data 
FROM pre_consulta AS pc
NATURAL JOIN agendamento AS ag
WHERE ag.usu_codigo = $usu_codigo
ORDER BY pc_codigo DESC";

$qry = db_query( $stmt );

if( pg_num_rows($qry) == 0 ) print "<strong>nenhuma...</strong>";

$consultas = array();
while( $row = pg_fetch_array($qry) )
{
	$consultas[] = "<a href='#' onclick=\"javascript:window.open('pre_consulta_popup.php?id_login=$id_login&codigo=$row[0]', '__pc__', 'width=475,height=275,top=150,left=140')\">$row[1]</a>";
}

print join( ",&nbsp; ", $consultas );
print "</fieldset>";

//// outra coisa aqui...
if(empty($acao))
{

   echo "
		<table cellspacing=0 cellpadding=2 border=0>
		<tr>
			<td width=15% align=right>Inicio:";
   
   echo( $ate["od_data"] == "" ) ?
          "<td width=20% colspan=3><input type=text size=12 name=ate_data class=box value=".date('d/m/Y').">"
         :"<td width=20% colspan=3><input type=text size=12 name=ate_data class=box value='$ate_dati'>";
   
   echo " &agrave;s " ;
   echo ( $ate["od_hora"] == "" ) ?
           "<input type=text size=06 name=ate_hora class=box value=".date('h:i').">"
          :"<input type=text size=06 name=ate_hora class=box value='$ate[od_hora]'>";
   
   echo "
			<input type=hidden size=12 name=ate_datafinal class=box>
			<input type=hidden size=06 name=ate_horafinal class=box>
		</td>
	";

   $Usu = pg_fetch_array(pg_query("select usu_nome from usuario where usu_codigo = '$Age[usu_codigo]'"));

   echo "
	</tr>
    <tr><td>&nbsp;</td></tr>
	<tr>
		<td width=15% align=right>Nome:
		<td width=85% colspan=3>
			<input type=text name=usu_nome value='$Usu[usu_nome]' class=box size=58 readonly />
		</td>
	</tr>
	</table>";

   $Anamnese =  new Anamnese( $id_login );

	//$Anamnese->id_tipo 		= 14;
	$Anamnese->id_tipo 		= 17;
	$Anamnese->tabela = $odonto_anamnese;
	$Anamnese->fk_nome 		= 'od_codigo';
	$Anamnese->action			= '&age_codigo='.$age_codigo;
	$Anamnese->auto_insert 	= 0;
	$Anamnese->edit 			= ( $JaTemAtend == 'S' ? true : false );
	$Anamnese->fk 				= $Anamnese->edit ?
			db_get("SELECT od_codigo FROM odonto WHERE age_codigo=$age_codigo") : 1;
	$Anamnese->form();

	if( $Anamnese->passo == 2 )
	{
		if( $JaTemAtend == 'S' )
		{
			//$Anamnese->fk = db_get("SELECT od_codigo FROM odonto WHERE age_codigo=$age_codigo");
		}
		else
		{
			db_query("BEGIN");
			$stmt = "INSERT INTO odonto  ( 
							od_data, 
							od_finalizado, 
							od_datafinal, 
							od_hora, 
							age_codigo
							 ) VALUES ( 
							CURRENT_DATE, 
							'N', 
							CURRENT_DATE, 
							CURRENT_TIME , 
							".intval($age_codigo)." )";
			
			db_query($stmt);
			$Anamnese->fk = db_get("SELECT MAX(od_codigo) FROM odonto");
			db_query("COMMIT");
		}
		$Anamnese->sql_form();		
		print "<p class='Aviso'>Anamnese Inserida...</p>";
	}



}

if ($JaTemAtend=='S' && $acao!='final') {
    if ($cmpAlter == 1) {
        $acao="edit" ;
    } else {
        $acao="nada";
        if ($Sub == 1) {
            echo "<SCRIPT LANGUAGE=\"JavaScript\">
                      setTimeout(\"location='fazer_atendimento.php?id_login=$id_login&age_codigo=$age_codigo'\", 1);
                  </SCRIPT>";
        }

    }
}
 

 if($acao=="fim")
 {
    $ate_datf = date("Y/m/d");
    $ate_horf = date("h:i");
    $ate_pressao = $pressao1."-".$pressao2."-".$pressao3;
    $sql = pg_query("update atendimento set ate_peso='$ate_peso',ate_altura='$ate_altura',ate_data='$ate_dati',ate_hora='$ate_hora',med_codigo='$med_codigo',usu_codigo='$usu_codigo',ate_observacao='$ate_observacao',age_codigo='$age_codigo',ate_pressao='$ate_pressao',ate_temperatura='$ate_temperatura',cd10_codigo=".($cd10_codigo ? "'$cd10_codigo'" : "'0'").",ate_encaminhamento=".($ate_encaminhamento!='---' ? "'$ate_encaminhamento'" : "null").",ate_reclamacao='$ate_reclamacao',ate_exame_fisico='$ate_exame_fisico',ate_diagnostico='$ate_diagnostico',ate_tratamento='$ate_tratamento',ate_datafinal='$ate_datf',ate_horafinal='$ate_horf',ate_finalizado='S'  where ate_codigo='$ate_codigo'"); 
    if($ate_encaminhamento!="---") {
       echo "<script> window.open(\"print_encaminhamento.php?uni_codigo=$uni_codigo&esp_codigo=$esp_codigo&agt_codigo=$agt_codigo&usu_codigo=$usu_codigo&age_codigo=$age_codigo&med_codigo=$med_codigo\",null,\"height=600,width=550,status=yes,toolbar=no,menubar=no,location=no\"); </script>";
    }
    echo "<br><br><br><br><br><br><br><br><br>
          <table height=100 width=100% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
            <tr bgcolor=f9f9f9>
              <td align=center><font size=2 color=green><b>Atendimento FINALIZADO com Sucesso</b></font></td>
            </tr>
          </table><br>";
    echo "<SCRIPT LANGUAGE=\"JavaScript\">
                setTimeout(\"location='fazer_atendimento.php?id_login=$id_login&age_codigo=$age_codigo'\", 2000);
         </SCRIPT>";
    $acao=null; $cmpAlter=0;
}
else if( $acao == 'fim' )
{
	
	$stmt = "UPDATE odonto SET od_finalizado = 'S' WHERE age_codigo = $age_codigo";
	//db_query($stmt);
	print "<p class='aviso ok'>Consulta Finalizada !</p>
	<script type='text/javascript'>
		setTimeout(\"odonto_atendimento.php?id_login=125\", 3000);
	</script>";
	
}	

?>
