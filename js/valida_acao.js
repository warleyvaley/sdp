// SCRIPT PARA VALIDACAO DE CADASTRO DE ACAO

//verifica se uma data é anterior a Data informada
function validaData(data1, data2, text){

    var d1 = data1.replace(/[\s\:\/\.\-]/g,"-").split("-");
    var odt1 = new Date(d1[2],--d1[1],d1[0]);

    if (data2 != "") {
        var d2 = data2.replace(/[\s\:\/\.\-]/g,"-").split("-");
        var odt2 = new Date(d2[2],--d2[1],d2[0]);
        if (odt1 > odt2) {  alert(text); return false; }
    }

    //document.forms[0].submit();
    checkform();
}

function validaData2(data1, data2, text){

    var d1 = data1.replace(/[\s\:\/\.\-]/g,"-").split("-");
    var odt1 = new Date(d1[2],--d1[1],d1[0]);

    if (data2 != "") {
        var d2 = data2.replace(/[\s\:\/\.\-]/g,"-").split("-");
        var odt2 = new Date(d2[2],--d2[1],d2[0]);
        if (odt1 > odt2) {  alert(text); return false; }
    }

    document.forms[0].submit();
   //checkform();
}

function checkform() {
	
	if (form.co_situacao.options[form.co_situacao.selectedIndex].value == "0") {
		alert("Informe a situação da ação!");
	    form.co_situacao.focus();
	    return (false);
	}
	
	if (form.co_solucao.options[form.co_solucao.selectedIndex].value == "0") {
		alert("Informe o tipo de solução da ação!");
	    form.co_solucao.focus();
	    return (false);
	}
	
	/*if (form.no_responsavel.value == "") {
		alert("Informe o responsável da ação!");
	    form.no_responsavel.focus();
	    return (false);
	}
	
	if (form.no_orgao.value == "") {
		alert("Informe o órgão responsável da ação!");
	    form.no_orgao.focus();
	    return (false);
	}*/
	
	if (form.dt_fim_acao.value == "") {
		alert("Informe a data fim da ação!");
	    form.dt_fim_acao.focus();
	    return (false);
	}
	
	if (form.dt_estimada_acao.value == "") {
		alert("Informe a data estimada da ação!");
	    form.dt_estimada_acao.focus();
	    return (false);
	}
	
	document.forms[0].submit();
	
}

function novaJanela(pagina,nome,w,h,scroll) {


	LeftPosition = (screen.width) ? (screen.width-w)/2 : 0;
	TopPosition = (screen.height) ? (screen.height-h)/2 : 0;
	settings = 'height='+h+',width='+w+',top='+TopPosition+',left='+LeftPosition+',scrollbars='+scroll+',resizable';
	//alert (pagina);
    var win = (window.open(pagina,'_blank',settings));

}

function finalizarCadResposta() {

   window.close();
   window.opener.location.reload(true);

}




function excluirAcaoAjax(cop,anp,coa){


if (confirm("Deseja realmente excluir a ação?")==true) {

if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
} else  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}

xmlhttp.onreadystatechange=function()  {

   // se sucesso efetua um refresh na pagina
  if (xmlhttp.readyState==4 && xmlhttp.status==200)    {
  
     document.location.reload(true);
    
  }
}

 //envia o codigo da acao a ser excluido
 xmlhttp.open("GET","scp_exclui_acao.php?cop="+cop+"&anp="+anp+"&coa="+coa,true);
 xmlhttp.send();
}

}



function excluirRespostaAjax(coa,resp){


if (confirm("Deseja realmente excluir a resposta da ação?")==true) {

if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
} else  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}

xmlhttp.onreadystatechange=function()  {

   // se sucesso efetua um refresh na pagina
  if (xmlhttp.readyState==4 && xmlhttp.status==200)    {

     document.location.reload(true);

  }
}

 //envia o codigo da acao a ser excluido
 xmlhttp.open("GET","scp_exclui_resposta_acao.php?coa="+coa+"&resp="+resp,true);
 xmlhttp.send();
}

}

