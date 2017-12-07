/**
 * Powermail functions
 *
 * @params {jQuery} $
 * @class PowermailForm
 */
function PowermailForm($) {
	'use strict';

	/**
	 * Initialize
	 *
	 * @returns {void}
	 */
	this.initialize = function() {
		addTabsListener();
		addAjaxFormSubmitListener();
		getLocationAndWrite();
		addDatePicker();
		hidePasswords();
		addResetListener();
		deleteAllFilesListener();
		uploadValidationListener();
	};

	/**
	 * Add tabs listener
	 *
	 * @returns {void}
	 * @private
	 */
	var addTabsListener = function() {
		if ($.fn.powermailTabs) {
			$('.powermail_morestep').each(function() {
				$(this).powermailTabs();
			});
		}
	};

	/**
	 * Add Ajax form submit listener
	 *
	 * @returns {void}
	 * @private
	 */
	var addAjaxFormSubmitListener = function() {
		if ($('form[data-powermail-ajax]').length) {
			ajaxFormSubmit();
		}
	};

	/**
	 * Getting the Location by the browser and write to inputform as address
	 *
	 * @return {void}
	 * @private
	 */
	var getLocationAndWrite = function() {
		if ($('*[data-powermail-location="prefill"]').length && navigator.geolocation) {
			var $this = $(this);
			navigator.geolocation.getCurrentPosition(function(position) {
				var lat = position.coords.latitude;
				var lng = position.coords.longitude;
				var url = getBaseUrl() + '/index.php' + '?eID=' + 'powermailEidGetLocation';
				jQuery.ajax({
					url: url,
					data: 'lat=' + lat + '&lng=' + lng,
					cache: false,
					success: function(data) {
						if (data) {
							$('*[data-powermail-location="prefill"]').val(data);
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
	 * @private
	 */
	var addDatePicker = function() {
		if ($.fn.datetimepicker) {
			$('.powermail_date').each(function() {
				var $this = $(this);
				// stop javascript datepicker, if browser supports type="date" or "datetime-local" or "time"
				if ($this.prop('type') === 'date' || $this.prop('type') === 'datetime-local' || $this.prop('type') === 'time') {
					if ($this.data('datepicker-force')) {
						// rewrite input type
						$this.prop('type', 'text');
						$this.val($(this).data('date-value'));
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
	};

	/**
	 * Simply change value of password fields
	 *
	 * @returns {void}
	 * @private
	 */
	var hidePasswords = function() {
		$('.powermail_all_type_password.powermail_all_value').html('********');
	};

	/**
	 * Add validation reseter on click on reset button
	 *
	 * @returns {void}
	 * @private
	 */
	var addResetListener = function() {
		if ($.fn.parsley) {
			$('.powermail_reset').on('click', '', function() {
				$('form[data-parsley-validate="data-parsley-validate"]').parsley().reset();
			});
		}
	};

	/**
	 * Add validation for upload fields
	 *
	 * @returns {void}
	 * @private
	 */
	var uploadValidationListener = function() {
		if (window.Parsley) {
			uploadSizeValidatorListener();
			uploadExtensionValidatorListener();
		}
	};

	/**
	 * ************ INTERNAL *************
	 */

	/**
	 * Allow AJAX Submit for powermail
	 *
	 * @returns {void}
	 * @private
	 */
	var ajaxFormSubmit = function() {
		var regularSubmitOnAjax = false;
		var redirectUri;

		// submit is called after parsley and html5 validation - so we don't have to check for errors
		$(document).on('submit', 'form[data-powermail-ajax]', function(e) {
			var $this = $(this);
			var $txPowermail = $this.closest('.tx-powermail');
			if ($this.data('powermail-ajax-uri')) {
				redirectUri = $this.data('powermail-ajax-uri');
			}
			var formUid = $this.data('powermail-form');

			if (!regularSubmitOnAjax) {
				$.ajax({
					type: 'POST',
					url: $this.prop('action'),
					data: new FormData($this.get(0)),
					contentType: false,
					processData: false,
					beforeSend: function() {
						addProgressbar($this);
					},
					complete: function() {
						removeProgressbar($this);
						deleteAllFilesListener();
 						fireAjaxCompleteEvent($txPowermail);
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
							reloadCaptchaImages();
						} else {
							// no form markup found try to redirect via javascript
							if (redirectUri) {
								redirectToUri(redirectUri);
							} else {
								// fallback if no location found (but will effect 2x submit)
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
	 * @param {jQuery} $this
	 * @returns {void}
	 * @private
	 */
	var addProgressbar = function($this) {
		removeProgressbar($this);
		if ($('.powermail_submit', $this).length) {
			$('.powermail_submit', $this).parent().append(getProgressbar());
		} else {
			$this.closest('.tx-powermail').append(getProgressbar());
		}
	};

	/**
	 * @param {jQuery} $this
	 * @returns {void}
	 * @private
	 */
	var removeProgressbar = function($this) {
		$this.closest('.tx-powermail').find('.powermail_progressbar').remove();
	};

	/**
	 * Fire event when ajax submission is complete.
	 * Note: this event fires on the .tx-powermail element, since its inner html is replaced
	 *
	 * example usage:
	 * $('.tx-powermail').on('submitted.powermail.form', function(){
	 * 		console.log('ajax form was submitted');
	 * })
	 * @param $txPowermail
	 */
	var fireAjaxCompleteEvent = function($txPowermail) {
		var submittedEvent = $.Event('submitted.powermail.form');
		$txPowermail.trigger(submittedEvent);
	};

	/**
	 * Add eventhandler for deleting all files button
	 *
	 * @returns {void}
	 * @private
	 */
	var deleteAllFilesListener = function() {
		$('.powermail_fieldwrap_file').find('.deleteAllFiles').each(function() {
			// initially hide upload fields
			disableUploadField($(this).closest('.powermail_fieldwrap_file').find('input[type="file"]'));
		});
		$('.deleteAllFiles').click(function() {
			enableUploadField($(this).closest('.powermail_fieldwrap_file').find('input[type="hidden"]'));
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
	 * @private
	 */
	var disableUploadField = function($element) {
		$element.prop('disabled', 'disabled').addClass('hide').prop('type', 'hidden');
	};

	/**
	 * Enable upload field
	 *
	 * @param {jQuery} $element
	 * @returns {void}
	 * @private
	 */
	var enableUploadField = function($element) {
		$element.prop('disabled', false).removeClass('hide').prop('type', 'file');
	};

	/**
	 * Reload captcha images
	 *
	 * @returns {void}
	 * @private
	 */
	var reloadCaptchaImages = function() {
		$('img.powermail_captchaimage').each(function() {
			var source = getUriWithoutGetParam($(this).prop('src'));
			$(this).prop('src', source + '?hash=' + getRandomString(5));
		});
	};

	/**
	 * Get uri without get params
	 *
	 * @param {string} uri
	 * @returns {string}
	 * @private
	 */
	var getUriWithoutGetParam = function(uri) {
		var parts = uri.split('?');
		return parts[0];
	};

	/**
	 * Get random string
	 *
	 * @param {int} length
	 * @returns {string}
	 * @private
	 */
	var getRandomString = function(length) {
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
	 * @private
	 */
	var getDatetimeForDateFields = function(value, format, type) {
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
	 * Get markup for progressbar
	 * 		div.powermail_progressbar>div.powermail_progress>div.powermail_progess_inner
	 *
	 * @returns {jQuery}
	 * @private
	 */
	var getProgressbar = function() {
		return $('<div />').addClass('powermail_progressbar').html(
			$('<div />').addClass('powermail_progress').html(
				$('<div />').addClass('powermail_progess_inner')
			)
		);
	};

	/**
	 * Get maximum filesize of files in multiple upload field
	 *
	 * @param {jQuery} $field
	 * @returns {int}
	 * @private
	 */
	var getMaxFileSize = function($field) {
		var field = $field.get(0);
		var size = 0;
		for (var i = 0; i < field.files.length; i++) {
			var file = field.files[i];
			if (file.size > size) {
				size = file.size;
			}
		}
		return parseInt(size);
	};

	/**
	 * Filesize check for upload fields
	 *
	 * @returns {void}
	 * @private
	 */
	var uploadSizeValidatorListener = function() {
		window.Parsley
			.addValidator('powermailfilesize', function(value, requirement) {
				if (requirement.indexOf(',') !== -1) {
					var requirements = requirement.split(',');
					var maxUploadSize = parseInt(requirements[0]);
					var $this = $('*[name="tx_powermail_pi1[field][' + requirements[1] + '][]"]');
					if ($this.length) {
						if (getMaxFileSize($this) > maxUploadSize) {
							return false;
						}
					}
				}

				// pass test if problems
				return true;
			}, 32)
			.addMessage('en', 'powermailfilesize', 'Error');
	};

	/**
	 * File extension check for upload fields
	 *
	 * @returns {void}
	 * @private
	 */
	var uploadExtensionValidatorListener = function() {
		window.Parsley
			.addValidator('powermailfileextensions', function(value, requirement) {
				var $this = $('*[name="tx_powermail_pi1[field][' + requirement + '][]"]');
				if ($this.length) {
					return isFileExtensionInList(getExtensionFromFileName(value), $this.prop('accept'));
				}

				// pass test if problems
				return true;
			}, 32)
			.addMessage('en', 'powermailfileextensions', 'Error');
	};

	/**
	 * Check if fileextension is allowed in dotted list
	 * 		"jpg" in ".jpg,.jpeg" => true
	 * 		"jpg" in ".gif,.png" => false
	 *
	 * @param {string} extension
	 * @param {string} list
	 * @returns {boolean}
	 * @private
	 */
	var isFileExtensionInList = function(extension, list) {
		return list.indexOf('.' + extension) !== -1;
	};

	/**
	 * Get extension from filename in lowercase
	 * 		image.jpg => jpg
	 * 		image.JPG => jpg
	 *
	 * @param {string} fileName
	 * @returns {string}
	 * @private
	 */
	var getExtensionFromFileName = function(fileName) {
		return fileName.split('.').pop().toLowerCase();
	};

	/**
	 * Redirect to an external or internal target
	 *
	 * @param {string} redirectUri
	 * @private
	 */
	var redirectToUri = function(redirectUri) {
		if (redirectUri.indexOf('http') !== -1) {
			window.location = redirectUri;
		} else {
			window.location.pathname = redirectUri;
		}
	};

	/**
	 * Return BaseUrl as prefix
	 *
	 * @return {string} Base Url
	 * @private
	 */
	var getBaseUrl = function() {
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
	};
}

jQuery(document).ready(function($) {
	'use strict';
	var PowermailForm = new window.PowermailForm($);
	PowermailForm.initialize();
});
