<?php
include "../mysqlconecta_dbbit.php";
include "../mysqlexecuta_pro.php";
include_once 'autenticacao/autorizacaoPerfil.php';

$usuario = $_SERVER["REMOTE_USER"];

if (!verificaPermissao(basename(__FILE__), $usuario,$conexao)) {
	header("Location:autenticacao/acessoNegado.php");
}

list($dominio,$usr) = explode ("[\]", $usuario);

//Codigo que seleciona os dados dos analista
$sql= "select  id_usuario, nom_usuario, email
       from derep_usuario
       where login = '$usr'";

$resultado = mysqlexecuta($conexao,$sql);

$rs_row = mysql_fetch_array($resultado);

$id_analista    =  $rs_row["id_usuario"];
$nome_analista  =  $rs_row["nom_usuario"];
$email_analista =  $rs_row["email"];


mysql_free_result($resultado);

$display_data =  date("d") ."/". date("m") ."/". date("Y");

//pega o código do problema que vieram da URL
$nu_pro     = $_GET["codigo"];
$ano_pro    = $_GET["ano"];

//seleciona o registro do problema

$sql = "SELECT dt_cadastro_problema,
               desc_problema,
               co_servico_afetado,
               co_supervisor,
               co_impacto,
               chr_causa_raiz,
               dt_causa_raiz,
               desc_causa_raiz,
               desc_observacao_problema,
               co_categoria
          FROM pro_v2_problema
          WHERE co_problema=$nu_pro AND an_problema=$ano_pro";

$res = mysqlexecuta($conexao,$sql);

$problema    = mysql_fetch_array($res);

mysql_free_result($res);
//formata os dados do registro

$tx_descricao        = ereg_replace("<br />", "",$problema["desc_problema"]);
$co_servico          = $problema["co_servico_afetado"];
$co_impacto          = $problema["co_impacto"];
$co_dono             = $problema["co_supervisor"];
$co_categoria        = $problema["co_categoria"];
$in_causa_raiz       = $problema["chr_causa_raiz"];
$dt_causa_raiz       = $problema["dt_causa_raiz"];
$tx_causa_raiz       = ereg_replace("<br />", "",$problema["desc_causa_raiz"]);
$tx_observacoes        = ereg_replace("<br />", "",$problema["desc_observacao_problema"]);

//formata as datas
if ($dt_causa_raiz <> '0000-00-00'){
   $arr_dt_causa_raiz = explode("-",$dt_causa_raiz);
   $dt_causa_raiz     = $arr_dt_causa_raiz[2]."/".$arr_dt_causa_raiz[1]."/".$arr_dt_causa_raiz[0];
} else{
      $dt_causa_raiz = null;
}

//seleciona os servicos para popular o combo
$sql= "SELECT ser_co_servico, ser_no_servico 
       FROM derep_servico 
       WHERE ser_in_situacao = 1
       ORDER BY ser_no_servico";

$resultado = mysqlexecuta($conexao,$sql);

$qtd_servicos = mysql_num_rows($resultado);

$arr_id_ser = array();
$arr_no_ser = array();

for($i=0; $i<$qtd_servicos; $i++) {
    $arr_id_ser["$i"] = mysql_result($resultado, $i, "ser_co_servico");
    $arr_no_ser["$i"] = mysql_result($resultado, $i, "ser_no_servico");
}

mysql_free_result($resultado); 

//seleciona os impactos para popular o combo
$sql= "SELECT co_impacto, no_impacto
       FROM pro_v2_impacto
       ORDER BY co_impacto";

$resultado = mysqlexecuta($conexao,$sql);

$qtd_impactos = mysql_num_rows($resultado);

$arr_id_imp = array();
$arr_no_imp = array();

for($i=0; $i<$qtd_impactos; $i++) {
    $arr_id_imp["$i"] = mysql_result($resultado, $i, "co_impacto");
    $arr_no_imp["$i"] = mysql_result($resultado, $i, "no_impacto");
}

mysql_free_result($resultado);


//seleciona os gestores dos problemas para popular o combo
$sql= "SELECT id_usuario, nom_usuario
       FROM derep_usuario
       WHERE (nom_perfil = 'GP' or nom_perfil = 'GS')
       ORDER BY nom_usuario";

$resultado = mysqlexecuta($conexao,$sql);

$qtd_usu_pro = mysql_num_rows($resultado);

$arr_id_usu_gp = array();
$arr_no_usu_gp = array();

