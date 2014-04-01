<?php
include "../mysqlconecta_dbbit.php";
include "../mysqlexecuta_pro.php";

 error_reporting(E_ALL);
 ini_set("display_errors", 1);


//pega o código do problema que vieram da URL
$nu_pro     = $_GET["nu"];
$ano_pro    = $_GET["ano"];

$display_data =  date("d") ."/". date("m") ."/". date("Y");

$usuario = $_SERVER["REMOTE_USER"];
//echo $usuario;
//exit();
list($dominio,$usr) = explode("\\", $usuario);

//Codigo que seleciona os dados dos analista 28062009
$sql= "select  id_usuario, nom_usuario, email
       from derep_usuario
       where login = '$usr'";

$resultado = mysqlexecuta($conexao,$sql);

$rs_row = mysql_fetch_array($resultado);

$id_analista    =  $rs_row["id_usuario"];
$nome_analista  =  $rs_row["nom_usuario"];
$email_analista =  $rs_row["email"];


mysql_free_result($resultado);

//seleciona tipo de situacao de uma acao
$sql= "SELECT co_situacao, no_situacao
       FROM pro_v2_tipo_situacao_acao
       ORDER BY co_situacao";

$resultado = mysqlexecuta($conexao,$sql);

$qtd_situacao = mysql_num_rows($resultado);

$arr_id_situacao = array();
$arr_no_situacao = array();

for($i=0; $i<$qtd_situacao; $i++) {
    $arr_id_situacao["$i"] = mysql_result($resultado, $i, "co_situacao");
    $arr_no_situacao["$i"] = mysql_result($resultado, $i, "no_situacao");
}

mysql_free_result($resultado);


//seleciona tipo de solucao de uma acao
$sql= "SELECT co_tipo_solucao, no_tipo_solucao
       FROM pro_v2_tipo_solucao
       ORDER BY co_tipo_solucao";

$resultado = mysqlexecuta($conexao,$sql);

$qtd_solucao = mysql_num_rows($resultado);

$arr_id_solucao = array();
$arr_no_solucao = array();

for($i=0; $i<$qtd_solucao; $i++) {
    $arr_id_solucao["$i"] = mysql_result($resultado, $i, "co_tipo_solucao");
    $arr_no_solucao["$i"] = mysql_result($resultado, $i, "no_tipo_solucao");
}
mysql_free_result($resultado);

//seleciona a situacao atual do problema
$sql = "SELECT  desc_problema
        FROM    pro_v2_problema
        WHERE   co_problema = $nu_pro and an_problema = $ano_pro";
$resultado		 = mysqlexecuta($conexao,$sql);
$problema    	 = mysql_fetch_array($resultado);
$tx_descricao    = preg_replace("<br />", "",$problema["desc_problema"]);

mysql_free_result($resultado);
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Registro de Problema - SDP v2.0</title>
<link rel="stylesheet" type="text/css" href="../geral_problema.css">
<link type="text/css" rel="stylesheet" href="../dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen">
<SCRIPT type="text/javascript" src="../dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
<SCRIPT src="../isEmpty.js" type="text/JavaScript"></script>
<script src="../jquery.js" type="text/javascript"></script>
<script src="../jquery.maskedinput.js" type="text/javascript"></script>
<script src="../valida_problema.js" type="text/JavaScript"></script>
<script src="js/valida_acao.js" type="text/JavaScript"></script>


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
<form name="form" method="POST" action="scp_gravar_acao_problema.php" >

