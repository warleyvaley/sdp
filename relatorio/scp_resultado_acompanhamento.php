<!DOCTYPE html>
<HTML>
<HEAD>
<!-- 
////////////////////////////////////////////////
// 
//  Relatório de Acompanhamento do Processo de Gerir Problemas 
//
//  Gera gráficos com os Indicadores do processo
//
//  Desenvolvido por GEOP/CESEP
//	20 de Novembro de 2013
//
////////////////////////////////////////////////
-->
<script src='../js/jquery-1.10.2.min.js' type="text/javascript"></script>
<script src='../js/tinymce/tinymce.min.js' type="text/javascript"></script>
<script>
        tinymce.init({
            	selector:'textarea',
            	width:803,
            	height:200,
            	statusbar : false, 
            	menubar: "tools table format view insert edit media",
            	plugins: "table media charmap image link",
            	tools: "inserttable"});
</script>	

<link rel="stylesheet" href="../skin.css">  
<style type="text/css"> 
    @import url("../menu/skin-xp-extended.css");
    		
    body{
  		padding:3em 0 0 0;
  		background:url(foo) fixed;
  	}
    .menuTop {
 		position:fixed;
  		_position:absolute;
  		top:0px;
  		_top:expression(eval(document.body.scrollTop));
  		left:0px;
  		margin:0;
  		padding:0;
  		background:transparent;
  		width: 100%;
  		text-align:left;
  	}
</style>
    
<script type="text/javascript">
      // WARNING: the following should be a path relative to site, like "/hmenu/"
      // here it is set relative to the current page only, which is not recommended
      // for production usage; it's useful in this case though to make the demo work
      // correctly on local systems.
      _dynarch_menu_url = "../menu/";
</script>
<script type="text/javascript" src="../js/menuControl.js"></script>	
<script type="text/javascript" src="../menu/hmenu.js"></script>	

<script src="../js/relatorio.js" type="text/javascript"></script>  

</HEAD>

<BODY onload="DynarchMenu.setup('menu', { electric: 250 });" style='background:#F0F0F0'>
<?php include_once '../menuDrop.php';  ?>

<h2 align="center">Relatório de Acompanhamento do Processo Gerir Problemas</h2> 
<br />
<br />

<form id='info' method='post' action='geraArquivo.php'>


     
<?php 
require_once "../../mysqlconecta_dbbit.php";
require_once "../../mysqlexecuta_pro.php";
require_once "funcoes_relatorio_analitico.php";
require_once 'GeradorGrafico.class.php';
require_once "../utils/Utils.class.php";

// limpa as imagens antigas
Utils::limpaDiretorio("../images/tmp/");

$grafico = array("","","","","","");

///////////PERÍODO DA CONSULTA
//recupera os ultimos 6 meses
//$anoAtual = date('Y');
//$mesAtual = date('m') + 1;

$anoAtual = $_POST['ano'];
$mesAtual = $_POST['mes'];
$coEquipe = $_POST['cod_equipe'];

$dataInicial = retornaDataInicialConsulta($mesAtual, $anoAtual, 2);
$dataFinal   = retornaDataFinalConsulta($mesAtual, $anoAtual, 2);
$intervalo   = retornaIntervalo($mesAtual, $anoAtual);

/// FECHAMENTO DE PROBLEMAS NO ANO (Ultimos 6 meses)
$sqlComplementar = "";

$j = 0;
$mes = "";
for ($i = 0; $i < (QTD_BASE + 1); $i++) {             	
   $mes = $intervalo[$i];
   if (($i + 1) == (QTD_BASE + 1)) {
   	     $sqlComplementar = $sqlComplementar . "SELECT " . $mes . " as mes, " . retornaAnoConsulta($mesAtual + $j, $anoAtual) . " as ano, 'SOLUCIONADO' as situacao UNION\n";
		 $sqlComplementar = $sqlComplementar . "SELECT " . $mes . " as mes, " . retornaAnoConsulta($mesAtual + $j, $anoAtual) . " as ano, 'CONTORNADO'  as situacao";
   } else {
        $sqlComplementar = $sqlComplementar . "SELECT " . $mes . " as mes, " . retornaAnoConsulta($mesAtual + $j, $anoAtual) . " as ano, 'SOLUCIONADO' as situacao UNION\n";
		$sqlComplementar = $sqlComplementar . "SELECT " . $mes . " as mes, " . retornaAnoConsulta($mesAtual + $j, $anoAtual) . " as ano, 'CONTORNADO'  as situacao UNION\n";
   }	
   $j++;
}
		
