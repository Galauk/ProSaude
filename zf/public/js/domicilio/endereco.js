$(function(){
    // Para a busca do cep funcionar, tem que setar um padrão
    //wscep({map: 'map1',auto:true});
    //wsmap('08615-000','555','map2');
    $("#form").validate({
        rules: {
            rua: { required: true },
            uf_codigo: { required: true },
            cidade_codigo:{ required: true },
            bairro_codigo:{ required: true }
        },
        messages: {
            rua: { required: "Campo Obrigatório!" },
            uf_codigo: { required: "Selecione um Estado!" },
            cidade_codigo: { required: "Selecione uma Cidade!" },
            bairro_codigo: { required: "Selecione um Bairro!" }
        }
    });
    
    $("#rua").buscar({
        url: baseUrl+'/domicilio/endereco/buscar/',
        template : function(ul, item) {
                return $("<li/>").data("item.autocomplete", item).append(
                        "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function(){
           return true;
        }
    });
    
});

function buscaEstado(){
    $("#uf_codigo").val("");
    $("#uf").buscar({
        url: baseUrl+'/default/estado/buscar/',
        template : function(ul, item) {
                return $("<li/>").data("item.autocomplete", item).append(
                        "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function(){
           return true;
        }
    });
}

function buscaCidade(){
    $("#cidade_codigo").val("");
    $("#cidade").buscar({
        url: baseUrl+'/default/cidade/buscar-tb-cidade/',
        template : function(ul, item) {
                return $("<li/>").data("item.autocomplete", item).append(
                        "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function(){
           return true;
        }
    });
}

function buscaBairro(){
    $("#bairro_codigo").val("");
    $("#bairro").buscar({
        url: baseUrl+'/default/bairro/buscar/',
        template : function(ul, item) {
            return $("<li/>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function(){
           $("#erro-bairro").hide();
           return true;
        }
    });
}

var last_cep = 0;
var address;
var lat;
var lng;
var wsconf;
function wscep(conf)
{
    //parametros padrao true
    if(!conf){
        conf = {
            'auto': true,
            'map' : '',
            'wsmap' : ''
        };
    }
    wsconf = conf;
    //evento keyup no campo cep opcional
    if(wsconf.auto == true){
        
        $('#cep').live('keyup',function(){
            var cep = $.trim($('#cep').val()).replace('_','');
            if(cep.length >= 9){
                if(cep != last_cep){
                    limpaCampos();
                    busca();
                }
            }
        });         
    }    
    $('#cep').mask('99999-999');    
}
//busca o cep
function busca(){
    var cep = $.trim($('#cep').val());
    var url = 'http://clareslab.com.br/ws/cep/json/'+cep+'/';    
    $.post(url,{
        cep:cep
    },
    function (rs) {
        rs = $.parseJSON(rs);
        if(rs != 0){
            address = rs.endereco + ', ' + rs.bairro + ', ' + rs.cidade + ', ' + ', ' + rs.uf;
            $('#rua').val(rs.endereco);
            $('#bairro').val(rs.bairro);
            validaBairro();
            $('#cidade').val(rs.cidade);
            validaCidade();
            $('#uf').val(rs.uf);
            validaEstado();
            $('#erro-cep').hide();
            $('#num').focus();
            last_cep = cep;
        }
        else{
            $('#erro-cep').show();
            $('#cep').focus();  
            last_cep = 0;
        }
    })    
}
 
function wsmap(cep,num,elm)
{
    var url = 'http://clareslab.com.br/ws/cep/json/'+cep+'/';    
    if ($.browser.msie) {
        var url = 'ie.php';    
    }    
    $.post(url,{
        cep:cep
    },
    function (rs) {
        rs = $.parseJSON(rs);
        if(rs != 0){
            address = rs.endereco + ', ' + num + ', ' + rs.bairro + ', ' + rs.cidade + ', ' + ', ' + rs.uf;
            setMap(elm);
        }
    })
}

function limpaCampos(){
    $("#uf").val("");
    $("#uf_codigo").val("");
    $("#cidade").val("");
    $("#cidade_codigo").val("");
    $("#bairro").val("");
    $("#bairro_codigo").val("");
    $("#rua").val("");
    $("#rua_codigo").val("");
}

function validaBairro(){
    var bairro = $("#bai_codigo").val();
    $.ajax({
        url: baseUrl+"/default/bairro/valida-bairro",
        type: "POST",
        data: {
            bairro: bairro
        },
        success: function(txt) {
            if (txt=="erro"){
                $("#erro-bairro").show();
                $("#")
            } else {
                $("#bairro_codigo").val(txt);
            }
        }
    });
}

function validaEstado(){
    var estado = $("#uf").val();
    $.ajax({
        url: baseUrl+"/default/estado/valida-estado",
        type: "POST",
        data: {
            estado: estado
        },
        success: function(txt) {
            if (txt=="erro"){
                $("#erro-uf").show();
            } else {
                $("#uf_codigo").val(txt);
            }
        }
    });
}

function validaCidade(){
    var cidade = $("#cid_codigo").val();
    $.ajax({
        url: baseUrl+"/default/cidade/valida-cidade",
        type: "POST",
        data: {
            cidade: cidade
        },
        success: function(txt) {
            if (txt=="erro"){
                $("#erro-cidade").show();
            } else {
                $("#cidade_codigo").val(txt);
            }
        }
    });
}