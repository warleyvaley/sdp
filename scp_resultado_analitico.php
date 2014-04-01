<!-- <!doctype html>
<html >
<head> -->

<!-- 
////////////////////////////////////////////////
// 
//  Relatóio de Problemas - resumo das informações dos
//	problemas cadastrados. Todos em andamento e todos dentro do período 
//  selecionado.
//
//  Gera arquivo em PDF.
//
//  Desenvolvido por GEOP/CESEP
//	23 de Julho de 2013
//
////////////////////////////////////////////////

 -->
	
<!--  	<link rel="stylesheet" href="skin.css"/>
 	<link rel="stylesheet" href="jquery-ui-1.10.3/themes/base/jquery.ui.all.css">
	<script src="jquery-ui-1.10.3/jquery-1.9.1.js"></script>
	<script src="jquery-ui-1.10.3/ui/jquery.ui.core.js"></script>
	<script src="jquery-ui-1.10.3/ui/jquery.ui.widget.js"></script>
	<script src="jquery-ui-1.10.3/ui/jquery.ui.accordion.js"></script>

</head>

<body>
<h1>Relatório Analítico de Problemas</h1> -->

<?php 

require_once "../mysqlconecta_dbbit.php";
require_once "../mysqlexecuta_pro.php";
require_once "funcoes_relatorio_analitico.php";
require_once "../dompdf/dompdf_config.inc.php";

DEFINE("TODOS","0");

error_reporting(E_ALL);
ini_set('display_errors','On');

$htmlPDF = "";

$cabecalho = "<html ><head>";
$cabecalho = $cabecalho . '<link rel="stylesheet" href="skin.css"/>';
$cabecalho = $cabecalho . "<style>@page{margin-top: 5px; margin-left: 6px; }</style></head><body><img style='width:87%' src='images/top_geao_p.PNG'/><h1>Relatório Analítico de Problemas</h1>";

$cabecalho = $cabecalho . "<span style='font-weight:bold;'>Período: " . $_POST['periodo_inicial'] . " à ". $_POST['periodo_final'] ."</span><br><br>";
//$periodo = $periodo . "<a style='margin-left:35%' href='sdp_rel_analitico_problema.pdf'> Gerar arquivo em PDF </a><br><br>";

$codProblema = "";
$anProblema = "";
$where = "";
$situacao = "";


