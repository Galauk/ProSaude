<?php
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>

include_once "authlib.inc.php";
verauth($id_login);
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
cabecario();

include_once "lib/debug.inc.php";
//------------------------------------------------------------------>
?>
<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<script language="JavaScript" type="text/javascript" src="ajax_motor.js"></script>
<script language="JavaScript" type="text/javascript" src="g_ajax.js"></script>
<script language="JavaScript" type="text/javascript" src="g_script.js"></script>
<script type="text/javascript">
    function at_agente()
    {
	var agt = $('agt_codigo');
	
	if( agt.value == 0 ) return false;
	
	var url = 'fazer_agendamento.ajax.php?acao=busca_agente&agt_codigo='+agt.value;
        
	ajax_tudo( url, at_agente_cb );
	return null;
    }
    
    function at_agente_cb( resp )
    {
        obj = eval( resp );

        var num = $('agt_numero'), respon = $('agt_responsavel');
	
	num.value = obj.agt_numero;
	respon.value = obj.agt_responsavel;
        
	//at_iframe_esq();
	
	return null;
    }
</script>
<script>
    function pacientes(codigo,nome,nascimento,mae,cidade)
    {
        document.getElementById("pac_codigo").value = codigo;
        document.getElementById("pac_nome").value = nome;
        document.getElementById("pac_nascimento").value = nascimento;
        document.getElementById("pac_mae").value = mae;
        document.getElementById("pac_cidade").value = cidade;
        document.getElementById("pac_prontuario").value = '';
        document.getElementById('pac_nascimento').focus();
    }

    function buscarMedicos(numero)
    {
        n = numero;
        esp_codigo = document.getElementById('esp_codigo').value;
        url = "buscarMedicos.php?esp_codigo="+esp_codigo;
        //alert(url);
        ajax_tudo(url, popularMedico);		
    }
    
    function popularMedico(txt)
    {
        d = document.getElementById('med_codigo');
        d.innerHTML = "";
        d.options[0]=new Option("....","");
        r =txt;
        res = r.split(";");
        for(x = 0; x < res.length; x++)
        {
            aux = res[x].split("-");
            if(aux[1] != undefined)
            {
                if(n == 1)
                {
                    med_codigo = document.getElementById("medico_codigo").value;
                    if(med_codigo == aux[0])
                    {
                        d.options[d.options.length] = new Option(aux[1],aux[0], '', true);
                    }
                    else
                    {
                        d.options[d.options.length] = new Option(aux[1],aux[0]);
                    }
                }
                else
                {
                    d.options[d.options.length] = new Option(aux[1],aux[0]);
                }
            }
        }
    }
    function validar()
    {
        if(document.getElementById("uni_codigo").value == "")
        {
            alert("Por favor escolha a unidade");
            document.getElementById("uni_codigo").focus();
            return false;
        }
        if(document.getElementById("esp_codigo").value == "")
        {
            alert("Por favor escolha a especialidade");
            document.getElementById("esp_codigo").focus();
            return false;
        }
        if(document.getElementById("med_codigo").value == "")
        {
            alert("Por favor escolha o medico");
            document.getElementById("med_codigo").focus();
            return false;
        }
        if(document.getElementById("agt_codigo").value == "")
        {
            alert("Por favor escolha o Agente Responsavel");
            document.getElementById("agt_codigo").focus();
            return false;
        }
        if(document.getElementById("pac_codigo").value == "")
        {
            alert("Por favor escolha o paciente");
            document.getElementById("pac_codigo").focus();
            return false;
        }
    }
    function buscar_dados_paciente(valor)
    {
        url = "buscar_generico.php?tipo=dados_paciente&usu_prontuario="+valor;
        ajax_tudo(url, preencher_campo);
    }
        
    function preencher_campo(txt)
    {
        if(txt != "vazio" && txt != undefined && txt != "")
        {
            txt = txt.split(";");
            document.getElementById('pac_codigo').value = txt[0];
            document.getElementById('pac_nome').value = txt[1];
            document.getElementById('pac_nascimento').value = txt[2];
            document.getElementById('pac_mae').value = txt[3];
            document.getElementById('pac_cidade').value = txt[4];
        }
        else
        {
            document.getElementById('pac_codigo').value = "";
            document.getElementById('pac_nome').value = "NADA ENCONTRADO";
            document.getElementById('pac_nascimento').value = "";
            document.getElementById('pac_mae').value = "";
            document.getElementById('pac_cidade').value = "";
        }
    }
    
    function buscar_prontuario()
    {
      pac_codigo = document.getElementById('pac_codigo').value;
      url = "buscar_generico.php?tipo=prontuario_paciente&usu_codigo="+pac_codigo;
      ajax_tudo(url, preencher_campo_prontuario);
    }
    
    function preencher_campo_prontuario(txt)
    {
        if(txt != "vazio" && txt != undefined && txt != "")
        {
        document.getElementById('pac_prontuario').value = txt;
        }
    }
</script>
<style>
    .tr
    {
        border-bottom:1px solid;
        border-right:1px solid;
        border-color:c9c9c9;
        background:white;
    }
    .td
    {
        border-bottom:1px dotted;
        border-right:1px dotted;
        border-color:c9c9c9;
    }
