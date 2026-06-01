<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
        
        echo "<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css' integrity='sha256-916EbMg70RQy9LHiGkXzG8hSg9EdNy97GazNG/aiY1w=' crossorigin='anonymous' />";
        
        $Tit = "Ficha Cadastral de Usuario";
    
        include_once $_SESSION[root].$_SESSION[modulo]."relatorio/cabecalho.php";
        
        echo "<style> 
        @media print{
                body{zoom:70%}
                input[name='imprimir'] { display:none}
        }
        
        </style>"; 
    $usu = intval($_GET[usu_codigo]);
    
    echo "<table border='1' class='table'> <CAPTION>INFORMACOES GERAIS DO USUARIO</CAPTION>  " ;
        //usu
        $sql_ce = "SELECT * FROM usuario 
                where usu_codigo = $usu";
        $query_ce = db_query($sql_ce);
        $row_ce = pg_fetch_array($query_ce);
        //dom
        $domicilioint = $row_ce[dom_codigo];
        $sql_dom = "SELECT * FROM domicilio 
                where dom_codigo = $domicilioint";
        $query_dom = db_query($sql_dom);
        $row_dom = pg_fetch_array($query_dom);

        $domnr = $row_dom[dom_numero];
        $domcomp = $row_dom[dom_complemento];
        $domreferencia = $row_dom[dom_ponto_referencia];

        $domrua = $row_dom[rua_codigo];
        $dombai = $row_dom[bai_codigo];
        //rua
        $sql_rua = "SELECT * FROM rua 
                where rua_codigo = $domrua";
        $query_rua = db_query($sql_rua);
        $row_rua = pg_fetch_array($query_rua);

        $rua = $row_rua[rua_nome];
        $cep = $row_rua[rua_cep];
        //bairro
        $sql_bai = "SELECT * FROM bairro 
                where bai_codigo = $dombai";
        $query_bai = db_query($sql_bai);
        $row_bai = pg_fetch_array($query_bai);
        $bairro = $row_bai[bai_nome];
        $cidcod = $row_bai[cid_codigo];

        //cidade
        $sql_cid = "SELECT * FROM cidade 
                where cid_codigo = $cidcod";
        $query_cid = db_query($sql_cid);
        $row_cid = pg_fetch_array($query_cid);
        $cidade = $row_cid[cid_nome];
        $uf = $row_cid[uf_sigla];

        //raca
        $usuraca = $row_ce[rac_codigo];
        $sql_ra = "SELECT rac_descricao FROM raca 
                where rac_codigo = '$usuraca'";
        $query_ra = db_query($sql_ra);
        $row_ra = pg_fetch_array($query_ra);
        $raca = $row_ra[rac_descricao];
        //ocupacao
        $especia = $row_ce[usu_cbo_r];
        $sql_es = "SELECT esp_nome FROM especialidade 
                where cod_cbo = '$especia'";
        $query_es = db_query($sql_es);
        $row_es = pg_fetch_array($query_es);
        $ocupacao = $row_es[esp_nome];
        //escolaridade
        $escolar = $row_ce[ecd_codigo];
        $sql_ecd = "SELECT ecd_descricao FROM escolaridade 
                where ecd_codigo = '$escolar'";
        $query_ecd = db_query($sql_ecd);
        $row_ecd = pg_fetch_array($query_ecd);
        $escolaridade = $row_ecd[ecd_descricao];



        //dom codigo bairro cidade rua numero
        if($row_ce[usu_sexo] == M || $row_ce[usu_sexo] == 0) $sexo = 'MASCULINO';
        else $sexo = 'FEMININO';
        
        if($row_ce[cd_nacionalidade] == 'B') $nacionalidade = 'BRASILEIRO';




        echo "<tr>
        <td><b>Usuario:</b> $row_ce[usu_codigo] </td>
        <td><b>Nome:</b> $row_ce[usu_nome] </td>
        <td><b>Sexo:</b> $sexo </td>
        </tr>
        <tr>
        <td><b>CNS:</b> $row_ce[usu_cartao_sus] </td>
        <td><b>Nascimento:</b> $row_ce[usu_datanasc] </td>
        <td><b>Nome do Pai:</b> $row_ce[usu_pai] </td>
        </tr>
        <tr>
        <td><b>Nome da Mae:</b> $row_ce[usu_mae] </td>
        </tr>
        <tr>
        <td><b>Nacionalidade:</b> $nacionalidade </td>
        <td><b>Chegada:</b> $row_ce[dt_naturalizacao] </td>
        <td><b>Naturalizacao:</b> $row_ce[dt_naturalizacao] </td>
        <td><b>Portaria:</b> $row_ce[nr_portaria_naturalizacao] </td>
        </tr>
        <tr>
        <td><b>Municipio de Nascimento:</b> $row_ce[usu_cidade_nasc] </td>
        <td><b>UF:</b> $row_ce[uf_codigo_pac] </td>
        <td><b>CEP:</b>  </td>
        </tr>
        <tr>
        <td><b>Situacao Familiar:</b> $row_ce[usu_sit_familiar] </td>
        <td><b>Raca:</b> $raca </td>
        </tr>
        <tr>
        <td><b>Grau de Escolaridade:</b> $escolaridade </td>
        <td><b>CBO - Ocupacao:</b> $ocupacao </td>

        </tr>";

   echo "</table> ";

   echo "<table border='1' class='table'> ";
    echo "<CAPTION>DOCUMENTOS</CAPTION>";
    echo "
    <tr>
    <td><b>CPF:</b> $row_ce[usu_cpf] </td>
    <td><b>PIS/PASEP:</b> $row_ce[usu_pis_pasep]</td>
    </tr>
    <tr>
    <td><b>Registro Geral:</b> $row_ce[usu_rg] </td>
    <td><b>Emissao:</b> $row_ce[usu_rg_dt_emissao] </td>
    <td><b>Orgao Emissor:</b> $row_ce[usu_rg_emissor] </td>
    </tr>
    ";


   echo "</table>";

   echo "<table border='1' class='table'>";
    echo "<CAPTION>ENDERECO</CAPTION>";
    echo "
    <tr>
    <td><b>Municipio de Endereco:</b> $cidade </td>
    <td><b>UF:</b> $uf </td>
    <td><b>CEP:</b> $cep </td>
    </tr>
    <tr>
    <td><b>Logradouro:</b> $rua </td>
    <td><b>Numero:</b> $domnr </td>
    <td><b>Complemento:</b> $domcomp </td>
    <td><b>Bairro:</b> $bairro </td>
    </tr>
    <tr>
    <td><b>Ponto de Referencia:</b> $domreferencia </td>
    <td><b>Telefone:</b> $row_ce[usu_fone] </td>
    <td><b>Celular:</b> $row_ce[usu_celular] </td>
    </tr>
    <tr>
    <td><b>Data de Inclusao:</b> $row_ce[dt_inclusao] </td>
    <td><b>Data de Alteracao:</b> $row_ce[dt_alteracao] </td>
    <td><b>Situacao:</b> $row_ce[usu_ativacao] </td>
    <td><b>Estado Civil:</b> $row_ce[usu_estado_civil] </td>
    </tr>
    <tr>
    <td><b>Observacoes:</b> $row_ce[usu_observacao] </td>
    </tr>
    ";
   echo "</table>";


?>