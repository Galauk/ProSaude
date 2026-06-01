 /*
 * Arquivo com funcoes comuns
 */ 
function ajax(url) {
    //alert(nick);
    //alert(dest);
    //alert(msg);
    req = null;
    // Procura por um objeto nativo (Mozilla/Safari)
    if (window.XMLHttpRequest) {
        req = new XMLHttpRequest();
        req.onreadystatechange = processReqChange;
        req.open("GET", url, true);
        req.send(null);
        // Procura por uma versão ActiveX (IE)
    } else if (window.ActiveXObject) {
        req = new ActiveXObject("Microsoft.XMLHTTP");
        if (req) {
            req.onreadystatechange = processReqChange;
            req.open("GET", url, true);
            req.send();
        }
    }
}

function processReqChange() {

    // apenas quando o estado for "completado"
    if (req.readyState == 4) {
        // apenas se o servidor retornar "OK"
        if (req.status == 200) {
            // procura pela div id="pagina" e insere o conteudo
            // retornado nela, como texto HTML
            var resp = req.responseText.split("|");
            if ((resp[0] == 0 || resp[0] == 1 || resp[0] == 2)) {
                alert(resp[1]);
            } else {
                //alert(req.responseText);
                document.getElementById('horario').innerHTML = req.responseText;
            }
        } else {
            alert("Houve um problema ao obter os dados:n" + req.statusText);
        }
    }
}

if (navigator.appName.indexOf('Microsoft') != -1) {
    clientNavigator = "IE";
} else {
    clientNavigator = "Other";
}

function Verifica_Data(data, obrigatorio) {
    var data = document.getElementById(data);
    var strdata = data.value;
    if ((obrigatorio == 1) || (obrigatorio == 0 && strdata != "")) {
        //Verifica a quantidade de digitos informada esta correta.
        if (strdata.length != 10) {
            alert("Formato de data invalido. Formato correto: DD/MM/AAAA.");
            data.focus();
            return false
        }
        //Verifica m�scara da data
        if ("/" != strdata.substr(2, 1) || "/" != strdata.substr(5, 1)) {
            alert("Formato de data invalido. Formato correto: DD/MM/AAAA.");
            data.focus();
            return false
        }
        dia = strdata.substr(0, 2)
        mes = strdata.substr(3, 2);
        ano = strdata.substr(6, 4);
        //Verifica o dia
        if (isNaN(dia) || dia > 31 || dia < 1) {
            alert("Formato do dia invalido.");
            data.focus();
            return false
        }
        if (mes == 4 || mes == 6 || mes == 9 || mes == 11) {
            if (dia == "31") {
                alert("O mes informado nao possui 31 dias.");
                data.focus();
                return false
            }
        }
        if (mes == "02") {
            bissexto = ano % 4;
            if (bissexto == 0) {
                if (dia > 29) {
                    alert("O mes informado possui somente 29 dias.");
                    data.focus();
                    return false
                }
            } else {
                if (dia > 28) {
                    alert("O mes informado possui somente 28 dias.");
                    data.focus();
                    return false
                }
            }
        }
        //Verifica o m�s
        if (isNaN(mes) || mes > 12 || mes < 1) {
            alert("Formato do mes invalido.");
            data.focus();
            return false
        }
        //Verifica o ano
        if (isNaN(ano)) {
            alert("Formato do ano invalido.");
            data.focus();
            return false
        }
    }
    return true;
}

function verificaCamposObrigatorios() {
    inputs = document.getElementsByTagName('input');
    for (var i = 0; i < inputs.length; i++) {
        if (inputs[i].type == 'text') {
            if (inputs[i].value == '') {
                alert('Existe elemento obrigatorio nao preenchido.');
                inputs[i].focus();
                return false;
            }
        }
    }
    return true;
}

