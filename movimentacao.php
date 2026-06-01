<?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	//verauth($id_login);
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
//------------------------------------------------------------------>


 reglog($id_login,"Acessando Materiais");
//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>

$data = date("d/m/Y");

  echo "<fieldset>
	    <legend>Opções</legend>
           ".ChmodBtn($id_login,'entrada','entrada.php?acao=form_entrada')."
           ".ChmodBtn($id_login,'saida','saida.php?acao=form_saida'),"
           ".ChmodBtn($id_login,'transferencia','remanejamento.php?acao=form_remanejamento')."
           ".ChmodBtn($id_login,'consolidacao','consolidacao.php?acao=form_consolid')."
           ".ChmodBtn($id_login,'recepcao_materiais','recepcao_materiais.php?acao=form_consolid')."
           ".ChmodBtn($id_login,'recepc_medicam','recepcao_transferencia.php?acao=form_consolid')."
           ".ChmodBtn($id_login,'alterar_req_mat','alterarequisicao.php?acao=form_requisicao')."
           ".ChmodBtn($id_login,'alterar_req_transf_med','alteratransferencia.php?acao=form_requisicao')."
           "./*ChmodBtn($id_login,'recepcao_impressao','recepcao_impressao.php?acao=form_consolid').*/"
	       <a href=materiais.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>
	   </fieldset><br />";
?>

