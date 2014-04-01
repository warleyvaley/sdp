/*
www.moinho.net
Verify an e-mail address
Verifica se uma endereço de e-mail é válido
Fucntion: isEmail
Return  : true if the e-mail address is valid
Retorno : true se o endereço de e-mail for válido
e-mail  : celso.goya@moinho.net
Author  : Celso Goya

Instructions
If you have any questions about the functionality or sugestions please send us a report.

Instruções
Se você tiver qualquer dúvida ou sugestão sobre a funcionalidade desta função por favor envie-nos um e-mail
*/
function isEmail(text){
   var 	arroba = "@",
       	ponto = ".",
	   	posponto = 0,
	   	posarroba = 0;
	
	 if (text =="") return false;
	
	 for (var indice = 0; indice < text.length; indice++){
	 	if (text.charAt(indice) == arroba) {
	 		posarroba = indice;
	      	break;
		 }
	 }
	
	for (var indice = posarroba; indice < text.length; indice++){
		if (text.charAt(indice) == ponto) {
			posponto = indice;
	     	break;
		}
	}
	if (posponto == 0 || posarroba == 0) return false;
	if (posponto == (posarroba + 1)) return false;
	if ((posponto + 1) == text.length) return false;
	return true;
}