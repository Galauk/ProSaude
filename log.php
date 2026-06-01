<?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();

$_SESSION[modulo] = "WebSocialSaude/"; $_SESSION[root] = $_SERVER[DOCUMENT_ROOT] . "/"; $_SESSION[linkroot] = "http://" . $_SERVER[HTTP_HOST] . "/"; $_SESSION[comum] = "WebSocialComum/"; $_SESSION[modulo] = "WebSocialSaude/"; require_once $_SESSION[root].$_SESSION[modulo]."sessao_controller.php";

$sessao = new TempoSessao();
$sessao->primeiraPagina();

//------------------------------------------------------------------>


//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>

//
//-> Botoes
  echo "<fieldset>
	    <legend>Opções</legend>
	       <a href=".$_SESSION[linkroot].$_SESSION[modulo]."zf/usuarios/usuarios><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>

			<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
			   <tr>
				  <form method=post action=$PHP_SELF>
					 <input type=hidden name=acao value=busca>
					 <td width=30>Buscar:</td>
					 <td width=120><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
					 <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg></td>
				  </form>
			   </tr>
			</table>
		 
	   </fieldset>
	  <br>";

//
//-> Listando

$common = new commonClass();
$form = new classForm();
$table = new tableClass();

echo $common->incJquery();
echo "
	 <fieldset>
	 <legend>Log dos Usuarios dos Sistema</legend>";
	$sql = "SELECT usr_nome,* 
			  FROM log l
		 	  JOIN usuarios us
		  		ON l.usr_codigo = us.usr_codigo";
	if($acao == "busca"){
	$sql.=" where log_arquivo like'%$palavra_chave%'";
}
  	$sql.="ORDER BY log_cod desc 
        	LIMIT 15";
  
	//
	
	
//	echo $table->openTable('lista','100%');
//		echo $table->criaLinha(Array("Usu?rio","Data/Hora","Caminho","Link","Ip","Sql"),null,null,"S");
//		while($res=pg_fetch_array($query)){
//			echo $table->criaLinha(Array($res[usr_nome],$res[log_data],$res[log_arquivo],$res[log_qs],$res[log_ip],$res[log_sql]));
//		}
//	echo $table->closeTable();
echo"<iframe name=fazer_agendamento src=verlog.php?id_login=$id_login frameborder=no marginheight=0 marginwidth=0 scrolling=yes width=100% height=300></iframe>
	</fieldset>
	";

?>

