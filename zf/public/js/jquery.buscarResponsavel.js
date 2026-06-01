/**
 * Dependências:
 * JS:
 *  - /WebSocialSaude/zf/public/js/jquery-1.6.2.min.js
 *  - /WebSocialSaude/zf/public/js/jquery-ui-1.8.16.custom.min.js
 *  - /WebSocialSaude/zf/public/js/geral.js (somente para a função "paciente bloqueado")
 *
 *  CSS:
 *  - /WebSocialSaude/zf/public/css/redmond/jquery-ui-1.8.16.custom.css
 *  ou
 *  - /WebSocialSocial/lib/themes/ui-lightness/jquery-ui-1.8.10.custom.css
 */

// compatibilidade com a parte externa ao Zend Framework
if(typeof(baseUrl) == "undefined" )
	var baseUrl = "/WebSocialSaude/zf";

jQuery.fn.extend( {
	buscarResponsavel : function(opcoes) {
		var options = {
			url : baseUrl + "/paciente/buscar/",
			limit : 50,
			categoria: false,
			suffix: '',
			replace: true,
			minLength : 0,
			parametros: '',
			search: function() {},
			callback: function(event, ui) {
				return false
			},
			template : function(ul, item) {
				return jQuery("<li></li>").data("item.autocomplete", item).append(
					"<a><strong>" + item.label + "</strong>" +
					"<br><strong>Data Nasc.: </strong>" + (item.data.usu_datanasc !== null ? item.data.usu_datanasc : '') +
					" - <strong>CNS: </strong>" + (item.data.usu_cartao_sus !== null ? item.data.usu_cartao_sus : '') +
					"<br><strong>Mãe: </strong>" + (item.data.usu_mae !== null ? item.data.usu_mae : '') +
					"</a>&nbsp;"
				).appendTo(ul);
			}
		}

		options = jQuery.extend(options, opcoes); // junta as opções do usuário

		var obj = jQuery(this);

		if(!obj.size())
			return false;

		var _renderMenu = false;

		if(options.categoria){
			_renderMenu = function( ul, items ) {
				var self = this,
				currentCategory = "";
				$.each( items, function( index, item ) {
					if ( item.data[options.categoria] != currentCategory ) {
						ul.append( "<li><span class='ui-state-active' style='display: inline-block; width:100%; margin: 2px 5px 0 0'>" + item.data[options.categoria] + "</span></li>" );
						currentCategory = item.data[options.categoria];
					}
					self._renderItem( ul, item );
				});
			}
		}

		var auto = jQuery(this).autocomplete( {
			source : options.url + "?l="+options.limit + options.parametros,
			minLength : options.minLength,
			search: function(){
				jQuery(this).css("background","url('/WebSocialComum/imgs/loading.gif') no-repeat center right");
				options.search();
			},
			open: function(){
				jQuery(this).css("background","none");
			},
			close: function(){
				jQuery(this).css("background","none");
			},
			focus: function( event, ui ) {
				return false;
			},
			select : function(event, ui) {
				if (ui.item && ui.item.id) {
					for ( var i in ui.item.data) {
						jQuery("#usu_responsavel_nome").val(ui.item.data[i]);
						jQuery("#efc_usu_responsavel").val(ui.item.id);

					}
					if(options.replace){
						obj.val(ui.item.label);
					};

					if(options.url.indexOf("paciente")> -1  && ui.item.data.usu_bloqueado){
						mensagem("Atenção","Paciente bloqueado", 200, 120);
					}
				}

				options.callback(event, ui);
				return false;
			}
		}).data("autocomplete");

		auto._renderItem = options.template;
		if(options.categoria){
			auto._renderMenu = _renderMenu;
		}
	}
});
