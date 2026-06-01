var req;
function loadXMLDoc(url) {
   req = null;
   if (window.XMLHttpRequest) {
      req = new XMLHttpRequest();
      req.onreadystatechange = processReqChange;
      req.open("GET", url, true);
      req.send(null);

   } else if (window.ActiveXObject) {
      try {
         req = new ActiveXObject("Msxml2.XMLHTTP.4.0");
      } catch (e) {
         try {
            req = new ActiveXObject("Msxml2.XMLHTTP.3.0");
         } catch (e) {
            try {
               req = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
               try {
                  req = new ActiveXObject("Microsoft.XMLHTTP");
               } catch (e) {
                  req = false;
               }
            }
         }
      }
      if (req) {
         req.onreadystatechange = processReqChange;
         req.open("GET", url, true);
         req.send();
      }
   }
}

function processReqChange() {

   if (req.readyState == 4) {
      if (req.status == 200) {
         document.getElementById("atualiza").innerHTML = req.responseText;
      } else {
         alert("Houve um problema ao obter os dados:\n" + req.statusText);
      }
   }
}

function atualiza(valor) {

   loadXMLDoc("exec.php?med_codigo=" + valor);

}

function processAgendamento() {

   if (req.readyState == 4) {
      if (req.status == 200) {
         document.getElementById("pac_codigo").innerHTML = req.responseText;
      } else {
         alert("Houve um problema ao obter os dados:\n" + req.statusText);
      }
   }
}


function atualizaAgendamento(valor) {

   loadXMLDoc("select_paciente.php?usu_codigo=" + valor);

}

