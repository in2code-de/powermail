/**
 * Powermail main JavaScript for form validation
 */
jQuery(document).ready(function($) {

	// Form validation
	$('.powermail_form').validationEngine();

	// Tabs
	$('.powermail_morestep').tabs();

	// Datepicker field
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
	$('.' + classes[1]).each(function() {
		if ($(this).attr('checked')) {
			checked = 1;
		}
	});

	if (!checked) {
		return options.allrules.checkCheckboxes.alertText;
	}
}
