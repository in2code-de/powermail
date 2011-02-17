// Function to update checkboxes
function insertCheckboxValueToHiddenField(id) {
	var checkid = 'check_' + id;
	var valueid = 'value_' + id;
	var checked = document.getElementsByName(checkid)[0].checked;
	var cvalue = document.getElementsByName(valueid)[0].value;

	if(checked == false) {
		document.getElementById(id).value = '';
	} else {
		document.getElementById(id).value = cvalue;
	}
}

// Maxlength for textareas
function checkTextArea(obj, maxLength) {
	var textArea = obj;
	var length = textArea.value.length;
	
	if (length > maxLength) {
		textArea.value = textArea.value.substr(0, maxLength);
	}
}

(function($) {
    $(function(){

        // initialize range input
        $(':range').rangeinput();

        $('.tx_powermail_pi1_form input:checkbox').click(function(){$(this).parent().parent().find('input:checkbox').blur();});


        // time validation
        $.tools.validator.fn('input[type=time]', 'required',
            function(input, value) {
                if(value != '' && !/\d\d:\d\d/.test(value)) {
                    return false;
                } else {
                    time = value.split(':');
                    hour = parseInt(time[0]);
                    minute = parseInt(time[1]);
                    if (hour > 23 || hour < 0 || minute > 59 || minute < 0) {
                        return false;
                    }
                    if (input.prevAll('input.powermail_datetime').length > 0) {
                        oldDate = new Date(input.prev('input').val() * 1000);
                        year = oldDate.getFullYear();
                        month = oldDate.getMonth();
                        day = oldDate.getDate();
                        secondsToAdd = hour * 3600 + minute * 60;
                        timestamp = (new Date(year, month, day).getTime() / 1000) + secondsToAdd;
                        input.prev('input').val(timestamp);
                    }
                    return true;
                }
            }
        );

        // validate time fields
        $('input[type=time]').addClass('powermail_time').each(function(i){
            // check if part of datetime field
            if ($(this).prevAll('input.powermail_datetime').length > 0) {
                if ($(this).prev('input').val() != '') {
                    timestamp = new Date($(this).prev('input').val() * 1000);
                    h = timestamp.getHours();
                    m = timestamp.getMinutes();
                    h = (h < 10) ? '0' + h : h;
                    m = (m < 10) ? '0' + m : m;
                    $(this).val(h + ':' + m);
                } else {
                    $(this).attr('placeholder','00:00');
                }
            }
        });

        // select validation
        $.tools.validator.fn('input[type=select].required', 'required',
            function(input, value) {
                return value.length > 0;
            }
        );

        // add tabs to fieldsets for multiple page
        $('ul.powermail_multiplejs_tabs li a:first').addClass('act'); // first tab with class "act"
        if ($.ui && typeof($.ui.tabs) == 'function') {
            // Add UI tabs
            $('.powermail_multiple_js .powermail_multiplejs_tabs_item a').each(function(id, item){
                var temp = item.href.split('#');
                var temp_last = temp[temp.length - 1];
                var search = /^tx\-powermail\-pi1\_fieldset/;
                if (search.test(temp_last)){
                    item.href = '#' + temp_last;
                }
            });
            $('.powermail_multiple_js').tabs(); // enable UI tabs()
        } else {
            // Add TOOLS tabs
            $('ul.powermail_multiplejs_tabs').tabs('div.fieldsets > fieldset'); // enable TOOLS tabs()
        }
        $('ul.powermail_multiplejs_tabs li a').click(function() { // change "act" on click
            $('ul.powermail_multiplejs_tabs li a').removeClass('act');
            $(this).addClass('act');
        });
    });
})(jQuery);