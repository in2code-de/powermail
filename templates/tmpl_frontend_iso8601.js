// function to update checkboxes
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

// maxlength for textareas
function checkTextArea(obj, maxLength) {
	var textArea = obj;
	var length = textArea.value.length;
	
	if (length > maxLength) {
		textArea.value = textArea.value.substr(0, maxLength);
	}
}

Date.prototype.setISO8601 = function (string) {
    var regexp = "([0-9]{4})(-([0-9]{2})(-([0-9]{2})" +
        "(T([0-9]{2}):([0-9]{2})(:([0-9]{2})(\.([0-9]+))?)?" +
        "(Z|(([-+])([0-9]{2}):([0-9]{2})))?)?)?)?";
    var d = string.match(new RegExp(regexp));

    var offset = 0;
    var date = new Date(d[1], 0, 1);

    if (d[3]) { date.setMonth(d[3] - 1); }
    if (d[5]) { date.setDate(d[5]); }
    if (d[7]) { date.setHours(d[7]); }
    if (d[8]) { date.setMinutes(d[8]); }
    if (d[10]) { date.setSeconds(d[10]); }
    if (d[12]) { date.setMilliseconds(Number("0." + d[12]) * 1000); }
    if (d[14]) {
        offset = (Number(d[16]) * 60) + Number(d[17]);
        offset *= ((d[15] == '-') ? 1 : -1);
    }

    offset -= date.getTimezoneOffset();
    time = (Number(date) + (offset * 60 * 1000));
    this.setTime(Number(time));
}

function ISODateString(d) {
    function pad(n){
        return n < 10 ? '0' + n : n
    }
    return d.getUTCFullYear() + '-'
    + pad(d.getUTCMonth() + 1) + '-'
    + pad(d.getUTCDate()) + 'T'
    + pad(d.getUTCHours()) + ':'
    + pad(d.getUTCMinutes()) + ':'
    + pad(d.getUTCSeconds()) + 'Z'
}

