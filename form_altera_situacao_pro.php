<?php
include "../mysqlconecta_dbbit.php";
include "../mysqlexecuta_pro.php";
include_once 'autenticacao/autorizacaoPerfil.php';

$usuario = $_SERVER["REMOTE_USER"];

if (!verificaPermissao(basename(__FILE__), $usuario,$conexao)) {
	header("Location:autenticacao/acessoNegado.php");
}

list($dominio,$usr) = split("[\]", $usuario);

//Codigo que seleciona os dados do analista
$sql= "SELECT  id_usuario, nom_usuario, email
       FROM    derep_usuario
       WHERE   login = '$usr'";

$resultado = mysqlexecuta($conexao,$sql);

$rs_row = mysql_fetch_array($resultado);

$id_analista    =  $rs_row["id_usuario"];
$nome_analista  =  $rs_row["nom_usuario"];
$email_analista =  $rs_row["email"];

mysql_free_result($resultado);

//EXECUTA A QUERY
$resultado = mysqlexecuta($conexao,$sql);

$display_data =  date("d") ."/". date("m") ."/". date("Y");

//pega o código do problema que vieram da URL
$nu_pro     = $_GET["codigo"];
$ano_pro    = $_GET["ano"];

//seleciona a situacao atual do problema
$sql = "SELECT a.co_servico_afetado, a.co_supervisor, a.desc_problema,
       b.co_tipo_situacao_problema, b.desc_problema_situacao, b.dt_cadastro, c.no_situacao

       FROM pro_v2_problema a INNER JOIN pro_v2_problema_situacao b INNER JOIN pro_v2_tipo_situacao_problema c
       ON (a.co_problema = b.co_problema and a.an_problema = b.an_problema
			 and b.co_tipo_situacao_problema = c.co_situacao)

       WHERE a.co_problema = $nu_pro and a.an_problema = $ano_pro
			 and b.co_tipo_situacao_problema in (select co_tipo_situacao_problema from pro_v2_problema_situacao d
			 where d.co_problema = $nu_pro and d.an_problema = $ano_pro and ativo = 1) and ativo = 1";

//echo $sql;
$resultado = mysqlexecuta($conexao,$sql);

$problema    = mysql_fetch_array($resultado);
$co_servico          = $problema["co_servico_afetado"];
$co_dono             = $problema["co_supervisor"];
$tx_descricao        = ereg_replace("<br />", "",$problema["desc_problema"]);
$no_situacao         = $problema["no_situacao"];
$dt_situacao_anterior= $problema["dt_cadastro"];
$co_situacao         = $problema["co_tipo_situacao_problema"];
//$id_situacao         = $problema["pps_co_pro_possui_sit"];
$observacao          = ereg_replace("<br />", "",$problema["desc_problema_situacao"]);

//formata a data da situação
$arr_dt_situacao      = explode("-",$dt_situacao_anterior);
$dt_situacao_anterior = $arr_dt_situacao[0].$arr_dt_situacao[1].$arr_dt_situacao[2];


mysql_free_result($resultado);
//seleciona as situações disponíveis

$sql = "SELECT co_situacao, no_situacao
        FROM pro_v2_tipo_situacao_problema
        WHERE co_situacao <> 1 and no_situacao <> '$no_situacao' ";

$resultado = mysqlexecuta($conexao,$sql);
$qtd_situacao = mysql_num_rows($resultado);

$arr_co_situacao = array();
$arr_no_situacao = array();

for($i=0; $i<$qtd_situacao; $i++) {
    $arr_co_situacao["$i"] = mysql_result($resultado, $i, "co_situacao");
    $arr_no_situacao["$i"] = mysql_result($resultado, $i, "no_situacao");
}

mysql_free_result($resultado);

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Altera Situação do Problema</title>
<link rel="stylesheet" type="text/css" href="../geral_problema.css">
 <link rel="stylesheet" href="skin.css">  
<link type="text/css" rel="stylesheet" href="../dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen">
<SCRIPT type="text/javascript" src="../dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
<SCRIPT src="../isEmpty.js" type="text/JavaScript"></script>
<script src="../jquery.js" type="text/javascript"></script>
<script src="../jquery.maskedinput.js" type="text/javascript"></script>

<script type="text/javascript">

function checkform(){
        if (form.dt_situacao.value == '')  {
           alert("Por favor, informe a data de mudança da situação!");
           form.cal1.focus();
           return (false);
        }
        if (form.co_situacao.value == '7' || form.co_situacao.value == '8' || form.co_situacao.value == '9')  {
           alert("A situação do problema não pode mais ser alterada!");
           form.no_situacao.focus();
           return (false);
        }


        var data_situacao   = new Date(form.dt_situacao.value.substr(6,4),form.dt_situacao.value.substr(3,2)-1,form.dt_situacao.value.substr(0,2));
        var data_situacao_anterior = new Date(form.dt_situacao_anterior.value.substr(0,4),form.dt_situacao_anterior.value.substr(4,2)-1,form.dt_situacao_anterior.value.substr(6,2));

        
        //alert("dt sit " + data_situacao.valueOf());
        //alert("dt sit ant " + data_situacao_anterior.valueOf());

        
        if (data_situacao.valueOf() < data_situacao_anterior.valueOf()) {
           alert ("Data da situação atual deve ser maior que a data da situação anterior ou igual!");
           form.cal1.focus();
           return(false);
        }
        return (true);
 }

