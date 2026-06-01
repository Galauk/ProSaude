<?php
header("Access-Control-Allow-Origin: *");
session_start();
$_SESSION['root'] = $_SERVER['DOCUMENT_ROOT']."/";
$_SESSION['modulo'] = "WebSocialSaude/";
$_SESSION['linkroot'] = "http://".$_SERVER['HTTP_HOST']."/";
$_SESSION['comum'] = "WebSocialComum/";
require_once $_SESSION['root'].$_SESSION['comum']."library/php/db.inc.php";
require_once $_SESSION['root'].$_SESSION['comum']."library/php/funcoes.inc.php";
require_once $_SESSION['root'].$_SESSION['comum']."library/php/funcoes.db.php";

$cidades = array(
    1=>'Nova Esperança',
    2=>'Barboza Ferraz',
    3=>'Engenheiro Beltrão',
    4=>'IDK',
);
$acoesVacina = array(
    'A'=>array('classe'=>'positive', 'acao'=>'Aplicada'),
    'P'=>array('classe'=>'balanced', 'acao'=>'Marcada'),
    'Z'=>array('classe'=>'assertive', 'acao'=>'Aprazada'),
    'R'=>array('classe'=>'dark', 'acao'=>'Vários Reforços'),
);

function recupera($dados, $decode = null){
    if(empty($dados))
        return array();
    
    foreach($dados as $key => $value){
        $valor = $decode ? addslashes(utf8_decode($value)) : addslashes($value);
        $valor = cleanuserinput($valor);
        $post[$key] = trim($valor);
    }
    
    return filter_var_array($post);
}
function cleanuserinput($dirty){
    if (get_magic_quotes_gpc()) {
        $clean = stripslashes($dirty);
    }else{
        $clean = $dirty;
    }
    return $clean;
}
function getUsuarioFb($id){
    $sqlUsuario = "SELECT
            usuario.usu_cartao_sus,
            usuario_app.usu_codigo,
            usuario_app.uapp_codigo,
            usuario_app.uapp_fbid,
            usuario_app.uapp_nome,
            usuario_app.uapp_fbid,
            usuario_app.uapp_ocupacao,
            usuario_app.uapp_fone,
            usuario_app.uapp_celular,
            usuario_app.uapp_email,
            usuario_app.uapp_servidor
            FROM usuario_app
            LEFT JOIN usuario ON usuario_app.usu_codigo = usuario.usu_codigo
            WHERE usuario_app.uapp_fbid = ";
    $queryUsuario = pg_query($sqlUsuario.$id);
    return pg_fetch_assoc($queryUsuario);
}
$chave = 'app_saude_cidadao';
extract(recupera($_POST, 1));
$retorno = array('success'=>false,'msg'=>'Escolha uma ação');
if($acao == md5($chave.'cidades')){
    $retorno['cidades'] = $cidades;
    $retorno['success'] = true;
}
if($cidade == 0){
    $retorno['msg'] = 'Selecione uma cidade';
    die(json_encode($retorno));
}
if($acao == md5($chave.'cadastrar')){
    $validado = true;
    if(empty($sus)){
        $retorno['msg'] = 'Informe o número do cartão SUS';
        $validado = false;
    }
    if(empty($senha)){
        $retorno['msg'] = 'Digite uma senha';
        $validado = false;
    }
    if(empty($senha2)){
        $retorno['msg'] = 'Repira a senha';
        $validado = false;
    }
    if($senha2 != $senha){
        $retorno['msg'] = 'As senhas não coincidem';
        $validado = false;
    }
    if($validado){
        $sqlUsuario = pg_query("SELECT
            usuario.usu_codigo,
            usuario.usu_nome,
            usuario.usu_ocupacao,
            usuario.usu_fone,
            usuario.usu_celular,
            usuario.usu_email,
            usuario_app.uapp_codigo
            FROM usuario
            LEFT JOIN usuario_app ON usuario.usu_codigo = usuario_app.usu_codigo
            WHERE usuario.usu_cartao_sus = '".$sus."'");
        $usuario = pg_fetch_assoc($sqlUsuario);
        if(!empty($usuario['usu_codigo']) && empty($usuario['uapp_codigo'])){
            if(pg_query("INSERT INTO usuario_app (usu_codigo, uapp_nome, uapp_ocupacao, uapp_fone, uapp_celular, uapp_email, uapp_senha, uapp_data_cad, uapp_servidor)
                VALUES ('".$usuario['usu_codigo']."', '".$usuario['usu_nome']."', '".$usuario['usu_ocupacao']."', '".$usuario['usu_fone']."', '".$usuario['usu_celular']."', '".$usuario['usu_email']."', '".$senha."', current_date, 0)")){
                $retorno['success'] = true;
                $retorno['msg'] = 'Cadastrado com sucesso!';
            } else {
                $retorno['msg'] = 'Ocorreu um erro ao cadastrar!';
            }
        } elseif(!empty($usuario['usu_codigo']) && !empty($usuario['uapp_codigo'])){
            $retorno['success'] = true;
            $retorno['msg'] = 'Voce ja tem cadastro!';
        } else {
            $retorno['alert'] = 'Você não possui cadastro no sistema';
            $retorno['msg'] = 'Compareça a uma unidade de saúde mais próxima e efutue seu cadastro.';
        }
    }
}
if($acao == md5($chave.'login')){
    $validado = true;
    if(empty($sus)){
        $retorno['msg'] = 'Informe o número do cartão SUS';
        $validado = false;
    }
    if(empty($senha)){
        $retorno['msg'] = 'Digite uma senha';
        $validado = false;
    }
    if($validado){
        $sqlUsuario = pg_query("SELECT
            usuario.usu_cartao_sus,
            usuario_app.usu_codigo,
            usuario_app.uapp_codigo,
            usuario_app.uapp_fbid,
            usuario_app.uapp_nome,
            usuario_app.uapp_fbid,
            usuario_app.uapp_ocupacao,
            usuario_app.uapp_fone,
            usuario_app.uapp_celular,
            usuario_app.uapp_email,
            usuario_app.uapp_servidor
            FROM usuario
            INNER JOIN usuario_app ON usuario.usu_codigo = usuario_app.usu_codigo
            WHERE usuario.usu_cartao_sus = '".$sus."' AND usuario_app.uapp_senha = '".$senha."'");
        $usuario = pg_fetch_assoc($sqlUsuario);
        if(!empty($usuario['uapp_codigo'])){
            $retorno['success'] = true;
            $retorno['user'] = $usuario;
        } else {
            $retorno['msg'] = 'Cadastrado não encontrado.';
        }
    }
}
if($acao == md5($chave.'facebookLogin')){
    $validado = true;
    if(empty($id)){
        $retorno['msg'] = 'Permita o aplicativo no Facebook';
        $validado = false;
    }
    if($validado){
        $usuario = getUsuarioFb($id);
        if(!empty($usuario['uapp_codigo'])){
            $retorno['success'] = true;
            $retorno['user'] = $usuario;
        } else {
            if(pg_query("INSERT INTO usuario_app (uapp_nome, uapp_fbid, uapp_email, uapp_data_cad, uapp_servidor)
                VALUES ('".$name."', '".$id."', '".$email."', current_date, 0)")){
                $retorno['success'] = true;
                $retorno['user'] = getUsuarioFb($id);
            } else {
                $retorno['msg'] = 'Erro ao logar';
            }
        }
    }
}
if($acao == md5($chave.'cartaoSus')){
    $validado = true;
    if(empty($fbid)){
        $retorno['msg'] = 'Somente usuários logados podem acessar.';
        $validado = false;
    }
    if(empty($sus)){
        $retorno['msg'] = 'Forneça o número do cartão SUS';
        $validado = false;
    }
    if($validado){
        $sqlUsuario = pg_query("SELECT
            usuario.usu_cartao_sus,
            usuario.usu_codigo,
            usuario.usu_nome,
            usuario.usu_ocupacao,
            usuario.usu_fone,
            usuario.usu_celular,
            usuario.usu_email,
            usuario_app.uapp_codigo
            FROM usuario
            LEFT JOIN usuario_app ON usuario.usu_codigo = usuario_app.usu_codigo
            WHERE usuario.usu_cartao_sus = '".$sus."'");
        $usuario = pg_fetch_assoc($sqlUsuario);
        //1o login feito via APP; ja se cadastrou com o cartao antes; apaga a cadastro feito via FB e atualiza o 1 cadastro com o FB_ID
        if(!empty($usuario['usu_codigo']) && !empty($usuario['uapp_codigo'])){
            if(pg_query("DELETE FROM usuario_app WHERE uapp_fbid = '".$fbid."'")){
                if(pg_query("UPDATE usuario_app SET uapp_fbid = '".$fbid."' WHERE usu_codigo = '".$usuario['usu_codigo']."'")){
                    $retorno['success'] = true;
                    $retorno['user'] = getUsuarioFb($fbid);
                } else {
                    $retorno['msg'] = 'Ocorreu um erro ao atualizar!';
                }
            } else {
                $retorno['msg'] = 'Erro ao atualizar cadastro!';
            }
        //1o login feito via FB; vincula o cartao SUS com o cadatro do FB 
        } elseif(!empty($usuario['usu_codigo']) && empty($usuario['uapp_codigo'])){
            if(pg_query("UPDATE usuario_app SET
                usu_codigo = '".$usuario['usu_codigo']."',
                uapp_ocupacao = '".$usuario['usu_ocupacao']."',
                uapp_fone = '".$usuario['usu_fone']."',
                uapp_celular = '".$usuario['usu_celular']."'
                WHERE uapp_fbid = '".$fbid."'")){
                $retorno['user'] = getUsuarioFb($fbid);
                $retorno['success'] = true;
            } else {
                $retorno['msg'] = 'Erro ao atualizar o cadastro!';
            }
        } else {
            $retorno['alert'] = 'Você não possui cadastro no sistema';
            $retorno['msg'] = 'Compareça a uma unidade de saúde mais próxima e efutue seu cadastro.';
        }
    }
}
if($acao == md5($chave.'meusDados')){
    $validado = true;
    if(empty($uapp_codigo)){
        $retorno['msg'] = 'Você não tem permissão.';
        $validado = false;
    }
    if($validado){
        if(pg_query("UPDATE usuario_app SET
            uapp_ocupacao = '".$uapp_ocupacao."',
            uapp_fone = '".$uapp_fone."',
            uapp_celular = '".$uapp_celular."',
            uapp_email = '".$uapp_email."'
            WHERE uapp_codigo = '".$uapp_codigo."'")){
            $retorno['success'] = true;
            $retorno['msg'] = 'Seus dados foram atualizados!';
        } else {
            $retorno['msg'] = 'Erro ao atualizar cadastro!';
        }
    }
}
if($acao == md5($chave.'agendamentos')){
    $validado = true;
    if(empty($usu_codigo)){
        $retorno['msg'] = 'Você não tem permissão.';
        $validado = false;
    }
    if($validado){
        $coresStatus = array(
            'P'=>'light',
            'S'=>'stable',
            'A'=>'positive',
            'N'=>'balanced',
            'F'=>'assertive',
            'T'=>'royal',
            'E'=>'calm',
            'I'=>'dark',
            'M'=>'assertive',
        );
        $retorno['agendamentos'] = array();
        $sqlAgendamentos = "SELECT age.age_data, age.age_ordem, age.age_codigo, age.age_horario, age.age_atendido, (CASE WHEN age_atendido='P' THEN 'Pré-Consulta' WHEN age_atendido='S' THEN 'Recepcionado' WHEN age_atendido='A' THEN 'Atendido' WHEN age_atendido='N' THEN 'Agendado' WHEN age_atendido='T' THEN 'Transferido' WHEN age_atendido='F' THEN 'Faltou' WHEN age_atendido='E' THEN 'Em Atendimento' WHEN age_atendido='I' THEN 'Atendimento Incluso' WHEN age_atendido='M' THEN 'Falta Médica' END) AS status, (CASE WHEN age_atendido='S' THEN 'blue' WHEN age_atendido='Atendido' THEN '#148e00' WHEN age_atendido='Agendado' THEN '#2e6e9e' END) AS cor, usr.usr_nome, esp.esp_nome
            FROM agendamento AS age
            INNER JOIN especialidade AS esp ON age.esp_codigo = esp.esp_codigo
            INNER JOIN usuarios AS usr ON age.med_codigo = usr.usr_codigo
            WHERE age.usu_codigo = '{$usu_codigo}'
            ORDER BY age_ordem ASC, age.age_horario ASC, age.age_horario ASC";
        $queryAgendamentos = pg_query($sqlAgendamentos);
        $c = 0;
        while($agendamento = pg_fetch_assoc($queryAgendamentos)){
            $retorno['agendamentos'][$c] = $agendamento;
            $retorno['agendamentos'][$c]['dia'] = date('d', strtotime($agendamento['age_data']));
            $retorno['agendamentos'][$c]['mes'] = substr(mes( date('n', strtotime($agendamento['age_data'])) ), 0, 3);
            $retorno['agendamentos'][$c]['hora'] = date('H:i', strtotime($agendamento['age_horario']));
            $retorno['agendamentos'][$c]['esp_nome'] = htmlentities(($agendamento['esp_nome']));
            if($agendamento['age_atendido'] == 'N' && $agendamento['age_data'] < date('Y-m-d')){
                $retorno['agendamentos'][$c]['status'] = 'Faltou';
                $retorno['agendamentos'][$c]['classe'] = $coresStatus['F'];
            } else {
                $retorno['agendamentos'][$c]['classe'] = $coresStatus[$agendamento['age_atendido']];
            }
            $c++;
        }
        $retorno['success'] = true;
    }
}
if($acao == md5($chave.'solicitacoesExames')){
    $validado = true;
    if(empty($usu_codigo)){
        $retorno['msg'] = 'Você não tem permissão.';
        $validado = false;
    }
    if($validado){
        $coresStatus = array(
            'A'=>array('classe'=>'balanced', 'status'=>'Agendado'),
            'R'=>array('classe'=>'positive', 'status'=>'Recepcionado'),
            'F'=>array('classe'=>'assertive', 'status'=>'Falta'),
            'C'=>array('classe'=>'dark', 'status'=>'Cancelado'),
            'T'=>array('classe'=>'royal', 'status'=>'Transferência'),
        );
        $retorno['exames'] = array();
        $sqlExames = "SELECT
            col_data_coleta,
            a.age_codigo,
            ai.agei_data,
            ai.agei_status
        FROM agenda a
        JOIN agenda_itens ai ON a.age_codigo = ai.age_codigo
        JOIN coleta c ON c.agei_codigo = ai.agei_codigo
        INNER JOIN usuario u ON u.usu_codigo = a.usu_codigo AND a.usu_codigo = '{$usu_codigo}'
        GROUP BY
        col_data_coleta,
        a.age_codigo,
        ai.agei_data,
        ai.agei_status            
        ORDER BY col_data_coleta DESC";
        $queryExames = pg_query($sqlExames);
        $c = 0;
        while($exame = pg_fetch_assoc($queryExames)){
            // $retorno['exames'][$c] = $exame;
            $retorno['exames'][$c]['data'] = date('d/m/Y', strtotime($exame['agei_data']));
            $retorno['exames'][$c]['classe'] = $coresStatus[$exame['agei_status']]['classe'];
            $retorno['exames'][$c]['status'] = $coresStatus[$exame['agei_status']]['status'];
            $retorno['exames'][$c]['age_codigo'] = $exame['age_codigo'];
            /*$retorno['exames'][$c]['mes'] = substr(mes( date('n', strtotime($exame['age_data'])) ), 0, 3);
            $retorno['exames'][$c]['hora'] = date('H:i', strtotime($exame['age_horario']));
            $retorno['exames'][$c]['esp_nome'] = htmlentities(($exame['esp_nome']));*/
            $c++;
        }
        $retorno['success'] = true;
    }
}
if($acao == md5($chave.'exames')){
    $validado = true;
    if(empty($usu_codigo)){
        $retorno['msg'] = 'Você não tem permissão.';
        $validado = false;
    }
    if(empty($id)){
        $retorno['msg'] = 'Forneça uma solicitação de exame para listar.';
        $validado = false;
    }
    if($validado){
        $coresStatus = array(
            'A'=>array('classe'=>'balanced', 'status'=>'Agendado'),
            'R'=>array('classe'=>'positive', 'status'=>'Recepcionado'),
            'F'=>array('classe'=>'assertive', 'status'=>'Falta'),
            'C'=>array('classe'=>'dark', 'status'=>'Cancelado'),
            'T'=>array('classe'=>'royal', 'status'=>'Transferência'),
        );
        $retorno['exames'] = array();
        $sqlExames = "SELECT 
            i.proc_codigo,
            proc_nome,
            c.med_codigo medico,
            a.usu_codigo,
            u.usu_nome,
            ai.agei_data,
            ai.agei_status,
            a.age_codigo,
            a.med_codigo,
            a.usr_codigo_medico,
            ai.agei_codigo,
            col.col_data_coleta
        FROM 
        medico m 
        JOIN convenio c ON c.med_codigo = m.med_codigo
        JOIN convenio_itens i ON i.conv_codigo = c.conv_codigo
        LEFT JOIN agenda_itens ai ON ai.coni_codigo = i.coni_codigo
        JOIN agenda a ON a.age_codigo = ai.age_codigo
        JOIN usuario u ON u.usu_codigo = a.usu_codigo
        JOIN procedimento proc ON proc.proc_codigo = i.proc_codigo
        JOIN coleta col ON col.agei_codigo = ai.agei_codigo
        JOIN tipodeexame as tp  on tp.proc_codigo = i.proc_codigo AND a.age_codigo = '{$id}'
        ORDER BY proc_nome";
        $queryExames = pg_query($sqlExames);
        $c = 0;
        while($exame = pg_fetch_assoc($queryExames)){
            $retorno['exames'][$c]['nome'] = $exame['proc_nome'];
            $retorno['exames'][$c]['data'] = date('d/m/Y', strtotime($exame['col_data_coleta']));
            $retorno['exames'][$c]['proc_codigo'] = $exame['proc_codigo'];
            $retorno['exames'][$c]['age_codigo'] = $exame['age_codigo'];
            $c++;
        }
        $retorno['success'] = true;
    }
}
if($acao == md5($chave.'detalhesExame')){
    $validado = true;
    if(empty($usu_codigo)){
        $retorno['msg'] = 'Você não tem permissão.';
        $validado = false;
    }
    if(empty($proc)){
        $retorno['msg'] = 'Forneça um procedimento para exibir os detalhes.';
        $validado = false;
    }
    if(empty($age)){
        $retorno['msg'] = 'Forneça um agendamento para exibir os detalhes.';
        $validado = false;
    }
    if($validado){
        $sqlProcedimento = "SELECT cat.cte_codigo, cat.cte_cargo, proc.proc_codigo, proc.proc_nome
        FROM categoriadeexames AS cat
        INNER JOIN tipodeexame AS txa ON txa.cte_codigo=cat.cte_codigo
        INNER JOIN procedimento AS proc ON proc.proc_codigo=txa.proc_codigo
        LEFT JOIN tipo_categoria_ordem AS tco ON txa.txa_codigo=tco.txa_codigo
        WHERE (txa.proc_codigo in (".$proc."))
        ORDER BY tco.tco_ordem ASC";
        $procedimento = pg_fetch_assoc(pg_query($sqlProcedimento));
        $sqlAgeItem = "SELECT agei.* FROM agenda_itens AS agei
        INNER JOIN convenio_itens AS coni ON coni.coni_codigo=agei.coni_codigo
        INNER JOIN procedimento AS proc ON proc.proc_codigo=coni.proc_codigo WHERE (coni.proc_codigo=".$proc.") AND (agei.age_codigo=".$age.")";
        $ageItem = pg_fetch_assoc(pg_query($sqlAgeItem));
        $sqlUsu = "SELECT p.proc_codigo, ai.*, a.*, usu.usu_sexo, ((DATE_PART('YEAR', AGE(NOW(), usu.usu_datanasc))*12)+DATE_PART('MONTH', AGE(NOW(), usu.usu_datanasc))) AS idade FROM coleta AS c INNER JOIN agenda_itens AS ai ON ai.agei_codigo = c.agei_codigo INNER JOIN agenda AS a ON a.age_codigo = ai.age_codigo INNER JOIN convenio_itens AS ci ON ci.coni_codigo = ai.coni_codigo INNER JOIN procedimento AS p ON p.proc_codigo = ci.proc_codigo INNER JOIN usuario AS usu ON usu.usu_codigo=a.usu_codigo WHERE ai.agei_codigo='".$ageItem['agei_codigo']."'";
        $usu = pg_fetch_assoc(pg_query($sqlUsu));
        $sqlLaudos = "SELECT DISTINCT r.vlr_valor, r.res_observacao, r.vlr_valor_m3, p.proc_nome, i.ite_itemdoexame, i.ite_tipo_medida, i.historico, i.ite_ordem, i.ite_codigo, v.vlr_valordereferencia, s.sex_codigo, s.sex_subexame FROM resultadoexame AS r INNER JOIN procedimento AS p ON p.proc_codigo=r.proc_codigo INNER JOIN itensanalise AS i ON i.ite_codigo=r.ite_codigo LEFT JOIN valoresdereferencia AS v ON v.ite_codigo=i.ite_codigo AND (v.vlr_sexo IS NULL OR v.vlr_sexo = '".$usu['usu_sexo']."') AND ".$usu['idade']." BETWEEN COALESCE(v.vlr_faixa_etaria_inicio,0) AND COALESCE(v.vlr_faixa_etaria_fim,9999999) LEFT JOIN subexame AS s ON s.sex_codigo=i.sex_codigo WHERE (r.agei_codigo='".$ageItem['agei_codigo']."') ORDER BY i.ite_ordem ASC, s.sex_codigo ASC, i.ite_codigo ASC";
        $laudos = array();
        $queryLaudos = pg_query($sqlLaudos);
        while($laudo = pg_fetch_assoc($queryLaudos)){
            $laudo['historico'] = $laudo['historico'] == 't' ? true : false;
            $laudo['res_observacao'] = (utf8_encode($laudo['res_observacao']));
            $laudo['ite_tipo_medida'] = htmlentities(utf8_encode($laudo['ite_tipo_medida']));
            $laudos[] = $laudo;
        }
        $sqlMetodos = "SELECT tpm.* FROM tipodemetodos AS tpm INNER JOIN tipodeexame AS txa ON txa.tpm_codigo=tpm.tpm_codigo WHERE (txa.proc_codigo=".$proc.")";
        $metodos = pg_fetch_assoc(pg_query($sqlMetodos));
        $sqlMaterial = "SELECT tma.* FROM tipodematerial AS tma INNER JOIN tipodeexame AS txa ON txa.tma_codigo=tma.tma_codigo WHERE (txa.proc_codigo=".$proc.")";
        $material = pg_fetch_assoc(pg_query($sqlMaterial));
        $sqlColeta = "SELECT col.* FROM coleta AS col WHERE (col.agei_codigo ='".$ageItem['agei_codigo']."')";
        $coleta = pg_fetch_assoc(pg_query($sqlColeta));
        $sqlHistorico = "SELECT res.vlr_valor, ia.ite_codigo, ia.ite_itemdoexame, ia.ite_tipo_medida, col.col_data_coleta FROM resultadoexame AS res INNER JOIN agenda_itens AS ai ON ai.agei_codigo=res.agei_codigo INNER JOIN agenda AS a ON a.age_codigo=ai.age_codigo INNER JOIN itensanalise AS ia ON ia.ite_codigo=res.ite_codigo INNER JOIN coleta AS col ON col.agei_codigo=ai.agei_codigo WHERE (usu_codigo=".$usu_codigo.") AND (proc_codigo=".$proc.") AND (a.age_codigo <> ".$ageItem['age_codigo'].") AND (historico='t') ORDER BY ite_ordem ASC, ite_codigo ASC, col_data_coleta desc";
        $queryHistorico = pg_query($sqlHistorico);
        $historico = array();
        while ($h = pg_fetch_assoc($queryHistorico)) {
            $historico[] = $h;
        }
        $retorno[$proc] = array("cte_codigo"=>$procedimento['cte_codigo'],
          "cte_cargo"=>$procedimento['cte_cargo'],
          "proc_nome"=>$procedimento['proc_nome'],
          "col_data_coleta"=>$coleta['col_data_coleta'],
          "agei_codigo"=>$ageItem['agei_codigo'],
          "metodo"=>htmlentities(utf8_encode($metodos['tpm_metodo'])),
          "material"=>$material['tma_tipo'],
          "laudos"=>$laudos,
          "historico"=>$historico
        );
        /*echo json_encode($retorno);
        pr($retorno);*/
        die(json_encode($retorno));
    }
}
if($acao == md5($chave.'medicamentos')){
    $validado = true;
    if(empty($usu_codigo)){
        $retorno['msg'] = 'Você não tem permissão.';
        $validado = false;
    }
    if($validado){
        $retorno['medicamentos'] = array();
        $sqlMedicamentos = "SELECT
            p.pro_codigo,
            p.pro_nome,
            i.ite_quantidade,
            i.ite_lote,
            i.ite_validade,
            i.ite_horadata,
            u.usr_nome,
            d.med_nome
            FROM movimento m
            INNER JOIN itens_movimento i ON i.mov_codigo = m.mov_codigo
            INNER JOIN produto p ON i.pro_codigo = p.pro_codigo
            LEFT JOIN usuarios u ON m.med_codigo_interno = u.usr_codigo
            LEFT JOIN medico d ON m.med_codigo_externo = d.med_codigo
            WHERE m.usu_codigo = '{$usu_codigo}' AND m.mov_saida = 'D'";
        $queryMedicamentos = pg_query($sqlMedicamentos);
        $c = 0;
        while($medicamento = pg_fetch_assoc($queryMedicamentos)){
            $retorno['medicamentos'][$c]['cod'] = $medicamento['pro_codigo'];
            $retorno['medicamentos'][$c]['nome'] = $medicamento['pro_nome'];
            $retorno['medicamentos'][$c]['quantidade'] = intval($medicamento['ite_quantidade']);
            $retorno['medicamentos'][$c]['lote'] = $medicamento['ite_lote'];
            $retorno['medicamentos'][$c]['validade'] = date('d/m/Y', strtotime($medicamento['ite_validade']));
            $retorno['medicamentos'][$c]['data'] = date('d/m/Y', strtotime($medicamento['ite_horadata']));
            $retorno['medicamentos'][$c]['medico'] = $medicamento['usr_nome'] ? $medicamento['usr_nome'] : $medicamento['med_nome'];
            $c++;
        }
        $retorno['success'] = true;
    }
}
if($acao == md5($chave.'alertas')){
    $validado = true;
    if(empty($usu_codigo)){
        $retorno['msg'] = 'Você não tem permissão.';
        $validado = false;
    }
    if($validado){
        $retorno['alertas'] = array();
        $sqlAlertas = "SELECT a.ale_desc, a.ale_data, u.usr_nome
        FROM alerta a
        JOIN usuarios u ON a.usr_codigo = u.usr_codigo
        WHERE a.usu_codigo = '{$usu_codigo}'";
        $queryAlertas = pg_query($sqlAlertas);
        while($alerta = pg_fetch_assoc($queryAlertas)){
            $alerta['ale_data'] = date('d/m/Y', strtotime($alerta['ale_data']));
            $retorno['alertas'][] = $alerta;
        }
        $retorno['success'] = true;
    }
}
if($acao == md5($chave.'consultas')){
    $validado = true;
    if(empty($usu_codigo)){
        $retorno['msg'] = 'Você não tem permissão.';
        $validado = false;
    }
    if($validado){
        $retorno['consultas'] = array();
        $sqlConsultas = "SELECT
            a.ate_codigo, a.ate_data, a.ate_hora, a.ate_reclamacao, a.ate_exame_fisico, a.ate_diagnostico, a.ate_tratamento, a.ate_curativo, u.uni_desc, usr.usr_nome, e.esp_nome, pc.pc_peso, pc.pc_altura, pc.pc_pressao_sistolica, pc.pc_pressao_diastolica, cd10.cd10_codigo_cid, cd10.cd10_descricao
            FROM atendimento AS a
            INNER JOIN unidade AS u ON u.uni_codigo=a.uni_codigo
            INNER JOIN agendamento AS age ON age.age_codigo=a.age_codigo
            LEFT JOIN usuarios AS usr ON usr.usr_codigo=age.med_codigo
            INNER JOIN especialidade AS e ON e.esp_codigo=age.esp_codigo
            LEFT JOIN pre_consulta AS pc ON pc.age_codigo=a.age_codigo
            LEFT JOIN cid10 AS cd10 ON cd10.cd10_codigo=a.cd10_codigo
            WHERE a.usu_codigo='{$usu_codigo}'
            ORDER BY ate_data DESC, ate_hora DESC";
        $queryConsultas = pg_query($sqlConsultas);
        while($consulta = pg_fetch_assoc($queryConsultas)){
            $consulta['ate_data'] = date('d/m/Y', strtotime($consulta['ate_data']));
            $consulta['esp_nome'] = htmlentities(($consulta['esp_nome']));
            $consulta['cd10_descricao'] = htmlentities(($consulta['cd10_descricao']));
            $consulta['imc'] = $consulta['pc_peso']/($consulta['pc_altura']*$consulta['pc_altura']);
            $retorno['consultas'][] = $consulta;
        }
        $retorno['success'] = true;
    }
}
if($acao == md5($chave.'preConsultas')){
    $validado = true;
    if(empty($usu_codigo)){
        $retorno['msg'] = 'Você não tem permissão.';
        $validado = false;
    }
    if($validado){
        $retorno['consultas'] = array();
        $sqlConsultas = "SELECT pc.pc_codigo, pc.pc_dados, pc.pc_temperatura, pc.pc_peso, pc.pc_altura, pc.pc_pressao_sistolica, pc.pc_pressao_diastolica, pc.pc_clas_risco, pc.pc_saturacao, pc.pc_freq_cardiaca, pc.pc_freq_respiratoria, pc.pc_perimetro_cefalico, pc.pc_glicose, pc.pc_data, age.age_data, uni.uni_desc, usr.usr_nome, usr2.usr_nome AS usr_nome_enf, esp.esp_nome
        FROM pre_consulta AS pc
        INNER JOIN agendamento AS age ON age.age_codigo=pc.age_codigo
        LEFT JOIN unidade AS uni ON uni.uni_codigo=age.uni_codigo
        LEFT JOIN usuarios AS usr ON usr.usr_codigo=age.med_codigo
        LEFT JOIN usuarios AS usr2 ON usr2.usr_codigo=pc.usr_codigo
        LEFT JOIN especialidade AS esp ON esp.esp_codigo=age.esp_codigo
        WHERE (age.usu_codigo='{$usu_codigo}') ORDER BY pc.pc_data DESC";
        $queryConsultas = pg_query($sqlConsultas);
        while($consulta = pg_fetch_assoc($queryConsultas)){
            if($consulta['pc_clas_risco'] == ""){
                $consulta['pc_clas_risco'] = "";
                $consulta['classe'] = "stable";
            }
            if($consulta['pc_clas_risco'] == 1){
                $consulta['pc_clas_risco'] = "Imediato";
                $consulta['classe'] = "assertive";
            }else if($consulta['pc_clas_risco'] == 2){
                $consulta['pc_clas_risco'] = "20 Min";
                $consulta['classe'] = "energized";
            }else if($consulta['pc_clas_risco'] == 3){
                $consulta['pc_clas_risco'] = "60 Min";
                $consulta['classe'] = "balanced";
            }else if($consulta['pc_clas_risco'] == 4){
                $consulta['pc_clas_risco'] = "4 Horas";
                $consulta['classe'] = "positive";
            }
            $consulta['esp_nome'] = htmlentities(($consulta['esp_nome']));
            $consulta['pc_dados'] = htmlentities(($consulta['pc_dados']));
            $consulta['age_data'] = date('d/m/Y', strtotime($consulta['age_data']));
            $consulta['imc'] = $consulta['pc_peso']/($consulta['pc_altura']*$consulta['pc_altura']);
            $retorno['consultas'][] = $consulta;
        }
        $retorno['success'] = true;
    }
}
if($acao == md5($chave.'procedimentos')){
    $validado = true;
    if(empty($usu_codigo)){
        $retorno['msg'] = 'Você não tem permissão.';
        $validado = false;
    }
    if($validado){
        $retorno['procedimentos'] = array();
        $sqlProcedimentos = "SELECT pa.pat_codigo, p.proc_nome, ate.ate_codigo, ate.ate_hora, ate.ate_diagnostico, age.age_data, esp.esp_nome, usr.usr_nome, c.cd10_descricao
        FROM procedimento_atendimento AS pa
        LEFT JOIN procedimento AS p ON p.proc_codigo=pa.proc_codigo
        LEFT JOIN atendimento AS ate ON ate.ate_codigo=pa.ate_codigo
        LEFT JOIN agendamento AS age ON age.age_codigo=ate.age_codigo
        LEFT JOIN especialidade AS esp ON esp.esp_codigo=age.esp_codigo
        LEFT JOIN usuarios AS usr ON usr.usr_codigo=age.med_codigo
        LEFT JOIN cid10 AS c ON c.cd10_codigo=pa.cd10_codigo WHERE (ate.usu_codigo='{$usu_codigo}')
            UNION SELECT pa.pat_codigo, p.proc_nome, ate.ate_codigo, ate.ate_hora, ate.ate_diagnostico, age.age_data, esp.esp_nome, usr.usr_nome, c.cd10_descricao
            FROM procedimento_atendimento AS pa
            INNER JOIN procedimento AS p ON p.proc_codigo=pa.proc_codigo
        LEFT JOIN atendimento AS ate ON ate.ate_codigo=pa.ate_codigo
        LEFT JOIN pre_consulta AS pre ON pre.pc_codigo=pa.pc_codigo
        LEFT JOIN agendamento AS age ON age.age_codigo=pre.age_codigo
        LEFT JOIN especialidade AS esp ON esp.esp_codigo=age.esp_codigo
        LEFT JOIN usuarios AS usr ON usr.usr_codigo=age.med_codigo
        LEFT JOIN cid10 AS c ON c.cd10_codigo=pa.cd10_codigo WHERE (age.usu_codigo='{$usu_codigo}') ORDER BY age_data DESC, ate_hora DESC";
        $queryProcedimentos = pg_query($sqlProcedimentos);
        while($procedimento = pg_fetch_assoc($queryProcedimentos)){
            $procedimento['proc_nome'] = htmlentities(($procedimento['proc_nome']));
            $procedimento['esp_nome'] = htmlentities(($procedimento['esp_nome']));
            $procedimento['cd10_descricao'] = htmlentities(($procedimento['cd10_descricao']));
            $procedimento['age_data'] = date('d/m/Y', strtotime($procedimento['age_data']));
            $retorno['procedimentos'][] = $procedimento;
        }
        $retorno['success'] = true;
    }
}
if($acao == md5($chave.'internacoes')){
    $validado = true;
    if(empty($usu_codigo)){
        $retorno['msg'] = 'Você não tem permissão.';
        $validado = false;
    }
    if($validado){
        $coresStatus = array(
            '1'=>array('classe'=>'energized', 'status'=>'Aguardando'),
            '2'=>array('classe'=>'balanced', 'status'=>'Internado'),
            '3'=>array('classe'=>'positive', 'status'=>'Alta'),
        );
        $retorno['internacoes'] = array();
        $sqlInternacoes = "SELECT DISTINCT io.*, uni.uni_desc, usr.usr_nome, usu.usu_nome, usr2.usr_nome as usr_alta
        FROM internacao_observacao AS io
        INNER JOIN atendimento_internacao AS atin ON atin.io_codigo=io.io_codigo
        INNER JOIN atendimento AS ate ON ate.ate_codigo=atin.ate_codigo
        INNER JOIN unidade AS uni ON uni.uni_codigo=ate.uni_codigo
        INNER JOIN usuarios AS usr ON usr.usr_codigo = ate.med_codigo
        INNER JOIN usuarios AS usr2 ON usr2.usr_codigo = io.usr_codigo_alta
        INNER JOIN usuario AS usu ON usu.usu_codigo=ate.usu_codigo
        WHERE (ate.usu_codigo='{$usu_codigo}')";
        $queryInternacoes = pg_query($sqlInternacoes);
        while($internacao = pg_fetch_assoc($queryInternacoes)){
            $internacao['proc_nome'] = htmlentities(($internacao['proc_nome']));
            $internacao['esp_nome'] = htmlentities(($internacao['esp_nome']));
            $internacao['cd10_descricao'] = htmlentities(($internacao['cd10_descricao']));
            $internacao['io_data_cadastro'] = date('d/m/Y', strtotime($internacao['io_data_cadastro']));
            $internacao['io_data_alta'] = date('d/m/Y', strtotime($internacao['io_data_alta']));
            $internacao['io_status'] = $internacao['io_status'] == 'I' ? 'Internação' : 'Para observação';
            $internacao['classe'] = $coresStatus[$internacao['io_situacao_internacao']]['classe'];
            $internacao['status'] = $coresStatus[$internacao['io_situacao_internacao']]['status'];
            $retorno['internacoes'][] = $internacao;
        }
        $retorno['success'] = true;
    }
}
if($acao == md5($chave.'atividadesColetivas')){
    $validado = true;
    if(empty($usu_codigo)){
        $retorno['msg'] = 'Você não tem permissão.';
        $validado = false;
    }
    if($validado){
        $retorno['atividades'] = array();
        $sqlAtividades = "SELECT
        dt_ativ_col, hr_inicio, hr_fim, tctac.no_cds_tipo_ativ_col, usrs.usr_nome
        FROM tb_cds_ativ_col_participante AS tcacp
        INNER JOIN tb_cds_ficha_ativ_col AS tcfac ON tcfac.co_cds_ficha_ativ_col=tcacp.co_cds_ficha_ativ_col
        INNER JOIN tb_cds_tipo_ativ_col AS tctac ON tctac.co_cds_tipo_ativ_col=tcfac.tp_cds_ativ_col
        INNER JOIN usuarios AS usrs ON usrs.usr_codigo=tcfac.usr_codigo
        WHERE tcacp.usu_codigo ='{$usu_codigo}'";
        $queryAtividades = pg_query($sqlAtividades);
        while($atividade = pg_fetch_assoc($queryAtividades)){
            $atividade['dia'] = date('d', strtotime($atividade['dt_ativ_col']));
            $atividade['mes'] = substr(mes( date('n', strtotime($atividade['dt_ativ_col'])) ), 0, 3);
            $atividade['hr_inicio'] = date('H:i', strtotime($atividade['hr_inicio']));
            $atividade['hr_fim'] = date('H:i', strtotime($atividade['hr_fim']));
            $atividade['no_cds_tipo_ativ_col'] = htmlentities(($atividade['no_cds_tipo_ativ_col']));
            $retorno['atividades'][] = $atividade;
        }
        $retorno['success'] = true;
    }
}
if($acao == md5($chave.'receitas')){
    $validado = true;
    if(empty($usu_codigo)){
        $retorno['msg'] = 'Você não tem permissão.';
        $validado = false;
    }
    if($validado){
        $retorno['receitas'] = array();
        $sqlReceita = "SELECT
            rec.rec_codigo, rec.rec_validade, rec.rec_data,
            itrec.pro_codigo, itrec.irec_recomendacao, itrec.irec_quantidade, itrec.irec_produto, itrec.irec_codigo,
            pro.pro_nome, u.usr_nome
        FROM receita AS rec
        INNER JOIN itemreceita AS itrec ON itrec.rec_codigo=rec.rec_codigo
        INNER JOIN atendimento AS ate ON ate.ate_codigo=rec.ate_codigo
        LEFT JOIN produto AS pro ON pro.pro_codigo=itrec.pro_codigo
        LEFT JOIN usuarios u ON ate.med_codigo = u.usr_codigo
        WHERE usu_codigo='{$usu_codigo}'";
        $queryReceita = pg_query($sqlReceita);
        $c = 0;
        $receitaAnterior = 0;
        while($receita = pg_fetch_assoc($queryReceita)){
            $receita['rec_validade'] = date('d/m/Y', strtotime($receita['rec_validade']));
            $receita['rec_data'] = date('d/m/Y', strtotime($receita['rec_data']));

            if($receitaAnterior != $receita['rec_codigo'] || $receitaAnterior == 0){
                $c = 0;
                $receitaAnterior = $receita['rec_codigo'];
                $retorno['receitas'][$receitaAnterior]['rec_codigo'] = $receita['rec_codigo'];
                $retorno['receitas'][$receitaAnterior]['rec_data'] = $receita['rec_data'];
                $retorno['receitas'][$receitaAnterior]['rec_validade'] = $receita['rec_validade'];
                $retorno['receitas'][$receitaAnterior]['usr_nome'] = $receita['usr_nome'];
            }

            $retorno['receitas'][$receitaAnterior]['itens'][$c]['irec_quantidade'] = $receita['irec_quantidade'];
            $retorno['receitas'][$receitaAnterior]['itens'][$c]['irec_recomendacao'] = $receita['irec_recomendacao'];
            $retorno['receitas'][$receitaAnterior]['itens'][$c]['pro_nome'] = $receita['pro_nome'];
            $receitaAnterior = $receita['rec_codigo'];
            $c++;
        }
        // pr($retorno);
        $retorno['success'] = true;
    }
}
if($acao == md5($chave.'atestados')){
    $validado = true;
    if(empty($usu_codigo)){
        $retorno['msg'] = 'Você não tem permissão.';
        $validado = false;
    }
    if($validado){
        $retorno['atestados'] = array();
        $sqlAtestados = "SELECT a.ate_data, t.atest_codigo
        FROM atendimento a
        INNER JOIN atestado t ON t.ate_codigo = a.ate_codigo
        WHERE a.usu_codigo = '{$usu_codigo}'";
        $queryAtestados = pg_query($sqlAtestados);
        while($atestado = pg_fetch_assoc($queryAtestados)){
            $atestado['ate_data'] = date('d/m/Y', strtotime($atestado['ate_data']));
            $retorno['atestados'][] = $atestado;
        }
        $retorno['success'] = true;
    }
}
if($acao == md5($chave.'detalhesAtestados')){
    $validado = true;
    if(empty($usu_codigo)){
        $retorno['msg'] = 'Você não tem permissão.';
        $validado = false;
    }
    if(empty($id)){
        $retorno['msg'] = 'Forneça um atestado para exibir.';
        $validado = false;
    }
    if($validado){
        $retorno['atestado'] = array();
        $sqlAtestado = "SELECT a.*, t.*, u.usr_nome
        FROM 
        atendimento a
        JOIN atestado t ON a.ate_codigo = t.ate_codigo
        JOIN usuarios u ON a.med_codigo = u.usr_codigo
        WHERE a.usu_codigo = '{$usu_codigo}'";
        $queryAtestado = pg_query($sqlAtestado);
        $retorno['atestado'] = array();
        while($atestado = pg_fetch_assoc($queryAtestado)){
            $atestado['ate_data'] = date('d/m/Y', strtotime($atestado['ate_data']));
            $itens = array();
        
            if($atestado['acompanhando_filho'] == 'S')
                $itens[] = "Acompanhado por <strong>".$atestado['acompanhando']."</strong>.";
            
            if($atestado['retorno_trabalho'] == 'S')
                $itens[] = "Devendo retornar ao trabalho ".$atestado['retornoaotrabalho'].".";
            
            if($atestado['repouso_hs'] == 'S')
                $itens[] = "Devendo permanecer em repouso <strong>".$atestado['repousohs_ini']."hs.</strong> a partir das <strong>".$atestado['repousohs_final']."hs.</strong>";
            
            if($atestado['repouso_hoje'] == 'S')
                $itens[] = "Devendo permanecer em repouso hoje.";
            
            if($atestado['repouso_dia'] == 'S')
                $itens[] = "Devendo permanecer em repouso <strong>".$atestado['repousodias']." dias</strong>, a partir desta data.";
            $atestado['itens'] = $itens;
            $retorno['atestado'] = $atestado;
        }
        $retorno['success'] = true;
    }
}
if($acao == md5($chave.'vacinas')){
    $validado = true;
    if(empty($usu_codigo)){
        $retorno['msg'] = 'Você não tem permissão.';
        $validado = false;
    }
    if($validado){
        $retorno['vacinas'] = array();
        $sqlVacinas = "SELECT vac.vac_usu_codigo, vac.vac_data, vac.vac_dose, vac.vac_acao, COALESCE(ite_lote,'--') AS ite_lote, COALESCE(ite_validade,'1900-01-01') AS ite_validade, pro.pro_nome, pro.pro_codigo, uni.uni_desc, COALESCE(usr_nome,'--') AS usr_nome
        FROM vacina_usuario AS vac
        LEFT JOIN controlefracionado AS cont ON cont.cont_codigo=vac.cont_codigo
        LEFT JOIN itens_movimento AS ite ON ite.ite_codigo=cont.ite_codigo
        LEFT JOIN produto AS pro ON pro.pro_codigo=vac.pro_codigo
        INNER JOIN unidade AS uni ON uni.uni_codigo::text=vac.vac_unidade
        LEFT JOIN usuarios AS usr ON usr.usr_codigo=vac.usr_codigo
        WHERE vac.usu_codigo='{$usu_codigo}' ORDER BY pro.pro_nome ASC, vac.vac_dose ASC";
        $sqlCarterinha = "SELECT car.*, pro.pro_nome
        FROM carteirinha AS car
        INNER JOIN produto AS pro ON pro.pro_codigo=car.pro_codigo
        ORDER BY pro_nome ASC";
        $queryVacinas = pg_query($sqlVacinas);
        $queryCarterinha = pg_query($sqlCarterinha);
        $vacinas = array();
        $carteirinha = array();
        while($vacina = pg_fetch_assoc($queryVacinas)){
            $vacina['id'] = $vacina['vac_usu_codigo'];
            $vacina['dia'] = date('d', strtotime($vacina['vac_data']));
            $vacina['mes'] = substr(mes( date('n', strtotime($vacina['vac_data'])) ), 0, 3);
            $vacina['data'] = date('d/m/Y', strtotime($vacina['vac_data']));
            $vacina['ano'] = date('Y', strtotime($vacina['vac_data']));
            $vacina['dose'] = $vacina['vac_dose'];
            $vacina['acao'] = $acoesVacina[$vacina['vac_acao']]['acao'];
            $vacina['classe'] = $acoesVacina[$vacina['vac_acao']]['classe'];
            $vacinas[] = $vacina;
        }
        foreach ($vacinas as $i => $v) {
            $retorno['vacinas'][$v['pro_codigo']]['pro_nome'] = $v['pro_nome'];
            $retorno['vacinas'][$v['pro_codigo']]['itens'][] = array(
                'id'=>$v['id'],
                'dia'=>$v['dia'],
                'mes'=>$v['mes'],
                'ano'=>$v['ano'],
                'acao'=>$v['acao'],
                'classe'=>$v['classe'],
                'id'=>$v['vac_usu_codigo'],
                'dose'=>$v['dose'],
                'data'=>$v['data']
            );
        }
        while($produto = pg_fetch_assoc($queryCarterinha)){
            $produto['dose_1'] = $produto['dose_um'];
            unset($produto['dose_um']);
            $produto['dose_2'] = $produto['dose_dois'];
            unset($produto['dose_dois']);
            $produto['dose_3'] = $produto['dose_tres'];
            unset($produto['dose_tres']);
            $produto['dose_4'] = $produto['dose_quatro'];
            unset($produto['dose_quatro']);
            $produto['dose_5'] = $produto['dose_cinco'];
            unset($produto['dose_cinco']);
            if(array_key_exists($produto['pro_codigo'], $retorno['vacinas'])){
                $retorno['vacinas'][$produto['pro_codigo']] = array_merge($produto, $retorno['vacinas'][$produto['pro_codigo']]);
            } else {
                $retorno['vacinas'][$produto['pro_codigo']] = $produto;
            }
        }
                
        $retorno['success'] = true;
    }
}
if($acao == md5($chave.'detalhesVacina')){
    $validado = true;
    if(empty($id)){
        $retorno['msg'] = 'Informe uma vacina.';
        $validado = false;
    }
    if(empty($usu_codigo)){
        $retorno['msg'] = 'Você não tem permissão.';
        $validado = false;
    }
    if($validado){
        $retorno['vacina'] = array();
        $sqlVacina = "SELECT vac.vac_usu_codigo, vac.vac_data, vac.vac_dose, vac.vac_acao, COALESCE(ite_lote,'--') AS ite_lote, COALESCE(ite_validade,'1900-01-01') AS ite_validade, pro.pro_nome, pro.pro_codigo, uni.uni_desc, COALESCE(usr_nome,'--') AS usr_nome
        FROM vacina_usuario AS vac
        LEFT JOIN controlefracionado AS cont ON cont.cont_codigo=vac.cont_codigo
        LEFT JOIN itens_movimento AS ite ON ite.ite_codigo=cont.ite_codigo
        LEFT JOIN produto AS pro ON pro.pro_codigo=vac.pro_codigo
        INNER JOIN unidade AS uni ON uni.uni_codigo::text=vac.vac_unidade
        LEFT JOIN usuarios AS usr ON usr.usr_codigo=vac.usr_codigo
        WHERE vac.usu_codigo='".$usu_codigo."' AND vac.vac_usu_codigo = '".$id."'";
        $queryVacina = pg_query($sqlVacina);
        while ($vacina = pg_fetch_assoc($queryVacina)) {
            $vacina['data'] = date('d/m/Y', strtotime($vacina['vac_data']));
            $vacina['validade'] = date('d/m/Y', strtotime($vacina['ite_validade']));
            $vacina['acao'] = $acoesVacina[$vacina['vac_acao']]['acao'];
            $vacina['classe'] = $acoesVacina[$vacina['vac_acao']]['classe'];
            $retorno['vacina'] = $vacina;
        }
        $retorno['success'] = true;
    }
}
if($acao == md5($chave.'unidades')){
    $retorno['unidades'] = array();
    $sqlUnidades = "SELECT uni_desc, uni_endereco, uni_coordenadas FROM unidade WHERE uni_coordenadas IS NOT NULL";
    $queryUnidades = pg_query($sqlUnidades);
    while($unidade = pg_fetch_assoc($queryUnidades)){
        $retorno['unidades'][] = $unidade;
    }
    $retorno['success'] = true;
}
if($acao == md5($chave.'contatosEmergencia')){
    $retorno['contatos'] = array(
        100=>'Secretaria dos Direitos Humanos',
        160=>'Disque Saúde',
        180=>'Delegacias Especializadas de Atendimento à Mulher',
        181=>'Disque Denúncia',
        190=>'Polícia Militar',
        191=>'Polícia Rodoviária Federal',
        192=>'Serviço Público de Remoção de Doentes (ambulância)',
        193=>'Corpo de Bombeiros',
        194=>'Polícia Federal',
        197=>'Polícia Civil',
        198=>'Polícia Rodoviária Estadual',
        199=>'Defesa Civil',
    );
    $retorno['success'] = true;
}
if($acao == md5($chave.'especialidades')){
    $retorno['especialidades'] = [
        356=>['nome'=>'Fisioterapeuta geral', 'icon'=>'bone'],
        367=>['nome'=>'Fonoaudiólogo', 'icon'=>'speak'],
        104=>[ 'nome'=>'Médico cardiologista', 'icon'=>'heart-line'],
        105=>[ 'nome'=>'Médico clínico', 'icon'=>'doctor'],
        105=>[ 'nome'=>'Médico dermatologista ', 'icon'=>'skin'],
        109=>[ 'nome'=>'Médico ginecologista e obstetra', 'icon'=>'uterus'],
        108=>[ 'nome'=>'Médico oncologista', 'icon'=>'ribbon'],
        108=>[ 'nome'=>'Médico ortopedista e traumatologista', 'icon'=>'injury'],
        108=>[ 'nome'=>'Médico pediatra', 'icon'=>'baby'],
        109=>[ 'nome'=>'Médico psiquiatra', 'icon'=>'mind'],
        366=>['nome'=>'Nutricionista', 'icon'=>'stomach'],
        115=>[ 'nome'=>'Psicólogo clínico', 'icon'=>'brain'],
    ];
    $retorno['success'] = true;
}
if($acao == md5($chave.'diasDisponiveis')){
    $retorno['dias'] = [];
    $retorno['success'] = true;
}
if($acao == md5($chave.'horariosDisponiveis')){
    $retorno['horarios'] = [];
    $retorno['success'] = true;
}
if($acao == md5($chave.'economiasSaude')){
    $validado = true;
    if(empty($usu_codigo)){
        $retorno['msg'] = 'Você não tem permissão.';
        $validado = false;
    }
    if($validado){
        $sqlMedicamentos = "SELECT
            i.ite_custo_medio, i.ite_quantidade
            FROM movimento m
            INNER JOIN itens_movimento i ON i.mov_codigo = m.mov_codigo
            WHERE m.usu_codigo = '{$usu_codigo}' AND m.mov_saida = 'D'";
        $queryMedicamentos = pg_query($sqlMedicamentos);
        $retorno['relatorio'] = array('medicamentos'=>0, 'consultas'=>0, 'exames'=>0);
        while ($medicamento = pg_fetch_assoc($queryMedicamentos)) {
            $medicamento['ite_custo_medio'] = $medicamento['ite_custo_medio'] == 0 ? 0.06 : $medicamento['ite_custo_medio'];
            $medicamento['ite_custo_medio'] = round(($medicamento['ite_custo_medio'] * $medicamento['ite_quantidade']), 2);
            $retorno['relatorio']['medicamentos'] += $medicamento['ite_custo_medio'];
        }

        $sqlExames = "SELECT proc_vlsa
        FROM convenio_itens i
        LEFT JOIN agenda_itens ai ON ai.coni_codigo = i.coni_codigo
        JOIN agenda a ON a.age_codigo = ai.age_codigo
        JOIN usuario u ON u.usu_codigo = a.usu_codigo
        JOIN procedimento proc ON proc.proc_codigo = i.proc_codigo
        JOIN tipodeexame as tp on tp.proc_codigo = i.proc_codigo AND a.usu_codigo = '{$usu_codigo}'
        ";
        
        $queryExames = pg_query($sqlExames);
        while ($exame = pg_fetch_assoc($queryExames)) {
            $retorno['relatorio']['exames'] += $exame['proc_vlsa'];
        }

        $sqlAgendamentos = "SELECT count(age.age_codigo) as qtd
            FROM agendamento AS age
            INNER JOIN especialidade AS esp ON age.esp_codigo = esp.esp_codigo
            INNER JOIN usuarios AS usr ON age.med_codigo = usr.usr_codigo
            WHERE age.usu_codigo = '{$usu_codigo}'";
        $queryAgendamentos = pg_query($sqlAgendamentos);
        $consultas = pg_fetch_assoc($queryAgendamentos);
        $retorno['relatorio']['consultas'] = 21 * $consultas['qtd'];

        $retorno['relatorio']['total'] = $retorno['relatorio']['exames'] + $retorno['relatorio']['consultas'] + $retorno['relatorio']['medicamentos'];
        $retorno['relatorio']['medicamentos'] = number_format($retorno['relatorio']['medicamentos'], 2, ',', '.');
        $retorno['relatorio']['consultas'] = number_format($retorno['relatorio']['consultas'], 2, ',', '.');
        $retorno['relatorio']['exames'] = number_format($retorno['relatorio']['exames'], 2, ',', '.');
        $retorno['relatorio']['total'] = number_format($retorno['relatorio']['total'], 2, ',', '.');
        $retorno['success'] = true;
    }

}

die(json_encode($retorno));
?>