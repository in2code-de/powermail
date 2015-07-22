/**
 * Baseurl
 *
 * @type {string}
 */
var baseurl;

/**
 * Powermail main JavaScript for form validation
 */
jQuery(document).ready(function($) {

	// Read baseURL
	baseurl = getBaseUrl();

	// Tabs
	if ($.fn.powermailTabs) {
		$('.powermail_morestep').powermailTabs();
	}

	// Location field
	if ($('.powermail_fieldwrap_location input').length) {
		getLocationAndWrite();
	}

	// AJAX Form submit
	if ($('form[data-powermail-ajax]').length) {
		ajaxFormSubmit();
	}

	// Datepicker field
	if ($.fn.datetimepicker) {
		$('.powermail_date').each(function() {
			var $this = $(this);
			// stop javascript datepicker, if browser supports type="date" or "datetime-local" or "time"
			if ($this.prop('type') === 'date' || $this.prop('type') === 'datetime-local' || $this.prop('type') === 'time') {
				if ($this.data('datepicker-force')) {
					// rewrite input type
					$this.prop('type', 'text');
				} else {
					// get date in format Y-m-d H:i for html5 date fields
					if ($(this).data('date-value')) {
						var prefillDate = getDatetimeForDateFields($(this).data('date-value'), $(this).data('datepicker-format'), $this.prop('type'));
						if (prefillDate !== null) {
							$(this).val(prefillDate);
						}
					}

					// stop js datepicker
					return;
				}
			}

			var datepickerStatus = true;
			var timepickerStatus = true;
			if ($this.data('datepicker-settings') === 'date') {
				timepickerStatus = false;
			} else if ($this.data('datepicker-settings') === 'time') {
				datepickerStatus = false;
			}

			// create datepicker
			$this.datetimepicker({
				format: $this.data('datepicker-format'),
				timepicker: timepickerStatus,
				datepicker: datepickerStatus,
				lang: 'en',
				i18n:{
					en:{
						months: $this.data('datepicker-months').split(','),
						dayOfWeek: $this.data('datepicker-days').split(',')
					}
				}
			});
		});
	}

	// Password Field Output
	$('.powermail_all_type_password.powermail_all_value').html('********');

	// Reset
	if ($.fn.parsley) {
		$('.powermail_reset').on('click', '', function(e) {
			$('form[data-parsley-validate="data-parsley-validate"]').parsley().reset();
		});
	}

	deleteAllFilesListener();
});

/**
 * Add eventhandler for deleting all files button
 *
 * @returns {void}
 */
function deleteAllFilesListener() {
	jQuery('.powermail_fieldwrap_file_inner').find('.deleteAllFiles').each(function() {
		// initially hide upload fields
		disableUploadField(jQuery(this).closest('.powermail_fieldwrap_file_inner').find('input[type="file"]'));
	});
	jQuery('.deleteAllFiles').click(function() {
		enableUploadField(jQuery(this).closest('.powermail_fieldwrap_file_inner').children('input[type="hidden"]'));
		jQuery(this).closest('ul').fadeOut(function() {
			jQuery(this).remove();
		});
	});
	function disableUploadField(element) {
		element.prop('disabled', 'disabled').addClass('hide').prop('type', 'hidden');
	}
	function enableUploadField(element) {
		element.removeProp('disabled').removeClass('hide').prop('type', 'file');
	}
}

/**
 * Allow AJAX Submit for powermail
 *
 * @return void
 */
