<?php 
//Esta fun��o executa um comando SQL no banco de dados MySQL
//$conexao - Ponteiro da Conex�o 
//$sql - Clausula SQL a executar 
//$erro - Especifica se a fun��o exibe ou n�o(0=n�o, 1=sim) 
//$res - Resposta 

function mysqlexecuta($conexao,$sql,$erro = 1) { 
    if(empty($sql) OR !($conexao)){
       return 0; //Erro na conex�o ou no comando SQL
    }
    if (!($res =mysql_query($sql,$conexao))) {
      if($erro){
        //para o envio em formato HTML
		/*$destinatario = "andredeoliveira@correios.com.br";
		$assunto = "Erro na grava��o dos dados";
        $corpo = "Ocorreu um erro na grava��o dos dados do formul�rio.<br>";
        $corpo .= "O erro �:  " . mysql_error() . mysql_errno();
       	$headers = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

		//endere�o do destinatario
		$headers .= "To: Andre <andredeoliveira@correios.com.br>\r\n";
		//endere�o do remetente
		$headers .= "From: Gerencia da Producao <derepgeao@correios.com.br>\r\n";

	    mail($destinatario,$assunto,$corpo,$headers) or DIE ("Mensagem n�o enviada");

         //echo "Ocorreu um erro na execu��o do Comando SQL no banco de dados. Favor contactar a equipe da GEAO.";
        header("Location:gravacao_nao_ok_problema.htm");*/
        
        echo mysql_error() . mysql_errno();

        }


    exit;
  } 
    return $res;
 }
?>



