<?php
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>

session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
verauth($id_login);
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
cabecario();
//------------------------------------------------------------------>

?>

<style>
	.linha {
		border-bottom: 1px solid;
		border-right: 1px solid;
		border-color: #C9C9C9;
	}
	.linha2 {
		border-bottom: 1px dotted;
		border-right: 1px dotted;
		border-color: #C9C9C9;
	}
</style>

<?

$stmt = "SELECT uni_codigo FROM usuarios WHERE usr_codigo = $id_login";
$stmt = db_query($stmt);
$dados = pg_fetch_array($stmt);

//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
reglog($id_login,"Entrando em PEDIDO");
//------------------------------------------------------------------>

if(empty($acao) || ($acao == 'form_pedido'))
{
	echo "
	<table width='98%' align='center' cellspacing='0' cellpadding='0' border='0'>
		<tr>
			<td>
				<fieldset>
					<legend>Op&ccedil;&otilde;es</legend>
					<form method='post' action='$PHP_SELF'>
					<input type=hidden name='acao' value=''busca>
					<input type=hidden name='id_login' value='$id_login'>
					<table width='100%' align='center' cellspacing='3' cellpadding='0' border='0'>
						<tr>
							<td width='95'>"
								.ChmodBtn($id_login,'adicionar','pedido.php?acao=form_add').
							"</td>
							<td width='180' align='right'>Buscar</td>
							<td width='90'>
								<input type='text' name='palavra_chave' class='box'
									onBlur=\"javascript:this.value=this.value.toUpperCase();\" />
							</td>
							<td width='79'>
								<a href='movimentacao.php?id_login=$id_login'>
									<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif' border='0'>
								</a>
							</td>
							<td width='107'>
								<a href='logoff.php?id_login=$id_login' target='_parent'>
									<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/sair.gif' border='0'>
								</a>
							</td>
						</tr>
					</table>
					</form>
				</fieldset>
			</td>
		</tr>
    </table>
	<br />
	<table width='98%' align='center' cellspacing='0' cellpadding='0' border='0'>
		<tr>
			<td>
				<fieldset>
					<legend>Listando &Uacute;ltimos <b>15</b> Transfer&ecirc;ncias Cadastradas</legend>
					<table width='100%' align='center' cellspacing='2' cellpadding='4' border='0'>
						<tr bgcolor='#F9F9F9'>
							<td width='40' class='linha'>Data</td>
							<td width='200' class='linha'>Entrada</td>
							<td width='200' class='linha'>Saida</td>
							<td width='200' class='linha'>Num.Movimento</td>
							<td colspan='3' class='linha'>&nbsp;</td>
						</tr>";
						$sql = "select mov_codigo, a.set_nome as desc_saida, b.set_nome as desc_entrada,
								to_char(mov_data, 'DD/MM/YYYY') as mov_data , mov_codigo
								from movimento, setor as a, setor as b
								where movimento.set_saida = a.set_codigo
								and movimento.set_entrada = b.set_codigo
								and mov_tipo = 'S'
								and mov_saida = 'S' "
								.($dados[0]=="" ? "" : " AND b.uni_codigo = ".$dados[0]).
								" order by mov_codigo desc limit 15";
						
						$sql=pg_query($sql);
		
						$controle = 0;
						
						while($row=pg_fetch_array($sql))
						{
							$c1 = "";
							$c2 = "#F2F2F2";
							if($controle == 0)
							{
							  $cor = $c1;
							  $controle++;
							} else {
							  $cor = $c2;
							  $controle = 0;
							}
							echo "
							<tr bgcolor='$cor'>
								<td align='center' class='linha2'>$row[mov_data]</td>
								<td class='linha2'>$row[desc_entrada]</td>
								<td class='linha2'>$row[desc_saida]</td>
								<td class='linha2'>$row[mov_codigo]</td>
								<td width='60' class='linha2'>
									<a href='javascript:abre_movim($id_login, $row[mov_codigo])'>
										<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/imprimir.jpg' border='0'>
									</a>
								</td>
								<td width='60' class='linha2'>"
									.ChmodBtn($id_login,'editar','pedido.php?acao=form_edit&mov_codigo='.$row[mov_codigo]).
								"</td>
								<td width='66' class='linha2'>"
									.ChmodBtn($id_login,'apagar','pedido.php?acao=del&mov_codigo='.$row[mov_codigo]).
								"</td>
							</tr>";
						}
						echo "
					</table>
				</fieldset>
			</td>
		</tr>
    </table>";
}


