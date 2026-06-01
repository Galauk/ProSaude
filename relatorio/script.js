// JavaScript Document
// FUNCAO: Atualiza div com as especializações de um determinado médico
// e mostra na div identificada

var gFormulario

    function IdentBrowser(url,f)  {

    req = null;
    gFormulario=f;
// Procura por um objeto nativo (Mozilla/Safari)
    if (window.XMLHttpRequest) {
        req = new XMLHttpRequest();
        req.onreadystatechange = processReqChange;
        req.open("GET",url,true);
        req.send(null);
// Procura por uma versÃ£o ActiveX (IE)
    } else if (window.ActiveXObject) {
               req = new ActiveXObject("Microsoft.XMLHTTP");
               if (req) {
                         req.onreadystatechange = processReqChange;
                         req.open("GET",url,true);
                         req.send();
               }
           }
}

function processReqChange() {

// apenas quando o estado for "completado"
    if (req.readyState == 4) {

// apenas se o servidor retornar "OK"

       if (req.status ==200) {

// procura pela div id="pagina" e insere o conteudo
// retornado nela, como texto HTML

          if (gFormulario==1) { document.getElementById('select_esp').innerHTML = req.responseText; }
          if (gFormulario==2) { document.getElementById('select_prod').innerHTML = req.responseText; }
  
       } else {
          alert("Houve um problema ao obter os dados:n" + req.statusText);
         }
    }  
} 
