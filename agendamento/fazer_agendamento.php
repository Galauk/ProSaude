<?php
session_start();
/**
 * Arquivo principal do agendamento !
*/

include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
verauth($id_login);

require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
cabecario( $hotkey = true);

reglog($id_login,"Acessando Fazer Agendamento");
?>

<script type="text/javascript" src="../funcoes.js"></script>
<script type="text/javascript" src="../ajax_motor.js"></script>
<script type="text/javascript" src="fazer_agendamento.js.php?id_login=<?=$id_login?>"></script>

<!--<pre id='teste'>&nbsp;</pre>-->

	<fieldset>
	<legend>Prestador</legend>
	<table cellpadding='4' border=0>
	<tr>
		<td align='right'>Atividade prof.</td>
		<td>
			<select id='esp_codigo' class='boxa' onchange='at_medico()'>
				<option value='0'>....</option>
				<?php
					$stmt = "SELECT esp_codigo, esp_nome FROM especialidade ORDER BY esp_nome";
					$qry = db_query( $stmt );
					while( $esp = pg_fetch_array($qry) )
					{
						echo "\n\t\t\t<option value='{$esp[0]}'>{$esp[1]}</option>";
					}
				?>
			
			</select>
		</td>
		<td width='1'>&nbsp;</td>
	    <td align='right' width='130' colspan='2'>Item de Agendamento</td>
		<td colspan='2' align='right'>
			<select name='age_item' id='age_item' class='boxa' onchange='at_iframe_esq();'>
				<option value='0'>....</option>
				<option value='ES'>ESPECIALIDADE</option>
				<option value='CB'>CLÍNICA BÁSICA</option>
			</select>
  		</td>
	</tr>
	<tr>
	    <td align='right'>Unidade de Saúde</td>
	    <td>
			<select name='uni_codigo' id='uni_codigo' class='boxa' onchange='at_iframe_esq();'>
				<option value='0'>....</option>
				<?php
					$stmt = "SELECT uni_codigo, uni_desc FROM unidade ORDER BY uni_desc";
					$qry = db_query($stmt);
					while( $uni = pg_fetch_array($qry) )
					{
						echo "\n\t\t\t<option value='{$uni[0]}'>{$uni[1]}</option>";
					}
				?>
			
			</select>
		</td>
		<td>&nbsp;</td>
	    <td align='right' colspan='2'>Profissional</td>
	    <td colspan='2' align='right'>
			<select id='med_codigo' class='boxa' disabled="disabled" onchange="preferencia_dia()">
		      <option value='0'>....</option>
			</select>
		</td>
	</tr>
	<tr>
		<td align='right'>Tipo de Agendamento</td>
        <td>
			<select name='age_tipo' id='age_tipo' class='box' onchange='at_iframe_esq();'>
				<option value='0'>...</option>
	                        <option>PC</option>
                                <option>GE</option>
                                <option>RT</option>
                                <option>AL</option>
                                <option>CA</option>
                                <option>CT</option>
                                <option>DI</option>
				<option>EX</option>
			</select>
		</td>
		<td>&nbsp;</td>
		<td align='right' width='90'>Preferência de dia&nbsp;</td>
		<td width='10'>
			<input type='text' id='pref_dia' class='boxl' size='12' maxlength='10'
			   onKeypress="return Ajusta_Data(this, event);" onchange="preferencia_dia(true)"/>
		</td>
		<td width='135' align='right'>Preferência de Horário&nbsp;</td>
		<td align='right'>
			<select id='pref_horario' class='boxl' disabled='disabled' style='width:85px;'
				onchange='at_iframe_esq()'>
				<option value='0'>...</option>
			</select>
		</td>
	</tr>
	</table>
	</fieldset>

	<fieldset>
	<legend>Agente de saúde</legend>
	<table cellpadding='4'>
	<tr>
	    <td width='113' align=right>Agente de saúde</td>
	    <td>
			<select id='agt_codigo' class='boxa' onchange='at_agente()'>
				<option value='0'>....</option>
				<?php
