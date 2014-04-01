<?php
include ("../../jpgraph/src/jpgraph.php");
include ("../../jpgraph/src/jpgraph_pie.php");
include ("../../jpgraph/src/jpgraph_pie3d.php");

$dados = unserialize(urldecode($_GET['dados']));
$legenda = unserialize(urldecode($_GET['legenda']));
$titulo = $_GET['titulo'];
$subtitulo= $_GET['subtitulo'];
$tamanho=$_GET['tamanho'];
list($y,$x) = split (",", $tamanho);

//echo "$titulo - $subtitulo - $dados - $legenda"; exit;

// Create the Pie Graph.
$graph = new PieGraph(800,300,"auto");
$graph->SetShadow();

// Set A title for the plot
$graph->title->Set($titulo);
$graph->title->SetFont(FF_VERDANA,FS_BOLD,10);
//$graph->title->SetColor("darkblue");

$graph->subtitle->Set($subtitulo);
$graph->subtitle->SetFont(FF_VERDANA,FS_NORMAL,8); 
$graph->subtitle->SetColor("red");

$graph->legend->Pos(0.05,0.85);
$graph->legend->SetFont(FF_VERDANA,FS_NORMAL,8); 
$graph->SetBox(true);

// Create 3D pie plot
$p1 = new PiePlot3d($dados);
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

$graph->Stroke();
?>