(function($) {
    $.tools.validator.localize('en', {
        '*': '###VALIDATOR_LABEL_PLEASE_CORRECT###',
        '[required]': '###VALIDATOR_LABEL_REQUIRED###',
        ':email': '###VALIDATOR_LABEL_EMAIL###',
        ':url': '###VALIDATOR_LABEL_URL###',
        ':number': '###VALIDATOR_LABEL_NUMBER###',
        ':digits': '###VALIDATOR_LABEL_DIGITS###',
        ':username': '###VALIDATOR_LABEL_ALPHANUM###',
        ':date': '###VALIDATOR_LABEL_DATE###',
        ':datetime': '###VALIDATOR_LABEL_DATETIME###',
        ':time': '###VALIDATOR_INVALIDTIME###',
        '[max]': '###VALIDATOR_LABEL_MAX###',
        '[min]': '###VALIDATOR_LABEL_MIN###'
    });
    
    $.tools.dateinput.localize('en', {
        'months': '###VALIDATOR_DATEINPUT_MONTH###',
        'shortMonths': '###VALIDATOR_DATEINPUT_SHORTMONTH###',
        'days': '###VALIDATOR_DATEINPUT_DAYS###',
        'shortDays': '###VALIDATOR_DATEINPUT_SHORTDAYS###'
    });

    $(function(){
        $(':date').dateinput({
            format: '###VALIDATOR_DATEINPUT_FORMAT###',
            firstDay: parseInt('###VALIDATOR_DATEINPUT_FIRSTDAY###'),
            selectors: true,
            disabled: false,
            readonly: false,
            yearRange: [-99, 99],
            change: function(event, date){
	            var year = parseInt(this.getValue('yyyy'));
	            var month = parseInt(this.getValue('m'));
	            var day = parseInt(this.getValue('d'));
	            var hour = 0;
	            var minute = 0;
	            var second = 0;
                if(this.getInput().nextAll('input[type=time]').length > 0) {
	                // it's a datetime field -> get time from old value
	                var oldDateTime = this.getInput().nextAll('input[type=hidden]').val();
                    if (oldDateTime != '' && parseInt(oldDateTime) != oldDateTime) {
						var oldDateTimeObject = new Date();
						oldDateTimeObject.setISO8601(oldDateTime);
						hour = oldDateTimeObject.getHours();
						minute = oldDateTimeObject.getMinutes();
                    }
                }
	            var newDateTimeObject = new Date(Date.UTC(year, month - 1, day, hour, minute, second));
                //this.getInput().nextAll('input[type=hidden]').val(ISODateString(newDateTimeObject));
	            this.getInput().nextAll('input[type=hidden]').val(newDateTimeObject.getTime() / 1000);
	            console.log('ISO8601: ' + (ISODateString(newDateTimeObject)));
	            console.log('timestamp: ' + (newDateTimeObject.getTime() / 1000));
            }
        }).each(function(i){
                var initDateTime = $(this).nextAll('input[type=hidden]').val();
                if(initDateTime != '' && parseInt(initDateTime) != initDateTime) {
	                var helper = new Date();
	                helper.setISO8601(initDateTime);
                    $(this).data('dateinput').setValue(helper);
                }
        });

        // time validation
        $.tools.validator.fn('input[type=time]', '###VALIDATOR_LABEL_REQUIRED###',
            function(input, value) {
                if(value != '' && !/\d\d:\d\d/.test(value)) {
                    return false;
                } else {
                    var time = value.split(':');
                    var hour = parseInt(time[0]);
                    var minute = parseInt(time[1]);
	                var second = 0;
                    if (hour > 23 || hour < 0 || minute > 59 || minute < 0) {
                        return false;
                    }
	                var oldDateTime = input.prev('input[type=hidden]').val();
                    if (input.prevAll('input.powermail_datetime').length > 0 && parseInt(oldDateTime) != oldDateTime) {
	                    var oldDateTimeObject = new Date();
                        oldDateTimeObject.setISO8601(oldDateTime);
	                    var year = oldDateTimeObject.getFullYear();
	                    var month = oldDateTimeObject.getMonth();
	                    var day = oldDateTimeObject.getDate();
	                    var newDateTimeObject = new Date(year, month, day, hour, minute, second);
                        input.prev('input[type=hidden]').val(ISODateString(newDateTimeObject));
                    }
                    return true;
                }
            }
        );

        // preset time fields
        $('input[type=time]').addClass('powermail_time').each(function(i){
            // check if part of datetime field
            if ($(this).prevAll('input.powermail_datetime').length > 0) {
	            var hour = '0';
	            var minute = '0';
	            var oldDateTime = $(this).prev('input[type=hidden]').val();
                if (oldDateTime != '' && parseInt(oldDateTime) != oldDateTime) {
	                var oldDateTimeObject = new Date();
                    oldDateTimeObject.setISO8601(oldDateTime);
	                hour = oldDateTimeObject.getHours();
	                minute = oldDateTimeObject.getMinutes();
                }
	            hour = (hour < 10) ? '0' + hour : hour;
	            minute = (minute < 10) ? '0' + minute : minute;
	            $(this).val(hour + ':' + minute);
            }
        });

        $.tools.validator.fn('input:checkbox',
            function(input, value) {
                checkboxes = input.parent().parent().find('input:checkbox');
                if (checkboxes.filter('.required_one').length > 0) {
                    if (checkboxes.filter(':checked').length == 0) {
                        return (input.filter('.required_one').length == 0) ? true : '###VALIDATOR_LABEL_ONE_REQUIRED###';
                    } else {
                        powermail_validator.data('validator').reset(checkboxes);
                    }
                }
                return true;
            }
        );

        // initialize range input
        $(':range').rangeinput();

        $('.tx_powermail_pi1_form input:checkbox').click(function(){$(this).parent().parent().find('input:checkbox').blur();});

        // select validation
        $.tools.validator.fn('select', '###VALIDATOR_LABEL_ONE_REQUIRED###',
            function(el, value) {
                return value.length > 0;
            }
        );

        if (!###VALIDATOR_DISABLE###) {
            powermail_validator = $('.tx_powermail_pi1_form').validator({
                position: 'top right',
                offset: [-5, -20],
                message: '<div><em/></div>',
                inputEvent: 'blur',
                grouped: true,
                singleError: false,
                formEvent : 'submit',
                onBeforeValidate: function(e, els) {
                   clearPlaceholderValue(e, els);
                },
                onBeforeFail: function(e, els, matcher) {
                   setPlaceholderValue(e, els, matcher);
                },
	            onFail: function(e, els){
		            $('html,body').animate({ "scrollTop": $(els[0].input).offset().top - 50}, 1000);
	            }
            });
        }

        // add placeholder attribute behavior in web browsers that don't support it
        var fakeInput = document.createElement('input'),
            placeHolderSupport = ('placeholder' in fakeInput);
        clearPlaceholderValues = function () {
            if (!placeHolderSupport) {
                $('input:text, textarea').each(function(i){
                    if ($(this).val() === $(this).attr('placeholder')) {
                        $(this).val('');
                    }
                });
            }
        };

        clearPlaceholderValue = function (e, els) {
            if (!placeHolderSupport) {
                $(this).removeClass('placeholder');
                if (els.val() === els.attr('placeholder')) {
                    els.val('');
                }
            }
        };

        setPlaceholderValue = function (e, els, matcher) {
            if (!placeHolderSupport) {
                if (els.val().length === 0 && e.keyCode != 9 && els.attr('placeholder') != undefined) {
                    els.val(els.attr('placeholder'));
                    els.addClass('placeholder');
                }
            }
        };

        if (!placeHolderSupport) {
            $('input:text, textarea').each(function(i){
                if($(this).val().length === 0) {
                    var originalText = $(this).attr('placeholder');
                    $(this).val(originalText);
                    $(this).addClass('placeholder');
                    $(this).bind('focus', function (i) {
                        $(this).removeClass('placeholder');
                        if ($(this).val() === $(this).attr('placeholder')) {
                            $(this).val('');
                        }
                    });
	                $(this).bind('blur', function (i) {
		                if (!$(this).attr('required') && $(this).val() === '') {
			                $(this).val($(this).attr('placeholder'));
		                }
	                });
                }
            });
            // empties the placeholder text at form submit if it hasn't changed
            $('form').bind('submit', function () {
                clearPlaceholderValues();
            });

            // clear at window reload to avoid it stored in autocomplete
            $(window).bind('unload', function () {
                clearPlaceholderValues();
            });
        }

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
            // add TOOLS tabs
            $('ul.powermail_multiplejs_tabs').tabs('div.fieldsets > fieldset');
        }
        $('ul.powermail_multiplejs_tabs li a').click(function() { // change "act" on click
            $('ul.powermail_multiplejs_tabs li a').removeClass('act');
            $(this).addClass('act');
            // reset error messages if js validation is enabled
            if (!###VALIDATOR_DISABLE###) {
                $(this).parent().parent().find('a').not('.current').each(function(id, item){
                    var temp = item.href.split('#');
                    var resetSelector = $('#' + temp[temp.length - 1] + ' :input');
                    powermail_validator.data('validator').reset(resetSelector);
                });
            }
        });
    });
})(jQuery);