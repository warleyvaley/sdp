<?php
/////////////////////////////////////////////////////////////////
// Definiчѕes de sistema
// Constantes utilizadas em todo o sistema
//
// Desenvolvido por Andrщ Santana
// CESEP - GEOP - 2013
/////////////////////////////////////////////////////////////////

//TIPO DE SISTEMA OPERACIONAL
DEFINE("SOLARIS", 2);
DEFINE("AIX", 3);
DEFINE("WINDOWS", 1);

//TIPO DE SERVIDOR (Banco de Dados)
DEFINE("TIPO_SERVIDOR_BD",2);

//TIPO DE POOL (ORACLE-RESERVA-POOL)
DEFINE("TIPO_POOL_BD_RESERVA",3);

//TIPO DE POOL (ORACLE-POOL)
DEFINE("TIPO_POOL_BD_ESPECIFICO",4);

//TIPO DE POOL (default)
DEFINE("TIPO_POOL_DEFAULT",1);

//TIPO DE POOL (default)
DEFINE("TIPO_POOL_ESPECIFICO",2);


/*
* Constantes utilizadas no relatѓrio de
* Tendъncia
*/

DEFINE("CPU","CPU");
DEFINE("MEM","MEMORIA");
DEFINE("SWAP","SWAP");

DEFINE("BASIC_GRAPH","BASIC");

//margem para cluster: 50%
DEFINE("MARGEM_SEGURANCA",50);

// Limiar de seguranca para CPU, MEMORIA E SWAP
DEFINE("LIMIAR_CPU",70);
DEFINE("LIMIAR_MEM",70);
DEFINE("LIMIAR_SWAP",5);
DEFINE("LIMIAR_SWAP_WINDOWS",20);

//diretorio de criaчуo da imagem do grсfico
DEFINE("DIR_GRAPHS","../img/temp/");

//formato da img do grсfico
DEFINE("GRAPH_TYPE",".jpeg");

// parametros definicao do grafico
DEFINE ("WIDTH",680);
DEFINE ("HEIGHT",250);

DEFINE("MARGIN_LEFT",40);
DEFINE("MARGIN_RIGHT",30);
DEFINE("MARGIN_TOP",58);
DEFINE("MARGIN_BOTTOM",79);

/*************************/

/*
 * Constantes utilizadas no calculo de perэodo
 */
//Total de meses do periodo
DEFINE("TOTAL_MESES", 12);

// qtd de meses anteriores que
// o relatorio devera retornar	
DEFINE("QTD_BASE", 5);

// padra de retorno de data 
DEFINE("MYSQL_DEFAULT", 1);
DEFINE("SQLSERVER_DEFAULT", 2);
DEFINE("ORACLE_DEFAULT", 3);
/*********************************/


/*
 * Tipos de lista de objetos 
 */
DEFINE("TYPE_MAPA","MPTY0101");
DEFINE("TYPE_MAPA_DETAIL","MPTY0102");
DEFINE("TYPE_SERVICO","SRTY0102");
DEFINE("TYPE_SERVIDOR","SVTY0201");
DEFINE("TYPE_CLUSTER","CLTY0202");


/*
 * Geraчуo de arquivos
 */
DEFINE("TIPO_PAPEL","A4");
DEFINE("ORIENTACAO_PAPEL","portrait");
DEFINE("PDF_FILE","1");
DEFINE("DOC_FILE","2");
DEFINE("REL_NOME","rel_acomp_proc");
?>