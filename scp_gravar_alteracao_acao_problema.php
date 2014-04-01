<?php
include "../mysqlconecta_dbbit.php";
include "../mysqlexecuta_pro.php";
include "../config.php";

error_reporting(E_ALL);
ini_set('display_errors','On');

$co_problema = $_POST[co_problema];
$an_problema = $_POST[an_problema];
$co_acao = $_POST[co_acao];

$email_analista   = $_POST[email_analista];

$descricao        = nl2br(addslashes($_POST[tx_descricao]));
$id_situacao      = $_POST["co_situacao"];
$id_solucao       = $_POST["co_solucao"];
$no_responsavel   = $_POST["no_responsavel"];
$no_orgao         = $_POST["no_orgao"];

if ($_POST["chr_acao_conclusiva"] == ""){
   $chr_conclusiva       = "Não";
} else {
   $chr_conclusiva       = "Sim";
}
   

//formata data fim
$form_dt_fim  = $_POST["dt_fim_acao"];
$arr_dt_fim   = explode("/",$form_dt_fim);
$banco_dt_fim = $arr_dt_fim[2]."-".$arr_dt_fim[1]."-".$arr_dt_fim[0];

// formata data estimada
$form_dt_estimada  = $_POST["dt_estimada_acao"];
$arr_dt_estimada   = explode("/",$form_dt_estimada);
$banco_dt_estimada = $arr_dt_estimada[2]."-".$arr_dt_estimada[1]."-".$arr_dt_estimada[0];


/*echo "co problema " . $co_problema;
echo "an problema " .  $an_problema;
echo "data_cad_acao  " . $data_cad_acao;
echo  "descricao" . $descricao;
echo "id_situacao ". $id_situacao;
echo "id_solucao " . $id_solucao;
echo  "no_responsavel " . $no_responsavel;
echo "no_orgao " . $no_orgao;
echo "chr_conclusiva " . $chr_conclusiva;
echo "banco_dt_fim " . $banco_dt_fim;
echo "form_dt_estimada " . $banco_dt_estimada;*/



$sql = "UPDATE pro_v2_acao SET
             desc_acao   =  '$descricao',
             co_situacao =  '$id_situacao',
             co_solucao  =  '$id_solucao',
             no_responsavel =  '$no_responsavel',
             no_orgao_responsavel = '$no_orgao',
             dt_fim_acao = '$banco_dt_fim',
             dt_estimado_acao = '$banco_dt_estimada',
             chr_conclusiva = '$chr_conclusiva'
          WHERE
             co_acao = $co_acao";
             
  //echo $sql;
  //exit;

             $res = mysqlexecuta($conexao,$sql);

//FECHAR CONEXÃO COM O BANCO
mysql_close($conexao);
header("Location:gravou_ok_acao.php?cop=" . $co_problema . "&anp=" . $an_problema);
?>
