<?php
//////////////////////////////////////////////////////
//
// Verifica o perfil do Usu�rio logado no sistema
// e permite ou n�o a op��o de acessar a op��o 
// informada
//
//////////////////////////////////////////////////////
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

	
//verifica se a p�gina � permitida para o usu�rio 
//logado

function verificaPermissao($nomePagina,$usuario,$conexao) {
		
	list($dominio,$usr) = explode ("[\]", $usuario);
	$sql= "SELECT  count(usu.id_usuario) as total
       	   FROM  derep_usuario usu
       	   INNER JOIN pro_v2_permissao_acesso perm ON usu.id_usuario = perm.id_usuario 
           WHERE usu.login = '$usr' AND perm.nome_pagina = '$nomePagina'";
	//$resultado = mysqlexecuta($conexao,$sql);
	$rs_row = mysql_fetch_array($resultado);
	
	if ($rs_row['total'] == 0) {
		return false;
	} else {
		return true;
	}	
}

?>