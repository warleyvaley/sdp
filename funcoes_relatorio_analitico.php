<?php
require_once "../mysqlconecta_dbbit.php";
require_once "../mysqlexecuta_pro.php";

////////////////////////////
// Diferença de dias entre duas datas
//
////////////////////////////
function qtdDias($dtInicial,$dtFinal) {
	
	 list($dia,$mes,$ano) = split ("/", $dtInicial);
	 $date1 = date("d-m-Y", mktime(0, 0, 0, $mes, $dia, $ano));
	 list($dia,$mes,$ano) = split ("/", $dtFinal);
	 $date2 = date("d-m-Y", mktime(0, 0, 0, $mes, $dia, $ano));
	 	 
	 $diff = abs(strtotime($date2) - strtotime($date1));

	 //$years  = floor($diff / (365*60*60*24));
	 //$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
	 $days   = floor($diff/(60*60*24));
	
	 return $days;    	
}


///////////////////////////
//
//  Verifica se o problema 
//  está no prazo máximo
//  de 30 dias
//
//
//  Regra:
//      Somente serão utilizados para fim de cálculo os
//		os períodos em que o status foi diferente de 
// 		suspenso (codigo 6).
//
//////////////////////////
function analisaPrazo($coProblema,$anProblema) {
include "../mysqlconecta_dbbit.php";
include_once "../mysqlexecuta_pro.php";

$sql = "select 
		 	date_format(dt_situacao,'%d/%m/%Y') as dt_situacao
		from 
			pro_v2_problema_situacao
		where co_problema = $coProblema and an_problema = $anProblema and co_tipo_situacao_problema <> 6";

$res = mysqlexecuta($conexao,$sql);
$datas = mysql_fetch_array($res);
$qtdDt = mysql_num_rows($res);

$arrDts = array();
$somaDias = 0;
for ($i = 0; $i < $qtdDt; $i++) {
	
	$arrDts[$i] =  mysql_result($res, $i, "dt_situacao");	
}

$arrDts[$qtdDt] = date('d') . "/" . date('m') . "/" . date('Y');
$qtdDt++;

for ($i = 0; $i < $qtdDt; $i++) {
	
	if (($i + 1) == $qtdDt) { 
		break;
	} 
	$days = qtdDias($arrDts[$i], $arrDts[$i+1]);
	$somaDias = $somaDias + $days;
}

if ($somaDias <= 30) {
	return "NO PRAZO";
} else {
	return "FORA DO PRAZO";
}
	
}


?>