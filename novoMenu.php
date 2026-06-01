<?
$sqlConfiguracao = "SELECT * FROM config WHERE conf_chave = 'CADASTRO_AISE'";
$queryConfiguracao = pg_query($sqlConfiguracao);
$regConfiguracao = pg_fetch_array($queryConfiguracao);

$sqlConfiguracaoUnid = "SELECT * FROM config WHERE conf_chave = 'MODULO_UNIDADES'";
$queryConfiguracaoUnid = pg_query($sqlConfiguracaoUnid);
$regConfiguracaoUnid = pg_fetch_array($queryConfiguracaoUnid);

$sqlConfiguracaoPrest = "select * from config where conf_chave = 'MODULO_PRESTADOR'";
$queryConfiguracaoPrest = pg_query($sqlConfiguracaoPrest);
$regConfiguracaoPrest = pg_fetch_array($queryConfiguracaoPrest);

$sqlConfiguracaoCnes = "select * from config where conf_chave = 'INTEGRACAO_ALMOXARIFADO'";
$queryConfiguracaoCnes = pg_query($sqlConfiguracaoCnes);
$regConfiguracaoCnes = pg_fetch_array($queryConfiguracaoCnes);
?>
<head>
	<meta charset="utf-8">
</head>
<div id="menu">
	<ul class="menu">
		<li>
			<a href="/<?= $_SESSION['modulo'] ?>index.php">
				<img src="logo-menu-pequena.png" width="30" height="30" />
			</a>
		</li>
		<li>
			<a href="#"><span>Cadastros</span></a>
			<div>
				<ul>
					<li>
						<?php
						$urlForm = ($regConfiguracao['conf_valor_bool'] == "f") ? "paciente.php?id_login=$id_login&acao=form" : "zf/paciente/form-paciente";
						echo (SelPerm($id_login,'paciente.php') != "0") ? "<a href='$PHP_SELF?link=paciente.php&id_login=$id_login' class='parent'><span>Pacientes</span></a>" : "<a href='#' onClick='alert(Usuario sem permissao!);' class='parent'><span>Pacientes</span></a>";
						?>
                        <ul>
	                        <li><?= (SelPerm($id_login,'paciente.php') != "0") ? "<a href='zf/default/paciente/form-paciente' target='frameprincipal'><span>Adicionar</span></a>" : "<a href='#' onClick=\"alert('Usuario sem permissao!');\"><span>Adicionar</span></a>"; ?></li>
	                        
							<li><?= (SelPerm($id_login,'paciente.php') != "0") ? "<a href='$PHP_SELF?link=paciente.php&id_login=$id_login'><span>Listar</span></a>" : "<a href='#' onClick=\"alert('Usuario sem permissao!');\"><span>Listar</span></a>"; ?></li>
							                        <li><a href='paciente_novo.php?id_login=$id_login' target='frameprincipal'><span>Cadastro Simplificado</span></a></li>

                        </ul>
					</li>
					<li>
						<?= "<a href='$PHP_SELF?link=medico.php&id_login=$id_login' class='parent'><span>Profissionais</span></a>" ?>
						<ul>
	                        <li><?= (SelPerm($id_login,'especialidade.php') != "0") ? "<a href='$PHP_SELF?link=especialidade.php&acao=form_espec&id_login=329'><span>Especialidade</span></a>" : "<a href='#' onClick=\"alert('Usuario sem permissao!');\"><span>Especialidade</span></a>"; ?></li>
                        </ul>
					</li>
                    <li>
						<?= (SelPerm($id_login,'agente.php') != "0") ? "<a href='$PHP_SELF?link=agente.php&id_login=$id_login' class='parent'><span>Responsavel T&eacute;cnico</span></a>" : "<a href='#' onClick=\"alert('Usuario sem permissao!');\" class='parent'><span>Responsavel T&eacute;cnico</span></a>"; ?>
			  			<ul>
			  				<li><?= (SelPerm($id_login,'agente.php') != "0") ? "<a href='$PHP_SELF?link=agente.php&acao=form_add&id_login=$id_login'><span>Adicionar</span></a>" : "<a href='#' onClick=\"alert('Usuario sem permissao!');\"><span>Adicionar</span></a>"; ?></li>
                     		<li><?= (SelPerm($id_login,'agente.php') != "0") ? "<a href=$PHP_SELF?link=agente.php&id_login=$id_login'><span>Listar</span></a>" : "<a href='#' onClick=\"alert('Usuario sem permissao!');\"><span>Listar</span></a>"; ?></li>
		    			</ul>
			   		</li>
					<?php if($regConfiguracaoUnid['conf_valor_bool'] == "t"){ ?>
						<li>
							<?= (SelPerm($id_login,'unidade.php') != "0") ? "<a href='$PHP_SELF?link=unidade.php&id_login=$id_login'class='parent'><span>Unidade de Sa&uacute;de</span></a>" : "<li><a href='#' onClick=\"alert('Usuario sem permissao!');\"><span>Unidade de Sa&uacute</span></a></li>"; ?>
							<ul>
								<?= (SelPerm($id_login,'unidade.php') != "0") ? "<li><a href='$PHP_SELF?link=unidade.php&acao=form_add&id_login=$id_login'><span>Adicionar</span></a></il>" : "<li><a href='#' onClick=\"alert('Usuario sem permissao!');\"><span>Adicionar</span></a></li>"; ?>
								<?= (SelPerm($id_login,'unidade.php') != "0") ? "<li><a href='$PHP_SELF?link=unidade.php&id_login=$id_login'><span>Listar</span></a></il>" : "<li><a href='#' onClick=\"alert('Usuario sem permissao!');\"><span>Listar</span></a></li>"; ?>
							</ul>
						</li>
					<?php } ?>
					<li>
						<?= (SelPerm($id_login,'feriado.php') != "0") ? "<a href='$PHP_SELF?link=feriado.php&id_login=$id_login?'class='parent'><span>Feriados</span></a>" : "<li><a href='#' onClick=\"alert('Usuario sem permissao!');\"><span>Feriados</span></a></li>"; ?>
						<ul>
							<?= (SelPerm($id_login,'feriado.php') != "0") ? "<li><a href='feriado.php?acao=form_add&id_login=$id_login' target='frameprincipal'><span>Adicionar</span></a></li>" : "<li><a href='#' onClick=\"alert('Usuario sem permissao!');\"><span>Adicionar</span></a></li>"; ?>
							<?= (SelPerm($id_login,'feriado.php') != "0") ? "<li><a href='$PHP_SELF?link=feriado.php&id_login=$id_login'><span>Listar</span></a></li>" : "<li><a href='#' onClick=\"alert('Usuario sem permissao!');\"><span>Listar</span></a></li>"; ?>
						</ul>
                  	</li>
					<li>
						<a href="#" class="parent"><span>Fam&iacute;lia</span></a>
						<ul>
							<li>
								<?= "<a href='$PHP_SELF?link=domicilio.php&id_login=$id_login' >
								<span>Domic&iacute;lio</span></a>" ?>
		
							</li>
							<li><?= "<a href='$PHP_SELF?link=zf/default/rua'><span>Cadastro de logradouro</span></a>" ?></li>
							<li><?= "<a href='$PHP_SELF?link=zf/domicilio/bairro'><span>Cadastro de Bairro</span></a>" ?></li>
							<li><?= "<a href='$PHP_SELF?link=psf/adicionarFichaPsf.php&id_login=$id_login'><span>Ficha PMA2</span></a>" ?></li>
							<li><?= "<a href='$PHP_SELF?link=psf/fichaSsa2.php&id_login=$id_login'><span>Ficha SSA2</span></a>" ?></li>
							<li><?= (SelPerm($id_login,'area.php') != "0") ? "<a href='$PHP_SELF?link=area.php&id_login=$id_login'><span>&Aacute;rea</span></a>" : "<a href='#' onClick=\"alert('Usuario sem permissao!');\"><span>&Aacute;rea</span></a>"; ?></li>
							<li><?= (SelPerm($id_login,'microarea.php') != "0") ? "<a href='$PHP_SELF?link=microarea.php&acao=&id_login=$id_login'><span>Micro &Aacute;rea</span></a>" : "<a href='#' onClick=\"alert('Usuario sem permissao!');\"><span>Micro &Aacute;rea</span></a>"; ?></li>
						</ul>
				   	</li>
					<li>
						<?= (SelPerm($id_login,'exame/exa_listapedidoexame.php') != "0") ? "<a href='$PHP_SELF?link=exame/exa_listapedidoexame.php&id_login=$id_login' class='parent'><span>Exame</span></a>" : "<a href='#' class='parent' onClick=\"alert('Usuario sem permissao!');\"><span>Exames</span></a>"; ?>
						<ul>
