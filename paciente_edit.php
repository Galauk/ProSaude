<?php
/**
 * adicionado campos:
 * - nome responsavel
 * - doc responsavel
 * - tipo doc responsavel
*/

// redirecionando...
header("Location: paciente.php?{$QUERY_STRING}");
exit;


//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>

session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
verauth($id_login);
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
cabecario();
//------------------------------------------------------------------>

//
//-> Abaixo sao os botoes de voltar / cadastro simples / cadastro completo

 $acao_btn = ( $acao != 'form_edit' ? 'form_add' : 'form_edit' );


  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções de Cadastro</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=79><a href='{$PHP_SELF}?id_login={$id_login}'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
	       <td width=115><a href='{$PHP_SELF}?id_login={$id_login}&acao={$acao_btn}&type=&usu_codigo={$usu_codigo}'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/cadastro_simples.jpg border=0></a></td>
	       <td width=127><a href='{$PHP_SELF}?id_login={$id_login}&acao={$acao_btn}&type=c&usu_codigo={$usu_codigo}'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/cadastro_completo.jpg border=0></a></td>
	       <td>&nbsp;</td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";

//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>

// se a pagina for redirecionada com uma '$palavra_chave', então fazer a busca...
if( ! empty($palavra_chave) && empty($acao) )
{
	$acao = 'busca';
}


if(empty($acao)) {

//
//-> Botoes
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
		<td width=200>".ChmodBtn($id_login,'adicionar','paciente.php?acao=form_add')."</td>
	       <form method=post action='$PHP_SELF?id_login=$id_login'>
		<input type=hidden name=acao value=busca>
	       <td width=30>Buscar:</td>
	       <td width=120><input type='text' name='palavra_chave' class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
	       <td>".ChmodBtn($id_login,'procurar','paciente.php')."</td></form>
	       <td width=107><a href='logoff.php?id_login=$id_login' target='_parent'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/sair.gif border=0></a></td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";

//
//-> Listando
  
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Listando Últimos <b>15</b> Pacientes Cadastrados</legend>
	     <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
	      <tr bgcolor=ffffff>
		<td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Prontuário</td>
		<td width=270 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Nome</td>
		<td width=20 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Sexo</td>
		<td width=50 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Dt. Nasc.</td>
		<td width=270 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>N. Mãe</td>
		<td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";

   $sql=pg_query("select usu_prontuario,usu_codigo,usu_nome,to_char(usu_datanasc,'DD/MM/YYYY') as usu_datanasc,usu_mae,usu_sexo from usuario order by usu_codigo desc limit 15");
     while($row=pg_fetch_array($sql)) {
       echo "<tr>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[usu_prontuario]</td>
	       <td width=270 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[usu_nome]</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[usu_sexo]</td>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[usu_datanasc]</td>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[usu_mae]&nbsp;</td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'editar','paciente.php?acao=form_edit&from='.$_GET[from].'&usu_codigo='.$row['usu_codigo'])."</td>
	       <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'apagar','paciente.php?acao=del&usu_codigo='.$row['usu_codigo'])."</td>
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
//-> Mostrando o resultado da busca
//------------------------------------------------------------------>

