<?php
include "../mysqlconecta_dbbit.php";
include "../mysqlexecuta_pro.php";
include "../config.php";

error_reporting(E_ALL);
ini_set('display_errors','On');

$co_acao = $_POST['co_acao'];

//formata data fim
$form_resposta  = $_POST["dt_resposta"];
$arr_dt_resposta   = explode("/",$form_resposta);
$banco_dt_fim = $arr_dt_resposta[2]."-".$arr_dt_resposta[1]."-".$arr_dt_resposta[0];

$email_analista   = $_POST['email_analista'];
$descricao        = nl2br(addslashes($_POST['tx_descricao']));

//controle
$control = $_POST["control"];

$sql = "INSERT INTO pro_v2_resposta_acao
            (co_acao,
             desc_resposta,
             dt_resposta
            )
             VALUES
             ('$co_acao',
              '$descricao',
             '$banco_dt_fim')";

             $res = mysqlexecuta($conexao,$sql);

//FECHAR CONEXÃO COM O BANCO
mysql_close($conexao);
header("Location:gravou_ok_resposta.php?ctrl=" . $control);
?>
