jQuery(document).ready(function($) {
        $('.tplus_date').datepicker({
                dateFormat : 'yy-mm-dd'
        });
        $(function() {
                $( "#plprovince" ).autocomplete({
                        source: "../wp-content/plugins/tennisplus_plugin/utils/getprovince.php",
                        minLength: 2,
                });
                $( "#plcity" ).autocomplete({
                        source: "../wp-content/plugins/tennisplus_plugin/utils/getcomuni.php",
                        minLength: 3,
                });
        });
});


