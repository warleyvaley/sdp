<?php
include "../mysqlconecta_dbbit.php";
include "../mysqlexecuta_pro.php";

 /*error_reporting(E_ALL);
 ini_set("display_errors", 1);*/


//pega o código do problema que vieram da URL
$nu_pro     = $_GET["cop"];
$ano_pro    = $_GET["anp"];
$nu_acao    = $_GET["coa"];

$display_data =  date("d") ."/". date("m") ."/". date("Y");

$usuario = $_SERVER["REMOTE_USER"];

list($dominio,$usr) = split("[\]", $usuario);

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


$resultado = mysqlexecuta($conexao,$sql);

$problema    = mysql_fetch_array($resultado);
$tx_descricao        = ereg_replace("<br />", "",$problema["desc_problema"]);

mysql_free_result($resultado);

//recupera os dados da acao
$sql = "SELECT  desc_acao, no_responsavel, no_orgao_responsavel, dt_cadastro_acao, dt_fim_acao, dt_estimado_acao, co_situacao,
                co_solucao, chr_conclusiva
        FROM    pro_v2_acao
        WHERE   co_acao = $nu_acao";

$resultado = mysqlexecuta($conexao,$sql);

$acao    = mysql_fetch_array($resultado);
mysql_free_result($resultado);


//formata as datas
if ($acao['dt_fim_acao'] <> '0000-00-00'){
   $arr_dt_fim_acao = explode("-",$acao['dt_fim_acao']);
   $dt_fim_acao     = $arr_dt_fim_acao[2]."/".$arr_dt_fim_acao[1]."/".$arr_dt_fim_acao[0];
} else{
      $dt_fim_acao = null;
}

//formata as datas
if ($acao['dt_estimado_acao'] <> '0000-00-00'){
   $arr_dt_estimado_acao = explode("-",$acao['dt_estimado_acao']);
   $dt_estimado_acao     = $arr_dt_estimado_acao[2]."/".$arr_dt_estimado_acao[1]."/".$arr_dt_estimado_acao[0];
} else{
      $dt_estimado_acao = null;
}


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
<form name="form" method="POST" action="scp_gravar_alteracao_acao_problema.php" >

<div class="tudo">

   <div class="topo">
        <div class="figura_normal"></div>
        <div class="h1">Alterar Registro de Ação</div>
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
       
       <input type="hidden" name="co_acao" value="<?php echo $nu_acao; ?>">
       
       
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
    	            	       <option  value="<?php echo $arr_id_situacao[$i]; ?>"
                                    <?php if ($arr_id_situacao[$i] == $acao['co_situacao']) echo 'selected="selected"';?>>
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
    	            	       <option  value="<?php echo $arr_id_solucao[$i]; ?>"
                                    <?php if ($arr_id_solucao[$i] == $acao['co_solucao']) echo 'selected="selected"';?>>
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
                        <br><textarea rows="5" name="tx_descricao" cols="5" title ="Descrição da ação."><?php echo ereg_replace("<br />", "",$acao['desc_acao']);  ?></textarea></td>
             </tr>
        </table>

        <table cellpadding="2" cellspacing="0" width="600" border="0">
             <tr>
                 <td >Responsável pela ação: </td>
                 <td>
                    <input type="text" name="no_responsavel" value="<?php echo $acao['no_responsavel'];?>" maxlength="70" size="60">
                 </td>
             </tr>
             <tr>
                 <td >Órgão responsável ação: </td>
                 <td>
                    <input type="text" name="no_orgao" value="<?php echo $acao['no_orgao_responsavel'];?>" maxlength="5" size="60">
                 </td>
             </tr>
             
             <tr>
              <td>Data Estimada Ação:</td>
              <td>
                  <input type="text" readonly value="<?php echo $dt_estimado_acao ?>" name="dt_estimada_acao" title = "Data estimada de finalização da ação" maxLength=10 size="10">
                  <input type="button" name="cal1" value="..." onclick="displayCalendar(document.forms[0].dt_estimada_acao,'dd/mm/yyyy',this)">
              </td>
             </tr>
             
             <tr>
              <td>Data Fim Ação:</td>
              <td>
                  <input type="text" readonly value="<?php echo $dt_fim_acao ?>" name="dt_fim_acao" title = "Data de finalização da ação" maxLength=10 size="10">
                  <input type="button" name="cal1" value="..." onclick="displayCalendar(document.forms[0].dt_fim_acao,'dd/mm/yyyy',this)">
              </td>
             </tr>
             
             <tr>
              <td>Ação Conlusiva?:</td>
              <td>
                  <?php if ($acao['chr_conclusiva'] == "Sim") { ?>
                           <input type="checkbox" name="chr_acao_conclusiva" value="Sim" checked > Sim<br>
                  <?php } else { ?>
                           <input type="checkbox" name="chr_acao_conclusiva" value="Sim"> Sim<br>
                  <?php } ?>
              </td>
             </tr>
       </table>
       
       <br><br>
       <!-- RECUPERA AS RESPOSTAS CADASTRADAS PARA A AÇÃO -->