for($i=0; $i<$qtd_usu_pro; $i++) {
    $arr_id_usu_gp["$i"] = mysql_result($resultado, $i, "id_usuario");
    $arr_no_usu_gp["$i"] = mysql_result($resultado, $i, "nom_usuario");
}

mysql_free_result($resultado);


//seleciona as categorias
$sql= "SELECT co_categoria, no_categoria
       FROM pro_v2_categoria
       ORDER BY co_categoria";

$resultado = mysqlexecuta($conexao,$sql);

$qtd_categoria = mysql_num_rows($resultado);

$arr_id_categoria = array();
$arr_no_categoria = array();

for($i=0; $i<$qtd_categoria; $i++) {
    $arr_id_categoria["$i"] = mysql_result($resultado, $i, "co_categoria");
    $arr_no_categoria["$i"] = mysql_result($resultado, $i, "no_categoria");
}

mysql_free_result($resultado);
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Registro de Problema</title>
<link rel="stylesheet" type="text/css" href="../geral_problema.css">
<link type="text/css" rel="stylesheet" href="../dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen">
<SCRIPT type="text/javascript" src="../dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
<SCRIPT src="../isEmpty.js" type="text/JavaScript"></script>
<script src="../jquery.js" type="text/javascript"></script>
<script src="../jquery.maskedinput.js" type="text/javascript"></script>
<!-- <script src="../valida_problema.js" type="text/JavaScript"></script> -->
 
 <link rel="stylesheet" href="skin.css">  
     
   	
        <style type="text/css"> 
    		@import url("menu/skin-xp-extended.css");
    		
    		 body{
  					padding:3em 0 0 0;
  					background:url(foo) fixed;
  					background: #aaa; 
  					
 			 }
    		 .menuTop {
 				 position:fixed;
  				 _position:absolute;
  				 top:0px;
  			    _top:expression(eval(document.body.scrollTop));
  				left:0px;
  				margin:0;
  				padding:0;
  				background:transparent;
  				width: 100%;
  				text-align:left;
 			}
    </style>
    <script type="text/javascript" src="js/menuControl.js"></script>	
    <script type="text/javascript" src="menu/hmenu.js"></script>	
     
 </head>
 <body onload="DynarchMenu.setup('menu', { electric: 250 });" >
 
 <?php include_once 'menuDrop.php';  ?>
