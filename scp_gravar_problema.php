<?php
include "../mysqlconecta_dbbit.php";
include "../mysqlexecuta_pro.php";
include "../config.php";

/*
error_reporting(E_ALL);
ini_set('display_errors','On');
*/
 
//rotina para obter o código do problema
$sql = "select max(pro_v2_problema.an_problema) from pro_v2_problema";
$resultado = mysqlexecuta($conexao,$sql);

$anobanco = mysql_result($resultado, 0);

$anoatual = date("Y");
mysql_free_result($resultado);


if ($anobanco == $anoatual) {

    $sql = "select max(pro_v2_problema.co_problema) from pro_v2_problema where an_problema = $anobanco";
	$rescodigo = mysqlexecuta($conexao,$sql);

    $codigobanco = mysql_result($rescodigo, 0);
    mysql_free_result($rescodigo);

    $codigobanco++;

} else {

	$codigobanco = 1;
}

$co_equipe        = $_POST["co_equipe"];
$email_analista   = $_POST['email_analista'];
$data_problema    = date('Y-m-d H:i:s');
$descricao        = nl2br(addslashes($_POST['tx_descricao']));
$causa_raiz       = nl2br(addslashes($_POST['tx_causa_raiz']));
$observacoes        = nl2br(addslashes($_POST['tx_observacoes']));

$id_servico       = $_POST["co_servico"];
$id_categoria     = $_POST["co_categoria"];
$id_impacto       = $_POST["co_impacto"];
$co_supervisor    = $_POST["co_supervisor"];
$causa_raiz       = $_POST["in_causa_raiz"];

//formata a data da descoberta da causa-raiz se existir
if ($_POST[in_causa_raiz] == 'Sim'){
   $form_dt_causa  = $_POST["dt_causa_raiz"];
   $arr_dt_causa   = explode("/",$form_dt_causa);
   $banco_dt_causa = $arr_dt_causa[2]."-".$arr_dt_causa[1]."-".$arr_dt_causa[0];
   $tx_causa_raiz = ereg_replace("/>", "<BR>", nl2br($_POST["tx_causa_raiz"]));
}else {
   $banco_dt_causa = '0000-00-00';
   $tx_causa_raiz = "Ainda desconhecida.";
}



$sql = "INSERT INTO pro_v2_problema
            (co_problema,
             an_problema,
             dt_cadastro_problema,
             desc_problema,
             co_servico_afetado,
             co_supervisor,
             co_categoria,
             chr_causa_raiz,
             dt_causa_raiz,
             desc_causa_raiz,
             desc_observacao_problema,
             co_impacto,
			 co_equipe
            )
             VALUES
             ('$codigobanco',
              '$anoatual',
             '$data_problema',
             '$descricao',
             '$id_servico',
             '$co_supervisor',
             '$id_categoria',
             '$causa_raiz',
             '$banco_dt_causa',
             '$causa_raiz',
             '$observacoes',
             '$id_impacto',
             '$co_equipe')";

  ///echo $sql;
  ///exit();

             $res = mysqlexecuta($conexao,$sql);

//insere a situacao do problema
$sql = "INSERT INTO pro_v2_problema_situacao
            (co_tipo_situacao_problema,
             dt_cadastro,
             dt_situacao,
             co_problema,
             an_problema,
             desc_problema_situacao,
             ativo
            )
             VALUES
             ('1',
              '$data_problema ',
              '$data_problema',
              '$codigobanco',
              '$anoatual',
              'Problema registrado',
              '1')";


//echo $sql;
//exit;

             $res = mysqlexecuta($conexao,$sql);



// seleciona o nome e e-mail do dono do problema

$sql = "SELECT nom_usuario,email FROM derep_usuario WHERE id_usuario = $co_supervisor";
//EXECUTA A QUERY
$resultado = mysqlexecuta($conexao,$sql);
$rs_row    = mysql_fetch_row($resultado);

$nome_dono        =  $rs_row[0];
$email_dono       =  $rs_row[1];

mysql_free_result($resultado);

// seleciona o impacto do problema

$sql = "SELECT no_impacto FROM pro_v2_impacto WHERE co_impacto = $id_impacto";
//EXECUTA A QUERY
$resultado = mysqlexecuta($conexao,$sql);
$rs_row    = mysql_fetch_row($resultado);

$tx_impacto   =  $rs_row[0];


mysql_free_result($resultado);


//pega o nome do servico afetado
$sql = "SELECT ser_no_servico FROM derep_servico WHERE ser_co_servico = $id_servico";

//EXECUTA A QUERY
$resultado = mysqlexecuta($conexao,$sql);
$rs_row    = mysql_fetch_row($resultado);

$nome_servico       =  $rs_row[0];

mysql_free_result($resultado);


//pega o nome da categoria afetada
$sql = "SELECT no_categoria FROM pro_v2_categoria WHERE co_categoria = $id_categoria";

//EXECUTA A QUERY
$resultado = mysqlexecuta($conexao,$sql);
$rs_row    = mysql_fetch_row($resultado);

$nome_categoria       =  $rs_row[0];

mysql_free_result($resultado);


//prepara o envio do e-mail em formato HTML

$codigo_problema  = $codigobanco . "/" . $anoatual;
$codigo_problema  = str_pad($codigo_problema,9,"0",STR_PAD_LEFT);
$descricao        = ereg_replace("/>", "<BR>", nl2br($_POST["tx_descricao"]));

