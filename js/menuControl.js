// WARNING: the following should be a path relative to site, like "/hmenu/"
// here it is set relative to the current page only, which is not recommended
// for production usage; it's useful in this case though to make the demo work
// correctly on local systems.
_dynarch_menu_url = "/menu/";


function executaAcao(acao) {
	
	
	/////////// MENU DE PROBLEMA
	if (acao == 'PRO_CADASTRO_PROBLEMA') {
		window.location = "scp_verifica_javascript.php?id=problema2";
	}	
	if (acao == 'PRO_ALTERA') {
			problema = prompt("Código/Ano do problema","0000/0000");
	
			codigo = problema.substring(0,4);
			ano    = problema.substring(5,9);
			window.location = "/cesep/sdp_v2/scp_altera_problema.php?codigo=" + codigo + "&ano=" + ano;
	}
	
	if (acao == 'PRO_ALTERA_SITUACAO') {
		problema = prompt("Código/Ano do problema","0000/0000");

		codigo = problema.substring(0,4);
		ano    = problema.substring(5,9);
		window.location = "/cesep/sdp_v2/form_altera_situacao_pro.php?codigo=" + codigo + "&ano=" + ano;
	}
	
	
	if (acao == 'PRO_ADD_EDIT_ACAO') {
		problema = prompt("Código/Ano do problema","0000/0000");

		codigo = problema.substring(0,4);
		ano    = problema.substring(5,9);
		window.location = "/cesep/sdp_v2/scp_alterar_acao_problema.php?codigo=" + codigo + "&ano=" + ano;
	}
	
	if (acao == 'PRO_REL_INCIDENTES') {
		problema = prompt("Código/Ano do problema","0000/0000");

		codigo = problema.substring(0,4);
		ano    = problema.substring(5,9);
		window.location = "/cesep/form_pesquisar_inc_rel_pro.php?codigo=" + codigo + "&ano=" + ano;
	}
	
	if (acao == 'PRO_PESQ_INC_RELACIONADOS') {
		problema = prompt("Código/Ano do problema","0000/0000");

		codigo = problema.substring(0,4);
		ano    = problema.substring(5,9);
		window.location = "/cesep/form_incidentes_relacionados.php?codigo=" + codigo + "&ano=" + ano;
	}
	
	/////////////////////////////////
	
	
	//////// MENU RELATORIO
	if (acao == 'REL_ANALITICO_PRO') {
		window.location = "/cesep/sdp_v2/relatorio/form_relatorio_analitico.php";
	}
	
	if (acao == 'REL_ACOMPANHAMENTO') {
		document.location = '/cesep/sdp_v2/relatorio/form_relatorio_acompanhamento.php';
	}
	
	if (acao == 'REL_PESQ_AVANCADA') {
		document.location = '/cesep/sdp_v2/form_pesquisar_problema.php';
	} 
	////////////////////////////
	
	
	/// MENU AJUDA
	
	if (acao == 'MANUAL') {
		w = 800;
		h = 500;
		LeftPosition = (screen.width) ? (screen.width-w)/2 : 0;
		TopPosition = (screen.height) ? (screen.height-h)/2 : 0;
		window.open("/cesep/sdp_v2/include/Manual_do_Sistema_de_Documentacao_de_Problemas.pdf", null, "height="+ h +", width=" + w + ", top="+TopPosition+", left="+LeftPosition+", status=yes, toolbar=no, menubar=no, location=no, resizable=yes");
		//window.open("", "Sobre o sistema", "height=200,width=200,status=0,scrollbars=0,resizable=0,menubar=0,toolbar=0,");
	}
	
	if (acao == 'ABOUT') {
		w = 400;
		h = 200;
		LeftPosition = (screen.width) ? (screen.width-w)/2 : 0;
		TopPosition = (screen.height) ? (screen.height-h)/2 : 0;
		window.open("/cesep/sdp_v2/about.php", null, "height="+ h +", width=" + w + ", top="+TopPosition+", left="+LeftPosition+", status=yes, toolbar=no, menubar=no, location=no, resizable=no");
		//window.open("", "Sobre o sistema", "height=200,width=200,status=0,scrollbars=0,resizable=0,menubar=0,toolbar=0,");
	}
	/////////////////
	
}
