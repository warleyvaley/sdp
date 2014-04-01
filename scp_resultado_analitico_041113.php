<?php 

require_once "../mysqlconecta_dbbit.php";
require_once "../mysqlexecuta_pro.php";
require_once "funcoes_relatorio_analitico.php";
require_once "../dompdf/dompdf_config.inc.php";

DEFINE("TODOS","0");

error_reporting(E_ALL);
ini_set('display_errors','On');

set_time_limit(120);

$htmlPDF = "";

$cabecalho = "<html ><head>";

$cabecalho = $cabecalho . "<style>
						@media all { #page-one, .footer, .page-break { display:none; } }
						@media print {	
								#page-one, .footer, .page-break { 
    											display: block;
    											color:black; 
    											font-family:helvetica; 
    											font-size: 16px; 
    											text-transform: uppercase; 
    							}
  				    			.page-break { page-break-before:always;} }</style>";

$cabecalho = $cabecalho . '<link rel="stylesheet" href="skin.css"/></head>';
$cabecalho = $cabecalho . "<body>";

$codProblema = "";
$anProblema = "";
$where = "";
$situacao = "";


if (!isset($_POST['cod_problema'])) {
	list($codProblema,$anProblema) = split("/",$_POST['cod_problema']);
} 


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

$diaI = "";
$mesI = "";
$anoI = "";

if (!isset($_POST['periodo_inicial'])) {
	list($diaI,$mesI,$anoI) = split("/", $_POST['periodo_inicial']);
	list($diaF,$mesF,$anoF) = split("/", $_POST['periodo_final']);
}
	
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

//echo isset($_POST['cmb_situacao']);

//if (!isset($_POST['periodo_inicial']) || !isset($_POST['cmb_situacao']) ) {
	$sql = $sql . ") order by pro.an_problema,pro.co_problema asc";
//} else {
	//$sql = $sql . " order by pro.an_problema,pro.co_problema asc";
//}

//echo "SQL " . $sql;
//exit();

$res = mysqlexecuta($conexao,$sql);
$problemas = mysql_fetch_array($res);
$qtd_problema = mysql_num_rows($res);

$qtdPanel = 0;
$nmsPanel = array();

