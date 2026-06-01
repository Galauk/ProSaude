<?php
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

	verauth($id_login);
	cabecario();
//------------------------------------------------------------------>

// Permissão de usuários acessos

$sql = "select * from usuarios_acessos where usr_codigo = '$id_login'";
						
$exec_sel = db_query($sql);

$controle = ((pg_num_rows($exec_sel) > 0) ? 0 : 1 );

//

?>

<style>
	.borda
	{
		border-bottom: 1px solid;
		border-top: 1px solid;
		border-left:1px solid;
		border-color: #909090;
	}
	.borda2
	{
		border-bottom: 1px solid;
		border-color: #909090;
	}
	.red_titulo
	{
		color: red;
		font-weight: bold;
		border-bottom: 1px solid;
		border-top: 1px solid;
		border-left:1px solid;
		border-color: #909090;
	}
	.fundo_linha
	{
		background: #CCCCCC;
	}
	.fundo_linha_bold
	{
		background: #CCCCCC;
		font-weight: bold;
	}
	.green
	{
		color: green;
		
	}
	.blue
	{
		color: blue;
	}
	.black
	{
		color: black;
	}
	.orange
	{
		color: orange;
	}
	.red
	{
		color: red;
	}
	.purple
	{
		color: purple;
	}
	.fundo_tabela
	{
		/*background: #F7F7F7;*/
	}
</style>
<script type="text/javascript" src="funcoes.js"></script>
<script type="text/javascript" src="ajax_motor.js"></script>
<script type="text/javascript" src="recepcao.js.php"></script>

    <fieldset>
        <legend>Opções</legend>
        <table width='100%' align='center' cellspacing='3' cellpadding='0' border='0'>
            <tr>
                <td width='126'>
					<?=ChmodBtn($id_login,'fazeragendamento','fazer_agendamento.php?')?>
				</td>
                <td width='146'>
					<?=ChmodBtn($id_login,'manutencaoagenda','manutencaomedicos.php?')?>
				</td>
                <td width='195'>
					<?=ChmodBtn($id_login,'manutencaogrupodeagente','manutencaoagentes.php?')?>
				</td>
                <td width='57'>
					<?=ChmodBtn($id_login,'pam','ambulatorio.php?')?>
				</td>
                <td>
				<?=ChmodBtn($id_login,'feriado','feriado.php?')?>
				</td>
                <td>
					<a href='logoff.php?id_login=$id_login' target='_parent'>
						<img src='<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/sair.gif' border='0'>
					</a>
				</td>
            </tr>
        </table>
    </fieldset>
	<fieldset>
		<legend>Recepção</legend>
		<table width='100%' align='center' cellspacing='3' cellpadding='0' border='0'>
			<tr>
				<td width="100" align="right">
					Unidade de saúde
				</td>
				<td colspan="2">
					<select id="uni_codigo" class="box" onchange="buscar_especialidade(<?=$controle?>)">
						<option value="">---</option>
						<?php
			
						if( pg_num_rows($exec_sel) > 0 )
						{
							$and_Select = "where uni_codigo in (select uni_codigo
											from usuarios_acessos
											where usr_codigo = $id_login)";
						}
	
						$sql = "select uni_codigo, uni_desc
								from unidade
								$and_Select
								order by uni_desc";
								
						$exec = db_query($sql);
						
						while($uni = pg_fetch_array($exec))
						{
							echo "<option value='$uni[uni_codigo]'>$uni[uni_desc]</option>";
						}
						
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td align="right">
					Profissional
				</td>
				<td colspan="2">
					<select id="med_codigo" class="box" onchange="buscar_especialidade(<?=$controle?>)">
						<option value="">---</option>
						<?php
						
						if(pg_num_rows($exec_sel) > 0)
						{
							$sql = "select *
									from medico
									where med_codigo in (select med_codigo
														from usuarios_acessos
														where usr_codigo = $id_login
														and uni_codigo = $uni_codigo)
									order by med_nome";
						}
						else
						{
							$sql = "select * from medico order by med_nome";
						}
						
						$exec = db_query($sql);
            
						while($med = pg_fetch_array($exec))
						{
						   echo "<option value='$med[med_codigo]'>$med[med_nome]</option>";
						}
						
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td align="right">
					Especialidade
				</td>
				<td colspan="2">
					<select id="esp_codigo" class="box" onchange="habilitar();">
						<option value="">---</option>
					</select>
				</td>
			</tr>
			<tr>
                <td width='15' align='right'>
					Data
				</td>
                <td width='85'>
					<input type='text' class='box' size='12' id='age_data' maxlength='10' onkeypress="return Ajusta_Data(this, event);">
				</td>
                <td>
					<img src='<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/listarpacientes_off.jpg' id='btn_lista_paciente' alt='Listar Pacientes' border='0' style='cursor:pointer' onclick="buscar_agendados();">
				</td>
            </tr>
		</table>
	</fieldset>
	<fieldset>
		<legend>Lista</legend>
		<div id="agendados">
		</div>
	</fieldset>
	
	