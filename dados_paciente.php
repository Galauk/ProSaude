<?
//echo"<pre>".print_r($_GET,true)."</pre>";

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
    verauth($id_login);

	require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
           
echo "<body bgcolor='#FFFFFF'>
      <link href='estilo.css' rel='stylesheet' type='text/css'>
      <link href='css/estiloForm.css' rel='stylesheet' type='text/css' />
 	  <link href='css/estiloCommon.css' rel='stylesheet' type='text/css' />";
//------------------------------------------------------------------>
$form = new classForm();
$common = new commonClass();
$table = new tableClass();

echo $common->incJquery();

 $Age = pg_fetch_array(pg_query("select *from agendamento where age_codigo='$age_codigo'"));
 $usu_codigo = $Age[usu_codigo];
echo"<pre>".print_r($_GET,true)."</pre>";
if(empty($acao)) { 
//
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
				<td valign='top'>&nbsp;&nbsp;&nbsp;<img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/sem_foto.gif border=0 style='margin-top:0px;'></td>
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
	echo $form->submitButton('Editar','".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg' )
				."</td>
			</tr>
		
		</table><br>";
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
	echo $form->submitButton('Editar','".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg' );*/
	
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
$form = new classForm();
$common = new commonClass();
echo $common->incJquery();
echo $common->menuTab(array('Consultas','PAM','Dispensação','Exames'));
echo"<table>
	<tr>
		<td>".
	 $common->bodyTab('1')."	
	
	\n<div id='form_a'>\n";
	
    $sql = "select to_char(dt_cadastro,'YYYY-MM-DD') as dt_cadastro,
            usr_codigo_alt, usr_codigo_cad, agt_codigo,
            to_char(age_data,'DD/MM/YYYY') as age_data,
            age_codigo, med_codigo, age_hora, usu_codigo, age_tipo, age_atendido,
            age_paciente, uni_codigo, age_item, esp_codigo
            from agendamento
            where usu_codigo = $usu_codigo
            order by to_char(age_data,'YYYY') desc,
            to_char(age_data,'MM') desc,
            to_char(age_data,'DD') desc";
    
    $sql_busca = db_query($sql);

    echo "
        <table width='900' class='lista'>
            <tr>
                <th colspan='1' class='borda'>&nbsp;</th>
                <th>Data</th>
                <th>Hora</th>
                <th>Tipo</th>
                <th>Especialidade</th>
                <th>M&eacute;dico</th>
                <th>Unidade</th>
                
            </tr>";
            while($row = pg_fetch_array($sql_busca))
            {
                
                if($row[age_atendido] == "S")
                {
                    $bold_font_open = "<font color='blue'><b>Recepcionado</font></b>";
                } else if($row[age_atendido] == "N") {
                    $bold_font_open = "Agendado";
                } else if($row[age_atendido] == "F") {
                    $bold_font_open = "<font color='red'><b>Faltou</font></b>";
                } else if($row[age_atendido] == "T") {
                    $bold_font_open = "<font color='orange'><b>Transferido</font></b>";
                }
                
                $sql = "select * from especialidade where esp_codigo = $row[esp_codigo]";
                $exec_sql = pg_query($sql);
                $esp=pg_fetch_array($exec_sql);
                
                $sql = "select * from medico where med_codigo = $row[med_codigo]";
                $exec_sql = pg_query($sql);
                $med=pg_fetch_array($exec_sql);
                
                $sql = "select * from unidade where uni_codigo = $row[uni_codigo]";
                $exec_sql = pg_query($sql);
                $uni=pg_fetch_array($exec_sql);
                
                $sql = "select * from usuarios where usr_codigo = $row[usr_codigo_cad]";
                $exec_sql = pg_query($sql);
                $pacCad = pg_fetch_array($exec_sql);
                
                $data_hoje = date('Y-m-d');
                echo "
                    <tr bgcolor='FFFFFF' style='white-space:nowrap;'>
                        <td class='borda2'>
                            <a href='#' onclick='window.open(\"print_guia_2via.php?uni_codigo=$row[uni_codigo]&esp_codigo=$row[esp_codigo]&agt_codigo=$row[agt_codigo]&usu_codigo=$row[usu_codigo]&age_codigo=$row[age_codigo]&med_codigo=$row[med_codigo]&id_login={$id_login}\",null,\"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no\");'>
                                <img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/print_prontuario.jpg' border='0'>
                            </a>
                        </td>";
                       
                        echo "
                            <td>$row[age_data]</td>
                            <td>$row[age_hora]</td>
                            <td>$bold_font_open</td>
                            <td>$esp[esp_nome]</td>
                            <td>$med[med_nome]</td>
                            <td>$uni[uni_desc]</td>
                           
                    </tr>";
            }
        echo "</table>";
		
	echo "\n</div>".
	// /ABA: CONSULTAS ---------------------------------------------------------</td>
	$common->closeTab();
	
	echo $common->bodyTab('3');
	
	echo" \n<div id='form_b'>\n";
	
	//echo "DISPENSACAO";
    
    //formata data para 6 meses atrás
    $ano_inicial = date("Y");
    $mes_inicial = date("n");
    
    for ($i=1; $i<=5; $i++)
    {
        $mes_inicial = $mes_inicial - 1;
        if ($mes_inicial == 0)
        {
            $mes_inicial = 12;
            $ano_inicial = $ano_inicial - 1;
        }
    }
    $dt_inicial = $ano_inicial.'-'.$mes_inicial."-01";

	//-> Listando
    
    $sql = "SELECT to_char(v_movimentacao.mov_data, 'DD/MM/YYYY'), v_movimentacao.pro_nome, 
            v_movimentacao.ite_quantidade, v_movimentacao.setor, 
            CASE WHEN v_movimentacao.tipomovim = 'S' THEN 'Saida de Consumo' 
            WHEN v_movimentacao.tipomovim = 'I' THEN 'Inventario' 
            WHEN v_movimentacao.tipomovim = 'M' THEN 'Emprestimo' 
            WHEN v_movimentacao.tipomovim = 'P' THEN 'Permuta' 
            WHEN v_movimentacao.tipomovim = 'R' THEN 'Perdas' 
            WHEN v_movimentacao.tipomovim = 'O' THEN 'Outras Saidas' 
            WHEN v_movimentacao.tipomovim = 'E' THEN 'Nota Fiscal de Compra' 
            WHEN v_movimentacao.tipomovim = 'A' THEN 'Ajuste' 
            WHEN v_movimentacao.tipomovim = 'D' THEN 'Doacao' 
            WHEN v_movimentacao.tipomovim = 'V' THEN 'Devol. Setor' 
            WHEN v_movimentacao.tipomovim = 'T' THEN 'Transferência' 
            ELSE 'Indefinido' 
            END AS tipo_consumo,
            itens_movimento.ite_qtde_dia, itens_movimento.ite_posologia,
            itens_movimento.ite_detalhes_tratamento, itens_movimento.ite_observacoes
            FROM v_movimentacao 
            LEFT JOIN itens_movimento 
                ON itens_movimento.mov_codigo = v_movimentacao.mov_codigo
            WHERE v_movimentacao.usu_codigo = ".$usu_codigo." 
            AND v_movimentacao.mov_data > '".$dt_inicial."' 
            AND itens_movimento.pro_codigo = v_movimentacao.pro_codigo
			ORDER BY mov_data";
   

    echo "
        <table width='98%'>
            <tr>
                <td>
                    <fieldset>
                        <legend>Medicamentos Retirados pelo Paciente nos Últimos 6 Meses</legend>
                        <table width='100%'  class='lista'>
                            <tr bgcolor='#F9F9F9' style='white-space:nowrap;'>
                                <th>Data</th>
                                <th>Produto</th>
                                <th>Qtd</th>
                                <th>Setor</th>
                                <th>Tipo de Consumo</th>
                                <th>Qtd Dias</th>
                                <th>Total de Dias</th>
                                <th>Próxima Liberação</th>
                            </tr>";
                            $res_sql = pg_query($sql);
                            $cor = 0;
                            $cor1 = "#F2F5F3";
                            while($row=pg_fetch_array($res_sql))
                            {
                                $cor++;
                                //separa a data da movimentacao para criar a data final, 
                                //de acordo com a qtd de dias que o medicamento sera utilizado
                                $temp_dia = substr($row[0],0,2);
                                $temp_mes = substr($row[0],3,2);
                                $temp_ano = substr($row[0],6,4);
                                $dt_liberacao = date("d/m/Y", mktime(0, 0, 0, $temp_mes, $temp_dia+((int)$row[2]/$row[5]), $temp_ano));
                                echo "<tr>
                                    <td>$row[0]</td>
                                    <td>$row[1]</td>
                                    <td align='right'>".number_format($row[2],0,',','.')."</td>
                                    <td>$row[3]</td>
                                    <td>$row[4]&nbsp;</td>
                                    <td align='right'>$row[5]&nbsp;</td>
                                    <td align='right'>".(int)($row[2]/$row[5])."&nbsp;</td>
                                    <td>$dt_liberacao</td>
                                </tr>";
                                /*echo "<tr ";
                                if( $cor%2 == 0 ){ echo "bgcolor='$cor1'"; }
                                echo ">
                                    <td align='left' class='borda4' colspan='2'><b>Posologia:</b><br />".$row[6]."</td>
                                    <td align='left' class='borda4' colspan='3'><b>Detalhes do tratamento:</b><br />".$row[7]."</td>
                                    <td align='left' class='borda4' colspan='3'><b>Obsevações:</b><br />".$row[8]."</td>
                                </tr>";*/
                            }
                    echo "
                        </table>
                    </fieldset>
                </td>
            </tr>
        </table>";
    
	echo "\n</div>";
	// /ABA: DISPENSACAO -------------------------------------------------------
	echo $common->closeTab();
	echo $common->bodyTab('2');
	
	// ABA: PAM ----------------------------------------------------------------
	echo "\n<div id='form_c'>\n";
	
	$stmt = "SELECT to_char(a.ate_data,'DD/MM/YYYY') as ate_data, a.ate_hora,
				uni.uni_desc
			FROM atendimento AS a
			NATURAL JOIN usuario AS u 
			LEFT JOIN unidade AS uni ON uni.uni_codigo = u.uni_unidade
			WHERE usu_codigo='$usu_codigo'
			ORDER BY ate_data, ate_hora DESC";
			
	$query = db_query($stmt);
	
	echo "
	<table cellspacing='2' cellpadding='4'>
		<tr bgcolor='#F9F9F9'>
			<td class='borda3 c' style='font-weight:bold' width='75'>Data</td>
			<td class='borda3 c' style='font-weight:bold' width='75'>Hora</td>
			<td class='borda3' style='font-weight:bold'>Unidade</td>
		</tr>
	";
	
	while( $rr = pg_fetch_array($query ))
	{
 
		echo "
		<tr>
			<td class='borda4 c'>$rr[ate_data]</td>
			<td class='borda4 c'>$rr[ate_hora]</td>
			<td class='borda4'>$rr[uni_desc]</td>
        </tr>";
	}
	
	echo "\n\t</table>";
	
	echo "\n</div>";
	// /ABA: PAM ---------------------------------------------------------------
	
	
	echo $common->closeTab();
	echo $common->bodyTab('4');
	

	// ABA: EXAMES ----------------------------------------------------------------
	echo "\n<div id='form_d'>\n";
	
        $stmt = "select *,to_char(lst.agexl_data,'DD/MM/YYYY') as data_age,TRANSLATE(proc_nome, 'ZZZ-', '') as proc_nome,lst.agexl_codigo,agt.usu_codigo from agendamento_exame as agt left join agendamento_exame_lista as lst on lst.agex_codigo = agt.agex_codigo
                 left join procedimento as proc on proc.proc_codigo = lst.proc_codigo left join medico as med on med.med_codigo = lst.med_codigo 
		 left join usuarios as usu on usu.usr_codigo = lst.usr_codigo_cad where agt.usu_codigo = $usu_codigo order by lst.agexl_data";
	$query = db_query($stmt);

     echo "
        <table width='900'class='lista'>
		<tr>
                
                <th>Data</th>
                <th>Procedimento</th>
                <th>Laboratorio</th>
                <th>Usuario Cadastro</th>
            </tr>";
	
	
	while( $rr = pg_fetch_array($query ))
	{
     echo "<tr>
		<tr>
               
                <td>$rr[data_age]</td>
                <td>$rr[proc_nome]</td>
                <td>$rr[med_nome]</td>
                <td>$rr[usr_nome]</td>
            </tr>";
	
	}
	
	echo "\n\t</table>";
	
	echo "\n</div>";
	// /ABA: EXAMES ---------------------------------------------------------------
	echo $common->closeTab();
	echo"</td>
	</tr>	
</table>";
?>
