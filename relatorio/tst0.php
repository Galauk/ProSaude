<div id="pagebody">
<div id="content">
<h1>:: A propriedade text ::</h1>
  <h2>Os textos nos elementos <abbr title="Hyper Text Markup Language">HTML</abbr></h2>
	<p>As propriedades para textos, definem as caracter&iacute;sticas (os valores na regra <abbr title="Cascading Style Sheet">CSS</abbr>) dos textos inseridos dentro dos elementos <abbr>HTML</abbr>.</p>

	<p>As  propriedades para textos s&atilde;o  as listadas abaixo:</p>
	<ul class="boxtut">
		<li>color.....................cor do texto;</li>
		<li>letter-spacing........espa&ccedil;amento entre letras;</li>
		<li>word-spacing.........espa&ccedil;amento entre palavras; </li>

		<li>text-align..............alinhamento do texto;</li>
		<li>text-decoration......decora&ccedil;&atilde;o do texto; </li>
		<li>text-indent............recuo do texto;</li>
		<li>text-transform.......forma das letras;</li>
		<li>direction...............dire&ccedil;&atilde;o do texto; </li>

		<li>white-space.........como o browser trata os espa&ccedil;os em branco;</li>
	</ul>
  <h2>Valores v&aacute;lidos para as propriedades do texto</h2>
  <ul class="boxtut">
		<li><strong>color:</strong>
			<ol>
				<li> c&oacute;digo hexadecimal: #FFFFFF</li>

				<li>c&oacute;digo rgb: rgb(255,235,0)</li>
				<li>nome da cor: red, blue, green...etc</li>
			</ol></li>
		<li><strong>letter-spacing:</strong>
			<ol>
				<li> normal: &eacute; o espa&ccedil;amento default</li>

				<li> lenght: uma medida reconhecida pelas <abbr>CSS</abbr> (px, pt, em, cm, ...) S&atilde;o
					v&aacute;lidos valores negativos </li>
			</ol></li>
		<li><strong>word-spacing: </strong>
			<ol>
				<li> normal: &eacute; o espa&ccedil;amento default</li>

				<li> lenght: uma medida reconhecida pelas <abbr>CSS</abbr> (px, pt, em, cm, ...) S&atilde;o
					v&aacute;lidos valores negativos </li>
			</ol></li>
		<li><strong>text-align:</strong>
			<ol>
				<li>left: alinha o texto a esquerda</li>

				<li>right: alinha o texto a direita</li>
				<li>center: alinha o texto no centro</li>
				<li>justify: for&ccedil;a o texto a ocupar toda a extens&atilde;o da linha
					da esquerda a direita</li>
			</ol></li>
		<li><strong>text-decoration:</strong>
			<ol>

				<li> none: nenhuma decora&ccedil;&atilde;o</li>
				<li>underline: coloca sublinhado no texto</li>
				<li>overline: coloca um sobrelinhado no texto</li>
				<li>line-through: coloca uma linha em cima do texto</li>
				<li>blink: faz o texto piscar</li>

			</ol></li>
		<li><strong>text-indent:</strong>
			<ol>
				<li> lenght: uma medida reconhecida pelas <abbr>CSS</abbr> (px, pt, em, cm, ...)</li>
				<li>% : porcentagem da largura do elemento pai</li>

			</ol></li>
		<li><strong>text-transform:</strong>
			<ol>
				<li> none: texto normal</li>
				<li>capitalize: todas as primeiras letras do texto em mai&uacute;sculas</li>
				<li>uppercase: todas as letras do texto em mai&uacute;sculas</li>

				<li>lowercase: todas as letras do texto em min&uacute;sculas</li>
			</ol></li>
		<li><strong>direction:</strong>
			<ol>
				<li> ltr: texto escrito da esquerda para a direita</li>
				<li>rtl: texto escrito da direita para a esquerda </li>

			</ol></li>
		<li><strong>white-space:</strong>
			<ol>
				<li> normal: os espa&ccedil;os em branco ser&atilde;o ignorados pelo browser</li>
				<li>pre: os espa&ccedil;os em branco ser&atilde;o preservados pelo browser</li>

				<li>nowrap: o texto ser&aacute; apresentado todo ele numa linha &uacute;nica
					na tela. N&atilde;o h&aacute; quebra de linha at&eacute; ser encontrada
					uma tag &lt;br&gt;</li>
			</ol></li>
	</ul>
	<p>Vamos a seguir analisar cada uma delas detalhadamente atrav&eacute;s de exemplos pr&aacute;ticos.</p><p><strong>Como estudar e entender os exemplos</strong></p>

	<p>Para cada propriedade apresento as regras <abbr>CSS</abbr> para um ou mais elementos <abbr>HTML</abbr> e definidas dentro de uma folha de estilos incorporada, bem como um trecho do documento <abbr>HTML</abbr> onde se aplicam as regras.</p><p>A seguir mostro o efeito que a regra produz. Observe a regra e o efeito e para melhor fixar seu aprendizado reproduza o c&oacute;digo no seu editor, mude os valores e veja o resultado no browser. Esta &eacute; a melhor e mais r&aacute;pida maneira de voc&ecirc; aprender <abbr>CSS</abbr>. Bons estudos! E fa&ccedil;a &oacute;timo proveito dos tutoriais.</p>

	<h2>color ... A cor do texto</h2>
  <pre><code>&lt;html&gt;
