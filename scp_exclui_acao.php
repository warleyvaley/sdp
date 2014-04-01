<?php
include "../mysqlconecta_dbbit.php";
include "../mysqlexecuta_pro.php";
include "../config.php";

error_reporting(E_ALL);
ini_set('display_errors','On');

$co_problema = $_GET[cop];
$an_problema = $_GET[anp];
$co_acao = $_GET[coa];


$sql = "DELETE FROM pro_v2_acao
        WHERE co_problema=" . $co_problema . " and an_problema=" . $an_problema . " and co_acao=" . $co_acao;

//echo $sql;exit;
$res = mysqlexecuta($conexao,$sql);

//FECHAR CONEXÃO COM O BANCO
mysql_close($conexao);

echo "OK";

?>
