<?php
	header("Content-Type: text/html; charset=ISO-8859-1", true);

	include "../global.php";
	$sqlUnidade = "SELECT * 
					 FROM unidade
					 ORDER BY uni_desc";
	$queryUnidade = pg_query($sqlUnidade);
	$i=0;
	$coluns = "'Procedimento',";
	$colunsConteudo .= "{name: 'proc_s', index: 'id_proc', width: 155, align: 'center', sorttype: 'int', frozen:true},";
	
	while ($regUnidade = pg_fetch_array($queryUnidade)){
		$coluns .= "'$regUnidade[uni_desc]',";
		$colunsConteudo .= "{name: 'quantidade_$regUnidade[uni_codigo]',index: 'id_$regUnidade[uni_codigo]', width: 155, align: 'center', sorttype: 'int', frozen:false, formatter:input},";
	}
	$parametros = substr($coluns, 0, -1);
	$parametrosConteudo = substr($colunsConteudo, 0, -1);
	
	// Até essa linha ele monta a GRID sem informações.
	
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>http://stackoverflow.com/q/8686616/315935</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" >

    <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/redmond/jquery-ui.css" />
    <link rel="stylesheet" type="text/css" href="http://www.ok-soft-gmbh.com/jqGrid/jquery.jqGrid-4.3.1/css/ui.jqgrid.css" />
    <style type="text/css">
        html, body { font-size: 75%; }
    </style>

    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
    <script type="text/javascript" src="http://www.ok-soft-gmbh.com/jqGrid/jquery.jqGrid-4.3.1/js/i18n/grid.locale-en.js"></script>
    <script type="text/javascript">
        $.jgrid.no_legacy_api = true;
        $.jgrid.useJSON = true;
    </script>
    <script type="text/javascript" src="http://www.ok-soft-gmbh.com/jqGrid/jquery.jqGrid-4.3.1/js/jquery.jqGrid.min.js"></script>
    <style type="text/css">
        th.ui-th-column div {
            /* see http://stackoverflow.com/a/7256972/315935 for details */
            word-wrap: break-word;      /* IE 5.5+ and CSS3 */
            white-space: -moz-pre-wrap; /* Mozilla, since 1999 */
            white-space: -pre-wrap;     /* Opera 4-6 */
            white-space: -o-pre-wrap;   /* Opera 7 */
            white-space: pre-wrap;      /* CSS3 */
            overflow: hidden;
            height: auto !important;
            vertical-align: middle;
        }
        .ui-jqgrid tr.jqgrow td {
            white-space: normal !important;
            height: auto;
            vertical-align: middle;
            padding-top: 2px;
            padding-bottom: 2px;
        }
        .ui-jqgrid .ui-jqgrid-htable th.ui-th-column {
            padding-top: 2px;
            padding-bottom: 2px;
        }
        .ui-jqgrid .frozen-bdiv, .ui-jqgrid .frozen-div {
            overflow: hidden;
        }
    </style>

    <script type="text/javascript"><!--
    //<![CDATA[
        /*global $ */
        /*jslint browser: true, nomen: true */
        $(document).ready(function () {

        	function isInt(x) {
        	  var y=parseInt(x);
        	  if (isNaN(y)) return false;
        	  return x==y && x.toString()==y.toString();
        	}

        	var input = function(cellvalue,options,rowdata){
                if( isInt(cellvalue) ){
					return "<input value=\""+cellvalue+"\" style=\"width:20px; text-align:center;\" />";
                }
            }

            
            'use strict';
            var $grid = $("#list"),
                resizeColumnHeader = function () {
                    var rowHight, resizeSpanHeight,
                        // get the header row which contains
                        headerRow = $(this).closest("div.ui-jqgrid-view")
                            .find("table.ui-jqgrid-htable>thead>tr.ui-jqgrid-labels");
        
                    // reset column height
                    headerRow.find("span.ui-jqgrid-resize").each(function () {
                        this.style.height = '';
                    });
        
                    // increase the height of the resizing span
                    resizeSpanHeight = 'height: ' + headerRow.height() + 'px !important; cursor: col-resize;';
                    headerRow.find("span.ui-jqgrid-resize").each(function () {
                        this.style.cssText = resizeSpanHeight;
                    });
        
                    // set position of the dive with the column header text to the middle
                    rowHight = headerRow.height();
                    headerRow.find("div.ui-jqgrid-sortable").each(function () {
                        var ts = $(this);
                        ts.css('top', (rowHight - ts.outerHeight()) / 2 + 'px');
                    });
                },
                fixPositionsOfFrozenDivs = function () {
                    var $rows;
                    if (typeof this.grid.fbDiv !== "undefined") {
                        $rows = $('>div>table.ui-jqgrid-btable>tbody>tr', this.grid.bDiv);
                        $('>table.ui-jqgrid-btable>tbody>tr', this.grid.fbDiv).each(function (i) {
                            var rowHight = $($rows[i]).height(), rowHightFrozen = $(this).height();
                            if ($(this).hasClass("jqgrow")) {
                                $(this).height(rowHight);
                                rowHightFrozen = $(this).height();
                                if (rowHight !== rowHightFrozen) {
                                    $(this).height(rowHight + (rowHight - rowHightFrozen));
                                }
                            }
                        });
                        $(this.grid.fbDiv).height(this.grid.bDiv.clientHeight);
                        $(this.grid.fbDiv).css($(this.grid.bDiv).position());
                    }
                    if (typeof this.grid.fhDiv !== "undefined") {
                        $rows = $('>div>table.ui-jqgrid-htable>thead>tr', this.grid.hDiv);
                        $('>table.ui-jqgrid-htable>thead>tr', this.grid.fhDiv).each(function (i) {
                            var rowHight = $($rows[i]).height(), rowHightFrozen = $(this).height();
                            $(this).height(rowHight);
                            rowHightFrozen = $(this).height();
                            if (rowHight !== rowHightFrozen) {
                                $(this).height(rowHight + (rowHight - rowHightFrozen));
                            }
                        });
                        $(this.grid.fhDiv).height(this.grid.hDiv.clientHeight);
                        $(this.grid.fhDiv).css($(this.grid.hDiv).position());
                    }
                },
                fixGboxHeight = function () {
                    var gviewHeight = $("#gview_" + $.jgrid.jqID(this.id)).outerHeight(),
                        pagerHeight = $(this.p.pager).outerHeight();
        
                    $("#gbox_" + $.jgrid.jqID(this.id)).height(gviewHeight + pagerHeight);
                    gviewHeight = $("#gview_" + $.jgrid.jqID(this.id)).outerHeight();
                    pagerHeight = $(this.p.pager).outerHeight();
                    $("#gbox_" + $.jgrid.jqID(this.id)).height(gviewHeight + pagerHeight);
                };
        
            $grid.jqGrid({
    			datatype: "json", 
                //data: mydata,
                url:"retornoProcedimentos.php",
                colNames: [<?=$parametros?>],
                colModel: [
                    <?=$parametrosConteudo?>
                ],
                //rowNum: 10,
                //rowList: [5, 10, 20],
                //pager: '#pager',
                gridview: true,
                rownumbers: true,
                sortname: 'invdate',
                viewrecords: true,
                sortorder: 'desc',
                caption: 'Quantidade de procedimentos por unidade',
                height: '230',
                shrinkToFit: false,
                width: '900',
                afterInsertRow: function(rowid, aData){
                	$grid.jqGrid('setCell',rowid,'log','<a href="/publicidade/log/cod/'+aData.log+'" target="_blank">Link</a>',{color:'blue'});
           		},
                resizeStop: function () {
                    resizeColumnHeader.call(this);
                    fixPositionsOfFrozenDivs.call(this);
                    fixGboxHeight.call(this);
                },
                loadComplete: function () {
                    fixPositionsOfFrozenDivs.call(this);
                }
            });
            $grid.jqGrid('gridResize', {
                minWidth: 450,
                stop: function () {
                    fixPositionsOfFrozenDivs.call($grid[0]);
                    fixGboxHeight.call($grid[0]);
                }
            });
            resizeColumnHeader.call($grid[0]);
            $grid.jqGrid('setFrozenColumns');
            $grid[0].p._complete.call($grid[0]);
            fixPositionsOfFrozenDivs.call($grid[0]);
        });
    //]]>
    --></script>
</head>
<body>
    <table id="list"><tr><td></td></tr></table>
    <div id="pager"></div>
</body>
</html>