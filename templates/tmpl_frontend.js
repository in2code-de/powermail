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
    
    $.tools.validator.localizeFn('input:time', { en: '###VALIDATOR_INVALIDTIME###'});
    $.tools.validator.localizeFn('input:checkbox', { en: '###VALIDATOR_ONE_REQUIRED###'});
    $.tools.validator.localizeFn('input:radio', { en: '###VALIDATOR_ONE_REQUIRED###'});
    $.tools.validator.localizeFn('input:select.required', { en: '###VALIDATOR_LABEL_REQUIRED###'});

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
                y = parseInt(this.getValue('yyyy'));
                m = parseInt(this.getValue('m'))-1;
                d = parseInt(this.getValue('d'));
                timestampOfDate = new Date(y,m,d).getTime() / 1000;
                oldTimestamp = this.getInput().nextAll('input[type=hidden]').val();
                if(this.getInput().nextAll('input[type=time]').length > 0 && oldTimestamp != '' && parseInt(oldTimestamp) == oldTimestamp) {
                    oldDate = new Date(oldTimestamp * 1000);
                    h = oldDate.getHours();
                    m = oldDate.getMinutes();
                    timestampOfDate += h * 3600 + m * 60;
                }
                this.getInput().nextAll('input[type=hidden]').val(timestampOfDate);
            }
        }).each(function(i){
                initTimestamp = $(this).nextAll('input[type=hidden]').val();
                if(initTimestamp != '' && parseInt(initTimestamp) == initTimestamp) {
                    $(this).data('dateinput').setValue(new Date(parseInt(initTimestamp)*1000));
                }
        });

        $.tools.validator.fn('input:checkbox', 'required',
            function(input, value) {
                checkboxes = input.parent().parent().find('input:checkbox');
                if (checkboxes.filter('.required_one').length > 0) {
                    if (checkboxes.filter(':checked').length == 0) {
                        return (input.filter('.required_one').length == 0);
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

        if (!###VALIDATOR_DISABLE###) {
            if (($.browser.mozilla && $.browser.version.slice(0,3) == '2.0') || ($.browser.webkit && $.browser.version.slice(0,3) >= 534)) {
                // workaround for jquery tools 1.2.5 bug with firefox 4.0 & chrome 11
                $('.tx_powermail_pi1_form').attr('novalidate', 'novalidate');
            }
		    console.info($.browser);
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
                $(this).parent().parent().find('a:NOT(.current)').each(function(id, item){
                    var temp = item.href.split('#');
                    var resetSelector = $('#' + temp[temp.length - 1] + ' :input');
                    powermail_validator.data('validator').reset(resetSelector);
                });
            }
        });
    });
})(jQuery);