<?
echo "<li>
	<a href='".$_SERVER['PHP_SELF']."?link=exa_categoriadeexames.php&id_login=$id_login'><span>Categoria de Exames</span></a>
</li>

<li>
<a href='".$_SERVER['PHP_SELF']."?link=exa_tipodematerial.php&acao=&id_login=$id_login'><span>Tipo de Material</span></a>
</li>
<li>
<a href='".$_SERVER['PHP_SELF']."?link=exa_tipodeexame.php&acao=&id_login=$id_login'><span>Tipo de Exame</span></a>
</li>
<li>
<a href='".$_SERVER['PHP_SELF']."?link=exa_material.php&acao=&id_login=$id_login'><span>Material</span></a>
</li>
<li>
<a href='".$_SERVER['PHP_SELF']."?link=exa_metododeanalise.php&acao=&id_login=$id_login'><span>M&eacute;tod o de An&aacute;lise</span></a>
</li>
<li>
	<a href='".$_SERVER['PHP_SELF']."?link=exa_subexames.php&acao=&id_login=$id_login'><span>Sub-Exame</span></a>
</li>
<li>
<a href='".$_SERVER['PHP_SELF']."?link=exa_itensdeanalise.php&acao=&id_login=$id_login'><span>Intens de An&aacute;lise</span></a>
</li>
<li>
	<a href='".$_SERVER['PHP_SELF']."?link=exa_valoresreferencia.php&acao=&id_login=$id_login'><span>Valores Referenciais</span></a>
</li>
<li>
<a href='".$_SERVER['PHP_SELF']."?link=exa_categorialaudo.php&acao=&id_login=$id_login'><span>Categoria de Laudos</span></a>	
</li>";

