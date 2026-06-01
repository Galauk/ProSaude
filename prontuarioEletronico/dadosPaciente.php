<?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	$unidades = array('P' => 'PAM',
				  'O' => 'ÓBITO',
				  'E' => 'ESCOLA DA GESTANTE',
				  'C' => 'CENTRO INFANTIL',
  				  'N' => 'NATTA',
				  'EM' => 'EM TRÂNSITO'				 
				  );
   $sexo = array('M' => 'MASCULINO',
   				 'F' => 'FEMININO');
   
  
	
	session_start();
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	//  verauth($id_login);
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
  

echo "<body bgcolor='#FFFFFF'>
      <link href='../estilo.css' rel='stylesheet' type='text/css'>
      <link href='../css/estiloForm.css' rel='stylesheet' type='text/css' />
 	  <link href='../css/estiloCommon.css' rel='stylesheet' type='text/css' />
 	  <link href='css/estiloFormularioProntuario.css' rel='stylesheet' type='text/css' />";
//------------------------------------------------------------------>
$form = new classForm();
$common = new commonClass();
$table = new tableClass();

echo $common->incJquery();

 $Age = pg_fetch_array(pg_query("select *from agendamento where age_codigo='$age_codigo'"));
 $usu_codigo = $Age[usu_codigo];