$sql = "SELECT  coalesce(CONVERT(t2.mes USING utf8),t.mes) as mes, 
		 	    coalesce(CONVERT(t2.ano USING utf8),t.ano) as ano,
	            coalesce(CONVERT(t2.total USING utf8),0) as total,
		        coalesce(CONVERT(t2.situacao USING utf8),t.situacao) as situacao
		FROM (";

//adiciona o complemento
$sql = $sql . $sqlComplementar;
		
$sql = $sql . ") as t

		LEFT JOIN ( 
				SELECT count(pro.co_problema) as total, 
		 			   pro_tp_sit.no_situacao as situacao,
		 			   date_format(pro_sit.dt_situacao,'%m') as mes,
		 			   date_format(pro_sit.dt_situacao,'%Y') as ano
		 
				FROM pro_v2_problema pro
				INNER JOIN pro_v2_problema_situacao pro_sit ON pro.co_problema = pro_sit.co_problema AND 
															  pro.an_problema = pro_sit.an_problema AND
															  ativo = 1										 AND
															  pro_sit.co_tipo_situacao_problema IN (8,9)
															  
				INNER JOIN pro_v2_tipo_situacao_problema  pro_tp_sit ON pro_sit.co_tipo_situacao_problema = pro_tp_sit.co_situacao
				WHERE  pro.co_equipe = " . $coEquipe . " AND  pro_sit.dt_situacao >= '" . $dataInicial . "' AND  pro_sit.dt_situacao <= '" . $dataFinal . "'" .
				"\nGROUP BY ano, mes, situacao) as t2

	   ON CONVERT(t2.mes USING utf8) = t.mes AND CONVERT(t2.ano USING utf8) = t.ano AND CONVERT(t2.situacao USING utf8) = t.situacao
	   ORDER BY mes, ano";

//echo $sql;
//exit();
$res = mysqlexecuta($conexao,$sql);

//nomes dos meses
$j = 0;
for ($i = 0; $i < (QTD_BASE + 1); $i++) {             	
   $mes = $intervalo[$i];
   $arr_mes[$i] = $mes . "-" . retornaAnoConsulta($mesAtual + $j, $anoAtual);
   $j++;
}
$arr_total_contornado  = array(0,0,0,0,0,0);
$arr_total_solucionado = array(0,0,0,0,0,0);

$i = 0;
while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {

	if ($row['situacao'] == 'CONTORNADO') {		
		$key = array_search($row['mes'] . "-" . $row['ano'], $arr_mes);
		$arr_total_contornado[$key] = $row['total'];		
		
	} else {		
		$key = array_search($row['mes'] . "-" . $row['ano'], $arr_mes);
		$arr_total_solucionado[$key] = $row['total'];
		
	}
}

//echo "Total C" . $arr_total_contornado[4];
//echo "Total S" . $arr_total_solucionado[4];

$total = 6;
$arrMediaContornado  = array(0,0,0,0,0,0);
$arrMediaSolucionado = array(0,0,0,0,0,0);
for ($i = 0; $i < $total; $i++) {	
	$totalMes = $arr_total_contornado[$i] + $arr_total_solucionado[$i];
	
	if ($totalMes > 0) {
		$arrMediaContornado[$i]  = round(($arr_total_contornado[$i]/$totalMes) * 100);
		$arrMediaSolucionado[$i] = round(($arr_total_solucionado[$i]/$totalMes) * 100);
	}  
}

//$m_contornado  = urlencode(serialize($arrMediaContornado));
//$m_solucionado = urlencode(serialize($arrMediaSolucionado));

for ($i = 0; $i < count($arr_mes); $i++) {
	$arr_mes[$i] = getMesById($arr_mes[$i]);
}
/*$m_anomes      = urlencode(serialize($arr_mes));
$m_legenda     = urlencode(serialize(array("% Contorno","% Definitiva")));
$titulo		   = ereg_replace(" ","&nbsp;","Fechamento de Problemas no Período (Período : " . $arr_mes[0] . " à " . $arr_mes[5] . " )");
$meta          = urlencode(serialize(array(60)));
$legendaMeta   = urlencode(serialize(array("Meta: Fechar 60% com solução definitiva")));*/

$titulo		   = "Fechamento de Problemas no Período (Período : " . $arr_mes[0] . " à " . $arr_mes[5] . " )";
$m_legenda     = array("% Contorno","% Definitiva");
$meta          = array(60);
$legendaMeta   = array("Meta: Fechar 60% com solução definitiva");

$grafico[0] = GeradorGrafico::geraGraficoBarras( "grafico01" ,$titulo, $arrMediaContornado, $arrMediaSolucionado, $arr_mes, $m_legenda, $meta, $legendaMeta);

echo"<table align='center' style='border: 1px solid;'>";
echo "<tr><td ><img alt='grafico01' title='grafico01' src='" . $grafico[0] . "' ></td></tr>";
echo "</table>";
echo "<br />";

//tabela de informações
$htmlTb = "";
$htmlTb .= "<table align='center' border='1' >";
$htmlTb .= "<tr><td><span style='font-weight:bold'>Mês - Ano</span></td><td><span style='font-weight:bold'>Qtd. Contorno</span></td><td><span style='font-weight:bold'>Qtd. Solucionado</span></td></tr>";	
for ($i = 0; $i < count($arr_mes); $i++) {
	$htmlTb .= "<tr><td width=120>" . $arr_mes[$i] .  "</td><td width=120>" . $arr_total_contornado[$i] . "</td><td width=120>" . $arr_total_solucionado[$i] . "</td></tr>";	
}
$htmlTb .= "</table>";

echo $htmlTb;
echo "<br /><br /><br />";
echo "<textarea id='tb00'  name='tb00' style='display:none;'>" . $htmlTb . "</textarea>";


//////////// FECHAMENTO DE PROBLEMAS NO PERÍODO POR GERÊNCIA
$sql 			 = "SELECT  count(pro.co_problema) as total,
		 					ger.sig_gerencia as gerencia,
		 					pro_tp_sit.no_situacao as situacao
					FROM 
							pro_v2_problema pro
		
					INNER JOIN derep_usuario  usu  ON pro.co_supervisor = usu.id_usuario
					LEFT  JOIN derep_gerencia  ger ON ger.id_gerencia = usu.derep_gerencia_id_gerencia
					
					INNER JOIN pro_v2_problema_situacao pro_sit ON pro.co_problema = pro_sit.co_problema AND 
															       pro.an_problema = pro_sit.an_problema AND
															       ativo = 1							 AND
															       pro_sit.co_tipo_situacao_problema IN (8,9)
															  
					INNER JOIN pro_v2_tipo_situacao_problema  pro_tp_sit ON pro_sit.co_tipo_situacao_problema = pro_tp_sit.co_situacao
					WHERE     pro.co_equipe = " . $coEquipe . " AND	  date_format(pro_sit.dt_situacao,'%m') IN (" . $mesAtual . ")" .
	   	 			"		  AND date_format(pro_sit.dt_situacao,'%Y') = " . $anoAtual .
					"\nGROUP BY gerencia,situacao		";


//echo $sql;
//exit();
$res = mysqlexecuta($conexao,$sql);

//nomes dos meses
$arrGerencia		  = array("");
$arrTotalSolucionado  = array(0);
$arrTotalContornado  = array(0);

$i = 0;
while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
	
	$key = array_search($row['gerencia'], $arrGerencia);	
	if ($key === false) {
	    $arrGerencia[$i] = $row['gerencia'];
		$key = $i;
		$i++;
	}
	
	//com verificação para evitar
	// o erro undefined offset
	if ($row['situacao'] == 'CONTORNADO') {		
		$arrTotalContornado[$key] = $row['total'];
		$arrTotalSolucionado[$key] = (isset($arrTotalSolucionado[$key])?intval($arrTotalSolucionado[$key]):0)  + 0;
	} else {		
		$arrTotalSolucionado[$key] = $row['total'];
		$arrTotalContornado[$key] = (isset($arrTotalContornado[$key])?intval($arrTotalContornado[$key]):0)  + 0;
	}
}

/*$m_contornado  = urlencode(serialize($arrTotalContornado));
$m_solucionado = urlencode(serialize($arrTotalSolucionado));
$m_anomes      = urlencode(serialize($arrGerencia));
$m_legenda     = urlencode(serialize(array("Qtd. Solução Contorno","Qtd. Solução Definitiva")));
$titulo		   = ereg_replace(" ","&nbsp;","Fechamento de Problemas no Período por Gerência (Período : " . $arr_mes[5] . " )");*/

$m_legenda     = array("Qtd. Solução Contorno","Qtd. Solução Definitiva");
$titulo		   = "Fechamento de Problemas no Período por Gerência (Período : " . $arr_mes[5] . " )";

$grafico[1] = GeradorGrafico::geraGraficoBarras( "grafico02" , $titulo, $arrTotalContornado, $arrTotalSolucionado, $arrGerencia, $m_legenda, null, null);

echo"<table align='center' style='border: 1px solid;'>";
echo "<tr><td ><img alt='grafico02' title='grafico02' src='" . $grafico[1] . "'></td></tr>";
echo "</table>";
echo "<br /><br />";

///////////////// CONTROLE DE FECHAMENTO NO PRAZO
$sql =	" SELECT pro.co_problema as coProblema,
		 			pro_tp_sit.no_situacao as situacao,
		 			pro.dt_cadastro_problema as dataCadastro,
		 			pro_sit.dt_situacao as dataSituacao,
		 			date_format(pro_sit.dt_situacao,'%m') as mes,
		 			date_format(pro_sit.dt_situacao,'%Y') as ano
		 			
			FROM 
					pro_v2_problema pro
			INNER JOIN 	pro_v2_problema_situacao pro_sit ON pro.co_problema = pro_sit.co_problema AND 
						pro.an_problema = pro_sit.an_problema AND
						ativo = 1	AND
						pro_sit.co_tipo_situacao_problema IN (8,9)
			INNER JOIN pro_v2_tipo_situacao_problema  pro_tp_sit ON pro_sit.co_tipo_situacao_problema = pro_tp_sit.co_situacao
			WHERE    pro.co_equipe = " . $coEquipe . " AND	pro_sit.dt_situacao >= '" . $dataInicial . "' AND  pro_sit.dt_situacao <= '" . $dataFinal . "'" .
	   	 	"\nORDER BY mes";
//echo $sql;
$res = mysqlexecuta($conexao,$sql);

//nomes dos meses
$j = 0;
for ($i = 0; $i < (QTD_BASE + 1); $i++) {             	
   $mes = $intervalo[$i];
   $arr_mes[$i] = $mes . "-" . retornaAnoConsulta($mesAtual + $j, $anoAtual);
   $j++;
}
$arrFechadoNoPrazo   = array(0,0,0,0,0,0);
$arrFechadoForaPrazo = array(0,0,0,0,0,0);

$totalGeral = 0;
while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
	
	$key = array_search($row['mes']."-".$row['ano'], $arr_mes);
	
	if (calculaPrazoFechamentoProblema($row['dataCadastro'],$row['dataSituacao'])) {
		$arrFechadoNoPrazo[$key] =  $arrFechadoNoPrazo[$key] + 1;
	} else {
		$arrFechadoForaPrazo[$key] =  $arrFechadoForaPrazo[$key] + 1;
	}
}

