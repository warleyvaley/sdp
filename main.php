<!-- **********************
  Equipe OpenView
  Desenvolvido por: André Luis de Almeida
  Março de 2011
********************** -->
<html>
 <head>

<?php
include "mysqlconecta_dbbit.php";
include "mysqlexecuta_pro.php";
include "config.php";

echo "<script type=\"text/javascript\">
     function AbrirDocumento(codigo,ano) {
   	   window.open('problema_visao.php?codigo='+codigo+'&ano='+ano,'','width=700,left=300,top=50,height=700,resizable=YES,Scrollbars=YES');
     }
     </script>";



//error_reporting(E_ALL);
//ini_set('display_errors','On');


$display_data =  date("d") ."/". date("m") ."/". date("Y");
$usuario = $_SERVER["REMOTE_USER"];

list($dominio,$usr) = explode ("[\]", $usuario);

//Codigo que seleciona os dados dos analista 28062009
$sql= "select  id_usuario, nom_usuario, email, usr_co_equipe
       from derep_usuario
       where login = '$usr'";

$resultado = mysqlexecuta($conexao,$sql);
$rs_row = mysql_fetch_array($resultado);

if (mysql_num_rows($resultado) == 0 ) {
	echo "<span style='color:red;margin-left:30%'> Usuário não possui permissão para acesso ao sistema. </span>";
	exit();
}

$id_analista    =  $rs_row["id_usuario"];
$nome_analista  =  $rs_row["nom_usuario"];
$email_analista =  $rs_row["email"];
$cod_equipe 	=  $rs_row["usr_co_equipe"];


//echo $usr;
//echo $email_analista;

//session_start();
if ( isset($_GET["cod_equipe"])) {
	$cod_equipe = $_GET["cod_equipe"];
} else {

	if($cod_equipe == 1) {	
		$selectedAC = "selected";
		$selectedSP = "";
	} else {
		$selectedAC = "";
		$selectedSP = "selected";
	}
}

mysql_free_result($resultado);

//seleciona problemas cadastrados
$sql= "SELECT tb1.co_problema, tb1.an_problema, tb1.dt_fim_problema,
              tb1.dt_modificacao_problema,tb1.desc_problema, tb1.co_impacto,
              tb1.chr_causa_raiz, tb1.co_servico_afetado, tb3.ser_no_servico,
              tb1.co_supervisor, tb4.nom_usuario, tb1.desc_observacao_problema,
              tb1.co_categoria, tb7.no_situacao,tb5.no_categoria, tb8.sig_gerencia
       FROM   pro_v2_problema tb1
       INNER JOIN pro_v2_impacto tb2 ON tb1.co_impacto = tb2.co_impacto
       INNER JOIN derep_servico tb3  ON tb1.co_servico_afetado = tb3.ser_co_servico
       INNER JOIN derep_usuario  tb4 ON tb1.co_supervisor = tb4.id_usuario
       LEFT  JOIN derep_gerencia  tb8 ON tb8.id_gerencia = tb4.derep_gerencia_id_gerencia
       INNER JOIN pro_v2_categoria tb5 ON tb1.co_categoria = tb5.co_categoria
       INNER JOIN pro_v2_problema_situacao tb6 ON tb1.co_problema = tb6.co_problema AND tb1.an_problema = tb6.an_problema AND tb6.ativo = 1
       INNER JOIN pro_v2_tipo_situacao_problema tb7 ON tb7.co_situacao = tb6.co_tipo_situacao_problema
		
       WHERE tb7.co_situacao not in (7,8,9)" ;
       

//echo $sql; 
 //exit();
$resultado = mysqlexecuta($conexao,$sql);

$qtd_problema = mysql_num_rows($resultado);

$arr_co_problema = array();
$arr_an_problema = array();
$arr_desc_problema = array();
$arr_nom_usuario = array();
$arr_no_categoria = array();
$arr_no_situacao = array();
$arr_ser_no_servico = array();


for($i=0; $i<$qtd_problema; $i++) {
    $arr_co_problema["$i"] = mysql_result($resultado, $i, "tb1.co_problema");
    $arr_an_problema["$i"] = mysql_result($resultado, $i, "tb1.an_problema");
    $arr_desc_problema["$i"] = mysql_result($resultado, $i, "tb1.desc_problema");
    $arr_nom_usuario["$i"] = mysql_result($resultado, $i, "tb4.nom_usuario");
    $arr_no_categoria["$i"] = mysql_result($resultado, $i, "tb5.no_categoria");
    //$arr_no_categoria["$i"] = "";
    $arr_no_situacao["$i"] = mysql_result($resultado, $i, "tb7.no_situacao");
    $arr_ser_no_servico["$i"] = mysql_result($resultado, $i, "tb3.ser_no_servico");
    $arr_sig_gerencia["$i"] = mysql_result($resultado, $i, "tb8.sig_gerencia");
    
}

