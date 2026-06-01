<?php
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	require_once $_SESSION[root].$_SESSION[comum].'class/commonClass.php';
	verauth($id_login);
	cabecario();
//------------------------------------------------------------------>

// Permissão de usuários acessos

$sql = "SELECT * 
		  FROM usuarios_acessos 
		 WHERE usr_codigo = '$id_login'";
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
		height: 18px;
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
	.cadastrador
	{
		font-size: 13px;
		font-weight: bold;
		color: #2F4F4F;
	}
	.alterador
	{
		font-size: 13px;
		font-weight: bold;
		color: #000080;
	}

</style>
<script type="text/javascript" src="/WebSocialComum/library/js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="/WebSocialSaude/lib/ui/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="<?=$_SESSION[linkroot].$_SESSION[comum]?>library/js/funcoes.js"></script>
<script type="text/javascript" src="<?=$_SESSION[linkroot].$_SESSION[comum]?>library/js/ajax_motor.js"></script>
<script type="text/javascript" src="recepcao.js.php"></script>

    <fieldset>
        <legend>Opções</legend>
        <table width='100%' align='center' cellspacing='3' cellpadding='0' border='0'>
            <tr>
                <td width='126'>
					<?=ChmodBtn($id_login,'fazeragendamento','fazer_agendamento.php?')?>
				</td>
                <td width='146'>
					<?=ChmodBtn($id_login,'manutencao_de_agenda','manutencaomedicos.php?')?>
				</td>
                <td width='195'>
					<?=ChmodBtn($id_login,'manutencaogrupodeagente','manutencaoagentes.php?')?>
				</td>
                <td>
					<a href=age_horario.php><img src=imgs/age_horario.png border=0></a>
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
				<?
				$sqlUni = "SELECT uni_codigo 
							 FROM usuarios 
							WHERE usr_codigo = $id_login";
				$row = pg_fetch_array(pg_query($sqlUni));
				//echo $row[0];
				?>
				
				<td colspan="2" width='150'>
					<select id="uni_codigo" class="box" onchange="buscar_especialidade(<?=$controle?>)">
						<option value="">---</option>
						<?php
			
						//if( pg_num_rows($exec_sel) > 0 )
						//{
						//	$and_Select = "where uni_codigo in (select uni_codigo
						//					from usuarios_acessos
						//					where usr_codigo = $id_login)";
						//}
						if ($row[0]){
							$sql = pg_query("SELECT * 
											   FROM unidade 
											  WHERE uni_codigo = $row[0] 
											  ORDER BY uni_desc");
						}else {
							$sql = pg_query("SELECT * 
											   FROM unidade 
											  ORDER BY uni_desc");
						}
						while($uni=pg_fetch_array($sql)){
							echo "<option value='$uni[uni_codigo]'>$uni[uni_desc]</option>";
						}
	
						//$sql = "select uni_codigo, uni_desc
						//		from unidade
						//		$and_Select
						//		order by uni_desc";
						//		
						//$exec = db_query($sql);
						//
						//while($uni = pg_fetch_array($exec))
						//{
						//	echo "<option value='$uni[uni_codigo]'>$uni[uni_desc]</option>";
						//}
						
						?>
					</select>
				</td>
				<td class='cadastrador' width='125' id='cadastrado_por'>
					&nbsp;
				</td>
				<td width='*' class='cadastrador'>&nbsp;
					<span id='nome_cadastrador'></span>
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
						
						$sql = "SELECT *
								  FROM usuarios
								 WHERE usr_tipo_medico in ('A','E','M','D')
								 ORDER BY usr_nome";
						
						$exec = db_query($sql);
            
						while($med = pg_fetch_array($exec))
						{
						   echo "<option value='$med[usr_codigo]'>$med[usr_nome]</option>";
						}
						
						?>
					</select>
				</td>
				<td class='alterador' id='alterado_por'>
					&nbsp;
				</td>
				<td class='alterador'>&nbsp;
					<span id='nome_alterador'></span>
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
					<img src='<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/listarpacientes_off.jpg' id='btn_lista_paciente' alt='Listar Pacientes' border='0' style='cursor:pointer'>
					<img src='<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/imprimir_off.jpg' id='btn_lista_paciente2' alt='Listar Pacientes' border='0' style='cursor:pointer'>
				</td>
            </tr>
		</table>
	</fieldset>
	<fieldset>
		<legend>Lista&nbsp;&nbsp;&nbsp;<span id='carregando' style='display:none;font-size:11px;font-weight:bold;color:#000066;width:100px;height:18px;background: #FFFFFF;border:1px solid black;'>Carregando...</span></legend>
		<div id="agendados">
		</div>
		</div>
	</fieldset>
	
	
