<?php

	@header('Content-Type: text/html; charset=ISO-8859-1');
	require_once '../global.php';
	require_once COMUM ."/library/php/funcoes.db.php";
	$set_codigo = getSetorByLogon();
	__autoload("commonClass");
	$common = new commonClass();
	
	$pro_qtd = $_REQUEST['pro_qtd'];
	$pro_fracionado = $_REQUEST['pro_fracionado'];
	$pro_duracao = $_REQUEST['pro_duracao'];
	
	$produtos = array();
	$out = array();
	$cont = 0;
	foreach($pro_qtd as $item){
		list($pro,$qtd) = explode("|",$item);
		
		$array_duracao = explode("|",$pro_duracao[$cont]);
		$duracao = $array_duracao[1];
		$cont++;
		
		$sqlRequisicao = "";
		
		$selectSaldo = "SELECT COALESCE(s.sal_qtde,0) 
								- 
								(SELECT COALESCE(sum(remil_quantidade),0) 
									FROM requisicao_materiais_itens remi
									JOIN requisicao_materiais_itens_lote remil
									  ON remil.remi_codigo = remi.remi_codigo
									JOIN requisicao_materiais rem
									  ON rem.rem_codigo = remi.rem_codigo
								   WHERE remi.remi_status = 'E'
								     AND remi.pro_codigo = $pro
								     AND set_codigo_sol = $set_codigo
								     AND remil.remil_lote = s.sal_lote) AS sal_qtde, 
								s.sal_lote, 
								TO_CHAR(s.sal_validade,'DD/MM/YYYY') AS sal_validade, 
								s.sal_validade as dataOrder,
								p.pro_nome, 
								s.sal_dose_lote,
								s.sal_dose_lote AS cont_dose
							FROM produto AS p 
						   LEFT JOIN saldo AS s 
							  ON s.pro_codigo=p.pro_codigo 
							 AND sal_qtde > 0 
							 AND sal_validade > NOW() 
							 AND s.set_codigo=$set_codigo
						       WHERE p.pro_codigo=$pro";
		
		
		$sql = "SELECT * FROM(
						SELECT 0 AS sal_qtde, 
								ite_lote AS sal_lote, 
								TO_CHAR(ite_validade,'DD/MM/YYYY')AS sal_validade, 
								ite_validade as dataOrder,
								p.pro_nome,
								ite.ite_dose AS sal_dose_lote ,
								cont_dose 
						  FROM controlefracionado c
						  JOIN itens_movimento ite
						    ON ite.ite_codigo = c.ite_codigo
						   AND ite_validade > NOW()
						  JOIN produto p
						    ON p.pro_codigo = ite.pro_codigo
						  JOIN movimento m
						    ON m.mov_codigo = ite.mov_codigo
						   AND m.set_saida = $set_codigo
			       		 WHERE p.pro_codigo=$pro
						   AND cont_dose > 0				
						
						UNION ALL 				
					
					$selectSaldo
				 ) as x
				 	 
			ORDER BY x.dataOrder, x.sal_qtde";
					
		if(!in_array("$pro|fracao", $pro_fracionado)){
			$sql = $selectSaldo." order by s.sal_validade,sal_qtde";
		}					
					
		$query = pg_query($sql) or die(pg_last_error());		
		$faltam = $qtd;
		$out[$pro] = array();
		while($r = pg_fetch_array($query)){
			//fdebug("<pre>".print_r($sql,1));
			$last_pro = $r['pro_nome'];
			$dose = $r['sal_dose_lote'];
			//if($r['sal_qtde'] == 0)
			//	break;
			if(in_array("$pro|fracao", $pro_fracionado)){
				fdebug("1: ".$r['cont_dose']." ". $faltam);
				if($r['cont_dose'] > $faltam){
				    fdebug("2: ".$r['cont_dose']." ". $faltam);
					$pegar = $faltam;			
				} else {
					if($r['sal_qtde']){
						$fracoesNoSaldo = $r['sal_qtde']*$r['sal_dose_lote'];
						fdebug("3: ".$fracoesNoSaldo);
					} else {
						$fracoesNoSaldo = $r['cont_dose'];
						fdebug("4: ".$fracoesNoSaldo);
					}
					if($fracoesNoSaldo > $faltam){
						$pegar = $faltam;
						fdebug("5: ".$pegar);
					} else {
						$pegar = $fracoesNoSaldo;
						fdebug("6: ".$pegar);
					}
				}
				
			} else {
				if($r['sal_qtde'] > $faltam){
					$pegar = $faltam;			
				} else {
					$pegar = $r['sal_qtde'];
				}
			}
			
			
			$sqlRequisicao = "SELECT COALESCE(sum(remil_quantidade),0) as remil_quantidade 
									FROM requisicao_materiais_itens remi
									JOIN requisicao_materiais_itens_lote remil
									  ON remil.remi_codigo = remi.remi_codigo
									JOIN requisicao_materiais rem
									  ON rem.rem_codigo = remi.rem_codigo
								   WHERE remi.remi_status = 'E'
								     AND remi.pro_codigo = $pro
								     AND set_codigo_sol = $set_codigo
								     AND remil.remil_lote = '$r[sal_lote]'";
			$queryRequisicao = pg_query($sqlRequisicao);
			$qtde_requisicao = 0;
			while($reg_requisicao = pg_fetch_array($queryRequisicao)){
				$qtde_requisicao += $reg_requisicao["remil_quantidade"];
			}
			
			
			if($qtde_requisicao > $r['sal_qtde']){
				echo $common->modalMsg("ALERTA","O lote $r[sal_lote] possui uma pendência de envio de materiais com a quantidade de $qtde_requisicao");
			}
			
			$faltam -= $pegar;
			$out[$pro][] = array(
				"pro_nome" => $r['pro_nome'],
				"lote"     => $r['sal_lote'],
				"validade" => $r['sal_validade'],
				"qtde"     => $pegar,
			    "fracao"   => $r['sal_qtde'],
				"fracionado" => in_array("$pro|fracao", $pro_fracionado),
			    "lote_dose" => $r['sal_dose_lote'],
				"duracao" => $duracao
			);
			
			
			
			if($faltam == 0){
				break;
			}
		}
		if($faltam > 0){
			$out[$pro][] = array(
				"pro_nome"   => $last_pro,
				"qtde"       => "-".$faltam,
				"fracionado" => in_array("$pro|fracao", $pro_fracionado)
			);
		}
	}
	// Confere configuração de medicamentos dispensados
	$sqlConfPeriodo = "SELECT * FROM config WHERE conf_chave = 'VALIDADE_DOS_MEDICAMENTOS' AND conf_valor_bool = true";
	$queryConfPeriodo = pg_query($sqlConfPeriodo);
	$numConfPeriodo = pg_num_rows($queryConfPeriodo);
	#echo "<pre>".print_r($out,1);exit;
	echo "<form method=\"POST\" action=\"dispensar.php\">";
	foreach($out as $pro_codigo => $arr): ?>
		<div class='por_titulo'><?=$arr[0]['pro_nome'];?></div>
		<table style="margin: 0 0 20px 10px;" class="grid ui-widget ui-widget-content ui-corner-all" width="95%">
			<tr class="ui-widget-header">
				<th>Lote</th>
				<th>Validade</th>
				<th>Quant.</th>
				<? if ($numConfPeriodo == "1") { ?>
					<th>Valido até.</th>
				<? } ?>
			</tr>
		<?php foreach($arr as $dados):?>
		<?php if($dados['qtde'] > 0): $dados['pro_nome'] = $pro_codigo;
			$dias = $dados['duracao'];
			if ($dias == "") { $dias = 0; }
		?>
			<tr>
				<td class="ui-widget ui-widget-content"><input type="hidden" class="dispensar" name="d[]" value="<?=implode("|",$dados);?>" /><?=$dados['lote'];?></td>
				<td class="ui-widget ui-widget-content"><?=$dados['validade'];?></td>
				<td class="ui-widget ui-widget-content"><?=$dados['qtde'];?></td>
				<? if ($numConfPeriodo == "1") { ?>
					<td class="ui-widget ui-widget-content"><?=date('d/m/Y', strtotime("+$dias days")); ?>
				<? } ?>
			</tr>
		<?php else: ?>
			<tr>
				<td class="ui-widget ui-widget-content ui-state-error" colspan="4"><em>Não há estoque suficiente. Faltou <?=($dados['qtde']*(-1));?>.</em></td>
			</tr>
		<?php endif;?>
		<?php endforeach; ?>
		</table>
	<?php endforeach; ?>
	</form>