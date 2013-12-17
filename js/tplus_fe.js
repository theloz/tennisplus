/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
jQuery(document).ready(function($) {
        if($('.tp_matches_table').length){
                $('.tp_matches_table').dataTable();
        }
        if($('.tp_matches_table_single').length){
                $('.tp_matches_table_single').dataTable({
                        "bPaginate": false,
                        "bInfo": false,
                        "bFilter": false,
                });
        }
});