$porcentagemNoPrazo     = array();
$porcentagemForaPrazo = array();
for ($i = 0; $i < count($arrFechadoNoPrazo); $i++) {	
	$total = $arrFechadoNoPrazo[$i] + $arrFechadoForaPrazo[$i];	
	
	if ($total > 0) {
		$porcentagemNoPrazo[$i]   = round(($arrFechadoNoPrazo[$i]/$total) * 100);
		$porcentagemForaPrazo[$i] = round(($arrFechadoForaPrazo[$i]/$total) * 100);
	} else {
		$porcentagemNoPrazo[$i]   = 0;
		$porcentagemForaPrazo[$i] = 0;
	}
}

/*$m_prazo  = urlencode(serialize($porcentagemNoPrazo));
$m_fprazo = urlencode(serialize($porcentagemForaPrazo));
$meta     = urlencode(serialize(array(70)));
$legendaMeta     = urlencode(serialize(array("Meta: Fechar 70% nos prazos acordados")));
$m_legenda       = urlencode(serialize(array("% Fechado Fora do Prazo","% Fechado no Prazo")));*/

for ($i = 0; $i < count($arr_mes); $i++) {
	$arr_mes[$i] = getMesById($arr_mes[$i]);
}
//$m_anomes      = urlencode(serialize($arr_mes));
//$titulo		   = ereg_replace(" ","&nbsp;","Controle de Fechamento no Prazo (Período : " . $arr_mes[0] . " à " . $arr_mes[5] . " )");

