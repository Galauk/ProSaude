<?php
/**
 * estava pegando apenas tipo dispensacao, agora pega também saida de consumo
 * 
*/
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	    
    $dtIni = "<b>".$_GET[data_ini]."</b>";
    $dtFin = "<b>".$_GET[data_fim]."</b>";

    $dt1 = explode("/",$_GET['data_ini']);
    $dt1 = $dt1[2]."-".$dt1[1]."-".$dt1[0];
    $dt2 = explode("/",$_GET['data_fim']);
    $dt2 = $dt2[2]."-".$dt2[1]."-".$dt2[0];
    
    $pro_cod = $_GET[pro_cod];
    $estocador = $_GET[estocador];
    
    if ($estocador == -1)
    {
        $CE = "TODOS OS CENTROS ESTOCADORES";
    }
    else
    {
        $busca = pg_query("select set_nome from setor where set_codigo = $estocador");
        $res=pg_fetch_array($busca);
        $CE = "<b>".$res[set_nome]."</b>";
    }
    
        
    
    $Tit = "Total de medicamentos dispensados por centro estocador";
    
    include_once $_SESSION[root].$_SESSION[modulo]."relatorio/cabecalho.php";
    
    echo "<style type=\"text/css\">
        tr{
            font-size   :12px;
        }
        </style>"; 
    
     
    
