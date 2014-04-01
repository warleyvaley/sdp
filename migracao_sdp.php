<?php
error_reporting(E_ALL);
ini_set('display_errors','On');

include "../mysqlconecta_dbbit.php";
include "../mysqlexecuta_pro.php";
include "../config.php";


/*
$sql= "select  *
       from pro_problema";


$resultado = mysqlexecuta($conexao,$sql);
$insert = array();
$i = 0;

WHILE ($row = mysql_fetch_array($resultado)) {
	
  $insert[$i] = "INSERT INTO pro_v2_problema (co_problema,an_problema,dt_cadastro_problema,dt_fim_problema,dt_modificacao_problema,desc_problema,co_impacto,chr_causa_raiz,dt_causa_raiz,desc_causa_raiz,co_servico_afetado,co_supervisor,desc_observacao_problema)
  				 VALUES (" . $row['pro_co_problema'] . "," . $row['pro_an_problema'] . ",'" . $row['pro_dt_problema'] . "','" . $row['pro_dt_fim_problema'] . "','" . $row['pro_dt_problema'] . "','" .  str_replace("'","\"",$row['pro_tx_descricao']) . "'," . $row['pro_co_impacto'] . ",'" . $row['pro_in_causa_raiz'] . "','" . $row['pro_dt_causa_raiz'] . "','" . str_replace("'","\"",$row['pro_tx_causa_raiz']) . "'," . $row['pro_co_servico'] . "," . $row['pro_co_dono'] . ",'" . str_replace("'","\"",$row['pro_tx_observacoes']) . "')";
  $i++;
 	
	
}

$qtd = count($insert);

for ($i = 0; $i < $qtd ; $i++) {
	echo $insert[$i];
	echo "<br>";
	 $res = mysqlexecuta($conexao,$insert[$i]);
	 
}

mysql_free_result($resultado);
mysql_free_result($res);
*/

///////////// 
//
// INSERE SITUAÇÃO PROBLEMA
//
///////////////
/*
$sql= "select  *
       from pro_problema_possui_situacao";


$resultado = mysqlexecuta($conexao,$sql);
$insert = array();
$i = 0;

WHILE ($row = mysql_fetch_array($resultado)) {
	
  $insert[$i] = "INSERT INTO pro_v2_problema_situacao (co_problema,an_problema,co_tipo_situacao_problema,desc_problema_situacao,dt_cadastro,dt_situacao,ativo)
  				 VALUES (" . $row['pps_co_problema'] . "," . $row['pps_an_problema'] . "," . $row['pps_co_situacao'] . ",'" . str_replace("'","\"",$row['pps_tx_observacao']) . "','" . $row['pps_dt_situacao'] . " 00:00:00','" . $row['pps_dt_situacao'] . "',0)";
  $i++;
 	
	
}

$qtd = count($insert);

for ($i = 0; $i < $qtd ; $i++) {
	echo $insert[$i];
	echo "<br>";
	$res = mysqlexecuta($conexao,$insert[$i]);
	 
}
*/


//////////////
//
// ALTERA STATUS DA SITUAÇÃO DO PROBLEMA
//
///////////////////

$sql= "select  *
       from pro_v2_problema";


$resultado = mysqlexecuta($conexao,$sql);
$insert = array();
$i = 0;

WHILE ($row = mysql_fetch_array($resultado)) {
	
	$sql = "update pro_v2_problema_situacao
	
			inner join (SELECT  MAX(dt_cadastro) as dt  FROM pro_v2_problema_situacao WHERE co_problema = " . $row['co_problema'] . " AND an_problema = " . $row['an_problema'] . ") r

			SET ativo = 1

			where co_problema = " . $row['co_problema'] . " AND an_problema = " . $row['an_problema'] . " and dt_cadastro = r.dt";
	
	
	//echo $sql;
	///echo "<br>";
	$res = mysqlexecuta($conexao,$sql);
}



/////////////////////////
//
// ATUALIZA CATEGORIA DO PROBLEMA
//
///////////////////////////

/*$sql= "select  pro_co_problema,pro_an_problema,pro_in_categoria
       from pro_problema";


$resultado = mysqlexecuta($conexao,$sql);
$insert = array();
$i = 0;

WHILE ($row = mysql_fetch_array($resultado)) {
	
	$categoria = 1;

	if ($row['pro_in_categoria'] == 'Aplicação') {
		$categoria = 4;
	} else if ($row['pro_in_categoria'] == 'Servidor') {
		$categoria = 12;
	} else if ($row['pro_in_categoria'] == 'Banco de Dados') {
		$categoria = 14;
	} else if ($row['pro_in_categoria'] == 'Rede') {
		$categoria = 15;		
	} 
		
	
	$sql = "update pro_v2_problema
				
			SET co_categoria = " . $categoria . "

			where co_problema = " . $row['pro_co_problema'] . " AND an_problema = " . $row['pro_an_problema'] ;
	
	
	//echo $sql;
	//echo "<br>";
	$res = mysqlexecuta($conexao,$sql);
}
*/

echo "aqui";






?>