/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
jQuery(document).ready(function($) {
        if($('.tp_matches_table').length){
                $('.tp_matches_table').dataTable({
                        "oLanguage": {
                                "sUrl": "/wp-content/plugins/tennisplus_plugin/js/dataTables.italian.txt"
                        },
                        "bJQueryUI": true,
                        //"sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>"
                        //"sPaginationType": "full_numbers"
                });
                $('.tp_matches_table th').css('fontSize','0.7em');
                $('.tp_matches_table td').css('fontSize','0.8em');
                /*$('.tp_matches_table tr.even').css('background', 'transparent');
                $('.tp_matches_table tr.odd').css('background', '#ffcc00');
                $('.tp_matches_table tr').hover(
                        function(){
                                $(this).css('background','#ffcc00')
                        },
                        function(){
                                $(this).css('background','transparent')
                        }
                );*/
        }
        if($('.tp_matches_table_single').length){
                $('.tp_matches_table_single').dataTable({
                        "bPaginate": false,
                        "bInfo": false,
                        "bFilter": false,
                        /*"oLanguage": {
                                "sUrl": "/wp-content/plugins/tennisplus_plugin/js/dataTables.italian.txt"
                        },*/
                        "bJQueryUI": true,
                        "sPaginationType": "full_numbers"
                });
                $('.tp_matches_table_single th').css('fontSize','0.7em');
                $('.tp_matches_table_single td').css('fontSize','0.8em');
                $('.tp_matches_table_single tr.even').css('background', 'transparent');
                $('.tp_matches_table_single tr.odd').css('background', '#ffcc00');
                $('.tp_matches_table_single tr').hover(
                        function(){
                                $(this).css('background','#ffcc00')
                        },
                        function(){
                                $(this).css('background','transparent')
                        }
                );
        }
        if($('#tp_friendly_tabs').length){
                var $tabs = $('#tp_friendly_tabs');

                $tabs.tabs({
                    fx: {
                        opacity: 'toggle'
                    }
                });
                $(".tp_nexttab").click(function() {
                        var selected = $("#tp_friendly_tabs").tabs("option", "active");
                        $("#tp_friendly_tabs").tabs("option", "active", selected + 1);
                });
                $(".tp_prevtab").click(function() {
                        var selected = $("#tp_friendly_tabs").tabs("option", "active");
                        $("#tp_friendly_tabs").tabs("option", "active", selected - 1);
                });
        }
});