<?php
error_reporting(E_ALL);
ini_set('display_errors','On');

require_once "../class/FileFactory.class.php";


$dados = array();
    
$dados["qtdImgs"]    = $_POST['qtdImgs'];
$dados["imgs"]    = $_POST["imgs"];

$arquivo = null;
$arquivo = FileFactory::getFile($_POST['opcaoArquivo']);
$arquivo->createFile($dados);
$arquivo = null;
?>