<?php
$usr_ad = "correiosnet\msxspi";    //nome do usu�rio
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
("N�o foi poss�vel efetuar a conex�o com o Servidor de Autentica��o, favor entrar em contato com a
equipe respons�vel, ramal: 1850 ou 1102!");

    if (!($ad = @ldap_bind($ds, $usr_ad, $pass))) {
    //se n�o validar
         echo "N�o foi poss�vel efetuar a valida��o no Servidor de Autentica��o, favor entrar em contato com a
equipe respons�vel, ramal: 1850 ou 1102!";


         exit;
    }
    
    
    
?>
