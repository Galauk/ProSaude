<fieldset><legend>DISPENSAÇÃO</legend>
<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<script>
//itens_dispensacao.php
//	itens_dispensacao.php?id_login=$id_login&mov_codigo=$mov_codigo&acao=form_inclui_item&mov_data=$mov_data	 

function vaiplaneta() {
	var id = document.getElementById("id_login").value;
	var mov = document.getElementById("mov_codigo").value;
	var dt = document.getElementById("mov_data").value;
	var url = 'itens_dispensacao.php?acao=form_inclui_item&id_login='+id+'&mov_codigo='+mov+'&mov_data='+dt;
			setTimeout(function(){location.href=url, 10} ); 
}
</script>
<?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>

	session_start();
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	verauth($id_login);

	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
           
 echo monta_calendario();
//------------------------------------------------------------------>

?>

<script>
function pacientes(codigo,nome,nascimento,mae,cidade)
{
	document.amb.pac_nome.value=nome;
	document.amb.pac_codigo.value=codigo;
	document.amb.pac_nascimento.value=nascimento;
	document.amb.pac_mae.value=mae;
	document.amb.pac_cidade.value=cidade;
//	document.frm_atd.paccodigo.value = codigo;
}

function abre_hist()
{
	var usu_cod = document.amb.pac_codigo.value;
	window.open("list_medicamento_saida.php?id_login=<?=$id_login?>&usu_codigo="+usu_cod,null,"height=460,width=600,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
}
</script>

<?

//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
	 reglog($id_login,"Entrando em DISPENSACAO");
//------------------------------------------------------------------>

 if (empty($acao) OR ($acao == 'form_dispensa')) {

//
//-> Botoes
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
      <td width=95><!--".ChmodBtn($id_login,'adicionar','dispensacao.php?acao=form_add')."!--></td>";
                if (chmodbtn($id_login,"procurar_if","dispensacao.php"))
                {                
                    echo "<form method=post action=$PHP_SELF>";
                }
                echo "
		<input type=hidden name=acao value=busca>
                <input type=hidden name=id_login value=$id_login>
	       <td width=180 align=right>Buscar </td>
	       <td width=90><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
	       <td>".ChmodBtn($id_login,'procurar','dispensacao.php')."</td></form>
	       <td width=79><a href=farmacia.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";

//
//-> Listando
    if (chmodbtn($id_login,"listar_if","dispensacao.php"))
    {
        echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
               <tr>
                <td>
                 <fieldset>
                  <legend>Listando Últimas <b>15</b> Dispensacoes Cadastradas</legend>
                   <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
                    <tr bgcolor=F9f9f9>
                      <td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Data</td>
                      <td width=100 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Centro Estoc. Saida</td>
                      <td width=300 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Paciente</td>
                      <td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Num.Movimento</td>
                      <td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";
      
         //echo "<pre>".print_r($_SESSION[cod_setor],1);
         $sql=pg_query("select mov_codigo, a.set_nome as desc_saida, usu_nome,
                        to_char(mov_data,'DD/MM/YYYY') as mov_data , mov_codigo
                        from movimento, setor as a, usuario
                        where movimento.set_saida = a.set_codigo
                        and    movimento.usu_codigo = usuario.usu_codigo
                        and   mov_tipo = 'S' 
                        and   mov_saida = 'D'
						and set_saida in (select distinct s.set_codigo
										  from usuarios usr
										  join usuarios_setores us
											on us.usr_codigo=usr.usr_codigo
										  join setor s
											on s.set_codigo=us.set_codigo
										 where usr.usr_codigo = $id_login)
                        order by mov_codigo desc limit 15");
           //echo"<pre>".print_r($_SESSION,1);
           
           $controle = 0;
           while($row=pg_fetch_array($sql)) {
                      $c1 = "";
                      $c2 = "#F2F2F2";
                      
                      if ($controle == 0) {
                        $cor = $c1;
                        $controle++;
                      } else {
                        $cor = $c2;
                        $controle = 0;
                      }
             echo "<tr bgcolor='$cor'>
                     <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[mov_data]</td>
                     <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[desc_saida]</td>
                     <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[usu_nome]</td>
                     <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[mov_codigo]</td>
                     <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'editar','dispensacao.php?acao=form_edit&mov_codigo='.$row[mov_codigo])."</td>
                     <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'apagar','dispensacao.php?acao=del&mov_codigo='.$row[mov_codigo])."</td>
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
 reglog($id_login,"Buscando em DISPENSACAO: $palavra_chave ");

if(strlen($palavra_chave)<"1") {
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
	       <td width=79><a href=materiais.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
	       <td width=95><!--".ChmodBtn($id_login,'adicionar','dispensacao.php?acao=form_add')."!--></td>";
               if (chmodbtn($id_login,"procurar_if","dispensacao.php"))
               {
                    echo "<form method=post action=$PHP_SELF>";
               }
               echo "
		<input type=hidden name=acao value=busca>
                <input type=hidden name=id_login value=$id_login>
	        <td width=180 align=right>Buscar </td>
	        <td width=90><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
	        <td>".ChmodBtn($id_login,'procurar','dispensacao.php')."</td></form>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";

   $sqlv="select mov_codigo, set_nome as desc_saida, to_char(mov_data, 'dd/mm/yyyy') as mov_data, 
                  mov_data as mov_data2 , mov_codigo, usu_nome
                  from movimento, setor, usuario
                  where movimento.set_saida = setor.set_codigo 
                  and   movimento.usu_codigo = usuario.usu_codigo 
                  and   mov_tipo = 'S' 
                  and   mov_saida = 'D'
				  and set_saida in (select distinct s.set_codigo
										  from usuarios usr
										  join usuarios_setores us
											on us.usr_codigo=usr.usr_codigo
										  join setor s
											on s.set_codigo=us.set_codigo
										 where usr.usr_codigo = $id_login)
                  and   (usu_nome like upper('$palavra_chave%')
                  or    set_nome like upper('$palavra_chave%') 
                  or  mov_nr_nota = '$palavra_chave' ";
    if (strpos($palavra_chave, "/") != 0) 
       $sqlv .= "or mov_data = '$palavra_chave'"; 
       
    $sqlv .= ")
              order by usu_nome, mov_data2 ";
    $sql=pg_query($sqlv);          
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
		<td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Data</td>
		<td width=70 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Centro Estoc. Saida</td>
		<td width=300 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Paciente</td>
		<td width=30 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Num.Movim.</td>
		<td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";

     $controle = 0;
     while($row=pg_fetch_array($sql)) {
		$c1 = "";
		$c2 = "#F2F2F2";
		
		if ($controle == 0) {
		  $cor = $c1;
		  $controle++;
		} else {
		  $cor = $c2;
		  $controle = 0;
		}
       echo "<tr bgcolor='$cor'>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[mov_data]</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[desc_saida]</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[usu_nome]</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[mov_codigo]</td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'editar','dispensacao.php?acao=form_edit&mov_codigo='.$row[mov_codigo])."</td>
	       <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'apagar','dispensacao.php?acao=del&mov_codigo='.$row[mov_codigo])."</td>
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
	 reglog($id_login,"Formulario de ADICAO DISPENSACAO");

//
//-> Abaixo sao os botoes de voltar / cadastro simples / cadastro completo
  echo "<table width=760 align=center cellspacing=0 cellpadding=0 border=0>
  <form name='amb' method=post action=$PHP_SELF>
<input type=hidden name=id_login value=$id_login>
<input type=hidden name=acao value=form_add>
	 <tr>
	  <td><fieldset>
	      <legend>Dados do Paciente</legend>
		<table width=100% cellspacing=0 cellpadding=4 border=0>
		 <tr>
	    	  <td width=70 align=right>Numero do Paciente</td>
	    	  <td><input type=text name='pac_codigo' id='teste' class=boxl size=10 OnChange='executeAcao()' value='$pac_codigo' readonly></td>
	    	  <td align=right>Paciente</td>
	    	  <td><input type=text name=pac_nome class=boxl size=60 value='$pac_nome' readonly><a href='#' OnClick='window.open(\"list_pacientes.php?id_login=$id_login\",null,\"height=460,width=600,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes\");'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg align=absmiddle border=0></a> &nbsp;<a href='#' OnClick='abre_hist()'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/historico_on.jpg align=absmiddle border=0></a></td>
		  <td>Nascimento</td>
	    	  <td><input type=text name=pac_nascimento class=boxl size=15 value='$pac_nascimento' readonly></td>
		 </tr>
		 </table>
		<table width=100% cellspacing=0 cellpadding=4 border=0>
		 <tr>
	    	  <td width=70 align=right>Mãe</td>
	    	  <td width=100><input type=text name=pac_mae class=boxl size=50 value='$pac_mae' readonly></td>
	    	  <td width=40 align=right>Cidade</td>
	    	  <td width=60><input type=text name=pac_cidade class=boxl size=23 value='$pac_cidade' value='$pac_cidade' readonly></td>
		  <td><a href='#' OnClick='window.open(\"paciente_ficha.php?acao=form_add\",null,\"height=460,width=500,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes\");'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/ficha_on.jpg border=0></a></td>";
   echo "<td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/enviar_on.jpg></td>";
    echo "</tr>
	        </table>
	      </fieldset>
	  </td>
	 </tr>
	</table></form>";


//
//-> Este if esta apontando quando a acao for vazia ele vai mostrar o cadastro simples
//   no cadastro completo vc vai ter que passar a variavel acao para completo

 if(($type=="" OR $acao=="simples")) {
  echo "<form method=post action=$PHP_SELF>
	<input type=hidden name=acao value=add>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=usu_codigo value=$pac_codigo>
	<input type=hidden name=type value=simples>

	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Cabecalho da Dispensacao</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>";
        $sqlsaida=pg_query("select nextval('seq_mov_codigo'::text) as novo_codigo");
        $rowsaida = pg_fetch_array($sqlsaida);
        echo "<input type=hidden name=mov_codigo value=$rowsaida[novo_codigo]>";
     $sqldata_hora = pg_query("select to_char(current_date, 'dd/mm/yyyy') as data, extract(hour from current_time) || ':' || extract(minute from current_time) as hora");
     $rowdata_hora = pg_fetch_array($sqldata_hora);
     $mov_data = $rowdata_hora['data'];
     $mov_dt_nota = $rowdata_hora['data'];
          echo "
	      <tr>
		<td width=70>Centro Estoc. de Saida:</td>
		<td>
		 <select name=set_saida class=box>";
	    //
	    //-> SQL da Centro Estoc. 
	    $query = pg_query("select set_codigo, set_nome 
                          from setor where set_estoque = 'S' order by set_nome ");
	      while($setor=pg_fetch_array($query)) {
	       echo "<option value='$setor[set_codigo]'>$setor[set_nome]</option>";
	      }
	   echo "</select>
	        </td>
	      </tr>
	     <tr>
     		<td width=40>Numero de Dispensacao:</td>
    		<td><input type=text name=mov_nr_nota class=box size=20 value=$rowsaida[novo_codigo]></td>
         </tr>
	     <tr>
     		<td width=40>Data de Dispensacao:</td>
    		<td>
            <table cellspacing=0 cellpadding=0 border=0>
                <tr>
                    <td width=10><input type=text name=mov_data id=mov_data class=box size=20 value=$mov_data onKeypress=\"return Ajusta_Data(this, event); \"></td>
                    <td>&nbsp;<input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/calendario.png onclick=\"abrirCalendario('mov_data');return false;\"></td>
                </tr>
            </table>
         </tr>
	     <tr>
		<td width=40>Observacao:</td>
            <td colspan=3><textarea name=mov_observacao class=box cols=100 rows=2></textarea></td> 
	      </tr>
	      <tr>
	       <td colspan=2><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg></td>
	       <td width=79><a href=materiais.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
	      </tr>

	  </td>
	 </tr>
        </table><br></form>";
 }//fechamento do if - acao = simples
#-------------------------------------------------------------
# BUSCA DE PACIENTES                                        >>
#-------------------------------------------------------------
if($action =="buscar")
{
	$sql=pg_query("select usu_nome, to_char(usu_datanasc, 'dd/mm/yyyy') as usu_datanasc, usu_codigo, usu_mae 
			from usuario where usu_nome like '$palavra%' order by usu_nome");
	if(pg_num_rows($sql)=="0")
	{
		$results="<b><font color=red>Nenhum Usuario encontrado com: 
			<font color=blue>\"$palavra\"</font></font></b>";
	}
	if(pg_num_rows($sql)=="1")
	{
		$results="<b><font color=red>Encontrado ".pg_num_rows($sql)." Usuario com o Nome: 
			<font color=blue>\"$palavra\"</font></font></b>";
	}
	if(pg_num_rows($sql)>"1")
	{
		$results="<b><font color=red>Encontrados ".pg_num_rows($sql)." Usuarios com o Nome: 
			<font color=blue>\"$palavra\"</font></font></b>";
	}
	echo "<fieldset>
		<legend><font size=2>Lista de Usuarios $results</font></legend>";
	echo "<table width=100% cellspacing=0 cellpadding=0 border=0>
		<tr>
		<td>";
	if($palavra=="")
	{
		echo "<table width=100% cellspacing=0 cellpadding=0 border=0>
		<tr>
		<td align=center><font size=2 color=blue>Nenhum Usuario Localizado</font></td>
		</tr>
		</table>";
	}
		echo "<table width=100% cellspacing=2 cellpadding=3 border=0>
		<tr bgcolor=c9c9c9>
		<td>Prontuario</td>
		<td width=40%>Nome</td>
		<td width=10%>Data Nascim.</td>
		<td width=40%>Mae</td>
		<td>&nbsp;</td>
		</tr>";
	while($row=pg_fetch_array($sql))
	{
		echo "<tr>
			<form name=forpaciente method=post action=$PHP_SELF>
			<input type=hidden name=id_login value=$id_login>
			<input type=hidden name=acao value=form_add>
			<input type=hidden name=action value=addpaciente>
			<input type=hidden name=paciente value='$row[usu_codigo]'>
			<td width=11% style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'> 
			<font color=red></font>&nbsp;<b>$row[usu_same]</b></td>
			<td width=40% style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>
			<font color=red><b>$row[usu_nome]</b></font></td>
			<td width=14% style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>
			<font color=red><b>$row[usu_datanasc]</b></font></td>
			<td width=40% style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>
			<font color=red><b>$row[usu_mae]</b></font></td>";
		echo "<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/add_on.gif></td>";
		echo "</tr></form>";
	
	} //-> FIM ACTION
	echo "</table>";
	
	echo "</td>";
}//fim do if
}
//}

//------------------------------------------------------------------>
//-> Formulario de Edicao de Conteudo
//------------------------------------------------------------------>

 if($acao=="form_edit") {
	 reglog($id_login,"Formulario de EDICAO DISPENSACAO");

//
//-> Formulario de edicao do cadastro SIMPLES
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
         <tr>
          <td>
           <fieldset>
            <legend>Opções de Cadastro</legend>
             <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
              <tr>
               <td width=79><a href=dispensacao.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
               <td>&nbsp;</td>
              </tr>
             </table>
           </fieldset>
          </td>
         </tr>
        </table><br>";

//
//-> Pegando as informcoes do banco pra mostrar no formulario
 $sqlmovimento =  pg_query("select mov_codigo, to_char(mov_data, 'dd/mm/yyyy') as mov_data, mov_tipo, 
                          mov_desconto, mov_observacao, usu_codigo, 
                          retorna_usuario(usr_codigo) as login_usuario, 
                          set_saida, mov_nr_nota, to_char(mov_dt_nota, 'dd/mm/yyyy') as mov_dt_nota,
                          to_char(mov_data_inclusao, 'dd/mm/yyyy') as mov_data_inclusao, mov_total_nota
                          from movimento
                   where  mov_codigo = '$mov_codigo'");
  $row = pg_fetch_array($sqlmovimento);                 

  echo "<br><br><form method=post action=$PHP_SELF name='movimento'>
	<input type=hidden name=acao value=edit>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=mov_codigo value=$mov_codigo>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Dispensacao de Medicamentos</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=5 border=0>";
         echo "Ultima Alteracao: $row[login_usuario] - $row[mov_data_inclusao]";
        $sqlpaciente=pg_query("select usu_nome, to_char(usu_datanasc, 'dd/mm/yyyy') as usu_datanasc, usu_codigo, usu_mae 
                from usuario where usu_codigo = $row[usu_codigo]");
        $rowpaciente = pg_fetch_array($sqlpaciente);        
         echo "<input type=hidden name=usu_codigo value='$rowpaciente[usu_codigo]'>
                <tr> 
		          <td width=70 align='right'  style='border-bottom:1px solid;border-color:#bababa' bgcolor='#ebebeb'>Dados do Paciente:&nbsp;</td>
		          <td width=10  style='border-bottom:1px solid;border-color:#bababa' bgcolor='#ebebeb'><input type=text readonly name=usu_same size=10 value='$rowpaciente[usu_same]' class=box></td> 
		          <td  style='border-bottom:1px solid;border-color:#bababa' bgcolor='#ebebeb'><input type=text readonly name=usu_nome   size=70 value='$rowpaciente[usu_nome]' class=box></td>
               </tr>
			   
               <tr>
		          <td width=70 style='border-bottom:1px solid;border-color:#bababa' bgcolor='#ebebeb'>&nbsp;</td>
		          <td width=20 style='border-bottom:1px solid;border-color:#bababa' bgcolor='#ebebeb'><input type=text readonly name=usu_datanasc size=20 value='$rowpaciente[usu_datanasc]' class=box></td>
		          <td style='border-bottom:1px solid;border-color:#bababa' bgcolor='#ebebeb'><input type=text readonly name=usu_mae size=70 value='$rowpaciente[usu_mae]' class=box></td>
               </tr>";
        $sqlsetor=pg_query("select set_codigo, set_nome from setor where set_codigo = '$row[set_saida]'");
        $rowsetor = pg_fetch_array($sqlsetor);
		
         echo "<input type=hidden name=set_saida value='$rowsetor[set_codigo]'>
                <tr> 
		          <td width=70 align=right>Centro Estocador:&nbsp;</td>
		          <td width=10><input type=text readonly name=setor size=10 value='$rowsetor[set_codigo]'></td> 
		          <td><input type=text readonly name=set_desc size=70 value='$rowsetor[set_nome]'></td> 
               </tr>
	     <tr>
     		<td width=40 align=right>Numero de Dispensacao:</td>
    		<td colspan='2'><input type=text name=mov_nr_nota class=box size=20 value=$row[mov_codigo]></td>
         </tr>
	      <tr>
		<td width=130 align=right>Data da Dispensacao:</td>
		<td colspan=2><input type=text name=mov_data class=box size=20 id='mov_data' value='$row[mov_data]' onKeypress=\"return Ajusta_Data(this, event); \"></td>
	      </tr>
	     <tr>
		<td width=40 align=right>Observacao:</td>
            <td colspan=2><textarea name=mov_observacao class=box cols=100 rows=2>$row[mov_observacao]</textarea></td> 
	      </tr>
	      <tr>
	       <td></td>
	       <td width=79 colspan=2>
<img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg border=0 onclick='vaiplaneta()'></td>
	      </tr>
	     </table>

	   </fieldset>
	  </td>
	 </tr>
        </table><br>
<input type=hidden name=id_login id='id_login' value=$id_login>
<input type=hidden name=mov_codigo id='mov_codigo' value=$mov_codigo>
<input type=hidden name=acao id='acao' value='form_inclui_item'></form>";

}

//------------------------------------------------------------------>
//-> SQL's
//------------------------------------------------------------------>
//
//-> ADD <---------------------------------------------------------->
/*
 if($acao=="add") {
	 reglog($id_login,"Adicionando Registro em DISPENSACAO");


    $tipo_movimento = 'S';
    $mov_saida = 'D';
    $sql = pg_query("insert into movimento ( " .
            "mov_codigo, " .
            "mov_data, " .
            "mov_tipo, " .
            "mov_saida, " .
            "usu_codigo, " .
            "mov_observacao, " .
            "set_saida, " .
            "mov_nr_nota, " .
            "mov_dt_nota, " .
            "usr_codigo, " .
            "mov_data_inclusao, " .
            "mov_ip, " .
            "mov_total_nota  " .
            ") values ( " .
            "$mov_codigo" . ", " .  //grava o codigo do movimento para que possa passar posteriormente para o outro formulÃ¡rio
            ($mov_data ? "'$mov_data'" : "null") . ", " .
            "'{$tipo_movimento}'" . ", " .  //tipo da movimentaÃ§Ã£o S - Saida 
            "'{$mov_saida}'" . ", " .  //tipo da movimentaÃ§Ã£o D - Dispensacao
            ($usu_codigo ? "'$usu_codigo'" : "null") . ", " .
            ($mov_observacao ? "'$mov_observacao'" : "null") . ", " .
            ($set_saida ? "'$set_saida'" : "null") . ", " .
            ($mov_nr_nota ? "'$mov_nr_nota'" : "null") . ", " .
            ($mov_dt_nota ? "'$mov_dt_nota'" : "null") . ", " .
            ($id_login ? "'$id_login'" : "null") . ", " . //Mover o login do usuario que esta usando
            "date(now())" . ", " .
            ($mov_ip ? "'$mov_ip'" : "null") . ", " . //Mover o ip do micro do que o usuario esta usando
            ($mov_total_nota ? "'$mov_total_nota'" : "null") . "  " . //Fazer update na gravacao da nota
            ")");
//msg($acao,$sql);
          echo "<SCRIPT LANGUAGE=\"JavaScript\">
                    setTimeout(\"location='itens_dispensacao.php?id_login=$id_login&mov_codigo=$mov_codigo'\", 0);
                </SCRIPT>";
}
*/
//
//-> EDIT <--------------------------------------------------------->

 if($acao=="edit") {
	 reglog($id_login,"Editando DISPENSACAO $mov_codigo");
//die($mov_data);
  $sql = pg_query("update movimento set " .
            ($mov_data ? "mov_data='$mov_data'" : "mov_data=null") . ", " .
            ($usu_codigo ? "usu_codigo='$usu_codigo'" : "usu_codigo=null") . ", " .
            ($mov_desconto ? "mov_desconto='$mov_desconto'" : "mov_desconto=null") . ", " .
            ($mov_observacao ? "mov_observacao='$mov_observacao'" : "mov_observacao=null") . ", " .
            ($set_saida ? "set_saida='$set_saida'" : "set_saida=null") . ", " .
            ($mov_nr_nota ? "mov_nr_nota='$mov_nr_nota'" : "mov_nr_nota=null") . ", " .
            ($mov_dt_nota ? "mov_dt_nota='$mov_dt_nota'" : "mov_dt_nota=null") . ", " .
            ($id_login ? "usr_codigo='$id_login'" : "usr_codigo=null") . ", " .
            "mov_data_inclusao = date(now()),  " . 
            ($mov_ip ? "mov_ip='$mov_ip'" : "mov_ip=null") . ", " .
            ($mov_total_nota ? "mov_total_nota='$mov_total_nota'" : "mov_total_nota=null") . "  " .
            "where mov_codigo='$mov_codigo'");

msg($id_login,$acao,$sql);
        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='itens_dispensacao.php?id_login=$id_login&mov_codigo=$mov_codigo'\", 0);
              </SCRIPT>";
}

//
//-> DEL <---------------------------------------------------------->

if($acao=="del")
{
    reglog($id_login,"Excluindo Registro de DISPENSACAO $mov_codigo");

    $sql = pg_query("delete from movimento where mov_codigo='$mov_codigo'");
    msg($id_login,$acao,$sql);
}

?>
</fieldset>
