<?php

	session_start();
	$_SESSION['modulo'] = "WebSocialSaude/";
	$_SESSION['root'] = $_SERVER['DOCUMENT_ROOT']."/";
	$_SESSION['linkroot'] = "http://".$_SERVER['HTTP_HOST']."/";
	$_SESSION['comum'] = "WebSocialComum/";

/*
	include_once $_SESSION['root'].$_SESSION['comum']."library/php/db.inc.php";
	
	include_once $_SESSION['root'].$_SESSION['comum']."library/php/funcoes.inc.php";
	include_once $_SESSION['root'].$_SESSION['comum'].'/library/php/funcoes.db.php';
	
	include_once $_SESSION['root'].$_SESSION['modulo']."authlib.inc.php";
	require_once $_SESSION['root'] . $_SESSION['modulo'] . "sessao_controller.php";
*/

$unidades = array(
	['id' => 1, 'desc' => 'Unidade 1'],
	['id' => 2, 'desc' => 'Unidade 2'],
	['id' => 3, 'desc' => 'Unidade 3']
);
$setores = array(
	['id' => 1, 'desc' => 'Setor 1'],
	['id' => 2, 'desc' => 'Setor 2'],
	['id' => 3, 'desc' => 'Setor 3']
);
$user = ['usr_nome' => 'Usuário Exemplo'];
$versao = "1.0.0";

	$dia = date('d');
	$dias = intval(date('w'));
	$mes = date('m');
	$ano = date('Y');
	if ($dia < 10){
		$dia = "0" . $dia;
	}

$NomeDia = array(
	0 => "Domingo",
	1 => "Segunda-feira",
	2 => "Terça-feira",
	3 => "Quarta-feira",
	4 => "Quinta-feira",
	5 => "Sexta-feira",
	6 => "Sábado"
);

$NomeMes = array(
	"0" => "Janeiro",
	"1" => "Fevereiro",
	"2" => "Março",
	"3" => "Abril",
	"4" => "Maio",
	"5" => "Junho",
	"6" => "Julho",
	"7" => "Agosto",
	"8" => "Setembro",
	"9" => "Outubro",
	"10" => "Novembro",
	"11" => "Dezembro",
);
?>
<script>
	document.write ("<?php echo $NomeDia[$dias]; ?>" + ", " + <?php echo $dia; ?> + " de " + "<?php echo $NomeMes[$mes]; ?>" + " de " + <?php echo $ano; ?>);
