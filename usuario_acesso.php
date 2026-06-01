<?
/**
 * alterações: Colocado restrições no acesso seguindo a tabela usuarios_acessos caso o usuario tenha restrição.
 */
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	verauth($id_login);
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
//------------------------------------------------------------------>
?>
<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<script language="JavaScript" type="text/javascript" src="ajax_motor.js"></script>
<script>
    function buscar_especialidade()
    {
        med_codigo = document.getElementById("med_codigo").value;
        d = document.getElementById('esp_codigo');
		d.innerHTML = "";
        url = "buscarEspecialidade.php?med_codigo="+med_codigo;
		ajax_tudo(url, popular_especialidade);
    }
    function popular_especialidade(txt)
    {
        d = document.getElementById('esp_codigo');
		d.options[0]=new Option("Todas","todos");
		r =txt;
		res = r.split(";");
		for(x = 0; x < res.length; x++)
		{
			aux = res[x].split("-");
			if(aux[1] != undefined)
			{
				d.options[d.options.length]=new Option(aux[1],aux[0]);
			}
		}
    }
    var esp_cod = "";
    function buscar_especialidade2(esp_codigo_selecionado)
    {
        esp_cod = esp_codigo_selecionado;
        med_codigo = document.getElementById("med_codigo").value;
        d = document.getElementById('esp_codigo');
		d.innerHTML = "";
        url = "buscarEspecialidade.php?med_codigo="+med_codigo;
		ajax_tudo(url, popular_especialidade2);
    }
    function popular_especialidade2(txt)
    {
        d = document.getElementById('esp_codigo');
		d.options[0]=new Option("Todas","todos");
		r =txt;
		res = r.split(";");
		for(x = 0; x < res.length; x++)
		{
			aux = res[x].split("-");
			if(aux[1] != undefined)
			{
                if(esp_cod == aux[0])
                {
                    d.options[d.options.length]=new Option(aux[1],aux[0], true);
                } else {
                    d.options[d.options.length]=new Option(aux[1],aux[0]);
                }
			}
		}
    }
</script>

<?
//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
	 reglog($id_login,"Entrando em USUARIO_ACESSO");
//------------------------------------------------------------------>

echo "<fieldset><legend>ACESSO POR USUÁRIO</legend>";

// if(empty($acao)) {
 if(empty($acao) OR $acao=='form_acesso') {

//
//-> Botoes
  echo "<fieldset>
	    <legend>Opções</legend>
	       <a href=".$_SESSION[linkroot].$_SESSION[modulo]."zf/usuarios/usuarios><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>
	       ".ChmodBtn($id_login,'adicionar','usuario_acesso.php?acao=form_add')."
	       
			<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
			  <tr>";
				if (chmodbtn($id_login,"procurar_if","usuario_acesso.php"))
                                {
                                    echo "<form method=post action=$PHP_SELF>";
                                }
                                echo "
					<input type=hidden name=acao value=busca>
					<input type=hidden name=id_login value=$id_login>
					<td width=30>Buscar:</td>
					<td width=120><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
					<td>".ChmodBtn($id_login,'procurar','usuario_acesso.php')."</td>
				
				</form>
				
			  </tr>
			</table>

	   </fieldset>
	  <br>";

//
//-> Listando
  
    if (chmodbtn($id_login,"listar_if","usuario_acesso.php"))
    {
        echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
               <tr>
                <td>
                 <fieldset>
                  <legend>Listando Últimas <b>15</b> Usuarios/Acessos Cadastrados</legend>
                   <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
                    <tr bgcolor=F9f9f9>
                      <td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Usuario</td>
                      <td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Unidade</td>
                      <td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Medico</td>
                      <td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Especialidade</td>
                      <td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";
      
         $sql=pg_query("select uunid_codigo, 
                         (select usr_nome from usuarios where usr_codigo = usuarios_acessos.usr_codigo) as nomeusuario,
                         (select uni_desc from unidade where uni_codigo = usuarios_acessos.uni_codigo) as nomeunid,
                         (select med_nome from medico where med_codigo = usuarios_acessos.med_codigo) as nomemedico,
                         (select esp_nome from especialidade where esp_codigo = usuarios_acessos.esp_codigo) as nomeespec
                        from usuarios_acessos
                        order by uunid_codigo desc limit 15");
           while($row=pg_fetch_array($sql)) {
             echo "<tr>
                     <td width=10 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[nomeusuario]</td>
                     <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[nomeunid]</td>
                     <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[nomemedico]</td>
                     <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[nomeespec]</td>
                     <!--<td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'editar','usuario_acesso.php?acao=form_edit&uunid_codigo='.$row[uunid_codigo])."</td>-->
                     <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'apagar','usuario_acesso.php?acao=del&uunid_codigo='.$row[uunid_codigo])."</td>
                   </tr>";
           }
    }           
	echo "</tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table>";
}


