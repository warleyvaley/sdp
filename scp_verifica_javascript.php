<?php
include "config.php";
?>

<html>
<head>
<title>Verifica JavaScript</title>
<noscript>
    <meta http-equiv="Refresh" content="1; url= <?php echo $url . "erro_javascript.php?id=".$_GET['id'] ?> ">
</noscript>
<script type="text/JavaScript">
 window.location.href = '<?php
if ($_GET['id']==bit){
    echo scp_verifica_login.".".php;
}

//versão antiga do SDP
if ($_GET['id']==problema){
    echo scp_verifica_login_problema.".".php;
}


//redireciona para a nova versão do sistema 
if ($_GET['id']==problema2){
    echo scp_verifica_login_problema_v2.".".php;
}

if ($_GET['id']==incidente){
   echo scp_verifica_login_incidente.".".php;
}
?>';
</script>
 </head>
<body>
</body>
</html>