</script>
<html>
<head>
	<title>ProSaude <?php echo $versao; ?> | Software de Gestão Pública</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
	<link rel="shortcut icon" href="<?=$_SESSION['linkroot'].$_SESSION['comum']?>imgsBotoes/mini_logo_elotech.png"> 
	<link href="fonts-proadmin.css" rel="stylesheet" type="text/css">
	<link href="estilo.css" rel="stylesheet" type="text/css">
	<style>
		.inputForm {
			font-family: Verdana, Arial, Helvetica, sans-serif;
			color: #153854;
			font-size: 8pt;
			font-weight: bold;	
			height: 18px;
			border-top: 1px solid #B0CCE5;
			border-left: 1px solid #B0CCE5;
			border-bottom: 1px solid #B0CCE5;
			border-right: 1px solid #B0CCE5;
			background-color:#E8F4FE;
		}
		.formul { background-color:#D0E0F0; width:50%; border: 0px solid; color:#153854; border-radius: 3px; }
	</style>
    <link type="text/css" href="css/menu.css" rel="stylesheet" />
    <script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="lib/ui/jquery.ui.all.js"></script>
    <script type="text/javascript" src="js/menu.js"></script>
    <script type="text/javascript" src="ajax_motor.js"></script>
<script language="javascript">

function getSessao(){
	root = "<?= $_SESSION['root']?>";
	linkroot = "<?= $_SESSION['linkroot']?>";
	comum = "<?= $_SESSION['comum']?>";
	modulo = "<?= $_SESSION['modulo']?>";
}


function atualizaSetor(){
	var set_codigo = $("#setor").val();
	var linkroot = "<?=$_SESSION["linkroot"]?>";
	var modulo = "<?=$_SESSION["modulo"]?>";
	var id_login = "";
	$.ajax({
		url: linkroot+modulo+"trocaDeSetor.php",
		type: "POST",
		data: { set_codigo: set_codigo, usr_codigo: id_login },
		success: function(txt){
			console.log(txt);
		}
	});

}
function atualizaUnidade(){         
	var uni_codigo = $("#unidade").val();
	var linkroot = "<?=$_SESSION["linkroot"]?>";
	var modulo = "<?=$_SESSION["modulo"]?>";
	var id_login = "";
	$('#setor option[value!=""]').remove();
	$("#setor").append("<option value='c' readonly>Carregando ...</option>");
	$.ajax({
		url: linkroot+modulo+"alteraSetorPorUnidade.php",
		type: "POST",
		data: { uni_codigo: uni_codigo, usr_codigo: id_login },
		success: function(txt){
                    console.log(txt);
			jQuery('#setor option[value!=""]').remove();
			if (txt.length>0) {
				jQuery('#setor-hidden').remove();
				jQuery('label[for=setor]').show();
				jQuery('#setor').show();
				jQuery("#setor").append(txt);
			} else {
				jQuery('label[for=setor]').hide();
				jQuery('#setor').hide();
				jQuery("#uni_codigo_t").append("<input type='hidden' name='setor' id='setor-hidden' value='0' />");
			}
		}
	});

}

</script>

</head>

<body bgcolor="#ebfbe3" text="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0" align=center>
	<tr>
		<td style="height: 30px;">
			<?php
			/*
			$_SESSION['id_login'] = '';
			$query = pg_query("select * from logon where id_login = ''");
			$regLogon = pg_fetch_array($query);
			$_SESSION['uni_codigo'] = $regLogon['uni_codigo'];
			//die($regLogon[uni_codigo]."a");
				//  Se a variavel $link for igual a vazia mostra a tela inicial
				if(empty($link)) { 
					$link = "zf/"; 
				}
				// -> Menu Superior
				include_once $_SESSION['root'].$_SESSION['modulo']."novoMenu.php";
			
			*/
			?>
		</td>
	</tr>
	<tr style="background: #D0E0F0;">
		<td height=57><?include_once $_SESSION['root'].$_SESSION['modulo']."menu.php"; ?></td>
	</tr>
	<tr style="background: #FFF;">
		<td height="5"><img src="<?= $_SESSION['linkroot'].$_SESSION['comum'];?>imgs/listra_submenu.jpg" width="100%" height="5"></td>
	</tr>
<?php 
	//	COME&ccedil;O IFRAME MEIO---------------------------------------------------------------->
?>
	<tr>
		<td>
			iframe aqui
		</td>
	</tr>
<?php
	//	FINAL IFRAME MEIO---------------------------------------------------------------->
	//	COME&ccedil;O RODAP�---------------------------------------------------------------->

/*
$selectSetor = "SELECT set_nome,uni_desc,u.uni_codigo,s.set_codigo
 				  FROM logon l
 				  LEFT JOIN setor s
 				    ON s.set_codigo = l.cod_setor
 				  JOIN unidade u ON u.uni_codigo = l.uni_codigo 
 				 WHERE l.id_login = $id_login                                  
 				  " ;
$qSetor = pg_query($selectSetor);

$res = pg_fetch_array($qSetor);

$set_nome = $res['set_nome'];

$uni_desc = $res['uni_desc'];

// Unidade com CNES, descomentar para utilizar
$sql = "SELECT 
			uni.uni_codigo,uni.uni_desc 
		FROM 
			unidade AS uni
		JOIN unidade_usuarios uu
		  on uu.uni_codigo=uni.uni_codigo
	  where usr_codigo = $id_login
	    AND cnes_ativo = 'A'
		ORDER BY uni_desc";
$queryUni = pg_query($sql);

// Setor

$sqlSetor2 = "SELECT 
				set.set_codigo, 
				set.set_nome 
			FROM 
				setor AS set
			INNER JOIN 
				usuarios_setores AS uset ON set.set_codigo=uset.set_codigo
			INNER JOIN 
				usuarios AS usr ON uset.usr_codigo=usr.usr_codigo
			INNER JOIN 
				unidade AS uni ON set.uni_codigo=uni.uni_codigo 
			WHERE 
				(uni.uni_codigo ={$res['uni_codigo']}) AND 
				(usr.usr_codigo ={$id_login})";
$sqlSetor = "SELECT * FROM setor s
		  JOIN usuarios_setores us
		    ON s.set_codigo = us.set_codigo
	     WHERE us.usr_codigo = {$id_login}
               AND s.uni_codigo = {$res['uni_codigo']}
		 ORDER BY set_nome";
$querySet = pg_query($sqlSetor);
*/
?>
	<tr>
		<td height="24">
			<table class="footer-bar" width="100%" height="30" border="0" cellspacing="0" cellpadding="0" align=center>
				<tr>
					<td width="450" >
                         Unidade:
						<select onchange=atualizaUnidade() id="unidade" class="formul" >                                              
						<?php foreach($unidades as $unidade){  
								echo "<option>{$unidade['id']} - {$unidade['desc']}</option>";
							}
                        ?>
                        </select>
					</td>
					<td>
						<label for="setor">Setor:</label> <?php //  var_dump($sqlSetor,$_SESSION['logon']['usr']->uni_codigo); ?>
						<select id="setor" onChange="atualizaSetor()" class="formul">                                                
							<?php foreach($setores as $setor){  
								echo "<option>{$setor['id']} - {$setor['desc']}</option>";
							}
							?>
						</select>
											
						<td  width=8% align="left" valign="middle" >
							<?php 
							if($dias <= 5){
								$color = "RED";
								$blink = "<blink>";
								$fechaBlink = "</blink>";
								}else{
									$color = "BLUE";
								}
							?>
						<?php 
							if($dias >= 0):
						?>
						<a href='../WebSocialComum/autentificacao/autentificacao.php?acao=registroFuturo' title="VALIDADE PARA REGISTRO DO SISTEMA">
							<font color="#0FF235">
								<b><blink>Faltam <?=$dias?> dias</blink></b>
							</font>
						</a>
						<?php endif; ?>
					</td>
					<td  align="left" valign="middle"><font color='#00F6FF'><?=ucwords(strtolower($user['usr_nome']))?></font></td>
					<td  width=10% valign="middle" align="center">
						Vers&atildeo: <?php echo $versao; ?>
					</td>
					<td width=230>
						<?php
						/*
						  $dadosRegistro = "SELECT * FROM CONFIG WHERE CONF_CHAVE = 'VERSAO_ESUS'";
						  //die($dadosRegistro);
						  $exeDadosRegistro = pg_query($dadosRegistro);
						  $resultadoDadosRegistro = pg_fetch_array($exeDadosRegistro);
						  $dias = $resultadoDadosRegistro['dias'];
						  echo "<i><font color=#FFE400>".utf8_decode($resultadoDadosRegistro['conf_valor_string'])."</font></i>";
						*/
						?>

					</td>
				</tr>
			</table>	
			
			
<?
	// FINAL RODAP�
?>
		</td>
	</tr>
</table>
<?php 
$hora = date('H');
//$hora = 20;
if($hora >= 6 && $hora < 12){
	$periodo = "2";
}else if($hora >= 12 && $hora < 18){
	$periodo = "3";
}else if($hora >= 18)
{
	$periodo = "4";
}else if($hora < 6){
	$periodo = "1";
}
/*
$pegaSetor = "select * from usuarios_setores as ususet
					   join geladeira as gel
					     on gel.set_codigo = ususet.set_codigo
				      where ususet.usr_codigo = {$id_login}";
$querySetor = pg_query($pegaSetor);
$qnt = pg_num_rows($querySetor);
*/
$qnt = 1;
if($qnt != 0){
	/*
	$validaPeriodo = "select * from temperatura_geladeira where temp_data =  CURRENT_DATE and temp_periodo ='$periodo'";
	$queryPeriodo = pg_query($validaPeriodo);
	$linhasPeriodo = pg_num_rows($queryPeriodo);
	*/
	$linhasPeriodo = 0;
	if($linhasPeriodo == 0){
		include_once $_SESSION['root'].$_SESSION['modulo']."temperaturaAlert.php";
	}
}

?>
<?php
/*
$id_login = intval($id_login);
$stmt_msg = "SELECT COUNT(msg_codigo) ".
    "FROM mensagem ".
    "WHERE usr_codigo_to = {$id_login} AND msg_copy = 'N' AND msg_dt_lida IS NULL ";
$total = (int) db_get($stmt_msg);
*/
$total = 0;
if( $total > 0 )
{
    print "
    <script type='text/javascript'>
        msg = 'Voce possui {$total} mensagem(ns) nao lida(s)\\nDeseja le-la(s) agora ?';
        if( confirm(msg) )
        {
            var endereco = 'mensagem.php?id_login=0';
            var params = 'width=600,height=350,scrollbars=yes,resizable=yes,top=100,left=10';
            window.open( endereco, 'msg', params );
        }
    </script>
    ";
}
?>
</body>
</html>
