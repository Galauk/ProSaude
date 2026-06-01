<?php


//------------------------------------------------------------------>
// -> Inclusao principal
//------------------------------------------------------------------>

session_start();
include_once $_SESSION['root'].$_SESSION['modulo']."authlib.inc.php";
verauth($id_login);
require_once $_SESSION['root'].$_SESSION['comum']."class/formClass.php";
require_once $_SESSION['root'].$_SESSION['comum']."class/commonClass.php";
include_once $_SESSION['root'].$_SESSION['comum']."library/php/funcoes.inc.php";
cabecario();

?>
<link href="css/estiloForm.css" rel="stylesheet" type="text/css" />
<link href="css/estiloCommon.css" rel="stylesheet" type="text/css" />

<?php
//------------------------------------------------------------------>
//-> Primeiro Cabecalho
//------------------------------------------------------------------>

   	$form = new classForm();
    $common = new commonClass();
    echo $common->incJquery();

    echo $common->menuTab(array('Agendamento','Atendimento','Materiais','Farm&aacute;cia','Epidemio','Vacina','Hiperdia','Laborat&oacute;rio','Transporte','Pacientes', 'Prog.Federais', 'Geral','Graficos','Administra&ccedil;&atilde;o'));


    $opcao  = @ $_GET['opcao'];
    $cont   = 0;

    function num($n=null){
        global $cont;
        if($n!=null) {
        $cont=0;
        }
        return ( sprintf( '<strong>%02d</strong>', ++$cont ) );
    }

    //
    //OPCAO DE RELATORIOS DE AGENDAMENTO
    //VERIFICACAO DE CADA UMA DAS PERMISSOES NA VARIAVEL NIVEL_I

    $GetNameFile = $_SERVER["SCRIPT_NAME"];
    $e = explode('/',$GetNameFile);
    if(!empty($e[3])) { echo "../"; }

    echo $common->bodyTab('1');
    echo "<table class='lista' width='90%' align='center' cellspacing='2' cellpadding='4' border='0'>
        <tr>
            <td><a href='relatorio/form_rel_lista_espera.php'>
            <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num('1')." Lista de Espera de Consultas</a>
            </td>
        </tr>
    </table>";