else if($acao=="busca") {
//
//-> Verificando Busca
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
		<td width=200>".ChmodBtn($id_login,'adicionar','paciente.php?acao=form_add')."</td>
	       <form method=post action=$PHP_SELF>
		<input type=hidden name=acao value=busca>
		<input type=hidden name=id_login value=$id_login>
	       <td width=30>Buscar:</td>
	       <td width=120><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
	       <td>".ChmodBtn($id_login,'procurar','paciente.php')."</td></form>
	       <td width=107><a href='logoff.php?id_login=$id_login' target='_parent'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/sair.gif border=0></a></td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";

$palavra_chave = strtoupper($palavra_chave);
$stmt = "select usu_prontuario,usu_codigo,usu_nome,to_char(usu_datanasc,'DD/MM/YYYY') as usu_datanasc,
	usu_mae,usu_sexo 
	from usuario 
	where ( upper( to_ascii(usu_nome) ) like to_ascii('$palavra_chave%') OR usu_prontuario like '$palavra_chave%')";
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
	      <tr bgcolor=ffffff>
		<td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Prontuário</td>
		<td width=270 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Nome</td>
		<td width=20 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Sexo</td>
		<td width=50 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Dt. Nasc.</td>
		<td width=270 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>N. Mãe</td>
		<td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";

     while($row=pg_fetch_array($sql)) {
       echo "<tr>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[usu_prontuario]</td>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[usu_nome]</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[usu_sexo]</td>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[usu_datanasc]</td>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[usu_mae]&nbsp;</td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'editar','paciente.php?acao=form_edit&from='.$_GET[from].'&usu_codigo='.$row['usu_codigo'].'&palavra_chave='.$palavra_chave)."</td>
	       <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'apagar','paciente.php?acao=del&usu_codigo='.$row['usu_codigo'])."</td>
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

//
//-> Este if esta apontando quando a acao for vazia ele vai mostrar o cadastro simples
//   no cadastro completo vc vai ter que passar a variavel acao para completo

	if( $type=="" || $type=="s" ) { //}|| $acao=="simples") ) {
 
  	echo "<form method=post action='$PHP_SELF?id_login=$id_login&palavra_chave=$palavra_chave'>
	<input type=hidden name=acao value=add>
	<input type=hidden name=type value=simples>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Cadastro Simples</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
		<td width=190>Nome:</td>
		<td><input type=text name=usu_nome class=box size=70></td>
	      </tr>
	      <tr>
		<td width=70>Nome da mãe:</td>
		<td><input type=text name=usu_mae class=box size=70></td>
	      </tr>
	      <tr>
		<td width=70>Data Nasc.: (dd/mm/yyyy)</td>
		<td><input type=text name=usu_datanasc class=box size=10></td>
	      </tr>
	      <tr>
		<td width=70>Rua:</td>
		<td><input type=text name=usu_end_rua class=box size=40></td>
	      </tr>
	      <tr>
		<td width=70>Número:</td>
		<td><input type=text name=usu_end_nr class=box size=6></td>
	      </tr>
	      <tr>
		<td width=70>Complemento:</td>
		<td><input type=text name=usu_end_compl class=box size=40></td>
	      </tr>
	      <tr>
		<td width=70>Bairro:</td>
		<td><input type=text name=usu_end_bairro class=box size=40></td>
	      </tr>
	      <tr>
		<td width=70>CEP:</td>
		<td><input type=text name=usu_end_cep class=box size=9></td>
	      </tr>
	      <tr>
		<td width=70>Estado:</td>
		<td>
		 <select name=uf class=box>";
	    //
	    //-> SQL do Estado
	    $query = pg_query("select * from estado order by uf_sigla");
	      while($uf=pg_fetch_array($query)) {
	       echo "<option value='$uf[uf_codigo]'>$uf[uf_sigla]</option>";
	      }
	   echo "</select>
	        </td>
	      </tr>
	      <tr>
		<td width=70>Cidade:</td>
		<td><input type=text name=usu_end_cidade class=box size=30></td>
	      </tr>
	      <tr>
		<td width=70>CISVIR:</td>
		<td><input type=text name=usu_cisvir class=box size=20></td>
	      </tr>
	      <tr>
		<td width=70>Prontuario:</td>
		<td><input type=text name=usu_prontuario class=box size=20 readonly></td>
	      </tr>
	      <tr>
		<td width=70>Sexo:</td>
		<td>
		 <select name=usu_sexo class=box>
		  <option value=M>Masculino</option>
		  <option value=F>Feminino</option>
		 </select>
	        </td>
	      </tr>
	
		<tr>
			<td><label for='usu_resp_nome'>Nome do Respons&aacute;vel</label></td>
			<td><input type='text' name='usu_resp_nome' id='usu_resp_nome' size='70' class='box' maxlength='70' /></td>
		</tr>
		<tr>
			<td><label for='usu_resp_doc_tipo'>Tipo de Documento do Respons&aacute;vel</label></td>
			<td>
				<select name='usu_resp_doc_tipo' id='usu_resp_doc_tipo' class='box'>
					<option>CPF</option>
					<option>RG</option>
					<option>CNPJ</option>
					<option>CNES</option>
					<option>outro</option>
				</select>
			</td>
		</tr>
		<tr>
			<td><label for='usu_resp_nome'>Documento do Respons&aacute;vel</label></td>
			<td><input type='text' name='usu_resp_doc' id='usu_resp_doc' size='15' maxlength='30' class='box' /></td>
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

 else //se acao = completo
 { 
  echo "<form method=post action='$PHP_SELF?id_login=$id_login&palavra_chave=$palavra_chave'>
	<input type=hidden name=acao value=add>
	<input type=hidden name=type value=completo>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Cadastro Completo</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
		<td width=190>Nome:</td>
		<td><input type=text name=usu_nome class=box size=70></td>
	      </tr>
	      <tr>
		<td width=70>Nome da mãe:</td>
		<td><input type=text name=usu_mae class=box size=70></td>
	      </tr>
	      <tr>
		<td width=70>Nome do Pai:</td>
		<td><input type=text name=usu_pai class=box size=70></td>
	      </tr>
      <tr>
		<td width=70>Data Nasc.:</td>
		<td><input type=text name=usu_datanasc class=box size=10></td>
	      </tr>
      <tr>
		<td width=70>Rua:</td>
		<td><input type=text name=usu_end_rua class=box size=40></td>
	      </tr>
	      <tr>
		<td width=70>Número:</td>
		<td><input type=text name=usu_end_nr class=box size=6></td>
	      </tr>
      <tr>
		<td width=70>Complemento:</td>
		<td><input type=text name=usu_end_compl class=box size=40></td>
	      </tr>
	      <tr>
		<td width=70>Bairro:</td>
		<td><input type=text name=usu_end_bairro class=box size=40></td>
	      </tr>
	      <tr>
		<td width=70>CEP:</td>
		<td><input type=text name=usu_end_cep class=box size=9></td>
	      </tr>
	      <tr>
		<td width=70>Estado:</td>
		<td>
		 <select name=uf class=box>";
	    //
	    //-> SQL do Estado
	    $query = pg_query("select * from estado order by uf_sigla");
	      while($uf=pg_fetch_array($query)) {
	       echo "<option value='$uf[uf_codigo]'>$uf[uf_sigla]</option>";
	      }
	   echo "</select>
	        </td>
	      </tr>
	      <tr>
		<td width=70>Cidade (Conv.):</td>
		<td><input type=text name=usu_end_cidade class=box size=30></td>
	      </tr>
	      <tr>
		<td width=70>Cidade:</td>
		<td>
		 <select name=cid_codigo_nasc class=box>";
	    //
	    //-> SQL da Cidade
#	    $query = pg_query("select * from cidade order by cid_nome");
#	      while($cidade=pg_fetch_array($query)) {
#	       echo "<option value='$cidade[cid_codigo]'>$cidade[cid_nome]</option>";
#	      }
	   echo "</select>
	        </td>
	      </tr>
	      <tr>
		<td width=70>CISVIR:</td>
		<td><input type=text name=usu_cisvir class=box size=20></td>
	      </tr>
	      <tr>
		<td width=70>Prontuario:</td>
		<td><input type=text name=usu_same class=box size=20></td>
	      </tr>
	      <tr>
		<td width=70>Sexo:</td>
		<td>
		 <select name=usu_sexo class=box>
		  <option value=M>Masculino</option>
		  <option value=F>Feminino</option>
		 </select>
	        </td>
	      </tr>
	      <tr>
		<td width=70>Fam&iacute;lia:</td>
		<td>
		 <select name=fam_codigo class=box>";
	    //
	    //-> SQL da Familia
	    $query = pg_query("select fam_codigo, fam_nr_ficha from familia order by fam_nr_ficha");
	      while($familia=pg_fetch_array($query)) {
	       echo "<option value='$familia[fam_codigo]'>$familia[fam_nr_ficha]</option>";
	      }
	   echo "</select>
	        </td>
	      </tr>
	      <tr>
		<td width=70>Observacao:</td>
		<td><input type=textarea name=usu_observacao class=box size=20></td>
	      </tr>
	      <tr>
		<td width=70>Situacao Familiar:</td>
		<td><input type=text name=usu_sit_familiar class=box size=20></td>
	      </tr>
	      <tr>
		<td width=70>Frequencia Escolar :</td>
		<td><input type=text name=usu_freq_escolar class=box size=20></td>
	      </tr>
	      <tr>
		<td width=70>Ocupacao:</td>
		<td><input type=text name=usu_ocupacao class=box size=30></td>
	      </tr>
	      <tr>
		<td width=70>Cod. CBO:</td>
		<td><input type=text name=usu_cbo_r class=box size=30></td>
	      </tr>
	      <tr>
		<td width=70>PIS/PASEP:</td>
		<td><input type=text name=usu_pis_pasep class=box size=20></td>
	      </tr>
	      <tr>
		<td width=70>CPF:</td>
		<td><input type=text name=usu_cpf class=box size=20></td>
	      </tr>
	      <tr>
		<td width=70>Cartao SUS (Temp.):</td>
		<td><input type=text name=usu_cartao_p_sus class=box size=15></td>
	      </tr>
	      <tr>
		<td width=70>Cartao SUS:</td>
		<td><input type=text name=usu_cartao_sus class=box size=15></td>
	      </tr>
	      <tr>
		<td width=70>Tipo Certidao:</td>
		<td><input type=text name=usu_tipo_certidao class=box size=15></td>
	      </tr>
	      <tr>
		<td width=70>Cartorio:</td>
		<td><input type=text name=usu_cert_cartorio class=box size=60></td>
	      </tr>
	      <tr>
		<td width=70>Livro:</td>
		<td><input type=text name=usu_cert_livro class=box size=10></td>
	      </tr>
	      <tr>
		<td width=70>Folha:</td>
		<td><input type=text name=usu_cert_lv_fls class=box size=10></td>
	      </tr>
	      <tr>
		<td width=70>Termo:</td>
		<td><input type=text name=usu_cert_termo class=box size=10></td>
	      </tr>
	      <tr>
		<td width=70>Data de Emissao:</td>
		<td><input type=text name=usu_cert_emissao class=box size=10></td>
	      </tr>
	      <tr>
		<td width=70>RG:</td>
		<td><input type=text name=usu_rg class=box size=15></td>
	      </tr>
	      <tr>
		<td width=70>RG-Orgao:</td>
		<td><input type=text name=usu_rg_compl class=box size=15></td>
	      </tr>
	      <tr>
		<td width=70>Rg-Estado:</td>
		<td>
		 <select name=uf_sigla_rg class=box>";
	    //
	    //-> SQL do Estado
	    $query = pg_query("select * from estado order by uf_sigla");
	      while($ufrg=pg_fetch_array($query)) {
	       echo "<option value='$ufrg[uf_codigo]'>$ufrg[uf_sigla]</option>";
	      }
	   echo "</select>
	        </td>
	      </tr>
	      <tr>
		<td width=70>RG-Data Emissao:</td>
		<td><input type=text name=usu_rg_dt_emissao class=box size=15></td>
	      </tr>
	      <tr>
		<td width=70>Cart.Trabalho:</td>
		<td><input type=text name=usu_ctps class=box size=10></td>
	      </tr>
	      <tr>
		<td width=70>Cart-Estado:</td>
		<td>
		 <select name=uf_sigla_ctps class=box>";
	    //
	    //-> SQL do Estado
	    $query = pg_query("select * from estado order by uf_sigla");
	      while($ufctps=pg_fetch_array($query)) {
	       echo "<option value='$ufctps[uf_codigo]'>$ufctps[uf_sigla]</option>";
	      }
	   echo "</select>
	        </td>
	      </tr>
	      <tr>
		<td width=70>Cart.Trabalho-Serie:</td>
		<td><input type=text name=usu_ctps_serie class=box size=10></td>
	      </tr>
	      <tr>
		<td width=70>Cart.Trabalho-Emissao:</td>
		<td><input type=text name=usu_ctps_dt_emissao class=box size=10></td>
	      </tr>
	      <tr>
		<td width=70>Tit. Eleitor:</td>
		<td><input type=text name=usu_tit_eleitor class=box size=14></td>
	      </tr>
	      <tr>
		<td width=70>Tit. Eleitor-Zona:</td>
		<td><input type=text name=usu_tit_eleitor_zona class=box size=10></td>
	      </tr>
	      <tr>
		<td width=70>Tit. Eleitor-Secao:</td>
		<td><input type=text name=usu_tit_eleitor_secao class=box size=10></td>
	      </tr>
	      
	      		<tr>
			<td><label for='usu_resp_nome'>Nome do Respons&aacute;vel</label></td>
			<td><input type='text' name='usu_resp_nome' id='usu_resp_nome' size='70' class='box' maxlength='70' /></td>
		</tr>
		<tr>
			<td><label for='usu_resp_doc_tipo'>Tipo de Documento do Respons&aacute;vel</label></td>
			<td>
				<select name='usu_resp_doc_tipo' id='usu_resp_doc_tipo' class='box'>
					<option>CPF</option>
					<option>RG</option>
					<option>CNPJ</option>
					<option>CNES</option>
					<option>outro</option>
				</select>
			</td>
		</tr>
		<tr>
			<td><label for='usu_resp_nome'>Documento do Respons&aacute;vel</label></td>
			<td><input type='text' name='usu_resp_doc' id='usu_resp_doc' size='15' maxlength='30' class='box' /></td>
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

 }//fechamento do else
}
//}

//------------------------------------------------------------------>
//-> Formulario de Edicao de Conteudo
//------------------------------------------------------------------>
else if($acao=="form_edit") {
//
//-> Formulario de edicao do cadastro SIMPLES

//
//-> Pegando as informcoes do banco pra mostrar no formulario
 $sqlusuario =
		"select usu_codigo, 
		usu_nome,usu_pai,
		usu_mae,uni_unidade,uni_origem,usu_fone,usu_celular,
		to_char(usu_datanasc, 'dd/mm/yyyy') as usu_datanasc,
		usu_end_rua, usu_end_nr, usu_end_compl, usu_end_bairro,
		usu_end_cep, cid_codigo_nasc, usu_same, usu_cisvir, 
		usu_sexo, fam_codigo, usu_observacao, usu_sit_familiar,
		usu_freq_escolar, usu_ocupacao, usu_cbo_r, usu_pis_pasep,
		usu_cpf, usu_cartao_p_sus, usu_cartao_sus, usu_tipo_certidao,
		usu_cert_cartorio, usu_cert_livro, usu_cert_lv_fls,usu_end_cidade, 
		usu_cert_termo, to_char(usu_cert_emissao, 'dd/mm/yyyy') as usu_cert_emissao,
		usu_rg, usu_rg_compl, uf_sigla_rg, 
		to_char(usu_rg_dt_emissao, 'dd/mm/yyyy') as usu_rg_dt_emissao,
		usu_ctps, uf_sigla_ctps, usu_ctps_serie, 
		to_char(usu_ctps_dt_emissao, 'dd/mm/yyyy') as usu_ctps_dt_emissao,
		usu_prontuario,usu_tit_eleitor, usu_tit_eleitor_zona, usu_tit_eleitor_secao,
		usu_resp_nome, usu_resp_doc_tipo, usu_resp_doc
		 ,usr1.usr_nome as usr_cad, usr2.usr_nome as usr_alt,
		 to_char(usr_cad_dt,'DD/MM/YYYY HH24:MI') as usr_cad_dt,
		to_char(usr_alt_dt,'DD/MM/YYYY HH24:MI') as usr_alt_dt
		FROM usuario as u
		LEFT JOIN usuarios as usr1 on u.usr_cad = usr1.usr_codigo
		LEFT JOIN usuarios as usr2 on u.usr_alt = usr2.usr_codigo
		where usu_codigo='$usu_codigo'";

	$sqlusuario = "SELECT u.* FROM usuario AS u
		LEFT JOIN usuarios as usr1 on u.usr_cad = usr1.usr_codigo
		LEFT JOIN usuarios as usr2 on u.usr_alt = usr2.usr_codigo
		WHERE usu_codigo='$usu_codigo'";
		
	 $row=pg_fetch_array(db_query($sqlusuario));


	print "	<p>Cadastrado por: \"<strong>{$row[usr_cad]}</strong>\" em <strong>{$row[usr_cad_dt]}</strong>".
		( ! empty($row['usr_alt']) ?
			" <br />&Uacute;ltima altera&ccedil;&atilde;o por: \"<strong>{$row[usr_alt]}</strong>\" 
				em: <strong>{$row[usr_alt_dt]}</strong>" : "" ).
		".</p>";

	if( empty($type) || $type == 's' )
	{

  	echo "<form method=post action='$PHP_SELF?&palavra_chave=$palavra_chave'>
	<input type=hidden name=acao value=edit>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=palavra_chave value='$palavra_chave'>
	<input type=hidden name=usu_codigo value=$usu_codigo>
	
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Cadastro Simples</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	     <tr>
		   <td width=190>Prontuario:</td>
		   <td><strong>$row[usu_prontuario]</strong></td>
	      </tr>
	      <tr>
		<td width=70>Nome:</td>
		<td><input type=text name=usu_nome class=box size=70 value='$row[usu_nome]'></td>
	      </tr>
	      <tr>
		<td width=70>Nome da mãe:</td>
		<td><input type=text name=usu_mae class=box size=70 value='$row[usu_mae]'></td>
	      </tr>
	      <tr>
		<td width=70>Nome da Pai:</td>
		<td><input type=text name=usu_pai class=box size=70 value='$row[usu_pai]'></td>
	      </tr>
	      <tr>
		<td width=70>Data Nasc.:</td>
		<td><input type=text name=usu_datanasc class=box size=10 value='$row[usu_datanasc]'></td>
	      </tr>
	      <tr>
		<td width=70>Rua:</td>
		<td><input type=text name=usu_end_rua class=box size=40 value='$row[usu_end_rua]'></td>
	      </tr>
	      <tr>
		<td width=70>Número:</td>
		<td><input type=text name=usu_end_nr class=box size=6 value='$row[usu_end_nr]'></td>
	      </tr>
	      <tr>
		<td width=70>Complemento:</td>
		<td><input type=text name=usu_end_compl class=box size=40 value='$row[usu_end_compl]'></td>
	      </tr>
	      <tr>
		<td width=70>Cartao SUS:</td>
		<td><input type=text name=usu_cartao_sus class=box size=20 value='$row[usu_cartao_sus]'></td>
	      </tr>
	      <tr>
		<td width=70>Bairro:</td>
		<td><input type=text name=usu_end_bairro class=box size=40 value='$row[usu_end_bairro]'></td>
	      </tr>
	      <tr>
		<td width=70>CEP:</td>
		<td><input type=text name=usu_end_cep class=box size=9 value='$row[usu_end_cep]'></td>
	      </tr>
	      <tr>
		<td width=70>Telefone:</td>
		<td><input type=text name=usu_fone class=box size=16 value='$row[usu_fone]'></td>
	      </tr>
	      <tr>
		<td width=70>Celular:</td>
		<td><input type=text name=usu_celular class=box size=16 value='$row[usu_celular]'></td>
	      </tr>
	      <tr>
		<td width=70>Cidade(Conv):</td>
		<td><input type=text name=usu_end_cidade class=box size=30 value='$row[usu_end_cidade]'></td>
	      </tr>
              <tr>
                <td width=70>Unidade (<font color=red>Paciente</font>):</td>
                <td>
                 <select name=uni_origem class=box>";
            //
            //-> SQL da UNIDADE
            $query = pg_query("select * from unidade order by uni_desc");
	        echo " <option value=''>---</option>";
              while($uni=pg_fetch_array($query)) {
               echo( $uni['uni_codigo'] == $row['uni_origem'] )?
               		"<option value='$uni[uni_codigo]' selected>$uni[uni_desc]</option>":
               		"<option value='$uni[uni_codigo]'>$uni[uni_desc]</option>";
              }
           echo "</select>
                </td>
              </tr>
              <tr>
                <td width=70>Unidade (<font color=red>Prontuario</font>):</td>
                <td>
                 <select name=uni_unidade class=box>";
		if($row[uni_unidade]=="pam") {
		$s1="selected";
		$s2="";
		$s3="";
		$s4="";
		$s5="";
		$s6="";
		$s7="";
		}
		if($row[uni_unidade]=="obito") {
		$s1="";
		$s2="selected";
		$s3="";
		$s4="";
		$s5="";
		$s6="";
		$s7="";
		}
		
		if($row[uni_unidade]=="escola da gestante") {
		$s1="";
		$s2="";
		$s3="selected";
		$s4="";
		$s5="";
		$s6="";
		$s7="";
		}
		
		if($row[uni_unidade]=="centro infantil") {
		$s1="";
		$s2="";
		$s3="";
		$s4="selected";
		$s5="";
		$s6="";
		$s7="";
		}
		if($row[uni_unidxade]=="natta") {
		$s1="";
		$s2="";
		$s3="";
		$s4="";
		$s5="selected";
		$s6="";
		$s7="";
		}
		
		if($row[uni_unidade]=="em transito") {
		$s1="";
		$s2="";
		$s3="";
		$s4="";
		$s5="";
		$s6="selected";
		$s7="";
		}
		
		if($row[uni_unidade]=="ubs central") {
		$s1="";
		$s2="";
		$s3="";
		$s4="";
		$s5="";
		$s6="";
		$s7="selected";
		}
		echo "<option value='ubs central' $s7>UBS CENTRAL</option>";
		echo "<option value=pam $s1>PAM</option>";
		echo "<option value=obito $s2>ÓBITO</option>";
		echo "<option value='escola da gestante' $s3>ESCOLA DA GESTANTE</option>";
		echo "<option value='centro infantil' $s4>CENTRO INFANTIL</option>";
		echo "<option value=natta $s5>NATTA</option>";
		echo "<option value='em transito' $s6>EM TRÂNSITO</option>";
		
		if($row[usu_sexo]=="M") { 
		$sx_1="selected";
		$sx_2="";
		} else {
		$sx_2="selected";
		$sx_1="";
		}
           echo "</select>
                </td>
              </tr>
	      <tr>
		<td width=70>CISVIR:</td>
		<td><input type=text name=usu_cisvir class=box size=20 value='$row[usu_cisvir]'></td>
	      </tr>
	      <tr>
		<td width=70>Prontuario:</td>
		<td><input type=text name=usu_prontuario class=box size=20  readonly value='$row[usu_prontuario]'></td>
	      </tr>
	      <tr>
		<td width=70>Sexo:</td>
		<td>
		 <select name=usu_sexo class=box>
		  <option value=M $sx_1>Masculino</option>
		  <option value=F $sx_2>Feminino</option>
		 </select>
	        </td>
	      </tr>
	      
	    <tr>
			<td><label for='usu_resp_nome'>Nome do Respons&aacute;vel</label></td>
			<td><input type='text' name='usu_resp_nome' id='usu_resp_nome' size='70' class='box' maxlength='70' value='$row[usu_resp_nome]' /></td>
		</tr>
		<tr>
			<td><label for='usu_resp_doc_tipo'>Tipo de Documento do Respons&aacute;vel</label></td>
			<td>
				<select name='usu_resp_doc_tipo' id='usu_resp_doc_tipo' class='box'>
					<option".( $row['usu_resp_doc_tipo'] == 'CPF' 	? ' selected' : '').">CPF</option>
					<option".( $row['usu_resp_doc_tipo'] == 'RG' 	? ' selected' : '').">RG</option>
					<option".( $row['usu_resp_doc_tipo'] == 'CNPJ' 	? ' selected' : '').">CNPJ</option>
					<option".( $row['usu_resp_doc_tipo'] == 'CNES' 	? ' selected' : '').">CNES</option>
					<option".( $row['usu_resp_doc_tipo'] == 'outro' ? ' selected' : '').">outro</option>
				</select>
			</td>
		</tr>
		<tr>
			<td><label for='usu_resp_nome'>Documento do Respons&aacute;vel</label></td>
			<td><input type='text' name='usu_resp_doc' id='usu_resp_doc' size='15' maxlength='30' class='box' value='$row[usu_resp_doc]' /></td>
		</tr>

	      <tr>
	       <td>&nbsp;</td>
	       <td>
	       	<a href=list_pacientes.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>
	       	&nbsp;&nbsp;
	       	<input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg></td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
    </table><br></form>";
    
    // type == 'c' (completo)
    } else {
    echo "<form method=post action='$PHP_SELF'>
	<input type='hidden' name='acao' value='edit'>
	<input type='hidden' name='id_login' value='$id_login'>
	<input type='hidden' name='usu_codigo' value='$usu_codigo'>
	<input type='hidden' name='palavra_chave' value='$palavra_chave'>
	<input type='hidden' name='tpbusca' value='$tpbusca'>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Cadastro Completo</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
         <tr>
		   <td width=190>Prontuario:</td>
		   <td><strong>$row[usu_prontuario]</strong></td>
	      </tr>
	      <tr>
		<td width=190>Nome:</td>
		<td><input type=text name=usu_nome class=box size=70 value='$row[usu_nome]'></td>
	      </tr>
	      <tr>
		<td width=70>Nome da mãe:</td>
		<td><input type=text name=usu_mae class=box size=70 value='$row[usu_mae]'></td>
	      </tr>
	      <tr>
		<td width=70>Nome do Pai:</td>
		<td><input type=text name=usu_pai class=box size=70 value='$row[usu_pai]'></td>
	      </tr>
	      <tr>
		<td width=70>Data Nasc.:</td>
		<td><input type=text name=usu_datanasc class=box size=15 value='$row[usu_datanasc]' maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\"></td>
	      </tr>
	      <tr>
		<td width=70>Rua:</td>
		<td><input type=text name=usu_end_rua class=box size=40 value='$row[usu_end_rua]'></td>
	      </tr>
	      <tr>
		<td width=70>Número:</td>
		<td><input type=text name=usu_end_nr class=box size=6 value='$row[usu_end_nr]'></td>
	      </tr>
            <tr>
                <td width=70>Telefone:</td>
                <td><input type=text name=usu_fone class=box size=10 value='$row[usu_fone]'></td>
              </tr>
              <tr>
                <td width=70>Celular:</td>
                <td><input type=text name=usu_celular class=box size=10 value='$row[usu_celular]'></td>
              </tr>
	      <tr>
		<td width=70>Complemento:</td>
		<td><input type=text name=usu_end_compl class=box size=40 value='$row[usu_end_compl]'></td>
	      </tr>
	      <tr>
		<td width=70>Cartao SUS:</td>
		<td><input type=text name=usu_cartao_sus class=box size=20 value='$row[usu_end_compl]'></td>
	      </tr>
	      <tr>
		<td width=70>Bairro:</td>
		<td><input type=text name=usu_end_bairro class=box size=40 value='$row[usu_end_bairro]'></td>
	      </tr>
	      <tr>
		<td width=70>CEP:</td>
		<td><input type=text name=usu_end_cep class=box size=9 value='$row[usu_end_cep]'></td>
	      </tr>
	      <tr>
		<td width=70>Cidade(Conv):</td>
		<td><input type=text name=usu_end_cidade class=box size=30 value='$row[usu_end_cidade]'></td>
	      </tr>
		  <tr>
		<td width=70>Unidade (<font color=red>Paciente</font>):</td>
		<td>
		 <select name=uni_origem class=box>";
	    //
	    //-> SQL da UNIDADE
	    $query = pg_query("select * from unidade order by uni_desc");
	      while($uni=pg_fetch_array($query)) {
	       echo ($uni[uni_codigo]==$row[uni_origem])?"<option value='$uni[uni_codigo]' selected>$uni[uni_desc]</option>":"<option value='$uni[uni_codigo]'>$uni[uni_desc]</option>";
	      }
	   echo "</select>
	        </td>
	      </tr>
	      <tr>
		<td width=70>Unidade (<font color=red>Prontuario</font>):</td>
		<td>
		 <select name=uni_unidade class=box>"; 
		 if($row[uni_unidade]=="pam") {
			$s1="selected";
			$s2="";
			$s3="";
			$s4="";
			$s5="";
			$s6="";
			$s7="";
			}
			
			if($row[uni_unidade]=="obito") {
			$s1="";
			$s2="selected";
			$s3="";
			$s4="";
			$s5="";
			$s6="";
			$s7="";
			}
			
			if($row[uni_unidade]=="escola da gestante") {
			$s1="";
			$s2="";
			$s3="selected";
			$s4="";
			$s5="";
			$s6="";
			$s7="";
			}
			
			if($row[uni_unidade]=="centro infantil") {
			$s1="";
			$s2="";
			$s3="";
			$s4="selected";
			$s5="";
			$s6="";
			$s7="";
			}
			
			if($row[uni_unidxade]=="natta") {
			$s1="";
			$s2="";
			$s3="";
			$s4="";
			$s5="selected";
			$s6="";
			$s7="";
			}
			
			if($row[uni_unidade]=="em transito") {
			$s1="";
			$s2="";
			$s3="";
			$s4="";
			$s5="";
			$s6="selected";
			$s7="";
			}
			
			if($row[uni_unidade]=="ubs central") {
			$s1="";
			$s2="";
			$s3="";
			$s4="";
			$s5="";
			$s7="selected";
			$s6="";
			}
		echo "<option value='ubs central' $s7>UBS CENTRAL</option>"; echo "<option value=pam $s1>PAM</option>"; echo "<option value=obito $s2>ÓBITO</option>"; echo "<option value='escola da gestante' $s3>ESCOLA DA GESTANTE</option>"; echo "<option value='centro infantil' $s4>CENTRO INFANTIL</option>"; echo "<option value=natta $s5>NATTA</option>"; echo "<option value='em transito' $s6>EM TRÂNSITO</option>";
	   echo "</select>
	        </td>
	      </tr>
	      <tr>
		<td width=70>CISVIR:</td>
		<td><input type=text name=usu_cisvir class=box size=20 value='$row[usu_cisvir]'></td>
	      </tr>
	      <tr>
		<td width=70>Sexo:</td>
		<td>"; 
		if($row[usu_sexo]=="F") {
		   $sx='selected';
   			$sx2='';
		} else {
   			$sx='';
   			$sx2='selected';
 		}
		 echo "<select name=usu_sexo class=box>
		  <option value='M' $sx2>Masculino</option>
		  <option value='F' $sx>Feminino</option>
		 </select>
	        </td>
	      </tr>
	      <tr>
		<td width=70>Fam&iacute;lia:</td>
		<td>
		 <select name=fam_codigo class=box>";
	    //
	    //-> SQL da Familia
	    $query = pg_query("select fam_codigo, fam_nr_ficha from familia order by fam_nr_ficha");
	      while($familia=pg_fetch_array($query)) {
	       echo ($familia[fam_codigo]==$row[fam_codigo])?"<option value='$familia[fam_codigo]' selected>$familia[fam_nr_ficha]</option>":"<option value='$familia[fam_codigo]'>$familia[fam_nr_ficha]</option>";
	      }
	   echo "</select>
	        </td>
	      </tr>
	      <tr>
		<td width=70>Observacao:</td>
		<td><input type=textarea name=usu_observacao class=box size=20 value='$row[usu_observacao]'></td>
	      </tr>
	      <tr>
		<td width=70>Situacao Familiar:</td>
		<td><input type=text name=usu_sit_familiar class=box size=20 value='$row[usu_sit_familiar]'></td>
	      </tr>
	      <tr>
		<td width=70>Frequencia Escolar :</td>
		<td><input type=text name=usu_freq_escolar class=box size='2' maxlength='1' value='$row[usu_freq_escolar]' ></td>
	      </tr>
	      <tr>
		<td width=70>Ocupacao:</td>
		<td><input type=text name=usu_ocupacao class=box size=30 value='$row[usu_ocupacao]'></td>
	      </tr>
	      <tr>
		<td width=70>Cod. CBO:</td>
		<td><input type=text name=usu_cbo_r class=box size=30 value='$row[usu_cbo_r]'></td>
	      </tr>
	      <tr>
		<td width=70>PIS/PASEP:</td>
		<td><input type=text name=usu_pis_pasep class=box size=20 value='$row[usu_pis_pasep]'></td>
	      </tr>
	      <tr>
		<td width=70>CPF:</td>
		<td><input type=text name=usu_cpf class=box size=20 value='$row[usu_cpf]' ></td>
	      </tr>
	      <tr>
		<td width=70>Cartao SUS (Temp.):</td>
		<td><input type=text name=usu_cartao_p_sus class=box size=15 value='$row[usu_cartao_p_sus]'></td>
	      </tr>
	      <tr>
		<td width=70>Cartao SUS:</td>
		<td><input type=text name=usu_cartao_sus class=box size=20 value='$row[usu_cartao_sus]' ></td>
	      </tr>
	      <tr>
		<td width=70>Tipo Certidao:</td>
		<td><input type=text name=usu_tipo_certidao class=box size=15 value='$row[usu_tipo_certidao]' ></td>
	      </tr>
	      <tr>
		<td width=70>Cartorio:</td>
		<td><input type=text name=usu_cert_cartorio class=box size=60 value='$row[usu_cert_cartorio]' ></td>
	      </tr>
	      <tr>
		<td width=70>Livro:</td>
		<td><input type=text name=usu_cert_livro class=box size=10 value='$row[usu_cert_livro]' ></td>
	      </tr>
	      <tr>
		<td width=70>Folha:</td>
		<td><input type=text name=usu_cert_lv_fls class=box size=10 value='$row[usu_cert_lv_fls]' ></td>
	      </tr>
	      <tr>
		<td width=70>Termo:</td>
		<td><input type=text name=usu_cert_termo class=box size=10 value='$row[usu_cert_termo]'></td>
	      </tr>
	      <tr>
		<td width=70>Data de Emissao:</td>
		<td><input type=text name=usu_cert_emissao class=box size=10 value='$row[usu_cert_emissao]'></td>
	      </tr>
	      <tr>
		<td width=70>RG:</td>
		<td><input type=text name=usu_rg class=box size=15 value='$row[usu_rg]' ></td>
	      </tr>
	      <tr>
		<td width=70>RG-Orgao:</td>
		<td><input type=text name=usu_rg_compl class=box size=15 value='$row[usu_rg_compl]'></td>
	      </tr>
	      <tr>
		<td width=70>Rg-Estado:</td>
		<td>
		 <select name=uf_sigla_rg class=box>";
	    //
	    //-> SQL do Estado
	    $query = pg_query("select * from estado order by uf_sigla");
	      while($ufrg=pg_fetch_array($query)) {
	       echo ($ufrg[uf_codigo]==$row[uf_sigla_rg])?"<option value='$ufrg[uf_codigo]' selected>$ufrg[uf_sigla]</option>":"<option value='$ufrg[uf_codigo]'>$ufrg[uf_sigla]</option>";
	      }
	   echo "</select>
	        </td>
	      </tr>
	      <tr>
		<td width=70>RG-Data Emissao:</td>
		<td><input type=text name=usu_rg_dt_emissao class=box size=15 value='$row[usu_rg_dt_emissao]'></td>
	      </tr>
	      <tr>
		<td width=70>Cart.Trabalho:</td>
		<td><input type=text name=usu_ctps class=box size=10 value='$row[usu_ctps]'></td>
	      </tr>
	      <tr>
		<td width=70>Cart-Estado:</td>
		<td>
		 <select name=uf_sigla_ctps class=box>";
	    //
	    //-> SQL do Estado
	    $query = pg_query("select * from estado order by uf_sigla");
	      while($ufctps=pg_fetch_array($query)) {
	       echo ($ufctps[uf_codigo]==$row[uf_sigla_ctps])?"<option value='$ufctps[uf_codigo]' selected>$ufctps[uf_sigla]</option>":"<option value='$ufctps[uf_codigo]'>$ufctps[uf_sigla]</option>";
	      }
	   echo "</select>
	        </td>
	      </tr>
	      <tr>
		<td width=70>Cart.Trabalho: S&eacute;rie:</td>
		<td><input type=text name=usu_ctps_serie class=box size=10 value='$row[usu_ctps_serie]' ></td>
	      </tr>
	      <tr>
		<td width=70>Cart.Trabalho: Data Emiss&atilde;o:</td>
		<td><input type=text name=usu_ctps_dt_emissao class=box size=10 value='$row[usu_ctps_dt_emissao]'></td>
	      </tr>
	      <tr>
		<td width=70>Tit. Eleitor:</td>
		<td><input type=text name=usu_tit_eleitor class=box size=14 value='$row[usu_tit_eleitor]' ></td>
	      </tr>
	      <tr>
		<td width=70>Tit. Eleitor-Zona:</td>
		<td><input type=text name=usu_tit_eleitor_zona class=box size=10 value='$row[usu_tit_eleitor_zona]'></td>
	      </tr>
	      <tr>
		<td width=70>Tit. Eleitor-Secao:</td>
		<td><input type=text name=usu_tit_eleitor_secao class=box size=10 value='$row[usu_tit_eleitor_secao]'></td>
	      </tr>
		
		<tr>
			<td><label for='usu_resp_nome'>Nome do Respons&aacute;vel</label></td>
			<td><input type='text' name='usu_resp_nome' id='usu_resp_nome' size='70' class='box' maxlength='70' value='$row[usu_resp_nome]' /></td>
		</tr>
		<tr>
			<td><label for='usu_resp_doc_tipo'>Tipo de Documento do Respons&aacute;vel</label></td>
			<td>
				<select name='usu_resp_doc_tipo' id='usu_resp_doc_tipo' class='box'>
					<option".( $row['usu_resp_doc_tipo'] == 'CPF' 	? ' selected' : '').">CPF</option>
					<option".( $row['usu_resp_doc_tipo'] == 'RG' 	? ' selected' : '').">RG</option>
					<option".( $row['usu_resp_doc_tipo'] == 'CNPJ' 	? ' selected' : '').">CNPJ</option>
					<option".( $row['usu_resp_doc_tipo'] == 'CNES' 	? ' selected' : '').">CNES</option>
					<option".( $row['usu_resp_doc_tipo'] == 'outro' ? ' selected' : '').">outro</option>
				</select>
			</td>
		</tr>
		<tr>
			<td><label for='usu_resp_nome'>Documento do Respons&aacute;vel</label></td>
			<td><input type='text' name='usu_resp_doc' id='usu_resp_doc' size='15' maxlength='30' class='box' value='$row[usu_resp_doc]' /></td>
		</tr>
		
		<tr>
	       <td>&nbsp;</td>
	       <td><a href='$PHP_SELF?id_login=$id_login&palavra_chave=$palavra_chave&tpbusca=$tpbusca'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>&nbsp;&nbsp;<input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg></td>
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
    $sql = pg_query("insert into usuario ( " .
            "usu_nome, " .
            "fam_codigo, " .
            "usu_observacao, " .
            "usu_sexo, " .
            "rac_codigo, " .
            "usu_datanasc, " .
            "cid_codigo_nasc, " .
            "usu_dt_entrada_pais, " .
            "ecd_codigo, " .
            "usu_sit_familiar, " .
            "usu_freq_escolar, " .
            "usu_ocupacao, " .
            "usu_cbo_r, " .
            "usu_pis_pasep, " .
            "usu_cpf, " .
            "usu_cartao_p_sus, " .
            "usu_cartao_sus, " .
            "usu_tipo_certidao, " .
            "usu_cert_cartorio, " .
            "usu_cert_livro, " .
            "usu_cert_lv_fls, " .
            "usu_cert_termo, " .
            "usu_cert_emissao, " .
            "usu_rg, " .
            "usu_rg_compl, " .
            "uf_sigla_rg, " .
            "usu_rg_emissor, " .
            "usu_rg_dt_emissao, " .
            "usu_ctps, " .
            "usu_ctps_serie, " .
            "uf_sigla_ctps, " .
            "usu_ctps_dt_emissao, " .
            "usu_tit_eleitor, " .
            "usu_tit_eleitor_zona, " .
            "usu_tit_eleitor_secao, " .
            "usu_mae, " .
            "usu_pai, " .
            "usu_cisvir, " .
            "usu_resp_nome, ".
            "usu_resp_doc_tipo, ".
            "usu_resp_doc, ".
            "usr_cad ".
            ") values ( " .
            "upper('$usu_nome'), " .
            ($fam_codigo ? "'$fam_codigo'" : "null") . ", " .
            ($usu_observacao ? "'$usu_observacao'" : "null") . ", " .
          "'$usu_sexo', " .
            ($rac_codigo ? "'$rac_codigo'" : "null") . ", " .
            ($usu_datanasc ? "'$usu_datanasc'" : "null") . ", " .
            ($cid_codigo_nasc ? "'$cid_codigo_nasc'" : "null") . ", " .
            ($usu_dt_entrada_pais ? "'$usu_dt_entrada_pais'" : "null") . ", " .
            ($ecd_codigo ? "'$ecd_codigo'" : "null") . ", " .
            ($usu_sit_familiar ? intval($usu_sit_familiar) : "null") . ", " .
            ($usu_freq_escolar ? "'$usu_freq_escolar'" : "null") . ", " .
            ($usu_ocupacao ? "'$usu_ocupacao'" : "null") . ", " .
            ($usu_cbo_r ? "'$usu_cbo_r'" : "null") . ", " .
            ($usu_pis_pasep ? "'$usu_pis_pasep'" : "null") . ", " .
            ($usu_cpf ? "'$usu_cpf'" : "null") . ", " .
            ($usu_cartao_p_sus ? "'$usu_cartao_p_sus'" : "null") . ", " .
            ($usu_cartao_sus ? "'$usu_cartao_sus'" : "null") . ", " .
            ($usu_tipo_certidao ? intval($usu_tipo_certidao) : "null") . ", " .
            ($usu_cert_cartorio ? "'$usu_cert_cartorio'" : "null") . ", " .
            ($usu_cert_livro ? "'$usu_cert_livro'" : "null") . ", " .
            ($usu_cert_lv_fls ? "'$usu_cert_lv_fls'" : "null") . ", " .
            ($usu_cert_termo ? "'$usu_cert_termo'" : "null") . ", " .
            ($usu_cert_emissao ? "'$usu_cert_emissao'" : "null") . ", " .
            ($usu_rg ? "'$usu_rg'" : "null") . ", " .
            ($usu_rg_compl ? "'$usu_rg_compl'" : "null") . ", " .
            ($uf_sigla_rg ? "'$uf_sigla_rg'" : "null") . ", " .
            ($usu_rg_emissor ? "'$usu_rg_emissor'" : "null") . ", " .
            ($usu_rg_dt_emissao ? "'$usu_rg_dt_emissao'" : "null") . ", " .
            ($usu_ctps ? "'$usu_ctps'" : "null") . ", " .
            ($usu_ctps_serie ? "'$usu_ctps_serie'" : "null") . ", " .
            ($uf_sigla_ctps ? "'$uf_sigla_ctps'" : "null") . ", " .
            ($usu_ctps_dt_emissao ? "'$usu_ctps_dt_emissao'" : "null") . ", " .
            ($usu_tit_eleitor ? "'$usu_tit_eleitor'" : "null") . ", " .
            ($usu_tit_eleitor_zona ? "'".substr($usu_tit_eleitor_zona,0,4)."'" : "null") . ", " .
            ($usu_tit_eleitor_secao ? "'".substr($usu_tit_eleitor_secao,0,4)."'" : "null") . ", " .
            ($usu_mae ? "upper('$usu_mae')" : "null") . ", " .
            ($usu_pai ? "upper('$usu_pai')" : "null") . ", " .
            ($usu_cisvir ? "'$usu_cisvir'" : "null") . ", " .
            ($usu_resp_nome ? "upper('$usu_resp_nome')" : "null") . ", " .
            ($usu_resp_doc_tipo ? "('$usu_resp_doc_tipo')" : "null") . ", " .
            ($usu_resp_doc ? "trim('$usu_resp_doc')" : "null") . ", " .
            intval($id_login) ." ".
            ")");

		//msg($id_login,$acao,$sql);
		print "
            <script type='text/javascript'>
            	var acao = \"document.location.href = '{$PHP_SELF}?id_login={$id_login}&palavra_chave={$palavra_chave}';\";
            	setTimeout( acao, 2500 );
            </script>
            <p class='aviso ok'>Paciente inserido com sucesso !</p>
            ";
}

//
//-> EDIT <--------------------------------------------------------->

 if($acao=="edit") {
	reglog($id_login,"Editando PACIENTE_EDIT $usu_codigo");
	$nome = strtoupper($usu_nome);
	#$sql = pg_query("update usuario set " .
	$stmt = "UPDATE usuario 
				SET usu_nome=upper('$usu_nome'), " .
		            ($fam_codigo ? "fam_codigo='$fam_codigo'" : "fam_codigo=null") . ", " .
		            ($usu_observacao ? "usu_observacao='$usu_observacao'" : "usu_observacao=null") . ", " .
		            ($usu_sexo ? "usu_sexo='$usu_sexo'" : "usu_sexo=null") . ", " .
		            ($usu_prontuario ? "usu_prontuario='$usu_prontuario'" : "usu_prontuario=null") . ", " .
		            ($usu_end_rua ? "usu_end_rua='$usu_end_rua'" : "usu_end_rua=null") . ", " .
		            ($usu_end_nr ? "usu_end_nr='$usu_end_nr'" : "usu_end_nr=null") . ", " .
		            ($usu_end_compl ? "usu_end_compl='$usu_end_compl'" : "usu_end_compl=null") . ", " .
		            ($usu_end_bairro ? "usu_end_bairro='$usu_end_bairro'" : "usu_end_bairro=null") . ", " .
		            ($usu_end_cep ? "usu_end_cep='$usu_end_cep'" : "usu_end_cep=null") . ", " .
		            ($usu_end_cidade ? "usu_end_cidade='$usu_end_cidade'" : "usu_end_cidade=null") . ", " .
		            ($usu_cidade_nasc ? "usu_cidade_nasc='$usu_cidade_nasc'" : "usu_cidade_nasc=null") . ", " .
		            ($usu_fone ? "usu_fone='$usu_fone'" : "usu_fone=null") . ", " .
		            ($usu_celular ? "usu_celular='$usu_celular'" : "usu_celular=null") . ", " .
		            ($usu_same ? "usu_same='$usu_same'" : "usu_same=null") . ", " .
		            ($rac_codigo ? "rac_codigo='$rac_codigo'" : "rac_codigo=null") . ", " .
		            ($usu_datanasc ? "usu_datanasc='$usu_datanasc'" : "usu_datanasc=null") . ", " .
		            ($cid_codigo_nasc ? "cid_codigo_nasc='$cid_codigo_nasc'" : "cid_codigo_nasc=null") . ", " .
		            ($usu_dt_entrada_pais ? "usu_dt_entrada_pais='$usu_dt_entrada_pais'" : "usu_dt_entrada_pais=null") . ", " .
		            ($ecd_codigo ? "ecd_codigo='$ecd_codigo'" : "ecd_codigo=null") . ", " .
		            ($usu_sit_familiar ? "usu_sit_familiar=".intval($usu_sit_familiar) : "usu_sit_familiar=null") . ", " .
		            ($usu_freq_escolar ? "usu_freq_escolar='".substr($usu_freq_escolar,0,1)."'" : "usu_freq_escolar=null") . ", " .
		            ($usu_ocupacao ? "usu_ocupacao='$usu_ocupacao'" : "usu_ocupacao=null") . ", " .
		            ($usu_cbo_r ? "usu_cbo_r='$usu_cbo_r'" : "usu_cbo_r=null") . ", " .
		            ($usu_pis_pasep ? "usu_pis_pasep='$usu_pis_pasep'" : "usu_pis_pasep=null") . ", " .
		            ($usu_cpf ? "usu_cpf='$usu_cpf'" : "usu_cpf=null") . ", " .
		            ($usu_cartao_p_sus ? "usu_cartao_p_sus='$usu_cartao_p_sus'" : "usu_cartao_p_sus=null") . ", " .
		            ($usu_cartao_sus ? "usu_cartao_sus='$usu_cartao_sus'" : "usu_cartao_sus=null") . ", " .
		            ($usu_tipo_certidao ? "usu_tipo_certidao=".intval($usu_tipo_certidao) : "usu_tipo_certidao=null") . ", " .
		            ($usu_cert_cartorio ? "usu_cert_cartorio='$usu_cert_cartorio'" : "usu_cert_cartorio=null") . ", " .
		            ($usu_cert_livro ? "usu_cert_livro='$usu_cert_livro'" : "usu_cert_livro=null") . ", " .
		            ($usu_cert_lv_fls ? "usu_cert_lv_fls='$usu_cert_lv_fls'" : "usu_cert_lv_fls=null") . ", " .
		            ($usu_cert_termo ? "usu_cert_termo='$usu_cert_termo'" : "usu_cert_termo=null") . ", " .
		            ($usu_cert_emissao ? "usu_cert_emissao='$usu_cert_emissao'" : "usu_cert_emissao=null") . ", " .
		            ($usu_rg ? "usu_rg='$usu_rg'" : "usu_rg=null") . ", " .
		            ($usu_rg_compl ? "usu_rg_compl='$usu_rg_compl'" : "usu_rg_compl=null") . ", " .
		            ($uf_sigla_rg ? "uf_sigla_rg='$uf_sigla_rg'" : "uf_sigla_rg=null") . ", " .
		            ($usu_rg_emissor ? "usu_rg_emissor='$usu_rg_emissor'" : "usu_rg_emissor=null") . ", " .
		            ($usu_rg_dt_emissao ? "usu_rg_dt_emissao='$usu_rg_dt_emissao'" : "usu_rg_dt_emissao=null") . ", " .
		            ($usu_ctps ? "usu_ctps='$usu_ctps'" : "usu_ctps=null") . ", " .
		            ($usu_ctps_serie ? "usu_ctps_serie='$usu_ctps_serie'" : "usu_ctps_serie=null") . ", " .
		            ($uf_sigla_ctps ? "uf_sigla_ctps='$uf_sigla_ctps'" : "uf_sigla_ctps=null") . ", " .
		            ($usu_ctps_dt_emissao ? "usu_ctps_dt_emissao='$usu_ctps_dt_emissao'" : "usu_ctps_dt_emissao=null") . ", " .
		            ($usu_tit_eleitor ? "usu_tit_eleitor='$usu_tit_eleitor'" : "usu_tit_eleitor=null") . ", " .
		            ($usu_tit_eleitor_zona ? "usu_tit_eleitor_zona='".substr($usu_tit_eleitor_zona,0,4)."'" : "usu_tit_eleitor_zona=null") . ", " .
		            ($usu_tit_eleitor_secao ? "usu_tit_eleitor_secao='".substr($usu_tit_eleitor_secao,0,4)."'" : "usu_tit_eleitor_secao=null") . ", " .
		            ($usu_mae ? "usu_mae=upper('$usu_mae')" : "usu_mae=null") . ", " .
		            ($usu_pai ? "usu_pai=upper('$usu_pai')" : "usu_pai=null") . ", " .
		            ($usu_cisvir ? "usu_cisvir='$usu_cisvir'" : "usu_cisvir=null") . ", " .
		            ($uni_origem ? "uni_origem='$uni_origem'" : "uni_origem=null") . ", " .
		            ($uni_unidade ? "uni_unidade='$uni_unidade'" : "uni_unidade=null") . ", " .
		            ($usu_resp_nome ? "usu_resp_nome=upper('$usu_resp_nome')" : "usu_resp_nome=null") . ", " .
		            ($usu_resp_doc_tipo ? "usu_resp_doc_tipo=('$usu_resp_doc_tipo')" : "usu_resp_doc_tipo=null") . ", " .
		            ($usu_resp_doc ? "usu_resp_doc=trim('$usu_resp_doc')" : "usu_resp_doc=null") . ", " .
		            "usr_alt = ". intval($id_login). ", ".
		            "usr_alt_dt = NOW() ".
             "WHERE usu_codigo = '$usu_codigo'";
            $sql = db_query( $stmt );
            //msg($id_login,$acao,$sql);
            print "
            <script type='text/javascript'>
            	//var acao = \"document.location.href = '{$PHP_SELF}?id_login={$id_login}&palavra_chave={$palavra_chave}';\";
		var acao = \"document.location.href = 'list_pacientes.php?id_login={$id_login}&palavra_chave=&tpbusca=n&forcar_busca={$usu_codigo}';\";
            	setTimeout( acao, 2500 );
            </script>
            <p class='aviso ok'>Paciente atualizado com sucesso !</p>
            ";
}

//
//-> DEL <---------------------------------------------------------->

if($acao=="del")
{
	$sql = db_query("delete from usuario where usu_codigo='$usu_codigo'");
	//msg($id_login,$acao,$sql);
	print "
	<script type='text/javascript'>
		var acao = \"document.location.href = '{$PHP_SELF}?id_login={$id_login}&palavra_chave={$palavra_chave}';\";
		setTimeout( acao, 2500 );
	</script>
	<p class='aviso ok'>Paciente removido com sucesso !</p>
	";
}

?>

