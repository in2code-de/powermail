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

	$(function() {

		// add placeholder attribute behavior in web browsers that don't support it
		var fakeInput = document.createElement('input'),
				placeHolderSupport = ('placeholder' in fakeInput);
		clearPlaceholderValues = function () {
			if (!placeHolderSupport) {
				$('input:text, textarea').each(function(i) {
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
			$('input:text, textarea').each(function(i) {
				if ($(this).val().length === 0) {
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

        $('form.tx_powermail_pi1_form input.powermail_date').each( function() {
            var uid = $(this).attr('id');
            var value = $(this).attr('value') != '' ? $(this).attr('value'): '';
            $(this).removeAttr('name').removeAttr('placeholder').removeAttr('value').after('<input type="hidden" name="tx_powermail_pi1[' + uid +  ']" value="' + value + '" />');
        });

        $('form.tx_powermail_pi1_form input.powermail_datetime').each( function() {
            var uid = $(this).attr('id');
            var value = $(this).val() != '' ? $(this).val() : '';
            $(this).removeAttr('name').removeAttr('placeholder').removeAttr('value').after('<input type="hidden" name="tx_powermail_pi1[' + uid +  ']" value="' + value + '" /><input type="time" size="5" maxlength="5" class="powermail_datetime powermail_time" />');
        });

        $('form.tx_powermail_pi1_form input.powermail_check').each( function() {
            var uid = $(this).attr('id').replace(/check_/, '');
            var name = $(this).attr('name');
            var value = $(this).attr('value');
            var checked = $(this).attr('checked');
            $(this).removeAttr('name').removeAttr('value').after('<input type="hidden" id="value_' + uid +  '" value="' + value + '" /><input type="hidden" id="' + uid + '" name="' + name + '"' + (checked ? ' value="' + value + '"':'') + ' />');
        });

		$(':date').dateinput({
			format: '###VALIDATOR_DATEINPUT_FORMAT###',
			firstDay: parseInt('###VALIDATOR_DATEINPUT_FIRSTDAY###'),
			selectors: ###SHOW_SELECTORS###,
			trigger: ###SHOW_TRIGGER_ICON###,
			disabled: false,
			readonly: false,
			yearRange: [-99, 99],
			change: function(event, date) {
				var y = parseInt(this.getValue('yyyy'));
				var m = parseInt(this.getValue('m')) - 1;
				var d = parseInt(this.getValue('d'));
				var timestampOfDate = new Date(y, m, d).getTime() / 1000;
				var timezoneOffset = new Date(y, m, d).getTimezoneOffset() * 60;
				timestampOfDate -= timezoneOffset;
				var oldTimestamp = this.getInput().nextAll('input[type=hidden]').val();
				if (this.getInput().nextAll('input[type=time]').length > 0 && oldTimestamp != '' && parseInt(oldTimestamp) == oldTimestamp) {
					var oldDate = new Date(oldTimestamp * 1000);
					var hours = parseInt(oldDate.getUTCHours());
					var minutes = parseInt(oldDate.getUTCMinutes());
					timestampOfDate += parseInt(hours * 3600 + minutes * 60);
				}
				this.getInput().nextAll('input[type=hidden]').val(timestampOfDate);
			}
		}).each(function(i) {
					var initTimestamp = $(this).nextAll('input[type=hidden]').val();
					if (initTimestamp != '' && parseInt(initTimestamp) == initTimestamp) {
						var initDatetime = new Date(parseInt(initTimestamp) * 1000);
						var year = initDatetime.getUTCFullYear();
						var month = initDatetime.getUTCMonth();
						var day = initDatetime.getUTCDate();
						$(this).data('dateinput').setValue(year, month, day);
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

		$('form.tx_powermail_pi1_form input:checkbox').click(function() {
			$(this).parent().parent().find('input:checkbox').blur();
            var checkid = $(this).attr('id');
            var valueid = checkid.replace(/check_/, 'value_');
            var targetid = checkid.replace(/check_/, '');
            $('#' + targetid).val($(this).is(':checked') ? $('#' + valueid).val() : '');
		});

		// time validation
		$.tools.validator.fn('input[type=time]', '###VALIDATOR_LABEL_REQUIRED###',
				function(input, value) {
					if (value != '' && !/\d\d:\d\d/.test(value)) {
						return false;
					} else {
                        if (input.prev('input').val() != '') {
                            var time = value.split(':');
                            var hour = parseInt(time[0], 10);
                            var minute = parseInt(time[1], 10);
                            if (hour > 23 || hour < 0 || minute > 59 || minute < 0) {
                                return false;
                            }
                            if (input.prevAll('input.powermail_datetime').length > 0) {
                                var oldDate = new Date(input.prev('input').val() * 1000);
                                var year = oldDate.getUTCFullYear();
                                var month = oldDate.getUTCMonth();
                                var day = oldDate.getUTCDate();
                                var secondsToAdd = hour * 3600 + minute * 60;
                                var timestamp = (new Date(year, month, day, hour, minute, 0).getTime() / 1000);
                                var timezoneOffset = new Date(year, month, day, hour, minute, 0).getTimezoneOffset() * 60;
                                input.prev('input').val(timestamp - timezoneOffset);
                            }
                        }
						return true;
					}
				}
		);

		// validate time fields
		$('input[type=time]').each(function(i) {
			// check if part of datetime field
			if ($(this).prevAll('input.powermail_datetime').length > 0) {
				if ($(this).prev('input').val() != '') {
					var newDate = new Date(parseInt($(this).prev('input').val() * 1000));
					var h = parseInt(newDate.getUTCHours());
					var m = parseInt(newDate.getUTCMinutes());
					h = (h < 10) ? '0' + h : h;
					m = (m < 10) ? '0' + m : m;
					$(this).val(h + ':' + m);
				}
			}
		});

		// select validation
		$.tools.validator.fn('select[required="required"]', '',
				function(el, value) {
                    if (el.attr('multiple')) {
                        return value != null ? true: '###VALIDATOR_LABEL_ONE_REQUIRED###';
                    } else {
                        return value.length > 0 ? true : '###VALIDATOR_LABEL_SELECTION###';
                    }
				}
		);

		if (!###VALIDATOR_DISABLE###) {
			powermail_validator = $('form.tx_powermail_pi1_form').attr('novalidate','novalidate').validator({
				position: '###VALIDATOR_POSITION###',
				offset: [###VALIDATOR_OFFSET_Y###, ###VALIDATOR_OFFSET_X###],
				message: '###VALIDATOR_MESSAGE###',
				messageClass: '###VALIDATOR_MESSAGE_CLASS###',
				inputEvent: 'blur',
				grouped: true,
				singleError: ###VALIDATOR_SINGLE_ERROR###,
				formEvent : 'submit',
				onBeforeValidate: function(e, els) {
					clearPlaceholderValue(e, els);
				},
				onBeforeFail: function(e, els, matcher) {
					setPlaceholderValue(e, els, matcher);
				},
				onFail: function(e, els) {
                    if ($('ul.powermail_multiplejs_tabs li').length > 0) {
                        $('ul.powermail_multiplejs_tabs li a[href*="#' + $(els[0].input).closest('fieldset.tx-powermail-pi1_fieldset').attr('id') + '"]').click();
                        
                    }
					if (###SCROLL_TO_ERROR###) {
						$('html,body').animate({ "scrollTop": $(els[0].input).offset().top - 50}, 1000);
					}
				}
			});

            $('.tx_powermail_pi1_form input:radio').change(function(e) {
                powermail_validator.data('validator').reset($('.tx_powermail_pi1_form input:radio'));
            });

            var fakeTextarea = document.createElement('textarea'),
                textareaMaxlengthSupport = ('maxlength' in fakeTextarea);

            if (!textareaMaxlengthSupport) {
                $('textarea[maxlength]').each(function() {
                    $(this).bind('keypress blur', function() {
                        $(this).val($(this).val().substr(0, $(this).attr('maxlength')));
                    });
                });
            }

		}

        reinitializeValidator = function() {
            if (!###VALIDATOR_DISABLE###) {
                var validatorConf = powermail_validator.data('validator').getConf();
                powermail_validator.data('validator').destroy();
                powermail_validator = $('form.tx_powermail_pi1_form').validator(validatorConf);
            }
        }

        $.fn.getCountryZones = function() {
            $.ajax({
                    url: '/index.php',
                    type: "GET",
                    data: {
                        eID: 'tx_powermail::countryzones',
                        iso2: $(this).val(),
                        uid: $(this).attr('id')
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (!###VALIDATOR_DISABLE###) {
                            powermail_validator.data('validator').reset();
                        }
                        var idCountry = parseInt(response[0]['id']);
                        var idCountryZone = idCountry + 100000;
                        var uidCountryZone = 'uid' + idCountryZone;
                        var countyClass = $('#uid' + idCountry).attr('class');
                        var selectedZone = response[0]['selected'];
                        $('#powermaildiv_' + uidCountryZone).remove();
                        if(response.length > 1 && response[1]['zn_code'] != null) {
                            var new_content = '';
                            var option = 0;
                            $.each(response, function(i, row) {
                                if (option == 0) {
                                    new_content += '<option value="">###PLEASE_SELECT###</option>';
                                    option ++;
                                } else {
                                    new_content += '<option value="' + row.zn_code + '"' + (row.zn_code == selectedZone ? ' selected="selected"' : '') + '>' + row.zn_name_local + '</option>';
                                }
                            });
                            // TODO: Labelname in piVars

                            $('#powermaildiv_uid' + idCountry)
                                .clone().removeAttr('id')
                                .attr('id', 'powermaildiv_' + uidCountryZone)
                                .removeClass('tx_powermail_pi1_fieldwrap_html_' + idCountry)
                                .addClass('tx_powermail_pi1_fieldwrap_html_' + idCountryZone)
                                .insertAfter('#powermaildiv_uid' + idCountry)
                                .find('label').html('###COUNTRY_ZONE###')
                                .attr('for', uidCountryZone)
                                .parent().find('select')
                                .replaceWith('<select name="tx_powermail_pi1[' + uidCountryZone + ']" id="' + uidCountryZone + '" class="' + countyClass + '">' + new_content + '</select>');
                            $('#' + uidCountryZone)
                                .removeClass('powermail_uid' + idCountry)
                                .addClass('powermail_' + uidCountryZone)
                                .attr('tabindex', parseInt($('#uid' + idCountry).attr('tabindex')) + 1);
                            if ($('#uid' + idCountry).attr('required') == 'required') {
                                $('#' + uidCountryZone).attr('required', 'required');
                            }
                            reinitializeValidator();
                        }
                    },
                    error: function(error) {
                            alert('Ajax request not successful.');
                    }
            });
        }

        if ($('.powermail_countryselect.powermail_with_countryzone').length > 0) {
            $('.powermail_countryselect.powermail_with_countryzone').each(function () {
                if ($(this).val() != '') {
                    $(this).getCountryZones();
                }
            });
        };

        $('.powermail_countryselect.powermail_with_countryzone').change(function () {
            if ($(this).val() != '') {
                $(this).getCountryZones();
            } else {
                if (!###VALIDATOR_DISABLE###) {
                    powermail_validator.data('validator').reset();
                }
                idCountryZone = parseInt($(this).attr('id').substr(3)) + 100000;
                $('#powermaildiv_uid' + idCountryZone).remove();
                reinitializeValidator();
            }
        });


        if (###SHOW_TRIGGER_ICON###) {
            $('.tx_powermail_pi1_fieldwrap_html_datetime, .tx_powermail_pi1_fieldwrap_html_date').addClass('calendar_icon');
        }

		// add tabs to fieldsets for multiple page
		$('ul.powermail_multiplejs_tabs li a:first').addClass('act'); // first tab with class "act"
		if ($.ui && typeof($.ui.tabs) == 'function') {
			// Add UI tabs
			$('.powermail_multiple_js .powermail_multiplejs_tabs_item a').each(function(id, item) {
				var temp = item.href.split('#');
				var temp_last = temp[temp.length - 1];
				var search = /^tx\-powermail\-pi1\_fieldset/;
				if (search.test(temp_last)) {
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
			if (!###VALIDATOR_DISABLE###)
			{
				$(this).parent().parent().find('a').not('.current').each(function(id, item) {
					var temp = item.href.split('#');
					var resetSelector = $('#' + temp[temp.length - 1] + ' :input');
   				powermail_validator.data('validator').reset(resetSelector);
				});
			}
		});
	});
})(jQuery);
