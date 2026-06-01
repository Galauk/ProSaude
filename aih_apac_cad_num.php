<?php
/**
 * Cadastro de Números para Procedimento de Alta Complexidade ( APAC ) e Autorização de Internação Hospitalar ( AIH )
 * - Arquivos Relacionados: funcoes.js, authilb.inc.php, funcoes.inc.php, config.inc.php
 * - Tabelas: aih
 * Adiciona os números de APAC e AIH.
*/ 

/**
 Cadastro dos numeros da  APAC/AIH
*/
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";
Cabecario( $hotkey = false );

verauth($id_login);

// verificando o tipo
if( empty($tipo) )
	$tipo = 'APAC';
else
	$tipo = ( strtoupper( $tipo ) == 'AIH' ? 'AIH' : 'APAC' );

// acao do form
$action = "?id_login=$id_login&acao=add&tipo=$tipo";

if( empty($acao) )
{
?>
<script type="text/javascript" src="funcoes.js"></script>
<script type="text/javascript">

function valida_num()
{
	
	if( ! valida('num_ini','Numero Inicial') ) return false;
	if( ! valida('num_fim','Numero Final') ) return false;

	var Arr = $F('num_ini','num_fim'); 
/*	if( parseInt( Arr[0] ) >=  parseInt( Arr[1] ) )
	{
		alert("Os valor do campo Inicial deve ser menor do que o Final !");
		return false;
	}
*/
	if( parseInt( Arr[0] ) >  parseInt( Arr[1] ) )
	{
		alert("Os valor do campo Inicial deve ser menor do que o Final !");
		return false;
	}
	return true;
}

</script>

<form action="<?=$action;?>" method="post" onsubmit="return( valida_num() )">
<fieldset>
<legend>Cadastro de N&uacute;meros</legend>

<?php

$prim = db_get("SELECT num_ini FROM aih_apac_numero WHERE tipo='$tipo' ORDER BY codigo DESC LIMIT 1");
$ult = db_get("SELECT num_fim FROM aih_apac_numero WHERE tipo='$tipo' ORDER BY codigo DESC LIMIT 1");
if( $prim )
	print "<p>Numero Inicial da &Uacute;ltima Sequencia cadastrada: <strong>".($prim)."</strong></p>";
if( $ult )
	print "<p>Numero Final da &Uacute;ltima Sequencia cadastrada: <strong>".($ult)."</strong></p>";

?>

<table border="0">
	<tr>
		<td width="120"><label for="num_ini">N&uacute;mero Inicial</label></td>
		<td><input type="text" name="num_ini" id="num_ini" class="box" size="15" maxlength="40" /></td>
	</tr>
	<tr>
		<td><label for="num_fim">N&uacute;mero Final</label></td>
		<td><input type="text" name="num_fim" id="num_fim" class="box" size="15" maxlength="40" /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="image" src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/adicionar_on.jpg" alt="Adicionar" /></td>
	</tr>
</table>
</fieldset>
</form>
<?php

}
else if( $acao == 'add' )
     
{
/*
$verprim = db_get("SELECT count(*) FROM aih WHERE aih_numero_aih='$num_ini%'");
echo ("SELECT count(*) FROM aih WHERE aih_numero_aih='$num_ini%'");
exit(0);
	if( $verprim > 0)
        {
		print '
		<script type="text/javascript">
			alert("Este numero Inicial de AIH ja foi utilizado anteriormente ");
		</script>
		<p class="aviso">Numero de AIH nao inserida !</p>
		</body></html>';
		die();
	}
$verult = db_get("SELECT count(*) FROM aih WHERE aih_numero_aih='$num_fim%'");
echo $verult;
	if( $verult > 0)
        {
		print '
		<script type="text/javascript">
			alert("Este numero Final de AIH ja foi utilizado anteriormente ");
		</script>
		<p class="aviso">Numero de AIH nao inserida !</p>
		</body></html>';
		die();
	}
*/
// Rotina para verificacao da existencia de algum numero de AIH ja cadastrada no cadastro de AIH
        $cont = $num_ini;
        while ( $cont <= $num_fim ) {
	     if ($tipo == 'APAC') {
	        $veapac = pg_fetch_array(pg_query("select count(*) from apac
		           where apac_num like '$cont%' "));
		if ($veapac[0] > 0) {
	           print " 
		        <script type=\"text/javascript\">
	         alert(\"Ja existe uma APAC com este numero. Este intervalo nao foi gerado. Verifique numeracao correta. \")
			    setTimeout(\"location='$PHP_SELF?aih_apac_cad_num.php?id_login=$id_login&tipo=APAC'\", 0);
		        </script>";
		    exit();
		}    
	      }
	     else {
	        $veaih = pg_fetch_array(pg_query("select count(*) from aih
		           where aih_numero_aih like '$cont%' "));
			   
		if ($veaih[0] > 0) {
	           print " 
		        <script type=\"text/javascript\">
	         alert(\"Ja existe uma AIH com este numero. Este intervalo nao foi gerado. Verifique numeracao correta. \")
			    setTimeout(\"location='$PHP_SELF?aih_apac_cad_num.php?id_login=$id_login&tipo=AIH'\", 0);
		        </script>";
		    exit();
		}    
             }	
	    $cont = $cont + 1; 
	}  //fim do while ($cont <= $num_fim)


	reglog( $id_login, "Cadastrando sequencia de numeros, tipo={$tipo}, num_ini={$num_ini}, num_fim={$num_fim}");

	$url  = ( $tipo == 'APAC' ? 'apac.php' : 'aih.php' );
	$url .= '?id_login='.$id_login;

	#inserido por André
	$num_prox = $num_fim + 1;

	$stmt = "INSERT INTO aih_apac_numero (
	num_ini,
	num_fim,
	num_prox,
	tipo
	 ) VALUES (
	".$num_ini.",
	".$num_fim.",
	".$num_prox.",
	'$tipo' )";
	db_query( $stmt );

	$prox = $num_ini;

	while( $prox <= $num_fim ){

		# GERANDO O DIGITO ------------------------------------------
		$stmt = "SELECT ( $prox - 11 * CEIL( $prox / 11 ) ) as digito" ;
		$dig = db_get( $stmt );
		//$qry = db_query( $stmt );
		//$row = pg_fetch_row( $qry );
		//$row = pg_fetch_array( $qry );

		//if( $row[1] < $row[2] || pg_num_rows($qry) == 0 ) return 0;
		$R = $prox . '-' . ($dig == '10' ? '0' : $dig );
		#------------------------------------------------------------

		$insert = "INSERT INTO aih_apac_numeros_resto (
                                       aan_numero_resto,
				       aan_tipo
				) VALUES (
                                       '".$R."',
				       '".$tipo."'
				)";
                db_query($insert);
		//print "<br />";
		$prox = $prox + 1;
	}



	print "<p class='aviso ok'>N&uacute;mero inserido !</p>".
	"<script type='text/javascript'>
		setTimeout('document.location.href=\"$url\"',3000);
	</script>";
}
?>