if (($estocador == -1) and ($pro_cod == -1)){ // nem medicamento nem centro estocador selecionado      
      $sql = "select sum(ite_quantidade) as qtde,setor,codsetor,pro_nome,pro_codigo from v_movimentacao as vm
                            left join setor as ss on ss.set_codigo = vm.codsetor      
                            where mov_data between '$dt1' and '$dt2'
                            and (tipomovim = 'D' or (tipomovim = 'S' and mov_tipo ='S'))
                            and sinal = '-'
                            group by setor,pro_nome,pro_codigo,codsetor
                            order by setor,pro_nome";                    
        echo "<table>
            <tr>
                <td width='150'><b>Medicamento</b></td>
                <td width='80'><b>Data</b></td>
                <td width='20'>&nbsp;</td>
                <td width='40'><b>Qtde</b></td>";
        $exec_sql = pg_query($sql);
        $teste = 0;
        $cont=0;
        while ($reg2 = pg_fetch_array($exec_sql))
        {
            $qr = "select pro_codigo,setor,pro_nome,mov_data,to_char(mov_data, 'dd/mm/yyyy') as data,sum(ite_quantidade) as qtde,codsetor from v_movimentacao as vm
                        left join setor as ss on ss.set_codigo = vm.codsetor
                        where ss.set_estoque='S'
                        and codsetor =  $reg2[codsetor]
                        and pro_codigo = $reg2[pro_codigo]
                        and (tipomovim = 'D' or (tipomovim = 'S' and mov_tipo ='S'))
                        and sinal = '-'
                        and mov_data between '$dt1' and '$dt2'
                        group by setor,pro_nome,pro_codigo,codsetor,mov_data
                        order by setor,pro_nome";
                        
            $exec2 = pg_query($qr);            
            while($reg = pg_fetch_array($exec2))
            {
                echo "<tr><td>".$reg[pro_nome]."</td><td>".$reg[data]."</td><td>&nbsp;</td><td>".number_format($reg[qtde],0,'.','.')."</td><td>".$reg[setor]."</tr>";
                $tot_medic+=$reg[qtde];                    
            }

            echo "<tr><td colspan='5'>Total do produto no centro estocador = ".number_format($reg2[qtde],0,'.','.')."</td></tr>";
            echo "<tr><td colspan = '5'><hr></td></tr>";                                            

        }
        echo "<tr><td>Total geral = ".$tot_medic."</td></tr>";
        echo "<tr><td colspan = '5'><hr></td></tr>";                            
echo "</table>";

}else{
    
    if (($estocador == -1) and ($pro_cod != -1))  // apenas produto selecionado e data inicial e final preenchidos
    {
        
            $sql_ext="select pro_codigo,setor,pro_nome,sum(ite_quantidade) as qtde,codsetor from v_movimentacao as vm
                            left join setor as ss on ss.set_codigo = vm.codsetor
                            where ss.set_estoque='S'
                            and pro_codigo = $pro_cod
                            and (tipomovim = 'D' or (tipomovim = 'S' and mov_tipo ='S'))
                            and sinal = '-'
                            and mov_data between '$dt1' and '$dt2'
                            group by pro_codigo,setor,pro_nome,codsetor
                            order by pro_nome";
        
        
                echo "<table>
                    <tr>
                        <td width='150'><b>Medicamento</b></td>
                        <td width='80'><b>Data</b></td>
                        <td width='20'>&nbsp;</td>
                        <td width='40'><b>Qtde</b></td>";        
    $d = pg_query($sql_ext);
    while ($r=pg_fetch_array($d))
    {        
        $sql = "select pro_codigo,setor,pro_nome,mov_data,to_char(mov_data, 'dd/mm/yyyy') as data,sum(ite_quantidade) as qtde,codsetor from v_movimentacao as vm
                        left join setor as ss on ss.set_codigo = vm.codsetor
                        where ss.set_estoque='S'
                        and pro_codigo = $r[pro_codigo]
                        and codsetor = $r[codsetor]
                        and (tipomovim = 'D' or (tipomovim = 'S' and mov_tipo ='S'))
                        and sinal = '-'
                        and mov_data between '$dt1' and '$dt2'
                        group by pro_codigo,setor,pro_nome,mov_data,codsetor
                        order by pro_nome";

        $exec_sql = pg_query($sql);            
        while ($reg = pg_fetch_array($exec_sql)){
            echo "<tr><td>".$reg[pro_nome]."</td><td>".$reg[data]."</td><td>&nbsp;</td><td>".number_format($reg[qtde],0,'.','.')."</td><td>".$reg[setor]."</tr>";
            $tot_medic+=$reg[qtde];
        }
        echo "<tr><td colspan='5'>Total do medicamento no centro estocador = ".number_format($r[qtde],0,'.','.')."</td></tr>";
        echo "<tr><td colspan = '5'><hr></td></tr>";                                    
    }
    echo "<tr><td>Total geral = ".number_format($tot_medic,0,'.','.')."</td></tr>";    
        echo "</table>";    
}else{       
        if (($estocador != -1) and ($pro_cod == -1)){ // apenas centro estcador e datas preenchidas
            $tot_medic=0;
            $sql = "select sum(ite_quantidade) as qtde,setor,codsetor,pro_nome,pro_codigo from v_movimentacao as vm
                            left join setor as ss on ss.set_codigo = vm.codsetor                            
                            where ss.set_estoque='S'
                            and codsetor = $estocador
			    and mov_data between '$dt1' and '$dt2'
                            and (tipomovim = 'D' or (tipomovim = 'S' and mov_tipo ='S'))
                            and sinal = '-'
                            group by setor,pro_nome,pro_codigo,codsetor
                            order by setor,pro_nome";

                echo "<table>
                    <tr>
                        <td width='150'><b>Medicamento</b></td>
                        <td width='80'><b>Data</b></td>
                        <td width='20'>&nbsp;</td>
                        <td width='40'><b>Qtde</b></td>";
            $s=pg_query($sql);
            while ($r = pg_fetch_array($s)){
                    $sql = "select pro_codigo,setor,pro_nome,mov_data,to_char(mov_data, 'dd/mm/yyyy') as data,codsetor, sum(ite_quantidade) as qtd from v_movimentacao as vm
                                    left join setor as ss on ss.set_codigo = vm.codsetor
                                    where ss.set_estoque='S'
                                    and codsetor = $estocador
                                    and pro_codigo = $r[pro_codigo]
                                    and (tipomovim = 'D' or (tipomovim = 'S' and mov_tipo ='S'))                            
                                    and sinal = '-'
                                    and mov_data between '$dt1' and '$dt2'
                                    group by pro_codigo,setor,pro_nome,mov_data,codsetor
                                    order by pro_nome";
                    $exec_sql = pg_query($sql);            
                    while ($reg = pg_fetch_array($exec_sql)){            
                        echo "<tr><td>".$reg[pro_nome]."</td><td>".$reg[data]."</td><td>&nbsp;</td><td>".number_format($reg[qtd],0,'.','.')."</td><td>".$reg[setor]."</tr>";
                        $tot_medic+=$reg[qtd];
                    }
                    echo "<tr><td colspan='5'>Total do medicamento no centro estocador = ".number_format($r[qtde],0,'.','.')."</td></tr>";
                    echo "<tr><td colspan = '5'><hr></td></tr>";                    
            }
        echo "<tr><td>Total geral = ".number_format($tot_medic,0,'.','.')."</td></tr>";
echo "</table>";
        }else{
                if(($estocador != -1) and ($pro_codigo != - 1)) // produto e centro estocador sao selecionados
                {
                    $tot_medic=0;
                    echo "<table>
                        <tr>
                            <td width='150'><b>Medicamento</b></td>
                            <td width='80'><b>Data</b></td>
                            <td width='20'>&nbsp;</td>
                            <td width='40'><b>Qtde</b></td>";
                    $sql = "select pro_codigo,setor,pro_nome,mov_data, to_char(mov_data, 'dd/mm/yyyy') as data, codsetor, sum(ite_quantidade) as qtd from v_movimentacao as vm
                                left join setor as ss on ss.set_codigo = vm.codsetor
                                where ss.set_estoque='S'
                                and codsetor = $estocador
                                and pro_codigo = $pro_cod
                                and (tipomovim = 'D' or (tipomovim = 'S' and mov_tipo ='S'))
                                and sinal = '-'
                                and mov_data between '$dt1' and '$dt2'
                                group by pro_codigo,setor,pro_nome,mov_data,codsetor
                                order by pro_nome";
                        $exec_sql = pg_query($sql);            
                        while ($reg = pg_fetch_array($exec_sql)){            
                            echo "<tr><td>".$reg[pro_nome]."</td><td>".$reg[data]."</td><td>&nbsp;</td><td>".number_format($reg[qtd],0,'.','.')."</td><td>".$reg[setor]."</tr>";
                            $tot_medic+=$reg[qtd];
                        }
                        echo "<tr><td colspan='5'>Total do medicamento no centro estocador = ".number_format($tot_medic,0,'.','.')."</td></tr>";
                        echo "<tr><td colspan = '5'><hr></td></tr>";                    
//                    }
                    echo "<tr><td>Total geral = ".number_format($tot_medic,0,'.','.')."</td></tr>";
                    echo "</table>";
                }
            }
        
    }
}
        
        
?>
        
            
            
            
    



        
