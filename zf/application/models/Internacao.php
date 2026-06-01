<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_Internacao extends Elotech_Db_Table_Abstract {

    protected $_name = 'internacao_observacao';// nome da tabela do banco
    protected $_primary = 'io_codigo'; // pk da tabela
    protected $_dependentTables = array();
	
    /* Backup sql de quartos 06-05-2013
	public function buscaQuartos($acao=FALSE){
        // "D" = DISPONIVEIS
        // "P" = PARCIALEMENTE OCUPADOS
        // "O" = OCUPADOS
        $where = $this->select(FALSE)
                      ->setIntegrityCheck(FALSE)
                      ->from(array("q"=>"quarto"),array("distinct (q.qua_codigo)",
                                                        "apt_codigo",
                                                        "(select count(*) 
                                                            from internacao_observacao i
                                                            join paciente_leito p
                                                              on p.io_codigo = i.io_codigo
                                                            join leito l
                                                              on l.lei_codigo = p.lei_codigo
                                                            join quarto q2
                                                              on q2.qua_codigo  = l.qua_codigo
                                                           where io_situacao_internacao= 2 
                                                             and q2.qua_codigo = q.qua_codigo ) as ocupados,
                                                         (select count(q3.qua_codigo) 
                                                            from leito l2
                                                            join quarto q3
                                                              on q3.qua_codigo = l2.qua_codigo
                                                           where q3.qua_codigo = q.qua_codigo) as disponiveis"));
        
         if($acao == "D"){
            $where->where("(select count(l.lei_codigo) 
                              from leito l 
                              join paciente_leito pl 
                                on pl.lei_codigo = l.lei_codigo
                              left join internacao_observacao iobs
                                on iobs.io_codigo = pl.io_codigo
                             where l.qua_codigo = q.qua_codigo
                               and iobs.io_situacao_internacao = 1) = 0")
               ->where("(select count(*) 
                                                            from internacao_observacao i
                                                            join paciente_leito p
                                                              on p.io_codigo = i.io_codigo
                                                            join leito l
                                                              on l.lei_codigo = p.lei_codigo
                                                            join quarto q2
                                                              on q2.qua_codigo  = l.qua_codigo
                                                           where io_situacao_internacao= 2 
                                                             and q2.qua_codigo = q.qua_codigo ) = 0");
        }
        if($acao == "P"){
            $where->where("(select count(*) 
                                from internacao_observacao i
                                join paciente_leito p
                                    on p.io_codigo = i.io_codigo
                                join leito l
                                    on l.lei_codigo = p.lei_codigo
                                join quarto q2
                                    on q2.qua_codigo  = l.qua_codigo
                                where io_situacao_internacao= 2 
                                    and q2.qua_codigo = q.qua_codigo ) < (select count(q3.qua_codigo) 
                                from leito l2
                                join quarto q3
                                    on q3.qua_codigo = l2.qua_codigo
                                where q3.qua_codigo = q.qua_codigo)
                                  and (select count(*) 
                                        from internacao_observacao i
                                        join paciente_leito p
                                            on p.io_codigo = i.io_codigo
                                        join leito l
                                            on l.lei_codigo = p.lei_codigo
                                        join quarto q2
                                            on q2.qua_codigo  = l.qua_codigo
                                        where io_situacao_internacao= 2 
                                            and q2.qua_codigo = q.qua_codigo ) > 0");
                   
        }
        
        if($acao == "O"){
            $where->where("(select count(*) 
                                from internacao_observacao i
                                join paciente_leito p
                                    on p.io_codigo = i.io_codigo
                                join leito l
                                    on l.lei_codigo = p.lei_codigo
                                join quarto q2
                                    on q2.qua_codigo  = l.qua_codigo
                                where io_situacao_internacao= 2 
                                    and q2.qua_codigo = q.qua_codigo ) = (select count(q3.qua_codigo) 
                                from leito l2
                                join quarto q3
                                    on q3.qua_codigo = l2.qua_codigo
                                where q3.qua_codigo = q.qua_codigo)");
            
            
        }
        
		die($where);
		
		return $this->fetchAll($where);
        
        }
	*/
	public function buscaQuartos($acao=FALSE){
            // "D" = DISPONIVEIS
            // "P" = PARCIALEMENTE OCUPADOS
            // "O" = OCUPADOS
            $where = $this->select(FALSE)
                          ->setIntegrityCheck(FALSE)
                          ->from(array("q"=>"quarto"),array("distinct (q.qua_codigo)","q.apt_codigo",
                                                            "(SELECT COUNT(*)
                                                                FROM
                                                                    paciente_leito
                                                                INNER JOIN
                                                                    leito ON leito.lei_codigo = paciente_leito.lei_codigo
                                                                INNER JOIN
                                                                    quarto ON leito.qua_codigo = quarto.qua_codigo
                                                                INNER JOIN
                                                                    internacao_observacao ON paciente_leito.io_codigo = internacao_observacao.io_codigo
                                                                INNER JOIN
                                                                    leito_grade ON internacao_observacao.io_codigo = leito_grade.io_codigo
                                                                WHERE
                                                                    leito_grade.lgra_status = 1 AND
                                                                    q.qua_codigo = quarto.qua_codigo AND
                                                                    leito_grade.lgra_codigo not in (SELECT DISTINCT
                                                                                                       leito_grade.lgra_codigo
                                                                                                    FROM
                                                                                                        leito_grade
                                                                                                    INNER JOIN
                                                                                                        leito_itens_grade ON leito_grade.lgra_codigo = leito_itens_grade.lgra_codigo
                                                                                                    INNER JOIN
                                                                                                        controlefracionado_reserva ON leito_grade.lgra_codigo = controlefracionado_reserva.lgra_codigo
                                                                                                    WHERE
                                                                                                        leito_grade.lgra_status = 1 AND
                                                                                                        leito_grade.lgra_proximo IS NULL)) AS pac_res,
                                                            (SELECT COUNT(*)
                                                                FROM
                                                                    paciente_leito
                                                                INNER JOIN
                                                                    leito ON leito.lei_codigo = paciente_leito.lei_codigo
                                                                INNER JOIN
                                                                    quarto ON leito.qua_codigo = quarto.qua_codigo
                                                                INNER JOIN
                                                                    internacao_observacao ON paciente_leito.io_codigo = internacao_observacao.io_codigo
                                                                INNER JOIN
                                                                    leito_grade ON internacao_observacao.io_codigo = leito_grade.io_codigo
                                                                WHERE
                                                                    leito_grade.lgra_status = 2 AND
                                                                    q.qua_codigo = quarto.qua_codigo AND
                                                                    leito_grade.lgra_codigo in (SELECT DISTINCT
                                                                                                       leito_grade.lgra_codigo
                                                                                                    FROM
                                                                                                        leito_grade
                                                                                                    INNER JOIN
                                                                                                        leito_itens_grade ON leito_grade.lgra_codigo = leito_itens_grade.lgra_codigo
                                                                                                    WHERE
                                                                                                        leito_grade.lgra_status = 2)) as pac_ok,
                                                            (SELECT COUNT(*) 
                                                                    FROM 
                                                                            internacao_observacao 
                                                                    INNER JOIN 
                                                                            paciente_leito on paciente_leito.io_codigo = internacao_observacao.io_codigo
                                                                    INNER JOIN   
                                                                            leito on leito.lei_codigo = paciente_leito.lei_codigo
                                                                    INNER JOIN  
                                                                            quarto ON leito.qua_codigo = quarto.qua_codigo
                                                                    WHERE 
                                                                            io_situacao_internacao = 2 and 
                                                                            q.qua_codigo = quarto.qua_codigo
                                                            ) as ocupados,
                                                            (SELECT COUNT(q3.qua_codigo) 
                                                                    from leito l2
                                                                    join quarto q3
                                                                      on q3.qua_codigo = l2.qua_codigo
                                                               where q3.qua_codigo = q.qua_codigo) as disponiveis
                                                            
                                                            "));
            // Disponiveis 
            if($acao == "D"){
                $where->where("(select count(l.lei_codigo) 
                              from leito l 
                              join paciente_leito pl 
                                on pl.lei_codigo = l.lei_codigo
                              left join internacao_observacao iobs
                                on iobs.io_codigo = pl.io_codigo
                             where l.qua_codigo = q.qua_codigo
                               and iobs.io_situacao_internacao = 1) = 0")
                        ->where("(select count(*) 
                                    from internacao_observacao i
                                    join paciente_leito p
                                      on p.io_codigo = i.io_codigo
                                    join leito l
                                      on l.lei_codigo = p.lei_codigo
                                    join quarto q2
                                      on q2.qua_codigo  = l.qua_codigo
                                   where io_situacao_internacao= 2 
                                     and q2.qua_codigo = q.qua_codigo ) = 0");
            }
            // Parcialmente ocupados
            if($acao == "P"){
                $where->where("(select count(*) 
                                    from internacao_observacao i
                                    join paciente_leito p
                                        on p.io_codigo = i.io_codigo
                                    join leito l
                                        on l.lei_codigo = p.lei_codigo
                                    join quarto q2
                                        on q2.qua_codigo  = l.qua_codigo
                                    where io_situacao_internacao= 2 
                                        and q2.qua_codigo = q.qua_codigo ) < (select count(q3.qua_codigo) 
                                    from leito l2
                                    join quarto q3
                                        on q3.qua_codigo = l2.qua_codigo
                                    where q3.qua_codigo = q.qua_codigo)
                                      and (select count(*) 
                                            from internacao_observacao i
                                            join paciente_leito p
                                                on p.io_codigo = i.io_codigo
                                            join leito l
                                                on l.lei_codigo = p.lei_codigo
                                            join quarto q2
                                                on q2.qua_codigo  = l.qua_codigo
                                            where io_situacao_internacao= 2 
                                                and q2.qua_codigo = q.qua_codigo ) > 0");
        }
        // Ocupados
        if($acao == "O"){
            $where->where("(select count(*) 
                                from internacao_observacao i
                                join paciente_leito p
                                    on p.io_codigo = i.io_codigo
                                join leito l
                                    on l.lei_codigo = p.lei_codigo
                                join quarto q2
                                    on q2.qua_codigo  = l.qua_codigo
                                where io_situacao_internacao= 2 
                                    and q2.qua_codigo = q.qua_codigo ) = (select count(q3.qua_codigo) 
                                from leito l2
                                join quarto q3
                                    on q3.qua_codigo = l2.qua_codigo
                                where q3.qua_codigo = q.qua_codigo)");
        }
        return $this->fetchAll($where);
    }
   
    public function buscaLeitos($qua_codigo){
        
        
        $where = $this
                    ->getDefaultAdapter()
                    ->query(" select lei_codigo,
                                    '' as apt_codigo,qua_codigo,
                                    '' as usu_nome,
                                    0 as io_situacao_internacao, 
                                    0 as usu_codigo,
                                    0 as io_codigo,
                                    0,
                                    0 from leito 
                                  where qua_codigo = $qua_codigo
                                    and lei_ocupado = 'f'
                                    
                                        UNION ALL
                                        
                                        select  l.lei_codigo,
                                            apt_codigo,
                                            q.qua_codigo,
                                            u.usu_nome,
                                            io.io_situacao_internacao,
                                            u.usu_codigo,
                                            io.io_codigo,
                                            (SELECT COUNT(*) FROM 
                                                usuario
                                              INNER JOIN
                                                      agendamento ON  usuario.usu_codigo = agendamento.usu_codigo
                                              INNER JOIN
                                                      atendimento ON agendamento.age_codigo = atendimento.age_codigo
                                              INNER JOIN 
                                                      atendimento_internacao ON atendimento.ate_codigo = atendimento_internacao.ate_codigo
                                              INNER JOIN
                                                      internacao_observacao ON atendimento_internacao.io_codigo = internacao_observacao.io_codigo
                                              INNER JOIN
                                                      leito_grade ON internacao_observacao.io_codigo = leito_grade.io_codigo
                                              WHERE
                                                      leito_grade.lgra_status = 2 AND
                                                      usuario.usu_codigo = u.usu_codigo AND
                                                      leito_grade.lgra_codigo in (SELECT DISTINCT
                                                                                         leito_grade.lgra_codigo
                                                                                      FROM
                                                                                              leito_grade
                                                                                      INNER JOIN
                                                                                              leito_itens_grade ON leito_grade.lgra_codigo = leito_itens_grade.lgra_codigo
                                                                                      WHERE
                                                                                              leito_grade.lgra_status = 2)),
                                                (SELECT COUNT(*) FROM usuario
                                                        INNER JOIN
                                                                agendamento ON  usuario.usu_codigo = agendamento.usu_codigo
                                                        INNER JOIN
                                                                atendimento ON agendamento.age_codigo = atendimento.age_codigo
                                                        INNER JOIN 
                                                                atendimento_internacao ON atendimento.ate_codigo = atendimento_internacao.ate_codigo
                                                        INNER JOIN
                                                                internacao_observacao ON atendimento_internacao.io_codigo = internacao_observacao.io_codigo
                                                        INNER JOIN
                                                                leito_grade ON internacao_observacao.io_codigo = leito_grade.io_codigo
                                                        WHERE
                                                                leito_grade.lgra_status = 1 AND
                                                                usuario.usu_codigo = u.usu_codigo AND
                                                                leito_grade.lgra_codigo in (SELECT DISTINCT
                                                                                                leito_grade.lgra_codigo
                                                                                             FROM
                                                                                                     leito_grade
                                                                                             INNER JOIN
                                                                                                     leito_itens_grade ON leito_grade.lgra_codigo = leito_itens_grade.lgra_codigo
                                                                                             INNER JOIN
                                                                                                     controlefracionado_reserva ON leito_grade.lgra_codigo = controlefracionado_reserva.lgra_codigo
                                                                                             WHERE
                                                                                                     leito_grade.lgra_status = 1 AND
                                                                                                     leito_grade.lgra_proximo IS NULL))
                                        from quarto q
                                        join leito l
                                            on l.qua_codigo = q.qua_codigo
                                        join paciente_leito pl
                                            on pl.lei_codigo = l.lei_codigo
                                        join internacao_observacao io
                                            on io.io_codigo = pl.io_codigo
                                        join atendimento_internacao ai
                                            on ai.io_codigo = io.io_codigo
                                        join atendimento at
                                            on at.ate_codigo = ai.ate_codigo
                                        join agendamento as ag
                                            on ag.age_codigo = at.age_codigo
                                        join usuario u
                                            on u.usu_codigo = ag.usu_codigo
                                        where q.qua_codigo = $qua_codigo
                                        and io_situacao_internacao = 2")
                                        ->fetchAll();
        
        

        
        return $where;

        
    }
    
    public function getPacientesInternados($lei_codigo=FALSE){
        $where = $this->select(FALSE)
                      ->setIntegrityCheck(FALSE)
                      ->from(array("l"=>"leito"),"distinct(l.lei_codigo)")
                      ->join(array("pl"=>"paciente_leito"),"pl.lei_codigo=l.lei_codigo","")
                      ->join(array("io"=>"internacao_observacao"),"io.io_codigo=pl.io_codigo",array("io_situacao_internacao","io_codigo"))
                      ->join(array("ai"=>"atendimento_internacao"),"ai.io_codigo=io.io_codigo","")
                      ->join(array("at"=>"atendimento"),"at.ate_codigo = ai.ate_codigo","")
                      ->join(array("age"=>"agendamento"),"age.age_codigo=at.age_codigo","")
                      ->join(array("usu"=>"usuario"),"usu.usu_codigo=age.usu_codigo",array("usu_codigo","usu_nome"))
                      ->where("l.lei_codigo=?",$lei_codigo)
                      ->where("io.io_situacao_internacao = 2");
         return $this->fetchAll($where);
    }
	
	public function getFichaInternacao($ate_codigo){
		$where = $this->select(FALSE)
					->setIntegrityCheck(FALSE)
					->from(array("age"=>"agendamento"),array(""))
					->join(array("uni"=>"unidade"),"age.uni_codigo = uni.uni_codigo",array("uni_desc"))
					->join(array("usu"=>"usuario"),"age.usu_codigo = usu.usu_codigo",array("usu_nome","usu_datanasc"))
					->joinLeft(array("pc"=>"pre_consulta"),"age.age_codigo = pc.age_codigo",array("pc_peso","pc_freq_cardiaca","pc_pressao_sistolica","pc_pressao_diastolica","pc_temperatura"))
					->joinLeft(array("dom"=>"domicilio"),"usu.dom_codigo = dom.dom_codigo",array("dom_numero"))
					->join(array("ate"=>"atendimento"),"age.age_codigo = ate.age_codigo",array("ate_reclamacao","ate_codigo","ate_data","ate_hora"))
					->joinLeft(array("atei"=>"atendimento_internacao"),"ate.ate_codigo = atei.ate_codigo",array(""))
					->joinLeft(array("io"=>"internacao_observacao"),"atei.io_codigo = io.io_codigo",array("io_observacao","io_data_cadastro","io_codigo"))
					->joinLeft(array("cd10"=>"cid10"),"ate.cd10_codigo = cd10.cd10_codigo",array("cd10_descricao"))
					->joinLeft(array("rua"=>"rua"),"dom.rua_codigo = rua.rua_codigo",array("rua_nome","rua_bairro"))
					->joinLeft(array("cid"=>"cidade"),"rua.cid_codigo = cid.cid_codigo",array("cid_nome"))
					->where("ate.ate_codigo=?",$ate_codigo);
		return $this->fetchRow($where);
	}
	
	public function getGradeMedicacaoFicha($io_codigo){
		$where = $this->select(FALSE)
					->setIntegrityCheck(FALSE)
					->from(array("lgra"=>"leito_grade"),array("lgra_repeticoes","lgra_intervalo"))
					->joinLeft(array("ldis"=>"leito_dispensacao"),"lgra.lgra_codigo = ldis.lgra_codigo",array("ldis_datahora"))
					->join(array("lig"=>"leito_itens_grade"),"lgra.lgra_codigo = lig.lgra_codigo",array("lig_quantidade"))
                                        ->join(array("ta"=>"tb_administracao_produto"),"ta.adm_codigo = lig.adm_codigo",array("adm_nome","adm_sigla"))
					->join(array("pro"=>"produto"),"lig.pro_codigo = pro.pro_codigo",array("pro_nome"))
					->where("lgra.io_codigo=?",$io_codigo);
		return $this->fetchAll($where);
	}

	
}