$observacao       = ereg_replace("/>", "<BR>", nl2br($_POST["tx_observacoes"]));

$assunto = "Problema nº $codigo_problema - $nome_servico";

$cabecalho = '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Language" content="pt-br">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Registro de Problema</title>
<style type="text/css">
.titulo_campos {
font-family: arial, verdana;
font-size: 10pt;
color: #003366;
font-weight:bold
}
.destaque {
font-family: arial, verdana;
font-size: 12pt;
color: #CC0000;
font-weight:bold
}
.topico {
font-family: arial, verdana;
background-color: #EAEAEA;
font-size: 10pt;
color: #0000FF;
font-weight:bold;
border-bottom: 1px solid #C1C1C1;
padding: 2;
}
.ocorrencia {
font-family: arial, verdana;
background-color: #EAEAEA;
font-size: 10pt;
color: #FF0000;
font-weight:bold;
border-bottom: 1px solid #C1C1C1;
padding: 2;
}
.geral {
font-family: arial, verdana;
font-size: 10pt;
}
.cabecalho {
color: 0000FF;
font-family: arial, verdana;
font-size: 10pt;
font-weight:bold;
vertical-align: middle;
border: 1px solid #C0C0C0;
}
</style>

</head>
';

$corpo = '
<body>
<table border="0" cellpadding="0" width="640" height="30">
	<tr>
		<td class="cabecalho" width="141" align="center">CORREIOS</td>
		<td class="cabecalho" width="54" align="center">VITEC</td>
		<td class="cabecalho" width="317" align="center">Registro de Problema</td>
	    <td class="cabecalho" width="113" align="left">Nº: '.$codigo_problema.'</td>
	</tr>
</table>
<br>
<table border="0" cellpadding="0" cellspacing="0" width="640">
    <tr>
		<td class="titulo_campos" width="100" height="30" valign="top">Descrição:</td>
		<td class="geral" valign="top" align="left">'.$descricao.'<br><br></td>
	</tr>
	<tr>
		<td class="titulo_campos" width="100" height="30" valign="top">Impacto:</td>
		<td class="geral" valign="top" align="left">'.$tx_impacto.'<br><br></td>
	</tr>
    <tr>
        <td class="titulo_campos" width="100" height="30" valign="top">Categoria:</td>
        <td class="geral" valign="top" align="left">'.$nome_categoria.'<br><br></td>
    </tr>
    <tr>
		<td class="titulo_campos" width="100" height="30" valign="top">Causa-raiz:</td>
		<td class="geral" valign="top" align="left">'.$tx_causa_raiz.'<br><br></td>
	</tr>
</table>
<table border="0" cellpadding="0" cellspacing="0" width="640">
    <tr>
		<td class="titulo_campos" width="160" height="30" valign="top">Solução de Contorno:</td>
		<td class="geral" valign="top" align="left">'.$tx_solucao_contorno.'<br><br></td>
	</tr>
    <tr>
		<td class="titulo_campos" width="160" height="30" valign="top">Solução Definitiva:</td>
		<td class="geral" valign="top" align="left">'.$tx_solucao_definitiva.'<br><br></td>
	</tr>
    <tr>
		<td class="titulo_campos" width="160" height="30" valign="top">Observação:</td>
		<td class="geral" valign="top" align="left">'.$observacao.'<br><br></td>
	</tr>
	<tr>
		<td class="titulo_campos" width="160" height="30" valign="top">Supervisor do Problema:</td>
		<td class="geral" valign="top" align="left">'.$nome_dono.'<br><br></td>
	</tr>
</table>
';

   $corpo = $cabecalho . $corpo;

if ($server == "desenvolvimento") {
    //$destino = "karinemonteiro@correios.com.br";
    $destino = "andredeoliveira@correios.com.br";
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
    $headers .= "To: Andre <andredeoliveira@correios.com.br>\r\n";
    $headers .= "From: AC - CESEP - Gestão de Problemas - Caixa Postal\r\n";

} else {
	
	
	   if ($co_equipe == 1) {	
       		$destino = "$email_gestor,$email_dono,gestaodeproblemas@correios.com.br";
       		$headers = "MIME-Version: 1.0\r\n";
       		$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
       		$headers .= "To: $email_dono,gestaodeproblemas@correios.com.br \r\n";
       		$headers .= "Cc: $email_analista\r\n";
	       $headers .= "From: AC - CESEP - Gestão de Problemas - Caixa Postal\r\n";
	       
	   } else {
	   		$destino = "$email_gestor,$email_dono,GESITGESTAO@correios.com.br";
       		$headers = "MIME-Version: 1.0\r\n";
       		$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
       		$headers .= "To: $email_dono,GESITGESTAO@correios.com.br\r\n";
       		$headers .= "Cc: $email_analista\r\n";
	        $headers .= "From: SPM - GERTE - Gestao de Problemas - Caixa Postal\r\n";
	   }
}

//Envia o e-mail, depois mostra a página.

if($res) {

  mail($destino,$assunto,$corpo,$headers);
  header("Location:gravou_ok_problema.php?nu=$codigobanco&ano=$anoatual");
 }

//FECHAR CONEXÃO COM O BANCO
mysql_close($conexao);
?>