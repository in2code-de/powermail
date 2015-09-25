/**
 * Powermail functions
 *
 * @params {jQuery} $
 * @class PowermailForm
 */
function PowermailForm($) {
	'use strict';

	/**
	 * This class
	 *
	 * @type {PowermailForm}
	 */
	var that = this;

	/**
	 * Initialize
	 *
	 * @returns {void}
	 */
	this.initialize = function() {
		that.addTabsListener();
		that.addAjaxFormSubmitListener();
		that.getLocationAndWrite();
		that.addDatePicker();
		that.hidePasswords();
		that.addResetListener();
		that.deleteAllFilesListener();
	};

	/**
	 * Add tabs listener
	 *
	 * @returns {void}
	 */
	this.addTabsListener = function() {
		if ($.fn.powermailTabs) {
			$('.powermail_morestep').powermailTabs();
		}
	};

	/**
	 * Add Ajax form submit listener
	 *
	 * @returns {void}
	 */
	this.addAjaxFormSubmitListener = function() {
		if ($('form[data-powermail-ajax]').length) {
			that.ajaxFormSubmit();
		}
	};

	/**
	 * Getting the Location by the browser and write to inputform as address
	 *
	 * @return {void}
	 */
	this.getLocationAndWrite = function() {
		if ($('.powermail_fieldwrap_location input').length && navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(function(position) {
				var lat = position.coords.latitude;
				var lng = position.coords.longitude;
				var url = that.getBaseUrl + '/index.php' + '?eID=' + 'powermailEidGetLocation';
				jQuery.ajax({
					url: url,
					data: 'lat=' + lat + '&lng=' + lng,
					cache: false,
					beforeSend: function() {
						$('body').css('cursor', 'wait');
					},
					complete: function() {
						$('body').css('cursor', 'default');
					},
					success: function(data) {
						if (data) {
							$('.powermail_fieldwrap_location input').val(data);
						}
					}
				});
			});
		}
	};

	/**
	 * Add datepicker to date fields
	 *
	 * @returns {void}
	 */
	this.addDatePicker = function() {
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
							var prefillDate = that.getDatetimeForDateFields($(this).data('date-value'), $(this).data('datepicker-format'), $this.prop('type'));
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
	};

	/**
	 * Simply change value of password fields
	 *
	 * @returns {void}
	 */
	this.hidePasswords = function() {
		$('.powermail_all_type_password.powermail_all_value').html('********');
	};

	/**
	 * Add validation reseter on click on reset button
	 *
	 * @returns {void}
	 */
	this.addResetListener = function() {
		if ($.fn.parsley) {
			$('.powermail_reset').on('click', '', function() {
				$('form[data-parsley-validate="data-parsley-validate"]').parsley().reset();
			});
		}
	};

	/**
	 * ************ INTERNAL *************
	 */

	/**
	 * Allow AJAX Submit for powermail
	 *
	 * @returns {void}
	 */
	this.ajaxFormSubmit = function() {
		var regularSubmitOnAjax = false;
		var redirectUri = that.getValueFromField($('#redirectUri'));

		// submit is called after parsley and html5 validation - so we don't have to check for errors
		$(document).on('submit', 'form[data-powermail-ajax]', function (e) {
			var $this = $(this);
			var formUid = $this.data('powermail-form');

			if (!regularSubmitOnAjax) {
				$.ajax({
					type: 'POST',
					url: $this.prop('action'),
					data: new FormData($this.get(0)),
					contentType: false,
					processData: false,
					beforeSend: function() {
						// add progressbar div.powermail_progressbar>div.powermail_progress>div.powermail_progess_inner
						var progressBar = $('<div />').addClass('powermail_progressbar').html(
							$('<div />').addClass('powermail_progress').html(
								$('<div />').addClass('powermail_progess_inner')
							)
						);
						$('.powermail_submit', $this).parent().append(progressBar);
						$('.powermail_confirmation_submit, .powermail_confirmation_form', $this).closest('.powermail_confirmation').append(progressBar);
					},
					complete: function() {
						// remove progressbar
						$('.powermail_fieldwrap_submit', $this).find('.powermail_progressbar').remove();
						that.deleteAllFilesListener();
					},
					success: function(data) {
						var html = $('*[data-powermail-form="' + formUid + '"]:first', data);
						if (html.length) {
							$('*[data-powermail-form="' + formUid + '"]:first').closest('.tx-powermail').html(html);
							// fire tabs and parsley again
							if ($.fn.powermailTabs) {
								$('.powermail_morestep').powermailTabs();
							}
							if ($.fn.parsley) {
								$('form[data-parsley-validate="data-parsley-validate"]').parsley();
							}
							that.reloadCaptchaImages();
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
	};

	/**
	 * Add eventhandler for deleting all files button
	 *
	 * @returns {void}
	 */
	this.deleteAllFilesListener = function() {
		$('.powermail_fieldwrap_file_inner').find('.deleteAllFiles').each(function() {
			// initially hide upload fields
			that.disableUploadField($(this).closest('.powermail_fieldwrap_file_inner').find('input[type="file"]'));
		});
		$('.deleteAllFiles').click(function() {
			that.enableUploadField($(this).closest('.powermail_fieldwrap_file_inner').children('input[type="hidden"]'));
			$(this).closest('ul').fadeOut(function() {
				$(this).remove();
			});
		});
	};

	/**
	 * Disable upload field
	 *
	 * @param {jQuery} $element
	 * @returns {void}
	 */
	this.disableUploadField = function($element) {
		$element.prop('disabled', 'disabled').addClass('hide').prop('type', 'hidden');
	};

	/**
	 * Enable upload field
	 *
	 * @param {jQuery} $element
	 * @returns {void}
	 */
	this.enableUploadField = function($element) {
		$element.removeProp('disabled').removeClass('hide').prop('type', 'file');
	};

	/**
	 * Reload captcha images
	 *
	 * @returns {void}
	 */
	this.reloadCaptchaImages = function() {
		$('img.powermail_captchaimage').each(function() {
			var source = that.getUriWithoutGetParam($(this).prop('src'));
			$(this).prop('src', source + '?hash=' + that.getRandomString(5));
		});
	};

	/**
	 * Get value of field and check if element exists
	 *
	 * @param {jQuery} $element
	 * @returns {string}
	 */
	this.getValueFromField = function($element) {
		var value = '';
		if ($element.length) {
			value = $element.val();
		}
		return value;
	};

	/**
	 * Get uri without get params
	 *
	 * @param {string} uri
	 * @returns {string}
	 */
	this.getUriWithoutGetParam = function(uri) {
		var parts = uri.split('?');
		return parts[0];
	};

	/**
	 * Get random string
	 *
	 * @param {int} length
	 * @returns {string}
	 */
	this.getRandomString = function(length) {
		var text = '';
		var possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		for (var i=0; i < length; i++) {
			text += possible.charAt(Math.floor(Math.random() * possible.length));
		}
		return text;
	};

	/**
	 * Convert date format for html5 date fields
	 *      31.08.2014 => 2014-08-31
	 *
	 * @param {string} value
	 * @param {string} format
	 * @param {string} type
	 * @returns {string|null}
	 */
	this.getDatetimeForDateFields = function(value, format, type) {
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
	};

	/**
	 * Return BaseUrl as prefix
	 *
	 * @return {string} Base Url
	 */
	this.getBaseUrl = function() {
		var baseurl;
		if ($('base').length > 0) {
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
}

jQuery(document).ready(function($) {
	var PowermailForm = new window.PowermailForm($);
	PowermailForm.initialize();
});