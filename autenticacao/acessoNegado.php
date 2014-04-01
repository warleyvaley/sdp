<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<title>Acesso não permitido</title>
<link rel="stylesheet" type="text/css" href="../geral_problema.css">

    <style type="text/css"> 
    		@import url("../menu/skin-xp-extended.css");
    		
    		 body{
  					padding:3em 0 0 0;
  					background:url(foo) fixed;
  					
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
<div class="tudo">
  <div class="topo">
     <div class="figura_atencao"></div>
     <div  style="margin-left:50%;" >ATENÇÃO</div>
  </div>
  <div style="margin-left:37%;">
    <p>O acesso a este recurso do sistema não é permitido para o seu perfil.</p>
  </div>
</div>
</body>
</html>