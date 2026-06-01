<?php

/**
@Modulo: Layout Critica
@Arquivos Relacionados: layout_critica.inc.php layout_critica_op.php layout_critica_popup.php
@Tabelas: familia, usuario, cidade, layout_critica
@Acao: Form para geracao do arquivo de exportacao para o SUS
*/ 

session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."layout_critica.inc.php";

Cabecario( $hotkey = false );

verauth($id_login);

print 
	monta_janela('jan_cidade','Cidades').
'
<script type="text/javascript" src="funcoes.js"></script>
<script type="text/javascript" src="ajax_motor.js"></script>

<form id="form_filtro" action="?">
<fieldset>
<legend>Filtros</legend>
<table>
	<tr>
		<td><label for="ibge">C&oacute;digo IBGE</label></td>
		<td>
			<input type="text" id="ibge" class="box" size="7" maxlength="7" />
			<img src="'.$_SESSION[linkroot].$_SESSION[comum].'imgs/localizar.jpg" alt="Localizar" id="lupa_ibge" style="cursor:pointer;" />
		</td>
	</tr>
	<tr>
		<td>Cidade</td>
		<td id="cid_nome" style="font-weight:bold;">---</td>
	</tr>
	<tr>
		<td width="155"><label for="tipo_filtro">Filtro da Data</label></td>
		<td>
			<select id="tipo_filtro" class="box">
				<option value="1">Data menor igual que...</option>
				<option value="2">Data maior igual que...</option>
				<option value="3">Per&iacute;odo de Data</option>
			</select>
		</td>
	</tr>
	<tr>
		<td><label for="data_ini">Data Inicial (dd/mm/aaaa)</label></td>
		<td><input type="text" id="data_ini" class="box" size="10" maxlength="10" onkeypress="Ajusta_Data(this,event)" value="'.date('d/m/Y').'" /></td>
	</tr>
	<tr>
		<td><label for="data_fim">Data Final (dd/mm/aaaa)</label></td>
		<td><input type="text" id="data_fim" class="box" size="10" maxlength="10" onkeypress="Ajusta_Data(this,event)" disabled /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="image" src="'.$_SESSION[linkroot].$_SESSION[comum].'imgs/gerar_arquivo_on.jpg" alt="Gerar Arquivo" /></td>
	</tr>
</table>
</fieldset>
</form>
<div id="resposta" style="marging:10px;padding:10px;">&nbsp;</div>
<script type="text/javascript" src="layout_critica.js.php?id_login='.$id_login.'"></script>
';