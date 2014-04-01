<?php
///////////////////////////////////////////////////////////////////////////
// Classe com fun��es gen�ricas utilizadas
// para alguma tarefa no sistema
//
// Desenvolvido por Andr� Santana
// CESEP - GEOP  - 2013
///////////////////////////////////////////////////////////////////////////

//tempo em que os arquivos devem ficar dispon�veis (milisegundos)
// 50 minutos
DEFINE ("TEMPOARQUIVO",3000000);

class Utils {
	
	public static function limpaDiretorio($dir) {
		/*** cycle through all files in the directory ***/
	  	foreach (glob($dir."*") as $file) {
			/*** if	( minutos) old then delete it ***/
			if (filemtime($file) < time() - TEMPOARQUIVO) { unlink($file); }
	   	}
	}
	
	public static function showErrors() {
		  error_reporting(E_ALL);
   		  ini_set('display_errors', '1');
	}
}
?>