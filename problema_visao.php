<?php
include "mysqlconecta_dbbit.php";
include "mysqlexecuta_pro.php";


//pega o código do problema que veio da URL
$nu_pro     = $_GET["codigo"];
$ano_pro    = $_GET["ano"];
$codigo_problema = $nu_pro . "/" . $ano_pro;
$codigo_problema = str_pad($codigo_problema,9,"0",STR_PAD_LEFT);
//seleciona o registro do problema

$sql = "SELECT dt_cadastro_problema,
               desc_problema,
               co_servico_afetado,
               co_supervisor,               
               cat.no_categoria,
               chr_causa_raiz,
               dt_causa_raiz,
               desc_causa_raiz,
               desc_observacao_problema,
               co_impacto
          FROM pro_v2_problema pr
          INNER JOIN pro_v2_categoria cat ON pr.co_categoria = cat.co_categoria
          WHERE co_problema=$nu_pro AND an_problema=$ano_pro";
//echo $sql;exit();
$res = mysqlexecuta($conexao,$sql);

$problema    = mysql_fetch_array($res);

mysql_free_result($res);
//formata os dados do registro

$tx_descricao        = $problema["desc_problema"];
$co_servico          = $problema["co_servico_afetado"];
$co_dono             = $problema["co_supervisor"];
//$co_gestor           = $problema["pro_co_gestor"];
$in_categoria        = $problema["no_categoria"];
$in_causa_raiz       = $problema["chr_causa_raiz"];
$dt_causa_raiz       = $problema["dt_causa_raiz"];
$tx_causa_raiz       = $problema["desc_causa_raiz"];
$in_solucao_contorno = "";
$dt_solucao_contorno = "00/00/00";
$tx_solucao_contorno = "";
$in_solucao_definitiva = "";
$dt_solucao_definitiva = "00/00/00";
$tx_solucao_definitiva = "";
$tx_observacoes        = $problema["desc_observacao_problema"];
//$in_erro_conhecido     = $problema["pro_in_erro_conhecido"];
$in_motivo_encerramento= "";
$in_impacto            = $problema["co_impacto"];


//formata as datas

   $arr_dt_causa_raiz = explode("-",$dt_causa_raiz);
   $dt_causa_raiz     = $arr_dt_causa_raiz[2]."/".$arr_dt_causa_raiz[1]."/".$arr_dt_causa_raiz[0];


   $arr_dt_solucao_contorno = explode("-",$dt_solucao_contorno);
   $dt_solucao_contorno     = $arr_dt_solucao_contorno[2]."/".$arr_dt_solucao_contorno[1]."/".$arr_dt_solucao_contorno[0];



    $arr_dt_solucao_definitiva = explode("-",$dt_solucao_definitiva);
    $dt_solucao_definitiva     = $arr_dt_solucao_definitiva[2]."/".$arr_dt_solucao_definitiva[1]."/".$arr_dt_solucao_definitiva[0];



//seleciona nome do serviço

$sql= "select  ser_no_servico
       from derep_servico
       where ser_co_servico = '$co_servico'";

$resultado = mysqlexecuta($conexao,$sql);

$rs_row = mysql_fetch_array($resultado);

$no_servico  =  $rs_row["ser_no_servico"];


mysql_free_result($resultado);


//seleciona a situacao atual do problema
$sql = "SELECT a.no_situacao
       from pro_v2_tipo_situacao_problema a INNER JOIN pro_v2_problema_situacao b INNER JOIN pro_v2_problema c
       ON (c.co_problema = b.co_problema and c.an_problema = b.an_problema
			 and b.co_tipo_situacao_problema = a.co_situacao)
       WHERE c.co_problema = $nu_pro and c.an_problema = $ano_pro
			 and ativo = 1 ";




$resultado = mysqlexecuta($conexao,$sql);

$rs_row = mysql_fetch_array($resultado);