echo "
			<tr>
			<td><a href='zf/relatorio/agendamento/fila-de-espera/'>
                    <img src='" . $_SESSION['linkroot'] . $_SESSION['comum'] . "imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;" . num() . " Fila de Espera</a>
                </td>
			</tr>";	
    echo "
		<table class='lista' width='90%' align='center' cellspacing='2' cellpadding='4' border='0'> ";

        $sql = "select p.perm_descricao,p.perm_programa,up.nivel_i,up.nivel_a,up.nivel_d,
            up.nivel_l,up.nivel_b,up.perm_set from usuarios_permissoes as up
            left join permissoes as p on up.perm_codigo=p.perm_codigo
            where up.usr_codigo = '$id_login' and p.perm_programa = 'AgPorAgente.php'";
        $query = db_query($sql);
        $resultado = pg_num_rows($query);
        if ($resultado != 0){
            $row = pg_fetch_array($query);
            echo "
			<tr>
                <td><a href='relatorio/AgTotalPorMedico.php?id_login=$id_login'>
                    <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Quantidade de Consultas por Medico</a>
                </td>
	        </tr>";
            if ($row['perm_set'] == 'S'){
                echo "
                <tr>
                    <td><a href='relatorio/AgPorAgente.php?id_login=$id_login'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Agendamento Por Unidade</a>
                    </td>
                </tr>";
            }
        }
        echo "
        <tr>
            <td>
                <a href='relatorio/AgExUnidade.php?id_login=$id_login'>
                <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Agendamento Externo por unidade e especialidade</a>
            </td>
	    </tr>";

        echo "
        <tr>
            <td><a href='relatorio/AgExMedico.php?id_login=$id_login'>
                <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Agendamento Externo por m&eacute;dico e especialidade</a>
            </td>
        </tr>";

        //-----------------------------------------------------------------------------------------------------------------
		//   AGENDAMENTO POR M�DICO   --  AgPorMedico.php

		$sql = "select p.perm_descricao,p.perm_programa,up.nivel_i,up.nivel_a,up.nivel_d,
		        up.nivel_l,up.nivel_b,up.perm_set from usuarios_permissoes as up
		        left join permissoes as p on up.perm_codigo=p.perm_codigo
		        where up.usr_codigo = '$id_login' and p.perm_programa = 'AgPorMedico.php'";
		$query = db_query($sql);
		$resultado = pg_num_rows($query);
		if ($resultado != 0){
		    $row = pg_fetch_array($query);
		    if ($row['perm_set'] == 'S')
		    echo "
			<tr>
                <td>
                    <a href='relatorio/AgPorMedico.php?id_login=$id_login'>
			        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Agendamento por M&eacute;dico</a>
			    </td>
			</tr>";
        }
        
		//------------------------------------------------------------------------------------------------------------------

		//-----------------------------------------------------------------------------------------------------------------
		//   AGENDAMENTO POR PACIENTE   --  AgPorPaciente.php


		//------------------------------------------------------------------------------------------------------------------
		$sql = "select p.perm_descricao,p.perm_programa,up.nivel_i,up.nivel_a,up.nivel_d,
		        up.nivel_l,up.nivel_b,up.perm_set from usuarios_permissoes as up
		        left join permissoes as p on up.perm_codigo=p.perm_codigo
		        where up.usr_codigo = '$id_login' and p.perm_programa = 'AgPorPaciente.php'";
		$query = db_query($sql);
		$resultado = pg_num_rows($query);
        
        if ($resultado != 0){
		    $row = pg_fetch_array($query);
		    if ($row['perm_set'] == 'S'){
		       echo "
                <tr>
                    <td>
                        <a href='relatorio/AgPorEspecialidade.php?id_login=$id_login'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Agendamento Por Especialidade</a>
                    </td>
                </tr>";
            }
		}

		$sql = "select p.perm_descricao,p.perm_programa,up.nivel_i,up.nivel_a,up.nivel_d,
		        up.nivel_l,up.nivel_b,up.perm_set from usuarios_permissoes as up
		        left join permissoes as p on up.perm_codigo=p.perm_codigo
		        where up.usr_codigo = '$id_login' and p.perm_programa = 'AgPorUnidade.php'";
		$query = db_query($sql);
		$resultado = pg_num_rows($query);
		if ($resultado != 0){
		    $row = pg_fetch_array($query);
		    if ($row['perm_set'] == 'S'){
                echo "
                <tr>
                    <td><a href='relatorio/AgPorUnidade.php?id_login=$id_login'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." CISVIR - Agendamento por Unidade</a>
                    </td>
                </tr>";
            }
        }
        
        /*
		$sql = "select p.perm_descricao,p.perm_programa,up.nivel_i,up.nivel_a,up.nivel_d,
		        up.nivel_l,up.nivel_b,up.perm_set from usuarios_permissoes as up
		        left join permissoes as p on up.perm_codigo=p.perm_codigo
		        where up.usr_codigo = '$id_login' and p.perm_programa = 'EfetAtendPorMedico.php'";
		$query = db_query($sql);
		$resultado = pg_num_rows($query);
		if ($resultado != 0)
		{
		    $row = pg_fetch_array($query);
		    if ($row['perm_set'] == 'S')
		    echo "


			<tr>
			    <td><a href='relatorio/EfetAtendPorMedico.php?id_login=$id_login'>
                    <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Efetivo Atendimento Por M�dico</a>
                </td>
            </tr>";
	    }*/


		$sql = "select p.perm_descricao,p.perm_programa,up.nivel_i,up.nivel_a,up.nivel_d,
		        up.nivel_l,up.nivel_b,up.perm_set from usuarios_permissoes as up
		        left join permissoes as p on up.perm_codigo=p.perm_codigo
		        where up.usr_codigo = '$id_login' and p.perm_programa = 'FaltPorPeriodo.php'";
		$query = db_query($sql);
		$resultado = pg_num_rows($query);
		if ($resultado != 0){
		    $row = pg_fetch_array($query);
		    if ($row['perm_set'] == 'S')
		    echo "
            <tr>
			    <td><a href='relatorio/FaltPorPeriodo.php?id_login=$id_login'>
                    <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Faltosos por Periodo</a>
                </td>
            </tr>";
		    /*
		    echo "
            <tr>
			    <td><a href='relatorio/MedPorEspecialidade.php?id_login=$id_login'>
                    <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Encaminhamento por especialidade</a>
                </td>
            </tr>";*/
	    }

		$sql = "select p.perm_descricao,p.perm_programa,up.nivel_i,up.nivel_a,up.nivel_d,
		        up.nivel_l,up.nivel_b,up.perm_set from usuarios_permissoes as up
		        left join permissoes as p on up.perm_codigo=p.perm_codigo
		        where up.usr_codigo = '$id_login' and p.perm_programa = 'TempoEsperaPaciente.php'";
		$query = db_query($sql);
		$resultado = pg_num_rows($query);
		if ($resultado != 0)
		{
		    $row = pg_fetch_array($query);
		   /* if ($row['perm_set'] == 'S')
		    echo "
            <tr>
			    <td><a href='relatorio/TempoEsperaPaciente.php?id_login=$id_login'>
                    <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Tempo de espera do paciente para ser atendido</a>
                </td>
            </tr>";*/
	    }
/*
		$sql = "select p.perm_descricao,p.perm_programa,up.nivel_i,up.nivel_a,up.nivel_d,
		        up.nivel_l,up.nivel_b,up.perm_set from usuarios_permissoes as up
		        left join permissoes as p on up.perm_codigo=p.perm_codigo
		        where up.usr_codigo = '$id_login' and p.perm_programa = 'ProntPorUnid.php'";
		$query = db_query($sql);
		$resultado = pg_num_rows($query);
		if ($resultado != 0)
		{
		    $row = pg_fetch_array($query);
		    if ($row['perm_set'] == 'S')
		    echo "
            <tr>
			    <td><a href='relatorio/ProntPorUnid.php?id_login=$id_login'>
                    <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Localiza��o dos Prontu�rios</a>
                </td>
            </tr>";
	    }*/
		$sql = "select p.perm_descricao,p.perm_programa,up.nivel_i,up.nivel_a,up.nivel_d,
		        up.nivel_l,up.nivel_b,up.perm_set from usuarios_permissoes as up
		        left join permissoes as p on up.perm_codigo=p.perm_codigo
		        where up.usr_codigo = '$id_login' and p.perm_programa = 'AgPorAgenteMedico.php'";
		$query = db_query($sql);
		$resultado = pg_num_rows($query);
		if ($resultado != 0)
		{
		    $row = pg_fetch_array($query);
		    if ($row['perm_set'] == 'S')
		    echo "
			<tr>
			<td><a href='relatorio/AgPorAgenteMedico.php?id_login=$id_login'>
                    <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Agendamentos Remarcados e Transfer&ecirc;ncias </a>
                </td>
			</tr>";
		}
		    echo "
			<tr>
			<td><a href='relatorio/AgPorPacienteFaltoso.php?id_login=$id_login'>
                    <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Pacientes Faltosos</a>
                </td>
			</tr>";

			 echo "
			<tr>
			<td><a href='imprimirConsultasAgendadasIndex.php?id_login=$id_login'>
                    <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Listagem de Consultas Agendadas</a>
                </td>
			</tr>";
      echo "<tr>
          <td><a href='relatorio/EscalaPlantoes.php?id_login=$id_login'>
              <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Escala de Plant&otilde;es</a>
          </td>
      </tr>";
        echo "<tr>
          <td><a href='relatorio/FechamentoMedico.php?id_login=$id_login'>
              <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Faturamento Mensal por M&eacutedico</a>
          </td>
      </tr>";
		echo "</table>";