function Verifica_Data_Validade(strdata, idVal) {
    elmt = document.getElementById(idVal);
    hoje = new Date();
    dia = hoje.getDate();
    mes = hoje.getMonth() + 1;
    ano = hoje.getFullYear();

    if (mes < 10) {
        mes = "0" + mes;
    }

    if (dia < 10) {
        dia = "0" + dia;
    }
    
    hoje = dia + "/" + mes + "/" + ano;

    //Verifica a quantidade de digitos informada esta correta.
    if (strdata.length != 10) {
        alert("Formato da data nao e valido. Formato correto: - dd/mm/aaaa.");
        elmt.focus();
        return false
    }
//Verifica mascara da data
if ("/" != strdata.substr(2, 1) || "/" != strdata.substr(5, 1)) {
    alert("Formato da data nao e valido. Formato correto: - dd/mm/aaaa.");
    elmt.focus();
    return false
}
dia = strdata.substr(0, 2)
mes = strdata.substr(3, 2);
ano = strdata.substr(6, 4);
//Verifica o dia
if (isNaN(dia) || dia > 31 || dia < 1) {
    alert("Formato do dia nao e valido.");
    elmt.focus();
    return false
}
if (mes == 4 || mes == 6 || mes == 9 || mes == 11) {
    if (dia == "31") {
        alert("O mes informado nao possui 31 dias.");
        elmt.focus();
        return false
    }
}
if (mes == "02") {
    bissexto = ano % 4;
    if (bissexto == 0) {
        if (dia > 29) {
            alert("O mes informado possui somente 29 dias.");
            elmt.focus();
            return false
        }
    } else {
        if (dia > 28) {
            alert("O mes informado possui somente 28 dias.");
            elmt.focus();
            return false
        }
    }
}
//Verifica o mes
if (isNaN(mes) || mes > 12 || mes < 1) {
    alert("Formato do mes nao e valido.");
    elmt.focus();
    return false
}
//Verifica o ano
if (isNaN(ano)) {
    alert("Formato do ano nao e valido.");
    elmt.focus();
    return false
}
//Verifica se a data digitada é menor que a data atual
var hj = parseInt(hoje.split("/")[2].toString() + hoje.split("/")[1].toString() + hoje.split("/")[0].toString());
var dtVal = parseInt(strdata.split("/")[2].toString() + strdata.split("/")[1].toString() + strdata.split("/")[0].toString());
if (hj > dtVal) {
    alert("A data de validade nao pode ser menor que a data atual.");
    elmt.focus();
    return false;
}
return true;
}

function Compara_Datas(data_inicial, data_final) {
    //Verifica se a data inicial � maior que a data final
    var data_inicial = document.getElementById(data_inicial);
    var data_final = document.getElementById(data_final);
    str_data_inicial = data_inicial.value;
    str_data_final = data_final.value;
    dia_inicial = data_inicial.value.substr(0, 2);
    dia_final = data_final.value.substr(0, 2);
    mes_inicial = data_inicial.value.substr(3, 2);
    mes_final = data_final.value.substr(3, 2);
    ano_inicial = data_inicial.value.substr(6, 4);
    ano_final = data_final.value.substr(6, 4);
    if (ano_inicial > ano_final) {
        alert("A data inicial deve ser menor que a data final.");
        data_inicial.focus();
        return false;
    } else {
        if (ano_inicial == ano_final) {
            if (mes_inicial > mes_final) {
                alert("A data inicial deve ser menor que a data final.");
                data_final.focus();
                return false;
            } else {
                if (mes_inicial == mes_final) {
                    if (dia_inicial > dia_final) {
                        alert("A data inicial deve ser menor que a data final.");
                        data_final.focus();
                        return false;
                    }
                }
            }
        }
    }
}

function Verifica_Hora(hora, obrigatorio) {
    //Se o par�metro obrigat�rio for igual � zero, significa que elepode estar vazio, caso contr�rio, n�o
    var hora = document.getElementById(hora);
    if ((obrigatorio == 1) || (obrigatorio == 0 && hora.value != "")) {
        if (hora.value.length < 5) {
            alert("Formato da hora inv�lido. Por favor, informe a hora no formato correto: hh:mm");
            hora.focus();
            return false
        }
        if (hora.value.substr(0, 2) > 23 || isNaN(hora.value.substr(0, 2))) {
            alert("Formato da hora inv�lido.");
            hora.focus();
            return false
        }
        if (hora.value.substr(3, 2) > 59 || isNaN(hora.value.substr(3, 2))) {
            alert("Formato do minuto inv�lido.");
            hora.focus();
            return false
        }
    }
}

function Verifica_Email(email, obrigatorio) {
    //Se o parametro obrigatorio for igual a zero, significa que elepode estar vazio, caso contrario, nao
    var email = document.getElementById(email);
    if ((obrigatorio == 1) || (obrigatorio == 0 && email.value != "")) {
        if (!email.value.match(/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+.[a-zA-Z0-9._-]+)/gi)) {
            alert("Informe um e-mail valido");
            email.focus();
            return false;
        }
    }
}

function Verifica_Tamanho(campo, tamanho) {
    //usado para campos textarea onde n�o se tem o atributo maxlenght
    var campo = document.getElementById(campo);
    if (campo.value.length > tamanho) {
        alert("O campo suporta no m�ximo " + tamanho + " caracteres.");
        campo.focus();
        return false
    }
}

