<?php
include "../mysqlconecta_dbbit.php";
include "../mysqlexecuta_pro.php";
include "../config.php";

//recupera os dados
//obter os dados do formulário
$nu_pro  			= $_POST["nu_pro"];
$ano_pro 			= $_POST["ano_pro"];
$situacao    	= $_POST["situacao"];   
$dt_situacao 		= $_POST["dt_situacao"];

//EM OBSERVAÇÃO(3) ou SUSPENSO(4)
if ($situacao == 4 || $situacao == 3) {
	$dt_limite_situacao = $_POST["dt_limite_situacao"];
} else {
	$dt_limite_situacao = '00/00/0000';
}

$co_servico  	= $_POST["co_servico"];
$co_dono     	= $_POST["co_dono"];

$id_situacao 	= $_POST["id_situacao"];  //echo "id_situacao: $id_situacao";
$co_situacao 	= $_POST["co_situacao"]; // echo "codigo-situacao:  $co_situacao";exit;
$txt_situacao 	= $_POST["txt_situacao"];

//formata a data da situação
$arr_dt_situacao   = explode("/",$dt_situacao);
$banco_dt_situacao = $arr_dt_situacao[2]."-".$arr_dt_situacao[1]."-".$arr_dt_situacao[0];

//formata a data de limite da situação
$arr_dt_limite_situacao   = explode("/",$dt_limite_situacao);
$banco_dt_limite_situacao = $arr_dt_limite_situacao[2]."-".$arr_dt_limite_situacao[1]."-".$arr_dt_limite_situacao[0];

$data_cadastro = date('Y-m-d H:i:s');

//seleciona nome do serviço
$sql= "select  ser_no_servico
       from derep_servico
       where ser_co_servico = '$co_servico'";

$resultado = mysqlexecuta($conexao,$sql);
$rs_row = mysql_fetch_array($resultado);
$no_servico  =  $rs_row["ser_no_servico"];

mysql_free_result($resultado);

//seleciona nome da situacao
$sql= "select  no_situacao
       from pro_v2_tipo_situacao_problema
       where co_situacao = '$situacao'";
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

$email_analista   = $_POST[email_analista];
$observacoes      = nl2br(addslashes($_POST["tx_observacoes"]));

//atualiza a tabela de situacao_problema
$sql = "UPDATE  pro_v2_problema_situacao
        SET     ativo  = 0
        WHERE   co_problema =  $nu_pro and an_problema = $ano_pro";

 $res =  mysqlexecuta($conexao,$sql);
 //mysql_free_result($res);

//atualiza os dados da situacao
$sql = "INSERT INTO pro_v2_problema_situacao
             (co_tipo_situacao_problema,
              dt_cadastro,
              dt_situacao,
              dt_limite_situacao,
              co_problema,
              an_problema,
              ativo,
              desc_problema_situacao )
             VALUES
             ( $situacao,
               '$data_cadastro',
              '$banco_dt_situacao',
              '$banco_dt_limite_situacao',
               $nu_pro,
               $ano_pro,
               '1',
              '$observacoes')";

   $res = mysqlexecuta($conexao,$sql);
//echo "update ok";
//exit;

//mysql_free_result($res);

$data_mudanca_pro    = date('Y-m-d H:i:s');

if ($txt_situacao == "CANCELADO" || $txt_situacao == "SOLUCIONADO" || $txt_situacao == "CAUSA-RAIZ NÃO IDENTIFICADA" || $txt_situacao == "SOLUÇÃO INVIÁVEL") {
//atualiza a situação do problema
$sql = "UPDATE pro_v2_problema SET
        dt_fim_problema  =   '$banco_dt_situacao',
        dt_modificacao_problema  =   '$data_mudanca_pro'
        WHERE  co_problema = $nu_pro  and an_problema = $ano_pro";

}   else {
//atualiza a situação do problema
$sql = "UPDATE pro_v2_problema SET
        dt_modificacao_problema  =   '$data_mudanca_pro'
        WHERE  co_problema = $nu_pro  and an_problema = $ano_pro";
}

$res =  mysqlexecuta($conexao,$sql);
//mysql_free_result($res);



//prepara o envio do e-mail em formato HTML
$codigo_problema  = $nu_pro . "/" . $ano_pro;
$codigo_problema  = str_pad($codigo_problema,9,"0",STR_PAD_LEFT);
$descricao        = $_POST["tx_descricao"];
$observacao       = $_POST["tx_observacoes"];
$assunto 		  = "Atualização da Situação do Problema nº $codigo_problema - $no_servico";

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
		<td class="cabecalho" width="54" align="center">DITEC</td>
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
</table>
<table border="0" cellpadding="0" cellspacing="0" width="640">
    <tr>
		<td class="titulo_campos" width="120" height="30" valign="top">Situação:</td>
        <td class="geral" valign="top" align="left">'.$no_situacao.'.'.$in_motivo.'<br><br></td>
	</tr>
    <tr>
		<td class="titulo_campos" width="120" height="30" valign="top">Data da situação:</td>
        <td class="geral" valign="top" align="left">'.$dt_situacao.'<br><br></td>
	</tr>
    <tr>
		<td class="titulo_campos" width="120" height="30" valign="top">Observação:</td>
		<td class="geral" valign="top" align="left">'.$observacao.'<br><br></td>
	</tr>
</table>
';

$corpo = $cabecalho . $corpo;

if ($server == "desenvolvimento") {
    $destino = "andredeoliveira@correios.com.br";
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
    $headers .= "To: Andre <andredeoliveira@correios.com.br>\r\n";
    $headers .= "From: AC - CESEP - Gestão de Problemas - Caixa Postal\r\n";  

} else {
       $destino = "$email_gestor,$email_dono,gestaodeproblemas@correios.com.br";
       $headers = "MIME-Version: 1.0\r\n";
       $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
       $headers .= "To: $email_dono,gestaodeproblemas@correios.com.br \r\n";
       $headers .= "Cc: $email_analista\r\n";
       $headers .= "From: AC - CESEP - Gestão de Problemas - Caixa Postal\r\n";
}

//Envia o e-mail, depois mostra a página.
if($res) {
  mail($destino,$assunto,$corpo,$headers);
  header("Location:gravou_ok_problema.php?nu=$nu_pro&ano=$ano_pro");
}

//FECHAR CONEXÃO COM O BANCO
mysql_close($conexao);
?>