echo $common->closeTab();


echo $common->bodyTab('2');
    //".num()."
    echo "
            <table class='lista' width='90%' align='center' cellspacing='2' cellpadding='4' border='0'>
                <tr>
                    <td><a href='relatorio/AtendimentoPorAnualPorUnidade.php?id_login=$id_login'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num('1')." Atendimentos Anual por Unidade</a>
                    </td>
                </tr>

            </table>";
    echo "
            <table class='lista' width='90%' align='center' cellspacing='2' cellpadding='4' border='0'>
                <tr>
                    <td><a href='relatorio/AtendimentoPorMensalUnidade.php?id_login=$id_login'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Atendimentos Gerais Mensal por Unidade</a>
                    </td>
                </tr>

            </table>";
    echo "
            <table class='lista' width='90%' align='center' cellspacing='2' cellpadding='4' border='0'>
                <tr>
                    <td><a href='relatorio/AtendimentoPorFaixaEtaria.php?id_login=$id_login'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Atendimentos Mensal por Faixa Etaria</a>
                    </td>
                </tr>

            </table>";
    echo "
    		<table class='lista' width='90%' align='center' cellspacing='2' cellpadding='4' border='0'>
				<tr>
                    <td><a href='relatorio/AtendimentoMensalMedico.php?id_login=$id_login'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Quantidade por Atendimentos Mensal por Medico</a>
                    </td>
                </tr>

            </table>";
    echo "
    		<table class='lista' width='90%' align='center' cellspacing='2' cellpadding='4' border='0'>
				<tr>
                    <td><a href='relatorio/classRiscoIndex.php?id_login=$id_login'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Quantidade por Classifica&ccedil;&atilde;o de Risco</a>
                    </td>
                </tr>

            </table>";
			echo "
		<table class='lista' width='90%' align='center' cellspacing='2' cellpadding='4' border='0'>";
	    echo "
		                <tr>
                    <td><a href='relatorio/AtenPorCboUnid.php?id_login=$id_login'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Atendimento por CBO por Unidade</a>
                    </td>
                </tr>
                <tr>
                    <td><a href='relatorio/FaltPorPeriodoPorMedico.php?id_login=$id_login'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." M&eacute;dicos Faltosos</a>
                    </td>
                </tr>
                <tr>
                    <td><a href='relatorio/AteEspecialidade.php?id_login=$id_login'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Especialidades Atendidas</a>
                    </td>
                </tr>
                <tr>
                    <td><a href='relatorio/AteProcedimento.php?id_login=$id_login'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Procedimentos Realizados</a>
                    </td>
                </tr>
                <tr>
                    <td><a href='relatorio/AtePaciente.php?id_login=$id_login'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Atendimento Completo por Paciente</a>
                    </td>
                </tr>
                 <tr>
                    <td><a href='relatorio/formTempoDeAtendimentoPorPaciente.php?id_login=$id_login'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Tempo de Espera para Atendimento</a>
                    </td>
                </tr>
                <tr>
                    <td><a href='zf/relatorio/producao-diaria'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Produ&ccedil;&atilde;o di&aacute;ria por profissional</a>
                    </td>
                </tr>
                <tr>
                    <td><a href='zf/relatorio/atendimento'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Atendimento por idade</a>
                    </td>
                </tr>
				        <tr>
                    <td><a href='zf/relatorio/atendimento/form-rel-atendimento-simplificado'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Atendimento por procedimento</a>
                    </td>
                </tr>
                <tr>
                    <td><a href='zf/relatorio/atendimento/form-atendimento-encaminhamento'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Encaminhamento por atendimentos</a>
                    </td>
                </tr>
				<tr>
                    <td><a href='zf/relatorio/esf/form-relatorio-media-atendimentos'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." M&eacutedia atendimentos Enfermeiro X M&eacutedico</a>
                    </td>
                </tr>
				<tr>
                    <td><a href='zf/relatorio/atendimento/form-agendamento-demanda'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." M&eacutedia atendimentos Agendados X Demanda Espont&acirc;nea</a>
                    </td>
                </tr>
                <tr>
                    <td><a href='zf/relatorio/atendimento/form-consulta-odonto'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Odonto - Primeira Consulta x Tratamento Concluido</a>
                    </td>
                </tr>
                <tr>
                    <td><a href='zf/relatorio/atendimento/form-relatorio-recem-nascido'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Rec&eacutem Nascidos</a>
                    </td>
                </tr>  
                <tr>
                    <td><a href='relatorio/TotProcGeral.php'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Procedimentos por m&ecirc;s por Profissional</a>
                    </td>
                </tr>
                <tr>
                    <td><a href='relatorio/AteProcedimentoImportacao.php'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Procedimentos por m&ecirc;s por Profissional(IMPORTADOS)</a>
                    </td>
                </tr>
		</table>
		";