for($i=0; $i<$qtd_problema; $i++) {
	
	if ( $i == 0) {
		$htmlPDF = $htmlPDF . "<h2 id='page-one'> <img style='width:89%;height:65%;' src='images/top_geao_p.PNG'/> <br>";
		$htmlPDF = $htmlPDF . "<span style='font-size:12px;font-weight:bold;color:black;'>Relatório Analítico de Problemas<br>";
		$htmlPDF = $htmlPDF . "Período: " . $_POST['periodo_inicial'] . " à ". $_POST['periodo_final'] ."</span><br>";
		
		
	} else {
		$htmlPDF = $htmlPDF . "<h2 class='page-break'>  <img style='width:89%;height:65%;' src='images/top_geao_p.PNG'/><br> ";
	}
	
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
	
	$htmlPDF = $htmlPDF . "<table border='0' cellspacing='0' cellpadding='0' width='97%' class='tabletopinfo' style='margin-left:10px;'>";
	$htmlPDF = $htmlPDF . "<tr>";
	$htmlPDF = $htmlPDF . "<td class='tableDem'>Nº Problema</td>";
	$htmlPDF = $htmlPDF . "<td class='tableDem'>Serviço</td>";
	$htmlPDF = $htmlPDF . "<td class='tableDem'>Data</td>";
	$htmlPDF = $htmlPDF . "<td class='tableDem'>Situação</td>";
	$htmlPDF = $htmlPDF . "<td class='tableDem'>Controle Prazo</td>";
	$htmlPDF = $htmlPDF . "</tr>";
	$htmlPDF = $htmlPDF . "<tr>";
	$htmlPDF = $htmlPDF . "<td width='10%' >" . str_pad( $coProblema ,4,"0",STR_PAD_LEFT). "/" . $anProblema . "</td>";
	$htmlPDF = $htmlPDF . "<td width='15%' >" . $noServico  . "</td>";
    $htmlPDF = $htmlPDF . "<td width='8%' >" . $dtProblema  . "</td>";
    $htmlPDF = $htmlPDF . "<td width='13%' >" . $stProblema  . "</td>";
    $htmlPDF = $htmlPDF . "<td width='20%' > " . $prProblema  . $diProblema ."</td>";
    $htmlPDF = $htmlPDF . "</tr></table>";

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
			//$htmlPDF = $htmlPDF . "<table width='97%'  style='margin-left:4px' ><tr><td>";
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
				
				$htmlPDF = $htmlPDF . "<table border='0' cellspacing='0' cellpadding='0' width='95%' style='margin-left:1.4%;'>";
				$htmlPDF = $htmlPDF . "<tr><td width='9%'><b>" . str_pad( ($j + 1) ,4,"0",STR_PAD_LEFT) . "</b></td><td width='22%' >Tipo: ". $tpSolucao . "</td><td width='35%'  >Conclusão estimada: ". $dtEstimado ."</td><td width='35%' >Conclusão realizada: ". $dtFim ."</td></tr>";
				$htmlPDF = $htmlPDF . "</table><br>";
				$htmlPDF = $htmlPDF . "<table border='0' cellspacing='0' cellpadding='0' width='95%' style='margin-left:9.5%;' >";
							
				
				$arrayTexto = explode(" ", $desc);
				$textoMod   = "";								
				// evita que palavras muito grandes 
				for ($i = 0; $i < count($arrayTexto); $i++) {					
					if (strlen($arrayTexto[$i]) > 100) {						
						$metade1 =  substr($arrayTexto[$i], 0, (strlen($arrayTexto[$i])/2) );
						$metade2 =  substr($arrayTexto[$i], (strlen($arrayTexto[$i])/2), strlen($arrayTexto[$i]) );
						$textoMod = $textoMod . "<br>" . $metade1 . "<br>" . $metade2; 
					} else {
						$textoMod = $textoMod . " " . $arrayTexto[$i];
					}					
				}
				
				$htmlPDF = $htmlPDF . "<tr><td width='85%' style='text-align:justify;text-justify:auto;'>" . $textoMod ."</td></tr>";	
				
				
				
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
					
					$htmlPDF = $htmlPDF . "<table border='0' width='95%' style='margin-left:47.7px' >";
					$htmlPDF = $htmlPDF . "<tr><td><b>Em</b></td><td >". $dtResp . "</td>";				
					$htmlPDF = $htmlPDF . "<tr><td >&nbsp;</td><td style='text-align:justify;text-justify:auto;' > " . $desc ."</td></tr>";
					$htmlPDF = $htmlPDF . "</table><br><br>";
				}
				////////////////////////////////////////////////////////////////////////
				//$htmlPDF = $htmlPDF . "</div>";
			}
			//$htmlPDF = $htmlPDF . "</div>";sac
			//$htmlPDF = $htmlPDF . "</td></tr></table><br><br>";
			////////////////////////////////////////////////////////////////////////////
		
	} else {
		$htmlPDF = $htmlPDF . "<span style='color:red;margin-left:80px;'> Não existem ações cadastradas para o problema </span><br><br>";
	}
	$htmlPDF = $htmlPDF. "</h2>";
}
$htmlPDF = $htmlPDF. "</body></html>";



$dompdf = new DOMPDF();
$final = $cabecalho . $htmlPDF;




$dompdf->load_html($final);

$dompdf->set_paper('a4', 'portrait');
$dompdf->render();

$canvas = $dompdf->get_canvas();
$w = $canvas->get_width();
$h = $canvas->get_height(); 
$footer = $canvas->open_object(); 
$canvas->add_object($footer, "all"); 
$font = Font_Metrics::get_font("helvetica", "bold");
$canvas->page_text($w-110,$h-30, "CESEP/GEOP: {PAGE_NUM} de {PAGE_COUNT}", $font, 8, array(0,0,0));
          
$dompdf->stream("sdp_rel_analitico_problema.pdf",array("Attachment" => 0));
?>