$titulo	    = "Controle de Fechamento no Prazo (Período : " . $arr_mes[0] . " à " . $arr_mes[5] . " )";
$m_legenda  = array("% Fechado Fora do Prazo","% Fechado no Prazo");
$meta       = array(70);
$legendaMeta     = array("Meta: Fechar 70% nos prazos acordados");

$grafico[2] = GeradorGrafico::geraGraficoBarras("grafico03" , $titulo, $porcentagemForaPrazo, $porcentagemNoPrazo, $arr_mes, $m_legenda, $meta, $legendaMeta);

echo"<table align='center' style='border: 1px solid;'>";
echo "<tr><td ><img alt='grafico03' title='grafico03' src='" . $grafico[2] . "'></td></tr>";
echo "</table>";
echo "<br />";


//tabela de informações
$htmlTb = "";
$htmlTb .= "<table align='center' border='1'>";
$htmlTb .= "<tr><td><span style='font-weight:bold'>Mês - Ano</span></td><td><span style='font-weight:bold'>Qtd. Fechado Prazo</span></td><td><span style='font-weight:bold'>Qtd. Fechado Fora Prazo</span></td></tr>";	
for ($i = 0; $i <  count($arr_mes); $i++) {	
	$htmlTb .= "<tr><td width=120>" . $arr_mes[$i] .  "</td><td width=120>" . $arrFechadoNoPrazo[$i] . "</td><td width=120>" . $arrFechadoForaPrazo[$i] . "</td></tr>";	
}
$htmlTb .= "</table>";

