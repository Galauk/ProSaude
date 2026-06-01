<?php
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>

	session_start();
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	verauth($id_login);
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
//------------------------------------------------------------------>


//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
	 reglog($id_login,"Entrando em PERMISSOES_USUARIOS");
//------------------------------------------------------------------>

echo "<fieldset><legend>PERMISS�ES POR USU�RIO</legend>";
// if(empty($acao)) {
if( empty($acao) || $acao=='form_perm_usu' || $acao=='busca' )
{
	
   //$sql = pg_query("SELECT * FROM usuarios WHERE usr_ativo = 'S'");
   if($acao=="busca")
   {
      reglog($id_login,"Buscando em PERMISSOES_USUARIOS: $palavra_chave ");
      //$stmt = "SELECT * FROM usuarios WHERE usr_ativo = 'S' AND (usr_nome ILIKE '%$palavra_chave%' OR usr_nome ILIKE '%$palavra_chave%')";
      //$stmt = "SELECT * FROM usuarios WHERE (usr_nome ILIKE '%$palavra_chave%' OR usr_nome ILIKE '%$palavra_chave%')";
      
      $pc = '%'.strtoupper($palavra_chave).'%';
      
      // S� lista os usu�rios que n�o possuem cadastros !
      $stmt = "SELECT u.usr_codigo, u.usr_nome, usr_login
         FROM usuarios AS u
         LEFT JOIN usuarios_permissoes AS up ON up.usr_codigo = u.usr_codigo 
         LEFT JOIN permissoes AS p ON up.perm_codigo = p.perm_codigo
         WHERE UPPER(usr_nome) LIKE '$pc' OR UPPER(usr_login) LIKE '$pc'         
         GROUP BY u.usr_codigo, u.usr_nome, usr_login
         --HAVING COUNT(up.perm_codigo) > 0
         ORDER BY usr_nome";
       //  echo $stmt;
      $sql = db_query($stmt);

		if(strlen($palavra_chave)<="2") {
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
		$str = str_replace("+","%",$palavra_chave);
		$pos = strpos($palavra_chave,"+");
		if($pos=="0") {
			$v1=1;
		} else {
			$v1=2;
		}
   }
   else
   {
      $stmt = "SELECT u.usr_codigo, u.usr_nome, usr_login
         FROM usuarios AS u
         LEFT JOIN usuarios_permissoes AS up ON up.usr_codigo = u.usr_codigo 
         LEFT JOIN permissoes AS p ON up.perm_codigo = p.perm_codigo
         GROUP BY u.usr_codigo, u.usr_nome, usr_login
         HAVING COUNT(up.perm_codigo) > 0
         ORDER BY usr_nome";
      
      $sql = pg_query($stmt);
	  
   }
   //-> Botoes
   echo "<fieldset>
	    <legend>Op��es</legend>
	       <a href=".$_SESSION[linkroot].$_SESSION[modulo]."zf/usuarios/usuarios><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>
	       ".ChmodBtn(	$id_login,'adicionar','permissoes_usuarios.php?acao=form_add')."";
		   if(SelPerm($id_login,'copiar_permissoes.php') != "0") {
		 		echo "<a href=copiar_permissoes.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/copiar_perm.png border=0></a>";
		   } else {
				echo " <img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/copiar_permissoes_off.jpg border=0>";
		   }
		   echo "<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
			  <tr>";
                              if (chmodbtn($id_login,"procurar_if","permissoes_usuarios.php"))
                              {
                                 echo "<form method=post action=$PHP_SELF>";
                              }
				
			      echo     "<input type=hidden name=acao value=busca>
					<input type=hidden name=id_login value=$id_login>
					<td width=30>Buscar:</td>
					<td width=120><input type=text name=palavra_chave class=box </td>
					<td>".ChmodBtn($id_login,'procurar','permissoes_usuarios.php')."</td>
					
				</form>
				
			  </tr>
			</table>

	   </fieldset>
	  <br>";

//
//-> Listando

		
            echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
                   <tr>
                    <td>
                     <fieldset>
                      <legend>Listando �ltimas <b>15</b> Permissoes/Usuarios Cadastrados</legend>
                       <table width=100% align=center cellspacing=2 cellpadding=4 border=0 class=\"lista\">
                        <tr bgcolor=F9f9f9>
                          <td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Usuario</td>
                          <td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Login</td>
                          <td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";
          
               while($row=pg_fetch_array($sql)) {
                 echo "<tr>
                         <td  style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[usr_nome]</td>
                         <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[usr_login]</td>
                         <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'editar','permissoes_usuarios.php?acao=form_edit&usr_cod='.$row[usr_codigo])."</td>
                         <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'apagar','permissoes_usuarios.php?acao=del&usr_cod='.$row[usr_codigo])."</td>
                       </tr>";
              }
             
	echo "</tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table>";
}

