<?

	
session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
verauth($id_login);
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
cabecario( $hotkey = true);

reglog($id_login,"Acessando LISTA DE EXAMES");

if($action=="sw") {
 if($agexl_status == "R") { 
    $status = "A";
 } else {
   $status = "R";
   $controle = 1;
   $dd = pg_fetch_array(pg_query("select to_char(current_date,'DD/MM/YYYY') as dataatual,(agexl_dt_atualizacao + interval '7 days') as dataini,agexl_dt_atualizacao from agendamento_exame_lista order by agexl_dt_atualizacao limit 1"));
   $id_dia = $dd[dataatual];
$exp=explode("/",$id_dia);
$ALLSEMANA = date('w', mktime(0,0,0,$exp[1],$exp[0],$exp[2]));
$rw = pg_fetch_array(pg_query("select max(cod_controle) as cod_controle from agendamento_exame limit 1"));
    if($rw[cod_controle]=="") {
       $control = 1;
    } else {
      if($ALLSEMANA == 1) {
         $control = 1;
      } else {
         $control = ($rw[cod_controle]+1);
      }
    }
      $query = pg_query("update agendamento_exame set cod_controle = '$cod_controle' where agex_codigo = '$agex_codigo'");
}
   $sql = pg_query("update agendamento_exame_lista set agexl_status = '$status',usr_codigo_alt = '$id_login',agexl_dt_atualizacao=CURRENT_TIMESTAMP where agex_codigo = $agex_codigo") or die(pg_last_error());
      echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='$PHP_SELF?id_login=$id_login&acao=$acao&age_data=$age_data&med_codigo=$med_codigo&cod'\", 1);
              </SCRIPT>";

}

//
//-> Botoes
?>
<script type="text/javascript" src="../funcoes.js"></script>
<script type="text/javascript" src="../ajax_motor.js"></script>
<script type="text/javascript" src="../recepcao.js.php"></script>
<script type='text/javascript' src='https://www.google.com/jsapi'></script>
  <script type='text/javascript'>
      google.load('visualization', '1', {packages:['orgchart']});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Name');
        data.addColumn('string', 'Manager');
        data.addColumn('string', 'ToolTip');
        data.addRows([
                    
          
         [{v:'Osvaldo', f:'Osvaldo De Araujo<div style="color:red; font-style:italic font-size:30"></div>'}, '', 'VP'],
        // [{v:'Vera', f:'Vera Lúcia<div style="color:red; font-style:italic font-size:30"></div>'}, '', 'VP'],
         //[{v:'Anderson', f:'Anderson Bernini<div style="color:red; font-style:italic">Chefe Da Familia</div>'}, '', ''],


          ['Anderson', 'Osvaldo', 'Vera'],
          ['Andressa', 'Osvaldo', 'Vera'],
         // ['Anderson', 'Vera', 'Osvaldo'],
          //['Anderson', 'Osvaldo', 'Osvaldo'],
          ['Matheus', 'Anderson', 'Matheuzinho'],
          ['Lucas', 'Anderson', 'Lucão'],
          ['Felipe', 'Anderson', 'Felipe'],
          ['Victor', 'Andressa', '']
//          ['Joao', 'Bob', ''],
//          ['Mike', 'Jim', 'teste'],
//		  ['Maria', 'Bob', ''],
//		  ['Jureza', 'Bob', ''],
//          ['Carol', 'Bob', ''],
//		  ['Luiza', 'Carol', '']
        ]);
        var chart = new google.visualization.OrgChart(document.getElementById('chart_div'));
		document.getElementById('chart_div').style.fontSize =50;
        chart.draw(data, {allowHtml:true});
      }
    </script>
<script>
function val () {
 if(document.form.med_codigo.value == "0") { alert("Selecione um Laboratorio"); return false; }
 if(document.form.age_data.value == "") { alert("Por favor - Preencha a Data"); return false; }
}
</script>
<?
  

//echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
//         <tr>
//          <td>
//           <fieldset>
//            <legend>Mapa de Ocupacao de Alas</legend>
//            <form method=post action='$PHP_SELF' name='form' onSubmit='return val();'>
//	    <input type=hidden name=acao value=listar> 
//	    <input type=hidden name=id_login value=$id_login> 
//            <table width='100%' align='center' cellspacing='2' cellpadding='4' border='0' class='lista'>
//              <tr bgcolor=FFFFFF>
//		 <td width='50'>Ala:</td>
//		 <td width='50'><input type='text' name='buscaPaciente' class='box' size='40'></td>
//		 <td><input src=img/buscar_on.jpg id='buscarPaciente' style='cursor: pointer;'type='image' border='0'></td>
//		</tr>                
//	     </table></form>
//	    </fieldset>
//	   </td>
//	  </tr>
//         </table>";


//if($acao=="listar") {

echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
         <tr>
          <td>
           <fieldset>
            <legend>Genograma</legend>
           <table style='width:600px;' border='0'>
		   	<tr>
				<td style='width:100px;' align='center'><div id='chart_div'></div></td>
			
			
			</tr>
			
			
		   </table>
             
           </fieldset>
          </td>
         </tr>
      </table>";
	  