?>
							<?php if(SelPerm($id_login,'laudos/tipoExames.php') != "0") echo "<li><a href='$PHP_SELF?link=laudos/tipoExames.php&id_login=$id_login'><span>Configuracoes Laudos</span></a></li>"; ?>
							<li><?= (SelPerm($id_login,'cadastroOrientacoes.php') != "0") ? "<a href='$PHP_SELF?link=cadastroOrientacoes.php&acao=&id_login=$id_login'><span>Cadastro de Orienta&ccedil;&otilde;es</span></a>" : "<a href='#' onClick=\"alert('Usuario sem permissao!');\"><span>Cadastro de Orienta&ccedil;&otilde;es</span></a>"; ?></li>
							<li><?= (SelPerm($id_login,'vinculaExamesOrientacao.php') != "0") ? "<a href='$PHP_SELF?link=vinculaExamesOrientacao.php&acao=&id_login=$id_login'><span>Vincular Orienta&ccedil;&otilde;es</span></a>" : "<a href='#' onClick=\"alert('Usuario sem permissao!');\"><span>Vincular Orienta&ccedil;&otilde;es</span></a>"; ?></li>
							<li><?= (SelPerm($id_login,'laudos/cadastroObservacoes.php') != "0") ? "<a href='$PHP_SELF?link=laudos/cadastroObservacoes.php&acao=&id_login=$id_login'><span>Cadastrar Observa&ccedil;&otilde;es</span></a>" : "<a href='#' onClick=\"alert('Usuario sem permissao!');\"><span>Cadastrar Observa&ccedil;&otilde;es</span></a>"; ?></li>
							<li><?= (SelPerm($id_login,'vinculaProcedimentosObservacoes.php') != "0") ? "<a href='$PHP_SELF?link=vinculaProcedimentosObservacoes.php&acao=&id_login=$id_login'><span>Vincular Observa&ccedil;&otilde;es</span></a>" : "<a href='#' onClick=\"alert('Usuario sem permissao!');\"><span>Vincular Observa&ccedil;&otilde;es</span></a>"; ?></li>
							<li><?= "<a href='$PHP_SELF?link=zf/laboratorio/grupo-de-exames'><span>Grupo de Exames</span></a>" ?></li>
							<li><?= "<a href='$PHP_SELF?link=zf/default/procedimento/apelido'><span>Exames Apelidos</span></a>" ?></li>
						</ul>
					</li>
					<li>
						<?= "<a href='$PHP_SELF?link=paciente.php&id_login=$id_login' class='parent'><span>Vacinas</span></a>" ?>
						<ul>
							<li><?= "<a href='$PHP_SELF?link=cadastroVacina.php?id_login=$id_login'><span>Adicionar</span></a>" ?></li>
							<li><?= "<a href='$PHP_SELF?link=geladeiraVacina.php?id_login=$id_login'><span>Geladeira</span></a>" ?></li>
							<li><?= "<a href='$PHP_SELF?link=temperaturaGeladeira.php&&id_login=$id_login'><span>Lista Temperaturas</span></a>" ?></li>
                        </ul>
					</li>
					<li><?= (SelPerm($id_login,'procedimento.php') != "0") ? "<a href='$PHP_SELF?link=procedimento.php&id_login=$id_login'><span>Procedimentos</span></a>" : "<a href='#' onClick=\"alert('Usuario sem permissao!');\"><span>Procedimentos</span></a>"; ?></li>
					<li><?= "<a href='$PHP_SELF?link=secretaria.php&id_login=$id_login'><span>Secretaria</span></a>" ?></li>
					
					<li><?= "<a href='$PHP_SELF?link=zf/estratificacao/estratificacao'><span>Ficha de Estratificacao</span></a>" ?></li>

                    <li><?= "<a href='$PHP_SELF?link=gestorPublico/cadastroGestor.php&id_login=$id_login'><span>Gestor Publico</span></a>" ?></li>
					<li><?= "<a href='zf/default/guiche' target='_blank'><span>Chamada de Paciente</span></a>" ?></li>
					<li>
						<a href='#' class='parent'><span>Grupos de Doen&ccedil;as</span></a>
						<ul>
							<li><?= "<a href='$PHP_SELF?link=grupo_doencas.php&id_login=$id_login'><span>Cadastro de Grupo</span></a>" ?></li>
							<li><?= "<a href='$PHP_SELF?link=vincular_grupos_doencas.php&id_login=$id_login'><span>Vincula&ccedil;&atilde;o de Grupos</span></a>" ?></li>
						</ul>
					</li>
					<?php if($regConfiguracaoPrest['conf_valor_bool'] == "t"){ ?>
						<li><?= (SelPerm($id_login,'laboratorio.php') != "0") ? "<a href='$PHP_SELF?link=prestador.php&id_login=$id_login'><span>Prestador de Servi&ccedil;os</span></a>" : "<a href='#' onClick=\"alert('Usuario sem permissao!');\"><span>Prestador de Servi&ccedil;os</span></a>"; ?></li>
					<?php } ?>
						<li><a href='<?=$PHP_SELF?>?link=medico_externo.php&id_login=<?=$id_login?>'><span>Medico Externo</span></a></li>
					<li><?= (SelPerm($id_login,'ci.php') != "0") ? "<a href='$PHP_SELF?link=ci.php&id_login=$id_login'><span>Car&aacute;ter de Interna&ccedil;&atilde;o</span></a>" : "<a href='#' onClick=\"alert('Usuario sem permissao!');\"><span>Car&aacute;ter de Interna&ccedil;&atilde;o</span></a>"; ?></li>
					<li><?= "<a href='$PHP_SELF?link=zf/default/raiox'><span>Exames de imagem</span></a>" ?></li>
					<li>
						<a href='#'><span>Duplica&ccedil;&atilde;o</span></a>
						<ul>
							<li><?= "<a href='$PHP_SELF?link=zf/duplicacao/paciente'><span>Paciente</span></a>" ?></li>
							<li><?= "<a href='$PHP_SELF?link=zf/duplicacao/rua'><span>Logradouro</span></a>" ?></li>
							<li><?= "<a href='$PHP_SELF?link=zf/duplicacao/produto'><span>Produto</span></a>" ?></li>
						</ul>
					</li>
					<li><?= "<a href='$PHP_SELF?link=../WebSocialSaude/zf/default/fabricante'><span>Cadastro de Fabricante</span></a>" ?></li>
					<li><?= "<a href='$PHP_SELF?link=../WebSocialComum/autentificacao/autentificacao.php?acao=registroFuturo'><span>Registro</span></a>" ?></li>
				</ul>
			</div>
		</li>
		<li>
			<a href="#" class="parent"><span>Atendimentos</span></a>
			<?php
				// echo"<pre>";print_r($_SESSION[logon][usr]->cnes_tp_unid_id);die();
			?>
            <div>
                <ul>
