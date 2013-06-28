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

	// Form validation
	if ($.fn.validationEngine) {
		$('.powermail_form').validationEngine();
	}

	// Tabs
	if ($.fn.powermailTabs) {
		$('.powermail_morestep').powermailTabs();
	}

	// Datepicker field
	if ($.fn.datepicker) {
		$('.powermail_date').datepicker({
			dateFormat: $('.container_datepicker_dateformat:first').val(),
			dayNamesMin: [
				$('.container_datepicker_day_so:first').val(),
				$('.container_datepicker_day_mo:first').val(),
				$('.container_datepicker_day_tu:first').val(),
				$('.container_datepicker_day_we:first').val(),
				$('.container_datepicker_day_th:first').val(),
				$('.container_datepicker_day_fr:first').val(),
				$('.container_datepicker_day_sa:first').val()
			],
			monthNames: [
				$('.container_datepicker_month_jan:first').val(),
				$('.container_datepicker_month_feb:first').val(),
				$('.container_datepicker_month_mar:first').val(),
				$('.container_datepicker_month_apr:first').val(),
				$('.container_datepicker_month_may:first').val(),
				$('.container_datepicker_month_jun:first').val(),
				$('.container_datepicker_month_jul:first').val(),
				$('.container_datepicker_month_aug:first').val(),
				$('.container_datepicker_month_sep:first').val(),
				$('.container_datepicker_month_oct:first').val(),
				$('.container_datepicker_month_nov:first').val(),
				$('.container_datepicker_month_dec:first').val()
			],
			nextText: '&gt;',
			prevText: '&lt;',
			firstDay: 1
		});
	}

	// Location field
	if ($('.powermail_fieldwrap_location input').length > 0) {
		getLocationAndWrite();
	}
});

/**
 * Custom Validation of checkboxes for powermail
 *
 * @param	object		Current Field
 * @param	object		Given Rules
 * @param	int			Index
 * @param	object		Options
 * @return	string		Error Message
 */
function checkCheckboxes(field, rules, i, options) {
	var checked = 0; // no checkbox checked at the beginning
	var classes = field.attr('class').split(' ');
	jQuery('.' + classes[1]).each(function() {
		if (jQuery(this).attr('checked')) {
			checked = 1;
		}
	});

	if (!checked) {
		return options.allrules.checkCheckboxes.alertText;
	}
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
		baseurl = jQuery('base').attr('href');
	} else {
		if (window.location.protocol != "https:") {
			baseurl = 'http://' + window.location.hostname;
		} else {
			baseurl = 'https://' + window.location.hostname;
		}
	}
	return baseurl;
}