<form name="form" method="POST" action="scp_gravar_alteracao_problema.php" onsubmit="return checkform()">
<div class="tudo">
   <div class="topo">
        <div class="figura_normal"></div>
        <div class="h1">Alteração do Registro do Problema</div>
   </div>
   <div class="identificacao">
		Bem-vindo(a), <b><?php echo $nome_analista; ?></b><input type="hidden" name="nome_analista" value="<?php echo $nome_analista ?>"><br>
		E-mail: <?php echo $email_analista; ?><input type="hidden" name="email_analista" value="<?php echo $email_analista ?>"><br>
  		Data: <?php echo $display_data; ?>
  </div>                
  <div class="corpo_problema">
       <input type="hidden" name="id_analista" value="<?php echo $id_analista; ?>">
       <input type="hidden" name="nu_pro" size=6 value="<?php echo $nu_pro;?>" >
       <input type="hidden" name="ano_pro" size=6 value="<?php echo $ano_pro;?>" >
       <table cellpadding="2" cellspacing="0" width="640" border="0">
             <tr><td class="topico">Descrição</td></tr>
       </table>
       <table cellpadding="2" cellspacing="0" width="640" border="0">
             <tr>
                 <td>Serviço afetado:
                   <select name="servico">
                       <option id="servico" value="0">Selecione... </option>
                       <?php
                          for($i=0; $i<$qtd_servicos; $i++) { ?>
    	            	       <option  value="<?php echo $arr_id_ser[$i]; ?>"
                                    <?php if ($arr_id_ser[$i] == $co_servico) echo 'selected';?>>
                                    <?php echo $arr_no_ser[$i]; ?></option>
                                    <?php } ?>
                      </select>
                  </td>
             </tr>
             <tr>
                 <td>Impacto:
                   <select name="impacto">
                       <option id="impacto" value="0">Selecione... </option>
                       <?php
                          for($i=0; $i<$qtd_impactos; $i++) { ?>
    	            	       <option  value="<?php echo $arr_id_imp[$i]; ?>"
                                    <?php if ($arr_id_imp[$i] == $co_impacto) echo 'selected';?>>
                                    <?php echo $arr_no_imp[$i]; ?></option>
                                    <?php } ?>
                      </select>
                  </td>
             </tr>
        </table>
        <table cellpadding="2" cellspacing="0" width="640" border="0">
             </tr>
                   <td>Descrição:*
                   		<span class="contador"><font id="Restante1">1000</font></span>
                        <br><textarea onkeyup="max1(this)" onkeypress="max1(this)" rows="5" name="tx_descricao" cols="100" title ="Descrição do problema."><?php echo $tx_descricao;?></textarea></td>
             </tr>
             <tr>
                 <td>Supervisor do problema:
                       <select name="dono">
                           <option id="dono" value="0">Selecione... </option>
                           <?php
                              for($i=0; $i<$qtd_usu_pro; $i++) { ?>
        	            	       <option  value="<?php echo $arr_id_usu_gp[$i]; ?>"
        	            	            <?php if ($arr_id_usu_gp[$i] == $co_dono) echo 'selected';?>>
                                        <?php echo $arr_no_usu_gp[$i]; ?></option>
                                        <?php } ?>
                          </select>
                  </td>
               </tr>

       </table>

       <table cellpadding="2" cellspacing="0" width="640" border="0">
             <tr><td class="topico">Categoria</td></tr>
       </table>
       <table cellpadding="0" cellspacing="2" width="640" border="0">
           <tr>
                <td>
                <select name="co_categoria">
                       <option id="none" value="0">Selecione... </option>
                       <?php
                          for($i=0; $i<$qtd_categoria; $i++) { ?>
    	            	       <option  value="<?php echo $arr_id_categoria[$i]; ?>"
                                    <?php if ($arr_id_categoria[$i] == $co_categoria) echo 'selected';?>>
                                    <?php echo $arr_no_categoria[$i]; ?></option>
                                    <?php } ?>
                </select>

              </td>
           </tr>
       </table>
       
       
       <table cellpadding="2" cellspacing="0" width="640" border="0">
             <tr><td class="topico">Causa-raiz</td></tr>
       </table>
       <table cellpadding="0" cellspacing="0" width="640" border="0">
           <tr>
                <td width="200" >Causa-raiz encontrada?
                <select size="1" name="in_causa_raiz" onchange="ativa_fun1(this.value)">
                    <option value="Sim"<?php if ($in_causa_raiz == "Sim") { echo 'selected'; } ?>>Sim</option>
	                <option value="Não"<?php if ($in_causa_raiz == "Não") { echo 'selected'; } ?>>Não</option>
                </select></td>
           </tr>
           <tr>
              <td><div id="div_causa_raiz" style="<?php if ($in_causa_raiz == "Sim")  echo 'display:block';  else echo 'display:none';?>">Quando?*
              <input type="text" readonly name="dt_causa_raiz" title = "Data de descoberta da causa-raiz." maxLength=10 size="10" value="<?php echo $dt_causa_raiz;?>">
              <input type="button" name="cal1" value="..." onclick="displayCalendar(document.forms[0].dt_causa_raiz,'dd/mm/yyyy',this)"> </div></td>
            <tr>
                   <td><div id="div_informe_causa_raiz" style="<?php if ($in_causa_raiz == "Sim")  echo 'display:block';  else echo 'display:none';?>">Informe causa-raiz:*
               		<span class="contador"><font id="Restante2">500</font></span>
                    <br><textarea onkeyup="max2(this)" onkeypress="max2(this)" rows="5" name="tx_causa_raiz" cols="100" title ="Causa-raiz do problema."><?php echo $tx_causa_raiz;?></textarea></div></td>
            </tr>
       </table>

       <table cellpadding="2" cellspacing="0" width="640" border="0">
             <tr><td class="topico">Observações</td></tr>
       </table>
       <table cellpadding="0" cellspacing="0" width="640" border="0">
            <tr>
               <td><textarea rows="5" name="tx_observacoes" cols="100" title ="Informações importantes sobre o problema."><?php echo $tx_observacoes;?></textarea></td>
            </tr>

       </table>
       <table cellpadding="0" cellspacing="0" width="640" border="0">
           <tr>
               <td align="center">
                    <input type="submit" value="Salvar" name="Salvar">

                    <!-- <input type="button" value="Cancelar" name="Cancel" onclick="javascript:history.back()">-->
               </td>
           </tr>
       </table>
     </div>
  </div>
</div>
</form>
</body>
</html>

<?php

//FECHAR CONEXÃO COM O BANCO
mysql_close($conexao);
?>