function max1(txarea)
{
  total = 1000;//Escolhemos o número máximo de posições
  tam = txarea.value.length;//controla o tamanho da variável digitada
  str="";//variável que vai controlar a digitação
  str=str+tam;
  //Digitado1.innerHTML = str;//Define as variáveis de controle, aqui digitado
  Restante1.innerHTML = total - str;//Aqui o dcontrole do restante
  //Aqui controla o tamanho e a igitação, e controla o excedente
  if (tam > total)  {
    aux = txarea.value;
    txarea.value = aux.substring(0,total);
   // Digitado1.innerHTML = total
    Restante1.innerHTML = 0
  }
}
/*function ativa_fun1(valor_pass){

if (valor_pass=="ENCERRADO") {
alert(valor_pass);
document.getElementById('div_motivo').style.display='block';
} else {
   document.getElementById('div_motivo').style.display='none';
}
}  */


//habilita a data limite da situacao
function habilitaDtLimite(seleciona) {

	form.txt_situacao.value=seleciona.options[seleciona.selectedIndex].innerHTML;

	if (seleciona.options[seleciona.selectedIndex].innerHTML == 'SUSPENSO' || seleciona.options[seleciona.selectedIndex].innerHTML == 'EM OBSERVAÇÃO') {
		form.dt_limite_situacao.disabled = '';
		form.cal2.disabled = '';	
	} else {
		form.dt_limite_situacao.value = "";
		form.dt_limite_situacao.disabled = 'disabled';
		form.cal2.disabled = 'disabled';
	}	
}

</SCRIPT>
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
<form name="form" method="POST" action="scp_gravar_situacao_pro.php" onsubmit="return checkform()">
<div class="tudo">
   <div class="topo">
        <div class="figura_normal"></div>
        <div class="h1">Alterar Situação do Problema</div>
   </div>
   <div class="identificacao">
		Bem-vindo(a), <b><?php echo $nome_analista; ?></b><input type="hidden" name="nome_analista" value="<?php echo $nome_analista ?>"><br>
		E-mail: <?php echo $email_analista; ?><input type="hidden" name="email_analista" value="<?php echo $email_analista ?>"><br>
  		Data: <?php echo $display_data; ?>
  </div>
  <div class="corpo_problema">
       <input type="hidden" name="co_servico" value="<?php echo $co_servico; ?>">
       <input type="hidden" name="co_dono" value="<?php echo $co_dono; ?>">
       <!--<input type="hidden" name="co_gestor" value="<?php //echo $co_gestor; ?>">-->
       <input type="hidden" name="nu_pro" value="<?php echo $nu_pro; ?>">
       <input type="hidden" name="ano_pro" value="<?php echo $ano_pro; ?>">
       <input type="hidden" name="dt_situacao_anterior" value="<?php echo $dt_situacao_anterior; ?>">
       <input type="hidden" name="no_situacao" value="<?php echo $no_situacao; ?>">
       <input type="hidden" name="co_situacao" value="<?php echo $co_situacao; ?>">
       <input type="hidden" name="id_situacao" value="<?php echo $id_situacao; ?>">
       <input type="hidden" name="txt_situacao" value="">
       <table cellpadding="2" cellspacing="0" width="640" border="1">
             <tr><td class="topico" align = "left"><?php echo "Código do problema: " . $nu_pro . "/" . $ano_pro ;?></td>
             </tr>
       </table>
       <table cellpadding="2" cellspacing="0" width="640" border="0">
             </tr>
                   <td class = 'destaque'>Descrição:*
                        <br><textarea type="text" readonly  name="tx_descricao" rows="5"><?php echo $tx_descricao; ?></textarea></td>
             </tr>
       </table>
       <table cellpadding="2" cellspacing="0" width="640" border="0">
            <tr ><td class="destaque" width="200px">De <i><?php echo $no_situacao;?> </i> para Situação:</td>
                <td>
                <select name="situacao" onchange="habilitaDtLimite(this);" >
                       <?php
                          for($i=0; $i<$qtd_situacao; $i++) { ?>
    	            	       <option  value="<?php echo $arr_co_situacao["$i"]; ?>"><?php echo $arr_no_situacao["$i"]; ?></option>
                          <?php } ?>
                      </select></td>
             </tr>

             <tr>
                 <td width="200px">
                    Data de mudança da situação:*
                 </td>
                 <td>
                    <input type="text" readonly name="dt_situacao" title = "Data que o problema mudou de situação." maxLength=10 size="10">
                    <input type="button" name="cal1" value="..." onclick="displayCalendar(document.forms[0].dt_situacao,'dd/mm/yyyy',this)"></td>
             </tr>
             
              <tr>
                 <td width="200px">
                    Data limite da situação:*
                 </td>
                 <td>
                    <input type="text" readonly name="dt_limite_situacao" title = "Data que o problema mudou de situação." maxLength=10 size="10" disabled="disabled" >
                    <input type="button" name="cal2" value="..." onclick="displayCalendar(document.forms[0].dt_limite_situacao,'dd/mm/yyyy',this)" disabled="disabled"></td>
             </tr>
       </table>
       <table cellpadding="0" cellspacing="0" width="640" border="0">
              <tr>
                   <td>Observações:
               		<span class="contador"></span>
                    <br><textarea rows="5" name="tx_observacoes" cols="100" title ="Observações sobre a situação."><?php //echo $observacao; ?></textarea></td>
            </tr>
       </table>
       <table cellpadding="0" cellspacing="0" width="640" border="0">
           <tr>
               <td align="center">
                    <input type="submit" value="Salvar" name="Salvar">
                    <!--  <input type="button" value="Cancelar" name="Cancel" onclick="javascript:location.href='problema.htm'">-->
               </td>
           </tr>
       </table>
  </div>
</div>
</form>
</body>
</html>