function Verifica_Cep(cep, obrigatorio) {
    //Se o par�metro obrigat�rio for igual � zero, significa que elepode estar vazio, caso contr�rio, n�o
    var cep = document.getElementById(cep);
    var strcep = cep.value;
    if ((obrigatorio == 1) || (obrigatorio == 0 && strcep != "")) {
        if (strcep.length != 9) {
            alert("CEP informado invalido.");
            cep.focus();
            return false
        } else {
            if (strcep.indexOf("-") != 5) {
                alert("Formato de CEP informado invalido.");
                cep.focus();
                return false
            } else {
                if (isNaN(strcep.replace("-", "0"))) {
                    alert("CEP informado invalido.");
                    cep.focus();
                    return false
                }
            }
        }
    }
}
///////////////Mascaras//////////////////
function PermiteNumeros(evnt) {
    var tecla = evnt.keyCode;
    tecla = String.fromCharCode(tecla);
    if (!((tecla >= "0") && (tecla <= "9"))) {
        window.event.keyCode = 0;
    }
}

function SomenteNumero(e) {
    var tecla = (window.event) ? event.keyCode : e.which;
    if ((tecla > 47) && (tecla < 58)) {
        return true;
    } else {
        if (tecla != 8) {
            return false;
        } else {
            return true;
        }
    }
}



function Bloqueia_Caracteres(evnt) {
    //Função permite digitação de números
    if (clientNavigator == "IE") {
        if (evnt.keyCode < 48 || evnt.keyCode > 57) {
            return false
        }
    } else {
        if ((evnt.charCode < 48 || evnt.charCode > 57) && evnt.keyCode == 0) {
            return false
        }
    }
}

function Ajusta_Data(input, evnt) {
    //Ajusta m�scara de Data e s� permite digita��o de n�meros
    if ((input.value.length == 2 || input.value.length == 5) && evnt.keyCode != 8) {
        input.value += "/";
    }
    //Chama a fun��o Bloqueia_Caracteres para s� permitir a digita��o de n�meros
    return Bloqueia_Caracteres(evnt);
}

/**
 * Mascara: 05/2012
 * @param input input com que ser� aplicado a m�scara
 * @param evnt
 * @returns void
 */
function Ajusta_Mes(input, evnt) {
    //Ajusta m�scara de Data e s� permite digita��o de n�meros
    if (input.value.length == 2) {
        if (clientNavigator == "IE") {
            input.value += "/";
        } else {
            if (evnt.keyCode == 0) {
                input.value += "/";
            }
        }
    }
    //Chama a fun��o Bloqueia_Caracteres para s� permitir a digita��o de n�meros
    return Bloqueia_Caracteres(evnt);
}

function Ajusta_Hora(input, evnt) {
    //Ajusta m�scara de Hora e s� permite digita��o de n�meros
    if (input.value.length == 2) {
        if (clientNavigator == "IE") {
            input.value += ":";
        } else {
            if (evnt.keyCode == 0) {
                input.value += ":";
            }
        }
    }
    //Chama a fun��o Bloqueia_Caracteres para s� permitir a digita��o de n�meros
    return Bloqueia_Caracteres(evnt);
}

function Ajusta_Cep(input, evnt) {
    //Ajusta m�scara de CEP e s� permite digita��o de n�meros
    if (input.value.length == 5) {
        if (clientNavigator == "IE") {
            input.value += "-";
        } else {
            if (evnt.keyCode == 0) {
                input.value += "-";
            }
        }
    }
    //Chama a fun��o Bloqueia_Caracteres para s� permitir a digita��o de n�meros
    return Bloqueia_Caracteres(evnt);
}

function Atualiza_Opener() {
    //Atualiza a p�gina opener da popup que chamar a fun��o
    window.opener.location.reload();
}

// <------------------------------------------------------------------------->
// <-> COISAS NOVAS - acrescentado por Andr� Filipe ( 31/01/2007 - 10:53 ) <->
// <------------------------------------------------------------------------->

//funcao que permite apenas numero
//aplicar a funcao nos eventos onKeyPress="apenasNumero(this)" onKeyUp="apenasNumero(this)"
//come�o da funcao apenasNumero
function apenasNumero(param) {
    // for(i=0; i <= param.value.length; i++)
    // {
    // if (!(param.value.charCodeAt(i)>47 && param.value.charCodeAt(i)<58))
    // {
    // param.value=param.value.substring(0,param.value.length-1);
    // }
    // }

    param.value = param.value.replace(/[^\d]/g, '');

} // final da funcao apenasNumero


