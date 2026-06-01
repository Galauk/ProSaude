<script language='JavaScript'>
function imprimir() {
	self.print();
}
</script>
<?
	include "../global.php";
	$sqlSecretaria = pg_query("SELECT * FROM secretaria WHERE tipo_secretaria = 'SAU'");
	$registroSecretaria = pg_fetch_array($sqlSecretaria);
	session_start();
	//cabecalho padrão para todos os relatorios
	echo "<table style='font-size:12px;font-family:Tahoma,Arial;' width=100% cellspacing=0 cellpadding=0 border=0 align=center opmargin=0 leftmargin=0>
		<tr>
			<td rowspan='";
	if( !empty($dtFin) ){ echo "5"; }else{ echo "4"; }
	echo "' align='center' width='1px'>&nbsp;</td>
			<td width=120 valign='bottom' height=20>$registroSecretaria[nome_secretaria]</td>
			<td rowspan='2' width= 120 align='right' valign='top'> ";
			if( empty($btprint) )
			{
				$caminho = $_SESSION[linkroot].$_SESSION[comum].'imgs/imprimir.jpg';
				echo "<input type='image' name='imprimir' src='$caminho' alt='Imprimir' onclick='imprimir()'>";
			}
			else
			{
				echo "&nbsp;";
			}
		echo "</td>
		</tr>
		<tr>
			<td width=120 valign='top'>GESTÃO PÚBLICA DE SAÚDE</td>
		</tr>";
		if( !empty($dtFin) )
		{
			echo "
			<tr>
				<td valign='top'>PERIODO: $dtIni AT&Eacute; $dtFin </td>
				<td> &nbsp; </td>
			</tr>";
		}
			echo "
			<tr>
				<td valign='top'>".strtoupper($Tit)."</td>
				<td colspan='2' width= 120 align='right'><font align=right>".date("d/m/Y h:i:s")."</font></td>
			</tr>";
		//inicio das particularidades de alguns relatorios
		//para saber referente a qual relatorio veja as variaveis que o mesmo envia para o include
		if( !empty($CE) )
		{
			echo "<tr>
				<td>CENTRO ESTOCADOR: ".$CE."</td>
				<td>&nbsp;</td>
			</tr>";
			if (!empty($Setor))
			{
				echo "
				<tr>
					<td>&nbsp;</td>
					<td>Setor: ".$Setor."</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>Grupo Produto: ".$Grupo;
					if( !empty($Produto) )
					{
						echo " - Produto: ".$Produto;
					}
					echo "</td>
					<td>&nbsp;</td>
				</tr>";
			}
		}
		elseif( !empty($Proc) )
		{
			echo "<tr>
				<td>PROCEDIMENTO: ".$Proc."</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>TIPO DE CUSTO: ".$TPCust."</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>SINTET / ANALIT: ".$SinteAnali."</td>
				<td>&nbsp;</td>
			</tr>";
		}
		elseif( !empty($UniNo) || !empty($Uni) )
		{
			$Unidade = $UniNo = $Uni;
			echo "<tr>
				<td>UNIDADE: ".$Unidade."</td>
				<td>&nbsp;</td>
			</tr>";
		}
		elseif( !empty($SetNo) )
		{
			echo "<tr>
				<td>SETOR:   ".$SetNo."</td>
				<td>&nbsp;</td>
			</tr>";
			if( !empty($GruNo) )
			{
				echo "<tr>
					<td>&nbsp;</td>
					<td>Grupo:   ".$GruNo."</td>
					<td>&nbsp;</td>
				</tr>";
			}
			if( !empty($zerado) )
			{
				echo "<tr>
					<td>&nbsp;</td>
					<td>Lista Zerado:   ".$zerado."</td>
					<td>&nbsp;</td>
				</tr>";
			}
			if( !empty($pro_codigo) || !empty($ProNo) )
			{
				if( !empty($ProNo) )
				{
					$produto = $ProNo;
				}
				else
				{
					$produto = $pro_codigo;
				}
				echo "<tr>
					<td>&nbsp;</td>
					<td>PRODUTO:   ".$produto."</td>
					<td>&nbsp;</td>
				</tr>";
			}
		}
		elseif( !empty($Grupo) )
		{
			echo "<tr>
				<td>Grupo: ".strtoupper($Grupo)."</td>
				<td>&nbsp;</td>
			</tr>";
		}
		elseif( !empty($Pac) )
		{
			echo "<tr>
				<td>USUARIO: ".$Pac."</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>MEDICO: ".$Med."</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>PROCEDIMENTO: ".$Proc."</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>TIPO DE CUSTO: ".$TPCust."</td>
				<td>&nbsp;</td>
			</tr>";
		}
		elseif( !empty($tipo) )
		{
			echo "<tr>
				<td>TIPO DE CONSUMO: ";
			switch($tipo)
			{
				case 'S': echo 'Saida de Consumo'; break;
				case 'I': echo 'Inventario'; break;
				case 'M': echo 'Emprestimo'; break;
				case 'P': echo 'Permuta'; break;
				case 'R': echo 'Perdas'; break;
				case 'O': echo 'Outras Saidas'; break;
				case 'E': echo 'Nota Fiscal de Compra'; break;
				case 'A': echo 'Ajuste'; break;
				case 'D': echo 'Doacao'; break;
				case 'V': echo 'Devol. Setor'; break;
				case 'T': echo 'Transferência'; break;
			}
			echo "</td>
			<td>&nbsp;</td>
			</tr>";
		}
		else
		{
			echo "<tr>
				<td>$dados_compet</td>
				<td>&nbsp;</td>
			</tr>";
		}
          echo "<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
	</table>";
?>