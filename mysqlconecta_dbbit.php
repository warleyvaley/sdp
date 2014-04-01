<?php
// Este arquivo conecta um banco de dados MySQL - Servidor = localhost
$dbname="dbbit"; // nome do banco de dados que será aberto
$usuario="root"; //  nome do usuário que tem acesso
$password="root"; //  senha do usuário
//1º passo - Conecta ao servidor MySQL
if(!($conexao = mysql_connect("localhost",$usuario,$password))) {
   echo "Não foi possível estabelecer uma conexão com o gerenciador MySQL. Favor Contactar o Administrador.  Detalhes:" . mysql_error();
   exit;
}

// 2º passo - Seleciona o Banco de Dados
if(!($banco=mysql_select_db($dbname,$conexao))) {
   echo "Erro ao selecionar o Database. Favor Contactar o Administrador. Detalhes:" . mysql_error();
   exit;
}

?>
