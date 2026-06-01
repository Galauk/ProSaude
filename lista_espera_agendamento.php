<?php
    //------------------------------------------------------------------>
	// -> Inclusao principal para montagem do sistema
	//------------------------------------------------------------------>

	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	verauth($id_login);
    cabecario();
?>
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
<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<script language="JavaScript" type="text/javascript" src="ajax_motor.js"></script>
<script language="JavaScript" type="text/javascript" src="g_ajax.js"></script>
<script language="JavaScript" type="text/javascript" src="g_script.js"></script>
<script>
    function buscarMedicos()
    {
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
                d.options[d.options.length] = new Option(aux[1],aux[0]);
            }
        }
        atualizarGrid();
    }
    function atualizarGrid()
    {
        esp_codigo = document.getElementById('esp_codigo').value;
        med_codigo = document.getElementById("med_codigo").value;
        uni_codigo = document.getElementById("uni_codigo").value;
        tipoconsulta = document.getElementById("tipoconsulta").value;
        
        if(med_codigo != "")
        {
            url = "buscarVaga.php?esp_codigo="+esp_codigo+"&med_codigo="+med_codigo+"&uni_codigo="+uni_codigo+"&tipoconsulta="+tipoconsulta;
        } else {
            url = "buscarVaga.php?esp_codigo="+esp_codigo+"&uni_codigo="+uni_codigo+"&tipoconsulta="+tipoconsulta;
        }
        document.getElementById("grid").innerHTML = "Carregando...";        
        ajax_tudo(url, atualizar);
    }
    
    function atualizar( txt )
    {
        document.getElementById("grid").innerHTML = txt;
    }

    function atualizarGrid2(esp, med, uni)
    {
        url = "buscarVaga.php?esp_codigo="+esp+"&med_codigo="+med+"&uni_codigo="+uni;
        ajax_tudo(url, atualizar2);
    }
    
    function atualizar2( txt )
    {
        document.getElementById("grid").innerHTML = txt;
        url = "buscarEspera.php?esp_codigo="+esp+"&uni_codigo="+uni+"&med_codigo="+med+"&limit="+limit+"&controle=1";
        ajax_tudo(url, popularLista);
    }
    
    function agendarComMedico(limit, esp_codigo, uni_codigo, med_codigo)
    {
        url = "buscarEspera.php?controle=1&esp_codigo="+esp_codigo+"&uni_codigo="+uni_codigo+"&med_codigo="+med_codigo+"&limit="+limit;
        ajax_tudo(url, popularLista);
    }
    
    function popularLista( txt )
    {
        document.getElementById('agendar_conteudo').innerHTML = txt;
        mostra_janela('agendar');
    }
    
    function agendarSemMedico(lie_codigo, esp_codigo, uni_codigo)
    {
        url = "buscarEspera.php?controle=2&lie_codigo="+lie_codigo+"&esp_codigo="+esp_codigo+"&uni_codigo="+uni_codigo;
        ajax_tudo(url, popularListaMedico);
    }
    
    function popularListaMedico( txt )
    {
        document.getElementById("agendar_conteudo").innerHTML = txt;
        mostra_janela('agendar');
    }
    
    function abrirAgendar(lie_codigo)
    {
        id_login = document.getElementById('id_login').value;
        url = "buscarDadosLista.php?controle=1&lie_codigo="+lie_codigo+"&id_login="+id_login;
        ajax_tudo(url, popularCadAgendar);
    }
    
    function popularCadAgendar(txt)
    {
        document.getElementById("cadAgenda_conteudo").innerHTML = txt;
        //document.getElementById("cadAgenda").style.left = document.getElementById("agendar").style.left + 12 + "px";
        //document.getElementById("cadAgenda").style.top = document.getElementById("agendar").style.top + 30 + "px";
        mostra_janela("cadAgenda");
    }
    
    function abrirAgendar2(lie_codigo, med_codigo, tipoconsulta)
    {
        id_login = document.getElementById('id_login').value;
        url = "buscarDadosLista.php?controle=2&lie_codigo="+lie_codigo+"&id_login="+id_login+"&med_codigo="+med_codigo+"&tipoconsulta="+tipoconsulta;
        ajax_tudo(url, popularCadAgendar2);
    }
    
    function popularCadAgendar2(txt)
    {
        document.getElementById("cadAgenda_conteudo").innerHTML = txt;
        document.getElementById("cadAgenda").style.left = document.getElementById("agendar").style.left + 12 + "px";
        document.getElementById("cadAgenda").style.top = document.getElementById("agendar").style.top + 30 + "px";
        mostra_janela("cadAgenda");
    }
    
    var tipo = "";
    function agendar(t)
    {
        tipo = t;
        lie_codigo = document.getElementById('lie_codigo').value;
        id_login = document.getElementById('id_login').value;
        usu_codigo = document.getElementById('usu_codigo').value;
        uni_codigo = document.getElementById('uni_codigo').value;
        esp_codigo = document.getElementById('esp_codigo').value;
        med_codigo = document.getElementById('med_codigo').value;
        data_hora = document.getElementById('age_data').value;
        dat_hor = data_hora.split("*");
        age_data = dat_hor[0];
        age_hora_ini = dat_hor[1];
		age_item = dat_hor[2];
        agt_codigo =  document.getElementById('agt_codigo').value;
        if(age_data == "")
        {
            alert("Por favor escolha a data");
            document.getElementById('age_data').focus();
            return false;
        }
        if(agt_codigo == "")
        {
            alert("Por favor escolha o responsavel tecnico");
            document.getElementById('agt_codigo').focus();
            return false;
        }
        url = "salvaLista.php?usu_codigo="+usu_codigo+"&uni_codigo="+uni_codigo+"&esp_codigo="+esp_codigo+"&med_codigo="+esp_codigo+"&med_codigo="+med_codigo+"&age_data="+age_data+"&id_login="+id_login+"&agt_codigo="+agt_codigo+"&age_hora_ini="+age_hora_ini+"&lie_codigo="+lie_codigo+"&age_item="+age_item;
        ajax_tudo(url, retorno);
    }
    
    function retorno( txt )
    {
        texto = txt.split("-");
        if(texto[1] != "1")
        {
            window.open(texto[1],null,"height=500,width=750,status=yes,toolbar=no,menubar=no,location=no");
        }
        alert(texto[0]);
        
        esp = document.getElementById('esp_codigo').value;
        uni = document.getElementById('uni_codigo').value;
        med = document.getElementById('med_codigo').value;
        
        document.getElementById('usu_codigo').value = "";
        document.getElementById('uni_codigo').value = "";
        document.getElementById('esp_codigo').value = "";
        document.getElementById('med_codigo').value = "";
        document.getElementById('tipoconsulta').value = "";
        document.getElementById('age_data').value = "";
 
        if(tipo == 1)
        {
            //limit = document.getElementById('limit').value;
        }
        
        esconde_janela("cadAgenda");
        esconde_janela("agendar");
        
        if(tipo == 1)
        {
            //atualizarGrid2(esp, med, uni);
            atualizarGrid();
        } else if(tipo == 2) {
            atualizarGrid();
        }
    }
    
    function limpar()
    {
        return false;
    }
    