$usr_uni_codigo = db_get("SELECT uni_codigo FROM usuarios WHERE usr_codigo = $id_login" );
					$id_login = intval($id_login);
					
					// sqls especifico para APUCARANA
					/*
					$stmt = 'SELECT a.agt_codigo, a.agt_descricao
							FROM usuarios AS u
							INNER JOIN agente AS a ON
								( a.uni_codigo = u.uni_codigo OR agt_codigo IN (384931,393519) )
							WHERE usr_codigo = '.$id_login . '
							ORDER BY agt_descricao';
					*/
										
					$stmt = "SELECT agt_codigo, agt_descricao ".
								"FROM agente ".
								( empty($usr_uni_codigo) ? '' :
									"WHERE uni_codigo = $usr_uni_codigo OR ".
										"agt_codigo IN (384931,393519) " ).
								"ORDER BY agt_descricao";
				
					$qry = db_query($stmt);
					
					while($agt=pg_fetch_array($qry))
					{
					  echo "\n\t\t\t<option value='{$agt[0]}'>{$agt[1]}</option>";
					}
				?>
			
			</select>
		</td>
		<td><input type='text' id='agt_numero' class='boxl' size='15' readonly='readonly'/></td>
		<td><input type='text' id='agt_responsavel' class='boxl' size='50' readonly='readonly'/></td>
	</tr>
	</table>
	</fieldset>
  
	<fieldset>
	<legend>Dados do Paciente<span id='pac_busca_status' style='font-style:italic;'>&nbsp;</span></legend>
	<table cellpadding='4'>
	<tr>
	    <td width='80' align='right'>Prontu&aacute;rio</td>
	    <td width='120'>
            <input type='hidden' id='pac_codigo' size='10' />
            <input type='text' id='pac_prontuario' class='boxl' size='10' onchange="busca_pac_prontuario()" />
			
        </td>
	    <td align='right'>Paciente</td>
	    <td style='white-space:nowrap;'>
			<input type='text' id='pac_nome' class='boxl' size='60' style="text-transform:uppercase;" onkeypress="if(event.keyCode == 13){buscar_nome($F('pac_nome'), 'buscar_nome');}" />
			<a href='#' onclick="buscar_nome($F('pac_nome'), 'buscar_nome');return false;link_f7()">
				<img src='<?=$_SESSION[linkroot].$_SESSION[comum]; ?>imgs/localizar.jpg' style='vertical-align:middle' border='0' alt='Localizar' />
			</a>(F7)
			<?=divBuscaPaciente();?>
		</td>
		<td>Nascimento</td>
	    <td>
			<input type='text' id='pac_nascimento' class='boxl' size='12' maxlength='10' onkeypress="if(event.keyCode == 13){buscar_nome($F('pac_nascimento'), 'buscar_data');}return Ajusta_Data(this, event);" />
			<a href='#' onclick="buscar_nome($F('pac_nascimento'), 'buscar_data');return false;link_f7()">
				<img src='<?=$_SESSION[linkroot].$_SESSION[comum]; ?>imgs/localizar.jpg' style='vertical-align:middle;' border='0' alt='Localizar' />
			</a>
		</td>
	</tr>
	<tr>
	    <td align='right'>Cidade</td>
	    <td>
			<input type='text' id='pac_cidade' class='boxl' size='20' readonly='readonly'>
		</td>
		<td align='right'>M&atilde;e</td>
	    <td>
			<input type='text' id='pac_mae' class='boxl' size='50' readonly='readonly'/>
		</td>
		<td colspan='2' style='white-space:nowrap;'>
			<a href='#' onclick="link_f8()">
				<img src='<?=$_SESSION[linkroot].$_SESSION[comum]; ?>imgs/ficha_on.jpg' style='vertical-align:middle' border='0' alt='Ficha'/>
			</a>
			<img id='btn_enviar' src='<?=$_SESSION[linkroot].$_SESSION[comum]; ?>imgs/enviar_dados_off.jpg' style='vertical-align:middle;cursor:pointer'
				alt='Enviar' onclick='at_iframe_esq(true);at_iframe_dir();' />
	</tr>
	</table>
	</fieldset>
	
	<table>
	<tr>
		<td width='50%'>
			<fieldset>
				<legend>Agendamento</legend>
				<iframe id='iframe_esq' name='iframe_esq' src='about:blank' frameborder='no'
					marginheight='0' marginwidth='0' scrolling='yes' width='100%' height='210'>
					</iframe>
			</fieldset>
	    </td>
		<td>
	        <fieldset>
				<legend>Hist&oacute;rico</legend>
				<iframe id='iframe_dir' name='iframe_dir' src='about:blank' frameborder='no'
					marginheight='0' marginwidth='0' scrolling='yes' width='100%' height='210'>
					</iframe>
	        </fieldset>
	    </td>
	</tr>
	</table>
	
	
</body>
</html>
