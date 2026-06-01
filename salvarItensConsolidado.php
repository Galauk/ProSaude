<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

$totnota = "select b.mov_entrada, sum(a.ite_vlrtotal) as total
            from itens_movimento a, movimento b
            where a.mov_codigo = b.mov_codigo
            and a.mov_codigo = {$_GET[mov_codigo]}
            group by 1";

$texec = pg_query($totnota);
$fetch = pg_fetch_array($texec);

$e = pg_query("begin;");

if ($fetch[mov_entrada] != 'E')
{
    $up = "update movimento set mov_total_nota = $fetch[total] where mov_codigo = {$_GET[mov_codigo]};";
    $exec = pg_query($up);
}

$update = "update itens_movimento set ite_consolidado = 'S' where mov_codigo = {$_GET[mov_codigo]};";
    
$exec_update = pg_query($update) or die( pg_last_error() );

pg_query("commit;");

//echo "<pre>$totnota<br>$up<br>$update</pre>";

if( pg_affected_rows($exec_update) )
{
    echo "true";
}
else
{
    echo "false";
}

