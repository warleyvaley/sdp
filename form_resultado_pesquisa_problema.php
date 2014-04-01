
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<title>Resultado da Pesquisa</title>
<link rel="stylesheet" type="text/css" href="../resultado_pesquisa.css">
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
     <div class="comum">
<?php

/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/

include "../mysqlconecta_dbbit.php";
include "../mysqlexecuta_inc.php";


echo "<script type=\"text/javascript\">
     function AbrirDocumento(codigo,ano) {
   	   window.open('problema_visao.php?codigo='+codigo+'&ano='+ano,'','width=700,left=300,top=50,height=700,resizable=YES,Scrollbars=YES');
     }
     </script>";

//$dt_emissao = date("Ymd");
//$dt_hoje    = date("Y")."-".date("m")."-".date("d");

//Limite de busca
//$tamanho_pagina = 20;
//
////examino a página a mostrar e o inicio do registo a mostrar
//$pagina = $_GET["pagina"];
//if (!$pagina) {
//   $inicio = 0;
//   $pagina = 1;
//}
//else {
//   $inicio = ($pagina - 1) * $tamanho_pagina;
//}

//inicio o critério e capturo o valor informado

$criterio = $_GET["criterio"];

if (!$criterio == ""){
       list($dt_inicial,$dt_final,$situacao) = split ("[,]",$criterio);
             $arr_dt_inicial = explode("-",$dt_inicial);
             $dt_i_ori  = $arr_dt_inicial[2]."/".$arr_dt_inicial[1]."/".$arr_dt_inicial[0];
             $arr_dt_final = explode("-",$dt_final);
             $dt_f_ori = $arr_dt_final[2]."/".$arr_dt_final[1]."/".$arr_dt_final[0];
}else {
    $situacao       = $_POST["cmb_situacao"];    //recebe a situação

//    if ($situacao == "0") {
//        $dt_inicial = "01/01/2009";
//        $dt_i_ori   = "01/01/2009";
//        $dt_final   = date("d") ."/". date("m") ."/". date("Y");
//        $dt_f_ori   = date("d") ."/". date("m") ."/". date("Y");
//    } else {
        $dt_inicial     = $_POST["periodo_inicial"];
        $dt_i_ori       = $_POST["periodo_inicial"];
        $dt_final       = $_POST["periodo_final"];
        $dt_f_ori       = $_POST["periodo_final"];
//    }

  //  $dt_i_ori       = $_POST["periodo_inicial"];
  //  $dt_f_ori       = $_POST["periodo_final"];
    $arr_dt_inicial = explode("/",$dt_inicial);
    $dt_inicial     = $arr_dt_inicial[2]."-".$arr_dt_inicial[1]."-".$arr_dt_inicial[0];
    $arr_dt_final   = explode("/",$dt_final);
    $dt_final       = $arr_dt_final[2]."-".$arr_dt_final[1]."-".$arr_dt_final[0];

  }


  
//  echo $situacao;  exit;
if ($situacao <> 0) {
    //seleciona o nome da situação
    $sql = "SELECT no_situacao from pro_v2_tipo_situacao_problema where co_situacao = $situacao";
    $resultado = mysqlexecuta($conexao,$sql);
    $rs_row       = mysql_fetch_row($resultado);
    mysql_free_result($resultado);
    $nome_situacao =  $rs_row["0"];
    $sql_situacao =  " and c.co_situacao = $situacao ";
} else {
    $sql_situacao = " ";
    $nome_situacao = "Todos";
}

  
     $criterio  = $dt_inicial.",".$dt_final;

   //  if ($situacao <> "Todos") {
         $criterio  = $criterio.",".$situacao;
//         $filtro_situacao = "  inc_in_status = '$situacao' and ";
  //   } else {
  //       $criterio  = $criterio.","."Todos";
 //       $filtro_situacao = "";
  //   }
  
echo  ' <p class = "cinza">
              <b>Parâmetros utilizados na pesquisa:</b><br>
              Período ='.$dt_i_ori.' a '.$dt_f_ori.'<br>
              Situação ='.$nome_situacao.'<br></p>
               &nbsp&nbsp<a href  = "form_pesquisar_problema.php?de='.$dt_i_ori.'&amp;ate='.$dt_f_ori.'&amp;situacao='.$situacao.'"><b>Fazer outra pesquisa</b></a>';
//cabeçalho da tabela
echo '
     <table width = "800">
     <tr>
            <td class="titulo_pesq" width=15%>Serviço</td>
			<td class="titulo_pesq" width=65%>Descrição</td>
			<td class="titulo_pesq" width=15%>Situação</td>
			<td class="titulo_pesq" width=10%>Data</td>
			<td class="titulo_pesq" width=10%>Nº</td>
     </tr>
     ';


$y=0;