//funcao que permite apenas numero e coloca mascara de telefone Ex.: (xx)xxxx-xxxx
//aplicar a funcao nos eventos  onKeyPress="soNumeroTelefone(this)" onKeyUp="soNumeroTelefone(this)"
//comeco da funcao soNumeroTelefone
function soNumeroTelefone(param, pos) {
    for (i = 0; i < param.value.length; i++) {
        //alert(param.value.charCodeAt(i));
        if (!(param.value.charCodeAt(i) > 47 && param.value.charCodeAt(i) < 58) && (param.value.charCodeAt(i) != 40 && param.value.charCodeAt(i) != 41 && param.value.charCodeAt(i) != 45)) {
            param.value = param.value.substring(0, param.value.length - 1);
        }
    }
    if (param.value.length == 1) {
        param.value = "(" + param.value;
    }
    if (param.value.length == 3) {
        param.value = param.value + ")";
    }
    if (param.value.length == 8) {
        param.value = param.value + "-";
    }
    document.forms[0].elements[pos].value = param.value;
} // final da funcao soNumeroTelefone

function soNumeroCPF(input, evnt) {
    if (input.value.length == 3) {
        input.value = input.value + ".";
    }
    if (input.value.length == 7) {
        input.value = input.value + ".";
    }
    if (input.value.length == 11) {
        input.value = input.value + "-";
    }
    return Bloqueia_Caracteres(evnt);

} // final da funcao soNumeroCPF

function soNumeroCNPJ(input, evnt) {
    if (input.value.length == 2) {
        input.value = input.value + ".";
    }
    if (input.value.length == 6) {
        input.value = input.value + ".";
    }
    if (input.value.length == 10) {
        input.value = input.value + "/";
    }
    if (input.value.length == 15) {
        input.value = input.value + "-";
    }
    return Bloqueia_Caracteres(evnt);

} // final da funcao soNumeroCNPJ

/** Retorna o objeto do elemento via "getElementById" 
 * Se passado mais de um elemento, ele ira devolver um Array, senao, devolve o proprio elemento
 */
function $() {
    var A = new Array;
    for (var i = 0; i < arguments.length; i++) {
        var obj = document.getElementById(arguments[i]);
        if (!obj) {
            alert("O elemento '" + arguments[i] + "' nao foi encontrado !");
            return null;
        }
        A.push(obj);
    }
    return (A.length == 1 ? A[0] : A);
}

/** Retorna o valor "value" do objeto
 * Se passado mais de um elemento, ele ira devolver um Array, senao, devolve o proprio elemento
 */
function $F() {
    var A = new Array;
    for (var i = 0; i < arguments.length; i++) {
        var obj = $(arguments[i]);
        if (!obj) continue;
        A.push(obj.value);
    }
    return (A.length == 1 ? A[0] : A);
}

//ADICIONADO POR RENATO -> SÃO AS FUNÇÕES DO CALENDARIO PARA ESCOLHA DE DIA
function abrirCalendario(id) {
    document.getElementById("janela").style.display = 'block';
    url = "buscaData.php?id=" + id;
    ajax_tudo(url, visualizar);
}

function visualizar(txt) {
    document.getElementById("cal").innerHTML = txt;
    buscarData();
}

function buscarData() {
    ano = document.getElementById("ano").value;
    mes = document.getElementById("mes").value;
    id = document.getElementById("id").value;
    url = "cal.inc.php?mes=" + mes + "&ano=" + ano + "&id=" + id;
    ajax_tudo(url, mostrarCalendario);
}

function mostrarCalendario(txt) {
    document.getElementById("calendario").innerHTML = txt;
}

function passarData(valor, id) {
    if (valor != "") {
        document.getElementById(id).value = valor + "/" + mes + "/" + ano;
        document.getElementById("janela").style.display = 'none';
        document.getElementById(id).focus();
    }
    //alert(id);
    //alert(valor);
}

function fecharCal(id) {
    document.getElementById(id).style.display = "none"
}
//
function PISPASEP(Fcamp) {
    if (Fcamp == 0) {
        return false;
    }
    if (Fcamp.length != 11) {
        return false;
    }
    FTAB = "3298765432";
    TOT = 0;
    for (i = 1; i < 10; i++) {
        //TOT = TOT + (Fcamp[i] * FTAB[i]);
        TOT = TOT + Fcamp.substr(i, 1) * FTAB.substr(i, 1);
    }
    RESTO = TOT % 11;
    if (RESTO != 0) {
        RESTO = 11 - RESTO;
    }
    if (RESTO != Fcamp, 11, 1) {
        return false;
    }
    return true;
}

