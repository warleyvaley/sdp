<?php
require_once "../../mysqlconecta_dbbit.php";
require_once "../../mysqlexecuta_pro.php";

// padra de retorno de data 
DEFINE("MYSQL_DEFAULT", 1);
DEFINE("SQLSERVER_DEFAULT", 2);
DEFINE("ORACLE_DEFAULT", 3);
/*********************************/


// qtd de meses anteriores que
// o relatorio devera retornar	
DEFINE("QTD_BASE", 5);


////////////////////////////
// Diferença de dias entre duas datas
//
////////////////////////////
function qtdDias($dtInicial,$dtFinal) {
	
	 list($dia,$mes,$ano) = explode("/", $dtInicial);
	 $date1 = date("d-m-Y", mktime(0, 0, 0, $mes, $dia, $ano));
	 list($dia,$mes,$ano) = explode("/", $dtFinal);
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
include "../../mysqlconecta_dbbit.php";
include_once "../../mysqlexecuta_pro.php";

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


//recupera o Nome do Mes de acordo com o parâmetro
function getMesById($id) {
         $listaMes = array('Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez');
         list($mes,$ano) = explode("-", $id);
         return $listaMes[$id-1] . "-" . $ano;
}

//calcula o prazo de fechamento do problema
function calculaPrazoFechamentoProblema($dtCadastro, $dtFechamento) {
	
	list($ano,$mes,$dia) = explode ("-", $dtCadastro);
	$date1 = date("d-m-Y", mktime(0, 0, 0, $mes, $dia, $ano));
	list($ano,$mes,$dia) = explode ("-", $dtFechamento);
	$date2 = date("d-m-Y", mktime(0, 0, 0, $mes, $dia, $ano));
	

	$diff = abs(strtotime($date2) - strtotime($date1));
	//$years = floor($diff / (365*60*60*24));
	//$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
	$days   = floor($diff/(60*60*24));
	
	//echo "dt cad ". $dtCadastro . "<br>";
	//ECHO "dt fec " . $dtFechamento . "<br>";
   //ECHO "dIAS " . $days . "<br>";
	
	if ($days > 30) {
		//fora do prazo
		return false;
	} else {
		// no prazo
		return true;
	}
	
}
     
function getMes() {
         $listaMes = array('Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez');
         
         $pos = 1;
         foreach ($listaMes as $mes) {
             echo "<option value='" . $pos . "' >" . $mes . "</option>";
             $pos++;
         }
}


///////////////////////////////////////////////////////
// Método utilizado para criar o período do relatório de
// análise de tendência.
//
// O mês inicial será o mês escolhido menos 4 meses
//
//
// @mesFinal - mês de consulta final no relatorio
// @anoFinal - ano de consulta final no relatorio
// @padrao   - padrao de retorno da data (SQL Server, MySQL, Oracle e etc)
//
// #dataInicial - retorna data inicial de consulta no relatorio com o
//				   padrao "d-m-Y" 
////////////////////////////////////////////////////////
function retornaDataInicialConsulta($mesFinal,$anoFinal,$padrao) {
     	
     	if ($padrao == MYSQL_DEFAULT || $padrao == ORACLE_DEFAULT) {
     		$dataInicial = date("d-m-Y", mktime(0, 0, 0, ($mesFinal - QTD_BASE), 1, $anoFinal));
     	} else if ($padrao == SQLSERVER_DEFAULT) {
     		$dataInicial = date("Y-m-d", mktime(0, 0, 0, ($mesFinal - QTD_BASE), 1, $anoFinal));
     	}     	
     	
		return $dataInicial;     	
}

///////////////////////////////////////////////////////
// Método utilizado para criar o período do relatório de
// análise de tendência.
//
// O mês final será o mês escolhido 
//
//
// @mesFinal - mês de consulta final no relatorio
// @anoFinal - ano de consulta final no relatorio
// @padrao   - padrao de retorno da data (SQL Server, MySQL, Oracle e etc)
//
// #dataFinal - retorna data final de consulta no relatorio com o
//				 padrao "t-m-Y", onde 't' e o ultimo dia do mes 
////////////////////////////////////////////////////////
function retornaDataFinalConsulta($mesFinal,$anoFinal,$padrao) {
     	
	 	if ($padrao == MYSQL_DEFAULT || $padrao == ORACLE_DEFAULT) {
     		$dataFinal = date("t-m-Y", strtotime("$mesFinal/1/$anoFinal"));
	 	} else if ($padrao == SQLSERVER_DEFAULT) {
	 		$dataFinal = date("Y-m-t", strtotime("$mesFinal/1/$anoFinal"));
	 	}     	
	 	
		return $dataFinal;
}



////////////////////////////////////////////////////////////
//Retorna o intervalo de meses entre um período
//
///////////////////////////////////////////////////////////
function retornaIntervalo($mesBase,$anoBase) {
        
     	$nomesMeses = array();
     	list($mesBase,$anoBase) = explode("-", ucfirst(gmstrftime("%m-%Y", mktime(0,0,0,($mesBase-QTD_BASE),1,$anoBase))));
     	 
     	for ($i=0; $i < 6; $i++) {
     	  $meses[$i] = ucfirst(gmstrftime("%m", mktime(0,0,0,$mesBase+$i,1,$anoBase)));     	  
     	}
     	
     	return $meses;
}


////////////////////////////////////////////////////////////////
// Recupera o ano de acordo com o mes
//
///////////////////////////////////////////////////////////////
function retornaAnoConsulta($mesFinal,$anoFinal) {
  $dataInicial = date("Y", mktime(0, 0, 0, ($mesFinal - QTD_BASE), 1, $anoFinal));
  return $dataInicial;
}    
?>