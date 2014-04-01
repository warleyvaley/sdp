<?php
include "../mysqlconecta_dbbit.php";
include "../mysqlexecuta_pro.php";
include "../config.php";

$codigo = $_GET['nu'];
$ano    = $_GET['ano'];

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<title>Gravou OK Problema</title>
<link rel="stylesheet" type="text/css" href="../geral_problema.css">
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
<div class="tudo">
    <div class="topo">
         <div class="figura_normal"></div>
         <div class="h1">Registro de Problema</div>
    </div>
   <div class="comum">
       <p>O registro foi gravado/atualizado com sucesso!</p>
       <p></p>
       <p> Para cadastrar uma ação para o problema <font color='red'> <?php echo $codigo."/".$ano ?>  </font>  : <a href="form_acao_problema.php?nu=<?php echo $codigo ?>&ano=<?php echo $ano ?>" > Cadastrar Ação </a></p>      
   </div>
</div>
</body>
</html>