echo $common->closeTab();

echo $common->bodyTab('3');

    echo "
    		<table class='lista' width='90%' align='center' cellspacing='2' cellpadding='4' border='0'>
                <tr>
                    <td><a href='relatorio/dispensacaoporsubgrupo.php?id_login=$id_login'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num('1')." Medicamentos Dispensados por Subgrupo</a>
                    </td>
                </tr>	

                <tr>
                    <td><a href='relatorio/dispensacaoporsubgrupopaciente.php?id_login=$id_login'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Medicamentos Dispensados por Subgrupo (Pacientes)</a>
                    </td>
                </tr>   
                <tr>
                    <td><a href='relatorio/transferenciadepsicotropico.php?id_login=$id_login'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Transferencia de Psicotropicos</a>
                    </td>
                </tr> 
                <tr>
                    <td><a href='relatorio/medicamentodispensadoporpaciente.php?id_login=$id_login'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Medicamentos Dispensados por Pacientes</a>
                    </td>
                </tr>   
                			<tr>
                    <td><a href='relatorio/MovPeriodLocalEstoq.php?id_login=$id_login'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Per&iacute;odo Movimento e Saldo  Centro Estocador</a>
                    </td>
                </tr>

                <tr>
                    <td><a href='relatorio/PosEstLocalEstoq.php?id_login=$id_login'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Posi&ccedil;&atilde;o Estoque Por Centro Estocador</a>
                    </td>
                </tr>
                <tr>
                    <td><a href='relatorio/PosEstLoteEstoq.php?id_login=$id_login'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Posi&ccedil;&atilde;o de Estoque por Lote/Validade</a></td>
                </tr>";
                /*<tr>
                    <td><a href='relatorio/ConsumoSemestral_transferencia.php?id_login=$id_login'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Consumo Semestral do Hospital</a></td>
                </tr>*/
			echo"<tr>
                    <td><a href='zf/relatorio/materiais/produtos-a-vencer'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Produtos a vencer</a></td>
                </tr>
                <tr>
                    <td><a href='zf/relatorio/materiais/form-movimento-entrada'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Movimento de Entrada</a></td>
                </tr>
                <tr>
                    <td><a href='../WebSocialComum/relatorio/index_fornecedor.php?id_login=$id_login'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Custo por Fornecedor</a></td>
                </tr>
                <tr>
                    <td><a href='relatorio/index_fornecedor_paciente.php?id_login=$id_login'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Custo por Fornecedor Paciente</a></td>
                </tr>
                <tr>
                    <td><a href='relatorio/relProduto.php?id_login=$id_login'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Produtos Cadastrados</a></td>
                </tr>
                <tr>
                    <td><a href='relatorio/formCurvaAbc.php'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Curva ABC de consumo</a></td>
                </tr>
				<tr>
                    <td><a href='zf/relatorio/materiais/form-ranking-de-consumo'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Ranking de Consumo de Materiais</a>
					</td>
                </tr>
                <tr>
                    <td><a href='zf/relatorio/materiais/form-saida-produto-setor'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Sa&iacute;da de Produtos por Setor</a>
					</td>
                </tr>
                <tr>
                    <td><a href='relatorio/SaidaPorUnidade.php'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Sa&iacute;da por Unidade</a>
                    </td>
                </tr>
            <!--<tr>
                    <td><a href='relatorio/CurABCConsumo.php?id_login=$id_login'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Curva ABC de Consumo</a></td>
                </tr>-->
                </table>";
echo $common->closeTab();

