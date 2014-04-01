<?php
include "../mysqlconecta_dbbit.php";
include "../mysqlexecuta_pro.php";
include "../config.php";

error_reporting(E_ALL);
ini_set('display_errors','On');

$co_resposta = $_GET[resp];
$co_acao = $_GET[coa];


$sql = "DELETE FROM pro_v2_resposta_acao
        WHERE co_resposta=" . $co_resposta . " and co_acao=" . $co_acao;

$res = mysqlexecuta($conexao,$sql);

//FECHAR CONEXÃO COM O BANCO
mysql_close($conexao);

echo "OK";

?>
