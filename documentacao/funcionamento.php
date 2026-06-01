<pre>
<h2>Funcionamento</h2>

 O funcionamento do sistema, parte do principio mais simples da programacao PHP
eu estou passando uma variavel para o arquivo local chamada ACAO, nela eu faco as verificacoes
e mostro o que eu quero.
 Achou meio complicado?
 Entao vamos explicar na pratica.:

 if($acao=="") { //-> Significa "Se acao for igual a vazia"
     //-> Mostra: os botoes de adicionar, de busca e de sair do sistema e outras coisas que voce preferir
     //-> Ah!. uma outra coisa que eu me esqueci quando voce adicionar um link o link deve ficar assim
	  vou colocar um exemplo de eu fazendo um link em uma imagem e quando clickar nesta imagem ele
	  vai para o seguinte passo.

      &lt;a href=$PHP_SELF?acao=form_add><i m g src=imgs/imagem border=0>&lt;/a>

  Vamos la, 
    o &lt;a href=> quer dizer onde vc esta comecao o LINK entao o &lt;/a> e onde voce quer que ele termine certo?
	o &lt;imgs src=imgs/imagem border=0> e a imagem que voce esta adicionando o link

 } //-> fecha o trem se nao, e capaz que nao funcione.

 Entao, a imagem que voce resolveu colocar o link vai ficar postando pra ela mesma e dizendo que a 
acao vai ser igual a "form_add".
 

  Ou seja quando voce clickar no botao que vc adicionou ele vai procurar dentro do mesmo arquivo
quem é a tal da acao=form_add

  Entao vamos dizer que esta acao=form_add que voce quer mostrar é um formulario.
  Entao no mesmo arquivo nos vamos fazer um IF mais ou menos assim

  if($acao=="form_add") { //-> Se a acao for igual a form_add mostra -->
      //-> aqui dentro deste IF voce vai fazer o formulario que voce quer mostrar ok?
  }

 Entao o funcionamento do programa basea-se em IF bem simples, recaptulando..

   if($acao=="") { // -> Se a acao for igual a vazia mostra os links um resultado do Bd qualquer coisa que vc
			 queira mostrar quando vc clicar no menu.
   Quando vc clicar no botao vc vai fazer o link pra ele postar pra ele mesmo passando uma variavel cujo o nome
eu tomei a liberdade de definir chamada "acao" nela vc coloca pra onde que vc quer que va quando clicar no botao ou formulario
   }
    ae abaixo do software vc coloca pra ele mostrar o conteudo da variavel que vc passou os parametros...
vou citar outro exemplo

               &lt;a href=$PHP_SELF?acao=form_add><i m g src=imgs/imagem border=0>&lt;/a>   



</pre>
