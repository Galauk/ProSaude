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

echo "<fieldset><legend>ACESSO DE SETORES DE ESTOQUE POR  USUÁRIO</legend>";

// if(empty($acao)) {
 if(empty($acao) OR $acao=='form_acesso') {

//
//-> Botoes
  echo "<fieldset>
	    <legend>Opções</legend>
	       <a href=usuarios.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>
	       ".ChmodBtn($id_login,'adicionar','usuario_setores.php?acao=form_add')."
	       
			<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
			  <tr>";
				if (chmodbtn($id_login,"procurar_if","usuario_setores.php"))
                                {
                                    echo "<form method=post action=$PHP_SELF>";
                                }
                                echo "
					<input type=hidden name=acao value=busca>
					<input type=hidden name=id_login value=$id_login>
					<td width=30>Buscar:</td>
					<td width=120><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
					<td>".ChmodBtn($id_login,'procurar','usuario_setores.php')."</td>
				
				</form>
				
			  </tr>
			</table>

	   </fieldset>
	  <br>";

//
//-> Listando
  
    if (chmodbtn($id_login,"listar_if","usuario_setores.php"))
    {
        echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
               <tr>
                <td>
                 <fieldset>
                  <legend>Listando Últimas <b>15</b> Usuarios/Setores Cadastrados</legend>
                   <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
                    <tr bgcolor=F9f9f9>
                      <td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Usuario</td>
                      <td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Setor</td>
                      <td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";
      
         $sql=pg_query("select uset_codigo, 
                         (select usr_nome from usuarios where usr_codigo = usuarios_setores.usr_codigo) as nomeusuario,
                         (select set_nome from setor where set_codigo = usuarios_setores.set_codigo) as nomesetor
                        from usuarios_setores
                        order by uset_codigo desc limit 15");
           while($row=pg_fetch_array($sql)) {
             echo "<tr>
                     <td width=300 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[nomeusuario]</td>
                     <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[nomesetor]</td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'editar','usuario_setores.php?acao=form_edit&uset_codigo='.$row[uset_codigo])."</td>
                     <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'apagar','usuario_setores.php?acao=del&uset_codigo='.$row[uset_codigo])."</td>
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
	       <td width=200>".ChmodBtn($id_login,'adicionar','usuario_setores.php?acao=form_add')."</td>
	       <form method=post action=$PHP_SELF>
		<input type=hidden name=acao value=busca>
	<input type=hidden name=id_login value=$id_login>
	       <td width=30>Buscar:</td>
	       <td width=120><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
	       <td>".ChmodBtn($id_login,'procurar','usuario_setores.php')."</td></form>
	       <td width=107><a href='logoff.php?id_login=$id_login' target='_parent'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/sair.gif border=0></a></td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";

   $sql=pg_query("
                select a.uset_codigo, b.usr_nome, c.set_nome 
                from usuarios_setores a, usuarios b, setor c
                where a.usr_codigo = b.usr_codigo
                and a.set_codigo = c.set_codigo
                and b.usr_nome ilike '$palavra_chave%'
                order by b.usr_nome, c.set_nome ");
    
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
		<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Setor</td>
		<td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";

     while($row=pg_fetch_array($sql)) {
       echo "<tr>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[usr_nome]</td>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[set_nome]</td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'editar','usuario_setores.php?acao=form_edit&uset_codigo='.$row[uset_codigo])."</td>
	       <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'apagar','usuario_setores.php?acao=del&uset_codigo='.$row[uset_codigo])."</td>
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
	    <legend>Cadastro de Setores de Estoque por Usuario</legend>
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
		<td width=70>Setor de Estoque:</td>
		<td>
		 <select name=set_codigo class=box>";
	    //
	    //-> SQL da Unidade 
	    $query = pg_query("select set_codigo, set_nome from setor order by set_nome ");
	      while($setor=pg_fetch_array($query)) {
	       echo "<option value='$setor[set_codigo]'>$setor[set_nome]</option>";
	      }
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
                                      from usuarios_setores where uset_codigo='$uset_codigo'";
 $row=pg_fetch_array(pg_query($sqlespecialidade));

  echo "<form method=post action=$PHP_SELF>
	<input type=hidden name=acao value=edit>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=uset_codigo value=$uset_codigo>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Cadastro de Setores de Estoque por Usuario</legend>
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
		<td width=70>Setor:</td>
		<td>
		 <select name=set_codigo class=box>";
	    //
	    //-> SQL da Unidade 
	    $query = pg_query("select set_codigo, set_nome from setor order by set_nome ");
	      while($setor=pg_fetch_array($query)) {
	       echo ($setor[set_codigo]==$row[set_codigo])?"<option value='$setor[set_codigo]' selected>$setor[set_nome]</option>":"<option value='$setor[set_codigo]'>$setor[set_nome]</option>";
	      }
	   echo "</select>
	        </td>
	      </tr>
	      <tr>
	      <tr>
	       <td>&nbsp;</td>
	       <td><a href=usuario_setores.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
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
    
        $sel = "select *
                from usuarios_setores
                where set_codigo = $set_codigo
                and usr_codigo = $usr_codigo";
        $exec_sel = pg_query($sel);
        if(pg_num_rows($exec_sel) == 0)
        {
            $sql = "insert into usuarios_setores ( " .
                    "usr_codigo, " .
                    "set_codigo  " .
                    ") values ( " .
                    ($usr_codigo ? "'$usr_codigo'" : "null") . ", " .
                    ($set_codigo ? "'$set_codigo'" : "null") . "  " .
                    ")";
            $exec_sql = pg_query($sql);
        }
        pg_query("commit");
    
    msg($id_login,$acao,$exec_sql);
}

//
//-> EDIT <--------------------------------------------------------->

 if($acao=="edit") {
	 reglog($id_login,"Editando USUARIO_ACESSO $uset_codigo");

  $sql = pg_query("update usuarios_setores set " .
            ($usr_codigo ? "usr_codigo='$usr_codigo'" : "usr_codigo=null") . "," .
            ($set_codigo ? "set_codigo='$set_codigo'" : "set_codigo=null") . " " .
            "where uset_codigo='$uset_codigo'");
#            echo $sql;
#            exit(0);

msg($id_login,$acao,$sql);
}

//
//-> DEL <---------------------------------------------------------->

 if($acao=="del") {
	 reglog($id_login,"Exluindo Registro de USUARIO_ACESSO $uset_codigo");

  $sql = pg_query("delete from usuarios_setores where uset_codigo='$uset_codigo'");
msg($id_login,$acao,$sql);
}

?>
</fieldset>