echo $htmlTb;
echo "<br /><br /><br />";
echo "<textarea id='tb02'  name='tb02' style='display:none;'>" . $htmlTb . "</textarea>";


//////////// FECHAMENTO DE PROBLEMAS NO PRAZO - POR GERÊNCIA
$sql = "SELECT pro.co_problema as coProblema,
		 pro.dt_cadastro_problema as dataCadastro,
		 pro_sit.dt_situacao as dataSituacao,
		 ger.sig_gerencia as gerencia
		
		FROM 
			pro_v2_problema pro

		INNER JOIN derep_usuario  usu  ON pro.co_supervisor = usu.id_usuario
		LEFT  JOIN derep_gerencia  ger ON ger.id_gerencia = usu.derep_gerencia_id_gerencia
					
		INNER JOIN pro_v2_problema_situacao pro_sit ON pro.co_problema = pro_sit.co_problema AND 
															  pro.an_problema = pro_sit.an_problema AND
															  ativo = 1										 AND
															  pro_sit.co_tipo_situacao_problema IN (8,9)

		INNER JOIN pro_v2_tipo_situacao_problema  pro_tp_sit ON pro_sit.co_tipo_situacao_problema = pro_tp_sit.co_situacao

		WHERE     	pro.co_equipe = " . $coEquipe . " AND date_format(pro_sit.dt_situacao,'%m') IN (" . $mesAtual . ")" .
	   	"			AND date_format(pro_sit.dt_situacao,'%Y') = " . $anoAtual .
	   	"\nORDER BY gerencia";

//echo $sql;
$res = mysqlexecuta($conexao,$sql);

//nomes dos meses
$arrGerencia	    = array("");
$arrTotalPrazo      = array(0);
$arrTotalForaPrazo  = array(0);

