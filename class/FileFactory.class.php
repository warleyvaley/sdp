<?php
require_once ("../include/DefinicoesSistema.php");
require_once ("DOCCreator.class.php");
require_once ("PDFCreator.class.php");

class FileFactory {
	
	public static function getFile($tipoArquivo) {
		switch ($tipoArquivo) {
			case DOC_FILE:
			     return new DOCCreator();;
			break;
			case PDF_FILE:
			     return new PDFCreator();;
			break;
		}
	}
}
?>