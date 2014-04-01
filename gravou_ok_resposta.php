<?php
include "../mysqlconecta_dbbit.php";
include "../mysqlexecuta_pro.php";
include "../config.php";

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<title>Gravou OK Resposta</title>
<link rel="stylesheet" type="text/css" href="../geral_problema.css">
<script src="js/valida_acao.js" type="text/JavaScript"></script>
</head>
<body>
<div class="tudo">
    <div class="topo">
         <div class="figura_normal"></div>
         <div class="h1">Registro de Resposta de Ação</div>
    </div>
   <div class="comum">
       <p>O registro foi gravado/atualizado com sucesso!</p>
       <p></p>
       
       <?php
       $an_pro = $_GET["anp"];
       $co_pro = $_GET["cop"];
       $nu_acao =  $_GET["noa"];

       if ($_GET["ctrl"] == '1') {
           echo '<p align="center"><a href="" onclick="finalizarCadResposta();">Fechar</a></p>';
       } else if ($_GET["ctrl"] == '2') {
           echo '<p align="center"><a href="form_alterar_acao_problema.php?cop='. $co_pro .'&anp='. $an_pro . '&coa=' . $nu_acao .'">Voltar ao cadastro</a></p>';
       } else {
           echo '<p align="center"><a href="problema.htm">Voltar ao menu</a></p>';
       }
       
       ?>
   </div>
</div>
</body>
</html>