&lt;head&gt;
&lt;style type=&quot;text/css&quot;&gt;
&lt;!--
h1 {color: #FF0000;}
h2 {color: #00FF00;}
p {color: rgb(0,0,255);}
--&gt;
&lt;/style&gt;
&lt;/head&gt;

&lt;body&gt;
&lt;h1&gt;Este  cabe&ccedil;alho &eacute; vermelho&lt;/h1&gt;
&lt;h2&gt;Este cabe&ccedil;alho &eacute; verde&lt;/h2&gt;
&lt;p&gt;Este par&aacute;grafo &eacute; azul&lt;/p&gt;

&lt;/body&gt;
&lt;/html&gt;</code></pre>

  <p> Este &eacute; o efeito da folha de estilo acima: </p>
<div class="boxtut" style="padding:20px 5px;"><p style="color:#f00;font-size:1.8em">Este  cabe&ccedil;alho &eacute; vermelho</p><p style="color:#0f0;font-size:1.6em">Este cabe&ccedil;alho   &eacute; verde</p> <p style="color:rgb(0,0,255)">Este  par&aacute;grafo &eacute; azul</p></div>

  <h2>letter-spacing...O espa&ccedil;o entre letras</h2>
	<pre><code>&lt;html&gt;
&lt;head&gt;
&lt;style type=&quot;text/css&quot;&gt;
&lt;!--
h2 {letter-spacing: 1.2em;}
p {letter-spacing: 0.4cm;}
--&gt;
&lt;/style&gt;
&lt;/head&gt;

&lt;body&gt;
&lt;h2&gt; Este &eacute; o cabe&ccedil;alho&lt;/h2&gt;
&lt;p&gt; Este &eacute; o par&aacute;grafo&lt;/p&gt;

&lt;/body&gt;
&lt;/html&gt;</code></pre>
	<p>Este &eacute; o efeito da folha de estilo acima: </p>
	<div class="boxtut" style="padding:20px 5px;"> <p style="letter-spacing:1.2em; font-size:1.8em; margin-bottom:20px;">Este  &eacute; o cabe&ccedil;alho </p><p style="letter-spacing:0.4cm; font-size:1.0em;">Este  &eacute; o paragr&aacute;fo </p></div>

  <h2>word-spacing...O espa&ccedil;o entre palavras</h2>
	<pre><code>&lt;html&gt;
&lt;head&gt;
&lt;style type=&quot;text/css&quot;&gt;
&lt;!--
h2 {word-spacing: 1.8em;}
p {word-spacing: 80px;}
--&gt;
&lt;/style&gt;
&lt;/head&gt;

&lt;body&gt;
&lt;h2&gt; Este &eacute; o cabe&ccedil;alho&lt;/h2&gt;
&lt;p&gt; Este &eacute; o par&aacute;grafo&lt;/p&gt;

&lt;/body&gt;
&lt;/html&gt;</code></pre>
	<p>Este &eacute; o efeito da folha de estilo acima: </p>
	<div class="boxtut" style="padding:20px 5px;"> <p style="word-spacing:1.8em; font-size:1.8em; margin-bottom:20px;">Este  &eacute; o cabe&ccedil;alho </p><p style="word-spacing:80px; font-size:1.0em;">Este  &eacute; o paragr&aacute;fo </p></div>

  <h2>text-align...Alinhar o texto</h2>
  <pre><code>&lt;html&gt;
&lt;head&gt;
&lt;style type=&quot;text/css&quot;&gt;
&lt;!--
h1 {text-align: left;}
h2 {text-align: center;}
h3 {text-align: right;}
p {text-align: justify;}
--&gt;
&lt;/style&gt;
&lt;/head&gt;

&lt;body&gt;
&lt;h1&gt;Este &eacute; o cabe&ccedil;alho 1&lt;/h1&gt;
&lt;h2&gt;Este &eacute; o cabe&ccedil;alho 2&lt;/h2&gt;
&lt;h3&gt;Este &eacute; o cabe&ccedil;alho 3&lt;/h3&gt;

&lt;p&gt;Este &eacute; o par&aacute;grafo cujo texto ...&lt;/p&gt;
&lt;/body&gt;
&lt;/html&gt;</code></pre>
	<p>Este &eacute; o efeito da folha de estilo acima: </p>
	<div class="boxtut" style="padding:20px 5px;"> <p style="text-align:left; font-size:2.5em; margin-bottom:30px;">Este &eacute; o cabe&ccedil;alho 1 </p><p style="text-align:center; font-size:2.2em; margin-bottom:30px;">Este &eacute; o cabe&ccedil;alho 2 </p><p style="text-align:right; font-size:1.9em; margin-bottom:30px;">Este &eacute; o cabe&ccedil;alho 3 </p><p style="text-align:justify; font-size:1.0em;">Este &eacute; o par&aacute;grafo cujo texto foi alongado para mais de duas linhas para que voc&ecirc; possa visualizar o efeito de <code>text-align: justify </code>que for&ccedil;a o texto a estender-se desde a direita at&eacute; a esquerda.</p></div>

  <h2>text-decoration...Decora&ccedil;&atilde;o do texto</h2>
  <pre><code>&lt;html&gt;
&lt;head&gt;
&lt;style type=&quot;text/css&quot;&gt;
&lt;!--
h1 {text-decoration: underline;}
h2 {text-decoration: line-through;}
h3 {text-decoration: overline;}
a {text-decoration: none;}
--&gt;
&lt;/style&gt;
&lt;/head&gt;

&lt;body&gt;
&lt;h1&gt;Texto com sublinhado&lt;/h1&gt;
&lt;h2&gt;Texto com linha em cima&lt;/h2&gt;
&lt;h3&gt;Texto com sobrelinhado&lt;/h3&gt;
&lt;p&gt;
&lt;a href=&quot;http://www.maujor.com&quot;&gt;<br />Este &eacute; um link sem sublinhado&lt;/a&gt;

&lt;/p&gt;
&lt;/body&gt;
&lt;/html&gt;</code></pre>
	  <p>Este &eacute; o efeito da folha de estilo acima: </p>
	<div class="boxtut" style="padding:20px 5px;"> <p style="text-decoration:underline; font-size:1.8em; margin-bottom:20px;">Texto com sublinhado</p><p style="text-decoration:line-through; font-size:1.5em; margin-bottom:20px;">Texto com linha em cima</p><p style="text-decoration:overline; font-size:1.2em; margin-bottom:20px;">Texto com sobrelinhado</p><p><a style="text-decoration:none; font-size:1.0em; margin-bottom:20px;" href="#content">Este &eacute; um link sem sublinhado</a></p></div>

  <h2>text-indent...Recuo do texto</h2>
  <pre><code>&lt;html&gt;
&lt;head&gt;
&lt;style type=&quot;text/css&quot;&gt;
&lt;!--
h3 {text-indent: 80px;}
p {text-indent: 3em;}
--&gt;
&lt;/style&gt;
&lt;/head&gt;

&lt;body&gt;
&lt;h3&gt;Texto com recuo de 80 pixel&lt;/h3&gt;
&lt;p&gt;Texto com recuo de 3.0em&lt;/p&gt;
&lt;/body&gt;
&lt;/html&gt;</code></pre>
	  <p>Este &eacute; o efeito da folha de estilo acima: </p>

	<div class="boxtut" style="padding:20px 5px;"> <p style="text-indent:80px; font-size:1.3em; margin-bottom:20px;">Texto com recuo de 80 pixeis</p><p style="text-indent:3.0em; font-size:1.0em; margin-bottom:20px;">Texto com recuo de 3.0em</p></div>
  <h2>text-transform...Forma das letras do texto</h2>
  <pre><code>&lt;html&gt;
&lt;head&gt;
&lt;style type=&quot;text/css&quot;&gt;
&lt;!--
h1 {text-transform: none;}
h2 {text-transform: capitalize;}
h3 {text-transform: uppercase;}
h4 {text-transform: lowercase;}
--&gt;

&lt;/style&gt;
&lt;/head&gt;
&lt;body&gt;
&lt;h1&gt;Texto com letras como digitadas&lt;/h1&gt;
&lt;h2&gt;Texto com primeira letra das palavras, mai&uacute;sculas&lt;/h2&gt;
&lt;h3&gt;Texto com todas letras, mai&uacute;sculas&lt;/h3&gt;

&lt;h4&gt;Texto com letras min&uacute;sculas&lt;/h4&gt;
&lt;/body&gt;
&lt;/html&gt;</code></pre>
	  <p>Este &eacute; o efeito da folha de estilo acima: </p>
	<div class="boxtut" style="padding:20px 5px;"> <p style="text-transform:none; font-size:1.8em; margin-bottom:20px;">Texto com letras como digitadas</p><p style="text-transform:capitalize; font-size:1.5em; margin-bottom:20px;">Texto com primeira letra das palavras, mai&uacute;sculas</p><p style="text-transform:uppercase; font-size:1.2em; margin-bottom:20px;">Texto com todas letras, mai&uacute;sculas</p><p style="text-transform:lowercase; font-size:1.0em; margin-bottom:20px;">Texto com letras min&uacute;sculas</p></div>

	<p>Voc&ecirc; poder fazer uso de um excelente editor para a propriedade background e descobrir mais efeitos para complementar este tutorial, <a href="/tutorial/interativo/itext.php" >nesta p&aacute;gina interativa</a>.</p>
<!-- inc endcontent -->
<p class="rev">Criado em: 2003-12-10<br />
Atualizado em: 2005-08-01</p>
</div><!-- Fim da div content -->
</div> <!--fim da pagebody -->