echo $common->bodyTab('4');
    /*<tr>
        <td><a href='relatorio/PacientesPorPrograma.php?id_login=$id_login'>
            <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num('')." Pacientes cadastrados por programa</a></td>
    </tr>*/
    echo "<table class='lista' width='90%' align='center' cellspacing='2' cellpadding='4' border='0'>
            <tr>
                <td><a href='zf/relatorio/materiais/form-anvisa'>
                    <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num('1')." Psicotr&oacute;picos ANVISA</a></td>
            </tr>
            <tr>
                <td>
                    <a href='relatorio/pacientes_medicamentos.php?id_login=$id_login'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." 
                        Pacientes cadastrados por medicamentos
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    <a href='relatorio/PacientesPorFaixaEtaria.php?id_login=$id_login'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." 
                        Pacientes por Faixa Et&aacute;ria/Produto
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    <a href='relatorio/PacientesPorFaixaEtariaPrograma.php?id_login=$id_login'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." 
                        Pacientes por Faixa Et&aacute;ria/Programa
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    <a href='relatorio/PacientesAtendidosPorPrograma.php?id_login=$id_login'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." 
                        N&uacute;mero de pacientes atendidos por programa
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    <a href='zf/relatorio/farmacia/form-numero-de-pacientes-atendidos-por-medicamento'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." 
                        N&uacute;mero de pacientes atendidos por medicamento
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    <a href='relatorio/QtdeMedicamentoDispensadosPorPeriodo.php?id_login=$id_login'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." 
                        Quantidade de medicamentos dispensados por per&iacute;odo
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    <a href='relatorio/PacientesAtendidos.php?id_login=$id_login'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." 
                        Pacientes Atendidos
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    <a href='zf/relatorio/materiais/estoque-psicotropicos'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." 
                        Estoque de Psicotr&oacute;picos
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    <a href='relatorio/medicamentoPorLote.php'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." 
                        Medicamentos Dispensados por lote
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    <a href='relatorio/medEstoque.php?id_login=$id_login'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." 
                        Posi&ccedil;&atilde;o do Estoque por Data
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    <a href='zf/relatorio/materiais/form-entrada-psicotropicos'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Entrada de psicotr&oacute;picos por fornecedor
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    <a href='zf/relatorio/materiais/form-balanco-produto-setor'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Balan&ccedil;o de produto por setor
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    <a href='zf/relatorio/materiais/transferencias-por-setor'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Transfer&ecirc;ncias por setor
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    <a href='relatorio/transferencia.php'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Transfer&ecirc;ncias por setor (Consolidado)
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    <a href='zf/relatorio/materiais/form-balanco-psicotropicos'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Balan&ccedil;o Completo de Psicotr&oacute;picos
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    <a href='zf/relatorio/materiais/form-relacao-notificacoes-receita'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." 
                        Rela&ccedil;&atilde;o de notifica&ccedil;&otilde;es de Receita
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    <a href='zf/relatorio/farmacia'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Medicamentos dispensados por paciente e setor
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    <a href='zf/relatorio/farmacia/form-numero-pacientes-atendidos-por-periodo-setor'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." N&uacute;mero de pacientes atendidos por per&iacute;odo e setor
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    <a href='zf/relatorio/farmacia/form-pacientes-faltosos'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." 
                        Pacientes Faltosos segundo Dispensa&ccedil;&atilde;o
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    <a href='relatorio/formRelLivroPsico.php'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." 
                        Livro de Psicotropicos
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    <a href='zf/relatorio/farmacia/form-entradas-saidas'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." 
                        Entradas e Saidas
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    <a href='relatorio/formProgramaProduto.php'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." 
                        Programa Paciente Produto
                    </a>
                </td>
            </tr>

            <tr>
                <td>
                    <a href='zf/relatorio/farmacia/form-quantidade-de-pacientes-atendidos-por-sub-grupo'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Relatorio por Sub-Grupo
                    </a>
                </td>
            </tr>

            <!--<tr>
                <td>
                    <a href='zf/relatorio/farmacia/componente-especializado'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." 
                        Componente Especializado da Assist&ecirc;ncia Farmac&ecirc;utica
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    <a href='zf/relatorio/farmacia/solicitacao-medicamentos'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." 
                        Solicita&ccedil;&atilde;o de Medicamento(s)
                    </a>
                </td>
            </tr>-->



            <tr>
                <td>
                    <a href='relatorio/administrativo/formDispensacaoPorSetor.php'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." 
                        Dispensacao por Setor
                    </a>
                </td>
            </tr>
        </table>
        ";
echo $common->closeTab();

echo $common->bodyTab('5');
    echo "
        <table class='lista' width='90%' align='center' cellspacing='2' cellpadding='4' border='0'>
            <tr>
                <td>
                    <a href='relatorio/grupo_doenca_index.php'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num('1')." 
                        Relat&oacute;rio de Doen&ccedil;as
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    <a href='relatorio/diarreiaIndex.php?id_login=$id_login'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." 
                        Relat&oacute;rio por Diarr&eacute;ia
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    <a href='relatorio/sindromeRespiratoriaIndex.php?id_login=$id_login'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." 
                        Relat&oacute;rio por Sindrome Respirat&oacute;ria
                    </a>
                </td>
            </tr>
        </table>";
echo $common->closeTab();


echo $common->bodyTab('6');
	 echo " <table class='lista' width='90%' align='center' cellspacing='2' cellpadding='4' border='0'>
                <tr>
                    <td>
                        <a href='relatorio/temperaturaDiaria.php?id_login=$id_login'>
                            <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num('1')." 
                            Controle de Temperatura de Geladeira
                        </a>
	                </td>
	            </tr>
	            <tr>
	             <td><a href='relatorio/VacUsuario.php?id_login=$id_login'>
				     <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Vacinas/Pacientes/Idade/Doses/Produtos</a>
	              </td>
	        </tr>
	         <tr>
	             <td><a href='relatorio/VacProduto.php?id_login=$id_login'>
				     <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Por Produto (aplicada)</a>
	              </td>
	        </tr>
	         <tr>
	             <td><a href='relatorio/VacProdutoEstoque.php?id_login=$id_login'>
				     <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Por Produto (em estoque)</a>
	              </td>
	        </tr>
	         <tr>
	             <td><a href='relatorio/VacAbertas.php?id_login=$id_login'>
				     <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Frascos abertos</a>
	              </td>
	        </tr>
	        <tr>
                <td>
                    <a href='relatorio/VacAprazPacie.php?id_login=$id_login'>
                    <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Vacinas aprazadas por paciente</a>
                </td>
				</tr>
				<tr>
                <td>
                    <a href='relatorio/vacinasTotais.php?id_login=$id_login' target='_blank'>
                    <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Inconsistencias das aplicacoes</a>
                </td>
	        </tr>
		</table>";
echo $common->closeTab();