</script>
<fieldset><legend>AGE. LISTA DE ESPERA</legend>

<?php
    echo monta_janela("agendar", "LISTA DE ESPERA");
    echo monta_janela("cadAgenda", "AGENDAR");
    echo "<input type='hidden' name='id_login' id='id_login' value='$id_login'>";
    echo "<fieldset>";
        echo "<legend>Op&ccedil;&otilde;es</legend>";
        echo "<table cellspacing='1' cellpadding='1' border='0'>";
            echo "<tr>";
                /*echo "<td width='150px'>";
                    echo "<a href='#?id_login=$id_login&acao=listar'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>";
                echo "</td>";*/
                echo "<td width='120px'>";
                    echo "Unidade:";
                echo "</td>";
                echo "<td>";
                    echo "<select name='uni_codigo' id='uni_codigo' class='boxa' onChange='buscarMedicos()'>";
                        echo "<option value=''>....</option>";
                        $sql = pg_query("select *from unidade order by uni_desc");
                        while($uni=pg_fetch_array($sql))
                        {
                            echo "<option value='$uni[uni_codigo]'>$uni[uni_desc]</option>";
                        }
                    echo "</select>";
                echo "</td>";
            echo "</tr>";
            echo "<tr>";
                /*echo "<td>";
                    echo "&nbsp;";
                echo "</td>";*/
                echo "<td>";
                    echo "Especialidade:";
                echo "</td>";
                echo "<td>";
                    echo "<select name='esp_codigo' id='esp_codigo' class='boxa' onChange='buscarMedicos();'>";
                        echo "<option value=''>....</option>";
                        $sql = pg_query("select * from especialidade order by esp_nome");
                        while($esp=pg_fetch_array($sql))
                        {
                            echo "<option value='$esp[esp_codigo]'>$esp[esp_nome]</option>";
                        }
                    echo "</select>";
                echo "</td>";
            echo "</tr>";
            echo "<tr>";
                /*echo "<td>";
                    echo "&nbsp;";
                echo "</td>";*/
                echo "<td>";
                    echo "M&eacute;dico:";
                echo "</td>";
                echo "<td>";
                    //echo "<select name='med_codigo' id='med_codigo' class='boxa' onChange=\"atualizarGrid();\">";
                    echo "<select name='med_codigo' id='med_codigo' class='boxa'>";
                        echo "<option value=''>....</option>";
                    echo "</select>";
                echo "</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td>";
                    echo "Tipo Consulta:";
                echo "</td>";
                echo "<td>";
                    echo "<select name='tipoconsulta' id='tipoconsulta' class='boxa' onChange=\"atualizarGrid();\">";
                        echo "<option value=''>....</option>";
                        echo "<option value='CB'>CB</option>";
                        echo "<option value='ES'>ES</option>";
                    echo "</select>";
                echo "</td>";
            echo "</tr>";
        echo "</table>";
    echo "</fieldset>";
    echo "<fieldset>";
        echo "<legend>Resultado</legend>";
        echo "<div style=\"width:100%;height:335px;overflow:auto;\" id=\"grid\"></div>";
    echo "</fieldset>";
    
?>
</fieldset>