<div class="tudo">

   <div class="topo">
        <div class="figura_normal"></div>
        <div class="h1">Registro de Ação</div>
   </div>

   <div class="identificacao">
		Bem-vindo(a), <b><?php echo $nome_analista; ?></b><input type="hidden" name="nome_analista" value="<?php echo $nome_analista ?>"><br>
		E-mail: <?php echo $email_analista; ?><input type="hidden" name="email_analista" value="<?php echo $email_analista ?>"><br>
  		Data: <?php echo $display_data; ?>
  </div>

  <div class="corpo_problema">
  
         <!-- PROBLEMA SECIONADO -->
         <table cellpadding="2" cellspacing="0" width="640" border="1">
             <tr><td class="topico" align = "left"><?php echo "Código do problema: " . $nu_pro . "/" . $ano_pro ;?></td>
             </tr>
       </table>
        <table cellpadding="2" cellspacing="0" width="640" border="0">
             </tr>
                   <td class = 'destaque'>Descrição:
                        <br><textarea type="text" readonly  name="tx_descricao" rows="5"><?php echo $tx_descricao; ?></textarea></td>
             </tr>
       </table>
  
        <br>

       <input type="hidden" name="id_analista" value="<?php echo $id_analista; ?>">
       <input type="hidden" name="co_problema" value="<?php echo $nu_pro; ?>">
       <input type="hidden" name="an_problema" value="<?php echo $ano_pro; ?>">
       
       
       <table cellpadding="2" cellspacing="0" width="640" border="0">
             <tr><td class="topico">Descrição</td></tr>
       </table>
       <br>
       <table cellpadding="2" cellspacing="0" width="400" border="0">
             <tr>
                 <td>Situação da Ação: </td>
                 <td>
                   <select name="co_situacao">
                       <option id="none" value="0">Selecione... </option>
                       <?php
                          for($i=0; $i<$qtd_situacao; $i++) { ?>
    	            	       <option  value="<?php echo $arr_id_situacao[$i]; ?>" >
                                    <?php echo $arr_no_situacao[$i]; ?></option>
                                    <?php } ?>
                      </select>
                  </td>
             </tr>
             <tr>
                 <td>Tipo Solução da Ação: </td>
                 <td>
                   <select name="co_solucao">
                       <option id="none" value="0">Selecione... </option>
                       <?php
                          for($i=0; $i<$qtd_solucao; $i++) { ?>
    	            	       <option  value="<?php echo $arr_id_solucao[$i]; ?>" >
                                    <?php echo $arr_no_solucao[$i]; ?></option>
                                    <?php } ?>
                      </select>
                  </td>
             </tr>
       </table>

        <table cellpadding="2" cellspacing="0" width="400" border="0">
             </tr>
                   <td>Descrição:*
                   		<span class="contador"></span>
                        <br><textarea rows="5" name="tx_descricao" cols="5" title ="Descrição da ação."></textarea></td>
             </tr>
        </table>

        <table cellpadding="2" cellspacing="0" width="600" border="0">
             <tr>
                 <td >Responsável pela ação: </td>
                 <td>
                    <input type="text" name="no_responsavel" value="" maxlength="70" size="60">
                 </td>
             </tr>
             <tr>
                 <td >Órgão responsável ação: </td>
                 <td>
                    <input type="text" name="no_orgao" value="" maxlength="5" size="60">
                 </td>
             </tr>
             
             <tr>
              <td>Data Estimada Ação:</td>
              <td>
                  <input type="text" readonly name="dt_estimada_acao" title = "Data estimada de finalização da ação" maxLength=10 size="10">
                  <input type="button" name="cal1" value="..." onclick="displayCalendar(document.forms[0].dt_estimada_acao,'dd/mm/yyyy',this)">
              </td>
             </tr>
             
             <tr>
              <td>Data Fim Ação:</td>
              <td>
                  <input type="text" readonly name="dt_fim_acao" title = "Data de finalização da ação" maxLength=10 size="10">
                  <input type="button" name="cal1" value="..." onclick="displayCalendar(document.forms[0].dt_fim_acao,'dd/mm/yyyy',this)">
              </td>
             </tr>
            
             <tr>
              <td>Ação Conlusiva?:</td>
              <td>
                  <input type="checkbox" name="chr_acao_conclusiva" value="Sim"> Sim<br>
              </td>
             </tr>
       </table>

       <br>
       
       <table cellpadding="0" cellspacing="0" width="640" border="0">
           <tr>
               <td align="center">
                    <input type="button" value="Salvar" name="Salvar" onclick="validaData(document.forms[0].dt_estimada_acao.value,document.forms[0].dt_fim_acao.value,'A Data Fim Ação deve ser maior ou igual a Data Estimada Ação.');">
                    <input type="button" value="Cancelar" name="Cancel" onclick="javascript:history.back()">
               </td>
           </tr>
       </table>
  </div>
</div>
</form>
</body>
</html>
<?php

//FECHAR CONEXÃO COM O BANCO
mysql_close($conexao);
?>