echo $common->bodyTab('7');
	 echo "<table class='lista' width='90%' align='center' cellspacing='2' cellpadding='4' border='0'>
	         <tr>
	             <td><a href='relatorio/numeroHiperdiaPorSexoERisco.php?id_login=$id_login'>
				     <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num('1')." Numero de Hipertensos e Diab&eacute;ticos por Sexo e Risco</a>
	              </td>
	        </tr>
			 <tr>
	             <td><a href='relatorio/numeroPacientesHiperdiaPorSexoEFaixaEtaria.php?id_login=$id_login'>
				     <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." N&uacute;mero Pacientes por Sexo e Faixa Et&aacute;ria</a>
	              </td>
	        </tr>
			<tr>
	             <td><a href='relatorio/medicamentosPreescritos.php?id_login=$id_login'>
				     <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Medicamentos Preescritos</a>
	              </td>
	        </tr>
			<tr>
	             <td><a href='relatorio/quantidadePorUnidade.php?id_login=$id_login'>
				     <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Quantidade de Hiperdia por Unidade</a>
	              </td>
	        </tr>
	        </table>";
echo $common->closeTab();


echo $common->bodyTab('8');
	 echo "<table class='lista' width='90%' align='center' cellspacing='2' cellpadding='4' border='0'>
            <tr>
                 <td><a href='relatorio/RelPacienteEncaminhamento.php'>
                     <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num(1)." Pacientes Agendados (Encaminhamento)</a>
                  </td>
            </tr>			<tr>
	             <td><a href='zf/relatorio/laboratorio/form-rel-paciente'>
				     <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Livro laborat&oacute;rio por paciente e solicitante</a>
	              </td>
	        </tr>
	         <tr>
	             <td><a href='relatorio/numeroPacientesHiperdiaPorSexoEFaixaEtaria.php?id_login=$id_login'>
				     <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." N&uacute;mero Pacientes por Sexo e Faixa Et&aacute;ria</a>
	              </td>
	        </tr>
			<tr>
	             <td><a href='zf/relatorio/laboratorio'>
				     <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()."Exames por solicitante por prontu&aacute;rio eletr&ocirc;nico</a>
	              </td>
	        </tr>
			<tr>
	             <td><a href='zf/relatorio/laboratorio/rel-solicitante-age'>
				     <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()."Exames por solicitante por agendamento</a>
	              </td>
	        </tr>
			<tr>
	             <td><a href='relatorio/valReferencia.php?id_login=$id_login'>
				     <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Valores de Refer&ecirc;ncia</a>
	              </td>
	        </tr>

	         <tr>
	             <td><a href='relatorio/RelQtExPorLaboratorio.php?id_login=$id_login'>
				     <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Relatorio Quantidade de Exames Agendados</a>
	              </td>
	        </tr>

	         <tr>
	             <td><a href='relatorio/RelPacienteExame.php?id_login=$id_login'>
				     <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Relatorio Pacientes/Exames Realizados</a>
	              </td>
	        </tr>

	         <tr>
	             <td><a href='relatorio/RelProcColetados.php?id_login=$id_login'>
				     <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Procedimentos Coletados por Per&iacute;odo e Unidade</a>
	              </td>
	        </tr>

	         <tr>
	             <td><a href='relatorio/RelProcColetadosMedico.php?id_login=$id_login'>
				     <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Procedimentos Coletados por M&eacute;dico Solicitante</a>
	              </td>
	        </tr>

            <tr>
	             <td><a href='zf/relatorio/laboratorio/form-extrato-exame'>
				     <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Extrato de Exames Autorizados por Prestador</a>
	              </td>
	        </tr>

            <tr>
	             <td><a href='zf/relatorio/laboratorio/form-extrato-paciente'>
				     <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Extrato por Paciente</a>
	              </td>
            </tr>
            
            <tr>
	             <td><a href='relatorio/form_hemoglobina.php?id_login=$id_login'>
				     <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Relatorio Hemoglobina</a>
	              </td>
            </tr>
            
            <tr>
                 <td><a href='relatorio/examesrealizados.php'>
                     <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Exames realizados por quantidade e valor</a>
                  </td>
            </tr>		

            <tr>
                  <td>
                        <a href='zf/relatorio/recepcao-de-exames'>
                            <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Faturamento de Exames
                        </a>
                  </td>
            </tr>

            <tr>
	             <td><a href='relatorio/form_hemoglobina.php?id_login=$id_login'>
				     <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Relatorio Hemoglobina</a>
	              </td>
            </tr>

            </table>";
echo $common->closeTab();



echo $common->bodyTab('9');

    echo "
    		<table class='lista' width='90%' align='center' cellspacing='2' cellpadding='4' border='0'>
                <tr>
                    <td><a href='relatorio/formRelatorioTransporte.php'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num('1')." Relat&oacute;rio Por Paciente por Destino</a>
                    </td>
                </tr>				<tr>
                    <td><a href='zf/relatorio/transporte/form-motorista/'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num('1')." Relat&oacute;rio Por Motorista</a>
                    </td>
                </tr>
                <tr>
                    <td><a href='zf/relatorio/transporte/form-veiculo'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Relat&oacute;rio Por Ve&iacute;culo</a>
                    </td>
                </tr>
                <tr>
                    <td><a href='zf/relatorio/transporte/form-encaminhamentos'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Relat&oacute;rio de Encaminhamentos</a>
                    </td>
                </tr>
                <tr>
                    <td><a href='zf/relatorio/transporte/form-custo-viagem'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Relat&oacute;rio de Custo de Viagem</a>
                    </td>
                </tr>
            </table>";

 

echo $common->closeTab();

