<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Cadastro de Analista</title>
<link rel="stylesheet" type="text/css" href="geral_incidente.css">
<SCRIPT src="isEmpty.js" type="text/JavaScript"></script>
<script src="valida_cadastro.js" type="text/JavaScript"></script>

</head>
<body>
<?php
include "mysqlconecta_dbbit.php";
include "mysqlexecuta_inc.php";
include "ldap_conecta.php";

$usuario = $_SERVER["REMOTE_USER"];

//separa o domínio e o usuário logado na máquina
list($dominio,$usr) = explode ("[\]", $usuario);

//entra no AD e coleta os dados necessários
$dn = "OU=Usuários,DC=ac, DC=correiosnet, DC=int";

$filter="(|(samaccountname=$usr))";

$justthese = array("cn","samaccountname","mail");

$sr=ldap_search($ds, $dn, $filter, $justthese);

$info = ldap_get_entries($ds, $sr);

if ($info["count"] > 0) {
   for ($i=0; $i<$info["count"]; $i++) {
      $nome  = $info[$i]["cn"][0];
      $email = $info[$i]["mail"][0];
      list($primeiro_email,$resto) = split ("[@]", $email);
    //  $dpto  = $info[$i]["department"][0];

   }
}
//selecionar os departamentos

$sql = "SELECT id_departamento,sig_departamento FROM derep_departamento ORDER BY sig_departamento asc";

//$sql = "SELECT b.id_departamento,b.sig_departamento,a.nom_usuario FROM derep_departamento b INNER JOIN derep_usuario a ON
//(a.id_usuario = b.derep_usuario_id_usuario) ORDER BY id_departamento asc";

//EXECUTA A QUERY
$sql = mysqlexecuta($conexao,$sql);

$row = mysql_num_rows($sql);

?>
<form name="form" method="POST" onsubmit="return checkform()" action="scp_gravar_analista.php">
<div class="tudo">
    <div class="topo">
         <div class="figura_chefe"></div>
         <div class="h1">Cadastro de Analista</div>
    </div>
    <div class="comum">
         <p>Nome: <input type="text" name="sol_no_solicitante" size="60" maxlength="60"  value="<?php echo $nome;?>" readonly></p>
         <p>E-mail: <input type="text" name="sol_no_email" size="18" maxlength="30"  value="<?php echo $primeiro_email;?>" readonly> @correios.com.br</p>
         <p>Telefone(virtual+ramal): <input type="text" name="sol_nu_telefone" size="9" maxlength="9" value="(400)"></p>
         <p>Departamento:
         <select name="listDepartamentos" onChange="Dados(this.value)">
                 <option id="dptos" value="0">--Selecione o departamento >></option>
                 <?php
                     for($i=0; $i<$row; $i++) { ?>
	                 <option value="<?php echo mysql_result($sql, $i, "id_departamento"); ?>">
			         <?php echo mysql_result($sql, $i, "sig_departamento"); ?></option>
                 <?php
                 } ?>
	     </select></p>
         <p>Gerência:
         <select name="listGerencias">
            <option id="opcoes" value="0">--Primeiro selecione o departamento--</option>
	     </select></p>
         <input type = "hidden" name="login" value="<?php echo $usr;?>" >
   	     <p align="center"><input type="submit" value="Cadastrar" name="B1"></p>
    </div>
</div>
</form>
</body>
</html>
<?php
//FECHAR CONEXÃO COM O BANCO
mysql_close($conexao);
?>
