<?php
// Este arquivo conecta um banco de dados MySQL - Servidor = localhost
$dbname="dbbit"; // nome do banco de dados que ser� aberto
$usuario="root"; //  nome do usu�rio que tem acesso
$password="root"; //  senha do usu�rio
//1� passo - Conecta ao servidor MySQL
if(!($conexao = mysql_connect("localhost",$usuario,$password))) {
   echo "N�o foi poss�vel estabelecer uma conex�o com o gerenciador MySQL. Favor Contactar o Administrador.  Detalhes:" . mysql_error();
   exit;
}

// 2� passo - Seleciona o Banco de Dados
if(!($banco=mysql_select_db($dbname,$conexao))) {
   echo "Erro ao selecionar o Database. Favor Contactar o Administrador. Detalhes:" . mysql_error();
   exit;
}

?>
