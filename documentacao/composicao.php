<pre>
<h2>Composição</h2>

  O sistema conta apenas com a liguagem PHP subdividida pela funcao IF do PHP que significa "SE"
  A interface grafica do sistema se localiza em um arquivo chamado "index.php" nele contem as imagens
dos botoes um iframe que mostra os links dos botoes no meio do programa, e a barra que faz o fechamento do programa
  A data que e demonstrada no final do sistema e um java script, que foi copiado de um site qualquer, uma funcao
simples, a hora atualizada com o sistema onde e implantada no caso o SERVIDOR tambem partiu do mesmo principio

 Contamos tambem com uma pasta chamada "imgs", esta pasta é onde ficam todas as imagens do sistema, ou seja
qualquer imagem que você quiser colocar no sistema por favor coloque dentro desta pasta imgs, so para
a gente nao se perder com uma porcao de imagens dentro do nosso sistema.

 Tambem exite uma pasta chamada Documentacao, que esta explicacao meio chula que eu estou fazendo, 
mais que ajuda no desenvolvimento =)

 Coloquei uma pasta chama "x" esta pasta na verdade é ainda um teste  este e uma java script que gera uma pagina
em full screen para ter uma visualizacao mais interessante do sistema, futuramente pretendemos utilizar
entao se alguem se sentir curioso em saber com funcionar e so acessar o endereco http://gps.institutocidadeverde.org.br/x
este aplicativo e um javascript chamado x-desktop

 Existe também um arquivo chamado db.inc.php é onde ficam a conexão com o posgres.

 Outro arquivo que você vai encontrar é o funcoes.inc.php foi onde que eu destinei algumas funcoes de uso continuo
para agilizar "ALGUNS" processos, pelo amor de deus nao vao fazer funcoes encapsuladas ALA NOEL.

 Quando você precisar criar um novo arquivo a unica coisa necessaria que vc tera que utilizar é:

  include "funcoes.inc.php";
           cabecario();

 De preferencia com um comentariozinho que eu adicionei nos arquivos assim:

	//------------------------------------------------------------------>
	// -> Inclusao principal para montagem do sistema
	//------------------------------------------------------------------>

	  include "funcoes.inc.php";
	           cabecario();
	//------------------------------------------------------------------>

  Este arquivo funcoes.inc.php e aquele arquivo que eu expliquei acima
e esta funcao cabecario(); que eu estou sentando e onde fica o link para
o CSS a cor cinza claro setaca como padrao para ficar mais amigavel e vai ficar
tambem a verificacao do usuario e suas permissoes, entao e legal você colocar ela
se não é capaz que não funcione. :)


</pre>

