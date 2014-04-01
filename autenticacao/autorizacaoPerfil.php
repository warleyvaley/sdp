<?php
//////////////////////////////////////////////////////
//
// Verifica o perfil do Usuсrio logado no sistema
// e permite ou nуo a opчуo de acessar a opчуo 
// informada
//
//////////////////////////////////////////////////////
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

	
//verifica se a pсgina щ permitida para o usuсrio 
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