function ValidCodCad(pCodCad) {
    d1 = pCodCad.substr(1, 1) * 6;
    d2 = pCodCad.substr(2, 1) * 5;
    d3 = pCodCad.substr(3, 1) * 4;
    d4 = pCodCad.substr(4, 1) * 3;
    d5 = pCodCad.substr(5, 1) * 2;
    soma = d1 + d2 + d3 + d4 + d5;
    resto = soma % 11;
    dv = 11 - resto;
    if (pCodCad == '') {
        return false;
    }
    if (pCodCad.length != 5) {
        alert('O n&uacute;mero tem que ser de 5 caracteres num&eacute;rico..');
        return false;
    }
    try {
        if (parseInt(pCodCad) == 0) {
            return false;
        }
    } catch (pCodCad) {
        return false;
    }
    erro = false;

    if (dv == 11) {
        dv = 0
    } else {
        if (dv == 10) {
            erro = true; // desconsidere dv = X
        }
    }

    if (erro == true) {
        //alert(dv); 
        return false;
    } else {
        //alert(dv);
        document.getElementById('usu_cadastrador_digito').value = dv;
        return true;
    }
}

function Verifica_CPF(CPF, campo) {

    var POSICAO, I, SOMA, DV, DV_INFORMADO;
    var DIGITO = new Array(10);
    DV_INFORMADO = CPF.substr(9, 2);

    for (I = 0; I <= 8; I++) {
        DIGITO[I] = CPF.substr(I, 1);
    }

    POSICAO = 10;
    SOMA = 0;
    for (I = 0; I <= 8; I++) {
        SOMA = SOMA + DIGITO[I] * POSICAO;
        POSICAO = POSICAO - 1;
    }
    DIGITO[9] = SOMA % 11;
    if (DIGITO[9] < 2) {
        DIGITO[9] = 0;
    } else {
        DIGITO[9] = 11 - DIGITO[9];
    }

    POSICAO = 11;
    SOMA = 0;
    for (I = 0; I <= 9; I++) {
        SOMA = SOMA + DIGITO[I] * POSICAO;
        POSICAO = POSICAO - 1;
    }

    DIGITO[10] = SOMA % 11;
    if (DIGITO[10] < 2) {
        DIGITO[10] = 0;
    } else {
        DIGITO[10] = 11 - DIGITO[10];
    }

    DV = DIGITO[9] * 10 + DIGITO[10];
    if (DV != DV_INFORMADO) {
        alert('CPF invalido');
        campo.focus();
        campo.value = '';
        return false;
    } else {
        //alert('CPF valido');
        return true;
    }
}

function validaTIT(rcpf1) {
    var aux = "";
    if (rcpf1.value.length == 0) {
        return false;
    }
    if (rcpf1.value.length < 13) {
        diferenca = new Number(13 - parseInt(rcpf1.value.length));
    }
    for (i = 0; i < diferenca; i++) {
        aux = aux + 0;
    }
    rcpf1.value = aux + rcpf1.value;
    //return rcpf1.value;  
    rcpf2 = rcpf1.value.substr(11, 2);
    j = rcpf1.value.substr(9, 2);

    if ((j < 1) || (j > 28)) {
        return false;
    }
    d1 = 0;
    for (i = 0; i < 9; i++) {
        d1 += rcpf1.value.charAt(i) * (10 - i);
    }
    d1 = (d1 % 11);
    if (d1 <= 1) {
        if (j <= 2) {
            d1 = 1 - d1;
        } else {
            d1 = 0;
        }
    } else {
        d1 = 11 - d1;
    }
    if (rcpf2.charAt(0) != d1) {
        return false;
    }

    d1 *= 2;
    for (i = 9; i < 11; i++) {
        d1 += rcpf1.value.charAt(i) * (13 - i);
    }
    d1 = (d1 % 11);
    if (d1 <= 1) {
        if (j <= 2) {
            d1 = 1 - d1;
        } else {
            d1 = 0;
        }
    } else {
        d1 = 11 - d1;
    }

    if (rcpf2.charAt(1) != d1) {
        return false;
    }
    return true;
}



//ADIONADO POR RENATO
//FUNCOES PARA VERIFICAR QUANTIDADE DE CONSULTAS PREVISTAS E AGENDADAS DO MEDICO
function buscarDadosComplementares2() {
    if (document.getElementById("uni_codigo1").value == "") {
        alert("Escolha a unidade");
        document.getElementById("uni_codigo1").focus();
        return false;
    } else if (document.getElementById("med_codigo1").value == "") {
        alert("Escolha o medico");
        document.getElementById("med_codigo1").focus();
        return false;
    } else if (document.getElementById("esp_codigo1").value == "") {
        alert("Escolha a especialidade");
        document.getElementById("esp_codigo1").focus();
        return false;
    } else if (document.getElementById("age_data1").value == "") {
        alert("Escolha a data");
        document.getElementById("age_data1").focus();
        return false;
    } else if (document.getElementById("hora1").value == "") {
        alert("Escolha a hora");
        document.getElementById("hora1").focus();
        return false;
    }
    esp_codigo = document.getElementById("esp_codigo1").value;
    data = document.getElementById('age_data1').value;
    hora = document.getElementById('hora1').value;
    age_item = document.getElementById('age_item1').value;
    age_tipo = document.getElementById('age_tipo1').value;
    url = "buscarDados2.php?med_codigo=" + med_codigo + "&esp_codigo=" + esp_codigo + "&data=" + data + "&hora=" + hora + "&uni_codigo=" + uni_codigo + "&age_item=" + age_item + "&age_tipo=" + age_tipo;
    ajax_tudo(url, popularDados2);
}

