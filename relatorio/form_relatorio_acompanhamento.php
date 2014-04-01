<HTML>
<HEAD>
<title>Pesquisa de Problemas</title>
<link rel="stylesheet" type="text/css" href="../../geral_problema.css">
<link type="text/css" rel="stylesheet" href="../../dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen">
<SCRIPT type="text/javascript" src="../../dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
<script src="../../jquery.js" type="text/javascript"></script>
<script src="../../jquery.maskedinput.js" type="text/javascript"></script>

<?php 
require_once "funcoes_relatorio_analitico.php";

error_reporting(E_ALL);
ini_set('display_errors', '1');

?>

<script type="text/javascript">

$(function() {
	$('input[@name=ano]').mask('9999');
	});


function checkform(){

    if ((form.ano.value == ''))  {
       alert("Por favor, informe o ANO inicial!");
       form.ano.focus();
       return (false);
    }
    
    return (true);
}


</SCRIPT>


   <link rel="stylesheet" href="../skin.css">  
  <style type="text/css"> 
    		@import url("../menu/skin-xp-extended.css");
    		
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
      _dynarch_menu_url = "../menu/";
    </script>
    <script type="text/javascript" src="../js/menuControl.js"></script>	
     <script type="text/javascript" src="../menu/hmenu.js"></script>	
</head>

<body onload="DynarchMenu.setup('menu', { electric: 250 });">

<?php include_once '../menuDrop.php';  ?>

<form name="form" method="POST" action="scp_resultado_acompanhamento.php"  onsubmit="return checkform();">
<div class="tudo">
     <div class="topo">
          <div class="figura_normal"></div>
          <div class="h1">Acompanhamento do Processo Gerir Problema</div>
     </div>
     <div class="comum">
          <table border="0"> 
          
          	  <tr>	
          	   <td>
               		Equipe de Problemas:
               </td>
               <td>		
               		<select name="cod_equipe" >
				   		<option value="1" >CCD/AC</option>
				   		<option value="2" >CCD/SP</option>
				   </select>	
               </td>
          	  </tr>	
          	  
              <tr>                        
               <td>Período:</td>
               <td>
                      <select name="mes" ><?php getMes(); ?></select> de <input name="ano" type="text" maxlength="4" size="10">
               </td>               
             </tr>  
               
               
              
          </table>
          <p align="center"><input type="submit" value="Pesquisar" name="Pesquisar"></p>
          <!-- <p align="center"><a href="../problema.htm">Voltar ao Menu</a></p> -->
    </div>
</div>
</form>
</body>
</html>