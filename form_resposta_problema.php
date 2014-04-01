<?php
include "../mysqlconecta_dbbit.php";
include "../mysqlexecuta_pro.php";


$usuario = $_SERVER["REMOTE_USER"];

list($dominio,$usr) = split ("[\]", $usuario);

//Codigo que seleciona os dados dos analista
$sql= "select  id_usuario, nom_usuario, email
       from derep_usuario
       where login = '$usr'";

$resultado = mysqlexecuta($conexao,$sql);

$rs_row = mysql_fetch_array($resultado);

$id_analista    =  $rs_row["id_usuario"];
$nome_analista  =  $rs_row["nom_usuario"];
$email_analista =  $rs_row["email"];

//controle
$control = $_GET['contrl'];


mysql_free_result($resultado);

$display_data =  date("d") ."/". date("m") ."/". date("Y");

//pega o código da acao que vieram da URL
$nu_acao     = $_GET["coa"];

//pega o código do problema que vieram da URL
$nu_pro     = $_GET["cop"];
$ano_pro    = $_GET["anp"];




//seleciona o registro do problema
$sql = "SELECT desc_acao, dt_fim_acao
        FROM   pro_v2_acao
        WHERE  co_acao=$nu_acao";

$res = mysqlexecuta($conexao,$sql);

$acao    = mysql_fetch_array($res);

mysql_free_result($res);
//formata os dados do registro

$tx_descricao = ereg_replace("<br />", "",$acao["desc_acao"]);

//formata as datas do fim da acao
if ($acao['dt_fim_acao'] <> '0000-00-00'){
   $arr_dt_fim_acao = explode("-",$acao['dt_fim_acao']);
   $dt_fim_acao     = $arr_dt_fim_acao[2]."/".$arr_dt_fim_acao[1]."/".$arr_dt_fim_acao[0];
} else{
      $dt_fim_acao = null;
}


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
<!--<script src="../valida_problema.js" type="text/JavaScript"></script>-->
<script src="js/valida_acao.js" type="text/JavaScript"></script>
 
 <script type="text/javascript">
window.onresize = function() 
{
    window.resizeTo(780,780);
}
window.onclick = function() 
{
    window.resizeTo(780,780);
}
</script>
 
</head>
<body>
<form name="form" method="POST" action="scp_gravar_resposta.php" >
<div class="tudo">
   <div class="topo">
        <div class="figura_normal"></div>
        <div class="h1">Registro de Resposta de Ação</div>
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
       <input type="hidden" name="co_acao" size=6 value="<?php echo $nu_acao;?>" >
       <input type="hidden" name="control" size=6 value="<?php echo $control;?>" >
      
       <table cellpadding="2" cellspacing="0" width="640" border="1">
             <tr><td class="topico" align = "left"><?php echo "Código do problema: " . $nu_pro . "/" . $ano_pro ;?></td>
             </tr>
       </table>
       <table cellpadding="2" cellspacing="0" width="640" border="0">
             <tr>
                   <td class = 'destaque'>Descrição Ação:
                        <br><textarea type="text" readonly  name="tx_descricao" rows="5"><?php echo $tx_descricao; ?></textarea></td>
             </tr>
       </table>
       <br>
        <table cellpadding="2" cellspacing="0" width="400" border="0">
             </tr>
                   <td>Descrição resposta:*
                   		<span class="contador"></span>
                        <br><textarea rows="5" name="tx_descricao" cols="5" title ="Descrição da resposta."></textarea></td>
             </tr>
        </table>
        <table cellpadding="2" cellspacing="0" width="400" border="0">
             <tr>
              <td>Data Resposta:</td>
               <td>
                  <input type="text" readonly value="" name="dt_resposta" title = "Data de resposta da ação" maxLength=10 size="10">
                  <input type="button" name="cal1" value="..." onclick="displayCalendar(document.forms[0].dt_resposta,'dd/mm/yyyy',this)">
               </td>
             </tr>
        </table>
       <br>
       
       <table cellpadding="0" cellspacing="0" width="640" border="0">
           <tr>
               <td align="center">
               
                   <?php
                     //verifica se a janela solicitada é de popup ou janela normal e cria os
                     // botões conforme a solicitação
                     
                     if (isset($_GET[contrl])) { ?>
                     
                        <input type="button" value="Salvar" name="Salvar" onclick="validaData2('<?php echo $dt_fim_acao; ?>',document.forms[0].dt_resposta.value,'A data da resposta da ação não pode ser menor que a data fim da ação.')">
                        <input type="button" value="Fechar" name="Fechar" onclick="javascript:window.close();">

                    <?php } else { ?>

                        <input type="button" value="Salvar" name="Salvar" onclick="validaData2('<?php echo $dt_fim_acao; ?>,document.forms[0].dt_resposta.value,'A data da resposta da ação não pode ser menor que a data fim da ação.')">
                        <input type="button" value="Cancelar" name="Cancel" onclick="javascript:history.back();">

                   <?php  }
                   ?>

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