if($acao == "busca")
{
reglog($id_login,"Buscando em Pedido: $palavra_chave ");

//
//-> Subistituindo o + por porcentagem na busca
	$str = str_replace("+","%",$palavra_chave);
	$pos = strpos($palavra_chave,"+");
	if($pos=="0")
	{
		$v1=1;
	} else {
		$v1=2;
	}

//
//-> Botoes
	echo "
	<table width='98%' align='center' cellspacing='0' cellpadding='0' border='0'>
		<tr>
			<td>
				<fieldset>
					<legend>Op&ccedil;&otilde;es</legend>
					<form method='post' action='$PHP_SELF'>
					<input type='hidden' name='acao' value='busca'>
					<input type='hidden' name='id_login' value='$id_login'>
					<table width='100%' align='center' cellspacing='3' cellpadding='0' border='0'>
						<tr>
							<td width='79'>
								<a href='movimentacao.php?id_login=$id_login'>
									<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif' border='0'>
								</a>
							</td>
							<td width=95>"
								.ChmodBtn($id_login,'adicionar','pedico.php?acao=form_add').
							"</td>
							<td width='180' align='right'>Buscar</td>
							<td width='90'>
								<input type='text' name='palavra_chave' class='box'
									onBlur=\"javascript:this.value=this.value.toUpperCase();\">
							</td>
							<td>"
								.ChmodBtn($id_login,'procurar','pedido.php').
							"</td>
							<td width='107'>
								<a href='logoff.php?id_login=$id_login' target='_parent'>
									<img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/sair.gif border=0>
								</a>
							</td>
						</tr>
					</table>
					</form>
				</fieldset>
			</td>
		</tr>
    </table>
	<br>";

	$sqlv="select mov_codigo, a.set_nome as desc_saida, to_char(mov_data, 'dd/mm/yyyy') as mov_data,
                  mov_data as mov_data2 , mov_codigo, b.set_nome as desc_entrada
                  from movimento, setor as a, setor as b
                  where movimento.set_saida = a.set_codigo
                  and movimento.set_entrada = b.set_codigo
                  and mov_tipo = 'S'
				  and mov_saida = 'S' "
                  .($dados[0]=="" ? "" : " AND b.uni_codigo = ".$dados[0]).
                  " and (a.set_nome like upper('$palavra_chave%')
                  or b.set_nome like upper('$palavra_chave%')
                  or mov_nr_nota = '$palavra_chave' ";
    if(strpos($palavra_chave, "/") != 0)
       $sqlv .= " or mov_data = '$palavra_chave' ";

    $sqlv .= ")
              order by a.set_nome, mov_data2 ";
    $sql = pg_query($sqlv);
	$num = pg_num_rows($sql);
	
	if($num == "0") { $resp = "Nenhum Registro encontrado com \"$palavra_chave\""; }
	if($num == "1") { $resp = "Encontrado <b>$num</b> Registro com \"$palavra_chave\""; }
	if($num > "1") { $resp = "Encontrados <b>$num</b> Registros com \"$palavra_chave\""; }

	echo "
	<table width='98%' align='center' cellspacing='0' cellpadding='0' border='0'>
		<tr>
			<td>
				<fieldset>
					<legend>$resp</legend>
					<table width='100%' align='center' cellspacing='2' cellpadding='4' border='0'>
						<tr>
							<td width='40' class='linha'>Data</td>
							<td width='200' class='linha'>Entrada</td>
							<td width='200' class='linha'>Sa&iacute;da</td>
							<td width='40' class='linha'>Num.Movim.</td>
							<td colspan='2' class='linha'>&nbsp;</td>
						</tr>";
		
						$controle = 0;
						
						while($row=pg_fetch_array($sql))
						{
							$c1 = "";
							$c2 = "#F2F2F2";
			
							if($controle == 0)
							{
								$cor = $c1;
								$controle++;
							} else {
								$cor = $c2;
								$controle = 0;
							}
							echo "
							<tr bgcolor='$cor'>
								<td align='center' class='linha2'>$row[mov_data]</td>
								<td align='center' class='linha2'>$row[desc_entrada]</td>
								<td align='center' class='linha2'>$row[desc_saida]</td>
								<td align='center' class='linha2'>$row[mov_codigo]</td>
								<td width='60' class='linha2'>"
									.ChmodBtn($id_login,'editar','pedido.php?acao=form_edit&mov_codigo='.$row[mov_codigo]).
								"</td>
								<td width='66' class='linha2'>"
									.ChmodBtn($id_login,'apagar','pedido.php?acao=del&mov_codigo='.$row[mov_codigo]).
								"</td>
							</tr>";
						}
					echo "
					</table>
				</fieldset>
			</td>
		</tr>
    </table>";
}