mysql_free_result($resultado);
?>
    <link rel="stylesheet" href="skin.css">  
    <style type="text/css"> 
    		@import url("menu/skin-xp-extended.css");
    		
    		 body{
  					padding:3em 0 0 0;
  					background:url(foo) fixed;
  					margin: 10px auto;
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
  				width: 101%;
 			}
    </style>
    <script type="text/javascript" src="js/menuControl.js"></script>	
    <script type="text/javascript" src="menu/hmenu.js"></script>	
     
 </head>
 <body onload="DynarchMenu.setup('menu', { electric: 250 });"  style='background:#F0F0F0'>
 
 <?php include_once 'menuDrop.php';  ?>
   
 <form method="GET">
 <table border="0" cellspacing="0" cellpadding="0" style="width:96%;margin-left:2%" >
    <tr>
       <td class="topinfo" width="70%">Problemas cadastrados - <span style="font-size: 10px;" > Total: <span style='color:red'><?php echo $qtd_problema; ?></span> </span></td>
   	   <td valign="baseline"  width="24%">					
				   Equipe de Problemas:	
				   <select name="cod_equipe" >
				   		<option value="1" <?php echo $selectedAC; ?>>CCD/AC</option>
				   		<option value="2" <?php echo $selectedSP; ?>>CCD/SP</option>
				   </select>		   	   		
			   	   <input type="submit" value="Atualizar Painel" >
		</td>
   </tr>
 </table>
 </form>
   
 <table border="0" cellspacing="1" cellpadding="4" class="tabletopinfo" style="width:96%;margin-left:2%" >
   <tr>
      <td class="tableDem"  ><nobr> Nr. Problema/Ano</nobr></td>
      <td class="tableDem"  ><nobr> Serviço Afetado </nobr></td>
      <td class="tableDem"  ><nobr> Descrição Problema </nobr></td>
      <td class="tableDem"  ><nobr> Nome Responsável </nobr></td>
      <td class="tableDem"  ><nobr> Gerência </nobr></td>
      <td class="tableDem"  ><nobr> Categoria </nobr></td>
      <td class="tableDem"  ><nobr> Situação </nobr></td>
   </tr>
   
   <?php
         //controle linha
         $i = 0;
         $stilo = "rowdem";
         //Exibe as linhas encontradas na consulta
         for($j=0; $j<$qtd_problema; $j++) {
    ?>
   
    <tr class="rowselected" >
      <td class="<?php echo $stilo ?>" );"><nobr><a alt="clique para visualizar problema" title="clique para visualizar problema" style="text-decoration:underline" href="#" onclick="javascript:AbrirDocumento('<?php echo $arr_co_problema[$j]; ?>',' <?php echo $arr_an_problema[$j]; ?>')"> <?php echo str_pad($arr_co_problema[$j],4,"0",STR_PAD_LEFT)."/".$arr_an_problema[$j]; ?></a></nobr></td>
      <td class="<?php echo $stilo ?>" );"><nobr> <?php echo $arr_ser_no_servico[$j]; ?></nobr></td>
      <td class="<?php echo $stilo ?>" );"><nobr> <span alt="<?php echo strip_tags($arr_desc_problema[$j]); ?>" title="<?php echo strip_tags($arr_desc_problema[$j]); ?>"> <?php if(strlen($arr_desc_problema[$j]) > 60) {echo strip_tags(substr($arr_desc_problema[$j], 0, 45)) ."..."; } else {echo strip_tags($arr_desc_problema[$j]);}  ?> </span></nobr></td>
      <td class="<?php echo $stilo ?>" );"><nobr> <?php echo $arr_nom_usuario[$j]; ?> </nobr></td>
      <td class="<?php echo $stilo ?>" );"><nobr> <?php echo $arr_sig_gerencia[$j]; ?> </nobr></td>
      <td class="<?php echo $stilo ?>" );"><nobr> <?php echo $arr_no_categoria[$j]; ?> </nobr></td>
      <td class="<?php echo $stilo ?>" );"><nobr> <?php echo $arr_no_situacao[$j]; ?></nobr></td>
    </tr>
<?php
      if ($i == 0) {
       $stilo = "rowdem2";
       $i = 1;
      } else {
        $stilo = "rowdem";
        $i = 0;
      }

 } //fim for
?>
 </table> 
 </body>
</html>