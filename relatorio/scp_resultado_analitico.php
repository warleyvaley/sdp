<?php 

require_once "../../mysqlconecta_dbbit.php";
require_once "../../mysqlexecuta_pro.php";
require_once "funcoes_relatorio_analitico.php";
//require_once "../../dompdf/dompdf_config.inc.php";
require_once('../../tcpdf/tcpdf.php');

DEFINE("TODOS","0");
DEFINE("ABERTOS","100");
DEFINE("FECHADOS","200");


error_reporting(E_ALL);
ini_set('display_errors','On');


set_time_limit(120);

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Relatorio de Analitico de Problemas');
$pdf->SetTitle('Sistema de Documentacao de Problemas');
$pdf->SetSubject('Relatorio Analitico');
$pdf->SetKeywords('Analitico, PDF, SDP');


// set default header data
$pdf->SetHeaderData('logo_ect.gif', PDF_HEADER_LOGO_WIDTH, utf8_encode('Sistema de Documentação de Problemas'),  utf8_encode("Relatório Analítico de Problemas"));

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->SetFont('dejavusans', '', 10);

$codProblema = "";
$anProblema = "";
$where = "";
$coSituacao = "";
$coEquipe = $_POST['cod_equipe'];
$noSituacao = "TODOS";
$coSituacao = $_POST['cmb_situacao'];
$diaI = "";

$where = 'WHERE co_equipe = ' . $coEquipe;

if ($_POST['cod_problema'] != "") {
	
	list($codProblema,$anProblema) = explode("/",$_POST['cod_problema']);
} 