</style>
<?php
	
    if(!$acao)
    {
        $acao = "listar";
    }

    if($acao == "listar")
    {
        echo "<fieldset>";
            echo "<legend>Op&ccedil;&otilde;es</legend>";
            echo "<table>";
                echo "<tr>";
                    echo "<td width='75px'>";
                        echo "<a href='cad_lista_espera.php?id_login=$id_login&acao=listar'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>";
                    echo "</td>";
                    echo "<td width='200x'>";
                        //echo "<a href='cad_lista_espera.php?acao=form_add&id_login=$id_login'>";
                            //echo "<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg' border='0'></a>&nbsp;<a href='javascript:' onclick=\"window.open('print_lista_espera.php?tipo_busca=todos',null,'width=800,height=500,scrollbars=yes');return false;\"><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/imprimir.jpg' border='0'></a>";
						echo ChmodBtn($id_login,'adicionar',"cad_lista_espera.php?acao=form_add&id_login=$id_login").
						"&nbsp;<a href='javascript:' onclick=\"window.open('print_lista_espera.php?tipo_busca=$tipo_busca&palavra_chave=$palavra_chave',null,'width=800,height=500,scrollbars=yes');return false;\"><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/imprimir.jpg' border='0'></a>";
                        
                    echo "</td>";
					if(chmodbtn($id_login, "procurar_if", "cad_lista_espera.php"))
					{
					  echo "<form method=get action=$PHP_SELF>";
					}
                        echo "<input type='hidden' name='acao' value='buscar'>";
                        echo "<input type='hidden' name='id_login' value='$id_login'>";
                        echo "<td width='100' align='right'>Buscar</td>";
                        echo "<td width='90'>";
                        echo "<input type='text' name='palavra_chave' class='box' onBlur='javascript:this.value=this.value.toUpperCase();'>";
                        echo "</td>";
                        echo "<td width='90'>";
                            echo "<select name=\"tipo_busca\" class='box'>";
                                echo "<option value=\"especialidade\">Especialidade</option>";
                                echo "<option value=\"paciente\" selected>Paciente</option>";
                                echo "<option value=\"medico\">M&eacute;dico</option>";
                                echo "<option value=\"unidade\">Unidade</option>";
                            echo "</select>";
                        echo "</td>";
                        echo "<td>";
                            echo ChmodBtn($id_login,'procurar','cad_lista_espera.php');
                        echo "</td>";
                    echo "</form>";
                    echo "<td width=107>";
                        echo "<a href='logoff.php?id_login=$id_login' target='_parent'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/sair.gif border=0></a>";
                    echo "</td>";
                echo "</tr>";
            echo "</table>";
        echo "</fieldset>";
        echo "<fieldset>";
                echo "<legend>Listando primeiros 15 registros</legend>";
                $sql = "SELECT e.lie_codigo, 
                			   e.lie_data_cad, 
                			   a.usu_codigo, 
                			   a.usu_nome, 
                			   b.uni_codigo,
                			   b.uni_desc, 
                			   c.esp_codigo, 
                			   c.esp_nome, 
                			   d.med_codigo, 
                			   d.med_nome, 
                			   agt_codigo
                          FROM usuario a, 
                          	   unidade b, 
                          	   especialidade c,
                        	   medico d, 
                        	   lista_espera e
                         WHERE a.usu_codigo = e.usu_codigo
                           AND b.uni_codigo = e.uni_codigo
                           AND c.esp_codigo = e.esp_codigo
                           AND d.med_codigo = e.med_codigo
                           AND (e.lie_status <> 'D' OR e.lie_status IS NULL) 
                           AND e.lie_data_age IS NULL
                         ORDER BY b.uni_desc, 
                         	   c.esp_nome, 
                         	   d.med_nome, 
                         	   e.lie_data_cad, 
                         	   a.usu_nome 
                         LIMIT 15";
                debug($sql, $PHP_SELF, $id_login);
						
				if(chmodbtn($id_login, "listar_if", "cad_lista_espera.php"))
				{
					$exec_sql = db_query($sql);
				}
				
				
                echo "<legend>Listando ".pg_num_rows($exec_uni)." registros</legend>";
                $auxUni = "";
                $auxEsp = "";
                $auxMed = "";
                $i = 0;
                $n = 0;
                $x = 0;
                echo "<table class='lista' width=100% cellpading=\"0\" cellspacing=\"0\">";
                while($row = pg_fetch_array($exec_sql))
                {
                    if($row[uni_desc] != $auxUni)
                    {
                        if($i != 0)
                        {
                            echo "<tr>";
                                echo "<td>";
                                    echo "&nbsp;";
                                echo "</td>";
                                echo "<td>";
                                    echo "&nbsp;";
                                echo "</td>";
                                echo "<th colspan=\"7\" align=\"left\">";
                                    echo $i." Registros";
                                echo "</th>";
                            echo "</tr>";
                        }
                        if($x != 0)
                        {
                            echo "<tr>";
                                echo "<td>";
                                    echo "&nbsp;";
                                echo "</td>";
                                echo "<th colspan=\"8\" align=\"left\">";
                                    echo $x." Registros";
                                echo "</th>";
                            echo "</tr>";
                        }
                        if($n != 0)
                        {
                            echo "<tr>";
                                echo "<th colspan=\"9\" align=\"left\">";
                                    echo "Total de registros: ".$n;
                                echo "</th>";
                            echo "</tr>";
                        }
                        echo "<tr>";
                            echo "<th class=\"td\" colspan=\"9\" align=\"left\">";
                                echo $row[uni_desc];
                            echo "</th>";
                        echo "</tr>";
                        $auxUni = "";
                        $auxEsp = "";
                        $auxMed = "";
                        $i = 0;
                        $n = 0;
                        $x = 0;
                    }
                    if($row[esp_nome] != $auxEsp)
                    {
                        if($i != 0)
                        {
                            echo "<tr>";
                                echo "<td>";
                                    echo "&nbsp;";
                                echo "</td>";
                                echo "<td>";
                                    echo "&nbsp;";
                                echo "</td>";
                                echo "<th colspan=\"7\" align=\"left\">";
                                    echo $i." Registros";
                                echo "</th>";
                            echo "</tr>";
                        }
                        if($x != 0)
                        {
                            echo "<tr>";
                                echo "<td>";
                                    echo "&nbsp;";
                                echo "</td>";
                                echo "<th colspan=\"8\" align=\"left\">";
                                    echo $x." Registros";
                                echo "</th>";
                            echo "</tr>";
                        }
                        echo "<tr>";
                            echo "<td>";
                                echo "&nbsp;";
                            echo "</td>";
                            echo "<td class=\"td\" colspan=\"8\">";
                                echo $row[esp_nome];
                            echo "</td>";
                        echo "</tr>";
                        $i = 0;
                        $x = 0;
                    }
                    if($row[med_nome] != $auxMed)
                    {
                        $pos = 1;
                        if($i != 0)
                        {
                            echo "<tr>";
                                echo "<td>";
                                    echo "&nbsp;";
                                echo "</td>";
                                echo "<td>";
                                    echo "&nbsp;";
                                echo "</td>";
                                echo "<th colspan=\"7\" align=\"left\">";
                                    echo $i." Registros";
                                echo "</th>";
                            echo "</tr>";
                        }
                        echo "<tr>";
                            echo "<td>";
                                echo "&nbsp;";
                            echo "</td>";
                            echo "<td>";
                                echo "&nbsp;";
                            echo "</td>";
                            echo "<td class=\"td\" colspan=\"7\">";
                                echo $row[med_nome];
                            echo "</td>";
                        echo "</tr>";
                        $i = 0;
                    } else {
                        $pos++;
                    }
                    echo "<tr>";
                        echo "<td>";
                            echo "&nbsp;";
                        echo "</td>";
                        echo "<td>";
                            echo "&nbsp;";
                        echo "</td>";
                        echo "<td>";
                            echo "&nbsp;";
                        echo "</td>";
                        echo "<td>";
                            echo "<b>".$pos."&ordm</b>";
                        echo "</td>";
                        echo "<td class=\"td\" width=\"135px\">";
                            $data = explode(" ", $row[lie_data_cad]);
                            $dat = explode("-", $data[0]);
                            $da = $dat[2]."/".$dat[1]."/".$dat[0];
                            echo $da." - ".substr($data[1], 0, 8);
                        echo "</td>";
//                        echo "<td class=\"td\">$row[lie_codigo]&#176; - ";
                        echo "<td class=\"td\">$row[usu_nome]";
                        echo "</td>";
                        echo "<td class=\"td\" width=\"180px\">";
                                if( !empty($row[agt_codigo]) )
                                {
                                    $row_resp = db_get("SELECT agt_descricao 
                                    					  FROM agente
                                                         WHERE agt_codigo = ".$row[agt_codigo]);
                                    echo $row_resp;
                                }
                                else
                                {
                                    echo "&nbsp;";
                                }
                            echo "</td>";
                        echo "<td class='td' width=\"60px\">";
                            echo ChmodBtn($id_login,'editar','cad_lista_espera.php?acao=edit&lie_codigo='.$row[lie_codigo]);
                        echo "</td>";
                        echo "<td class='td' width=\"60px\">";
                            echo ChmodBtn($id_login,'apagar','cad_lista_espera.php?acao=del&lie_codigo='.$row[lie_codigo]);
                        echo "</td>";
                    echo "</tr>";
                    $auxUni = $row[uni_desc];
                    $auxEsp = $row[esp_nome];
                    $auxMed = $row[med_nome];
                    $i++;
                    $n++;
                    $x++;
                }
                if($n != 0)
                {
                    echo "<tr>";
                        echo "<th colspan=\"5\" align=\"left\">";
                            echo "Total de registros: ".$n;
                        echo "</th>";
                    echo "</tr>";
                }
                echo "</table>";
        echo "</fieldset>";
            
    } else if($acao == "buscar") {
            
        if(strlen($palavra_chave) < 3)
        {
            echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>";
            echo "<table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>";
                echo "<tr bgcolor=#F9F9F9>";
                    echo "<td align=center>";
                        echo "<font color=red size=2>";
                            echo "<b>ERRO</b>";
                        echo "</font>";
                        echo "<br />";
                        echo "Busca com menos de <b>3</b> caracteres n&atilde;o permitida";
                    echo "</td>";
                echo "</tr>";
            echo "</table>";
            echo "<br />";
            echo "<SCRIPT LANGUAGE=\"JavaScript\">";//"
                echo "setTimeout(\"location='$PHP_SELF?id_login=$id_login&acao=listar'\", 2000);";//"
            echo "</SCRIPT>";
            exit();
        }
        
        //-> Subistituindo o + por porcentagem na busca
        $str = str_replace("+","%",$palavra_chave);
        $pos = strpos($palavra_chave,"+");
        if($pos=="0")
        {
            $v1=1;
        }
        else
        {
            $v1=2;
        }
        //
        echo "<fieldset>";
            echo "<legend>Op&ccedil;&otilde;es</legend>";
            echo "<table border=0>";
                echo "<tr>";
                    echo "<td width='75px'>";
                        echo "<a href='cad_lista_espera.php?id_login=$id_login&acao=listar'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>";
                    echo "</td>";
                    echo "<td width='200px'>";
                        /*echo "<a href='cad_lista_espera.php?acao=form_add&id_login=$id_login'>";
                            echo "<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg' border='0'>&nbsp;<a href='javascript:' onclick=\"window.open('print_lista_espera.php?tipo_busca=$tipo_busca&palavra_chave=$palavra_chave',null,'width=800,height=500,scrollbars=yes');return false;\"><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/imprimir.jpg' border='0'></a>";*/
						echo ChmodBtn($id_login,'adicionar',"cad_lista_espera.php?acao=form_add&id_login=$id_login").
						"&nbsp;<a href='javascript:' onclick=\"window.open('print_lista_espera.php?tipo_busca=$tipo_busca&palavra_chave=$palavra_chave',null,'width=800,height=500,scrollbars=yes');return false;\"><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/imprimir.jpg' border='0'></a>";
                    echo "</td>";
                    if(chmodbtn($id_login, "procurar_if", "cad_lista_espera.php"))
					{
					  echo "<form method=get action=$PHP_SELF>";
					}
                        echo "<input type='hidden' name='acao' value='buscar'>";
                        echo "<input type='hidden' name='id_login' value='$id_login'>";
                        echo "<td width='180' align='right'>Buscar</td>";
                        echo "<td width='90'>";
                                echo "<input type='text' name='palavra_chave' class='box' onBlur='javascript:this.value=this.value.toUpperCase();'>";
                        echo "</td>";
                        echo "<td width='90'>";
                            echo "<select name=\"tipo_busca\" class='box'>";
                                echo "<option value=\"especialidade\" ".($tipo_busca == "especialidade" ? "selected" : "").">Especialidade</option>";
                                echo "<option value=\"paciente\" ".($tipo_busca == "paciente" ? "selected" : "").">Paciente</option>";
                                echo "<option value=\"medico\" ".($tipo_busca == "medico" ? "selected" : "").">M&eacute;dico</option>";
                                echo "<option value=\"unidade\" ".($tipo_busca == "unidade" ? "selected" : "").">Unidade</option>";
                            echo "</select>";
                        echo "</td>";
                        echo "<td>";
                            echo ChmodBtn($id_login,'procurar','cad_lista_espera.php');
                        echo "</td>";
                    echo "</form>";
                    echo "<td width=107>";
                            echo "<a href='logoff.php?id_login=$id_login' target='_parent'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/sair.gif border=0></a>";
                    echo "</td>";
                echo "</tr>";
            echo "</table>";
        echo "</fieldset>";
        echo "<fieldset>";
                if($tipo_busca == "unidade")
                {
                    /*$sql = "select e.lie_codigo, e.lie_data_cad, a.usu_codigo, a.usu_nome, b.uni_codigo, b.uni_desc, c.esp_codigo,
                            c.esp_nome, d.med_codigo, d.med_nome
                            from usuario a, unidade b, especialidade c,
                            medico d, lista_espera e
                            where a.usu_codigo = e.usu_codigo
                            and b.uni_codigo = e.uni_codigo
                            and c.esp_codigo = e.esp_codigo
                            and d.med_codigo = e.med_codigo
                            and b.uni_desc like upper('%$palavra_chave%')
                            and (e.lie_status <> 'D' or e.lie_status is null) and e.lie_data_age is null
                            order by b.uni_desc, c.esp_nome, d.med_nome, e.lie_data_cad, a.usu_nome";*/
                    $sql = "(select e.lie_codigo, e.lie_data_cad, a.usu_codigo, a.usu_nome, b.uni_codigo, b.uni_desc,
                    c.esp_codigo, c.esp_nome, d.med_codigo, d.med_nome, e.agt_codigo
                    from 
                    lista_espera e
                    
                    inner join usuario AS a ON a.usu_codigo = e.usu_codigo
                    inner join unidade AS b ON b.uni_codigo = e.uni_codigo
                    inner join especialidade AS c ON c.esp_codigo = e.esp_codigo
                    inner join medico AS d ON d.med_codigo = e.med_codigo
                    
                    where 
                    b.uni_desc like upper('%$palavra_chave%')
                    and (e.lie_status <> 'D' or e.lie_status is null)
                    and e.lie_data_age is null
                    order by b.uni_desc, c.esp_nome, d.med_nome, e.lie_data_cad, a.usu_nome)
                    
                    union
                    
                    (select e.lie_codigo, e.lie_data_cad, a.usu_codigo, a.usu_nome, b.uni_codigo, b.uni_desc, c.esp_codigo,
                    c.esp_nome, 0, 'SEM M&Eacute;DICO', e.agt_codigo
                    from 
                    lista_espera e
                    
                    inner join usuario AS a ON a.usu_codigo = e.usu_codigo
                    inner join unidade AS b ON b.uni_codigo = e.uni_codigo
                    inner join especialidade AS c ON c.esp_codigo = e.esp_codigo
                    
                    where 
                    e.med_codigo is null 
                    and b.uni_desc like upper('%$palavra_chave%')
                    and (e.lie_status <> 'D' or e.lie_status is null)
                    and e.lie_data_age is null
                    order by b.uni_desc, c.esp_nome, e.lie_data_cad, a.usu_nome)
                    
                    order by 6, 8, 10, 2, 4";
                }
                else if($tipo_busca == "especialidade")
                {
                    $sql = "(select e.lie_codigo, e.lie_data_cad, a.usu_codigo, a.usu_nome, b.uni_codigo, b.uni_desc,
                    c.esp_codigo, c.esp_nome, d.med_codigo, d.med_nome, e.agt_codigo
                    from 
                    lista_espera e
                    
                    inner join usuario AS a ON a.usu_codigo = e.usu_codigo
                    inner join unidade AS b ON b.uni_codigo = e.uni_codigo
                    inner join especialidade AS c ON c.esp_codigo = e.esp_codigo
                    inner join medico AS d ON d.med_codigo = e.med_codigo
                    
                    where 
                    c.esp_nome like upper('%$palavra_chave%')
                    and (e.lie_status <> 'D' or e.lie_status is null)
                    and e.lie_data_age is null
                    order by b.uni_desc, c.esp_nome, d.med_nome, e.lie_data_cad, a.usu_nome)
                    
                    union
                    
                    (select e.lie_codigo, e.lie_data_cad, a.usu_codigo, a.usu_nome, b.uni_codigo, b.uni_desc, c.esp_codigo,
                    c.esp_nome, 0, 'SEM M&Eacute;DICO', e.agt_codigo
                    from 
                    lista_espera e
                    
                    inner join usuario AS a ON a.usu_codigo = e.usu_codigo
                    inner join unidade AS b ON b.uni_codigo = e.uni_codigo
                    inner join especialidade AS c ON c.esp_codigo = e.esp_codigo
                    
                    where 
                    e.med_codigo is null 
                    and c.esp_nome like upper('%$palavra_chave%')
                    and (e.lie_status <> 'D' or e.lie_status is null)
                    and e.lie_data_age is null
                    order by b.uni_desc, c.esp_nome, e.lie_data_cad, a.usu_nome)
                    
                    order by 6, 8, 10, 2, 4";
                }
                else if($tipo_busca == "medico")
                {
                    $sql = "select e.lie_codigo, e.lie_data_cad, a.usu_codigo, a.usu_nome, b.uni_codigo, b.uni_desc,
                    c.esp_codigo, c.esp_nome, d.med_codigo, d.med_nome, e.agt_codigo
                    from 
                    lista_espera e
                    
                    inner join usuario AS a ON a.usu_codigo = e.usu_codigo
                    inner join unidade AS b ON b.uni_codigo = e.uni_codigo
                    inner join especialidade AS c ON c.esp_codigo = e.esp_codigo
                    inner join medico AS d ON d.med_codigo = e.med_codigo
                    
                    where 
                    d.med_nome like upper('%$palavra_chave%')
                    and (e.lie_status <> 'D' or e.lie_status is null)
                    and e.lie_data_age is null
                    order by b.uni_desc, c.esp_nome, d.med_nome, e.lie_data_cad, a.usu_nome";
                    
                }
                else if($tipo_busca == "paciente")
                {
                    $sql = "(select e.lie_codigo, e.lie_data_cad, a.usu_codigo, a.usu_nome, b.uni_codigo, b.uni_desc,
                    c.esp_codigo, c.esp_nome, d.med_codigo, d.med_nome, e.agt_codigo
                    from 
                    lista_espera e
                    
                    inner join usuario AS a ON a.usu_codigo = e.usu_codigo
                    inner join unidade AS b ON b.uni_codigo = e.uni_codigo
                    inner join especialidade AS c ON c.esp_codigo = e.esp_codigo
                    inner join medico AS d ON d.med_codigo = e.med_codigo
                    
                    where 
                    a.usu_nome like upper('%$palavra_chave%')
                    and (e.lie_status <> 'D' or e.lie_status is null)
                    and e.lie_data_age is null
                    order by b.uni_desc, c.esp_nome, d.med_nome, e.lie_data_cad, a.usu_nome)
                    
                    union
                    
                    (select e.lie_codigo, e.lie_data_cad, a.usu_codigo, a.usu_nome, b.uni_codigo, b.uni_desc, c.esp_codigo,
                    c.esp_nome, 0, 'SEM M&Eacute;DICO', e.agt_codigo
                    from 
                    lista_espera e
                    
                    inner join usuario AS a ON a.usu_codigo = e.usu_codigo
                    inner join unidade AS b ON b.uni_codigo = e.uni_codigo
                    inner join especialidade AS c ON c.esp_codigo = e.esp_codigo
                    
                    where 
                    e.med_codigo is null 
                    and a.usu_nome like upper('%$palavra_chave%')
                    and (e.lie_status <> 'D' or e.lie_status is null)
                    and e.lie_data_age is null
                    order by b.uni_desc, c.esp_nome, e.lie_data_cad, a.usu_nome)
                    
                    order by 6, 8, 10, 2, 4";
                }
				
                if(chmodbtn($id_login, "listar_if", "cad_lista_espera.php"))
				{
					$exec_sql = db_query($sql);
				}
				
                echo "<legend>Listando ".pg_num_rows($exec_sql)." registros</legend>";
                $auxUni = "";
                $auxEsp = "";
                $auxMed = "";
                $i = 0;
                $n = 0;
                $x = 0;          
             
                
                echo "<table class='lista' width=100% cellpading=\"0\" cellspacing=\"0\">";
                while($row = pg_fetch_array($exec_sql))
                {
                    // Implementado para buscar a posicao na lista do médico  {Marcos Ramos}                   
                    $contador = 0;
                    $posicao = 0;
                    $sub = "select usu_codigo
                                from lista_espera
                                where uni_codigo = $row[uni_codigo]
                                and esp_codigo = $row[esp_codigo]
                                and med_codigo = $row[med_codigo]
                                and (lie_status <> 'D' or lie_status is null)
                                and lie_data_age is null
                                order by lie_codigo";
                    $st = db_query($sub);
                    while ($reg = pg_fetch_array($st))                    
                    {
                        $contador+=1;
                        if ($reg[usu_codigo] == $row[usu_codigo])
                        {
                            $posicao = $contador;
                        }                      
                    }
                    // fim Implementacao para buscar a posicao na lista do medico (Marcos Ramos}                    
                    if($row[uni_desc] != $auxUni)
                    {
                        if($i != 0)
                        {
                            echo "<tr>";
                                echo "<td>";
                                    echo "&nbsp;";
                                echo "</td>";
                                echo "<td>";
                                    echo "&nbsp;";
                                echo "</td>";
                                echo "<th colspan=\"5\" align=\"left\">";
                                    echo $i." Registros";
                                echo "</th>";
                            echo "</tr>";
                        }
                        if($x != 0)
                        {
                            echo "<tr>";
                                echo "<td>";
                                    echo "&nbsp;";
                                echo "</td>";
                                echo "<th colspan=\"6\" align=\"left\">";
                                    echo $x." Registros";
                                echo "</th>";
                            echo "</tr>";
                        }
                        if($n != 0)
                        {
                            echo "<tr>";
                                echo "<th colspan=\"7\" align=\"left\">";
                                    echo "Total de registros: ".$n;
                                echo "</th>";
                            echo "</tr>";
                        }
                        echo "<tr>";
                            echo "<th class=\"td\" colspan=\"7\" align=\"left\">";
                                echo $row[uni_desc];
                            echo "</th>";
                        echo "</tr>";
                        $auxUni = "";
                        $auxEsp = "";
                        $auxMed = "";
                        $i = 0;
                        $n = 0;
                        $x = 0;
                    }
                    if($row[esp_nome] != $auxEsp)
                    {
                        if($i != 0)
                        {
                            echo "<tr>";
                                echo "<td>";
                                    echo "&nbsp;";
                                echo "</td>";
                                echo "<td>";
                                    echo "&nbsp;";
                                echo "</td>";
                                echo "<th colspan=\"5\" align=\"left\">";
                                    echo $i." Registros";
                                echo "</th>";
                            echo "</tr>";
                        }
                        if($x != 0)
                        {
                            echo "<tr>";
                                echo "<td>";
                                    echo "&nbsp;";
                                echo "</td>";
                                echo "<th colspan=\"6\" align=\"left\">";
                                    echo $x." Registros";
                                echo "</th>";
                            echo "</tr>";
                        }
                        echo "<tr>";
                            echo "<td>";
                                echo "&nbsp;";
                            echo "</td>";
                            echo "<td class=\"td\" colspan=\"6\">";
                                echo $row[esp_nome];
                            echo "</td>";
                        echo "</tr>";
                        $i = 0;
                        $x = 0;
                    }
                    if($row[med_nome] != $auxMed)
                    {
                        $pos = 1;
                        if($i != 0)
                        {
                            echo "<tr>";
                                echo "<td>";
                                    echo "&nbsp;";
                                echo "</td>";
                                echo "<td>";
                                    echo "&nbsp;";
                                echo "</td>";
                                echo "<th colspan=\"5\" align=\"left\">";
                                    echo $i." Registros";
                                echo "</th>";
                            echo "</tr>";
                        }
                        echo "<tr>";
                            echo "<td>";
                                echo "&nbsp;";
                            echo "</td>";
                            echo "<td>";
                                echo "&nbsp;";
                            echo "</td>";
                            echo "<td class=\"td\" colspan=\"5\">";
                                echo $row[med_nome];
                            echo "</td>";
                        echo "</tr>";
                        $i = 0;
                    } else {
                        $pos++;
                    }
                    echo "<tr>";
                        echo "<td>";
                            echo "&nbsp;";
                        echo "</td>";
                        echo "<td>";
                            echo "&nbsp;";
                        echo "</td>";
                        echo "<td>";
                            echo "&nbsp;";
                        echo "</td>";
                        echo "<td>";
                            echo "<font color='red'>".$posicao."&ordm;</a>";
//                            echo "<font color='red'>".$pos."</a>";                            
                        echo "</td>";
                        echo "<td class=\"td\" width=\"135px\">";
                            //echo $row[lie_data_cad];
                            $data = explode(" ", $row[lie_data_cad]);
                            list($dia,$mes,$ano,$hr) = split ('[-/ ]',$row[lie_data_cad]);
                            //$dat = explode([-/], $data[0]);
                            /*
                            echo "dia".$dia."<br>";
                            echo "mes".$mes."<br>";
                            echo "ano".$ano."<br>";
                            echo "hora".$hr."<br>";
                            */
                            $da = sprintf("%02d/%02d/%04d",$dia,$mes,$ano);
                            //$da = $dat[2]."/".$dat[1]."/".$dat[0];
                            //echo $da." - ".substr($data[1], 0, 8);
                            echo $da." - ".substr($hr,0,8);                            
                        echo "</td>";
//                        echo "<td class=\"td\">$row[lie_codigo]&#176; - ";
                        echo "<td class=\"td\">$row[usu_nome]";
                        echo "</td>";
                        echo "<td class=\"td\" width=\"180px\">";
                                if( !empty($row[agt_codigo]) )
                                {
                                    $row_resp = db_get("SELECT agt_descricao FROM agente
                                                        WHERE agt_codigo = ".$row[agt_codigo]);
                                    echo $row_resp;
                                }
                                else
                                {
                                    echo "&nbsp;";
                                }
                            echo "</td>";
                        echo "<td class='td' width=\"60px\">";
                            echo ChmodBtn($id_login,'editar','cad_lista_espera.php?acao=edit&lie_codigo='.$row[lie_codigo]);
                        echo "</td>";
                        echo "<td class='td' width=\"60px\">";
                            echo ChmodBtn($id_login,'apagar','cad_lista_espera.php?acao=del&lie_codigo='.$row[lie_codigo]);
                        echo "</td>";
                    echo "</tr>";
                    $auxUni = $row[uni_desc];
                    $auxEsp = $row[esp_nome];
                    $auxMed = $row[med_nome];
                    $i++;
                    $n++;
                    $x++;
                }
                if($i != 0)
                {
                    echo "<tr>";
                        echo "<td>";
                            echo "&nbsp;";
                        echo "</td>";
                        echo "<td>";
                            echo "&nbsp;";
                        echo "</td>";
                        echo "<th colspan=\"5\" align=\"left\">";
                            echo $i." Registros";
                        echo "</th>";
                    echo "</tr>";
                }
                if($x != 0)
                {
                    echo "<tr>";
                        echo "<td>";
                            echo "&nbsp;";
                        echo "</td>";
                        echo "<th colspan=\"6\" align=\"left\">";
                            echo $x." Registros";
                        echo "</th>";
                    echo "</tr>";
                }
                if($n != 0)
                {
                    echo "<tr>";
                        echo "<th colspan=\"5\" align=\"left\">";
                            echo "Total de registros: ".$n;
                        echo "</th>";
                    echo "</tr>";
                }
                echo "<tr>";
                    echo "<td colspan=\"7\"><b>";
                        echo "Total geral: ".pg_num_rows($exec_sql);
                    echo "</b></td>";
                echo "</tr>";
            echo "</table>";
        echo "</fieldset>";  
    }
    else if($acao == "del")
    {
        //$sql = "delete from lista_espera where lie_codigo = $_GET[lie_codigo]";
        $sql = "update lista_espera set lie_status = 'D', usr_codigo_alt = $_GET[id_login], lie_data_alt = now() where lie_codigo = $_GET[lie_codigo]";
        
        $exec_sql = db_query($sql);
        msg($id_login, $acao, $exec_sql);
    }
    else if($acao == "edit")
    {
        
        $select = "select * from lista_espera where lie_codigo = $_GET[lie_codigo]";
        $exec_select = db_query($select);
        $linha = pg_fetch_array($exec_select);		
        echo "<fieldset>\n";
            echo "<legend>Op&ccedil;&otilde;es</legend>\n";
            echo "<table>\n";
                echo "<tr>\n";
                    echo "<td width='75px'>\n";
                        echo "<a href='cad_lista_espera.php?id_login=$id_login&acao=listar'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>\n";
                    echo "</td>\n";
                echo "</tr>\n";
            echo "</table>\n";
        echo "</fieldset>\n";
        echo "<form name='lista_espera' method='post' action='$PHP_SELF' onsubmit='return validar();'>\n";
            echo "<input type='hidden' name='id_login' value='$id_login'>\n";
            echo "<input type='hidden' name='acao' value='add'>\n";
            echo "<input type='hidden' name='action' value='edit'>\n";
            echo "<input type='hidden' name='lie_codigo' value='$_GET[lie_codigo]'>\n";
            echo "<table width='760' align='center' cellspacing='0' cellpadding='0' border='0'>\n";
                echo "<tr>\n";
                    echo "<td>\n";
                        echo "<fieldset>\n";
                            echo "<legend>Prestador</legend>\n";
                            echo "<table width='100%' cellspacing='0' cellpadding='4' border='0'>\n";
                                echo "<tr>\n";
                                    echo "<td align='right' width='115px'>Unidade de Sa&uacute;de</td>\n";
                                    echo "<td>\n";
                                        echo "<select name='uni_codigo' id='uni_codigo' class='boxa' onChange='buscarMedicos(0)'>\n";
                                            echo "<option value=''>....</option>\n";
                                            $sql = db_query("select *from unidade order by uni_desc");
                                            while($uni=pg_fetch_array($sql))
                                            {
                                                if($uni[uni_codigo] == $linha[uni_codigo])
                                                {
                                                    echo "<option value='$uni[uni_codigo]' selected>$uni[uni_desc]</option>\n";
                                                }
                                                else
                                                {
                                                    echo "<option value='$uni[uni_codigo]'>$uni[uni_desc]</option>\n";
                                                }
                                            }
                                        echo "</select>\n";
                                    echo "</td>\n";
                                echo "</tr>\n";
                                echo "<tr>\n";
                                    echo "<td align=right>Atividade prof.</td>\n";
                                    echo "<td>\n";
                                        echo "<select name='esp_codigo' id='esp_codigo' class='boxa' onChange='buscarMedicos(0)'>\n";
                                            echo "<option value=''>....</option>\n";
                                            $sql = db_query("select *from especialidade order by esp_nome");
                                            while($esp=pg_fetch_array($sql))
                                            {
                                                if($esp[esp_codigo] == $linha[esp_codigo])
                                                {
                                                    echo "<option value='$esp[esp_codigo]' selected>$esp[esp_nome]</option>\n";
                                                }
                                                else
                                                {
                                                    echo "<option value='$esp[esp_codigo]'>$esp[esp_nome]</option>\n";
                                                }
                                            }
                                        echo "</select>\n";
                                    echo "</td>\n";
                                echo "</tr>\n";
                                echo "<tr>\n";
                                    echo "<td align='right'>Profissional</td>\n";
                                    echo "<td>\n";
                                        echo "<input type='hidden' name='medico_codigo' id='medico_codigo' value='$linha[med_codigo]'>\n";
                                        echo "<script>buscarMedicos(1)</script>\n";
                                        echo "<select name='med_codigo' id='med_codigo' class='boxa'  >\n";
                                            echo "<option value=''>....</option>\n";
                                        echo "</select>\n";
                                    echo "</td>\n";
                                echo "</tr>\n";
                            echo "</table>\n";
                        echo "</fieldset>\n";
                    echo "</td>\n";
                echo "</tr>\n";
            echo "</table>\n";
//atualizacao 20/06/07
            echo "
            <fieldset>
            <legend>Agente de sa&uacute;de</legend>
            <table cellpadding='4'>
            <tr>
                <td width='113' align=right>Agente de sa&uacute;de</td>
                <td>
                    <select id='agt_codigo' name='agt_codigo' class='boxa' onchange='at_agente()'>
                        <option value=''>....</option>";
                        
                            $id_login = intval($id_login);
                            
                            // sqls especifico para APUCARANA
                            $usr_uni_codigo = db_get("SELECT uni_codigo FROM usuarios
                                                        WHERE usr_codigo = $id_login" );
                             
                            $stmt = "SELECT agt_codigo, agt_descricao, agt_numero, agt_responsavel ".
                                    "FROM agente ".
                                    ( empty($usr_uni_codigo) ? '' :
                                    "WHERE uni_codigo = $usr_uni_codigo OR ".
                                    "agt_codigo IN (384931,393519) " ).
                                    "ORDER BY agt_descricao";
                            $qry = db_query($stmt);
                            
                            while( $agt=pg_fetch_array($qry) )
                            {
                                echo "\n\t\t\t<option value='{$agt[0]}' ".($agt[0]==$linha[agt_codigo] ? 'selected' : '').">{$agt[1]}</option>";
                                if( $agt[0]==$linha[agt_codigo] )
                                {
                                    $row_agt_numero = $agt[agt_numero];
                                    $row_agt_responsavel = $agt[agt_responsavel];
                                }
                            }
                            
                    echo "
                    </select>
                </td>
                <td><input type='text' id='agt_numero' name='agt_numero' class='boxl' size='15' readonly='readonly' value='".$row_agt_numero."'/></td>
                <td><input type='text' id='agt_responsavel' name='agt_responsavel' class='boxl' size='50' readonly='readonly' value='".$row_agt_responsavel."'/></td>
            </tr>
            </table>
            </fieldset>";
//fim (20/06/07)
            echo "<fieldset>\n";
                echo "<legend>Dados do Paciente</legend>\n";
                echo "<table width='100%' cellspacing='0' cellpadding='1' border='0'>\n";
                    $busca = "select usu_codigo, usu_nome, to_char(usu_datanasc, 'dd/mm/yyyy') as usu_datanasc, usu_mae, usu_end_cidade from usuario where usu_codigo = $linha[usu_codigo]";
                    $exec_busca = db_query($busca);
                    $row = pg_fetch_array($exec_busca);
                    echo "<tr>\n";
                        echo "<td width='110'>Numero do Paciente</td>\n";
                        echo "<td width='40'>\n";
                            echo "<input type='text' name='pac_codigo' id='pac_codigo' class='boxl' size='10' readonly value='$linha[usu_codigo]'>\n";
                            echo "</td>\n";
                        echo "<td width='40'>Paciente</td>\n";
                        echo "<td>\n";
                            echo "<input type='text' name='pac_nome' id='pac_nome' class='boxl' size='60' readonly value='$row[usu_nome]'>\n";
                        echo "</td>\n";
                        echo "<td>Nascimento</td>\n";
                        echo "<td width='250'>\n";
                            echo "<input type='text' name='pac_nascimento' id='pac_nascimento' class='boxl' size='15' readonly value='$row[usu_datanasc]'>\n";
                        echo "</td>\n";
                    echo "</tr>\n";
                    echo "<tr>\n";
                        echo "<td width='70'>M&atilde;e</td>\n";
                        echo "<td width='100' colspan='3'>\n";
                            echo "<input type='text' name='pac_mae' id='pac_mae' class='boxl' size='50' readonly value='$row[usu_mae]'>\n";
                        echo "</td>\n";
                        echo "<td width=40>Cidade</td>\n";
                        echo "<td width=60>\n";
                            echo "<input type='text' name='pac_cidade' id='pac_cidade' class='boxl' size='23' readonly value='$row[usu_end_cidade]'>\n";
                        echo "</td>\n";
                    echo "</tr>\n";
                    echo "<tr>\n";
                        echo "<td>\n";
                            echo "<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg'>\n";
                        echo "</td>\n";
                    echo "</tr>\n";
                echo "</table>\n";
            echo "</fieldset>\n";
        echo "</form>\n";
    }
    else if($acao == "add")
    {
        /*echo "<pre>";
                print_r($_REQUEST);
        echo "</pre>";*/
        if($action == "edit")
        {
            $sql = "UPDATE lista_espera SET
                    med_codigo = ".(empty($med_codigo) ? 'null' : $_POST[med_codigo]).",
                    esp_codigo = '{$_POST[esp_codigo]}',
                    lie_data_alt = current_timestamp,
                    usr_codigo_alt = '{$_POST[id_login]}',
                    agt_codigo = ".(empty($agt_codigo) ? 'null' : $_POST[agt_codigo])."
                    WHERE lie_codigo = '{$_POST[lie_codigo]}'";
        }
        else
        {
            $sql = "SELECT *
                    FROM lista_espera
                    WHERE usu_codigo = {$_POST[pac_codigo]}
                    AND esp_codigo = {$_POST[esp_codigo]}
                    AND lie_data_age is null
					AND lie_status is null or lie_status <> 'D'";
            $exec_sql = db_query($sql);
            $linha = pg_fetch_array($exec_sql);
            
            if(pg_num_rows($exec_sql) == 0)
            {
                $sql = "INSERT INTO lista_espera
                        (usu_codigo, med_codigo, esp_codigo, uni_codigo, lie_data_cad, usr_codigo_cad,
                        agt_codigo)
                        VALUES
                        ('{$_POST[pac_codigo]}', ".(empty($med_codigo) ? 'null' :$_POST[med_codigo]).
                        ", '{$_POST[esp_codigo]}', '{$_POST[uni_codigo]}', current_timestamp,
                        '{$_POST[id_login]}', ".(empty($agt_codigo) ? 'null' : $_POST[agt_codigo]).")";
            }
            else
            {
               //Alterado para que exiba uma mensagem caso ja exista uma agenda para o paciente e para a especialidade.
	       //e nao altere o registro.
		   

	       
	//       $sql = "UPDATE lista_espera SET
        //                med_codigo = ".(empty($med_codigo) ? 'null' : $_POST[med_codigo]).",
        //                esp_codigo = '{$_POST[esp_codigo]}', lie_data_alt = current_timestamp,
        //                usr_codigo_alt = '{$_POST[id_login]}',
        //                agt_codigo = ".(empty($agt_codigo) ? 'null' : $_POST[agt_codigo])."
        //                WHERE lie_codigo = '{$linha[lie_codigo]}'";
        //        $exec_sql = db_query($sql);
                echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
                <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                 <tr bgcolor=f9f9f9><td align=center>";
                //if(pg_affected_rows($exec_sql) > 0)
                //{
                    //echo "<font size=2 color=green><b>REGISTRO $aux COM SUCESSO</b></font>";
                //} else {
                    echo "<font size=2 color=green><b>PACIENTE J&Aacute; EXISTE NA LISTA DE ESPERA</b></font>";
                //}
                echo "</td></tr>
                </table><br>";
                echo "<script>setInterval(\"location.href='cad_lista_espera.php?acao=form_add&id_login=$id_login&med_codigo=$_POST[med_codigo]&uni_codigo=$_POST[uni_codigo]&esp_codigo=$_POST[esp_codigo]'\",4000)</script>";
                exit();
            }
        }
        
        $exec_sql = db_query($sql);
        $aux = ($acao == "add" ? "ADICIONADO" : "ALTERADO");
        $aux2 = ($acao == "add" ? "ADICIONAR" : "ALTERAR");
        echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
            <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
             <tr bgcolor=f9f9f9><td align=center>";
        if(pg_affected_rows($exec_sql) > 0)
        {
            echo "<font size=2 color=green><b>REGISTRO $aux COM SUCESSO</b></font>";
        }
        else
        {
            echo "<font size=2 color=green><b>ERRO AO $aux2 REGISTRO</b></font>";
        }
        echo "</td></tr>
            </table><br>";
        /*if($action)
        {
                msg($id_login, "edit", $exec_sql);
        } else {
                pg_num_rows($exec_sql) == 0 ? msg($id_login, $acao, $exec_sql) : msg($id_login, "edit", $exec_sql);
        }*/
        echo "<script>setInterval(\"location.href='cad_lista_espera.php?acao=form_add&id_login=$id_login&med_codigo=$_POST[med_codigo]&uni_codigo=$_POST[uni_codigo]&esp_codigo=$_POST[esp_codigo]'\",4000)</script>";
    }
    else if($acao == "form_add")
    {
        echo "<fieldset>";
            echo "<legend>Op&ccedil;&otilde;es</legend>";
            echo "<table>";
                echo "<tr>";
                    echo "<td width='75px'>";
                        echo "<a href='cad_lista_espera.php?id_login=$id_login&acao=listar'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>";
                    echo "</td>";
                echo "</tr>";
            echo "</table>";
        echo "</fieldset>";
        echo "<form name='lista_espera' method='post' action='$PHP_SELF' onsubmit='return validar();'>";
            echo "<input type='hidden' name='id_login' value='$id_login'>";
            echo "<input type='hidden' name='acao' value='add'>";
            echo "<table width='760' align='center' cellspacing='0' cellpadding='0' border='0'>";
                echo "<tr>";
                    echo "<td>";
                        echo "<fieldset>";
                            echo "<legend>Prestador</legend>";
                            echo "<table width='100%' cellspacing='0' cellpadding='4' border='0'>";
                                echo "<tr>";
                                    echo "<td align='right' width='115px'>Unidade de Sa&uacute;de</td>";
                                    echo "<td>";
                                        echo "<select name='uni_codigo' id='uni_codigo' class='boxa' onChange='buscarMedicos(0)'>";
                                            echo "<option value=''>....</option>";
                                            $sql = db_query("select *from unidade order by uni_desc");
                                            while($uni=pg_fetch_array($sql))
                                            {
                                                if($_GET[uni_codigo] == $uni[uni_codigo])
                                                {
                                                    echo "<option value='$uni[uni_codigo]' selected>$uni[uni_desc]</option>";
                                                }
                                                else
                                                {
                                                    echo "<option value='$uni[uni_codigo]'>$uni[uni_desc]</option>";
                                                }
                                            }
                                        echo "</select>";
                                    echo "</td>";
                                echo "</tr>";
                                echo "<tr>";
                                    echo "<td align=right>Atividade prof.</td>";
                                    echo "<td>";
                                        echo "<select name='esp_codigo' id='esp_codigo' class='boxa' onChange='buscarMedicos(0)'>";
                                            echo "<option value=''>....</option>";
                                            $sql = db_query("select *from especialidade order by esp_nome");
                                            while($esp=pg_fetch_array($sql))
                                            {
                                                if($_GET[esp_codigo] == $esp[esp_codigo])
                                                {
                                                    echo "<option value='$esp[esp_codigo]' selected>$esp[esp_nome]</option>";
                                                }
                                                else
                                                {
                                                    echo "<option value='$esp[esp_codigo]'>$esp[esp_nome]</option>";
                                                }
                                            }
                                        echo "</select>";
                                    echo "</td>";
                                echo "</tr>";
                                echo "<tr>";
                                    echo "<td align='right'>Profissional</td>";
                                    echo "<td>";
                                        echo "<select name='med_codigo' id='med_codigo' class='boxa'  >";
                                            echo "<option value=''>....</option>";
                                            echo "<input type='hidden' name='medico_codigo' id='medico_codigo' value='$_GET[med_codigo]'>";
                                            echo "<script>buscarMedicos(1);</script>";
                                        echo "</select>";
                                    echo "</td>";
                                echo "</tr>";
                            echo "</table>";
                        echo "</fieldset>";
                    echo "</td>";
                echo "</tr>";
            echo "</table>";
//atualizacao 20/06/07
            echo "
            <fieldset>
            <legend>Agente de sa&uacute;de</legend>
            <table cellpadding='4'>
            <tr>
                <td width='113' align=right>Agente de sa&uacute;de</td>
                <td>
                    <select id='agt_codigo' name='agt_codigo' class='boxa' onchange='at_agente()'>
                        <option value=''>....</option>";
                        $id_login = intval($id_login);
                        // sqls especifico para APUCARANA
                        $usr_uni_codigo = db_get("SELECT uni_codigo FROM usuarios
                                                 WHERE usr_codigo = $id_login" );
                        $stmt = "SELECT agt_codigo, agt_descricao ".
                                "FROM agente ".
                                ( empty($usr_uni_codigo) ? '' :
                                "WHERE uni_codigo = $usr_uni_codigo OR ".
                                "agt_codigo IN (384931,393519) " ).
                                "ORDER BY agt_descricao";
                        $qry = db_query($stmt);
                        
                        while($agt=pg_fetch_array($qry))
                        {
                          echo "\n\t\t\t<option value='{$agt[0]}'>{$agt[1]}</option>";
                        }
                    echo "
                    </select>
                </td>
                <td><input type='text' id='agt_numero' name='agt_numero' class='boxl' size='15' readonly='readonly'/></td>
                <td><input type='text' id='agt_responsavel' name='agt_responsavel' class='boxl' size='50' readonly='readonly'/></td>
            </tr>
            </table>
            </fieldset>";
//fim (20/06/07)
            echo "<fieldset>";
                echo "<legend>Dados do Paciente</legend>";
                echo "<table width='100%' cellspacing='0' cellpadding='1' border='0'>";
                    echo "<tr>";
                        echo "<td width='110'>Prontu&aacute;rio</td>";
                        echo "<td width='40'>";
                            //echo "<input type='text' name='pac_codigo' id='pac_codigo' class='boxl' size='10' readonly>";
                            echo "<input type=hidden name='pac_codigo' id='pac_codigo' class=boxl size=10 onchange='buscar_dados_paciente();' readonly>";
                            echo "<input type=text name='pac_prontuario' id='pac_prontuario' class=boxl size=10 onchange='buscar_dados_paciente(this.value);' onkeypress=\"if(event.keyCode == 13){return false;}\">";
                            echo "</td>";
                        echo "<td width='40'>Paciente</td>";
                        echo "<td>";
                            //echo "<input type='text' name='pac_nome' id='pac_nome' class='boxl' size='60' readonly>";
                            //echo "<a href='#' OnClick=\"window.open('paciente.php?id_login=$id_login&controle=1', null, 'height=460,width=800,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes');\">";
                            //echo "<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg' align='absmiddle' border='0'></a>";
                            echo "<input type=text name=pac_nome id=pac_nome class=boxl size=60 readonly><a href='#' OnClick='window.open(\"list_pacientes.php?id_login=$id_login\",null,\"height=460,width=600,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes\");'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg align=absmiddle border=0></a>";
                        //"
                        echo "</td>";
                        echo "<td>Nascimento</td>";
                        echo "<td width='250'>";
                                echo "<input type='text' name='pac_nascimento' id='pac_nascimento' class='boxl' size='15' readonly onfocus='buscar_prontuario();'>";
                        echo "</td>";
                    echo "</tr>";
                    echo "<tr>";
                        echo "<td width='70'>M&atilde;e</td>";
                        echo "<td width='100' colspan='3'>";
                            echo "<input type='text' name='pac_mae' id='pac_mae' class='boxl' size='50' readonly>";
                        echo "</td>";
                        echo "<td width=40>Cidade</td>";
                        echo "<td width=60>";
                            echo "<input type='text' name='pac_cidade' id='pac_cidade' class='boxl' size='23' readonly>";
                        echo "</td>";
                    echo "</tr>";
                    echo "<tr>";
                        echo "<td>";
                            echo "<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg'>";
                        echo "</td>";
                    echo "</tr>";
                echo "</table>";
            echo "</fieldset>";
        echo "</form>";
    }
?>
</body>
</html>