<?php
       
$sql= "SELECT co_resposta, desc_resposta, DATE_FORMAT(dt_resposta,'%d/%m/%Y') as dt_resposta
       FROM pro_v2_resposta_acao
       WHERE co_acao = $nu_acao
       ORDER BY co_resposta";

$resultado = mysqlexecuta($conexao,$sql);

$qtd_resposta = mysql_num_rows($resultado);

$arr_id_resposta   = array();
$arr_desc_resposta = array();
$arr_dt_resposta   = array();

for($i=0; $i<$qtd_resposta; $i++) {
    $arr_id_resposta["$i"]     = mysql_result($resultado, $i, "co_resposta");
    $arr_desc_resposta["$i"]   = mysql_result($resultado, $i, "desc_resposta");
    $arr_dt_resposta["$i"]     = mysql_result($resultado, $i, "dt_resposta");
}

mysql_free_result($resultado);
?>

      <!-- ADICIONAR RESPOSTA -->
      <div> <img onclick="novaJanela('form_resposta_problema.php?contrl=1&coa=<?php echo $nu_acao; ?>&cop=<?php echo $nu_pro;?>&anp=<?php echo $ano_pro; ?>','Cadastro Resposta',680,580,'no');return false;" style="cursor:pointer;vertical-align:middle;" src="plus.png" width=30 height=30 alt="Adicionar resposta" title="Adicionar resposta" ><a style="cursor:pointer" onclick="novaJanela('form_resposta_problema.php?contrl=1&coa=<?php echo $nu_acao; ?>&cop=<?php echo $nu_pro;?>&anp=<?php echo $ano_pro; ?>','Cadastro Resposta',680,600,'no');return false;">Adicionar Nova Resposta</a></div>
      <!-- #################-->
      
      <table cellpadding="2" cellspacing="0" width="620" border="0">
         <tr align="center" style="background:#E6E6FA;font-weight:bold;">
            <td nowrap width='80'>Número</td>
            <td nowrap width='90'>Data Resposta</td>
            <td nowrap width='160' align='center'>Resposta</td>
            <td nowrap width='50'>Alterar</td>
         </tr>
       
       
            <?php

               if ($qtd_resposta > 0) {
                for($i=0; $i<$qtd_resposta; $i++) {
            ?>
            
              <tr align="center" >

               <td nowrap style="border-bottom:1px solid black;"> <?php
                   $format = '%1$03d';
                   echo sprintf($format, $i + 1);
               ?> </td>
				
			   <?php 
			   
			   		//remove tags html
                	$descricao =  strip_tags($arr_desc_resposta["$i"]);
			   ?>		
               <td nowrap style="border-bottom:1px solid black;"> <?php echo $arr_dt_resposta["$i"]; ?>   </td>
               <td nowrap align="left" style="border-bottom:1px solid black;" title="<?php echo $descricao; ?>"> <?php if(strlen($descricao) > 40) {echo substr($descricao, 0, 45) ."..."; } else {echo $descricao;}  ?> </td>
               <td nowrap style="border-bottom:1px solid black;">
                             <img onclick="location.href='scp_altera_resposta_problema.php?cop=<?php echo $nu_pro;?>&anp=<?php echo $ano_pro;?>&coa=<?php echo $nu_acao; ?>&resp=<?php echo $arr_id_resposta["$i"]; ?>'" style="cursor:pointer" src="altera.png" width=20 height=20 alt="alterar resposta" title="alterar resposta" >
                             <img onclick="excluirRespostaAjax('<?php echo $nu_acao; ?>','<?php echo $arr_id_resposta["$i"]; ?>');" style="cursor:pointer" src="excluir.bmp" width=20 height=20 alt="remover resposta" title="remover resposta">
               </td>
              </tr>
            <?php }

            echo "</table>";
            
            } else { ?>
            
                      <table> <tr> <td style="font-weight:bold"> Não existem respostas cadastradas!</td></tr></table>
                      
            <?php } ?>


       
       <!-- ############################################## -->

       <br>
       
       <table cellpadding="0" cellspacing="0" width="640" border="0">
           <tr>
               <td align="center">
                    <input type="button" value="Salvar" name="Salvar" onclick="validaData(document.forms[0].dt_estimada_acao.value,document.forms[0].dt_fim_acao.value,'A Data Fim Ação deve ser maior ou igual a Data Estimada Ação.');">
                    <input type="button" value="Cancelar" name="Cancel" onclick="javascript:history.back()">
                     <!--<input type="button" value="Voltar ao Menu" name="Menu" onclick="javascript:location.href='problema.htm'">-->

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