//seleciona os problemas que foram registrados no período informado
$sql = "SELECT co_problema, an_problema
       FROM pro_v2_problema_situacao
       WHERE co_tipo_situacao_problema = 1 and dt_situacao >= '$dt_inicial'
       and dt_situacao <= '$dt_final'";
      //echo $sql;//exit;
       $resultado = mysqlexecuta($conexao,$sql);
       $qtd_registrado = mysql_num_rows($resultado);

        $arr_co_problema = array();
        $arr_an_problema = array();
        $arr_co_problema_f = array();
        $arr_an_problema_f = array();

        

        
        for($i=0; $i<$qtd_registrado; $i++) {
        	
            $arr_co_problema["$i"] = mysql_result($resultado, $i, "co_problema");
            $arr_an_problema["$i"] = mysql_result($resultado, $i, "an_problema");

            //seleciona os problemas que estão na situação informada


           $sql =  "SELECT serv.ser_no_servico, b.co_problema, b.an_problema, a.dt_situacao,b.desc_problema, c.no_situacao
           FROM pro_v2_problema_situacao a INNER JOIN pro_v2_problema b INNER JOIN pro_v2_tipo_situacao_problema c
           ON (a.co_problema = b.co_problema and a.an_problema = b.an_problema
           and c.co_situacao = a.co_tipo_situacao_problema) INNER JOIN derep_servico serv ON b.co_servico_afetado = serv.ser_co_servico
           WHERE b.co_problema = $arr_co_problema[$i] and b.an_problema = $arr_an_problema[$i] "
           . $sql_situacao . "
           and a.ativo = 1";
  
//           $sql = "SELECT a.pps_co_problema, a.pps_an_problema, a.pps_dt_situacao,b.pro_tx_descricao,
//			       FROM pro_problema_possui_situacao a INNER JOIN pro_problema b
//			       ON (a.pps_co_problema = b.pro_co_problema and a.pps_an_problema = b.pro_an_problema)
//			       WHERE pps_co_problema = $arr_co_problema[$i] and pps_an_problema = $arr_an_problema[$i] and pps_co_situacao = $situacao
//				   and pps_co_pro_possui_sit in
//			      (select max(pps_co_pro_possui_sit) from pro_problema_possui_situacao
//			      where  pps_co_problema = $arr_co_problema[$i] and pps_an_problema = $arr_an_problema[$i])";
			        

            $res = mysqlexecuta($conexao,$sql);
            $qtd_total = mysql_num_rows($res);

            if ($qtd_total > 0) {

                  $rs_array_pesquisa = mysql_fetch_array($res);
                  $arr_servico["$y"]       =  $rs_array_pesquisa["ser_no_servico"];
                  $arr_co_problema_f["$y"] =  $rs_array_pesquisa["co_problema"];
                  $arr_an_problema_f["$y"] =  $rs_array_pesquisa["an_problema"];
                  $arr_tx_descricao["$y"]  =  $rs_array_pesquisa["desc_problema"];
                  $arr_dt_situacao["$y"]   =  $rs_array_pesquisa["dt_situacao"];
                  $arr_no_situacao["$y"]   =  $rs_array_pesquisa["no_situacao"];
                      //acerta a data
                  $a_dt_situacao           = explode("-",$arr_dt_situacao["$y"]);
                  $dt_situacao       = $a_dt_situacao[2]."/".$a_dt_situacao[1]."/".$a_dt_situacao[0];
                 // echo $dt_situacao["$y"];exit;
                  // Atribui conteúdo do array por enumeração dentro de variáveis
                  $no_servico        = $arr_servico["$y"];
                  $no_situacao       = $arr_no_situacao["$y"];
                  $cod_problema      = $arr_co_problema_f["$y"];
                  $cod_problema_z    = str_pad($cod_problema,4,"0",STR_PAD_LEFT);
                  $ano_problema      = $arr_an_problema_f["$y"];
                  $tx_descricao  = ereg_replace("<br />", "<br>",$arr_tx_descricao["$y"]);

                echo '
                    <tr align=center>
                        <td class="acionado" width="10%" align=left>'.$no_servico.'</td>
                        <td class="acionado" width="65%" align=left>'.$tx_descricao.'</td>
                        <td class="acionado" width="15%" align=center>'.$no_situacao.'</td>
                        <td class="acionado" width="10%" align=center>'.$dt_situacao.'</td>
                        <td class="acionado" width="10%">
                        <a HREF="javascript:AbrirDocumento('.$cod_problema.','.$ano_problema.')">'.$cod_problema_z.'/'.$ano_problema.'</a></td>
                    </tr>

                     ';
                $y++;
               }

             }
          echo '</table>';

 //   mysql_free_result($resultado);
   // mysql_free_result($res);
    






 //     $criterio = $criterio.",".$servico.",".$descricao;


  //    $sql = $sql . $clausula_orderby;

 //    $resultado = mysqlexecuta($conexao,$sql);

   //  $num_total_registros = mysql_num_rows($resultado);

   //  mysql_free_result($resultado);

  //   $total_paginas = ceil(($qtd_total) / ($tamanho_pagina));

   //  $sql = $sql . " limit " . $inicio . "," . $tamanho_pagina;

 //    $resultado = mysqlexecuta($conexao,$sql);

 //    $qtd_registros_pesquisa = mysql_num_rows($resultado);

    // echo $qtd_registros_pesquisa;exit;



       if ($y == "0"){
         echo '<p class="retorno">Não existem registros com os parâmetros informados!</p>';
            exit;
      }   else {
         echo '
            <tr>
              <td colspan=4 align=right>
              <font size="2">Total de Problemas: <b>'. $y. '</b>&nbsp&nbsp</font><br></td>
            </tr>   ';

     }
              //mostro os diferentes índices das páginas, se é que há várias páginas

             // if ($total_paginas > 1){
//                 echo '<font size="2"><br><p class="retorno">Páginas: ';
//              for ($i=1;$i<=$total_paginas;$i++){
//                 if ($pagina == $i)
//                    //se mostro o índice da página atual, não coloco link
//                    echo ''.$pagina.'</font>';
//                 else
//                    //se o índice não corresponde à página mostrada atualmente, coloco o link para essa página
//                    echo "
//                      <font size='2'><a href='form_resultado_pesquisa_problema.php?pagina=" . $i . "&amp;criterio=" . $criterio. "'>" . $i . "</a></font>
//                     ";
//                 }
//               }

     mysql_free_result($resultado);
?>
</div>
</div>
</form>
</body>
</HTML>
<?php

//FECHAR CONEXÃO COM O BANCO
mysql_close($conexao);


?>