else if($acao=="form_add")
{
	 reglog($id_login,"Formulario de ADICAO PERMISSOES_USUARIOS");


//
//-> Abaixo sao os botoes de voltar / cadastro simples / cadastro completo

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Op��es de Cadastro</legend>
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

   //if(($type=="" OR $acao=="simples")) {
   
   echo "<form method=post action=$PHP_SELF>
	<input type=hidden name=acao value=add>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=type value=simples>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Cadastro de Permissoes por Usuario</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
		<td width=200 align=right><b>Usuario:</b></td>
		<td>";
       
      // S� lista os usu�rios que n�o possuem cadastros !
      $stmt = "SELECT u.usr_codigo, u.usr_nome, COUNT(up.perm_codigo)
         FROM usuarios AS u
         LEFT JOIN usuarios_permissoes AS up ON up.usr_codigo = u.usr_codigo 
         LEFT JOIN permissoes AS p ON up.perm_codigo = p.perm_codigo 
         GROUP BY u.usr_codigo, u.usr_nome
         HAVING COUNT(up.perm_codigo) = 0
         ORDER BY usr_nome";

      $query = db_query($stmt);
      
      if( pg_num_rows($query) == 0 )
      {
         print "<strong>Todos os usu&aacute;rios j&aacute; possuem permiss&otilde;es cadastradas !
         <br /> Por favor, edite as permiss&otilde;s do usu&aacute;rio desejado !
         </strong>";
         exit;
      }
      else
      {
         print "<select name=usr_codigo class=box style='width=650'>";
   
         //
         //-> SQL da Usuario do Sistema
         /*$query = pg_query("select usr_codigo, usr_nome from usuarios order by usr_nome ");
         while($usuario=pg_fetch_array($query)) {
            $usu_perm=pg_fetch_array(pg_query("select *from usuarios_permissoes"));
            if($usuario[usr_codigo]!=$usu_perm[usr_codigo]) {
               echo "<option value='$usuario[usr_codigo]'>$usuario[usr_nome]</option>";
            }
         }*/
         
         
         while( $usuario = pg_fetch_array($query) )
         {
            echo "<option value='$usuario[usr_codigo]'>$usuario[usr_nome]</option>";
         }
         
          echo "</select>
          <a href='javascript:;' class='info'>&nbsp;?&nbsp;
				<span>Se o usu&aacute;rio estiver cadastrado mas n&atilde;o aparecer aqui,
                &eacute; porqu&ecirc; ele j&aacute; possui permiss&otilde;s cadastradas !
                <br /> Por favor, edite as permiss&otilde;s do usu&aacute;rio desejado !
				</span>
			</a>
          ";
      }
      
      print "
	        </td>
	      </tr>
	</table>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	      <tr>
		<td><table width=100% cellspacing=2 cellpadding=4 border=0>";
	    //
	    //-> SQL das Permissoes
	    $query = "select perm_codigo, perm_descricao from permissoes order by perm_descricao ";
          $sql = pg_query($query);
	      $i=0;
	      while($permissoes=pg_fetch_array($sql)) {
	      
		$estilo = "style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'";
	       echo "<tr>
	   	      <td width=120 align=right $estilo><b><font color=blue>$permissoes[perm_descricao]</font></b></td>
			
			<td width=50 align=center $estilo><b><font color=green>
					Sim
				</font>
				<b>
				<input type='radio' name='sim_nao[$i]' value='S|$permissoes[perm_codigo]'>
			</td>

	   	      <td width=50 align=center $estilo><b><font color=red>N�o</font></b> <input type='radio' name='sim_nao[$i]' value='N|$permissoes[perm_codigo]' checked></td>
	   	      <td width=70 align=center $estilo><font color=blue>Inclus�o</font> <select name=incluir[] class=box><option value='S'>Sim</option><option value='N' selected>N�o</option></select></td>
	   	      <td width=70 align=center $estilo><font color=orange>Altera��o</font> <select name=alteracao[] class=box><option value='S'>Sim</option><option value='N' selected>N�o</option></select></td>
	   	      <td width=70 align=center $estilo><font color=red>Dele��o</font> <select name=delecao[] class=box><option value='S'>Sim</option><option value='N' selected>N�o</option></select></td>
	   	      <td width=70 align=center $estilo><font color=909090>Listagem</font> <select name=listagem[] class=box><option value='S'>Sim</option><option value='N' selected>N�o</option></select></td>
	   	      <td width=70 align=center $estilo><font color=507070>Buscar</font> <select name=buscar[] class=box><option value='S'>Sim</option><option value='N' selected>N�o</option></select></td>
		     </tr>";
                     $i++;
	      }
	   echo "</table></td>
	      </tr>
	      <tr>
	       <td colspan=5 align=center><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg></td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
   </table><br></form>";
   
   // }//fechamento do if
}
//}

