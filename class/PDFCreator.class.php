<?php
///////////////////////////////////////////////////////////
//
// Classe para geração de arquivo no TIPO PDF
//
// Desenvolvido por André Santana
// CESEP - GEOP  - 2013
//
///////////////////////////////////////////////////////////
require_once ("../utils/dompdf_6/dompdf_config.inc.php");
require_once ("../include/DefinicoesSistema.php");
require_once ("iFileCreator.php");

class PDFCreator implements iFileCreator {
		
	public function createFile($dados) {				
			
				$qtd = $dados['qtdImgs'];
				$imgs = $dados["imgs"];
									
				$html = '<html><head>';
				$html = $html. "<style>
						@media all { #page-one, .footer, .page-break { display:none; } }
						@media print {	
								#page-one, .footer, .page-break { 
    											display: block;
    											color:red; 
    											font-family:helvetica; 
    											font-size: 16px; 
    											text-transform: uppercase; 
    							}
  				    			.page-break { page-break-before:always;} }</style></head>";
				$html = $html. "<body>";

				$html = $html. "<img style='width:100%' src='../images/top_geao_p.jpeg'/><br /><br />";
				$html = $html. "<table border='0' width='100%' style='margin-left:0%;'>";
				$text = $_POST['textAnalise'];
  				$html = $html. "<tr><td><div style='border: 1px solid #000000; font-size:11px;font-family:helvetica;text-align:justify;text-justify:auto;'><b>Análise dos Indicadores:</b><br>" . $text. "</div></td></tr>";
  				$html = $html. "</table> ";
  				//$html = $html. "</h2>";
				
  							
  				
				for ($j = 0; $j < $qtd; $j++) {  
  					/*if ( $j == 0) {
  						$html = $html. "<h2 id='page-one'> <img style='width:100%' src='../images/top_geao_p.jpeg'/>";
  						$html = $html. "<table border='0' width='630px' style='margin-left:23%;'>";
						//$html = $html. "<tr><td style='font-family:helvetica;'>Per&iacute;odo do relat&oacute;rio:   " . $dtIni . " a " . $dtFim . "</td></tr>";
	    				//$html = $html. "<tr><td > <div style='font-family:helvetica;'> Servi&ccedil;o: " . $servico  . " </div></td></tr></table>";
  					} else {
  	    				$html = $html.  "<h2 class='page-break'>  <img style='width:17%' src='../images/top_geao_p.jpeg'/> ";
  					}*/
  					
  					$html = $html. "<table border='0' cellspacing='0' cellpadding='0'  style='margin-left:0%;margin-top:2%;'>"; 
  					$html = $html. "<tr><td><img align='center' width='100%' style='margin:0px 0px 0px 0%;border-style:solid;border-width:1px;' alt='grafico' title='grafico' src='" . $imgs[$j] . "'></td></tr>";
  					$html = $html. "</table>";
  					
					if ($j == 0 || $j == 2 || $j == 4) {
  						
  						$text = $_POST['tb0' . $j];
  						$html = $html. "<br />";
  						$html = $html. $text;
  					}
  					
  					//$html = $html. "<tr><td><img align='center' style='margin:0px 0px 15px 0%;border-style:solid;border-width:1px;' alt='grafico' title='grafico' src='" . $imgs[$index+1] . "'></td></tr>";
  					//$html = $html. "<tr><td><img align='center' style='margin:0px 0px 15px 0%;border-style:solid;border-width:1px;' alt='grafico' title='grafico' src='" . $imgs[$index+2] . "'></td></tr>";
  					//$index += 3;		
				} 
				
				///////////////////////
				$html = $html. "</body></html>";
				//echo $html;
				//exit();
				$dompdf = new DOMPDF();
				$dompdf->load_html($html);
				$dompdf->set_paper(TIPO_PAPEL,ORIENTACAO_PAPEL);
				$dompdf->render();

				$canvas = $dompdf->get_canvas();
				$w = $canvas->get_width();
				$h = $canvas->get_height(); 
				$footer = $canvas->open_object(); 
				//$canvas->add_object($footer, "all"); 	
				$font = Font_Metrics::get_font("helvetica", "bold");
				$canvas->page_text($w-550,$h-30, utf8_encode("Relatório de Acompanhamento do Processo Gerir Problemas" ), $font, 8, array(0,0,0));

				$canvas->close_object();
				$canvas->add_object($footer, "all"); 
				$dompdf->stream(REL_NOME. "_" . time() . ".pdf",array("Attachment" =>1));
	}
}
?>