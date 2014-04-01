<?php
include "../mysqlconecta_dbbit.php";
include "../mysqlexecuta_pro.php";

error_reporting(E_ALL);
ini_set('display_errors','On');


$usuario = $_SERVER["REMOTE_USER"];

list($dominio,$usr) = split ("[\]", $usuario);


$codigo_problema = $_POST["cod_problema"];
$acao_combo     = $_POST["Acao"];


$arr_codigo_problema = explode("/",$codigo_problema);
$nu_pro  = $arr_codigo_problema[0];
$ano_pro = $arr_codigo_problema[1];

$ano_pro =  str_replace("_", "0" ,$ano_pro);

//verifica se o problema existe no banco

// retorna a quantidade de registros da tabela
$sql = "SELECT COUNT(*) as total
        FROM pro_v2_problema
        WHERE co_problema = $nu_pro and an_problema = $ano_pro";

$resultado = mysqlexecuta($conexao,$sql);

$existe = mysql_fetch_array($resultado);
mysql_free_result($resultado);

if ($existe["total"] == "0") {
    $url = "../problema_nao_cadastrado.htm";
    header("Location:$url");
} else {
    switch ($acao_combo) {
    case "Altera_problema":
                   $url = "scp_altera_problema.php?codigo=${nu_pro}&ano=${ano_pro}";
                   header("Location:$url");

    break;
    case "Altera_situacao":
                   $url = "form_altera_situacao_pro.php?codigo=${nu_pro}&ano=${ano_pro}";
                   header("Location:$url");
     break;
     
     // desativado pois as alterações e inclusões serão realizadas na mesma tela
     /*case "Registra_acao":

                   //chama a tela de edição do histórico com os dados do banco
                   $url = "form_acao_problema.php?nu=${nu_pro}&ano=${ano_pro}";
                   header("Location:$url");

      break; */
      
      case "Edicao_acao":

                   //chama a tela de edição do histórico com os dados do banco
                   $url = "scp_alterar_acao_problema.php?codigo=${nu_pro}&ano=${ano_pro}";
                   header("Location:$url");

      break;
      case "Relaciona_incidentes":
                   //chama a tela de pesquisa de incidentes
                   $url = "../form_pesquisar_inc_rel_pro.php?codigo=${nu_pro}&ano=${ano_pro}";
                   header("Location:$url");
      break;
      case "Incidentes_relacionados":
                   //chama a tela de pesquisa de incidentes
                   $url = "../form_incidentes_relacionados.php?codigo=${nu_pro}&ano=${ano_pro}";
                   header("Location:$url");
      break;
      case "Pesquisa_problema":
               print "<script>window.open('problema_visao.php?codigo=${nu_pro}&ano=${ano_pro}','','width=700,left=262,top=184,height=400,resizable=YES,Scrollbars=YES');</script>";
               include("problema.htm");
      break;
           
    

     }
}

//FECHAR CONEXÃO COM O BANCO
mysql_close($conexao);
?>
