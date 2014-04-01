<?php
include "../mysqlconecta_dbbit.php";
include "../mysqlexecuta_pro.php";
include "../config.php";


if (isset($_GET[cop])){
   $co_problema = $_GET[cop];
}

if (isset($_GET[anp])) {
   $an_problema = $_GET[anp];
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<title>Gravou OK Ação</title>
<link rel="stylesheet" type="text/css" href="../geral_problema.css">
</head>
<body>
<div class="tudo">
    <div class="topo">
         <div class="figura_normal"></div>
         <div class="h1">Registro de Ação</div>
    </div>
   <div class="comum">
       <p>O registro foi gravado/atualizado com sucesso!</p>
       <p></p>
       
       <p align="center"><a href="scp_alterar_acao_problema.php?codigo=<?php echo $co_problema; ?>&ano=<?php echo $an_problema; ?>">Voltar a tela de cadastro</a></p>

   </div>
</div>
</body>
</html>