$i = 0;
while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
	
	$key = array_search($row['gerencia'], $arrGerencia);	
	if ($key === false) {
	    $arrGerencia[$i] = $row['gerencia'];
		$key = $i;
		$i++;
	}	
	
	
	//com verificação para evitar o erro
	//undefined offset
	if (calculaPrazoFechamentoProblema($row['dataCadastro'],$row['dataSituacao'])) {
		$arrTotalPrazo[$key] =  (isset($arrTotalPrazo[$key])?intval($arrTotalPrazo[$key]):0)  + 1;
		$arrTotalForaPrazo[$key] =  (isset($arrTotalForaPrazo[$key])?intval($arrTotalForaPrazo[$key]):0)  + 0;
	} else {
		$arrTotalForaPrazo[$key] =  (isset($arrTotalForaPrazo[$key])?intval($arrTotalForaPrazo[$key]):0)  + 1;
		$arrTotalPrazo[$key] =  (isset($arrTotalPrazo[$key])?intval($arrTotalPrazo[$key]):0)  + 0;
	}
}

$m_legenda     = array("Qtd. Fechado Fora do Prazo","Qtd. Fechado no Prazo");
$titulo		   = "Fechamento de Problemas no Prazo por Gerência (Período : " . $arr_mes[5] . " )";

$grafico[3] = GeradorGrafico::geraGraficoBarras("grafico04" , $titulo, $arrTotalForaPrazo, $arrTotalPrazo, $arrGerencia, $m_legenda, null, null);

echo"<table align='center' style='border: 1px solid;'>";
echo "<tr><td ><img alt='grafico04' title='grafico04' src='". $grafico[3] . "'></td></tr>";
echo "</table>";
echo "<br /><br />";

///////////// TENDÊNCIA DE FECHAMENTO NO PRAZO (RDPs em Andamento)
$sql= "SELECT pro.co_problema as coProblema,
		 pro.dt_cadastro_problema as dataCadastro,
		 pro_sit.dt_situacao as dataSituacao,
		 pro_tp_sit.no_situacao,
		 ger.sig_gerencia as gerencia
	   FROM 
		 pro_v2_problema pro

	   INNER JOIN derep_usuario  usu  ON pro.co_supervisor = usu.id_usuario
	   LEFT  JOIN derep_gerencia  ger ON ger.id_gerencia = usu.derep_gerencia_id_gerencia

	   INNER JOIN pro_v2_problema_situacao pro_sit ON pro.co_problema = pro_sit.co_problema AND 
															  pro.an_problema = pro_sit.an_problema AND
															  ativo = 1										 AND
															  pro_sit.co_tipo_situacao_problema NOT IN (7,8,9)

	   INNER JOIN pro_v2_tipo_situacao_problema  pro_tp_sit ON pro_sit.co_tipo_situacao_problema = pro_tp_sit.co_situacao
	   WHERE pro.co_equipe = " . $coEquipe;

$res = mysqlexecuta($conexao,$sql);

$totalPrazo      = 0;
$totalForaPrazo  = 0;
$total           = 0;

$i = 0;
while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
	
	if (calculaPrazoFechamentoProblema($row['dataCadastro'],date("Y-m-d"))) {
		$totalPrazo     =  $totalPrazo + 1;
	} else {
		$totalForaPrazo =  $totalForaPrazo + 1;
	}
}

$porcentagemNoPrazo   = 0;
$porcentagemForaPrazo = 0;

//if ($totalPrazo > 0 || $totalForaPrazo > 0) {
	$total = $totalPrazo + $totalForaPrazo;	
	$porcentagemNoPrazo   = round(($totalPrazo/$total) * 100);
	$porcentagemForaPrazo = round(($totalForaPrazo/$total) * 100);
//}

$dados 		   = array($porcentagemForaPrazo,$porcentagemNoPrazo);	
$m_legenda     = array("Em atraso","No prazo");
$titulo		   = "Tendência de Fechamento no Prazo";

$grafico[4] = GeradorGrafico::geraGraficoPizza("grafico05", $titulo, $dados, $m_legenda);