if(empty($acao)) { //
//-> Pegando as informcoes do banco pra mostrar no formulario
					  $sql = "select * from unidade order by uni_desc";
                      $sqlusuario = "SELECT  usu_codigo,
                                      usu_nome,
                                      usu_mae,
                                      uni_unidade,
                                      uni_origem,
                                      to_char(usu_datanasc, 'dd/mm/yyyy') as usu_datanasc,
                                      usu_end_rua, 
                                      usu_end_nr, 
                                      usu_end_compl, 
                                      usu_end_bairro,
                                      usu_end_cep, 
                                      cid_codigo_nasc, 
                                      usu_same, 
                                      usu_cisvir,
                                      usu_sexo, 
                                      fam_codigo, 
                                      usu_observacao, 
                                      usu_sit_familiar,
                                      usu_freq_escolar, 
                                      usu_ocupacao, 
                                      usu_cbo_r, 
                                      usu_pis_pasep,
                                      usu_cpf, 
                                      usu_cartao_p_sus, 
                                      usu_cartao_sus, 
                                      usu_tipo_certidao,
                                      usu_cert_cartorio, 
                                      usu_cert_livro, 
                                      usu_cert_lv_fls,
                                      usu_end_cidade,
                                      usu_cert_termo, 
                                      to_char(usu_cert_emissao, 'dd/mm/yyyy') as usu_cert_emissao,
                                      usu_rg, 
                                      usu_rg_compl, 
                                      uf_sigla_rg,
                                      to_char(usu_rg_dt_emissao, 'dd/mm/yyyy') as usu_rg_dt_emissao,
                                      usu_ctps, 
                                      uf_sigla_ctps, 
                                      usu_ctps_serie,
                                      to_char(usu_ctps_dt_emissao, 'dd/mm/yyyy') as usu_ctps_dt_emissao,
                                      usu_prontuario,
                                      usu_tit_eleitor, 
                                      usu_tit_eleitor_zona, 
                                      usu_tit_eleitor_secao
                                    FROM usuario 
                                   WHERE usu_codigo='$usu_codigo'";
 $row=pg_fetch_array(pg_query($sqlusuario));

echo $form->openForm($PHP_SELF,'POST');

	echo"<table border ='0'>
			<tr><br>
				<td valign='top' width=120>&nbsp;&nbsp;&nbsp;<img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/sem_foto.gif border=0 style='margin-top:0px;'></td>
				<td>".
				$form->hiddenForm('id_login',$id_login);
	echo $form->hiddenForm('usu_codigo',$usu_codigo);
	echo $form->hiddenForm('age_codigo',$age_codigo);
	echo $form->hiddenForm('acao','edit');
	

	echo $form->inputText('usu_nome',$row[usu_nome],'Nome','30');
	echo $form->inputText('usu_mae',$row[usu_mae],'Nome da m&atilde;e','30');
	echo $form->inputText('usu_datanasc',$row[usu_datanasc],'Data Nasc.','30');
	echo $form->inputText('usu_end_rua',$row[usu_end_rua],'Rua','30');
	echo $form->inputText('usu_end_nr',$row[usu_end_nr],'N&uacute;mero','30');
	echo $form->inputText('usu_end_compl',$row[usu_end_compl],'Complemento','30');
	echo $form->inputText('usu_end_bairro',$row[usu_end_bairro],'Bairro','30');
	echo $form->inputText('usu_end_cep',$row[usu_end_cep],'CEP','30');
	echo $form->inputText('usu_end_cidade',$row[usu_end_cidade],'Cidade(Conv)','30');
	echo $form->inputSelect('uni_null', null,'Unidade (Paciente)',$sql);
	echo $form->inputSelect('uni_null', $unidades,'Unidade (Prontuario)',null);
	echo $form->inputText('usu_prontuario',$row[usu_prontuario],'Prontu&aacute;rio','30');
	echo $form->inputSelect('uni_null', $sexo,'Sexo',null);
	echo"<br>";echo"<br>";echo"<br>";	
	echo $form->submitButton('Editar',$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg" )
				."</td>
			</tr>
		
		</table>";

	/*echo $form->hiddenForm('id_login',$id_login);
	echo $form->hiddenForm('usu_codigo',$usu_codigo);
	echo $form->hiddenForm('age_codigo',$age_codigo);
	echo $form->hiddenForm('acao','edit');
	

	echo $form->inputText('usu_nome',$row[usu_nome],'Nome','64');
	echo $form->inputText('usu_mae',$row[usu_mae],'Nome da m&atilde;e','64');
	echo $form->inputText('usu_datanasc',$row[usu_datanasc],'Data Nasc.','64');
	echo $form->inputText('usu_end_rua',$row[usu_end_rua],'Rua','64');
	echo $form->inputText('usu_end_nr',$row[usu_end_nr],'N&uacute;mero','64');
	echo $form->inputText('usu_end_compl',$row[usu_end_compl],'Complemento','64');
	echo $form->inputText('usu_end_bairro',$row[usu_end_bairro],'Bairro','64');
	echo $form->inputText('usu_end_cep',$row[usu_end_cep],'CEP','64');
	echo $form->inputText('usu_end_cidade',$row[usu_end_cidade],'Cidade(Conv)','64');
	echo $form->inputSelect('uni_null', null,'Unidade (Paciente)',$sql);
	echo $form->inputSelect('uni_null', $unidades,'Unidade (Prontuario)',null);
	echo $form->inputText('usu_prontuario',$row[usu_prontuario],'Prontu&aacute;rio','64');
	echo $form->inputSelect('uni_null', $sexo,'Sexo',null);
	echo"<br>";echo"<br>";echo"<br>";	
	echo $form->submitButton('Editar',$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg" );*/
	
		//public function inputSelect($nome,$valor,$caption=null,$sql=null,$js=null,$id=null,$sel=null,$fSize=null,$option="SELECIONE") 
echo $form->closeForm();

/* echo "<form method=post action=$PHP_SELF>
        <input type=hidden name=acao value=edit>
        <input type=hidden name=id_login value=$id_login>
        <input type=hidden name=usu_codigo value=$usu_codigo>
        <input type=hidden name=age_codigo value=$age_codigo>
             <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
              <tr>
                <td width=70>Prontuario:</td>
                <td><font color=red size=2><b>$row[usu_prontuario]</b></font></td>
              </tr>
              <tr>
                <td width=70>Nome:</td>
                <td><input type=text name=usu_nome class=box size=50 value='$row[usu_nome]'></td>
              </tr>
              <tr>
                <td width=70>Nome da mãe:</td>
                <td><input type=text name=usu_mae class=box size=50 value='$row[usu_mae]'></td>
              </tr>
              <tr>
                <td width=70>Data Nasc.:</td>
                <td><input type=text name=usu_datanasc class=box size=14 value='$row[usu_datanasc]'></td>
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
                <td width=70>Unidade (<font color=red>Paciente</font>):</td>
                <td>
                 <select name=uni_origem class=boxra>";
            //
            //-> SQL da UNIDADE
            $query = pg_query("select * from unidade order by uni_desc");
                echo " <option value=''>---</option>";
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
}
if($row[uni_unidade]=="obito") {
   $s1="";
   $s2="selected";
   $s3="";
   $s4="";
   $s5="";
   $s6="";
}

if($row[uni_unidade]=="escola da gestante") {
   $s1="";
   $s2="";
   $s3="selected";
   $s4="";
   $s5="";
   $s6="";
}

if($row[uni_unidade]=="centro infantil") {
   $s1="";
   $s2="";
   $s3="";
   $s4="selected";
   $s5="";
   $s6="";
}
if($row[uni_unidade]=="natta") {
   $s1="";
   $s2="";
   $s3="";
   $s4="";
   $s5="selected";
   $s6="";
}

if($row[uni_unidade]=="em transito") {
   $s1="";
   $s2="";
   $s3="";
   $s4="";
   $s5="";
   $s6="selected";
}
$unidades = array('P' => 'PAM',
				  'O' => 'ÓBITO',
				  'E' => 'escola da gestante',
				  'C' => 'centro infantil',
  				  'N' => 'NATTA',
				  'EM' => 'EM TRÂNSITO'				 
				  );

$selected = 'selected=selected';
echo "<option value=>---</option>";
echo "<option value=pam ($row[uni_unidade]=='pam'? $selected : '' )>PAM</option>";
echo "<option value=obito ($row[uni_unidade]=='pam'? $selected : '' )>ÓBITO</option>";
echo "<option value='escola da gestante' ($row[uni_unidade]=='pam'? $selected : '' )>ESCOLA DA GESTANTE</option>";
echo "<option value='centro infantil' ($row[uni_unidade]=='pam'? $selected : '' )>CENTRO INFANTIL</option>";
echo "<option value=natta ($row[uni_unidade]=='pam'? $selected : '' )>NATTA</option>";
echo "<option value='em transito' ($row[uni_unidade]=='pam'? $selected : '' )>EM TRÂNSITO</option>";

           echo "</select>
                </td>
              </tr>
              <tr>
                <td width=70>Prontuario:</td>
                <td><input type=text name=usu_prontuario class=box size=20 readonly value='$row[usu_prontuario]'></td>
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
               <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg></td>
              </tr>
             </table></form>";*/
}
 if($acao=="edit") {
	 reglog($id_login,"Alterando DADOS_PACIENTE $usu_codigo");
  $nome = strtoupper($usu_nome);
  $sql = pg_query("update usuario set " .
#  $sql = "update usuario set " .
            "usu_nome=upper('$usu_nome'), " .
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
            ($usu_cisvir ? "usu_cisvir='$usu_cisvir'" : "usu_cisvir=null") . ", " .
            ($uni_origem ? "uni_origem='$uni_origem'" : "uni_origem=null") . ", " .
            ($uni_unidade ? "uni_unidade='$uni_unidade'" : "uni_unidade=null") . " " .
            "where usu_codigo='$usu_codigo'");

          echo "<br><br><br><br><br><br><br><br><br>
                <table height=100 width=100% align=center cellspacing=0 cellpadding=0 border=0>
                 <tr bgcolor=f9f9f9>
                   <td align=center><font size=2 color=green><b>EDITADO com Sucesso</b></font></td>
                 </tr>
                </table><br>";
        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='dados_paciente.php?id_login=$id_login&age_codigo=$age_codigo'\", 2000);
              </SCRIPT>";

}

?>