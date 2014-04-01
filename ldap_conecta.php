<?php
$usr_ad = "correiosnet\msxspi";    //nome do usuário
$pass = "HpExchange.04";


$usuario = $_SERVER["REMOTE_USER"];
print_r ($usuario);
echo "tudo ok";

list($dominio,$usr) = explode ("[\]", $usuario);


if (strtoupper($dominio) == "CORREIOSNET") {
	$server = "correiosnet.int";
} else {
	$server = $dominio.".correiosnet.int";
}

//$ds = ldap_connect($server)  or die
("Não foi possível efetuar a conexão com o Servidor de Autenticação, favor entrar em contato com a
equipe responsável, ramal: 1850 ou 1102!");

    if (!($ad = @ldap_bind($ds, $usr_ad, $pass))) {
    //se não validar
         echo "Não foi possível efetuar a validação no Servidor de Autenticação, favor entrar em contato com a
equipe responsável, ramal: 1850 ou 1102!";


         exit;
    }
    
    
    
?>
