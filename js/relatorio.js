
function gerarArquivo() {
	
	var opcao = document.getElementById("opcaoArquivo").options[document.getElementById("opcaoArquivo").selectedIndex].value;
	
	selectAll();
	document.getElementById("info").submit();
}

function selectAll() {

	 for (var i=0;i<document.getElementById("imgs").options.length;i++) {
		 document.getElementById("imgs").options[i].selected = true;
	 }
	 
}

function validaRelatorioTendencia(form) {
 
 
 if (document.getElementById("codigoServico").options[document.getElementById("codigoServico").selectedIndex].value == "none") {
	 alert("Favor selecionar um serviço."); 
	 return false;
 }
  
 /*if (document.getElementById("codigoCluster").disabled != true) {
 	if (document.getElementById("codigoCluster").options[document.getElementById("codigoCluster").selectedIndex].value == "none") {
 		alert("Favor selecionar um cluster.");  
 		return false;
 	}
 }*/
 
 document.getElementById("btEnviar").setAttribute('disabled', 'disabled');
  	
 form.submit();
 return false;
}


function habilita(select,value) {
	
	if (value != "none") {
		select.setAttribute('disabled', 'disabled');
	} else {
		select.removeAttribute("disabled");   
	}
}
