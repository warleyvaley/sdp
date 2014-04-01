<HTML>
<HEAD>
<title>Pesquisa de Problemas</title>
<link rel="stylesheet" type="text/css" href="../geral_problema.css">
<link type="text/css" rel="stylesheet" href="../dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen">
<SCRIPT type="text/javascript" src="../dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
<script src="../jquery.js" type="text/javascript"></script>
<script src="../jquery.maskedinput.js" type="text/javascript"></script>

<?php 
error_reporting(E_ALL);
   		  ini_set('display_errors', '1');
   		  
include "../mysqlconecta_dbbit.php";
include "../mysqlexecuta_pro.php";

$de = "";
$ate = "";

$sql = "SELECT pro_co_situacao, pro_no_situacao
        FROM pro_situacao";

$resultado = mysqlexecuta($conexao,$sql);
$qtd_situacao = mysql_num_rows($resultado);

$arr_co_situacao = array();
$arr_no_situacao = array();

for($i=0; $i<$qtd_situacao; $i++) {
    $arr_co_situacao["$i"] = mysql_result($resultado, $i, "pro_co_situacao");
    $arr_no_situacao["$i"] = mysql_result($resultado, $i, "pro_no_situacao");
}

mysql_free_result($resultado);

?>

<script type="text/javascript">

$(function() {
	$('input[@name=cod_problema]').mask('9999/9999');
	});

function checkform(){

        if ((form.periodo_inicial.value == ''))  {
           alert("Por favor, informe a data inicial!");
           form.cal1.focus();
           return (false);
        }
        if ((form.periodo_final.value == '' )) {
           alert("Por favor, informe a data final!");
           form.cal2.focus();
           return (false);
        }
        var data_inicial = (form.periodo_inicial.value.substr(6,4) + form.periodo_inicial.value.substr(3,2) + form.periodo_inicial.value.substr(0,2));
        var data_final   = (form.periodo_final.value.substr(6,4) + form.periodo_final.value.substr(3,2) + form.periodo_final.value.substr(0,2));


        if (data_final < data_inicial) {
           alert ("Data final menor que a data inicial!");
           form.cal1.focus();
           return(false);
        }
        return (true);
 }

</SCRIPT>


</head>
<body>
<form name="form" method="POST" action="scp_resultado_analitico.php" onsubmit="">
<div class="tudo">
     <div class="topo">
          <div class="figura_normal"></div>
          <div class="h1">Relatório Analítico de Problemas</div>
     </div>
     <div class="comum">
          <table border="0">       
          
           	  <tr>	
          	   <td>
               		Código do Problema:
               </td>
               <td>		
               		<input type="text" name="cod_problema"  maxlength ="9" size="9" />
               </td>
          	  </tr>	
          	  
          	  <tr>
               <td>Por Situação:</td>
               <td>
                   <select name="cmb_situacao">
                   		<option value="0" >Todos</option>
                       <?php
                          for($i=0; $i<$qtd_situacao; $i++) { ?>
    	            	       <option  value="<?php echo $arr_co_situacao["$i"]; ?>">
                                    
                                    <?php echo $arr_no_situacao["$i"]; ?></option>
                          <?php } ?>
                                
                      </select>
               </td>
              </tr>
              
              <tr>                        
               <td>Período:</td>
               <td>
                       <input type="text" name="periodo_inicial"  maxlength ="10" size="10" value="<?php if ($de <> "")  echo "$de"; ?>" readonly>
                       <input type="button" name="cal1" value="..." onclick="displayCalendar(document.forms[0].periodo_inicial,'dd/mm/yyyy',this)">&nbsp;&nbsp;
                       e &nbsp;<input type="text" name="periodo_final"  maxlength ="10" size="10" value="<?php if ($ate <> "")  echo "$ate"; ?>" readonly>
                       <input type="button" name="cal2" value="..." onclick="displayCalendar(document.forms[0].periodo_final,'dd/mm/yyyy',this)">&nbsp;&nbsp;
               </td>
               
             </tr>  
               
               
              
          </table>
          <p align="center"><input type="submit" value="Pesquisar" name="Pesquisar"></p>
          <p align="center"><a href="problema.htm">Voltar ao Menu</a></p>
    </div>
</div>
</form>
</body>
</html>
<?php

//FECHAR CONEXÃO COM O BANCO
mysql_close($conexao);
?>