echo $common->bodyTab('10');
//echo"<pre>";print_r($_SESSION);die();
    echo "
            <table class='lista' width='90%' align='center' cellspacing='2' cellpadding='4' border='0'>
                ";$acesso = $_SESSION[logon][usr]->usr_tipo_medico;
                if($acesso!='C' || $acesso!='R' || $acesso!='A'){
                    echo"    
                    <tr>
                        <td><a href='zf/relatorio/usuario/prontuario/?id_login=$id_login'>
                            <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num('1')." Prontu&aacute;rio por Paciente</a>
                        </td>
                    </tr>
                    ";}echo"
				<tr>
                    <td><a href='relatorio/pacientesPorUnidadeImport.php?id_login=$id_login'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Quantidade de Pacientes por Sexo Por Unidade</a>
                    </td>
                </tr>
                <tr>
                    <td><a href='relatorio/formpacienteprontuariomp.php?id_login=$id_login'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Prontu&aacute;rio do Paciente (MP)</a>
                    </td>
                </tr>
            </table>";
echo "<table class='lista' width='90%' align='center' cellspacing='2' cellpadding='4' border='0'>
			<tr>
				<td><a href='zf/relatorio/paciente/index/'>
					<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." N&uacute;mero de pacientes ativos</a>
				</td>
			</tr>
			<tr>
        <td><a href='#' onclick=window.open('".$_SESSION['linkroot'].$_SESSION['modulo']."zf/relatorio/usuario/relatorio-duplicados','teste','width=600,height=400')>
          <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Pacientes duplicados por nome</a>
        </td>
      </tr>
      <tr>
				<td><a href='relatorio/doenca_paciente_index.php'>
					<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Pacientes com do&ecirc;n&ccedil;as</a>
				</td>
			</tr>
      <tr>
				<td><a href='relatorio/formRelImc.php'>
					<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Pacientes com IMC Acima de 30</a>
				</td>
        <tr>
				<td><a href='relatorio/formRankingPacientes.php'>
					<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Ranking de pacientes por atendimento</a>
				</td>

		</tr>
                <tr>
                <td><a href='relatorio/formpacienteprontuariogeral.php'>
                    <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Prontuario Por Paciente</a>
                </td>

        </tr>
            
		</table>";
echo $common->closeTab(); //
echo $common->bodyTab('11');
    echo "
    		<table class='lista' width='90%' align='center' cellspacing='2' cellpadding='4' border='0'>
				<tr>
                    <td><a href='zf/relatorio/bpa/index/tipo/consolidado/'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num('1')." Relat&oacute;rio BPA Consolidado</a>
                    </td>
                </tr>

            </table>";
  echo "
    		<table class='lista' width='90%' align='center' cellspacing='2' cellpadding='4' border='0'>
				<tr>
                    <td><a href='zf/relatorio/programas-federais/consulta-dados-horus/'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Horus Relat&oacute;rio Por Consulta de Dados</a>
                    </td>
                </tr>
            </table>";

    //ID #106475

    echo "
    		<table class='lista' width='90%' align='center' cellspacing='2' cellpadding='4' border='0'>
				<tr>
                    <td><a href='relatorio/administrativo/formAtivColetiva.php'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Relat&oacute;rio de Atividade Coletiva</a>
                    </td>
                </tr>
            </table>";
    echo "
    		<table class='lista' width='90%' align='center' cellspacing='2' cellpadding='4' border='0'>
				<tr>
                    <td><a href='zf/relatorio/programas-federais/form-total-exporta-envio-ficha-esus/'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Relat&oacute;rio de Quantidade de fichas exportadas</a>
                    </td>
                </tr>
                <tr>
                <td><a href='zf/relatorio/programas-federais/form-indice-saude-avaliada'>
                    <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Indice de atendimento por condi&ccedil;&atilde;o de sa&uacute;de avaliada</a>
                </td>
            </tr>
            </table>";
    echo "
    		<table class='lista' width='90%' align='center' cellspacing='2' cellpadding='4' border='0'>
				<tr>
                    <td><a href='zf/relatorio/programas-federais/form-total-envio-ficha-esus-pmaq/'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Relat&oacute;rio PMAQ - Aten&ccedil;&atildeo B&aacute;sica e Odontologia</a>
                    </td>
                </tr>
            </table>";
            

    echo "<table class='lista' width='90%' align='center' cellspacing='2' cellpadding='4' border='0'>
	<tr>
               <td><a href='zf/relatorio/pma2/'>
             <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." PMA2</a>
                </td>
          </tr>
          <tr>
               <td><a href='relatorio/familia_unidade_index.php'>
             <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Fam&iacute;lia por Unidade</a>
                </td>
          </tr>
          <tr>
	             <td><a href='relatorio/etaria_unidade_index.php'>
				     <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Faixa et&aacute;ria por Unidade</a>
	              </td>
	        </tr>
	<tr>
			<td>
				<a href='zf/relatorio/paciente/index/'>
					<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." N&uacute;mero de pacientes ativos
				</a>
			</td>
		</tr>

		<tr>
        	<td>
        		<a href='#' onclick=window.open('".$_SESSION['linkroot'].$_SESSION['modulo']."zf/relatorio/usuario/relatorio-duplicados','teste','width=600,height=400')>
          			<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Pacientes duplicados por nome
          		</a>
        </td>

        </tr>
      		<tr>
				<td>
					<a href='relatorio/doenca_paciente_index.php'>
						<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Pacientes com do&ecirc;n&ccedil;as
					</a>
			</td>
		</tr>

		</tr>
      		<tr>
				<td>
					<a href='relatorio/pacientePorFaixaEtaria.php'>
						<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Pacientes por faixa et&aacute;ria
					</a>
			</td>
		</tr>

</table>";
echo "<table class='lista' width='90%' align='center' cellspacing='2' cellpadding='4' border='0'>
      <tr>
        <td><a href='relatorio/form_atendimento_profissional.php'>
          <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Quantidade de Atendimentos por Profissional</a>
        </td>
      </tr>
      <tr>
        <td><a href='relatorio/form_atendimento_profissional_uni.php'>
          <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Quantidade de Atendimentos por Unidade</a>
        </td>
      </tr>
      <tr>
        <td><a href='relatorio/form_condensado_proc_realizados.php'>
          <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Condensado de Procedimentos Realizados</a>
        </td>
      </tr>
      <tr>
        <td><a href='relatorio/form_quantidade_atendimento_especialidade.php'>
          <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Quantidade de Atendimentos por Especialidade</a>
        </td>
      </tr>
      <tr>
        <td><a href='relatorio/form_quantidade_atendimento_proc_ciap.php'>
          <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Quantidade de Atendimentos por Procedimentos e CIAP</a>
        </td>
      </tr>
      <tr>
        <td><a href='zf/relatorio/esus/index'>
          <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Visita Domiciliar por Per&iacute;odo</a>
        </td>
      </tr>
      <tr>
        <td><a href='zf/relatorio/esus/form-pacientes-por-area-acs'>
          <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Pacientes Cadastrados por &Aacute;rea/ACS</a>
        </td>
      </tr> 
      <tr>
        <td><a href='zf/relatorio/atendimento/form-visita-para-internamento'>
          <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Visita para Internamento</a>
        </td>
      </tr>
      <tr>
        <td><a href='zf/relatorio/atendimento/form-estratificacao-risco'>
          <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Estratifica&ccedil;&atilde;o de Risco</a>
        </td>
      </tr>
      <tr>
        <td><a href='zf/relatorio/esus/form-erros-esus'>
          <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Erros Fichas Esus</a>
        </td>
      </tr>
    </table>";

echo $common->closeTab();

#-------------------------

echo $common->bodyTab('13');
    echo "
    		<table class='lista' width='90%' align='center' cellspacing='2' cellpadding='4' border='0'>
				<tr>
                    <td><a href='relatorio/formAtendimentoTotais.php'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num('1')." Total de Atendimentos por Profissional</a>
                    </td>
                </tr>
				<tr>
                    <td><a href='relatorio/formGrafAbc.php'>
                        <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num('1')." Curva ABC de Consumo</a>
                    </td>
                </tr>

            </table>";

echo $common->closeTab();


?><script>
$( document ).ready(function(){
  $('#listaconsulta').click(function(){
    window.open('zf/relatorio/lista-espera/imprimir/tipo/1');
  });
  $('#listaexame').click(function(){
    window.open('zf/relatorio/lista-espera/imprimir/tipo/2');
  });
  $('#listaodonto').click(function(){
    window.open('zf/relatorio/lista-espera/imprimir/tipo/5');
  });
  $('#listacirurgia').click(function(){
    window.open('zf/relatorio/lista-espera/imprimir/tipo/3');
  });
});
</script><?php


echo $common->bodyTab('12');

echo "<table class='lista' width='90%' align='center' cellspacing='2' cellpadding='4' border='0'>
      <tr>
        <td><a href='zf/relatorio/adm/form-acesso-por-usuarios'>
          <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num('1')." Acessos por Usu&aacute;rio</a>
        </td>
      </tr>
      <tr>
        <td><a href='zf/relatorio/adm/form-producao-por-profissional'>
          <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Produ&ccedil;&atilde;o por Profissional</a>
        </td>
      </tr>
    </table>";

echo $common->closeTab();


echo $common->bodyTab('14');

echo "<table class='lista' width='90%' align='center' cellspacing='2' cellpadding='4' border='0'>
      <tr>
        <td><a href='relatorio/administrativo/formFichaAtendimentoPorIdade.php'>
          <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num('1')." Fichas de Atendimento por Idade</a>
        </td>
      </tr>
      <tr>
        <td><a href='relatorio/administrativo/formAgendamentodeTransporte.php'>
          <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Agendamento de Transportes</a>
        </td>
      </tr>
      <tr>
        <td><a href='relatorio/administrativo/formAgendamentodeTransporteViagens.php'>
          <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Agendamento de Transportes Quantidade de Viagens/Pacientes</a>
        </td>
      </tr>
      <tr>
        <td><a href='relatorio/administrativo/formBeneficiosConcedidos.php'>
          <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Beneficios Concedidos</a>
        </td>
      </tr>
      <tr>
        <td><a href='relatorio/administrativo/formExportacaoBpa.php'>
          <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Relatorio BPA Individualizado</a>
        </td>
      </tr>
      <tr>
        <td><a href='relatorio/administrativo/formProcedimentosRealizados.php'>
          <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Procedimentos Realizados</a>
        </td>
      </tr>
      <tr>
        <td><a href='relatorio/administrativo/formPacienteAtendidosFarmacia.php'>
          <img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/iconrel.png' align='absmiddle' border='0'/>&nbsp;".num()." Pacientes Atendidos na Farmacia</a>
        </td>
      </tr>
    </table>";

echo $common->closeTab();



echo "
    </table>

";

?>
