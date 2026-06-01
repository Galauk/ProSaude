<?php
	session_start();
$common = new commonClass();
echo $common->incJquery();
switch ($pagina) {
    case 1:
        include $_SESSION[root].$_SESSION[modulo]."recepcionado_medico.php";
        break;
    case 2:
        include $_SESSION[root].$_SESSION[modulo]."prontuarioEletronico/alerta.php";
        break;
    case 3:
        include $_SESSION[root].$_SESSION[modulo]."pre_consulta.php";
        break;
    case 4:
        include $_SESSION[root].$_SESSION[modulo]."fazer_atendimento.php";
        break;
    case 5:
        include $_SESSION[root].$_SESSION[modulo]."prontuarioEletronico/hist_atendimento.php";
        break;
    case 6:
        include $_SESSION[root].$_SESSION[modulo]."requisicao_exames.php";
        break;
    case 7:
        include $_SESSION[root].$_SESSION[modulo]."itens_receita_novo.php";
        break;
    case 8:
       include $_SESSION[root].$_SESSION[modulo]."print_atestado.php";
       break;
    case 9:
        include $_SESSION[root].$_SESSION[modulo]."anamnese_medico.php";
        break;
    case 10:
        include $_SESSION[root].$_SESSION[modulo]."hiperdia/entrada.php";//fazer validaчуo para entrar direto com usu_codigo carregado
        break;
    case 11:
        include $_SESSION[root].$_SESSION[modulo]."prontuarioEletronico/odontograma.php";
        break;
    case 12:
        #include $_SESSION[root].$_SESSION[modulo]."prontuarioEletronico/encaminhamentoPopUp.php";
        include $_SESSION[root].$_SESSION[modulo]."prontuarioEletronico/encaminhamento.php";
        break;
    case 13:
        include $_SESSION[root].$_SESSION[modulo]."prontuarioEletronico/dados_paciente.php";
        break;
	case 14:
        include $_SESSION[root].$_SESSION[modulo]."requisicao_exames.php";
        break; 
	case 15:
        include $_SESSION[root].$_SESSION[modulo]."prontuarioEletronico/procedimentos.php";
        break;  
	case 17:
        include $_SESSION[root].$_SESSION[modulo]."preNatal/sisprenatal.php";
        break;
    case 18:
    	echo $common->modalConfirm("Deseja finalizar o atendimento ?","prontuario.php?pagina=4&acao=final&id_login=$id_login&age_codigo=$age_codigo&usu_codigo=$usu_codigo&uni_codigo=$uni_codigo&med_codigo=$med_codigo&esp_codigo=$esp_codigo&age_data=$age_data","prontuario.php?pagina=99&id_login=$id_login&age_codigo=$age_codigo&usu_codigo=$usu_codigo&uni_codigo=$uni_codigo&med_codigo=$med_codigo&esp_codigo=$esp_codigo&age_data=$age_data");
        break;
    case 19:
    	include $_SESSION[root].$_SESSION[modulo]."prontuarioEletronico/hist_atendimento_itens.php";
    	break;      
    case 99:
    	include $_SESSION[root].$_SESSION[modulo]."prontuarioEletronico/imagem.php";
    	break;
    default:    	
    	require_once $_SESSION[root].$_SESSION[modulo]."recepcionado_medico.php";
        #include $_SESSION[root].$_SESSION[modulo]."prontuarioEletronico/lista.php";
    	break;
}
?>