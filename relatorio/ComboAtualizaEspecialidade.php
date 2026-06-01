<?php
//if(!empty($_GET["valor"])) {
   $med_codigo = $_GET["valor"];
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
   echo "      <select name='esp_codigo' class='inputForm'>\n";
                     if (!$med_codigo) {
                        $query=pg_query("SELECT esp_codigo, esp_nome FROM especialidade ORDER BY esp_nome");
                    } else {
                            $query=pg_query("SELECT especialidade.esp_codigo, especialidade.esp_nome
                                               FROM especialidade ,  medico_especialidade
											   WHERE especialidade.esp_codigo=medico_especialidade.esp_codigo
									  		     AND medico_especialidade.med_codigo=$med_codigo
										    ORDER BY esp_nome");
                    }
					while($especial=pg_fetch_array($query)) {
                          echo ($esp_codigo==$especial[esp_codigo])?
                                "<option value='$especial[esp_codigo]' selected> $especial[esp_nome]</option>" :
                                "<option value='$especial[esp_codigo]' > $especial[esp_nome]</option>\n";
					}
echo "          </select>\n";
//}
?>
