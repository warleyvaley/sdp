<?php
///////////////////////////////////////////////////////////
//
// Classe para geração de arquivo no TIPO DOC
//
// Desenvolvido por André Santana
// CESEP - GEOP  - 2013
//
///////////////////////////////////////////////////////////
// Load the files we need:
require_once '../utils/htmltodocx/phpword/PHPWord.php';
require_once '../utils/htmltodocx/simplehtmldom/simple_html_dom.php';
require_once '../utils/htmltodocx/htmltodocx_converter/h2d_htmlconverter.php';
require_once '../utils/htmltodocx/example_files/styles.inc';
require_once ("../include/DefinicoesSistema.php");
require_once ("iFileCreator.php");

class DOCCreator implements iFileCreator {
	
	public function createFile($dados) {		
				$qtd = $dados['qtdImgs'];
				$imgs = $dados["imgs"];
				
				$phpword_object = new PHPWord();
				$section = $phpword_object->createSection();

				$phpword_object->addFontStyle('rStyle', array('bold'=>false, 'italic'=>false, 'size'=>12, 'name'=>'Arial' ));
				$phpword_object->addFontStyle('rStyleFooter', array('bold'=>true, 'italic'=>false, 'size'=>8, 'name'=>'Arial' ));
				$phpword_object->addParagraphStyle('pStyle1', array('align'=>'left', 'spaceBefore' => 200));

				//Add header
				$header = $section->createHeader();
				$table = $header->addTable();
				$table->addRow();
				$table->addCell(4300)->addImage('../images/top_geao_p.PNG', array('width'=>557, 'height'=>45, 'align'=>'left'));
				 
				//Add footer
				$footer = $section->createFooter();
				$table = $footer->addTable();
				$table->addRow();
				$table->addCell(5700)->addText(utf8_encode("Relatório de Acompanhamento do Processo Gerir Problemas" ), 'rStyleFooter', null );

				$phpword_object->addParagraphStyle('pStyle2', array('align'=>'left', 'spaceAfter'=>400,'spaceBefore' => 100));
				$styleTable = array('borderSize'=>1, 'borderColor'=>'000000');

				//	Define cell style arrays
				$styleCell = array('valign'=>'center','cellMarginTop'=>150,
			  						'cellMarginLeft'=>60,
			  						'cellMarginRight'=>80,
			  						'cellMarginBottom'=>80);
 
				// 	Add table style
				$phpword_object->addTableStyle('tableStyle', $styleTable, null);

				// Provide some initial settings:
				$initial_state = array(
							//Required parameters:
  							'phpword_object' => &$phpword_object, // Must be passed by reference.
  							'base_root' => 'http://sac0244', // Required for link elements - change it to your domain.
  							'base_path' => '/', // Path from base_root to whatever url your links are relative to.
  
  							// Optional parameters - showing the defaults if you don't set anything:
  							'current_style' => array('size' => '11'), // The PHPWord style on the top element - may be inherited by descendent elements.
  							'parents' => array(0 => 'body'), // Our parent is body.
  							'list_depth' => 0, // This is the current depth of any current list.
  							'context' => 'section', // Possible values - section, footer or header.
  							'pseudo_list' => TRUE, // NOTE: Word lists not yet supported (TRUE is the only option at present).
  							'pseudo_list_indicator_font_name' => 'Wingdings', // Bullet indicator font.
  							'pseudo_list_indicator_font_size' => '7', // Bullet indicator size.
  							'pseudo_list_indicator_character' => 'l ', // Gives a circle bullet point with wingdings.
  							'table_allowed' => TRUE, // Note, if you are adding this html into a PHPWord table you should set this to FALSE: tables cannot be nested in PHPWord.
  							'treat_div_as_paragraph' => TRUE, // If set to TRUE, each new div will trigger a new line in the Word document.
      
 							// 	Optional - no default:    
  							'style_sheet' => htmltodocx_styles_example(), // This is an array (the "style sheet") - returned by htmltodocx_styles_example() here (in styles.inc) - see this function for an example of how to construct this array.
  			  );   
  

  			$section->addText("" , 'rStyle', 'pStyle1');  
			$text = $_POST['textAnalise'];
			// HTML Dom object:
   			$html_dom = new simple_html_dom();
   			$html_dom->load('<html><body> <table><tr><td width=620> <b>' . utf8_encode('Análise dos Indicadores:') . '</b><br />' . $text . ' </td></tr></table></body></html>');
			//Note, we needed to nest the html in a couple of dummy elements.

			//Create the dom array of elements which we are going to work on:
   			$html_dom_array = $html_dom->find('html',0)->children();
  
			// Convert the HTML and put it into the PHPWord object
   			htmltodocx_insert_html($section, $html_dom_array[0]->nodes, $initial_state);

		    //Clear the HTML dom object:
   			$html_dom->clear(); 
   			unset($html_dom);
   			$section->addText("" , 'rStyle', 'pStyle1');
   			  
			for ($j = 0; $j < $qtd; $j++) {
				
				
				
  				//Add table
				$table = $section->addTable('tableStyle');
			    //Add row
   				$table->addRow();
   				$table->addCell(9500)->addImage($imgs[$j], array('width'=>550, 'height'=>180, 'align'=>'left'));
   				
   				if ($j == 0 || $j == 2 || $j == 4) {
   					$section->addText("" , 'rStyle', 'pStyle1');
   					
   					$text = str_replace("120","200",$_POST['tb0' . $j]);
					// HTML Dom object:
   					$html_dom = new simple_html_dom();
   					$html_dom->load('<html><body>' . utf8_encode($text) . '</body></html>');
					//Note, we needed to nest the html in a couple of dummy elements.

					//Create the dom array of elements which we are going to work on:
   					$html_dom_array = $html_dom->find('html',0)->children();
  
					// Convert the HTML and put it into the PHPWord object
   					htmltodocx_insert_html($section, $html_dom_array[0]->nodes, $initial_state);

		    		//Clear the HTML dom object:
   					$html_dom->clear(); 
   					unset($html_dom);   					
   				} else {
   					$section->addText("" , 'rStyle', 'pStyle1');
   				}
   				
   				//$section->addText("" , 'rStyle', 'pStyle1');
   
   				//$section->addText("" , 'rStyle', 'pStyle1');
   			
			   //Create the dom array of elements which we are going to work on:
   				//$html_dom_array = $html_dom->find('html',0)->children();
  
			   // Convert the HTML and put it into the PHPWord object
   			   //htmltodocx_insert_html($section, $html_dom_array[0]->nodes, $initial_state);

		      //Clear the HTML dom object:
   			 // $html_dom->clear(); 
   			  //unset($html_dom);
   			
		} 

		// 	Save File
		$h2d_file_uri = tempnam(REL_NOME.".doc", 'htd');
		$objWriter = PHPWord_IOFactory::createWriter($phpword_object, 'Word2007');
		$objWriter->save($h2d_file_uri);

		// Download the file:
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=' . REL_NOME . "_" . time() . ".doc");
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . filesize($h2d_file_uri));
		ob_clean();
		flush();
		$status = readfile($h2d_file_uri);
		unlink($h2d_file_uri);
		exit();
	}
}
?>