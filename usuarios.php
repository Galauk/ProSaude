<?php
session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
verauth($id_login);
include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
Cabecario();
?>
<script type="text/javascript" src="funcoes.js"></script>
<script type="text/javascript" src="ajax_motor.js"></script>
<script type="text/javascript">
function wbio(u) {
window.open( 'biometria/bioUsuarioCadastro.php?usr_codigo='+u,
		 null,
		 'height=268,width=230,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes');
}
function confirma_senha(valor)
{
	var usr_confirm = document.getElementById('usr_confirm');
	var usr_senha = document.getElementById('usr_senha');
	if (valor == 'add')
	{
		if ((usr_confirm.value == "" || usr_senha.value == "") || usr_confirm.value != usr_senha.value)
		{
			alert('Preencha os campos senha a confirmação corretamente.');
			return false;
		}
		else
			return true;
	}
	else if (valor == 'edit')
	{
		if ((usr_confirm.value != "" || usr_senha.value != "") && usr_confirm.value != usr_senha.value)
		{
			alert('Preencha os campos senha a confirmação corretamente.');
			return false;
		}
		else
			return true;
	}
	else
		return false;
}

function valida_form( valor, usr_codigo )
{
	if(valor!='edit'){
    	if( ! confirma_senha( valor ) ) return false;
	}
    if( ! valida('usr_nome',"Nome") ) return false;
    if( ! valida('usr_cpf',"CPF") ) return false;
	if( ! valida('usr_login',"Login") ) return false;
    //if( ! valida('usr_email',"Email") ) return false;
    
    var Email = $('usr_email'), Login = $('usr_login');
    var endereco = 'usuarios_op.php?acao=verifica&usr_login='+(Login.value ? Login.value : '' );
    endereco += '&usr_email='+(Email.value ? Email.value : '' );
    endereco += '&usr_codigo='+( usr_codigo ? usr_codigo : 0 );
    
    ajax_tudo( endereco, valida_form_callback );
    
    return false;
}
function valida_form_callback( txt )
{
    var Resp = eval(txt);
    if( ! Resp.ok )
    {
        alert( Resp.msg );
        return false;
    }
    $('form_usr').submit();
}
function mostraCampos(){
	var isOpen = false;
	if(isOpen)
	{
		document.getElementById('oculta').style.display = 'none';
	}
	else
	{
		document.getElementById('oculta').style.display = 'block';
	}
	isOpen = !isOpen;
}

function deletaEspecialidade(usr_codigo){
	decisao = confirm("Se o usuario for alterado sera desvinculado suas especialidades! Deseja realizar essa operacao?");
	if (decisao){
		url = "deletaEspecialidades.php?usr_codigo="+usr_codigo;
		ajax_tudo(url,sucesso);
	} else {
		alert ("Você clicou no botão CANCELAR,\n"+"porque foi retornado o valor: "+decisao);
	}
}
function sucesso(txt){
	if(txt != ""){
		alert(txt);
	}
}

</script>
<?php

//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
	 reglog($id_login,"Entrando em USUARIOS");
//------------------------------------------------------------------>


echo "<fieldset><legend>CADASTRO DE USUÁRIOS</legend>";

if($act=="kill")
{
   $sql=db_query("update logon set dt_atualizacao='2000-10-10 10:10:10' where id_login='$id_logout'");
}