$no_situacao  =  $rs_row["no_situacao"];

mysql_free_result($resultado);

//seleciona e-mail do dono

$sql= "select  nom_usuario, email
       from derep_usuario
       where id_usuario = '$co_dono'";

$resultado = mysqlexecuta($conexao,$sql);

$rs_row = mysql_fetch_array($resultado);

$no_dono  =  $rs_row["nom_usuario"];
$email_dono =  $rs_row["email"];


mysql_free_result($resultado);


//pega o impacto do problema
$sql = "SELECT imp_tx_descricao FROM inc_impacto WHERE imp_co_impacto = $in_impacto";
//EXECUTA A QUERY
$resultado = mysqlexecuta($conexao,$sql);
$rs_row    = mysql_fetch_row($resultado);

$tx_impacto   =  $rs_row[0];

mysql_free_result($resultado);


//seleciona e-mail do gestor

//$sql= "select  nom_usuario, email
//       from derep_usuario
//       where id_usuario = '$co_gestor'";
//
//$resultado = mysqlexecuta($conexao,$sql);
//
//$rs_row = mysql_fetch_array($resultado);
//
//$no_gestor  =  $rs_row["nom_usuario"];
//$email_gestor =  $rs_row["email"];
//
//
//mysql_free_result($resultado);
//seleciona as situacoes do problema
$sql = "SELECT no_situacao, dt_situacao, desc_problema_situacao
        FROM pro_v2_problema_situacao
        INNER JOIN pro_v2_tipo_situacao_problema on
        (co_situacao = co_tipo_situacao_problema)
        WHERE co_problema = $nu_pro and an_problema = $ano_pro
        order by dt_cadastro";

        
$sql = mysqlexecuta($conexao, $sql);

$qtd_situacao = mysql_num_rows($sql);

if ($qtd_situacao > 0) {
    for($i=0; $i<$qtd_situacao; $i++) {

            $arr_no_situacao["$i"]    = mysql_result($sql, $i, "no_situacao");
            $arr_dt_situacao["$i"]    = mysql_result($sql, $i, "dt_situacao");
            $arr_obs_situacao["$i"]    = mysql_result($sql, $i, "desc_problema_situacao");
            
            list($data,$hora) = explode ("[ ]", $arr_dt_situacao["$i"]);
            $arr_dt_situacao  = explode("-",$data);
            $dt_situacao["$i"]     = $arr_dt_situacao[2]."/".$arr_dt_situacao[1]."/".$arr_dt_situacao[0];


    }
}
   mysql_free_result($sql);

//seleciona os BITs relacionados ao problema

$sql = "SELECT bit_co_bit, bit_an_bit, bit_tx_descricao, bit_in_situacao
        FROM bit_solicitacao
        WHERE bit_co_problema = $nu_pro and bit_an_problema = $ano_pro";
        
$sql = mysqlexecuta($conexao, $sql);

$qtd_bit = mysql_num_rows($sql);

if ($qtd_bit > 0) {
    for($i=0; $i<$qtd_bit; $i++) {

            $arr_co_bit["$i"]    = mysql_result($sql, $i, "bit_co_bit");
            $arr_an_bit["$i"]    = mysql_result($sql, $i, "bit_an_bit");
            $arr_tx_descricao["$i"]    = mysql_result($sql, $i, "bit_tx_descricao");
            $arr_in_situacao["$i"]    = mysql_result($sql, $i, "bit_in_situacao");

            $arr_co_an_bit["$i"] = $arr_co_bit["$i"]."/".$arr_an_bit["$i"];

            $codigo_bit["$i"] = str_pad($arr_co_an_bit["$i"],9,"0",STR_PAD_LEFT);
            //list($data,$hora) = split ("[ ]", $arr_dt_situacao["$i"]);
//            $arr_dt_situacao  = explode("-",$data);
//            $dt_situacao["$i"]     = $arr_dt_situacao[2]."/".$arr_dt_situacao[1]."/".$arr_dt_situacao[0];
//

    }
}
   mysql_free_result($sql);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Registro do Problema</title>