if($acao == "form_add")
{
	reglog($id_login,"Formulario de ADICAO PEDIDO");

	echo "
	<table width='98%' align='center' cellspacing='0' cellpadding='0' border='0'>
		<tr>
			<td>
				<fieldset>
					<legend>Opções de Cadastro</legend>
					<form name='formbusca' method='post' action='$PHP_SELF'>
					<input type='hidden' name='acao' value='form_add'>
					<input type='hidden' name='action' value='buscar'>
					<input type='hidden' name='id_login' value='$id_login'>
					<table width='100%' align='center' cellspacing='3' cellpadding='0' border='0'>
						<tr>
							<td width='79'>
								<a href='pedido.php?id_login=$id_login'>
									<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif' border='0'>
								</a>
							</td>
						</tr>
					</table>
					</form>
				</fieldset>
			</td>
		</tr>
    </table>
	<br>";

	if(($type=="" || $acao=="simples"))
	{
		echo "
		<form method='post' action='$PHP_SELF' onsubmit='return valida_iguais()'>
		<input type='hidden' name='acao' value='add'>
		<input type='hidden' name='id_login' value='$id_login'>
		<input type='hidden' name='type' value='simples'>
		<table width='98%' align='center' cellspacing='0' cellpadding='0' border='0'>
			<tr>
				<td>
					<fieldset>
						<legend>Cabecalho da Transferencia</legend>
						<table width='100%' align='center' cellspacing='3' cellpadding='0' border='0'>";
						$sql = "select nextval('seq_mov_codigo'::text) as novo_codigo";
						$sqlsaida = db_query($sql);
						$rowsaida = pg_fetch_array($sqlsaida);
						echo "<input type='hidden' name='mov_codigo' value='$rowsaida[novo_codigo]'>";
						$sql = "select to_char(current_date, 'dd/mm/yyyy') as data,
								extract(hour from current_time) || ':' || extract(minute from current_time) as hora";
						$sqldata_hora = db_query($sql);
						$rowdata_hora = pg_fetch_array($sqldata_hora);
						$mov_data = $rowdata_hora['data'];
						$mov_dt_nota = $rowdata_hora['data'];
						echo "
							<tr>
								<td width='70'>Centro Estoc. Saida:</td>
								<td>
									<select name='set_saida' id='set_saida' class='box'>";
	
									$query = db_query("select * from setor
													   where set_estoque = 'S'
													   and set_distribuidor = 'S'
													   order by set_nome");
	
									while($cestsaida=pg_fetch_array($query))
									{
										echo "<option value='$cestsaida[set_codigo]'>$cestsaida[set_nome]</option>";
									}
									echo "</select>
								</td>
							</tr>
							<tr>
								<td width='70'>Centro Estoc. Entrada:</td>
								<td>
									<select name='set_entrada' id='set_entrada' class='box'>";
	
									$query = db_query("select * from setor
													   where set_estoque = 'S'"
													   .($dados[0]=="" ? "" : " AND setor.uni_codigo = ".$dados[0]).
													   "order by set_nome");
	
									while($cestentrada=pg_fetch_array($query))
									{
										echo "<option value='$cestentrada[set_codigo]'>$cestentrada[set_nome]</option>";
									}
									echo "</select>
								</td>
							</tr>
							<tr>
								<td width='40'>Data da Transferencia:</td>
								<td>
									<input type='text' name='mov_data' class='box' size='20' value='$mov_data' onKeypress=\"return Ajusta_Data(this, event); \">
								</td>
							</tr>
							<tr>
								<td width='40'>Numero da Transferencia:</td>
								<td>
									<input type='text' name='mov_nr_nota' class='box' size='20' value='$rowsaida[novo_codigo]'>
								</td>
							</tr>
							<tr>
								<td width='40'>Observacao:</td>
								<td><textarea name='mov_observacao' class='box' cols='100' rows='2'></textarea></td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td><input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg'></td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
        </table>
		</form>
		<br>";
	}
}


if($acao=="form_edit")
{
	reglog($id_login,"Formulario de EDICAO DE PEDIDO");

	echo "
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
        <tr>
			<td>
				<fieldset>
					<legend>Opções de Cadastro</legend>
					<table width='100%' align='center' cellspacing='3' cellpadding='0' border='0'>
						<tr>
							<td width='79'>
								<a href='pedido.php?id_login=$id_login'>
									<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif' border='0'>
								</a>
							</td>
							<td>&nbsp;</td>
						</tr>
					</table>
				</fieldset>
			</td>
        </tr>
    </table>
	<br>";

	$sql = "select mov_codigo, to_char(mov_data, 'dd/mm/yyyy') as mov_data, mov_tipo,
			mov_desconto, mov_observacao, set_entrada,
			retorna_usuario(usr_codigo) as login_usuario,
			set_saida, mov_nr_nota, to_char(mov_dt_nota, 'dd/mm/yyyy') as mov_dt_nota,
			to_char(mov_data_inclusao, 'dd/mm/yyyy') as mov_data_inclusao, mov_total_nota
			from movimento
			where  mov_codigo = '$mov_codigo'";
	$sqlmovimento =  pg_query($sql);
	$row = pg_fetch_array($sqlmovimento);

	echo "<br><br>
	<form method='post' action='$PHP_SELF'>
	<input type='hidden' name='acao' value='edit'>
	<input type='hidden' name='id_login' value='$id_login'>
	<input type='hidden' name='mov_codigo' value='$mov_codigo'>
	<fieldset>
		<legend>Transferencia de Materiais</legend>
		<table width='98%' align='center' cellspacing='0' cellpadding='0' border='0'>
			<tr>
				<td>
					<table width='100%' align='center' cellspacing='3' cellpadding='0' border='0'>";
						echo "Ultima Alteracao: $row[login_usuario] - $row[mov_data_inclusao]";
						$sql = "select set_codigo, set_nome
								from setor
								where set_codigo = '$row[set_entrada]'";
						$sqlunidadee = pg_query($sql);
						$rowunidadee = pg_fetch_array($sqlunidadee);
						echo "
						<input type='hidden' name='set_entrada' value='$rowunidadee[set_codigo]'>
						<tr>
							<td width='20'>Centro Est. Entrada</td>
							<td width='70'>
								<input type='text' readonly name='uni_desc' size='70' value='$rowunidadee[set_nome]'>
							</td>
						</tr>";
						$sql = "select set_codigo, set_nome
								from setor
								where set_codigo = '$row[set_saida]'";
						$sqlunidades = pg_query($sql);
						$rowunidades = pg_fetch_array($sqlunidades);
						echo "
						<input type='hidden' name='set_saida' value='$rowunidades[set_codigo]'>
						<tr>
							<td width='20'>Centro Est. Saida</td>
							<td width='70'>
								<input type='text' readonly name='set_nome' size='70' value='$rowunidades[set_nome]'>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td width='70'>Data da Saida:</td>
				<td>
					<input type='text' name='mov_data' class='box' size='20' value='$row[mov_data]' onKeypress=\"return Ajusta_Data(this, event); \">
				</td>
			</tr>
			<tr>
				<td width='40'>Numero da Transferencia:</td>
				<td>
					<input type='text' name='mov_nr_nota' class='box' size='20' value='$row[mov_nr_nota]'>
				</td>
			</tr>
			<tr>
			<td width='40'>Observacao:</td>
				<td>
					<textarea name='mov_observacao' class='box' cols='100' rows='2'>$row[mov_observacao]</textarea>
				</td>
			</tr>
			<tr>
				<td>
					<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg'>
				</td>
			</tr>
		</table>
	</fieldset>
	<br>
	</form>";
}


if($acao=="add")
{
	reglog($id_login,"Adicionando Registro em PEDIDO");


    echo "
	<SCRIPT LANGUAGE=\"JavaScript\">;
            setTimeout(\"location='itens_pedido.php?id_login=$id_login&mov_codigo=$mov_codigo'\", 0);
	</SCRIPT>";
}


if($acao=="edit")
{
	
	reglog($id_login,"Editando PEDIDO $mov_codigo");

	msg($acao,$sql);

    echo "
	<SCRIPT LANGUAGE=\"JavaScript\">
        setTimeout(\"location='itens_pedido.php?id_login=$id_login&mov_codigo=$mov_codigo'\", 0);
    </SCRIPT>";
}


if($acao=="del")
{
	reglog($id_login,"Exluindo Registro de PEDIDO $mov_codigo");

	msg($id_login,$acao,$sql);
}

?>