<!--                     <li>
						<?//echo "<a href='".$_SERVER['PHP_SELF']."?link=portadeEntrada/portadeentrada2.php&id_login=$id_login'><span>Porta de Entrada</span></a>";?>
					</li> -->
					<?
				//echo "<pre>";print_r($_SESSION);die();
				if($_SESSION[logon][usr]->uni_codigo != 513){
				//if($_SESSION['logon']['usr']->cnes_tp_unid_id != '05') {
					?>
					<li>
						<? if(SelPerm($id_login,'portadeEntrada/portadeentrada2.php') != '0') { echo "<a href='".$_SERVER['PHP_SELF']."?link=portadeEntrada/portadeentrada2.php&id_login=$id_login'><span>Porta de Entrada</span></a>"; } ?>
					</li>
					
				<?}
				?>



					<li>
					
						<?= "<a href='$PHP_SELF?link=ambulatorio.php&id_login=$id_login'><span>Ficha Ambulatorial</span></a>" ?>
					</li>

					<li>
						<?= "<a href='$PHP_SELF?link=zf/guia-diagnostico'><span>Lan&ccedil;amento de Consultas</span></a>" ?>
					</li>
                
					<li>
                        <?echo "<a href='".$_SERVER['PHP_SELF']."?link=zf/atendimento/atendimento-simplificado/index'><span>Atend. Individual</span></a>";?> 
                    </li>

                    <li>
                        <?echo "<a href='".$_SERVER['PHP_SELF']."?link=zf/atendimento/atendimento-simplificado/index-visita-domiciliar'><span>Visita Domiciliar</span></a>";?> 
                    </li>

                    <li>
                        <?echo "<a href='".$_SERVER['PHP_SELF']."?link=zf/atendimento/atendimento-simplificado/index-procedimento'><span>Procedimentos</span></a>";?> 
                    </li>

                    <li>
                        <?echo "<a href='".$_SERVER['PHP_SELF']."?link=zf/atendimento/atendimento-simplificado/index-beneficios-concedidos'><span>Beneficios Concedidos</span></a>";?> 
                    </li>  

                    <li>
                        <?echo "<a href='".$_SERVER['PHP_SELF']."?link=zf/atendimento/atendimento-simplificado/index-ficha-raas'><span>Ficha RAAS</span></a>";?> 
                    </li>

                    <li>
                        <?echo "<a href='".$_SERVER['PHP_SELF']."?link=zf/programasfederais/ficha-odontologica'><span>Atend. Odontol&oacutegico</span></a>";?> 
                    </li>

                    <li>
                        <?echo "<a href='".$_SERVER['PHP_SELF']."?link=zf/programasfederais/atividade-coletiva'><span>Atividade Coletiva</span></a>";?> 
                    </li>

                    <li>
                        <?echo "<a href='".$_SERVER['PHP_SELF']."?link=zf/default/paciente/form-paciente'><span>Cadastro Individual</span></a>";?> 
                    </li>

                    <li>
                        <?echo "<a href='".$_SERVER['PHP_SELF']."?link=domicilio.php'><span>Cadastro Domiciliar</span></a>";?> 
                    </li>

                    <li>
                        <?echo "<a href='".$_SERVER['PHP_SELF']."?link=zf/default/cadastro-familiar/index'><span>Cadastro Familiar</span></a>";?>
                    </li>

                    <li>
                        <?echo "<a href='".$_SERVER['PHP_SELF']."?link=zf/programasfederais/consumo-alimentar'><span>Consumo Alimentar</span></a>";?>
                    </li>

					<li>
						<?= "<a href='$PHP_SELF?link=zf/prontuario/receita-medica/aprazamento'><span>Aprazamento de receitas</span></a>" ?>
					</li>

                    <li>
                        <?echo "<a href='".$_SERVER['PHP_SELF']."?link=zf/programasfederais/ficha-complementar'><span>Ficha Complementar <br>Zika/Microcefalia</span></a>";?>
                    </li>

                  </ul>
            </div>			

		</li>
		<li>
			<a href="#"><span>Agendamento</span></a>
			<div>
				<ul>
					<li><?= (SelPerm($id_login,'fazer_agendamento.php') != "0") ? "<a href='$PHP_SELF?link=zf/agendamento'><span>Fazer Agendamento</span></a>" : "<a href='#' onClick=\"alert('Usuario sem permissao!');\"><span>Fazer Agendamento</span></a>"; ?></li>
					<li><?= "<a href='$PHP_SELF?link=zf/agendamento-externo'><span>Fazer Agendamento Externo</span></a>" ?></li>
					<li><?= "<a href='$PHP_SELF?link=zf/agenda/convenio/agendamento-estabelecimentos-de-saude'><span>Vincular Profissional</span></a>" ?></li>
					<li><?= "<a href='$PHP_SELF?link=zf/agenda/distribuicao'><span>Manuten&ccedil&atildeo de Hor&aacuterios</span></a>" ?></li>
					<li><?= "<a href='$PHP_SELF?link=zf/agenda/recepcao'><span>Recep&ccedil&atildeo de Pacientes</span></a>" ?></li>
					<li><?= "<a href='$PHP_SELF?link=zf/plantao'><span>Escala de Plant&atilde;o</span></a>" ?></li>
					<li><?= "<a href='$PHP_SELF?link=listadeespera.php?acao=init'><span>Lista de Espera</span></a>" ?></li>
					<li><?= "<a href='$PHP_SELF?link=zf/default/agendamento-anterior'><span>Agendamento Anterior</span></a>" ?></li>
					<li><?= "<a href='$PHP_SELF?link=zf/agendamento/central-de-regulacao/index'><span>Central de Regulacao</span></a>" ?></li>
					</li>
					</li>

				</ul>
			</div>
		</li>
		<li>
			<a href="#"><span>Laborat&oacuterio</span></a>
			<div>
				<ul>
					<li><?= "<a href='$PHP_SELF?link=zf/agenda/agenda&id_login=$id_login'><span>Fazer Agendamento</span></a>" ?></li>
					<li><?= "<a href='$PHP_SELF?link=exame/exa_lab_valor.php&id_login=$id_login'><span>Regula&ccedil&atildeo por Valor</span></a>" ?></li>
					<li><?= "<a href='$PHP_SELF?link=zf/agenda/convenio&id_login=$id_login'><span>Regula&ccedil&atildeo por Quantidade</span></a>" ?></li>
					<li><?= "<a href='$PHP_SELF?link=exa_listapedidoexame.php&id_login=$id_login'><span>Laudos e Coletas</span></a>" ?></li>
					<li><?= "<a href='$PHP_SELF?link=exame/controleExames.php&id_login=$id_login'><span>Central de Regula&ccedil&atildeo</span></a>" ?></li>
					<li><?= "<a href='$PHP_SELF?link=zf/agenda/agenda-emergencia&id_login=$id_login'><span>Exames Emergenciais</span></a>" ?></li>


				<li><?= "<a href='$PHP_SELF?link=liberacao/liberacao.php&id_login=$id_login'><span>Libera&ccedil;&atilde;o Prestador</span></a>" ?></li>
				</ul>
			</div>
		</li>
		<li>
			<a href="#"><span>Interna&ccedil;&atilde;o</span></a>
			<div>
				<ul>
					<li><?= "<a href='$PHP_SELF?link=zf/leito/medicamentos'><span>Medicamentos</span></a>" ?></li>
					<li><?= "<a href='$PHP_SELF?link=zf/leito/modelo-grade/categorias'><span>Manut. Grade</span></a>" ?></li>
					<li><?= "<a href='$PHP_SELF?link=zf/leito/categoria'><span>Cad.Categoria</span></a>" ?></li>
					<li><?= "<a href='$PHP_SELF?link=hospitalar/cadquarto.php'><span>Cad.Quarto</span></a>" ?></li>
					<li><?= "<a href='$PHP_SELF?link=hospitalar/cadleito.php'><span>Cad.Leito</span></a>" ?></li>
					<li><?= "<a href='$PHP_SELF?link=aih.php'><span>AIH</span></a>" ?></li>
					<li><?= "<a href='$PHP_SELF?link=apac.php'><span>APAC</span></a>" ?></li>
				</ul>
			</div>
		</li>
		<li>
			<a href="#"><span>Materiais</span></a>
			<div>
				<ul>
					<?php if($regConfiguracaoCnes['conf_valor_bool'] != "t"){ ?>
						<li><?= (SelPerm($id_login,'grupo.php') != "0") ? "<a href='$PHP_SELF?link=grupo.php&acao=form_grupo&id_login=$id_login'><span>Grupo</span></a>" : "<a href='#' onClick=\"alert('Usuario sem permissao!');\"><span>Grupo</span></a>"; ?></li>
						<li><?= (SelPerm($id_login,'setor.php') != "0") ? "<a href='$PHP_SELF?link=setor.php&acao=form_setor&id_login=$id_login'><span>Setor</span></a>" : "<a href='#' onClick=\"alert('Usuario sem permissao!');\"><span>Setor</span></a>"; ?></li>

						<li><?= (SelPerm($id_login,'psico.php') != "0") ? "<a href='$PHP_SELF?link=psico.php&acao=form_psico&id_login=$id_login'><span>Psicotr&oacute;picos</span></a>" : "<a href='#' onClick=\"alert('Usuario sem permissao!');\"><span>Psicotr&oacute;picos</span></a>"; ?></li>

						<li><?= (SelPerm($id_login,'fornecedor.php') != "0") ? "<a href='$PHP_SELF?link=../WebSocialComum/fornecedor.php&acao=form_forn&id_login=$id_login'><span>Fornecedor</span></a>" : "<a href='#' onClick=\"alert('Usuario sem permissao!');\"><span>Fornecedor</span></a>"; ?></li>
						
					<?php } ?>
					<li><?= (SelPerm($id_login,'zf/materiais/movimentacao') != "0") ? "<a href='$PHP_SELF?link=zf/materiais/movimentacao'><span>Movimenta&ccedil;&atilde;o</span></a>" : "<a href='#' onClick=\"alert('Usuario sem permissao!');\"><span>Movimenta&ccedil;&atilde;o</span></a>"; ?></li>
				</ul>
			</div>
			<li>
				<a href="#"><span>Farm&aacute;cia</span></a>
				<div>
					<ul>
						<li>
							<a href='#' class="parent"><span>Dispensa&ccedil;&atilde;o</span></a>
							<div>
								<ul>
									<li><?= (SelPerm($id_login,'dispensacao.php') != "0") ? "<a href='$PHP_SELF?link=zf/farmacia/farmacia/index/cod_barras/1'><span>C&oacute;digo de Barras</span></a>" : "<a href='#' onClick=\"alert('Usuario sem permissao!');\"><span>C&oacute;digo de Barras</span></a>"; ?></li>
								</ul>
							</div>
						</li>
						
						<li>
							<?
							if(SelPerm($id_login,'dispensacao.php') != "0") {
								echo "
							<a href='".$_SERVER['PHP_SELF']."?link=dispensacao.php&acao=&id_login=$id_login'><span>Adm da Dispensa&ccedil&atildeo</span></a>";
							} else{
								echo "
							<a href='#' onClick=\"alert('Usuario sem permissao!');\"><span>Adm da Dispensacao</span></a>";
							}
							?>
						</li>
						<li>
							<?
							if(SelPerm($id_login,'psico.php') != "0") {
								echo "
							<a href='".$_SERVER['PHP_SELF']."?link=psico.php&acao=&id_login=$id_login'><span>Psicotr&oacutepicos</span></a>";
							} else{
								echo "
							<a href='#' onClick=\"alert('Usuario sem permissao!');\"><span>Psicotropicos</span></a>";
							}
							?>
						</li>						
						
						<li><?= (SelPerm($id_login,'cota_paciente.php') != "0") ? "<a href='$PHP_SELF?link=cota_paciente.php&acao=&id_login=$id_login'><span>Cotas Prod. por Paciente</span></a>" : "<a href='#' onClick=\"alert('Usuario sem permissao!');\"><span>Cotas Prod. por Paciente</span></a>"; ?>
							
						</li>
						<li><a href='<?=$PHP_SELF?>?link=programa_atendimento.php&acao=&id_login=$id_login'><span>Programa Atendimento</span></a>
							
						</li>
						<!-- <li><?= "<a href='$PHP_SELF?link=compraMedicamentosIndex.php&acao=&id_login=$id_login'><span>Comprar Produto</span></a>"; ?> -->
							
						</li>
						<li><?= (SelPerm($id_login,'programa_produto.php') != "0") ? 
						"<a href='$PHP_SELF?link=programa_produto.php&acao=&id_login=$id_login'>
						<span>Programa Produto</span>
						</a>" : "<a href='#' onClick=\"alert('Usuario sem permissao!');\"><span>Programa Produto
						</span>
						</a>"; ?>
						</li>
						<li>
							<a href='<?=$PHP_SELF?>?link=zf/farmacia/medicamentos-especiais'><span>Medicamento especial</span></a>
						</li>
					</ul>
				</div>
			</li>
		</li>

		<li>
			<a href="#"><span>Administrativo</span></a>
			<div>
				<ul>
					<li><?= "<a href='$PHP_SELF?link=zf/ferramentas/backup'><span>Backup do Banco</span></a>"; ?></li>
					<li><?= "<a href='$PHP_SELF?link=zf/ferramentas/novidades'><span>Novidades</span></a>"; ?></li>
					<li><?= (SelPerm($id_login,'usuarios.php') != "0") ? "<a href='$PHP_SELF?link=zf/usuarios/usuarios'><span>Usu&aacuterios</span></a>" : "<a href='#' onClick=\"alert('Usuario sem permissao!');\"><span>Adicionar</span></a>"; ?></li>
					<li><?= (SelPerm($id_login,'usuario_acesso.php') != "0") ? "<a href='$PHP_SELF?link=usuario_acesso.php&acao=form_acesso&id_login=$id_login'><span>Acesso por Usu&aacute;rios</span></a>" : "<a href='#' onClick=\"alert('Usuario sem permissao!');\"><span>Acesso por Usu&aacute;rios</span></a>"; ?></li>
					<li><?= (SelPerm($id_login,'permissoes.php') != "0") ? "<a href='$PHP_SELF?link=permissoes.php&acao=form_perm&id_login=$id_login'><span>Permiss&otilde;es</span></a>" : "<a href='#' onClick=\"alert('Usuario sem permissao!');\"><span>Permiss&otilde;es</span></a>"; ?></li>
					<li><?= (SelPerm($id_login,'permissoes_usuarios.php') != "0") ? "<a href='$PHP_SELF?link=permissoes_usuarios.php&acao=form_perm&id_login=$id_login'><span>Permiss&otilde;es por Us&uacute;ario</span></a>" : "<a href='#' onClick=\"alert('Usuario sem permissao!');\"><span>Permiss&otilde;es por Us&uacute;ario</span></a>"; ?></li>
					<li><?= (SelPerm($id_login,'link=log.php') != "0") ? "<a href='$PHP_SELF?link=log.php&id_login=$id_login'><span>Log</span></a>" : "<a href='#' onClick=\"alert('Usuario sem permissao!');\"><span>Log</span></a>"; ?></li>
					<li><?= "<a href='$PHP_SELF?link=alterarsenha.php&id_login=$id_login'><span>Alterar Senha</span></a>"; ?></li>
					<li><?= "<a href='$PHP_SELF?link=realocacao.php&id_login=$id_login'><span>Realoca&ccedil&atildeo de Pacientes</span></a>"; ?></li>
					<li><?= "<a href='receita_branco.php?uni_codigo=".$_SESSION['uni_codigo']."&qtd=1' target='_blank'><span>Receita em Branco 1via</span></a>"; ?></li>
					<li><?= "<a href='receita_branco.php?uni_codigo=".$_SESSION['uni_codigo']."&qtd=2' target='_blank'><span>Receita em Branco 2vias</span></a>"; ?></li>
				</ul>
			</div>
		</li>
		