if ($codProblema != "" ) {
	
	$where = $where . " AND pro.co_problema = $codProblema  and pro.an_problema = $anProblema ";
	
} else {	

if ( isset($_POST['cmb_situacao'])) {
		 if ($_POST['cmb_situacao'] == TODOS)  {
			
			if ($where != "") {
				$where = $where . " and tp.no_situacao in  ('EM ANDAMENTO','EM OBSERVAÇÃO','SUSPENSO') or 
			 	   	(tp.no_situacao in  ('ENCERRADO','SOLUCIONADO','CANCELADO')";
			} else {
				$where = "where tp.no_situacao in  ('EM ANDAMENTO','EM OBSERVAÇÃO','SUSPENSO') or 
			 	   	(tp.no_situacao in  ('ENCERRADO','SOLUCIONADO','CANCELADO')";
			}
			
		} else if ($_POST['cmb_situacao'] == ABERTOS)  {
			
			if ($where != "") {
				$where = $where . " and tp.no_situacao in  ('REGISTRADO','EM ANDAMENTO','EM OBSERVAÇÃO','SUSPENSO') ";
			} else {
				$where = "where tp.no_situacao in  ('REGISTRADO','EM ANDAMENTO','EM OBSERVAÇÃO','SUSPENSO') ";
			}
		} else if ($_POST['cmb_situacao'] == FECHADOS)  {
			
			if ($where != "") {
				$where = $where . " and tp.no_situacao in  ('ENCERRADO','SOLUCIONADO','CANCELADO') ";
			} else {
				$where = "where tp.no_situacao in  ('ENCERRADO','SOLUCIONADO','CANCELADO') ";
			}
		} else {
				
			if ($where != "") {
				$where = $where  . " and tp.co_situacao in  (" . $coSituacao . ") ";
			} else {
				$where = "where tp.co_situacao in  (" . $coSituacao . ") ";
			}
		
		} 	 	
}



 if ($_POST["periodo_inicial"] != "" ) {
	list($diaI,$mesI,$anoI) = explode("/", $_POST['periodo_inicial']);
	list($diaF,$mesF,$anoF) = explode("/", $_POST['periodo_final']);
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
	
} else {
	
	if ($_POST['cmb_situacao'] == TODOS) {
		$where = $where . ")";
	}
}

}

// seleciona o registro do problema
$sql = "SELECT pro.co_problema,
		 	   pro.an_problema,
		 	   pro.desc_observacao_problema,
		 	   pro.desc_problema,
		 	   date_format(pros.dt_situacao,'%d/%m/%Y') as dt_situacao,
		 	   tp.no_situacao,
		 	   pros.desc_problema_situacao,
		 	   ds.ser_no_servico, 
		 	   usu.nom_usuario,
		 	   ger.sig_gerencia
		FROM
			   pro_v2_problema pro
		INNER JOIN pro_v2_problema_situacao pros on pro.co_problema = pros.co_problema and pro.an_problema = pros.an_problema and ativo = 1
		INNER JOIN pro_v2_tipo_situacao_problema tp on tp.co_situacao = pros.co_tipo_situacao_problema
		INNER JOIN derep_servico ds on pro.co_servico_afetado = ds.ser_co_servico
		INNER JOIN derep_usuario  usu ON pro.co_supervisor = usu.id_usuario
		LEFT  JOIN derep_gerencia  ger ON ger.id_gerencia = usu.derep_gerencia_id_gerencia       
		" ;

$sql = $sql . " " . $where;
$sql = $sql . " order by pro.an_problema,pro.co_problema asc";

//echo "SQL " . $sql;
//exit();
$res = mysqlexecuta($conexao,$sql);
$qtd_problema = mysql_num_rows($res);


$qtdPanel = 0;
$nmsPanel = array();

$equipe =  ($coEquipe == 1 ) ? utf8_encode("CCD/AC") : utf8_encode("CCD/SP");


if ($coSituacao == "0" ) {
	$noSituacao = "TODOS";
} else if ($coSituacao == "100" ) {
	$noSituacao = "ABERTOS";
} else if ($coSituacao == "200" ) {
	$noSituacao = "FECHADOS";
} else {
$sql = 'SELECT tp.no_situacao
		FROM pro_v2_tipo_situacao_problema tp
		WHERE  tp.co_situacao = ' . $coSituacao;

$resSit = mysqlexecuta($conexao,$sql);
$situacao = mysql_fetch_array($resSit);
$noSituacao = $situacao['no_situacao'];

} 


//add a page
$pdf->AddPage();

$htmlPDF = '
<!-- EXAMPLE OF CSS STYLE -->
<style>
    h1 {
        color: navy;
        font-family: times;
        font-size: 15pt;
        
    }
  
    table.first {
        color: #003300;
        font-family: helvetica;
        font-size: 10pt;
        border-left: 1px solid black;
        border-right: 1px solid black;
        border-top: 1px solid black;
        border-bottom: 1px solid black;
        background-color: none;
        width:100%;
    }
    
    table.sub {
        color: #003300;
        font-family: helvetica;
        font-size: 10pt;
        background-color: none;
        width:100%;
        text-align:justify;
    }
    
    td.main {
        border: 1px solid black;
        
    }
    
    td.subTb {
        width:5%;
        
    }
    
    div.descricao {
        color: red;
        font-family: helvetica;
        font-size: 10pt;
        width:100%;
        
    }
    div.descricaoDetalhe {
        color: #000;
        font-family: helvetica;
        font-size: 10pt;
        width:100%;
        padding-left:50px;
    }        
    .tipo {
        padding-left:5px;
    }
    .estimada {
        margin-left:15px;
    }
    span.realizada {
        margin-left:25px;
    }
</style>';

$htmlHead = '<table class="sub">
	<tr>
		<td width="150"><b>Equipe de problema:</b> </td><td> ' . $equipe . '</td>
	</tr>
	<tr>
		<td width="150"><b>' . utf8_encode("Situação: ") . "</b> </td><td> " . $noSituacao . '</td>
	</tr>
	<tr>
		<td width="150"><b>' . utf8_encode("Período: ") . "</b> </td><td> " . utf8_encode($_POST['periodo_inicial']) . utf8_encode(" à ") . utf8_encode($_POST['periodo_final']) . '</td>
	</tr>
</table><br /><br />';

$htmlHead = $htmlPDF . $htmlHead;
$pdf->writeHTML($htmlHead, true, false, true, false, '');  
 
for($i=0; $i<$qtd_problema; $i++) {
	
	$coProblema 	  = mysql_result($res, $i, "pro.co_problema");
	$anProblema 	  = mysql_result($res, $i, "pro.an_problema");
	$descObProblema   = mysql_result($res, $i, "pro.desc_observacao_problema");
	$descProblema 	  = mysql_result($res, $i, "pro.desc_problema");
	$dtProblema       = mysql_result($res, $i, "dt_situacao");
	$stProblema       = mysql_result($res, $i, "tp.no_situacao");
	$descStProblema   = mysql_result($res, $i, "pros.desc_problema_situacao");
	$noServico  	  = mysql_result($res, $i, "ds.ser_no_servico");
	$noResponsavel    = mysql_result($res, $i, "usu.nom_usuario");
	$noGerencia 	  = mysql_result($res, $i, "ger.sig_gerencia");
		
	$dtHoje =  date('d') . "/" .  date('m') . "/" .  date('Y');
		
	$diProblema = "";
	$prProblema = "";
	
	if ($stProblema == "EM ANDAMENTO" || $stProblema == "EM OBSERVAÇÃO") {
		$prProblema = analisaPrazo($coProblema,$anProblema) . " - ";
		$diProblema = qtdDias($dtProblema, $dtHoje) . " dias";	
	}
	


// define some HTML content with style
$html = '
<table class="first" cellpadding="1" cellspacing="1">
 <tr>
  <td class="main" align="center"><b>' . utf8_encode("Nº Problema") .'</b></td>
  <td class="main" align="center" ><b>' . utf8_encode("Serviço") .'</b></td>
  <td class="main" align="center"><b>' . utf8_encode("Gerência") .'</b></td>
  <td class="main" align="center"> <b>' . utf8_encode("Responsável") .'</b></td>
  <td class="main" align="center"><b>Data</b></td>
  <td class="main" align="center"><b>' . utf8_encode("Situação") . '</b></td>
  <td class="main" align="center"><b>Controle Prazo</b></td>  
 </tr>
 <tr>
 	<td class="main">'. utf8_encode(str_pad( $coProblema ,4,"0",STR_PAD_LEFT). "/" . $anProblema) . '</td>'.
	'<td class="main">'. utf8_encode($noServico) .'</td>'.
	'<td class="main">'. utf8_encode($noGerencia) .'</td>'.
	'<td class="main">'. utf8_encode($noResponsavel) .'</td>'.
    '<td class="main">'. utf8_encode($dtProblema)  .'</td>'.
    '<td class="main">'. utf8_encode($stProblema)  .'</td>'.
    '<td class="main">'. utf8_encode($prProblema) . " ". utf8_encode($diProblema) .'</td>'.
 '</tr> 
 <tr>
 	<td  class="main" colspan="7" ><b>' . utf8_encode("Descrição: ") .  '</b><br /> '. utf8_encode($descProblema) .'</td>
 </tr>
</table>

 
<br /> <br /><table  class="sub">
     <tr>
		<td ><b>'. utf8_encode("Situação: ") .'</b></td>'.
    '</tr>
	<tr>
		<td >' .utf8_encode($descStProblema) .'</td>
	</tr>
</table><br />';

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
			$qtdPanel++;
			
			for($j=0; $j<$qtd_acoes; $j++) {
				
				$coAcao 	= mysql_result($res2, $j, "ac.co_acao");
				$tpSolucao  = mysql_result($res2, $j, "tps.no_tipo_solucao");
				$desc 		= mysql_result($res2, $j, "ac.desc_acao");
				$dtEstimado = mysql_result($res2, $j, "dt_estimado_acao");
				$dtFim 		= mysql_result($res2, $j, "dt_fim_acao");
								
			   	$html = $html . '<br /><table  class="sub">
								    		<tr> 
								    		    <td  width="60"> <b>' . str_pad( ($j + 1) ,4,"0",STR_PAD_LEFT) . '</b> </td>
								    			<td  width="150"> <b>Tipo:</b> '. $tpSolucao . ' </td>'. 
								   				'<td width="232"><b>'. utf8_encode("Conclusão estimada: ") ."</b>".  utf8_encode($dtEstimado) . '</td>'.
								   				'<td width="200"><b>' .utf8_encode("Conclusão realizada: ") ."</b>". utf8_encode($dtFim) . '</td>
											</tr>
											<tr>
								 				<td  colspan="4">' .utf8_encode($desc) .'</td>
								 			</tr>
								 		</table><br /><br />';
			   				   			   	
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
					
					$html = $html . '<table class="sub">
										<tr>	
											<td  class="subTb" ></td>
											<td ><b>' . utf8_encode($dtResp) . ' </b></td>
										</tr>
										<tr>
										 	<td colspan="2" class="subTb" ></td>
										 	<td colspan="2">'. utf8_encode($desc) . '</td>
										</tr>   
									</table><br /><br />';
				}
				
			}	
			
	if ($descObProblema != "") {		
		$html = $html . '<table class="sub">
					  <tr>	
						  <td ><b>'. utf8_encode("Acompanhamento do Problema:") . '</b></td>
					  </tr>
					  <tr>
						  <td >' . utf8_encode($descObProblema) . '</td>
					  </tr>
				   </table><br /><br /><br />';
	}	
			
	} else {
		$html = $html . '<div class="descricao" >' . utf8_encode("Não existem ações cadastradas para o problema") . '</div><br /><br />';
	}		

 $html = $htmlPDF . $html; 	
 $pdf->writeHTML($html, true, false, true, false, ''); 
	
}

	

// reset pointer to the last page
$pdf->lastPage();

ob_clean();
//Close and output PDF document
$pdf->Output('relatorio_analitico_' . time() .  '.pdf', 'I');

?>	