//------------------------------------------------------------------>
//-> Mostrando o resultado da busca
//------------------------------------------------------------------>

 if($acao=="busca") {
//
//-> Verificando Busca
 reglog($id_login,"Buscando em USUARIO_ACESSO: $palavra_chave ");

if(strlen($palavra_chave)<="3") {
        echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
                <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                 <tr bgcolor=f9f9f9>
                   <td align=center><font color=red size=2><b>ERRO</b></font><br>Busca com menos de <b>3</b> caracteres não permitida</td>
                 </tr>
                </table><br>";
        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='$PHP_SELF?id_login=$id_login'\", 2000);
              </SCRIPT>";
 exit;
}

//
//-> Subistituindo o + por porcentagem na busca
   $str = str_replace("+","%",$palavra_chave);
   $pos = strpos($palavra_chave,"+");
  if($pos=="0") {
     $v1=1;
  } else {
     $v1=2;
  }
//
//-> Botoes
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=200>".ChmodBtn($id_login,'adicionar','usuario_acesso.php?acao=form_add')."</td>
	       <form method=post action=$PHP_SELF>
		<input type=hidden name=acao value=busca>
	<input type=hidden name=id_login value=$id_login>
	       <td width=30>Buscar:</td>
	       <td width=120><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
	       <td>".ChmodBtn($id_login,'procurar','usuario_acesso.php')."</td></form>
	       <td width=107><a href='logoff.php?id_login=$id_login' target='_parent'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/sair.gif border=0></a></td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";

   $sql=pg_query("
                select a.uunid_codigo, b.usr_nome, c.uni_desc, d.med_nome, e.esp_nome
                from usuarios_acessos a, usuarios b, unidade c,
                medico d, especialidade e
                where a.usr_codigo = b.usr_codigo
                and a.uni_codigo = c.uni_codigo
                and a.med_codigo = d.med_codigo
                and a.esp_codigo = e.esp_codigo
                and b.usr_nome ilike '$palavra_chave%'
                order by b.usr_nome, c.uni_desc ");
    
$num=pg_num_rows($sql);
  if($num=="0") { $resp = "Nenhum Registro encontrado com \"$palavra_chave\""; }
  if($num=="1") { $resp = "Encontrado <b>$num</b> Registro com \"$palavra_chave\""; }
  if($num>"1") { $resp = "Encontrados <b>$num</b> Registros com \"$palavra_chave\""; }

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>$resp</legend>
	     <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
	      <tr bgcolor=F9f9f9>
		<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Usuario</td>
		<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Unidade</td>
		<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Medico</td>
		<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Especialidade</td>
		<td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";

     while($row=pg_fetch_array($sql)) {
       echo "<tr>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[usr_nome]</td>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[uni_desc]</td>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[med_nome]</td>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[esp_nome]</td>
	       <!--<td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'editar','usuario_acesso.php?acao=form_edit&uunid_codigo='.$row[uunid_codigo])."</td>-->
	       <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'apagar','usuario_acesso.php?acao=del&uunid_codigo='.$row[uunid_codigo])."</td>
	     </tr>";
     }
	echo "</tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table>";
}

