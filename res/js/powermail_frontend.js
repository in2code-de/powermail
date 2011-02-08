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

        var powermail_validator = $('.tx_powermail_pi1_form').validator({
            inputEvent: 'blur',
            grouped: true,
            singleError: false,
            position: 'top right',
            offset: [-5, -20],
            message: '<div><em/></div>'

        });

        // initialize range input
        $(':range').rangeinput();
        
        // multiple checkbox validation
        $.tools.validator.fn('input:checkbox', 'required',
            function(input, value) {
                checkboxes = input.parent().parent().find('input:checkbox');
                if (checkboxes.filter('.required_one').length > 0) {
                    if (checkboxes.filter(':checked').length == 0) {
                        return (input.filter('.required_one').length == 0);
                    } else {
                        powermail_validator.data('validator').reset(checkboxes);
                        return true;
                    }
                } else {
                    return true;
                }
            }
        );

        $('input:radio.required_one').parent().parent().append($(this).find('input:radio.required_one').clone()).find('> input').removeAttr('tabindex').removeAttr('id').removeAttr('class').val('').attr('checked', 'checked').attr('style','visibility:hidden;height:1px;');

        // multiple radio validation
        $.tools.validator.fn('input:radio', 'required',
            function(input, value) {
                radios = input.parent().parent().find('input:radio');
                if (radios.filter('.required_one').length > 0) {
                    //alert(value);
                    if (value == '') {
                        return (input.filter('.required_one').length == 0);
                    } else {
                        powermail_validator.data('validator').reset(radios);
                        return true;
                    }
                } else {
                    return true;
                }
            }
        );

        // time validation
        $.tools.validator.fn('input[type=time]', 'required',
            function(input, value) {
                if(value != '' && !/\d\d:\d\d/.test(value)) {
                    return false;
                } else {
                    time = value.split(':');
                    hour = parseInt(time[0]);
                    minute = parseInt(time[1]);
                    if(hour > 23 || hour < 0 || minute > 59 || minute < 0) {
                        return false;
                    }
                    if(input.prevAll('input.powermail_datetime').length > 0) {
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

        // select validation
        $.tools.validator.fn('input[type=select].required', 'required',
            function(input, value) {
                return value.length > 0;
            }
        );
        
        // add tabs to fieldsets for multiple page
        $('ul.powermail_multiplejs_tabs li a:first').addClass('act'); // first tab with class "act"
        $('ul.powermail_multiplejs_tabs').tabs('div.fieldsets > fieldset'); // enable tabs()
        $('ul.powermail_multiplejs_tabs li a').click(function() { // change "act" on click
            $('ul.powermail_multiplejs_tabs li a').removeClass('act');
            $(this).addClass('act');
        });
    });
})(jQuery);