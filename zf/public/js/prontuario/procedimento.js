$(function(){
  jQuery.expr[':'].Contains = function(a,i,m){
    return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase())>=0;
  };
	// preenche o select do CID, baseado no procedimento informado
  $(".salvar").click(function(){
		ate_codigo = $("#ate_codigo").val();
		io_codigo = $("#io_codigo").val();
		window.opener.document.location= baseUrl + "/leito/atendimento/index/cod/"+io_codigo+"/ate_codigo/"+ate_codigo;
		return false;
	});

  $("#proc_nome").buscar({
    
		url: baseUrl+'/procedimento/buscar/esp/'+$("#esp_codigo").val()+'/',
		template : function(ul, item) {
			return $("<li></li>").data("item.autocomplete", item).append("<a>" + item.label + "</a>").appendTo(ul);
		},
		callback: function(){
      url = baseUrl + "/prontuario/cid/procedimento/id/"+$("#proc_codigo").val();
      $("#cid")
      .attr("disabled","disabled")
      .html("<option value=\"0\">Carregando...</option>")
      .load(url, function(r){
        if(r == "")
          $(this).html("<option value=\"0\">Nenhum CID relacionado</option>");
        else
          $("#cid").removeAttr("disabled").focus();
      });
      return true;
		}
	});
  var btnListarTodos = $('#btn-listar-todos');
  btnListarTodos.clicado = false;
  var modal = $('#modal-proc');
  var nptFiltro;
  btnListarTodos.click(function(e){
    e.preventDefault();
    if(!btnListarTodos.clicado){
      $.ajax({
        url:baseUrl+'/procedimento/buscar/esp/'+$("#esp_codigo").val()+'/',
        dataType:'JSON',
        beforeSend:function(){
          btnListarTodos.clicado = true;
          modal.html('Carregando...');
          modal.dialog({
            width:Math.round(window.outerWidth * 0.7),
            height:Math.round(window.outerHeight * 0.8),
            modal:true,
            close: function(){
              nptFiltro.val('');
            }
          });
        },
        success:function(r){
          var html = '<form><input type="text" id="npt-filtro" placeholder="Digite aqui para filtrar"></form><ul class="ui-menu" id="ul-proc">';
          for(var i in r){
            html += '<li class="ui-menu-item"><a class="ui-corner-all" data-id="'+r[i].id+'">'+r[i].label+'</a></li>';
          }
          html += '</ul>';
          modal.html(html);
          $('#modal-proc a.ui-corner-all').click(function(e){
            e.preventDefault();
            var self = $(this);
            var id = self.data('id');
            $("#esp_codigo").val(id);
            $("#proc_codigo").val(id);
            $("#proc_nome").val(self.text());
            $("#cid")
            .attr("disabled","disabled")
            .html("<option value=\"0\">Carregando...</option>")
            .load(baseUrl + "/prontuario/cid/procedimento/id/"+id, function(r){
              if(r == "")
                $(this).html("<option value=\"0\">Nenhum CID relacionado</option>");
              else
                $("#cid").removeAttr("disabled").focus();
            });
            $('#modal-proc').dialog('close');
          }).hover(function(){
            $(this).addClass('ui-state-hover');
          }, function(){
            $(this).removeClass('ui-state-hover');
          });
          var list = $('#ul-proc');
          nptFiltro = $('#npt-filtro');
          nptFiltro.change(function(){
            var filter = $(this).val();
            if(filter) {
              $(list).find("a:not(:Contains(" + filter + "))").parent().hide();
              $(list).find("a:Contains(" + filter + ")").parent().show();
            } else {
              $(list).find("li").show();
            }
            return false;
          })
          .keyup( function () {
            $(this).change();
          });
        },
      });
    } else {
      modal.dialog('open');
    }
  });

	$("#proc_codigo").change(function(){


	});

	// validações
	$("form:first").validate({
		rules: {
			proc_codigo: {
				min: 1
			}
		},
		messages: {
			proc_codigo: {
				min: "Infome um procedimento"
			}
		}
	});
});