if(empty($acao))
{

//
//-> Botoes



  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=95>".ChmodBtn($id_login,'adicionar','usuarios.php?acao=form_add')."</td>
               <td width=123>".ChmodBtn($id_login,'acesso_por_usuario','usuario_acesso.php?acao=form_acesso')."</td>
               <td width=85>".ChmodBtn($id_login,'permissoes','permissoes.php?acao=form_perm')."</td>
               <td width=85>".ChmodBtn($id_login,'permissoes_po_usuario','permissoes_usuarios.php?acao=form_perm_usu')."</td>
               <td width=50><a href=log.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/log_on.jpg border=0></a></td>
               <td><a href=alterarsenha.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/alterar_senha_on.jpg border=0></a></td>
               <td width=123>".ChmodBtn($id_login,'setor_por_usuario','usuario_setores.php?acao=form_acesso')."</td>
	       </tr>
	      </table>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
		<tr>";
            if (chmodbtn($id_login,"procurar_if","usuarios.php"))
            {                             
               echo "<form method=post action=$PHP_SELF>";
            }
	    echo "<input type=hidden name=acao value=busca>
			  <input type=hidden name=id_login value=$id_login>
	       <td width=30>Buscar:</td>
	       <td width=120><input type=text name=palavra_chave class=box></td>
	       <td>".ChmodBtn($id_login,'procurar','usuarios.php')."</td></form>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";

   //
   //-> Listando


   if (chmodbtn($id_login,"listar_if","usuarios.php")) // adicionado este if para fazer funcionar a permissão "listar" usuarios
   {    
         echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
               <tr>
                <td>
                 <fieldset>
                  <legend>Listando Últimos <b>15</b> Usuarios Cadastrados</legend>
                   <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
                    <tr bgcolor=F9f9f9>
                      <td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Codigo</td>
                      <td width=150 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Nome</td>
                      <td width=150 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Uni. Trabalho</td>
                      <td width=100 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Ultima Conexão</td>
                      <td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>
                      <td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";
         
         $stmt =
                  "SELECT ".
                        "u.usr_ativo,  ".
                        "u.uni_codigo,  ".
                        "u.usr_codigo,  ".
                        "UPPER(u.usr_nome) AS usr_nome,  ".
                        "u.usr_login, ".
                        "TO_CHAR(l.dt_entrada,'dd/mm/yyyy- hh24:mi') AS entrada, ".
                        "( CASE WHEN dt_atualizacao > NOW() THEN 'on' ELSE 'off' END ) AS status,  ".
                        "n.uni_desc ".
                  "FROM usuarios AS u ".
                  "LEFT JOIN logon AS l ON l.id_login = u.usr_codigo ".
                  "LEFT JOIN unidade AS n ON n.uni_codigo = u.uni_codigo ".
                  "WHERE usr_ativo = 'S'".
                  "ORDER BY u.usr_nome ";
                  
         $sql = db_query( $stmt );
        
         while($row=pg_fetch_array($sql))
         {
            //$rr=pg_fetch_array(pg_query("select to_char(dt_entrada,'dd/mm/YYYY - hh24:mi') as entrada from logon where id_login='$row[usr_codigo]'"));
            //$log=pg_query("select *from logon where id_login='$row[usr_codigo]' and dt_atualizacao>NOW()");
            //if(pg_num_rows($log)!="0")
            if( $row['status'] == "on" )
            {
              $txt="Status: <font color=green><b>On</b></font>&nbsp;&nbsp;&nbsp;";
              $txt.="<b>(</b><a href=$PHP_SELF?id_logout=$row[usr_codigo]&act=kill>Desconectar</a><b>)</b>";
            } else {
              $txt="Status: <font color=red><b>Off</b></font>";
            }
            
                //$uni = pg_fetch_array(pg_query("select *from unidade where uni_codigo = '$row[uni_codigo]'"));
                echo "<tr>
                        <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[usr_codigo]</td>
                        <td width=150 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[usr_nome]</td>
                        <td width=150 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[uni_desc]</td>
                        <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[entrada]</td>
                        <td align=left style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$txt</td>
                        <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href='#' onclick='wbio(\"$row[usr_codigo]\");'><img src=imgs/bio.png border=0></a></td>
                        <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'editar','usuarios.php?acao=form_edit&usr_codigo='.$row[usr_codigo])."</td>
                        <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'apagar','usuarios.php?acao=del&usr_codigo='.$row['usr_codigo'])."</td>
                      </tr>";
         }
   }
   
	echo "</tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table>";

   //$depois = microtime(1);
   //echo "<p>Diferenaça :". ( ($depois - $antes) / 1000 ) ."</p>";

}