function popularDados2(texto) {
    //alert(texto);
    //document.getElementById('resposta1').innerHTML = texto;
    //return false;
    texto = texto.split("-");
    /*document.getElementById('resposta1').innerHTML = "<b><font color=red size=2px>"+texto[0]+"</font></b>";
    document.getElementById('resposta2').innerHTML = "<b><font color=blue size=2px>"+texto[2]+"</font></b>";
    document.getElementById('resposta3').innerHTML = "<b><font color=black size=2px>"+texto[1]+"</font></b>";*/
    if (texto[0] == undefined || texto[0] == "") {
        texto[0] = 0;
    }
    if (texto[1] == undefined || texto[1] == "") {
        texto[1] = 0;
    }
    if (texto[2] == undefined || texto[2] == "") {
        texto[2] = 0;
    }
    document.getElementById('resposta1').innerHTML = "<b><font color=red size=2px>" + texto[0] + "</font></b>";
    document.getElementById('resposta2').innerHTML = "<b><font color=blue size=2px>" + texto[1] + "</font></b>";
    document.getElementById('resposta3').innerHTML = "<b><font color=black size=2px>" + texto[2] + "</font></b>";

}

function buscarEspecialidade2() {
    document.getElementById('resposta1').innerHTML = "";
    document.getElementById('resposta2').innerHTML = "";
    document.getElementById('resposta3').innerHTML = "";
    e = document.getElementById('age_data1');
    d = document.getElementById('esp_codigo1');
    d.innerHTML = "";
    e.value = "";
    uni_codigo = document.getElementById('uni_codigo1').value;
    med_codigo = document.getElementById('med_codigo1').value;
    url = "buscarEspecialidade.php?uni_codigo=" + uni_codigo + "&med_codigo=" + med_codigo;
    ajax_tudo(url, popularEspecialidade2);
}

function popularEspecialidade2(txt) {
    d = document.getElementById('esp_codigo1');
    d.options[0] = new Option("...", "");
    r = txt;
    res = r.split(";");
    for (x = 0; x < res.length; x++) {
        aux = res[x].split("-");
        if (aux[1] != undefined) {
            d.options[d.options.length] = new Option(aux[1], aux[0]);
        }
    }
}
/*
 * M�scara para formatar campos do tipo moeda
 * Para utilizar: onKeyDown="return formata_moeda(this, 20, event, 2)";
 */
function formata_moeda(campo, tammax, teclapres, decimal) {
    var tecla = teclapres.keyCode;
    vr = limpar_campo_moeda(campo.value, "0123456789");
    tam = vr.length;
    dec = decimal;
    if (tam < tammax && tecla != 8) {
        tam = vr.length + 1;
    }
    if (tecla == 8) {
        tam = tam - 1;
    }
    if (tecla == 8 || tecla >= 48 && tecla <= 57 || tecla >= 96 && tecla <= 105) {
        if (tam <= dec) {
            campo.value = vr;
        }
        if ((tam > dec) && (tam <= 5)) {
            campo.value = vr.substr(0, tam - 2) + "," + vr.substr(tam - dec, tam);
        }
        if ((tam >= 6) && (tam <= 8)) {
            campo.value = vr.substr(0, tam - 5) + "." + vr.substr(tam - 5, 3) + "," + vr.substr(tam - dec, tam);
        }
        if ((tam >= 9) && (tam <= 11)) {
            campo.value = vr.substr(0, tam - 8) + "." + vr.substr(tam - 8, 3) + "." + vr.substr(tam - 5, 3) + "," + vr.substr(tam - dec, tam);
        }
        if ((tam >= 12) && (tam <= 14)) {
            campo.value = vr.substr(0, tam - 11) + "." + vr.substr(tam - 11, 3) + "." + vr.substr(tam - 8, 3) + "." + vr.substr(tam - 5, 3) + "," + vr.substr(tam - dec, tam);
        }
        if ((tam >= 15) && (tam <= 17)) {
            campo.value = vr.substr(0, tam - 14) + "." + vr.substr(tam - 14, 3) + "." + vr.substr(tam - 11, 3) + "." + vr.substr(tam - 8, 3) + "" + vr.substr(tam - 5, 3) + "," + vr.substr(tam - 2, tam);
        }
    }
}