function ajaxFormSubmit() {
	var regularSubmitOnAjax = false;
	var redirectUri = getValueFromField($('#redirectUri'));

	// submit is called after parsley and html5 validation - so we don't have to check for errors
	jQuery(document).on('submit', 'form[data-powermail-ajax]', function (e) {
		var $this = jQuery(this);
		var formUid = $this.data('powermail-form');

		if (!regularSubmitOnAjax) {
			jQuery.ajax({
				type: 'POST',
				url: $this.prop('action'),
				data: new FormData($this.get(0)),
				contentType: false,
				processData: false,
				beforeSend: function() {
					// add progressbar div.powermail_progressbar>div.powermail_progress>div.powermail_progess_inner
					var progressBar = jQuery('<div />').addClass('powermail_progressbar').html(
						jQuery('<div />').addClass('powermail_progress').html(
							jQuery('<div />').addClass('powermail_progess_inner')
						)
					);
					jQuery('.powermail_submit', $this).parent().append(progressBar);
					jQuery('.powermail_confirmation_submit, .powermail_confirmation_form', $this).closest('.powermail_confirmation').append(progressBar);
				},
				complete: function() {
					// remove progressbar
					jQuery('.powermail_fieldwrap_submit', $this).find('.powermail_progressbar').remove();
					deleteAllFilesListener();
				},
				success: function(data) {
					var html = jQuery('*[data-powermail-form="' + formUid + '"]:first', data);
					if (html.length) {
						jQuery('*[data-powermail-form="' + formUid + '"]:first').closest('.tx-powermail').html(html);
						// fire tabs and parsley again
						if (jQuery.fn.powermailTabs) {
							jQuery('.powermail_morestep').powermailTabs();
						}
						if (jQuery.fn.parsley) {
							jQuery('form[data-parsley-validate="data-parsley-validate"]').parsley();
						}
					} else {
						// no form markup found try to redirect via clientside
						if (redirectUri) {
							window.location = redirectUri;
						} else {
							$this.submit();
						}
						regularSubmitOnAjax = true;
					}
				}
			});

			e.preventDefault();
		}
	});
}

/**
 * Get value of field and check if element exists
 *
 * @param element
 * @returns {string}
 */
function getValueFromField(element) {
	var value = '';
	if (element.length) {
		value = element.val();
	}
	return value;
}

/**
 * Convert date format for html5 date fields
 *      31.08.2014 => 2014-08-31
 *
 * @param value
 * @param format
 * @param type
 * @returns {string|null}
 */
function getDatetimeForDateFields(value, format, type) {
	var formatDate = Date.parseDate(value, format);
	if (formatDate === null) {
		return null;
	}
	var date = new Date(formatDate);
	var valueDate = date.getFullYear() + '-';
	valueDate += ('0' + (date.getMonth() + 1)).slice(-2) + '-';
	valueDate += ('0' + date.getDate()).slice(-2);
	var valueTime = ('0' + date.getHours()).slice(-2) + ':' + ('0' + date.getMinutes()).slice(-2);
	var valueDateTime = valueDate + 'T' + valueTime;

	if (type === 'date') {
		return valueDate;
	}
	if (type === 'datetime-local') {
		return valueDateTime;
	}
	if (type === 'time') {
		return valueTime;
	}
	return null;
}

/**
 * Getting the Location by the browser and write to inputform as address
 *
 * @return void
 */
function getLocationAndWrite() {
	if (navigator.geolocation) { // Read location from Browser
		navigator.geolocation.getCurrentPosition(function(position) {
			var lat = position.coords.latitude;
			var lng = position.coords.longitude;
			var url = baseurl + '/index.php' + '?eID=' + 'powermailEidGetLocation';
			jQuery.ajax({
				url: url,
				data: 'lat=' + lat + '&lng=' + lng,
				cache: false,
				beforeSend: function(jqXHR, settings) {
					jQuery('body').css('cursor', 'wait');
				},
				complete: function(jqXHR, textStatus) {
					jQuery('body').css('cursor', 'default');
				},
				success: function(data) { // return values
					if (data) {
						jQuery('.powermail_fieldwrap_location input').val(data);
					}
				}
			});
		});
	}
}

/**
 * Return BaseUrl as prefix
 *
 * @return	string	Base Url
 */
function getBaseUrl() {
	var baseurl;
	if (jQuery('base').length > 0) {
		baseurl = jQuery('base').prop('href');
	} else {
		if (window.location.protocol != "https:") {
			baseurl = 'http://' + window.location.hostname;
		} else {
			baseurl = 'https://' + window.location.hostname;
		}
	}
	return baseurl;
}