<?php
include "../mysqlconecta_dbbit.php";
include "../mysqlexecuta_pro.php";


//se � retorno, inicia a captura dos par�metros

//$periodo = $_GET["periodo"];
$de      = $_GET["de"];
$ate     = $_GET["ate"];
$situacao= $_GET["situacao"];
//echo $situacao;exit;
//seleciona as situa��es dispon�veis

$sql = "SELECT co_situacao, no_situacao
        FROM pro_v2_tipo_situacao_problema";

$resultado = mysqlexecuta($conexao,$sql);
$qtd_situacao = mysql_num_rows($resultado);

$arr_co_situacao = array();
$arr_no_situacao = array();

for($i=0; $i<$qtd_situacao; $i++) {
    $arr_co_situacao["$i"] = mysql_result($resultado, $i, "co_situacao");
    $arr_no_situacao["$i"] = mysql_result($resultado, $i, "no_situacao");
}

mysql_free_result($resultado);



?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<title>Pesquisa de Problemas</title>
<link rel="stylesheet" type="text/css" href="../geral_problema.css">
<link type="text/css" rel="stylesheet" href="../dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen">
<SCRIPT type="text/javascript" src="../dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
<script src="../jquery.js" type="text/javascript"></script>
<script src="../jquery.maskedinput.js" type="text/javascript"></script>

<script type="text/javascript">

function checkform(){

        if ((form.periodo_inicial.value == '') && (form.cmb_situacao.value == "0"))  {
           alert("Por favor, informe a data inicial!");
           form.cal1.focus();
           return (false);
        }
        if ((form.periodo_final.value == '' ) && (form.cmb_situacao.value == "0")) {
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
<form name="form" method="POST" action="form_resultado_pesquisa_problema.php" onsubmit="return checkform()">
<div class="tudo">
     <div class="topo">
          <div class="figura_normal"></div>
          <div class="h1">Pesquisa de Problemas</div>
     </div>
     <div class="comum">
          <ul>
          
          
               <li>Por Situa��o:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                   <select name="cmb_situacao">
                       <?php
                          for($i=0; $i<$qtd_situacao; $i++) { ?>
    	            	       <option  value="<?php echo $arr_co_situacao["$i"]; ?>"
                                    <?php if ($arr_co_situacao["$i"] == $situacao) echo selected;?>>
                                    <?php echo $arr_no_situacao["$i"]; ?></option>
                                    <?php } ?>
                                <option value="0" <?php if ($situacao == 0) echo selected;?>>Todos</option>
                      </select>
               </li>
               
               
               <li>Registrado entre:&nbsp;
                       <input type="text" name="periodo_inicial"  maxlength ="10" size="10" value="<?php if ($de <> "")  echo "$de"; ?>" readonly>
                       <input type="button" name="cal1" value="..." onclick="displayCalendar(document.forms[0].periodo_inicial,'dd/mm/yyyy',this)">&nbsp;&nbsp;
                       e &nbsp;<input type="text" name="periodo_final"  maxlength ="10" size="10" value="<?php if ($ate <> "")  echo "$ate"; ?>" readonly>
                       <input type="button" name="cal2" value="..." onclick="displayCalendar(document.forms[0].periodo_final,'dd/mm/yyyy',this)">&nbsp;&nbsp;
               </li>

          </ul>
          <p align="center"><input type="submit" value="Pesquisar" name="Pesquisar"></p>
          <!-- <p align="center"><a href="problema.htm">Voltar ao Menu</a></p> -->
    </div>
</div>
</form>
</body>
</html>
<?php

//FECHAR CONEX�O COM O BANCO
mysql_close($conexao);
?>