//------------------------------------------------------------------>
//-> Formulario de Adicao de Conteudo
//------------------------------------------------------------------>

 if($acao=="form_add") {
	 reglog($id_login,"Formulario de ADICAO USUARIO_ACESSO");


//
//-> Abaixo sao os botoes de voltar / cadastro simples / cadastro completo

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções de Cadastro</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=79><a href=$PHP_SELF?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
	       <td>&nbsp;</td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";
//
//-> Este if esta apontando quando a acao for vazia ele vai mostrar o cadastro simples
//   no cadastro completo vc vai ter que passar a variavel acao para completo

 if(($type=="" OR $acao=="simples")) {
  echo "<form method=post action=$PHP_SELF>
	<input type=hidden name=acao value=add>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=type value=simples>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Cadastro de Acessos por Usuario</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
		<td width=70>Usuario:</td>
		<td>
		 <select name=usr_codigo class=box>";
	    //
	    //-> SQL da Usuario do Sistema
	    $query = pg_query("select usr_codigo, usr_nome from usuarios order by usr_nome ");
	      while($usuario=pg_fetch_array($query)) {
	       echo "<option value='$usuario[usr_codigo]'>$usuario[usr_nome]</option>";
	      }
	   echo "</select>
	        </td>
	      </tr>
	      <tr>
		<td width=70>Unidade:</td>
		<td>
		 <select name=uni_codigo class=box>";
	    //
	    //-> SQL da Unidade 
	    $query = pg_query("select uni_codigo, uni_desc from unidade order by uni_desc ");
	      while($unidade=pg_fetch_array($query)) {
	       echo "<option value='$unidade[uni_codigo]'>$unidade[uni_desc]</option>";
	      }
	   echo "</select>
	        </td>
	      </tr>
	      <tr>
		<td width=70>Medico:</td>
		<td>
		 <select name=med_codigo id=\"med_codigo\" class=box onchange=\"buscar_especialidade()\">";
	    //
	    //-> SQL da Usuario do Sistema
	    $query = pg_query("select med_codigo, med_nome from medico order by med_nome ");
	    echo "<option value='todos'>Todos</option>";
	      while($medico=pg_fetch_array($query)) {
	       echo "<option value='$medico[med_codigo]'>$medico[med_nome]</option>";
	      }
	   echo "</select>
	        </td>
	      </tr>
	      <tr>
		<td width=70>Especialidade:</td>
		<td>
		 <select name=esp_codigo id=\"esp_codigo\" class=box>";
	    //
	    //-> SQL da Especialidade 
	    $query = pg_query("select esp_codigo, esp_nome from especialidade order by esp_nome ");
	    echo "<option value='todos'>Todas</option>";
	      /*while($especialidade=pg_fetch_array($query)) {
	       echo "<option value='$especialidade[esp_codigo]'>$especialidade[esp_nome]</option>";
	      }*/
	   echo "</select>
	        </td>
	      </tr>
	      <tr>
	       <td>&nbsp;</td>
	       <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg></td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br></form>";
 }//fechamento do if
}
//}

//------------------------------------------------------------------>
//-> Formulario de Edicao de Conteudo
//------------------------------------------------------------------>

 if($acao=="form_edit") {
	 reglog($id_login,"Formulario de EDICAO USUARIO_ACESSO");

//
//-> Formulario de edicao do cadastro SIMPLES

//
//-> Abaixo sao os botoes de voltar / cadastro simples / cadastro completo

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções de Cadastro</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=79><a href=$PHP_SELF?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
	       <td>&nbsp;</td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";

//
//-> Pegando as informcoes do banco pra mostrar no formulario
 $sqlespecialidade =                       "select *  
                                      from usuarios_acessos where uunid_codigo='$uunid_codigo'";
 $row=pg_fetch_array(pg_query($sqlespecialidade));

  echo "<form method=post action=$PHP_SELF>
	<input type=hidden name=acao value=edit>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=uunid_codigo value=$uunid_codigo>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Cadastro de Acessos por Usuario</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
		<td width=70>Usuario:</td>
		<td>
		 <select name=usr_codigo class=box>";
	    //
	    //-> SQL da Usuario do Sistema
	    $query = pg_query("select usr_codigo, usr_nome from usuarios order by usr_nome ");
	      while($usuario=pg_fetch_array($query)) {
	       echo ($usuario[usr_codigo]==$row[usr_codigo])?"<option value='$usuario[usr_codigo]' selected>$usuario[usr_nome]</option>":"<option value='$usuario[usr_codigo]'>$usuario[usr_nome]</option>";
	      }
	   echo "</select>
	        </td>
	      </tr>
	      <tr>
		<td width=70>Unidade:</td>
		<td>
		 <select name=uni_codigo class=box>";
	    //
	    //-> SQL da Unidade 
	    $query = pg_query("select uni_codigo, uni_desc from unidade order by uni_desc ");
	      while($unidade=pg_fetch_array($query)) {
	       echo ($unidade[uni_codigo]==$row[uni_codigo])?"<option value='$unidade[uni_codigo]' selected>$unidade[uni_desc]</option>":"<option value='$unidade[uni_codigo]'>$unidade[uni_desc]</option>";
	      }
	   echo "</select>
	        </td>
	      </tr>
	      <tr>
		<td width=70>Medico:</td>
		<td>
		 <select name=med_codigo id=\"med_codigo\" class=box onchange=\"buscar_especialidade()\">";
	    //
	    //-> SQL da Usuario do Sistema
	    $query = pg_query("select med_codigo, med_nome from medico order by med_nome ");
	    echo "<option value='todos'>Todos</option>";
	      while($medico=pg_fetch_array($query)) {
	       echo ($medico[med_codigo]==$row[med_codigo])?"<option value='$medico[med_codigo]' selected>$medico[med_nome]</option>":"<option value='$medico[med_codigo]'>$medico[med_nome]</option>";
	      }
	   echo "</select>
	        </td>
	      </tr>
	      <tr>
		<td width=70>Especialidade:</td>
		<td>
		 <select name=esp_codigo id=\"esp_codigo\" class=box>";
	    //
	    //-> SQL da Especialidade 
	    //$query = pg_query("select esp_codigo, esp_nome from especialidade order by esp_nome ");
	    echo "<option value='todos'>Todas</option>";
	      /*while($especialidade=pg_fetch_array($query)) {
	       echo ($especialidade[esp_codigo]==$row[esp_codigo])?"<option value='$especialidade[esp_codigo]' selected>$especialidade[esp_nome]</option>":"<option value='$especialidade[esp_codigo]'>$especialidade[esp_nome]</option>";
	      }*/
	   echo "</select>";
       echo "<script>buscar_especialidade2($row[esp_codigo]);</script>";
	    echo "</td>
	      </tr>
	      <tr>
	      <tr>
	       <td>&nbsp;</td>
	       <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg></td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br></form>";
}