function limpar_campo_moeda(valor, validos) {
    // retira caracteres invalidos da string
    var result = "";
    var aux;
    for (var i = 0; i < valor.length; i++) {
        aux = validos.indexOf(valor.substring(i, i + 1));
        if (aux >= 0) {
            result += aux;
        }
    }
    return result;
}

function buscar_produto(valor) {
    url = "busca_produto.php?palavra=" + valor;
    ajax_tudo(url, popular_produto);
    $('lista_produto').style.display = '';
    $('table_produtos').innerHTML = '';

}

function popular_produto(txt) {
    try {
        t = $('table_produtos');

        t.innerHTML = txt;
    } catch (e) {
        alert(e);
    }
}
//Funções para o Produto
function passar_produto(codigo, nome) {
    $("pro_codigo").value = codigo;
    $("pro_nome").value = nome;

    /* if(document.getElementById("pac_prontuario") != null)
     {
             $("pac_prontuario").value = prontuario;
           // if( at_iframe_esq != null )
           //          at_iframe_esq();
     }*/

    $('lista_produto').style.display = 'none';
    $('pro_nome').focus();
}
/*
  Funções para busca de pacientes
 */
function buscar_nome(valor, acao, farmacia) {
    url = "buscar_nomes.php?palavra=" + valor + "&acao=" + acao + "&farmacia=" + farmacia;
    ajax_tudo(url, popular_nome);
    $('lista_nomes').style.display = '';
    $('table_nomes').innerHTML = '';
    $("lista_carregando").style.display = '';
}


function popular_nome(txt) {
    try {
        t = $('table_nomes');
        $("lista_carregando").style.display = 'none';
        t.innerHTML = txt;
    } catch (e) {
        alert(e);
    }
}

function trocar_cor(id, id2) {
    campo = $(id);
    campo.style.background = "#ABCDEF";
    if (id2 != null) {
        $(id2).style.display = '';
    }
}

function retirar_cor(id, id2) {
    campo = $(id);
    campo.style.background = "#FFFFFF";
    if (id2 != null) {
        $(id2).style.display = 'none';
    }
}

function passar_usuario(codigo, nome, mae, data_nasc, cidade, prontuario, farmacia) {
    $("pac_codigo").value = codigo;
    $("pac_nome").value = nome;
    $("pac_mae").value = mae;
    $("pac_nascimento").value = data_nasc;
    $("pac_cidade").value = cidade;

    if (document.getElementById("pac_prontuario") != null) {
        $("pac_prontuario").value = prontuario;
        /*if( at_iframe_esq != null )
        at_iframe_esq();*/
    }

    $('lista_nomes').style.display = 'none';
    $('pac_nome').focus();

    if (farmacia == 'F') {
        historicoPacAjax(nome, codigo);
    }
}

function historicoPacAjax(nome, codigo) {
    url = "historicoPaciente.php?nome=" + nome + "&codigo=" + codigo;
    ajax_tudo(url, function (retorno) {
        if (retorno != "0")
            alert(retorno);
    });
}

//adiciona mascara de data
function MascaraData(data) {
    if (mascaraInteiro(data) == false) {
        event.returnValue = false;
    }
    return formataCampo(data, '00/00/0000', event);
}
//formata de forma generica os campos
function formataCampo(campo, Mascara, evento) {
    var boleanoMascara;
    var Digitado = evento.keyCode;
    exp = /\-|\.|\/|\(|\)| /g
    campoSoNumeros = campo.value.toString().replace(exp, "");
    var posicaoCampo = 0;
    var NovoValorCampo = "";
    var TamanhoMascara = campoSoNumeros.length;;
    if (Digitado != 8) { // backspace 
        for (i = 0; i <= TamanhoMascara; i++) {
            boleanoMascara = ((Mascara.charAt(i) == "-") || (Mascara.charAt(i) == ".") ||
                (Mascara.charAt(i) == "/"))
            boleanoMascara = boleanoMascara || ((Mascara.charAt(i) == "(") ||
                (Mascara.charAt(i) == ")") || (Mascara.charAt(i) == " "))
            if (boleanoMascara) {
                NovoValorCampo += Mascara.charAt(i);
                TamanhoMascara++;
            } else {
                NovoValorCampo += campoSoNumeros.charAt(posicaoCampo);
                posicaoCampo++;
            }
        }
        campo.value = NovoValorCampo;
        return true;
    } else {
        return true;
    }
}

