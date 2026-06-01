<?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>

	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	verauth($id_login);
	cabecario();
//------------------------------------------------------------------>


//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
	 reglog($id_login,"Entrando em LIST_PACIENTES");
//------------------------------------------------------------------>
$usu_codigo = $_GET["usu_codigo"];

if(empty($acao))
{

//
//-> Listando


  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Programas do Paciente</legend>
	     <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
	      <tr bgcolor=F9f9f9>
		<td width=250 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Programas</td>";
   /*
	$sql=pg_query("SELECT programa_atendimento.prg_nome
			FROM programa_produto
			LEFT JOIN programa_atendimento ON 
				programa_atendimento.prg_codigo = programa_produto.prg_codigo
			LEFT JOIN cota_paciente ON cota_paciente.prgp_codigo = programa_produto.prgp_codigo
			LEFT JOIN usuario ON usuario.usu_codigo = cota_paciente.usu_codigo
			WHERE usuario.usu_codigo = '".$usu_codigo."'
			GROUP BY programa_atendimento.prg_nome
			ORDER BY programa_atendimento.prg_nome");
   */
	$sql = pg_query("SELECT pa.prg_nome FROM v_movimentacao AS vm 
			LEFT JOIN programa_produto AS pp ON pp.pro_codigo = vm.pro_codigo 
			LEFT JOIN programa_atendimento AS pa ON pa.prg_codigo = pp.prg_codigo 
			LEFT JOIN cota_paciente AS cp ON cp.prgp_codigo = pp.prg_codigo 
			WHERE vm.usu_codigo = ".$usu_codigo." AND vm.tipomovim = 'D' 
			AND cp.prgp_codigo is not null
			GROUP BY pa.prg_nome");
     while($row=pg_fetch_array($sql))
     {
       echo "<tr>
	       <td align='left' style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[0]</td>
	     </tr>";
     }
	echo "</tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table>";
}
?>