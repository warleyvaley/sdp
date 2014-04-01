<?php 
//Esta função executa um comando SQL no banco de dados MySQL
//$conexao - Ponteiro da Conexão 
//$sql - Clausula SQL a executar 
//$erro - Especifica se a função exibe ou não(0=não, 1=sim) 
//$res - Resposta 

function mysqlexecuta($conexao,$sql,$erro = 1) { 
    if(empty($sql) OR !($conexao)){
       return 0; //Erro na conexão ou no comando SQL
    }
    if (!($res =mysql_query($sql,$conexao))) {
      if($erro){
        //para o envio em formato HTML
		/*$destinatario = "andredeoliveira@correios.com.br";
		$assunto = "Erro na gravação dos dados";
        $corpo = "Ocorreu um erro na gravação dos dados do formulário.<br>";
        $corpo .= "O erro é:  " . mysql_error() . mysql_errno();
       	$headers = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

		//endereço do destinatario
		$headers .= "To: Andre <andredeoliveira@correios.com.br>\r\n";
		//endereço do remetente
		$headers .= "From: Gerencia da Producao <derepgeao@correios.com.br>\r\n";

	    mail($destinatario,$assunto,$corpo,$headers) or DIE ("Mensagem não enviada");

         //echo "Ocorreu um erro na execução do Comando SQL no banco de dados. Favor contactar a equipe da GEAO.";
        header("Location:gravacao_nao_ok_problema.htm");*/
        
        echo mysql_error() . mysql_errno();

        }


    exit;
  } 
    return $res;
 }
?>



