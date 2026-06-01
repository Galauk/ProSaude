<?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>

	session_start();
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	verauth($id_login);
	
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
//------------------------------------------------------------------>
// -> Inclusao funcao
//------------------------------------------------------------------>

reglog($id_login,"Acessando o Cadastro Complementar do PAM");


function CalcIdade($data_nasc) { // YYYY-MM-DD
      $h_ano=date("Y");
      $h_mes=date("m");
      $h_dia=date("d");

      $n_ano=substr($data_nasc, 0, 4);
      $n_mes=substr($data_nasc, 5, 2);
      $n_dia=substr($data_nasc, 8, 2);
      return ($h_mes>$n_mes || ($n_mes==$h_mes && $h_dia>=$n_dia) ) ? $h_ano - $n_ano : $h_ano - $n_ano - 1;
}
//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>

if(empty($acao)) {


  $med = pg_fetch_array(pg_query("SELECT * FROM medico where medico=$med_codigo"));
 
//
//-> Botoes
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
         <tr>
          <td>
           <fieldset>
            <legend>Opções</legend>
             <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
              <tr>
               <td width=79><a href=ambulatorio.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
	           <td width=480 align=center>&nbsp;M&eacute;dico :<font size=2 color=brown> nome-do-medico $med[med_nome]</font></td>
               <td width=107><a href='logoff.php?id_login=$id_login' target='_parent'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/sair.gif border=0></a></td>
              </tr>
              <tr><td>&nbsp;</td>
               <td> Lucio : este programa terá 3 telas , aqui estão duas telas</td>
               <td>&nbsp;</td>
              </tr> 
              <tr><td>&nbsp;</td>
               <td> ------- A 0(zero)  vai ter uma tela de pré-consulta, ou não terá a pré (medição da pressão e temperatura e os pneus) </td>
               <td>&nbsp;</td>
              </tr>
              <tr><td>&nbsp;</td>
               <td> ------- A 1 vai do Inicio até Encaminhamento      </td>
               <td>&nbsp;</td>
              </tr>
              <tr><td>&nbsp;</td>
               <td> ------- A 2 vai de Procedimentos até Medicamentos </td>
               <td>&nbsp;</td>
              </tr>
              <tr><td>&nbsp;</td>
               <td> ------- A 3  o Marco esta terminando , que é a Receita e os Itens_da_Receita .(ponto) </td>
               <td>&nbsp;</td>
              </tr>
              <tr><td>&nbsp;</td>
               <td> -------         que devera ser parecidissimo ao da dispensação , podes fazer o manual </td>
               <td>&nbsp;</td>
              </tr>
             </table>
           </fieldset>
          </td>
         </tr>
        </table><br>";
$ate = pg_fetch_array(pg_query("select ate_acidentetrab,ate_encaminhamento,ate_observacao,ate_diagnostico,cd10_codigo,to_char(ate_datafinal,'dd/mm/yyyy') as ate_datafinal,usu_codigo,ate_horafinal,ate_acidentetrab,to_char(ate_data,'dd/mm/yyyy') as ate_data,ate_hora,ate_codigo from atendimento where ate_codigo='$ate_codigo'")); 
$usu = pg_fetch_array(pg_query("select usu_datanasc as datanasc,to_char(usu_datanasc,'dd/mm/yyyy') as usu_datanasc,usu_codigo,usu_nome,usu_mae,usu_end_rua,usu_ocupacao,usu_end_nr,usu_end_cep,usu_end_cidade,uni_origem from usuario where usu_codigo = '$ate[usu_codigo]'"));
$uni = pg_fetch_array(pg_query("SELECT uni_desc FROM usuario, unidade 
                                 WHERE usuario.usu_codigo = '$ate[usu_codigo]'
                                   AND usuario.uni_origem = unidade.uni_codigo "));

 echo "<fieldset><form method=post action=$PHP_SELF>
       <input type=hidden name=acao value=atualiza>
	   <input type=hidden name=id_login value=$id_login>
	   <input type=hidden name=ate_codigo value='$ate[ate_codigo]'>
	   <legend>Dados do Paciente</legend>
        <table width=100% cellspacing=0 cellpadding=2 border=0>
	     <tr>
	      <td width=30%>Nome:&nbsp;<b>".substr($usu[usu_nome],0,20)."</b></td>
	      <td width=30%>Nome Mãe:&nbsp;<b>".substr($usu[usu_mae],0,20)."</b></td>
          <td width=40% align=right><a href=$PHP_SELF?Show=ShortWind&acao=&id_login=$id_login&ate_codigo=$ate_codigo><font color=blue size=1>ultimas consultas</font></a></td>
	     </tr>
	     <tr>
	      <td>Idade:&nbsp;<b>$usu[usu_datanasc] (".CalcIdade($usu[datanasc]).")</b> </td>
	      <td>Profissão:&nbsp;<b>".substr($usu[usu_ocupacao],0,15)."</b> </td>
	     </tr>
	     <tr>
	      <td width=15% colspan=2>Pertece à Unidade:&nbsp;<b>".substr($uni[uni_codigo],0,20)."</b></td>
	     </tr>
	    </table>
       </fieldset>";

  if($ate[ate_acidentetrab]=="S") { $ca = "selected"; $cb=""; } else {  $ca = ""; $cb="selected"; }
  if($ate[ate_encaminhamento]=="A")   { $a   = "checked"; }
  if($ate[ate_encaminhamento]=="I")   { $i   = "checked"; }
  if($ate[ate_encaminhamento]=="AMB") { $amb = "checked"; }
  if($ate[ate_encaminhamento]=="o")   { $o   = "checked"; }

 echo "<fieldset>
	   <legend>Dados do Atendimento</legend>
       <table width=100% cellspacing=0 cellpadding=2 border=0>
	    <tr>
	     <td width=15% align=right><b>Atendimento    Inicio:</b> ";
echo ($ate[ate_data]=="")?
        "<td width=15% align=right><input type=text size=12 name=ate_datafinal class=box value=".date('d/m/Y')."></td>"
       :"<td width=15% align=right><input type=text size=12 name=ate_datafinal class=box value='$ate[ate_data]'></td>";
echo "   <td width=10% align=right><b>&Agrave;s:</b></td>";
echo ($ate[ate_hora]=="")?
        "<td width=10% align=left><input type=text size=06 name=ate_horafinal class=box value=".date('h:i')."></td>"
       :"<td width=15% align=left><input type=text size=06 name=ate_horafinal class=box value='$ate[ate_hora]'></td>";

echo "   <td width=70%>&nbsp;</td>
        </tr>
   	    <tr>
	     <td width=15% align=right><b>                  Fim:</b> ";
echo ($ate[ate_datafinal]=="")?
        "<td align=right><input type=text size=12 name=ate_datafinal class=box value=".date('d/m/Y')."></td>"
       :"<td align=right><input type=text size=12 name=ate_datafinal class=box value='$ate[ate_datafinal]'></td>";
echo "   <td align=right><b>&Agrave;s:</b></td>";
echo ($ate[ate_horafinal]=="")?
        "<td align=left><input type=text size=06 name=ate_horafinal class=box value=".date('h:i')."></td>"
       :"<td align=left><input type=text size=06 name=ate_horafinal class=box value='$ate[ate_horafinal]'></td>";
echo "   <td width=70%>&nbsp;</td>
        </tr>
	   </table>
       <table width=100% cellspacing=0 cellpadding=2 border=0>
        <tr>
	     <td width=15% align=right><b>Acidente Trabalho:</b></td>
	     <td align=center><select name=ate_acidentetrab class=box>
              <option value='S' $ca>Sim</option>
              <option value='N' $cb>Não</option>
            </select>
         </td>
         <td width=65%>&nbsp;</td>
	    </tr>
	   </table>

        <table width=100% cellspacing=0 cellpadding=2 border=0>
         <tr>
	      <td width=15%>&nbsp;</td>
	      <td><b>Motivo do atendimento e descrição sumária do exame clínico:</b></td>
	     </tr>
	     <tr>
	      <td width=15%>&nbsp;</td>
	      <td><textarea name=ate_observacao cols=80 rows=5 class=box>$ate[ate_observacao]</textarea></td>
	     </tr>
	    </table>

        <table width=100% cellspacing=0 cellpadding=2 border=0>
         <tr>
	      <td width=15% align=right><b>CID:</b></td>
	      <td width=10%><input type=text name=ate_cid name=numero class=box size=10 value='$ate[cd10_codigo]'></td>
	      <td><input type=text name=cid name=descricao class=box size=53>&nbsp;<button class=box OnClick=\"window.open('cidchoice.php?id_login=$id_login' ,null,'height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes');\">Escolher</button></td>
	     </tr>
	    </table>

        <table width=100% cellspacing=0 cellpadding=2 border=0>
         <tr>
	      <td width=15%>&nbsp;</td>
	      <td><b>Diagnóstico:</b>(Descricao)</td>
	     </tr>
	     <tr>
	      <td width=15%>&nbsp;</td>
	      <td><textarea name=ate_diagnostico cols=80 rows=5 class=box>$ate[ate_diagnostico]</textarea></td>
	     </tr>
	    </table>


        <table width=100% cellspacing=0 cellpadding=2 border=0>
         <tr>
	      <td width=15%>&nbsp;</td>
	      <td><b>Encaminhamento:</b></td>
	     </tr>
         <tr>
	      <td width=15%>&nbsp;</td>
	      <td><input name=ate_encaminhamento type=radio value='A' $a>&nbsp;Alta</td>
	     </tr>
         <tr>
	      <td width=15%>&nbsp;</td>
	      <td><input name=ate_encaminhamento type=radio value='I' $i>&nbsp;Internação</td>
	     </tr>
         <tr>
	      <td width=15%>&nbsp;</td>
	      <td><input name=ate_encaminhamento type=radio value='AMB' $amb>&nbsp;P/ AMD do SUS</td>
	     </tr>
         <tr>
	      <td width=15%>&nbsp;</td>
	      <td><input name=ate_encaminhamento type=radio value='O' $o>&nbsp;Óbito</td>
	     </tr>
	    </table>

        <table width=100% cellspacing=0 cellpadding=2 border=0>
         <tr>
          <input type=hidden name=action value=insert>
          <input type=hidden name=acao value=>
         </tr>
         <tr>
	      <td width=15%>&nbsp;</td>
	      <td><input type=submit value='NEXT STEP' class=box></td>
	     </tr>
         <tr>
	      <td colspan=2><font size=3 color=red>aqui termina a primeira tela e começa a 2 tela</font></td>
	     </tr>
	    </table></form> ";

//-----------------------------------------------------------------------------------------

echo "
    <table width=100% cellspacing=0 cellpadding=2 border=0>
     <tr>
	  <td width=15%>&nbsp;</td>
	  <td><b>Procedimentos:</b></td>
	 </tr>
	</table>
        <table width=100% cellspacing=0 cellpadding=2 border=0>
         <tr>
	  <form method=post action='add_procedimento.php' target='add_procedimento'>
	  <input type=hidden name=id_login value='$id_login'>
	  <input type=hidden name=ate_codigo value='$ate[ate_codigo]'>
	  <input type=hidden name=act value='addproc'>
	  <td width=15%>&nbsp;</td>
	  <td width=61%><select name=procedimento class=box>";
	  echo "<option>...</option>";
	$query=pg_query("select *from procedimento where proc_exame!='S'");
	  while($row=pg_fetch_array($query)) {
	   echo "<option value='$row[proc_codigo]'>$row[proc_nome]</option>";
	  }
 echo "</select></td>
	 <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/add_on.gif></td>
	 </tr></form>
	</table>
   <table width=100% cellspacing=0 cellpadding=2 border=0>
         <tr>
          <td width=15%>&nbsp;</td>
          <td><iframe name=add_procedimento src=add_procedimento.php?id_login=$id_login&id_paciente=$usu[usu_codigo]&ate_codigo=$ate[ate_codigo] frameborder=no marginheight=0 marginwidth=0 scrolling=yes width=450 height=100></iframe></td>
	 </tr>
   </table>




        <table width=100% cellspacing=0 cellpadding=2 border=0>
         <tr>
	  <td width=15%>&nbsp;</td>
	  <td><b>Exames Complementares:</b></td>
	 </tr>
	</table>
        <table width=100% cellspacing=0 cellpadding=2 border=0>
	  <form method=post action='add_exames.php' target='add_exames'>
	  <input type=hidden name=id_login value='$id_login'>
	  <input type=hidden name=ate_codigo value='$ate[ate_codigo]'>
	  <input type=hidden name=act value='addproc'>
         <tr>
	  <td width=15%>&nbsp;</td>
	  <td width=53%><select name=exames class=box>";
	  echo "<option>...</option>";
	$query=pg_query("select *from procedimento where proc_exame='S'");
	  while($row=pg_fetch_array($query)) {
	   echo "<option value='$row[proc_codigo]'>$row[proc_nome]</option>";
	  }
 echo "</select></td>
	 <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/add_on.gif></td>
	 </tr></form>
	</table>
   <table width=100% cellspacing=0 cellpadding=2 border=0>
         <tr>
          <td width=15%>&nbsp;</td>
          <td><iframe name=add_exames src=add_exames.php?id_login=$id_login&id_paciente=$usu[usu_codigo]&ate_codigo=$ate[ate_codigo] frameborder=no marginheight=0 marginwidth=0 scrolling=yes width=450 height=100></iframe></td>
	 </tr>
   </table>


        <table width=100% cellspacing=0 cellpadding=2 border=0>
         <tr>
	  <td width=15%>&nbsp;</td>
	  <td><b>Medicamentos:</b></td>
	 </tr>
	</table>
        <table width=100% cellspacing=0 cellpadding=2 border=0>
	  <form method=post action='add_produtos.php' target='add_produtos'>
	  <input type=hidden name=id_login value='$id_login'>
	  <input type=hidden name=ate_codigo value='$ate[ate_codigo]'>
	  <input type=hidden name=usu_codigo value='$usu[usu_codigo]'>
	  <input type=hidden name=usu_codigo value='$usu[usu_codigo]'>
	  <input type=hidden name=act value='addproc'>
         <tr>
	  <td width=15%>&nbsp;</td>
	  <td width=70%><select name=produto class=box>";
	  echo "<option>...</option>";
	$query=pg_query("select *from produto order by pro_nome");
	  while($row=pg_fetch_array($query)) {
	   echo "<option value='$row[pro_codigo]'>$row[pro_nome]</option>";
	  }
 echo "</select></td>
	 <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/add_on.gif></td>
	 </tr>
	</table></form>
   <table width=100% cellspacing=0 cellpadding=2 border=0>
         <tr>
          <td width=15%>&nbsp;</td>
          <td><iframe name=add_produtos src=add_produtos.php?id_login=$id_login&usu_codigo=$usu[usu_codigo] frameborder=no marginheight=0 marginwidth=0 scrolling=yes width=450 height=100></iframe></td>
	 </tr>
   </table><fielset>";




}
if($acao=="atualiza") {
    $sql = pg_query("update atendimento set ate_datafinal='$ate_datafinal',ate_observacao='$ate_observacao',ate_horafinal='$ate_horafinal',cd10_codigo='$ate_cid',ate_diagnostico='$ate_diagnostico',ate_acidentetrab='$ate_acidentetrab',ate_encaminhamento='$ate_encaminhamento' where ate_codigo = '$ate_codigo'");
    echo "<font color=green><b>OK Atualizado</b></font>$ate_codigo";
        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='ambulatorio.php?id_login=$id_login'\", 0);
              </SCRIPT>";
}

?>



