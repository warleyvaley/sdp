<?php
include "../mysqlconecta_dbbit.php";
include "../mysqlexecuta_pro.php";
include "../config.php";

error_reporting(E_ALL);
ini_set('display_errors','On');

$co_acao = $_POST['co_acao'];
$nu_pro  = $_POST['nu_pro'];
$ano_pro = $_POST['ano_pro'];

//formata data fim
$form_resposta  = $_POST["dt_resposta"];
$arr_dt_resposta   = explode("/",$form_resposta);
$banco_dt_fim = $arr_dt_resposta[2]."-".$arr_dt_resposta[1]."-".$arr_dt_resposta[0];

$email_analista   = $_POST['email_analista'];
$descricao        = nl2br(addslashes($_POST['tx_descricao']));
$co_resposta      = $_POST['co_resposta'];

$sql = "UPDATE pro_v2_resposta_acao  SET
            desc_resposta = '$descricao',
            dt_resposta = '$banco_dt_fim'
         WHERE
            co_resposta = $co_resposta";

         $res = mysqlexecuta($conexao,$sql);

//FECHAR CONEXÃO COM O BANCO
mysql_close($conexao);
header("Location:gravou_ok_resposta.php?ctrl=2&anp=" . $ano_pro . "&cop=" . $nu_pro . "&noa=" . $co_acao);
?>
