<?php
// redirecionando...
header("Location: paciente.php?{$QUERY_STRING}");
exit;

?>
<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<script>
  function verifica() {
   if(document.paciente.usu_nome.value == '') {
        alert("Por favor Preencha o Nome");
        return false;
   }

   if(document.paciente.usu_mae.value == '') {
        alert("Por favor Preencha o Nome da Mae");
        return false;
   }

   if(document.paciente.usu_datanasc.value == '') {
        alert("Por favor Preencha a Data de Nascimento");
        return false;
   }

   if(document.paciente.usu_end_rua.value == '') {
        alert("Por favor Preencha a Rua");
        return false;
   }

   if(document.paciente.usu_end_nr.value == '') {
        alert("Por favor Preencha o Numero");
        return false;
   }

   if(document.paciente.usu_end_bairro.value == '') {
        alert("Por favor Preencha o Bairro");
        return false;
   }

 return true;
}
</script>
<?php
session_start();
//------------------------------------------------------------------>


//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
	 reglog($id_login,"Entrando em PACIENTE_FICHA");
//------------------------------------------------------------------>

//-> Formulario de Adicao de Conteudo
//------------------------------------------------------------------>

 if($acao=="form_add") {
	 reglog($id_login,"Formulario de ADICAO PACIENTE_FICHA");


//
//-> Abaixo sao os botoes de voltar / cadastro simples / cadastro completo
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções de Cadastro</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=115><a href=$PHP_SELF?acao=form_add&type=&id_login=$id_login&from=$from><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/cadastro_simples.jpg border=0></a></td>
	       <td width=127><a href=$PHP_SELF?acao=form_add&type=c&id_login=$id_login&from=$from><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/cadastro_completo.jpg border=0></a></td>
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
  echo "<form method=post action=$PHP_SELF?from=$from name=paciente OnSubmit='return verifica()'>
	<input type=hidden name=acao value=add>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=type value=simples>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Cadastro Simples</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
		<td width=70>Nome:</td>
		<td><input type=text name=usu_nome class=box size=70></td>
	      </tr>
	      <tr>
		<td width=70>Nome da mãe:</td>
		<td><input type=text name=usu_mae class=box size=70></td>
	      </tr>
	      <tr>
		<td width=70>Data Nasc.: (dd/mm/yyyy)</td>
		<td><input type=text name=usu_datanasc class=box size=10  maxlength='10' value='$pdia' onKeypress=\"return Ajusta_Data(this, event);\"></td>
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
		<td width=70>Telefone:</td>
		<td><input type=text name=usu_fone class=box size=6></td>
	      </tr>
	      <tr>
		<td width=70>Celular:</td>
		<td><input type=text name=usu_celular class=box size=6></td>
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
                <td width=70>Unidade (<font color=red>Paciente</font>):</td>
                <td>
                 <select name=uni_origem class=box>";
            //
            //-> SQL da UNIDADE
            $query = pg_query("select * from unidade order by uni_desc");
              while($uni=pg_fetch_array($query)) {
               echo ($uni[uni_codigo]==$row[uni_codigo])?"<option value='$uni[uni_codigo]' selected>$uni[uni_desc]</option>":"<option value='$uni[uni_codigo]'>$uni[uni_desc]</option>";
              }
           echo "</select>
                </td>
              </tr>
              <tr>
                <td width=70>Unidade (<font color=red>Prontuario</font>):</td>
                <td>
                 <select name=uni_unidade class=box>";
echo "<option value='ubs central'>UBS CENTRAL</option>";
echo "<option value=pam>PAM</option>";
echo "<option value=obito>ÓBITO</option>";
echo "<option value='escola da gestante'>ESCOLA DA GESTANTE</option>";
echo "<option value='centro infantil'>CENTRO INFANTIL</option>";
echo "<option value=natta>NATTA</option>";
echo "<option value='em transito'>EM TRÂNSITO</option>";
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
  echo "<form method=post action=$PHP_SELF?from=$from>
	<input type=hidden name=acao value=add>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=type value=completo>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Cadastro Completo</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
		<td width=70>Nome:</td>
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
                <td width=70>Unidade (<font color=red>Paciente</font>):</td>
                <td>
                 <select name=uni_origem class=box>";
            //
            //-> SQL da UNIDADE
            $query = pg_query("select * from unidade order by uni_desc");
              while($uni=pg_fetch_array($query)) {
               echo ($uni[uni_codigo]==$row[uni_codigo])?"<option value='$uni[uni_codigo]' selected>$uni[uni_desc]</option>":"<option value='$uni[uni_codigo]'>$uni[uni_desc]</option>";
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
           echo "</select>
                </td>
              </tr>";

	   echo "<tr>
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

 if($acao=="form_edit") {
	 reglog($id_login,"Formulario de EDICAO PACIENTE_FICHA");

//
//-> Formulario de edicao do cadastro SIMPLES

//
//-> Pegando as informcoes do banco pra mostrar no formulario
 $sqlusuario =                       "select usu_codigo, 
                                      usu_nome,
                                      usu_mae,
                                      to_char(usu_datanasc, 'dd/mm/yyyy') as usu_datanasc,
                                      usu_end_rua, usu_end_nr, usu_end_compl, usu_end_bairro,
                                      usu_end_cep, cid_codigo_nasc, usu_same, usu_cisvir, 
                                      usu_sexo, fam_codigo, usu_observacao, usu_sit_familiar,
                                      usu_freq_escolar, usu_ocupacao, usu_cbo_r, usu_pis_pasep,
                                      usu_cpf, usu_cartao_p_sus, usu_cartao_sus, usu_tipo_certidao,
                                      usu_cert_cartorio, usu_cert_livro, usu_cert_lv_fls, 
                                      usu_cert_termo, to_char(usu_cert_emissao, 'dd/mm/yyyy') as usu_cert_emissao,
                                      usu_rg, usu_rg_compl, uf_sigla_rg, 
                                      to_char(usu_rg_dt_emissao, 'dd/mm/yyyy') as usu_rg_dt_emissao,
                                      usu_ctps, uf_sigla_ctps, usu_ctps_serie, 
                                      to_char(usu_ctps_dt_emissao, 'dd/mm/yyyy') as usu_ctps_dt_emissao,
                                      usu_tit_eleitor, usu_tit_eleitor_zona, usu_tit_eleitor_secao
                                      from usuario where usu_codigo='$usu_codigo'";
 $row=pg_fetch_array(pg_query($sqlusuario));

  echo "<br><br><form method=post action=$PHP_SELF?from=$from>
	<input type=hidden name=acao value=edit>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=usu_codigo value=$usu_codigo>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Cadastro Completo</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
		<td width=70>Nome:</td>
		<td><input type=text name=usu_nome class=box size=70 value='$row[usu_nome]'></td>
	      </tr>
	      <tr>
		<td width=70>Nome da mãe:</td>
		<td><input type=text name=usu_mae class=box size=70 value='$row[usu_mae]'></td>
	      </tr>
	      <tr>
		<td width=70>Nome do Pai:</td>
		<td><input type=text name=usu_pai class=box size=70> value='$row[usu_pai]'</td>
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
		<td width=70>Cidade:</td>
		<td>
		 <select name=cid_codigo_nasc class=box>";
	    //
	    //-> SQL da Cidade
	    $query = pg_query("select * from cidade order by cid_nome");
	      while($cidade=pg_fetch_array($query)) {
	       echo ($cidade[cid_codigo]==$row[cid_codigo_nasc])?"<option value='$cidade[cid_codigo]' selected>$cidade[cid_nome]</option>":"<option value='$cidade[cid_codigo]'>$cidade[cid_nome]</option>";
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
		<td><input type=text name=usu_same class=box size=20 value='$row[usu_same]'></td>
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
		<td><input type=text name=usu_freq_escolar class=box size=20 value='$row[usu_freq_escolar]' ></td>
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
		<td><input type=text name=usu_cartao_sus class=box size=15 value='$row[usu_cartao_sus]' ></td>
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
		<td width=70>Cart.Trabalho-Serie:</td>
		<td><input type=text name=usu_ctps_serie class=box size=10 value='$row[usu_ctps_serie]' ></td>
	      </tr>
	      <tr>
		<td width=70>Cart.Trabalho-Emissao:</td>
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
	       <td>&nbsp;</td>
	       <td><a href=paciente.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>&nbsp;&nbsp;<input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg></td>
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
	 reglog($id_login,"Adicionando Registro em PACIENTE_FICHA");

   $usu_nome=strtoupper($usu_nome);
   $usu_mae=strtoupper($usu_mae);
   $sql = pg_query("INSERT INTO usuario (usu_nome,
   											   usu_mae,
   											   usu_datanasc,
   											   usu_end_rua,
   											   usu_end_nr,
   											   usu_end_compl,
   											   usu_end_bairro,
   											   usu_end_cep,
   											   uni_unidade,
   											   uni_origem,
   											   usu_end_cidade,
   											   usu_cisvir,
   											   usu_sexo,
   											   usu_fone,
   											   usu_celular,
   											   usr_cad
   									 ) VALUES ('$usu_nome',
   									 		   '$usu_mae',
   									 		   '$usu_datanasc',
   									 		   '$usu_end_rua',
   									 		   '$usu_end_nr',
   									 		   '$usu_end_compl',
   									 		   '$usu_end_bairro',
   									 		   '$usu_end_cep',
   									 		   '$uni_unidade',
   									 		   '$uni_origem',
   									 		   '$usu_end_cidade',
   									 		   '$usu_cisvir',
   									 		   '$usu_sexo',
   									 		   '$usu_fone',
   									 		   '$usu_celular',
   									 		   '$id_login')");
#   $sql = pg_query("insert into usuario (usu_nome,usu_mae,usu_datanasc,usu_end_rua,usu_end_nr,usu_end_compl,usu_end_bairro,usu_end_cep,uni_unidade,uni_origem,usu_end_cidade,usu_cisvir,usu_sexo) values('$usu_nome','$usu_mae','$usu_datanasc','$usu_end_rua','$usu_end_nr','$usu_end_compl','$usu_end_bairro','$usu_end_cep','$uni_unidade','$uni_origem','$usu_end_cidade','$usu_cisvir','$usu_sexo')");

/*
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
            "uni_origem, " .
            "uni_unidade " .
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
            ($usu_sit_familiar ? "'$usu_sit_familiar'" : "null") . ", " .
            ($usu_ocupacao ? "'$usu_ocupacao'" : "null") . ", " .
            ($usu_cbo_r ? "'$usu_cbo_r'" : "null") . ", " .
            ($usu_pis_pasep ? "'$usu_pis_pasep'" : "null") . ", " .
            ($usu_cpf ? "'$usu_cpf'" : "null") . ", " .
            ($usu_cartao_p_sus ? "'$usu_cartao_p_sus'" : "null") . ", " .
            ($usu_cartao_sus ? "'$usu_cartao_sus'" : "null") . ", " .
            ($usu_tipo_certidao ? "'$usu_tipo_certidao'" : "null") . ", " .
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
            ($usu_tit_eleitor_zona ? "'$usu_tit_eleitor_zona'" : "null") . ", " .
            ($usu_tit_eleitor_secao ? "'$usu_tit_eleitor_secao'" : "null") . ", " .
            ($usu_mae ? "upper('$usu_mae')" : "null") . ", " .
            ($usu_pai ? "upper('$usu_pai')" : "null") . ", " .
            ($usu_cisvir ? "'$usu_cisvir'" : "null") . ", " .
            ($uni_origem ? "'$uni_origem'" : "null") . ", " .
            ($uni_unidade ? "'$uni_unidade'" : "null") . " " .
            ")");
*/
          echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
                <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                 <tr bgcolor=f9f9f9>
                   <td align=center><font size=2 color=green><b>INCLUSO com Sucesso</b></font></td>
                 </tr>
                </table><br>";
        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='list_pacientes.php?id_login=$id_login&palavra_chave=$usu_nome&tpbusca=n&acao=busca'\", 2000);
              </SCRIPT>";
        //javascript:getpaciente('$row[usu_codigo]','$row[usu_nome]','$row[usu_datanasc]','$row[usu_mae]','$row[usu_end_cidade]');
}

//
//-> EDIT <--------------------------------------------------------->

 if($acao=="edit") {
	 reglog($id_login,"Editando PACIENTE_FICHA $usu_codigo");

  $sql = pg_query("update usuario set " .
            "usu_nome=upper('$usu_nome'), " .
            ($fam_codigo ? "fam_codigo='$fam_codigo'" : "fam_codigo=null") . ", " .
            ($usu_observacao ? "usu_observacao='$usu_observacao'" : "usu_observacao=null") . ", " .
            "usu_sexo='$usu_sexo', " .
            ($rac_codigo ? "rac_codigo='$rac_codigo'" : "rac_codigo=null") . ", " .
            ($usu_datanasc ? "usu_datanasc='$usu_datanasc'" : "usu_datanasc=null") . ", " .
            ($cid_codigo_nasc ? "cid_codigo_nasc='$cid_codigo_nasc'" : "cid_codigo_nasc=null") . ", " .
            ($usu_dt_entrada_pais ? "usu_dt_entrada_pais='$usu_dt_entrada_pais'" : "usu_dt_entrada_pais=null") . ", " .
            ($ecd_codigo ? "ecd_codigo='$ecd_codigo'" : "ecd_codigo=null") . ", " .
            ($usu_sit_familiar ? "usu_sit_familiar='$usu_sit_familiar'" : "usu_sit_familiar=null") . ", " .
            ($usu_freq_escolar ? "usu_freq_escolar='$usu_freq_escolar'" : "usu_freq_escolar=null") . ", " .
            ($usu_ocupacao ? "usu_ocupacao='$usu_ocupacao'" : "usu_ocupacao=null") . ", " .
            ($usu_cbo_r ? "usu_cbo_r='$usu_cbo_r'" : "usu_cbo_r=null") . ", " .
            ($usu_pis_pasep ? "usu_pis_pasep='$usu_pis_pasep'" : "usu_pis_pasep=null") . ", " .
            ($usu_cpf ? "usu_cpf='$usu_cpf'" : "usu_cpf=null") . ", " .
            ($usu_cartao_p_sus ? "usu_cartao_p_sus='$usu_cartao_p_sus'" : "usu_cartao_p_sus=null") . ", " .
            ($usu_cartao_sus ? "usu_cartao_sus='$usu_cartao_sus'" : "usu_cartao_sus=null") . ", " .
            ($usu_tipo_certidao ? "usu_tipo_certidao='$usu_tipo_certidao'" : "usu_tipo_certidao=null") . ", " .
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
            ($usu_tit_eleitor_zona ? "usu_tit_eleitor_zona='$usu_tit_eleitor_zona'" : "usu_tit_eleitor_zona=null") . ", " .
            ($usu_tit_eleitor_secao ? "usu_tit_eleitor_secao='$usu_tit_eleitor_secao'" : "usu_tit_eleitor_secao=null") . ", " .
            ($usu_mae ? "usu_mae=upper('$usu_mae')" : "usu_mae=null") . ", " .
            ($usu_pai ? "usu_pai=upper('$usu_pai')" : "usu_pai=null") . ", " .
            "usr_alt = '$id_login', " .
            ($usu_cisvir ? "usu_cisvir='$usu_cisvir'" : "usu_cisvir=null") . " " .
            "where usu_codigo='$usu_codigo'");

          echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
                <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                 <tr bgcolor=f9f9f9>
                   <td align=center><font size=2 color=green><b>INCLUSO com Sucesso</b></font></td>
                 </tr>
                </table><br>";
        echo "<SCRIPT LANGUAGE=\"JavaScript\">
	          window.close();
              </SCRIPT>";
}

//
//-> DEL <---------------------------------------------------------->


?>