//------------------------------------------------------------------>
//-> Formulario de Edicao de Conteudo
//------------------------------------------------------------------>
else if($acao=="form_edit")
{
	 reglog($id_login,"Formulario de EDICAO PERMISSOES_USUARIOS");
echo "<form method=post action=$PHP_SELF>
	<input type=hidden name=acao value=edit>
	<input type=hidden name=id_login value=$id_login>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Cadastro de Permissoes por Usuario</legend>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	      
	      <tr>
		<td><table width=100% cellspacing=2 cellpadding=4 border=0>";       
            // atualiza este usuario
            // add: Dudu 2007-08-31
            db_query( "SELECT atualiza_permissoes( $_GET[usr_cod] )", $log = false );
            
            
	    //-> SQL das Permissoes
        
	    $query = "SELECT * 
	    			FROM 	usuarios_permissoes as usuPerm, 
	    					permissoes as perm
				   WHERE usuPerm.perm_codigo = perm.perm_codigo 
				     AND usuPerm.usr_codigo = $_GET[usr_cod]
                   ORDER BY UPPER(perm.perm_descricao)";
                     
          $sql = pg_query($query) or die($query.pg_last_error());
	      $i=0;

	      $recuperaSuperior = pg_query("SELECT * FROM permissao_menu_superior WHERE usr_codigo = '$_GET[usr_cod]'") or die(pg_last_error());

	      $recuperaInferior = pg_query("SELECT * FROM permissao_menu_inferior WHERE usr_codigo = '$_GET[usr_cod]'") or die(pg_last_error());

	      $resultadoSuperior = pg_fetch_object($recuperaSuperior);
	      $resultadoInferior = pg_fetch_object($recuperaInferior);

	      // echo "<pre>";print_r($resultadoSuperior);die();
	      // echo "<pre>";print_r($resultadoInferior);die();
	      ?>
			<div>
				<fieldset>
					<legend>Menu Superior</legend>
					<div>
						<label for="cadastro">Cadastro : </label>
						Sim
						<input <?=$resultadoSuperior->menu_cadastro == 't' ? 'checked' : '' ?>
							type="radio" name="cadastro" id="cadastro" value = "true"> 
						N�o
						<input <?=$resultadoSuperior->menu_cadastro != 't' ? 'checked' : '' ?>
							type="radio" name="cadastro" id="cadastro" value = "false">
					</div>
					
					<div>
						<label for="Atendimentos">Atendimentos</label>
						Sim
						<input <?=$resultadoSuperior->menu_atendimentos == 't' ? 'checked' : '' ?> 
						 type="radio" name="Atendimentos" id="Atendimentos" value = "true">
						N�o
						<input <?=$resultadoSuperior->menu_atendimentos != 't' ? 'checked' : '' ?> 
						 type="radio" name="Atendimentos" id="Atendimentos" value = "false" >
					</div>

					<div>
						<label for="Agendamento">Agendamento</label>
						Sim
						<input <?=$resultadoSuperior->menu_agendamentos == 't' ? 'checked' : '' ?>  
						type="radio" name="Agendamento" id="Agendamento" value = "true">
						N�o
						<input <?=$resultadoSuperior->menu_agendamentos != 't' ? 'checked' : '' ?>  
						type="radio" name="Agendamento" id="Agendamento" value = "false" >
					</div>

					<div>
						<label for="LaboratorioSuperior">Laboratorio</label>
						Sim
						<input <?=$resultadoSuperior->menu_laboratorios == 't' ? 'checked' : '' ?>  
						 type="radio" name="LaboratorioSuperior" id="LaboratorioSuperior" value = "true">
						N�o
						<input <?=$resultadoSuperior->menu_laboratorios != 't' ? 'checked' : '' ?>  
						 type="radio" name="LaboratorioSuperior" id="LaboratorioSuperior" value = "false" >
					</div>

					<div>
						<label for="InternacaoSuperior">InternacaoSuperior</label>
						Sim
						<input <?=$resultadoSuperior->menu_internacao == 't' ? 'checked' : '' ?>  
						type="radio" name="InternacaoSuperior" id="InternacaoSuperior" value = "true">
						N�o
						<input <?=$resultadoSuperior->menu_internacao != 't' ? 'checked' : '' ?>  
						type="radio" name="InternacaoSuperior" id="InternacaoSuperior" value = "false" >
					</div>

					<div>
						<label for="MateriaisSuperior">MateriaisSuperior</label>
						Sim
						<input <?=$resultadoSuperior->menu_materiais == 't' ? 'checked' : '' ?>  
						 type="radio" name="MateriaisSuperior" id="MateriaisSuperior" value = "true">
						N�o
						<input <?=$resultadoSuperior->menu_materiais != 't' ? 'checked' : '' ?>  
						 type="radio" name="MateriaisSuperior" id="MateriaisSuperior" value = "false" >
					</div>

					<div>
						<label for="FarmaciaSuperior">FarmaciaSuperior</label>
						Sim
						<input <?=$resultadoSuperior->menu_farmacia == 't' ? 'checked' : '' ?>  
						 type="radio" name="FarmaciaSuperior" id="FarmaciaSuperior" value = "true">
						N�o
						<input <?=$resultadoSuperior->menu_farmacia != 't' ? 'checked' : '' ?>  
						 type="radio" name="FarmaciaSuperior" id="FarmaciaSuperior" value = "false" >
					</div>

					<div>
						<label for="Administrativo">Administrativo</label>
						Sim
						<input <?=$resultadoSuperior->menu_administrativo == 't' ? 'checked' : '' ?>  
						 type="radio" name="Administrativo" id="Administrativo" value = "true">
						N�o
						<input <?=$resultadoSuperior->menu_administrativo != 't' ? 'checked' : '' ?>  
						 type="radio" name="Administrativo" id="Administrativo" value = "false" >
					</div>

					<div>
						<label for="Transporte">Transporte</label>
						Sim
						<input <?=$resultadoSuperior->menu_transporte == 't' ? 'checked' : '' ?>  
						type="radio" name="Transporte" id="Transporte" value = "true">
						N�o
						<input <?=$resultadoSuperior->menu_transporte != 't' ? 'checked' : '' ?>  
						type="radio" name="Transporte" id="Transporte" value = "false" >
					</div>

					<div>
						<label for="ProgramasFederais">Programas Federais</label>
						Sim
						<input <?=$resultadoSuperior->menu_programas_federais == 't' ? 'checked' : '' ?>  
						type="radio" name="ProgramasFederais" id="ProgramasFederais" value = "true">
						N�o
						<input <?=$resultadoSuperior->menu_programas_federais != 't' ? 'checked' : '' ?>  
						type="radio" name="ProgramasFederais" id="ProgramasFederais" value = "false" >
					</div>
					
				</fieldset>
			</div>

			<div>
				<fieldset>
					<legend>Menu Inferiror</legend>

					<div>
						<label for="Pacientes">Pacientes</label>
						Sim
						<input <?=$resultadoInferior->menu_paciente == 't' ? 'checked' : '' ?> 
						type="radio" name="Pacientes" id="Pacientes" value="true">
						N�o
						<input <?=$resultadoInferior->menu_paciente != 't' ? 'checked' : '' ?> 
						type="radio" name="Pacientes" id="Pacientes" value="false">
					</div>
					
					<div>
						<label for="ESF">ESF</label>
						Sim
						<input <?=$resultadoInferior->menu_esf == 't' ? 'checked' : '' ?>  
						type="radio" name="ESF" id="ESF" value="true">
						N�o
						<input <?=$resultadoInferior->menu_esf != 't' ? 'checked' : '' ?>  
						type="radio" name="ESF" id="ESF" value="false">
					</div>

					<div>
						<label for="Laboratorio">Laboratorio</label>
						Sim
						<input <?=$resultadoInferior->menu_laboratorio == 't' ? 'checked' : '' ?>  
						type="radio" name="Laboratorio" id="Laboratorio" value="true">
						N�o
						<input <?=$resultadoInferior->menu_laboratorio != 't' ? 'checked' : '' ?>  
						type="radio" name="Laboratorio" id="Laboratorio" value="false">
					</div>

					<div>
						<label for="Internacao">Internacao</label>
						Sim
						<input <?=$resultadoInferior->menu_internacao == 't' ? 'checked' : '' ?>  
						type="radio" name="Internacao" id="Internacao" value="true">
						N�o
						<input <?=$resultadoInferior->menu_internacao != 't' ? 'checked' : '' ?>  
						type="radio" name="Internacao" id="Internacao" value="false">
					</div>

					<div>
						<label for="AgendamentoInferior">AgendamentoInferior</label>
						Sim
						<input <?=$resultadoInferior->menu_agendamento == 't' ? 'checked' : '' ?>  
						type="radio" name="AgendamentoInferior" id="AgendamentoInferior" value="true">
						N�o
						<input <?=$resultadoInferior->menu_agendamento != 't' ? 'checked' : '' ?>  
						type="radio" name="AgendamentoInferior" id="AgendamentoInferior" value="false">
					</div>

					<div>
						<label for="Farmacia">Farmacia</label>
						Sim
						<input <?=$resultadoInferior->menu_farmacia == 't' ? 'checked' : '' ?>  
						type="radio" name="Farmacia" id="Farmacia" value="true">
						N�o
						<input <?=$resultadoInferior->menu_farmacia != 't' ? 'checked' : '' ?>  
						type="radio" name="Farmacia" id="Farmacia" value="false">
					</div>

					<div>
						<label for="Materiais">Materiais</label>
						Sim
						<input <?=$resultadoInferior->menu_materiais == 't' ? 'checked' : '' ?>  
						type="radio" name="Materiais" id="Materiais" value="true">
						N�o
						<input <?=$resultadoInferior->menu_materiais != 't' ? 'checked' : '' ?>  
						type="radio" name="Materiais" id="Materiais" value="false">
					</div>

					<div>
						<label for="Vacinas">Vacinas</label>
						Sim
						<input <?=$resultadoInferior->menu_vacinas == 't' ? 'checked' : '' ?>  
						type="radio" name="Vacinas" id="Vacinas" value="true">
						N�o
						<input <?=$resultadoInferior->menu_vacinas != 't' ? 'checked' : '' ?>  
						type="radio" name="Vacinas" id="Vacinas" value="false">
					</div>

					<div>
						<label for="Relatorio">Relatorio</label>
						Sim
						<input <?=$resultadoInferior->menu_relatorios == 't' ? 'checked' : '' ?>  
						type="radio" name="Relatorio" id="Relatorio" value="true">
						N�o
						<input <?=$resultadoInferior->menu_relatorios != 't' ? 'checked' : '' ?>  
						type="radio" name="Relatorio" id="Relatorio" value="false">
					</div>

					<div>
						<label for="Prontuario">Prontuario</label>
						Sim
						<input <?=$resultadoInferior->menu_prontuario == 't' ? 'checked' : '' ?>  
						type="radio" name="Prontuario" id="Prontuario" value="true">
						N�o
						<input <?=$resultadoInferior->menu_prontuario != 't' ? 'checked' : '' ?>  
						type="radio" name="Prontuario" id="Prontuario" value="false">
					</div>

					<div>
						<label for="Usuarios">Usuarios</label>
						Sim
						<input <?=$resultadoInferior->menu_usuarios == 't' ? 'checked' : '' ?>  
						type="radio" name="Usuarios" id="Usuarios" value="true">
						N�o
						<input <?=$resultadoInferior->menu_usuarios != 't' ? 'checked' : '' ?>  
						type="radio" name="Usuarios" id="Usuarios" value="false">
					</div>

					<div>
						<label for="Email">Email</label>
						Sim
						<input <?=$resultadoInferior->menu_email == 't' ? 'checked' : '' ?>  
						type="radio" name="Email" id="Email" value="true">
						N�o
						<input <?=$resultadoInferior->menu_email != 't' ? 'checked' : '' ?>  
						type="radio" name="Email" id="Email" value="false">
					</div>

					<div>
						<label for="Chat">Chat</label>
						Sim
						<input <?=$resultadoInferior->menu_chat == 't' ? 'checked' : '' ?>  
						type="radio" name="Chat" id="Chat" value="true">
						N�o
						<input <?=$resultadoInferior->menu_chat != 't' ? 'checked' : '' ?>  
						type="radio" name="Chat" id="Chat" value="false">
					</div>
					
				</fieldset>
			</div>
	      <?
	    while($permissoes=pg_fetch_array($sql)) {
		//for($j=0;$j<=280;$j++){
	    	// echo "<pre>";print_r($permissoes);die();

			$estilo = "style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'";
			
			//echo "<input type=\"text\" name=\"temp1[$j]\" value=$permissoes[perus_codigo]>";
		echo "	<tr>
					<td width=160 align=right $estilo><b><font color=blue>$permissoes[perm_descricao]</font></b>";
					echo "<input type=\"hidden\" name=\"temp[$j]\" value=$permissoes[perus_codigo]>";
		echo "
					</td>
					<td width=50 align=center $estilo><b><font color=green>Sim</font><b>
						<input type='radio' name='sim_nao[$j]' value='S' ".($permissoes['perm_set']=='S' ? 'checked' : '').">	
					</td>
					<td width=50 align=center $estilo><b><font color=red>N�o</font></b> 
						<input type='radio' name='sim_nao[$j]' value='N' ".($permissoes['perm_set']=='N' ? 'checked' : '').">
					</td>
					
				</tr>";
			$j++;     
		  
	    }
		  
	   echo "</table></td>
	      </tr>
	      <tr>
	       <td colspan=5 align=center><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg></td>
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
else if($acao=="add")
{
   reglog($id_login,"Adicionando Registro em PERMISSOES_USUARIOS");
   
   $sql = '';
   
   for($i=0;$i<count($sim_nao);$i++)
   {
      $yorn_perm = explode ("|",$sim_nao[$i]);
      $perm_set = $yorn_perm[0];
      $perm_codigo = $yorn_perm[1];
      
      // se algum codigo for vazio, ele continua com o LOOP
      // nao vamos criar uma matriz esparsa, certo ? [=
      if( empty($perm_codigo) ) continue;
      
      $sql .= "insert into usuarios_permissoes ( " .
      "usr_codigo, " .
      "perm_codigo, " .
      "perm_set " .
      
      ") values ( " .
      ($usr_codigo ? "'$usr_codigo'" : "null") . ", " .
      ($perm_codigo ? "'$perm_codigo'" : "null") . ", " .
      //valor DEFAULT vai ser 'N'
      //($perm_set ? "'$perm_set'" : "null") . ", " .
      ($perm_set ? "'$perm_set'" : "'N'") . " " .
      
      ");";
   }
   //print $sql;
   $qq = db_query($sql);
   msg($id_login,$acao,$sql);
}
//
//-> EDIT <--------------------------------------------------------->
else if($acao=="edit")
{
//echo "<pre>".print_r($_POST,1);DIE();
	reglog($id_login,"Editando PERMISSOES_USUARIOS $perus_codigo");
	$usr_codigo = $_GET['usr_cod'];
	// echo "<pre>";print_r($_POST);die();

	$result = pg_query("SELECT * from permissao_menu_superior where usr_codigo = '$usr_codigo'") or die(pg_last_error());

   	$respostaDaQuery = pg_num_rows($result);

	if ($respostaDaQuery > 0) {
		pg_query("UPDATE permissao_menu_superior set menu_cadastro = '$_POST[cadastro]' ,menu_atendimentos = '$_POST[Atendimentos]',menu_agendamentos = '$_POST[Agendamento]',menu_laboratorios = '$_POST[LaboratorioSuperior]' ,menu_internacao = '$_POST[InternacaoSuperior]' ,menu_materiais = '$_POST[MateriaisSuperior]',menu_farmacia = '$_POST[FarmaciaSuperior]',menu_administrativo = '$_POST[Administrativo]',menu_transporte = '$_POST[Transporte]',menu_programas_federais = '$_POST[ProgramasFederais]' where usr_codigo = '$usr_codigo'") or die(pg_last_error());
	} else{
		pg_query("INSERT INTO permissao_menu_superior (usr_codigo, menu_cadastro ,menu_atendimentos ,menu_agendamentos ,menu_laboratorios ,menu_internacao ,menu_materiais ,menu_farmacia ,menu_administrativo ,menu_transporte ,menu_programas_federais ) VALUES ('$usr_codigo', '$_POST[cadastro]', '$_POST[Atendimentos]','$_POST[Agendamento]','$_POST[LaboratorioSuperior]','$_POST[InternacaoSuperior]', '$_POST[MateriaisSuperior]', '$_POST[FarmaciaSuperior]', '$_POST[Administrativo]', '$_POST[Transporte]', '$_POST[ProgramasFederais]') ") or die(pg_last_error());
	}

	$resultInferior = pg_query("SELECT * from permissao_menu_inferior where usr_codigo = '$usr_codigo'") or die(pg_last_error());



   	$respostaDaQueryInferior = pg_num_rows($resultInferior);

	if ($respostaDaQueryInferior > 0) {
		pg_query("UPDATE permissao_menu_inferior set menu_paciente  = '$_POST[Pacientes]' ,menu_esf = '$_POST[ESF]',menu_laboratorio = '$_POST[Laboratorio]',menu_internacao = '$_POST[Internacao]',menu_agendamento = '$_POST[AgendamentoInferior]',menu_farmacia = '$_POST[Farmacia]',menu_materiais = '$_POST[Materiais]',menu_vacinas = '$_POST[Vacinas]' ,menu_relatorios = '$_POST[Relatorio]',menu_prontuario = '$_POST[Prontuario]', menu_usuarios = '$_POST[Usuarios]' , menu_email = '$_POST[Email]', menu_chat = '$_POST[Chat]'  where usr_codigo = '$usr_codigo'") or die(pg_last_error());
	} else{
		pg_query("INSERT INTO permissao_menu_inferior (	menu_paciente, menu_esf, menu_laboratorio, menu_internacao, menu_agendamento, menu_farmacia, menu_materiais, menu_vacinas, menu_relatorios, menu_prontuario,  menu_usuarios,  menu_email, menu_chat, usr_codigo ) VALUES ('$_POST[Pacientes]','$_POST[ESF]', '$_POST[Laboratorio]', '$_POST[Internacao]', '$_POST[AgendamentoInferior]', '$_POST[Farmacia]', '$_POST[Materiais]', '$_POST[Vacinas]', '$_POST[Relatorio]', '$_POST[Prontuario]', '$_POST[Usuarios]', '$_POST[Email]', '$_POST[Chat]', '$usr_codigo') ") or die(pg_last_error());
	}

   $sql = '';
   	
   for($i=0;$i<count($temp);$i++)
   {

      /*$sql .= "UPDATE usuarios_permissoes SET 
      perm_set = ".($sim_nao[$i] ? "'$sim_nao[$i]'" : "null").", 
      nivel_i = ".($incluir[$i] ? "'$incluir[$i]'" : "null").",
      nivel_a = ".($alteracao[$i] ? "'$alteracao[$i]'" : "null").", 
      nivel_d = ".($delecao[$i] ? "'$delecao[$i]'" : "null").", 
      nivel_l = ".($listagem[$i] ? "'$listagem[$i]'" : "null").", 
      nivel_b = ".($buscar[$i] ? "'$buscar[$i]'" : "null")."
      WHERE perus_codigo = $temp[$i];";*/
	  
	  if ($_POST["sim_nao"][$i] != "") { $sim_nao_n = "'".$_POST["sim_nao"][$i]."'"; } else { $sim_nao_n = "null"; }	  
	  if ($_POST["incluir"][$i] != "") { $incluir_n = "'".$_POST["incluir"][$i]."'"; } else { $incluir_n = "null"; }	  
	  if ($_POST["alteracao"][$i] != "") { $alteracao_n = "'".$_POST["alteracao"][$i]."'"; } else { $alteracao_n = "null"; }	  
	  if ($_POST["delecao"][$i] != "") { $delecao_n = "'".$_POST["delecao"][$i]."'"; } else { $delecao_n = "null"; }	  
	  if ($_POST["listagem"][$i] != "") { $listagem_n = "'".$_POST["listagem"][$i]."'"; } else { $listagem_n = "null"; }	  
	  if ($_POST["buscar"][$i] != "") { $buscar_n = "'".$_POST["buscar"][$i]."'"; } else { $buscar_n = "null"; }	  
	  
	  
	  $sql .= "UPDATE usuarios_permissoes SET 
      perm_set = ".$sim_nao_n."
      WHERE perus_codigo = $temp[$i];";
	  
   }
   
   //print $sql;
   $qq = db_query($sql);
   msg($id_login,$acao,$qq);
}

//
//-> DEL <---------------------------------------------------------->
else if($acao=="del")
{
	$usr_codigo = $_GET['usr_cod'];
	reglog($id_login,"Exluindo Registro de PERMISSOES_USUARIOS $usr_codigo");
	$stmt = "delete from usuarios_permissoes where usr_codigo='$usr_codigo'";
	$sql = db_query($stmt);
	msg($id_login,$acao,$sql);
}

?>
</fieldset>