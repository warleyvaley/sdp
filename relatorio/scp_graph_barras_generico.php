<?php
include ("../../jpgraph/src/jpgraph.php");
include ("../../jpgraph/src/jpgraph_bar.php");
include ("../../jpgraph/src/jpgraph_line.php");
include ('../../jpgraph/SRC/jpgraph_plotline.php');


$titulo = $_GET['titulo'];
$subtitulo= $_GET['subtitulo'];
$matriz_label=unserialize(urldecode($_GET['matriz_label']));
$matriz_valor1 =unserialize(urldecode($_GET['matriz_valor1']));
$matriz_valor2 =unserialize(urldecode($_GET['matriz_valor2']));
$matriz_valor3 =unserialize(urldecode($_GET['matriz_valor3']));
$matriz_legenda =unserialize(urldecode($_GET['matriz_legenda']));


$grafico = new graph(800,400);
//left,right,top,bottom
$grafico->img->SetMargin(40,40,55,65);
$grafico->SetScale("textlin");
$grafico->SetShadow();
 
$grafico->title->Set("$titulo");
$grafico->title->SetFont(FF_VERDANA,FS_BOLD,10);
$grafico->subtitle->Set("$subtitulo");
$grafico->ygrid->Show(true);
$grafico->xgrid->Show(true);
 
$gBarras1 = new BarPlot($matriz_valor1);
$gBarras1->SetLegend($matriz_legenda[0]);
$gBarras1->Set3D();


$gBarras2 = new BarPlot($matriz_valor2);
$gBarras2->SetLegend($matriz_legenda[1]);
$gBarras2->Set3D();

$gBarras3 = new BarPlot($matriz_valor3);
$gBarras3->SetLegend($matriz_legenda[2]);
$gBarras3->Set3D();

$grupoBarras = new GroupBarPlot(array($gBarras1,$gBarras2,$gBarras3));
$grafico->Add($grupoBarras);
 
$grafico->xaxis->SetTickLabels($matriz_label);
$grafico->xaxis->SetLabelAngle(40);

if (isset($_GET['meta'])) {
	$grafico->legend->Pos(0.15,0.90);
} else {
	$grafico->legend->Pos(0.30,0.90);
}


$gBarras1->value->show(true);
$gBarras2->value->show(true);
$gBarras3->value->show(true);
$gBarras1->value->SetFormat('%d');
$gBarras2->value->SetFormat('%d');
$gBarras3->value->SetFormat('%d');

$gBarras1->SetFillColor("#FF7F00");
$gBarras2->SetFillColor("#009900");
//$gBarras3->SetFillColor("blue");

if (isset($_GET['meta'])) {
	
	$meta =  unserialize(urldecode($_GET['meta']));
	$legMeta = unserialize(urldecode($_GET['legendaMeta']));
	$l1 = new PlotLine(HORIZONTAL,$meta[0],'red',12);
	$l1->SetLegend($legMeta[0]);
	$grafico->AddLine($l1);
}	


$graph->Stroke();
?>