//////FUNCAO DA VACINA.
function executaAcao(id, data, unidade_cod, resposta, para, pro_codigo, usu_codigo_prontuario) {
    var a = document.getElementById(id); // a é um td... que id por sua vez é seu id sequencial.
    var separaCodigoNome = unidade_cod.split('|');
    var unidade_codigo = separaCodigoNome[0];
    var unidade_nome = separaCodigoNome[1];
    if (para != 'para') {
        switch (resposta) {
            case "A":
                a.setAttribute("class", "dosesAplicados");
                a.innerHTML = "<b>" + unidade_nome.toUpperCase() + "<br />" + data + "</b>";
                salvaVacina(resposta, data, unidade_codigo, id);
                break;
            case "P":
                a.setAttribute("class", "dosesPreenchidas");
                a.innerHTML = "<b><br />" + data + "</b>";
                salvaVacina(resposta, data, unidade_cod, id);
                break;
            case "Z":
                a.setAttribute("class", "dosesAprazadas");
                a.innerHTML = "<b><br />" + data + "</b>";
                salvaVacina(resposta, data, unidade_cod, id);
                break;
            case "C":
                a.setAttribute("class", "dosesVacina");
                a.innerHTML = "";
                deletaVacina(id);
        }
    } else {
        salvaVacina(resposta, data, unidade_codigo, id, para, pro_codigo, usu_codigo_prontuario);
    }
}

function salvaVacina(resposta, data, unidade, id, para, pro_codigo, usu_codigo_prontuario) {
    if (para != "para") {
        var pac_codigo = document.getElementById('pac_codigo').value;
        var pro_codigo = document.getElementById(produto).value;
    }
    var prod_dose = id.replace("vacina", "");
    produto = "pro_codigo" + prod_dose.substring(0, 1);
    dose = prod_dose.substring(1);
    if (para == "para") {
        url = "../salvarVacinas.php?resposta=" + resposta + "&data=" + data + "&unidade=" + unidade + "&pro_codigo=" + pro_codigo + "&dose=" + dose + "&usu_codigo_prontuario=" + usu_codigo_prontuario;
    } else {
        url = "salvarVacinas.php?resposta=" + resposta + "&data=" + data + "&unidade=" + unidade + "&pac_codigo=" + pac_codigo + "&pro_codigo=" + pro_codigo + "&dose=" + dose;
    }
    ajax_tudo(url, sucesso);
}

function sucesso(txt) {
    alert(txt);
    if (txt == "false") {
        alert('Houve um erro e a acao nao foi salva, tente novamente.');
    } else {
        var a = document.getElementById(txt);
        a.setAttribute("class", "dosesVacina");
        a.innerHTML = "";
        deletaVacina(id);
    }
}

function deletaVacina(id) {
    if (confirm("Deseja Apagar esse registro?")) {
        var pac_codigo = document.getElementById('pac_codigo').value;
        var prod_dose = id.replace("vacina", "");
        produto = "pro_codigo" + prod_dose.substring(0, 1);
        dose = prod_dose.substring(1);
        var pro_codigo = document.getElementById(produto).value;
        url = "deletaVacinas.php?pac_codigo=" + pac_codigo + "&pro_codigo=" + pro_codigo + "&dose=" + dose + "&id=" + id;
        ajax_tudo(url, sucesso);
    } else {
        return false;
    }
}

function validarDatas(di, df) {

    if (di == "" && df == "") {
        return true;
    }

    /*if((validarData(di) && df=="") || (di=="" && validarData(df) )){
    	return true;
    }
	
    if( !validarData(di) || !validarData(df)){
    	return false;
    }*/

    var separaData = di.split('/'); // 01/01/2012
    var dataInvertidaInicial = separaData[2] + separaData[1] + separaData[0]; // 20120101

    var separaDataFinal = df.split('/'); // 10/01/2012
    var dataInvertidaFinal = separaDataFinal[2] + separaDataFinal[1] + separaDataFinal[0]; // 20120110
    if (di != null && di != "") {

        if (dataInvertidaInicial > dataInvertidaFinal) {
            alert('A data final e menor que a data inicial');
            return false;
        }

        return true;
    }

    return false;
}

function validarData(data) {
    return /^((([0][1-9]|[12][0-9])\/02\/(19|20)([13579][26]|[02468][048]))|(([0][1-9]|[1][0-9]|[2][0-8])\/02\/(19|20)([02468][12356]|[013579][13579]))|((([0][1-9]|[12][0-9]|30)\/(0[469]|11)|([0][1-9]|[12][0-9]|3[01])\/(0[13578]|1[02]))\/(19|20)[0-9][0-9]))$/.test(data);
}