//------------------------------------------------------------------>
//-> Mostrando o resultado da busca
//------------------------------------------------------------------>
else if($acao=="busca")
{
   //
   //-> Verificando Busca
   reglog($id_login,"Buscando em USUARIOS: $palavra_chave ");

   if(strlen($palavra_chave)<="3")
   {
      echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
      <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
      <tr bgcolor=f9f9f9>
      <td align=center><font color=red size=2><b>ERRO</b></font><br>Busca com menos de <b>3</b> caracteres não permitida</td>
      </tr>
      </table><br>";
      echo "<SCRIPT LANGUAGE=\"JavaScript\">
      setTimeout(\"location='$PHP_SELF?id_login=$id_login'\", 0);
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

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=95>".ChmodBtn($id_login,'adicionar','usuarios.php?acao=form_add')."</td>
               <td width=123>".ChmodBtn($id_login,'acesso_por_usuario','usuario_acesso.php?acao=form_acesso')."</td>
               <td width=85>".ChmodBtn($id_login,'permissoes','permissoes.php?acao=form_perm')."</td>
               <td width=142>".ChmodBtn($id_login,'permissoes_po_usuario','permissoes_usuarios.php?acao=form_perm_usu')."</td>
               <td><a href=log.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/log_on.jpg border=0></a></td>
	       </tr>
	      </table>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
		<tr>";
            if (chmodbtn($id_login,"procurar_if","usuarios.php"))
            {
               echo "<form method=post action=$PHP_SELF>";
            }
	    echo "<input type=hidden name=acao value=busca>
		<input type=hidden name=id_login value=$id_login>
	       <td width=30>Buscar:</td>
	       <td width=120><input type=text name=palavra_chave class=box></td>
	       <td>".ChmodBtn($id_login,'procurar','usuarios.php')."</td></form>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";        

   $stmt = "select usr_codigo, usr_nome, usr_login from usuarios where (usr_nome ilike '%$palavra_chave%')";
   $sql=db_query($stmt);
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
		<td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Codigo</td>
		<td width=270 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Nome</td>
		<td width=20 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Login</td>
		<td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";

     while($row=pg_fetch_array($sql)) {
       echo "<tr>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[usr_codigo]</td>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[usr_nome]</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[usr_login]</td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href='#' onclick='wbio(\"$row[usr_codigo]\");'><img src=imgs/bio.png border=0></a></td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'editar','usuarios.php?acao=form_edit&usr_codigo='.$row[usr_codigo])."</td>
	       <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'apagar','usuarios.php?acao=del&usr_codigo='.$row[usr_codigo])."</td>
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

else if($acao=="form_add") {
	 reglog($id_login,"Formulario de ADICAO USUARIOS");


//
//-> Abaixo sao os botoes de voltar / cadastro simples / cadastro completo

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções de Cadastro</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=79><a href=usuarios.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
	       <td>&nbsp;</td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table>";
//
//-> Este if esta apontando quando a acao for vazia ele vai mostrar o cadastro simples
//   no cadastro completo vc vai ter que passar a variavel acao para completo

// if(($type=="" || $acao=="simples")) {

    echo "
    <fieldset>
    <legend>Cadastro de Usuarios</legend>
   
    <form method='post' id='form_usr' action='$PHP_SELF?id_login=$id_login&acao=add' onsubmit=\"return valida_form('add');\">
        
        <p>Obs: Os campos são obrigatórios: <span class='destaque'>Nome, Login e Senha</span></p>
		<p>Obs: Devido a integração com o <span class='destaque'>CNES</span> o campo <span class='destaque'>CPF</span> a partir de agora será obrigatório, servindo de parametro de pesquisa para atualização do cadastro do usuário.</p>
		<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
		<td class='destaque'>Nome:</td>
		<td><input type='text' name='usr_nome' id='usr_nome' class='box' size='70' /></td>
	      </tr>
	      <tr>
				<td>Função:</td>
				<td>
				<select class='box' name='usr_tipo_medico' onChange=\"mostraCampos()\">
					<option value=''>--SELECIONE--</option>
		            <option value='M'>Medico(a)</option>
		            <option value='E'>Enfermeiro(a)</option>
					<option value='A'>Aux. Enfermagem</option>
		            <option value='F'>Farmáceutico(a)</option>
		            <option value='B'>Bioquimico(a)</option>
		            <option value='D'>Dentista</option>
		            <option value='P'>Psic&oacute;logo(a)</option>
		            <option value='C'>Comum</option> 
		        </select>
		       </td>
            </tr>
            <tr>
            	<td>Num.Conselho:</td>
            	<td>
            		<input type='text' name='usr_num_conselho' id='usr_num_conselho' class='box' size='20' />
            	</td>
            </tr>
            <tr>
            	<td>CNS:</td>
            	<td>
            		<input type='text' name='usr_medico_cnes' id='usr_medico_cnes' class='box' size='20' />
            	</td>
            </tr>
            <tr>
            	<td>Carga Horaria:</td>
            	<td>
            		<input type='text' name='usr_carga_horaria' id='usr_num_conselho' class='box' size='20' onKeypress=\"return Ajusta_Hora(this,event)\"/>
            	</td>
            </tr>
            <tr>
            <td>Unidade de Trabalho:</td>
            <td><select name=uni_codigo class=box>";
       echo "<option value=>...</option>";
    $sql = pg_query("select *from unidade order by uni_desc");
      while($row=pg_fetch_array($sql)) {
       echo "<option value=$row[uni_codigo]>$row[uni_desc]</option>";
    }
	  echo "</select></td>
	      </tr>
		<tr>
		<td valign=top>Prestador de Servi&ccedil;o:</td>
		<td><select name=med_codigo[] multiple=\"multiple\" class=box size=5>";
   echo "<option value=>...</option>";
		$sql = pg_query("SELECT * FROM medico ORDER BY med_nome");
		while($rr =  pg_fetch_array($sql)) {
			echo ($rr[med_codigo]==$row[med_codigo])?"<option value=$rr[med_codigo] selected>$rr[med_nome]</option>":"<option value=$rr[med_codigo]>$rr[med_nome]</option>";
		}
	  echo "</select></td>
	      </tr>	      
	      <tr>
		<td class='destaque'>Login:</td>
		<td><input type='text' name='usr_login' id='usr_login' class='box' size='15'></td>
	      </tr>
	      <tr>
		<td class='destaque'>Senha:</td>
		<td>
			<input type=password name=usr_senha id=usr_senha class=box size='22'>&nbsp;&nbsp;&nbsp;&nbsp;Confirma&ccedil;&atilde;o&nbsp;
			<input type=password name=usr_confirm id=usr_confirm class=box size='22'>
		</td>
	      </tr>
	      <tr>
		<td class='destaque'>E-mail: </td>
		<td><input type='text' name='usr_email' id='usr_email' class='box' size='40'></td>
	      </tr>
	      <tr>
		<td>CPF: </td>
		<td><input type='text' name='usr_cpf' id='usr_cpf' class='box' size='40'></td>
	      </tr>
	      <tr>
		<td>Funcao: </td>
		<td><input type=text name=usr_funcao class=box size='40'></td>
	      </tr>
	      <tr>
		<td>MSN:</td>
		<td><input type=text name=usr_msn class=box size='40'></td>
	      </tr>
	      <tr>
		<td>Celular:</td>
		<td><input type=text name=usr_celular class=box maxlength=13 size=15 onKeyPress='soNumeroTelefone(this)'></td>
	      </tr>
	    <tr>
		    <td>Requisicao de Movimentacao: </td>
		    <td>
		        <select name=usr_requisicao class=box>
          	         <option value=S>Sim</option>
    		         <option value=N selected>Nao</option>
		        </select>
            </td>    
	    </tr>
	    <tr>
		    <td>Consolidacao Automatica: </td>
		    <td>
		        <select name=usr_consolidacao_automatica class=box>
          	         <option value=S>Sim</option>
    		         <option value=N selected>Nao</option>
		        </select>
            </td>    
	    </tr>
	    <tr>
		    <td>Ativo: </td>
		    <td>
		        <select name=usr_ativo class=box>
				<option value=S selected>Sim</option>
				<option value=N>Nao</option>
		        </select>
            </td>    
	    </tr>
	    <tr>
		    <td>Tipo Mestre: </td>
		    <td>
		        <select name=usr_mestre class=box>
				<option value=S>Sim</option>
				<option value=N selected>Nao</option>
		        </select>
            </td>    
	    </tr>
        <tr>
		    <td>Permitir visualização de todos centro<br /> estocadores na Transfer&ecirc;ncia: </td>
		    <td valign='top'>
		        <select name='usr_transferencia' class='box'>
				<option value='S'>Sim</option>
				<option value='N' selected>N&atilde;o</option>
		        </select>
            </td>    
	    </tr>
	      <tr>
	       <td>&nbsp;</td>
	       <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg></td>
	      </tr>
	     </table>
	   </fieldset>
	  </form>";
 }//fechamento do if

//}
//}

//------------------------------------------------------------------>
//-> Formulario de Edicao de Conteudo
//------------------------------------------------------------------>

 if($acao=="form_edit") {
	 reglog($id_login,"Formulario de EDICAO USUARIOS");
	 $usr_codigo = $_GET['usr_codigo'];
//
//-> Formulario de edicao do cadastro SIMPLES
//
//-> Pegando as informcoes do banco pra mostrar no formulario
 $sqlusuarios = "SELECT * 
 				   FROM usuarios 
 				  WHERE usr_codigo = '$usr_codigo'";
 //echo $sqlusuarios;
 $row=pg_fetch_array(pg_query($sqlusuarios));
 $senha = '$row[usr_senha]';
 if ($row[usr_requisicao] == 'S' ) {
    $vlreq1 = 'selected ';
    $vlreq2 = '';
    }
  else {
    $vlreq1 = '';
    $vlreq2 = 'selected';
    }
 if ($row[usr_consolidacao_automatica] == 'S' ) {
    $vlcon1 = 'selected ';
    $vlcon2 = '';
    }
  else {
    $vlcon1 = '';
    $vlcon2 = 'selected';
    }

    $cpf = $row['usr_cpf'];
    if($cpf){
	    $a = substr($cpf, 0, 3);
	    $b = substr($cpf, 3, 3);
	    $c = substr($cpf, 6, 3);
	    $d = substr($cpf, -2);
	    $cpf = "$a.$b.$c-$d";
    }
	// Usuário com CNES ativo somente edita login e senha
    if ($row["cnes_ativo"] == 'S') {
		echo "<form method='post' id='form_usr' action='$PHP_SELF?id_login=$id_login&acao=edit&usr_codigo=$usr_codigo' onsubmit=\"return valida_form('edit','$usr_codigo');\">
		<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
        <tr>
            <td>
                <fieldset>
                    <legend>Cadastro de Usuario</legend>

                    <p>Obs: Os campos são obrigatórios: <span class='destaque'>Nome, Login e Senha</span></p>
					<p>Obs: Devido a integração com o <span class='destaque'>CNES</span> o campo <span class='destaque'>CPF</span> a partir de agora será obrigatório, servindo de parametro de pesquisa para atualização do cadastro do usuário.</p>
					<p>Obs: <span class='destaque'>Usuário importado do CNES só pode editar seu Login e senha</span></p>

                    <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
                        <tr>
                            <td class='destaque'>Nome:</td>
                            <td><input type='text' name='usr_nome' id='usr_nome' class='box' size='70' value='$row[usr_nome]' readonly='readonly'></td>
                        </tr>
                        <tr>
                            <td>Médico:</td>
                            <td>";
								 if ($row[usr_tipo_medico] == "M") { $usr_tipo_medicov = "Médico"; }
								 if ($row[usr_tipo_medico] == "E") { $usr_tipo_medicov = "Enfermeiro(a)"; }
								 if ($row[usr_tipo_medico] == "A") { $usr_tipo_medicov = "Aux. Enfermagem"; }
								 if ($row[usr_tipo_medico] == "F") { $usr_tipo_medicov = "Farmáceutico(a)"; }
								 if ($row[usr_tipo_medico] == "B") { $usr_tipo_medicov = "Bioquimico(a)"; }
								 if ($row[usr_tipo_medico] == "D") { $usr_tipo_medicov = "Dentista"; }
								 if ($row[usr_tipo_medico] == "P") { $usr_tipo_medicov = "Psic&oacute;logo(a)"; }
								 if ($row[usr_tipo_medico] == "C") { $usr_tipo_medicov = "Comum"; }
								 echo "<input type='text' name='usr_tipo_medicov' id='usr_tipo_medicov' class='box' size='70' value='$usr_tipo_medicov' readonly='readonly'>
								 <input type='hidden' name='usr_tipo_medico' id='usr_tipo_medico' class='box' size='70' value='".$row[usr_tipo_medico]."' readonly='readonly'>
							</td>

                            </td>
                        </tr>
                        <tr>
                            <td>Num.Conselho:</td>
                            <td>
                                <input type='text' name='usr_num_conselho' id='usr_num_conselho' class='box' size='20' value='$row[usr_num_conselho]' readonly='readonly' />
                            </td>
                        </tr>
                        <tr>
                            <td>CNES:</td>
                            <td>
                                <input type='text' name='usr_medico_cnes' id='usr_medico_cnes' class='box' size='20' value='$row[usr_medico_cnes]' readonly='readonly' />
                            </td>
                        </tr>
                        <tr>
                            <td>Carga Horaria:</td>
                            <td>
                                <input type='text' name='usr_carga_horaria' id='usr_carga_horaria' class='box' size='20' value='$row[usr_carga_horaria]'  onKeypress=\"return Ajusta_Hora(this,event)\" readonly='readonly'/>
                            </td>
                        </tr>
                        <tr>
                            <td>Unidade de Trabalho:</td>
                            <td>";
								$sql = pg_query("select *from unidade order by uni_desc");
								while($rr=pg_fetch_array($sql)) {
									if ($rr[uni_codigo]==$row[uni_codigo]) { $uni_desc = $rr[uni_desc]; }
								}
								echo "<input type='text' name='uni_codigov' id='uni_codigov' class='box' size='70' value='$uni_desc' readonly='readonly'>
								<input type='hidden' name='uni_codigo' id='uni_codigo' class='box' size='70' value='".$row[uni_codigo]."' readonly='readonly'>
							</td>
                        </tr>
                        <tr>
                            <td valign=top>Prestador de Servi&ccedil;o:</td>
                            <td><select disabled name=med_codigo[] multiple=\"multiple\" class=box size=5>";
                                    echo "<option value=>...</option>";
                                    $sql = pg_query("SELECT * FROM medico ORDER BY med_nome");
                                    while($rr =  pg_fetch_array($sql)) {
                                    echo ($rr[med_codigo]==$row[med_codigo])?"<option value=$rr[med_codigo] selected>$rr[med_nome]</option>":"<option value=$rr[med_codigo]>$rr[med_nome]</option>";
                                    }
                                    echo "</select></td>
                        </tr>
                        <tr>
                            <td class='destaque'>Login:</td>
                            <td><input type='text' name='usr_login' id='usr_login' class='box' size='30' value='$row[usr_login]' ></td>
                        </tr>
                        <tr>
                            <td class='destaque'>Senha:</td>
                            <td>
                                <input type=password name=usr_senha id=usr_senha class=box size=22>&nbsp;&nbsp;&nbsp;&nbsp;Confirma&ccedil;&atilde;o&nbsp;
                                <input type=password name=usr_confirm id=usr_confirm class=box size=22>
                            </td>
                        </tr>
                        <tr>
                            <td class='destaque'>E-mail: </td>
                            <td><input type='text' name='usr_email' id='usr_email' readonly='readonly' class='box' size='40' value='$row[usr_email]' onChange=\"return Verifica_Email('usr_email', 1)\" ></td>
                        </tr>
                        <tr>
                            <td>CPF: </td>
                            <td><input type='text' name='usr_cpf' id='usr_cpf' readonly='readonly' class='box' size='40' value='$cpf'></td>
                        </tr>
                        <tr>
                            <td>Funcao: </td>
                            <td><input type=text name=usr_funcao readonly='readonly' class=box size=40 value='$row[usr_funcao]'></td>
                        </tr>
                        <tr>
                            <td>MSN:</td>
                            <td><input type=text name=usr_msn readonly='readonly' class=box size=40 value='$row[usr_msn]'></td>
                        </tr>
                        <tr>
                            <td>Celular:</td>
                            <td><input type=text name=usr_celular readonly='readonly' class=box maxlength=13 size=15 value='$row[usr_celular]' onKeyPress='soNumeroTelefone(this)'></td>
                        </tr>
                        <tr>
                            <td>Requisicao de Movimentacao:</td>
                            <td>";
								if ($vlreq1 == "selected") { $vlrReqv="Sim"; $vlrReq="S"; } else { $vlrReqv="Não"; $vlrReq="N"; }
								echo "<input type=text name=usr_requisicaov readonly='readonly' class=box maxlength=13 size=05 value='$vlrReqv'>
								<input type=hidden name=usr_requisicao readonly='readonly' class=box maxlength=13 size=05 value='$vlrReq'>
                            </td>    
                        </tr>
                        <tr>
                            <td>Consolidacao Automatica: </td>
							<td>";
								if ($vlcon1 == "selected") { $vlconv = "Sim"; $vlcon = "S"; } else { $vlconv = "Não"; $vlcon = "N"; }
								echo "<input type=text name=usr_consolidacao_automaticav readonly='readonly' class=box maxlength=13 size=05 value='$vlconv'>
								<input type=hidden name=usr_consolidacao_automatica readonly='readonly' class=box maxlength=13 size=05 value='$vlcon'>
                            </td>
                        </tr>
                        <tr>
                            <td>Ativo: </td>
							<td>";
								if ($row['usr_ativo'] == "S") { $ativov = "Sim"; $ativo = "S"; } else { $ativov = "Não"; $ativo = "N"; }
								echo "<input type=text name=usr_ativov readonly='readonly' class=box maxlength=13 size=05 value='$ativov'>
								<input type=hidden name=usr_ativo readonly='readonly' class=box maxlength=13 size=05 value='$ativo'>
                            </td>
						</tr>
                        <tr>
                            <td>Tipo Mestre: </td>
                            <td>";
								if ($row['usr_mestre']=="S") {  $usr_mestrev = "Sim";  $usr_mestre = "S";} else { $usr_mestrev = "Não"; $usr_mestre = "N"; }
								echo "<input type=text name=usr_mestrev readonly='readonly' class=box maxlength=13 size=05 value='$usr_mestrev'>
								<input type=hidden name=usr_mestre readonly='readonly' class=box maxlength=13 size=05 value='$usr_mestre'>
                            </td> 
                        </tr>
                        <tr>
                            <td>Permitir visualização de todos centro<br /> estocadores na Transfer&ecirc;ncia: </td>
                            <td>";
								if ($row['usr_transferencia']=="S") {  $usr_transferenciav = "Sim"; $usr_transferencia = "S"; } else { $usr_transferenciav = "Não"; $usr_transferencia = "N"; }
								echo "<input type=text name=usr_transferenciav readonly='readonly' class=box maxlength=13 size=05 value='$usr_transferenciav'>
								<input type=hidden name=usr_transferencia readonly='readonly' class=box maxlength=13 size=05 value='$usr_transferencia'>
                            </td> 
						</tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td><a href=usuarios.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>&nbsp;&nbsp;<input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg ></td>
                        </tr>
                    </table>
                </fieldset>
            </td>
        </tr>
		</table><br></form>";
	} else {
		echo "<form method='post' id='form_usr' action='$PHP_SELF?id_login=$id_login&acao=edit&usr_codigo=$usr_codigo' onsubmit=\"return valida_form('edit','$usr_codigo');\">
		<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
        <tr>
            <td>
                <fieldset>
                    <legend>Cadastro de Usuario</legend>

                    <p>Obs: Os campos são obrigatórios: <span class='destaque'>Nome, Login e Senha</span></p>
					<p>Obs: Devido a integração com o <span class='destaque'>CNES</span> o campo <span class='destaque'>CPF</span> a partir de agora será obrigatório, servindo de parametro de pesquisa para atualização do cadastro do usuário.</p>

                    <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
                        <tr>
                            <td class='destaque'>Nome:</td>
                            <td><input type='text' name='usr_nome' id='usr_nome' class='box' size='70' value='$row[usr_nome]' ></td>
                        </tr>
                        <tr>
                            <td>Médico:</td>
                            <td>
                                <select class='box' name='usr_tipo_medico' onChange=\"deletaEspecialidade($usr_codigo)\" >
                                    <option value=''>--SELECIONE--</option>
                                    <option value='M' ".($row[usr_tipo_medico] == "M" ? "selected=selected" : "").">Medico(a)</option>
                                    <option value='E' ".($row[usr_tipo_medico] == "E" ? "selected=selected" : "").">Enfermeiro(a)</option>
                                    <option value='A' ".($row[usr_tipo_medico] == "A" ? "selected=selected" : "").">Aux. Enfermagem</option>
                                    <option value='F' ".($row[usr_tipo_medico] == "F" ? "selected=selected" : "").">Farmáceutico(a)</option>
                                    <option value='B' ".($row[usr_tipo_medico] == "B" ? "selected=selected" : "").">Bioquimico(a)</option>
                                    <option value='D' ".($row[usr_tipo_medico] == "D" ? "selected=selected" : "").">Dentista</option>
                                    <option value='P' ".($row[usr_tipo_medico] == "P" ? "selected=selected" : "").">Psic&oacute;logo(a)</option>
                                    <option value='C' ".($row[usr_tipo_medico] == "C" ? "selected=selected" : "").">Comum</option> 
                                </select>

                            </td>
                        </tr>
                        <tr>
                            <td>Num.Conselho:</td>
                            <td>
                                <input type='text' name='usr_num_conselho' id='usr_num_conselho' class='box' size='20' value='$row[usr_num_conselho]' />
                            </td>
                        </tr>
                        <tr>
                            <td>CNES:</td>
                            <td>
                                <input type='text' name='usr_medico_cnes' id='usr_medico_cnes' class='box' size='20' value='$row[usr_medico_cnes]' />
                            </td>
                        </tr>
                        <tr>
                            <td>Carga Horaria:</td>
                            <td>
                                <input type='text' name='usr_carga_horaria' id='usr_carga_horaria' class='box' size='20' value='$row[usr_carga_horaria]'  onKeypress=\"return Ajusta_Hora(this,event)\" />
                            </td>
                        </tr>
                        <tr>
                            <td>Unidade de Trabalho:</td>
                            <td><select name=uni_codigo class=box>";
                                    echo "<option value=>...</option>";
                                    $sql = pg_query("select *from unidade order by uni_desc");
                                    while($rr=pg_fetch_array($sql)) {
                                    echo ($rr[uni_codigo]==$row[uni_codigo])?"<option value=$rr[uni_codigo] selected>$rr[uni_desc]</option>":"<option value=$rr[uni_codigo]>$rr[uni_desc]</option>";
                                    }
                                    echo "</select></td>
                        </tr>
                        <tr>
                            <td valign=top>Prestador de Servi&ccedil;o:</td>
                            <td><select name=med_codigo[] multiple=\"multiple\" class=box size=5>";
                                    echo "<option value=>...</option>";
                                    $sql = pg_query("SELECT * FROM medico ORDER BY med_nome");
                                    while($rr =  pg_fetch_array($sql)) {
                                    echo ($rr[med_codigo]==$row[med_codigo])?"<option value=$rr[med_codigo] selected>$rr[med_nome]</option>":"<option value=$rr[med_codigo]>$rr[med_nome]</option>";
                                    }
                                    echo "</select></td>
                        </tr>
                        <tr>
                            <td class='destaque'>Login:</td>
                            <td><input type='text' name='usr_login' id='usr_login' class='box' size='30' value='$row[usr_login]' ></td>
                        </tr>
                        <tr>
                            <td class='destaque'>Senha:</td>
                            <td>
                                <input type=password name=usr_senha id=usr_senha class=box size=22>&nbsp;&nbsp;&nbsp;&nbsp;Confirma&ccedil;&atilde;o&nbsp;
                                <input type=password name=usr_confirm id=usr_confirm class=box size=22>
                            </td>
                        </tr>
                        <tr>
                            <td class='destaque'>E-mail: </td>
                            <td><input type='text' name='usr_email' id='usr_email' class='box' size='40' value='$row[usr_email]' onChange=\"return Verifica_Email('usr_email', 1)\" ></td>
                        </tr>
                        <tr>
                            <td>CPF: </td>
                            <td><input type='text' name='usr_cpf' id='usr_cpf' class='box' size='40' value='$cpf'></td>
                        </tr>
                        <tr>
                            <td>Funcao: </td>
                            <td><input type=text name=usr_funcao class=box size=40 value='$row[usr_funcao]'></td>
                        </tr>
                        <tr>
                            <td>MSN:</td>
                            <td><input type=text name=usr_msn class=box size=40 value='$row[usr_msn]'></td>
                        </tr>
                        <tr>
                            <td>Celular:</td>
                            <td><input type=text name=usr_celular class=box maxlength=13 size=15 value='$row[usr_celular]' onKeyPress='soNumeroTelefone(this)'></td>
                        </tr>
                        <tr>
                            <td>Requisicao de Movimentacao:</td>
                            <td>
                                <select name=usr_requisicao class=box>
                                    <option value=S $vlreq1>Sim</option>
                                    <option value=N $vlreq2>Nao</option>
                                </select>
                            </td>    
                        </tr>
                        <tr>
                            <td>Consolidacao Automatica: </td>
                            <td>
                                <select name=usr_consolidacao_automatica class=box>
                                    <option value=S $vlcon1>Sim</option>
                                    <option value=N $vlcon2>Nao</option>
                                </select>
                            </td>    
                        </tr>
                        <tr>
                            <td>Ativo: </td>
                            <td>
                                <select name=usr_ativo class=box>
                                    <option value=S ".($row['usr_ativo']=="S" ? "selected" : "").">Sim</option>
                                    <option value=N ".($row['usr_ativo']=="N" ? "selected" : "").">Nao</option>
                                </select>
                            </td>    
                        </tr>
                        <tr>
                            <td>Tipo Mestre: </td>
                            <td>
                                <select name=usr_mestre class=box>
                                    <option value=S ".($row['usr_mestre']=="S" ? "selected" : "").">Sim</option>
                                    <option value=N ".($row['usr_mestre']=="N" ? "selected" : "").">Nao</option>
                                </select>
                            </td>    
                        </tr>
                        <tr>
                            <td>Permitir visualização de todos centro<br /> estocadores na Transfer&ecirc;ncia: </td>
                            <td valign='top'>
                                <select name='usr_transferencia' class='box'>
                                    <option value='S' ".($row['usr_transferencia']=="S" ? "selected" : "").">Sim</option>
                                    <option value='N' ".($row['usr_transferencia']=="N" ? "selected" : "").">N&atilde;o</option>
                                </select>
                            </td>    
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td><a href=usuarios.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>&nbsp;&nbsp;<input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg ></td>
                        </tr>
                    </table>
                </fieldset>
            </td>
        </tr>
		</table><br></form>";
	}
}

//------------------------------------------------------------------>
//-> SQL's
//------------------------------------------------------------------>
//
//-> ADD <---------------------------------------------------------->

if($acao=="add") {

	reglog($id_login,"Adicionando Registro em USUARIOS");

	$selSeq = "SELECT nextval(('seq_usr_codigo_9041'::text)::regclass)";
	$execSel = pg_query($selSeq);
	$linha = pg_fetch_row($execSel);
	$usr_codigo = $linha[0];
	$usr_cpf = preg_replace("/[^0-9]/", "", $_POST['usr_cpf']); // tira tudo que não for número 
	
	$sql = "INSERT 
			  INTO usuarios 
				   ( 
				   		usr_codigo,
				   		usr_nome,
			            usr_login, 
			            usr_senha, 
			            usr_email, 
			            usr_funcao, 
			            usr_msn, 
			            usr_celular,  
			            usr_requisicao,  
			            usr_consolidacao_automatica,  
			            usr_ativo,   
			            usr_mestre,      
			            uni_codigo,  
			      		set_codigo,  
			            usr_transferencia,  
			      		usr_tipo_medico,
			      		usr_num_conselho,
			      		usr_medico_cnes,
			      		usr_carga_horaria,
			      		usr_cpf
            	   ) 
            	   VALUES 
            	   ( " .
						$usr_codigo.", ".
						($usr_nome ? "UPPER('$usr_nome')" : "null") . ", " .
						($usr_login ? "'$usr_login'" : "null") . ", " .
						($usr_senha ? "md5('$usr_senha')" : "null") . ", " .
						($usr_email ? "'$usr_email'" : "null") . ", " .
						($usr_funcao ? "'$usr_funcao'" : "null") . ", " .
						($usr_msn ? "'$usr_msn'" : "null") . ", " .
						($usr_celular ? "'$usr_celular'" : "null") . ",  " .
						($usr_requisicao ? "'$usr_requisicao'" : "null") . ",  " .
						($usr_consolidacao_automatica ? "'$usr_consolidacao_automatica'" : "null") . ", '" .
						$usr_ativo. "', '" .   
						$usr_mestre. "', " .      
						($uni_codigo ? "'$uni_codigo'" : "null")." ,".
						($set_codigo ? "'$set_codigo'" : "null")." ,
						'$usr_transferencia',
						'$usr_tipo_medico',
						'$usr_num_conselho',
						'$usr_medico_cnes',
						'$usr_carga_horaria',
						'$usr_cpf'
				   )";
      
	$sql = db_query($sql);
	
	$prestadores = $_POST[med_codigo];
	foreach ($prestadores as $valor){
		$insert = "INSERT 
					 INTO usuarios_medico 
					 	  (
					 	  		usr_codigo, 
					 	  		med_codigo
					 	  ) 
				    	  VALUES 
				    	  (
								$usr_codigo,
								$valor
						  )";
		$execInsert = pg_query($insert);
	}

      msg($id_login,$acao,$sql);
}

//
//-> EDIT <--------------------------------------------------------->

if($acao=="edit") {
	reglog($id_login,"Editando USUARIOS $usr_codigo");
	$prestadores = $_POST[med_codigo];
	foreach ($prestadores as $valor){
		$sel = "SELECT *
				  FROM usuarios_medico
				 WHERE med_codigo = $valor
				   AND usr_codigo = $usr_codigo";
		$exec = pg_query($sel);
		$num = pg_num_rows($exec);
		$valores .= $valor.", ";//Guardar os valores já concatenados com virgula e espaço para serem usados no delete
		if ($num == 0){
			$insert = "INSERT 
						 INTO usuarios_medico 
						 	  (
						 	  		usr_codigo, 
						 	  		med_codigo
						 	  ) 
					    	  VALUES 
					    	  (
									$usr_codigo,
									$valor
							  )";
			$execInsert = pg_query($insert);
		}		
	}
	$valores = substr($valores, 0, -2);//retirar o último espaço a última vírgula para não dar erro de sintaxe
	$del = "DELETE 
			  FROM usuarios_medico
			 WHERE usr_codigo = $usr_codigo
			   AND med_codigo NOT IN ($valores)";
	$run = pg_query($del);
	
	$usr_cpf = preg_replace("/[^0-9]/", "", $_POST['usr_cpf']); // tira tudo que não for número 

	$sql = "UPDATE usuarios 
			   SET " .
		           ($usr_nome ? "usr_nome=UPPER('$usr_nome')" : "usr_nome=null") . ", " .
				   ($usr_senha == "" ? "" : "usr_senha = md5('$usr_senha'), ").
		           ($usr_login ? "usr_login='$usr_login'" : "usr_login=null") . "," .
		           ($usr_email ? "usr_email='$usr_email'" : "usr_email=null") . "," .
		           ($usr_funcao ? "usr_funcao='$usr_funcao'" : "usr_funcao=null") . "," .
		           ($usr_msn ? "usr_msn='$usr_msn'" : "usr_msn=null") . "," .
		           ($uni_codigo ? "uni_codigo='$uni_codigo'" : "uni_codigo=null") . "," .
		           ($set_codigo ? "set_codigo='$set_codigo'" : "set_codigo=null") . "," .
		           ($usr_requisicao ? "usr_requisicao='$usr_requisicao'" : "usr_requisicao=null") . "," .
		           ($usr_consolidacao_automatica ? "usr_consolidacao_automatica='$usr_consolidacao_automatica'" : "usr_consolidacao_automatica=null") . ", ".
				   "usr_ativo = '$usr_ativo', 
				   usr_mestre = '$usr_mestre'," .   
				   ($usr_celular ? "usr_celular='$usr_celular'" : "usr_celular=null") . ", " .
				   "usr_transferencia = '$usr_transferencia',
				   usr_tipo_medico = '$usr_tipo_medico',
				   usr_num_conselho = '$usr_num_conselho',
				   usr_medico_cnes = '$usr_medico_cnes',
				   usr_carga_horaria = '$usr_carga_horaria',
				   usr_cpf = '$usr_cpf'
			 WHERE usr_codigo = '$usr_codigo'";
	$query = pg_query($sql) or die (pg_last_error());
	msg($id_login,$acao,$query);
}

//
//-> DEL <---------------------------------------------------------->

 if($acao=="del") {
	 reglog($id_login,"Exluindo Registro de USUARIOS $usr_codigo");

  $sql = pg_query("delete from usuarios where usr_codigo='$usr_codigo'");
msg($id_login,$acao,$sql);
}

?>
</fieldset>