list($codProblema,$anProblema) = split("/",$_POST['cod_problema']);
if ($codProblema != "" ) {
	
	$where = "where pro.co_problema = $codProblema  and pro.an_problema = $anProblema ";
	
} else {	

if ( isset($_POST['cmb_situacao'])) {
		if ($_POST['cmb_situacao'] != TODOS) {
		
			$situacao = $_POST['cmb_situacao'];
			
			if ($where != "") {
				$where = $where  . " and tp.co_situacao in  (" . $situacao . ") ";
			} else {
				$where = "where tp.co_situacao in  (" . $situacao . ") ";
			}
		
		} else {
			
			if ($where != "") {
				$where = $where . " and tp.no_situacao in  ('EM ANDAMENTO','EM OBSERVAÇÃO','SUSPENSO') or 
			 	   	(tp.no_situacao in  ('ENCERRADO','SOLUCIONADO','CANCELADO')";
			} else {
				$where = "where tp.no_situacao in  ('EM ANDAMENTO','EM OBSERVAÇÃO','SUSPENSO') or 
			 	   	(tp.no_situacao in  ('ENCERRADO','SOLUCIONADO','CANCELADO')";
			}
		}	
}

list($diaI,$mesI,$anoI) = split("/", $_POST['periodo_inicial']);
list($diaF,$mesF,$anoF) = split("/", $_POST['periodo_final']);
	
if ($diaI != "") {
	
	if ($where != "") {
		
		if ($_POST['cmb_situacao'] == TODOS) {
			$where = $where . " and pros.dt_situacao between '$anoI-$mesI-$diaI' and '$anoF-$mesF-$diaF')";
		} else {
			$where = $where . " and pros.dt_situacao between '$anoI-$mesI-$diaI' and '$anoF-$mesF-$diaF'";
		}
	} else {
		
		$where = "where pros.dt_situacao between '$anoI-$mesI-$diaI' and '$anoF-$mesF-$diaF'";
	}
	
}
}
//$where = $where . " and pros.dt_situacao between '$anoI-$mesI-$diaI' and '$anoF-$mesF-$diaF'";
//$where = $where . " and pros.dt_situacao between '$anoI-$mesI-$diaI' and '$anoF-$mesF-$diaF')";



// seleciona o registro da resposta
$sql = "select pro.co_problema,
		 	   pro.an_problema,
		 	    date_format(pros.dt_situacao,'%d/%m/%Y') as dt_situacao,
		 	   tp.no_situacao,
		 	   ds.ser_no_servico 
		 	   
		from
			   pro_v2_problema pro
		inner join pro_v2_problema_situacao pros on pro.co_problema = pros.co_problema and pro.an_problema = pros.an_problema and ativo = 1
		inner join pro_v2_tipo_situacao_problema tp on tp.co_situacao = pros.co_tipo_situacao_problema
		inner join derep_servico ds on pro.co_servico_afetado = ds.ser_co_servico" ;

$sql = $sql . " " . $where;

$sql = $sql . " order by pro.an_problema,pro.co_problema asc";

//echo "SQL " . $sql;
//exit();
$res = mysqlexecuta($conexao,$sql);
$problemas = mysql_fetch_array($res);
$qtd_problema = mysql_num_rows($res);


$qtdPanel = 0;
$nmsPanel = array();

for($i=0; $i<$qtd_problema; $i++) {
	
	$coProblema = mysql_result($res, $i, "pro.co_problema");
	$anProblema = mysql_result($res, $i, "pro.an_problema");
	$dtProblema = mysql_result($res, $i, "dt_situacao");
	$stProblema = mysql_result($res, $i, "tp.no_situacao");
	$noServico  = mysql_result($res, $i, "ds.ser_no_servico");
	$dtHoje =  date('d') . "/" .  date('m') . "/" .  date('Y');
		
	$diProblema = "";
	$prProblema = "";
	
	if ($stProblema == "EM ANDAMENTO" || $stProblema == "EM OBSERVAÇÃO") {
		$prProblema = analisaPrazo($coProblema,$anProblema) . " - ";
		$diProblema = qtdDias($dtProblema, $dtHoje) . " dias";	
	}
	
	$htmlPDF = $htmlPDF . "<table border='0' cellspacing='1' cellpadding='0' width='97%' class='tabletopinfo' style='margin-left:10px;'>";
	$htmlPDF = $htmlPDF . "<tr>";
	$htmlPDF = $htmlPDF . "<td class='tableDem'>Nº Problema</td>";
	$htmlPDF = $htmlPDF . "<td class='tableDem'>Serviço</td>";
	$htmlPDF = $htmlPDF . "<td class='tableDem'>Data</td>";
	$htmlPDF = $htmlPDF . "<td class='tableDem'>Situação</td>";
	$htmlPDF = $htmlPDF . "<td class='tableDem'>Controle Prazo</td>";
	$htmlPDF = $htmlPDF . "</tr>";
	$htmlPDF = $htmlPDF . "<tr><td >" . str_pad( $coProblema ,4,"0",STR_PAD_LEFT). "/" . $anProblema . "</td>";
	$htmlPDF = $htmlPDF . "<td >" . $noServico  . "</td>";
    $htmlPDF = $htmlPDF . "<td >" . $dtProblema  . "</td>";
    $htmlPDF = $htmlPDF . "<td >" . $stProblema  . "</td>";
    $htmlPDF = $htmlPDF . "<td > " . $prProblema  . $diProblema ."</td>";
    $htmlPDF = $htmlPDF . "</tr></table><br>";

    $sql = "select ac.co_acao,
				   tps.no_tipo_solucao,
		 		   ac.desc_acao,
		 		   date_format(ac.dt_estimado_acao,'%d/%m/%Y') as dt_estimado_acao,
		 		   date_format(ac.dt_fim_acao,'%d/%m/%Y') as dt_fim_acao
			from pro_v2_acao ac 
			inner join pro_v2_tipo_solucao tps on ac.co_solucao = tps.co_tipo_solucao
			where ac.co_problema = $coProblema and ac.an_problema = $anProblema";
    
    $res2 = mysqlexecuta($conexao,$sql);
	$acoes = mysql_fetch_array($res2);
	$qtd_acoes = mysql_num_rows($res2);
    
	
	if ($qtd_acoes > 0) {
		
		
			/////////////////// AÇÕES //////////////////////////////////////////////////
			$htmlPDF = $htmlPDF . "<table width='95%' height='40%' style='margin-left:4px' ><tr><td>";
			//$nmsPanel[$qtdPanel] = "accordion".$coProblema.$anProblema	;
			//$htmlPDF = $htmlPDF . "<div id=\"$nmsPanel[$qtdPanel]\">";
			$qtdPanel++;
			
			for($j=0; $j<$qtd_acoes; $j++) {
				
				$coAcao 	= mysql_result($res2, $j, "ac.co_acao");
				$tpSolucao  = mysql_result($res2, $j, "tps.no_tipo_solucao");
				$desc 		= mysql_result($res2, $j, "ac.desc_acao");
				$dtEstimado = mysql_result($res2, $j, "dt_estimado_acao");
				$dtFim 		= mysql_result($res2, $j, "dt_fim_acao");
				
				/*if(strlen($desc) > 80) {
					$desc =  substr($desc, 0, 80) ."..."; 
				}*/
				
				$htmlPDF = $htmlPDF . "<table border='0' cellspacing='0' cellpadding='0' width='100%'>";
				$htmlPDF = $htmlPDF . "<tr><td><b>" . str_pad( ($j + 1) ,4,"0",STR_PAD_LEFT) . "</b></td><td >Tipo: ". $tpSolucao . "</td><td >Conclusão estimada: ". $dtEstimado ."</td><td >Conclusão realizada: ". $dtFim ."</td></tr>";
				$htmlPDF = $htmlPDF . "</table><br>";
				$htmlPDF = $htmlPDF . "<table border='0' cellspacing='0' cellpadding='0' width='100%' style='margin-left:8.5%' >";
				$htmlPDF = $htmlPDF . "<tr><td >" . $desc ."</td></tr>";	
				$htmlPDF = $htmlPDF . "</table><br><br>";
				
				///////////////// REPOSTAS DAS AÇÕES ///////////////////////////////
				$sql = "select 
							date_format(dt_resposta,'%d/%m/%Y') as dt_resposta,
							desc_resposta
						from 
							pro_v2_resposta_acao
						where 
							co_acao = $coAcao";
    
    			$res3 = mysqlexecuta($conexao,$sql);
				$repostas = mysql_fetch_array($res3);
				$qtd_resposta = mysql_num_rows($res3);
				
				for($y=0; $y<$qtd_resposta; $y++) {
					$desc 	   = mysql_result($res3, $y, "desc_resposta");
					$dtResp    = mysql_result($res3, $y, "dt_resposta");
					
					$htmlPDF = $htmlPDF . "<table border='0' width='100%' style='margin-left:47.7px' >";
					$htmlPDF = $htmlPDF . "<tr><td><b>Em</b></td><td >". $dtResp . "</td>";				
					$htmlPDF = $htmlPDF . "<tr><td >&nbsp;</td><td > " . $desc ."</td></tr>";
					$htmlPDF = $htmlPDF . "</table><br><br>";
				}
				////////////////////////////////////////////////////////////////////////
				//$htmlPDF = $htmlPDF . "</div>";
			}
			$htmlPDF = $htmlPDF . "</div>";
			$htmlPDF = $htmlPDF . "</td></tr></table><br><br>";
			////////////////////////////////////////////////////////////////////////////
		
	} else {
		$htmlPDF = $htmlPDF . "<span style='color:red;margin-left:80px;'> Não existem ações cadastradas para o problema </span><br><br>";
	}
}


$dompdf = new DOMPDF();

$final = $cabecalho . $htmlPDF;
$dompdf->load_html($final);

$dompdf->set_paper('a4', 'portrait');
$dompdf->render();

//$footer = $dompdf->open_object(); 
//$dompdf->add_object($footer, "all"); 

$canvas = $dompdf->get_canvas();
$w = $canvas->get_width();
$h = $canvas->get_height(); 
$footer = $canvas->open_object(); 
$canvas->add_object($footer, "all"); 
$font = Font_Metrics::get_font("helvetica", "bold");
$canvas->page_text($w-110,$h-30, "CESEP/GEOP: {PAGE_NUM} de {PAGE_COUNT}", $font, 8, array(0,0,0));
          
$dompdf->stream("sdp_rel_analitico_problema.pdf",array("Attachment" => 0));

/*$pdf = $dompdf->output();
file_put_contents("sdp_rel_analitico_problema.pdf", $pdf);*/
//echo $periodo . $htmlPDF;
?>	

<!-- <script>
	$(function() {
		<?php
		  /*  echo "$( \"";
		    
			 for ($i = 0; $i < $qtdPanel; $i++) {				
			 	if ($i == $qtdPanel -1 ) {
			 	  echo "#" . $nmsPanel[$i];
			 	} else {
			 	  echo "#" . $nmsPanel[$i] . ",";	
			 	}
			 }			 
			 echo "\" ).accordion({ collapsible: true });\n";*/
		?>
	});
</script>
	
</body>
</html> -->