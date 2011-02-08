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

        // validate checkboxes
        $.tools.validator.fn('input:checkbox', '|',
        function(input, value) {
            if (input.parent().parent().find('input:checkbox.required_one').length > 0 && input.parent().parent().find('input:checkbox:checked').length > 0 && input.find('[class*=required_one]').length > 0) {
                return false;
            } else {
                return true;
            }
        });

        // validate time fields
        $('input[type=time]').addClass('powermail_time').each(function(i){
            // check if part of datetime field
            if($(this).prevAll('input.powermail_datetime').length > 0) {
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
        
        // add tabs to fieldsets for multiple page
        $('ul.powermail_multiplejs_tabs li a:first').addClass('act'); // first tab with class "act"
        $('ul.powermail_multiplejs_tabs').tabs('div.fieldsets > fieldset'); // enable tabs()
        $('ul.powermail_multiplejs_tabs li a').click(function() { // change "act" on click
            $('ul.powermail_multiplejs_tabs li a').removeClass('act');
            $(this).addClass('act');
        });
    });
})(jQuery);