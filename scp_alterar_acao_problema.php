<?php
include "../mysqlconecta_dbbit.php";
include "../mysqlexecuta_pro.php";

$usuario = $_SERVER["REMOTE_USER"];

list($dominio,$usr) = split("[\]", $usuario);

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

$sql = "SELECT desc_problema
        FROM   pro_v2_problema
        WHERE  co_problema=$nu_pro AND an_problema=$ano_pro";

$res = mysqlexecuta($conexao,$sql);

$problema    = mysql_fetch_array($res);

mysql_free_result($res);
//formata os dados do registro

$tx_descricao = ereg_replace("<br />", "",$problema["desc_problema"]);



//seleciona as acoes do problema
$sql= "SELECT co_acao, desc_acao, DATE_FORMAT(dt_fim_acao,'%d/%m/%Y') as dt_fim_acao, no_situacao, dt_cadastro_acao
       FROM pro_v2_acao a inner join pro_v2_tipo_situacao_acao b on  b.co_situacao = a.co_situacao
       WHERE a.co_problema = $nu_pro AND  a.an_problema = $ano_pro
       ORDER BY dt_cadastro_acao asc";
//echo $sql;

$resultado = mysqlexecuta($conexao,$sql);

$qtd_acao = mysql_num_rows($resultado);

$arr_id_acao = array();
$arr_desc_acao = array();
$arr_dt_acao = array();
$arr_no_sit_acao = array();

for($i=0; $i<$qtd_acao; $i++) {
    $arr_id_acao["$i"] = mysql_result($resultado, $i, "co_acao");
    $arr_desc_acao["$i"] = mysql_result($resultado, $i, "desc_acao");
    $arr_dt_acao["$i"] = mysql_result($resultado, $i, "dt_fim_acao");
    $arr_no_sit_acao["$i"] = mysql_result($resultado, $i, "no_situacao");
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
<form name="form" method="POST" >
<div class="tudo">
   <div class="topo">
        <div class="figura_normal"></div>
        <div class="h1">Alteração de Ações do Problema</div>
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

         <table cellpadding="2" cellspacing="0" width="640" border="1">
             <tr><td class="topico" align = "left"><?php echo "Código do problema: " . $nu_pro . "/" . $ano_pro ;?></td>
             </tr>
       </table>
       <table cellpadding="2" cellspacing="0" width="640" border="0">
             <tr>
                   <td class = 'destaque'>Descrição:*
                        <br><textarea type="text" readonly  name="tx_descricao" rows="5"><?php echo $tx_descricao; ?></textarea></td>
             </tr>
       </table>
       <br>
       
       <!-- ADICIONAR AÇÃO -->
      <div> <img onclick="location.href='form_acao_problema.php?nu=<?php echo $nu_pro; ?>&ano=<?php echo $ano_pro; ?>'" style="cursor:pointer;vertical-align:middle;" src="plus.png" width=30 height=30 alt="Adicionar ação" title="Adicionar ação" ><a style="cursor:pointer" onclick="location.href='form_acao_problema.php?nu=<?php echo $nu_pro; ?>&ano=<?php echo $ano_pro; ?>'">Adicionar Nova Ação</a></div>
      <!-- #################-->
      
       <table cellpadding="2" cellspacing="0" width="620" border="0">
         <tr align="center" style="background:#E6E6FA;font-weight:bold;">
            <td width='60'>Número</td>
            <td width='90'>Data Fim Ação</td>
            <td width='150'>Descrição</td>
            <td width='30'>Status</td>
            <td width='30'>Alterar</td>
         </tr>
         

            <?php for($i=0; $i<$qtd_acao; $i++) { ?>
              <tr align="center" >
              
               <td nowrap style="border-bottom:1px solid black;"> <?php
                   $format = '%1$03d';
                   echo sprintf($format, $i + 1);
               ?> </td>
               
               <td nowrap style="border-bottom:1px solid black;"> <?php echo $arr_dt_acao["$i"]; ?>   </td>
               
               <?php 
	               //remove tags html
                	$descricao =  strip_tags($arr_desc_acao["$i"]); 
               ?>
               <td nowrap align="left" style="border-bottom:1px solid black;" title="<?php echo $descricao; ?>"> <?php if(strlen($descricao) > 40) {echo substr($descricao, 0, 45) ."..."; } else {echo  $descricao;}  ?> </td>
               <td nowrap style="border-bottom:1px solid black;"> <?php echo $arr_no_sit_acao["$i"]; ?>   </td>
               <td nowrap style="border-bottom:1px solid black;">
                             <img onclick="location.href='form_alterar_acao_problema.php?cop=<?php echo $nu_pro;?>&anp=<?php echo $ano_pro;?>&coa=<?php echo $arr_id_acao["$i"]; ?>'" style="cursor:pointer" src="altera.png" width=20 height=20 alt="alterar ação" title="alterar ação" >
                             <img onclick="excluirAcaoAjax('<?php echo $nu_pro;?>','<?php echo $ano_pro;?>','<?php echo $arr_id_acao["$i"]; ?>');" style="cursor:pointer" src="excluir.bmp" width=20 height=20 alt="remover ação" title="remover ação">
               </td>
              </tr>
            <?php } ?>

       </table>
       <br>
       
       <table cellpadding="0" cellspacing="0" width="640" border="0">
           <tr>
               <td align="center">
                   <!--  <input type="button" value="Voltar ao Menu" name="Menu" onclick="javascript:location.href='problema.htm'">-->
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