<link rel="stylesheet" type="text/css" href="geral_problema.css">
<script type="text/JavaScript">
function imprime() {
   window.print()
}

function AbrirDocumento(codigo,anobit) {
   	   window.open('BIT_visao.php?codigo='+codigo+'&anobit='+anobit,'','width=700,left=300,top=50,height=700,resizable=YES,Scrollbars=YES');
}
</script>
</script>
</head>
<body class="impressao">
<div class="tudo">
    <table border="1" cellpadding="0" width="640" height="30">
    	<tr>
    		<td class="destaque" width="141" align="center"><img border="0" src="images/logo_ect.gif" alt="Logotipo dos Correios"></td>
    		<td class="destaque" width="54" align="center"  bgcolor = "EAEAEA">VITEC</td>
    		<td class="destaque" width="317" align="center"  bgcolor = "EAEAEA">Registro de Problema</td>
    	    <td class="destaque" width="113" align="left"  bgcolor = "EAEAEA">Nº: <?php echo $codigo_problema; ?>
    	    <td class="destaque" width="15" align="right"  bgcolor = "EAEAEA"><img border="0" style="cursor:pointer;" src="images/impressora_peq.jpg" alt="Imprimir" onclick="imprime()"></td>
            </td>
    	</tr>
    </table>
  <div class="corpo_problema_visao">
       <table cellpadding="2" cellspacing="0" width="640" border="1">
             <tr>
                <td class="destaque" bgcolor = "EAEAEA" width="170" height="30">Situação:</td>
                <td width="470"><?php echo $no_situacao . "-" . $in_motivo_encerramento;?></td>
             </tr>
             <tr>
                <td class="destaque" bgcolor = "EAEAEA" width="170" height="30">Serviço afetado:</td>
                <td width="470"><?php echo $no_servico;?></td>
            </tr>
             <tr>
                <td class="destaque" bgcolor = "EAEAEA">Supervisor do problema:</td>
                <td><?php echo $no_dono; ?></td>
             </tr>
             <!--<tr>
                <td class="destaque" bgcolor = "EAEAEA">Gestor do Serviço:</td>
                <td><?php echo $no_gestor; ?></td>
             </tr> -->
             <tr>
                <td class="destaque" bgcolor = "EAEAEA">Categoria:</td>
                <td><?php echo $in_categoria;  ?></td>
             </tr>
             <tr>
                <td class="destaque" bgcolor = "EAEAEA">Impacto:</td>
                <td><?php echo $tx_impacto;  ?></td>
             </tr>
             <!--<tr>
                <td class="destaque" bgcolor = "EAEAEA">Erro conhecido:</td>
                <td><?php //if ($in_erro_conhecido == 1)  echo "Sim"; else echo "Nao";  ?></td>
             </tr>-->
       </table>
       <table cellpadding="2" cellspacing="0" width="640" border="1">
            <tr>
               	<td class="destaque" bgcolor = "EAEAEA" height="25" valign="top">Descrição:</td>
            </tr>
            <tr>
               <td>
                  <?php echo $tx_descricao;?></td>
          	</tr>
            <tr>
               	<td class="destaque" bgcolor = "EAEAEA" height="25" valign="top">Causa-raiz:</td>
            </tr>
            <tr>
                   <td><div id="div_informe_causa_raiz" style="<?php if ($in_causa_raiz == "Sim")  echo 'display:block';  else echo 'display:none';?>">Encontrada em:
                       <?php echo $dt_causa_raiz ;?></div></td>
            </tr>
            <tr>
                   <td><div id="div_informe_causa_raiz" style="<?php if ($in_causa_raiz == "Sim")  echo 'display:block';  else echo 'display:none';?>">
                       <?php echo $tx_causa_raiz;?></div></td>
            </tr>
            
            
            
            
            
            
            <!-- CAMPOS NÃO EXISTEM MAIS NO SISTEMA NOVO -->
           <!--  <tr>
               	<td class="destaque" bgcolor = "EAEAEA" height="25" valign="top">Solução de Contorno:</td>
            </tr> 
            <tr>
                   <td><div id="div_informe_solucao_contorno" style="<?php //if ($in_solucao_contorno == "Sim")  echo 'display:block';  else echo 'display:none';?>">Encontrada em:
                       <?php //echo $dt_solucao_contorno ;?></div></td>
            </tr>
            <tr>
                   <td><div id="div_informe_solucao_contorno" style="<?php //if ($in_solucao_contorno == "Sim")  echo 'display:block';  else echo 'display:none';?>">
                       <?php //echo $tx_solucao_contorno ;?></div></td>
            </tr>-->
           <!--  <tr>
               	<td class="destaque" bgcolor = "EAEAEA" height="25" valign="top">Solução definitiva:</td>
            </tr>
            <tr>
                   <td><div id="div_informe_solucao_definitiva" style="<?php //if ($in_solucao_definitiva == "Sim")  echo 'display:block';  else echo 'display:none';?>">Encontrada em:
                       <?php //echo $dt_solucao_definitiva ;?></div></td>
            </tr>
            <tr>
                   <td><div id="div_informe_solucao_definitiva" style="<?php //if ($in_solucao_definitiva == "Sim")  echo 'display:block';  else echo 'display:none';?>">
                       <?php //echo $tx_solucao_definitiva ;?></div></td>
            </tr>-->
            <!-- #################################################################### -->
            
            
            
            
            
            
            <tr>
               	<td class="destaque" bgcolor = "EAEAEA" height="25" valign="top">Observações:</td>
            </tr>
            <tr>
               <td>
                  <?php echo $tx_observacoes;?></td>
          	</tr>
       </table>
       <table cellpadding="2" cellspacing="0" width="640" border="1">
           	<tr>
           	   <td class="topico" colspan="4" style="border-top: 2px solid #C1C1C1">Andamento do Problema:</td>
           	</tr>
            <tr>
               <td width="100" class="cinza" height="25">Situação</td>
               <td width="470" class="cinza" height="25">Observação</td>
               <td width="70" class="cinza" height="25">Data</td>
            </tr>
            <?php  for($i=0;$i<$qtd_situacao;$i++) {
                    echo '<tr>
                               <td width="100">'.$arr_no_situacao["$i"].'</td>
                               <td width="470">'.$arr_obs_situacao["$i"].'</td>
                               <td width="70">'.$dt_situacao["$i"].'</td>
                          </tr>
                          ';
                  }
              ?>
       </table>
       <table cellpadding="2" cellspacing="0" width="640" border="1">
           	<tr>
           	   <td class="topico" colspan="4" style="border-top: 2px solid #C1C1C1">BITs relacionados:</td>
           	</tr>
            <tr>
               <td width="100" class="cinza" height="25">Número</td>
               <td width="450" class="cinza" height="25">Descrição</td>
               <td width="90" class="cinza" height="25">Situação</td>
            </tr>
            <?php  for($i=0;$i<$qtd_bit;$i++) {
                    echo '<tr>
                               <td width="100">
                               <a HREF="javascript:AbrirDocumento('.$arr_co_bit["$i"] .','.$arr_an_bit["$i"].')">'.$codigo_bit["$i"].'</a></td>
                               <td width="450">'.$arr_tx_descricao["$i"].'</td>
                               <td width="90">'.$arr_in_situacao["$i"].'<td>
                          </tr>
                          ';
                  }
              ?>
       </table>
     </div>
  </div>
</div>
</body>
</html>

<?php

//FECHAR CONEXÃO COM O BANCO
mysql_close($conexao);
?>