if ($totalPrazo > 0 || $totalForaPrazo > 0) {
	echo"<table align='center' style='border: 1px solid;'>";
	echo "<tr><td ><img alt='grafico05' title='grafico05' src='" . $grafico[4] . "'></td></tr>";
	echo "</table>";
	echo "<br />";
} else {
	echo "<span style='color:red;margin-left:42%'>Não existem registros para o gráfico.</span><br /><br />";
}

//tabela de informações
$htmlTb = "";
$htmlTb .= "<table align='center' border='1'>";
$htmlTb .= "<tr><td><span style='font-weight:bold'>Qtd. No Prazo</span></td><td><span style='font-weight:bold'>Qtd. Em Atraso</span></td></tr>";	
$htmlTb .= "<tr><td width=120>" . $totalPrazo . "</td><td width=120>" . $totalForaPrazo . "</td></tr>";	
$htmlTb .= "</table>";

echo $htmlTb;
echo "<br /><br /><br />";
echo "<textarea id='tb04'  name='tb04' style='display:none;'>" . $htmlTb . "</textarea>";


/////////////////// ACOMPANHAMENTO DE PRAZO POR GERÊNCIA
//reseta o ponteiro para o inicio
mysql_data_seek($res, 0) ;

//nomes dos meses
$arrGerencia	    = array("");
$arrTotalPrazo      = array(0);
$arrTotalForaPrazo  = array(0);

$i = 0;
while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {	
		 
	$key = array_search($row['gerencia'], $arrGerencia);	
		
	if ($key === false) {
	    $arrGerencia[$i] = $row['gerencia'];
		$key = $i;
		$i++;
	}
		
 	if (calculaPrazoFechamentoProblema($row['dataCadastro'],date("Y-m-d"))) { 		
		$arrTotalPrazo[$key] =  (isset($arrTotalPrazo[$key])?intval($arrTotalPrazo[$key]):0) + 1;
		$arrTotalForaPrazo[$key] = (isset($arrTotalForaPrazo[$key])?intval($arrTotalForaPrazo[$key]):0) + 0;
	} else {
		$arrTotalForaPrazo[$key] =  (isset($arrTotalForaPrazo[$key])?intval($arrTotalForaPrazo[$key]):0)  + 1;
		$arrTotalPrazo[$key] =  (isset($arrTotalPrazo[$key])?intval($arrTotalPrazo[$key]):0)  + 0;
	}
}

$m_legenda     = array("Qtd. Em Atraso","Qtd. No Prazo");
$titulo		   = "Acompanhamento de Prazos por Gerência";

$grafico[5] = GeradorGrafico::geraGraficoBarras("grafico06" , $titulo, $arrTotalForaPrazo, $arrTotalPrazo, $arrGerencia, $m_legenda, null, null);

echo"<table align='center' style='border: 1px solid;'>";
echo "<tr><td ><img alt='grafico06' title='grafico06' src='" . $grafico[5] . "'></td></tr>";
echo "</table>";
echo "<br /><br />";

$qtd = count($grafico);
echo "<input type='hidden'  id='qtdImgs' name='qtdImgs'    value='" . $qtd .  "'>";
echo "<select id='imgs' name='imgs[]' multiple style='display:none;'>";
for ($i = 0; $i < $qtd; $i++) {
      echo "<option value='" . $grafico[$i] .  "'>" . $grafico[$i] . "</option>";
}
echo "</select>";
                        
?>

<br />
<table  style='margin-left:22%;width:800px;'>
	<tr>
		<td width="480px">
			<b>Análise dos Indicadores:</b>		
		</td>
	
       <td width="200px" align='right'> <select id='opcaoArquivo' name='opcaoArquivo'> <option value='1' >Arquivo PDF</option> <option value='2' >Arquivo DOC</option>  </select>   <input type='button' value='Gerar Arquivo' onclick='javascript:gerarArquivo();' /></td>
	</tr>
</table>
<br />
<div class='textInfo'><textarea id='textAnalise' name='textAnalise'>Escreva aqui a análise do processo no que se refere à Eficiência, Eficácia e Efetividade, levando em conta os quesitos: Gestão, Qualidade, Pessoas e Resultado.</textarea></div>        
<br />

</form>
</BODY>
</HTML>