  <?php
  	session_start();
  	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
  	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

    $dini = $_REQUEST['data_ini'];
    $dfim = $_REQUEST['data_fim'];
    $prg = $_REQUEST['prg_codigo'];

  cabecario_rel('Relatorio Programa Produto',$dini,$dfim,null);

  $sql = pg_query("select usu.usu_codigo,usu_nome,to_char(usu_datanasc,'DD/MM/YYYY') as datanasc,calcula_idade(usu.usu_codigo) as idade from programa_produto as pp 
  join programa_atendimento as pa on pa.prg_codigo = pp.prg_codigo 
  join produto as pro on pro.pro_codigo = pp.pro_codigo 
  join cota_paciente as cp on cp.prgp_codigo=pp.prgp_codigo 
  join usuario as usu on usu.usu_codigo = cp.usu_codigo where pp.prg_codigo = $prg
  group by usu.usu_codigo,usu_nome,datanasc,idade order by usu_nome") or die(pg_last_error());

  if(pg_num_rows($sql)==0) { echo 'Nenhum Dado Cadastrado.'; exit; } 
  echo "<table class='lista' width='900'cellspacing=3 cellpadding=5 border=1>";
  while($rr=pg_fetch_array($sql)) {
    if($programa!= $rr[prg_nome]) {
       echo "<tr>
              <td colspan='7' bgcolor='#ebebeb' height='30'><b>$rr[prg_nome]</b></td>
             </tr>";
       echo "<tr>
              <td colspan='7' height='30'>&nbsp;</td>
             </tr>";
    }
       $programa = $rr[prg_nome];


        echo "<tr>
             <td height='30'><b>$rr[usu_nome]</b></td>
             <td>$rr[idade] anos</td>
             <td>$rr[datanasc]</td>
             <td>$sexo</td>
             </tr>";

     $query = pg_query("select to_char(usu_datanasc,'DD/MM/YYYY') as datanasc,calcula_idade(usu.usu_codigo) as idade,* from programa_produto as pp 
  join programa_atendimento as pa on pa.prg_codigo = pp.prg_codigo 
  join produto as pro on pro.pro_codigo = pp.pro_codigo 
  join cota_paciente as cp on cp.prgp_codigo=pp.prgp_codigo 
  join usuario as usu on usu.usu_codigo = cp.usu_codigo where cp.usu_codigo = $rr[usu_codigo]") or die(pg_last_error());
    while($rw=pg_fetch_array($query)) {      
  $med = pg_fetch_array(pg_query("select *from movimento as mov join itens_movimento as ite on ite.mov_codigo=mov.mov_codigo where pro_codigo = '$rw[pro_codigo]' and usu_codigo = $rr[usu_codigo] and mov_data >= '".$data_ini."' and mov_data <= '".$data_fim."'"));

  if(($med[pro_nome]==$rr[pro_nome] and $med[usu_codigo]==$rr[usu_codigo])) {
    $msg = '<font color=green><b>Dispensado</b></font>';
  } else {
    $msg = '<font color=red><b>Faltou</b></font>';
  }

  $sexo = ($rw[usu_sexo]=='F')?"Fem.":"Masc.";

        echo "<tr>
             <td>$rw[pro_nome] $rw[usu_codigo] $rw[pro_codigo]  </td>
             <td>$rw[ctp_quantidade]</td>
             <td>$rw[ctp_periodo]</td>
             <td> $msg</td>
             </tr>";
      }
       echo "<tr>
              <td colspan='7' height='30'>&nbsp;</td>
             </tr>";

  }
  echo "</table>";
  ?>