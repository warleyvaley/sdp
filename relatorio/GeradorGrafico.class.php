<?php
//BIBIOTECA JPGRAPH
include ("../../jpgraph/src/jpgraph.php");
include ("../../jpgraph/src/jpgraph_bar.php");
include ("../../jpgraph/src/jpgraph_line.php");
include ('../../jpgraph/SRC/jpgraph_plotline.php');
include ("../../jpgraph/src/jpgraph_pie.php");
include ("../../jpgraph/src/jpgraph_pie3d.php");
 
set_time_limit(0);

//diretorio de criação da imagem do gráfico
DEFINE("DIR_GRAPHS","../images/tmp/");
//formato da img do gráfico
DEFINE("GRAPH_TYPE",".jpeg");

	   
class GeradorGrafico {
	
	public static function  geraGraficoBarras($nome , $titulo, $matrizCol1, $matrizCol2, $matrizLabel, $legenda, $meta, $legendaMeta) {
	
			
		$titulo = $titulo;
		//$subtitulo= $_GET['subtitulo'];
		$matriz_label  = $matrizLabel;
		$matriz_valor1 = $matrizCol1;
		$matriz_valor2 = $matrizCol2;
		//$matriz_valor3 = unserialize(urldecode($_GET['matriz_valor3']));
		$matriz_legenda = $matrizLabel;

		$grafico = new graph(800,400);
		//left,right,top,bottom
		$grafico->img->SetMargin(40,40,55,65);
		$grafico->SetScale("textlin");
		$grafico->SetShadow();
 
		$grafico->title->Set("$titulo");
		$grafico->title->SetFont(FF_VERDANA,FS_BOLD,10);
		//$grafico->subtitle->Set("$subtitulo");
		$grafico->ygrid->Show(true);
		$grafico->xgrid->Show(true);
 
		$gBarras1 = new BarPlot($matriz_valor1);
		$gBarras1->SetLegend($legenda[0]);
		$gBarras1->Set3D();

		$gBarras2 = new BarPlot($matriz_valor2);
		$gBarras2->SetLegend($legenda[1]);
		$gBarras2->Set3D();

		//$gBarras3 = new BarPlot($matriz_valor3);
		//$gBarras3->SetLegend($matriz_legenda[2]);
		//$gBarras3->Set3D();

		$grupoBarras = new GroupBarPlot(array($gBarras1,$gBarras2));
		$grafico->Add($grupoBarras);
 
		$grafico->xaxis->SetTickLabels($matriz_label);
		$grafico->xaxis->SetLabelAngle(40);

		if (isset($meta)) {
			$grafico->legend->Pos(0.15,0.90);
		} else {
			$grafico->legend->Pos(0.30,0.90);
		}
	
		$gBarras1->value->show(true);
		$gBarras2->value->show(true);
		//$gBarras3->value->show(true);
		$gBarras1->value->SetFormat('%d');
		$gBarras2->value->SetFormat('%d');
		//$gBarras3->value->SetFormat('%d');

		$gBarras1->SetFillColor("#FF7F00");
		$gBarras2->SetFillColor("#009900");
		//$gBarras3->SetFillColor("blue");

		if (isset($meta)) {
			$meta =  $meta;
			$legMeta = $legendaMeta;
			$l1 = new PlotLine(HORIZONTAL,$meta[0],'red',12);
			$l1->SetLegend($legMeta[0]);
			$grafico->AddLine($l1);
		}	

		 // create the graph
         $date = new DateTime();
         $image = DIR_GRAPHS . $nome . "_" . $date->format('U') . GRAPH_TYPE;
         $grafico->Stroke($image);
         return $image;
	}
	
	public static function  geraGraficoPizza($nome , $titulo, $matrizCol1, $legenda) {
		
		//$subtitulo= $_GET['subtitulo'];
		//list($y,$x) = explode(",", $tamanho);

		//echo "$titulo - $subtitulo - $dados - $legenda"; exit;

		// Create the Pie Graph.
		$graph = new PieGraph(800,300,"auto");
		$graph->SetShadow();

		// Set A title for the plot
		$graph->title->Set($titulo);
		$graph->title->SetFont(FF_VERDANA,FS_BOLD,10);
		//$graph->title->SetColor("darkblue");

		//$graph->subtitle->Set($subtitulo);
		$graph->subtitle->SetFont(FF_VERDANA,FS_NORMAL,8); 
		$graph->subtitle->SetColor("red");

		$graph->legend->Pos(0.05,0.85);
		$graph->legend->SetFont(FF_VERDANA,FS_NORMAL,8); 
		$graph->SetBox(true);

		// Create 3D pie plot
		$p1 = new PiePlot3d($matrizCol1);
		$p1->SetTheme("earth");
		//$p1->SetCenter(0.35);
		$p1->SetSize(220);

		// Adjust projection angle
		$p1->SetAngle(30);

		// Adjsut angle for first slice
		$p1->SetStartAngle(170);

		// Display the slice values
		$p1->value->SetFont(FF_ARIAL,FS_BOLD,11);
		$p1->value->SetColor("black");

		// Add colored edges to the 3D pie
		// NOTE: You can't have exploded slices with edges!
		//$p1->SetEdge("navy");

		$p1->SetLegends($legenda);
		$graph->legend->Pos(0.35,0.92);

		$graph->Add($p1);

		$p1->ExplodeAll();
		$p1->value->SetFormat('%.1f%%');
		$p1->value->SetFont(FF_VERDANA,FS_NORMAL,8); 
		$p1->SetSliceColors(array("#FF7F00","#009900"));

		 // create the graph
         $date = new DateTime();
         $image = DIR_GRAPHS . $nome . "_" . $date->format('U') . GRAPH_TYPE;
         $graph->Stroke($image);
         return $image;
	}
	
}
?>