//------------------------------------------------------------------>
//-> SQL's
//------------------------------------------------------------------>
//
//-> ADD <---------------------------------------------------------->

 if($acao=="add") {
	reglog($id_login,"Adicionando Registro em USUARIO_ACESSO");
    
    if($med_codigo != "todos" && $esp_codigo != "todos")
    {
        $sel = "select *
                from usuarios_acessos
                where med_codigo = $med_codigo
                and esp_codigo = $esp_codigo
                and uni_codigo = $uni_codigo
                and usr_codigo = $usr_codigo";
        $exec_sel = pg_query($sel);
        if(pg_num_rows($exec_sel) == 0)
        {
            $sql = "insert into usuarios_acessos ( " .
                    "usr_codigo, " .
                    "uni_codigo, " .
                    "med_codigo, " .
                    "esp_codigo  " .
                    ") values ( " .
                    ($usr_codigo ? "'$usr_codigo'" : "null") . ", " .
                    ($uni_codigo ? "'$uni_codigo'" : "null") . ", " .
                    ($med_codigo ? "'$med_codigo'" : "null") . ", " .
                    ($esp_codigo ? "'$esp_codigo'" : "null") . "  " .
                    ")";
            $exec_sql = pg_query($sql);
        }
    }
    
    if($med_codigo == "todos" && $esp_codigo != "todos")
    {
        $select = "select med_codigo, esp_codigo
                    from medico_especialidade
                    where esp_codigo = $esp_codigo
                    order by med_codigo, esp_codigo";
        $exec_select = pg_query($select);
        pg_query("begim");
        while($row = pg_fetch_array($exec_select))
        {
            $sel = "select *
                    from usuarios_acessos
                    where med_codigo = $row[med_codigo]
                    and esp_codigo = $esp_codigo
                    and uni_codigo = $uni_codigo
                    and usr_codigo = $usr_codigo";
            $exec_sel = pg_query($sel);
            if(pg_num_rows($exec_sel) == 0)
            {
                $sql = "insert into usuarios_acessos ( " .
                    "usr_codigo, " .
                    "uni_codigo, " .
                    "med_codigo, " .
                    "esp_codigo  " .
                    ") values ( " .
                    ($usr_codigo ? "'$usr_codigo'" : "null") . ", " .
                    ($uni_codigo ? "'$uni_codigo'" : "null") . ", " .
                    (!empty($row['med_codigo']) ? "'$row[med_codigo]'" : "null") . ", " .
                    ($esp_codigo ? "'$esp_codigo'" : "null") . "  " .
                    ")";
                $exec_sql = pg_query($sql);
            }
        }
        pg_query("commit");
    }
    
    if($med_codigo != "todos" && $esp_codigo == "todos")
    {
        $select = "select med_codigo, esp_codigo
                    from medico_especialidade
                    where med_codigo = $med_codigo
                    order by med_codigo, esp_codigo";
        $exec_select = pg_query($select);
        pg_query("begim");
        while($row = pg_fetch_array($exec_select))
        {
            $sel = "select *
                    from usuarios_acessos
                    where med_codigo = $med_codigo
                    and esp_codigo = $row[esp_codigo]
                    and uni_codigo = $uni_codigo
                    and usr_codigo = $usr_codigo";
            $exec_sel = pg_query($sel);
            if(pg_num_rows($exec_sel) == 0)
            {
                $sql = "insert into usuarios_acessos ( " .
                    "usr_codigo, " .
                    "uni_codigo, " .
                    "med_codigo, " .
                    "esp_codigo  " .
                    ") values ( " .
                    ($usr_codigo ? "'$usr_codigo'" : "null") . ", " .
                    ($uni_codigo ? "'$uni_codigo'" : "null") . ", " .
                    ($med_codigo ? "'$med_codigo'" : "null") . ", " .
                    (!empty($row[esp_codigo]) ? "'$row[esp_codigo]'" : "null") . "  " .
                    ")";
                $exec_sql = pg_query($sql);
            }
        }
        pg_query("commit");
    }
    
    if($med_codigo == "todos" && $esp_codigo == "todos")
    {
        $sql_sel = "select med_codigo, esp_codigo
                from medico_especialidade
                order by med_codigo, esp_codigo";
        
        $exec_sql_sel = pg_query($sql_sel);
        pg_query("begim");
        while($row = pg_fetch_array($exec_sql_sel))
        {
            $exec_select = pg_query($select);
            $sel = "select *
                    from usuarios_acessos
                    where med_codigo = $row[med_codigo]
                    and esp_codigo = $row[esp_codigo]
                    and uni_codigo = $uni_codigo
                    and usr_codigo = $usr_codigo";
            $exec_sel = pg_query($sel);
            if(pg_num_rows($exec_sel) == 0)
            {
                $sql = "insert into usuarios_acessos ( " .
                        "usr_codigo, " .
                        "uni_codigo, " .
                        "med_codigo, " .
                        "esp_codigo  " .
                        ") values ( " .
                        ($usr_codigo ? "'$usr_codigo'" : "null") . ", " .
                        ($uni_codigo ? "'$uni_codigo'" : "null") . ", " .
                        (!empty($row[med_codigo]) ? "'$row[med_codigo]'" : "null") . ", " .
                        (!empty($row[esp_codigo]) ? "'$row[esp_codigo]'" : "null") . "  " .
                        ")";
                $exec_sql = pg_query($sql);
            }
        }
        pg_query("commit");
    }
    
    //echo $sql;
    /*echo "insert into usuarios_acessos ( " .
            "usr_codigo, " .
            "uni_codigo, " .
            "med_codigo, " .
            "esp_codigo  " .
            ") values ( " .
            ($usr_codigo ? "'$usr_codigo'" : "null") . ", " .
            ($uni_codigo ? "'$uni_codigo'" : "null") . ", " .
            ($med_codigo ? "'$med_codigo'" : "null") . ", " .
            ($esp_codigo ? "'$esp_codigo'" : "null") . "  " .
            ")";*/
    msg($id_login,$acao,$exec_sql);
}

//
//-> EDIT <--------------------------------------------------------->

 if($acao=="edit") {
	 reglog($id_login,"Editando USUARIO_ACESSO $uunid_codigo");

  $sql = pg_query("update usuarios_acessos set " .
            ($usr_codigo ? "usr_codigo='$usr_codigo'" : "usr_codigo=null") . "," .
            ($uni_codigo ? "uni_codigo='$uni_codigo'" : "uni_codigo=null") . "," .
            ($med_codigo ? "med_codigo='$med_codigo'" : "med_codigo=null") . "," .
            ($esp_codigo ? "esp_codigo='$esp_codigo'" : "esp_codigo=null") . " " .
            "where uunid_codigo='$uunid_codigo'");
#            echo $sql;
#            exit(0);

msg($id_login,$acao,$sql);
}

//
//-> DEL <---------------------------------------------------------->

 if($acao=="del") {
	 reglog($id_login,"Exluindo Registro de USUARIO_ACESSO $uunid_codigo");

  $sql = pg_query("delete from usuarios_acessos where uunid_codigo='$uunid_codigo'");
msg($id_login,$acao,$sql);
}

?>
</fieldset>