<!-- 		
		<li>
			<a href="#"><span>Transporte(TFD) </span></a>
			<div>
				<ul>
					<li><?= "<a href='$PHP_SELF?link=zf/transporte/veiculo'><span>Cadastro de ve&iacuteculo</span></a>"; ?></li>
					<li><?= "<a href='$PHP_SELF?link=zf/transporte/viagem'><span>Cadastro de viagem</span></a>"; ?></li>
					<li><?= "<a href='$PHP_SELF?link=zf/transporte/viagem-usuario'><span>Agenda Pacientes</span></a>"; ?></li>
				</ul>
			</div>
		</li>	 -->	
		<li>
			<a href="#"><span>Transporte(TFD) </span></a>
			<div>
				<ul>
					<li><?= "<a href='$PHP_SELF?link=zf/transporte/veiculo'><span>Cadastro de ve&iacuteculo</span></a>"; ?></li>
					<li><?= "<a href='$PHP_SELF?link=zf/transporte/viagem'><span>Cadastro de viagem</span></a>"; ?></li>
					<li><?= "<a href='$PHP_SELF?link=zf/transporte/viagem/rota'><span>Cadastro de rotas</span></a>"; ?></li>
					<li><?= "<a href='$PHP_SELF?link=zf/transporte/viagem-usuario'><span>Agenda Pacientes</span></a>"; ?></li>
				</ul>
			</div>
		</li>
		
        <li>	
            <a href="#"><span>Prog. Federais </span></a>	
            <div>
                <ul>
					<li><?= "<a href='$PHP_SELF?link=hiperdiaNovo/layoutExportacao.php&id_login=$id_login'><span>HIPERDIA</span></a>"; ?></li>
					<li><?= "<a href='$PHP_SELF?link=dataSusUpdate/update2/importacaoSigtap.php&id_login=$id_login'><span>SIGTAP</span></a>"; ?></li>
					<li><?= "<a href='$PHP_SELF?link=exportacao/exportacao_pni.php&id_login=$id_login'><span>SI-PNI</span></a>"; ?></li>
					<li>
						<a href='#' class="parent"><span>BPA</span></a>
                        <!--<? echo "<a href='".$_SERVER['PHP_SELF']."?link=zf/programasfederais/bpa/index'><span>BPA</span></a>";?>-->
						<ul>
                            <li>
                            	<? echo "<a href='" . $_SERVER['PHP_SELF'] . "?link=zf/programasfederais/bpa/index'><span>Exportar</span></a>"; ?>
                            </li>
                            <li>
                            	<? echo "<a href='" . $_SERVER['PHP_SELF'] . "?link=zf/programasfederais/bpa/listagem'><span>Listagem</span></a>"; ?>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href='#' class="parent"><span>CNES</span></a>
                        <ul>
							<?php
							$sqlConfiguracaoCnes = "select * from config where conf_chave = 'IMPORTACAO_CNES'";
							$queryConfiguracaoCnes = pg_query($sqlConfiguracaoCnes);
							$regConfiguracaoCnes = pg_fetch_array($queryConfiguracaoCnes);
							?>
                            <li>
                            	<?php echo "<a href='".$_SERVER['PHP_SELF']."?link=zf/programasfederais/cnes'><span>Importa&ccedil;&atilde;o</span></a>"; ?> 
                            </li>
                            <li>
                            	<?php echo "<a href='".$_SERVER['PHP_SELF']."?link=zf/programasfederais/cnes-web'><span>Sincroniza&ccedil&atildeo</span></a>"; ?> 
                            </li>
                            <li>
                                <?php echo "<a href='".$_SERVER['PHP_SELF']."?link=zf/programasfederais/inconsistencias-cnes'><span>Inconsist&ecircncias</span></a>"; ?> 
                            </li>
                        </ul>
                    </li>
                    <!-- li>	
            <a href="#" class="parent"><span>SUS</span></a>	
            <div>
                <ul>	 
                                        <li>
                        <a href='#' class="parent"><span>Exporta&ccedil;&atilde;o</span></a>	                      
                        <ul>	
                            <li>
                                <--?
                                //if(SelPerm($id_login,'exportacao_pni.php') != "0") {
                                echo "
                                <a href='".$_SERVER['PHP_SELF']."?link=exportacao/exportacao_pni.php&id_login=$id_login'><span>SI-PNI</span></a>";
                                /*} else{ 
                                echo "
                                <a href='#' onClick=\"alert('Usuario sem permissao!');\"><span>SI-PNI</span></a>";
                                }*/
                                ?> 
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </li-->

                    <li>
                        <a href='#' class="parent"><span>E-SUS</span></a>
                        <ul>
                            <li>
                                <?echo "<a href='".$_SERVER['PHP_SELF']."?link=zf/programasfederais/esus/exportacao'><span>Exporta&ccedil&atildeo</span></a>";?> 
                            </li>
                            <li>
								<?php echo "<a href='".$_SERVER['PHP_SELF']."?link=zf/programasfederais/esus/historico-exportacao'><span>Hist&oacute;rico de Exporta&ccedil;&atilde;o</span></a>"; ?> 
                            </li>
                        </ul>
                    </li>
					<li>
                        <a href='#' class="parent"><span>E-SUS Inconsist&ecircncias</span></a>
                        <ul>
                            <li>
                                <?echo "<a href='".$_SERVER['PHP_SELF']."?link=zf/programasfederais/atendimento-individual/inconsistencias'><span>Atend. Individual</span></a>";?> 
                            </li>
                            <li>
                                <?echo "<a href='".$_SERVER['PHP_SELF']."?link=zf/programasfederais/atividade-coletiva/inconsistencias'><span>Atividade Coletiva</span></a>";?> 
                            </li>
                            <li>
                                <?echo "<a href='".$_SERVER['PHP_SELF']."?link=zf/programasfederais/cadastro-individual/inconsistencias'><span>Cadastro Individual</span></a>";?> 
                            </li>
                            <li>
                                <?echo "<a href='".$_SERVER['PHP_SELF']."?link=zf/programasfederais/ficha-procedimento/inconsistencias'><span>Procedimentos</span></a>";?> 
                            </li>
                            <li>
                                <?echo "<a href='".$_SERVER['PHP_SELF']."?link=zf/programasfederais/visita-domiciliar/inconsistencias'><span>Visita Domiciliar</span></a>";?> 
                            </li>
                            <li>
                                <?echo "<a href='".$_SERVER['PHP_SELF']."?link=zf/programasfederais/ficha-odontologica/inconsistencias'><span>Odontologia</span></a>";?> 
                            </li>
                            <li>
								<?php echo "<a href='".$_SERVER['PHP_SELF']."?link=zf/programasfederais/cadastro-domiciliar/inconsistencias'><span>Cadastro Domiciliar</span></a>"; ?> 
                            </li>
                        </ul>
                    </li>
 					<li>
                        <a href='#' class="parent"><span>Horus</span></a>
                        <ul>
                            <li>
                                <?echo "<a href='".$_SERVER['PHP_SELF']."?link=zf/programasfederais/horus'><span>Exporta&ccedil&atildeo</span></a>";?> 
                            </li>
                            <li>
                                <?echo "<a href='".$_SERVER['PHP_SELF']."?link=zf/programasfederais/horus/consulta-dados-horus'><span>Consulta Envios</span></a>";?> 
                            </li>
                            <li>
                                <?echo "<a href='".$_SERVER['PHP_SELF']."?link=zf/programasfederais/horus/consulta-por-protocolo'><span>Corre&ccedil&atildeo de Envios</span></a>";?> 
                            </li>
                            <li>
                                <?echo "<a href='".$_SERVER['PHP_SELF']."?link=zf/programasfederais/horus/lista-protocolos'><span>Lista Protocolos</span></a>";?> 
                            </li>
                            <li>
								<? echo "<a href='".$_SERVER['PHP_SELF']."?link=zf/duplicacao/horus'><span>Controle de Duplica&ccedil;&atilde;o</span></a>"; ?> 
							</li>
                        </ul>
                    </li>

					<?php /*
						<li>
						<a href='#' class="parent"><span>E-SUS Manuais</span></a>
						<ul>
						<li>
						<?echo "<a href='".$_SERVER['PHP_SELF']."?link=zf/programasfederais/esus/manual-utilizacao'><span>Utilização do sistema</span></a>";?>
						</li>
						<li>
						<?echo "<a href='".$_SERVER['PHP_SELF']."?link=zf/programasfederais/esus/manual-exportacao'><span>Exportação de dados</span></a>";?>
						</li>
						</ul>
						</li> */ ?>
                </ul>
            </div>
        </li>
		<li class="logout"><a href="logoff.php?id_login=<?=$id_login;?>" title='SAIR' target='_parent' onClick="if (!confirm('Realmente deseja Sair do Sistema?')) return false"><span>Sair</span></a></li>
	</ul>
</div>