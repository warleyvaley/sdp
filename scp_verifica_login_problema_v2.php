

<?php
//include "ldap_conecta.php";

include_once "autentica_sdp.php";


if ($row > 0){
  //chama a tela de problema com os dados do banco
    $url = "sdp_v2/form_problema.php";
    header("Location:$url");
} else {
  //abre a tela de cadastro
   $url = "form_cadastro_analista.php";
   header("Location:$url");
}
//FECHAR CONEXÃO COM O BANCO
mysql_close($conexao);
?>

</body>
</html>


