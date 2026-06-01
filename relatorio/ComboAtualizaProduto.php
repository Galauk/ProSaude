<?php
//if(!empty($_GET["valor"])) {
   $gru_codigo = $_GET["valor"];

   	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
   echo "      <select name='pro_codigo' class=box>\n";
   echo "           8<option value=''> --- Todos Produtos ---</option>\n";
                    if (!$gru_codigo) {
                        $query=pg_query("SELECT pro_codigo, pro_nome FROM produto ORDER BY pro_nome");
                    } else {
                            $query=pg_query("SELECT produto.pro_codigo, produto.pro_nome
                                               FROM produto, grupo
											   WHERE produto.gru_codigo=grupo.gru_codigo
									  		     AND produto.gru_codigo=$gru_codigo
										    ORDER BY pro_nome");
                    }
					while($Produto=pg_fetch_array($query)) {
                          echo ($pro_codigo==$Produto[pro_codigo])?
                              "<option value='$Produto[pro_codigo]' selected>".substr($Produto[pro_nome],0,60)."</option>" :
                              "<option value='$Produto[pro_codigo]'         >".substr($Produto[pro_nome],0,60)."</option>\n";
					}
echo "          </select>\n";
//} 
?>
