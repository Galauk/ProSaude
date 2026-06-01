<?
//echo "<pre>".print_r($_REQUEST,1);
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
verauth($id_login);
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
cabecario( $hotkey = true);

reglog($id_login,"Acessando LISTA DE EXAMES");

//<input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg>
//-> Botoes
  echo "<fieldset>
            <legend>Opções</legend>
             <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
              <tr>
                <td width=30><a href='exame/exa_listapedidoexame.php'><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.jpg></a></td>
                <form method='post' action='$_SERVER[PHP_SELF]'>
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
                   <td width='50'>C&oacute;d.</th>
                   <td width='*'>Nome</th>
                   <td width='*'>Nome da Mae</th>
                   <td width='*'>Nome da Pai</th>
                   <td width='*'>Dt.Pedido</th>
                   <td colspan=2 width='65' align='center'>&nbsp;</th>
                  </tr>";
if($action=="busca") {
   $palavra_chave = strtoupper($palavra_chave);
   $query = pg_query("SELECT u.usu_codigo,
						       usu_nome,
						       usu_mae,
						       usu_pai,
						       col_data_coleta,
						       a.age_codigo,
						       ai.agei_data
						  FROM agenda a
						  JOIN agenda_itens ai
						  ON a.age_codigo = ai.age_codigo
						  JOIN coleta c
						    ON c.agei_codigo = ai.agei_codigo
						  LEFT JOIN usuario u
							ON u.usu_codigo = a.usu_codigo
						 WHERE u.usu_nome ilike '%$palavra_chave%'
						 GROUP BY u.usu_codigo,
						       usu_nome,
						       usu_mae,
						       usu_pai,
						       col_data_coleta,
						       a.age_codigo,
						       ai.agei_data   
						 ORDER BY col_data_coleta desc");
 
	} else {
   $query = pg_query(  "SELECT u.usu_codigo,
						       usu_nome,
						       usu_mae,
						       usu_pai,
						       col_data_coleta,
						       a.age_codigo,
						       ai.agei_data
						  FROM agenda a
						  JOIN agenda_itens ai
						  ON a.age_codigo = ai.age_codigo
						  JOIN coleta c
						    ON c.agei_codigo = ai.agei_codigo
						  LEFT JOIN usuario u
							ON u.usu_codigo = a.usu_codigo
						 GROUP BY u.usu_codigo,
						       usu_nome,
						       usu_mae,
						       usu_pai,
						       col_data_coleta,
						       a.age_codigo,
						       ai.agei_data            
						 ORDER BY col_data_coleta desc						
						limit 15");
						//die("?");
	}
           while($row=pg_fetch_array($query)) {
		 $dt = explode("-",$row[agei_data]);
		 $datapedido = "$dt[2]/$dt[1]/$dt[0]";
           echo "<tr>
                           <td width='50' align='center'>$row[usu_codigo]</td>
                           <td width='*'>$row[usu_nome]</td>
                           <td width='*'>$row[usu_mae]</td>
                           <td width='*'>$row[usu_pai]</td>
                           <td width='*'>$datapedido</td>
                           <td width='65' align='center'>
                           <a href='exa_digitacaoresultado2.php?age_codigo=$row[age_codigo]&id_login=$id_login&usu_codigo=$row[usu_codigo]'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/resultado_on.png border=0></a></td>
                         </tr>";
                 }

           echo "</table>
           </fieldset>
          </td>
         </tr>
      </table>";
