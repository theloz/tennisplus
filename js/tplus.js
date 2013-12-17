jQuery(document).ready(function($) {
        $('.tplus_date').datepicker({
                dateFormat : 'yy-mm-dd'
        });
        $(function() {
                $( "#date_start" ).datepicker({
                        defaultDate: "+1w",
                        dateFormat : 'yy-mm-dd',
                        changeMonth: true,
                        numberOfMonths: 3,
                        onClose: function( selectedDate ) {
                        $( "#date_end" ).datepicker( "option", "minDate", selectedDate );
                        }
                });
                $( "#date_end" ).datepicker({
                        defaultDate: "+1w",
                        dateFormat : 'yy-mm-dd',
                        changeMonth: true,
                        numberOfMonths: 3,
                        onClose: function( selectedDate ) {
                        $( "#date_start" ).datepicker( "option", "maxDate", selectedDate );
                        }
                });
        });
});


