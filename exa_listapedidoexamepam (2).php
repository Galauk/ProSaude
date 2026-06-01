<?
session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
verauth($id_login);
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
cabecario( $hotkey = true);

reglog($id_login,"Acessando LISTA DE EXAMES");

//
//-> Botoes
  echo "<fieldset>
            <legend>Opções</legend>
            <a href=exa_pedidoexamePAM.php?acao=form_add&id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg border=0></a>
             <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
              <tr>
                <form method='post' action='$PHP_SELF?$_SERVER[QUERY_STRING]'>
                  <input type=hidden name=action value=busca>
                <td width=30>Buscar:</td>
                <td width=120><input type='text' name=palavra_chave class=box /></td>
                <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg></td>
               </form>
              </tr>
             </table>
       </fieldset>
      <br>";

echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
         <tr>
          <td>
           <fieldset>
            <legend>Listando Tipo de Metodos Cadastradas</legend>
             <table width='100%' align='center' cellspacing='2' cellpadding='4' border='0' class='lista'>
              <tr bgcolor=FFFFFF>
                   <td width='50'>Cód.</th>
                   <td width='*'>Nome</th>
                   <td width='*'>Nome da Mae</th>
                   <td width='*'>Nome da Pai</th>
                   <td width='*'>Dt.Pedido</th>
                   <td colspan=2 width='65' align='center'>&nbsp;</th>
                  </tr>";
if($action=="busca") {
   $palavra_chave = strtoupper($palavra_chave);
   $query = pg_query("SELECT *from cadastrodoexame as t left join usuario as u on u.usu_codigo = t.usu_codigo where u.usu_nome like '%$palavra_chave%'");
	} else {
   $query = pg_query("SELECT *from cadastrodoexame as t left join usuario as u on u.usu_codigo = t.usu_codigo");
}
           while($row=pg_fetch_array($query)) {
		 $dt = explode("-",$row[cad_datapedido]);
		 $datapedido = "$dt[2]/$dt[1]/$dt[0]";
           echo "<tr>
                           <td width='50' align='center'>$row[usu_codigo]</td>
                           <td width='*'>$row[usu_nome]</td>
                           <td width='*'>$row[usu_mae]</td>
                           <td width='*'>$row[usu_pai]</td>
                           <td width='*'>$datapedido</td>
                           <td width='65' align='center'>
                           <a href='exa_pedidoexamePAM.php?acao=form_edit&cad_exame=$row[cad_exame]&id_login=$id_login'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg border=0></a></td>
                           <td width='65' align='center'>
                           <a href='exa_digitacaoresultado.php?cad_exame=$row[cad_exame]&id_login=$id_login'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/resultado_on.png border=0></a></td>
                         </tr>";
                 }

           echo "</table>
           </fieldset>
          </td>
         </tr>
      </table>";
