<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<?php
include "mysqlconecta_dbbit.php";
include "mysqlexecuta_pro.php";
include_once 'autenticacao/autorizacaoPerfil.php';

$usuario = $_SERVER["REMOTE_USER"];

if (!verificaPermissao(basename(__FILE__), $usuario,$conexao)) {
	//echo "<script> window.location = 'cesep/sdp_v2/autenticacao/acessoNegado.php';  </script>";
	header("Location:autenticacao/acessoNegado.php");
}
list($dominio,$usr) = explode ("[\]", $usuario);

//Codigo que seleciona os dados dos analista 28062009
$sql= "SELECT id_usuario, nom_usuario, email, usr_co_equipe
       FROM   derep_usuario
       WHERE  login = '$usr'";

$resultado = mysqlexecuta($conexao,$sql);
$rs_row = mysql_fetch_array($resultado);

$id_analista    =  $rs_row["id_usuario"];
$nome_analista  =  $rs_row["nom_usuario"];
$email_analista =  $rs_row["email"];
$co_equipe      =  $rs_row["usr_co_equipe"];

mysql_free_result($resultado);

$display_data =  date("d") ."/". date("m") ."/". date("Y");

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

//seleciona as categorias
$sql= "SELECT co_categoria, no_categoria
       FROM pro_v2_categoria
       where no_categoria IN ('ERRO DE CÓDIGO','BACKUP/STORAGE/BANCO','REDE','CCD','SO/HARDWARE','SERVIÇOS (CLONE)','SERVIÇOS DE REDE','LOCK','BANCO DO BRASIL')
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


<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<link rel="stylesheet" type="text/css" href="../geral_problema.css">
<link rel="stylesheet" href="skin.css">  
     
<link type="text/css" rel="stylesheet" href="../dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen">
<SCRIPT type="text/javascript" src="../dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
<SCRIPT src="../isEmpty.js" type="text/JavaScript"></script>
<script src="../jquery.js" type="text/javascript"></script>
<script src="../jquery.maskedinput.js" type="text/javascript"></script>
<script src="../valida_problema.js" type="text/JavaScript"></script>
   
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
    
    <script type="text/javascript">
      // WARNING: the following should be a path relative to site, like "/hmenu/"
      // here it is set relative to the current page only, which is not recommended
      // for production usage; it's useful in this case though to make the demo work
      // correctly on local systems.
      _dynarch_menu_url = "/menu/";
    </script>
    <script type="text/javascript" src="js/menuControl.js"></script>	
     <script type="text/javascript" src="menu/hmenu.js"></script>	
</head>

<body onload="DynarchMenu.setup('menu', { electric: 250 });">

<?php include_once 'menuDrop.php';  ?>

<form name="form" method="POST" action="scp_gravar_problema.php" onsubmit="return checkform()">
<div class="tudo">
   <div class="topo">
        <div class="figura_normal"></div>
        <div class="h1">Cadastro de Problema</div>
   </div>
   <div class="identificacao">
		Bem-vindo(a), <b><?php echo $nome_analista; ?></b><input type="hidden" name="nome_analista" value="<?php echo $nome_analista; ?>"> <input type="hidden" name="co_equipe" value="<?php echo $co_equipe; ?>"><br>
		E-mail: <?php echo $email_analista; ?><input type="hidden" name="email_analista" value="<?php echo $email_analista ?>"><br>
  		Data: <?php echo $display_data; ?>
  </div>                
  <div class="corpo_problema">
       <input type="hidden" name="id_analista" value="<?php echo $id_analista; ?>">
       <table cellpadding="2" cellspacing="0" width="640" border="0">
             <tr><td class="topico">Descrição</td></tr>
       </table>
       <br>
       <table cellpadding="2" cellspacing="0" width="640" border="0">
             <tr>
                 <td>Serviço afetado: </td>
                 <td>
                   <select name="co_servico">
                       <option id="none" value="0">Selecione... </option>
                       <?php
                          for($i=0; $i<$qtd_servicos; $i++) { ?>
    	            	       <option  value="<?php echo $arr_id_ser[$i]; ?>" >
                                    <?php echo $arr_no_ser[$i]; ?></option>
                                    <?php } ?>
                      </select>
                  </td>
             </tr>
             <tr>
                 <td>Impacto: </td>
                 <td>
                   <select name="co_impacto">
                       <option id="none" value="0">Selecione... </option>
                       <?php
                          for($i=0; $i<$qtd_impactos; $i++) { ?>
    	            	       <option  value="<?php echo $arr_id_imp[$i]; ?>" >
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
                        <br><textarea onkeyup="max1(this)" onkeypress="max1(this)" rows="5" name="tx_descricao" cols="100" title ="Descrição do problema."></textarea></td>
             </tr>
             <tr>
                 <td >Dono do problema:
                       <select name="co_supervisor">
                           <option id="none" value="0">Selecione... </option>
                           <?php
                              for($i=0; $i<$qtd_usu_pro; $i++) { ?>
        	            	       <option  value="<?php echo $arr_id_usu_gp[$i]; ?>" >
                                        <?php echo $arr_no_usu_gp[$i]; ?></option>
                                        <?php } ?>
                          </select>
                  </td>
               </tr>

       </table>
       <br>
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
    	            	       <option  value="<?php echo $arr_id_categoria[$i]; ?>" >
                                    <?php echo $arr_no_categoria[$i]; ?></option>
                                    <?php } ?>
                </select>
                      
              </td>
           </tr>
       </table>
       <br>
       
       <table cellpadding="2" cellspacing="0" width="640" border="0">
             <tr><td class="topico">Causa-raiz</td></tr>
       </table>
       <table cellpadding="0" cellspacing="0" width="640" border="0">
           <tr>
                <td width="200">Causa-raiz encontrada? </td>
                <td><select size="1" name="in_causa_raiz" onchange="ativa_fun1(this.value)">
                    <option value="Sim">Sim</option>
	                <option selected value="Não">Não</option>
                </select></td>
           </tr>
        </table>

         <table cellpadding="0" cellspacing="0" width="640" border="0">
           <tr>
              <td><div id="div_causa_raiz" style="display:none">Quando?*
              <input type="text" readonly name="dt_causa_raiz" title = "Data de descoberta da causa-raiz." maxLength=10 size="10">
              <input type="button" name="cal1" value="..." onclick="displayCalendar(document.forms[0].dt_causa_raiz,'dd/mm/yyyy',this)"> </div></td>
            <tr>
                   <td><div id="div_informe_causa_raiz" style="display:none">Informe causa-raiz:*
               		<span class="contador"><font id="Restante2">500</font></span>
                    <br><textarea onkeyup="max2(this)" onkeypress="max2(this)" rows="5" name="tx_causa_raiz" cols="100" title ="Causa-raiz do problema."></textarea></div></td>
            </tr>
       </table>
       
       <br>

       <table cellpadding="2" cellspacing="0" width="640" border="0">
             <tr><td class="topico">Observações</td></tr>
       </table>
       <table cellpadding="0" cellspacing="0" width="640" border="0">
            <tr>
               <td><textarea rows="5" name="tx_observacoes" cols="100" title ="Informações importantes sobre o problema."></textarea></td>
            </tr>
       </table>
       <table cellpadding="0" cellspacing="0" width="640" border="0">
           <tr>
               <td align="center">
                    <input type="submit" value="Salvar" id="Salvar" name="Salvar" >
                    
                    <input type="button" value="Cancelar" name="Cancel" onclick="window.location='main.php'">
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